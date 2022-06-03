<?php
    if(!defined("TYPE")) header("Location: ".$SERVER."login");
    $TEMPLATE = file_get_contents("templates/admin/temp/basic.html");
    require "pages/admin/menu.php";
    require "pages/admin/attention.php";
    nowDateMovementsActive($CONNECTION);
?>