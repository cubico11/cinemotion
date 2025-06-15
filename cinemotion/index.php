<?php
require_once 'funzioni_connessione.php';
require_once 'classi/film.php';
require_once 'classi/recensione.php';
require_once 'classi/filmrepository.php';
require_once 'funzioni.php';
session_start();

$repo = new FilmRepository($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stile.css">
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <title>CinEmotions</title>
</head>

<body>
    <?php echoHeader("", $conn); ?>

    <div class="film-list" id="popular-films">
        <h1>Film migliori</h1>
        <div class='film-grid'>
            <?php
            $popolari = $repo->getFilm("Media_Voti DESC");
            foreach ($popolari as $film) {
                echo $film->renderCard("pages/dettaglio_film");
            }
            ?>
        </div>
    </div>

    <div class="film-list" id="new-films">
        <h1>Film nuovi</h1>
        <div class='film-grid'>
            <?php
            $nuovi = $repo->getFilm("Film.Data_Uscita DESC");
            foreach ($nuovi as $film) {
                echo $film->renderCard("pages/dettaglio_film");
            }
            ?>
        </div>
    </div>

    <div class="film-list" id="most-reviewed-films">
        <h1>Film più discussi</h1>
        <div class='film-grid'>
            <?php
            $famosi = $repo->getFilm("Numero_Recensioni DESC");
            foreach ($famosi as $film) {
                echo $film->renderCard("pages/dettaglio_film");
            }
            ?>
        </div>
    </div>

    <footer></footer>
    <?php $conn->close(); ?>
</body>

</html>