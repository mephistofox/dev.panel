<?php

    $TITLE = "Товары";
    $HEAD .= "
    <link rel = 'stylesheet' type = 'text/css' href = '".$SERVER."templates/admin/css/products.css?fDdd' />";
    $HEAD .= "
    <script src = '%SERVER%templates/admin/script/products.js'></script>";

    $BASE = "
        <div id = 'products'></div>
    ";

    $SCRIPT .= "productsStart();";




?>