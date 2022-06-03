<?php
    $TEXT = "";

    $OTL = "";
    if(isset($_COOKIE["prod"])){
        $mas = explode("X", $_COOKIE["prod"]);
        $count = count($mas) - 1;
        $OTL = $count;
    }
    echo $OTL;
?>