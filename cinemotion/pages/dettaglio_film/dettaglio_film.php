<?php
require_once 'funzioni_dettaglio_film.php';
require_once '../../funzioni.php';
require_once '../../funzioni_connessione.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['username'])) {
    //azione da eseguire
    $action = $_POST['action'] ?? '';

    //codice per inserire la recensione
    if ($action === 'inserisci_recensione') {
        $voto = isset($_POST['voto']) ? intval($_POST['voto'] * 2) : null;
        $testo = isset($_POST['recensione']) ? trim($_POST['recensione']) : null;
        $id_film = isset(($_GET['id'])) ? intval(($_GET['id'])) : null;

        if ($voto !== null && $id_film !== null) {
            $username = $conn->real_escape_string($_SESSION['username']);
            $query_user = "SELECT Id FROM Users WHERE Username = '$username'";
            $result_user = $conn->query($query_user);

            if ($result_user && $result_user->num_rows === 1) {
                $id_utente = intval($result_user->fetch_assoc()['Id']);

                // Verifica recensione già presente
                $query_check = "SELECT Id FROM Recensione WHERE Id_Film = $id_film AND Id_Utente = $id_utente";
                $result_check = $conn->query($query_check);

                if ($result_check && $result_check->num_rows == 0) {
                    $testo = $conn->real_escape_string($testo);
                    $query_insert = "INSERT INTO Recensione (Data, Voto, Testo, Id_Film, Id_Utente)
                                 VALUES (CURDATE(), $voto, '$testo', $id_film, $id_utente)";

                    if ($conn->query($query_insert)) {
                        header("Location: " . $_SERVER['REQUEST_URI']);
                        echo "<script>alert('Recensione pubblicata con successo!');</script>";
                    } else {
                        echo "<script>alert('Errore durante l\'inserimento della recensione.');</script>";
                    }
                } else {
                    echo "<script>alert('Hai già inserito una recensione per questo film.');</script>";
                }
            } else {
                echo "<script>alert('Utente non valido.');</script>";
            }
        } else {
            echo "<script>alert('Dati mancanti.');</script>";
        }
    }

    //codice per eliminare la recensione
    elseif ($action === 'elimina_recensione') {
        $username = $conn->real_escape_string($_SESSION['username']);
        $id_recensione = intval($_POST['id']);

        // Recupera l'id dell'utente
        $query_user = "SELECT Id FROM Users WHERE Username = '$username'";
        $result_user = $conn->query($query_user);

        if ($result_user && $row = $result_user->fetch_assoc()) {
            $id_utente = intval($row['Id']);

            // Cancella la recensione
            $query_delete = "DELETE FROM Recensione WHERE Id = $id_recensione AND Id_Utente = $id_utente";
            if ($conn->query($query_delete)) {
                header("Location: " . $_SERVER['REQUEST_URI']);
                echo "<script>alert('Recensione eliminata.'); location.reload();</script>";
            } else {
                echo "<script>alert('Errore durante l\'eliminazione.');</script>";
            }
        } else {
            echo "<script>alert('Utente non valido.');</script>";
        }
    }
}

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
        }
    </style>
</head>

<body onload="charcountupdate('')">
    <div class="bg-image"></div>
    <?php echoHeader("../../", $conn); ?>

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
            <button id="btn-reviews" class="active" onclick="showSection('reviews-container', this)">Recensioni</button>
            <button id="btn-cast" onclick="showSection('cast', this)">Cast</button>
            <button id="btn-registi" onclick="showSection('registi', this)">Registi</button>
            <button id="btn-sceneggiatori" onclick="showSection('sceneggiatori', this)">Sceneggiatori</button>
        </div>
        <div class="toggle-section" id="reviews-container">
            <div class='reviews-info'>
                <?php echo $film->getInfoRecensioni(); ?>
            </div>

            <form id="form-recensione" action="" method="POST">
                <input type="hidden" name="action" value="inserisci_recensione">
                <h2>Inserisci una recensione:</h2>
                Voto: <input type="number" name="voto" required min="0,5" step=".5" max="5">
                <textarea name="recensione" id="text-recensione" maxlength="1000"
                    onkeyup="charcountupdate(this.value)"></textarea>
                <div class="charcount-container"><span id="charcount" class="light"></span></div>
                <button type="submit">Pubblica</button>
            </form>

            <div id="reviews">
                <?php echo $film->getRecensioni(); ?>
            </div>
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

    function charcountupdate(str) {
        var lng = str.length;
        document.getElementById("charcount").innerHTML = lng + '/1000';
    }
</script>

</html>

<?php $conn->close(); ?>