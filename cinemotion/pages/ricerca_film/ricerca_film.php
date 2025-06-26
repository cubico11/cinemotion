<?php
require_once '../../classi/film.php';
require_once '../../funzioni_connessione.php';
require_once '../../classi/filmrepository.php';
session_start();

$repo = new FilmRepository($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../stile.css">
    <link rel="icon" href="../../img/logo.png" type="image/x-icon">
    <title>CinEmotion - Ricerca</title>
</head>

<body>
    <?php echoHeader("../../", $conn); ?>

    <div class="ricerca-film">
        <?php
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $ricerca = test_input($_GET['ricerca']);

            if ($repo->cercaFilmPerTitolo($ricerca) == null) {
                echo "<h1>Nessun film trovato con \"$ricerca\" nel titolo.";
            } else {
                if ($ricerca == null) {
                    echo "<h1>Tutti i film disponibili</h1>
                    <div class=\"found-films\">";
                } else {
                    echo "<h1>Ricerca per: \"$ricerca\"</h1>
                    <div class=\"found-films\">";
                }

                $repo->stampaFilmPerTitolo($ricerca, "../../pages/dettaglio_film");
                echo "</div>";
            }
        }
        ?>
    </div>
</body>

</html>