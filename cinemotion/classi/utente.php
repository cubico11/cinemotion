<?php

class Utente
{
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $createdAt;
    private ?string $fotoProfilo;
    private bool $isAdmin;

    public function __construct($conn, $id)
    {
        $query = "SELECT * FROM Users WHERE Id = $id";
        $result = $conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            $this->id = $row['Id'];
            $this->username = $row['Username'];
            $this->email = $row['Email'];
            $this->password = $row['Password'];
            $this->createdAt = $row['Created_at'];
            $this->fotoProfilo = $row['Foto_Profilo'] ? base64_encode($row['Foto_Profilo']) : null;
            $this->isAdmin = boolval($row['IsAdmin']);
        } else {
            throw new Exception("Utente non trovato");
        }
    }

    //costruttore con nome utente
    public static function fromUsername(mysqli $conn, string $username): Utente
    {
        $usernameSafe = $conn->real_escape_string($username);
        $query = "SELECT * FROM Users WHERE Username = '$usernameSafe'";
        $result = $conn->query($query);

        if ($result && $row = $result->fetch_assoc()) {
            $utente = new self($conn, $row['Id']); // riusa costruttore principale
            return $utente;
        } else {
            throw new Exception("Utente non trovato con username '$username'");
        }
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getFotoProfilo(): ?string
    {
        return $this->fotoProfilo;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    // Se hai bisogno del base64 pronto per img src
    public function getFotoProfiloImgTag(): string
    {
        if ($this->fotoProfilo) {
            return 'data:image/jpeg;base64,' . $this->fotoProfilo;
        } else {
            return 'img/default_user.png'; // un fallback
        }
    }

    // (Opzionale) Verifica se la password in chiaro combacia con l'hash
    public function verificaPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }
}