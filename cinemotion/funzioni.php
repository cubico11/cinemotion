<?php
function generaStelle($n): string
{
    if(is_null($n)){
        return "N/A";
    }

    $stelle = "";

    // Converti da scala 10 a scala 5
    $voto5 = $n / 2;

    $intero = floor($voto5);
    $decimale = $voto5 - $intero;

    // Stelle piene
    for ($i = 0; $i < $intero; $i++) {
        $stelle .= "★";
    }

    // Mezza stella se necessario
    if ($decimale >= 0.25 && $decimale < 0.75) {
        $stelle .= "⯨";
        $intero++;
    } elseif ($decimale >= 0.75) {
        $stelle .= "★";
        $intero++;
    }

    // Stelle vuote rimanenti
    for ($i = $intero; $i < 5; $i++) {
        $stelle .= "☆";
    }

    return $stelle;
}

// funzione che restituisce true se l'username passatogli come paramentro è lo stesso dell'utente loggato
function isThisUserLogged(string $user): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['username']) && strcasecmp($user, $_SESSION['username']) === 0;
}

function echoHeader($baseurl, $conn){
    echo "<header>".
        //l'immagine linka alla home solo se non si è nella home
        (($baseurl != "") ? "<a href=\"$baseurl\">" : "") . "<div class=\"logo\"><img src=\"".$baseurl."img/logo_text.png\"></div>" . (($baseurl != "") ? "</a>" : "");
    if (!isset($_SESSION['username'])) {
        echo "<div> <a href=\"".$baseurl."pages/login/pagina_login.php\"><button class=\"login-btn\">Login</button></a> <a href=\"".$baseurl."pages/register/pagina_register.php\"><button class=\"register-btn\">Registrati</button></a> </div>";
    }
    else{
        $utente = Utente::fromUsername($conn, $_SESSION['username']);

        echo "<div class=\"user-info\"> <a href=\"".$baseurl."logout.php\"><button class=\"logout-btn\">Logout</button></a>" . $utente->getUsername() . "</div>";
    }
    echo "</header>";
}
?>