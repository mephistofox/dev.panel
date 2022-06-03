<?

    $TITLE = "Касса";
    $HEAD .= "
    <link rel = 'stylesheet' type = 'text/css' href = '".$SERVER."templates/admin/css/cash.css?ddddddddddddddddd' />
    <link rel = 'stylesheet' type = 'text/css' href = '".$SERVER."templates/admin/css/transactions.css?dddddd' />";
    $HEAD .= "";

    $BASE = "
        <div id = 'cash'></div>
    ";

    $SCRIPT .= "cashStart();";




?>