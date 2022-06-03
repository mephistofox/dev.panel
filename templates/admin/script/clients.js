ID = 0;
function clientsStart(){   // Загрузка общей плашки
    $.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "clientsStart"},
        success: function(data) {
            $("#clients").html(data);
            selectLoad();
            clientsSearch();
        }
    });
}
function clientsSearch(param){    // Загрузка списка клиентов согласно поиску
    if($("#name").val().length > 2) name = $("#name").val(); else name = "";
    if($("#inn").val().length > 2) inn = $("#inn").val(); else inn = "";
    if($("#mail").val().length > 2) mail = $("#mail").val(); else mail = "";
    if($("#address").val().length > 2) address = $("#address").val(); else address = "";
    if($("#clients_head_phone").val().length > 2) phone = $("#clients_head_phone").val(); else phone = "";
    if(param !== undefined) opt = param; else opt = $("#opt_hidden").val();

    $.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "clientsSearch",
            name : name,
            inn : inn,
            mail : mail,
            address : address,
            phone : phone,
            opt : opt
        },
        success: function(data) {
            $("#clients_body_list").html(data);
        }
    });

}
function clientsSkidkaChange(){  // Скрывает либо раскрывает указание скидки
    if($("#tumbler_skidka").hasClass("tumbler_active")) param = 1; else param = 0;
    if(param == 1){
        $("#cc_skidka_value").css("display", "inline-block");
        $("#cc_skidka_per").css("display", "inline-block");
    }
    else {
        $("#cc_skidka_value").css("display", "none");
        $("#cc_skidka_per").css("display", "none");
    }
}
function clientsContactAdd(id){    // Добавляет форму добавления контактного лица
    if(id === undefined) id = ID; else ID = id;
    if($("#contact_phone").length > 0){
        phone = $("#contact_phone").val();
        name = $("#contact_fio").val();
        if(phone != "" && name != ""){
            $.ajax({
                url: "../../../../ajax/admin/clients.php",
                dataType: "html",
                type: "POST",
                data: {
                    methodName : "clientsContactAdd",
                    phone : phone,
                    name : name,
                    id : id
                },
                success: function(data) {
                    windowClientView(id);
                }
            });
        }
    }
    else{
        getTemplateHTML("clients/contact_add.html", "cc_contacts_list", addPhoneMask());
        $("#cc_contacts_button").css("display", "none");
    }
}
function clientsContactDel(id){   // Удаление контакта клиента
    $.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "clientsContactDel",
            id : id
        },
        success: function(data) {
            windowClientView(ID);
        }
    });
}
function clientsChange(id){    // Сохраняет изменения
    if($("#contact_phone").length > 0){
        phone = $("#contact_phone").val();
        name = $("#contact_fio").val();
        if(phone != "" && name != ""){
            $.ajax({
                url: "../../../../ajax/admin/clients.php",
                dataType: "html",
                type: "POST",
                data: {
                    methodName : "clientsContactAdd",
                    phone : phone,
                    name : name,
                    id : id
                }
            });
        }
    }

    if($("#checkbox_1").prop("checked")) opt = 1; else opt = 0;
    if($("#tumbler_skidka").hasClass("tumbler_active")) skidka = 1; else skidka = 0;
    skidka_val = $("#cc_skidka_value").val();
    $.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "clientsChange",
            opt : opt,
            skidka : skidka,
            skidka_val : skidka_val,
            id : id
        },
        success: function(data) {
            clientsSearch();
            closeWindow();
        }
    });

}
function clientsAdd(){      // Добавление клиента
    name = $("#name_add").val();
    inn = $("#inn_add").val();
    mail = $("#mail_add").val();
    contact = $("#contact_add").val();
    phone = $("#phone_add").val();
    address = $("#address_add").val();

    //if(name == "") addBorderRed("name_add");
    //if(inn == "") addBorderRed("inn_add");
    //if(mail == "") addBorderRed("mail_add");
    if(contact == "") addBorderRed("contact_add");
    if(phone == "") addBorderRed("phone_add");
    //if(address == "") addBorderRed("address_add");

    if($("#checkbox_1").is(":checked")) dop = 1; else dop = 0;

    if($(".border_red").length == 0)$.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "clientsAdd",
            name : name,
            inn : inn,
            mail : mail,
            contact : contact,
            phone : phone,
            address : address,
            dop : dop
        },
        success: function(data) {
            switch(data){
                case "4": addBorderRed("inn_add"); break;
                case "3": addBorderRed("mail_add"); break;
                case "2": addBorderRed("inn_add"); break;
                default:{
                    closeWindow();
                    clientsSearch();
                    if(dop == 1){
                        mas = data.split(SEP);
                        windowSaleAdd(mas[1]);
                    }
                }
            }
        }
    });
}
function clientsRedact(id){    // Редактирование клиента
    name = $("#name_add").val();
    inn = $("#inn_add").val();
    mail = $("#mail_add").val();
    address = $("#address_add").val();

    if(name == "") addBorderRed("name_add");
    //if(inn == "") addBorderRed("inn_add");
    //if(mail == "") addBorderRed("mail_add");
    //if(address == "") addBorderRed("address_add");

    $.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "clientsRedact",
            name : name,
            inn : inn,
            mail : mail,
            address : address,
            id : id
        },
        success: function(data) {
            //alert(data);
            switch(data){
                case "4": addBorderRed("inn_add"); break;
                case "3": addBorderRed("mail_add"); break;
                case "2": addBorderRed("inn_add"); break;
                default: closeWindow(); clientsSearch();
            }
        }
    });
}
function clientsPassChange(id){     // Отправка нового пароля
    $.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: { methodName : "clientsPassChange", id : id},
        success: function(data) {
            $("#cc_str_right_pass").css("display", "none");
            setTimeout(function(){$("#cc_str_right_pass").css("display", "inline-block");}, 1000);
        }
    });
}
function clientsDownload(){     // Скачивание списка клиентов
    $.ajax({
        url: "../../../../ajax/admin/excel.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "clientsDownload"},
        success: function(data) {
            getFile(data);
        }
    });
}
function clientsSend(param){    // Отправка сообщения всем клиентам
    msg = $("#message").val();
    if(msg != "")$.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "clientsSend", msg : msg, param : param},
        success: function(data) {
            //alert(data);
            closeWindow();
        }
    });
}



