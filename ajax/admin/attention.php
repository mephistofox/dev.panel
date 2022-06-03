<?php

    require "../../settings.php";
    require "../../functions.php";
    proof();
    class Attention {
        public $connect = "";
        public $items = array();
        public $sql;
        public $allow_params = array('cureer','date','action','status');
        public $params;

        public function __construct($connect,$params) {
            $this->connect  = $connect;    
            $this->params = $params;           
        }

        function getCoords($address){
            $cache_coords = mysqli_fetch_array(mysqli_query($this->connect, "SELECT * FROM coords_tmp WHERE address = '$address'"));
            if (!isset($cache_coords[0])) {
                $a = urlencode("г Санкт-Петербург, ".$address);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://geocode-maps.yandex.ru/1.x?apikey=4280c083-e2dc-4345-b347-7a13d40bc5d2&geocode=$a&format=json");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $output = curl_exec($ch);
                curl_close($ch);
                $data = json_decode($output)->response->GeoObjectCollection->featureMember;
                if ($data[0]) {
                    $coords = $data[0]->GeoObject->Point->pos;
                    mysqli_query($this->connect, "INSERT INTO coords_tmp (coords,address) VALUES('$coords','$address')");
                    // var_dump(mysqli_query($this->connect, "INSERT INTO coords_tmp (coords,address) VALUES($coords,$address)"));
                    return $coords;
                } else {
                    return '';
                }
            } else {
                $coords = $cache_coords['coords'];
                return $coords;
            }
        }

        function getCureers(){
            $sql = mysqli_query($this->connect, "SELECT id, name, surname FROM user WHERE type = 5");
            $cureers = array();
            while($cureer = mysqli_fetch_array($sql)){
                $cureers[] = array('id'=>$cureer['id'],'name'=>$cureer['name'].' '.$cureer['surname']);
            }
            return json_encode($cureers, JSON_UNESCAPED_UNICODE);
        }

        function item_parse_name($item) {
            switch($item["p_type"]){
                case 1: $table = "tire"; break;
                case 2: $table = "disk"; break;
                case 3: $table = "product"; break;
                case 4: $table = "service"; break;
                case 5: $table = "season_temp"; break;
            }
            $item_content = mysqli_fetch_array(mysqli_query($this->connect, "SELECT * FROM $table WHERE id=".$item["p_id"]));
            switch($item["p_type"]){
                case 1: $item_name = "Шина ".$item_content["brand"]." ".$item_content["model"]." ".$item_content["w"]."/".$item_content["h"]."R".$item_content["r"]; break;
                case 2: $item_name = "Диск ".$item_content["nomenclature"]; break;
                case 3: $item_name = "Товар ".$item_content["name"]; break;
                case 4: $item_name = "Услуга ".$item_content["name"]; break;
                case 5: $item_name = "Услуга Шинка"; break;
            }
            return $item_name;
        }

        // All items to json
        function itemDataProcessing ($item) {
            if ($item) {
                $manager_id = (array_key_exists("manager", $item)) ? $item["manager"] : '';
                $manager = mysqli_fetch_array(mysqli_query($this->connect, "SELECT name, surname FROM user WHERE id = '$manager_id'"));
                $compile_item = array();
                $compile_item['type'] = '';
                $compile_item['index'] = "P".$item['number'];
                $compile_item['id'] = $item["id"];
                $compile_item['deal'] = (array_key_exists("sale", $item)) ? "P".getRight8Number($item['sale']) : '';
                $compile_item['from'] = (array_key_exists("otkuda", $item)) ? explode(" - ", $item["otkuda"])[0] : '';
                $compile_item['to'] = (array_key_exists("kuda", $item)) ? explode(" - ", $item["kuda"])[0] : '';
                $compile_item['store'] = '';
                $compile_item['base'] = (array_key_exists("otkuda", $item)) ? $item["otkuda"] : '';
                $store = mysqli_fetch_array(mysqli_query($this->connect, "SELECT otkuda FROM sale_product WHERE sale='".$compile_item['id']."'"));

                $compile_item['recived'] = (array_key_exists("action", $item) and $item["action"] == 3) ? 'Принято на склад':false;
                $compile_item['manager'] = ($manager)?$manager['surname']." ".$manager['name']:'';
                $compile_item['client_phone'] = (array_key_exists("client_phone",$item)) ? $item["client_phone"] : '';
                $compile_item['client_name'] = (array_key_exists("client_name",$item)) ? $item["client_name"] : '';
                $compile_item['client'] = $compile_item['client_name'].$compile_item['client_phone'];
                if (isset($item['date_or'])) {
                    $compile_item['item_date'] = (array_key_exists("date_or",$item)) ? date('d.m.Y', intval($item["date_or"])) : date('d.m.Y', intval($item["date"]));
                }
                if (isset($item['date_plan'])) {
                    $compile_item['item_date'] = (array_key_exists("date_plan",$item)) ? date('d.m.Y', intval($item["date_plan"])) : $compile_item['item_date'];
                }
                $compile_item['info'] = (array_key_exists("info",$item)) ? $item["info"] : '';
                $compile_item['info2'] = (array_key_exists("track",$item)) ? $item["track"] : '';
                $compile_item['items'] = array();
                $compile_item['provider'] = '';
                if (array_key_exists('p_type',$item)){    
                    $compile_item['items'][] = $this->item_parse_name($item)." (".$item['count']."шт.)";
                    $compile_item['articul'] = '';
                    $compile_item['type'] = 'Перемещение';
                    $compile_item['data'] = 'movement';
                } else {
                    $movement_sale = mysqli_query($this->connect, "SELECT * FROM sale_product WHERE sale=".$compile_item['id']);
                    while($i = mysqli_fetch_array($movement_sale)){
                        // $storage = mysqli_fetch_array(mysqli_query($this->connect, "SELECT name FROM storage WHERE code='".$storage."'"));
                        $compile_item['items'][]= $this->item_parse_name($i)." (".$i['count']."шт. ".$i['otkuda'].")";
                    }
                    $compile_item['articul'] = (array_key_exists("number", $item)) ? $item["number"] : '';
                    $compile_item['type'] = 'Доставка';
                    $compile_item['data'] = 'sale';
                }
                if (array_key_exists('delivery',$item)) {
                    $delivery = mysqli_fetch_array(mysqli_query($this->connect, "SELECT name,address FROM delivery WHERE id = ".$item["delivery"]));
                    $compile_item['provider'] = ($delivery)?$delivery[0]:'';
                    $compile_item['to'] = ($delivery)?$delivery['address']:'';
                    $compile_item['type'] = ($compile_item['provider'])?'Отправка':$compile_item['type'];
                    $compile_item['data'] = 'sale';
                }
                
                $address = (array_key_exists("vydacha",$item)) ? $item['vydacha'] : mysqli_fetch_array(mysqli_query($this->connect, "SELECT * FROM `provider` WHERE name ='".$item["otkuda"]."'"))["address"];
                $compile_item['to'] = ($address) ? $address : $compile_item['to'];
                
                $is_base_to = mysqli_fetch_array(mysqli_query($this->connect, "SELECT * FROM base WHERE code='".$compile_item['to']."'"));
                $is_base_from = mysqli_fetch_array(mysqli_query($this->connect, "SELECT * FROM base WHERE code='".$compile_item['from']."'"));
                if ($is_base_to) {
                    $compile_item['to'] = $is_base_to['address'];
                }

                if ($is_base_from) {
                    $compile_item['from'] = $is_base_from['address'];
                }

                $a = ($compile_item['data']=='movement') ? $compile_item['from'] : $compile_item['to'];
                $compile_item['coords'] = $this->getCoords($a);

                return $compile_item;
            }
        }

        // Get movements and sale lists
        function getMovements() {
            $movements_list = mysqli_query($this->connect, "SELECT * FROM movement WHERE cureer='' AND status<1 AND action>2");
            $sales_list = mysqli_query($this->connect, "SELECT * FROM sale WHERE cureer='' AND poluchenie LIKE 'Доставка%'");
            $sales_list2 = mysqli_query($this->connect, "SELECT * FROM sale WHERE cureer='0' AND poluchenie LIKE 'Доставка%'");
            
            while ($movement = mysqli_fetch_array($movements_list)) {
                $movements_items[] = $this->itemDataProcessing($movement);
            };
            
            while ($movement = mysqli_fetch_array($sales_list)) {
                $movements_items[] = $this->itemDataProcessing($movement);
            };
            while ($movement = mysqli_fetch_array($sales_list)) {
                $movements_items[] = $this->itemDataProcessing($movement);
            };

            header('Content-Type: application/json; charset=utf-8');
            return json_encode($movements_items, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
        }

        function getReserved() {
            // $movements_list = mysqli_query($this->connect, "SELECT * FROM movement WHERE cureer = '' AND status=0 AND action > 2 ORDER BY date DESC");
            $movements_list = mysqli_query($this->connect, "SELECT * FROM movement WHERE status=2 ORDER BY date DESC");
            $sales_list = mysqli_query($this->connect, "SELECT * FROM sale WHERE status=2 ORDER BY date_plan DESC");
            
            $movements_items = array();
            
            while ($movement = mysqli_fetch_array($movements_list)) {
                $movements_items[] = $this->itemDataProcessing($movement);
            };

            while ($movement = mysqli_fetch_array($sales_list)) {
                $movements_items[] = $this->itemDataProcessing($movement);
            };
            header('Content-Type: application/json; charset=utf-8');
            return json_encode($movements_items, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
        }
        // Create temporary movements list 
        function createTmpMovements ($items,$cureer) {
            $sql = '';
            mysqli_query($this->connect, "DELETE FROM movements_tmp WHERE cureer='$cureer';");

            for ($i=0; $i<count($items); $i=$i+1){
                $item = $items[$i];
                $id = $item['id'];
                $cureer = $item['cureer'];
                $number = $item['number'];
                $type = $item['type'];
                $sql .= "INSERT INTO movements_tmp (id,number,cureer,type) VALUES ('$id','$number','$cureer','$type');";
            }
            mysqli_multi_query($this->connect, $sql);
        }
        // Get temporary movements list 
        function getTmpMovements ($cureer=false) {
            $sqltmp="SELECT * FROM movements_tmp";

            if ($cureer == "Не закреплён" or !$cureer) {
                $sqltmp .= "";
            } else {
                $sqltmp .= " WHERE cureer=".$cureer;
            }

            $tmp_movements_list = mysqli_query($this->connect, $sqltmp);

            if ($tmp_movements_list) {
                $cureer_list = array();
                while ($movement = mysqli_fetch_array($tmp_movements_list)) {
                    $index = $movement['number'];
                    $table = $movement['type'];
                    $id = $movement['id'];
                    $sql = "SELECT * FROM $table WHERE id='$id'";
                    $item = mysqli_fetch_array(mysqli_query($this->connect,$sql));
                    $cureer_list[] = $this->itemDataProcessing($item);
                };
                header('Content-Type: application/json; charset=utf-8');
                return json_encode($cureer_list, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
            }
        }
        // Assign a courier for delivery
        function setCureers($items) {
            for ($i=0; $i < count($items); $i++) { 

                $item = $items[$i]; 
                $table = $item['table'];
                $cureer = $item['cureer'];
                $cureer_id = $item['cureer_id'];
                $number = $item['number'];
                $sql = "UPDATE $table SET cureer='$cureer' WHERE number='$number';";
                mysqli_query($this->connect, $sql);
                if ($table = 'sale') {
                    $sql = "UPDATE $table SET cureer_id='$cureer_id' WHERE number='$number';";
                    mysqli_query($this->connect, $sql);
                }

                var_dump(mysqli_query($this->connect, $sql));
                
                if ($item['cureer_id']) {
                    mysqli_query($this->connect, 'DELETE FROM movements_tmp WHERE number="'.$item['number'].'";');
                }

            }
        }
    }
    
    $movements = new Attention($CONNECTION,$_GET);

    if ($_POST) {
        if($_POST['methodName'] == "createTmpMovements"){ 
            echo $movements->createTmpMovements($_POST['items'],$_POST['cureer']);
        }
        if($_POST["methodName"] == "getReserved"){ 
            echo $movements->getReserved();
        }
        if($_POST["methodName"] == "getCureers"){ 
            echo $movements->getCureers();
        }
    
        if($_POST["methodName"] == "setCureers"){ 
            echo $movements->setCureers($_POST['items']);
        }
        if($_POST["methodName"] == "getTmpMovements"){ 
            echo (array_key_exists('cureer',$_POST)) ? $movements->getTmpMovements($_POST['cureer']) : $movements->getTmpMovements();
        }    
    }
    if($_GET["methodName"] == "getMovements"){
        echo $movements->getMovements();
    }
?>