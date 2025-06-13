<?php
require_once '../../funzioni_connessione.php';
require_once '../../classi/persona.php';

if (!isset($_GET['id'])) {
    die("ID persona mancante.");
}

$id_persona = intval($_GET['id']);

try {
    $persona = new Persona($conn, $id_persona);
} catch (Exception $e) {
    die($e->getMessage());
}
?>