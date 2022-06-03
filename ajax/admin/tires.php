<?php

    require "../../settings.php";
    require "../../functions.php";
    require_once '../../ajax/admin/CustomLogger.php';

    proof(); 

    if($_POST["methodName"] == "tiresStart"){      // Загрузка шин
        $TEXT = file_get_contents("../../templates/admin/temp/tires/tire_list.html");

        $TEXT = str_replace("%HEAD%", rootAndSortHead($CONNECTION, ID, 2, $SEP), $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "tiresCol"){      // Загрузка шин
        echo ceil(mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(id) FROM tire"))[0]/100)-1;
    }
    if($_POST["methodName"] == "tiresSearch"){      // Загрузка шин
        $page = ($_POST["page"]-1)*99;
        $article = clean($_POST["article"]);
        $count = clean($_POST["count"]);
        $price_purchase = clean($_POST["price_purchase"]);
        $price_sale = clean($_POST["price_sale"]);
        $price_wholesale = clean($_POST["price_wholesale"]);
        if(isset($_POST["season"])) $season = clean($_POST["season"]); else $season = -1;
        if(isset($_POST["w"])) $w = clean($_POST["w"]); else $w = -1;
        if(isset($_POST["h"])) $h = clean($_POST["h"]); else $h = -1;
        if(isset($_POST["rft"])) $rft = clean($_POST["rft"]); else $rft = -1;
        if(isset($_POST["spike"])) $spike = clean($_POST["spike"]); else $spike = -1;
        if(isset($_POST["cargo"])) $cargo = clean($_POST["cargo"]); else $cargo = -1;
        if(isset($_POST["brand"])) $brand = clean($_POST["brand"]); else $brand = -1;
        if(isset($_POST["r"])) $r = clean($_POST["r"]); else $r = "";
        $model = clean($_POST["model"]);
        $nagr = dotView(clean($_POST["nagr"]));
        $resist = dotView(clean($_POST["resist"]));

        $sql_text = "SELECT * FROM tire WHERE id > 0 AND status = 1 ";
        if($season >= 0) $sql_text .= "AND season = ".$season." ";
        if($w >= 0) $sql_text .= "AND w = ".$w." ";
        if($h >= 0) $sql_text .= "AND h = ".$h." ";
        if($rft >= 0) $sql_text .= "AND rft = ".$rft." ";
        if($spike >= 0) $sql_text .= "AND spike = ".$spike." ";
        if($cargo >= 0) $sql_text .= "AND cargo = ".$cargo." ";
        if($brand >= 0) $sql_text .= "AND brand = '".$brand."' ";
        if($r != ""){
            $sql_text .= "AND (";
            $mas = explode($SEP, $r);
            for($i = 0; $i < count($mas) - 1; $i++){
                if($i < count($mas) - 2) $sql_text .= "r = ".$mas[$i]." OR ";
                else $sql_text .= "r = ".$mas[$i]."";
            }
            $sql_text .= ") ";
        }
        if($model != "") $sql_text .= "AND model LIKE '$model%' ";
        if($nagr != "") $sql_text .= "AND nagr LIKE '$nagr%' ";
        if($resist != "") $sql_text .= "AND resist LIKE '$resist%' ";
        if($article == 1) $sql_text .= "ORDER BY article ";
        if($article == 2) $sql_text .= "ORDER BY article DESC ";
        if($count == 1) $sql_text .= "ORDER BY count ";
        if($count == 2) $sql_text .= "ORDER BY count DESC ";
        if($price_purchase == 1) $sql_text .= "ORDER BY price_purchase ";
        if($price_purchase == 2) $sql_text .= "ORDER BY price_purchase DESC ";
        if($price_sale == 1) $sql_text .= "ORDER BY price_sale ";
        if($price_sale == 2) $sql_text .= "ORDER BY price_sale DESC ";
        if($price_wholesale == 1) $sql_text .= "ORDER BY price_wholesale ";
        if($price_wholesale == 2) $sql_text .= "ORDER BY price_wholesale DESC ";
        $sql_text .= "LIMIT $page,99";

        $data = rootAndSort($CONNECTION, ID, 2, $SEP);
        $mas = explode("XXX", $data);
        $root = $mas[0];
        $sort = $mas[1];
        $count = $mas[2];
        $sort = explode($SEP, $sort);

        $SERVICES_LIST = "";
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $func = "onClick = 'windowTireView(".$data["id"].");'";
            $SERVICES_LIST .= "<div class = 'tires_body_list_item'>";
            switch($data["season"]){
                case 0 : $season = "Зима"; break;
                case 1 : $season = "Лето"; break;
                case 2 : $season = "Всесезон"; break;
            }

            if($data["rft"] == 0) $rft = "нет"; else $rft = "да";
            if($data["spike"] == 0) $spike = "нет"; else $spike = "да";
            if($data["cargo"] == 0) $cargo = "нет"; else $cargo = "да";

            $action = "
                <div class = 'select select_small' style = 'width: 132px;' id = 'action_".$data["id"]."'>
                    <arrow></arrow>
                    <headline><i>Выбрать</i></headline>
                    <div data = '0' onClick = 'productSaleAdd(".$data["id"].", 1);'>Продать</div>
                    <div data = '1' onClick = 'windowDownAdd2(".$data["id"].", 1);'>Списать</div>
                    <div data = '2' onClick = 'windowReceiptAdd2(".$data["id"].", 1);'>Приемка</div>
                    <div data = '3' onClick = 'windowMovingAdd(".$data["id"].", 1);'>Перемещение</div>
                </div>";
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE tire = '".$data["id"]."' LIMIT 1"));

            if(isset($temp["id"])){
                $sql_temp = mysqli_query($CONNECTION, "SELECT * FROM code WHERE tire = '".$data["id"]."'/* GROUP BY payer*/");
                $showBtn = 0;
                while($temp = mysqli_fetch_array($sql_temp)){
                    $payer_id = $temp["payer"];
                    $temp_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE id = '$payer_id'"));
                    $payer = $temp_2["name"];

                    //$movementQuery = mysqli_query($CONNECTION, "select * from `movement` where `id`='".$data['id']."'");

                    if($temp['sale'] == 0 && $temp['code'] != ''){
                        $showBtn = $showBtn + 1;
                    }

                }
                $codes = "<div class = 'link_blue_4' onClick = 'windowCodesView(".$data["id"].", 3, ".$payer_id.");'>Коды маркировки</div>";

                $action = "
                        <div class = 'select select_small' style = 'width: 132px;' id = 'action_".$data["id"]."_".$payer_id."'>
                            <arrow></arrow>
                            <headline><i>Выбрать</i></headline>
                            <div data = '0' onClick = 'productSaleAdd(".$data["id"].", 1);'>Продать</div>
                            <div data = '1' onClick = 'windowDownAdd2(".$data["id"].", 1);'>Списать</div>
                            <div data = '2' onClick = 'windowReceiptAdd2(".$data["id"].", 1);'>Приемка</div>
                            <div data = '3' onClick = 'windowMovingAdd(".$data["id"].", 1);'>Перемещение</div>
                            <div data = '4' onClick = 'windowCodesArea(".$data["id"].", ".$payer_id.", 1);'>Приемка кодов</div>
                            <div data = '5' onClick = 'windowCodesArea(".$data["id"].", ".$payer_id.", 2);'>Списание кодов</div>
                        </div>";
                $func = "onClick = 'windowTireView(".$data["id"].", $payer_id, ".$showBtn.");'";

                $SERVICES_LIST .= "<div class = 'tires_body_list_item'>"; //onClick = 'windowTireView(".$data["id"].");

                if($root[ 0] == 1) $mas[ 0] = "<div data-cont='".$data["brand"]." ".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"]." ".commaView($data["nagr"]).commaView($data["resist"])." - ".commaView($data["price_sale"])." руб."."' class = 'tire_item text_overflow ' style = 'width: 95px;' onclick='tireCopy(this)'>S".$data["article"]."</div><div class = 'tire_item text_overflow tire-name' ".$func." id = 't_".$data["id"]."' style = 'width: 387px;'>".$data["brand"]." ".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"]." ".commaView($data["nagr"]).commaView($data["resist"])."</div>";
                if($root[ 1] == 1) $mas[ 1] = "<div class = 'tire_item text_overflow' style = 'width: 110px;' ".$func.">".$season."</div>";
                if($root[ 2] == 1) $mas[ 2] = "<div class = 'tire_item text_overflow' style = 'width: 110px;' ".$func.">".$data["w"]."</div>";
                if($root[ 3] == 1) $mas[ 3] = "<div class = 'tire_item text_overflow' style = 'width: 110px;' ".$func.">".$data["h"]."</div>";
                if($root[ 4] == 1) $mas[ 4] = "<div class = 'tire_item text_overflow' style = 'width: 100px;' ".$func.">R".$data["r"]."</div>";
                if($root[ 5] == 1) $mas[ 5] = "<div class = 'tire_item text_overflow' style = 'width: 150px;' ".$func.">".$data["brand"]."</div>";
                if($root[ 6] == 1) $mas[ 6] = "<div class = 'tire_item text_overflow' style = 'width: 153px;' ".$func.">".$data["model"]."</div>";
                if($root[ 7] == 1) $mas[ 7] = "<div class = 'tire_item text_overflow' style = 'width: 73px;'  ".$func.">".commaView($data["nagr"])."</div>";
                if($root[ 8] == 1) $mas[ 8] = "<div class = 'tire_item text_overflow' style = 'width: 73px;'  ".$func.">".commaView($data["resist"])."</div>";
                if($root[ 9] == 1) $mas[ 9] = "<div class = 'tire_item text_overflow' style = 'width: 110px;' ".$func.">".$rft."</div>";
                if($root[10] == 1) $mas[10] = "<div class = 'tire_item text_overflow' style = 'width: 110px;' ".$func.">".$spike."</div>";
                if($root[11] == 1) $mas[11] = "<div class = 'tire_item text_overflow' style = 'width: 110px;' ".$func.">".$cargo."</div>";
                if($root[12] == 1) $mas[12] = "<div class = 'tire_item text_overflow count-col' style = 'width: 95px;'  ".$func.">".$data["count"]."</div>";
                if($root[13] == 1) $mas[13] = "<div class = 'tire_item text_overflow' style = 'width: 122px;' ".$func.">".commaView($data["price_purchase"])."</div>";
                if($root[14] == 1) $mas[14] = "<div class = 'tire_item text_overflow' style = 'width: 136px;' ".$func.">".commaView($data["price_sale"])."</div>";
                if($root[15] == 1) $mas[15] = "<div class = 'tire_item text_overflow' style = 'width: 109px;' ".$func.">".commaView($data["price_wholesale"])."</div>";
                if($root[16] == 1) $mas[16] = "<div class = 'tire_item'>".$action."</div>";
                if($root[17] == 1) $mas[17] = "<div class = 'tire_item' style = 'width: 136px;'>".$codes."</div>";
                if($root[18] == 1) $mas[18] = "<div class = 'tire_item' style = 'width: 186px;'>".$payer."</div>";

                for($i = 1; $i < $count*2; $i++){
                    if($i%2 == 1){
                        $num = $sort[$i];
                        if($sort[$i+1] == 1) $SERVICES_LIST .= $mas[$num];
                    }
                }
                $SERVICES_LIST .= "</div></div><br>";
            }
            else{
                $func = "onClick = 'windowTireView(".$data["id"].");'";

                $SERVICES_LIST .= "<div class = 'tires_body_list_item'>"; //onClick = 'windowTireView(".$data["id"].");
    
            if($root[ 0] == 1) $mas[ 0] = "<div data-cont='".$data["brand"]." ".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"]." ".commaView($data["nagr"]).commaView($data["resist"])." - ".commaView($data["price_sale"])." руб."."' class = 'tire_item text_overflow ' id = 't_".$data["id"]."' style = 'width: 95px;' onclick='tireCopy(this)'>S".$data["article"]."</div><div class = 'tire_item text_overflow tire-name' ".$func." id = 't_".$data["id"]."' style = 'width: 387px;'>".$data["brand"]." ".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"]." ".commaView($data["nagr"]).commaView($data["resist"])."</div>";
            if($root[ 1] == 1) $mas[ 1] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 110px;' ".$func.">".$season."</div>";
            if($root[ 2] == 1) $mas[ 2] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 110px;' ".$func.">".$data["w"]."</div>";
            if($root[ 3] == 1) $mas[ 3] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 110px;' ".$func.">".$data["h"]."</div>";
            if($root[ 4] == 1) $mas[ 4] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 100px;' ".$func.">R".$data["r"]."</div>";
            if($root[ 5] == 1) $mas[ 5] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 150px;' ".$func.">".$data["brand"]."</div>";
            if($root[ 6] == 1) $mas[ 6] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 153px;' ".$func.">".$data["model"]."</div>";
            if($root[ 7] == 1) $mas[ 7] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 73px;'  ".$func.">".commaView($data["nagr"])."</div>";
            if($root[ 8] == 1) $mas[ 8] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 73px;'  ".$func.">".commaView($data["resist"])."</div>";
            if($root[ 9] == 1) $mas[ 9] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 110px;' ".$func.">".$rft."</div>";
            if($root[10] == 1) $mas[10] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 110px;' ".$func.">".$spike."</div>";
            if($root[11] == 1) $mas[11] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 110px;' ".$func.">".$cargo."</div>";
            if($root[12] == 1) $mas[12] = "<div class = 'tire_item text_overflow count-col' id = 't_".$data["id"]."' style = 'width: 95px;'  ".$func.">".$data["count"]."</div>";
            if($root[13] == 1) $mas[13] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 122px;' ".$func.">".commaView($data["price_purchase"])."</div>";
            if($root[14] == 1) $mas[14] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 136px;' ".$func.">".commaView($data["price_sale"])."</div>";
            if($root[15] == 1) $mas[15] = "<div class = 'tire_item text_overflow' id = 't_".$data["id"]."' style = 'width: 109px;' ".$func.">".commaView($data["price_wholesale"])."</div>";
            if($root[16] == 1) $mas[16] = "<div class = 'tire_item'>".$action."</div>";
            if($root[17] == 1) $mas[17] = "<div class = 'tire_item' style = 'width: 136px;'></div>";
            if($root[18] == 1) $mas[18] = "<div class = 'tire_item' style = 'width: 186px;'></div>";
           
            for($i = 1; $i < $count*2; $i++){
                if($i%2 == 1){
                    $num = $sort[$i];
                    if($sort[$i+1] == 1) $SERVICES_LIST .= $mas[$num];
                }
            }
            $SERVICES_LIST .= "</div><br>";
        }
    }
        echo $SERVICES_LIST;
    }
    if($_POST["methodName"] == "tiresLoad"){      // Загрузка карточки шины
        $id = clean($_POST["id"]);
        $payer = clean($_POST["payer"]);
        $TEXT = file_get_contents("../../templates/admin/temp/tires/tire_card.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM tire WHERE id = '$id'"));
        $barcode = $data["barcode"];
        $TITLE = $data["brand"]." ".$data["model"];
        $TEXT = str_replace("%SIZE%", $data["w"]."/".$data["h"]."R".$data["r"], $TEXT);
        $TEXT = str_replace("%ARTICLE%", "S".$data["article"], $TEXT);
        switch($data["season"]){
            case 0 : $season = "Зима"; break;
            case 1 : $season = "Лето"; break;
            case 2 : $season = "Всесезон"; break;
        }
        $TEXT = str_replace("%SEASON%", $season, $TEXT);
        $TEXT = str_replace("%BRAND%", $data["brand"], $TEXT);
        $TEXT = str_replace("%MODEL%", $data["model"], $TEXT);
        $TEXT = str_replace("%NAGR%", commaView($data["nagr"]), $TEXT);
        $TEXT = str_replace("%RESIST%", commaView($data["resist"]), $TEXT);
        $TEXT = str_replace("%ID%", $data["id"], $TEXT);
        $TEXT = str_replace("%PRICE_1%", getPriceTroyki($data["price_purchase"]), $TEXT);
        $TEXT = str_replace("%PRICE_2%", getPriceTroyki($data["price_sale"]), $TEXT);
        $TEXT = str_replace("%PRICE_3%", getPriceTroyki($data["price_wholesale"]), $TEXT);
        $TEXT = str_replace("%IMG_RFT%", $SERVER."templates/img/rft_".$data["rft"].".png", $TEXT);
        $TEXT = str_replace("%IMG_SPIKE%", $SERVER."templates/img/spike_".$data["spike"].".png", $TEXT);
        $TEXT = str_replace("%IMG_CARGO%", $SERVER."templates/img/cargo_".$data["cargo"].".png", $TEXT);

        $mas = explode($SEP, $data["photo"]);
        $count = 0;
        $PHOTOS = "";
        for($i = 1; $i < count($mas); $i++) if($mas[$i] != ""){
            $count++;
            if($count == 1) $PHOTOS .= "<div id = 'phoduct_photos_1'><img src = '".$SERVER."img/".$mas[$i]."' /></div><div id = 'phoduct_photos_2'>";
            if($count > 1 && $count < 5) $PHOTOS .= "<img src = '".$SERVER."img/".$mas[$i]."' />";
            if($count == 5) $PHOTOS .= "</div><div id = 'phoduct_photos_3'><img src = '".$SERVER."img/".$mas[$i]."' />";
        }
        $PHOTOS .= "</div>";
        $TEXT = str_replace("%PHOTO_TITLE%", "Фото (".$count.")", $TEXT);
        $TEXT = str_replace("%PHOTOS%", $PHOTOS, $TEXT);

        $count = 0;
        $brand = $data["brand"];
        $model = $data["model"];
        $OTHER = "";
        $sql = mysqli_query($CONNECTION, "SELECT id, w, h, r FROM tire WHERE brand = '$brand' AND model = '$model' AND id != '$id'");
        while($data = mysqli_fetch_array($sql)){
            $OTHER .= "<div onClick = 'windowTireView(".$data["id"].");'>".$data["w"]."/".$data["h"]."R".$data["r"]."</div>";
            $count++;
        }
        $TEXT = str_replace("%OTHER_TITLE%", "Другие размеры (".$count.")", $TEXT);
        $TEXT = str_replace("%OTHER%", $OTHER, $TEXT);

        $count = 0;
        $sql = mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode'");
        while($data = mysqli_fetch_array($sql)){
            $count += $data["count"];
        }
        $TEXT = str_replace("%COUNT_TIRE%", $count, $TEXT);

        $nal = "";
        $arr = array();
        $sql = mysqli_query($CONNECTION, "SELECT storage FROM available WHERE barcode = '$barcode' AND count > 0");
        while($data = mysqli_fetch_array($sql)){
            $storage = $data["storage"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM storage WHERE id = '$storage'"));
            $base = $temp["base"];
            if(!in_array($base, $arr)){
                array_push($arr, $base);

                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, code, name FROM base WHERE id = '$base'"));
                $color = $temp["color"];
                $base_code = $temp["code"];
                $base_name = $temp["name"];
                $count = 0;
                $bases = "";
                $sql_2 = mysqli_query($CONNECTION, "SELECT storage.code AS code, available.count AS count, storage.mother AS mother FROM storage LEFT JOIN available ON storage.id = available.storage WHERE storage.base = '$base' AND available.barcode = '$barcode' AND available.count > 0");
                while($data_2 = mysqli_fetch_array($sql_2)){
                    if($data_2["mother"] > 0){
                        $mother = $data_2["mother"];
                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM storage WHERE id = '$mother'"));
                        $mother_code = $temp["code"];
                        $finish_code = $base_code."-".$mother_code."-".$data_2["code"];
                    }
                    else $finish_code = $base_code."-".$data_2["code"];
                    $count += $data_2["count"];
                    $bases .= "
                        <block>
                            <count style = 'background: #".$color."'>".$data_2["count"]."</count>
                            <name>".$finish_code."</name>
                        </block>";
                }
                $nal .= "
                    <div class = 'tire_count_str'>
                        <circle style = 'background: #".$color.";'></circle>
                        <span onClick = 'tiresAvailableView(this);'>".$base_name."</span>
                        <span1>".$count."</span1>
                        <rightcol><cross onClick = 'tiresAvailableHide(this);'></cross>".$bases."</rightcol>
                    </div>";
            }
        }
        $TEXT = str_replace("%NALICHIE%", $nal, $TEXT);
        
        $PAYER = "";
        $PAYER_ID = 0;
        if($payer != 0){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE id = '$payer'"));
            $PAYER = "
                <div class = 'sc_str' style = 'margin-top: 10px;'>
                    <title2>Плательщик</title2>
                    <span>".$temp["name"]."</span>
                </div>
            ";
            $PAYER_ID = $payer;
        }
        $TEXT = str_replace("%PAYER%", $PAYER, $TEXT);
        $TEXT = str_replace("%PAYER_ID%", $PAYER_ID, $TEXT);
        
        echo $TITLE.$SEP.$TEXT;
    }
    if($_POST["methodName"] == "tireLoadRedact"){      // Загрузка карточки шины для редактирования
        $id = clean($_POST["id"]);
        $payer = clean($_POST["payer"]);
        $TEXT = file_get_contents("../../templates/admin/temp/tires/tire_redact.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM tire WHERE id = '$id'"));
        $barcode = $data["barcode"];
        $TITLE = $data["brand"]." ".$data["model"];

        $TEXT = str_replace("%SIZE%", $data["w"]."/".$data["h"]."R".$data["r"], $TEXT);
        $TEXT = str_replace("%TIRE_ARTICLE%", "S".$data["article"], $TEXT);
        $TEXT = str_replace("%BRAND%", $data["brand"], $TEXT);
        $TEXT = str_replace("%MODEL%", $data["model"], $TEXT);
        $TEXT = str_replace("%NAGR%", commaView($data["nagr"]), $TEXT);
        $TEXT = str_replace("%RESIST%", commaView($data["resist"]), $TEXT);
        $TEXT = str_replace("%ID%", $data["id"], $TEXT);
        $TEXT = str_replace("%PRICE_1%", getPriceTroyki($data["price_purchase"]), $TEXT);
        $TEXT = str_replace("%PRICE_2%", getPriceTroyki($data["price_sale"]), $TEXT);
        $TEXT = str_replace("%PRICE_3%", getPriceTroyki($data["price_wholesale"]), $TEXT);
        $TEXT = str_replace("%RFT%", tumbler("rft", $data["rft"]), $TEXT);
        $TEXT = str_replace("%SPIKE%", tumbler("spike", $data["spike"]), $TEXT);
        $TEXT = str_replace("%CARGO%", tumbler("cargo", $data["cargo"]), $TEXT);
        $TEXT = str_replace("%BUTTONS_SEASON%", doubleButton(2, "<i>Зима</i>", "<i>Лето</i>", "<i>Всесезон</i>", $data["season"] + 1), $TEXT);
        $query = "select * from `third_party_settings` where `name`='priceSet'";
        $row = mysqli_fetch_assoc(mysqli_query($CONNECTION, $query));
        $priceSet = json_decode($row['setting'], true);
        $buyout = $data["price_purchase"];
        $minGross = ceil(Round($buyout * (1 + 0.01*$priceSet['gross']), 0)/100) * 100;
        $minRetail = ceil(Round($buyout * (1 + 0.01*$priceSet['retail']), 0)/100) * 100;
        $TEXT = str_replace('_GM_', $minGross, $TEXT);
        $TEXT = str_replace('_RM_', $minRetail, $TEXT);

        $photo = str_replace("%-%", "%-%img/", $data["photo"]);
        $photo = substr($photo, 0, -4);
        $w = $data["w"];
        $h = $data["h"];
        $r = $data["r"];

        $W = "
            <div class = 'select' style = 'width: 110px;' id = 'w_tire'>
                <arrow></arrow>
                <headline>".$w."</headline>
                <input type = 'hidden' id = 'w_tire_hidden' value = '".$w."'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 1");
        while($data = mysqli_fetch_array($sql)) $W .= "<div data = '".$data["value"]."'>".$data["value"]."</div>";
        $W .= "</div>";
        $TEXT = str_replace("%TIRE_W%", $W, $TEXT);

        $H = "
            <div class = 'select' style = 'width: 110px;' id = 'h_tire'>
                <arrow></arrow>
                <headline>".$h."</headline>
                <input type = 'hidden' id = 'h_tire_hidden' value = '".$h."'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 2");
        while($data = mysqli_fetch_array($sql)) $H .= "<div data = '".$data["value"]."' >".$data["value"]."</div>";
        $H .= "</div>";
        $TEXT = str_replace("%TIRE_H%", $H, $TEXT);

        $R = "
            <div class = 'select' style = 'width: 110px;' id = 'r_tire'>
                <arrow></arrow>
                <headline>R".$r."</headline>
                <input type = 'hidden' id = 'r_tire_hidden' value = '".$r."'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 3");
        while($data = mysqli_fetch_array($sql)) $R .= "<div data = '".$data["value"]."'>R".$data["value"]."</div>";
        $R .= "</div>";
        $TEXT = str_replace("%TIRE_R%", $R, $TEXT);

        $payer_name = "Выбрать";
        if($payer != 0){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE id = '$payer'"));
            $payer_name = $temp["name"];
        }
        if($payer == 0) $payer = -1;

        $temp = "
            <div class = 'select' id = 'payer' style = 'min-width: 234px;'>
                <arrow></arrow>
                <headline>".$payer_name."</headline>
                <input type = 'hidden' id = 'payer_hidden' value = '".$payer."' />
        ";
        $sql = mysqli_query($CONNECTION, "SELECT id, name, codes FROM payer WHERE status = 1");
        while($data = mysqli_fetch_array($sql)){
           $temp .= "<div data = '".$data["id"]."' data_2 = '".$data["codes"]."' onClick = 'tiresCodeWrite(this);'>".$data["name"]."</div>";
        }
        $temp .= "</div>";
        $PAYER = "
            <div class = 'pa_str'>
                <title2>Плательщик</title2>
                ".$temp."
            </div>
        ";
        $TEXT = str_replace("%PAYER%", $PAYER, $TEXT);

        
        echo $TEXT."X-X-X".$photo;
    }
    if($_POST["methodName"] == "tiresPriceChange"){      // Изменение стоимости
        $id = clean($_POST["id"]);
        $param = clean($_POST["param"]);
        $price = clean($_POST["price"]);
        $price = str_replace(" ", "", $price);
        switch($param){
            case 1: $sql = "price_purchase"; break;
            case 2: $sql = "price_sale "; break;
            case 3: $sql = "price_wholesale"; break;
        }
        mysqli_query($CONNECTION, "UPDATE tire SET ".$sql." = '$price' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "tiresRedact"){   // Редактирование шины
        $id = clean($_POST["id"]);
        $customLogger = new CustomLogger($id, 'tires', $DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME);
        $customLogger->addLogEntry($_POST);
        $tire_brand = clean($_POST["tire_brand"]);
        $tire_model = clean($_POST["tire_model"]);
        $tire_nagr = clean($_POST["tire_nagr"]);
        $tire_resist = clean($_POST["tire_resist"]);
        $tire_w = clean($_POST["tire_w"]);
        $tire_h = clean($_POST["tire_h"]);
        $tire_r = clean($_POST["tire_r"]);
        $tire_season = clean($_POST["tire_season"]);
        $tire_rft = clean($_POST["tire_rft"]);
        $tire_spike = clean($_POST["tire_spike"]);
        $tire_cargo = clean($_POST["tire_cargo"]);
        $tire_payer = clean($_POST["tire_payer"]);

        $price_purchase = clean($_POST["price_purchase"]);
        $price_wholesale = clean($_POST["price_wholesale"]);
        $price_sale = clean($_POST["price_sale"]);

        $price_purchase = str_replace(" ", "", $price_purchase);
        $price_wholesale = str_replace(" ", "", $price_wholesale);
        $price_sale = str_replace(" ", "", $price_sale);

        $photos = clean($_POST["photos"]);
        $general_photo = clean($_POST["general_photo"]);

        $mas = explode($SEP, $photos);
        $photos = $SEP;
        if(($key = array_search($general_photo, $mas)) !== false) unset($mas[$key]); else $general_photo = 0;
        if($general_photo != "0") $photos .= imgAdd($general_photo).$SEP;

        for($i = 1; $i < count($mas); $i++) if($mas[$i] != ""){
            $photos .= imgAdd($mas[$i]).$SEP;
        }

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE tire = '$id' AND payer = '$tire_payer' LIMIT 1"));
        if(!isset($data["id"])){
            mysqli_query($CONNECTION, "INSERT INTO code (tire, payer) VALUES ('$id', '$tire_payer')");
        }

        mysqli_query($CONNECTION, "
            UPDATE tire SET
                season = '$tire_season',
                w = '$tire_w',
                h = '$tire_h',
                r = '$tire_r',
                brand = '$tire_brand',
                model = '$tire_model',
                nagr = '$tire_nagr',
                resist = '$tire_resist',
                rft = '$tire_rft',
                spike = '$tire_spike',
                cargo = '$tire_cargo',
                price_purchase = '$price_purchase',
                price_wholesale = '$price_wholesale',
                price_sale = '$price_sale',
                photo = '$photos'
            WHERE id = '$id' ");
    }
    if($_POST["methodName"] == "tireCodesChange"){   // Добавление или удаление кодов
        $tire = clean($_POST["tire"]);
        $payer = clean($_POST["payer"]);
        $code = clean($_POST["code"]);
        $param = clean($_POST["param"]);

        $code = str_replace("(", "&#40;", $code);
        $code = str_replace(")", "&#41;", $code);
        $code = str_replace("<", "&#706;", $code);
        $code = str_replace(">", "&#707;", $code);
        $mas = explode("%-%", $code);

        $flag = true;
        if($param == 1){
            for($i = 0; $i < count($mas)-1; $i++){
                $code = $mas[$i];
                $code = str_replace("&amp;", "&", $code);
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE code = '$code'"));
                if(isset($data["id"])) $flag = false;
            }

            if($flag){
                $count = count($mas)-1;
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(id) FROM code WHERE tire = '$tire'"));
                $count += $data[0];
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM tire WHERE id = '$tire'"));
                if($count > $data["count"]) echo "Количество кодов будет больше чем количество шин";
                else{
                    for($i = 0; $i < count($mas)-1; $i++) if($mas[$i] != ""){
                        $code = $mas[$i];
                        $code = str_replace("&amp;", "&", $code);
                        mysqli_query($CONNECTION, "INSERT INTO code (tire, payer, code) VALUES ('$tire', '$payer', '$code')");
                    }
                    echo "Коды успешно добавлены";
                }
            }
            else{
                echo "Такие коды уже есть";
            }
        }
        if($param == 2){
            for($i = 0; $i < count($mas)-1; $i++){
                $code = $mas[$i];
                $code = str_replace("&amp;", "&", $code);
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, sale FROM code WHERE code = '$code'"));
                if(isset($data["id"])){
                    $id = $data["id"];
                    if($data["sale"] == 0) mysqli_query($CONNECTION, "DELETE FROM code WHERE id = '$id'");
                }

            }
            echo "Коды удалены";
        }
    }
    if($_POST["methodName"] == "getCustomTireLogs"){
        $id = clean($_POST["id"]);
        $customLogger = new CustomLogger($id, 'tires', $DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME);
        echo $customLogger->getLogs();
    }

?>