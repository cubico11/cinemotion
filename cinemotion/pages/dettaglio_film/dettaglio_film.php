<?php
include 'funzioni_dettaglio.php';
include '../../funzioni.php';
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($film->getTitolo()); ?></title>
    <link rel="stylesheet" href="../../stile.css">
    <link rel="icon" href="../../img/logo.png" type="image/x-icon">

    <style>
        <style>body {
            position: relative;
        }

        /* Sfondo con l'immagine sfocata */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vw;
            background-image:
                linear-gradient(to bottom right, rgb(4, 4, 4), rgba(18, 18, 18, 0.41)),
                url('data:image/jpeg;base64,<?php echo $film->getImmagineBase64(); ?>');
            background-size: cover;
            background-repeat: no-repeat;
            filter: blur(150px);
            z-index: -1;
            opacity: 0.5;
        }

        main {
            position: relative;
            z-index: 1;
            backdrop-filter: blur(0px);
            /* opzionale */
        }
    </style>
    </style>
</head>

<body>
    <div class="bg-image"></div>
    <header>
        <div class="logo"><a href="../../"><img src="../../img/logo_text.png"></div></a>
        <div class="profile-icon"></div>
    </header>

    <main>
        <div class="film-detail">
            <img class="film-poster" src="data:image/jpeg;base64,<?php echo $film->getImmagineBase64(); ?>"
                alt="Poster">
            <div class="film-info">
                <h1><?php echo htmlspecialchars($film->getTitolo()); ?></h1>
                <p><strong>Descrizione:</strong></p>
                <div class="film-description">
                    <p><?php echo htmlspecialchars($film->getDescrizione()); ?></p>
                </div>
                <p><strong>Durata:</strong> <?php echo $film->getDurataFormattata(); ?></p>
                <p><strong>Data uscita:</strong> <?php echo $film->getDataUscitaTesto(); ?></p>
            </div>
        </div>
        <div id="toggle-buttons" class="toggle-buttons">
            <button id="btn-reviews" class="active" onclick="showSection('reviews', this)">Recensioni</button>
            <button id="btn-cast" onclick="showSection('cast', this)">Cast</button>
            <button id="btn-registi" onclick="showSection('registi', this)">Registi</button>
            <button id="btn-sceneggiatori" onclick="showSection('sceneggiatori', this)">Sceneggiatori</button>
        </div>
        <div class="toggle-section" id="reviews">
            <?php echo $film->getRecensioni(); ?>
        </div>
        <div id="cast" class="toggle-section staff" style="display: none;">
            <?php echo $film->getPersone("Attore"); ?>
        </div>
        <div id="registi" class="toggle-section staff" style="display: none;">
            <?php echo $film->getPersone("Regista"); ?>
        </div>
        <div id="sceneggiatori" class="toggle-section staff" style="display: none;">
            <?php echo $film->getPersone("Sceneggiatore"); ?>
        </div>
    </main>

    <footer></footer>
</body>
<script>
    function showSection(sectionId, button) {
        // Nasconde tutte le sezioni
        document.querySelectorAll('.toggle-section').forEach(section => {
            section.style.display = 'none';
        });

        // Mostra solo quella selezionata
        document.getElementById(sectionId).style.display = '';

        // Rimuove "active" da tutti i bottoni
        document.querySelectorAll('.toggle-buttons button').forEach(btn => {
            btn.classList.remove('active');
        });

        // Aggiunge "active" al bottone cliccato
        button.classList.add('active');
    }
</script>

</html>

<?php $conn->close(); ?>