<?php

    require "../../settings.php";
    require "../../functions.php";

    proof();

    if($_POST["methodName"] == "warehousesStart"){      // Загрузка складов
        $TEXT = file_get_contents("../../templates/admin/temp/warehouses/warehouse_list.html");

        $BASES_LIST = "";
        $BASE_ID = 0;
        $BASE_COLOR = 0;
        $sql = mysqli_query($CONNECTION, "SELECT id, color, code FROM base");
        while($data = mysqli_fetch_array($sql)){
            if($BASE_ID == 0){
                $BASE_ID = $data["id"];
                $BASE_COLOR = $data["color"];
            }
            $BASES_LIST .= "<item id = 'base_".$data["id"]."' onClick = 'warehouseBaseActiveChange(".$data["id"].", \"".$data["color"]."\", this);'>".$data["code"]."</item>";
        }
        $BASES_LIST .= "";
        $TEXT = str_replace("%BASES_LIST%", $BASES_LIST, $TEXT);



        $TEXT = str_replace("%SERVER%", $SERVER, $TEXT);

        echo $TEXT.$SEP.$BASE_ID.$SEP.$BASE_COLOR;
    }
    if($_POST["methodName"] == "warehousesBaseLoad"){  // Загрузка базы
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM base WHERE id = '$id'"));
        $color = $data["color"];
        $BASE_INFO = "
            <div id = 'base_address'>
                <pimp></pimp>
                <span>".$data["address"]."</span>
            </div>";

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM storage WHERE base = '$id' AND name != 'На продажу' AND mother = 0"));
        $count = $temp[0];
        $BASE_INFO .= "<a id = 'base_storage_1' href = '".$SERVER."cp/settings/bases/".$id."' target = '_blank'>".$count." хранилищ</a>";
        if($data["vydacha"] == 1) $BASE_INFO .= "<div id = 'base_vydacha'><gal></gal>Пункт выдачи <span2 onClick = 'windowWarehousesTimeRedact(".$id.");'>".$data["time_1"]." — ".$data["time_2"]."</span2></div>";

        $count = 0;
        $occ = 0;
        $sql = mysqli_query($CONNECTION, "SELECT count, occupied FROM storage WHERE base = '$id' AND name != 'На продажу' AND mother = 0");
        while($temp = mysqli_fetch_array($sql)){
            $count += $temp["count"];
            $occ += $temp["occupied"];
        }
        if($count > 0){
            $occ2 = $occ;
            if($occ > $count) $occ2 = $count;
            $per = floor($occ*100/$count);
            $w = floor(225*$occ2/$count);
        }
        else {
            $per = 0;
            $w = 0;
        }

        $w2 = 225 - $w;
        $BASE_COUNT = "
            <div id = 'base_count'>
                <span>".$per."%</span><br>
                <div id = 'base_count_left' style = 'background: #".$color."; width: ".$w."px;'></div>
                <div id = 'base_count_right' style = 'background: #D8D8D8; width: ".$w2."px;'></div>
                <span style = 'float: right;'>".$occ." из ".$count."</span>
                <span>Наполнение</span>
            </div>
        ";

        echo $BASE_INFO.$SEP.$BASE_COUNT;
    }
    if($_POST["methodName"] == "warehousesLoad"){  // Загрузка шин, дисков, товаров и услуг
        $base = clean($_POST["base"]);
        $param = clean($_POST["param"]);

        $TEXT = "";

        if($param == 1){
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color FROM base WHERE id = '$base'"));
            $color = $data["color"];
            $STORAGE_LIST = "";
            $sql = mysqli_query($CONNECTION, "SELECT * FROM storage WHERE base = '$base' AND mother = 0");
            while($data = mysqli_fetch_array($sql))if(($data["name"] != "На продажу") || ($data["name"] == "На продажу" && $data["occupied"] > 0)){
                if($data["composite"] == 1){
                    $composite = "visibility: visible;";
                    $id = $data["id"];
                    $storage_son = "";
                    $sql_2 = mysqli_query($CONNECTION, "SELECT * FROM storage WHERE mother = '$id'");
                    while($data_2 = mysqli_fetch_array($sql_2)){
                        $storage_son .= "
                            <div class = 'storage_son' id = 'storage_".$data_2["id"]."' onClick = 'warehousesStorageLoad(".$data_2["id"].");'>
                                <circle style = 'background: #EC975E;'></circle>
                                <span>".$data_2["name"]."</span>
                            </div>";
                    }
                    $script = "warehousesStorageCompositeOpen(".$data["id"].");";
                }
                else{
                    $composite = "visibility: hidden;";
                    $storage_son = "";
                    $script = "warehousesStorageLoad(".$data["id"].");";
                }
                $STORAGE_LIST .= "
                    <div class = 'storage' id = 'storage_".$data["id"]."' onClick = '".$script."'>
                        <line style = '".$composite."'></line>
                        <span>".$data["name"]."</span>
                        ".getPercentLine($data["occupied"], $data["count"])."
                        ".$storage_son."
                    </div>
                ";
            }
            $TEXT = $STORAGE_LIST.$SEP."#".$color;
        }
        if($param == 2){
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color FROM base WHERE id = '$base'"));
            $color = $data["color"];
            $STORAGE_LIST = "";
            $sql = mysqli_query($CONNECTION, "SELECT * FROM storage WHERE base = '$base' AND mother = 0");
            while($data = mysqli_fetch_array($sql)){
                if($data["composite"] == 1){
                    $composite = "visibility: visible;";
                    $id = $data["id"];
                    $storage_son = "";
                    $sql_2 = mysqli_query($CONNECTION, "SELECT * FROM storage WHERE mother = '$id'");
                    while($data_2 = mysqli_fetch_array($sql_2)){
                        $storage_son .= "
                            <div class = 'storage_son' id = 'storage_".$data_2["id"]."' onClick = 'warehousesStorageLoad(".$data_2["id"].", 2);'>
                                <circle style = 'background: #EC975E;'></circle>
                                <span>".$data_2["name"]."</span>
                            </div>";
                    }
                    $script = "warehousesStorageCompositeOpen(".$data["id"].");";
                }
                else{
                    $composite = "visibility: hidden;";
                    $storage_son = "";
                    $script = "warehousesStorageLoad(".$data["id"].", 2);";
                }
                $STORAGE_LIST .= "
                    <div class = 'storage' id = 'storage_".$data["id"]."' onClick = '".$script."'>
                        <line style = '".$composite."'></line>
                        <span>".$data["name"]."</span>
                        ".getPercentLine($data["occupied"], $data["count"])."
                        ".$storage_son."
                    </div>
                ";
            }
            $TEXT = $STORAGE_LIST.$SEP."#".$color;
        }
        if($param == 3){
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
            $code = $data["code"];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM storage WHERE composite = 0 and mother = 0"));
            $count = $data[0];
            $TEXT = "
                <div id = 'warehouse_product_top'>
                    <div id = 'warehouse_product_top_left'>
                        <basecode>".$code."</basecode>
                        <a href = '".$SERVER."cp/settings/bases/".$base."' target = '_blank'>".$count." хранилищ</a>
                    </div>
                    <div class = 'button button_green button_auto inline' onClick = 'buttonClick(this);windowReceiptAdd(3);'>Принять</div>
                    <div class = 'button button_yellow button_auto inline' onClick = 'buttonClick(this);windowDownAdd();' style = 'margin-left: 27px;'>Списать</div>
                </div>
                <div id = 'warehouse_product_bottom'>
                    <div id = 'warehouse_product_bottom_head'>
                        <div class = 'but_empty' style = 'width: 79px;'>Склад</div>
                        <input type = 'text' class = 'input height-23' onKeyUp = 'warehousesProductsSearch();' placeholder = 'Товар' id = 'name' style = 'width: 397px;' />
                        <div class = 'but' style = 'width: 73px;' id = 'count' onClick = 'warehousesProductsSearch(1);'>Кол-во<triangle></triangle></div>
                        <div class = 'but_empty' style = 'width: 216px;'>Действие</div>
                    </div>
                    <div id = 'warehouse_product_bottom_list'></div></div>
            ";
        }
        if($param == 4){
            $TEXT = "<span>Доступные услуги</span>";
            $sql = mysqli_query($CONNECTION, "SELECT barcode, name, id FROM service WHERE status != 0");
            while($data = mysqli_fetch_array($sql)){
                $barcode = $data["barcode"];
                $id = $data["id"];
                $name = $data["name"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM available WHERE barcode = '$barcode' AND base = '$base'"));
                if($temp["id"] > 0) $param = 1; else $param = 0;
                $TEXT .= checkbox($id, $param, $name);
            }
            $TEXT .= "<br><div class = 'button button_green inline button_small' onClick = 'buttonClick(this);warehousesServicesSave();'>Сохранить</div>";
        }

        echo $TEXT;

        //echo 123;
    }
    if($_POST["methodName"] == "warehousesServicesSave"){  // Сохранение данных по услугам выбранной базы
        $base = clean($_POST["base"]);
        $str = clean($_POST["str"]);

        $str = str_replace("checkbox_", "", $str);
        $mas = explode($SEP, $str);
        for($i = 0; $i < count($mas); $i++){
            $temp = explode(":", $mas[$i]);
            $id = $temp[0];
            $param = $temp[1];

            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT barcode FROM service WHERE id = '$id'"));
            $barcode = $data["barcode"];

            if($param == 0){
                mysqli_query($CONNECTION, "DELETE FROM available WHERE barcode = '$barcode' AND base = '$base'");
            }
            if($param == 1){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM available WHERE barcode = '$barcode' AND base = '$base'"));
                if(!isset($data["id"])) mysqli_query($CONNECTION, "INSERT INTO available (barcode, base) VALUES ('$barcode', '$base')");
            }
        }
    }
    if($_POST["methodName"] == "warehousesProductsSearch"){  // Загрузка продуктов склада
        $base = clean($_POST["base"]);
        $name = clean($_POST["name"]);
        $count = clean($_POST["count"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
        $code = $data["code"];

        $sql_text = "SELECT product.name AS name, product.id AS id, product.article AS article, available.count AS count, product.params AS params, product.barcode AS barcode FROM product LEFT JOIN available ON product.barcode = available.barcode WHERE available.base = '$base' ";
        if($name != "") $sql_text .= "AND product.name LIKE '$name%' ";

        if($count == 1) $sql_text .= "ORDER BY available.count ";
        if($count == 2) $sql_text .= "ORDER BY available.count DESC ";

        $PRODUCT_LIST = "";
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $PRODUCT_LIST .= "
                <div class = 'warehouse_product_bottom_list_item'>
                    <div class = 'product_item' style = 'width: 95px;'>".$code."</div>
                    <div class = 'product_item' style = 'width: 407px;'>".$data["name"]."<span>".$data["params"]."</span></div>
                    <div class = 'product_item' style = 'width: 83px;'>".$data["count"]."</div>
                    <div class = 'product_item' style = 'width: 232px;'>
                        <span2 onClick = 'windowReceiptAdd2(".$data["id"].", 3);'>Принять</span2>
                        <span2 onClick = 'windowDownAdd2(".$data["id"].", 3);'>Списать</span2>
                        <span2 onClick = 'productSaleAdd(".$data["id"].", 3);'>Продать</span2>
                    </div>
                </div>";
        }
        echo $PRODUCT_LIST;
    }
    if($_POST["methodName"] == "warehousesStorageLoad"){  // Загрузка выбранного хранилища
        $id = clean($_POST["id"]);
        $param = clean($_POST["param"]);
        $TEXT = file_get_contents("../../templates/admin/temp/warehouses/storage.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM storage WHERE id = '$id'"));
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $base = $data["base"];
        if (strpos($data["code"], "SC") === false) $visible = 1; else $visible = 0;

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
        $TEXT = str_replace("%CODE%", $temp["code"]."-<b>".$data["code"]."</b>", $TEXT);
        $TEXT = str_replace("%COUNT%", $data["count"], $TEXT);
        $TEXT = str_replace("%OCCUPIED%", $data["occupied"], $TEXT);

        if($param == 1){
            $tires = 0;
            $sql = mysqli_query($CONNECTION, "SELECT available.count AS count FROM available LEFT JOIN tire ON available.barcode = tire.barcode WHERE tire.id > 0 AND available.storage = '$id'");
            while($temp = mysqli_fetch_array($sql)){
                $tires += $temp["count"];
            }
            $TEXT = str_replace("%TIRES%", $tires, $TEXT);
            $TEXT = str_replace("%TIRES_NAME%", "шинами", $TEXT);
            $TEXT = str_replace("%SCRIPT%", "warehousesTiresSearch", $TEXT);
            $TEXT = str_replace("%TIRE_NAME_2%", "Шина", $TEXT);
        }
        else{
            $tires = 0;
            $sql = mysqli_query($CONNECTION, "SELECT available.count AS count FROM available LEFT JOIN disk ON available.barcode = disk.barcode WHERE disk.id > 0 AND available.storage = '$id'");
            while($temp = mysqli_fetch_array($sql)){
                $tires += $temp["count"];
            }
            $TEXT = str_replace("%TIRES%", $tires, $TEXT);
            $TEXT = str_replace("%TIRES_NAME%", "дисками", $TEXT);
            $TEXT = str_replace("%SCRIPT%", "warehousesDisksSearch", $TEXT);
            $TEXT = str_replace("%TIRE_NAME_2%", "Диск", $TEXT);
        }

        $TEXT = str_replace("%ID%", $id, $TEXT);
        echo $TEXT.$SEP.$visible;
    }
    if($_POST["methodName"] == "warehousesTiresSearch"){       // Загрузка шин склада
        $storage = clean($_POST["storage"]);
        $name = clean($_POST["name"]);
        $count = clean($_POST["count"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$storage'"));
        $base = $data["base"];
        $code = $data["code"];

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
        $code = $data["code"]."-".$code;

        $sql_text = "
            SELECT
                tire.brand AS brand,
                tire.model AS model,
                tire.article AS article,
                available.count AS count,
                tire.w AS w,
                tire.barcode AS barcode,
                tire.h AS h,
                tire.r AS r,
                tire.season AS season,
                tire.id AS id,
                tire.article AS article
            FROM tire LEFT JOIN available
            ON tire.barcode = available.barcode
            WHERE available.storage = '$storage' AND available.count > 0";
        if($name != ""){
            $sql_text .= "AND (tire.brand LIKE '$name%' OR tire.model LIKE '$name%' ";
            $name = str_replace("S", "", $name);
            $name = str_replace("s", "", $name);
            $sql_text .= "OR tire.article LIKE '$name%') ";
        }

        if($count == 1) $sql_text .= " ORDER BY available.count ";
        if($count == 2) $sql_text .= " ORDER BY available.count DESC ";
        //echo $sql_text;
        $TIRE_LIST = "";
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $tire = $data["brand"]." ".$data["model"]." ";
            switch($data["season"]){
                case 0: $tire .= "(зима)"; break;
                case 1: $tire .= "(лето)"; break;
                case 2: $tire .= "(всесезон)"; break;
            }
            $tire .= " ".$data["w"]."/".$data["h"]."R".$data["r"];

            $diagramm = movementsStatProd($CONNECTION, 1, $data["id"]);

            $TIRE_LIST .= "
                <div class = 'storage_middle_list_item' data = '".$data["id"]."' onClick = 'warehousesTireLoad(this);'>
                    <div class = 'tire_item' style = 'width: 95px;'>".$code."</div>
                    <div class = 'tire_item' style = 'width: 407px;'>S".$data["article"]."<span>".$tire."</span></div>
                    <div class = 'tire_item' style = 'width: 83px;'>".$data["count"]."</div>
                    <div class = 'tire_item action' style = 'width: 290px;' >
                        <span2 onClick = 'windowReceiptAdd2(".$data["id"].", 1);'>Приёмка</span2>
                        <span2 onClick = 'windowReceiptAdd2(".$data["id"].", 1, 1);'>Пополнение</span2><br>
                        <span2 onClick = 'windowDownAdd2(".$data["id"].", 1);'>Списание</span2>
                        <span2 style = 'margin-right: 27px;' onClick = 'windowMovingAdd(".$data["id"].", 1);'>Перемещение</span2>
                        <span2 onClick = 'productSaleAdd(".$data["id"].", 1);'>Продажа</span2>
                    </div>
                </div>
                <div class = 'storage_middle_list_item2' id = 'tire_block_bottom_".$data["id"]."'>
                    <a href = '".$SERVER."cp/movements/S".$data["article"]."'>Движения</a>
                    ".$diagramm."
                </div>";
        }
        echo $TIRE_LIST;
    }
    if($_POST["methodName"] == "warehousesDisksSearch"){       // Загрузка дисков склада
        $storage = clean($_POST["storage"]);
        $name = clean($_POST["name"]);
        $count = clean($_POST["count"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$storage'"));
        $base = $data["base"];
        $code = $data["code"];

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
        $code = $data["code"]."-".$code;

        $sql_text = "
            SELECT
                disk.nomenclature AS nome,
                disk.article AS article,
                available.count AS count,
                disk.w AS w,
                disk.barcode AS barcode,
                disk.r AS r,
                disk.id AS id
            FROM disk LEFT JOIN available
            ON disk.barcode = available.barcode
            WHERE available.storage = '$storage' AND available.count > 0";
        if($name != ""){
            $sql_text .= "AND (disk.nomenclature LIKE '$name%' ";
            $name = str_replace("D", "", $name);
            $name = str_replace("d", "", $name);
            $sql_text .= "OR disk.article LIKE '$name%') ";
        }

        if($count == 1) $sql_text .= "ORDER BY available.count ";
        if($count == 2) $sql_text .= "ORDER BY available.count DESC ";

        $DISK_LIST = "";
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $tire = $data["nome"]." ";
            $tire .= " ".$data["w"]."/R".$data["r"];
            $DISK_LIST .= "
                <div class = 'storage_middle_list_item' onClick = 'warehousesTireLoad(this);'>
                    <div class = 'tire_item' style = 'width: 95px;'>".$code."</div>
                    <div class = 'tire_item' style = 'width: 407px;'>D".$data["article"]."<span>".$tire."</span></div>
                    <div class = 'tire_item' style = 'width: 83px;'>".$data["count"]."</div>
                    <div class = 'tire_item action' style = 'width: 290px;' >
                        <span2 onClick = 'windowReceiptAdd2(".$data["id"].", 2);'>Приёмка</span2>
                        <span2 onClick = 'windowReceiptAdd2(".$data["id"].", 2, 1);'>Пополнение</span2><br>
                        <span2 onClick = 'windowDownAdd2(".$data["id"].", 2);'>Списание</span2>
                        <span2 style = 'margin-right: 27px;' onClick = 'windowMovingAdd(".$data["id"].", 2);'>Перемещение</span2>
                        <span2 onClick = 'productSaleAdd(".$data["id"].", 2);'>Продажа</span2>
                    </div>
                </div>";
        }
        echo $DISK_LIST;
    }
    if($_POST["methodName"] == "warehousesBaseTimeChange"){       // Изменение времени работы склада
        $id = clean($_POST["id"]);
        $time_1 = clean($_POST["time_1"]);
        $time_2 = clean($_POST["time_2"]);

        mysqli_query($CONNECTION, "UPDATE base SET time_1 = '$time_1', time_2 = '$time_2' WHERE id = '$id'");
    }




?>