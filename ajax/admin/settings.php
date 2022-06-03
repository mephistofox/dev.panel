<?php

    require "../../settings.php";
    require "../../functions.php";

    proof();

    if($_POST["methodName"] == "settingsStart"){      // Загрузка списка позиций меню исходя их типа и прав пользователя
        $TEXT = "<div id = 'settings_menu'>";

        if((TYPE == 1 || $root[8]) && TYPE != 3) $TEXT .= "<div class = 'list_item' id = 'settings_menu_providers' onClick = 'settingsLoad(\"providers\");'>Поставщики</div>";
        if(TYPE == 1) $TEXT .= "<div class = 'list_item' id = 'settings_menu_payers'   onClick = 'settingsLoad(\"payers\");'  >Плательщики</div>";
        if(TYPE == 1) $TEXT .= "<div class = 'list_item' id = 'settings_menu_workers'   onClick = 'settingsLoad(\"workers\");'  >Сотрудники</div>";
        if((TYPE == 1 || $root[9]) && TYPE != 3) $TEXT .= "<div class = 'list_item' id = 'settings_menu_couriers'  onClick = 'settingsLoad(\"couriers\");' >Курьеры</div>";
        if(TYPE == 1) $TEXT .= "<div class = 'list_item' id = 'settings_menu_bases'     onClick = 'settingsLoad(\"bases\");'    >Базы и хранилища</div>";

        if(TYPE == 1 || $root[8] || $root[9]) $TEXT .= "<br>";

        $TEXT .= "<div class = 'list_item' id = 'settings_menu_pass'     onClick = 'settingsLoad(\"pass\");'    >Смена пароля</div>";
        $TEXT .= "<div class = 'list_item' id = 'settings_menu_personal' onClick = 'settingsLoad(\"personal\");'>Личные сведения</div>";
        $TEXT .= "<br>";
        if(TYPE == 1){
            $TEXT .= "<div class = 'list_item' id = 'settings_menu_products' onClick = 'settingsLoad(\"products\");'>Параметры товаров</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_menu_offs'     onClick = 'settingsLoad(\"offs\");'    >Основания списаний</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_menu_money'    onClick = 'settingsLoad(\"money\");'   >Разменные деньги</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_menu_delivery' onClick = 'settingsLoad(\"delivery\");'>Транспортные компании</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_menu_season'   onClick = 'settingsLoad(\"season\");'  >Сезонное хранение</div>";
            //$TEXT .= "<div class = 'list_item' id = 'settings_menu_naklad'   onClick = 'settingsLoad(\"naklad\");'  >Товарная накладная</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_menu_file'     onClick = 'settingsLoad(\"file\");'    >Активный файл (ссылка)</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_menu_massa'    onClick = 'settingsLoad(\"massa\");'   >Массовая загрузка</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_menu_priceSet'    onClick = 'settingsLoad(\"priceSet\");'   >Ценообразование</div>";
        }

        $TEXT .= "</div>";

        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsLoad"){      // Загрузка пункта меню настроек
        $param = clean($_POST["param"]);

        $TEXT = "";

        if($param == "providers"){       // Поставщики
            $TEXT = "<div id = 'settings_providers'><plus onClick = 'settingsProviderLoadAdd();'>+</plus><div class = 'settings_column_head'>Поставщики</div>";
            $sql = mysqli_query($CONNECTION, "SELECT * FROM provider WHERE status = 1 order by `name` asc");
            while($data = mysqli_fetch_array($sql)){
                $TEXT .= "<div class = 'list_item' id = 'settings_providers_".$data["id"]."' onClick = 'settingsProviderLoad(".$data["id"].");'><cross id = 'settings_providers_cross_".$data["id"]."' onClick = 'settingsProviderDelete(".$data["id"].");'></cross>".$data["name"]."</div>";
            }
            $TEXT .= "</div>";
        }
        if($param == "payers"){       // Плательщики
            $TEXT = "<div id = 'settings_payers'><plus onClick = 'settingsPayerLoadAdd();'>+</plus><div class = 'settings_column_head'>Плательщики</div>";
            $sql = mysqli_query($CONNECTION, "SELECT * FROM payer WHERE status = 1");
            while($data = mysqli_fetch_array($sql)){
                $TEXT .= "<div class = 'list_item' id = 'settings_payers_".$data["id"]."' onClick = 'settingsPayerLoad(".$data["id"].");'><cross id = 'settings_payers_cross_".$data["id"]."' onClick = 'settingsPayerDelete(".$data["id"].");'></cross>".$data["name"]."</div>";
            }
            $TEXT .= "</div>";
        }
        if($param == "workers"){         // Сотрудники
            $TEXT = "
                <div id = 'settings_workers'>
                    <div class = 'settings_column_head'>Роли</div>
                    <div class = 'list_item' id = 'settings_workers_1' onClick = 'settingsWorkersListLoad(1);'>Суперадмин</div>
                    <div class = 'list_item' id = 'settings_workers_2' onClick = 'settingsWorkersListLoad(2);'>Менеджер</div>
                    <div class = 'list_item' id = 'settings_workers_3' onClick = 'settingsWorkersListLoad(3);'>Кассир</div>
                    <div class = 'list_item' id = 'settings_workers_4' onClick = 'settingsWorkersListLoad(4);'>Кладовщик</div>
                </div>";
        }
        if($param == "couriers"){        // Курьеры
            $TEXT = "<div id = 'settings_couriers'><plus onClick = 'settingsCourierLoadAdd();'>+</plus><div class = 'settings_column_head'>Курьеры</div>";
            $sql = mysqli_query($CONNECTION, "SELECT * FROM user WHERE type = 5");
            while($data = mysqli_fetch_array($sql)){
                $TEXT .= "<div class = 'list_item' id = 'settings_couriers_".$data["id"]."' onClick = 'settingsCourierLoad(".$data["id"].");'><cross id = 'settings_couriers_cross_".$data["id"]."' onClick = 'settingsCourierDelete(".$data["id"].");'></cross>".$data["surname"]." ".$data["name"]."</div>";
            }
            $TEXT .= "</div>";
        }
        if($param == "bases"){        // Базы и хранилища
            $TEXT = "<div id = 'settings_bases'><plus onClick = 'windowBaseAdd();'>+</plus><div class = 'settings_column_head'>Базы</div>";
            $sql = mysqli_query($CONNECTION, "SELECT * FROM base");
            while($data = mysqli_fetch_array($sql)){
                $TEXT .= "<div class = 'list_item' id = 'settings_bases_".$data["id"]."' onClick = 'settingsBaseLoad(".$data["id"].");'><cross id = 'settings_bases_cross_".$data["id"]."' onClick = 'settingsBaseDelete(".$data["id"].");'></cross><circle style = 'background-color: #".$data["color"]."'></circle>".$data["name"]."</div>";
            }
            $TEXT .= "</div>";
        }
        if($param == "pass"){            // Смена пароля
            $TEXT = file_get_contents("../../templates/admin/temp/settings/pass.html");
        }
        if($param == "priceSet"){            // getPriceSet
            $query = "select * from `third_party_settings` where `name`='priceSet'";
            $row = mysqli_fetch_assoc(mysqli_query($CONNECTION, $query));
            $data = json_decode($row['setting'], true);
            $TEXT = file_get_contents("../../templates/admin/temp/settings/priceSet.html");
            $TEXT = str_replace("%grossMargin%", $data['gross'], $TEXT);
            $TEXT = str_replace("%retailMargin%", $data['retail'], $TEXT);
        }

        if($param == "products"){       // Параметры товаров
            $TEXT = "
                <div id = 'settings_products'>
                    <div class = 'list_item' id = 'settings_products_1' onClick = 'settingsProductsListLoad(1);'>Шины</div>
                    <div class = 'list_item' id = 'settings_products_2' onClick = 'settingsProductsListLoad(2);'>Диски</div>
                </div>";
        }
        if($param == "offs"){       // Основания списаний
            $TEXT = file_get_contents("../../templates/admin/temp/settings/offs.html");
            $STR = "";
            $sql = mysqli_query($CONNECTION, "SELECT id, value FROM product_param WHERE type = 8 AND status = 1");
            while($data = mysqli_fetch_array($sql)){
                $STR .= "<str><cross onClick = 'settingsOffsDelete(".$data["id"].");'></cross>".$data["value"]."</str>";
            }
            $TEXT = str_replace("%STR%", $STR, $TEXT);

            $STR_2 = "";
            $sql = mysqli_query($CONNECTION, "SELECT id, value FROM product_param WHERE type = 8 AND status = 2");
            while($data = mysqli_fetch_array($sql)){
                $STR_2 .= "<str><cross onClick = 'settingsOffsDelete(".$data["id"].");'></cross><plus2 onClick = 'settingsOffsAdd2(".$data["id"].");'></plus2>".$data["value"]."</str>";
            }
            $TEXT = str_replace("%STR_2%", $STR_2, $TEXT);
        }

        if($param == "delivery"){       // Транспортные компании
            $TEXT = "<div id = 'settings_delivery'><plus onClick = 'settingsDeliveryLoadAdd();'>+</plus><div class = 'settings_column_head'>ТК</div>";
            $sql = mysqli_query($CONNECTION, "SELECT * FROM delivery");
            while($data = mysqli_fetch_array($sql)){
                $TEXT .= "<div class = 'list_item' id = 'settings_delivery_".$data["id"]."' onClick = 'settingsDeliveryLoad(".$data["id"].");'><cross id = 'settings_delivery_cross_".$data["id"]."' onClick = 'settingsDeliveryDelete(".$data["id"].");'></cross>".$data["name"]."</div>";
            }
            $TEXT .= "</div>";
        }
        if($param == "season"){            // Сезонное хранение
            $TEXT = file_get_contents("../../templates/admin/temp/settings/season.html");

            if(file_exists("../../docs/season.docx")){
                $TEXT = str_replace("%FILE_NAME%", "season.docx&nbsp;&nbsp;&nbsp;<a href = '".$SERVER."docs/season.docx'>Скачать</a><br>", $TEXT);
                $TEXT = str_replace("%FILE_ACTION%", "Заменить...", $TEXT);
            }
            else {
                $TEXT = str_replace("%FILE_NAME%", "", $TEXT);
                $TEXT = str_replace("%FILE_ACTION%", "Обзор...", $TEXT);
            }

        }
        if($param == "naklad"){            // Товарная накладная
            $TEXT = file_get_contents("../../templates/admin/temp/settings/naklad.html");
            $TEXT = str_replace("%SERVER%", $SERVER, $TEXT);

            if(file_exists("../../docs/naklad.txt")){
                $TEXT = str_replace("%FILE_NAME%", "naklad.txt&nbsp;&nbsp;&nbsp;<a href = '".$SERVER."docs/naklad.txt'>Скачать</a><br>", $TEXT);
                $TEXT = str_replace("%FILE_ACTION%", "Заменить...", $TEXT);
                $TEMPLATE_TEXT = file_get_contents("../../docs/naklad.txt");
            }
            else {
                $TEXT = str_replace("%FILE_NAME%", "", $TEXT);
                $TEXT = str_replace("%FILE_ACTION%", "Обзор...", $TEXT);
                $TEMPLATE_TEXT = "";
            }
            $TEXT = str_replace("%TEMPLATE_TEXT%", nl2br($TEMPLATE_TEXT), $TEXT);

        }
        if($param == "file"){            // Активный файл (ссылка)
            $TEXT = file_get_contents("../../templates/admin/temp/settings/file.html");
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT value FROM settings WHERE id = 1"));
            $TEXT = str_replace("%VAL%", $data["value"], $TEXT);
            $TEXT = str_replace("%SERVER%", $SERVER, $TEXT);
        }
        if($param == "massa"){            // Массовая загрузка
            $TEXT = file_get_contents("../../templates/admin/temp/settings/massa.html");
        }

        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsProviderLoad"){      // Загрузка поставщика
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM provider WHERE id = '$id'"));
        $TEXT = file_get_contents("../../templates/admin/temp/settings/provider.html");
        $TEXT = str_replace("%ID%", $data["id"], $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%ADDRESS%", $data["address"], $TEXT);
        $TEXT = str_replace("%NOTE%", $data["note"], $TEXT);
        $TUMBLER = ($data["sklad"] == 1) ? "tumbler_active" : "tumbler_passive";
        $TEXT = str_replace("%TUMBLER%", $TUMBLER, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsProviderChange"){      // Изменение данных поставщика
        $id = clean($_POST["id"]);
        $name = clean($_POST["name"]);
        $sklad = clean($_POST["sklad"]);
        $address = clean($_POST["address"]);
        $note = clean($_POST["note"]);

        mysqli_query($CONNECTION, "UPDATE provider SET name = '$name', sklad = '$sklad', address = '$address', note = '$note' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsProviderAdd"){      // Добавление поставщика
        $name = clean($_POST["name"]);
        $sklad = clean($_POST["sklad"]);
        $address = clean($_POST["address"]);
        $note = clean($_POST["note"]);

        mysqli_query($CONNECTION, "INSERT INTO provider (name, sklad, address, note) VALUES ('$name', '$sklad', '$address', '$note')");
    }
    if($_POST["methodName"] == "settingsProviderDelete"){      // Удаление поставщика
        $id = clean($_POST["id"]);

        mysqli_query($CONNECTION, "UPDATE provider SET status = 0 WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsPayerLoad"){      // Загрузка плательщика
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM payer WHERE id = '$id'"));
        $TEXT = file_get_contents("../../templates/admin/temp/settings/payer.html");
        $TEXT = str_replace("%ID%", $data["id"], $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%INN%", $data["inn"], $TEXT);
        $TUMBLER = ($data["codes"] == 1) ? "tumbler_active" : "tumbler_passive";
        $TEXT = str_replace("%TUMBLER%", $TUMBLER, $TEXT);

        $TEXT .= "<br><br><br>".file_get_contents("../../templates/admin/temp/settings/naklad.html");
        $TEXT = str_replace("%SERVER%", $SERVER, $TEXT);

        if($data["rek"] != "" && file_exists("../../docs/".$data["rek"])){
            $TEXT = str_replace("%FILE_NAME%", "naklad.txt&nbsp;&nbsp;&nbsp;<a href = '".$SERVER."docs/".$data["rek"]."'>Скачать</a><br>", $TEXT);
            $TEXT = str_replace("%FILE_ACTION%", "Заменить...", $TEXT);
            $TEMPLATE_TEXT = file_get_contents("../../docs/".$data["rek"]);
        }
        else {
            $TEXT = str_replace("%FILE_NAME%", "", $TEXT);
            $TEXT = str_replace("%FILE_ACTION%", "Обзор...", $TEXT);
            $TEMPLATE_TEXT = "";
        }
        $TEXT = str_replace("%TEMPLATE_TEXT%", nl2br($TEMPLATE_TEXT), $TEXT);

        $TUMBLER_2 = ($data["priority"] == 1) ? "tumbler_active" : "tumbler_passive";
        $TEXT = str_replace("%TUMBLER_2%", $TUMBLER_2, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsPayerDelete"){      // Удаление плательщика
        $id = clean($_POST["id"]);
        mysqli_query($CONNECTION, "UPDATE payer SET status = 0 WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsPayerPriotiryChange"){      // Смена приоритетного плательщика
        $id = clean($_POST["id"]);
        $param = clean($_POST["param"]);
        mysqli_query($CONNECTION, "UPDATE payer SET priority = 0");
        if($param == 1) mysqli_query($CONNECTION, "UPDATE payer SET priority = 1 WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsPayerChange"){      // Изменение данных плательщика
        $id = clean($_POST["id"]);
        $name = clean($_POST["name"]);
        $code = clean($_POST["code"]);
        $inn = clean($_POST["inn"]);

        mysqli_query($CONNECTION, "UPDATE payer SET name = '$name', codes = '$code', inn = '$inn' WHERE id = '$id'");
        echo mysqli_error($CONNECTION);
    }
    if($_POST["methodName"] == "settingsPayerAdd"){      // Добавление плательщика
        $name = clean($_POST["name"]);
        $code = clean($_POST["code"]);
        $inn = clean($_POST["inn"]);

        mysqli_query($CONNECTION, "INSERT INTO payer (name, codes, inn) VALUES ('$name', '$code', '$inn')");
    }
    if($_POST["methodName"] == "settingsPayerRekChange"){      // Изменение файла реквизитов плательщика
        $id = clean($_POST["id"]);
        $file = clean($_POST["file"]);

        mysqli_query($CONNECTION, "UPDATE payer SET rek = '$file' WHERE id = '$id'");
        echo mysqli_error($CONNECTION);
    }


    if($_POST["methodName"] == "settingsWorkersListLoad"){      // Загрузка списка пользователей данного типа
        $type = clean($_POST["type"]);
        $TEXT = "<div id = 'settings_workers_list'><plus onClick = 'settingsWorkerLoadAdd(".$type.");'>+</plus>";
        switch($type){
            case  1: $TEXT .= "<div class = 'settings_column_head'>Суперадмины</div>"; break;
            case  2: $TEXT .= "<div class = 'settings_column_head'>Менеджеры</div>"; break;
            case  3: $TEXT .= "<div class = 'settings_column_head'>Кассиры</div>"; break;
            case  4: $TEXT .= "<div class = 'settings_column_head'>Кладовщики</div>"; break;
            default: $TEXT .= "<div class = 'settings_column_head'>Суперадмины</div>";
        }

        $sql = mysqli_query($CONNECTION, "SELECT id, name, surname FROM user WHERE type = '$type' AND id > 1");
        while($data = mysqli_fetch_array($sql)){
            if($data["id"] == 2) $TEXT .= "<div class = 'list_item' id = 'settings_workers_list_".$data["id"]."' onClick = 'settingsWorkerLoad(".$data["id"].");'>".$data["surname"]." ".$data["name"]."</div>";
            else $TEXT .= "<div class = 'list_item' id = 'settings_workers_list_".$data["id"]."' onClick = 'settingsWorkerLoad(".$data["id"].");'><cross id = 'settings_workers_list_cross_".$data["id"]."' onClick = 'settingsWorkerDelete(".$data["id"].");'></cross>".$data["surname"]." ".$data["name"]."</div>";
        }
        $TEXT .= "</div>";
        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsWorkerLoad"){      // Загрузка пользователя
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM user WHERE id = '$id'"));
        $TYPE = $data["type"];
        $TEXT = file_get_contents("../../templates/admin/temp/settings/worker_global.html");
        $TEXT = str_replace("%ID%", $id, $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%SURNAME%", $data["surname"], $TEXT);
        $TEXT = str_replace("%PHONE%", $data["phone"], $TEXT);
        $TEXT = str_replace("%MAIL%", $data["mail"], $TEXT);

        if($TYPE == 2){
            $TEXT .= file_get_contents("../../templates/admin/temp/settings/worker_manager.html");
            $koef = str_replace(".", ",", $data["koef"]);
            $koef0 = $data["koef"];
            $TEXT = str_replace("%KOEF%", $koef, $TEXT);
            $TEXT = str_replace("%DAY_A%", $data["day_a"], $TEXT);
            $TEXT = str_replace("%DAY_Z%", $data["day_z"], $TEXT);
            $day_z2 = $data["day_z"] + 1;
            $today = date("d");
            $month = date("m");
            if($today <= $day_z2) $month--;
            if($month == 0) $month = 12;

            if(!checkdate($month, $day_z2, date("Y"))){
                $month++;
                $day_z2 = 1;
                if($month == 13) $month = 1;
            }

            $TEXT_ZP = "С ".$day_z2." ";
            $first_date = strtotime($day_z2."-".$month."-".date("Y"));
            switch($month){
                case  1: $TEXT_ZP .= "января"; break;
                case  2: $TEXT_ZP .= "февраля"; break;
                case  3: $TEXT_ZP .= "марта"; break;
                case  4: $TEXT_ZP .= "апреля"; break;
                case  5: $TEXT_ZP .= "мая"; break;
                case  6: $TEXT_ZP .= "июня"; break;
                case  7: $TEXT_ZP .= "июля"; break;
                case  8: $TEXT_ZP .= "августа"; break;
                case  9: $TEXT_ZP .= "сентября"; break;
                case 10: $TEXT_ZP .= "октября"; break;
                case 11: $TEXT_ZP .= "ноября"; break;
                case 12: $TEXT_ZP .= "декабря"; break;
            }
            $TEXT_ZP .= " начислено";
            $TEXT = str_replace("%TEXT_ZP%", $TEXT_ZP, $TEXT);

            // Здесь происходит расчет зарплаты
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(price_sale) FROM sale WHERE manager = '$id' AND status = 7 AND date > $first_date AND date <=".time()));
            $price = $temp[0]*$koef0/100;
            $COUNT_ZP = $price." ₽";
            $TEXT = str_replace("%COUNT_ZP%", $COUNT_ZP, $TEXT);
        }

        if($TYPE > 1){
            $TEXT .= "
                <div class = 's_worker_item3'>
                    <tname>База приписки</tname>
                    <div class = 'select' style = 'width: 195px;' id = 'base'>
                        <arrow></arrow>
            ";
            if($data["base"] == 0) $TEXT .= "<headline>Выбрать</headline><input type = 'hidden' id = 'base_hidden' value = '0' />";
            else{
                $base = $data["base"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color, name FROM base WHERE id = '$base'"));
                $TEXT .= "<headline><bcircle style = 'background: #".$temp["color"].";'></bcircle>".$temp["name"]."</headline><input type = 'hidden' id = 'base_hidden' value = '".$base."' />";
            }
            $sql = mysqli_query($CONNECTION, "SELECT id, name, color FROM base");
            while($temp = mysqli_fetch_array($sql)){
                $TEXT .= "<div data = '".$temp["id"]."'><bcircle style = 'background: #".$temp["color"].";'></bcircle>".$temp["name"]."</div>";
            }
            $TEXT .= "</div></div><br><br><br>";
        }

        if($TYPE == 2 || $TYPE == 4){
            $TEXT .= file_get_contents("../../templates/admin/temp/settings/worker_root.html");
            $temp = $data["root"];
            $COLUMN_1 = "";
            $COLUMN_2 = "";

            $COLUMN_1 .= tumbler(10, $temp[10], "Касса");
            $COLUMN_1 .= tumbler(0, $temp[0], "Продажи");
            $COLUMN_1 .= tumbler(1, $temp[1], "Шины");
            $COLUMN_1 .= tumbler(2, $temp[2], "Диски");
            $COLUMN_1 .= tumbler(3, $temp[3], "Товары");
            $COLUMN_1 .= tumbler(4, $temp[4], "Услуги");

            $COLUMN_2 .= tumbler(5, $temp[5], "Склады");
            $COLUMN_2 .= tumbler(6, $temp[6], "Движения");
            $COLUMN_2 .= tumbler(7, $temp[7], "Клиенты");
            $COLUMN_2 .= tumbler(8, $temp[8], "Поставщики");
            $COLUMN_2 .= tumbler(9, $temp[9], "Курьеры");

            $TEXT = str_replace("%COLUMN_1%", $COLUMN_1, $TEXT);
            $TEXT = str_replace("%COLUMN_2%", $COLUMN_2, $TEXT);

        }

        $TEXT .= "<div class = 'button_green inline button_small' onClick = 'settingsWorkerChange(".$id.");buttonClick(this);'>Сохранить</div>";

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(id) FROM cash WHERE user = '$id'"));
        if($data[0] > 0){
            $TEXT .= "<br><br><br><br><div class = 'settings_column_head'>Отчеты по кассам</div>";
            $sql = mysqli_query($CONNECTION, "SELECT * FROM cash WHERE user = '$id' AND status = 1");
            while($data = mysqli_fetch_array($sql)){
                $base = $data["base"];
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM base WHERE id = '$base'"));
                $TEXT .= "
                    <div class = 'cash_report'>
                        <bcircle style = 'background: #".$temp["color"].";'></bcircle>".$temp["code"]."
                        <span>".date("d.m.Y H:i", $data["time_start"])." - ".date("d.m.Y H:i", $data["time_end"])."</span>
                        <links onClick = 'settingsCashReportDownload(".$data["id"].");'>Скачать</links>
                    </div>";
            }
        }

        echo $TYPE.$SEP.$TEXT;
    }
    if($_POST["methodName"] == "settingsWorkerChange"){      // Сохранение данных пользователя
        $id = clean($_POST["id"]);
        $name = clean($_POST["name"]);
        $surname = clean($_POST["surname"]);
        $phone = clean($_POST["phone"]);
        $mail = clean($_POST["mail"]);
        $koef = clean($_POST["koef"]);
        $day_a = clean($_POST["day_a"]);
        $day_z = clean($_POST["day_z"]);
        $base = clean($_POST["base"]);
        $root = clean($_POST["root"]);

        $koef = str_replace(",", ".", $koef);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM user WHERE mail = '$mail' AND id <> '$id'"));
        if($data[0] > 0) echo -1;
        else{
            mysqli_query($CONNECTION, "
                UPDATE user SET
                    name = '$name',
                    surname = '$surname',
                    phone = '$phone',
                    mail = '$mail',
                    root = '$root',
                    koef = '$koef',
                    day_a = '$day_a',
                    day_z = '$day_z',
                    base = '$base'
                WHERE id = '$id' ");
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT type FROM user WHERE id = '$id'"));
            echo $data["type"];
        }

    }
    if($_POST["methodName"] == "settingsWorkerNewPass"){      // Генерация нового пароля для пользователя
        $id = clean($_POST["id"]);

        $pass = generate_16(10);
        $pass_2 = md5($pass.$SALT);

        mysqli_query($CONNECTION, "UPDATE user SET pass = '$pass_2', first = 1 WHERE id = '$id'");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT mail FROM user WHERE id = '$id'"));
        $TEXT = "
            Ваш новый пароль: ".$pass."<br><br>
            Ссылка для входа в систему: <a href = '".$SERVER."login'>".$SERVER."login</a>
        ";
        send_mail($data["mail"], "Смена пароля", $TEXT);
    }
    if($_POST["methodName"] == "settingsWorkerAdd"){      // Добавление нового пользователя
        $name = clean($_POST["name"]);
        $surname = clean($_POST["surname"]);
        $phone = clean($_POST["phone"]);
        $mail = clean($_POST["mail"]);
        $type = clean($_POST["type"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM user WHERE mail = '$mail'"));
        if($data["id"] > 0) echo -1;
        else {
            $pass = generate_16(10);
            $pass_2 = md5($pass.$SALT);

            mysqli_query($CONNECTION, "INSERT INTO user (name, surname, mail, pass, phone, type, root) VALUES ('$name', '$surname', '$mail', '$pass_2', '$phone', '$type', '11111111111')");
            $id = mysqli_insert_id($CONNECTION);
            mysqli_query($CONNECTION, "INSERT INTO user_root
                (uId, tire_root,               disk_root,        product_root, service_root, movement_root, sale_root, transaction_root) VALUES
                ('$id', '1111111111111111111', '11111111111111', '111111111', '11111', '1111111111111', '1111111111111111111', '11111111')");
            $TEXT = "
                Вы были зарегистрированы на сате ".$SERVER.":<br><br>
                Ваш логин: ".$mail."<br>
                Ваш пароль: ".$pass."<br><br>
                Ссылка для входа в систему: <a href = '".$SERVER."login'>".$SERVER."login</a>
            ";
            send_mail($mail, "Регистрация", $TEXT);
            echo mysqli_insert_id($CONNECTION);
        }

    }
    if($_POST["methodName"] == "settingsWorkerDelete"){      // Удаление пользователя
        $id = clean($_POST["id"]);

        mysqli_query($CONNECTION, "DELETE FROM user WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsCourierLoad"){      // Загрузка курьера
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM user WHERE id = '$id'"));
        $TEXT = file_get_contents("../../templates/admin/temp/settings/courier.html");
        $TEXT = str_replace("%ID%", $data["id"], $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%SURNAME%", $data["surname"], $TEXT);
        $TEXT = str_replace("%MAIL%", $data["mail"], $TEXT);
        $TEXT = str_replace("%PHONE%", $data["phone"], $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsCourierChange"){      // Изменение данных курьера
        $id = clean($_POST["id"]);
        $name = clean($_POST["name"]);
        $surname = clean($_POST["surname"]);
        $mail = clean($_POST["mail"]);
        $phone = clean($_POST["phone"]);

        mysqli_query($CONNECTION, "UPDATE user SET name = '$name', surname = '$surname', mail = '$mail', phone = '$phone' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsCourierAdd"){      // Добавление курьера
        $name = clean($_POST["name"]);
        $surname = clean($_POST["surname"]);
        $mail = clean($_POST["mail"]);
        $phone = clean($_POST["phone"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM user WHERE mail = '$mail'"));
        if($data["id"] > 0) echo -1;
        else {
            $pass = generate_16(10);
            $pass_2 = md5($pass.$SALT);

            mysqli_query($CONNECTION, "INSERT INTO user (name, surname, mail, pass, phone, type) VALUES ('$name', '$surname', '$mail', '$pass_2', '$phone', '5')");

            $TEXT = "
                Вы были зарегистрированы на сате ".$SERVER.":<br><br>
                Ваш логин: ".$mail."<br>
                Ваш пароль: ".$pass."<br><br>
                Ссылка для входа в систему: <a href = '".$SERVER."login'>".$SERVER."login</a>
            ";
            send_mail($mail, "Регистрация", $TEXT);
            echo mysqli_insert_id($CONNECTION);
        }
    }
    if($_POST["methodName"] == "settingsCourierDelete"){      // Удаление курьера
        $id = clean($_POST["id"]);

        mysqli_query($CONNECTION, "DELETE FROM user WHERE id = '$id'");
    }



    if($_POST["methodName"] == "settingsPassChange"){      // Изменение пароля
        $pass_old = clean($_POST["pass_old"]);
        $pass_new = clean($_POST["pass_new"]);

        $pass_2 = $_COOKIE["pass"];
        $pass_old = md5($pass_old.$SALT);
        if($pass_old == $pass_2){
            $pass_new = md5($pass_new.$SALT);
            mysqli_query($CONNECTION, "UPDATE user SET pass = '$pass_new' WHERE id = ".ID);
            setcookie("pass", $pass_new, time() + 6048000, "/");
            echo 1;
        }
        else echo -1;
    }
    if($_POST["methodName"] == "settingsPriceSet"){
        $setStr = $_POST['settingStr'];
        $query = "update `third_party_settings` set `setting`='$setStr' where `name`='priceSet'";
        if(mysqli_query($CONNECTION, $query)){
            echo 'Значения успешно установлены';
        }else{
            echo 'Ошибка при внесении изменений в базу данных: '.mysqli_error();
        }
    }
    if($_POST["methodName"] == "settingsProductsListLoad"){    // Загрузка списка параметров данного типа товаров
        $type = clean($_POST["type"]);
        $TEXT = "<div id = 'settings_product_list'>";
        if($type == 1){
            $TEXT .= "<div class = 'list_item' id = 'settings_product_list_1' onClick = 'settingsProductLoad(1);'>Ширина</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_product_list_2' onClick = 'settingsProductLoad(2);'>Высота</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_product_list_3' onClick = 'settingsProductLoad(3);'>Радиус</div>";
        }
        if($type == 2){
            $TEXT .= "<div class = 'list_item' id = 'settings_product_list_4' onClick = 'settingsProductLoad(4);'>Цвет</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_product_list_5' onClick = 'settingsProductLoad(5);'>Ширина</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_product_list_6' onClick = 'settingsProductLoad(6);'>Радиус</div>";
            $TEXT .= "<div class = 'list_item' id = 'settings_product_list_7' onClick = 'settingsProductLoad(7);'>Межболт</div>";
        }
        $TEXT .= "</div>";

        echo $TEXT;

    }
    if($_POST["methodName"] == "settingsProductLoad"){      // Загрузка значений данного параметра
        $id = clean($_POST["id"]);

        $TEXT = "
            <div id = 'settings_params_list'>
                <input id = 'product_param' type = 'text' class = 'input height-23' style = 'width: 61px; margin-bottom: 10px;' />
                <div onClick = 'settingsProductParamAdd(".$id.");' class = 'button_green button_extra_small inline' style = 'margin-left: 10px;'>Добавить</div>";

        $sql = mysqli_query($CONNECTION, "SELECT * FROM product_param WHERE type = '$id'");
        while($data = mysqli_fetch_array($sql)){
            $TEXT .= "<div class = 'list_item' id = 'settings_params_list_".$data["id"]."' onClick = 'settingsProductParamActive(".$data["id"].");'><cross id = 'settings_params_list_cross_".$data["id"]."' onClick = 'settingsProductParamDelete(".$data["id"].");'></cross>".$data["value"]."</div>";
        }

        $TEXT .= "</div>";

        if($id < 4) $type = 1; else $type = 2;

        echo $type.$SEP.$TEXT;

    }
    if($_POST["methodName"] == "settingsProductParamDelete"){      // Удаление значения параметра
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT type FROM product_param WHERE id = '$id'"));
        $type = $data["type"];

        mysqli_query($CONNECTION, "DELETE FROM product_param WHERE id = '$id'");

        echo $type;
    }
    if($_POST["methodName"] == "settingsProductParamAdd"){       // Добавление нового значения выбранного параметра
        $id = clean($_POST["id"]);
        $val = clean($_POST["val"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM product_param WHERE type = '$id' AND value = '$val'"));
        if($data[0] == 0){
            mysqli_query($CONNECTION, "INSERT INTO product_param (value, type) VALUES ('$val', '$id')");
            echo 1;
        }
        else echo 0;
    }

    if($_POST["methodName"] == "settingsDeliveryLoad"){      // Загрузка транспортной компании
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM delivery WHERE id = '$id'"));
        $TEXT = file_get_contents("../../templates/admin/temp/settings/delivery.html");
        $TEXT = str_replace("%ID%", $data["id"], $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%ADDRESS%", $data["address"], $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsDeliveryChange"){      // Изменение данных транспортной компании
        $id = clean($_POST["id"]);
        $name = clean($_POST["name"]);
        $address = clean($_POST["address"]);

        mysqli_query($CONNECTION, "UPDATE delivery SET name = '$name', address = '$address' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsDeliveryAdd"){      // Добавление транспортной компании
        $name = clean($_POST["name"]);
        $address = clean($_POST["address"]);

        mysqli_query($CONNECTION, "INSERT INTO delivery (name, address) VALUES ('$name', '$address')");
    }
    if($_POST["methodName"] == "settingsDeliveryDelete"){      // Удаление транспортной компании
        $id = clean($_POST["id"]);

        mysqli_query($CONNECTION, "DELETE FROM delivery WHERE id = '$id'");
    }

    if($_POST["methodName"] == "settingsFileValChange"){      // Изменение верхней границы файла
        $val = clean($_POST["val"]);

        mysqli_query($CONNECTION, "UPDATE settings SET value = '$val' WHERE id = 1");
    }
    if($_POST["methodName"] == "settingsOffsAdd"){      // Добавление нового основания для списания
        $val = clean($_POST["val"]);

        mysqli_query($CONNECTION, "INSERT INTO product_param (value, type, status) VALUES ('$val', '8', '1')");
    }
    if($_POST["methodName"] == "settingsOffsAdd2"){      // Добавление другого основания списания в основные
        $id = clean($_POST["id"]);

        mysqli_query($CONNECTION, "UPDATE product_param SET status = 1 WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsOffsDelete"){      // Удаление основания списания
        $id = clean($_POST["id"]);

        mysqli_query($CONNECTION, "DELETE FROM product_param WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsBaseAdd"){      // Добавление новой базы
        $name = clean($_POST["name"]);
        $color = clean($_POST["color"]);
        $code = clean($_POST["code"]);
        $address = clean($_POST["address"]);
        $vydacha = clean($_POST["vydacha"]);
        $time_1 = clean($_POST["time_1"]);
        $time_2 = clean($_POST["time_2"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE name = '$name'"));
        if($data["id"] > 0) echo -1;
        else{
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$code'"));
            if($data["id"] > 0) echo -2;
            else{
                mysqli_query($CONNECTION, "
                    INSERT INTO
                        base
                    (name, color, code, address, vydacha, time_1, time_2)
                        VALUES
                    ('$name', '$color', '$code', '$address', '$vydacha', '$time_1', '$time_2')");
                $id = mysqli_insert_id($CONNECTION);
                mysqli_query($CONNECTION, "INSERT INTO storage (base, name, code, count) VALUES ('$id', 'На продажу', 'SC".$id."', 1000000)");
                echo 1;
            }
        }
    }
    if($_POST["methodName"] == "settingsBaseLoad"){      // Загрузка данных по базе
        $id = clean($_POST["id"]);
        $TEXT = file_get_contents("../../templates/admin/temp/settings/base_load.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM base WHERE id = '$id'"));
        $TEXT = str_replace("%CODE%", $data["code"], $TEXT);
        $TEXT = str_replace("%COLOR%", $data["color"], $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%ADDRESS%", $data["address"], $TEXT);
        if($data["vydacha"] == 1) $VYDACHA = "<div id = 'base_vydacha'><gal></gal>Пункт выдачи <span>".$data["time_1"]." — ".$data["time_2"]."</span></div>";
        else $VYDACHA = "";
        $TEXT = str_replace("%VYDACHA%", $VYDACHA, $TEXT);
        $TEXT = str_replace("%ID%", $id, $TEXT);

        $STORAGE = "";
        $sql = mysqli_query($CONNECTION, "SELECT * FROM storage WHERE base = '$id' AND name != 'На продажу' AND mother = 0");
        while($data = mysqli_fetch_array($sql)){
            if($data["composite"] == 0){
                $STORAGE .= "
                    <div class = 'base_storage_str'>
                        <cod>".$data["code"]."</cod>
                        <count>".$data["count"]." мест</count>
                        <name class = 'text_overflow'>".$data["name"]."</name>
                        ".getPercentLine($data["occupied"], $data["count"])."
                        <pen onClick = 'windowStorageRedact(".$data["id"].");'></pen>
                        <blue_cross onClick = 'settingsBaseStorageDel(".$data["id"].");'></blue_cross>
                    </div>
                ";
            }
            else {
                $STORAGE .= "
                    <div class = 'base_storage_composite'>
                        <div class = 'base_storage_composite_head'>
                            <cod>".$data["code"]."</cod>
                            <count>".$data["count"]." мест</count>
                            <name class = 'text_overflow'>".$data["name"]."</name>
                            ".getPercentLine($data["occupied"], $data["count"])."
                            <pen onClick = 'windowStorageRedact(".$data["id"].");'></pen>
                            <blue_cross onClick = 'settingsBaseStorageDel(".$data["id"].");'></blue_cross>
                        </div>
                    ";
                $mother = $data["id"];
                $sql_2 = mysqli_query($CONNECTION, "SELECT * FROM storage WHERE mother = '$mother'");
                while($data_2 = mysqli_fetch_array($sql_2)){
                    $STORAGE .= "
                        <div class = 'base_storage_composite_str'>
                            <cod>".$data_2["code"]."</cod>
                            <count>".$data_2["count"]." мест</count>
                            <name class = 'text_overflow'>".$data_2["name"]."</name>
                            <perc>".$data_2["occupied"]."</perc>
                            <pen onClick = 'windowStorageRedact(".$data_2["id"].");'></pen>
                            <blue_cross onClick = 'settingsBaseStorageDel(".$data_2["id"].");'></blue_cross>
                        </div>
                    ";
                }
                $STORAGE .= "<div class = 'base_storage_add' onClick = 'windowBaseStorageAdd2(".$id.", ".$data["id"].");'>добавить</div></div>";
            }
        }
        $TEXT = str_replace("%STORAGE%", $STORAGE, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsBaseStorageDel"){      // Удаление хранилища
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT occupied, mother FROM storage WHERE id = '$id'"));
        $mother = $data["mother"];
        if($data["occupied"] > 0) echo -1;
        else{
            mysqli_query($CONNECTION, "DELETE FROM storage WHERE id = '$id'");
            mysqli_query($CONNECTION, "DELETE FROM storage WHERE mother = '$id'");
            if($mother > 0) storageCalc($CONNECTION, $mother);
            echo 1;
        }
    }
    if($_POST["methodName"] == "settingsBaseDel"){      // Удаление базы
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM storage WHERE base = '$id'"));
        if($data[0] > 1) echo -1;
        else{
            mysqli_query($CONNECTION, "DELETE FROM base WHERE id = '$id'");
            echo 1;
        }
    }
    if($_POST["methodName"] == "settingsBaseStorageAddLoad"){      // Загрузка данных для добавления хранилища
        $id = clean($_POST["id"]);

        $TEXT = file_get_contents("../../templates/admin/temp/settings/storage_add.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM base WHERE id = '$id'"));
        $BASE = "<circle style = 'background-color: #".$data["color"]."'></circle> ".$data["name"];
        $TEXT = str_replace("%BASE%", $BASE, $TEXT);
        $TEXT = str_replace("%TUMBLER%", tumbler("1", 0, null, "settingsBaseStorageAddVis(this)"), $TEXT);
        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsBaseStorageAdd"){      // Добавление хранилища в базу
        $id = clean($_POST["id"]);
        $name = clean($_POST["name"]);
        $composite = clean($_POST["composite"]);
        $code = clean($_POST["code"]);
        $count = clean($_POST["count"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$code'"));
        if($data["id"] > 0) echo -1;
        else{
            mysqli_query($CONNECTION, "
                INSERT INTO storage
                    (base, composite, name, code, count)
                VALUES
                    ('$id', '$composite', '$name', '$code', '$count')");
            echo 1;
        }

    }
    if($_POST["methodName"] == "settingsBaseStorageAddLoad2"){      // Загрузка данных для добавления хранилища в хранилище
        $id = clean($_POST["id"]);
        $id_2 = clean($_POST["id_2"]);

        $TEXT = file_get_contents("../../templates/admin/temp/settings/storage_add2.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM base WHERE id = '$id'"));
        $BASE = "<circle style = 'background-color: #".$data["color"]."'></circle> ".$data["name"];
        $TEXT = str_replace("%BASE%", $BASE, $TEXT);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM storage WHERE id = '$id_2'"));
        $TEXT = str_replace("%STORAGE%", $data["code"]." ".$data["name"], $TEXT);
        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsBaseStorageAdd2"){      // Добавление хранилища в хранилище
        $base = clean($_POST["base"]);
        $mother = clean($_POST["mother"]);
        $name = clean($_POST["name"]);
        $composite = clean($_POST["composite"]);
        $code = clean($_POST["code"]);
        $count = clean($_POST["count"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM storage WHERE code = '$code'"));
        if($data["id"] > 0) echo -1;
        else{
            mysqli_query($CONNECTION, "
                INSERT INTO storage
                    (base, mother, composite, name, code, count)
                VALUES
                    ('$base', '$mother', '$composite', '$name', '$code', '$count')");
            storageCalc($CONNECTION, $mother);
            echo 1;
        }
    }
    if($_POST["methodName"] == "settingsBaseRedactLoad"){      // Получение данных для редактирования базы
        $id = clean($_POST["id"]);
        $TEXT = file_get_contents("../../templates/admin/temp/settings/base_redact.html");
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM base WHERE id = '$id'"));
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%CODE%", $data["code"], $TEXT);
        $TEXT = str_replace("%COLOR%", $data["color"], $TEXT);
        $TEXT = str_replace("%ADDRESS%", $data["address"], $TEXT);
        $TEXT = str_replace("%TIME_1%", $data["time_1"], $TEXT);
        $TEXT = str_replace("%TIME_2%", $data["time_2"], $TEXT);
        if($data["vydacha"] == 1) $vydacha = "tumbler_active"; else $vydacha = "tumbler_passive";
        $TEXT = str_replace("%VYDACHA%", $vydacha, $TEXT);
        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsBaseRedact"){      // Редактирование базы
        $name = clean($_POST["name"]);
        $color = clean($_POST["color"]);
        $code = clean($_POST["code"]);
        $address = clean($_POST["address"]);
        $vydacha = clean($_POST["vydacha"]);
        $time_1 = clean($_POST["time_1"]);
        $time_2 = clean($_POST["time_2"]);
        $id = clean($_POST["id"]);
        mysqli_query($CONNECTION, "
            UPDATE base SET
                name = '$name',
                color = '$color',
                code = '$code',
                address = '$address',
                vydacha = '$vydacha',
                time_1 = '$time_1',
                time_2 = '$time_2'
            WHERE id = '$id'");
    }
    if($_POST["methodName"] == "settingsBaseStorageRedactLoad"){      // Получение данных для редактирования хранилища
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM storage WHERE id = '$id'"));
        if($data["composite"] == 1) $TEXT = file_get_contents("../../templates/admin/temp/settings/storage_redact.html");
        else $TEXT = file_get_contents("../../templates/admin/temp/settings/storage_redact2.html");
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%CODE%", $data["code"], $TEXT);
        $TEXT = str_replace("%COUNT%", $data["count"], $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "settingsBaseStorageRedact"){      // Редактирование хранилища
        $name = clean($_POST["name"]);
        $count = clean($_POST["count"]);
        $code = clean($_POST["code"]);
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM storage WHERE code = '$code' AND id != '$id'"));
        if($data[0] > 0) echo -2;
        else{
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM storage WHERE id = '$id'"));
            if($data["composite"] == 1){
                mysqli_query($CONNECTION, "UPDATE storage SET name = '$name', code = '$code' WHERE id = '$id'");
                echo 1;
            }
            else {
                if($count < $data["occupied"]) echo -1;
                else {
                    mysqli_query($CONNECTION, "UPDATE storage SET name = '$name', code = '$code', count = '$count' WHERE id = '$id'");
                    if($data["mother"] != 0) storageCalc($CONNECTION, $data["mother"]);
                    echo 1;
                }
            }
        }

    }
    if($_POST["methodName"] == "getDiscount"){
        $query = "select * from `third_party_settings` where `name`='priceSet'";
        $row = mysqli_fetch_assoc(mysqli_query($CONNECTION, $query));
        $priceSet = json_decode($row['setting'], true);
		$discount = Round((1 - Round((100 + $priceSet['gross'])/(100 + $priceSet['retail']), 2, PHP_ROUND_HALF_UP))*100, 0, 1);
        echo json_encode(['discount'=>$discount], 64|256);
    }
    if($_POST["methodName"] == "getPriceSet"){
        $query = "select * from `third_party_settings` where `name`='priceSet'";
        $row = mysqli_fetch_assoc(mysqli_query($CONNECTION, $query));
        echo $row['setting'];
    }
    if($_POST["methodName"] == "calculateGRPrices"){
        $buyout = str_replace(' ', '', $_POST["buyout"]);
        // var_dump($buyout);
        $query = "select * from `third_party_settings` where `name`='priceSet'";
        $row = mysqli_fetch_assoc(mysqli_query($CONNECTION, $query));
        $priceSet = json_decode($row['setting'], true);
        $minGross = ceil(Round($buyout * (1 + 0.01*$priceSet['gross']), 0)/100) * 100;
        $minRetail = ceil(Round($buyout * (1 + 0.01*$priceSet['retail']), 0)/100) * 100;

        echo json_encode(['minGross'=>$minGross, 'minRetail'=>$minRetail], 64|256);

    }

?>