<?php

class Emozione
{
    private int $id;
    private string $denominazione;

    //funzione che restituisce tutte le emozioni
    public static function getAll($conn): array
    {
        $query = "SELECT Id FROM Emozione";
        $result = $conn->query($query);
        $emozioni = [];

        while ($row = $result->fetch_assoc()) {
            $emozioni[] = new Emozione($conn, $row['Id']);
        }

        return $emozioni;
    }

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

    // stessa logica di caricaEmozioniTop() in film:
    // carica la top 3 di emozioni più selezionate, MA se ce ne sono di pari le aggiunge comunque senza contare il limite di 3
    // non si può utilizzare getEmozioniTop() per ogni film all'interno di questo metodo perché sarebbe troppo pesante
    public function getNFilm(mysqli $conn): int
    {
        $filmEmozioni = [];

        // Step 1: Ottieni i conteggi emozioni per ogni film
        $query = "
        SELECT id_film, id_emozione, COUNT(*) as count_emozione
        FROM Recensione
        GROUP BY id_film, id_emozione
    ";

        $result = $conn->query($query);
        if (!$result)
            return 0;

        while ($row = $result->fetch_assoc()) {
            $id_film = (int) $row['id_film'];
            $id_emozione = (int) $row['id_emozione'];
            $count = (int) $row['count_emozione'];

            $filmEmozioni[$id_film][] = ['id_emozione' => $id_emozione, 'count' => $count];
        }

        $filmCount = 0;

        foreach ($filmEmozioni as $emozioni) {
            usort($emozioni, function ($a, $b) {
                return $b['count'] - $a['count']; // decrescente
            });

            // Trova la soglia minima (3° posto)
            $soglia = isset($emozioni[2]) ? $emozioni[2]['count'] : 0;

            foreach ($emozioni as $e) {
                if ($e['count'] >= $soglia && $e['id_emozione'] === (int) $this->id) {
                    $filmCount++;
                    break;
                }
            }
        }

        return $filmCount;
    }
}
?>