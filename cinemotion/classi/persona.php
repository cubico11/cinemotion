<?php
require_once 'film.php';
class Persona
{
    private $id;
    private $nome;
    private $cognome;
    private $immagine;
    private $data_nascita;
    private $nazionalita = [];
    private $ruoliPerFilm = []; // film_id => ruolo

    public function __construct($conn, $id_persona)
    {
        $this->id = intval($id_persona);

        // Query per recuperare info della persona e le sue nazionalità
        $query = "
            SELECT 
                Persona.nome, Persona.cognome, Persona.immagine, Persona.data_nascita,
                Nazionalita.Denominazione AS nazionalita
            FROM Persona
            LEFT JOIN Persona_Nazionalita ON Persona.id = Persona_Nazionalita.id_persona
            LEFT JOIN Nazionalita ON Persona_Nazionalita.id_nazionalita = Nazionalita.id
            WHERE Persona.id = $this->id
        ";

        $result = $conn->query($query);
        if (!$result || $result->num_rows === 0) {
            throw new Exception("Persona non trovata con id $id_persona");
        }

        while ($row = $result->fetch_assoc()) {
            if (!isset($this->nome)) {
                $this->nome = $row['nome'];
                $this->cognome = $row['cognome'];
                $this->immagine = $row['immagine'];
                $this->data_nascita = $row['data_nascita'];
            }

            if (!empty($row['nazionalita'])) {
                $this->nazionalita[] = $row['nazionalita'];
            }
        }
    }

    public function aggiungiNazionalita(string $naz)
    {
        if (!in_array($naz, $this->nazionalita)) {
            $this->nazionalita[] = $naz;
        }
    }

    // Metodo opzionale se vuoi gestire ruolo per attori
    public function setRuoloNelFilm(int $filmId, string $ruolo)
    {
        $this->ruoliPerFilm[$filmId] = $ruolo;
    }

    public function getRuoloNelFilm(int $filmId): ?string
    {
        return $this->ruoliPerFilm[$filmId] ?? null;
    }

    // Getter
    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getCognome()
    {
        return $this->cognome;
    }

    public function getImmagine()
    {
        return $this->immagine;
    }

    public function getDataNascita()
    {
        return $this->data_nascita;
    }

    public function getNazionalita()
    {
        return $this->nazionalita;
    }

    public function getTestoNazionalita()
    {
        $out = "";

        foreach ($this->nazionalita as $item) {
            $out .= $item . ", ";
        }

        //rimuove la virgola finale
        $out = substr($out, 0, -2);

        return $out;
    }

    public function getNomeCompleto()
    {
        return $this->nome . ' ' . $this->cognome;
    }

    public function getImmagineBase64()
    {
        return base64_encode($this->immagine);
    }

    public function getDataNascitaTesto()
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

        $data = new DateTime($this->data_nascita);
        $giorno = $data->format('d');
        $mese = $mesi[intval($data->format('m'))];
        $anno = $data->format('Y');

        echo "$giorno $mese $anno";
    }

    public function getFilmByRuolo($conn, $ruolo): array
    {
        $film = [];

        $query = "
            SELECT F.Id
            FROM Film F
            INNER JOIN $ruolo R ON R.id_film = F.Id
            WHERE R.id_persona = {$this->id}
        ";

        $result = $conn->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                try {
                    $film[] = new Film($conn, $row['Id']);
                } catch (Exception $e) {
                    // Log eventuali film mancanti
                }
            }
        }

        return $film;
    }

    public function getNFilmByRuolo($conn, $ruolo) {
        return count($this->getFilmByRuolo($conn, $ruolo));
    }

    public function printFilmByRuolo($conn, $ruolo, $baseurl)
    {
        $out = "";
        foreach ($this->getFilmByRuolo($conn, $ruolo) as $film) {
            $out .= $film->renderCard($baseurl);
        }
        return $out;
    }

    public function printAllFilms($conn)
    {
        $out = "";

        if ($this->getNFilmByRuolo($conn, "Attore") > 0) {
            $out .= "<div class='film-list' style='padding-left: 0'>
                <h1>Film da attore</h1>
                <div class='film-grid'>" .
                    $this->printFilmByRuolo($conn, 'Attore', '../dettaglio_film')
                    . "</div>
                </div>";
        }
        if ($this->getNFilmByRuolo($conn, "Regista") > 0) {
            $out .= "<div class='film-list' style='padding-left: 0'>
                <h1>Film da regista</h1>
                <div class='film-grid'>" .
                    $this->printFilmByRuolo($conn, 'Regista', '../dettaglio_film')
                    . "</div>
                </div>";
        }
        if ($this->getNFilmByRuolo($conn, "Sceneggiatore") > 0) {
            $out .= "<div class='film-list' style='padding-left: 0'>
                <h1>Film da sceneggiatore</h1>
                <div class='film-grid'>" .
                    $this->printFilmByRuolo($conn, 'Sceneggiatore', '../dettaglio_film')
                    . "</div>
                </div>";
        }

        return $out;
    }


    public function __toString()
    {
        return "
        <div class='persona'>
            <a href='../dettaglio_persona/dettaglio_persona.php?id={$this->id}'>
                <img src='data:image/jpeg;base64," . $this->getImmagineBase64() . "' alt='Attore'>
                <div class='persona-info'>
                    <strong>" . htmlspecialchars($this->getNomeCompleto()) . "</strong><br>
                </div>
            </a>
        </div>";
    }
}
?>