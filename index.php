<?php

    require "settings.php";            // Настройки сайта
    require "functions.php";           // Основные функции
    require "vendor/autoload.php";     // Допы


    $FLAG = false;
    $TEMPLATE = file_get_contents("templates/front/temp/basic.html");
    $HEAD = "";
    $BASE = "";
    $MENU = "";
    $ATTENTION = "";
    $TITLE = "";

    // Админка
    if($catA == "cp" AND !$FLAG){
        require "pages/admin/none.php"; // Загрузка начальных данных на всю админку
        switch($catB){
            case "cash"         : require "pages/admin/cash.php"        ; break; // Касса
            case "transactions" : require "pages/admin/transactions.php"; break; // Транзакции
            case "sales"        : require "pages/admin/sales.php"       ; break; // Продажи
            case "tires"        : require "pages/admin/tires.php"       ; break; // Шины
            case "disks"        : require "pages/admin/disks.php"       ; break; // Диски
            case "products"     : require "pages/admin/products.php"    ; break; // Товары
            case "services"     : require "pages/admin/services.php"    ; break; // Услуги
            case "warehouses"   : require "pages/admin/warehouses.php"  ; break; // Склады
            case "movements"    : require "pages/admin/movements.php"   ; break; // Движения
            case "clients"      : require "pages/admin/clients.php"     ; break; // Клиенты
            case "settings"     : require "pages/admin/settings.php"    ; break; // Настройки
            case "map"          : require "pages/admin/ymap.php"        ; break; // Карта
            default             : require "pages/admin/home.php"        ; break; // Главная
        }
        $FLAG = true;
    }

    if(!$FLAG){
        switch($catA){
            case "login"    : require "pages/front/login.php"; break; // Вход
            case "test"     : require "map/test.html";  break; // Тестовая
            case "tires"    : require "bot-front/index.html";  break; // Тестовая
            case "lot"      : require "bot-front/tires.php";  break; // Тестовая
            case "tireshop" : require "bot-front/index-tireshop.ru.html";  break; // Тестовая
            case "api"      : require "bot-front/api/getTires.php";  break; // Тестовая
            case "datamatrix" : require "pages/admin/datamatrix.php"  ; break; // datamatrix
            case "opt"      : require "pages/front/excel.php"; break; // Создание файла для оптовиков
            case "roznitsa" : require "pages/front/excel.php"; break; // Создание файла для розничных клиентов
            default         : require "pages/front/home.php";  break; // Главная страница до входа
        }
        if ($catA != "api") {
            $FLAG = true;
        }
        if ($catA != "datamatrix") {
            $FLAG = true;
        }
    }
    


    if($TECHNICAL_WORKS){
        echo "Сайт временно закрыт";
    }

    //  Наложение шаблонов
    if($FLAG){
        $TEMPLATE = str_replace("%TITLE%",            $TITLE,            $TEMPLATE);
        $TEMPLATE = str_replace("%SCRIPT%",           $SCRIPT,           $TEMPLATE);
        $TEMPLATE = str_replace("%HEAD%",             $HEAD,             $TEMPLATE);
        $TEMPLATE = str_replace("%MENU%",             $MENU,             $TEMPLATE);
        $TEMPLATE = str_replace("%ATTENTION%",        $ATTENTION,        $TEMPLATE);
        $TEMPLATE = str_replace("%BASE%",             $BASE,             $TEMPLATE);
        $TEMPLATE = str_replace("%SERVER%",           $SERVER,           $TEMPLATE);
        echo $TEMPLATE;
    }

?>