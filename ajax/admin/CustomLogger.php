<?php

class CustomLogger
{
    protected $user;
    public $db;
    public $type; //category
    public $id; //product id
    public $tmp = ['%date%', '%user%', '%oldB%', '%oldG%','%oldR%','%newB%','%newG%','%newR%'];
    public function __construct($id, $type, $DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME){
        $this->db = new mysqli($DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME);
        $this->db->set_charset('utf8');
        $data = mysqli_fetch_assoc($this->db->query("SELECT name, surname, root FROM user WHERE id = ".clean($_COOKIE["id"])));
        $this->user = $data['name'].' '.$data['surname'];
        $this->type = $type;
        $this->id = $id;
    }

    public function addLogEntry($params){
        $month = date('Ym');
        $date = date("Y-m-d H:i:s");
        $logFile = '../../logs/'.$this->type.'/'.$this->id.'/'.$month.'.json';
        if(file_exists($logFile)){
            $logs = json_decode(file_get_contents($logFile), true);
        }
        $entry = [
            'user'=>$this->user,
            'oldBuyout'=>$params['b'],
            'oldGross'=>$params['g'],
            'oldRetail'=>$params['r'],
            'newBuyout'=>$params['price_purchase'],
            'newGross'=>$params['price_wholesale'],
            'newRetail'=>$params['price_sale']
        ];
        $logs[$date] = $entry;
        if(!is_dir('../../logs')){
            mkdir('../../logs');
        }
        if(!is_dir('../../logs/'.$this->type)){
            mkdir('../../logs/'.$this->type);
        }
        if(!is_dir('../../logs/'.$this->type.'/'.$this->id)){
            mkdir('../../logs/'.$this->type.'/'.$this->id);
        }

        file_put_contents($logFile, json_encode($logs, 64|256));

    }

    public function getLogs(){
        $logDir = '../../logs/'.$this->type.'/'.$this->id;
        $month = date('Ym');
        $thisMonthsLog = $logDir.'/'.$month.'.json';
        if(is_dir($logDir)){
            $logFiles = scandir($logDir);
        }else{
            exit();
        }
        if(is_array($logFiles)){
            unset($logFiles['.']);
            unset($logFiles['..']);
        }else{
            exit();
        }
        $total = [];
        $monthLog = [];
        foreach($logFiles as $logFile){
            if($logFile != '.' && $logFile != '..' && $logFile != 'total.json'){
                $monthlyLog = json_decode(file_get_contents($logDir.'/'.$logFile), true);
                $total[] = $monthlyLog;
            }
        }
        file_put_contents($logDir.'/total.json', json_encode($total, 64|256));
        $block = file_get_contents('../../templates/admin/temp/customlogblock.tpl');
        $html = '';
        if(file_exists($thisMonthsLog)){
            $arrThisMonthsLog = json_decode(file_get_contents($thisMonthsLog), true);
            foreach($arrThisMonthsLog as $entryDate=>$entry){
                array_unshift($entry, $entryDate);
                $html .= str_replace($this->tmp, $entry, $block);
            }
        }



        return $html;
    }

    public function __destruct(){
        unset($this->id);
        unset($this->type);
    }


}