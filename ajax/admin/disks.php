<?php

    require "../../settings.php";
    require "../../functions.php";
    require_once '../../ajax/admin/CustomLogger.php';

    proof(); 

    if($_POST["methodName"] == "disksStart"){      // Загрузка дисков
        $TEXT = file_get_contents("../../templates/admin/temp/disks/disk_list.html");

        $TEXT = str_replace("%HEAD%", rootAndSortHead($CONNECTION, ID, 3, $SEP), $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "disksSearch"){      // Загрузка дисков согласно поиска
        $article = clean($_POST["article"]);
        $count = clean($_POST["count"]);
        $price_purchase = clean($_POST["price_purchase"]);
        $price_sale = clean($_POST["price_sale"]);
        $price_wholesale = clean($_POST["price_wholesale"]);
        $w = clean($_POST["w"]);
        $r = clean($_POST["r"]);
        $hole = clean($_POST["hole"]);
        $bolt = clean($_POST["bolt"]);
        $vylet = clean($_POST["vylet"]);
        $hub = clean($_POST["hub"]);
        $color = clean($_POST["color"]);
        $nomenclature = clean($_POST["nomenclature"]);

        $sql_text = "SELECT * FROM disk WHERE id > 0 AND status = 1 ";
        if($w >= 0) $sql_text .= "AND w = ".$w." ";
        if($r != ""){
            $sql_text .= "AND (";
            $mas = explode($SEP, $r);
            for($i = 0; $i < count($mas) - 1; $i++){
                if($i < count($mas) - 2) $sql_text .= "r = ".$mas[$i]." OR ";
                else $sql_text .= "r = ".$mas[$i]."";
            }
            $sql_text .= ") ";
        }
        if($hole >= 0) $sql_text .= "AND hole = ".$hole." ";
        if($bolt >= 0) $sql_text .= "AND bolt = ".$bolt." ";
        if($vylet >= 0) $sql_text .= "AND vylet = ".$vylet." ";
        if($hub >= 0) $sql_text .= "AND hub = ".$hub." ";
        if($color >= 0) $sql_text .= "AND color = '".$color."' ";

        if($nomenclature != "") $sql_text .= "AND nomenclature LIKE '$nomenclature%' ";
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

        $data = rootAndSort($CONNECTION, ID, 3, $SEP);
        $mas = explode("XXX", $data);
        $root = $mas[0];
        $sort = $mas[1];
        $count = $mas[2];
        $sort = explode($SEP, $sort);

        $SERVICES_LIST = "";
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $func = "onClick = 'windowDiskView(".$data["id"].");'";
            $SERVICES_LIST .= "<div class = 'disks_body_list_item'>"; //onClick = 'windowTireView(".$data["id"].");

            $action = "
                <div class = 'select select_small' style = 'width: 132px;' id = 'action_".$data["id"]."'>
                    <arrow></arrow>
                    <headline><i>Выбрать</i></headline>
                    <div data = '0' onClick = 'productSaleAdd(".$data["id"].", 2);'>Продать</div>
                    <div data = '1' onClick = 'windowDownAdd2(".$data["id"].", 2);'>Списать</div>
                    <div data = '2' onClick = 'windowReceiptAdd2(".$data["id"].", 2);'>Приемка</div>
                    <div data = '3' onClick = 'windowMovingAdd(".$data["id"].", 2);'>Перемещение</div>
                </div>";

            if($root[ 0] == 1) $mas[ 0] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 95px;'  ".$func.">D".$data["article"]."</div>";
            if($root[ 1] == 1) $mas[ 1] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 312px;' ".$func.">".$data["nomenclature"]."</div>";
            if($root[ 2] == 1) $mas[ 2] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 91px;'  ".$func.">".$data["w"]."</div>";
            if($root[ 3] == 1) $mas[ 3] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 91px;'  ".$func.">R".$data["r"]."</div>";
            if($root[ 4] == 1) $mas[ 4] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 105px;' ".$func.">".$data["hole"]."</div>";
            if($root[ 5] == 1) $mas[ 5] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 105px;' ".$func.">".$data["bolt"]."</div>";
            if($root[ 6] == 1) $mas[ 6] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 91px;'  ".$func.">".$data["vylet"]."</div>";
            if($root[ 7] == 1) $mas[ 7] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 91px;'  ".$func.">".$data["hub"]."</div>";
            if($root[ 8] == 1) $mas[ 8] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 140px;' ".$func.">".$data["color"]."</div>";
            if($root[ 9] == 1) $mas[ 9] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 95px;'  ".$func.">".$data["count"]."</div>";
            if($root[10] == 1) $mas[10] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 122px;' ".$func.">".commaView($data["price_purchase"])."</div>";
            if($root[11] == 1) $mas[11] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 136px;' ".$func.">".commaView($data["price_sale"])."</div>";
            if($root[12] == 1) $mas[12] = "<div class = 'disk_item text_overflow' id = 'd_".$data["id"]."' style = 'width: 109px;' ".$func.">".commaView($data["price_wholesale"])."</div>";
            if($root[13] == 1) $mas[13] = "<div class = 'disk_item'>".$action."</div>";

            for($i = 1; $i < $count*2; $i++){
                if($i%2 == 1){
                    $num = $sort[$i];
                    if($sort[$i+1] == 1) $SERVICES_LIST .= $mas[$num];
                }
            }
            $SERVICES_LIST .= "</div><br>";
        }

        echo $SERVICES_LIST;
    }
    if($_POST["methodName"] == "disksLoad"){      // Загрузка карточки шины
        $id = clean($_POST["id"]);

        $TEXT = file_get_contents("../../templates/admin/temp/disks/disk_card.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM disk WHERE id = '$id'"));
        $barcode = $data["barcode"];
        $TITLE = $data["nomenclature"];
        $TEXT = str_replace("%SIZE%", $data["w"]."/R".$data["r"], $TEXT);
        $TEXT = str_replace("%ARTICLE%", "D".$data["article"], $TEXT);
        $TEXT = str_replace("%NOMENCLATURE%", $data["nomenclature"], $TEXT);
        $TEXT = str_replace("%HOLE%", $data["hole"], $TEXT);
        $TEXT = str_replace("%BOLT%", $data["bolt"], $TEXT);
        $TEXT = str_replace("%VYLET%", $data["vylet"], $TEXT);
        $TEXT = str_replace("%HUB%", $data["hub"], $TEXT);
        $TEXT = str_replace("%COLOR%", $data["color"], $TEXT);
        $TEXT = str_replace("%ID%", $data["id"], $TEXT);
        $TEXT = str_replace("%PRICE_1%", getPriceTroyki($data["price_purchase"]), $TEXT);
        $TEXT = str_replace("%PRICE_2%", getPriceTroyki($data["price_sale"]), $TEXT);
        $TEXT = str_replace("%PRICE_3%", getPriceTroyki($data["price_wholesale"]), $TEXT);
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
        $nomenclature = $data["nomenclature"];
        $OTHER = "";
        $sql = mysqli_query($CONNECTION, "SELECT id, w, r FROM disk WHERE nomenclature = '$nomenclature' AND id != '$id'");
        while($data = mysqli_fetch_array($sql)){
            $OTHER .= "<div onClick = 'windowDiskView(".$data["id"].");'>".$data["w"]."/R".$data["r"]."</div>";
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
                        <span onClick = 'disksAvailableView(this);'>".$base_name."</span>
                        <span1>".$count."</span1>
                        <rightcol><cross onClick = 'disksAvailableHide(this);'></cross>".$bases."</rightcol>
                    </div>";
            }
        }
        $TEXT = str_replace("%NALICHIE%", $nal, $TEXT);

        echo $TITLE.$SEP.$TEXT;
    }
    if($_POST["methodName"] == "disksPriceChange"){      // Изменение стоимости
        $id = clean($_POST["id"]);
        $param = clean($_POST["param"]);
        $price = clean($_POST["price"]);
        $price = str_replace(" ", "", $price);
        switch($param){
            case 1: $sql = "price_purchase"; break;
            case 2: $sql = "price_sale "; break;
            case 3: $sql = "price_wholesale"; break;
        }
        mysqli_query($CONNECTION, "UPDATE disk SET ".$sql." = '$price' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "diskLoadRedact"){      // Загрузка карточки диска для редактирования
        $id = clean($_POST["id"]);
        $TEXT = file_get_contents("../../templates/admin/temp/disks/disk_redact.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM disk WHERE id = '$id'"));
        $barcode = $data["barcode"];
        $color = $data["color"];
        $hole = $data["hole"];
        $TEXT = str_replace("%DISK_ARTICLE%", "D".$data["article"], $TEXT);
        $TEXT = str_replace("%ID%", $data["id"], $TEXT);
        $TEXT = str_replace("%NOME%", $data["nomenclature"], $TEXT);
        $TEXT = str_replace("%BOLT%", $data["bolt"], $TEXT);
        $TEXT = str_replace("%VYLET%", $data["vylet"], $TEXT);
        $TEXT = str_replace("%HUB%", $data["hub"], $TEXT);
        $TEXT = str_replace("%PRICE_1%", getPriceTroyki($data["price_purchase"]), $TEXT);
        $TEXT = str_replace("%PRICE_2%", getPriceTroyki($data["price_sale"]), $TEXT);
        $TEXT = str_replace("%PRICE_3%", getPriceTroyki($data["price_wholesale"]), $TEXT);

        $photo = str_replace("%-%", "%-%img/", $data["photo"]);
        $photo = substr($photo, 0, -4);
        $w = $data["w"];
        $r = $data["r"];

        $W = "
            <div class = 'select' style = 'width: 110px;' id = 'w_disk'>
                <arrow></arrow>
                <headline>".$w."</headline>
                <input type = 'hidden' id = 'w_disk_hidden' value = '".$w."'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 1");
        while($data = mysqli_fetch_array($sql)) $W .= "<div data = '".$data["value"]."'>".$data["value"]."</div>";
        $W .= "</div>";
        $TEXT = str_replace("%DISK_W%", $W, $TEXT);

        $R = "
            <div class = 'select' style = 'width: 110px;' id = 'r_disk'>
                <arrow></arrow>
                <headline>R".$r."</headline>
                <input type = 'hidden' id = 'r_disk_hidden' value = '".$r."'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 6");
        while($data = mysqli_fetch_array($sql)) $R .= "<div data = '".$data["value"]."'>R".$data["value"]."</div>";
        $R .= "</div>";
        $TEXT = str_replace("%DISK_R%", $R, $TEXT);

        $COLOR = "
            <div class = 'select' style = 'width: 110px;' id = 'color'>
                <arrow></arrow>
                <headline>".$color."</headline>
                <input type = 'hidden' id = 'color_hidden' value = '".$color."'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 4");
        while($data = mysqli_fetch_array($sql)) $COLOR .= "<div data = '".$data["value"]."'>".$data["value"]."</div>";
        $COLOR .= "</div>";
        $TEXT = str_replace("%COLOR%", $COLOR, $TEXT);

        $HOLE = "
            <div class = 'select' style = 'width: 110px;' id = 'hole'>
                <arrow></arrow>
                <headline>".$hole."</headline>
                <input type = 'hidden' id = 'hole_hidden' value = '".$hole."'>
                <div data = '3'>3</div>
                <div data = '4'>4</div>
                <div data = '5'>5</div>
                <div data = '6'>6</div>
                <div data = '10'>10</div>
            </div>";
        $TEXT = str_replace("%HOLE%", $HOLE, $TEXT);


        echo $TEXT."X-X-X".$photo;
    }
    if($_POST["methodName"] == "disksRedact"){   // Редактирование диска
        $id = clean($_POST["id"]);
        $customLogger = new CustomLogger($id, 'rims', $DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME);
        $customLogger->addLogEntry($_POST);

        $disk_nomenclature = clean($_POST["disk_nomenclature"]);
        $disk_bolt = clean($_POST["disk_bolt"]);
        $disk_vylet = clean($_POST["disk_vylet"]);
        $disk_hub = clean($_POST["disk_hub"]);
        $disk_w = clean($_POST["disk_w"]);
        $disk_r = clean($_POST["disk_r"]);
        $disk_hole = clean($_POST["disk_hole"]);
        $disk_color = clean($_POST["disk_color"]);

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

        mysqli_query($CONNECTION, "
            UPDATE disk SET
                nomenclature = '$disk_nomenclature',
                w = '$disk_w',
                r = '$disk_r',
                hole = '$disk_hole',
                bolt = '$disk_bolt',
                vylet = '$disk_vylet',
                hub = '$disk_hub',
                color = '$disk_color',
                price_purchase = '$price_purchase',
                price_wholesale = '$price_wholesale',
                price_sale = '$price_sale',
                photo = '$photos'
            WHERE id = '$id' ");
        echo mysqli_error($CONNECTION);
    }
    if($_POST["methodName"] == "getCustomRimLogs"){
        $id = clean($_POST["id"]);
        $customLogger = new CustomLogger($id, 'rims', $DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME);
        echo $customLogger->getLogs();
    }



?>