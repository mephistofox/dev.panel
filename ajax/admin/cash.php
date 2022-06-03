<?php

    require "../../settings.php";
    require "../../functions.php";

    proof();

    if($_POST["methodName"] == "cashStart"){      // Загрузка стартовая операций
        //$TEXT = file_get_contents("../../templates/admin/temp/cash/transaction_list.html");
        $TEXT = "";
        if(TYPE == 1){
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT open, id, code FROM base ORDER BY id LIMIT 1"));

            if(isset($_COOKIE["CURRENT_BASE"])) $base = $_COOKIE["CURRENT_BASE"]; else $base = 0;
            if($base == 0) $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, id, open FROM base ORDER BY id LIMIT 1"));
            else $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, id, open FROM base WHERE id = '$base'"));
            $BASES = "
                <div class = 'select_base' style = 'margin-left: 59px; margin-bottom: 20px;'><div class = 'select' style = 'width: 70px;' id = 'bases'>
                    <arrow></arrow>
                    <headline>".$temp["code"]."</headline>
                    <input type = 'hidden' id = 'bases_hidden' value = '".$temp["id"]."' />
            ";
            $base = $temp["id"];
            $sql = mysqli_query($CONNECTION, "SELECT code, id FROM base");
            while($data = mysqli_fetch_array($sql)){
                $BASES .= "<div onClick = 'cashBaseChange(".$data["id"].");'>".$data["code"]."</div>";
            }
            $BASES .= "</div></div>";
            if($temp["open"] == 0) $TEXT = file_get_contents("../../templates/admin/temp/cash/cash_default.html");
            else $TEXT = file_get_contents("../../templates/admin/temp/cash/cash_open.html");
        }
        if(TYPE == 3 || ($root[10] == 1) && TYPE != 1){
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT open, cashier FROM base WHERE id = ".BASE));
            if($data["open"] == 0){
                $TEXT = file_get_contents("../../templates/admin/temp/cash/cash_default.html");
            }
            else{
                if($data["cashier"] == ID){
                    $TEXT = file_get_contents("../../templates/admin/temp/cash/cash_open.html");
                }
                else echo "Касса уже открыта. Другим.";
            }
            $BASES = "";
            $base = BASE;
        }
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname, base FROM user WHERE id = ".ID));
        $NAME = $data["name"]." ".$data["surname"];
        if($data["base"] == 0) $BASE = "";
        else{
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, name FROM base WHERE id = ".$data["base"]));
            $BASE = "<circle style = 'background: #".$temp["color"]."'></circle>".$temp["name"];
        }
        $TEXT = str_replace("%NAME%", $NAME, $TEXT);
        $TEXT = str_replace("%BASE%", $BASE, $TEXT);
        $TEXT = str_replace("%DATE%", currentDateMonth(), $TEXT);
        $TEXT = str_replace("%BASES%", $BASES, $TEXT);

        echo $TEXT.$SEP.$base;
    }
    if($_POST["methodName"] == "cashSearch"){      // Загрузка сделок
        $base = clean($_POST["base"]);
        $date_1 = strtotime(date("d")."-".date("m")."-".date("Y"));
        //$date_1 = mktime(0, 0, 0, date("m"), date("d"), date("Y")) + date("Z");
        $date_2 = $date_1 + 24*3600;

        if($base == 0){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM user WHERE id = ".ID));
            $base = $temp["base"];
        }

        $sql_text = "SELECT * FROM transactions WHERE id > 0 AND date >= $date_1 AND date <= $date_2 AND base = $base";
        $sql_text_2 = " AND date >= $date_1 AND date <= $date_2 AND base = $base";

        $PRICE_PROD = 0;
        $PRICE_SERV = 0;

        $T_LIST = "";
        //echo $sql_text;
        file_put_contents('../../logs/query.txt', $sql_text);
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $func = "onClick = 'windowTransactionView(".$data["id"].");'";
            $T_LIST .= "<div ".$func." class = 'cash_body_list_item'>"; //onClick = 'windowTireView(".$data["id"].");

            $sale = $data["sale"];
            if(!empty($sale)){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, skidka_percent, oplata FROM sale WHERE number = '$sale'"));
                $sale = $temp["id"];
                $skidka = round((100-$temp["skidka_percent"])/100, 4);
                if($temp["oplata"] == 2) $koef = 1.02; else $koef = 1;
                $sql_2 = mysqli_query($CONNECTION, "SELECT * FROM sale_product WHERE sale = '$sale'");
                while($temp = mysqli_fetch_array($sql_2)){

                    $p_id = $temp["p_id"];
                    $p_type = $temp["p_type"];
                    $p_param = $temp["p_param"];
                    $p_count = $temp["count"];
                    switch($p_type){
                        case 1: $a = "tire"; break;
                        case 2: $a = "disk"; break;
                        case 3: $a = "product"; break;
                        case 4: $a = "service"; break;
                    }
                    $temp_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM $a WHERE id = '$p_id'"));

                }
                if($p_type < 4){
                    /*file_put_contents('../../logs/errors.txt', $PRICE_PROD, FILE_APPEND);
                    file_put_contents('../../logs/errors.txt', '+'.(int)($temp_2["price_sale"]*$p_count*$skidka*$koef), FILE_APPEND);*/
                    $PRICE_PROD = $PRICE_PROD + (int)($temp_2["price_sale"]*$p_count*$skidka*$koef);
                    /*file_put_contents('../../logs/errors.txt', '=>'.$PRICE_PROD."[end]\r\n", FILE_APPEND);
                    file_put_contents('../../logs/prods.txt', $a.'=>'.$p_id."[end]\r\n", FILE_APPEND);*/
                }
                else{
                    if($p_param == 0) $PRICE_SERV += $temp_2["price_1"]*$p_count*$skidka*$koef;
                    else $PRICE_SERV += $temp_2["price_".$p_param]*$p_count*$skidka*$koef;
                }
            }
            //file_put_contents('../../logs/query.txt', "SELECT id, skidka_percent, oplata FROM sale WHERE number = '$sale'", FILE_APPEND);
            /*
             * Выслеживание ошибочных кусков кода
             * */




            switch($data["type"]){
                case 1: $type = "Приход"; break;
                case 2: $type = "Списание"; break;
                default: $type = "Приход";
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

            switch($data["type"]){
                case 1: $number = "P".$data["number"]; break;
                case 2: $number = ""; break;
                default: $number = "P".$data["number"];
            }

            switch($data["type"]){
                case 1: $price_style = "cash_item_green"; break;
                case 2: $price_style = "cash_item_red"; break;
                default: $price_style = "cash_item_green";
            }


            $T_LIST .= "<div ".$func." class = 'cash_item' style = 'width: 88px;'>".$number."</div>";
            $T_LIST .= "<div ".$func." class = 'cash_item' style = 'width: 104px;'>".$type."</div>";
            $T_LIST .= "<div ".$func." class = 'cash_item' style = 'width: 88px;' >".date("H:i", $data["date"])."</div>";
            $T_LIST .= "<div ".$func." class = 'cash_item text_overflow' style = 'width: 244px;'>".$client."</div>";
            $T_LIST .= "<div ".$func." class = 'cash_item' style = 'width: 117px;'>".$oplata."</div>";
            $T_LIST .= "<div ".$func." class = 'cash_item ".$price_style."' style = 'width: 109px; text-align: right;'>".getPriceTroyki($data["summa"])."</div>";
            $T_LIST .= "</div><br>";
        }

        $T_RIGHT = file_get_contents("../../templates/admin/temp/cash/cash_right.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(summa) FROM transactions WHERE type = 1".$sql_text_2));
        $T_RIGHT = str_replace("%POSTUP%", getPriceTroyki($data[0]), $T_RIGHT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(summa) FROM transactions WHERE type = 1 AND oplata < 2".$sql_text_2));
        $NAL = $data[0];
        $T_RIGHT = str_replace("%NAL%", getPriceTroyki($data[0]), $T_RIGHT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(summa) FROM transactions WHERE type = 1 AND oplata > 1".$sql_text_2));
        $T_RIGHT = str_replace("%BEZNAL%", getPriceTroyki($data[0]), $T_RIGHT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(summa) FROM transactions WHERE type = 2".$sql_text_2));
        $SPIS = $data[0];
        $T_RIGHT = str_replace("%SPIS%", getPriceTroyki($data[0]), $T_RIGHT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT razmen_start FROM cash WHERE base = '$base' AND status = 0"));
        $RAZMEN = $data["razmen_start"];
        $T_RIGHT = str_replace("%RAZMEN%", getPriceTroyki($RAZMEN), $T_RIGHT);

        $CASH = $RAZMEN + $NAL - $SPIS;
        $T_RIGHT = str_replace("%CASH%", getPriceTroyki($CASH), $T_RIGHT);

        $T_RIGHT = str_replace("%RADIO%", radio("radio_1", 1, "платежи", "radio", "cashRightOsnovOpen(1);")."&nbsp;&nbsp;&nbsp;".radio("radio_2", 0, "основания", "radio", "cashRightOsnovOpen(2);"), $T_RIGHT);

        $T_RIGHT = str_replace("%PRODUCTS%", getPriceTroyki($PRICE_PROD), $T_RIGHT);
        $T_RIGHT = str_replace("%SERVICES%", getPriceTroyki($PRICE_SERV), $T_RIGHT);

        echo $T_LIST.$SEP.$T_RIGHT;
    }
    if($_POST["methodName"] == "cashViewHead"){      // Загрузка шапки транзакции
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT number FROM cash WHERE id = '$id'"));
        echo $data["number"];
    }
    if($_POST["methodName"] == "cashViewBody"){      // Загрузка тела транзакции
        $id = clean($_POST["id"]);
        $TEXT = file_get_contents("../../templates/admin/temp/cash/transaction_view.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM cash WHERE id = '$id'"));

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
            default: $BEZNAL = "Оплата наличными";
        }
        $TEXT = str_replace("%BEZNAL%", $BEZNAL, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "cashOpen"){      // Открытие кассы
        $razmen = clean($_POST["razmen"]);
        $pass = clean($_POST["pass"]);
        $base = clean($_POST["base"]);
        $pass = md5($pass.$SALT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM user WHERE id = ".ID));
        if($pass == $data["pass"]){
            if($base == 0) $base = $data["base"];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT time_1, time_2 FROM base WHERE id = '$base'"));
            $mas = explode(":", $data["time_1"]);
            $h_1 = (int)$mas[0];
            $m_1  = (int)$mas[1];
            $mas = explode(":", $data["time_2"]);
            $h_2 = (int)$mas[0];
            $m_2  = (int)$mas[1];
            $time = time();
            $h = (int)date("H", $time);
            $m = (int)date("i", $time);
            $t_1 = $h_1*60+$m_1-60;
            $t_2 = $h_2*60+$m_2;
            $t = $h*60+$m;
            if($t >= $t_1 && $t < $t_2){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM cash WHERE user = '".ID."' AND base = '$base' AND status = 0"));
                if($data["id"] > 0) echo 1;
                else{
                    mysqli_query($CONNECTION, "INSERT INTO cash (base, user, time_start, razmen_start) VALUES ('$base', '".ID."', '$time', '$razmen')");
                    mysqli_query($CONNECTION, "UPDATE base SET open = 1, cashier = '".ID."' WHERE id = '$base'");
                    echo 1;
                }
            }
            else echo -2;


        }
        else echo -1;
    }
    if($_POST["methodName"] == "cashTime"){      // Время открытия кассы
        if(isset($_COOKIE["CURRENT_BASE"])) $base = $_COOKIE["CURRENT_BASE"];
        else{
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM user WHERE id = ".ID));
            $base = $data["base"];
        }
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT time_1 FROM base WHERE id = '$base'"));
        $mas = explode(":", $data["time_1"]);
        $h_1 = (int)$mas[0];
        $m_1  = (int)$mas[1];
        $h_1--;
        if($h_1 < 10) $h_1 = "0".$h_1;
        if($m_1 < 10) $m_1 = "0".$m_1;
        $time = $h_1.":".$m_1;
        echo $time;

    }
    if($_POST["methodName"] == "cashSaleProof"){      // Проверка наличия сделки
        $barcode = clean($_POST["barcode"]);
        if(strlen($barcode) == 12) {
            $barcode = substr_replace($barcode, "", -1);
            $barcode = substr($barcode, 3);
        }
        if($barcode[0] == "P") $barcode = substr($barcode, 1);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM sale WHERE number LIKE '%$barcode' LIMIT 1"));
        if($data["id"] > 0) echo $data["id"];
        else echo 0;
        //echo $barcode;
    }
    if($_POST["methodName"] == "cashDownAdd"){     // Возвращает шаблон списания из кассы с кассиром
        $TEXT = file_get_contents("../../templates/admin/temp/cash/cash_down.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname, base FROM user WHERE id = ".ID));
        $NAME = $data["name"]." ".$data["surname"];
        if($data["base"] == 0) $BASE = "";
        else{
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, name FROM base WHERE id = ".$data["base"]));
            $BASE = "<circle style = 'background: #".$temp["color"]."'></circle>".$temp["name"];
        }
        $TEXT = str_replace("%NAME%", $NAME, $TEXT);
        $TEXT = str_replace("%BASE%", $BASE, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "cashDownAdd2"){    // Создание списания из кассы
        $summa = clean($_POST["summa"]);
        $reason = clean($_POST["reason"]);
        $base = clean($_POST["base"]);
        $pass = clean($_POST["pass"]);
        $pass = md5($pass.$SALT);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM user WHERE id = ".ID));
        if($pass == $data["pass"]){
            //if(isset($_COOKIE["CURRENT_BASE"])) $base = $_COOKIE["CURRENT_BASE"]; else $base = $data["base"];

            $date = time();
            $year = date("y", $date);
            $month = (int)date("m", $date);
            $day = date("d", $date);
            if($month < 10) $month = "0".$month;
            if($day < 10) $day = "0".$day;
            $number = $year.$month.$day;
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM transactions WHERE number LIKE '$number%'"));
            $count = $data[0];
            $count++;
            $count = getRight4Number($count);
            $number .= $count;

            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM product_param WHERE type = 8 AND value = '$reason'"));
            if(!isset($data["id"])) mysqli_query($CONNECTION, "INSERT INTO product_param (value, type, status) VALUES ('$reason', '8', '2')");

            mysqli_query($CONNECTION, "INSERT INTO transactions
                (number, base, date, cashier, summa, type, oplata,  reason) VALUES
                ('$number', '$base', '".time()."', '".ID."', '$summa', '2', '1',  '$reason')");
            echo mysqli_error($CONNECTION)." ".$number;
        }
        else echo 0;
    }
    if($_POST["methodName"] == "cashCloseLoad"){    // Загрузка окно закрытия кассы
        $TEXT = file_get_contents("../../templates/admin/temp/cash/cash_close.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname, base FROM user WHERE id = ".ID));
        $TEXT = str_replace("%CASHIER%", $data["name"]." ".$data["surname"], $TEXT);
        if(isset($_COOKIE["CURRENT_BASE"])) $base = $_COOKIE["CURRENT_BASE"]; else $base = $data["base"];

        $date_1 = mktime(0, 0, 0, date("m"), date("d"), date("Y")) + date("Z");
        $date_2 = $date_1 + 24*3600;

        $sql_text_2 = " AND date >= $date_1 AND date <= $date_2 AND base = $base";

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(summa) FROM transactions WHERE type = 1 AND oplata < 2".$sql_text_2));
        $TEXT = str_replace("%NAL%", getPriceTroyki($data[0]), $TEXT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(summa) FROM transactions WHERE type = 1 AND oplata > 1".$sql_text_2));
        $TEXT = str_replace("%BEZNAL%", getPriceTroyki($data[0]), $TEXT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT time_start FROM cash WHERE base = '$base' AND status = 0"));
        $TEXT = str_replace("%OPEN%", date("H:i", $data["time_start"]), $TEXT);
        $duration = time() - $data["time_start"];
        $h = (int)($duration/3600);
        $duration -= $h*3600;
        $m = (int)($duration/60);
        if($h < 10) $h = "0".$h;
        if($m < 10) $m = "0".$m;
        $TEXT = str_replace("%DURATION%", $h.":".$m, $TEXT);
        $duration = date("H", $data["time_start"])*3600+date("i", $data["time_start"])*60;
        $TEXT = str_replace("%START%", $duration, $TEXT);
        $TEXT = str_replace("%SKACH%", checkbox("scach", 1, "Скачать отчет за день (xls)"), $TEXT);


        echo $TEXT;
    }
    if($_POST["methodName"] == "cashClose"){    // Закрытие кассы
        $razmen = clean($_POST["razmen"]);
        $pass = clean($_POST["pass"]);
        $base = clean($_POST["base"]);
        $pass = md5($pass.$SALT);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM user WHERE id = ".ID));
        if($pass == $data["pass"]){
            //if($base == 0) $base = $data["base"];

            mysqli_query($CONNECTION, "UPDATE base SET open = 0, cashier = 0 WHERE id = '$base'");
            mysqli_query($CONNECTION, "UPDATE cash SET time_end = '".time()."', razmen_end = '$razmen', status = 1 WHERE base = '$base' AND status = 0");

            echo 1;
        }
        else echo 0;
    }






?>