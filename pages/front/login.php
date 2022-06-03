<?php

    $BASE = file_get_contents("templates/front/temp/login.html");
    $HEAD = "
        <link rel = \"stylesheet\" type = \"text/css\" href = \"".$SERVER."templates/front/css/login.css?3fd\" />
        <script src = \"".$SERVER."templates/front/script/login.js\"></script>";

    $BLOCK_HEAD = "";
    $BLOCK_BODY = "";
    $BLOCK_FOOTER = "";

    // Стандартный вход
    if($catB == ""){
        $TITLE = "Вход";
        $BLOCK_HEAD = "Личный кабинет";
        $BLOCK_BODY = "
            <div class = \"block_body_string\">
                <div class = \"block_body_string_name\">Эл. почта</div>
                <div class = \"block_body_string_value\"><input type = \"text\" name = \"mail\" id = \"mail\" style = \"width: 290px;\" class = \"input height-34\" onKeyUp = \"loginChange(this);\" /></div>
            </div>
            <div class = \"block_body_string\">
                <div class = \"block_body_string_name\">Пароль</div>
                <div class = \"block_body_string_value\"><input type = \"password\" name = \"pass\" id = \"pass\" style = \"width: 290px;\" class = \"input height-34\" onKeyUp = \"loginChange(this);\" /></div>
            </div>
        ";
        $BLOCK_FOOTER = "
            <div id = \"block_footer_left\"><div class = \"button_green\" onClick = \"loginEnter();\">Войти</div></div>
            <div id = \"block_footer_right\"><a href = \"".$SERVER."login/recovery\" class = \"link_blue\">Восстановление пароля</a></div>
        ";
    }

    // Изменение пароля
    if($catB == "change"){
        $TITLE = "Смена пароля";
        $BLOCK_HEAD = "Смена пароля";
        $BLOCK_BODY = "
            <b>Измените пароль на тот, который легко запомнить</b><br>
            Вы вошли по автоматически сгенерированному паролю. Такой пароль крайне сложно запомнить. Измените пароль на собственный, чтобы не забыть.<br>
            <div class = \"block_body_string\">
                <div class = \"block_body_string_name\">Новый пароль</div>
                <div class = \"block_body_string_value\"><input type = \"password\" name = \"pass\" id = \"pass\" style = \"width: 290px;\" class = \"input height-34\" /></div>
            </div>
            <div class = \"block_body_string\">
                <div class = \"block_body_string_name\">Еще раз</div>
                <div class = \"block_body_string_value\"><input type = \"password\" name = \"pass_repeat\" id = \"pass_repeat\" style = \"width: 290px;\" class = \"input height-34\" onKeyUp = \"loginChange('pass_repeat');\" /></div>
            </div>
        ";
        $BLOCK_FOOTER = "
            <div id = \"block_footer_left\"><div class = \"button_green\" onClick = \"loginChangeEnter();\">Войти</div></div>
            <div id = \"block_footer_right\">Изменю позже в настройках<br><div class = \"link_blue_2\" onClick = \"loginChangeMiss();\">Пропустить</div</div>
        ";
    }

    // Восстановление пароля
    if($catB == "recovery"){
        $TITLE = "Восстановление пароля";
        $BLOCK_HEAD = "Восстановление пароля";
        $BLOCK_BODY = "
            <div class = \"block_body_string\">
                <div class = \"block_body_string_name\">Эл. почта</div>
                <div class = \"block_body_string_value\"><input type = \"text\" name = \"mail\" id = \"mail\" style = \"width: 290px;\" class = \"input height-34\" onKeyUp = \"loginChange(this);\" /></div>
            </div>
            <div class = \"block_body_string\" id = 'notification_string' style = 'position: absolute;'>
                <div class = \"block_body_string_name\"></div>
                <div class = \"block_body_string_value\" id = 'notification_mail' style = 'margin-top: -10px; font-size: 9px;'></div>
            </div>
        ";
        $BLOCK_FOOTER = "
            <div id = \"block_footer_left\"><div class = \"button_green\" style = 'position: relative; z-index: 10;' onClick = \"loginRecoveryEnter();\">Отправить</div></div>
            <div id = \"block_footer_right\"><a href = \"".$SERVER."login\" class = \"link_blue\">Назад</a></div>
        ";
    }

    $BASE = str_replace("%BLOCK_HEAD%",   $BLOCK_HEAD,   $BASE);
    $BASE = str_replace("%BLOCK_BODY%",   $BLOCK_BODY,   $BASE);
    $BASE = str_replace("%BLOCK_FOOTER%", $BLOCK_FOOTER, $BASE);

?>