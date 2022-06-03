<?php

    $TITLE = "Операции";
    $HEAD .= "
    <link rel = 'stylesheet' type = 'text/css' href = '".$SERVER."templates/admin/css/transactions.css?ddddddda' />";
    $HEAD .= "
    <script src = '%SERVER%templates/admin/script/transactions.js'></script>";

    $BASE = "
        <div id = 'transactions'></div>
    ";

    $SCRIPT .= "transactionsStart();";




?>