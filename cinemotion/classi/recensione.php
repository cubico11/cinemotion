<?php
class Recensione
{
    private int $id;
    private string $data;
    private int $voto;
    private string $testo;
    private string $idUtente;
    private int $idFilm;

    public function __construct($id, $data, $voto, $testo, $idUtente, $idFilm)
    {
        $this->id = $id;
        $this->data = $data;
        $this->voto = $voto;
        $this->testo = $testo;
        $this->idUtente = $idUtente;
        $this->idFilm = $idFilm;
    }

    // Getter
    public function getId(): int
    {
        return $this->id;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getVoto(): int
    {
        return $this->voto;
    }

    public function getTesto(): string
    {
        return $this->testo;
    }

    public function getIdUtente(): string
    {
        return $this->idUtente;
    }

    public function getIdFilm(): int
    {
        return $this->idFilm;
    }

    // Setter
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function setVoto(int $voto): void
    {
        $this->voto = $voto;
    }

    public function setTesto(string $testo): void
    {
        $this->testo = $testo;
    }

    public function setUtente(string $utente): void
    {
        $this->utente = $utente;
    }

    public function setIdFilm(int $idFilm): void
    {
        $this->idFilm = $idFilm;
    }

    public function __toString(): string
    {
        return "
            <div class='review'>
                <p><strong>" . htmlspecialchars($this->idUtente) . "</strong>&ensp;
                <em>" . htmlspecialchars($this->data) . "</em></p>
                <strong>" . generaStelle($this->voto) ."</strong>
                <p>" . nl2br(htmlspecialchars($this->testo)) . "</p>
            </div>";
    }
}
?>