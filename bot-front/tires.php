<?php
    require "./settings.php";

    class Tires {
        public $connect = "";
        public $items = array();
        public $sql;
        public $allow_params = array('cureer','date','action','status');
        public $params;
        public $movements_items = array();

        public function __construct($connect,$params) {
            $this->connect  = $connect;    
            $this->params = $params;           
        }

        function getWidth() {
            $sql = "SELECT DISTINCT w FROM tire";
            $width = array();
            $sql_width = mysqli_query($this->connect,$sql);
            while ($w = mysqli_fetch_array($sql_width)) {
                $width[] = intval($w['w']);
            };
            sort($width);
            header('Content-Type: application/json; charset=utf-8');
            return json_encode($width, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
        }

        function getHeight() {
            $sql = "SELECT DISTINCT h FROM tire";
            $height = array();
            $sql_height = mysqli_query($this->connect,$sql);
            while ($h = mysqli_fetch_array($sql_height)) {
                $height[] = intval($h['h']);
            };
            sort($height);
            header('Content-Type: application/json; charset=utf-8');
            return json_encode($height, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
        }

        function getRadius() {
            $sql = "SELECT DISTINCT r FROM tire";
            $radius = array();
            $sql_radius = mysqli_query($this->connect,$sql);
            while ($r = mysqli_fetch_array($sql_radius)) {
                $radius[] = intval($r['r']);
            };
            sort($radius);
            header('Content-Type: application/json; charset=utf-8');
            return json_encode($radius, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
        }
        
        function getBrand() {
            $sql = "SELECT DISTINCT brand FROM tire";
            $brand = array();
            $sql_brand = mysqli_query($this->connect,$sql);
            while ($b = mysqli_fetch_array($sql_brand)) {
                $brand[] = $b['brand'];
            };
            sort($brand);
            header('Content-Type: application/json; charset=utf-8');
            return json_encode($brand, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
        }
        function paginate() {
            echo ceil(mysqli_fetch_array(mysqli_query($this->connect, "SELECT COUNT(id) FROM tire"))[0]/100)-1;
        }

        function load() {
            $sql = "SELECT w,h,r,brand,model,season,nagr,resist,rft,spike,cargo,count,price_sale,photo,price_wholesale FROM `tire` WHERE count>0 and status=1";
            $page = ($this->params['page']!=1) ? $this->params['page']+100 : 0;
            $season = (isset($this->params["season"])) ? $this->params['season'] : -1;
            $w = (isset($this->params["w"])) ? $this->params['w'] : -1;
            $h = (isset($this->params["h"])) ? $this->params['h'] : -1;
            $r = (isset($this->params["r"])) ? $this->params['r'] : '';
            $rft = (isset($this->params["rft"])) ? $this->params['rft'] : -1;
            $spike = (isset($this->params["spike"])) ? $this->params['spike'] : -1;
            $cargo = (isset($this->params["cargo"])) ? $this->params['cargo'] : -1;
            $brand = (isset($this->params["brand"])) ? $this->params['brand'] : -1;
            $model = (isset($this->params["model"])) ? $this->params['model'] : -1;

            $params = array('w'=>$w,'h'=>$h,'rft'=>$rft,'spike'=>$spike,'cargo'=>$cargo,'brand'=>$brand,'model'=>$model);

            foreach ($params as $key => $value){
                $sql.= ($value >= 0) ? " AND $key=$value":"";
            }

            $sql.= " LIMIT $page,100";
            $tires_list = array();
            $sql_tires = mysqli_query($this->connect,$sql);
            while ($tire = mysqli_fetch_array($sql_tires)) {
                $tire['photo'] = array_filter(explode('%-%',$tire['photo']))[0];
                $tires_list[] = $tire;
            };
            
            header('Content-Type: application/json; charset=utf-8');
            return json_encode($tires_list, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
        }
    }

    $tires = new Tires($CONNECTION,$_POST);
    if ($_GET['method']=='tires') {
        echo $tires->load();
    }

    if ($_GET['method']=='width') {
        echo $tires->getWidth();
    }
    if ($_GET['method']=='height') {
        echo $tires->getHeight();
    }
    if ($_GET['method']=='radius') {
        echo $tires->getRadius();
    }
    if ($_GET['method']=='brand') {
        echo $tires->getBrand();
    }
    if ($_GET['method']=='paginate') {
        echo $tires->paginate();
    }
?>