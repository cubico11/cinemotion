<?php
class Persona {
    private $id;
    private $nome;
    private $cognome;
    private $immagine;
    private $data_nascita;
    private $nazionalita = [];
    private $ruoliPerFilm = []; // film_id => ruolo

    public function __construct($id, $nome, $cognome, $immagine, $data_nascita, $nazionalita = []) {
        $this->id = $id;
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->immagine = $immagine;
        $this->data_nascita = $data_nascita;
        $this->nazionalita = $nazionalita;
    }

    public function aggiungiNazionalita(string $naz) {
        if (!in_array($naz, $this->nazionalita)) {
            $this->nazionalita[] = $naz;
        }
    }

    public function setRuoloNelFilm(int $filmId, string $ruolo) {
        $this->ruoliPerFilm[$filmId] = $ruolo;
    }

    public function getRuoloNelFilm(int $filmId): ?string {
        return $this->ruoliPerFilm[$filmId] ?? null;
    }

    public function getNomeCompleto() {
        return $this->nome . ' ' . $this->cognome;
    }

    public function getImmagineBase64() {
        return base64_encode($this->immagine);
    }

    public function getNazionalita() {
        return implode(', ', $this->nazionalita);
    }

    public function __toString() {
        return "
        <div class='persona'>
            <img src='data:image/jpeg;base64," . $this->getImmagineBase64() . "' alt='Attore'>
            <div class='persona-info'>
                <strong>" . htmlspecialchars($this->getNomeCompleto()) . "</strong><br>
            </div>
        </div>";
    }
}
?>
