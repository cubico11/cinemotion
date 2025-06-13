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

<body>
    <div class="bg-image"></div>
    <header>
        <div class="logo"><a href="../../"><img src="../../img/logo_text.png"></div></a>
        <div class="profile-icon"></div>
    </header>

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
    </main>

    <footer></footer>
</body>
</html>

<?php $conn->close(); ?>