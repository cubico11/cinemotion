<?php
require_once 'recensione.php';
require_once 'persona.php';
class Film
{
    private $id;
    private $titolo;
    private $immagine;
    private $durata;
    private $data_uscita;
    private $media_voti;
    private $numero_recensioni;
    private $descrizione;
    private $recensioni = [];
    private $personePerRuolo = [];

    public function __construct($conn, $id_film)
    {
        $this->id = intval($id_film);

        $query = "
            SELECT Film.Titolo, Film.Immagine, Film.Durata, Film.Data_Uscita, Film.Descrizione,
                   AVG(Recensione.Voto) AS Media_Voti,
                   COUNT(Recensione.Id) AS Numero_Recensioni
            FROM Film
            LEFT JOIN Recensione ON Film.Id = Recensione.Id_Film
            WHERE Film.Id = $this->id
            GROUP BY Film.Id
        ";

        $result = $conn->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            $this->titolo = $row['Titolo'];
            $this->immagine = $row['Immagine'];
            $this->durata = $row['Durata'];
            $this->data_uscita = $row['Data_Uscita'];
            $this->media_voti = $row['Media_Voti'] ?? null;
            $this->numero_recensioni = $row['Numero_Recensioni'];
            $this->descrizione = $row['Descrizione'];
        } else {
            throw new Exception("Film non trovato");
        }

        $this->caricaRecensioni($conn);
        $this->caricaPersone($conn, "Attore");
        $this->caricaPersone($conn, "Regista");
        $this->caricaPersone($conn, "Sceneggiatore");
    }

    // Metodo che carica le recensioni
    private function caricaRecensioni($conn)
    {
        $query = "SELECT Id, Data, Voto, Testo, Id_Utente FROM Recensione WHERE Id_Film = $this->id";
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $recensione = new recensione(
                $row['Id'],
                $row['Data'],
                $row['Voto'],
                $row['Testo'],
                $row['Id_Utente'],
                $this->id
            );
            $this->recensioni[] = $recensione;
        }
    }

    // GETTER per recensioni
    public function getRecensioni()
    {
        $msg = "Nessuna recensione presente.";

        //stampa "Recensione" se ce n'è una sola, altrimenti "Recensioni"
        $parolaRecensione = ($this->numero_recensioni == 1) ? ' recensione' : ' recensioni';

        if (!empty($this->recensioni)) {
            $msg = "<div class='reviews-info'> <b>" . $this->numero_recensioni . "</b>" . $parolaRecensione .
                "<br> Media voti: " . generaStelle($this->media_voti) . "</div>
            <h2>Recensioni: </h2>";
            foreach ($this->recensioni as $rec) {
                $msg .= $rec->__toString();
            }
        }
        return $msg;
    }

    // Metodo che carica il cast
    public function caricaPersone(mysqli $conn, string $ruolo)
    {
        // Verifica se il ruolo è valido
        $ruoliValidi = ['Attore', 'Sceneggiatore', 'Regista'];
        if (!in_array($ruolo, $ruoliValidi)) {
            throw new Exception("Ruolo non valido: $ruolo");
        }

        // Se già caricato, non lo ricaricare
        if (isset($this->personePerRuolo[$ruolo]))
            return;

        $query = "
            SELECT 
                Persona.id, Persona.nome, Persona.cognome, Persona.immagine, Persona.data_nascita,
                Nazionalita.Denominazione AS nazionalita" . ($ruolo === 'Attore' ? ", $ruolo.ruolo" : "") . "
            FROM $ruolo
            JOIN Persona ON Persona.id = $ruolo.id_persona
            LEFT JOIN Persona_Nazionalita ON Persona.id = Persona_Nazionalita.id_persona
            LEFT JOIN Nazionalita ON Persona_Nazionalita.id_nazionalita = Nazionalita.id
            WHERE $ruolo.id_film = {$this->id}
        ";

        $result = $conn->query($query);
        $persone = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];

                if (!isset($persone[$id])) {
                    $persone[$id] = new Persona(
                        $conn,
                        $row['id']
                    );
                }

                if (!empty($row['nazionalita'])) {
                    $persone[$id]->aggiungiNazionalita($row['nazionalita']);
                }

                // Se è attore, aggiungi ruolo
                if ($ruolo === 'Attore' && isset($row['ruolo'])) {
                    $persone[$id]->setRuoloNelFilm($this->id, $row['ruolo']);
                }
            }
        }

        $this->personePerRuolo[$ruolo] = array_values($persone);
    }


    // GETTER per cast
    public function getPersone(string $ruolo)
    {
        if (empty($this->personePerRuolo[$ruolo])) {
            return "<p>Nessun $ruolo trovato per questo film.</p>";
        }

        $output = "";
        foreach ($this->personePerRuolo[$ruolo] as $item) {
            $output .= "<div class='persona-wrapper'>";
            $output .= $item->__toString();
            $output .= ($ruolo === "Attore") ? "<div class='persona-ruolo'>" . htmlspecialchars($item->getRuoloNelFilm($this->id)) . "</div>" : "";
            $output .= "</div>";
        }
        return $output;
    }


    // GETTER
    public function getId()
    {
        return $this->id;
    }

    public function getTitolo()
    {
        return $this->titolo;
    }

    public function getDescrizione()
    {
        return $this->descrizione;
    }

    public function getImmagine()
    {
        return $this->immagine;
    }

    public function getImmagineBase64()
    {
        return base64_encode($this->immagine);
    }

    public function getDurata()
    {
        return $this->durata;
    }

    public function getDurataFormattata()
    {
        $hours = floor($this->durata / 60);
        $minutes = $this->durata % 60;
        return $hours . " ore, " . $minutes . " min";
    }

    public function getDataUscita()
    {
        return $this->data_uscita;
    }

    public function getDataUscitaFormattata()
    {
        return date("d/m/Y", strtotime($this->data_uscita));
    }

    public function getDataUscitaTesto()
    {
        $mesi = [
            1 => 'gennaio',
            2 => 'febbraio',
            3 => 'marzo',
            4 => 'aprile',
            5 => 'maggio',
            6 => 'giugno',
            7 => 'luglio',
            8 => 'agosto',
            9 => 'settembre',
            10 => 'ottobre',
            11 => 'novembre',
            12 => 'dicembre'
        ];

        $data = new DateTime($this->data_uscita);
        $giorno = $data->format('d');
        $mese = $mesi[intval($data->format('m'))];
        $anno = $data->format('Y');

        echo "$giorno $mese $anno";
    }

    public function getMediaVoti()
    {
        return $this->media_voti;
    }

    public function getMediaVotiFormattata()
    {
        return $this->media_voti !== null ? number_format($this->media_voti, 1) : "N/A";
    }

    public function getNumeroRecensioni()
    {
        return $this->numero_recensioni;
    }

    // SETTER (opzionali, solo se prevedi modifiche)
    public function setTitolo($titolo)
    {
        $this->titolo = $titolo;
    }

    public function setDescrizione($descrizione)
    {
        $this->descrizione = $descrizione;
    }

    public function setImmagine($immagine)
    {
        $this->immagine = $immagine;
    }

    public function setDurata($durata)
    {
        $this->durata = $durata;
    }

    public function setDataUscita($data_uscita)
    {
        $this->data_uscita = $data_uscita;
    }

    public function setMediaVoti($media_voti)
    {
        $this->media_voti = $media_voti;
    }

    public function setNumeroRecensioni($numero_recensioni)
    {
        $this->numero_recensioni = $numero_recensioni;
    }

    public function renderCard(): string
    {
        $titolo = htmlspecialchars($this->titolo);
        $media = generaStelle($this->media_voti);

        return "
        <div class='film'>
            <a href='pages/dettaglio_film/dettaglio_film.php?id={$this->id}'>
                <img src='data:image/jpeg;base64," . base64_encode($this->immagine) . "'>
                <div class='film-title'>$titolo</div>
                <div class='film-rating'>$media</div>
            </a>
        </div>
        ";
    }

}
?>