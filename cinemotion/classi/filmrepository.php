<?php
class FilmRepository
{
    private $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function getFilm($orderBy = "Media_Voti DESC", $limit = 20): array
    {
        $query = "
            SELECT Film.Id, Film.Titolo, Film.Immagine, Film.Data_Uscita, AVG(Recensione.Voto) AS Media_Voti, COUNT(Recensione.Id) AS Numero_Recensioni
            FROM Film
            LEFT JOIN Recensione ON Film.Id = Recensione.Id_Film
            GROUP BY Film.Id
            ORDER BY $orderBy
            LIMIT $limit
        ";
        $result = $this->conn->query($query);

        $films = [];
        while ($row = $result->fetch_assoc()) {
            $films[] = new Film($this->conn, $row['Id']);
        }

        return $films;
    }

    public function cercaFilmPerTitolo($titolo, $idGenere = null, $idEmozione = null, $ordinamento = ''): array
    {
        $titolo = $this->conn->real_escape_string($titolo);
        $whereClause = $titolo ? "WHERE f.Titolo LIKE '%$titolo%'" : "";
        $joinGenere = $idGenere !== null ? "JOIN Appartiene a ON a.Id_Film = f.Id AND a.Id_Genere = $idGenere" : "";

        if ($idGenere !== null) {
            $filtroGenere = "JOIN Appartiene a ON a.Id_Film = f.Id AND a.Id_Genere = $idGenere";
        } else {
            $filtroGenere = "";
        }

        // Ordina in base all'opzione selezionata
        switch ($ordinamento) {
            case 'data_asc':
                $orderBy = "ORDER BY f.Data_Uscita ASC";
                break;
            case 'data_desc':
                $orderBy = "ORDER BY f.Data_Uscita DESC";
                break;
            case 'voti_asc':
                $orderBy = "ORDER BY Media_Voti ASC";
                break;
            case 'voti_desc':
                $orderBy = "ORDER BY Media_Voti DESC";
                break;
            case 'titolo_asc':
                $orderBy = "ORDER BY f.Titolo ASC";
                break;
            case 'recensioni_asc':
                $orderBy = "ORDER BY Num_Recensioni ASC";
                break;
            case 'recensioni_desc':
                $orderBy = "ORDER BY Num_Recensioni DESC";
                break;
            default:
                $orderBy = "ORDER BY Media_Voti DESC";
        }

        $query = "
            SELECT f.Id, AVG(r.Voto) AS Media_Voti, COUNT(r.Id) AS Num_Recensioni
            FROM Film f
            LEFT JOIN Recensione r ON r.Id_Film = f.Id
            $joinGenere
            $whereClause
            GROUP BY f.Id
            $orderBy
        ";

        $result = $this->conn->query($query);
        $filmList = [];

        while ($row = $result->fetch_assoc()) {
            $film = new Film($this->conn, $row['Id']);

            // Filtro per emozione: verifica che sia tra le top emozioni del film
            if ($idEmozione !== null) {
                $trovata = false;
                foreach ($film->getEmozioniTop() as $emozione) {
                    if ($emozione->getId() == $idEmozione) {
                        $trovata = true;
                        break;
                    }
                }
                if (!$trovata)
                    continue;
            }

            $filmList[] = $film;
        }

        return $filmList;
    }

    public function stampaFilmPerTitolo(string $stringa, $baseurl): void
    {
        $film = $this->cercaFilmPerTitolo($stringa);

        foreach ($film as $item) {
            echo $item->renderCard($baseurl);
        }
    }
}

?>