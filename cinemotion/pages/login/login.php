<?php
session_start();
require_once '../../funzioni_connessione.php';

$username = test_input($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: pagina_login.php?error=Please fill in all fields.");
    exit();
}

$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($hashed);
    $stmt->fetch();
    if (password_verify($password, $hashed)) {
        $_SESSION['username'] = $username;
        header("Location: ../../");
        exit();
    }
}
header("Location: pagina_login.php?error=Password o nome utente non validi.");
exit();