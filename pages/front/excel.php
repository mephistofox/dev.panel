<?php

    require "vendor/autoload.php";

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $TITLE = "Создание нового файла";

    if($catA == "opt"){
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
        $letters[14] = "N";
        $letters[15] = "O";
        $i = 1;

        $sheet->setCellValue("A1", "Сезон");
        $sheet->setCellValue("B1", "Артикул");
        $sheet->setCellValue("C1", "Размеры");
        $sheet->setCellValue("D1", "Производитель");
        $sheet->setCellValue("E1", "Модель");
        $sheet->setCellValue("F1", "Ширина");
        $sheet->setCellValue("G1", "Высота");
        $sheet->setCellValue("H1", "Радиус");
        $sheet->setCellValue("I1", "ИН");
        $sheet->setCellValue("J1", "ИС");
        $sheet->setCellValue("K1", "RFT");
        $sheet->setCellValue("L1", "ШИП");
        $sheet->setCellValue("M1", "Груз");
        $sheet->setCellValue("N1", "Кол");
        $sheet->setCellValue("O1", "Оптовая цена");

        $spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("F")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("G")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("H")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("I")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("J")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("K")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("L")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("M")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("N")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("O")->setWidth(20);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT value FROM settings WHERE id = 1"));
        $high = $data["value"];

        $k = 2;
        $sql = mysqli_query($CONNECTION, "SELECT * FROM tire");
        while($data = mysqli_fetch_array($sql)){
            switch($data["season"]){
                case 0: $season = "Зима"; break;
                case 1: $season = "Лето"; break;
                case 2: $season = "Всесезон"; break;
            }
            if($data["rft"] == 1) $rft = "да"; else $rft = "нет";
            if($data["spike"] == 1) $spike = "шип"; else $spike = "нет";
            if($data["cargo"] == 1) $cargo = "да"; else $cargo = "нет";
            if($data["count"] <= $high) $count = $data["count"]; else $count = "от ".$high;
            $sheet->setCellValue("A".$k, $season);
            $sheet->setCellValue("B".$k, "S".$data["article"]);
            $sheet->setCellValue("C".$k, $data["w"].", ".$data["h"].", R".$data["r"]);
            $sheet->setCellValue("D".$k, $data["brand"]);
            $sheet->setCellValue("E".$k, $data["model"]);
            $sheet->setCellValue("F".$k, $data["w"]);
            $sheet->setCellValue("G".$k, $data["h"]);
            $sheet->setCellValue("H".$k, "R".$data["r"]);
            $sheet->setCellValue("I".$k, $data["nagr"]);
            $sheet->setCellValue("J".$k, $data["resist"]);
            $sheet->setCellValue("K".$k, $rft);
            $sheet->setCellValue("L".$k, $spike);
            $sheet->setCellValue("M".$k, $cargo);
            $sheet->setCellValue("N".$k, $count);
            $sheet->setCellValue("O".$k, $data["price_wholesale"]);
            $k++;
        }


        $writer = new Xlsx($spreadsheet);
        $name = generate_16(10);
        $writer->save("opt.xlsx");
        echo "Файл для оптовиков успешно создан!";
    }
    if($catA == "roznitsa"){
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
        $letters[14] = "N";
        $letters[15] = "O";
        $letters[15] = "P";
        $i = 1;

        $sheet->setCellValue("A1", "Сезон");
        $sheet->setCellValue("B1", "Артикул");
        $sheet->setCellValue("C1", "Размеры");
        $sheet->setCellValue("D1", "Производитель");
        $sheet->setCellValue("E1", "Модель");
        $sheet->setCellValue("F1", "Ширина");
        $sheet->setCellValue("G1", "Высота");
        $sheet->setCellValue("H1", "Радиус");
        $sheet->setCellValue("I1", "ИН");
        $sheet->setCellValue("J1", "ИС");
        $sheet->setCellValue("K1", "RFT");
        $sheet->setCellValue("L1", "ШИП");
        $sheet->setCellValue("M1", "Груз");
        $sheet->setCellValue("N1", "Кол");
        $sheet->setCellValue("O1", "Цена");
        $sheet->setCellValue("P1", "Оптовая цена");

        $spreadsheet->getActiveSheet()->getColumnDimension("A")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("F")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("G")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("H")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("I")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("J")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("K")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("L")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("M")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("N")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("O")->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension("P")->setWidth(20);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT value FROM settings WHERE id = 1"));
        $high = $data["value"];

        $k = 2;
        $sql = mysqli_query($CONNECTION, "SELECT * FROM tire");
        while($data = mysqli_fetch_array($sql)){
            switch($data["season"]){
                case 0: $season = "Зима"; break;
                case 1: $season = "Лето"; break;
                case 2: $season = "Всесезон"; break;
            }
            if($data["rft"] == 1) $rft = "да"; else $rft = "нет";
            if($data["spike"] == 1) $spike = "шип"; else $spike = "нет";
            if($data["cargo"] == 1) $cargo = "да"; else $cargo = "нет";
            if($data["count"] <= $high) $count = $data["count"]; else $count = "от ".$high;
            $sheet->setCellValue("A".$k, $season);
            $sheet->setCellValue("B".$k, "S".$data["article"]);
            $sheet->setCellValue("C".$k, $data["w"].", ".$data["h"].", R".$data["r"]);
            $sheet->setCellValue("D".$k, $data["brand"]);
            $sheet->setCellValue("E".$k, $data["model"]);
            $sheet->setCellValue("F".$k, $data["w"]);
            $sheet->setCellValue("G".$k, $data["h"]);
            $sheet->setCellValue("H".$k, "R".$data["r"]);
            $sheet->setCellValue("I".$k, $data["nagr"]);
            $sheet->setCellValue("J".$k, $data["resist"]);
            $sheet->setCellValue("K".$k, $rft);
            $sheet->setCellValue("L".$k, $spike);
            $sheet->setCellValue("M".$k, $cargo);
            $sheet->setCellValue("N".$k, $count);
            $sheet->setCellValue("O".$k, $data["price_sale"]);
            $sheet->setCellValue("P".$k, $data["price_wholesale"]);
            $k++;
        }


        $writer = new Xlsx($spreadsheet);
        $name = generate_16(10);
        $writer->save("roznitsa.xlsx");
        echo "Файл для розничных клиентов успешно создан!";
    }

?>