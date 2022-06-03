<?php


    $SCRIPT = "SERVER = '".$SERVER."';";        // Назначает глобальную переменную сервера JS

    $SCRIPT .= "SEP = '".$SEP."';";      // Назначает глобальную переменную сервера разделителя

    {    // Разбивает адрес
        $catA = "";
        $catB = "";
        $catC = "";
        $catD = "";
        $catE = "";

        if ($_SERVER['REQUEST_URI'] != '/') {
            $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        	$uri_parts = explode('/', trim($url_path, ' /'));
        	$catA = array_shift($uri_parts);
            $catB = array_shift($uri_parts);
            $catC = array_shift($uri_parts);
            $catD = array_shift($uri_parts);
            $catE = array_shift($uri_parts);
        }

        $SCRIPT .= "catA = '".$catA."';";
        $SCRIPT .= "catB = '".$catB."';";
        $SCRIPT .= "catC = '".$catC."';";
        $SCRIPT .= "catD = '".$catD."';";
        $SCRIPT .= "catE = '".$catE."';";
    }
    {     // Соединяет с БД
        $CONNECTION = mysqli_connect($DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME);
        if(mysqli_connect_errno()) echo "Не удалось подключиться к MySQL: " . mysqli_connect_error();

        mysqli_query($CONNECTION, "SET NAMES utf8");
    }
    function clean($value){      // Очищает входящие данные
        $value = trim($value);
        $value = stripslashes($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value);

        return $value;
    }
    function send_mail($to, $subject, $text){        // Оправляет почту
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: Робот <robot@spb.loc>\r\n";

        mail($to, $subject, $text, $headers);
    }
    if(isset($_COOKIE["id"]) AND isset($_COOKIE["pass"])){   // Проверка входа на сайт
        $id = clean($_COOKIE["id"]);
        $pass = clean($_COOKIE["pass"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT type, pass, root, base FROM user WHERE id = '$id'"));
        if($data["pass"] == $pass){
            define("TYPE", $data["type"]);
            define("ID", $id);
            define("BASE", $data["base"]);
            $root = $data["root"];
            if($root == "") $root = "0000000000";
            $SCRIPT .= "USER_TYPE = ".TYPE.";";
        }
        else {
            setcookie("id","");
            setcookie("pass","");
        }
    }
    function proof(){
        if(!isset($_COOKIE["id"])) exit();
    }
    function generate_16($count){           // Генерация 16-ричного кода заданной длины
        $arr = array('a','b','c','d','e','f',
            '1','2','3','4','5','6',
            '7','8','9','0');
        $pass = "";
        for($i = 0; $i < $count; $i++){
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }
    function tumbler($id, $param, $name = null, $dop = null){    // Возвращает тумблер с указанным ID, значением и именем что это за тумблер, dop - доп. функция
        if($param == 0) $temp = "tumbler_passive"; else $temp = "tumbler_active";
        $text = "";
        if($name !== null) $text .= "<div class = 'tumbler_base'>";
        if($dop == null) $dop = "";
        $text .= "<div id = 'tumbler_".$id."' class = 'tumbler ".$temp."' onclick = 'tumblerChange(\"".$id."\"); ".$dop.";'>
                <div class = 'tumbler_left'></div>
                <div class = 'tumbler_right'></div>
                <div class = 'tumbler_circle'></div>
            </div>";
        if($name !== null) $text .= "<span>".$name."</span></div>";
        return $text;
    }
    function checkbox($id, $param, $name, $dop = null){   // Возвращает чекбокс с указанным ID, значением и именем
        if($dop == null) $dop = ""; else $dop = "onChange = '".$dop.";'";
        if($param == 1) $param = "checked"; else $param = "";
        $TEXT = "
            <checkbox>
                <input id = 'checkbox_".$id."' type = 'checkbox' ".$dop." ".$param." />
                <label for = 'checkbox_".$id."'>".$name."</label>
            </checkbox>
        ";
        return $TEXT;
    }
    function radio($id, $param, $title, $name, $dop = null){        // Возвращает radio
        if($dop == null) $dop = ""; else $dop = "onChange = '".$dop.";'";
        if($param == 1) $param = "checked"; else $param = "";
        $TEXT = "
            <radio>
                <input name = '".$name."' id = 'radioimg_".$id."' type = 'radio' ".$param." ".$dop." />
                <label for = 'radioimg_".$id."'></label>
                <title>".$title."</title>
            </radio>
        ";
        return $TEXT;
    }
    function radioImg($id, $param, $img, $title, $name, $SERVER){         // Возвращает radio с картинкой и надписью
        if($param == 1) $param = "checked"; else $param = "";
        $TEXT = "
            <radioimg>
                <img src = '".$SERVER.$img."'/>
                <input name = '".$name."' id = 'radioimg_".$id."' type = 'radio' ".$param." />
                <label for = 'radioimg_".$id."'></label>
                <title>".$title."</title>
            </radioimg>
        ";
        return $TEXT;
    }
    function defaultCount($id, $count){        // Возвращает поле с + и -
        $TEXT = "
            <defcount>
                <minus id = 'defcount_minus_".$id."'>-</minus>
                <input type = 'text' id = 'defcount_".$id."' value = '".$count."' class = 'number' />
                <plus id = 'defcount_plus_".$id."'>+</plus>
            </defcount>
        ";
        return $TEXT;
    }
    function doubleButton($id, $name_1, $name_2, $name_3 = "0", $active = 0){              // Возвращает двойную кнопку
        if($active != 0){
            $a[1] = "";
            $a[2] = "";
            $a[3] = "";
            $a[$active] = "class = 'active'";
        }
        else {
            $a[1] = "class = 'active'";
            $a[2] = "";
            $a[3] = "";
        }

        $TEXT = "
            <doublebutton id = 'doublebutton_".$id."'>
                <div ".$a[1].">".$name_1."</div>
                <div ".$a[2].">".$name_2."</div>
        ";
        if($name_3 != "0") $TEXT .= "<div ".$a[3].">".$name_3."</div>";
        $TEXT .= "</doublebutton>";
        return $TEXT;
    }
    function dotView($number){             // Переводит число в число с точкой
        $number = str_replace(",", ".", $number);
        return $number;
    }
    function commaView($number){             // Переводит число в число с запятой
        $number = str_replace(".", ",", $number);
        return $number;
    }
    function DADATAGetAddressesList($val, $key){    // Возвращает список вариантов по адресу
        $dadata = new \Dadata\DadataClient($key, null);
        $result = $dadata->suggest("address", $val);
        $TEXT = "%-%";

        foreach ($result as $mas) {
            $TEXT .= $mas["value"]."%-%";
        }
        return $TEXT;
    }
    function GMapsGetCoordAddress($val, $key){        // Возвращает координаты адреса
        $address = "https://maps.google.com/maps/api/geocode/json?address=".urlencode($val)."&key=".$key;
        $data = json_decode(file_get_contents($address));
        if(isset($data->results[0]->geometry->location)){
            $result = $data->results[0]->geometry->location;
            $TEXT = $result->lat."%-%".$result->lng;
        }
        else $TEXT = 0;
        return $TEXT;
    }
    function DADATAGetCompany($inn, $key){       // Возвращает данные по компании
        $dadata = new \Dadata\DadataClient($key, null);
        $result = $dadata->findById("party", $inn, 1);
        if(isset($result[0])){
            $mas = $result[0];

            $TEXT = $mas["value"]."%-%";

            $mas = $mas["data"];
            $TEXT .= $mas["inn"]."%-%";
            if(isset($mas["kpp"])) $TEXT .= $mas["kpp"]."%-%"; else $TEXT .= "%-%";
            $TEXT .= $mas["ogrn"]."%-%".$mas["type"]."%-%";
            $TEXT .= $mas["name"]["full_with_opf"]."%-%".$mas["name"]["short_with_opf"]."%-%";
            $TEXT .= $mas["address"]["unrestricted_value"]."%-%";
            if(isset($mas["management"]["name"])) $TEXT .= $mas["management"]["name"];
            else{
                $temp = $mas["name"]["full_with_opf"];
                $temp = str_replace("Индивидуальный предприниматель ", "", $temp);
                $TEXT .= $temp;
            }
            $ogrn_date = (int)($mas["ogrn_date"]/1000);
            $TEXT .= "%-%".$ogrn_date;

            return $TEXT;
        }
        else return 0;
    }
    function phoneToBase($number){    // Перевод номера телефона в вид для базы
        $number = str_replace("(", "", $number);
        $number = str_replace(")", "", $number);
        $number = str_replace("-", "", $number);
        $number = str_replace(" ", "", $number);
        return $number;
    }
    function baseToPhone($n){   // Перевод номера телефона из базы в нормальный вид
        if(isset($n[11])){
            $text = "+7(".$n[2].$n[3].$n[4].") ".$n[5].$n[6].$n[7]."-".$n[8].$n[9]."-".$n[10].$n[11];
            return $text;
        }
        else return $n;

    }
    function rootAndSort($CONNECTION, $id, $param, $SEP){         // Возвращает права доступа и сортировку
        switch($param){
            case 1: $sql = "service_root, service_sort"        ; $sql_2 = "service_sort"    ; break;
            case 2: $sql = "tire_root, tire_sort"              ; $sql_2 = "tire_sort"       ; break;
            case 3: $sql = "disk_root, disk_sort"              ; $sql_2 = "disk_sort"       ; break;
            case 4: $sql = "product_root, product_sort"        ; $sql_2 = "product_sort"    ; break;
            case 5: $sql = "movement_root, movement_sort"      ; $sql_2 = "movement_sort"   ; break;
            case 6: $sql = "sale_root, sale_sort"              ; $sql_2 = "sale_sort"       ; break;
            case 7: $sql = "transaction_root, transaction_sort"; $sql_2 = "transaction_sort"; break;
        }
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT ".$sql." FROM user_root WHERE uId = '$id'"));
        $root = $data[0];
        $sort = $data[1];
        if($sort == ""){
            $SORT_2 = "%-%";
            $t = 0;
            for($i = 0; $i < strlen($root); $i++){
                if($root[$i] == 1){
                    $SORT_2 .= $i."%-%1%-%";
                    $t++;
                }
            }
            $SORT_2 = $t.$SORT_2;
            mysqli_query($CONNECTION, "UPDATE user_root SET ".$sql_2." = '$SORT_2' WHERE uId = '$id'");
            $sort = $SORT_2;
        }
        $mas = explode($SEP, $sort);
        $count = $mas[0];

        return $root."XXX".$sort."XXX".$count;
    }
    function rootAndSortHead($CONNECTION, $id, $param, $SEP){     // Возвращает шапки в зависимости от сортировки и прав доступа
        $data = rootAndSort($CONNECTION, $id, $param, $SEP);
        $mas = explode("XXX", $data);
        $root = $mas[0];
        $sort = $mas[1];
        $count = $mas[2];
        $sort = explode($SEP, $sort);

        if($param == 1){    // Услуги
            if($root[ 0] == 1) $mas[ 0] = "<div class = 'but' onClick = 'servicesSearch(1);' id = 'article' style = 'width: 79px;'>Артикул <triangle></triangle></div>";
            if($root[ 1] == 1) $mas[ 1] = "<input type = 'text' class = 'input height-23' onKeyUp = 'servicesSearch();' style = 'width: 291px;' id = 'name'        placeholder = 'Наименование' />";
            if($root[ 2] == 1) $mas[ 2] = "<div class = 'but' onClick = 'servicesSearch(2);' id = 'price' style = 'width: 154px;'>Стоимость <triangle></triangle></div>";
            if($root[ 3] == 1) $mas[ 3] = "<input type = 'text' class = 'input height-23' onKeyUp = 'servicesSearch();' style = 'width: 193px;' id = 'note'        placeholder = 'Примечание' />";
            if($root[ 4] == 1) $mas[ 4] = "<input type = 'text' class = 'input height-23' onKeyUp = 'servicesSearch();' style = 'width: 403px;' id = 'description' placeholder = 'Описание' />";
        }
        if($param == 2){    // Шины
            $SEASON = "
                <div class = 'select' style = 'width: 110px;' id = 'season'>
                    <arrow></arrow>
                    <headline>Сезон</headline>
                    <input type = 'hidden' id = 'season_hidden' value = '-1'>
                    <div data = '0' onClick = 'tiresSearch(6, 0);'>Зима</div>
                    <div data = '1' onClick = 'tiresSearch(6, 1);'>Лето</div>
                    <div data = '2' onClick = 'tiresSearch(6, 2);'>Всесезон</div>
                </div>";

            $W = "
                <div class = 'select' style = 'width: 110px;' id = 'w'>
                    <arrow></arrow>
                    <headline>Ширина</headline>
                    <input type = 'hidden' id = 'w_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 1");
            while($data = mysqli_fetch_array($sql)) $W .= "<div data = '".$data["value"]."' onClick = 'tiresSearch(7, ".$data["value"].");'>".$data["value"]."</div>";
            $W .= "</div>";

            $H = "
                <div class = 'select' style = 'width: 110px;' id = 'h'>
                    <arrow></arrow>
                    <headline>Высота</headline>
                    <input type = 'hidden' id = 'h_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 2");
            while($data = mysqli_fetch_array($sql)) $H .= "<div data = '".$data["value"]."' onClick = 'tiresSearch(8, ".$data["value"].");'>".$data["value"]."</div>";
            $H .= "</div>";

            $RFT = "
                <div class = 'select' style = 'width: 110px;' id = 'rft'>
                    <arrow></arrow>
                    <headline>RFT: все</headline>
                    <input type = 'hidden' id = 'rft_hidden' value = '-1'>
                    <div data = '0' onClick = 'tiresSearch(9, -1);'>RFT: все</div>
                    <div data = '1' onClick = 'tiresSearch(9, 1);'>RFT: да</div>
                    <div data = '2' onClick = 'tiresSearch(9, 0);'>RFT: нет</div>
                </div>";

            $SPIKE = "
                <div class = 'select' style = 'width: 110px;' id = 'spike'>
                    <arrow></arrow>
                    <headline>Шип: все</headline>
                    <input type = 'hidden' id = 'spike_hidden' value = '-1'>
                    <div data = '0' onClick = 'tiresSearch(10, -1);'>Шип: все</div>
                    <div data = '1' onClick = 'tiresSearch(10, 1);'>Шип: да</div>
                    <div data = '2' onClick = 'tiresSearch(10, 0);'>Шип: нет</div>
                </div>";

            $CARGO = "
                <div class = 'select' style = 'width: 110px;' id = 'cargo'>
                    <arrow></arrow>
                    <headline>Груз: все</headline>
                    <input type = 'hidden' id = 'cargo_hidden' value = '-1'>
                    <div data = '0' onClick = 'tiresSearch(11, -1);'>Груз: все</div>
                    <div data = '1' onClick = 'tiresSearch(11, 1);'>Груз: да</div>
                    <div data = '2' onClick = 'tiresSearch(11, 0);'>Груз: нет</div>
                </div>";

            $BRAND = "
                <div class = 'select' style = 'width: 150px;' id = 'brand'>
                    <arrow></arrow>
                    <headline>Производитель</headline>
                    <input type = 'hidden' id = 'brand_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT brand FROM tire GROUP BY brand");
            while($data = mysqli_fetch_array($sql)) $BRAND .= "<div data = '".$data["brand"]."' onClick = 'tiresSearch(12, \"".$data["brand"]."\");'>".$data["brand"]."</div>";
            $BRAND .= "</div>";

            $R = "
                <div class = 'select_table' style = 'width: 100px;' id = 'r' onClick = 'tiresSearch(13);'>
                    <arrow></arrow>
                    <headline>Радиус</headline>
                    <input type = 'hidden' id = 'r_hidden' value = ''>
                    <container>";
            $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 3");
            while($data = mysqli_fetch_array($sql)) $R .= "<div data = '".$data["value"]."' onClick = 'tiresRadiusSelect(this);'>R".$data["value"]."</div>";
            $R .= "<cross></cross></container></div>";

            if($root[ 0] == 1) $mas[ 0] = "<div class = 'but' onClick = 'tiresSearch(1);' id = 'article' style = 'width: 79px;'>Артикул <triangle></triangle></div>";
            if($root[ 1] == 1) $mas[ 1] = $SEASON;
            if($root[ 2] == 1) $mas[ 2] = $W;
            if($root[ 3] == 1) $mas[ 3] = $H;
            if($root[ 4] == 1) $mas[ 4] = $R;
            if($root[ 5] == 1) $mas[ 5] = $BRAND;
            if($root[ 6] == 1) $mas[ 6] = "<input type = 'text' class = 'input height-23' onKeyUp = 'tiresSearch();' style = 'width: 143px;' id = 'model'  placeholder = 'Модель' />";
            if($root[ 7] == 1) $mas[ 7] = "<input type = 'text' class = 'input height-23' onKeyUp = 'tiresSearch();' style = 'width: 63px;'  id = 'nagr'   placeholder = 'ИН' />";
            if($root[ 8] == 1) $mas[ 8] = "<input type = 'text' class = 'input height-23' onKeyUp = 'tiresSearch();' style = 'width: 63px;'  id = 'resist' placeholder = 'ИС' />";
            if($root[ 9] == 1) $mas[ 9] = $RFT;
            if($root[10] == 1) $mas[10] = $SPIKE;
            if($root[11] == 1) $mas[11] = $CARGO;
            if($root[12] == 1) $mas[12] = "<div class = 'but' onClick = 'tiresSearch(2);' id = 'count' style = 'width: 79px;' >Кол-во<triangle></triangle></div>";
            if($root[13] == 1) $mas[13] = "<div class = 'but' onClick = 'tiresSearch(3);' id = 'price_purchase' style = 'width: 106px;'>Цена закуп.<triangle></triangle></div>";
            if($root[14] == 1) $mas[14] = "<div class = 'but' onClick = 'tiresSearch(4);' id = 'price_sale' style = 'width: 120px;'>Цена продаж.<triangle></triangle></div>";
            if($root[15] == 1) $mas[15] = "<div class = 'but' onClick = 'tiresSearch(5);' id = 'price_wholesale' style = 'width: 93px;' >Цена опт.<triangle></triangle></div>";
            if($root[16] == 1) $mas[16] = "<div class = 'but_empty' style = 'width: 116px;'>Действие</div>";
            if($root[17] == 1) $mas[17] = "<div class = 'but_empty' style = 'width: 116px;'>Коды маркировки</div>";
            if($root[18] == 1) $mas[18] = "<div class = 'but_empty' style = 'width: 166px;'>Плательщик</div>";
        }
        if($param == 3){    // Диски
            $W = "
                <div class = 'select' style = 'width: 91px;' id = 'w'>
                    <arrow></arrow>
                    <headline>Ширина</headline>
                    <input type = 'hidden' id = 'w_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 5");
            while($data = mysqli_fetch_array($sql)) $W .= "<div data = '".$data["value"]."' onClick = 'disksSearch(6, ".$data["value"].");'>".$data["value"]."</div>";
            $W .= "</div>";

            $R = "
                <div class = 'select_table' style = 'width: 91px;' id = 'r' onClick = 'disksSearch(7);'>
                    <arrow></arrow>
                    <headline>Радиус</headline>
                    <input type = 'hidden' id = 'r_hidden' value = ''>
                    <container>";
            $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 6");
            while($data = mysqli_fetch_array($sql)) $R .= "<div data = '".$data["value"]."' onClick = 'disksRadiusSelect(this);'>R".$data["value"]."</div>";
            $R .= "</container></div>";

            $HOLE = "
                <div class = 'select' style = 'width: 105px;' id = 'hole'>
                    <arrow></arrow>
                    <headline>Отверстий</headline>
                    <input type = 'hidden' id = 'hole_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT hole FROM disk GROUP BY hole");
            while($data = mysqli_fetch_array($sql)) $HOLE .= "<div data = '".$data["hole"]."' onClick = 'disksSearch(8, \"".$data["hole"]."\");'>".$data["hole"]."</div>";
            $HOLE .= "</div>";

            $BOLT = "
                <div class = 'select' style = 'width: 105px;' id = 'bolt'>
                    <arrow></arrow>
                    <headline>Межболт</headline>
                    <input type = 'hidden' id = 'bolt_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT bolt FROM disk GROUP BY bolt");
            while($data = mysqli_fetch_array($sql)) $BOLT .= "<div data = '".$data["bolt"]."' onClick = 'disksSearch(9, \"".$data["bolt"]."\");'>".$data["bolt"]."</div>";
            $BOLT .= "</div>";

            $VYLET = "
                <div class = 'select' style = 'width: 91px;' id = 'vylet'>
                    <arrow></arrow>
                    <headline>Вылет</headline>
                    <input type = 'hidden' id = 'vylet_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT vylet FROM disk GROUP BY vylet");
            while($data = mysqli_fetch_array($sql)) $VYLET .= "<div data = '".$data["vylet"]."' onClick = 'disksSearch(10, \"".$data["vylet"]."\");'>".$data["vylet"]."</div>";
            $VYLET .= "</div>";

            $HUB = "
                <div class = 'select' style = 'width: 91px;' id = 'hub'>
                    <arrow></arrow>
                    <headline>Ступица</headline>
                    <input type = 'hidden' id = 'hub_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT hub FROM disk GROUP BY hub");
            while($data = mysqli_fetch_array($sql)) $HUB .= "<div data = '".$data["hub"]."' onClick = 'disksSearch(11, \"".$data["hub"]."\");'>".$data["hub"]."</div>";
            $HUB .= "</div>";

            $COLOR = "
                <div class = 'select' style = 'width: 140px;' id = 'color'>
                    <arrow></arrow>
                    <headline>Цвет</headline>
                    <input type = 'hidden' id = 'color_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT value FROM product_param WHERE type = 4");
            while($data = mysqli_fetch_array($sql)) $COLOR .= "<div data = '".$data["value"]."' onClick = 'disksSearch(12, \"".$data["value"]."\");'>".$data["value"]."</div>";
            $COLOR .= "</div>";

            if($root[ 0] == 1) $mas[ 0] = "<div class = 'but' onClick = 'disksSearch(1);' id = 'article' style = 'width: 79px;'>Артикул <triangle></triangle></div>";
            if($root[ 1] == 1) $mas[ 1] = "<input type = 'text' class = 'input height-23' onKeyUp = 'disksSearch();' style = 'width: 303px;' id = 'nomenclature'  placeholder = 'Номенклатура' />";
            if($root[ 2] == 1) $mas[ 2] = $W;
            if($root[ 3] == 1) $mas[ 3] = $R;
            if($root[ 4] == 1) $mas[ 4] = $HOLE;
            if($root[ 5] == 1) $mas[ 5] = $BOLT;
            if($root[ 6] == 1) $mas[ 6] = $VYLET;
            if($root[ 7] == 1) $mas[ 7] = $HUB;
            if($root[ 8] == 1) $mas[ 8] = $COLOR;
            if($root[ 9] == 1) $mas[ 9] = "<div class = 'but' onClick = 'disksSearch(2);' id = 'count' style = 'width: 79px;' >Кол-во<triangle></triangle></div>";
            if($root[10] == 1) $mas[10] = "<div class = 'but' onClick = 'disksSearch(3);' id = 'price_purchase' style = 'width: 106px;'>Цена закуп.<triangle></triangle></div>";
            if($root[11] == 1) $mas[11] = "<div class = 'but' onClick = 'disksSearch(4);' id = 'price_sale' style = 'width: 120px;'>Цена продаж.<triangle></triangle></div>";
            if($root[12] == 1) $mas[12] = "<div class = 'but' onClick = 'disksSearch(5);' id = 'price_wholesale' style = 'width: 93px;' >Цена опт.<triangle></triangle></div>";
            if($root[13] == 1) $mas[13] = "<div class = 'but_empty' style = 'width: 116px;'>Действие</div>";
        }
        if($param == 4){    // Товары
            if($root[ 0] == 1) $mas[ 0] = "<div class = 'but' onClick = 'productsSearch(1);' id = 'article' style = 'width: 79px;'>Артикул <triangle></triangle></div>";
            if($root[ 1] == 1) $mas[ 1] = "<input type = 'text' class = 'input height-23' onKeyUp = 'productsSearch();' style = 'width: 180px;' id = 'name'  placeholder = 'Наименование' />";
            if($root[ 2] == 1) $mas[ 2] = "<input type = 'text' class = 'input height-23' onKeyUp = 'productsSearch();' style = 'width: 172px;' id = 'params'  placeholder = 'Параметры' />";
            if($root[ 3] == 1) $mas[ 3] = "<input type = 'text' class = 'input height-23' onKeyUp = 'productsSearch();' style = 'width: 193px;' id = 'note'  placeholder = 'Примечание' />";
            if($root[ 4] == 1) $mas[ 4] = "<div class = 'but' onClick = 'productsSearch(2);' id = 'count' style = 'width: 79px;' >Кол-во<triangle></triangle></div>";
            if($root[ 5] == 1) $mas[ 5] = "<div class = 'but' onClick = 'productsSearch(3);' id = 'price_purchase' style = 'width: 106px;'>Цена закуп.<triangle></triangle></div>";
            if($root[ 6] == 1) $mas[ 6] = "<div class = 'but' onClick = 'productsSearch(4);' id = 'price_sale' style = 'width: 120px;'>Цена продаж.<triangle></triangle></div>";
            if($root[ 7] == 1) $mas[ 7] = "<div class = 'but' onClick = 'productsSearch(5);' id = 'price_wholesale' style = 'width: 93px;' >Цена опт.<triangle></triangle></div>";
            if($root[ 8] == 1) $mas[ 8] = "<div class = 'but_empty' style = 'width: 116px;'>Действие</div>";
        }
        if($param == 5){    // Движения
            $action = "
                <div class = 'select' style = 'width: 119px;' id = 'action'>
                    <arrow></arrow>
                    <headline>Действие</headline>
                    <input type = 'hidden' id = 'action_hidden' value = '-1'>
                    <div data = '1' onClick = 'movementsSearch(3, 1);'>Приемка</div>
                    <div data = '2' onClick = 'movementsSearch(3, 2);'>Списание</div>
                    <div data = '3' onClick = 'movementsSearch(3, 3);'>Перемещение</div>
                    <div data = '4' onClick = 'movementsSearch(3, 4);'>Пополнение</div>
                    <div data = '5' onClick = 'movementsSearch(3, 5);'>Продажа</div>
                </div>";

            $kuda = "
                <div class = 'select' style = 'width: 119px;' id = 'kuda'>
                    <arrow></arrow>
                    <headline>Куда</headline>
                    <input type = 'hidden' id = 'kuda_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT kuda FROM movement GROUP BY kuda");
            while($data = mysqli_fetch_array($sql)) $kuda .= "<div data = '".$data["kuda"]."' onClick = 'movementsSearch(5, \"".$data["kuda"]."\");'>".$data["kuda"]."</div>";
            $kuda .= "</div>";

            $otkuda = "
                <div class = 'select' style = 'width: 119px;' id = 'otkuda'>
                    <arrow></arrow>
                    <headline>Откуда</headline>
                    <input type = 'hidden' id = 'otkuda_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT otkuda FROM movement GROUP BY otkuda");
            while($data = mysqli_fetch_array($sql)) $otkuda .= "<div data = '".$data["otkuda"]."' onClick = 'movementsSearch(6, \"".$data["otkuda"]."\");'>".$data["otkuda"]."</div>";
            $otkuda .= "</div>";

            $cureer = "
                <div class = 'select' style = 'width: 201px;' id = 'cureer'>
                    <arrow></arrow>
                    <headline>Курьер</headline>
                    <input type = 'hidden' id = 'cureer_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT cureer FROM movement GROUP BY cureer");
            while($data = mysqli_fetch_array($sql)) $cureer .= "<div data = '".$data["cureer"]."' onClick = 'movementsSearch(7, \"".$data["cureer"]."\");'>".$data["cureer"]."</div>";
            $cureer .= "</div>";

            if($root[ 0] == 1) $mas[ 0] = "<div class = 'but' onClick = 'movementsSearch(1);' id = 'number' style = 'width: 98px;'>ID <triangle></triangle></div>";
            if($root[ 1] == 1) $mas[ 1] = "<div class = 'but' onClick = 'movementsSearch(2);' id = 'date' style = 'width: 78px;'>Дата <triangle></triangle></div>";
            if($root[ 2] == 1) $mas[ 2] = $action;
            if($root[ 3] == 1) $mas[ 3] = "<div class = 'but_empty' id = 'move_object' style = 'width: 88px;'>Объект</div>";
            if($root[ 4] == 1) $mas[ 4] = "<div class = 'but_empty' style = 'width: 631px;'>Информация</div>";
            if($root[ 5] == 1) $mas[ 5] = $kuda;
            if($root[ 6] == 1) $mas[ 6] = $otkuda;
            if($root[ 7] == 1) $mas[ 7] = "<div class = 'but' onClick = 'movementsSearch(4);' id = 'count' style = 'width: 70px;'>Кол-во <triangle></triangle></div>";
            if($root[ 8] == 1) $mas[ 8] = "<div class = 'but_empty' style = 'width: 78px;'>Было стало</div>";
            if($root[ 9] == 1) $mas[ 9] = $cureer;
            if($root[10] == 1) $mas[10] = "<div class = 'but_empty' style = 'width: 185px;'>Действия</div>";
            if($root[11] == 1) $mas[11] = "<div class = 'but' onClick = 'movementsSearch(11);' id = 'date_plan' style = 'width: 95px;'>Дата план<triangle></triangle></div>";
            if($root[12] == 1) $mas[12] = "<div class = 'but_empty' style = 'width: 116px;'>Коды маркировки</div>";
            if($root[13] == 1) $mas[13] = "<div class = 'but_empty' style = 'width: 166px;'>Плательщик</div>";
        }
        if($param == 6){    // Продажи
            $cureer = "
                <div class = 'select' style = 'width: 201px;' id = 'cureer'>
                    <arrow></arrow>
                    <headline>Курьер</headline>
                    <input type = 'hidden' id = 'cureer_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT cureer FROM movement GROUP BY cureer");
            while($data = mysqli_fetch_array($sql)) $cureer .= "<div data = '".$data["cureer"]."' onClick = 'salesSearch(6, \"".$data["cureer"]."\");'>".$data["cureer"]."</div>";
            $cureer .= "</div>";

            $status = "
                <div class = 'select' style = 'width: 148px;' id = 'status'>
                    <arrow></arrow>
                    <headline>Статус</headline>
                    <input type = 'hidden' id = 'status_hidden' value = '-1'>
                    <div data = '1' onClick = 'salesSearch(2, 1);'>На сборке</div>
                    <div data = '2' onClick = 'salesSearch(2, 2);'>Бронь</div>
                    <div data = '3' onClick = 'salesSearch(2, 3);'>Оплачено</div>
                    <div data = '4' onClick = 'salesSearch(2, 4);'>Ждет отправки</div>
                    <div data = '5' onClick = 'salesSearch(2, 5);'>У курьера</div>
                    <div data = '6' onClick = 'salesSearch(2, 6);'>Доставляется</div>
                    <div data = '7' onClick = 'salesSearch(2, 7);'>Получено</div>
                </div>";
            $poluchenie = "
                <div class = 'select' style = 'width: 157px;' id = 'poluchenie'>
                    <arrow></arrow>
                    <headline>Получение</headline>
                    <input type = 'hidden' id = 'poluchenie_hidden' value = '-1'>
                    <div data = 'Пункт выдачи' onClick = 'salesSearch(3, \"Пункт выдачи\");'>Пункт выдачи</div>
                    <div data = 'В местах хранения' onClick = 'salesSearch(3, \"В местах хранения\");'>В местах хранения</div>
                    <div data = 'Доставка' onClick = 'salesSearch(3, \"Доставка\");'>Доставка</div>
                    <div data = 'Доставка ТК' onClick = 'salesSearch(3, \"Доставка ТК\");'>Доставка ТК</div>
                </div>
            ";
            $base = "
                <div class = 'select' style = 'width: 167px;' id = 'base_sale'>
                    <arrow></arrow>
                    <headline>База</headline>
                    <input type = 'hidden' id = 'base_sale_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT color, code, id FROM base");
            while($data = mysqli_fetch_array($sql)) $base .= "<div data = '".$data["id"]."' onClick = 'salesSearch(5, \"".$data["id"]."\");'><circle style = 'background-color: #".$data["color"]."; margin-top: 6px;'></circle>".$data["code"]."</div>";
            $base .= "</div>";
            $tk = "
                <div class = 'select' style = 'width: 138px;' id = 'delivery'>
                    <arrow></arrow>
                    <headline>ТК</headline>
                    <input type = 'hidden' id = 'delivery_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT name, id FROM delivery");
            while($data = mysqli_fetch_array($sql)) $tk .= "<div data = '".$data["id"]."' onClick = 'salesSearch(7, \"".$data["id"]."\");'>".$data["name"]."</div>";
            $tk .= "</div>";
            $vid_oplaty = "
                <div class = 'select' style = 'width: 176px;' id = 'oplata'>
                    <arrow></arrow>
                    <headline>Вид оплаты</headline>
                    <input type = 'hidden' id = 'oplata_hidden0' value = '-1'>
                    <div data = '1' onClick = 'salesSearch(11, 1);'>Наличные</div>
                    <div data = '2' onClick = 'salesSearch(11, 2);'>По карте +2%</div>
                </div>
            ";
            $manager = "
                <div class = 'select' style = 'width: 145px;' id = 'manager'>
                    <arrow></arrow>
                    <headline>Менеджер</headline>
                    <input type = 'hidden' id = 'manager_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT name, surname, id FROM user WHERE type = 2");
            while($data = mysqli_fetch_array($sql)) $manager .= "<div data = '".$data["id"]."' onClick = 'salesSearch(14, \"".$data["id"]."\");'>".$data["surname"]." ".$data["name"]."</div>";
            $manager .= "</div>";

            if($root[ 0] == 1) $mas[ 0] = "<input type = 'text' class = 'input height-23' onKeyUp = 'salesSearch();' style = 'width: 93px;' id = 'number' placeholder = 'ID'>";
            if($root[ 1] == 1) $mas[ 1] = $status;
            if($root[ 2] == 1) $mas[ 2] = $poluchenie;
            if($root[ 3] == 1) $mas[ 3] = "<div class = 'but' onClick = 'salesSearch(4);' id = 'date' style = 'width: 115px;'>Дата <triangle></triangle></div>";
            if($root[ 4] == 1) $mas[ 4] = "<div class = 'but_empty' id = 'move_vydacha' style = 'width: 151px;'>Выдача</div>";
            if($root[ 5] == 1) $mas[ 5] = $base;
            if($root[ 6] == 1) $mas[ 6] = "<div class = 'but_empty' id = 'move_object' style = 'width: 151px;'>Объект</div>";
            if($root[ 7] == 1) $mas[ 7] = "<input type = 'text' class = 'input height-23' onKeyUp = 'salesSearch();' style = 'width: 166px;' id = 'client' placeholder = 'Клиент'>";
            if($root[ 8] == 1) $mas[ 8] = $cureer;
            if($root[ 9] == 1) $mas[ 9] = $tk;
            if($root[10] == 1) $mas[10] = "<div class = 'but' onClick = 'salesSearch(9);' id = 'price_purchase' style = 'width: 106px;'>Цена закуп.<triangle></triangle></div>";
            if($root[11] == 1) $mas[11] = "<div class = 'but' onClick = 'salesSearch(10);' id = 'price_sale' style = 'width: 120px;'>Цена продаж.<triangle></triangle></div>";
            if($root[12] == 1) $mas[12] = $vid_oplaty;
            if($root[13] == 1) $mas[13] = "<div class = 'but_empty' style = 'width: 94px;'>Движение</div>";
            if($root[14] == 1) $mas[14] = "<div class = 'but' onClick = 'salesSearch(12);' id = 'skidka_percent' style = 'width: 106px;'>Скидка, %<triangle></triangle></div>";
            if($root[15] == 1) $mas[15] = "<div class = 'but' onClick = 'salesSearch(13);' id = 'skidka_ruble' style = 'width: 106px;'>Скидка, Р<triangle></triangle></div>";
            if($root[16] == 1) $mas[16] = $manager;
            if($root[17] == 1) $mas[17] = "<div class = 'but_empty' style = 'width: 116px;'>Коды маркировки</div>";
            if($root[18] == 1) $mas[18] = "<div class = 'but_empty' style = 'width: 166px;'>Плательщик</div>";
        }
        if($param == 7){    // Операции
            $type = "
                <div class = 'select' style = 'width: 176px;' id = 'type'>
                    <arrow></arrow>
                    <headline>Операция</headline>
                    <input type = 'hidden' id = 'type_hidden' value = '-1'>
                    <div data = '1' onClick = 'transactionsSearch(4, 1);'>Прием оплаты</div>
                    <div data = '2' onClick = 'transactionsSearch(4, 2);'>Списание</div>
                </div>
            ";
            $cashier = "
                <div class = 'select' style = 'width: 145px;' id = 'cashier'>
                    <arrow></arrow>
                    <headline>Кассир</headline>
                    <input type = 'hidden' id = 'cashier_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT name, surname, id FROM user WHERE type = 3");
            while($data = mysqli_fetch_array($sql)) $cashier .= "<div data = '".$data["id"]."' onClick = 'transactionsSearch(5, \"".$data["id"]."\");'>".$data["surname"]." ".$data["name"]."</div>";
            $cashier .= "</div>";
            $oplata = "
                <div class = 'select' style = 'width: 176px;' id = 'oplata'>
                    <arrow></arrow>
                    <headline>Оплата</headline>
                    <input type = 'hidden' id = 'oplata_hidden' value = '-1'>
                    <div data = '1' onClick = 'transactionsSearch(6, 1);'>Наличные</div>
                    <div data = '2' onClick = 'transactionsSearch(6, 2);'>Карта</div>
                </div>
            ";
            $client = "
                <div class = 'select' style = 'width: 145px;' id = 'client'>
                    <arrow></arrow>
                    <headline>Клиент</headline>
                    <input type = 'hidden' id = 'client_hidden' value = '-1'>";
            $sql = mysqli_query($CONNECTION, "SELECT name, id FROM client");
            while($data = mysqli_fetch_array($sql)) $client .= "<div data = '".$data["id"]."' onClick = 'transactionsSearch(7, \"".$data["id"]."\");'>".$data["name"]."</div>";
            $client .= "</div>";

            if($root[ 0] == 1) $mas[ 0] = "<input type = 'text' class = 'input height-23' onKeyUp = 'transactionsSearch();' style = 'width: 93px;' id = 'number' placeholder = 'ID'>";
            if($root[ 1] == 1) $mas[ 1] = "<div class = 'but' onClick = 'transactionsSearch(2);' id = 'date' style = 'width: 115px;'>Дата <triangle></triangle></div>";
            if($root[ 2] == 1) $mas[ 2] = "<div class = 'but' onClick = 'transactionsSearch(3);' id = 'summa' style = 'width: 115px;'>Сумма <triangle></triangle></div>";
            if($root[ 3] == 1) $mas[ 3] = $type;
            if($root[ 4] == 1) $mas[ 4] = $cashier;
            if($root[ 5] == 1) $mas[ 5] = $oplata;
            if($root[ 6] == 1) $mas[ 6] = "<div class = 'but_empty' id = 'sale' style = 'width: 151px;'>Сделка</div>";
            if($root[ 7] == 1) $mas[ 7] = $client;

        }

        $HEAD = "";
        for($i = 1; $i < $count*2; $i++){
            if($i % 2 == 1){
                $num = $sort[$i];
                if($sort[$i+1] == 1) $HEAD .= $mas[$num];
            }
        }

        return $HEAD;
    }
    function getPriceTroyki($str){        // Возвращает цену в формате по тройкам
        $str = (string)$str;
        $len = strlen($str);
        $j = 0;
        $pos = strpos($str, ".");
        if ($pos === false) {
            for($i = 0; $i < $len; $i++){
                $mas[$j] = $str[$len - $i - 1];
                $j++;
                if(($i + 1)%3 == 0){
                    $mas[$j] = " ";
                    $j++;
                }
            }
        } else {
            if($len - $pos == 3){
                $mas[0] = $str[$len-1];
                $mas[1] = $str[$len-2];
                $mas[2] = ",";
                $j = 3;
                for($i = 3; $i < $len; $i++){
                    $mas[$j] = $str[$len - $i - 1];
                    $j++;
                    if(($i + 1)%3 == 0){
                        $mas[$j] = " ";
                        $j++;
                    }
                }
            }
            if($len - $pos == 2){
                $mas[0] = $str[$len-1];
                $mas[1] = ",";
                $j = 2;
                for($i = 2; $i < $len; $i++){
                    $mas[$j] = $str[$len - $i - 1];
                    $j++;
                    if(($i + 2)%3 == 0){
                        $mas[$j] = " ";
                        $j++;
                    }
                }
            }

        }
        $j--;
        $i = 0;
        for($k = $j; $k >= 0; $k--){
            $str[$i] = $mas[$k];
            $i++;
        }

        $temp = explode(",", $str);
        $temp_1 = $temp[0];
        //Добавление запятой и 2 цифр после нее
        //if(!isset($temp[1])) $str = $temp_1.",00"; else $str = $temp_1.",".$temp[1];
        //if(isset($temp[1]) && strlen($temp[1]) < 2) $str .= "0";

        $str = $temp[0];

        return $str;
    }
    function getRight8Number($count){    // Возвращает номер в восьмизначном формате с ведущими нулями
        switch(strlen($count)){
            case 1: $count = "0000000".$count; break;
            case 2: $count = "000000".$count; break;
            case 3: $count = "00000".$count; break;
            case 4: $count = "0000".$count; break;
            case 5: $count = "000".$count; break;
            case 6: $count = "00".$count; break;
            case 7: $count = "0".$count; break;
        }
        return $count;
    }
    function getRight5Number($count){    // Возвращает номер в пятизначном формате с ведущими нулями
        if(strlen($count) == 1) $count = "0000".$count;
        else {
            if(strlen($count) == 2) $count = "000".$count;
            else {
                if(strlen($count) == 3) $count = "00".$count;
                else{
                    if(strlen($count) == 4) $count = "0".$count;
                }
            }
        }
        return $count;
    }
    function getRight4Number($count){    // Возвращает номер в четырехзначном формате с ведущими нулями
        if(strlen($count) == 1) $count = "000".$count;
        else {
            if(strlen($count) == 2) $count = "00".$count;
            else {
                if(strlen($count) == 3) $count = "0".$count;
            }
        }
        return $count;
    }
    function imgAdd($name_old){                                 // Перемещение изображения из temp в основную папку
        if(strpos($name_old, "img") == false){
            $raz = $name_old;
            $raz = explode(".", $raz);
            $raz = end($raz);

            $flag = true;

            while($flag){
                $name = generate_16(10).".".$raz;
                $name_new = "../../img/".$name; break;
                if(!file_exists($name_new)) $flag = false;
            }
            @rename("../../".$name_old, "../../img/".$name);
        }
        else $name = str_replace("img/", "", $name_old);
        return $name;

    }
    function getPercentLine($a, $b){              // Возвращает линию с процентами заполнения
        if($a > $b) $a = $b;
        $w1 = (int)$a*210/$b;
        if($w1 < 4) $w1 = 4;
        $w2 = 210 - $w1;
        $w1 -=1;

        $TEXT = "
            <percent>
                <percent_left style = 'width: ".$w1."px;'></percent_left>
                <percent_right style = 'width: ".$w2."px;'></percent_right>
                <percent_text>".$a."</percent_text>
            </percent>";
        return $TEXT;
    }
    function storageCalc($CONNECTION, $id){     // Пересчет количества мест в хранилище
        $count = 0;
        $occupied = 0;
        $sql = mysqli_query($CONNECTION, "SELECT count, occupied FROM storage WHERE mother = '$id'");
        while($data = mysqli_fetch_array($sql)){
            $count += $data["count"];
            $occupied += $data["occupied"];
        }
        mysqli_query($CONNECTION, "UPDATE storage SET count = '$count', occupied = '$occupied' WHERE id = '$id'");
    }
    function storageProductAdd($CONNECTION, $storage, $count){    // Добавляет товар в хранилище (с точки зрения занятости мест)
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT occupied, mother FROM storage WHERE id = '$storage'"));
        $count += $data["occupied"];
        mysqli_query($CONNECTION, "UPDATE storage SET occupied = '$count' WHERE id = '$storage'");
        if($data["mother"] != 0) storageCalc($CONNECTION, $data["mother"]);
    }
    function storageProductRemove($CONNECTION, $storage, $count){    // Убирает товар из хранилища (с точки зрения занятости мест)
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT occupied, mother FROM storage WHERE id = '$storage'"));
        $count = $data["occupied"] - $count;
        mysqli_query($CONNECTION, "UPDATE storage SET occupied = '$count' WHERE id = '$storage'");
        if($data["mother"] != 0) storageCalc($CONNECTION, $data["mother"]);
    }
    function getProductLineAndBase($CONNECTION, $id, $type, $base, $param_0, $count = 4, $param_2 = 0, $storage = 0, $payer = -1){   // Строка с товаром при добавлении в заказ с проверкой наличия на складе
        $sql = "SELECT * FROM ";
        switch($type){
            case 1: $sql .= "tire"; break;
            case 2: $sql .= "disk"; break;
            case 3: $sql .= "product"; break;
            case 4: $sql .= "service"; break;
            case 5: $sql .= "season_temp"; break;
        }
        $sql .= " WHERE id = '$id'";
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
        $barcode = $data["barcode"];
        $payer_param = 1;
        $payer_tire_count = -1;
        if($type == 1){
            $article = "S".$data["article"];
            $desc = $data["brand"]." ".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"];
            $price = $data["price_sale"];
            
            if($payer != "-1"){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT codes FROM payer WHERE id = '$payer'"));
                if($temp["codes"] == 1){
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM code WHERE tire = '$id' AND payer = '$payer' AND sale = 0"));
                    if($temp[0] < $count) $payer_param = -1;
                    else{
                        $payer_tire_count = $temp[0];
                    }
                }
                else{
                    $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM code WHERE tire = '$id' AND payer = '$payer' LIMIT 1"));
                    if(!isset($temp["id"])) $payer_param = -1;
                }
            }

        }
        if($type == 2){
            $article = "D".$data["article"];
            $desc = $data["nomenclature"]." ".$data["w"]."R".$data["r"];
            $price = $data["price_sale"];
        }
        if($type == 3){
            $article = "T".$data["article"];
            $desc = $data["name"]." ".$data["params"];
            $price = $data["price_sale"];
        }
        if($type == 4){
            $article = "U".$data["article"];
            $desc = $data["name"];
            if($param_0 > 0) $price = $data["price_".$param_0];
            else $price = $data["price_1"];
            if($count == 4) $count = $data["count"];
        }
        if($type == 5){
            $article = "V".$data["article"];
            $desc = $data["name"];
            $price = $data["price"];
            $count = 1;
        }

        $param = 1;
        if($type < 4){
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM base WHERE code = '$base'"));
            $base = $data["id"];
            if($type < 3){
                $temp = 0;
                $sql = mysqli_query($CONNECTION, "SELECT id FROM storage WHERE base = '$base' AND composite = 0");
                while($data = mysqli_fetch_array($sql)){
                    $s_id = $data["id"];
                    $temp_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE storage = '$s_id' AND barcode = '$barcode'"));
                    if($temp_2["count"] > 0) $temp += $temp_2["count"];
                }
            }
            else {
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE base = '$base' AND barcode = '$barcode'"));
                $temp = $data["count"];
            }
            $mult = $price * $count;
            $PRICE_ALL = "
                <price_all>".getPriceTroyki($mult)." ₽</price_all>
            ";
            $det = 0;
            if($temp >= $count){
                $PRICE_ALL .= "<span>".$count." x ".getPriceTroyki($price)."</span><span_red></span_red>";
            }
            else {
                $det = $count - $temp;
                $PRICE_ALL .= "<span>".$count." x ".getPriceTroyki($price)."</span><span_red>".$det."</span_red>";
                $param = 0;
            }
        }
        else {
            $mult = $price * $count;
            $PRICE_ALL = "
                <price_all>".getPriceTroyki($mult)." ₽</price_all>
                <span>".$count." x ".getPriceTroyki($price)."</span>
            ";
        }

        if($type < 4 && $param_2 != 0){
            $temp = explode(" - ", $storage);
            if($type < 3){
                $storage = (isset($temp[1]))?$temp[1]:'';
            }
            if($type == 3){
                $storage = $temp[0];
            }
        }

        $BASES = "";
        $COUNT_ON_BASE = "";
        if($type < 4){
            $sql = mysqli_query($CONNECTION, "SELECT id, code, color FROM base");
            while($data = mysqli_fetch_array($sql)){
                $b_id = $data["id"];
                $base_name = $data["code"];
                $color = $data["color"];
                if($type < 3){
                    $sql2 = mysqli_query($CONNECTION, "SELECT id, code FROM storage WHERE base = '$b_id' AND composite = 0");
                    while($data2 = mysqli_fetch_array($sql2)){
                        $storage_code = $data2["code"];
                        $s_id = $data2["id"];
                        $temp_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE storage = '$s_id' AND barcode = '$barcode'"));
                        if($temp_2["count"] > 0){
                            $bases_item_active = "";
                            if ($storage == $storage_code && $param_2 != 0) $bases_item_active = "bases_item_active";
                            $BASES .= "
                                <div class = 'bases_item ".$bases_item_active."' onClick = 'salesAddProductBaseProductAdd(this);'>
                                    <count style = 'background-color: #".$color."'>".$temp_2["count"]."</count>
                                    <name>".$base_name." - ".$storage_code."</name>
                                </div>";
                        }
                    }
                }
                else{
                    $temp_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE base = '$b_id' AND barcode = '$barcode'"));
                    if($temp_2["count"] > 0){
                        $bases_item_active = "";
                        if ($base_name == $storage && $param_2 != 0) $bases_item_active = "bases_item_active";
                        $BASES .= "
                            <div class = 'bases_item ".$bases_item_active."' onClick = 'salesAddProductBaseProductAdd(this);'>
                                <count style = 'background-color: #".$color."'>".$temp_2["count"]."</count>
                                <name>".$base_name."</name>
                            </div>";
                    }
                }
            }
        }


        //if($param == 1){
        //    $input_class = "green";
        //    $bottom_class = "none";
        //}
        //else{
        //    $input_class = "red";
        //    $bottom_class = "block";
        //}

        if($type < 4){
            $bottom_class = "block";
            $input_class = "green";
        }
        else{
            $bottom_class = "none";
            $input_class = "green";
        }



        $TEXT = "
            <div class = 'pl' id = '".$barcode."'>
                <price_param>".$param_0."</price_param>
                <price>".$price."</price>
                <payer_tire_count>".$payer_tire_count."</payer_tire_count>
                <count_on_base>".$COUNT_ON_BASE."</count_on_base>
                <div class = 'pl_number'>%NUMBER%</div>
                <div class = 'pl_left'>
                    <name>".$article."</name>
                    <span>".$desc."</span>
                </div>
                <div class = 'pl_center'>".$PRICE_ALL."</div>
                <div class = 'pl_right'><input type = 'text' onKeyUp = 'salesAddProductCountChange(this);' value = '".$count."' class = 'number ".$input_class."'  /></div>
                <cross onClick = 'salesAddProductDelete(this);'></cross>
        ";
        if($type < 4){
            $TEXT .= "
                <div class = 'pl_bottom' style = 'display: ".$bottom_class."'>
                    <div class = 'pl_bottom_line'></div>
                    <div class = 'pl_bottom_left' id = '".$barcode."_storage'>Берем: ".$BASES."</div>
                </div>";
        }
        $TEXT .= "</div>";
        // <div class = 'pl_bottom_right'><input class = 'number' onKeyUp = 'salesAddProductChangeDopCount(this);' type = 'text' value = '".$det."' /></div>
        return $TEXT."%-%".$payer_param;
    }
    function getProductLine($CONNECTION, $id, $type, $count, $param_0, $otkuda, $sId){   // Строка с товаром при выводе заказа
        $sql = "SELECT * FROM ";
        switch($type){
            case 1: $sql .= "tire"; break;
            case 2: $sql .= "disk"; break;
            case 3: $sql .= "product"; break;
            case 4: $sql .= "service"; break;
            case 5: $sql .= "season_temp"; break;
        }
        $sql .= " WHERE id = '$id'";
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, $sql));
        $barcode = $data["barcode"];
        if($type == 1){
            $article = "S".$data["article"];
            $desc = $data["brand"]." ".$data["model"]." ".$data["w"]."/".$data["h"]."R".$data["r"];
            $price = $data["price_sale"];
        }
        if($type == 2){
            $article = "D".$data["article"];
            $desc = $data["nomenclature"]." ".$data["w"]."R".$data["r"];
            $price = $data["price_sale"];
        }
        if($type == 3){
            $article = "T".$data["article"];
            $desc = $data["name"]." ".$data["params"];
            $price = $data["price_sale"];
        }
        if($type == 4){
            $article = "U".$data["article"];
            $desc = $data["name"];
            if($param_0 > 0) $price = $data["price_".$param_0];
            else $price = $data["price_1"];
        }
        if($type == 5){
            $article = "V".$data["article"];
            $desc = $data["name"];
            $price = $data["price"];
        }

        $param = 1;
        $mult = $price * $count;
        $PRICE_ALL = "
            <price_all>".getPriceTroyki($mult)." ₽</price_all>
            <span>".$count." x ".getPriceTroyki($price)."</span><span_red></span_red>
        ";


        $TEXT = "
            <div class = 'pl' id = '".$barcode."' data = '".$sId."'>
                <price_param>".$param_0."</price_param>
                <price>".$price."</price>
                <div class = 'pl_number'>%NUMBER%</div>
                <div class = 'pl_left'>
                    <name>".$article."</name>
                    <span>".$desc."</span>
                    <span>".$otkuda."</span>
                </div>
                <div class = 'pl_center'>".$PRICE_ALL."</div>
                <div class = 'pl_right'><input type = 'text' readonly value = '".$count."' class = 'number'  /></div>
                <cross onClick = 'salesViewProductDelete(this);'></cross>     
        ";
        $TEXT .= "</div>";
        return $TEXT;
    }
    function movementAdd($CONNECTION, $barcode, $count, $otkuda, $kuda, $sale){     // Создание нового перемещения
        $data_1 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article FROM tire WHERE barcode = '$barcode'"));
        $data_2 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article FROM disk WHERE barcode = '$barcode'"));
        $data_3 = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, article FROM product WHERE barcode = '$barcode'"));
        if($data_1["id"] > 0 || $data_2["id"] > 0 || $data_3["id"] > 0){
            if($data_1["id"] > 0){
                $article = "S".$data_1["article"];
                $p_id = $data_1["id"];
                $p_type = 1;
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND storage = '$otkuda'"));
            }
            if($data_2["id"] > 0){
                $article = "D".$data_2["article"];
                $p_id = $data_2["id"];
                $p_type = 2;
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND storage = '$otkuda'"));
            }
            if($data_3["id"] > 0){
                $article = "T".$data_3["article"];
                $p_id = $data_3["id"];
                $p_type = 3;
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count FROM available WHERE barcode = '$barcode' AND base = '$otkuda'"));
            }

            $count_old = $data["count"];
            $count_new = $count_old - $count;

            if($p_type < 3){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$otkuda'"));
                $code = $data["code"];
                $base = $data["base"];
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
                $code = $data["code"]." - ".$code;

                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base, code FROM storage WHERE id = '$kuda'"));
                $kuda = $data["code"];
                $base = $data["base"];
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$base'"));
                $kuda = $data["code"]." - ".$kuda;
            }
            if($p_type == 3){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$otkuda'"));
                $code = $temp["code"];

                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT code FROM base WHERE id = '$kuda'"));
                $kuda = $temp["code"];
            }

            $time = time();
            $number = date("ymd", $time);
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM movement WHERE number LIKE '$number%'"));
            $c = $temp[0];
            $c++;
            $number = $number.getRight4Number($c);

            mysqli_query($CONNECTION, "INSERT INTO movement (number, article, p_id, p_type, date, action, info, otkuda, kuda, count, bef, aft, sale)
                VALUES ('$number', '$article', '$p_id', '$p_type', '$time', '3', 'Перемещение', '$code', '$kuda',  '$count', '$count_old', '$count_new', '$sale')");
            //productMove($CONNECTION, $otkuda, 0, $p_type, $p_id, $count);
        }
    }
    function saleStatusChange($CONNECTION, $sale, $status){      // Смена статуса продажи
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT status FROM sale WHERE id = '$sale'"));
        $status_old = $data["status"];
        $time = time();
        if($status_old == 6 && $status == 3){
            mysqli_query($CONNECTION, "INSERT INTO sale_action (sale, user, date, status) VALUES ('$sale', '".ID."', '$time', '$status')");
        }
        else{
            mysqli_query($CONNECTION, "UPDATE sale SET status = '$status' WHERE id = '$sale'");

            mysqli_query($CONNECTION, "INSERT INTO sale_action (sale, user, date, status) VALUES ('$sale', '".ID."', '$time', '$status')");
            if($status == 3){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT poluchenie FROM sale WHERE id = '$sale'"));
                if($data["poluchenie"] == "Доставка" || $data["poluchenie"] == "Доставка ТК"){
                    $status += 1;
                    $time += 60;
                    mysqli_query($CONNECTION, "INSERT INTO sale_action (sale, user, date, status) VALUES ('$sale', '".ID."', '$time', '$status')");
                    mysqli_query($CONNECTION, "UPDATE sale SET status = '$status' WHERE id = '$sale'");
                }
            }
        }
    }
    function salePriceCalculate($CONNECTION, $id){              // Пересчет стоимости заявки
        $price_purchase = 0;
        $price_sale = 0;
        $sql = mysqli_query($CONNECTION, "SELECT p_id, p_type, count FROM sale_product WHERE sale = '$id'");
        while($data = mysqli_fetch_array($sql)){
            switch($data["p_type"]){
                case 1: $type = "tire"; $param = "price_purchase, price_sale"; break;
                case 2: $type = "disc"; $param = "price_purchase, price_sale"; break;
                case 3: $type = "product"; $param = "price_purchase, price_sale"; break;
                case 4: $type = "service"; $param = "price_1"; break;
            }
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT $param FROM $type WHERE id = '".$data["p_id"]."' "));
            if($data["p_type"] < 4){
                $price_purchase += $data["count"] * $temp["price_purchase"];
                $price_sale += $data["count"] * $temp["price_sale"];
            }
            else{
                $price_sale += $data["count"] * $temp["price_1"];
            }
        }
        mysqli_query($CONNECTION, "UPDATE sale SET price_purchase = '$price_purchase', price_sale = '$price_sale' WHERE id = '$id'");
    }
    function currentDateMonth($date = null){           // Дата с прописным месяцем
        $_monthsList = array(
                "1"=>"января","2"=>"февраля","3"=>"марта",
                "4"=>"апреля","5"=>"мая", "6"=>"июня",
                "7"=>"июля","8"=>"августа","9"=>"сентября",
                "10"=>"октября","11"=>"ноября","12"=>"декабря");

        if($date == null) $date = time();
        $TEXT = date("d", $date)." ".$_monthsList[date("n", $date)]." ".date("Y", $date);
        return $TEXT;
    }
    function transactionAdd($CONNECTION, $sale, $param){    // Новая транзакция
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM sale WHERE id = '$sale'"));
        $price = $data["price_sale"] - $data["skidka_ruble"];
        if($data["oplata"] == 2) $price *= 1.02;
        $price = floor($price);
        if(isset($_COOKIE["CURRENT_BASE"])) $base = $_COOKIE["CURRENT_BASE"];
        else{
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT base FROM user WHERE id = ".ID));
            $base = $temp["base"];
        }
        if($base == 0) $base = $data["base_sale"];
        $client = $data["client"];
        $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT cId FROM client_contact WHERE id = '$client'"));
        $client = $temp["cId"];
        $oplata = $data["oplata"];
        $sale_number = $data["number"];

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM transactions WHERE sale = '$sale_number'"));
        if(!isset($data["id"])){
            $date = time();
            $year = date("y", $date);
            $month = date("m", $date);
            $day = date("d", $date);
            //if($month < 10) $month = "0".$month;
            //if($day < 10) $day = "0".$day;
            $number = $year.$month.$day;
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM transactions WHERE number LIKE '$number%'"));
            $count = $data[0];
            $count++;
            $count = getRight4Number($count);
            $number .= $count;

            mysqli_query($CONNECTION, "INSERT INTO transactions
                (number, base, date, cashier, summa, type, oplata, sale, client) VALUES
                ('$number', '$base', '".$date."', '".ID."', '$price', '$param', '$oplata', '$sale_number', '$client')");
        }
        //echo $number;
    }
    function generate_barcode($CONNECTION){                // Генерирование случайного баркода
        $number = generate_16(10);
        $count = 0;
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM tire WHERE barcode = '$number'"));
        $count += $data[0];
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM disk WHERE barcode = '$number'"));
        $count += $data[0];
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM product WHERE barcode = '$number'"));
        $count += $data[0];
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM service WHERE barcode = '$number'"));
        $count += $data[0];
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM season_temp WHERE barcode = '$number'"));
        $count += $data[0];
        while($count != 0){
            $number = generate_16(10);
            $count = 0;
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM tire WHERE barcode = '$number'"));
            $count += $data[0];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM disk WHERE barcode = '$number'"));
            $count += $data[0];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM product WHERE barcode = '$number'"));
            $count += $data[0];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM service WHERE barcode = '$number'"));
            $count += $data[0];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM season_temp WHERE barcode = '$number'"));
            $count += $data[0];
        }
        return $number;
    }
    function productCountCalculate($CONNECTION, $type, $id){   // Пересчет количества товара
        switch($type){
            case 1: $b = "tire"; break;
            case 2: $b = "disk"; break;
            case 3: $b = "product"; break;
            default: $b = "none";
        }
        if($b != "none"){
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT barcode, id FROM ".$b." WHERE id = '$id'"));
            $id = $data["id"];
            $barcode = $data["barcode"];
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM available WHERE barcode = '$barcode' LIMIT 1"));
            if(isset($data["id"])){
                $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(count) FROM available WHERE barcode = '$barcode'"));
                $count = $data[0];
            }
            else $count = 0;
            mysqli_query($CONNECTION, "UPDATE ".$b." SET count = '$count' WHERE id = '$id'");
        }
    }
    function allStorageCalc($CONNECTION){      // Пересчет количества занятых позиций у всех хранилищ
        mysqli_query($CONNECTION, "DELETE FROM available WHERE count <= 0");
        mysqli_query($CONNECTION, "DELETE FROM available WHERE barcode = ''");
        $sql = mysqli_query($CONNECTION, "SELECT id FROM storage WHERE composite = 0");
        while($data = mysqli_fetch_array($sql)){
            $sId = $data["id"];
            $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT SUM(count) AS count FROM available WHERE storage = '$sId'"));
            $count = (int)$temp["count"];
            mysqli_query($CONNECTION, "UPDATE storage SET occupied = '$count' WHERE id = '$sId'");
        }
        $sql = mysqli_query($CONNECTION, "SELECT id FROM storage WHERE composite = 1");
        while($data = mysqli_fetch_array($sql)){
            $sId = $data["id"];
            storageCalc($CONNECTION, $sId);
        }

    }
    function productMove($CONNECTION, $otkuda, $kuda, $type, $id, $count){   // Перемещение, списание, получение товара без создания движения
        switch($type){
            case 1: $a = "tire"; break;
            case 2: $a = "disk"; break;
            case 3: $a = "product"; break;
        }
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT barcode FROM $a WHERE id = '$id'"));
        $barcode = $data["barcode"];

        if($type == 3){
            if($otkuda > 0){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count, id FROM available WHERE base = '$otkuda' AND barcode = '$barcode'"));
                if(isset($temp["id"])){
                    $aId = $temp["id"];
                    if($temp["count"] >= $count){
                        $count_new = $temp["count"] - $count;
                        if($count_new > 0) mysqli_query($CONNECTION, "UPDATE available SET count = '$count_new' WHERE id = '$aId'");
                        else mysqli_query($CONNECTION, "DELETE FROM available WHERE id = '$aId'");
                    }
                    else return -2; // Не хватает товара на складе либо базе
                }
                else return -1; // Нет товара на складе либо базе
            }

            if($kuda > 0){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, count FROM available WHERE base = '$kuda' AND barcode = '$barcode'"));
                if(isset($temp["id"])){
                    $aId = $temp["id"];
                    $count_new = $temp["count"] + $count;
                    mysqli_query($CONNECTION, "UPDATE available SET count = '$count_new' WHERE id = '$aId'");
                }
                else mysqli_query($CONNECTION, "INSERT INTO available (barcode, base, count) VALUES ('$barcode', '$kuda', '$count')");
            }
            productCountCalculate($CONNECTION, $type, $id);
            return 1;  // Перемещение произошло успешно
        }
        else {
            if($otkuda > 0){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT count, id FROM available WHERE storage = '$otkuda' AND barcode = '$barcode'"));
                if(isset($temp["id"])){
                    $aId = $temp["id"];
                    if($temp["count"] >= $count){
                        $count_new = $temp["count"] - $count;
                        if($count_new > 0) mysqli_query($CONNECTION, "UPDATE available SET count = '$count_new' WHERE id = '$aId'");
                        else mysqli_query($CONNECTION, "DELETE FROM available WHERE id = '$aId'");
                    }
                    else return -2; // Не хватает товара на складе либо базе
                }
                else return -1; // Нет товара на складе либо базе
            }
            if($kuda > 0){
                $temp = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, count FROM available WHERE storage = '$kuda' AND barcode = '$barcode'"));
                if(isset($temp["id"])){
                    $aId = $temp["id"];
                    $count_new = $temp["count"] + $count;
                    mysqli_query($CONNECTION, "UPDATE available SET count = '$count_new' WHERE id = '$aId'");
                }
                else mysqli_query($CONNECTION, "INSERT INTO available (barcode, storage, count) VALUES ('$barcode', '$kuda', '$count')");
            }
            productCountCalculate($CONNECTION, $type, $id);
            return 1;  // Перемещение произошло успешно
        }
    }
    function insertSaleProduct($CONNECTION, $barcode, $type, $id, $param, $count, $otkuda, $sale){    // Добавление товара в заказ
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM sale_product WHERE barcode = '$barcode' AND sale = '$sale' AND otkuda = '$otkuda'"));
        if(isset($data["id"])){
            $temp_id = $data["id"];
            mysqli_query($CONNECTION, "UPDATE sale_product SET
                count = '$count' WHERE id = '$temp_id'");
        }
        else{
            mysqli_query($CONNECTION, "INSERT INTO sale_product (sale, barcode, p_id, p_type, p_param, count, otkuda)
                VALUES ('$sale', '$barcode', '$id', '$type', '$param', '$count', '$otkuda')");
        }
    }
    function nowDateMovementsActive($CONNECTION){     // Обновление статусов на активные у тех движений, у которых запланированная дата сегодня
        $date = date("d.m.Y");
        $date_now = strtotime($date);
        mysqli_query($CONNECTION, "UPDATE movement SET status = 0 WHERE status = -1 AND date_or = '$date_now'");
    }
    function movementsStatProd($CONNECTION, $type, $id){        // Возвращает блок с диаграммой по движениям
        $block = "<div class = 'dpm'>";
        $count_max = 0;
        $sql = mysqli_query($CONNECTION, "SELECT count FROM movement WHERE p_id = '$id' AND p_type = '$type' ORDER BY id DESC LIMIT 10");
        while($data = mysqli_fetch_array($sql)){
            if($data["count"] > $count_max) $count_max = $data["count"];
        }
        $sql = mysqli_query($CONNECTION, "SELECT count FROM movement WHERE p_id = '$id' AND p_type = '$type' ORDER BY id DESC LIMIT 10");
        while($data = mysqli_fetch_array($sql)){
            $block .= "<div class = 'dpm_col' title = '".$data["count"]."'>";
            $b = (int)($data["count"]*15/$count_max);
            if($b > 15) $b = 15;
            if($b < 1) $b = 1;
            $a = 15 - $b;
            $block .= "<div class = 'dpm_block_a' style = 'height: ".$a."px'></div>";
            $block .= "<div class = 'dpm_block_b' style = 'height: ".$b."px'></div>";
            $block .= "</div>";
        }
        $block .= "</div>";
        return $block;
    }
    function getCircleBase($CONNECTION, $code){                 // Возвращает кругляшок с цветом базы
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT color FROM base WHERE code = '$code'"));
        $text = "<circle style = 'background: #".$data["color"]."'></circle>";
        return $text;
    }

?>