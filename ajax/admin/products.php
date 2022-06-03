<?php

    require "../../settings.php";
    require "../../functions.php";

    proof(); 

    if($_POST["methodName"] == "productsStart"){      // Загрузка шин
        $TEXT = file_get_contents("../../templates/admin/temp/products/product_list.html");

        $TEXT = str_replace("%HEAD%", rootAndSortHead($CONNECTION, ID, 4, $SEP), $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "productsSearch"){      // Загрузка шин
        $article = clean($_POST["article"]);
        $count = clean($_POST["count"]);
        $price_purchase = clean($_POST["price_purchase"]);
        $price_sale = clean($_POST["price_sale"]);
        $price_wholesale = clean($_POST["price_wholesale"]);
        $name = clean($_POST["name"]);
        $params = clean($_POST["params"]);
        $note = clean($_POST["note"]);

        $sql_text = "SELECT * FROM product WHERE id > 0 AND status = 1 ";
        if($name != "") $sql_text .= "AND name LIKE '%$name%' ";
        if($params != "") $sql_text .= "AND params LIKE '%$params%' ";
        if($note != "") $sql_text .= "AND note LIKE '$note%' ";
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

        //echo $sql_text;

        $data = rootAndSort($CONNECTION, ID, 4, $SEP);
        $mas = explode("XXX", $data);
        $root = $mas[0];
        $sort = $mas[1];
        $count = $mas[2];
        $sort = explode($SEP, $sort);

        $SERVICES_LIST = "";
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $func = "onClick = 'windowProductView(".$data["id"].");'";
            $SERVICES_LIST .= "<div class = 'products_body_list_item'>"; //onClick = 'windowTireView(".$data["id"].");

            $action = "
                <div class = 'select select_small' style = 'width: 132px;' id = 'action_".$data["id"]."'>
                    <arrow></arrow>
                    <headline><i>Выбрать</i></headline>
                    <div data = '0' onClick = 'productSaleAdd(".$data["id"].", 3);'>Продать</div>
                    <div data = '1' onClick = 'windowDownAdd2(".$data["id"].", 3);'>Списать</div>
                    <div data = '2' onClick = 'windowReceiptAdd2(".$data["id"].", 3);'>Приемка</div>
                </div>";


            if($root[ 0] == 1) $mas[ 0] = "<div class = 'product_item text_overflow' id = 'p_".$data["id"]."' style = 'width: 95px;'  ".$func.">T".$data["article"]."</div>";
            if($root[ 1] == 1) $mas[ 1] = "<div class = 'product_item text_overflow' id = 'p_".$data["id"]."' style = 'width: 190px;' ".$func.">".$data["name"]."</div>";
            if($root[ 2] == 1) $mas[ 2] = "<div class = 'product_item text_overflow' id = 'p_".$data["id"]."' style = 'width: 182px;' ".$func.">".$data["params"]."</div>";
            if($root[ 3] == 1) $mas[ 3] = "<div class = 'product_item text_overflow' id = 'p_".$data["id"]."' style = 'width: 203px;' ".$func.">".$data["note"]."</div>";
            if($root[ 4] == 1) $mas[ 4] = "<div class = 'product_item text_overflow' id = 'p_".$data["id"]."' style = 'width: 95px;'  ".$func.">".$data["count"]."</div>";
            if($root[ 5] == 1) $mas[ 5] = "<div class = 'product_item text_overflow' id = 'p_".$data["id"]."' style = 'width: 122px;' ".$func.">".commaView($data["price_purchase"])."</div>";
            if($root[ 6] == 1) $mas[ 6] = "<div class = 'product_item text_overflow' id = 'p_".$data["id"]."' style = 'width: 136px;' ".$func.">".commaView($data["price_sale"])."</div>";
            if($root[ 7] == 1) $mas[ 7] = "<div class = 'product_item text_overflow' id = 'p_".$data["id"]."' style = 'width: 109px;' ".$func.">".commaView($data["price_wholesale"])."</div>";
            if($root[ 8] == 1) $mas[ 8] = "<div class = 'product_item'>".$action."</div>";

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
    if($_POST["methodName"] == "productsLoad"){      // Загрузка карточки товара
        $id = clean($_POST["id"]);

        $TEXT = file_get_contents("../../templates/admin/temp/products/product_card.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM product WHERE id = '$id'"));
        $barcode = $data["barcode"];
        $TITLE = $data["name"];
        $TEXT = str_replace("%ARTICLE%", "T".$data["article"], $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%PARAMS%", $data["params"], $TEXT);
        $TEXT = str_replace("%NOTE%", $data["note"], $TEXT);
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
        $name = $data["name"];
        $OTHER = "";
        $sql = mysqli_query($CONNECTION, "SELECT id, params FROM product WHERE name = '$name' AND id != '$id'");
        while($data = mysqli_fetch_array($sql)){
            $OTHER .= "<div onClick = 'windowProductView(".$data["id"].");'>".$data["params"]."</div>";
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
        $sql = mysqli_query($CONNECTION, "SELECT base, count FROM available WHERE barcode = '$barcode' AND count > 0");
        while($data = mysqli_fetch_array($sql)){
            $base = $data["base"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, code, name FROM base WHERE id = '$base'"));

            $nal .= "
                <div class = 'tire_count_str'>
                    <circle style = 'background: #".$temp["color"].";'></circle>
                    <span>".$temp["name"]."</span>
                    <span1>".$data["count"]."</span1>
                </div>";
        }
        $TEXT = str_replace("%NALICHIE%", $nal, $TEXT);

        echo $TITLE.$SEP.$TEXT;
    }
    if($_POST["methodName"] == "productsPriceChange"){      // Изменение стоимости
        $id = clean($_POST["id"]);
        $param = clean($_POST["param"]);
        $price = clean($_POST["price"]);
        $price = str_replace(" ", "", $price);
        switch($param){
            case 1: $sql = "price_purchase"; break;
            case 2: $sql = "price_sale "; break;
            case 3: $sql = "price_wholesale"; break;
        }
        mysqli_query($CONNECTION, "UPDATE product SET ".$sql." = '$price' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "productLoadRedact"){      // Загрузка карточки товара для редактирования
        $id = clean($_POST["id"]);
        $TEXT = file_get_contents("../../templates/admin/temp/products/product_redact.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM product WHERE id = '$id'"));
        $TEXT = str_replace("%PRODUCT_ARTICLE%", "D".$data["article"], $TEXT);
        $TEXT = str_replace("%ID%", $data["id"], $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%PARAMS%", $data["params"], $TEXT);
        $TEXT = str_replace("%NOTE%", $data["note"], $TEXT);
        $TEXT = str_replace("%PRICE_1%", getPriceTroyki($data["price_purchase"]), $TEXT);
        $TEXT = str_replace("%PRICE_2%", getPriceTroyki($data["price_sale"]), $TEXT);
        $TEXT = str_replace("%PRICE_3%", getPriceTroyki($data["price_wholesale"]), $TEXT);

        $photo = str_replace("%-%", "%-%img/", $data["photo"]);
        $photo = substr($photo, 0, -4);

        echo $TEXT."X-X-X".$photo;
    }
    if($_POST["methodName"] == "productsRedact"){   // Редактирование товара
        $id = clean($_POST["id"]);

        $product_name = clean($_POST["product_name"]);
        $product_params = clean($_POST["product_params"]);
        $product_note = clean($_POST["product_note"]);

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
            UPDATE product SET
                name = '$product_name',
                params = '$product_params',
                note = '$product_note',
                price_purchase = '$price_purchase',
                price_wholesale = '$price_wholesale',
                price_sale = '$price_sale',
                photo = '$photos'
            WHERE id = '$id'");
        echo mysqli_error($CONNECTION);
    }




?>