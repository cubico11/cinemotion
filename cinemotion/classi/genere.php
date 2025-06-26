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

    public function getId(): int
    {
        return $this->id;
    }

    public function getDenominazione(): string
    {
        return $this->denominazione;
    }

    public function __toString(): string
    {
        return $this->denominazione;
    }
}
?>