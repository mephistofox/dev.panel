<?php

    require "../../settings.php";
    require "../../functions.php";

    proof();

    require_once "../../vendor/autoload.php";

    if($_POST["methodName"] == "servicesStart"){      // Загрузка услуг
        $TEXT = file_get_contents("../../templates/admin/temp/services/service_list.html");

        $TEXT = str_replace("%HEAD%", rootAndSortHead($CONNECTION, ID, 1, $SEP), $TEXT);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM service"));
        $NUMBER = $data[0] + 1;
        $TEXT = str_replace("%NUMBER%", $NUMBER, $TEXT);


        echo $TEXT;
    }
    if($_POST["methodName"] == "servicesSearch"){      // Загрузка услуг
        $article = clean($_POST["article"]);
        $name = clean($_POST["name"]);
        $note = clean($_POST["note"]);
        $description = clean($_POST["description"]);
        $price = clean($_POST["price"]);

        $sql_text = "SELECT * FROM service WHERE id > 0 AND status = 1 ";
        if($name != "") $sql_text .= "AND name LIKE '%$name%' ";
        if($note != "") $sql_text .= "AND note LIKE '%$note%' ";
        if($description != "") $sql_text .= "AND description LIKE '%$description%' ";
        if($article == 1) $sql_text .= "ORDER BY article ";
        if($article == 2) $sql_text .= "ORDER BY article DESC ";
        if($price == 1) $sql_text .= "ORDER BY price_1 ";
        if($price == 2) $sql_text .= "ORDER BY price_1 DESC ";

        $data = rootAndSort($CONNECTION, ID, 1, $SEP);
        $mas = explode("XXX", $data);
        $root = $mas[0];
        $sort = $mas[1];
        $count = $mas[2];
        $sort = explode($SEP, $sort);

        $SERVICES_LIST = "";
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $SERVICES_LIST .= "<div class = 'services_body_list_item' onClick = 'windowServiceView(".$data["id"].", \"".$data["name"]."\");'>";
            if($data["type_auto"] == 0) $price = commaView($data["price_1"]);
            else $price = commaView($data["price_1"])."; ".commaView($data["price_2"])."; ".commaView($data["price_3"]);
            if($root[0] == 1) $mas[0] = "<div class = 'service_item text_overflow' style = 'width: 95px;'>U".$data["article"]."</div>";
            if($root[1] == 1) $mas[1] = "<div class = 'service_item text_overflow' style = 'width: 301px;'>".$data["name"]."</div>";
            if($root[2] == 1) $mas[2] = "<div class = 'service_item text_overflow' style = 'width: 170px;'>".$price."</div>";
            if($root[3] == 1) $mas[3] = "<div class = 'service_item text_overflow' style = 'width: 203px;'>".$data["note"]."</div>";
            if($root[4] == 1) $mas[4] = "<div class = 'service_item text_overflow' style = 'width: 413px;'>".$data["description"]."</div>";

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
    if($_POST["methodName"] == "servicesLoad"){      // Загрузка карточки услуги
        $id = clean($_POST["id"]);

        $TEXT = file_get_contents("../../templates/admin/temp/services/service_card.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM service WHERE id = '$id'"));
        $TEXT = str_replace("%ARTICLE%", "U".$data["article"], $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%DESCRIPTION%", $data["description"], $TEXT);
        $TEXT = str_replace("%NOTE%", $data["note"], $TEXT);
        $TEXT = str_replace("%COUNT_DEFAULT%", defaultCount(1, $data["count"]), $TEXT);
        $TEXT = str_replace("%ID%", $id, $TEXT);

        if($data["type_auto"] == 0) $PRICE = "<span>".commaView($data["price_1"])."</span>";
        else {
            $PRICE = radioImg(1, 1, "templates/img/car_1.png", commaView($data["price_1"]), "price", $SERVER);
            $PRICE .= radioImg(2, 0, "templates/img/car_2.png", commaView($data["price_2"]), "price", $SERVER);
            $PRICE .= radioImg(3, 0, "templates/img/car_3.png", commaView($data["price_3"]), "price", $SERVER);
        }
        $TEXT = str_replace("%PRICE%", $PRICE, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "servicesAddLoad"){      // Загрузка карточки добавления услуги
        $TEXT = file_get_contents("../../templates/admin/temp/services/service_add.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM service ORDER BY id DESC LIMIT 1"));
        $count = $data["id"];
        $count++;
        $count = getRight5Number($count);
        $TEXT = str_replace("%ARTICLE%", "U".$count, $TEXT);
        $TEXT = str_replace("%COUNT_DEFAULT%", defaultCount(1, 4), $TEXT);
        $TEXT = str_replace("%CHECK%", checkbox(1, 0, "<i>тип авто</i>", "servicesAddTypeChange()"), $TEXT);


        echo $TEXT;
    }
    if($_POST["methodName"] == "servicesRedactLoad"){      // Загрузка карточки редактирования услуги
        $id = clean($_POST["id"]);
        $TEXT = file_get_contents("../../templates/admin/temp/services/service_redact.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM service WHERE id = '$id'"));
        $TEXT = str_replace("%BARCODE%", $data["barcode"], $TEXT);
        $TEXT = str_replace("%ARTICLE%", "U".$data["article"], $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%DESCRIPTION%", $data["description"], $TEXT);
        $TEXT = str_replace("%NOTE%", $data["note"], $TEXT);
        $TEXT = str_replace("%PRICE_1%", commaView($data["price_1"]), $TEXT);
        $TEXT = str_replace("%PRICE_2%", commaView($data["price_2"]), $TEXT);
        $TEXT = str_replace("%PRICE_3%", commaView($data["price_3"]), $TEXT);
        $TEXT = str_replace("%CHECK%", checkbox(1, $data["type_auto"], "<i>тип авто</i>", "servicesAddTypeChange()"), $TEXT);
        if($data["type_auto"] == 0){
            $D_1 = "";
            $D_2 = "";
        }
        else {
            $D_1 = "display: none;";
            $D_2 = "display: inline-block;";
        }
        $TEXT = str_replace("%D_1%", $D_1, $TEXT);
        $TEXT = str_replace("%D_2%", $D_2, $TEXT);
        $TEXT = str_replace("%COUNT_DEFAULT%", defaultCount(1, $data["count"]), $TEXT);
        echo $TEXT;
    }
    if($_POST["methodName"] == "servicesAdd"){      // Добавление новой услуги
        $barcode = clean($_POST["barcode"]);
        $name = clean($_POST["name"]);
        $description = clean($_POST["description"]);
        $note = clean($_POST["note"]);
        $count = clean($_POST["count"]);
        $type_auto = clean($_POST["type_auto"]);
        $price_1 = clean(dotView($_POST["price_1"]));
        $price_2 = clean(dotView($_POST["price_2"]));
        $price_3 = clean(dotView($_POST["price_3"]));

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM service"));
        $article = $data[0];
        $article++;
        if(strlen($article) == 1) $article = "0000".$article;
        else {
            if(strlen($article) == 2) $article = "000".$article;
            else {
                if(strlen($article) == 3) $article = "00".$article;
                else{
                    if(strlen($article) == 4) $article = "0".$article;
                }
            }
        }

        mysqli_query($CONNECTION, "INSERT INTO service
                (  barcode,    article,    name,    description,    note,    type_auto,    price_1,    price_2,    price_3,    count)
            VALUES
                ('$barcode', '$article', '$name', '$description', '$note', '$type_auto', '$price_1', '$price_2', '$price_3', '$count')");
    }
    if($_POST["methodName"] == "servicesRedact" AND TYPE == 1){      // Редактирование услуги
        $id = clean($_POST["id"]);
        $barcode = clean($_POST["barcode"]);
        $name = clean($_POST["name"]);
        $description = clean($_POST["description"]);
        $note = clean($_POST["note"]);
        $count = clean($_POST["count"]);
        $type_auto = clean($_POST["type_auto"]);
        $price_1 = clean(dotView($_POST["price_1"]));
        $price_2 = clean(dotView($_POST["price_2"]));
        $price_3 = clean(dotView($_POST["price_3"]));

        mysqli_query($CONNECTION, "UPDATE service SET
                barcode = '$barcode',
                name = '$name',
                description = '$description',
                note = '$note',
                type_auto = '$type_auto',
                price_1 = '$price_1',
                price_2 = '$price_2',
                price_3 = '$price_3',
                count = '$count'
        WHERE id = '$id'");
    }
    if($_POST["methodName"] == "servicesSeasonAddLoad"){      // Загрузка карточки добавления сезонного хранения
        $TEXT = file_get_contents("../../templates/admin/temp/services/season_add.html");

        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . $googleAccountKeyFilePath);

        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/spreadsheets"]);

        $service = new Google_Service_Sheets($client);

        $range = $GST1;
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);

        $i = 0;
        $values = $response->getValues();
        foreach ($values as $row){
            $i++;
        }

        $TEXT = str_replace("%NUMBER%", $i, $TEXT);
        $TEXT = str_replace("%COUNT%", defaultCount(1, 4), $TEXT);
        $DIAMETR = "
            <div class = 'select' id = 'diametr_add'>
                <arrow></arrow>
                <headline>Диаметр</headline>
            ";
        $sql = mysqli_query($CONNECTION, "SELECT * FROM product_param WHERE type = 3");
        while($data = mysqli_fetch_array($sql)){
            $DIAMETR .= "<div data = '".$data["id"]."'>R".$data["value"]."</div>";
        }
        $DIAMETR .= "</div>";
        $TEXT = str_replace("%DIAMETR%", $DIAMETR, $TEXT);

        $OBJECT = "
            <div id = 'sa_str_right'>
                ".doubleButton(1, "Зима", "Лето")."
                ".tumbler(1, 1, "Резина")."
                <input type = 'text' class = 'input height-28' id = 'rezina_add' style = 'width: 260px; margin-top: 9px; margin-bottom: 20px;' onKeyUp = 'deleteBorderRed(this);' />
                ".tumbler(2, 1, "Диски")."
                <input type = 'text' class = 'input height-28' id = 'disk_add' style = 'width: 260px; margin-top: 9px;' onKeyUp = 'deleteBorderRed(this);' />
            </div>";

        $TEXT = str_replace("%OBJECT%", $OBJECT, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "servicesSeasonAdd"){      // Загрузка карточки добавления сезонного хранения
        $date = clean($_POST["date"]);
        $fio = clean($_POST["fio"]);
        $phone = clean($_POST["phone"]);
        $desc = clean($_POST["desc"]);
        $price = clean($_POST["price"]);
        $shink = clean($_POST["shink"]);

        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . $googleAccountKeyFilePath);

        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(["https://www.googleapis.com/auth/drive", "https://www.googleapis.com/auth/spreadsheets"]);

        $service = new Google_Service_Sheets($client);

        $range = $GST1;
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);

        $i = 0;
        $values = $response->getValues();
        foreach ($values as $row){
            if(isset($row[0])) $mas[$i][0] = $row[0]; else $mas[$i][0] = "";
            if(isset($row[1])) $mas[$i][1] = $row[1]; else $mas[$i][1] = "";
            if(isset($row[2])) $mas[$i][2] = $row[2]; else $mas[$i][2] = "";
            if(isset($row[3])) $mas[$i][3] = $row[3]; else $mas[$i][3] = "";
            if(isset($row[4])) $mas[$i][4] = $row[4]; else $mas[$i][4] = "";
            if(isset($row[5])) $mas[$i][5] = $row[5]; else $mas[$i][5] = "";
            if(isset($row[6])) $mas[$i][6] = $row[6]; else $mas[$i][6] = "";
            $i++;
        }
            $phone = str_replace("+7", "8", $phone);
            $mas[$i][0] = $date;
            $mas[$i][1] = $i;
            $mas[$i][2] = $fio;
            $mas[$i][3] = $phone;
            $mas[$i][4] = $desc;
            $mas[$i][5] = $price;
            $mas[$i][6] = $shink;

        $requestBody = new Google_Service_Sheets_ValueRange(array("values" => $mas));
        $params = ["valueInputOption" => "USER_ENTERED"];
        $service->spreadsheets_values->update($spreadsheetId, $range, $requestBody, $params);

        //6(шесть) месяцев
        $count_month = round($price/350);
        switch($count_month){
            case 1: $month = "1 (один) месяц"; break;
            case 2: $month = "2 (два) месяца"; break;
            case 3: $month = "3 (три) месяца"; break;
            case 4: $month = "4 (четыре) месяца"; break;
            case 5: $month = "5 (пять) месяцев"; break;
            case 6: $month = "6 (шесть) месяцев"; break;
            case 7: $month = "7 (семь) месяцев"; break;
            case 8: $month = "8 (восемь) месяцев"; break;
            case 9: $month = "9 (девять) месяцев"; break;
            defaul: $month = "Не удалось определить";
        }                    //strtotime('+1 MONTH', strtotime($date));
        $date_end = strtotime("+".$count_month." month", strtotime($date));
        $date_end = date("d.m.Y", $date_end);
        //$date_end = date("d.m.Y", $temp + strtotime("+".$count_month." month") - time());

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("../../docs/season.docx");
        $templateProcessor->setValue("NUMBER", $i);
        $templateProcessor->setValue("DATE", $date);
        $templateProcessor->setValue("NAME", $fio);
        $templateProcessor->setValue("PRICE", $price);
        $templateProcessor->setValue("DESC", $desc);
        $templateProcessor->setValue("DATEOFF", $date_end);
        $templateProcessor->setValue("MONTH", $month);
        $templateProcessor->setValue("MOBILE", $phone);

        $temp = generate_16(20).".docx";
        $templateProcessor->saveAs("../../temp/".$temp);

        echo $temp;
    }


?>