<?php
include '../../funzioni.php';
session_start();

if (isset($_SESSION['username'])) {
    header("Location: ../../");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../stile.css">
    <link rel="icon" href="../../img/logo.png" type="image/x-icon">
    <title>Registrazione - CinEmotions</title>
</head>

<body>
    <?php echoHeader("../../", $conn); ?>

    <div class="container-register">
        <h2>Registrazione</h2>
        <form action="register.php" method="POST" class="form">
            <input type="text" name="username" placeholder="Username" required /><br>
            <input type="email" name="email" placeholder="Email" required /><br>
            <input type="password" name="password" placeholder="Password" required /><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required /><br>
            <button type="submit">Registrati</button>
        </form>
        <p class="light">Già registrato? <a href="../login/pagina_login.php">Effettua il login</a>.</p>
        <?php
        if (isset($_GET['error'])) {
            echo "<div class='error'>" . htmlspecialchars($_GET['error']) . "</div>";
        }
        if (isset($_GET['success'])) {
            echo "<div class='success'>" . htmlspecialchars($_GET['success']) . "</div>";
        }
        ?>
    </div>
</body>

</html>