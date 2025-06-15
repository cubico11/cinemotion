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

function echoHeader($baseurl, $conn){
    echo "<header>".
        //l'immagine linka alla home solo se non si è nella home
        (($baseurl != "") ? "<a href=\"$baseurl\">" : "") . "<div class=\"logo\"><img src=\"".$baseurl."img/logo_text.png\"></div>" . (($baseurl != "") ? "</a>" : "");
    if (!isset($_SESSION['username'])) {
        echo "<div> <a href=\"".$baseurl."pages/login/pagina_login.php\"><button class=\"login-btn\">Login</button></a> <a href=\"".$baseurl."pages/register/pagina_register.php\"><button class=\"register-btn\">Registrati</button></a> </div>";
    }
    else{
        $result = $conn->query("SELECT Username FROM users WHERE Username = \"" . $_SESSION['username'] . "\"");
        if ($result && $row = $result->fetch_assoc()) {
            $username = $row['Username'];
            
            echo "<div class=\"user-info\"> <a href=\"".$baseurl."logout.php\"><button class=\"logout-btn\">Logout</button></a>" . $username . "</div>";
        }
    }
    echo "</header>";
}
?>