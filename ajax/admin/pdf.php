<?php

    require "../../settings.php";
    require "../../functions.php";
    require "../../vendor/autoload.php";

    proof(); 

    use Dompdf\Dompdf;

    if($_POST["methodName"] == "getSalePDF"){     // Получение PDF продажи
        $id = clean($_POST["id"]);
        $p1 = clean($_POST['p1']);
        $p2 = clean($_POST['p2']);
        $TEXT = file_get_contents("../../templates/admin/temp/sales/sale_pdf.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM sale WHERE id = '$id'"));
        $barcode = $data["number"];
        $TEXT = str_replace("%NUMBER%", $data["number"], $TEXT);
        $summa_all = $data["price_sale"];
        if($data["skidka_ruble"] != "") $summa_all -= $data["skidka_ruble"];
        if($data["oplata"] == 2) $summa_all *= 1.02;
        $summa_all = round($summa_all);
        $koef = $summa_all/$data["price_sale"];
        $TEXT = str_replace("%SUMMA_ALL%", getPriceTroyki($summa_all), $TEXT);
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode =  '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode, $generator::TYPE_EAN_13)) . '">';

        $TEXT = str_replace("%BARCODE%", $barcode, $TEXT);

        $_monthsList = array(
                "1"=>"января","2"=>"февраля","3"=>"марта",
                "4"=>"апреля","5"=>"мая", "6"=>"июня",
                "7"=>"июля","8"=>"августа","9"=>"сентября",
                "10"=>"октября","11"=>"ноября","12"=>"декабря");

        $date = time();
        $TEXT = str_replace("%DAY%", date("d", $date), $TEXT);
        $TEXT = str_replace("%MONTH%", $_monthsList[date("n", $date)], $TEXT);
        $TEXT = str_replace("%YEAR%", date("Y", $date), $TEXT);

        //$naklad = file_get_contents("../../docs/naklad.txt");
        $payerData = mysqli_fetch_assoc(mysqli_query($CONNECTION, "select * from payer where `id`='$p2'"));
        $naklad = file_get_contents('../../docs/'.$payerData['rek']);
        $TEXT = str_replace("%NAKLAD%", nl2br($naklad), $TEXT);


        $table = "";
        $i = 1;
        $sql0 = mysqli_query($CONNECTION, "SELECT * FROM sale_product WHERE sale = '$id' GROUP BY barcode");
        while($data0 = mysqli_fetch_array($sql0)){
            $p_id = $data0["p_id"];
            $p_type = $data0["p_type"];
            $barcode = $data0["barcode"];
            $p_param = $data0['p_param'];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(count) FROM sale_product WHERE sale = '$id' AND barcode = '$barcode'"));
            $count = $temp[0];

            $sql = "SELECT * FROM ";
            switch($p_type){
                case 1: $sql .= "tire"; break;
                case 2: $sql .= "disk"; break;
                case 3: $sql .= "product"; break;
                case 4: $sql .= "service"; break;
                case 5: $sql .= "season_temp"; break;
            }
            $sql .= " WHERE id = '$p_id'";

            $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
            $barcode = $data["barcode"];
            if($p_type == 1){
                $desc = $data["brand"]." ".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"];
                $price = round($data["price_sale"]*$koef, 2);
            }
            if($p_type == 2){
                $desc = $data["nomenclature"]." ".$data["w"]."R".$data["r"];
                $price = round($data["price_sale"]*$koef, 2);
            }
            if($p_type == 3){
                $desc = $data["name"]." ".$data["params"];
                $price = round($data["price_sale"]*$koef, 2);
            }
            if($p_type == 4){
                $desc = $data["name"];
                if(isset($p_param) && !empty($p_param)){
                    $price = round($data["price_".$p_param]*$koef, 2);
                }else{
                    $price = round($data["price_1"]*$koef, 2);
                }
            }
            if($p_type == 5){
                $desc = $data["name"];
                $price = round($data["price"]*$koef, 2);
            }
            $summa = round($price*$count, 2);
            $table .= "
                <div class = 'tr'>
                    <div class = 'td t_number'>".$i."</div>
                    <div class = 'td t_name'>".$desc."</div>
                    <div class = 'td t_count tr_right'>".getPriceTroyki($count)."</div>
                    <div class = 'td t_price tr_right'>".getPriceTroyki($price)."</div>
                    <div class = 'td t_all tr_right'>".getPriceTroyki($summa)."</div>
                </div>";
            $i++;
        }
        $TEXT = str_replace("%TABLE%", $table, $TEXT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT surname, name FROM user WHERE id = ".ID));
        $fio = $data["surname"]." ".mb_substr($data["name"], 0, 1, 'UTF-8').".";
        $TEXT = str_replace("%FIO%", $fio, $TEXT);

        //echo $TEXT;
        $dompdf = new DOMPDF();
        $dompdf->load_html($TEXT);
        $dompdf->setPaper('A4', 'letter');
        $dompdf->render();
        //$dompdf->stream();
        $temp = generate_16(20);
        file_put_contents("../../temp/".$temp.".pdf", $dompdf->output());

        echo $temp;
        //echo $TEXT;

    }
    if($_POST["methodName"] == "tireCodesPrint"){     // Получение кодов маркировки шин
        $id = clean($_POST["id"]);
        $payer = clean($_POST["payer"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM tire WHERE id = '$id'"));
        $tire = $data["brand"]." ".$data["model"]."<br>".$data["w"]."/".$data["h"]."R".$data["r"]." ".$data["nagr"]."".$data["resist"];


        $TEXT = file_get_contents("../../templates/admin/temp/tires/tire_code_pdf.html");

        $sql = mysqli_query($CONNECTION, "SELECT * FROM code WHERE tire = '$id' AND payer = '$payer' AND sale = 0");
        while($data = mysqli_fetch_array($sql)){
            $code = $data["code"];
            $code = str_replace("&#40;", "(", $code);
            $code = str_replace("&#41;", ")", $code);
            $code = str_replace("&#706;", "<", $code);
            $code = str_replace("&#707;", ">", $code);
            $code = str_replace("&#8216;", "'", $code);
            if($data["img"] == ''){
                $code_2 = $code;
                $code_2 = str_replace("&", "%26", $code_2);
                $code_2 = str_replace("+", "%2B", $code_2);
                $code_2 = str_replace("?", "%3F", $code_2);
                $code_2 = str_replace("%", "%25", $code_2);
                $code_2 = str_replace("'", "%27", $code_2);
                $code_2 = str_replace("*", "%2A", $code_2);
                $code_2 = str_replace("_", "%5F", $code_2);
                $code_2 = str_replace('"', "%22", $code_2);
                $code_2 = str_replace('-', "%2D", $code_2);
                $code_2 = str_replace(';', "%3B", $code_2);
                $code_2 = str_replace(':', "%3A", $code_2);
                $code_2 = str_replace('(', "%28", $code_2);
                $code_2 = str_replace(')', "%29", $code_2);
                //sleep(0.5);
                $img = file_get_contents("https://mdxv.store/datamatrix?code=".$code_2);
                $img = 'data:image/jpg;base64,' . base64_encode($img);
            }
            else $img = $data["img"];

            $TEXT .= "
                <div class = 'code'>".$code."</div>
                <img src = '".$img."' />
                <div class = 'tire'>".$tire."</div>
                <div class = 'wrapper-page'></div>";

        }

        $TEXT .= "</body></html>";
        $dompdf = new DOMPDF();
        $dompdf->load_html($TEXT);
        $dompdf->setPaper(array(0,0,213,266));
        $dompdf->render();
        //$dompdf->stream();
        $temp = generate_16(20);
        file_put_contents("../../temp/".$temp.".pdf", $dompdf->output());

        echo $temp;
    }
    if($_POST["methodName"] == "printSaleCodes"){
        $sale = clean($_POST["id"]);
        $sql = mysqli_query($CONNECTION, "SELECT code, id, tire FROM code WHERE sale = ".$sale);


        $codes = file_get_contents("../../templates/admin/temp/tires/tire_code_pdf.html");;
        while($data = mysqli_fetch_array($sql)){
            $data2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM tire WHERE id = '".$data['tire']."'"));
            $tire = $data2["brand"]." ".$data2["model"]."<br>".$data2["w"]."/".$data2["h"]."R".$data2["r"]." ".$data2["nagr"]."".$data2["resist"];
            $codes .= "<div class = 'code'>".$data["code"]."</div>";
            $id = $data["id"];
            $code = $data["code"];
            $code = str_replace("&#40;", "(", $code);
            $code = str_replace("&#41;", ")", $code);
            $code = str_replace("&#706;", "<", $code);
            $code = str_replace("&#707;", ">", $code);
            $code = str_replace("&", "%26", $code);
            $img = file_get_contents("https://mdxv.store/datamatrix?code=".$code);
            $img = 'data:image/jpg;base64,' . base64_encode($img);
            $codes .= "<img src='$img'><div class = 'tire'>".$tire."</div><div class = 'wrapper-page'></div>";
        }
        //$codes = substr_replace($codes, "", -2);
        $codes .= "</body></html>";
        $dompdf = new DOMPDF();
        $dompdf->load_html($codes);
        $dompdf->setPaper(array(0,0,213,266));
        $dompdf->render();
        $temp = generate_16(20);
        file_put_contents("../../temp/".$temp.".pdf", $dompdf->output());

        echo $temp;
    }




?>