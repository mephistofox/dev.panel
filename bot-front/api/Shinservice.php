<?php
    require "./bot-front/api/utils.php";

    class Shinservice {
        public function __construct($connect) {
            $this->utils = new Utils($connect);
        }

        public function load() {
            $url = "https://duplo.shinservice.ru/xml/shinservice-b2b-15.xml?id=89342285";
            $xml = simplexml_load_string(file_get_contents($url));
            $tires = array();
            $order_arr = array('id','title','brand','model','width','height','diam','load','load_desc','speed','speed_desc','season','spike','runflat','cargo','count','price','price_opt','slug','from','images','supplier','hash');
            foreach ($xml->tires->tire as $tire) {
                $tire = $tire->attributes();
                $tire_complete['id'] = "";
                $tire_complete['title'] = sprintf("%s %s %s/%s%s %s%s", $tire->brand, $tire->model,$tire->width, $tire->profile, $tire->diam, $tire->load, $tire->speed);
                $tire_complete['brand'] = "$tire->brand";
                $tire_complete['model'] = "$tire->model";
                $tire_complete['width'] = "$tire->width";
                $tire_complete['height'] = "$tire->profile";
                $tire_complete['diam'] = str_replace('R','',"$tire->diam");
                $tire_complete['load'] = "$tire->load";
                $tire_complete['load_desc'] = $this->utils->index_load_description($tire_complete['load']);
                $tire_complete['speed'] = "$tire->speed";
                $tire_complete['speed_desc'] = $this->utils->index_speed_description($tire_complete['speed']);
                $tire_complete['season'] = "$tire->season";
                $tire_complete['spike'] = ("$tire->pin"=="Y")?1:0;
                $tire_complete['runflat'] = ("$tire->runflat"=="Y")?1:0;
                $tire_complete['cargo'] = (strpos('C',"$tire->diam"))?1:0;
                $tire_complete['diam'] = str_replace('C','',$tire_complete['diam']);
                $tire_complete['count'] = ("$tire->stock">20)?20:"$tire->stock";
                $tire_complete['price'] = $this->utils->priceCalculate("$tire->price")['price'];
                $tire_complete['price_opt'] = $this->utils->priceCalculate("$tire->price")['price_opt'];
                $tire_complete['slug'] = $this->utils->createAlias($tire_complete['model']);
                $tire_complete['from'] = array('СПБ'=>$tire_complete['count']);
                $tire_complete['id'] = $this->utils->createArticul('sh',$tire_complete);
                $tire_complete['images'] = $this->utils->getImages($tire_complete['slug']);
                $tire_complete['supplier'] = "2";
                $tire_complete['hash'] = md5($tire_complete['title']);
                $tire_sorted = array();
                foreach ($order_arr as $key) {
                    $tire_sorted[$key] = $tire_complete[$key];
                }
                ($tire_complete['count']>0 and $tire_complete['images']!==false)?$tires[] = $tire_sorted:false;
            } 
            return $tires;
        }
    }
    