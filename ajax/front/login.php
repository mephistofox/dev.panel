<?php

    require "../../settings.php";
    require "../../functions.php";


    if($_POST["methodName"] == "loginEnter"){         // Вход на сайт
        $mail = clean($_POST["mail"]);
        $pass = clean($_POST["pass"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, pass, first, type FROM user WHERE mail = '$mail'"));
        if($data["id"] > 0){
            $pass = md5($pass.$SALT);
            if($pass == $data["pass"]){
                setcookie("id", $data["id"], time() + 6048000, "/");
                setcookie("pass", $pass, time() + 6048000, "/");
                if($data["first"] == 1) echo 2;
                else echo 1;
            }
            else echo -2;
        }
        else echo -1;
    }
    if($_POST["methodName"] == "loginChangeEnter"){      // Вход на сайт с изменением пароля
        $id = clean($_COOKIE["id"]);
        $pass = clean($_POST["pass"]);
        $pass = md5($pass.$SALT);

        if($id > 0){
            mysqli_query($CONNECTION, "UPDATE user SET pass = '$pass', first = 0 WHERE id = '$id'");
            setcookie("pass", $pass, time() + 6048000, "/");
            echo 1;
        }
        else echo -1;
    }
    if($_POST["methodName"] == "loginRecoveryEnter"){        // Сброс пароля
        $mail = clean($_POST["mail"]);
        $pass = generate_16(10);
        $pass_2 = md5($pass.$SALT);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM user WHERE mail = '$mail'"));
        if($data["id"] > 0){
            $id = $data["id"];
            mysqli_query($CONNECTION, "UPDATE user SET pass = '$pass_2', first = 1 WHERE id = '$id'");
            $TEXT = "
                По запросу на странице авторизации мы сгенерировали новый пароль.<br><br>
                Логин: ".$mail."<br>
                Пароль: ".$pass."<br><br>
                Ссылка для входа в систему: <a href = '".$SERVER."login'>".$SERVER."login</a>
            ";
            send_mail($mail, "Смена пароля", $TEXT);
            echo 1;
        }
        else echo -1;

    }
    if($_POST["methodName"] == "loginChangeMiss"){      // Пропустить изменение пароля
        $id = clean($_COOKIE["id"]);
        mysqli_query($CONNECTION, "UPDATE user SET first = 0 WHERE id = '$id'");
        echo 1;
    }

?>