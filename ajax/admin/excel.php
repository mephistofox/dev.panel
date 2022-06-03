<?php

    require "../../settings.php";
    require "../../functions.php";
    require "../../vendor/autoload.php";

    proof(); 

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


    if($_POST["methodName"] == "clientsDownload"){    // Скачивание списка клиентов компании
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $letters[1] = "A";
        $letters[2] = "B";
        $letters[3] = "C";
        $letters[4] = "D";
        $letters[5] = "E";
        $letters[6] = "F";
        $letters[7] = "G";
        $letters[8] = "H";
        $letters[9] = "I";
        $letters[10] = "J";
        $letters[11] = "K";
        $letters[12] = "L";
        $letters[13] = "M";
        $i = 1;

        $sheet->setCellValue("A1", "Имя");
        $sheet->setCellValue("B1", "ИНН");
        $sheet->setCellValue("C1", "Эл. почта");
        $sheet->setCellValue("D1", "Номер телефона");
        $sheet->setCellValue("E1", "Адрес доставки");
        $sheet->setCellValue("F1", "Опт");

        $spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("F")->setWidth(20);

        $k = 2;
        $sql_text = "SELECT client.id AS id, client.name AS name, client.inn AS inn, client.mail AS mail, client_contact.phone AS phone, client.address AS address, client.opt AS opt FROM client LEFT JOIN client_contact ON client.id = client_contact.cId WHERE client.id > 0 ";
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            if($data["opt"] == 1) $opt = "да"; else $opt = "нет";
            $sheet->setCellValue("A".$k, $data["name"]);
            $sheet->setCellValue("B".$k, $data["inn"]);
            $sheet->setCellValue("C".$k, $data["mail"]);
            $sheet->setCellValue("D".$k, $data["phone"]);
            $sheet->setCellValue("E".$k, $data["address"]);
            $sheet->setCellValue("F".$k, $opt);
            $k++;
        }


        $writer = new Xlsx($spreadsheet);
        $name = generate_16(10);
        $writer->save("../../temp/".$name.".xlsx");
        echo $SERVER."temp/".$name.".xlsx";
    }
    if($_POST["methodName"] == "settingsMassaProof"){       // Проверка файла массовой загрузки шин
        $file = clean($_POST["file"]);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load("../../temp/".$file);

        $cells = $spreadsheet->getActiveSheet()->getCellCollection();
        $flag = true;
        $count_row = 0;
        for ($row = 1; $row <= $cells->getHighestRow(); $row++)if(!is_null($cells->get("A".$row)) && $cells->get("A".$row)->getValue() != ""){
            if(!is_null($cells->get("A".$row))) $season  = $cells->get("A".$row)->getValue(); else $season = "";
            if(!is_null($cells->get("B".$row))) $w       = $cells->get("B".$row)->getValue(); else $w = "";
            if(!is_null($cells->get("C".$row))) $h       = $cells->get("C".$row)->getValue(); else $h = "";
            if(!is_null($cells->get("D".$row))) $r       = $cells->get("D".$row)->getValue(); else $r = "";
            if(!is_null($cells->get("E".$row))) $brand   = $cells->get("E".$row)->getValue(); else $brand = "";
            if(!is_null($cells->get("F".$row))) $model   = $cells->get("F".$row)->getValue(); else $model = "";
            if(!is_null($cells->get("G".$row))) $nagr    = $cells->get("G".$row)->getValue(); else $nagr = "";
            if(!is_null($cells->get("H".$row))) $resist  = $cells->get("H".$row)->getValue(); else $resist = "";
            if(!is_null($cells->get("I".$row))) $rft     = $cells->get("I".$row)->getValue(); else $rft = "";
            if(!is_null($cells->get("J".$row))) $spike   = $cells->get("J".$row)->getValue(); else $spike = "";
            if(!is_null($cells->get("K".$row))) $cargo   = $cells->get("K".$row)->getValue(); else $cargo = "";
            if(!is_null($cells->get("L".$row))) $price_1 = $cells->get("L".$row)->getValue(); else $price_1 = "";
            if(!is_null($cells->get("M".$row))) $price_2 = $cells->get("M".$row)->getValue(); else $price_2 = "";
            if(!is_null($cells->get("N".$row))) $price_3 = $cells->get("N".$row)->getValue(); else $price_3 = "";
            if(!is_null($cells->get("O".$row))) $count   = $cells->get("O".$row)->getValue(); else $count = "";
            if(!is_null($cells->get("P".$row))) $storage = $cells->get("P".$row)->getValue();
            if(!is_null($cells->get("Q".$row))) $payer   = $cells->get("Q".$row)->getValue(); else $payer = "";
            if(!is_null($cells->get("R".$row))) $code    = $cells->get("R".$row)->getValue(); else $code = "";

            if($season != "лето" && $season != "зима" && $season != "всесезон") $flag = false;
            if(!is_int($w))       $flag = false;
            if(!is_int($h))       $flag = false;
            if(!is_int($r))       $flag = false;
            if(!is_int($price_1)) $flag = false;
            if(!is_int($price_2)) $flag = false;
            if(!is_int($price_3)) $flag = false;
            if(!is_int($count)) $flag = false;
            $code = str_replace("(", "&#40;", $code);
            $code = str_replace(")", "&#41;", $code);
            $code = str_replace("<", "&#706;", $code);
            $code = str_replace(">", "&#707;", $code);
            $code = str_replace("'", "&#8216;", $code);
            $mas = explode(", ", $code);
            //if($count != count($mas)) $flag = false;
            //echo $count." ".count($mas)."<br>";
            for($i = 0; $i < count($mas); $i++) if($mas[$i] != ""){
                $code = $mas[$i];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE code = '$code'"));
                //if(isset($temp["id"])) $flag = false;
            }
            $count_row++;
            //echo $row." ".$season."<br>";
        }
        if($flag) echo $count_row;
        else echo -1;
    }
    if($_POST["methodName"] == "settingsMassaAdd"){       // Добавление шин при массовой загрузке
        $file = clean($_POST["file"]);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load("../../temp/".$file);

        $cells = $spreadsheet->getActiveSheet()->getCellCollection();
        $count0 = 0;
        for ($row = 1; $row <= $cells->getHighestRow(); $row++)if(!is_null($cells->get("A".$row)) && $cells->get("A".$row)->getValue() != ""){
            $season  = $cells->get("A".$row)->getValue();
            $w       = $cells->get("B".$row)->getValue();
            $h       = $cells->get("C".$row)->getValue();
            $r       = $cells->get("D".$row)->getValue();
            $brand   = $cells->get("E".$row)->getValue();
            $model   = $cells->get("F".$row)->getValue();
            $nagr    = $cells->get("G".$row)->getValue();
            $resist  = $cells->get("H".$row)->getValue();
            $rft     = $cells->get("I".$row)->getValue();
            $spike   = $cells->get("J".$row)->getValue();
            $cargo   = $cells->get("K".$row)->getValue();
            $price_1 = $cells->get("L".$row)->getValue();
            $price_2 = $cells->get("M".$row)->getValue();
            $price_3 = $cells->get("N".$row)->getValue();
            $count_all = $cells->get("O".$row)->getValue();
            if(!is_null($cells->get("P".$row))) $storage = $cells->get("P".$row)->getValue(); else $storage = 0;
            if(!is_null($cells->get("Q".$row))) $payer = $cells->get("Q".$row)->getValue(); else $payer = "";
            if(!is_null($cells->get("R".$row))) $code = $cells->get("R".$row)->getValue(); else $code = "";

            switch($season){
                case "зима"     : $season = 0; break;
                case "лето"     : $season = 1; break;
                case "всесезон" : $season = 2; break;
            }
            $rft = ($rft == "да") ? 1 : 0;
            $spike = ($spike == "да") ? 1 : 0;
            $cargo = ($cargo == "да") ? 1 : 0;

            $data = mysqli_fetch_array(mysqli_query($CONNECTION,
                "SELECT id, barcode FROM tire WHERE season = '$season' AND w = '$w' AND h = '$h' AND r = '$r' AND brand = '$brand'
                AND model = '$model'"));
            if(!isset($data["id"])){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM tire ORDER BY id DESC LIMIT 1"));
                $count = $data["id"];
                $count++;
                $count = getRight5Number($count);
                $barcode = generate_barcode($CONNECTION);
                mysqli_query($CONNECTION, "
                    INSERT INTO tire
                        (article, barcode, season, w, h, r, brand, model, nagr, resist, rft, spike, cargo, price_purchase, price_sale, price_wholesale)
                    VALUES
                        ('$count', '$barcode', '$season', '$w', '$h', '$r', '$brand', '$model', '$nagr', '$resist', '$rft', '$spike', '$cargo', '$price_1', '$price_2', '$price_3')");
                $pId = mysqli_insert_id($CONNECTION);
            }
            else{
                $barcode = $data["barcode"];
                $pId = $data["id"];
                mysqli_query($CONNECTION, "UPDATE tire SET status = 1, price_purchase = '$price_1', price_sale = '$price_2', price_wholesale = '$price_3' WHERE id = '$pId'");
            }
            $count0++;
            if($payer != ""){
                $payer = str_replace("\"", "&quot;", $payer);

                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM payer WHERE name = '$payer'"));
                if(isset($temp["id"])){
                    $payer = $temp["id"];
                    //echo $payer;
                    if($code != ""){
                        $code = str_replace("(", "&#40;", $code);
                        $code = str_replace(")", "&#41;", $code);
                        $code = str_replace("<", "&#706;", $code);
                        $code = str_replace(">", "&#707;", $code);
                        $code = str_replace("'", "&#8216;", $code);
                        $mas = explode(", ", $code);
                        if(count($mas) == 1){
                            $temp = mysqli_query($CONNECTION, "SELECT id FROM code WHERE code = \"$code\"");
                            if(!isset($temp["id"])) mysqli_query($CONNECTION, "INSERT INTO code (tire, payer, code) VALUES ('$pId', '$payer', '$code')");

                        }
                        for($i = 0; $i < count($mas); $i++) if(strlen($mas[$i]) > 0){
                            //echo " ".$mas[$i];
                            $code = $mas[$i];
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE code = '$code'"));
                            if(!isset($temp["id"])) mysqli_query($CONNECTION, "INSERT INTO code (tire, payer, code) VALUES ('$pId', '$payer', '$code')");

                        }
                    }

                }

            }

            $storage = strtoupper($storage);
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$storage'"));
            if(isset($data["id"])){
                $sId = $data["id"];
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, count FROM available WHERE storage = '$sId' AND barcode = '$barcode'"));
                if(isset($data["id"])){
                    $op = $data["id"];
                    $count = $data["count"] + $count_all;
                    mysqli_query($CONNECTION, "UPDATE available SET count = '$count' WHERE id = '$op'");
                }
                else{
                    mysqli_query($CONNECTION, "INSERT INTO available (barcode, storage, count) VALUES ('$barcode', '$sId', '$count_all')");
                }
                productCountCalculate($CONNECTION, 1, $pId);
            }
        }

        allStorageCalc($CONNECTION);

        echo $count0;
    }
    if($_POST["methodName"] == "tiresOpt"){    // Скачивание списка шин для оптовиков
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $letters[1] = "A";
        $letters[2] = "B";
        $letters[3] = "C";
        $letters[4] = "D";
        $letters[5] = "E";
        $letters[6] = "F";
        $letters[7] = "G";
        $letters[8] = "H";
        $letters[9] = "I";
        $letters[10] = "J";
        $letters[11] = "K";
        $letters[12] = "L";
        $letters[13] = "M";
        $i = 1;

        $sheet->setCellValue("A1", "Имя");
        $sheet->setCellValue("B1", "ИНН");
        $sheet->setCellValue("C1", "Эл. почта");
        $sheet->setCellValue("D1", "Адрес доставки");
        $sheet->setCellValue("E1", "Опт");

        $spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(20);

        $k = 2;
        $sql = mysqli_query($CONNECTION, "SELECT * FROM client");
        while($data = mysqli_fetch_array($sql)){
            if($data["opt"] == 1) $opt = "да"; else $opt = "нет";
            $sheet->setCellValue("A".$k, $data["name"]);
            $sheet->setCellValue("B".$k, $data["inn"]);
            $sheet->setCellValue("C".$k, $data["mail"]);
            $sheet->setCellValue("D".$k, $data["address"]);
            $sheet->setCellValue("E".$k, $opt);
            $k++;
        }


        $writer = new Xlsx($spreadsheet);
        $name = generate_16(10);
        $writer->save("../../temp/".$name.".xlsx");
        echo $SERVER."temp/".$name.".xlsx";
    }
    if($_POST["methodName"] == "cashReport"){    // Скачивание отчета по кассе за день
        $base = clean($_POST["base"]);
        $id = clean($_POST["id"]);
        $param = clean($_POST["param"]);

        $uId = ID;

        $spreadsheet = new Spreadsheet();

        $letters[1] = "A";
        $letters[2] = "B";
        $letters[3] = "C";
        $letters[4] = "D";
        $letters[5] = "E";
        $letters[6] = "F";
        $letters[7] = "G";
        $letters[8] = "H";
        $letters[9] = "I";
        $letters[10] = "J";
        $letters[11] = "K";
        $letters[12] = "L";
        $letters[13] = "M";

        {    // Общий отчет за день
            $sheet = $spreadsheet->getActiveSheet()->setTitle("Общий отчет за день");
            $i = 1;
            if($param == 1) $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM cash WHERE base = '$base' AND user = '".ID."' ORDER BY id DESC LIMIT 1"));
            else {
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM cash WHERE id = '$id'"));
                $base = $data["base"];
                $uId = $data["user"];
            }
            $time_start = $data["time_start"];
            $time_end = $data["time_end"];
            $razmen_end = $data["razmen_end"];

            $spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(40);

            $sheet->mergeCells("A1:B1");
            $sheet->setCellValue("A1", "Общий отчет за ".date("d.m.Y", $time_end));

            $NAL = 0;
            $BEZNAL = 0;
            $OTHER = 0;
            $TIRES = 0;
            $USERS = [];
            $USERS_NAME = [];
            $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type < 4");
            while($data = mysqli_fetch_array($sql)){
                $id2 = $data["id"];
                $USERS[$id2] = 0;
                $USERS_NAME[$id2] = $data["name"]." ".$data["surname"];
            }

            $sql = mysqli_query($CONNECTION, "SELECT * FROM transactions WHERE date > $time_start AND date < $time_end AND base = '$base' AND type = 1");
            while($data = mysqli_fetch_array($sql)){
                $sale = $data["sale"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM sale WHERE number = '$sale'"));
                if(isset($temp["id"])){
                    $sale_id = $temp["id"];
                    $price_start = $temp["price_sale"];
                    $price_finish = $price_start - $temp["skidka_ruble"];
                    $oplata_type = $temp["oplata"];
                    if($oplata_type == 2) $price_finish = $price_finish*1.02;
                    if($price_start > 0) $koef = $price_finish/$price_start; else $koef = 1;
                    $manager = $temp["manager"];
                    $sql_2 = mysqli_query($CONNECTION, "SELECT * FROM sale_product WHERE sale = '$sale_id' ORDER BY barcode");
                    while($data_2 = mysqli_fetch_array($sql_2)){
                        $p_id = $data_2["p_id"];
                        $p_type = $data_2["p_type"];
                        //$barcode = $data_2["barcode"];
                        //$temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(count) FROM sale_product WHERE sale = '$sale_id' AND barcode = '$barcode'"));
                        $p_count = $data_2["count"];
                        $p_param = $data_2["p_param"];
                        if($p_type == 1){
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT price_sale FROM tire WHERE id = '$p_id'"));
                            $price = round($temp["price_sale"]*$koef, 2);
                            $TIRES += $p_count;
                        }
                        if($p_type == 2){
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT price_sale FROM disk WHERE id = '$p_id'"));
                            $price = round($temp["price_sale"]*$koef, 2);
                        }
                        if($p_type == 3){
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT price_sale FROM product WHERE id = '$p_id'"));
                            $price = round($temp["price_sale"]*$koef, 2);
                        }
                        if($p_type == 4){
                            if($p_param == 0) $p_param = 1;
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM service WHERE id = '$p_id'"));
                            $price = round($temp["price_".$p_param]*$koef, 2);
                        }
                        if($p_type == 5){
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT price FROM season_temp WHERE id = '$p_id'"));
                            $price = round($temp["price"]*$koef, 2);
                        }

                        $price = $price*$p_count;
                        $USERS[$manager] += $price;

                        if($oplata_type < 2) $NAL += $price;
                        if($oplata_type == 2) $BEZNAL += $price;
                        if($oplata_type > 2) $OTHER += $price;
                    }
                }
            }

            $ALL = $NAL + $BEZNAL + $OTHER;

            $sheet->setCellValue("A2", "Количество проданных шин:");
            $sheet->setCellValue("B2", $TIRES);

            $sheet->setCellValue("A4", "Общая выручка за день:");
            $sheet->setCellValue("B4", $ALL);

            $sheet->setCellValue("A5", "Всего наличных:");
            $sheet->setCellValue("B5", $NAL);

            $sheet->setCellValue("A6", "Всего безналичных:");
            $sheet->setCellValue("B6", $BEZNAL);

            $sheet->setCellValue("A7", "Другая форма оплаты:");
            $sheet->setCellValue("B7", $OTHER);

            $k = 9;

            $sheet->mergeCells("A".$k.":B".$k);
            $sheet->setCellValue("A".$k, "Выручка по каждому менеджеру");
            $k++;

            $sheet->setCellValue("A".$k, "Имя");
            $sheet->setCellValue("B".$k, "Выручка");
            $k++;

            foreach ($USERS as $i => $value) if($value > 0){
                $sheet->setCellValue("A".$k, $USERS_NAME[$i]);
                $sheet->setCellValue("B".$k, $value);
                $k++;
            }

            $k++;
            $k++;

            $sheet->mergeCells("A".$k.":E".$k);
            $sheet->setCellValue("A".$k, "Списания за день");
            $k++;

            $sheet->setCellValue("A".$k, "Номер операции");
            $sheet->setCellValue("B".$k, "Время");
            $sheet->setCellValue("C".$k, "Сумма");
            $sheet->setCellValue("D".$k, "Сотрудник");
            $sheet->setCellValue("E".$k, "Комментарий");

            $k++;
            $sql = mysqli_query($CONNECTION, "SELECT * FROM transactions WHERE date > $time_start AND date < $time_end AND base = '$base' AND type = 2");
            while($data = mysqli_fetch_array($sql))if($data["cashier"] > 0){
                $cashier = $data["cashier"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = '$cashier'"));
                $cashier = $temp["name"]." ".$temp["surname"];
                $sheet->setCellValue("A".$k, $data["number"]);
                $sheet->setCellValue("B".$k, date("H:i", $data["date"]));
                $sheet->setCellValue("C".$k, $data["summa"]);
                $sheet->setCellValue("D".$k, $cashier);
                $sheet->setCellValue("E".$k, $data["reason"]);

                $k++;
            }

            $k++;
            $k++;

            $sheet->mergeCells("B".$k.":C".$k);
            $sheet->setCellValue("B".$k, "Остаток наличных в кассе после списания:");
            $sheet->setCellValue("D".$k, $razmen_end);

        }

        {   // Продажи
            $sheet = $spreadsheet->createSheet()->setTitle("Продажи");

            $sheet->getColumnDimension("A")->setWidth(20);
            $sheet->getColumnDimension("B")->setWidth(20);
            $sheet->getColumnDimension("C")->setWidth(20);
            $sheet->getColumnDimension("D")->setWidth(20);
            $sheet->getColumnDimension("E")->setWidth(20);
            $sheet->getColumnDimension("F")->setWidth(20);
            $sheet->getColumnDimension("G")->setWidth(20);
            $sheet->getColumnDimension("H")->setWidth(20);
            $sheet->getColumnDimension("I")->setWidth(20);
            $sheet->getColumnDimension("J")->setWidth(20);
            $sheet->getColumnDimension("K")->setWidth(20);
            $sheet->getColumnDimension("L")->setWidth(20);

            $sheet->setCellValue("F2", "Размен на начало дня");
            $sheet->setCellValue("G2", "ХХХ");

            $sheet->setCellValue("A3", "Номер операции");
            $sheet->setCellValue("B3", "Время");
            $sheet->setCellValue("C3", "Тип");
            $sheet->setCellValue("D3", "Товар/Услуга");
            $sheet->setCellValue("E3", "Количество");
            $sheet->setCellValue("F3", "Склад");
            $sheet->setCellValue("G3", "Стоимость");
            $sheet->setCellValue("H3", "Тип платежа");
            $sheet->setCellValue("I3", "Платеж на карту");
            $sheet->setCellValue("J3", "Комментарий");
            $sheet->setCellValue("K3", "Менеджер");
            $sheet->setCellValue("L3", "Прибыль");

            $sheet->getStyle("A3:L3")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB("00ffff");

            if($param == 1) $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM cash WHERE base = '$base' AND user = '$uId' ORDER BY id DESC LIMIT 1"));
            else $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM cash WHERE id = '$id'"));
            $time_start = $data["time_start"];
            $time_end = $data["time_end"];
            $sheet->setCellValue("G2", $data["razmen_start"]);
            $k = 4;
            $PROFIT_ALL = 0;
            $NAL = 0;
            $BEZNAL = 0;
            $OTHER = 0;

            $USERS = [];
            $TIRES = [];
            $USERS_NAME = [];
            $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type < 4");
            while($data = mysqli_fetch_array($sql)){
                $id2 = $data["id"];
                $USERS[$id2] = 0;
                $TIRES[$id2] = 0;
                $USERS_NAME[$id2] = $data["name"]." ".$data["surname"];
            }

            $sql = mysqli_query($CONNECTION, "SELECT * FROM transactions WHERE date > $time_start AND date < $time_end AND base = '$base' AND type = 1");
            while($data = mysqli_fetch_array($sql)){
                $sale = $data["sale"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM sale WHERE number = '$sale'"));
                if(isset($temp["id"])){
                    $sale_id = $temp["id"];
                    $price_start = $temp["price_sale"];
                    $price_finish = $price_start - $temp["skidka_ruble"];
                    $oplata_type = $temp["oplata"];
                    if($oplata_type == 2) $price_finish = $price_finish*1.02;
                    if($price_start > 0) $koef = $price_finish/$price_start; else $koef = 1;
                    $manager = $temp["manager"];
                    $oplata_comment = $temp["oplata_comment"];
                    $sql_2 = mysqli_query($CONNECTION, "SELECT * FROM sale_product WHERE sale = '$sale_id' AND p_type < 3 GROUP BY barcode");
                    while($data_2 = mysqli_fetch_array($sql_2)){
                        $p_id = $data_2["p_id"];
                        $p_type = $data_2["p_type"];
                        $barcode = $data_2["barcode"];
                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(count) FROM sale_product WHERE sale = '$sale_id' AND barcode = '$barcode'"));
                        $p_count = $temp[0];
                        $otkuda = "";
                        $sql_3 = mysqli_query($CONNECTION, "SELECT * FROM sale_product WHERE sale = '$sale_id' AND barcode = '$barcode'");
                        while($temp = mysqli_fetch_array($sql_3)){
                            $otkuda .= $temp["otkuda"]." ".$temp["count"]."шт;";
                        }

                        if($p_type == 1){
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM tire WHERE id = '$p_id'"));
                            $name = $temp["brand"]." ".$temp["model"]." ".$temp["w"]."/".$temp["h"]."R".$temp["r"];
                            $price = round($temp["price_sale"]*$koef, 2);
                            $price_purchase = $temp["price_purchase"];
                            $TIRES[$manager] += $p_count;
                        }
                        if($p_type == 2){
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM disk WHERE id = '$p_id'"));
                            $name = $temp["nomenclature"]." ".$temp["w"]."/R".$temp["r"];
                            $price = round($temp["price_sale"]*$koef, 2);
                            $price_purchase = $temp["price_purchase"];
                        }
                        $profit = round(($price-$price_purchase)*$p_count, 2);
                        $PROFIT_ALL += $profit;

                        $price = $price*$p_count;
                        $USERS[$manager] += $price;

                        if($oplata_type < 2){
                            $oplata = "Наличный расчет";
                            $NAL += $price;
                        }

                        if($oplata_type >= 2 && $oplata_type != 4){
                            $sheet->getStyle("A".$k.":L".$k)
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB("ff66cc");
                            switch($oplata_type){
                                case 2: $oplata = "Безналичный расчет"; break;
                                case 3: $oplata = "На расчетный счет"; break;
                                case 5: $oplata = "Карта"; break;
                            }
                            $BEZNAL += $price;
                        }
                        if($oplata_type == 4){
                            $sheet->getStyle("A".$k.":L".$k)
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB("ffff00");
                            $oplata = "Перевод на карту";
                            $OTHER += $price;
                        }



                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = '$manager'"));
                        $manager_name = $temp["name"]." ".$temp["surname"];



                        $sheet->setCellValue("A".$k, $data["number"]);
                        $sheet->setCellValue("B".$k, date("d.m.Y H:i:s", $data["date"]));
                        $sheet->setCellValue("C".$k, "Продажа");
                        $sheet->setCellValue("D".$k, $name);
                        $sheet->setCellValue("E".$k, $p_count);
                        $sheet->setCellValue("F".$k, $otkuda);
                        $sheet->setCellValue("G".$k, $price);
                        $sheet->setCellValue("H".$k, $oplata);
                        $sheet->setCellValue("I".$k, "Нет");
                        $sheet->setCellValue("J".$k, $oplata_comment);
                        $sheet->setCellValue("K".$k, $manager_name);
                        $sheet->setCellValue("L".$k, $profit);

                        $k++;
                    }
                }
            }

            $k++;

            $ALL = $NAL + $BEZNAL + $OTHER;
            $sheet->setCellValue("F".$k, "Всего");
            $sheet->setCellValue("G".$k, $ALL);
            $k++;
            $sheet->setCellValue("F".$k, "Оплата наличными");
            $sheet->setCellValue("G".$k, $NAL);
            $k++;
            $sheet->setCellValue("F".$k, "Оплата безналичным расчетом");
            $sheet->setCellValue("G".$k, $BEZNAL);
            $k++;
            $sheet->setCellValue("F".$k, "Другая форма оплаты");
            $sheet->setCellValue("G".$k, $OTHER);

            $k--;
            $k--;
            $sheet->setCellValue("K".$k, "Общая прибыль");
            $sheet->setCellValue("L".$k, $PROFIT_ALL);

            $sheet->mergeCells("B".$k.":D".$k);
            $sheet->setCellValue("B".$k, "Выручка по каждому менеджеру");
            $k++;
            $sheet->setCellValue("B".$k, "Имя");
            $sheet->setCellValue("C".$k, "Кол-во шин");
            $sheet->setCellValue("D".$k, "Выручка");
            foreach ($USERS as $i => $value) if($value > 0){
                $sheet->setCellValue("B".$k, $USERS_NAME[$i]);
                $sheet->setCellValue("C".$k, $TIRES[$i]);
                $sheet->setCellValue("D".$k, $value);
                $k++;
            }
        }

        {   // Услуги
            $sheet = $spreadsheet->createSheet()->setTitle("Услуги");
            $sheet->getColumnDimension("A")->setWidth(20);
            $sheet->getColumnDimension("B")->setWidth(20);
            $sheet->getColumnDimension("C")->setWidth(20);
            $sheet->getColumnDimension("D")->setWidth(20);
            $sheet->getColumnDimension("E")->setWidth(20);
            $sheet->getColumnDimension("F")->setWidth(20);
            $sheet->getColumnDimension("G")->setWidth(20);
            $sheet->getColumnDimension("H")->setWidth(20);
            $sheet->getColumnDimension("I")->setWidth(20);
            $sheet->getColumnDimension("J")->setWidth(20);
            $sheet->getColumnDimension("K")->setWidth(20);
            $sheet->getColumnDimension("L")->setWidth(20);

            $sheet->setCellValue("A2", "Номер операции");
            $sheet->setCellValue("B2", "Время");
            $sheet->setCellValue("C2", "Тип");
            $sheet->setCellValue("D2", "Товар/Услуга");
            $sheet->setCellValue("E2", "Количество");
            $sheet->setCellValue("F2", "Стоимость");
            $sheet->setCellValue("G2", "Тип платежа");
            $sheet->setCellValue("H2", "Платеж на карту");
            $sheet->setCellValue("I2", "Менеджер");
            $sheet->setCellValue("J2", "Комментарий");


            $sheet->getStyle("A2:J2")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB("00ffff");
            if($param == 1) $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM cash WHERE base = '$base' AND user = '$uId' ORDER BY id DESC LIMIT 1"));
            else $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM cash WHERE id = '$id'"));
            $time_start = $data["time_start"];
            $time_end = $data["time_end"];
            $k = 3;
            $PROFIT_ALL = 0;
            $NAL = 0;
            $BEZNAL = 0;
            $OTHER = 0;

            $USERS = [];
            $TIRES = [];
            $USERS_NAME = [];
            $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type < 4");
            while($data = mysqli_fetch_array($sql)){
                $id2 = $data["id"];
                $USERS[$id2] = 0;
                $USERS_NAME[$id2] = $data["name"]." ".$data["surname"];
            }

            $sql = mysqli_query($CONNECTION, "SELECT * FROM transactions WHERE date > $time_start AND date < $time_end AND base = '$base' AND type = 1");
            while($data = mysqli_fetch_array($sql)){
                $sale = $data["sale"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM sale WHERE number = '$sale'"));
                if(isset($temp["id"])){
                    $sale_id = $temp["id"];
                    $price_start = $temp["price_sale"];
                    $price_finish = $price_start - $temp["skidka_ruble"];
                    $oplata_type = $temp["oplata"];
                    if($oplata_type == 2) $price_finish = $price_finish*1.02;
                    if($price_start > 0) $koef = $price_finish/$price_start; else $koef = 1;
                    $manager = $temp["manager"];
                    $oplata_comment = $temp["oplata_comment"];
                    $sql_2 = mysqli_query($CONNECTION, "SELECT * FROM sale_product WHERE sale = '$sale_id' AND p_type = 4");
                    while($data_2 = mysqli_fetch_array($sql_2)){
                        $p_id = $data_2["p_id"];
                        $p_type = $data_2["p_type"];
                        $p_count = $data_2["count"];
                        $p_param = $data_2["p_param"];
                        if($p_param == 0) $p_param = 1;

                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM service WHERE id = '$p_id'"));
                        $name = $temp["name"];
                        $price = round($temp["price_".$p_param]*$koef, 2);
                        $price_purchase = 0;

                        $price = $price*$p_count;
                        $USERS[$manager] += $price;

                        if($oplata_type < 2){
                            $oplata = "Наличный расчет";
                            $NAL += $price;
                        }

                        if($oplata_type >= 2 && $oplata_type != 4){
                            $sheet->getStyle("A".$k.":L".$k)
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB("ff66cc");
                            switch($oplata_type){
                                case 2: $oplata = "Безналичный расчет"; break;
                                case 3: $oplata = "На расчетный счет"; break;
                                case 5: $oplata = "Карта"; break;
                            }
                            $BEZNAL += $price;
                        }
                        if($oplata_type == 4){
                            $sheet->getStyle("A".$k.":L".$k)
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB("ffff00");
                            $oplata = "Перевод на карту";
                            $OTHER += $price;
                        }

                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = '$manager'"));
                        $manager_name = $temp["name"]." ".$temp["surname"];



                        $sheet->setCellValue("A".$k, $data["number"]);
                        $sheet->setCellValue("B".$k, date("d.m.Y H:i:s", $data["date"]));
                        $sheet->setCellValue("C".$k, "Услуга");
                        $sheet->setCellValue("D".$k, $name);
                        $sheet->setCellValue("E".$k, $p_count);
                        $sheet->setCellValue("F".$k, $price);
                        $sheet->setCellValue("G".$k, $oplata);
                        $sheet->setCellValue("H".$k, "Нет");
                        $sheet->setCellValue("I".$k, $manager_name);
                        $sheet->setCellValue("J".$k, $oplata_comment);

                        $k++;
                    }
                }
            }

            $k++;

            $ALL = $NAL + $BEZNAL + $OTHER;
            $sheet->setCellValue("E".$k, "Всего");
            $sheet->setCellValue("F".$k, $ALL);
            $k++;
            $sheet->setCellValue("E".$k, "Оплата наличными");
            $sheet->setCellValue("F".$k, $NAL);
            $k++;
            $sheet->setCellValue("E".$k, "Оплата безналичным расчетом");
            $sheet->setCellValue("F".$k, $BEZNAL);
            $k++;
            $sheet->setCellValue("E".$k, "Другая форма оплаты");
            $sheet->setCellValue("F".$k, $OTHER);

            $k--;
            $k--;

            $sheet->mergeCells("B".$k.":C".$k);
            $sheet->setCellValue("B".$k, "Выручка по каждому менеджеру");
            $k++;
            $sheet->setCellValue("B".$k, "Имя");
            $sheet->setCellValue("C".$k, "Выручка");
            foreach ($USERS as $i => $value) if($value > 0){
                $sheet->setCellValue("B".$k, $USERS_NAME[$i]);
                $sheet->setCellValue("C".$k, $value);
                $k++;
            }
        }

        {   // Товары и прочее
            $sheet = $spreadsheet->createSheet()->setTitle("Товары и прочее");
            $sheet->getColumnDimension("A")->setWidth(20);
            $sheet->getColumnDimension("B")->setWidth(20);
            $sheet->getColumnDimension("C")->setWidth(20);
            $sheet->getColumnDimension("D")->setWidth(20);
            $sheet->getColumnDimension("E")->setWidth(20);
            $sheet->getColumnDimension("F")->setWidth(20);
            $sheet->getColumnDimension("G")->setWidth(20);
            $sheet->getColumnDimension("H")->setWidth(20);
            $sheet->getColumnDimension("I")->setWidth(20);
            $sheet->getColumnDimension("J")->setWidth(20);
            $sheet->getColumnDimension("K")->setWidth(20);
            $sheet->getColumnDimension("L")->setWidth(20);

            $sheet->setCellValue("A2", "Номер операции");
            $sheet->setCellValue("B2", "Время");
            $sheet->setCellValue("C2", "Тип");
            $sheet->setCellValue("D2", "Товар/Услуга");
            $sheet->setCellValue("E2", "Количество");
            $sheet->setCellValue("F2", "Стоимость");
            $sheet->setCellValue("G2", "Тип платежа");
            $sheet->setCellValue("H2", "Платеж на карту");
            $sheet->setCellValue("I2", "Менеджер");
            $sheet->setCellValue("J2", "Комментарий");


            $sheet->getStyle("A2:J2")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB("00ffff");

            if($param == 1) $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM cash WHERE base = '$base' AND user = '".$uId."' ORDER BY id DESC LIMIT 1"));
            else $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM cash WHERE id = '$id'"));
            $time_start = $data["time_start"];
            $time_end = $data["time_end"];
            $k = 3;
            $NAL = 0;
            $BEZNAL = 0;
            $OTHER = 0;

            $USERS = [];
            $USERS_NAME = [];
            $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type < 4");
            while($data = mysqli_fetch_array($sql)){
                $id2 = $data["id"];
                $USERS[$id2] = 0;
                $USERS_NAME[$id2] = $data["name"]." ".$data["surname"];
            }

            $sql = mysqli_query($CONNECTION, "SELECT * FROM transactions WHERE date > $time_start AND date < $time_end AND base = '$base' AND type = 1");
            while($data = mysqli_fetch_array($sql)){
                $sale = $data["sale"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM sale WHERE number = '$sale'"));
                if(isset($temp["id"])){
                    $sale_id = $temp["id"];
                    $price_start = $temp["price_sale"];
                    $price_finish = $price_start - $temp["skidka_ruble"];
                    $oplata_type = $temp["oplata"];
                    if($oplata_type == 2) $price_finish = $price_finish*1.02;
                    if($price_start > 0) $koef = $price_finish/$price_start; else $koef = 1;
                    $manager = $temp["manager"];
                    $oplata_comment = $temp["oplata_comment"];
                    $sql_2 = mysqli_query($CONNECTION, "SELECT * FROM sale_product WHERE sale = '$sale_id' AND (p_type = 3 OR p_type = 5)");
                    while($data_2 = mysqli_fetch_array($sql_2)){
                        $p_id = $data_2["p_id"];
                        $p_type = $data_2["p_type"];
                        $p_count = $data_2["count"];
                        $p_param = $data_2["p_param"];
                        if($p_type == 3){
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM product WHERE id = '$p_id'"));
                            $name = $temp["name"];
                            $price = round($temp["price_sale"]*$koef, 2);
                            $price_purchase = $temp["price_purchase"];
                            $p_type_name = "Товар";
                        }
                        if($p_type == 5){
                            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM season_temp WHERE id = '$p_id'"));
                            $name = $temp["name"];
                            $price = round($temp["price"]*$koef, 2);
                            $price_purchase = 0;
                            $p_type_name = "Прочее";
                        }

                        $price = $price*$p_count;
                        $USERS[$manager] += $price;

                        if($oplata_type < 2){
                            $oplata = "Наличный расчет";
                            $NAL += $price;
                        }

                        if($oplata_type >= 2 && $oplata_type != 4){
                            $sheet->getStyle("A".$k.":L".$k)
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB("ff66cc");
                            switch($oplata_type){
                                case 2: $oplata = "Безналичный расчет"; break;
                                case 3: $oplata = "На расчетный счет"; break;
                                case 5: $oplata = "Карта"; break;
                            }
                            $BEZNAL += $price;
                        }
                        if($oplata_type == 4){
                            $sheet->getStyle("A".$k.":L".$k)
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB("ffff00");
                            $oplata = "Перевод на карту";
                            $OTHER += $price;
                        }

                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = '$manager'"));
                        $manager_name = $temp["name"]." ".$temp["surname"];


                        $sheet->setCellValue("A".$k, $data["number"]);
                        $sheet->setCellValue("B".$k, date("d.m.Y H:i:s", $data["date"]));
                        $sheet->setCellValue("C".$k, $p_type_name);
                        $sheet->setCellValue("D".$k, $name);
                        $sheet->setCellValue("E".$k, $p_count);
                        $sheet->setCellValue("F".$k, $price);
                        $sheet->setCellValue("G".$k, $oplata);
                        $sheet->setCellValue("H".$k, "Нет");
                        $sheet->setCellValue("I".$k, $manager_name);
                        $sheet->setCellValue("J".$k, $oplata_comment);

                        $k++;
                    }
                }
            }

            $k++;

            $ALL = $NAL + $BEZNAL + $OTHER;
            $sheet->setCellValue("E".$k, "Всего");
            $sheet->setCellValue("F".$k, $ALL);
            $k++;
            $sheet->setCellValue("E".$k, "Оплата наличными");
            $sheet->setCellValue("F".$k, $NAL);
            $k++;
            $sheet->setCellValue("E".$k, "Оплата безналичным расчетом");
            $sheet->setCellValue("F".$k, $BEZNAL);
            $k++;
            $sheet->setCellValue("E".$k, "Другая форма оплаты");
            $sheet->setCellValue("F".$k, $OTHER);

            $k--;
            $k--;

            $sheet->mergeCells("B".$k.":C".$k);
            $sheet->setCellValue("B".$k, "Выручка по каждому менеджеру");
            $k++;
            $sheet->setCellValue("B".$k, "Имя");
            $sheet->setCellValue("C".$k, "Выручка");
            foreach ($USERS as $i => $value) if($value > 0){
                $sheet->setCellValue("B".$k, $USERS_NAME[$i]);
                $sheet->setCellValue("C".$k, $value);
                $k++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = ".$uId));
        if($param == 1) $name = $data["surname"]." ".$data["name"]." ".date("d-m-Y", time());
        else $name = $data["surname"]." ".$data["name"]." ".date("d-m-Y", $time_end);

        //$name = generate_16(10);
        $writer->save("../../temp/".$name.".xlsx");
        echo $SERVER."temp/".$name.".xlsx";
    }

?>