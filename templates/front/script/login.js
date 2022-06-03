$(document).ready(function() {  // Вход по Enter
    $(".input").keydown(function(e) {
        if(e.keyCode === 13) {
            loginEnter();
        }
    });
});
function loginEnter(){        // Вход на сайт
    mail = $("#mail").val();
    pass = $("#pass").val();
    if(mail == "") addBorderRed("mail");
    if(pass == "") addBorderRed("pass");
    if(mail != "" && pass != ""){
        $.ajax({
            url: "../../ajax/front/login.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "loginEnter", mail : mail, pass : pass},
            success: function(data) {
                if(data == -1) addBorderRed("mail");
                if(data == -2) addBorderRed("pass");
                if(data == 1) location.href = SERVER + "cp";
                if(data == 2) location.href = SERVER + "login/change";
            }
        });
    }
}
function loginChange(that){            // Проверка на непустое значение
    temp = $(that).val();
    if(temp != "") deleteBorderRed(that);
    $("#notification_mail").html("");
}
function loginChangeEnter(){    // Вход на сайт с изменением пароля
    pass = $("#pass").val();
    pass_repeat = $("#pass_repeat").val();
    if(pass != pass_repeat) addBorderRed("pass_repeat");
    if(pass != "" && pass_repeat != "" && pass == pass_repeat){
        $.ajax({
            url: "../../ajax/front/login.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "loginChangeEnter", pass : pass},
            success: function(data) {
                if(data == -1) location.href = SERVER + "login";
                if(data == 1) location.href = SERVER + "cp";
            }
        });
    }
}
function loginRecoveryEnter(){        // Восстановление пароля
    mail = $("#mail").val();
    if(mail == "") addBorderRed("mail");
    if(mail != ""){
        $.ajax({
            url: "../../ajax/front/login.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "loginRecoveryEnter", mail : mail},
            success: function(data) {
                if(data == -1){
                    addBorderRed("mail");
                    $("#notification_mail").html("Пользователь с указанным адресом не обнаружен");
                }
                if(data == 1){
                    $("#notification_mail").html("Новый пароль сгенерирован и отправлен на указанный адрес эл. почты");
                    setTimeout(function(){
                        location.href = SERVER + "login";
                    }, 2000);

                }
            }
        });
    }
}
function loginChangeMiss(){     // Пропустить изменение пароля
    $.ajax({
        url: "../../ajax/front/login.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "loginChangeMiss"},
        success: function(data) {
            location.href = SERVER + "cp";
        }
    });
}