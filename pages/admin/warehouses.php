<?php

    $TITLE = "Склады";
    $HEAD .= "
    <link rel = 'stylesheet' type = 'text/css' href = '".$SERVER."templates/admin/css/warehouses.css?ddddddddddd' />
    <link rel = 'stylesheet' type = 'text/css' href = '".$SERVER."templates/admin/css/settings.css?d' />";
    $HEAD .= "
    <script src = '%SERVER%templates/admin/script/warehouses.js'></script>
    <script src = '%SERVER%templates/admin/script/settings.js'></script>";

    $BASE = "
        <div id = 'warehouses'></div>
    ";

    $SCRIPT .= "warehousesStart();";




?>