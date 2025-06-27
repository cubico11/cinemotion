<?php
class Genere
{
    private int $id;
    private string $denominazione;

    public function __construct(mysqli $conn, int $id)
    {
        $id = intval($id); // sicurezza base
        $query = "SELECT * FROM Genere WHERE Id = $id";
        $result = $conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            $this->id = $row['Id'];
            $this->denominazione = $row['Denominazione'];
        } else {
            throw new Exception("Genere con ID $id non trovato");
        }
    }

    public static function getTuttiConConteggio(mysqli $conn): array
    {
        $query = "SELECT g.Id, g.Denominazione, COUNT(a.Id_Film) AS NumeroFilm
                  FROM Genere g
                  LEFT JOIN Appartiene a ON g.Id = a.Id_Genere
                  GROUP BY g.Id, g.Denominazione
                  ORDER BY g.Denominazione";

        $result = $conn->query($query);
        $generi = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Per ogni genere, puoi creare un array con le informazioni
                $generi[] = [
                    'id' => intval($row['Id']),
                    'denominazione' => $row['Denominazione'],
                    'numeroFilm' => intval($row['NumeroFilm'])
                ];
            }
        }

        return $generi;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDenominazione(): string
    {
        return $this->denominazione;
    }

    public function getNFilm(mysqli $conn): int
    {
        $id = intval($this->id);

        $query = "SELECT COUNT(*) AS n FROM Appartiene WHERE Id_Genere = $id";
        $result = $conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            return intval($row['n']);
        }

        return 0;
    }


    public function __toString(): string
    {
        return $this->denominazione;
    }
}
?>