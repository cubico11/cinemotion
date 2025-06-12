<?php
require_once '../../funzioni_connessione.php';
require_once '../../classi/film.php';

if (!isset($_GET['id'])) {
    die("ID film mancante.");
}

$id_film = intval($_GET['id']);

try {
    $film = new Film($conn, $id_film);
} catch (Exception $e) {
    die($e->getMessage());
}
?>