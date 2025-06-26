<?php

class Emozione
{
    private int $id;
    private string $denominazione;

    public function __construct(mysqli $conn, int $id)
    {
        $this->id = $id;

        $query = "SELECT * FROM Emozione WHERE Id = $this->id";
        $result = $conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            $this->denominazione = $row['Denominazione'];
        } else {
            throw new Exception("Emozione non trovata");
        }
    }

    // Getter
    public function getId(): int
    {
        return $this->id;
    }

    public function getDenominazione(): string
    {
        return $this->denominazione;
    }

    // Setter
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setDenominazione(string $denominazione): void
    {
        $this->denominazione = $denominazione;
    }
}
?>