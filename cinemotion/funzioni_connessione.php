<?php
//importa il file config
require_once 'config.php';
global $conn;

//funzione per testare l'input
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

//Crea connessione
$conn = new mysqli($servername, $username, $password, $db);

//Controlla connection
if ($conn->connect_error) {
  die("Connessione fallita: " . $conn->connect_error . "<br><br>");
}
?>