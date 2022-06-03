<?php

require "./bot-front/api/utils.php";

class Tireshop {
    public function __construct($connect) {
        $this->connect  = $connect;  
        $this->utils = new Utils($connect);
    }

    public function load(){
        $_tires = array();
        $tires_list = array();
        $new_key_name = array('w'=>'width','h'=>'height','r'=>'diam','rft'=>'runflat','nagr'=>'load','resist'=>'speed','price_sale'=>'price','price_wholesale'=>'price_opt');
        $order_arr = array('id','title','brand','model','width','height','diam','load','load_desc','speed','speed_desc','season','spike','runflat','cargo','count','price','price_opt','slug','from','images','supplier','hash');
        $sql = "SELECT barcode,w,h,r,brand,model,season,nagr,resist,rft,spike,cargo,count,photo,price_sale,price_wholesale FROM `tire` WHERE count>0 and status=1";
        $sql_tires = mysqli_query($this->connect,$sql);
        while ($tire = mysqli_fetch_array($sql_tires)) {
            unset($tire['photo']);
            foreach ($new_key_name as $key => $value) {
                $tire[$value] = $tire[$key];
                unset($tire[$key]);
            }
            $tire['id'] = '';
            $tire['supplier'] = '1';
            $tire['spike'] = ($tire['spike']==0)?0:1;
            $tire['runflat'] = ($tire['runflat']==0)?0:1;
            $tire['cargo'] = ($tire['cargo']==0)?0:1;
            $tire['slug'] = $this->utils->createAlias($tire['model']);
            $tire['diam'] = str_replace('C','',$tire['diam']);
            $tire['title'] = sprintf("%s %s %s/%sR%s %s%s", $tire['brand'], $tire['model'], $tire['width'], $tire['height'], $tire['diam'], $tire['load'], $tire['speed']);
            // $tire['from'] = $this->getTireshopBase($tire['barcode']);
            $tire['hash'] = md5($tire['title']);
            $tire['count'] = ($tire['count']>20)?20:$tire['count'];
            $tire['from'] = array("СПБ"=>$tire['count']);
            $tire['images'] = $this->utils->getImages($tire['slug']);
            $tire['speed_desc'] = $this->utils->index_speed_description($tire['speed']);
            $tire['load_desc'] = $this->utils->index_load_description($tire['load']);
            switch ($tire['season']) {
                case '0':
                    $tire['season'] = 'W';
                    break;
                case '1':
                    $tire['season'] = 'S';
                    break;
                default:
                    $tire['season'] = 'A';
                    break;
            }

            $tire_sorted = array();

            
            foreach ($order_arr as $key) {
                $tire_sorted[$key] = $tire[$key];
            }
            
            $tire_sorted['id'] = $this->utils->createArticul('ts',$tire_sorted);
            ($tire['from'] and $tire['images'] !== false)?$tires_list[] = array_filter($tire_sorted, function($key){return !is_numeric($key);},ARRAY_FILTER_USE_KEY):false;
        }
        
        return $tires_list;
    }
}


?>