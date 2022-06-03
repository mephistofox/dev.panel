<?php

    require "../../settings.php";
    require "../../functions.php";

    proof();

    if($_POST["methodName"] == "movementsStart"){      // Загрузка движений
        $TEXT = file_get_contents("../../templates/admin/temp/movements/movement_list.html");

        $TEXT = str_replace("%HEAD%", rootAndSortHead($CONNECTION, ID, 5, $SEP), $TEXT);

        $BASES = "<div id = 'movements_head_bases'><item data = '0' class = 'active' onClick = 'movementsSearch(8, 0);movementsBaseChange(this);'>Все</item>";
        $sql = mysqli_query($CONNECTION, "SELECT code, color FROM base");
        while($data = mysqli_fetch_array($sql)){
            $BASES .= "<item data = '".$data["code"]."' onClick = 'movementsSearch(8, \"".$data["code"]."\");movementsBaseChange(this);'><circle style = 'background: #".$data["color"]."'></circle>".$data["code"]."</item>";
        }
        $BASES .= "</div>";

        $TEXT = str_replace("%BASES%", $BASES, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "movementsSearch"){      // Загрузка движений
        $number = clean($_POST["number"]);
        $date = clean($_POST["date"]);
        $date_plan = clean($_POST["date_plan"]);
        $action = clean($_POST["action"]);
        $count = clean($_POST["count"]);
        $kuda = clean($_POST["kuda"]);
        $otkuda = clean($_POST["otkuda"]);
        $cureer = clean($_POST["cureer"]);
        $base = clean($_POST["base"]);
        $date_1 = clean($_POST["date_1"]);
        $date_2 = clean($_POST["date_2"]);
        $product = clean($_POST["product"]);

        if($date_1 != "0") $date_1 = strtotime($date_1) - 3*3600;
        if($date_2 != "0") $date_2 = strtotime($date_2) + 24*3600 - 3*3600;

        $sql_text = "SELECT * FROM movement WHERE id > 0 AND status > -2 ";
        //if($resist != "") $sql_text .= "AND resist LIKE '$resist%' ";
        if($action != "-1") $sql_text .= "AND action = '$action' ";
        if($kuda != "-1") $sql_text .= "AND kuda = '$kuda' ";
        if($otkuda != "-1") $sql_text .= "AND otkuda = '$otkuda' ";
        if($cureer != "-1") $sql_text .= "AND cureer = '$cureer' ";
        if($base != "0") $sql_text .= "AND (otkuda LIKE '%$base%' OR kuda LIKE '%$base%') ";
        if($date_1 != "0") $sql_text .= "AND date >= '$date_1' ";
        if($date_2 != "0") $sql_text .= "AND date <= '$date_2' ";
        if($product != "0") $sql_text .= "AND article = '$product' ";
        if($number == 1) $sql_text .= "ORDER BY number ";
        if($number == 2) $sql_text .= "ORDER BY number DESC ";
        if($date == 1) $sql_text .= "ORDER BY date ";
        if($date == 2) $sql_text .= "ORDER BY date DESC ";
        if($date_plan == 1) $sql_text .= "ORDER BY date_or ";
        if($date_plan == 2) $sql_text .= "ORDER BY date_or DESC ";
        if($count == 1) $sql_text .= "ORDER BY count ";
        if($count == 2) $sql_text .= "ORDER BY count DESC ";

        $data = rootAndSort($CONNECTION, ID, 5, $SEP);
        $mas = explode("XXX", $data);
        $root = $mas[0];
        $sort = $mas[1];
        $count = $mas[2];
        $sort = explode($SEP, $sort);

        $MOVEMENTS_LIST = "";
        $k = 1000;
        //echo $sql_text;
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $func = "onClick = 'windowMovementView(".$data["id"].");'";
            //$func = "";
            $MOVEMENTS_LIST .= "<div class = 'movements_body_list_item'>"; //onClick = 'windowTireView(".$data["id"].");

            $dop = "";
            if($data["status"] ==-1) $dop = "style = 'background: #DBDBDB;'";
            if($data["status"] == 0) $dop = "style = 'background: #ff8a2c;'";
            if($data["status"] == 1) $dop = "style = 'background: #8bf824;'";

            //$action = "
            //    <div class = 'select select_small' style = 'width: 132px; z-index: ".$k."' id = 'action_".$data["id"]."'>
            //        <arrow></arrow>
            //        <headline><i>Выбрать</i></headline>
            //        <div data = '0' onClick = 'tiresAction(".$data["id"].", 0);'>Продать</div>
            //        <div data = '1' onClick = 'tiresAction(".$data["id"].", 1);'>Списать</div>
            //        <div data = '2' onClick = 'tiresAction(".$data["id"].", 2);'>Приемка</div>
            //        <div data = '3' onClick = 'tiresAction(".$data["id"].", 3);'>Перемещение</div>
            //    </div>";
            $k--;

            switch($data["action"]){
                case 1: $action = "Приемка"; break;
                case 2: $action = "Списание"; break;
                case 3: $action = "Перемещение"; break;
                case 4: $action = "Пополнение"; break;
                case 5: $action = "Продажа"; break;
            }
            switch($data["action"]){
                case 1: $count2 = "+ ".$data["count"]; break;
                case 2: $count2 = "- ".$data["count"]; break;
                case 3: $count2 = "+ ".$data["count"]; break;
                case 4: $count2 = "+ ".$data["count"]; break;
                case 5: $count2 = "- ".$data["count"]; break;
            }

            if($data["status"] == 0)$actions_list = "<span onClick = 'movementsReceiptConfirmation(".$data["id"].")'>Подтвердить</span>";
            else $actions_list = "";

            if($data["date_or"] != "") $date_or = date("d.m.y", $data["date_or"]); else $date_or = "";
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE movement = '".$data["id"]."' LIMIT 1"));
            if(isset($temp["id"])) $codes = "<div class = 'link_blue_4' onClick = 'windowCodesView(".$data["id"].", 1);'>Коды маркировки</div>"; else $codes = "";

            if($data["payer"] != 0){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE id = '".$data["payer"]."'"));
                $payer = $temp["name"];
            }
            else $payer = "";
            if($root[ 0] == 1) $mas[ 0] = "<div $func class = 'movement_item text_overflow' style = 'width: 114px;'><circle $dop></circle> ".$data["number"]."</div>";
            if($root[ 1] == 1) $mas[ 1] = "<div $func class = 'movement_item text_overflow' style = 'width: 94px;' >".date("d.m.y H:i", $data["date"])."</div>";
            if($root[ 2] == 1) $mas[ 2] = "<div $func class = 'movement_item text_overflow' style = 'width: 119px;'>".$action."</div>";
            if($root[ 3] == 1) $mas[ 3] = "<div $func class = 'movement_item text_overflow' style = 'width: 104px;'><span3 onClick = 'movementsSearch(10, \"".$data["article"]."\")'>".$data["article"]."</span3></div>";
            if($root[ 4] == 1) $mas[ 4] = "<div $func class = 'movement_item text_overflow' style = 'width: 647px;' title = '".$data["info"]."'>".$data["info"]."</div>";
            if($root[ 5] == 1) $mas[ 5] = "<div $func class = 'movement_item text_overflow' style = 'width: 119px;'>".$data["kuda"]."</div>";
            if($root[ 6] == 1) $mas[ 6] = "<div $func class = 'movement_item text_overflow' style = 'width: 119px;'>".$data["otkuda"]."</div>";
            if($root[ 7] == 1) $mas[ 7] = "<div $func class = 'movement_item text_overflow' style = 'width: 86px; text-align: right;' >".$count2."</div>";
            if($root[ 8] == 1) $mas[ 8] = "<div $func class = 'movement_item text_overflow' style = 'width: 94px; text-align: right;' >".$data["bef"]." <span2>".$data["aft"]."</span2></div>";
            if($root[ 9] == 1) $mas[ 9] = "<div $func class = 'movement_item text_overflow' style = 'width: 201px;'>".$data["cureer"]."</div>";
            if($root[10] == 1) $mas[10] = "<div class = 'movement_item' style = 'width: 201px;'>".$actions_list."</div>";
            if($root[11] == 1) $mas[11] = "<div $func class = 'movement_item' style = 'width: 111px;'>".$date_or."</div>";
            if($root[12] == 1) $mas[12] = "<div class = 'movement_item' style = 'width: 136px;'>".$codes."</div>";
            if($root[13] == 1) $mas[13] = "<div class = 'movement_item' style = 'width: 186px;'>".$payer."</div>";
            for($i = 1; $i < $count*2; $i++){
                if($i%2 == 1){
                    $num = $sort[$i];
                    if($sort[$i+1] == 1) $MOVEMENTS_LIST .= $mas[$num];
                }
            }
            $MOVEMENTS_LIST .= "</div><br>";
        }

        echo $MOVEMENTS_LIST;
    }
    if($_POST["methodName"] == "movementsReceiptConfirmation"){  // Подверждение приемки товара
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT status, kuda, otkuda, count, p_id, p_type, action FROM movement WHERE id = '$id'"));
        if($data["status"] < 1){
            $time = time();
            mysqli_query($CONNECTION, "UPDATE movement SET date_finish = '$time' WHERE id = '$id'");
            $p_id = $data["p_id"];
            $p_type  = $data["p_type"];
            $action = $data["action"];
            if($action == 1 || $action == 4){   // Приёмка и пополнение
                $count = $data["count"];
                $kuda = $data["kuda"];
                $p_id = $data["p_id"];
                $p_type  = $data["p_type"];
                $mas = explode(" - ", $kuda);
                if($p_type < 3){
                    $storage = $mas[1];
                    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$storage'"));
                    $kuda = $data["id"];
                }
                else{
                    $base = $mas[0];
                    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$base'"));
                    $kuda = $data["id"];
                }
                if(productMove($CONNECTION, 0, $kuda, $p_type, $p_id, $count)) mysqli_query($CONNECTION, "UPDATE movement SET status = 1 WHERE id = '$id'");
            }
            if($action == 2){       // Списание
                $count = $data["count"];
                $otkuda = $data["otkuda"];
                $p_id = $data["p_id"];
                $p_type  = $data["p_type"];
                $mas = explode(" - ", $otkuda);
                if($p_type < 3){
                    $storage = $mas[1];
                    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$storage'"));
                    $otkuda = $data["id"];
                }
                else{
                    $base = $mas[0];
                    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$base'"));
                    $otkuda = $data["id"];
                }

                if(productMove($CONNECTION, $otkuda, 0, $p_type, $p_id, $count)) mysqli_query($CONNECTION, "UPDATE movement SET status = 1 WHERE id = '$id'");
            }
            if($action == 3){       // Перемещение
                $count = $data["count"];
                $otkuda = $data["otkuda"];
                $kuda = $data["kuda"];
                $p_id = $data["p_id"];
                $p_type  = $data["p_type"];

                if($p_type < 3){
                    $mas = explode(" - ", $otkuda);
                    $storage = $mas[1];
                    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$storage'"));
                    $otkuda = $data["id"];

                    $mas = explode(" - ", $kuda);
                    $storage = $mas[1];
                    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$storage'"));
                    $kuda = $data["id"];
                }
                if($p_type == 3){
                    $mas = explode(" - ", $otkuda);
                    $storage = $mas[0];
                    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$storage'"));
                    $otkuda = $data["id"];

                    $mas = explode(" - ", $kuda);
                    $storage = $mas[0];
                    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$storage'"));
                    $kuda = $data["id"];
                }
                if(productMove($CONNECTION, $otkuda, $kuda, $p_type, $p_id, $count)) mysqli_query($CONNECTION, "UPDATE movement SET status = 1 WHERE id = '$id'");
            }

            echo date("d.m.Y H:i");
        }

    }
    if($_POST["methodName"] == "movementsMovementView"){  // Загрузка движения
        $id = clean($_POST["id"]);

        $TEXT = file_get_contents("../../templates/admin/temp/moving_view.html");

        $TEXT = str_replace("%ID%", $id, $TEXT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM movement WHERE id = '$id'"));

        $action_type = $data["action"];
        $movement_status = $data["status"];

        if($data["date_finish"] != "") $DATE_FINISH = date("d.m.Y", $data["date_finish"]);
        else $DATE_FINISH = "";
        $TEXT = str_replace("%DATE%", $DATE_FINISH, $TEXT);

        if($data["date_or"] != "") $DATE_PLAN = date("d.m.Y", $data["date_or"]);
        else $DATE_PLAN = "";
        $TEXT = str_replace("%DATE_PLAN%", $DATE_PLAN, $TEXT);

        switch($action_type){
            case 1: $MOVING_TYPE = "Приёмка"; break;
            case 2: $MOVING_TYPE = "Списание"; break;
            case 3: $MOVING_TYPE = "Перемещение"; break;
            case 4: $MOVING_TYPE = "Пополнение"; break;
        }

        $TEXT = str_replace("%MOVING_TYPE%", $MOVING_TYPE, $TEXT);

        $dop = "";
        if($data["status"] == 0) $dop = "style = 'background: #ff8a2c;'";
        if($data["status"] == 1) $dop = "style = 'background: #8bf824;'";
        $TEXT = str_replace("%ID_STATUS%", "<circle $dop></circle>".$data["number"], $TEXT);

        $PRODUCT = "";
        $sql = "SELECT * FROM ";
        switch($data["p_type"]){
            case 1: $sql .= "tire"; $a = "S"; break;
            case 2: $sql .= "disk"; $a = "D"; break;
            case 3: $sql .= "product"; $a = "T"; break;
        }
        $sql .= " WHERE id = '".$data["p_id"]."'";
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));

        $price_0 = $temp["price_purchase"];
        $barcode = $temp["barcode"];

        if($data["p_type"] == 1) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$temp["article"]."</pid>
                <desc>".$temp["model"]." ".$temp["w"]."/".$temp["h"]."R".$temp["r"]."</desc>
            </div>";
        if($data["p_type"] == 2) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$temp["article"]."</pid>
                <desc>".$temp["nomenclature"]." ".$temp["w"]."/R".$temp["r"]."</desc>
            </div>";
        if($data["p_type"] == 3) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$temp["article"]."</pid>
                <desc>".$temp["name"].", ".$temp["params"]."</desc>
            </div>";

        $TEXT = str_replace("%PRODUCT%", $PRODUCT, $TEXT);

        $TEXT = str_replace("%COUNT%", $data["count"], $TEXT);

        $osnov = $data["info"];

        $cur = $data["cureer"];
        if($cur == "" || $cur == " ") $param_delete = 1; else $param_delete = 0;

        if($action_type == 1){ // Приёмка
            $TEXT = str_replace("%PROVIDERS%", $data["otkuda"], $TEXT);
            $TEXT = str_replace("%PRICE_0%", $price_0, $TEXT);
            $TEXT = str_replace("%BASE_STORAGE%", $data["kuda"], $TEXT);
        }

        if($action_type == 2){ // Списание
            $TEXT = str_replace("%STORAGE%", $data["otkuda"], $TEXT);
            $TEXT = str_replace("%OSNOVANIE%", $osnov, $TEXT);
        }

        if($action_type == 3){ // Перемещение
            $TEXT = str_replace("%CUREER%", $cur, $TEXT);
            $TEXT = str_replace("%STORAGE%", $data["otkuda"], $TEXT);
            $TEXT = str_replace("%KUDA%", $data["kuda"], $TEXT);
        }

        if($action_type == 4){ // Пополнение
            $TEXT = str_replace("%CUREER%", $cur, $TEXT);
            $TEXT = str_replace("%STORAGE%", $data["otkuda"], $TEXT);
            $TEXT = str_replace("%KUDA%", $data["kuda"], $TEXT);
        }
        $PAYER = "";
        if($data["payer"] != 0){
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM payer WHERE id = '".$data["payer"]."'"));
            $PAYER = "
                <div class = 'receipt_str'>
                    <title style = 'line-height: 30px;'>Плательщик</title>
                    <val>".$temp['name']."</val>
                </div>";
        }
        $TEXT = str_replace("%PAYER%", $PAYER, $TEXT);

        $CODES = "";
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE movement = '".$data["id"]."' LIMIT 1"));
        if(isset($temp["id"])){
            $CODES = "
                <div class = 'receipt_str'>
                    <title style = 'line-height: 30px;'>Коды маркировки</title>
                    <val>";
            $sql = mysqli_query($CONNECTION, "SELECT code FROM code WHERE movement = '".$data["id"]."'");
            while($temp = mysqli_fetch_array($sql)){
                $CODES .= $temp["code"]."; ";
            }
            $CODES .= "</val></div>";
        }
        else $CODES = "";
        $TEXT = str_replace("%CODES%", $CODES, $TEXT);


        echo $TEXT.$SEP.$action_type.$SEP.$param_delete.$SEP.$movement_status;
    }
    if($_POST["methodName"] == "movementsMovementRedactLoad"){  // Загрузка движения для редактирования
        $id = clean($_POST["id"]);

        $TEXT = file_get_contents("../../templates/admin/temp/moving_view.html");

        $TEXT = str_replace("%ID%", $id, $TEXT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM movement WHERE id = '$id'"));

        $action_type = $data["action"];
        $otkuda = $data["otkuda"];
        $count_global = $data["count"];

        switch($action_type){
            case 1: $MOVING_TYPE = "Приёмка"; break;
            case 2: $MOVING_TYPE = "Списание"; break;
            case 3: $MOVING_TYPE = "Перемещение"; break;
            case 4: $MOVING_TYPE = "Пополнение"; break;
        }

        $TEXT = str_replace("%MOVING_TYPE%", $MOVING_TYPE, $TEXT);
        $TEXT = str_replace("%ACTION_TYPE%", $action_type, $TEXT);


        $dop = "";
        if($data["status"] == 0) $dop = "style = 'background: #ff8a2c;'";
        if($data["status"] == 1) $dop = "style = 'background: #8bf824;'";
        $TEXT = str_replace("%ID_STATUS%", "<circle $dop></circle>".$data["number"], $TEXT);

        if($data["date_finish"] != "") $DATE_FINISH = date("d.m.Y", $data["date_finish"]);
        else $DATE_FINISH = "";
        $TEXT = str_replace("%DATE%", $DATE_FINISH, $TEXT);

        if($data["date_or"] != "") $DATE_PLAN = date("d.m.Y", $data["date_or"]);
        else $DATE_PLAN = "";
        $TEXT = str_replace("%DATE_PLAN%", "<div id = 'move_date_plan'></div>", $TEXT);


        $PRODUCT = "";
        $sql = "SELECT * FROM ";
        switch($data["p_type"]){
            case 1: $sql .= "tire"; $a = "S"; break;
            case 2: $sql .= "disk"; $a = "D"; break;
            case 3: $sql .= "product"; $a = "T"; break;
        }
        $sql .= " WHERE id = '".$data["p_id"]."'";
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));

        $price_0 = $temp["price_purchase"];
        $barcode = $temp["barcode"];

        if($data["p_type"] == 1) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$temp["article"]."</pid>
                <desc>".$temp["model"]." ".$temp["w"]."/".$temp["h"]."R".$temp["r"]."</desc>
            </div>";
        if($data["p_type"] == 2) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$temp["article"]."</pid>
                <desc>".$temp["nomenclature"]." ".$temp["w"]."/R".$temp["r"]."</desc>
            </div>";
        if($data["p_type"] == 3) $PRODUCT = "
            <div id = 'receipt_product'>
                <pid>".$a.$temp["article"]."</pid>
                <desc>".$temp["name"].", ".$temp["params"]."</desc>
            </div>";

        $p_type = $data["p_type"];

        $TEXT = str_replace("%PRODUCT%", $PRODUCT, $TEXT);

        $TEXT = str_replace("%COUNT%", $data["count"], $TEXT);

        $osnov = $data["info"];

        $cur = $data["cureer"];

        $kuda_global = $data["kuda"];
        $otkuda_global = $data["otkuda"];

        if($action_type == 1){
            $provider = $data["otkuda"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM provider WHERE name = '$provider'"));

            $PROVIDERS = "
                <div class = 'select' id = 'provider' style = 'min-width: 234px;'>
                    <arrow></arrow>
                    <headline>".$data["otkuda"]."</headline>
                    <input type = 'hidden' id = 'provider_hidden' value = '".$temp["id"]."' />
            ";
            $sql = mysqli_query($CONNECTION, "SELECT id, name FROM provider");
            while($temp = mysqli_fetch_array($sql)){
               $PROVIDERS .= "<div data = '".$temp["id"]."'>".$temp["name"]."</div>";
            }
            $PROVIDERS .= "</div>";
            $TEXT = str_replace("%PROVIDERS%", $PROVIDERS, $TEXT);

            $TEXT = str_replace("%PRICE_0%", $price_0, $TEXT);

            $temp = explode(" - ", $data["kuda"]);
            $c_base = $temp[0];
            $c_storage = $temp[1];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$c_base'"));
            $c_base_id = $temp["id"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$c_storage'"));
            $c_storage_id = $temp["id"];

            $BASE_STORAGE = "<div id = 'base_storage'>";
            if(TYPE == 1){
                $BASE_STORAGE .= "
                    <div class = 'select' id = 'base_1' style = 'width: 80px;'>
                        <arrow></arrow>
                        <headline>".$c_base."</headline>
                        <input type = 'hidden' id = 'base_1_hidden' value = '".$c_base_id."' />
                ";
                $sql = mysqli_query($CONNECTION, "SELECT id, code, color FROM base");
                while($temp = mysqli_fetch_array($sql)){
                    if($data["p_type"] == 3) $BASE_STORAGE .= "<div data = '".$temp["id"]."'><circle style = 'background: #".$temp["color"]."'></circle>".$temp["code"]."</div>";
                    else $BASE_STORAGE .= "<div data = '".$temp["id"]."' onClick = 'baseStorageLoad(this);'><circle style = 'background: #".$temp["color"]."'></circle>".$temp["code"]."</div>";
                }
                $BASE_STORAGE .= "</div>";

                $base = $c_base_id;
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base WHERE id = '$base'"));
                $BASE_STORAGE .= "
                    <div id = 'storage_base'>
                        <div class = 'select' id = 'storage_1' style = 'width: 80px;'>
                            <arrow></arrow>
                            <headline>".$c_storage."</headline>
                            <input type = 'hidden' id = 'storage_1_hidden' value = '".$c_storage_id."' />";
                $sql = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$base' AND composite = 0");
                while($temp = mysqli_fetch_array($sql)){
                    $BASE_STORAGE .= "<div data = '".$temp["id"]."' onClick = 'baseStorageProof(this);'>".$temp["code"]."</div>";
                }
                $BASE_STORAGE .= "</div>";
            }
            else{
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM user WHERE id = ".ID));
                $base = $temp["base"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base WHERE id = '$base'"));
                $BASE_STORAGE .= "<input type = 'hidden' id = 'base_1_hidden' value = '".$base."'><circle style = 'background: #".$temp["color"]."'></circle>".$temp["code"];
                $BASE_STORAGE .= "
                    <div id = 'storage_base'>
                        <div class = 'select' id = 'storage_1' style = 'width: 80px;'>
                            <arrow></arrow>
                            <headline>".$c_storage."</headline>
                            <input type = 'hidden' id = 'storage_1_hidden' value = '".$c_storage_id."' />";
                $sql = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$base' AND composite = 0");
                while($temp = mysqli_fetch_array($sql)){
                    $BASE_STORAGE .= "<div data = '".$temp["id"]."' onClick = 'baseStorageProof(this);'>".$temp["code"]."</div>";
                }
                $BASE_STORAGE .= "</div>";
            }

            $BASE_STORAGE .= "</div><input type = 'hidden' id = 'storage_hidden_id' value = '".$c_storage_id."' />";
            $TEXT = str_replace("%BASE_STORAGE%", $BASE_STORAGE, $TEXT);


        }

        if($action_type == 2){
            $nal = "";
            $flag = false;
            if($data["p_type"] < 3){
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
                        $name_2 = $base_code." - ".$temp["code"]." - ".$code;
                    }
                    else{
                        $name = "<circle style = 'background: #".$color."'></circle>".$base_code." - ".$code;
                        $name_2 = $base_code." - ".$code;
                    }
                    $nal .= "
                        <div class = 'storage_str' data = '".$storage."' data_2 = '".$name_2."' onClick = 'productStorageClick(this);'>
                            <span1>".$count."</span1>
                            <rightcol>".$name."</rightcol>
                        </div>
                    ";

                    if($name_2 == $otkuda) $flag = true;
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

                    if($base_code == $otkuda) $flag = true;

                    $nal .= "
                        <div class = 'storage_str' data = '".$base."' data_2 = '".$base_code."' onClick = 'productStorageClick(this);'>
                            <span1>".$count."</span1>
                            <rightcol><circle style = 'background: #".$color."'></circle>".$base_code."</rightcol>
                        </div>
                    ";
                }
            }
            if(!$flag){
                if($data["p_type"] < 3){
                    $temp = explode(" - ", $otkuda);
                    $base = $temp[0];
                    $storage = $temp[1];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, id, name FROM base WHERE code = '$base'"));
                    $color = $temp["color"];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$storage'"));
                    $base = $temp["id"];
                }
                else{
                    $temp = explode(" - ", $otkuda);
                    $base = $temp[0];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, id, name FROM base WHERE code = '$base'"));
                    $color = $temp["color"];
                    $base = $temp["id"];
                }

                $nal .= "
                    <div class = 'storage_str' data = '".$base."' data_2 = '".$otkuda."' onClick = 'productStorageClick(this);'>
                        <span1>".$count_global."</span1>
                        <rightcol><circle style = 'background: #".$color."'></circle>".$otkuda."</rightcol>
                    </div>
                ";
            }
            $TEXT = str_replace("%STORAGE%", $nal, $TEXT);

            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM product_param WHERE type = 8 AND value = '$osnov'"));
            $osnov_id = $data["id"];

            $OSNOVANIE = "
                <div class = 'select' style = 'width: 280px;' id = 'osnovanie'>
                    <arrow></arrow>
                    <headline>".$osnov."</headline>
                    <input type = 'hidden' id = 'osnovanie_hidden' value = '".$osnov_id."'>";
            $sql = mysqli_query($CONNECTION, "SELECT value, id FROM product_param WHERE type = 8 AND status = 1");
            while($data = mysqli_fetch_array($sql)) $OSNOVANIE .= "<div data = '".$data["id"]."' onClick = 'downAddOsnovanie(\"".$data["id"]."\");'>".$data["value"]."</div>";
            $OSNOVANIE .= "<div data = '-2' onClick = 'downAddOsnovanie(\"-2\");'>Другое основание</div></div><textarea onKeyUp = 'deleteBorderRed(this);deleteBorderRed(\"#osnovanie\");' id = 'osnovanie_textarea'></textarea>";
            $TEXT = str_replace("%OSNOVANIE%", $OSNOVANIE, $TEXT);

        }

        if($action_type == 3){
            if($cur == "" || $cur == " "){
                $cur = "Курьер";
                $cur_id = -1;
            }
            else{
                $temp = explode(" ", $cur);
                $surname = $temp[0];
                $name = $temp[1];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM user WHERE surname = '$surname' AND name = '$name' AND type = 5"));
                $cur_id = $temp["id"];
            }
            $CUREER = "
                <div class = 'select' id = 'cureer_2' style = 'min-width: 234px;'>
                    <arrow></arrow>
                    <headline>".$cur."</headline>
                    <input type = 'hidden' id = 'cureer_2_hidden' value = '".$cur_id."' />
            ";
            $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type = 5");
            while($data = mysqli_fetch_array($sql)){
               $CUREER .= "<div data = '".$data["id"]."'>".$data["surname"]." ".$data["name"]."</div>";
            }
            $CUREER .= "</div>";
            $TEXT = str_replace("%CUREER%", $CUREER, $TEXT);

            $nal = "";
            $flag = false;
            if($p_type < 3){
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
                        $name_2 = $base_code." - ".$temp["code"]." - ".$code;
                    }
                    else{
                        $name = "<circle style = 'background: #".$color."'></circle>".$base_code." - ".$code;
                        $name_2 = $base_code." - ".$code;
                    }
                    $nal .= "
                        <div class = 'storage_str' data = '".$storage."' data_2 = '".$name_2."' onClick = 'productStorageClick(this);'>
                            <span1>".$count."</span1>
                            <rightcol>".$name."</rightcol>
                        </div>
                    ";

                    if($name_2 == $otkuda) $flag = true;
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

                    if($base_code == $otkuda) $flag = true;

                    $nal .= "
                        <div class = 'storage_str' data = '".$base."' data_2 = '".$base_code."' onClick = 'productStorageClick(this);'>
                            <span1>".$count."</span1>
                            <rightcol><circle style = 'background: #".$color."'></circle>".$base_code."</rightcol>
                        </div>
                    ";
                }
            }
            if(!$flag){
                if($p_type < 3){
                    $temp = explode(" - ", $otkuda);
                    $base = $temp[0];
                    $storage = $temp[1];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, id, name, code FROM base WHERE code = '$base'"));
                    $color = $temp["color"];
                    $base_code = $temp["code"];
                    $base_name = $temp["name"];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, mother, code FROM storage WHERE code = '$storage'"));
                    $mother = $temp["mother"];
                    $base = $temp["id"];
                    $code = $temp["code"];

                    if($mother > 0){
                        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM storage WHERE id = '$mother'"));
                        $name = "<circle style = 'background: #".$color."'></circle>".$base_code." - ".$temp["code"]." - ".$code;
                        $name_2 = $base_code." - ".$temp["code"]." - ".$code;
                    }
                    else{
                        $name = "<circle style = 'background: #".$color."'></circle>".$base_code." - ".$code;
                        $name_2 = $base_code." - ".$code;
                    }
                }
                else{
                    $temp = explode(" - ", $otkuda);
                    $base = $temp[0];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, id, name FROM base WHERE code = '$base'"));
                    $color = $temp["color"];
                    $base = $temp["id"];
                }

                $nal .= "
                    <div class = 'storage_str' data = '".$base."' data_2 = '".$name_2."' onClick = 'productStorageClick(this);'>
                        <span1>".$count_global."</span1>
                        <rightcol><circle style = 'background: #".$color."'></circle>".$otkuda."</rightcol>
                    </div>
                ";
            }
            $TEXT = str_replace("%STORAGE%", $nal, $TEXT);

            $temp = explode(" - ", $kuda_global);
            $c_base = $temp[0];
            $c_storage_id = 0;
            $c_storage = 0;
            if(isset($temp[1])){
                $c_storage = $temp[1];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$c_storage'"));
                $c_storage_id = $temp["id"];
            }

            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$c_base'"));
            $c_base_id = $temp["id"];


            $BASE_STORAGE = "<div id = 'base_storage'>";
            if(TYPE == 1){
                $BASE_STORAGE .= "
                    <div class = 'select' id = 'base_1' style = 'width: 80px;'>
                        <arrow></arrow>
                        <headline>".$c_base."</headline>
                        <input type = 'hidden' id = 'base_1_hidden' value = '".$c_base_id."' />
                ";
                $sql = mysqli_query($CONNECTION, "SELECT id, code, color FROM base");
                while($temp = mysqli_fetch_array($sql)){
                    if($data["p_type"] == 3) $BASE_STORAGE .= "<div data = '".$temp["id"]."'><circle style = 'background: #".$temp["color"]."'></circle>".$temp["code"]."</div>";
                    else $BASE_STORAGE .= "<div data = '".$temp["id"]."' onClick = 'baseStorageLoad(this);'><circle style = 'background: #".$temp["color"]."'></circle>".$temp["code"]."</div>";
                }
                $BASE_STORAGE .= "</div>";
                if($p_type < 3){
                    $base = $c_base_id;
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base WHERE id = '$base'"));
                    $BASE_STORAGE .= "
                        <div id = 'storage_base'>
                            <div class = 'select' id = 'storage_1' style = 'width: 80px;'>
                                <arrow></arrow>
                                <headline>".$c_storage."</headline>
                                <input type = 'hidden' id = 'storage_1_hidden' value = '".$c_storage_id."' />";
                    $sql = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$base' AND composite = 0");
                    while($temp = mysqli_fetch_array($sql)){
                        $BASE_STORAGE .= "<div data = '".$temp["id"]."' onClick = 'baseStorageProof(this);'>".$temp["code"]."</div>";
                    }
                    $BASE_STORAGE .= "</div>";
                }
            }
            else{
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM user WHERE id = ".ID));
                $base = $temp["base"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base WHERE id = '$base'"));
                $BASE_STORAGE .= "<input type = 'hidden' id = 'base_1_hidden' value = '".$base."'><circle style = 'background: #".$temp["color"]."'></circle>".$temp["code"];
                $BASE_STORAGE .= "
                    <div id = 'storage_base'>
                        <div class = 'select' id = 'storage_1' style = 'width: 80px;'>
                            <arrow></arrow>
                            <headline>".$c_storage."</headline>
                            <input type = 'hidden' id = 'storage_1_hidden' value = '".$c_storage_id."' />";
                $sql = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$base' AND composite = 0");
                while($temp = mysqli_fetch_array($sql)){
                    $BASE_STORAGE .= "<div data = '".$temp["id"]."' onClick = 'baseStorageProof(this);'>".$temp["code"]."</div>";
                }
                $BASE_STORAGE .= "</div>";
            }

            $BASE_STORAGE .= "</div><input type = 'hidden' id = 'storage_hidden_id' value = '".$c_storage_id."' />";
            $TEXT = str_replace("%KUDA%", $BASE_STORAGE, $TEXT);
        }

        if($action_type == 4){
            if($cur == "" || $cur == " "){
                $cur = "Курьер";
                $cur_id = -1;
            }
            else{
                $temp = explode(" ", $cur);
                $surname = $temp[0];
                $name = $temp[1];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM user WHERE surname = '$surname' AND name = '$name' AND type = 5"));
                $cur_id = $temp["id"];
            }
            $CUREER = "
                <div class = 'select' id = 'cureer_2' style = 'min-width: 234px;'>
                    <arrow></arrow>
                    <headline>".$cur."</headline>
                    <input type = 'hidden' id = 'cureer_2_hidden' value = '".$cur_id."' />
            ";
            $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type = 5");
            while($data = mysqli_fetch_array($sql)){
               $CUREER .= "<div data = '".$data["id"]."'>".$data["surname"]." ".$data["name"]."</div>";
            }
            $CUREER .= "</div>";
            $TEXT = str_replace("%CUREER%", $CUREER, $TEXT);

            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM provider WHERE name = '$otkuda_global'"));

            $PROVIDERS = "
                <div class = 'select' id = 'provider' style = 'min-width: 234px;'>
                    <arrow></arrow>
                    <headline>".$otkuda_global."</headline>
                    <input type = 'hidden' id = 'provider_hidden' value = '".$temp["id"]."' />
            ";
            $sql = mysqli_query($CONNECTION, "SELECT id, name FROM provider");
            while($temp = mysqli_fetch_array($sql)){
               $PROVIDERS .= "<div data = '".$temp["id"]."'>".$temp["name"]."</div>";
            }
            $PROVIDERS .= "</div>";
            $TEXT = str_replace("%STORAGE%", $PROVIDERS, $TEXT);

            $temp = explode(" - ", $kuda_global);
            $c_base = $temp[0];
            $c_storage = $temp[1];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$c_base'"));
            $c_base_id = $temp["id"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$c_storage'"));
            $c_storage_id = $temp["id"];

            $BASE_STORAGE = "<div id = 'base_storage'>";
            if(TYPE == 1){
                $BASE_STORAGE .= "
                    <div class = 'select' id = 'base_1' style = 'width: 80px;'>
                        <arrow></arrow>
                        <headline>".$c_base."</headline>
                        <input type = 'hidden' id = 'base_1_hidden' value = '".$c_base_id."' />
                ";
                $sql = mysqli_query($CONNECTION, "SELECT id, code, color FROM base");
                while($temp = mysqli_fetch_array($sql)){
                    if($data["p_type"] == 3) $BASE_STORAGE .= "<div data = '".$temp["id"]."'><circle style = 'background: #".$temp["color"]."'></circle>".$temp["code"]."</div>";
                    else $BASE_STORAGE .= "<div data = '".$temp["id"]."' onClick = 'baseStorageLoad(this);'><circle style = 'background: #".$temp["color"]."'></circle>".$temp["code"]."</div>";
                }
                $BASE_STORAGE .= "</div>";

                $base = $c_base_id;
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base WHERE id = '$base'"));
                $BASE_STORAGE .= "
                    <div id = 'storage_base'>
                        <div class = 'select' id = 'storage_1' style = 'width: 80px;'>
                            <arrow></arrow>
                            <headline>".$c_storage."</headline>
                            <input type = 'hidden' id = 'storage_1_hidden' value = '".$c_storage_id."' />";
                $sql = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$base' AND composite = 0");
                while($temp = mysqli_fetch_array($sql)){
                    $BASE_STORAGE .= "<div data = '".$temp["id"]."' onClick = 'baseStorageProof(this);'>".$temp["code"]."</div>";
                }
                $BASE_STORAGE .= "</div>";
            }
            else{
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM user WHERE id = ".ID));
                $base = $temp["base"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code, color FROM base WHERE id = '$base'"));
                $BASE_STORAGE .= "<input type = 'hidden' id = 'base_1_hidden' value = '".$base."'><circle style = 'background: #".$temp["color"]."'></circle>".$temp["code"];
                $BASE_STORAGE .= "
                    <div id = 'storage_base'>
                        <div class = 'select' id = 'storage_1' style = 'width: 80px;'>
                            <arrow></arrow>
                            <headline>".$c_storage."</headline>
                            <input type = 'hidden' id = 'storage_1_hidden' value = '".$c_storage_id."' />";
                $sql = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$base' AND composite = 0");
                while($temp = mysqli_fetch_array($sql)){
                    $BASE_STORAGE .= "<div data = '".$temp["id"]."' onClick = 'baseStorageProof(this);'>".$temp["code"]."</div>";
                }
                $BASE_STORAGE .= "</div>";
            }

            $BASE_STORAGE .= "</div><input type = 'hidden' id = 'storage_hidden_id' value = '".$c_storage_id."' />";
            $TEXT = str_replace("%KUDA%", $BASE_STORAGE, $TEXT);
        }



        echo $TEXT.$SEP.$action_type.$SEP.$DATE_PLAN.$SEP.$otkuda;

    }
    if($_POST["methodName"] == "movementsDelete"){  // Удаление движения
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT cureer, id FROM movement WHERE id = '$id'"));
        if(isset($data["id"])){
            if($data["cureer"] == "" || $data["cureer"] == " ") mysqli_query($CONNECTION, "UPDATE movement SET status = -2 WHERE id = '$id'");
        }
        $codeSql = mysqli_query($CONNECTION, "select * from `code` where `movement`='".$id."'");
        while($r = mysqli_fetch_assoc($codeSql)){
            if($data['status'] != '1'){
                mysqli_query($CONNECTION, "delete from `code` where `id`='".$r['id']."'");
            }
        }
    }
    if($_POST["methodName"] == "movementRedact"){  // Редактирование движения
        $id = clean($_POST["id"]);
        $provider = clean($_POST["provider"]);
        $storage = clean($_POST["storage"]);
        $date_plan = clean($_POST["date_plan"]);
        $otkuda = clean($_POST["otkuda"]);
        $kuda = clean($_POST["kuda"]);
        $cureer = clean($_POST["cureer"]);
        $osnovanie = clean($_POST["osnovanie"]);
        $osnovanie_text = clean($_POST["osnovanie_text"]);
        $kuda_base = clean($_POST["kuda_base"]);

        $now_date = date("d.m.Y");
        if($date_plan == "") $new_status = 0;
        else{
            if($now_date == $date_plan) $new_status = 0; else $new_status = -1;
        }


        if($date_plan != "") $date_plan = strtotime($date_plan);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM movement WHERE id = '$id'"));
        if(isset($data["id"])){
            $action = $data["action"];

            if($data["status"] == -1 && $new_status == 0) mysqli_query($CONNECTION, "UPDATE movement SET status = 0 WHERE id = '$id'");
            if($data["status"] == 0 && $new_status == -1) mysqli_query($CONNECTION, "UPDATE movement SET status = -1 WHERE id = '$id'");

            if($action == 1){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM provider WHERE id = '$provider'"));
                $otkuda = $temp["name"];

                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$storage'"));
                $base = $temp["base"];
                $code = $temp["code"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
                $kuda = $temp["code"]." - ".$code;

                mysqli_query($CONNECTION, "UPDATE movement SET otkuda = '$otkuda', kuda = '$kuda', date_or = '$date_plan' WHERE id = '$id'");
                echo 1;
            }

            if($action == 2){
                if($osnovanie == -2) $info = $osnovanie_text;
                else {
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE id = '$osnovanie'"));
                    $info = $temp["value"];
                }

                mysqli_query($CONNECTION, "UPDATE movement SET otkuda = '$otkuda', info = '$info', date_or = '$date_plan' WHERE id = '$id'");
                echo 1;
            }

            if($action == 3){
                if($cureer == -1) $cureer = "";
                else{
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = '$cureer'"));
                    $cureer = $temp["surname"]." ".$temp["name"];
                }

                if($kuda == "-1"){
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$kuda_base'"));
                    $kuda = $temp["code"];
                }
                else{
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$kuda'"));
                    $base = $temp["base"];
                    $code = $temp["code"];
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
                    $kuda = $temp["code"]." - ".$code;
                }



                mysqli_query($CONNECTION, "UPDATE movement SET otkuda = '$otkuda', kuda = '$kuda', date_or = '$date_plan', cureer = '$cureer' WHERE id = '$id'");
                echo 1;
            }

            if($action == 4){
                if($cureer == -1) $cureer = "";
                else{
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname FROM user WHERE id = '$cureer'"));
                    $cureer = $temp["surname"]." ".$temp["name"];
                }

                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$kuda'"));
                $base = $temp["base"];
                $code = $temp["code"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
                $kuda = $temp["code"]." - ".$code;

                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name FROM provider WHERE id = '$provider'"));
                $otkuda = $temp["name"];

                mysqli_query($CONNECTION, "UPDATE movement SET otkuda = '$otkuda', kuda = '$kuda', date_or = '$date_plan', cureer = '$cureer' WHERE id = '$id'");
                echo 1;
            }

        }

    }

?>