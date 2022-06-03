<?php

    class Utils {  
        public function __construct($connect) {
            require "./settings.php";
            $this->connect  = $connect;    
        }
        public function createAlias($model){
            $alias_clear = array(' xl',' rft',' runflat',' lt', ' suv', ' ', '-','/','*','+');
            $alias = str_replace($alias_clear,'',strtolower($model));
            return $alias;
        }

        public function createArticul($shop,$tire){
            $articul = $shop.substr($tire['brand'],0,2).substr($tire['model'],0,2).$tire['width'].$tire['height'].$tire['diam'].$tire['load'].$tire['speed'];
            return strtoupper($articul);
        }
        
        public function calculate($price,$gross){
            $c = ceil(((ceil($price/100)*100)+ceil($price*($gross/100)))/100)*100;
            return $c;
        }

        public function index_speed_description($speed){
            $sql = mysqli_query($this->connect,"SELECT * FROM `index_speed` WHERE `index`='$speed'");
            $index = ($sql)?mysqli_fetch_array($sql):array();
            return (isset($index['speed']))?$index['speed']:$speed;
        }

        public function index_load_description($load){
            $sql = mysqli_query($this->connect,"SELECT * FROM `index_load` WHERE `index`='$load'");
            $index = ($sql)?mysqli_fetch_array($sql):array();
            return (isset($index['load']))?$index['load']:$load;
        }

        public function getImages($slug){
            $sql = mysqli_query($this->connect,"SELECT model,images FROM `images` WHERE slug='$slug'");
            $images = ($sql)?mysqli_fetch_array($sql):array();
            $img = (isset($images['images']))?explode(';',$images['images']):array();
            $imgs = array();
            foreach ($img as $key => $value) {
                $imgs[] = "https://panel.tiredrop.ru/images/".$images['model'].'/'.$value;
            }
            return (isset($images['images']))?$imgs:false;
        }
        
        public function priceCalculate($price){
            $gross = json_decode(mysqli_fetch_array(mysqli_query($this->connect,"SELECT setting FROM `third_party_settings`"))['setting']);
            $price = intval($price);
            $price_perc = intval($gross->retail);
            $opt_price_perc = intval($gross->gross);
            // var_dump($gross);
            return array("price"=>$this->calculate($price,$price_perc),"price_opt"=>$this->calculate($price,$opt_price_perc));
        }
    }
?>