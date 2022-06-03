<?php

    $TITLE = "Движения";
    $HEAD .= "";
    $HEAD .= "";

    $BASE = "
        <div id = 'movements'></div>
    ";

    if($catC != ""){
        $SCRIPT .= "movementsStart('".$catC."');";
    }
    else{
        $SCRIPT .= "movementsStart();";
    }






?>