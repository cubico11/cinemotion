<?php
require_once 'utente.php';
require_once 'emozione.php';

class Recensione
{
    private int $id;
    private string $data;
    private int $voto;
    private string $testo;
    private Utente $utente;
    private int $idFilm;
    private Emozione $emozione;

    public function __construct(mysqli $conn, int $id)
    {
        $this->id = $id;

        $query = "SELECT * FROM Recensione WHERE Id = $this->id";
        $result = $conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            $this->data = $row['Data'];
            $this->voto = (int) $row['Voto'];
            $this->testo = $row['Testo'];
            $this->idFilm = (int) $row['Id_Film'];
            $this->utente = new Utente($conn, $row['Id_Utente']);
            $this->emozione = new Emozione($conn, $row['Id_Emozione']);
        } else {
            throw new Exception("Recensione non trovata");
        }
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

    public function getUtente(): Utente
    {
        return $this->utente;
    }

    public function getIdFilm(): int
    {
        return $this->idFilm;
    }

    public function getEmozione(): Emozione
    {
        return $this->emozione;
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

    public function setUtente(Utente $utente): void
    {
        $this->utente = $utente;
    }

    public function setIdFilm(int $idFilm): void
    {
        $this->idFilm = $idFilm;
    }

    public function setEmozione(Emozione $emozione): void
    {
        $this->emozione = $emozione;
    }

    public function __toString(): string
    {
        global $conn;
        $msg = "
            <div class='review'>
                <p class='dati-recensione'><strong class=\"nome-account\">" . htmlspecialchars($this->utente->getUsername()) . "</strong>&ensp;
                <em>" . htmlspecialchars($this->data) . "</em></p>
                <strong>" . generaStelle($this->voto) . "</strong>&emsp;
                <strong>" . $this->emozione->getDenominazione() . "</strong>" ;
                $msg .= ($this->testo != "") ? "<hr>" : "";
                $msg .= "<p class='testo-recensione'>" . nl2br(htmlspecialchars($this->testo)) . "</p>";

                if(isThisUserLogged($this->utente->getUsername()) || isThisUserAdmin($_SESSION['username'], $conn)) {
                    $msg .= "
                    <br>
                    <form method='POST' onsubmit=\"return confirm('Sei sicuro di voler eliminare questa recensione?');\" style='display:inline;'>
                        <input type=\"hidden\" name=\"action\" value=\"elimina_recensione\">
                        <input type='hidden' name='elimina_recensione' value='1'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($this->id) . "'>
                        <input type='hidden' name='id_utente' value='" . htmlspecialchars($this->utente->getId()) . "'>
                        <button type='submit'>Elimina</button>
                    </form>
                ";
                }
            
        $msg .= "</div>";

        return $msg;
    }
}
?>