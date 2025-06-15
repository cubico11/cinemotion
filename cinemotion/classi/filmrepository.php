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
            if(!($orderBy == "Media_Voti DESC" && $row['Media_Voti'] == null)){
                $films[] = new Film($this->conn, $row['Id']);
            }
        }

        return $films;
    }
}

?>