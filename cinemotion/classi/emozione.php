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

    public static function getColorVariants(): array
    {
        return [
            "Felicità" => [
                "light" => "var(--emotion-happy-light)",
                "base" => "var(--emotion-happy)",
                "dark" => "var(--emotion-happy-dark)"
            ],
            "Tristezza" => [
                "light" => "var(--emotion-sad-light)",
                "base" => "var(--emotion-sad)",
                "dark" => "var(--emotion-sad-dark)"
            ],
            "Rabbia" => [
                "light" => "var(--emotion-angry-light)",
                "base" => "var(--emotion-angry)",
                "dark" => "var(--emotion-angry-dark)"
            ],
            "Paura" => [
                "light" => "var(--emotion-scared-light)",
                "base" => "var(--emotion-scared)",
                "dark" => "var(--emotion-scared-dark)"
            ],
            "Sorpresa" => [
                "light" => "var(--emotion-surprised-light)",
                "base" => "var(--emotion-surprised)",
                "dark" => "var(--emotion-surprised-dark)"
            ],
            "Disgusto" => [
                "light" => "var(--emotion-disgusted-light)",
                "base" => "var(--emotion-disgusted)",
                "dark" => "var(--emotion-disgusted-dark)"
            ],
            "Speranza" => [
                "light" => "var(--emotion-hopeful-light)",
                "base" => "var(--emotion-hopeful)",
                "dark" => "var(--emotion-hopeful-dark)"
            ],
            "Ansia" => [
                "light" => "var(--emotion-anxious-light)",
                "base" => "var(--emotion-anxious)",
                "dark" => "var(--emotion-anxious-dark)"
            ],
            "Serenità" => [
                "light" => "var(--emotion-serene-light)",
                "base" => "var(--emotion-serene)",
                "dark" => "var(--emotion-serene-dark)"
            ],
            "Frustrazione" => [
                "light" => "var(--emotion-frustrated-light)",
                "base" => "var(--emotion-frustrated)",
                "dark" => "var(--emotion-frustrated-dark)"
            ],
            "Nostalgia" => [
                "light" => "var(--emotion-nostalgic-light)",
                "base" => "var(--emotion-nostalgic)",
                "dark" => "var(--emotion-nostalgic-dark)"
            ],
            "Confusione" => [
                "light" => "var(--emotion-confused-light)",
                "base" => "var(--emotion-confused)",
                "dark" => "var(--emotion-confused-dark)"
            ],
            "Tensione" => [
                "light" => "var(--emotion-tense-light)",
                "base" => "var(--emotion-tense)",
                "dark" => "var(--emotion-tense-dark)"
            ],
            "Sconforto" => [
                "light" => "var(--emotion-uncomfortable-light)",
                "base" => "var(--emotion-uncomfortable)",
                "dark" => "var(--emotion-uncomfortable-dark)"
            ],
            "Soddisfazione" => [
                "light" => "var(--emotion-satisfied-light)",
                "base" => "var(--emotion-satisfied)",
                "dark" => "var(--emotion-satisfied-dark)"
            ],
            "Interesse" => [
                "light" => "var(--emotion-interested-light)",
                "base" => "var(--emotion-interested)",
                "dark" => "var(--emotion-interested-dark)"
            ],
            "Noia" => [
                "light" => "var(--emotion-bored-light)",
                "base" => "var(--emotion-bored)",
                "dark" => "var(--emotion-bored-dark)"
            ]
        ];
    }

    public function getColorVariant(string $variant = "base"): string
    {
        $colors = self::getColorVariants();
        return $colors[$this->denominazione][$variant] ?? "inherit";
    }
}
?>