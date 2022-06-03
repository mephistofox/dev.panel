<?php

require "./settings.php";
require "./bot-front/api/utils.php";
require "./bot-front/api/DBController.php";
require "./bot-front/api/Tireshop.php";
require "./bot-front/api/Shinservice.php";
require "./bot-front/api/Exclusive.php";

class Tires {
    public $tires = array();
    public $allowed_params = array('width','height','diam','load','speed','brand','model','season','rft','spike','cargo','count');

    public function __construct($connect,$params) {
        $this->connect  = $connect;    
        $this->allowed = array();
        $this->utils = new Utils($connect);
        $this->tirebase = new Tirebase();
        foreach ($params as $key => $value) {
            (in_array($key, $this->allowed_params)) ? $this->allowed[$key]=$value : false;
        }
    }

    public function update(){
        $tireshop = new Tireshop($this->connect);
        $shinservice = new Shinservice($this->connect);
        $exclusive = new Exclusive($this->connect);
        $tires = array_merge($tireshop->load(),$shinservice->load(),$exclusive->load());
        $tsql = "INSERT OR REPLACE INTO tires ";
        $keys = array();
        $vals = array();
        foreach ($tires as $tire) {
            $values = array();
            $st = "";
            foreach ($tire['from'] as $store => $col) {
                $st.="'$store':'$col',";
            }
            unset($tire['from']);
            $tire['from'] = '{'.rtrim($st,',').'}';
            foreach ($tire as $key => $value) {
                (!in_array($key,$keys))?$keys[]=$key:false;
                if (is_array($value)) { $value = implode(',',$value); }
                $values[]=SQLite3::escapeString("$value");
            }
            $vals[]="('".implode("','",$values)."')";
        }
        $tsql.="(`".implode("`,`",$keys)."`) VALUES ".implode(",",$vals);
        $this->tirebase->db->query("DELETE FROM sqlite_sequence");
        $this->tirebase->db->query("DELETE FROM tires");
        echo "Трунь";
        $this->tirebase->db->query($tsql);
        echo "Tirebase has been update!";
    }

    public function get(){
        $sql_arr = array();
        foreach ($this->allowed as $key => $value) {
            $val = $this->connect->real_escape_string($value);
            $sql_arr[] = "`$key`='$val'";
        }
        $tires = array();
        $sql = "SELECT * FROM tires";
        $sql .= (!empty($sql_arr)) ? " WHERE ".implode(' AND ',$sql_arr):"";
        $sql_tires = $this->tirebase->db->query($sql);
        while ($tire = $sql_tires->fetchArray()) {
            (!is_array($tire['images']))?$tire['images'] = explode(',',$tire['images']):false;
            $tire['from'] = json_decode(str_replace("'",'"',$tire['from']));
            foreach ($tire as $key => $value) {if (is_numeric($key)) {unset($tire[$key]);}}
            $tires[]=$tire;
        }
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header("Access-Control-Allow-Headers: X-Requested-With");
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($tires, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES);
    }
    
}

if ($_GET) {
    $tires = new Tires($CONNECTION,$_GET);
    if (isset($_GET['method'])) {
        switch ($_GET['method']) {
            case 'update':
                echo $tires->update();
                break;
            case 'get':
                echo $tires->get();
                break;
            default:
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, POST');
                header("Access-Control-Allow-Headers: X-Requested-With");
                header('HTTP/1.1 405 Method Not Allowed');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message'=> 'error','decription' => 'The method is not allowed for the requested URL', 'code' => 405)));
                break;
        }
    }
}


?>