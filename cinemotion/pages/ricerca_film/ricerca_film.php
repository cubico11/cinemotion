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

    <div id="container-filtri">
        <!-- Form per i filtri - il form fa il submit al cambiamento delle select -->
        <form method="get" class="filtri-ricerca" id="form-filtri">
            <input type="hidden" name="ricerca" value="<?= htmlspecialchars($_GET['ricerca'] ?? '') ?>">


            <!-- Filtri -->
            <div id="filtri">
                <div id="filtro-genere">
                    <label for="genere">Filtra per genere:</label>
                    <select name="genere" id="genere" onchange="this.form.submit()">
                        <option value="">-- Tutti i generi --</option>
                        <?php
                        $generi = Genere::getTuttiConConteggio($conn);
                        foreach ($generi  as $genere) {
                            $selected = (isset($_GET['genere']) && $_GET['genere'] == $genere['id']) ? 'selected' : '';
                            echo "<option value='". $genere['id'] ."' $selected>". $genere['denominazione'] ." (". $genere['numeroFilm'] .")</option>";
                        }
                        ?>
                    </select>
                </div>
                <div id="filtro-emozione">
                    <label for="emozione">Filtra per emozione:</label>
                    <select name="emozione" onchange="this.form.submit()">
                        <option value="">-- Tutte le emozioni --</option>
                        <?php
                        require_once '../../classi/emozione.php';
                        $emozioniDisponibili = Emozione::getAll($conn);
                        foreach ($emozioniDisponibili as $emo) {
                            $id = $emo->getId();
                            $denom = $emo->getDenominazione();
                            $color = $emo->getColorVariant("light");
                            $selected = (isset($_GET['emozione']) && $_GET['emozione'] == $id) ? 'selected' : '';
                            $count = $emo->getNFilm($conn);
                            //TODO: il colore non viene applicato all'opzione selezionata
                            echo "<option value='$id' style='color: $color' $selected>$denom ($count)</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Ordinamento -->
            <div id="ordinamento">
                <label for="ordinamento">Ordina per:</label>
                <select name="ordinamento" id="ordinamento" onchange="this.form.submit()">
                    <?php
                    $opzioni = [
                        "voti_desc" => "Media voti (decrescente)",
                        "voti_asc" => "Media voti (crescente)",
                        "recensioni_desc" => "Numero recensioni (decrescente)",
                        "recensioni_asc" => "Numero recensioni (crescente)",
                        "data_desc" => "Data di uscita (dal più recente)",
                        "data_asc" => "Data di uscita (dal più vecchio)",
                        "titolo_asc" => "Titolo (A-Z)"
                    ];

                    $ordinamentoSelezionato = $_GET['ordinamento'] ?? '';
                    foreach ($opzioni as $val => $label) {
                        $selected = ($ordinamentoSelezionato === $val) ? "selected" : "";
                        echo "<option value=\"$val\" $selected>$label</option>";
                    }
                    ?>
                </select>
            </div>
        </form>
    </div>


    <div class="ricerca-film">
        <?php
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            //titolo cercato
            $ricerca = isset($_GET['ricerca']) ? test_input($_GET['ricerca']) : null;

            //filtro del genere
            $idGenere = isset($_GET['genere']) && $_GET['genere'] !== '' ? intval($_GET['genere']) : null;
            //filtro dell'emozione
            $idEmozione = isset($_GET['emozione']) && $_GET['emozione'] !== '' ? intval($_GET['emozione']) : null;

            //ordinamento
            $ordinamento = $_GET['ordinamento'] ?? '';

            $filmTrovati = $repo->cercaFilmPerTitolo($ricerca, $idGenere, $idEmozione, $ordinamento);

            if (empty($filmTrovati)) {
                echo "<h1>Nessun film trovato";
                echo "</h1>";
            } else {
                echo "<h1> Trovati " . count($filmTrovati) . " film</h1><div class=\"found-films\">";

                foreach ($filmTrovati as $film) {
                    echo $film->renderCard("../../pages/dettaglio_film");
                }

                echo "</div>";
            }
        }
        ?>
    </div>
</body>

</html>