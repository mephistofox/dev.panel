<?php

    require "../../settings.php";
    require "../../functions.php";
    require "../../vendor/autoload.php";

    proof(); 

    if($_POST["methodName"] == "clientsStart"){      // Загрузка клиентов
        $TEXT = file_get_contents("../../templates/admin/temp/clients/clients_list.html");

        $HEAD_LIST = "";
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM client"));
        $HEAD_LIST .= "
            <div class = 'clients_head_list_item'>
                <div onClick = 'clientsDownload();'>Скачать</div>
                <div onClick = 'windowClientSend(1);'>Рассылка</div>
                <span>".$data[0]." всего</span>
            </div>
        ";
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM client WHERE opt = 1"));
        $HEAD_LIST .= "
            <div class = 'clients_head_list_item'>
                <div onClick = 'windowClientSend(2);'>Рассылка</div>
                <span2>".$data[0]." оптовые</span2>
            </div>
        ";
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT COUNT(*) FROM client WHERE opt = 0"));
        $HEAD_LIST .= "
            <div class = 'clients_head_list_item'>
                <div onClick = 'windowClientSend(3);'>Рассылка</div>
                <span2>".$data[0]." остальные</span2>
            </div>
        ";

        $TEXT = str_replace("%HEAD_LIST%", $HEAD_LIST, $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "clientsSearch"){      // Загрузка клиентов
        $name = clean($_POST["name"]);
        $inn = clean($_POST["inn"]);
        $mail = clean($_POST["mail"]);
        $address = clean($_POST["address"]);
        $phone = clean($_POST["phone"]);
        $opt = clean($_POST["opt"]);

        $sql_text = "
            SELECT
                client.id AS id,
                client.name AS name,
                client.inn AS inn,
                client.mail AS mail,
                client_contact.phone AS phone,
                client.address AS address,
                client.opt AS opt
            FROM client LEFT JOIN client_contact ON client.id = client_contact.cId WHERE client.id > 0 ";
        if($name != "") $sql_text .= "AND client.name LIKE '$name%' ";
        if($inn != "") $sql_text .= "AND client.inn LIKE '$inn%' ";
        if($mail != "") $sql_text .= "AND client.mail LIKE '$mail%' ";
        if($address != "") $sql_text .= "AND client.address LIKE '%$address%' ";
        if($opt != 0) $sql_text .= "AND client.opt = 1 ";
        if($phone != "") $sql_text .= "AND client_contact.phone LIKE '%$phone%' ";
        $sql_text .= "GROUP BY client_contact.cId";


        $CLIENTS_LIST = "";
        $sql = mysqli_query($CONNECTION, $sql_text);
        while($data = mysqli_fetch_array($sql)){
            $opt = ($data["opt"] == 1) ? "да" : "нет";
            $CLIENTS_LIST .= "
                <div class = 'clients_body_list_item' onClick = 'windowClientView(".$data["id"].");'>
                    <div class = 'text_overflow' style = 'width: 187px;'>".$data["name"]."</div>
                    <div class = 'text_overflow' style = 'width: 133px;'>".$data["inn"]."</div>
                    <div class = 'text_overflow mail' style = 'width: 144px;'>".$data["mail"]."</div>
                    <div class = 'text_overflow phone' style = 'width: 144px;'>".$data["phone"]."</div>
                    <div class = 'text_overflow' style = 'width: 264px;'>".$data["address"]."</div>
                    <div class = 'text_overflow' style = 'width: 102px;'>".$opt."</div>
                </div>
            ";
        }

        echo $CLIENTS_LIST;
    }
    if($_POST["methodName"] == "clientsLoad"){      // Загрузка карточки клиента
        $id = clean($_POST["id"]);

        $TEXT = file_get_contents("../../templates/admin/temp/clients/client_card.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM client WHERE id = '$id'"));

        $TUMBLER = tumbler("skidka", $data["skidka"], "", "clientsSkidkaChange()");
        if($data["skidka"] == 0) $hidden = "style = 'display: none;'"; else $hidden = "";
        $TUMBLER .= "<input type = 'text' class = 'input height-23' ".$hidden." id = 'cc_skidka_value' value = '".$data["skidka_value"]."' />";
        $TUMBLER .= "<per id = 'cc_skidka_per' ".$hidden.">%</per>";

        $STATUS = checkbox(1, $data["opt"], "Оптовый закупщик");

        $TEXT = str_replace("%ID%", $id, $TEXT);
        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%INN%", $data["inn"], $TEXT);
        $TEXT = str_replace("%FULL_NAME%", $data["full_name"], $TEXT);
        $TEXT = str_replace("%MAIL%", $data["mail"], $TEXT);
        $TEXT = str_replace("%ADDRESS%", $data["address"], $TEXT);
        $TEXT = str_replace("%TUMBLER%", $TUMBLER, $TEXT);
        $TEXT = str_replace("%STATUS%", $STATUS, $TEXT);


        $CONTACTS = "";
        $sql = mysqli_query($CONNECTION, "SELECT * FROM client_contact WHERE cId = '$id'");
        while($data = mysqli_fetch_array($sql)){
            $CONTACTS .= "
                <div class = 'cc_contacts_list_str'>
                    <phone>".$data["phone"]."</phone>
                    <fio>".$data["name"]."</fio>
                    <a href = 'tel:".$data["phone"]."'>Позвонить</a>
                    <cross onCLick = 'clientsContactDel(".$data["id"].")'></cross>
                </div>
            ";
        }
        $TEXT = str_replace("%CONTACTS%", $CONTACTS, $TEXT);



        echo $TEXT;
    }
    if($_POST["methodName"] == "clientsLoad2"){      // Загрузка карточки клиента при глобальном изменении
        $id = clean($_POST["id"]);

        $TEXT = file_get_contents("../../templates/admin/temp/clients/client_redact.html");

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT * FROM client WHERE id = '$id'"));

        $TEXT = str_replace("%NAME%", $data["name"], $TEXT);
        $TEXT = str_replace("%INN%", $data["inn"], $TEXT);
        $TEXT = str_replace("%MAIL%", $data["mail"], $TEXT);
        $TEXT = str_replace("%ADDRESS%", $data["address"], $TEXT);

        echo $TEXT;
    }
    if($_POST["methodName"] == "clientsContactAdd"){      // Сохранение контактного лица клиента
        $id = clean($_POST["id"]);
        $name = clean($_POST["name"]);
        $phone = phoneToBase(clean($_POST["phone"]));

        mysqli_query($CONNECTION, "INSERT INTO client_contact (cId, name, phone) VALUES ('$id', '$name', '$phone')");
    }
    if($_POST["methodName"] == "clientsContactDel"){      // Удаление контактного лица клиента
        $id = clean($_POST["id"]);

        mysqli_query($CONNECTION, "DELETE FROM client_contact WHERE id = '$id'");
    }
    if($_POST["methodName"] == "clientsChange"){       // Изменение данных клиента
        $id = clean($_POST["id"]);
        $opt = clean($_POST["opt"]);
        $skidka = clean($_POST["skidka"]);
        $skidka_val = clean($_POST["skidka_val"]);

        mysqli_query($CONNECTION, "UPDATE client SET opt = '$opt', skidka = '$skidka', skidka_value = '$skidka_val' WHERE id = '$id'");
    }
    if($_POST["methodName"] == "clientsAdd"){       // Добавление клиента
        $name = clean($_POST["name"]);
        $inn = clean($_POST["inn"]);
        $mail = clean($_POST["mail"]);
        $contact = clean($_POST["contact"]);
        $phone = phoneToBase(clean($_POST["phone"]));
        $address = clean($_POST["address"]);
        $dop = clean($_POST["dop"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM client WHERE inn = '$inn'"));
        if($data["id"] > 0 && $inn != "") echo 4;
        else{
            $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM user WHERE mail = '$mail'"));
            if($data["id"] > 0 && $mail != "") echo 3;
            else{
                if($inn != ""){
                    $company = DADATAGetCompany($inn, $DADATA_KEY);
                    $company = explode($SEP, $company);
                }
                else $company[0] = $name;

                if($company[0] == "0") echo 2;
                else{
                    $full_name = $company[0];
                    if($mail == ""){
                        mysqli_query($CONNECTION, "INSERT INTO client (inn, name, full_name, mail, address) VALUES ('$inn', '$contact', '$contact', '$mail', '$address')");

                        $id = mysqli_insert_id($CONNECTION);
                        mysqli_query($CONNECTION, "INSERT INTO client_contact (cId, name, phone) VALUES ('$id', '$contact', '$phone')");
                    }
                    else{
                        mysqli_query($CONNECTION, "INSERT INTO client (inn, name, full_name, mail, address) VALUES ('$inn', '$name', '$full_name', '$mail', '$address')");

                        $id = mysqli_insert_id($CONNECTION);
                        mysqli_query($CONNECTION, "INSERT INTO client_contact (cId, name, phone) VALUES ('$id', '$contact', '$phone')");
                    }

                    if($dop == 1) echo $SEP.$id;
                    else echo $SEP."0";


                    $pass = generate_16(10);
                    $pass_2 = md5($pass.$SALT);

                    if($mail != ""){
                        mysqli_query($CONNECTION, "INSERT INTO user (mail, pass, type) VALUES ('$mail', '$pass_2', '6')");

                        $TEXT = "
                            Вы были зарегистрированы на сате ".$SERVER.":<br><br>
                            Ваш логин: ".$mail."<br>
                            Ваш пароль: ".$pass."<br><br>
                            Ссылка для входа в систему: <a href = '".$SERVER."login'>".$SERVER."login</a>
                        ";
                        send_mail($mail, "Регистрация", $TEXT);
                    }


                }
            }
        }


    }
    if($_POST["methodName"] == "clientsRedact"){       // Редактирование клиента
        $name = clean($_POST["name"]);
        $inn = clean($_POST["inn"]);
        $mail = clean($_POST["mail"]);
        $address = clean($_POST["address"]);
        $id = clean($_POST["id"]);

        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id, mail FROM client WHERE id = '$id'"));
        if(!isset($data["id"])) echo 4;
        else {
            if($inn != ""){
                $company = DADATAGetCompany($inn, $DADATA_KEY);
                $company = explode($SEP, $company);
            }
            else $company[0] = $name;
            if($company[0] == "0") echo 2;
            else {
                $full_name = $company[0];
                $mail_old = $data["mail"];
                if($mail != ""){
                    if($mail_old == $mail){
                        mysqli_query($CONNECTION, "UPDATE client SET inn = '$inn', name = '$name', full_name = '$full_name', mail = '$mail', address = '$address' WHERE id = '$id'");
                        echo 1;
                    }
                    else {
                        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM client WHERE mail = '$mail'"));
                        if($data["id"] != $id) echo 3;
                        else{
                            $pass = generate_16(10);
                            $pass_2 = md5($pass.$SALT);

                            mysqli_query($CONNECTION, "UPDATE user SET mail = '$mail', pass = '$pass_2' WHERE mail = '$mail_old'");

                            mysqli_query($CONNECTION, "UPDATE client SET inn = '$inn', name = '$name', full_name = '$full_name', mail = '$mail', address = '$address' WHERE id = '$id'");

                            $TEXT = "
                                Вы были зарегистрированы на сате ".$SERVER.":<br><br>
                                Ваш логин: ".$mail."<br>
                                Ваш пароль: ".$pass."<br><br>
                                Ссылка для входа в систему: <a href = '".$SERVER."login'>".$SERVER."login</a>
                            ";
                            send_mail($mail, "Регистрация", $TEXT);

                            echo 1;
                        }
                    }
                }
                else{
                    mysqli_query($CONNECTION, "UPDATE client SET inn = '$inn', name = '$name', full_name = '$full_name', mail = '$mail', address = '$address' WHERE id = '$id'");
                }
            }
        }
    }
    if($_POST["methodName"] == "clientsPassChange"){       // Новый пароль для клиента
        $id = clean($_POST["id"]);
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT mail FROM client WHERE id = '$id'"));
        $mail = $data["mail"];
        $data = mysqli_fetch_array(mysqli_query($CONNECTION, "SELECT id FROM user WHERE mail = '$mail'"));
        $id = $data["id"];

        $pass = generate_16(10);
        $pass_2 = md5($pass.$SALT);

        mysqli_query($CONNECTION, "UPDATE user SET pass = '$pass_2' WHERE id = '$id'");
        $TEXT = "
            Ваш пароль был успешно изменен<br><br>
            Ваш логин: ".$mail."<br>
            Ваш пароль: ".$pass."<br><br>
            Ссылка для входа в систему: <a href = '".$SERVER."login'>".$SERVER."login</a>
        ";
        send_mail($mail, "Смена пароля", $TEXT);
    }
    if($_POST["methodName"] == "clientsSend"){             // Рассылка сообщений клиентам
        $param = clean($_POST["param"]);
        $msg = clean($_POST["msg"]);

        switch($param){
            case 2: $sql_text = " WHERE opt = 1"; break;
            case 3: $sql_text = " WHERE opt = 0"; break;
            default: $sql_text = "";
        }
        $sql = mysqli_query($CONNECTION, "SELECT client_contact.phone AS phone FROM client LEFT JOIN client_contact ON client.id = client_contact.cId ".$sql_text." GROUP BY client.inn");
        while($data = mysqli_fetch_array($sql)){
            //echo $data["phone"];
            // Здесь должно быть отправление смс пользователям
        }

    }

?>