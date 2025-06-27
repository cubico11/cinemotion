<?php
require_once 'recensione.php';
require_once 'persona.php';
require_once 'genere.php';
require_once __DIR__ . '\..\funzioni.php';

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
    private array $emozioni_top = [];
    private array $generi = [];



    public function __construct($conn, $id_film)
    {
        $this->id = intval($id_film);

        $query = "SELECT Film.Titolo, Film.Immagine, Film.Durata, Film.Data_Uscita, Film.Descrizione,
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
        $this->caricaEmozioniTop($conn);
        $this->caricaGeneri($conn);
    }

    // Metodo che carica le recensioni
    private function caricaRecensioni($conn)
    {
        $query = "SELECT Id FROM Recensione WHERE Id_Film = $this->id";
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $recensione = new Recensione($conn, $row['Id']);
            if (!isset($_SESSION['username']) !== null && isThisUserLogged($recensione->getUtente()->getUsername())) {
                array_unshift($this->recensioni, $recensione); // Prima quelle dell'utente loggato
            } else {
                $this->recensioni[] = $recensione;
            }
        }
    }

    //carica la top 3 di emozioni più selezionate, MA se ce ne sono di pari le aggiunge comunque senza contare il limite di 3
    private function caricaEmozioniTop($conn)
    {
        $query = "
            SELECT e.Id, e.Denominazione, COUNT(*) AS Totale
            FROM Emozione e
            JOIN Recensione r ON e.id = r.id_emozione
            WHERE r.Id_Film = $this->id
            GROUP BY e.Id
            ORDER BY Totale DESC
        ";

        $result = $conn->query($query);
        if ($result) {
            $emozioni = [];
            while ($row = $result->fetch_assoc()) {
                $emozioni[] = [
                    'id' => $row['Id'],
                    'denominazione' => $row['Denominazione'],
                    'totale' => (int) $row['Totale']
                ];
            }

            // Trova la soglia di count per la top 3
            $soglia = 0;
            if (count($emozioni) >= 3) {
                $soglia = $emozioni[2]['totale'];
            }

            // Includi tutte quelle >= soglia
            foreach ($emozioni as $e) {
                if ($e['totale'] >= $soglia) {
                    $this->emozioni_top[] = new Emozione($conn, $e['id']);
                } else {
                    break;
                }
            }
        }
    }

    public function getEmozioniTop(): array
    {
        return $this->emozioni_top;
    }

    public function stampaEmozioniTop(int $n = 3): string
    {
        $msg = "";
        $i = 0;
        foreach ($this->emozioni_top as $emozione) {
            $msg .= "<span style=\"color:" . $emozione->getColorVariant("light") . "\">";
            $msg .= $emozione->getDenominazione() . "</span>";
            $msg .= ", ";
            if (++$i == $n)
                break;
        }
        $msg = substr($msg, 0, -2);
        return $msg;
    }

    public function setEmozioniTop(array $emozioni): void
    {
        $this->emozioni_top = $emozioni;
    }

    private function caricaGeneri(mysqli $conn): void
    {
        $query = "SELECT Id_Genere FROM Appartiene WHERE Id_Film = $this->id";
        $result = $conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $this->generi[] = new Genere($conn, intval($row['Id_Genere']));
            }
        }
    }

    public function getGeneri(): array
    {
        return $this->generi;
    }

    public function stampaGeneri(): string
    {
        $nomi = array_map(function ($g) {
            return $g->getDenominazione();
        }, $this->generi);

        return implode(", ", $nomi);
    }

    public function getInfoRecensioni()
    {
        $msg = "<b>Nessuna recensione presente.</b>";

        //stampa "Recensione" se ce n'è una sola, altrimenti "Recensioni"
        $parolaRecensione = ($this->numero_recensioni == 1) ? ' recensione' : ' recensioni';

        if (!empty($this->recensioni)) {
            $msg = "<b>" . $this->numero_recensioni . "</b>" . $parolaRecensione .
                "<br> Media voti: " . generaStelle($this->media_voti) .
                "<br> Emozioni più selezionate: <b>" . $this->stampaEmozioniTop() . "</b>";
        }
        return $msg;
    }

    // GETTER per recensioni
    public function getRecensioni()
    {
        $msg = "";
        if (!empty($this->recensioni)) {
            $msg = "<h2>Recensioni: </h2>";
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
            $output .= "<div class='persona-wrapper";
            $output .= ($ruolo == "Attore") ? " actor" : "";
            $output .= "'>";
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

    //stampa scheda del film; baseurl è l'inizio del percorso relativo rispetto alla pagina che chiama la funzione
    public function renderCard($baseurl): string
    {
        $titolo = htmlspecialchars($this->titolo);
        $media = generaStelle($this->media_voti);

        return "
        <div class='film'>
            <a href='$baseurl/dettaglio_film.php?id={$this->id}'>
                <img src='data:image/jpeg;base64," . base64_encode($this->immagine) . "'>
                <div class='film-title'>$titolo</div>
                <div class='film-emotion'>" . $this->stampaEmozioniTop(1) . "</div>
                <div class='film-rating'>$media (" . count($this->recensioni) . ")</div>
            </a>
        </div>
        ";
    }

}
?>