<?php
require_once '../../funzioni_connessione.php';

session_start();

$username = test_input($_POST['username'] ?? '');
$email = test_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($username === '' || $email === '' || $password === '' || $confirm_password === '') {
    header("Location: pagina_register.php?error=Riempi tutti i campi.");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: pagina_register.php?error=Indirizzo email non valido.");
    exit();
}

if ($password !== $confirm_password) {
    header("Location: pagina_register.php?error=Le password non combaciano.");
    exit();
}

// Check if username or email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    header("Location: pagina_register.php?error=Nome utente o indirizzo email già utilizzati.");
    exit();
}
$stmt->close();

// Insert new user
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashed);

if ($stmt->execute()) {
    $_SESSION['username'] = $username;
    header("Location: ../../index.php");
} else {
    header("Location: pagina_register.php?error=Registrazione fallita, riprova.");
}
$stmt->close();
$conn->close();
exit();