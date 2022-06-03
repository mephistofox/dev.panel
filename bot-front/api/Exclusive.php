<?php
    require "./bot-front/api/utils.php";

    class Exclusive {
        public function __construct($connect) {
            $this->utils = new Utils($connect);
        }

        public function load() {
            $url = "https://b2b.tyres.spb.ru/price/download?file_key=8e6d9ebe2a8c6df3abf77652bd84dedc&good_alias=tyre&format=xml";
            $xml = simplexml_load_string(file_get_contents($url));
            $tires = array();
            $order_arr = array('id','title','brand','model','width','height','diam','load','load_desc','speed','speed_desc','season','spike','runflat','cargo','count','price','price_opt','slug','from','images','supplier','hash');
            foreach ($xml as $tire) {
                $tire_complete['id'] = substr("$tire->brand",0,2).substr("$tire->model",0,2)."$tire->w";
                $tire_complete['title'] = "";
                $tire_complete['brand'] = "$tire->brand";
                $tire_complete['model'] = "$tire->model";
                $tire_complete['width'] = "$tire->w";
                $tire_complete['height'] = "$tire->h";
                $tire_complete['diam'] = "$tire->d";
                $tire_complete['load'] = "$tire->index_load";
                $tire_complete['load_desc'] = $this->utils->index_load_description($tire_complete['load']);
                $tire_complete['speed'] = "$tire->index_speed";
                $tire_complete['speed_desc'] = $this->utils->index_speed_description($tire_complete['speed']);
                $tire_complete['season'] = ("$tire->season"=="Летняя")?"S":"W";
                $tire_complete['spike'] = ("$tire->spikes"=="Да")?1:0;
                $tire_complete['runflat'] = strpos("$tire->title"," Run Flat")?1:0;
                $tire_complete['cargo'] = strpos("$tire->title",' C ')?1:0;
                $tire_complete['title'] = sprintf("%s %s %s/%sR%s %s%s", $tire->brand, $tire->model,$tire->w, $tire->h, ($tire_complete['cargo'])?$tire->d.'С':$tire->d, $tire->index_load, $tire->index_speed);
                $tire_complete['count'] = (intval("$tire->Количество")>20)?20:intval("$tire->Количество");
                $tire_complete['price'] = $this->utils->priceCalculate("$tire->Розница")['price'];
                $tire_complete['price_opt'] = $this->utils->priceCalculate("$tire->Розница")['price_opt'];
                $tire_complete['slug'] = $this->utils->createAlias($tire_complete['model']);
                $stores = array("МСК"=>0,"СПБ"=>0);
                foreach ($tire->shops->shop as $shop) {
                    if (strpos( "$shop->address", "Москва," ) !== false) {
                        $stores['МСК'] = (intval("$shop->storeQuantity")) ? $stores['МСК']+intval("$shop->storeQuantity") : 0;
                    } 
                    if (!strpos( "$shop->address", "Москва," ) !== false) {
                        $stores['СПБ'] = (intval("$shop->storeQuantity")) ? $stores['СПБ']+intval("$shop->storeQuantity") : 0;
                    }
                }
                foreach ($stores as $key => $value) {
                    if ($value==0){ 
                        unset($stores[$key]);
                    };
                }
                $tire_complete['id'] = $this->utils->createArticul('ex',$tire_complete);
                $tire_complete['from'] = $stores;
                $tire_complete['images'] = $this->utils->getImages($tire_complete['slug']);
                $tire_complete['supplier'] = "3";
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
    