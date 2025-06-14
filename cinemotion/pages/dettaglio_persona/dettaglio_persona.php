<?php
include 'funzioni_dettaglio_persona.php';
include '../../funzioni.php';
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($persona->getNomeCompleto()); ?></title>
    <link rel="stylesheet" href="../../stile.css">
    <link rel="icon" href="../../img/logo.png" type="image/x-icon">
</head>

<body onload="attivaPrimoBottone()">
    <div class="bg-image"></div>
    <?php echoHeader("../../"); ?>

    <main>
        <div class="person-detail">
            <img class="person-img" src="data:image/jpeg;base64,<?php echo $persona->getImmagineBase64(); ?>"
                alt="Poster">
            <div class="person-info">
                <h1><?php echo htmlspecialchars($persona->getNomeCompleto()); ?></h1>
                <p><strong>Nazionalità:</strong> <?php echo $persona->getTestoNazionalita(); ?></p>
                <p><strong>Data nascita:</strong> <?php echo $persona->getDataNascitaTesto(); ?></p>
            </div>
        </div>

        <div id="toggle-buttons" class="toggle-buttons">
            <?php
            if ($persona->getNFilmByRuolo($conn, "Attore") > 0) {
                echo "<button id='btn-attore' onclick='showSection(\"attore\", this)'>Film da attore</button>";
            }
            if ($persona->getNFilmByRuolo($conn, "Regista") > 0) {
                echo "<button id='btn-regista' onclick='showSection(\"regista\", this)'>Film da regista</button>";
            }
            if ($persona->getNFilmByRuolo($conn, "Sceneggiatore") > 0) {
                echo "<button id='btn-sceneggiatore' onclick='showSection(\"sceneggiatore\", this)'>Film da sceneggiatore</button>";
            }
            ?>
        </div>

        <div class="person-films-list">
            <?php
            if ($persona->getNFilmByRuolo($conn, "Attore") > 0) {
                echo "<div id='attore' class='toggle-section' style='display: none;'>
                    <div class='film-grid'>" .
                    $persona->printFilmByRuolo($conn, 'Attore', '../dettaglio_film') .
                    "</div>
                </div>";
            }

            if ($persona->getNFilmByRuolo($conn, "Regista") > 0) {
                echo "<div id='regista' class='toggle-section' style='display: none;'>
                    <div class='film-grid'>" .
                    $persona->printFilmByRuolo($conn, 'Regista', '../dettaglio_film') .
                    "</div>
                </div>";
            }

            if ($persona->getNFilmByRuolo($conn, "Sceneggiatore") > 0) {
                echo "<div id='sceneggiatore' class='toggle-section' style='display: none;'>
                    <div class='film-grid'>" .
                    $persona->printFilmByRuolo($conn, 'Sceneggiatore', '../dettaglio_film') .
                    "</div>
                </div>";
            }
            ?>
        </div>
    </main>

    <footer></footer>
</body>
<script>
    function attivaPrimoBottone() {
        <?php
        //attiva il primo bottone tra i tre (attore, regista o sceneggiatore)
        if ($persona->getNFilmByRuolo($conn, "Attore") == 0 && $persona->getNFilmByRuolo($conn, "Regista") == 0) {
            echo "document.getElementById(\"sceneggiatore\").style.display = '';
                document.getElementById(\"btn-sceneggiatore\").classList.add('active');";
        } else if ($persona->getNFilmByRuolo($conn, "Attore") == 0) {
            echo "document.getElementById(\"regista\").style.display = '';
                document.getElementById(\"btn-regista\").classList.add('active');";
        } else {
            echo "document.getElementById(\"attore\").style.display = '';
                document.getElementById(\"btn-attore\").classList.add('active');";
        }
        ?>

    }

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