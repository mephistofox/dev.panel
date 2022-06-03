<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require "../../settings.php";
    require "../../functions.php";
    require "../../vendor/autoload.php";
    require_once 'CustomLogger.php';
    proof();

    if ($_POST['methodName'] == 'clientRemove') {
        $id = $_POST['client_id'];
        mysqli_query($CONNECTION,"DELETE FROM client WHERE id=$id");
        mysqli_query($CONNECTION,"DELETE FROM client_contact WHERE cId=$id");
    }
    if($_POST["methodName"] == "exitCabinet"){     // Выход из личного кабинета
        setcookie("id","", time()-10000, "/");
        setcookie("pass","", time()-10000, "/");
        setcookie("CURRENT_BASE","", time()-10000, "/");
        echo 1;
    }
    if($_POST["methodName"] == "getTemplateHTML"){    // Получение файла шаблона
        $url = clean($_POST["url"]);
        $TEXT = file_get_contents("../../templates/admin/temp/".$url);
        echo $TEXT;
    }
    if($_POST["methodName"] == "addressList"){    // Получение списка адресов
        $val = clean($_POST["val"]);

        $TEXT = DADATAGetAddressesList($val, $DADATA_KEY);
        $mas = explode($SEP, $TEXT);
        $TEXT = "";
        for($i = 1; $i < count($mas)-1; $i++) $TEXT .= "<div onClick = 'addressChange(this);' title = '".$mas[$i]."' class = 'text_overflow'>".$mas[$i]."</div>";
        echo $TEXT;
    }
    if($_POST["methodName"] == "contactList"){    // Получение списка контактов
        $val = clean($_POST["val"]);
        $TEXT = "";
        $sql = mysqli_query($CONNECTION, "SELECT name, phone, cId, id FROM client_contact WHERE phone LIKE '%$val%' OR name LIKE '%$val%'");
        while($data = mysqli_fetch_array($sql)){
            $address = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT address FROM client WHERE id=".$data["cId"]))['address'];
            $TEXT .= "<div data = '".$data["cId"]."' data2 = '".$data["id"]."' data-address='".$address."' title = '".$data["name"]." (".$data["phone"].")' class = 'text_overflow'  onClick = 'contactChange(this);'>".$data["name"]." (".$data["phone"].")</div>";
        }
        echo $TEXT;
    }
    if($_POST["methodName"] == "reasonList"){    // Получение списка причин для списания
        $val = clean($_POST["val"]);
        $TEXT = "";
        $sql = mysqli_query($CONNECTION, "SELECT value, id FROM product_param WHERE value LIKE '$val%' AND type = 8 AND status = 1");
        while($data = mysqli_fetch_array($sql)){
            $TEXT .= "<div data = 'reason_".$data["id"]."' data2 = '".$data["value"]."' title = '".$data["value"]."' class = 'text_overflow'  onClick = 'reasonChange(this);'>".$data["value"]."</div>";
        }
        echo $TEXT;
    }
    if($_POST["methodName"] == "contactList2"){    // Получение списка контактов при выборе клиента
        $id = clean($_POST["id"]);
        $TEXT = "";
        $sql = mysqli_query($CONNECTION, "SELECT name, phone, cId, id FROM client_contact WHERE cId = '$id'");
        while($data = mysqli_fetch_array($sql)){
            $TEXT .= "<div data = '".$data["cId"]."' data2 = '".$data["id"]."' title = '".$data["name"]." (".$data["phone"].")' class = 'text_overflow' onClick = 'contactChange(this);'>".$data["name"]." (".$data["phone"].")</div>";
        }
        echo $TEXT;
    }
    if($_POST["methodName"] == "docLoad"){      // Загрузка файлов на сервер
        $raz = $_FILES["file"]["name"];
        $param = clean($_POST["param"]);
        $name_old = $raz;
        $raz = explode(".", $raz);
        $raz = end($raz);
        $name = time()."-".generate_16(5).".".$raz;
        switch($param){
            case 1: move_uploaded_file($_FILES["file"]["tmp_name"], "../../docs/season.docx"); break;
            case 2: move_uploaded_file($_FILES["file"]["tmp_name"], "../../temp/".$name); break;
            case 3: move_uploaded_file($_FILES["file"]["tmp_name"], "../../docs/".$name); break;
        }

        echo $name.$SEP.$name_old;
    }
    if($_POST["methodName"] == "getColumn"){          // Получение списка колонок данного человека
        $param = clean($_POST["param"]);
        $data = rootAndSort($CONNECTION, ID, $param, $SEP);
        $mas = explode("XXX", $data);
        $root = $mas[0];
        $sort = $mas[1];
        $count = $mas[2];
        $sort = explode($SEP, $sort);

        $TEXT = "";
        for($i = 1; $i < $count*2; $i++){
            if($i%2 == 1){
                $num = $sort[$i];
                $visible = 0;
                if($sort[$i+1] == 1) $visible = 1;
                if($param == 1) switch($num){
                    case 0 : $name = "Артикул"     ; break;
                    case 1 : $name = "Наименование"; break;
                    case 2 : $name = "Стоимость"   ; break;
                    case 3 : $name = "Примечание"  ; break;
                    case 4 : $name = "Описание"    ; break;
                }
                if($param == 2) switch($num){
                    case 0 : $name = "Артикул"        ; break;
                    case 1 : $name = "Сезон"          ; break;
                    case 2 : $name = "Ширина"         ; break;
                    case 3 : $name = "Высота"         ; break;
                    case 4 : $name = "Радиус"         ; break;
                    case 5 : $name = "Производитель"  ; break;
                    case 6 : $name = "Модель"         ; break;
                    case 7 : $name = "ИН"             ; break;
                    case 8 : $name = "ИС"             ; break;
                    case 9 : $name = "RFT"            ; break;
                    case 10: $name = "Шип"            ; break;
                    case 11: $name = "Груз"           ; break;
                    case 12: $name = "Количество"     ; break;
                    case 13: $name = "Цена закупочная"; break;
                    case 14: $name = "Цена продажная" ; break;
                    case 15: $name = "Цена оптовая"   ; break;
                    case 16: $name = "Действия"       ; break;
                    case 17: $name = "Коды маркировки"; break;
                    case 18: $name = "Плательщик"     ; break;
                }
                if($param == 3) switch($num){
                    case 0 : $name = "Артикул"        ; break;
                    case 1 : $name = "Номенклатура"   ; break;
                    case 2 : $name = "Ширина"         ; break;
                    case 3 : $name = "Радиус"         ; break;
                    case 4 : $name = "Отверстий"      ; break;
                    case 5 : $name = "Межболт"        ; break;
                    case 6 : $name = "Вылет"          ; break;
                    case 7 : $name = "Ступица"        ; break;
                    case 8 : $name = "Цвет"           ; break;
                    case 9 : $name = "Количество"     ; break;
                    case 10: $name = "Цена закупочная"; break;
                    case 11: $name = "Цена продажная" ; break;
                    case 12: $name = "Цена оптовая"   ; break;
                    case 13: $name = "Действия"       ; break;
                }
                if($param == 4) switch($num){
                    case 0 : $name = "Артикул"        ; break;
                    case 1 : $name = "Наименование"   ; break;
                    case 2 : $name = "Параметры"      ; break;
                    case 3 : $name = "Примечание"     ; break;
                    case 4 : $name = "Количество"     ; break;
                    case 5 : $name = "Цена закупочная"; break;
                    case 6 : $name = "Цена продажная" ; break;
                    case 7 : $name = "Цена оптовая"   ; break;
                    case 8 : $name = "Действия"       ; break;
                }
                if($param == 5) switch($num){
                    case 0 : $name = "ID"         ; break;
                    case 1 : $name = "Дата"       ; break;
                    case 2 : $name = "Действие"   ; break;
                    case 3 : $name = "Объект"     ; break;
                    case 4 : $name = "Информация" ; break;
                    case 5 : $name = "Куда"       ; break;
                    case 6 : $name = "Откуда"     ; break;
                    case 7 : $name = "Количество" ; break;
                    case 8 : $name = "Было стало" ; break;
                    case 9 : $name = "Курьер"     ; break;
                    case 10: $name = "Действия"   ; break;
                    case 11: $name = "Дата план"  ; break;
                    case 12: $name = "Коды маркировки"; break;
                    case 13: $name = "Плательщик"     ; break;
                }
                if($param == 6) switch($num){
                    case 0 : $name = "ID"           ; break;
                    case 1 : $name = "Статус"       ; break;
                    case 2 : $name = "Получение"    ; break;
                    case 3 : $name = "Дата"         ; break;
                    case 4 : $name = "Выдача"       ; break;
                    case 5 : $name = "База"         ; break;
                    case 6 : $name = "Объект"       ; break;
                    case 7 : $name = "Клиент"       ; break;
                    case 8 : $name = "Курьер"       ; break;
                    case 9 : $name = "ТК"           ; break;
                    case 10: $name = "Цена закуп."  ; break;
                    case 11: $name = "Цена продажи" ; break;
                    case 12: $name = "Вид оплаты"   ; break;
                    case 13: $name = "Движение"     ; break;
                    case 14: $name = "Скидка, %"    ; break;
                    case 15: $name = "Скидка, Р"    ; break;
                    case 16: $name = "Менеджер"     ; break;
                    case 17: $name = "Коды маркировки"; break;
                    case 18: $name = "Плательщик"     ; break;
                }
                if($param == 7) switch($num){
                    case 0 : $name = "ID"         ; break;
                    case 1 : $name = "Дата"       ; break;
                    case 2 : $name = "Сумма"      ; break;
                    case 3 : $name = "Операция"   ; break;
                    case 4 : $name = "Кассир"     ; break;
                    case 5 : $name = "Вид оплаты" ; break;
                    case 6 : $name = "Сделка"     ; break;
                    case 7 : $name = "Клиент"     ; break;
                }
                $TEXT .= "
                    <div class = 'sort_item' id = 'sort_item_".$num."'>
                        <div class = 'sort_item_drag'></div>
                        ".checkbox($num, $visible, $name)."
                    </div>";
            }
        }

        echo $TEXT;

    }
    if($_POST["methodName"] == "columnSave"){                   // Сохранение видимости и порядка колонок
        $list = clean($_POST["list"]);
        $check = clean($_POST["check"]);
        $param = clean($_POST["param"]);

        $mas_visible = explode($SEP, $check);
        $TEXT = "%-%";
        //$list = "&amp;".$list;
        $list = str_replace("sort_item[]=", "", $list);
        $mas_column = explode("&amp;", $list);
        $count = 0;
        switch($param){
            case 1: $count_2 = 5 ; break;
            case 2: $count_2 = 19; break;
            case 3: $count_2 = 14; break;
            case 4: $count_2 = 9 ; break;
            case 5: $count_2 = 14; break;
            case 6: $count_2 = 19; break;
            case 7: $count_2 = 8 ; break;
        }
        for($i = 0; $i < $count_2; $i++){
            if(isset($mas_column[$i])){
                $count++;
                $t = $mas_column[$i];
                $TEXT .= $mas_column[$i]."%-%".$mas_visible[$t]."%-%";
            }
        }
        $TEXT = $count.$TEXT;
        switch($param){
            case 1: $sql = "service_sort = '".$TEXT."'"    ; break;
            case 2: $sql = "tire_sort = '".$TEXT."'"       ; break;
            case 3: $sql = "disk_sort = '".$TEXT."'"       ; break;
            case 4: $sql = "product_sort = '".$TEXT."'"    ; break;
            case 5: $sql = "movement_sort = '".$TEXT."'"   ; break;
            case 6: $sql = "sale_sort = '".$TEXT."'"       ; break;
            case 7: $sql = "transaction_sort = '".$TEXT."'"; break;
        }
        mysqli_query($CONNECTION, "UPDATE user_root SET ".$sql." WHERE uId = ".ID);
        //echo $TEXT;


        //echo 1;
    }
    if($_POST["methodName"] == "copy"){                   // Копирование информации
        $id = clean($_POST["id"]);
        $param = clean($_POST["param"]);

        switch($param){
            case "t": $sql = "SELECT * FROM tire WHERE id = '$id'"; break;
            case "d": $sql = "SELECT * FROM disk WHERE id = '$id'"; break;
            case "p": $sql = "SELECT * FROM product WHERE id = '$id'"; break;
        }

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
        switch($param){
            case "t": $TEXT = $data["brand"]." ".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"]." ".$data["price_purchase"]; break;
            case "d": $TEXT = $data["nomenclature"]." ".$data["w"]."/R".$data["r"]." ".$data["price_purchase"]; break;
            case "p": $TEXT = $data["name"]." ".$data["params"]." ".$data["price_purchase"]; break;
        }

        echo $TEXT;

    }
    if($_POST["methodName"] == "positionAddLoad"){                   // Загрузка информации для добавления новой позиции
        $param = clean($_POST["param"]);
        $TEXT = file_get_contents("../../templates/admin/temp/position_add.html");
        $TEXT = str_replace("%THIRD_BUTTON%", doubleButton(1, "Шина", "Диск", "Товар", $param), $TEXT);

        $TEXT = str_replace("%PARAM%", $param, $TEXT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM tire ORDER BY id DESC LIMIT 1"));
        $count = $data["id"];
        $count++;
        $count = getRight5Number($count);
        $TEXT = str_replace("%TIRE_ARTICLE%", "S".$count, $TEXT);
        $TEXT = str_replace("%BUTTONS_SEASON%", doubleButton(2, "<i>Зима</i>", "<i>Лето</i>", "<i>Всесезон</i>"), $TEXT);

        $W = "
            <div class = 'select' style = 'width: 110px;' id = 'w_tire'>
                <arrow></arrow>
                <headline>Ширина</headline>
                <input type = 'hidden' id = 'w_tire_hidden' value = '-1'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 1");
        while($data = mysqli_fetch_array($sql)) $W .= "<div data = '".$data["value"]."'>".$data["value"]."</div>";
        $W .= "</div>";
        $TEXT = str_replace("%TIRE_W%", $W, $TEXT);

        $H = "
            <div class = 'select' style = 'width: 110px;' id = 'h_tire'>
                <arrow></arrow>
                <headline>Высота</headline>
                <input type = 'hidden' id = 'h_tire_hidden' value = '-1'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 2");
        while($data = mysqli_fetch_array($sql)) $H .= "<div data = '".$data["value"]."' >".$data["value"]."</div>";
        $H .= "</div>";
        $TEXT = str_replace("%TIRE_H%", $H, $TEXT);

        $R = "
            <div class = 'select' style = 'width: 110px;' id = 'r_tire'>
                <arrow></arrow>
                <headline>Радиус</headline>
                <input type = 'hidden' id = 'r_tire_hidden' value = '-1'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 3");
        while($data = mysqli_fetch_array($sql)) $R .= "<div data = '".$data["value"]."'>R".$data["value"]."</div>";
        $R .= "</div>";
        $TEXT = str_replace("%TIRE_R%", $R, $TEXT);
        $TEXT = str_replace("%RFT%", tumbler("rft", 0), $TEXT);
        $TEXT = str_replace("%SPIKE%", tumbler("spike", 0), $TEXT);
        $TEXT = str_replace("%CARGO%", tumbler("cargo", 0), $TEXT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM disk ORDER BY id DESC LIMIT 1"));
        $count = $data["id"];
        $count++;
        $count = getRight5Number($count);
        $TEXT = str_replace("%DISK_ARTICLE%", "D".$count, $TEXT);
        $W = "
            <div class = 'select' style = 'width: 110px;' id = 'w_disk'>
                <arrow></arrow>
                <headline>Ширина</headline>
                <input type = 'hidden' id = 'w_disk_hidden' value = '-1'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 5");
        while($data = mysqli_fetch_array($sql)) $W .= "<div data = '".$data["value"]."'>".$data["value"]."</div>";
        $W .= "</div>";
        $TEXT = str_replace("%DISK_W%", $W, $TEXT);

        $R = "
            <div class = 'select' style = 'width: 110px;' id = 'r_disk'>
                <arrow></arrow>
                <headline>Радиус</headline>
                <input type = 'hidden' id = 'r_disk_hidden' value = '-1'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 6");
        while($data = mysqli_fetch_array($sql)) $R .= "<div data = '".$data["value"]."'>R".$data["value"]."</div>";
        $R .= "</div>";
        $TEXT = str_replace("%DISK_R%", $R, $TEXT);

        $HOLE = "
            <div class = 'select' style = 'width: 110px;' id = 'hole'>
                <arrow></arrow>
                <headline>Отверстий</headline>
                <input type = 'hidden' id = 'hole_hidden' value = '-1'>
                <div data = '3'>3</div>
                <div data = '4'>4</div>
                <div data = '5'>5</div>
                <div data = '6'>6</div>
                <div data = '10'>10</div>
            </div>";
        $TEXT = str_replace("%HOLE%", $HOLE, $TEXT);

        $COLOR = "
            <div class = 'select' style = 'width: 110px;' id = 'color'>
                <arrow></arrow>
                <headline>Цвет</headline>
                <input type = 'hidden' id = 'color_hidden' value = '-1'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 4");
        while($data = mysqli_fetch_array($sql)) $COLOR .= "<div data = '".$data["value"]."'>".$data["value"]."</div>";
        $COLOR .= "</div>";
        $TEXT = str_replace("%COLOR%", $COLOR, $TEXT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM product ORDER BY id DESC LIMIT 1"));
        $count = $data["id"];
        $count++;
        $count = getRight5Number($count);
        $TEXT = str_replace("%PRODUCT_ARTICLE%", "T".$count, $TEXT);
$PAYER = "";
        $temp = "
            <div class = 'select' id = 'payer' style = 'min-width: 234px;'>
                <arrow></arrow>
                <headline>Выбрать</headline>
                <input type = 'hidden' id = 'payer_hidden' value = '-1' />
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
        echo $TEXT;
    }
    if($_POST["methodName"] == "positionAddLoadPhotos"){                   // Загрузка уже добавленных изображений
        $photos = clean($_POST["photos"]);
        $TEXT = file_get_contents("../../templates/admin/temp/position_add_photos.html");

        $IMG = "";
        $mas = explode($SEP, $photos);
        for($i = 1; $i < count($mas); $i++) if($mas[$i] != ""){
            $IMG .= file_get_contents("../../templates/admin/temp/position_img_add.html");
            $IMG = str_replace("%SERVER%", $SERVER, $IMG);
            $IMG = str_replace("%URL%", $mas[$i], $IMG);
        }
        $TEXT = str_replace("%IMG%", $IMG, $TEXT);
        echo $TEXT;
    }
    if($_POST["methodName"] == "imgLoad"){      // Загрузка файлов на сервер
        $raz = $_FILES["file"]["name"];
        $name_old = $raz;
        $raz = explode(".", $raz);
        $raz = end($raz);
        $name = time()."-".generate_16(5).".".$raz;

        move_uploaded_file($_FILES["file"]["tmp_name"], "../../temp/".$name);

        echo $name.$SEP.$name_old;
    }
    if($_POST["methodName"] == "deleteImg"){  // Удаление изображения
        $name = clean($_POST["name"]);
        @unlink("../../".$name);
    }
    if($_POST["methodName"] == "productAdd"){  // Добавление нового продукта
        $param = clean($_POST["param"]);

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
        //$tire_code = clean($_POST["tire_code"]);

        $disk_nomenclature = clean($_POST["disk_nomenclature"]);
        $disk_bolt = clean($_POST["disk_bolt"]);
        $disk_vylet = clean($_POST["disk_vylet"]);
        $disk_hub = clean($_POST["disk_hub"]);
        $disk_w = clean($_POST["disk_w"]);
        $disk_r = clean($_POST["disk_r"]);
        $disk_hole = clean($_POST["disk_hole"]);
        $disk_color = clean($_POST["disk_color"]);

        $product_name = clean($_POST["product_name"]);
        $product_params = clean($_POST["product_params"]);
        $product_note = clean($_POST["product_note"]);

        $price_purchase = clean($_POST["price_purchase"]);
        $price_wholesale = clean($_POST["price_wholesale"]);
        $price_sale = clean($_POST["price_sale"]);
        $barcode = clean($_POST["barcode"]);

        $photos = clean($_POST["photos"]);
        $general_photo = clean($_POST["general_photo"]);

        if($barcode == "") $barcode = generate_barcode($CONNECTION);

        $flag = true;
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM tire WHERE barcode = '$barcode'"));
        if($temp["id"] > 0) $flag = false;
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM disk WHERE barcode = '$barcode'"));
        if($temp["id"] > 0) $flag = false;
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM product WHERE barcode = '$barcode'"));
        if($temp["id"] > 0) $flag = false;
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM service WHERE barcode = '$barcode'"));
        if($temp["id"] > 0) $flag = false;

        $mas = explode($SEP, $photos);
        $photos = $SEP;
        if(($key = array_search($general_photo, $mas)) !== false) unset($mas[$key]); else $general_photo = 0;
        if($general_photo != "0") $photos .= imgAdd($general_photo).$SEP;

        for($i = 1; $i < count($mas); $i++) if($mas[$i] != ""){
            $photos .= imgAdd($mas[$i]).$SEP;
        }

        if($flag){
            if($param == 1){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM tire ORDER BY id DESC LIMIT 1"));
                $count = $data["id"];
                $count++;
                $count = getRight5Number($count);
                mysqli_query($CONNECTION, "
                    INSERT INTO tire
                        (photo, barcode, article, season, w, h, r, brand, model, nagr, resist, rft, spike, cargo, price_purchase, price_wholesale, price_sale)
                    VALUES
                        ('$photos', '$barcode', '$count', '$tire_season', '$tire_w', '$tire_h', '$tire_r', '$tire_brand', '$tire_model', '$tire_nagr', '$tire_resist', '$tire_rft', '$tire_spike', '$tire_cargo', '$price_purchase', '$price_wholesale', '$price_sale')");
            }
            if($param == 2){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM disk ORDER BY id DESC LIMIT 1"));
                $count = $data["id"];
                $count++;
                $count = getRight5Number($count);
                mysqli_query($CONNECTION, "
                    INSERT INTO disk
                        (photo, barcode, article, nomenclature, w, r, hole, bolt, vylet, hub, color, price_purchase, price_wholesale, price_sale)
                    VALUES
                        ('$photos', '$barcode', '$count', '$disk_nomenclature', '$disk_w', '$disk_r', '$disk_hole', '$disk_bolt', '$disk_vylet', '$disk_hub', '$disk_color', '$price_purchase', '$price_wholesale', '$price_sale')
                ");
            }
            if($param == 3){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM product ORDER BY id DESC LIMIT 1"));
                $count = $data["id"];
                $count++;
                $count = getRight5Number($count);
                mysqli_query($CONNECTION, "
                    INSERT INTO product
                        (photo, barcode, article, name, params, note, price_purchase, price_wholesale, price_sale)
                    VALUES
                        ('$photos', '$barcode', '$count', '$product_name', '$product_params', '$product_note', '$price_purchase', '$price_wholesale', '$price_sale')
                ");
            }
            echo mysqli_insert_id($CONNECTION);
            //echo mysqli_error($CONNECTION);
        }
        else echo -1;
    }
    if($_POST["methodName"] == "barcodeArticleProof"){  // Проверка существования штрих кода либо артикула
        $code = clean($_POST["code"]);
        switch($code[0]){
            case "S": $type = 1; $param = 1; break;
            case "D": $type = 2; $param = 1; break;
            case "T": $type = 3; $param = 1; break;
            default : $type = 0; $param = 2; break;
        }
        $code = str_replace("S", "", $code);
        $code = str_replace("D", "", $code);
        $code = str_replace("T", "", $code);
        if($param == 1){
            $sql = "SELECT id FROM ";
            switch($type){
                case 1: $sql .= "tire"; break;
                case 2: $sql .= "disk"; break;
                case 3: $sql .= "product"; break;
            }
            $sql .= " WHERE article = '$code'";
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
            if($data["id"] > 0) echo $data["id"].$SEP.$type;
            else echo 0;
        }
        if($param == 2){
            $data_1 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM tire WHERE barcode = '$code'"));
            $data_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM disk WHERE barcode = '$code'"));
            $data_3 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM product WHERE barcode = '$code'"));
            if($data_1["id"] > 0 || $data_2["id"] > 0 || $data_3["id"] > 0){
                if($data_1["id"] > 0) echo $data_1["id"].$SEP."1";
                if($data_2["id"] > 0) echo $data_2["id"].$SEP."2";
                if($data_3["id"] > 0) echo $data_3["id"].$SEP."3";
            }
            else echo 0;
        }
    }
    if($_POST["methodName"] == "receiptProductLoad"){  // Загрузка окна приемки или попоплнения уже известного товара
        $id = clean($_POST["id"]);
        $type = clean($_POST["type"]);

        $sql = "SELECT * FROM ";
        switch($type){
            case 1: $sql .= "tire"; $a = "S"; break;
            case 2: $sql .= "disk"; $a = "D"; break;
            case 3: $sql .= "product"; $a = "T"; break;
        }
        $sql .= " WHERE id = '$id'";
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
        $TEXT = file_get_contents("../../templates/admin/temp/receipt_add.html");

        if($type == 1) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$data["article"]."</pid>
                <desc>".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"]."</desc>
            </div>";
        if($type == 2) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$data["article"]."</pid>
                <desc>".$data["nomenclature"]." ".$data["w"]."/R".$data["r"]."</desc>
            </div>";
        if($type == 3) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$data["article"]."</pid>
                <desc>".$data["name"].", ".$data["params"]."</desc>
            </div>";

        $TEXT = str_replace("%PRODUCT%", $PRODUCT, $TEXT);
        $TEXT = str_replace("%COUNT%", defaultCount(1, 4), $TEXT);

        $date = date("ymd");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE number LIKE '$date%'"));
        $count = $data[0] + 1;
        $count = getRight4Number($count);
        $TEXT = str_replace("%ID_STATUS%", "<circle></circle>".$date.$count, $TEXT);

        $PROVIDERS = "
            <div class = 'select' id = 'provider' style = 'min-width: 234px;'>
                <arrow></arrow>
                <headline>Поставщик</headline>
                <input type = 'hidden' id = 'provider_hidden' value = '-1' />
        ";
        $sql = mysqli_query($CONNECTION, "SELECT id, name FROM provider");
        while($data = mysqli_fetch_array($sql)){
           $PROVIDERS .= "<div data = '".$data["id"]."'>".$data["name"]."</div>";
        }
        $PROVIDERS .= "</div>";
        $TEXT = str_replace("%PROVIDERS%", $PROVIDERS, $TEXT);

        $CUREER = "
            <div class = 'select' id = 'cureer' style = 'min-width: 234px;'>
                <arrow></arrow>
                <headline>Курьер</headline>
                <input type = 'hidden' id = 'cureer_hidden' value = '-1' />
        ";
        $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type = 5");
        while($data = mysqli_fetch_array($sql)){
           $CUREER .= "<div data = '".$data["id"]."'>".$data["surname"]." ".$data["name"]."</div>";
        }
        $CUREER .= "</div>";
        $TEXT = str_replace("%CUREER%", $CUREER, $TEXT);

        $CLIENT = "
            <div class = 'select' id = 'client' style = 'min-width: 234px;'>
                <arrow></arrow>
                <headline>Клиент</headline>
                <input type = 'hidden' id = 'client_hidden' value = '-1' />
        ";
        $sql = mysqli_query($CONNECTION, "SELECT id, name FROM client");
        while($data = mysqli_fetch_array($sql)){
           $CLIENT .= "<div data = '".$data["id"]."' onClick = 'contactList2(this);'>".$data["name"]."</div>";
        }
        $CLIENT .= "</div>
            <br>
            <input type = 'text' class = 'input' id = 'client_phone' onKeyUp = 'deleteBorderRed(this);contactList(this);' />
            <list id = 'client_phone_list'></list>";
        $TEXT = str_replace("%CLIENT%", $CLIENT, $TEXT);

        $BASE_STORAGE = "<div id = 'base_storage'>";
        if(TYPE == 1){
            $BASE_STORAGE .= "
                <div class = 'select' id = 'base_1' style = 'width: 80px;'>
                    <arrow></arrow>
                    <headline>База</headline>
                    <input type = 'hidden' id = 'base_1_hidden' value = '-1' />
            ";
            $sql = mysqli_query($CONNECTION, "SELECT id, code, color FROM base");
            while($data = mysqli_fetch_array($sql)){
                if($type == 3) $BASE_STORAGE .= "<div data = '".$data["id"]."'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</div>";
                else $BASE_STORAGE .= "<div data = '".$data["id"]."' onClick = 'baseStorageLoad(this);'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</div>";
            }
            $BASE_STORAGE .= "</div><div id = 'storage_base'></div>";
        }
        else{
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM user WHERE id = ".ID));
            $base = $data["base"];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base WHERE id = '$base'"));
            $BASE_STORAGE .= "<input type = 'hidden' id = 'base_1_hidden' value = '".$base."'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"];
            $BASE_STORAGE .= "
                <div id = 'storage_base'>
                    <div class = 'select' id = 'storage_1' style = 'width: 80px;'>
                        <arrow></arrow>
                        <headline>Хран.</headline>";
            $sql = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$base' AND composite = 0");
            while($data = mysqli_fetch_array($sql)){
                $BASE_STORAGE .= "<div data = '".$data["id"]."' onClick = 'baseStorageProof(this);'>".$data["code"]."</div>";
            }
            $BASE_STORAGE .= "</div>";
        }

        $BASE_STORAGE .= "</div>";
        $TEXT = str_replace("%BASE_STORAGE%", $BASE_STORAGE, $TEXT);

        $PAYER = "";
        if($a == "S"){
            $temp = "
                <div class = 'select' id = 'payer' style = 'min-width: 234px;'>
                    <arrow></arrow>
                    <headline>Выбрать</headline>
                    <input type = 'hidden' id = 'payer_hidden' value = '-1' />
            ";
            $sql = mysqli_query($CONNECTION, "SELECT id, name, codes FROM payer WHERE status = 1");
            while($data = mysqli_fetch_array($sql)){
               $temp .= "<div data = '".$data["id"]."' data_2 = '".$data["codes"]."' onClick = 'tiresCodeWrite(this);'>".$data["name"]."</div>";
            }
            $temp .= "</div>";
            $PAYER = "
                <div class = 'receipt_str receipt_str_1'>
                    <title>Плательщик</title>
                    ".$temp."
                </div>
                <div class = 'receipt_str receipt_str_1' id = 'codes_back' style = 'display: none'>
                    <title>Коды маркировки</title>
                    <textarea class = 'textarea' style = 'width: 226px; height: 100px;' onKeyUp = 'deleteBorderRed(this);' id = 'codes'></textarea>
                </div>
            ";
        }
        $TEXT = str_replace("%PAYER%", $PAYER, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "baseStorageCount"){  // Возвращает количество свободных мест в хранилище
        $id = clean($_POST["id"]);
        $count = clean($_POST["count"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count, occupied FROM storage WHERE id = '$id'"));
        if($data["count"] - $data["occupied"] - $count >= 0) echo 1;
        else echo 0;
    }
    if($_POST["methodName"] == "baseStorageLoad"){  // Возвращает выпадающий список с хранилищами выбранной базы
        $id = clean($_POST["id"]);
        $BASE_STORAGE = "
            <div class = 'select' id = 'storage_1' style = 'width: 80px;'>
                <arrow></arrow>
                <headline>Хран.</headline>";
        $sql = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$id' AND composite = 0");
        while($data = mysqli_fetch_array($sql)){
            $BASE_STORAGE .= "<div data = '".$data["id"]."' onClick = 'baseStorageProof(this);'>".$data["code"]."</div>";
        }
        $BASE_STORAGE .= "</div>";
        echo $BASE_STORAGE;
    }
    if($_POST["methodName"] == "receiptAdd"){  // Приемка товара
        $id = clean($_POST["id"]);
        $type = clean($_POST["type"]);
        $provider = clean($_POST["provider"]);
        $count = clean($_POST["count"]);
        $price = clean($_POST["price"]);
        $base = clean($_POST["base"]);
        $storage = clean($_POST["storage"]);
        $payer = clean($_POST["payer"]);
        $codes = clean($_POST["code"]);
        $time = time();
        $number = date("ymd", $time);
        $temp = 0;

        //echo $codes;

        if($type == "1" && $codes != "0"){
            $mas = explode("%-%", $codes);
            for($i = 0; $i < $count; $i++){
                $code = $mas[$i];
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE code = \"$code\""));
                if(isset($data["id"])) $temp++;
            }
        }

        if($temp == 0){
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE number LIKE '$number%'"));
        $c = $temp[0];
        $c++;

        $number = $number.getRight4Number($c);

        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
        $code = $temp["code"];
        if($type < 3){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM storage WHERE id = '$storage'"));
            $code = $code." - ".$temp["code"];
        }
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM provider WHERE id = '$provider'"));
        $provider = $temp["name"];

        switch($type){
            case 1 : $p_type = "tire"; break;
            case 2 : $p_type = "disk"; break;
            case 3 : $p_type = "product"; break;
        }
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT barcode FROM $p_type WHERE id = '$id'"));
        $barcode = $temp["barcode"];
        if($type < 3){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND storage = '$storage'"));
        }
        else $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND base = '$base'"));

        if(isset($temp["count"])) $count_old = $temp["count"]; else $count_old = 0;
        $count_new = $count_old + $count;

        switch($type){
            case 1: $temp = "tire"; $clType = 'tires'; $t = "S"; break;
            case 2: $temp = "disk"; $clType = 'rims'; $t = "D"; break;
            case 3: $temp = "product"; $clType = 'other'; $t = "T"; break;
        }

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM $temp WHERE id = '$id'"));
        $article = $t.$data["article"];
        $customLogger = new CustomLogger($id, $clType, $DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME);
        if(isset($_POST['gross'])){
            $entry = ['b'=>$data['price_purchase'], 'g'=>$data['price_wholesale'], 'r'=>$data['price_sale'], 'price_purchase'=>clean($_POST['price']), 'price_wholesale'=>clean($_POST['gross']), 'price_sale'=>clean($_POST['retail'])];
            $customLogger->addLogEntry($entry);
            $query = "update `$temp` set `price_purchase`='".clean($_POST['price'])."', `price_wholesale`='".clean($_POST['gross'])."', `price_sale`='".clean($_POST['retail'])."' where `article`='".$data['article']."'";
            mysqli_query($CONNECTION, $query);
            unset($query);
        }

        mysqli_query($CONNECTION, "INSERT INTO movement
                (number, payer, article, p_id, p_type, date, action, kuda, otkuda, info, count, bef, aft, price) VALUES
                ('$number', '$payer', '$article', '$id', '$type', '$time', '1', '$code', '$provider', 'Поступление на склад', '$count', '$count_old', '$count_new', '$price')");
            // $movement = mysqli_insert_id($CONNECTION);

            if($type == "1" && $codes != "0"){
                for($i = 0; $i < $count; $i++) mysqli_query($CONNECTION, "INSERT INTO code (tire, payer, code, movement) VALUES ('$id', '$payer', \"".$mas[$i]."\", '$movement')");
            }
            if($type == "1" && $codes == "0"){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM code WHERE tire = '$id' AND payer = '$payer'"));
                if($data[0] == 0) mysqli_query($CONNECTION, "INSERT INTO code (tire, payer) VALUES ('$id', '$payer')");
            }

            echo 1;
        }
        else echo 0;
    }
    if($_POST["methodName"] == "loadBaseName"){     // Возвращает название и кружок базы
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, color FROM base WHERE id = '$id'"));
        $text = "<circle style = 'background: #".$data["color"]."'></circle>".$data["name"];

        echo $text;
    }
    if($_POST["methodName"] == "loadBaseTime"){     // Возвращает часы работы базы
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT time_1, time_2 FROM base WHERE id = '$id'"));
        echo $data["time_1"].$SEP.$data["time_2"];
    }
    if($_POST["methodName"] == "receiptDownLoad"){  // Загрузка окна списания товара
        $id = clean($_POST["id"]);
        $type = clean($_POST["type"]);

        $sql = "SELECT * FROM ";
        switch($type){
            case 1: $sql .= "tire"; $a = "S"; break;
            case 2: $sql .= "disk"; $a = "D"; break;
            case 3: $sql .= "product"; $a = "T"; break;
        }
        $sql .= " WHERE id = '$id'";
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
        $barcode = $data["barcode"];
        $TEXT = file_get_contents("../../templates/admin/temp/down_add.html");

        if($type == 1) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$data["article"]."</pid>
                <desc>".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"]."</desc>
            </div>";
        if($type == 2) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$data["article"]."</pid>
                <desc>".$data["nomenclature"]." ".$data["w"]."/R".$data["r"]."</desc>
            </div>";
        if($type == 3) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$data["article"]."</pid>
                <desc>".$data["name"].", ".$data["params"]."</desc>
            </div>";

        $TEXT = str_replace("%PRODUCT%", $PRODUCT, $TEXT);
        $TEXT = str_replace("%COUNT%", defaultCount(1, 4), $TEXT);

        $date = date("ymd");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE number LIKE '$date%'"));
        $count = $data[0] + 1;
        $count = getRight4Number($count);
        $TEXT = str_replace("%ID_STATUS%", "<circle></circle>".$date.$count, $TEXT);

        $nal = "";
        if($type < 3){
            $sql = mysqli_query($CONNECTION, "SELECT storage, count FROM available WHERE barcode = '$barcode' AND count > 0");
            while($data = mysqli_fetch_array($sql)){
                $storage = $data["storage"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, mother, code FROM storage WHERE id = '$storage'"));
                $base = $temp["base"];
                $code = $temp["code"];
                $mother = $temp["mother"];
                $count = $data["count"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, code, name FROM base WHERE id = '$base'"));
                $color = $temp["color"];
                $base_code = $temp["code"];
                $base_name = $temp["name"];
                if($mother > 0){
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM storage WHERE id = '$mother'"));
                    $name = "<circle style = 'background: #".$color."'></circle>".$base_code." - ".$temp["code"]." - ".$code;
                }
                else{
                    $name = "<circle style = 'background: #".$color."'></circle>".$base_code." - ".$code;
                }
                $nal .= "
                    <div class = 'storage_str' data = '".$storage."' onClick = 'productStorageClick(this);'>
                        <span1>".$count."</span1>
                        <rightcol>".$name."</rightcol>
                    </div>
                ";
            }
        }
        else {
            $sql = mysqli_query($CONNECTION, "SELECT base, count FROM available WHERE barcode = '$barcode'");
            while($data = mysqli_fetch_array($sql)){
                $base = $data["base"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, code, name FROM base WHERE id = '$base'"));
                $color = $temp["color"];
                $base_code = $temp["code"];
                $count = $data["count"];
                $nal .= "
                    <div class = 'storage_str' data = '".$base."' onClick = 'productStorageClick(this);'>
                        <span1>".$count."</span1>
                        <rightcol><circle style = 'background: #".$color."'></circle>".$base_code."</rightcol>
                    </div>
                ";
            }
        }
        $TEXT = str_replace("%STORAGE%", $nal, $TEXT);

        $OSNOVANIE = "
            <div class = 'select' style = 'width: 280px;' id = 'osnovanie'>
                <arrow></arrow>
                <headline>Основание</headline>
                <input type = 'hidden' id = 'osnovanie_hidden' value = '-1'>";
        $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 8 AND status = 1");
        while($data = mysqli_fetch_array($sql)) $OSNOVANIE .= "<div data = '".$data["value"]."' onClick = 'downAddOsnovanie(\"".$data["value"]."\");'>".$data["value"]."</div>";
        $OSNOVANIE .= "<div data = '-2' onClick = 'downAddOsnovanie(\"-2\");'>Другое основание</div></div><textarea onKeyUp = 'deleteBorderRed(this);deleteBorderRed(\"#osnovanie\");' id = 'osnovanie_textarea'></textarea>";
        $TEXT = str_replace("%OSNOVANIE%", $OSNOVANIE, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "downAdd"){  // Списывание товара
        $id = clean($_POST["id"]);
        $type = clean($_POST["type"]);
        $count = clean($_POST["count"]);
        $storage = clean($_POST["storage"]);
        $info = clean($_POST["info"]);

        switch($type){
            case 1: $temp = "tire"; $n = "S"; break;
            case 2: $temp = "disk"; $n = "D"; break;
            case 3: $temp = "product"; $n = "P"; break;
        }

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT article, barcode FROM $temp WHERE id = '$id'"));
        $article = $n.$data["article"];
        $barcode = $data["barcode"];
        if($type < 3) $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND storage = '$storage'"));
        else $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND base = '$storage'"));
        $count_old = $data["count"];
        $count_new = $count_old - $count;

        $time = time();
        $number = date("ymd", $time);
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE number LIKE '$number%'"));
        $c = $temp[0];
        $c++;
        $number = $number.getRight4Number($c);
        if($type < 3){
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$storage'"));
            $code = $data["code"];
            $base = $data["base"];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
            $code = $data["code"]." - ".$code;
        }
        else{
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$storage'"));
            $code = $data["code"];
        }
        echo $type;
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM product_param WHERE value = '$info' AND type = 8"));
        if(!isset($data["id"])) mysqli_query($CONNECTION, "INSERT INTO product_param (value, type, status) VALUES ('$info', '8', '2')");

        mysqli_query($CONNECTION, "INSERT INTO movement (number, article, p_id, p_type, date, action, info, otkuda, count, bef, aft)
        VALUES ('$number', '$article', '$id', '$type', '$time', '2', '$info', '$code', '$count', '$count_old', '$count_new')");

        echo mysqli_error($CONNECTION);
    }

    if($_POST["methodName"] == "additionAdd"){
        // Получение переменных
        $deal_type = clean($_POST["addition_type"]);
        $id = clean($_POST["id"]); // item id
        $type = clean($_POST["type"]); // item type
        $provider_id = clean($_POST["provider"]); // storage provider id
        $cureer_id = clean($_POST["cureer"]); // cureer id
        $deal_count = clean($_POST["count"]); // items count
        $price = clean($_POST["price"]); // items price
        $base = clean($_POST["base"]); // local base
        $storage = clean($_POST["storage"]); // local storage
        $addition_type = clean($_POST["addition_type"]); // type of movement
        $contact = clean($_POST["contact"]); // client id
        $date = clean($_POST["date"]); // date of movement
        $information = clean($_POST["information"]); // info about movement
        $time = time(); // current timestamp
        $convert_date = date("ymd", $time); // convert date
        $number = $convert_date.getRight4Number(mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE number LIKE '$convert_date%'"))[0]++); // generate number of deal
        // get info about base and storage
        $base_info = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, name FROM base WHERE id = '$base'"));
        $code = $base_info["code"];
        $base_name = $base_info["name"];
        // get name of storage provider
        $provider = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name,address FROM provider WHERE id=$provider_id"));
        $provider_name = $provider["name"];
        $provider_address = $provider["address"];
        // if set cureer get info about it
        $cureer = ($cureer_id>0) ? mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE id = '$cureer'")) : false; 
        $cureer_name = ($cureer) ? $cureer["surname"]." ".$cureer["name"]:'';
        $cureer_id = ($cureer) ? $cureer_id : 0;
        // set table of items
        switch($type){
            case 1: $table = "tire"; $clType = 'tires'; $product_index = "S"; $p_type=1; break;
            case 2: $table = "disk"; $clType = 'rims'; $product_index = "D"; $p_type=2; break;
            case 3: $table = "product"; $clType = 'other'; $product_index = "T"; $p_type=3; break;
        }

        // get barcode and article of item
        $item = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM $table WHERE id = '$id'"));
        $barcode = $item["barcode"];
        $article = $product_index.$item["article"];
        $customLogger = new CustomLogger($id, $clType, $DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME);
        if(isset($_POST['gross'])){
            $entry = ['b'=>$item['price_purchase'], 'g'=>$item['price_wholesale'], 'r'=>$item['price_sale'], 'price_purchase'=>clean($_POST['price']), 'price_wholesale'=>clean($_POST['gross']), 'price_sale'=>clean($_POST['retail'])];
            $customLogger->addLogEntry($entry);
            $query = "update `$table` set `price_purchase`='".clean($_POST['price'])."', `price_wholesale`='".clean($_POST['gross'])."', `price_sale`='".clean($_POST['retail'])."' where `article`='".$item['article']."'";
            mysqli_query($CONNECTION, $query);
            unset($query);
        }
        // if type is tire, disk or product, than set storage and get cout of item
        if($type < 3){ 
            $storage_info = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM storage WHERE id = '$storage'"));
            $code = $code." - ".$storage_info["code"];
            $id_storage = $storage;
            $item_current_count = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND storage = '$storage'"));
        } else {
            $item_current_count = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND base = '$base'"));
        }

        // set new count of item
        if(isset($item_current_count["count"])) $item_current_count = $item_current_count["count"]; else $item_current_count = 0;
        $count_new = $deal_count;
        
        // date plane check and set movement status
        $date_now = date("d.m.Y");
        $status = 0;
        $date = strtotime($date);

        if ($deal_type == 2) {
            // Перемещение под клиента
            $client = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT cId, name, phone FROM client_contact WHERE id = '$contact'"));
            $client_id = $client["cId"];
            $client_name = $client["name"];
            $client_phone = $client["phone"];
            $client_address = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT address FROM client WHERE id = '$client_id'"))['address'];
            $info = "Для: ".$client_name." (".$client_phone.")"." ".$information;

        } else {
            $info = "Пополнение";
            $info .= " ".$information;
        }

        // CREATE NEW MOVEMENT ITEM
        mysqli_query($CONNECTION, "INSERT INTO movement (
            number,article,p_id,p_type,date,action,kuda,otkuda,info,
            count,bef,aft,price,cureer,date_or,status
        ) VALUES (
            '$number','$article','$id','$type','$time','4','$code','$provider_address','$info',
            '$deal_count','$item_current_count','$count_new','$price','$cureer','$date','$status'
        )");

        $movement_id = mysqli_insert_id($CONNECTION);
        
        // CREATE NEW SALE ITEM
        if ($deal_type == 2) {
            $sale = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM sale ORDER BY id DESC LIMIT 1"));
            if(isset($sale["id"]))$number = $sale["id"] + 1; else $number = 1;
            $number = getRight8Number($number);
            $poluch = ($client_address) ? "Доставка" : "Пункт выдачи";
            $vydacha = ($client_address) ? $client_address : mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT address FROM base WHERE code = '$base_name'"))['address'];
            $vydacha = ($vydacha) ? $vydacha : "Калинина д5";
            $base_sale = $base;

            $date_now = time();
            $status = 1;

            mysqli_query($CONNECTION, "INSERT INTO sale 
                (
                    number,date,date_plan,poluchenie,vydacha,base_sale,client,
                    client_name,client_phone,cureer,cureer_id,manager,status
                ) VALUES (
                    '$number','$date_now','$date','$poluch','$vydacha','$base_sale','$client_id',
                    '$client_name','$client_phone','$cureer','$cureer_id','".ID."','$status'
                )"
            );

            $sale_id = mysqli_insert_id($CONNECTION);
            mysqli_query($CONNECTION, "UPDATE movement SET sale = '$sale_id' WHERE id = '$movement_id'");

            // if (!$client) {
            //     if(productMove($CONNECTION, 0, $id_storage, $type, $id, $deal_count)) mysqli_query($CONNECTION, "UPDATE movement SET status = 1, date_finish = '".time()."' WHERE id = '$movement_id'");  
            // }
            
            $price_purchase = 0;
            $price_sale = 0;

            $item_info = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article, price_purchase, price_sale FROM $table WHERE barcode = '$barcode'"));
            if ($item_info) {
                $p_id = $item_info["id"];
                $price_purchase += $deal_count * $item_info["price_purchase"];
                $price_sale += $deal_count * $item_info["price_sale"];
            }
            mysqli_query($CONNECTION, "INSERT INTO sale_product
                ( sale, barcode, p_id, p_type, p_param, count, otkuda) VALUES
                ('$sale_id', '$barcode', '$p_id', '$p_type', 0, '$deal_count', '$code')");

            mysqli_query($CONNECTION, "UPDATE sale SET price_purchase = '$price_purchase', price_sale = '$price_sale' WHERE id = '$sale_id'");
            mysqli_query($CONNECTION, "INSERT INTO sale_action (sale, user, date, status) VALUES ('$sale_id', '".ID."', '$date_now', '$status')");
        }

    }
        
    if($_POST["methodName"] == "movingAddLoad"){  // Загрузка окна перемещения товара
        $id = clean($_POST["id"]);
        $type = clean($_POST["type"]);

        $sql = "SELECT * FROM ";
        switch($type){
            case 1: $sql .= "tire"; $a = "S"; break;
            case 2: $sql .= "disk"; $a = "D"; break;
        }
        $sql .= " WHERE id = '$id'";
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
        $barcode = $data["barcode"];
        $TEXT = file_get_contents("../../templates/admin/temp/moving_add.html");

        if($type == 1) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$data["article"]."</pid>
                <desc>".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"]."</desc>
            </div>";
        if($type == 2) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$data["article"]."</pid>
                <desc>".$data["nomenclature"]." ".$data["w"]."/R".$data["r"]."</desc>
            </div>";

        $TEXT = str_replace("%PRODUCT%", $PRODUCT, $TEXT);
        $TEXT = str_replace("%COUNT%", defaultCount(1, 4), $TEXT);

        $date = date("ymd");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE number LIKE '$date%'"));
        $count = $data[0] + 1;
        $count = getRight4Number($count);
        $TEXT = str_replace("%ID_STATUS%", "<circle></circle>".$date.$count, $TEXT);

        $nal = "";
        if($type < 3){
            $sql = mysqli_query($CONNECTION, "SELECT storage, count FROM available WHERE barcode = '$barcode' AND count > 0");
            while($data = mysqli_fetch_array($sql)){
                $storage = $data["storage"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, mother, code FROM storage WHERE id = '$storage'"));
                $base = $temp["base"];
                $code = $temp["code"];
                $mother = $temp["mother"];
                $count = $data["count"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, code, name FROM base WHERE id = '$base'"));
                $color = $temp["color"];
                $base_code = $temp["code"];
                $base_name = $temp["name"];
                if($mother > 0){
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM storage WHERE id = '$mother'"));
                    $name = "<circle style = 'background: #".$color."'></circle>".$base_code." - ".$temp["code"]." - ".$code;
                }
                else{
                    $name = "<circle style = 'background: #".$color."'></circle>".$base_code." - ".$code;
                }
                $nal .= "
                    <div class = 'storage_str' data = '".$storage."' onClick = 'productStorageClick(this);'>
                        <span1>".$count."</span1>
                        <rightcol>".$name."</rightcol>
                    </div>
                ";
            }
        }
        else {
            $sql = mysqli_query($CONNECTION, "SELECT base, count FROM available WHERE barcode = '$barcode' AND count > 0");
            while($data = mysqli_fetch_array($sql)){
                $base = $data["base"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, code, name FROM base WHERE id = '$base'"));
                $color = $temp["color"];
                $base_code = $temp["code"];
                $count = $data["count"];
                $nal .= "
                    <div class = 'storage_str' data = '".$base."' onClick = 'productStorageClick(this);'>
                        <span1>".$count."</span1>
                        <rightcol><circle style = 'background: #".$color."'></circle>".$base_code."</rightcol>
                    </div>
                ";
            }
        }
        $TEXT = str_replace("%STORAGE%", $nal, $TEXT);

        $CUREER = "
            <div class = 'select' id = 'cureer' style = 'min-width: 234px;'>
                <arrow></arrow>
                <headline>Курьер</headline>
                <input type = 'hidden' id = 'cureer_hidden' value = '-1' />
        ";
        $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type = 5");
        while($data = mysqli_fetch_array($sql)){
           $CUREER .= "<div data = '".$data["id"]."'>".$data["surname"]." ".$data["name"]."</div>";
        }
        $CUREER .= "</div>";
        $TEXT = str_replace("%CUREER%", $CUREER, $TEXT);

        $BASE_STORAGE = "<div id = 'base_storage'>";
        if(TYPE < 3){
            $BASE_STORAGE .= "
                <div class = 'select' id = 'base_1' style = 'width: 80px;'>
                    <arrow></arrow>
                    <headline>База</headline>
                    <input type = 'hidden' id = 'base_1_hidden' value = '-1' />
            ";
            $sql = mysqli_query($CONNECTION, "SELECT id, code, color FROM base");
            while($data = mysqli_fetch_array($sql)){
                if($type == 3) $BASE_STORAGE .= "<div data = '".$data["id"]."'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</div>";
                else $BASE_STORAGE .= "<div data = '".$data["id"]."' onClick = 'baseStorageLoad(this);'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</div>";
            }
            $BASE_STORAGE .= "</div><div id = 'storage_base'></div>";
        }
        else{
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM user WHERE id = ".ID));
            $base = $data["base"];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base WHERE id = '$base'"));
            $BASE_STORAGE .= "<input type = 'hidden' id = 'base_1_hidden' value = '".$base."'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"];
            $BASE_STORAGE .= "
                <div id = 'storage_base'>
                    <div class = 'select' id = 'storage_1' style = 'width: 80px;'>
                        <arrow></arrow>
                        <headline>Хран.</headline>";
            $sql = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$base' AND composite = 0");
            while($data = mysqli_fetch_array($sql)){
                $BASE_STORAGE .= "<div data = '".$data["id"]."' onClick = 'baseStorageProof(this);'>".$data["code"]."</div>";
            }
            $BASE_STORAGE .= "</div>";
        }

        $BASE_STORAGE .= "</div>";
        $TEXT = str_replace("%BASE_STORAGE%", $BASE_STORAGE, $TEXT);



        echo $TEXT;
    }
    if($_POST["methodName"] == "movingAdd"){  // Перемещение товара
        $id = clean($_POST["id"]);
        $type = clean($_POST["type"]);
        $count = clean($_POST["count"]);
        $storage = clean($_POST["storage"]);
        $kuda = clean($_POST["kuda"]);
        $cureer = clean($_POST["cureer"]);
        $date_plan = clean($_POST["date_plan"]);

        switch($type){
            case 1: $temp = "tire"; $n = "S"; break;
            case 2: $temp = "disk"; $n = "D"; break;
        }

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT article, barcode FROM $temp WHERE id = '$id'"));
        $article = $n.$data["article"];
        $barcode = $data["barcode"];
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND storage = '$storage'"));
        $count_old = $data["count"];
        $count_new = $count_old - $count;

        $time = time();
        $number = date("ymd", $time);
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE number LIKE '$number%'"));
        $c = $temp[0];
        $c++;
        $number = $number.getRight4Number($c);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$storage'"));
        $code = $data["code"];
        $base = $data["base"];
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
        $code = $data["code"]." - ".$code;

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$kuda'"));
        $kuda = $data["code"];
        $base = $data["base"];
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
        $kuda = $data["code"]." - ".$kuda;

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = '$cureer'"));
        $cureer = $data["surname"]." ".$data["name"];

        $status = 0;
        $date_or = strtotime($date_plan);

        mysqli_query($CONNECTION, "INSERT INTO movement (date_or, number, article, p_id, p_type, date, action, info, otkuda, kuda, count, bef, aft, cureer, status)
        VALUES ('$date_or', '$number', '$article', '$id', '$type', '$time', '3', 'Перемещение', '$code', '$kuda',  '$count', '$count_old', '$count_new', '$cureer', '$status')");

        echo mysqli_error($CONNECTION);
    }
    if($_POST["methodName"] == "productSaleAdd"){  // Добавление товара в отложенные покупки
        $id = clean($_POST["id"]);
        $type = clean($_POST["type"]);
        $param = clean($_POST["param"]);
        $count = clean($_POST["count"]);
        $flag = 1;

        if($type == 5){
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM season_temp WHERE price = '$id'"));
            if($data["id"] > 0) $id = $data["id"];
            else{
                $barcode = generate_barcode($CONNECTION);
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM season_temp ORDER BY id DESC LIMIT 1"));
                $count = $data["id"];
                $count++;
                $count = getRight5Number($count);
                mysqli_query($CONNECTION, "
                    INSERT INTO season_temp
                        (barcode, article, name, price)
                    VALUES
                        ('$barcode', '$count', 'Хранение шин', '$id')");
                $id = mysqli_insert_id($CONNECTION);
            }
        }

        if(isset($_COOKIE["prod"])){
            $text = $_COOKIE["prod"];
            $pos = strripos($text, $type."-".$id.".".$param.".".$count."X");

            if ($pos === false) {
                $text = $_COOKIE["prod"].$type."-".$id.".".$param.".".$count."X";
                setcookie("prod", $text, time() + 6048000, "/; samesite=lax");
            }
            else $flag = 0;

        }
        else setcookie("prod", $type."-".$id.".".$param.".".$count."X", time() + 6048000, "/; samesite=lax");
        echo $flag;
    }
    if($_POST["methodName"] == "test"){
        echo "123";
    }
    if($_POST["methodName"] == "productDelete"){   // Удаление товара
        $id = clean($_POST["id"]);
        $type = clean($_POST["type"]);
        $payer = clean($_POST["payer"]);

        switch($type){
            case 1: $name = "tire"; break;
            case 2: $name = "disk"; break;
            case 3: $name = "product"; break;
            case 4: $name = "service"; break;
            default: $name = "";
        }

        if($type == '1' && $payer != '0'){
            mysqli_query($CONNECTION, "DELETE FROM code WHERE tire = '$id' AND payer = '$payer'");
            file_put_contents('../../logs/query_logs/productDelete.txt', "DELETE FROM code WHERE tire = '$id' AND payer = '$payer'\r\n", 8);
        }
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT barcode FROM ".$name." WHERE id = '$id'"));
        if(isset($data["barcode"])){
            $barcode = $data["barcode"];
            mysqli_query($CONNECTION, "DELETE FROM available WHERE barcode = '$barcode'");
            if($type < 3) allStorageCalc($CONNECTION);
            mysqli_query($CONNECTION, "UPDATE ".$name." SET status = 0, count = 0 WHERE id = '$id'");
        }
    }
    if($_POST["methodName"] == "codesLoad"){   // Загрузка кодов маркировки
        $id = clean($_POST["id"]);
        $param = clean($_POST["param"]);
        $payer = clean($_POST["payer"]);
        $codes = "";
        if($param == 1) $sql = mysqli_query($CONNECTION, "SELECT code FROM code WHERE movement = ".$id);
        if($param == 2) $sql = mysqli_query($CONNECTION, "SELECT code FROM code WHERE sale = ".$id);
        if($param == 3) $sql = mysqli_query($CONNECTION, "SELECT code, id, img FROM code WHERE tire = '$id' AND payer = '$payer' AND sale = 0");
        while($data = mysqli_fetch_array($sql)){
            $codes .= $data["code"]."<br>";
            if($param == 3){
                $id = $data["id"];
                if($data["img"] == ""){

                    $code = $data["code"];
                    $code = str_replace("&#40;", "(", $code);
                    $code = str_replace("&#41;", ")", $code);
                    $code = str_replace("&#706;", "<", $code);
                    $code = str_replace("&#707;", ">", $code);
                    $code = str_replace("&", "%26", $code);
                    $img = file_get_contents("https://barcode.tec-it.com/barcode.ashx?code=GS1DataMatrix&translate-esc=on&data=".$code);
                    $img = 'data:image/jpg;base64,' . base64_encode($img);
                    mysqli_query($CONNECTION, "UPDATE code SET img = '$img' WHERE id = '$id'");
                    $codes .= "<img src = '".$img."' /><div class = 'link_blue_4' onClick = 'codeImgReload(".$id.", this);'>Обновить...</div><br><br>";
                }
                else $codes .= "<img src = '".$data["img"]."' /><div class = 'link_blue_4' onClick = 'codeImgReload(".$id.", this);'>Обновить...</div><br><br>";
            }
        }
        $codes = substr_replace($codes, "", -2);
        echo $codes;
    }
    if($_POST["methodName"] == "codesLoad2"){   // Загрузка кодов маркировки с картинками у продажи
        $sale = clean($_POST["sale"]);
        $sql = mysqli_query($CONNECTION, "SELECT code, id FROM code WHERE sale = ".$sale);
        $codes = "";
        while($data = mysqli_fetch_array($sql)){
            $codes .= $data["code"]."<br>";
            $id = $data["id"];
            $code = $data["code"];
            $code = str_replace("&#40;", "(", $code);
            $code = str_replace("&#41;", ")", $code);
            $code = str_replace("&#706;", "<", $code);
            $code = str_replace("&#707;", ">", $code);
            $code = str_replace("&", "%26", $code);
            $img = file_get_contents("https://barcode.tec-it.com/barcode.ashx?code=GS1DataMatrix&translate-esc=on&data=".$code);
            $img = 'data:image/jpg;base64,' . base64_encode($img);
            $codes .= "<img src = '".$img."' /><div class = 'link_blue_4' onClick = 'codeImgReload(".$id.", this);'>Обновить...</div><br><br>";
        }
        $codes = substr_replace($codes, "", -2);
        echo $codes;
    }
    if($_POST["methodName"] == "codeImgReload"){   // Обновление картинки у кода
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, payer, tire FROM code WHERE id = '$id'"));
        $code = $data["code"];
        $code = str_replace("&#40;", "(", $code);
        $code = str_replace("&#41;", ")", $code);
        $code = str_replace("&#706;", "<", $code);
        $code = str_replace("&#707;", ">", $code);
        $code = str_replace("&", "%26", $code);
        $img = file_get_contents("https://barcode.tec-it.com/barcode.ashx?code=GS1DataMatrix&translate-esc=on&data=".$code);
        $img = 'data:image/jpg;base64,' . base64_encode($img);
        mysqli_query($CONNECTION, "UPDATE code SET img = '$img' WHERE id = '$id'");
        echo $data["tire"]."%-%".$data["payer"];
    }
    if($_POST["methodName"] == "checkUserType"){
        $userId = $_COOKIE["id"];
        $userData = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id,type  FROM user WHERE id = '$userId'"));
        if($userData['type'] == '1'){
            echo '1';
        }else{
            echo '0';
        }
    }

    
?>