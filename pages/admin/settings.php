<?php

    $TITLE = "Настройки";
    $HEAD .= "
    <link rel = 'stylesheet' type = 'text/css' href = '".$SERVER."templates/admin/css/settings.css?fdddd' />";
    $HEAD .= "
    <script src = '%SERVER%templates/admin/script/settings.js'></script>";

    $BASE = "
        <div id = 'settings_col_1' class = 'settings_col settings_col_thin'></div>
        <div id = 'settings_col_2' class = 'settings_col'></div>
        <div id = 'settings_col_3' class = 'settings_col'></div>
        <div id = 'settings_col_4' class = 'settings_col'></div>
    ";

    $SCRIPT .= "settingsStart('".$catC."', '".$catD."', '".$catE."');";




?>