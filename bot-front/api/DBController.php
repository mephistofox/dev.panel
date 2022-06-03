<?php
    class Tirebase {
        public function __construct() {
            $path = "./bot-front/api/tirebase.db";
            if(!file_exists($path)){
                $this->db = new SQLite3($path);
                $sql="CREATE TABLE tires(
                    `index` INTEGER PRIMARY KEY AUTOINCREMENT,
                    supplier TEXT,
                    id TEXT,
                    title TEXT,
                    brand TEXT,
                    model TEXT,
                    width TEXT,
                    height TEXT,
                    diam TEXT,
                    load TEXT,
                    load_desc TEXT,
                    speed TEXT,
                    speed_desc TEXT,
                    season TEXT,
                    spike TEXT,
                    runflat TEXT,
                    cargo TEXT,
                    count TEXT,
                    price TEXT,
                    price_opt TEXT,
                    slug TEXT,
                    images TEXT,
                    `from` TEXT,
                    hash TEXT
                );";
                $this->db->query($sql);
            }else{
                $this->db = new SQLite3($path);
            }
        }
    }
    