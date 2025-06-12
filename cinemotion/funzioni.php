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
?>