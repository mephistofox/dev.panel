<?php

    require "../../settings.php";
    require "../../functions.php";

    proof(); 

    if($_POST["methodName"] == "transactionsStart"){      // Загрузка стартовая операций
        $TEXT = file_get_contents("../../templates/admin/temp/transactions/transaction_list.html");

        $TEXT = str_replace("%HEAD%", rootAndSortHead($CONNECTION, ID, 7, $SEP), $TEXT);

        $BASES = "<div class = 'transactions_head_bases' id = 'transactions_head_bases_1'><item data = '-1' class = 'active' onClick = 'transactionsSearch(8, \"-1\");transactionsBaseChange(this);'>Все</item>";
        $sql = mysqli_query($CONNECTION, "SELECT code, color, id FROM base");
        while($data = mysqli_fetch_array($sql)){
            $BASES .= "<item data = '".$data["id"]."' onClick = 'transactionsSearch(8, \"".$data["id"]."\");transactionsBaseChange(this);'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</item>";
        }
        $BASES .= "</div>";

        $TEXT = str_replace("%BASES%", $BASES, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "transactionsSearch"){      // Загрузка сделок
        $number = clean($_POST["number"]);
        $date = clean($_POST["date"]);
        $summa = clean($_POST["summa"]);
        $type = clean($_POST["type"]);
        $cashier = clean($_POST["cashier"]);
        $oplata = clean($_POST["oplata"]);
        $client = clean($_POST["client"]);

        $date_1 = clean($_POST["date_1"]);
        $date_2 = clean($_POST["date_2"]);
        $base = clean($_POST["base"]);

        if($date_1 != "0") $date_1 = strtotime($date_1) - 3*3600;
        if($date_2 != "0") $date_2 = strtotime($date_2) + 24*3600 - 3*3600;

        $sql_text = "SELECT * FROM transactions WHERE id > 0 ";
        if($number != "") $sql_text .= "AND number LIKE '%$number%' ";
        if($client != "-1") $sql_text .= "AND client = '$client' ";
        if($type != "-1") $sql_text .= "AND type = '$type' ";
        if($cashier != "-1") $sql_text .= "AND cashier = '$cashier' ";
        if($oplata != "-1") $sql_text .= "AND oplata = '$oplata' ";
        if($base != "-1") $sql_text .= "AND base = '$base' ";
        if($date_1 != "0") $sql_text .= "AND date >= $date_1 ";
        if($date_2 != "0") $sql_text .= "AND date <= $date_2 ";

        if($date == 1) $sql_text .= "ORDER BY date ";
        if($date == 2) $sql_text .= "ORDER BY date DESC ";
        if($summa == 1) $sql_text .= "ORDER BY summa ";
        if($summa == 2) $sql_text .= "ORDER BY summa DESC ";;

        $data = rootAndSort($CONNECTION, ID, 7, $SEP);
        $mas = explode("XXX", $data);
        $root = $mas[0];
        $sort = $mas[1];
        $count = $mas[2];
        $sort = explode($SEP, $sort);

        $T_LIST = "";
        //echo $sql_text;
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $func = "onClick = 'windowTransactionView(".$data["id"].");'";
            $T_LIST .= "<div ".$func." class = 'transactions_body_list_item'>"; //onClick = 'windowTireView(".$data["id"].");

            switch($data["type"]){
                case 1: $type = "Прием оплаты"; break;
                case 2: $type = "Списание"; break;
                default: $type = "Прием оплаты";
            }

            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = ".$data["cashier"]));
            $cashier = $temp["surname"]." ".mb_substr($temp["name"], 0, 1, 'UTF-8').".";

            switch($data["oplata"]){
                case 1: $oplata = "Наличные"; break;
                case 2: $oplata = "Карта"; break;
                case 3: $oplata = "На расчетный счет"; break;
                case 4: $oplata = "Перевод на карту"; break;
                case 5: $oplata = "Карта"; break;
                default: $oplata = "Наличные";
            }

            if($data["sale"] != 0) $sale = "P".$data["sale"]; else $sale = "";

            if($data["type"] == 1){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM client WHERE id = ".$data["client"]));
                $client = $temp["name"];
            }
            else $client = $data["reason"];


            if($root[ 0] == 1) $mas[ 0] = "<div ".$func." class = 'transactions_item' style = 'width: 103px;'>K".$data["number"]."</div>";
            if($root[ 1] == 1) $mas[ 1] = "<div ".$func." class = 'transactions_item' style = 'width: 131px;' >".date("d.m.Y H:i", $data["date"])."</div>";
            if($root[ 2] == 1) $mas[ 2] = "<div ".$func." class = 'transactions_item' style = 'width: 131px; text-align: right;'>".getPriceTroyki($data["summa"])."</div>";
            if($root[ 3] == 1) $mas[ 3] = "<div ".$func." class = 'transactions_item' style = 'width: 176px;'>".$type."</div>";
            if($root[ 4] == 1) $mas[ 4] = "<div ".$func." class = 'transactions_item' style = 'width: 145px;'>".$cashier."</div>";
            if($root[ 5] == 1) $mas[ 5] = "<div ".$func." class = 'transactions_item' style = 'width: 176px;'>".$oplata."</div>";
            if($root[ 6] == 1) $mas[ 6] = "<div ".$func." class = 'transactions_item' style = 'width: 167px;'>".$sale."</div>";
            if($root[ 7] == 1) $mas[ 7] = "<div ".$func." class = 'transactions_item' style = 'width: 145px;'>".$client."</div>";

            for($i = 1; $i < $count*2; $i++){
                if($i%2 == 1){
                    $num = $sort[$i];
                    if($sort[$i+1] == 1) $T_LIST .= $mas[$num];
                }
            }
            $T_LIST .= "</div><br>";
        }

        echo $T_LIST;
    }
    if($_POST["methodName"] == "transactionsViewHead"){      // Загрузка шапки транзакции
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT number FROM transactions WHERE id = '$id'"));
        echo $data["number"];
    }
    if($_POST["methodName"] == "transactionsViewBody"){      // Загрузка тела транзакции
        $id = clean($_POST["id"]);
        $TEXT = file_get_contents("../../templates/admin/temp/transactions/transaction_view.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM transactions WHERE id = '$id'"));

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = ".$data["cashier"]));
        $cashier = $temp["surname"]." ".mb_substr($temp["name"], 0, 1, 'UTF-8').".";
        $TEXT = str_replace("%CASHIER%", $cashier, $TEXT);

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM base WHERE id = ".$data["base"]));
        $TEXT = str_replace("%BASE%", $temp["name"], $TEXT);

        switch($data["type"]){
            case 1: $type = "Прием оплаты"; break;
            case 2: $type = "Списание"; break;
            default: $type = "Прием оплаты";
        }
        $TEXT = str_replace("%TYPE%", $type, $TEXT);

        if($data["sale"] != 0) $sale = "P".$data["sale"]; else $sale = "";
        $TEXT = str_replace("%SALE%", $sale, $TEXT);

        if($data["type"] == 1){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM client WHERE id = ".$data["client"]));
            $client = $temp["name"];
        }
        else $client = $data["reason"];
        $TEXT = str_replace("%CLIENT%", $client, $TEXT);

        $TEXT = str_replace("%DATE%", date("d.m.Y H:i", $data["date"]), $TEXT);
        $TEXT = str_replace("%PRICE%", getPriceTroyki($data["summa"]), $TEXT);

        switch($data["type"]){
            case 1: $class = "tv_price_arrow_green"; break;
            case 2: $class = "tv_price_arrow_red"; break;
            default: $class = "tv_price_arrow_green";
        }
        $TEXT = str_replace("%ARROW%", $class, $TEXT);

        switch($data["oplata"]){
            case 1: $BEZNAL = "Оплата наличными"; break;
            case 2: $BEZNAL = "<img src = '".$SERVER."templates/img/visa.png'/>"; break;
            case 3: $BEZNAL = "Расчетный счет"; break;
            case 4: $BEZNAL = "Перевод на карту"; break;
            case 5: $BEZNAL = "<img src = '".$SERVER."templates/img/visa.png'/>"; break;
            default: $BEZNAL = "Оплата наличными";
        }
        $TEXT = str_replace("%BEZNAL%", $BEZNAL, $TEXT);

        echo $TEXT;
    }
?>