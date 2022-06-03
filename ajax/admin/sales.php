<?php

    require "../../settings.php";
    require "../../functions.php";

    proof();

    if($_POST["methodName"] == "salesStart"){      // Загрузка стартовая сделок
        $TEXT = file_get_contents("../../templates/admin/temp/sales/sale_list.html");

        $TEXT = str_replace("%HEAD%", rootAndSortHead($CONNECTION, ID, 6, $SEP), $TEXT);

        $BASES = "<div class = 'sales_head_bases' id = 'sales_head_bases_1'>";
        $BASES .= "<item data = '-1' class = 'active' id = 'base_num_-1' onClick = 'salesSearch(5, \"-1\");salesBaseChange(this);'>Все</item>";
        $sql = mysqli_query($CONNECTION, "SELECT code, color, id FROM base");
        while($data = mysqli_fetch_array($sql)){
            $BASES .= "<item data = '".$data["code"]."' id = 'base_num_".$data["id"]."' onClick = 'salesSearch(5, \"".$data["id"]."\");salesBaseChange(this);'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</item>";
        }
        $BASES .= "</div>";

        $TEXT = str_replace("%BASES%", $BASES, $TEXT);
        
        $OTL = "";
        if(isset($_COOKIE["prod"])){
            $mas = explode("X", $_COOKIE["prod"]);
            $count = count($mas) - 1;
            $OTL = "<div id = 'sales_head_otl'>".$count."</div>";
        }
        $TEXT = str_replace("%OTL%", $OTL, $TEXT);

        echo $TEXT.$SEP.BASE;
    }
    if($_POST["methodName"] == "salesSearch"){      // Загрузка сделок
        $number = clean($_POST["number"]);
        $status = clean($_POST["status"]);
        $poluchenie = clean($_POST["poluchenie"]);
        $date = clean($_POST["date"]);
        $base_sale = clean($_POST["base_sale"]);
        $client = clean($_POST["client"]);
        $cureer = clean($_POST["cureer"]);
        $delivery = clean($_POST["delivery"]);
        $price_purchase = clean($_POST["price_purchase"]);
        $price_sale = clean($_POST["price_sale"]);
        $oplata = clean($_POST["oplata"]);
        $skidka_percent = clean($_POST["skidka_percent"]);
        $skidka_ruble = clean($_POST["skidka_ruble"]);
        $manager = clean($_POST["manager"]);
        $date_1 = clean($_POST["date_1"]);
        $date_2 = clean($_POST["date_2"]);

        if($date_1 != "0") $date_1 = strtotime($date_1);// - 3*3600;
        if($date_2 != "0") $date_2 = strtotime($date_2) + 24*3600;// - 3*3600;

        $sql_text = "SELECT * FROM sale WHERE id > 0 AND status > 0 ";
        if($number != "") $sql_text .= "AND number LIKE '%$number%' ";
        if($client != "") $sql_text .= "AND client_phone LIKE '%$client%' OR client_name LIKE '%$client%'";
        if($status != "-1") $sql_text .= "AND status = '$status' ";
        if($poluchenie != "-1") $sql_text .= "AND poluchenie = '$poluchenie' ";
        if($base_sale != "-1") $sql_text .= "AND base_sale = '$base_sale' ";
        if($cureer != "-1") $sql_text .= "AND cureer = '$cureer' ";
        if($delivery != "-1") $sql_text .= "AND delivery = '$delivery' ";
        if($oplata > 0) $sql_text .= "AND oplata = '$oplata' ";
        if($manager != "-1") $sql_text .= "AND manager = '$manager' ";
        if($date_1 != "0") $sql_text .= "AND date >= $date_1 ";
        if($date_2 != "0") $sql_text .= "AND date <= $date_2 ";

        if($date == 1) $sql_text .= "ORDER BY date ";
        if($date == 2) $sql_text .= "ORDER BY date DESC ";
        if($price_purchase == 1) $sql_text .= "ORDER BY price_purchase ";
        if($price_purchase == 2) $sql_text .= "ORDER BY price_purchase DESC ";
        if($price_sale == 1) $sql_text .= "ORDER BY price_sale ";
        if($price_sale == 2) $sql_text .= "ORDER BY price_sale DESC ";
        if($skidka_percent == 1) $sql_text .= "ORDER BY skidka_percent ";
        if($skidka_percent == 2) $sql_text .= "ORDER BY skidka_percent DESC ";
        if($skidka_ruble == 1) $sql_text .= "ORDER BY skidka_ruble ";
        if($skidka_ruble == 2) $sql_text .= "ORDER BY skidka_ruble DESC ";

        $data = rootAndSort($CONNECTION, ID, 6, $SEP);
        $mas = explode("XXX", $data);
        $root = $mas[0];
        $sort = $mas[1];
        $count = $mas[2];
        $sort = explode($SEP, $sort);

        $SALES_LIST = "";
        $k = 1000;
        //echo $sql_text;
        $count_all = 0;
        $price_purchase_all = 0;
        $price_sale_all = 0;
        $skidka_percent_all = 0;
        $skidka_ruble_all = 0;
        $sql = mysqli_query($CONNECTION, $sql_text);
        $func = "";
        while($data = mysqli_fetch_array($sql)){
            $func = "onClick = 'windowSaleView(".$data["id"].");'";
            $SALES_LIST .= "<div ".$func." class = 'sales_body_list_item'>"; //onClick = 'windowTireView(".$data["id"].");
            $k--;
            $count_all++;

            $status = "статус";
            switch($data["status"]){
                case -1:$status = "<circle style = 'background-color: #000000'></circle>План"; break;
                case 1: $status = "<circle style = 'background-color: #72C2FF'></circle>На сборке"; break;
                case 2: $status = "<circle style = 'background-color: #FFD560'></circle>Бронь"; break;
                case 3: $status = "<circle style = 'background-color: #B7FB70'></circle>Оплачено"; break;
                case 4: $status = "<circle style = 'background-color: #0091FF'></circle>Ждет отправки"; break;
                case 5: $status = "<circle style = 'background-color: #F7B500'></circle>У курьера"; break;
                case 6: $status = "<circle style = 'background-color: #6236FF'></circle>Доставляется"; break;
                case 7: $status = "<circle style = 'background-color: #6DD400'></circle>Получено"; break;
                default: $status = "Упс)";
            }

            $base = $data["base_sale"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, code FROM base WHERE id = '$base'"));
            $base = "<circle style = 'background-color: #".$temp["color"].";'></circle>".$temp["code"];

            $object = "";
            $i = 0;
            $sql_2 = mysqli_query($CONNECTION, "SELECT p_id, p_type FROM sale_product WHERE sale = '".$data["id"]."' GROUP BY barcode");
            while($temp = mysqli_fetch_array($sql_2)){
                if($i > 0) $object .= "; ";
                $p_id = $temp["p_id"];
                switch($temp["p_type"]){
                    case 1: $text = "tire"; $n = "S"; break;
                    case 2: $text = "disk"; $n = "D"; break;
                    case 3: $text = "product"; $n = "T"; break;
                    case 4: $text = "service"; $n = "U"; break;
                    case 5: $text = "season_temp"; $n = "V"; break;
                }
                $temp_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT article FROM $text WHERE id = '$p_id'"));
                $object .= $n."".$temp_2["article"];
                $i++;
            }

            switch($data["oplata"]){
                case 1 : $oplata = "Наличные"; break;
                case 2 : $oplata = "По карте +2%"; break;
                case 3 : $oplata = "На расчетный счет"; break;
                case 4 : $oplata = "Переводом на карту"; break;
                case 5 : $oplata = "По карте (без +2%)"; break;
                default: $oplata = "Не оплачено";
            }

            $movement = "";
            $i = 0;
            $sql_2 = mysqli_query($CONNECTION, "SELECT number FROM movement WHERE sale = '".$data["id"]."'");
            while($temp = mysqli_fetch_array($sql_2)){
                if($i > 0) $movement .= "; ";
                $movement .= $temp["number"];
                $i++;
            }

            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = '".$data["manager"]."'"));
            $manager = $temp["name"]." ".$temp["surname"];

            $delivery = $data["delivery"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM delivery WHERE id = '$delivery'"));
            $delivery = $temp["name"];

            $mas = explode(" ", $data["client_name"]);
            $client = "";
            if(!isset($mas[1])) $client = $mas[0];
            else{
                if(isset($mas[1])) $client = $mas[1]." ".mb_substr($mas[0], 0, 1, 'UTF-8').". ";
                if(isset($mas[2])) $client .= mb_substr($mas[2], 0, 1, 'UTF-8').".";
            }

            $price_purchase_all += $data["price_purchase"];
            $price_sale_all += $data["price_sale"];
            $skidka_percent_all += $data["skidka_percent"];
            $skidka_ruble_all += $data["skidka_ruble"];

            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE sale = '".$data["id"]."' LIMIT 1"));
            if(isset($temp["id"])) $codes = "<div class = 'link_blue_4' onClick = 'windowCodesView(".$data["id"].", 2);'>Коды маркировки</div>"; else $codes = "";

            if($data["payer_1"] != 0){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE id = '".$data["payer_1"]."'"));
                $payer = $temp["name"];
            }
            else $payer = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE priority=1"))["name"];

            if($root[ 0] == 1) $mas[ 0] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 103px;'>P".$data["number"]."</div>";
            if($root[ 1] == 1) $mas[ 1] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 148px;' >".$status."</div>";
            if($root[ 2] == 1) $mas[ 2] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 157px;'>".$data["poluchenie"]."</div>";
            if($root[ 3] == 1) $mas[ 3] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 131px;'>".date("d.m.Y H:i:s", $data["date"])."</div>";
            if($root[ 4] == 1) $mas[ 4] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 167px;' title = '".$data["vydacha"]."'>".$data["vydacha"]."</div>";
            if($root[ 5] == 1) $mas[ 5] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 167px;'>".$base."</div>";
            if($root[ 6] == 1) $mas[ 6] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 167px;'>".$object."</div>";
            if($root[ 7] == 1) $mas[ 7] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 176px;'>".$client."</div>";
            if($root[ 8] == 1) $mas[ 8] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 201px;'>".$data["cureer"]."</div>";
            if($root[ 9] == 1) $mas[ 9] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 138px;'>".$delivery."</div>";
            if($root[10] == 1) $mas[10] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 122px; text-align: right;'>".getPriceTroyki($data["price_purchase"])."</div>";
            if($root[11] == 1) $mas[11] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 136px; text-align: right;'>".getPriceTroyki($data["price_sale"])."</div>";
            if($root[12] == 1) $mas[12] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 176px;'>".$oplata."</div>";
            if($root[13] == 1) $mas[13] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 110px;' title = '".$movement."'>".$movement."</div>";
            if($root[14] == 1) $mas[14] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 122px; text-align: right;'>".$data["skidka_percent"]." %</div>";
            if($root[15] == 1) $mas[15] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 122px; text-align: right;'>".$data["skidka_ruble"]."</div>";
            if($root[16] == 1) $mas[16] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 145px;'>".$manager."</div>";
            if($root[17] == 1) $mas[17] = "<div           class = 'sale_item text_overflow' style = 'width: 136px;'>".$codes."</div>";
            if($root[18] == 1) $mas[18] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 186px;'>".$payer."</div>";

            for($i = 1; $i < $count*2; $i++){
                if($i%2 == 1){
                    $num = $sort[$i];
                    if($sort[$i+1] == 1) $SALES_LIST .= $mas[$num];
                }
            }
            $SALES_LIST .= "</div><br>";
        }
        if($count_all > 0){
            $price_purchase_all = round($price_purchase_all/$count_all);
            $price_sale_all = round($price_sale_all/$count_all);
            $skidka_percent_all = round($skidka_percent_all/$count_all);
            $skidka_ruble_all = round($skidka_ruble_all/$count_all);
        }

        $TEMP = "<div class = 'sales_body_list_item_global'>";

        if($root[ 0] == 1) $mas[ 0] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 103px;'></div>";
        if($root[ 1] == 1) $mas[ 1] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 148px;' ></div>";
        if($root[ 2] == 1) $mas[ 2] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 157px;'></div>";
        if($root[ 3] == 1) $mas[ 3] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 131px;'></div>";
        if($root[ 4] == 1) $mas[ 4] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 167px;'></div>";
        if($root[ 5] == 1) $mas[ 5] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 167px;'></div>";
        if($root[ 6] == 1) $mas[ 6] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 167px;'></div>";
        if($root[ 7] == 1) $mas[ 7] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 176px;'></div>";
        if($root[ 8] == 1) $mas[ 8] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 201px;'></div>";
        if($root[ 9] == 1) $mas[ 9] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 138px;'></div>";
        if($root[10] == 1) $mas[10] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 122px; text-align: right;'><b>".getPriceTroyki($price_purchase_all)."</b></div>";
        if($root[11] == 1) $mas[11] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 136px; text-align: right;'><b>".getPriceTroyki($price_sale_all)."</b></div>";
        if($root[12] == 1) $mas[12] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 176px;'></div>";
        if($root[13] == 1) $mas[13] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 110px;'></div>";
        if($root[14] == 1) $mas[14] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 122px; text-align: right;'><b>".$skidka_percent_all." %</b></div>";
        if($root[15] == 1) $mas[15] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 122px; text-align: right;'><b>".$skidka_ruble_all."</b></div>";
        if($root[16] == 1) $mas[16] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 145px;'></div>";
        if($root[17] == 1) $mas[17] = "<div           class = 'sale_item text_overflow' style = 'width: 136px;'></div>";
        if($root[18] == 1) $mas[18] = "<div ".$func." class = 'sale_item text_overflow' style = 'width: 186px;'></div>";

        for($i = 1; $i < $count*2; $i++){
            if($i%2 == 1){
                $num = $sort[$i];
                if($sort[$i+1] == 1) $TEMP .= $mas[$num];
            }
        }

        $TEMP .= "</div><br>";

        echo $TEMP.$SALES_LIST;
    }
    if($_POST["methodName"] == "salesSaleAddLoad"){      // Загрузка окна добавления продажи
        $contact = clean($_POST["contact"]);
        $BASES = "<div class = 'sales_head_bases' id = 'sales_head_bases_2' style = 'margin-top: 0;' >";
        if(TYPE == 1){
            $sql = mysqli_query($CONNECTION, "SELECT code, color FROM base");
            while($data = mysqli_fetch_array($sql)){
                $BASES .= "<item data = '".$data["code"]."' onClick = 'salesAddBaseChange(this);'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</item>";
            }
        }
        else {
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM user WHERE id = ".ID));
            $base = $temp["base"];
            if($base > 0){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base WHERE id = '$base'"));
                $BASES .= "<item data = '".$data["code"]."' onClick = 'salesAddBaseChange(this);'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</item>";
            }
            else {
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base LIMIT 1"));
                $BASES .= "<item data = '".$data["code"]."' onClick = 'salesAddBaseChange(this);'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</item>";
            }
        }

        $BASES .= "</div>";
        $TEXT = $BASES;
        $TEXT .= file_get_contents("../../templates/admin/temp/sales/sale_add.html");

        $DATE = "
            <div id = 'sa_date_link' onClick = 'salesDateOpen();'>Запланировать</div>
            <div id = 'sa_date_cal'></div>
        ";

        $TEXT = str_replace("%DATE%", $DATE, $TEXT);

        if($contact != 0){
            $client_id = $contact;
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM client WHERE id = '$client_id'"));
            $client_name = $data["name"];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, phone FROM client_contact WHERE cId = '$client_id'"));
            $contact = $data["name"]." (".$data["phone"].")";
        }
        else{
            $client_id = -1;
            $client_name = "Клиент";
            $contact = "";
        }
        $CLIENT = "
            <div class = 'select_base'>
            <div class = 'select' id = 'client' style = 'width: 167px;'>
                <arrow></arrow>
                <headline>".$client_name."</headline>
                <input type = 'hidden' id = 'client_hidden' value = '".$client_id."' />
        ";
        $sql = mysqli_query($CONNECTION, "SELECT id, name FROM client");
        while($data = mysqli_fetch_array($sql)){
           $CLIENT .= "<div data = '".$data["id"]."' onClick = 'contactList2(this);' title = '".$data["name"]."' class = 'text_overflow'>".$data["name"]."</div>";
        }
        $CLIENT .= "</div></div>
            <input type = 'text' style = 'width: 250px;' autocomplete = 'new-password' value = '".$contact."' class = 'input height-23' id = 'client_phone' onKeyUp = 'deleteBorderRed(this);contactList(this);' />
            <list id = 'client_phone_list'></list>";
        $TEXT = str_replace("%CLIENT%", $CLIENT, $TEXT);
        $cureer = "
            <div class = 'select_base'>
            <div class = 'select' style = 'width: 167px;' id = 'cureer'>
                <arrow></arrow>
                <headline>Курьер</headline>
                <input type = 'hidden' id = 'cureer_hidden' value = '-1'>";
        $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type = 5");
        while($data = mysqli_fetch_array($sql)) $cureer .= "<div data = '".$data["id"]."'>".$data["surname"]." ".$data["name"]."</div>";
        $cureer .= "</div></div>";
        $TEXT = str_replace("%CUREER%", $cureer, $TEXT);

        $OTL = "";
        if(isset($_COOKIE["prod"])){
            $mas = explode("X", $_COOKIE["prod"]);
            for($i = 0; $i < count($mas) - 1; $i++){
                $temp = explode("-", $mas[$i]);
                $type = $temp[0];
                $id = $temp[1];
                $temp = explode(".", $id);
                $id = $temp[0];
                $param = $temp[1];
                if(isset($temp[2])) $count = $temp[2]; else $count = 0;
                switch($type){
                    case 1: $table = "tire"; break;
                    case 2: $table = "disk"; break;
                    case 3: $table = "product"; break;
                    case 4: $table = "service"; break;
                    case 5: $table = "season_temp"; break;
                }
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT barcode FROM $table WHERE id = '$id'"));
                $OTL .= $data["barcode"].".".$param.".".$count."%";
            }
            setCookie("prod", $_COOKIE["prod"], time()+100000, "/; samesite=lax");
            // setCookie("prod", "", time()-100000, "/; samesite=lax");

        }

        $PAYER_1 = "
            <div class = 'select' id = 'payer_1' style = 'min-width: 200px;'>
                <arrow></arrow>
                <headline>Выбрать</headline>
                <input type = 'hidden' id = 'payer_1_hidden' value = '-1' />
        ";
        $sql = mysqli_query($CONNECTION, "SELECT id, name, codes FROM payer WHERE status = 1");
        while($data = mysqli_fetch_array($sql)){
           $PAYER_1 .= "<div data = '".$data["id"]."' data_2 = '".$data["codes"]."' onClick = 'salesPayerChange(this);'>".$data["name"]."</div>";
        }
        $PAYER_1 .= "</div>";

        $TEXT = str_replace("%PAYER_1%", $PAYER_1, $TEXT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, name FROM payer WHERE priority = 1"));
        if(isset($data["id"])){
            $payer_id = $data["id"];
            $payer_name = $data["name"];
        }
        else{
            $payer_id = -1;
            $payer_name = "Выбрать";
        }

        $PAYER_2 = "
            <div class = 'select' id = 'payer_2' style = 'min-width: 225px;'>
                <arrow></arrow>
                <headline>".$payer_name."</headline>
                <input type = 'hidden' id = 'payer_2_hidden' value = '".$payer_id."' />
        ";
        $sql = mysqli_query($CONNECTION, "SELECT id, name, codes FROM payer WHERE status = 1 AND rek != ''");
        while($data = mysqli_fetch_array($sql)){
           $PAYER_2 .= "<div data = '".$data["id"]."' data_2 = '".$data["codes"]."'>".$data["name"]."</div>";
        }
        $PAYER_2 .= "</div>";

        $TEXT = str_replace("%PAYER_2%", $PAYER_2, $TEXT);

        echo $TEXT.$SEP.$OTL;
    }
    if($_POST["methodName"] == "salesAddProductAdd"){      // Добавление товара к заказу
        $temp = clean($_POST["barcode"]);
        $temp = explode(".", $temp);
        $barcode = $temp[0];
        $param_0 = $temp[1];
        if(isset($temp[2])) $count = $temp[2]; else $count = 0;
        $base = clean($_POST["base"]);
        $payer = clean($_POST["payer"]);

        switch($barcode[0]){
            case "S": $type = 1; $param = 1; break;
            case "D": $type = 2; $param = 1; break;
            case "T": $type = 3; $param = 1; break;
            case "U": $type = 4; $param = 1; break;
            case "V": $type = 5; $param = 1; break;
            default : $type = 0; $param = 2; break;
        }
        $barcode = str_replace("S", "", $barcode);
        $barcode = str_replace("D", "", $barcode);
        $barcode = str_replace("T", "", $barcode);
        $barcode = str_replace("U", "", $barcode);
        $barcode = str_replace("V", "", $barcode);

        $id = 0;

        if($param == 1){
            $sql = "SELECT id, barcode FROM ";
            switch($type){
                case 1: $sql .= "tire"; break;
                case 2: $sql .= "disk"; break;
                case 3: $sql .= "product"; break;
                case 4: $sql .= "service"; break;
                case 5: $sql .= "season_temp"; break;
            }
            $sql .= " WHERE article = '$barcode'";
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
            if($data["id"] > 0){
                $id = $data["id"];
                $barcode = $data["barcode"];
            }
        }
        if($param == 2){
            $data_1 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM tire WHERE barcode = '$barcode'"));
            $data_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM disk WHERE barcode = '$barcode'"));
            $data_3 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM product WHERE barcode = '$barcode'"));
            $data_4 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM service WHERE barcode = '$barcode'"));
            $data_5 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM season_temp WHERE barcode = '$barcode'"));
            if($data_1["id"] > 0 || $data_2["id"] > 0 || $data_3["id"] > 0 || $data_4["id"] > 0 || $data_5["id"] > 0){
                if($data_1["id"] > 0){
                    $id = $data_1["id"];
                    $type = 1;
                }
                if($data_2["id"] > 0){
                    $id = $data_2["id"];
                    $type = 2;
                }
                if($data_3["id"] > 0){
                    $id = $data_3["id"];
                    $type = 3;
                }
                if($data_4["id"] > 0){
                    $id = $data_4["id"];
                    $type = 4;
                }
                if($data_5["id"] > 0){
                    $id = $data_5["id"];
                    $type = 5;
                }
            }
        }
        if($id > 0){
            //echo $count;
            if($count > 0) echo $barcode.$SEP.getProductLineAndBase($CONNECTION, $id, $type, $base, $param_0, $count, 0, 0, $payer);
            else echo $barcode.$SEP.getProductLineAndBase($CONNECTION, $id, $type, $base, $param_0, 4, 0, 0, $payer);
        }
        else echo -1;
    }
    if($_POST["methodName"] == "salesPayersTires"){      // Отдает список плательщиков, у которых есть этот товар. Иначе убирает товар.
        $temp = clean($_POST["barcode"]);
        $temp = explode(".", $temp);
        $barcode = $temp[0];
        $param_0 = $temp[1];

        switch($barcode[0]){
            case "S": $type = 1; $param = 1; break;
            default : $type = 0; $param = 2; break;
        }
        $barcode = str_replace("S", "", $barcode);

        $id = 0;

        if($param == 1){
            $sql = "SELECT id FROM tire WHERE article = '$barcode'";
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
            if($data["id"] > 0){
                $id = $data["id"];
            }
        }
        if($param == 2){
            $data_1 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM tire WHERE barcode = '$barcode'"));
            $id = $data_1["id"];
        }

        if($id > 0){
            $tire = $id;
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM code WHERE tire = '$tire' AND sale = 0"));
            if($data[0] > 0){
                $i = 0;
                $sql = mysqli_query($CONNECTION, "SELECT payer FROM code WHERE tire = '$tire' AND sale = 0 GROUP BY payer");
                while($data = mysqli_fetch_array($sql)){
                    $m_payer[$i] = $data["payer"];
                    $payer = $data["payer"];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM code WHERE tire = '$tire' AND sale = 0 AND payer = '$payer'"));
                    $m_count[$i] = $temp[0];
                    $i++;
                }
                $count = $i;
                $max = 0;
                $payer = 0;
                for($i = 0; $i < $count; $i++) if($m_count[$i] > $max){
                    $max = $m_count[$i];
                    $payer = $m_payer[$i];
                }

                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE id = '$payer'"));

                $TEXT = "
                    <div class = 'select' id = 'payer_1' style = 'min-width: 200px;'>
                        <arrow></arrow>
                        <headline>".$data["name"]."</headline>
                        <input type = 'hidden' id = 'payer_1_hidden' value = '".$payer."' />
                ";
                for($i = 0; $i < $count; $i++){
                    $payer = $m_payer[$i];
                    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM payer WHERE id = '$payer'"));
                    $TEXT .= "<div data = '".$data["id"]."' data_2 = '".$data["codes"]."' onClick = 'salesPayerChange(this);'>".$data["name"]."</div>";
                }
                $TEXT .= "</div>";
                echo $TEXT;
            }
            else echo -1;
        }
        else echo -1;
    }
    if($_POST["methodName"] == "salesAddPoluch"){      // Загрузка типов доставки в зависимости от выбора
        $param = clean($_POST["param"]);
        $TEXT = "";
        if($param == 1){
            $TEXT = "
                <div class = 'select_base'>
                <div class = 'select' style = 'width: 249px;' id = 'base_sale_param'>
                    <arrow></arrow>
                    <headline>База выдачи</headline>
                    <input type = 'hidden' id = 'base_sale_param_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT color, name, id FROM base");
            while($data = mysqli_fetch_array($sql)) $TEXT .= "<div data = '".$data["id"]."'><circle style = 'background-color: #".$data["color"]."; margin-top: 6px;'></circle>".$data["name"]."</div>";
            $TEXT .= "</div></div>";
        }
        if($param == 3){
            $TEXT = "
                <input type = 'text' class = 'input height-23' style = 'width: 240px;' placeholder = 'Адрес' id = 'address' onKeyUp = 'deleteBorderRed(this);addressList(this);'>
                <list id = 'address_list'></list>
            ";
        }
        if($param == 4){
            $TEXT = "
                <span>ТК</span><div class = 'select_base'><div class = 'select' style = 'width: 221px;' id = 'tk_param'>
                    <arrow></arrow>
                    <headline>Выберите</headline>
                    <input type = 'hidden' id = 'tk_param_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT name, id FROM delivery");
            while($data = mysqli_fetch_array($sql)) $TEXT .= "<div data = '".$data["id"]."'>".$data["name"]."</div>";
            $TEXT .= "</div></div>";
        }
        echo $TEXT;
    }
    if($_POST["methodName"] == "salesSaleAdd"){      // Добавление нового заказа
        $json = $_POST["json"];
        $data = json_decode($json, true);
        $flag = true;

        $base_sale = $data["base"];

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM sale ORDER BY id DESC LIMIT 1"));
        if(isset($temp["id"]))$number = $temp["id"] + 1; else $number = 1;
        $number = getRight8Number($number);

        $current_date = date("d.m.Y", time());
        if($current_date != $data["date_plan"]) $new_status = 1; else $new_status = 1;

        $date_plan = strtotime($data["date_plan"]);

        $delivery = 0;
        $vydacha = "";
        switch($data["poluch"]){
            case 1: $poluch = "Пункт выдачи"; $vydacha = $data["poluch_desc"]; break;
            case 2: $poluch = "В местах хранения"; break;
            case 3: $poluch = "Доставка"; $vydacha = $data["poluch_desc"]; break;
            case 4: $poluch = "Доставка ТК"; $delivery = $data["poluch_desc"]; break;
        }
        if($data["poluch"] == 1){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM base WHERE id = '$vydacha'"));
            $vydacha = $temp["name"];
        }

        if($data["cureer"] != -1){
            $cureer_id = $data["cureer"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = '".$data["cureer"]."'"));
            $cureer = $temp["surname"]." ".$temp["name"];
        }
        else{
            $cureer = "";
            $cureer_id = 0;
        }

        $payer_1 = $data["payer_1"];
        $payer_2 = $data["payer_2"];

        $mas = explode("(", $data["client_contact"]);
        if(isset($mas[1])){
            $client_contact = $mas[1];
            $client_contact = str_replace(")", "", $client_contact);
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, name, phone FROM client_contact WHERE phone = '$client_contact'"));
            if(isset($temp["id"])){
                $client_contact = $temp["id"];
                $client_name = $temp["name"];
                $client_phone = $temp["phone"];

                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '".$data["base"]."'"));
                $base = $temp["id"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = 'SC".$base."'"));
                $SC_id = $temp["id"];

                $time = time();
                if (array_key_exists('inform', $data)) {
                    $info = ($data['inform']) ? $data['inform'] : '';
                } else {
                    $info = '';
                }
                
                mysqli_query($CONNECTION, "INSERT INTO sale
                    (  number,  payer_1,     payer_2,    date_plan,   poluchenie, vydacha,    client,            client_name, client_phone,    cureer, cureer_id,    delivery,   date, base_sale, manager)
                VALUES
                    ('$number', '$payer_1', '$payer_2', '$date_plan', '$poluch', '$vydacha', '$client_contact', '$client_name', '$client_phone', '$cureer', '$cureer_id', '$delivery', '$time', '$base', '".ID."')");

                $id = mysqli_insert_id($CONNECTION);

                $flag = true;
                $mas = $data["mas"];
                $price_purchase = 0;
                $price_sale = 0;
                for($i = 0; $i < count($mas); $i++){
                    $barcode = $mas[$i]["barcode"];
                    $count = $mas[$i]["count"];
                    $param = $mas[$i]["param"];

                    $data_1 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price_purchase, price_sale FROM tire WHERE barcode = '$barcode'"));
                    $data_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price_purchase, price_sale FROM disk WHERE barcode = '$barcode'"));
                    $data_3 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price_purchase, price_sale FROM product WHERE barcode = '$barcode'"));
                    $data_4 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price_1, price_2, price_3 FROM service WHERE barcode = '$barcode'"));
                    $data_5 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price FROM season_temp WHERE barcode = '$barcode'"));

                    if($data_1["id"] > 0 || $data_2["id"] > 0 || $data_3["id"] > 0 || $data_4["id"] > 0 || $data_5["id"] > 0){
                        if($data_1["id"] > 0){
                            $p_id = $data_1["id"];
                            $p_type = 1;
                            $price_purchase += $count * $data_1["price_purchase"];
                            $price_sale += $count * $data_1["price_sale"];
                        }
                        if($data_2["id"] > 0){
                            $p_id = $data_2["id"];
                            $p_type = 2;
                            $price_purchase += $count * $data_2["price_purchase"];
                            $price_sale += $count * $data_2["price_sale"];
                        }
                        if($data_3["id"] > 0){
                            $p_id = $data_3["id"];
                            $p_type = 3;
                            $price_purchase += $count * $data_3["price_purchase"];
                            $price_sale += $count * $data_3["price_sale"];
                        }
                        if($data_4["id"] > 0){
                            $p_id = $data_4["id"];
                            $p_type = 4;
                            if($param == 0) $price_sale += $count * $data_4["price_1"];
                            else $price_sale += $count * $data_4["price_".$param];
                        }
                        if($data_5["id"] > 0){
                            $p_id = $data_5["id"];
                            $p_type = 5;
                            $price_sale += $count * $data_5["price"];
                        }
                    }

                    $storage_mas = $mas[$i]["storage"];
                    if(count($storage_mas) == 0){
                        insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $count, "", $id);
                    }
                    else{
                        $count_need = $count;
                        for($j = 0; $j < count($storage_mas); $j++)if($count_need > 0){
                            $temp_mas = explode(" - ", $storage_mas[$j]);
                            $temp_base = $temp_mas[0];
                            if(isset($temp_mas[1])) $temp_storage = $temp_mas[1]; else $temp_storage = "";

                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$temp_base'"));
                            $temp_base_id = $temp["id"];

                            if($p_type < 3){
                                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$temp_storage'"));
                                $temp_storage_id = $temp["id"];
                                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, count FROM available WHERE barcode = '$barcode' AND storage = '$temp_storage_id'"));
                                if(isset($temp["id"])){
                                    $temp_count = $temp["count"];
                                    if($temp_count >= $count_need){
                                        insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $count_need, $storage_mas[$j], $id);
                                        if($data["base"] != $temp_base){
                                            movementAdd($CONNECTION, $barcode, $count_need, $temp_storage_id, $SC_id, $id);
                                            $flag = false;
                                        }
                                        $count_need = 0;
                                    }
                                    else{
                                        insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $temp_count, $storage_mas[$j], $id);
                                        if($data["base"] != $temp_base){
                                            movementAdd($CONNECTION, $barcode, $temp_count, $temp_storage_id, $SC_id, $id);
                                            $flag = false;
                                        }
                                        $count_need = $count_need - $temp_count;
                                    }
                                }
                            }
                            if($p_type == 3){
                                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, count FROM available WHERE barcode = '$barcode' AND base = '$temp_base_id'"));
                                if(isset($temp["id"])){
                                    $temp_count = $temp["count"];
                                    if($temp_count >= $count_need){
                                        insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $count_need, $storage_mas[$j], $id);
                                        if($data["base"] != $temp_base){
                                            movementAdd($CONNECTION, $barcode, $count_need, $temp_base_id, $base, $id);
                                            $flag = false;
                                        }
                                        $count_need = 0;
                                    }
                                    else{
                                        insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $temp_count, $storage_mas[$j], $id);

                                        if($data["base"] != $temp_base){
                                            movementAdd($CONNECTION, $barcode, $temp_count, $temp_base_id, $base, $id);
                                            $flag = false;
                                        }
                                        $count_need = $count_need - $temp_count;
                                    }
                                }
                            }

                        }
                    }
                }
                if($new_status == 1){
                    //if($flag) $status = 2;
                    //else $status = 1;
                    $status = 1;
                }
                else $status = -1;

                mysqli_query($CONNECTION, "UPDATE sale SET price_purchase = '$price_purchase', price_sale = '$price_sale' WHERE id = '$id'");

                saleStatusChange($CONNECTION, $id, $status);

                if($status == 2){
                    $sql = mysqli_query($CONNECTION, "SELECT p_id, p_type, count, barcode, otkuda FROM sale_product WHERE sale = '$id'");
                    while($data = mysqli_fetch_array($sql)){
                        $p_id = $data["p_id"];
                        $p_type = $data["p_type"];
                        $count = $data["count"];
                        $otkuda = $data["otkuda"];

                        if($p_type < 3){
                            $mas = explode(" - ", $otkuda);
                            $storage = $mas[1];
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$storage'"));
                            $storage = $temp["id"];
                            productMove($CONNECTION, $storage, 0, $p_type, $p_id, $count);
                            storageProductRemove($CONNECTION, $storage, $count);
                        }
                        if($p_type == 3){
                            productMove($CONNECTION, $base, 0, $p_type, $p_id, $count);
                        }
                    }
                }



                //print_r();
                echo "P".$number;
            }
            else echo -1;
        }
        else echo -1;
    }
    if($_POST["methodName"] == "salesSaleViewHead"){      // Загрузка шапки продажи
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT status, number FROM sale WHERE id = '$id'"));
        if(isset($data["number"])){
            $head = "Сделка P".$data["number"];
            switch($data["status"]){
                case -1:$status = "<circle style = 'background-color: #000000'></circle>План"; break;
                case 1: $status = "<circle style = 'background-color: #72C2FF'></circle>На сборке"; break;
                case 2: $status = "<circle style = 'background-color: #FFD560'></circle>Бронь"; break;
                case 3: $status = "<circle style = 'background-color: #B7FB70'></circle>Оплачено"; break;
                case 4: $status = "<circle style = 'background-color: #0091FF'></circle>Ждет отправки"; break;
                case 5: $status = "<circle style = 'background-color: #F7B500'></circle>У курьера"; break;
                case 6: $status = "<circle style = 'background-color: #6236FF'></circle>Доставляется"; break;
                case 7: $status = "<circle style = 'background-color: #6DD400'></circle>Получено"; break;
                default: $status = "Упс)";
            }
            $head .= "<br><span id = 'sale_status'>".$status."</span>";
            $head .= "
                <div id = 'sale_head_bottom'>
                    <item data = '1' class = 'sale_head_bottom_active' onClick = 'saleViewInfoLog(this);'>Инфо</item>
                    |
                    <item data = '2' onClick = 'saleViewInfoLog(this);'>Логи</item>
                </div>";
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM sale_action WHERE sale = '$id' AND status = 3"));
            if(isset($temp["id"])) $param = false; else $param = true;
            if($data["status"] <= 2 || $param){
                $head .= "
                    <div id = 'sale_head_delete' class = 'sale_head_delete_1' onClick = 'salesSaleDeleteStart(".$id.");'></div>
                    <div id = 'sale_head_delete_cancel' onClick = 'salesSaleDeleteCancel();'>Отмена</div><div id = 'sale_head_delete_time'>9</div>";
            }
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE sale = '$id'"));
            if(isset($temp["id"])){
                $head .= "<div class = 'link_blue_4' style = 'display: inline-block;margin-top: 4px;float: right;' onClick = 'windowCodesView2(".$id.");'>Коды</div>";
            }

            echo $head.$SEP.$data["status"];
        }
        else echo -1;
    }
    if($_POST["methodName"] == "salesSaleViewBody"){      // Загрузка тела продажи
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM sale WHERE id = '$id'"));
        $TEXT = file_get_contents("../../templates/admin/temp/sales/sale_view.html");
        $GLOBAL_STATUS = $data["status"];
        $payer_1 = $data["payer_1"];
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE id = '$payer_1'"));
        $TEXT = str_replace("%PAYER_1%", $temp["name"], $TEXT);

        $payer_2 = $data["payer_2"];
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE id = '$payer_2'"));
        $TEXT = str_replace("%PAYER_2%", $temp["name"], $TEXT);

        $TEXT = str_replace("%PRICE%", getPriceTroyki($data["price_sale"])." ₽", $TEXT);
        $TEXT = str_replace("%PRICE_0%", $data["price_sale"], $TEXT);
        $TEXT = str_replace("%SALE_STATUS%", $data["status"], $TEXT);
        $TEXT = str_replace("%_PAYER_1%", $payer_1, $TEXT);
        $TEXT = str_replace("%_PAYER_2%", $payer_2, $TEXT);

        switch($data["poluchenie"]){
            case "Пункт выдачи": $dop = 1; $CUREER_DEFAULT = ""; break;
            case "В местах хранения": $dop = ""; $CUREER_DEFAULT = ""; break;
            case "Доставка": $dop = 3; $CUREER_DEFAULT = "style = 'display: block;'"; break;
            case "Доставка ТК": $dop = 4; $CUREER_DEFAULT = "style = 'display: block;'"; break;
        }

        if($data["cureer"] != ""){
            $CUREER_DEFAULT = "";

            switch($data["status"]){
                case 2: $button = "<div onClick = 'buttonClick(this); salesSaleStatusChange(this);' class = 'button button_green button_extra_small fright'>Товар передан</div>"; break;
                case 4: $button = "<div onClick = 'buttonClick(this); salesSaleStatusChange(this);' class = 'button button_green button_extra_small fright'>Товар передан</div>"; break;
                case 5: $button = "<div onClick = 'buttonClick(this); salesSaleStatusChange(this);' class = 'button button_green button_extra_small fright'>Товар доставлен</div>"; break;
                default: $button = "";
            }
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname, phone FROM user WHERE id = ".$data["cureer_id"]));
            $name = $temp["surname"]."<br>".$temp["name"];
            $phone = $temp["phone"];

            if($data["status"] == 4) $cross = "block"; else $cross = "none";

            $CUREER_2 = "
                <div id = 'sa_cureer2'>
                    <span>Курьер</span>
                    <div id = 'cureer_desc'>
                        <div id = 'cureer_desc_left'>
                            <div id = 'cureer_desc_left_name'>".$name."</div>
                            <div id = 'cureer_desc_left_phone'>".$phone."</div>
                            <div id = 'cureer_desc_left_call'>Позвонить</div>
                        </div>
                        <div id = 'cureer_desc_right'>
                            <div id = 'cureer_desc_right_top'>
                                <cross onClick = 'salesSaleCureerDel();' style = 'display: ".$cross.";'></cross>
                            </div>
                            <div id = 'cureer_desc_right_bottom'>".$button."</div>
                        </div>
                    </div>
                </div>
            ";
        }
        else $CUREER_2 = "";
        $TEXT = str_replace("%CUREER_DEFAULT%", $CUREER_DEFAULT, $TEXT);
        $TEXT = str_replace("%CUREER_2%", $CUREER_2, $TEXT);
        $cureer = "
            <div class = 'select_base'>
            <div class = 'select' style = 'width: 167px;' id = 'cureer'>
                <arrow></arrow>
                <headline>Курьер</headline>
                <input type = 'hidden' id = 'cureer_hidden' value = '-1'>";
        $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type = 5");
        while($data2 = mysqli_fetch_array($sql)) $cureer .= "<div data = '".$data2["id"]."' onClick = 'salesSalesCureerChange(this);'>".$data2["surname"]." ".$data2["name"]."</div>";
        $cureer .= "</div></div>";
        $TEXT = str_replace("%CUREER%", $cureer, $TEXT);

        $dop_text = "";
        if($dop == 1){
            $dop_text = "
                <div class = 'select_base'>
                <div class = 'select' style = 'width: 259px;' id = 'base_sale_param'>
                    <arrow></arrow>
                    <headline>".$data["vydacha"]."</headline>
                    <input type = 'hidden' id = 'base_sale_param_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT color, name, id FROM base");
            while($data_0 = mysqli_fetch_array($sql)) $dop_text .= "<div data = '".$data_0["id"]."'><circle style = 'background-color: #".$data_0["color"]."; margin-top: 6px;'></circle>".$data_0["name"]."</div>";
            $dop_text .= "</div></div>";
        }
        if($dop == 3){
            $dop_text = "
                <input type = 'text' class = 'input height-23' value = '".$data["vydacha"]."' style = 'width: 250px;' placeholder = 'Адрес' id = 'address' onKeyUp = 'deleteBorderRed(this);addressList(this);'>
                <list id = 'address_list'></list>
            ";
        }
        if($dop == 4){
            $data_0 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM delivery WHERE id = '".$data["delivery"]."'"));
            $dop_text = "
                <span>ТК</span><div class = 'select_base'><div class = 'select' style = 'width: 231px;' id = 'tk_param'>
                    <arrow></arrow>
                    <headline>".$data_0["name"]."</headline>
                    <input type = 'hidden' id = 'tk_param_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT name, id FROM delivery");
            while($data_0 = mysqli_fetch_array($sql)) $dop_text .= "<div data = '".$data_0["id"]."'>".$data_0["name"]."</div>";
            $dop_text .= "</div></div>";
        }
          //<div data = '2' onClick = 'salesAddPoluch(2);'>В местах хранения</div>
        $POLUCHENIE = "
            <div class = 'select_base'>
                <div class = 'select' style = 'width: 167px;' id = 'poluch'>
                    <arrow></arrow>
                    <headline>".$data["poluchenie"]."</headline>
                    <input type = 'hidden' id = 'poluch_hidden' value = '-1'>
                    <div data = '1' onClick = 'salesAddPoluch(1);'>Пункт выдачи</div>

                    <div data = '3' onClick = 'salesAddPoluch(3);'>Доставка</div>
                    <div data = '4' onClick = 'salesAddPoluch(4);'>Доставка ТК</div>
                </div>
            </div>
            <div id = 'sa_poluch_dop'>".$dop_text."</div>";

        $TEXT = str_replace("%POLUCHENIE%", $POLUCHENIE, $TEXT);
        $TEXT = str_replace("%ID%", $id, $TEXT);

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT cId, phone FROM client_contact WHERE id = '".$data["client"]."'"));
        $cId = $temp["cId"];
        $client_phone = $temp["phone"];
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, inn FROM client WHERE id = '$cId'"));
        $company = $temp["name"];
        $company_inn = $temp["inn"];
        $CLIENT = "
            <span2>".$company."</span2>&nbsp;&nbsp;&nbsp;&nbsp;
            <span2>ИНН: ".$company_inn."</span2><br>
            ".$data["client_name"]."<br>".$client_phone."&nbsp;&nbsp;<span3>Позвонить</span3>";
        $TEXT = str_replace("%CLIENT%", $CLIENT, $TEXT);
        $INFORM = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT track FROM sale WHERE id = '$id'"))['track'];
        if ($INFORM) {
            $INFORM = '<br><p><b>Дополнительная информация</b></p>'.$INFORM;
        }
        $TEXT = str_replace("%INFORM%", $INFORM, $TEXT);

        $OPLATA = "";
        $OPLATA_COMMENT = "none";
        if($data["status"] > 1){
            $OPLATA = "<div id = 'sw_oplata'><span>Оплата</span>";
            if($data["oplata"] == 0) $variant = "Выбрать";
            else{
                switch($data["oplata"]){
                    case 1: $variant = "Наличные"; $OPLATA_COMMENT .= "none"; break;
                    case 2: $variant = "По карте +2%"; $OPLATA_COMMENT .= "none"; break;
                    case 3: $variant = "На расчетный счет"; $OPLATA_COMMENT .= "block"; break;
                    case 4: $variant = "Переводом на карту"; $OPLATA_COMMENT .= "block"; break;
                    case 5: $variant = "По карте (без +2%)"; $OPLATA_COMMENT .= "block"; break;
                    default: $variant = "Выбрать"; $OPLATA_COMMENT .= "none";
                }
            }
            $OPLATA .= "
                <div class = 'select_base'>
                    <div class = 'select' style = 'width: 167px;' id = 'oplata'>
                        <arrow></arrow>
                        <headline>".$variant."</headline>
                        <input type = 'hidden' id = 'oplata_hidden' value = '".$data["oplata"]."'>
                        <div data = '1' onClick = 'salesSaleSummCalc(1);salesSaleSummSave(1);'>Наличные</div>
                        <div data = '2' onClick = 'salesSaleSummCalc(2);salesSaleSummSave(2);'>По карте +2%</div>
                        <div data = '3' onClick = 'salesSaleSummCalc(3);salesSaleSummSave(3);'>На расчетный счет</div>
                        <div data = '4' onClick = 'salesSaleSummCalc(4);salesSaleSummSave(4);'>Переводом на карту</div>
                        <div data = '5' onClick = 'salesSaleSummCalc(5);salesSaleSummSave(5);'>По карте (без +2%)</div>
                    </div>
                </div>
            </div>";
        }
        $TEXT = str_replace("%OPLATA%", $OPLATA, $TEXT);
        $TEXT = str_replace("%OPLATA_COMMENT%", $OPLATA_COMMENT, $TEXT);
        $TEXT = str_replace("%OPLATA_COMMENT_TEXT%", $data["oplata_comment"], $TEXT);
        $TEXT = str_replace("%SKIDKA_PERCENT%", $data["skidka_percent"], $TEXT);
        $TEXT = str_replace("%SKIDKA_RUBLE%", $data["skidka_ruble"], $TEXT);

        if($GLOBAL_STATUS < 2){
            $PRODUCT = "
            <div id = 'sa_head'>
                <input type = 'text' class = 'input height-28' style = 'width: 268px;' placeholder = 'Штрих-код или артикул' id = 'barcode' onkeyup = 'deleteBorderRed(this); salesBarcodeProof(this);'>
                <barcode></barcode>
                <div onclick = 'salesAddProductAdd();' class = 'button button_green button_extra_small' style = 'float: right; display: none;'>Добавить</div>
            </div>";
        }
        else $PRODUCT = "";

        $sql = mysqli_query($CONNECTION, "SELECT p_id, p_type, p_param, count, otkuda, id FROM sale_product WHERE sale = '$id'");
        while($data = mysqli_fetch_array($sql)){
            if($GLOBAL_STATUS < 2){
                $temp = explode(" - ", $data["otkuda"]);
                $mas = explode($SEP, getProductLineAndBase($CONNECTION, $data["p_id"], $data["p_type"], $temp[0], $data["p_param"], $data["count"], 1, $data["otkuda"]));
                $PRODUCT .= $mas[0];
            }
            else{
                $mas = explode($SEP, getProductLine($CONNECTION, $data["p_id"], $data["p_type"], $data["count"], $data["p_param"], $data["otkuda"], $data["id"]));
                $PRODUCT .= $mas[0];
            }
        }
        $TEXT = str_replace("%PRODUCTS%", $PRODUCT, $TEXT);

        $MOVEMENTS = "";
        $sql = mysqli_query($CONNECTION, "SELECT * FROM movement WHERE sale = '$id'");
        while($data = mysqli_fetch_array($sql)){
            if($data["p_type"] == 1){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM tire WHERE id = '".$data["p_id"]."'"));
                $product_info = $temp["brand"]." ".$temp["model"]." ".$temp["w"]."/".$temp["h"]."R".$temp["r"];
            }
            if($data["p_type"] == 2){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM disk WHERE id = '".$data["p_id"]."'"));
                $product_info = $temp["nomenclature"]." ".$temp["w"]."R".$temp["r"];
            }
            if($data["p_type"] == 3){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM product WHERE id = '".$data["p_id"]."'"));
                $product_info = $temp["name"]." ".$temp["params"];
            }

            if($data["status"] < 1) $top = "<div onClick = 'salesReceiptConfirmation(".$data["id"].", this);' class = 'button button_movements button_green button_extra_small'>Принять</div>";
            else $top = "<div>".date("d.m.Y H:i", $data["date_finish"])."<gal2></gal2></div>";

            $bases = "<number>D".$data["number"]."</number>".$data["otkuda"]."<arrow_base></arrow_base>".$data["kuda"];

            $MOVEMENTS .= "
                <div class = 'sv_move'>
                    <div class = 'sv_move_left'>".$data["count"]." x ".$product_info."<br><article>".$data["article"]."</article></div>
                    <div class = 'sv_move_right'>
                        <div class = 'sv_move_right_top'>".$top."</div>
                        <div class = 'sv_move_right_bottom'>".$bases."</div>
                    </div>
                </div>";
        }
        if($MOVEMENTS != "") $MOVEMENTS = "<span>Перемещения</span>".$MOVEMENTS;
        $TEXT = str_replace("%MOVEMENTS%", $MOVEMENTS, $TEXT);

        $LOGS = "";
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM sale WHERE id = '$id'"));
        $cureer = $data["cureer"];
        $track = $data["track"];
        $sql = mysqli_query($CONNECTION, "SELECT * FROM sale_action WHERE sale = '$id' ORDER BY id DESC");
        while($data = mysqli_fetch_array($sql)){
            switch($data["status"]){
                case -1:$status = "<circle style = 'background-color: #000000'></circle>План"; break;
                case 1: $status = "<circle style = 'background-color: #72C2FF'></circle>На сборке"; break;
                case 2: $status = "<circle style = 'background-color: #FFD560'></circle>Бронь"; break;
                case 3: $status = "<circle style = 'background-color: #B7FB70'></circle>Оплачено"; break;
                case 4: $status = "<circle style = 'background-color: #0091FF'></circle>Ждет отправки"; break;
                case 5: $status = "<circle style = 'background-color: #F7B500'></circle>У курьера"; break;
                case 6: $status = "<circle style = 'background-color: #6236FF'></circle>Доставляется"; break;
                case 7: $status = "<circle style = 'background-color: #6DD400'></circle>Получено"; break;
            }
            $user = $data["user"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT surname, name FROM user WHERE id = '$user'"));
            $user = $temp["surname"]." ".mb_substr($temp["name"], 0, 1, 'UTF-8').".";
            switch($data["status"]){
                case 5: $dop = $cureer; break;
                case 6: if($track != "") $dop = "Трек: ".$track; else $dop = ""; break;
                default: $dop = "";
            }
            $LOGS .= "
                <div class = 'log_str'>
                    <div class = 'log_str_status'>".$status."</div>
                    <div class = 'log_str_date'>".date("d.m.Y H:i", $data["date"])."</div>
                    <div class = 'log_str_name'>".$user."</div>
                    <div class = 'log_str_dop'>".$dop."</div>
                </div>";
        }
        $TEXT = str_replace("%LOGS%", $LOGS, $TEXT);


        echo $TEXT;
    }

    if($_POST["methodName"] == "salesSaleAddressChange2"){    // Изменение адреса доставки в уже созданной заявке
        $id = clean($_POST["id"]);
        $address = clean($_POST["address"]);
        $type = clean($_POST["type"]);
        if ($type == 'Доставка ТК') {
            $delivery = clean($_POST["delivery"]);
            mysqli_query($CONNECTION, "UPDATE sale SET poluchenie='$type', delivery='$delivery' WHERE id = '$id'");
        } else {
            mysqli_query($CONNECTION, "UPDATE sale SET vydacha='$address', poluchenie='$type' WHERE id = '$id'");
        }
    }

    if($_POST["methodName"] == "changeSaleDatePlan"){    // Изменение адреса доставки в уже созданной заявке
        $number = $_POST["number"];
        $time = $_POST["timestamp"]+36000;
        mysqli_query($CONNECTION, "UPDATE sale SET date = '$time' WHERE number = '$number'");
        mysqli_query($CONNECTION, "UPDATE sale SET date_plan = '$time' WHERE number = '$number'");
    }

    if($_POST["methodName"] == "salesSaleViewFooter"){      // Загрузка подвала продажи
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM sale WHERE id = '$id'"));
        $status = $data["status"];
        $poluchenie = $data["poluchenie"];

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM sale_action WHERE sale = '$id' AND status = 3 LIMIT 1"));
        if(isset($temp["id"])) $status_sale_complete = 1; else $status_sale_complete = 0;

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM sale_action WHERE sale = '$id' AND status = 6 LIMIT 1"));
        if(isset($temp["id"])) $status_delivery_complete = 1; else $status_delivery_complete = 0;

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT type FROM user WHERE id = '".ID."'"));
        $user_type = $temp["type"];
        $flag = true;
        if($status == 1){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE sale = '$id' AND status = 0"));
            if($temp[0] != 0) $flag = false;
        }
        if($flag) $display = "inline-block"; else $display = "none";
        $button = "";
        $button_2 = "";
        switch($status){
            case 0: {$button = "Товарный чек";$display = "inline-block";}; break;
            case 4: $display = "none"; if($status_delivery_complete == 1){$button = "Товар получен"; $display = "inline-block";}  break;
            case 5: $display = "none"; break;
            case 6: $button = "Товар получен"; if($status_sale_complete == 0) $button = "Принять оплату"; break;
            case 7: $display = "none"; break;
            case 1: $button = "Забронировать"; $button_2 = "Товарный чек"; break;
            case -1:$button = "Активировать"; break;
            case 2: {
                switch($user_type){
                    case 1: $button = "Товарный чек"; $button_2 = "Принять оплату"; break;
                    case 2: $button = "Товарный чек"; if($root[10] == 1) $button_2 = "Принять оплату"; break;
                    case 3: $button = "Товарный чек"; $button_2 = "Принять оплату"; break;
                    //default: $button = ""; $button_2 = "";
                }
            }
            //
            //default: $button = ""; $button_2 = "";
        }
        $dop = "";
        if($button == "Принять оплату" || $button_2 == "Принять оплату"){
            $dop = "<div id = 'sale_bottom_oplata_cancel' onClick = 'salesSaleOplataCancel();'>Отмена</div><div id = 'sale_bottom_oplata_time'>9</div>";
        }
        if($poluchenie = "Пункт выдачи" || $poluchenie = "В местах хранения"){
            if($status == 3) $button = "Товар получен";
        }
        else{
            if($status == 6) $button = "Получено";
        }
        $TEXT = "<div style = 'display: ".$display.";' class = 'button_green inline' onClick = 'buttonClick(this); salesSaleStatusChange(this);'>".$button."</div>";
        if($button_2 != "") $TEXT .= "<div style = 'margin-left: 30px; display: ".$display.";' class = 'button_green inline' onClick = 'buttonClick(this); salesSaleStatusChange(this);'>".$button_2."</div>";
        $TEXT .= $dop;

        echo $TEXT;
    }
    if($_POST["methodName"] == "salesViewProductDelete"){      // Удаление товара из заказа
        $id = clean($_POST["id"]);
        $sale = clean($_POST["sale"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT status, base_sale FROM sale WHERE id = '$sale'"));
        if(isset($data["status"])){
            $status = $data["status"];
            $base_sale = $data["base_sale"];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base_sale'"));
            $base_sale_code = $data["code"];
            if($status > 1){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION," SELECT * FROM sale_product WHERE id = '$id'"));
                $otkuda = $data["otkuda"];
                $count = $data["count"];
                $p_id = $data["p_id"];
                $p_type = $data["p_type"];
                if($p_type == 1){
                    mysqli_query($CONNECTION, "UPDATE code SET sale = 0 WHERE tire = '$p_id' AND sale = '$id'");
                }
                if($p_type < 4){
                    if($p_type < 3){
                        $mas = explode(" - ", $otkuda);
                        if($mas[0] == $base_sale_code){
                            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '".$mas[1]."'"));
                            $storage = $data["id"];
                            productMove($CONNECTION, 0, $storage, $p_type, $p_id, $count);
                        }
                        else{
                            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = 'SC".$base_sale."'"));
                            $storage = $data["id"];
                            productMove($CONNECTION, 0, $storage, $p_type, $p_id, 0);
                        }
                    }
                    else{
                        productMove($CONNECTION, 0, $otkuda, $p_type, $p_id, $count);
                    }
                }
            }
        }

        mysqli_query($CONNECTION, "DELETE FROM sale_product WHERE id = '$id' AND sale = '$sale'");
        mysqli_query($CONNECTION, "DELETE FROM movement WHERE id = '$id' AND sale = '$sale'");
        mysqli_query($CONNECTION, "DELETE FROM sale WHERE id = '$id'");

        salePriceCalculate($CONNECTION, $sale);
    }
    if($_POST["methodName"] == "saleSaleSummSave"){      // Сохранение данных по скидкам и вариантам оплаты
        $id = clean($_POST["id"]);
        $skidka_ruble = clean($_POST["skidka_ruble"]);
        $skidka_percent = clean($_POST["skidka_percent"]);
        $oplata = clean($_POST["oplata"]);

        if($skidka_ruble == "") $skidka_ruble = 0;
        if($skidka_percent == "") $skidka_percent = 0;

        mysqli_query($CONNECTION, "UPDATE sale SET skidka_ruble = '$skidka_ruble', skidka_percent = '$skidka_percent', oplata = '$oplata' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "salesSaleStatusChange"){      // Смена статуса сделки
        $id = clean($_POST["id"]);
        $name = clean($_POST["name"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT status, poluchenie, base_sale, payer_1 FROM sale WHERE id = '$id'"));
        $old_status = $data["status"];
        $poluchenie = $data["poluchenie"];
        $base_sale = $data["base_sale"];
        $payer = $data["payer_1"];
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base_sale'"));
        $base_sale_code = $data["code"];
        $new_status = 0;
        if($name == "Активировать"){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE sale = '$id' AND status = 0"));
            if($temp[0] == 0) $new_status = 2;
            else $new_status = 1;
        }
        if($name == "Забронировать") $new_status = 2;
        if($name == "Принять оплату") $new_status = 3;
        if($name == "Товар передан") $new_status = 5;
        if($name == "Товар доставлен") $new_status = 6;
        if($name == "Товар получен") $new_status = 7;

        if($old_status != $new_status && $new_status > 0){
            saleStatusChange($CONNECTION, $id, $new_status);

            if($new_status == 2){
                $sql = mysqli_query($CONNECTION, "SELECT p_id, p_type, count, barcode, otkuda FROM sale_product WHERE sale = '$id'");
                while($data = mysqli_fetch_array($sql)){
                    $p_id = $data["p_id"];
                    $p_type = $data["p_type"];
                    $count = $data["count"];
                    $otkuda = $data["otkuda"];

                    if($p_type == 1){
                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT codes FROM payer WHERE id = '$payer'"));
                        if($temp["codes"] == 1){
                            mysqli_query($CONNECTION, "UPDATE code SET sale = '$id' WHERE tire = '$p_id' AND payer = '$payer' AND sale = 0 LIMIT $count");
                        }
                    }


                    if($p_type < 3){
                        $mas = explode(" - ", $otkuda);
                        if($base_sale_code == $mas[0]){
                            $storage = $mas[1];
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$storage'"));
                            $storage = $temp["id"];
                        }
                        else{
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = 'SC".$base_sale."'"));
                            $storage = $temp["id"];
                        }
                        productMove($CONNECTION, $storage, 0, $p_type, $p_id, $count);
                        storageProductRemove($CONNECTION, $storage, $count);
                    }
                    if($p_type == 3){
                        productMove($CONNECTION, $base_sale, 0, $p_type, $p_id, $count);
                    }
                    productCountCalculate($CONNECTION, $p_type, $p_id);
                }
            }

            if($new_status == 3) transactionAdd($CONNECTION, $id, 1);
        }
    }
    if($_POST["methodName"] == "salesSalesCureerChange"){      // Выбор курьера
        $id = clean($_POST["id"]);
        $cureer_id = clean($_POST["cureer"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT surname, name FROM user WHERE id = '$cureer_id'"));
        $cureer = $data["surname"]." ".$data["name"];
        mysqli_query($CONNECTION, "UPDATE sale SET cureer = '$cureer', cureer_id = '$cureer_id' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "salesSaleCureerDel"){      // Удаление курьера
        $id = clean($_POST["id"]);
        mysqli_query($CONNECTION, "UPDATE sale SET cureer = '', cureer_id = 0 WHERE id = '$id'");
    }
    if($_POST["methodName"] == "salesSaleDeleteFinish"){      // Удаление заказа
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT status, base_sale FROM sale WHERE id = '$id'"));
        $base = $data["base_sale"];
        $status = $data['status'];
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM sale_action WHERE sale = '$id' AND status = 3"));
        if(isset($temp["id"])) $param = false; else $param = true;
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
        $base_code = $temp["code"];
        if($param){
            $sql = mysqli_query($CONNECTION, "SELECT p_id, p_type, count, barcode, otkuda FROM sale_product WHERE sale = '$id' AND p_type < 4");
            while($data = mysqli_fetch_array($sql)){
                $p_id = $data["p_id"];
                $p_type = $data["p_type"];
                if($status == '1'){
                    $count = 0;
                }else{
                    $count = $data["count"];
                }
                $barcode = $data["barcode"];
                $kuda = $data["otkuda"];

                if($p_type == 1){
                    mysqli_query($CONNECTION, "UPDATE code SET sale = 0 WHERE tire = '$p_id' AND sale = '$id'");
                }

                if($p_type < 3){
                    $mas = explode(" - ", $kuda);
                    if($mas[0] == $base_code){
                        $storage = $mas[1];
                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$storage'"));
                        $sId = $temp["id"];
                        productMove($CONNECTION, 0, $sId, $p_type, $p_id, $count);
                    }
                    else{
                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = 'SC".$base."'"));
                        $sId = $temp["id"];
                        productMove($CONNECTION, 0, $sId, $p_type, $p_id, $count);
                    }
                }
                if($p_type == 3){
                    productMove($CONNECTION, 0, $base_code, $p_type, $p_id, $count);
                }


                //if($pType < 3){
                //    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = 'SC".$base."'"));
                //    $sId = $temp["id"];
                //    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, count FROM available WHERE barcode = '$barcode' AND storage = '$sId'"));
                //    if(isset($temp["id"])){
                //        $aId = $temp["id"];
                //        $count2 = $temp["count"] + $count;
                //        mysqli_query($CONNECTION, "UPDATE available SET count = '$count2' WHERE id = '$aId'");
                //    }
                //    else{
                //        mysqli_query($CONNECTION, "INSERT INTO available (barcode, storage, count) VALUES ('$barcode', '$sId', '$count')");
                //    }
                //    productCountCalculate($CONNECTION, $pType, $pId);
                //}
                //if($pType == 3){
                //   $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, count FROM available WHERE barcode = '$barcode' AND base = '$base'"));
                //    if(isset($temp["id"])){
                //        $aId = $temp["id"];
                //        $count2 = $temp["count"] + $count;
                //        mysqli_query($CONNECTION, "UPDATE available SET count = '$count2' WHERE id = '$aId'");
                //    }
                //    else{
                //        mysqli_query($CONNECTION, "INSERT INTO available (barcode, base, count) VALUES ('$barcode', '$base', '$count')");
                //    }
                //    productCountCalculate($CONNECTION, $pType, $pId);
                //}
            }

            $sql = mysqli_query($CONNECTION, "SELECT * FROM movement WHERE sale = '$id' AND action = 3 AND status = 1");
            while($data = mysqli_fetch_array($sql)){
                $p_id = $data["p_id"];
                $p_type = $data["p_type"];
                $kuda = $data["kuda"];
                $otkuda = $data["otkuda"];
                $count = $data["count"];

                if($p_type < 3){
                    $mas = explode(" - ", $kuda);
                    $kuda = $mas[1];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$kuda'"));
                    $kuda = $temp["id"];

                    $mas = explode(" - ", $otkuda);
                    $otkuda = $mas[1];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$otkuda'"));
                    $otkuda = $temp["id"];
                }
                if($p_type == 3){
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$kuda'"));
                    $kuda = $temp["id"];

                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$otkuda'"));
                    $otkuda = $temp["id"];
                }

                productMove($CONNECTION, $kuda, $otkuda, $p_type, $p_id, $count);
            }
            allStorageCalc($CONNECTION);
        }
        mysqli_query($CONNECTION, "DELETE FROM sale WHERE id = '$id'");
        mysqli_query($CONNECTION, "DELETE FROM movement WHERE sale = '$id'");
    }
    if($_POST["methodName"] == "salesOplataCommentChange"){      // Изменение комментария к оплате
        $id = clean($_POST["id"]);
        $text = clean($_POST["text"]);
        mysqli_query($CONNECTION, "UPDATE sale SET oplata_comment = '$text' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "salesSaleProductListChange"){      // Изменение товаров в заказе
        $json = $_POST["json"];
        $sale = $_POST["id"];
        $data = json_decode($json, true);

        mysqli_query($CONNECTION, "DELETE FROM sale_product WHERE sale = '$sale'");
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base_sale FROM sale WHERE id = '$sale'"));
        $base_sale = $temp["base_sale"];
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base_sale'"));
        $base_sale_code = $temp["code"];
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = 'SC".$base_sale."'"));
        $SC_id = $temp["id"];

        $price_purchase = 0;
        $price_sale = 0;
        for($i = 0; $i < count($data); $i++){
            $barcode = $data[$i]["barcode"];
            $count = $data[$i]["count"];
            $param = $data[$i]["param"];

            $data_1 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price_purchase, price_sale FROM tire WHERE barcode = '$barcode'"));
            $data_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price_purchase, price_sale FROM disk WHERE barcode = '$barcode'"));
            $data_3 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price_purchase, price_sale FROM product WHERE barcode = '$barcode'"));
            $data_4 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price_1, price_2, price_3 FROM service WHERE barcode = '$barcode'"));
            $data_5 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price FROM season_temp WHERE barcode = '$barcode'"));

            if($data_1["id"] > 0 || $data_2["id"] > 0 || $data_3["id"] > 0 || $data_4["id"] > 0 || $data_5["id"] > 0){
                if($data_1["id"] > 0){
                    $p_id = $data_1["id"];
                    $p_type = 1;
                    $price_purchase += $count * $data_1["price_purchase"];
                    $price_sale += $count * $data_1["price_sale"];
                }
                if($data_2["id"] > 0){
                    $p_id = $data_2["id"];
                    $p_type = 2;
                    $price_purchase += $count * $data_2["price_purchase"];
                    $price_sale += $count * $data_2["price_sale"];
                }
                if($data_3["id"] > 0){
                    $p_id = $data_3["id"];
                    $p_type = 3;
                    $price_purchase += $count * $data_3["price_purchase"];
                    $price_sale += $count * $data_3["price_sale"];
                }
                if($data_4["id"] > 0){
                    $p_id = $data_4["id"];
                    $p_type = 4;
                    if($param == 0) $price_sale += $count * $data_4["price_1"];
                    else $price_sale += $count * $data_4["price_".$param];
                }
                if($data_5["id"] > 0){
                    $p_id = $data_5["id"];
                    $p_type = 5;
                    $price_sale += $count * $data_5["price"];
                }
            }
            $p_param = $param;
            //echo $p_param;
            $storage_mas = $data[$i]["storage"];
            if(count($storage_mas) == 0){
                insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $count, "", $sale);
            }
            else{
                $count_need = $count;
                for($j = 0; $j < count($storage_mas); $j++)if($count_need > 0){
                    $temp_mas = explode(" - ", $storage_mas[$j]);
                    $temp_base = $temp_mas[0];
                    if(isset($temp_mas[1])) $temp_storage = $temp_mas[1]; else $temp_storage = "";

                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$temp_base'"));
                    $temp_base_id = $temp["id"];

                    if($p_type < 3){
                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$temp_storage'"));
                        $temp_storage_id = $temp["id"];
                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, count FROM available WHERE barcode = '$barcode' AND storage = '$temp_storage_id'"));
                        if(isset($temp["id"])){
                            $temp_count = $temp["count"];
                            if($temp_count >= $count_need){
                                insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $count_need, $storage_mas[$j], $sale);
                                if($base_sale_code != $temp_base){
                                    movementAdd($CONNECTION, $barcode, $count_need, $temp_storage_id, $SC_id, $sale);
                                    $flag = false;
                                }
                                $count_need = 0;
                            }
                            else{
                                insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $temp_count, $storage_mas[$j], $sale);
                                if($base_sale_code != $temp_base){
                                    movementAdd($CONNECTION, $barcode, $temp_count, $temp_storage_id, $SC_id, $sale);
                                    $flag = false;
                                }
                                $count_need = $count_need - $temp_count;
                            }
                        }
                    }
                    if($p_type == 3){
                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, count FROM available WHERE barcode = '$barcode' AND base = '$temp_base_id'"));
                        if(isset($temp["id"])){
                            $temp_count = $temp["count"];
                            if($temp_count >= $count_need){
                                insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $count_need, $storage_mas[$j], $sale);
                                if($base_sale_code != $temp_base){
                                    movementAdd($CONNECTION, $barcode, $count_need, $temp_base_id, $base, $sale);
                                    $flag = false;
                                }
                                $count_need = 0;
                            }
                            else{
                                insertSaleProduct($CONNECTION, $barcode, $p_type, $p_id, $param, $temp_count, $storage_mas[$j], $sale);

                                if($base_sale_code != $temp_base){
                                    movementAdd($CONNECTION, $barcode, $temp_count, $temp_base_id, $base, $sale);
                                    $flag = false;
                                }
                                $count_need = $count_need - $temp_count;
                            }
                        }
                    }
                }
            }
        }
        mysqli_query($CONNECTION, "UPDATE sale SET price_purchase = '$price_purchase', price_sale = '$price_sale' WHERE id = '$sale'");
        //mysqli_query($CONNECTION, "DELETE FROM sale_product WHERE sale = '$sale' AND status = 0");
        //print_r($data);

    }
    if($_POST["methodName"] == "saleAtGross"){
        $saleData = array();
        $query = mysqli_query($CONNECTION, "SELECT * FROM sale_product WHERE sale = '".clean($_POST['id'])."'");
        while($r = mysqli_fetch_assoc($query)){
            $saleData[] = $r;
        }
        $totalDiscountRub = 0;
        foreach($saleData as $saleItem){
            $product_type = $saleItem['p_type'];
            switch($product_type){
                case "1": $table = 'tire'; break;
                case "2": $table = 'disk'; break;
                case "3": $table = 'product'; break;
            }
            $product = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM $table WHERE barcode = '".$saleItem['barcode']."'"));
            $gross = ($product['price_wholesale'] * $saleItem['count']);
            $retail = ($product['price_sale'] * $saleItem['count']);
            $discountRub = $retail - $gross;
            $totalDiscountRub += $totalDiscountRub + $discountRub;
        }
        echo json_encode(['discount'=>$totalDiscountRub], 64|256);
        unset($totalDiscountRub);

    }




?>