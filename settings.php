<?php

    // Отображение всех ошибок
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors','on');
    ini_set('error_log', __DIR__ . '/main_error.log');

    // для подключения к базе данных
    $DB_SERVER   =   "mephistofx.beget.tech";
    $DB_USER     =   "mephistofx_test";
    $DB_PASSWORD =   "Y%4PHC0p";
    $DB_NAME     =   "mephistofx_test";

    // полное имя сайта
    $SERVER      =   "https://mdxv.store/";

    // Соль для шифровки пароля
    $SALT = '!@$#%^$%&*^2&(*(*^m&Y$Y%$^%^^&%$j&$$))';

    // Админская почта
    $MAIL_ADMIN = "mephistofox@yandex.ru";

    // Технические работы
    $TECHNICAL_WORKS = false;

    // Разделитель
    $SEP = "%-%";

    // DADATA API
    $DADATA_KEY = "2744032e514eaeb8ad373c3c08e820f98252df78";
    $DADATA_SECRET = "2c3247661dde097ea40d24e1cf175be6c370d213";

    //Google Sheet page
    $spreadsheetId = "1hCtNIpDXGlYUAq94k8coimijknO8WRf9aVopOGsd0d0";
    $googleAccountKeyFilePath = "../../service_key.json";
    $GST1 = "Сезонное хранение";

    //Google Maps API
    $G_MAPS_KEY = "AIzaSyAEGc-l9S5KFjdQwN5aDMUZkG-VkeLCVSg";

?>