<?php

    $MENU = file_get_contents("templates/admin/temp/menu.html");
    $HEAD .= "<link rel = \"stylesheet\" type = \"text/css\" href = \"".$SERVER."templates/admin/css/menu.css?4dв\" />";

    $MENU_LEFT = "";
    $MENU_RIGHT = "";

    $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT name, surname, root FROM user WHERE id = ".ID));
    $name = mb_substr($data["name"], 0, 1, 'UTF-8');
    $surname = mb_substr($data["surname"], 0, 1, 'UTF-8');
    $root = $data["root"];

    //if($root[0] == 1) $MENU_LEFT .= "<a id = \"menu_orders\"    href = \"".$SERVER."cp/orders\"           class = \"menu_left_item\">Заявки</a>";
    //if($root[1] == 1) $MENU_LEFT .= "<a id = \"menu_customers\" href = \"".$SERVER."cp/customers\"        class = \"menu_left_item\">Заказчики</a>";
    //if($root[2] == 1) $MENU_LEFT .= "<a id = \"menu_carriers\"  href = \"".$SERVER."cp/carriers\"         class = \"menu_left_item\">Перевозчики</a>";
    //if($root[3] == 1) $MENU_LEFT .= "<a id = \"menu_expenses\"  href = \"".$SERVER."cp/expenses/office\"  class = \"menu_left_item\">Расходы</a>";
    //if($root[4] == 1) $MENU_LEFT .= "<a id = \"menu_reports\"   href = \"".$SERVER."cp/reports\"          class = \"menu_left_item\">Отчеты</a>";
    $temp = 5;
    //for($i = 0; $i < 5; $i++) if($root[$i] == 1){
    //    $temp = $i;
    //    break;
    //}
    if($catB == ""){
        switch($temp){
            case 0 : header("Location: ".$SERVER."cp/sales"); break;
            case 1 : header("Location: ".$SERVER."cp/customers"); break;
            case 2 : header("Location: ".$SERVER."cp/carriers"); break;
            case 3 : header("Location: ".$SERVER."cp/expenses/office"); break;
            //case 4 : header("Location: ".$SERVER."cp/reports"); break;
            default: header("Location: ".$SERVER."cp/settings"); break;
        }
    }

    $TEXT = "";

    $OTL = "";
    if(isset($_COOKIE["prod"])){
        $mas = explode("X", $_COOKIE["prod"]);
        $count = count($mas) - 1;
        $OTL = "<div id = 'sales_head_otl' class='sales_head_otl'>".$count."</div>";
    }
    $TEXT = str_replace("%OTL%", $OTL, $TEXT);

    if(TYPE == 1){
        $MENU_LEFT .= "
            <a id = \"menu_transactions\"  href = \"".$SERVER."cp/transactions\"     class = \"menu_left_item_2\">Операции</a>
            <a id = \"menu_cash\"          href = \"".$SERVER."cp/cash\"             class = \"menu_left_item\">Касса</a>
            <a id = \"menu_sales\"         href = \"".$SERVER."cp/sales\"            class = \"menu_left_item\">Продажи</a>
            <a id = \"menu_tires\"         href = \"".$SERVER."cp/tires\"            class = \"menu_left_item\">Шины</a>
            <a id = \"menu_disks\"         href = \"".$SERVER."cp/disks\"            class = \"menu_left_item\">Диски</a>
            <a id = \"menu_products\"      href = \"".$SERVER."cp/products\"         class = \"menu_left_item\">Товары</a>
            <a id = \"menu_services\"      href = \"".$SERVER."cp/services\"         class = \"menu_left_item\">Услуги</a>
            <a id = \"menu_movements\"     href = \"".$SERVER."cp/movements\"        class = \"menu_left_item_2\">Движения</a>
            <a id = \"menu_warehouses\"    href = \"".$SERVER."cp/warehouses\"       class = \"menu_left_item\">Склады</a>

            <a id = \"menu_clients\"       href = \"".$SERVER."cp/clients\"          class = \"menu_left_item\">Клиенты</a>
            <a id = \"menu_settings\"      href = \"".$SERVER."cp/settings\"         class = \"menu_left_item\">Настройки</a>

            <div id = \"sales_head_button\" onClick = \"windowSaleAdd();\" class = \"menu_left_item btn-basket\">".$OTL."</div>
            <div onClick = \"clearBasket();\" class = \"menu_left_item btn-trash\" ><img class='image-menu' style=\"cursor:pointer;\" src=\"/trash-can.png\"></div>"
            
            ;
            
    }
    if(TYPE == 2 || TYPE == 4){
        if($root[10] == 1) $MENU_LEFT .= "<a id = \"menu_transactions\"  href = \"".$SERVER."cp/transactions\"     class = \"menu_left_item_2\">Операции</a>
            <a id = \"menu_cash\"          href = \"".$SERVER."cp/cash\"             class = \"menu_left_item\">Касса</a>";
        if($root[0] == 1) $MENU_LEFT .= "<a id = \"menu_sales\"         href = \"".$SERVER."cp/sales\"            class = \"menu_left_item\">Продажи</a>";
        if($root[1] == 1) $MENU_LEFT .= "<a id = \"menu_tires\"         href = \"".$SERVER."cp/tires\"            class = \"menu_left_item\">Шины</a>";
        if($root[2] == 1) $MENU_LEFT .= "<a id = \"menu_disks\"         href = \"".$SERVER."cp/disks\"            class = \"menu_left_item\">Диски</a>";
        if($root[3] == 1) $MENU_LEFT .= "<a id = \"menu_products\"      href = \"".$SERVER."cp/products\"         class = \"menu_left_item\">Товары</a>";
        if($root[4] == 1) $MENU_LEFT .= "<a id = \"menu_services\"      href = \"".$SERVER."cp/services\"         class = \"menu_left_item\">Услуги</a>";
        if($root[6] == 1) $MENU_LEFT .= "<a id = \"menu_movements\"     href = \"".$SERVER."cp/movements\"        class = \"menu_left_item_2\">Движения</a>";
        if($root[5] == 1) $MENU_LEFT .= "<a id = \"menu_warehouses\"    href = \"".$SERVER."cp/warehouses\"       class = \"menu_left_item\">Склады</a>";
        if($root[7] == 1) $MENU_LEFT .= "<a id = \"menu_clients\"       href = \"".$SERVER."cp/clients\"          class = \"menu_left_item\">Клиенты</a>";
        $MENU_LEFT .= "<a id = \"menu_settings\"      href = \"".$SERVER."cp/settings\"         class = \"menu_left_item\">Настройки</a>";
        $MENU_LEFT .= "<div id = \"sales_head_button\" onClick = \"windowSaleAdd();\" class = \"menu_left_item btn-basket\">".$OTL."</div><div onClick = \"clearBasket();\" class = \"menu_left_item btn-trash\" ><img class='image-menu' style=\"cursor:pointer;\" src=\"/trash-can.png\"></div>";
        
    }
    if(TYPE == 3){
        $MENU_LEFT .= "
            <a id = \"menu_transactions\"  href = \"".$SERVER."cp/transactions\"     class = \"menu_left_item_2\">Операции</a>
            <a id = \"menu_cash\"          href = \"".$SERVER."cp/cash\"             class = \"menu_left_item\">Касса</a>
            <a id = \"menu_settings\"      href = \"".$SERVER."cp/settings\"         class = \"menu_left_item\">Настройки</a>
            ";
        $MENU_LEFT .= "<a id = \"menu_services\"      href = \"".$SERVER."cp/services\"         class = \"menu_left_item\">Услуги</a>";
        $MENU_LEFT .= "<div id = \"sales_head_button\" onClick = \"windowSaleAdd();\" class = \"menu_left_item btn-basket\">".$OTL."</div><div onClick = \"clearBasket();\" class = \"menu_left_item btn-trash\" ><img class='image-menu' style=\"cursor:pointer;\" src=\"/trash-can.png\"></div>";

    }   


    $MENU_RIGHT = "
        <div id = \"menu_right_circle\">
            <div id = \"menu_right_circle_name\">".$name.$surname."</div>
            <div id = \"menu_right_circle_exit\" onClick = \"windowOpenExitCabinet();\">выход</div>
        </div>";

    switch($catB){
        case "cash"        : $SCRIPT .= "itemMenuActive('menu_cash');"      ; break;
        case "transactions": $SCRIPT .= "itemMenuActive('menu_cash');itemMenuActive('menu_transactions');"; break;
        case "sales"       : $SCRIPT .= "itemMenuActive('menu_sales');"     ; break;
        case "tires"       : $SCRIPT .= "itemMenuActive('menu_tires');"     ; break;
        case "disks"       : $SCRIPT .= "itemMenuActive('menu_disks');"     ; break;
        case "products"    : $SCRIPT .= "itemMenuActive('menu_products');"  ; break;
        case "services"    : $SCRIPT .= "itemMenuActive('menu_services');"  ; break;
        case "warehouses"  : $SCRIPT .= "itemMenuActive('menu_warehouses');"; break;
        case "movements"   : $SCRIPT .= "itemMenuActive('menu_warehouses');itemMenuActive('menu_movements');"; break;
        case "clients"     : $SCRIPT .= "itemMenuActive('menu_clients');"   ; break;
        case "settings"    : $SCRIPT .= "itemMenuActive('menu_settings');"  ; break;
    }

    $MENU = str_replace("%MENU_LEFT%",  $MENU_LEFT,  $MENU);
    $MENU = str_replace("%MENU_RIGHT%", $MENU_RIGHT, $MENU);


?>