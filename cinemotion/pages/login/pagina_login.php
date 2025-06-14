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
    <title>Register - CinEmotions</title>
</head>

<body>
    <?php echoHeader("../../"); ?>
    
    <div class="container-login">
        <h2>Login</h2>
        <form action="login.php" method="POST" class="form">
            <input type="text" name="username" placeholder="Username" required /><br>
            <input type="password" name="password" placeholder="Password" required /><br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="../register/pagina_register.php">Register here</a>.</p>
        <?php
        if (isset($_GET['error'])) {
            echo "<div class='error'>" . htmlspecialchars($_GET['error']) . "</div>";
        }
        ?>
    </div>
</body>

</html>