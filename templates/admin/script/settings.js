PAYER = 0;
function settingsStart(param, param_2, param_3){           // Загружает в первый столбец существующие позиции настроек для данного пользователя
    settingsCleanColumn("all");
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsStart"},
        success: function(data) {
            $("#settings_col_1").html(data);
            if(param != "") settingsLoad(param, param_2, param_3);
        }
    });
}
function settingsCleanColumn(id){     // Очищение заданного столбца
    if(id == "all"){
        settingsCleanColumn(2);
        settingsCleanColumn(3);
        settingsCleanColumn(4);
    }
    else{
        $("#settings_col_" + id).html("");
        $("#settings_col_" + id).removeClass("settings_col_thin");
        $("#settings_col_" + id).removeClass("settings_col_fat");
        $("#settings_col_" + id).css("display", "none");
    }
}
function settingsLoad(param, param_2, param_3){          // Загрузка пункта меню настроек
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsLoad", param : param},
        success: function(data) {
            history.pushState(null, null, SERVER + "cp/settings/" + param);
            listItemActive("settings_menu", param);
            settingsCleanColumn("all");

            $("#settings_col_2").html(data);
            $("#settings_col_2").css("display", "inline-block");

            switch(param){
                case "pass" : $("#settings_col_2").addClass("settings_col_fat"); break;
                default     : $("#settings_col_2").addClass("settings_col_thin");
            }
            if(param_2 !== undefined && param_2 != ""){
                switch(param){
                    case "providers" : settingsProviderLoad(param_2); break;
                    case "payers"    : settingsPayerLoad(param_2); break;
                    case "workers"   : settingsWorkersListLoad(param_2, param_3); break;
                    case "couriers"  : settingsCourierLoad(param_2); break;
                    case "bases"     : settingsBaseLoad(param_2); break;
                    case "products"  : settingsProductsListLoad(param_2, param_3); break;
                    case "delivery"  : settingsDeliveryLoad(param_2); break;
                }
            }
        }
    });
}
function settingsPassChange(){         // Изменение пароля
    pass_old = $("#pass_old").val();
    pass_new = $("#pass_new").val();
    pass_repeat = $("#pass_repeat").val();

    if(pass_old == "") addBorderRed("pass_old");
    if(pass_new == "") addBorderRed("pass_new");
    if(pass_repeat == "") addBorderRed("pass_repeat");
    if(pass_new != pass_repeat){
        addBorderRed("pass_new");
        addBorderRed("pass_repeat");
    }
    if(pass_new == pass_old){
        addBorderRed("pass_old");
        addBorderRed("pass_new");
    }
    if(pass_old != "" && pass_new != "" && pass_repeat != "" && pass_new == pass_repeat && pass_new != pass_old){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsPassChange", pass_old : pass_old, pass_new : pass_new},
            success: function(data) {
                if(data == -1) addBorderRed("pass_old");
                if(data == 1){
                    windowNotification("Ваш пароль успешно изменен", "Хорошо");
                    $("#pass_old").val("");
                    $("#pass_new").val("");
                    $("#pass_repeat").val("");
                }
            }
        });
    }
}
function settingsProviderLoad(id){     // Загрузка поставщика
    if(id == "add") settingsProviderLoadAdd();
    else {
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsProviderLoad", id : id},
            success: function(data) {
                history.pushState(null, null, SERVER + "cp/settings/providers/" + id);
                listItemActive("settings_providers", id);
                $("#settings_col_3").html(data);
                $("#settings_col_3").css("display", "inline-block");
                $("#settings_col_3").addClass("settings_col_fat");
            }
        });
    }
}
function settingsProviderLoadAdd(){   // Загрузка формы добавления поставщика
    getTemplateHTML("settings/provider_add.html", "settings_col_3");
    $("#settings_col_3").css("display", "inline-block");
    $("#settings_col_3").addClass("settings_col_fat");
    history.pushState(null, null, SERVER + "cp/settings/providers/add");
}
function settingsProviderChange(id){   // Изменение данных поставщика
    name = $("#name").val();
    if($("#tumbler_1").hasClass("tumbler_active")) sklad = 1; else sklad = 0;
    address = $("#address").val();
    note = $("#note").val();
    if(name == "") addBorderRed("name");
    if(address == "") addBorderRed("address");
    if(name != "" && address != ""){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsProviderChange", id : id, name : name, sklad : sklad, address : address, note : note},
            success: function(data) {
                windowNotification("Данные успешно изменены", "Хорошо");
            }
        });
    }

}
function settingsProviderAdd(){   // Добавление нового поставщика
    name = $("#name").val();
    if($("#tumbler_1").hasClass("tumbler_active")) sklad = 1; else sklad = 0;
    address = $("#address").val();
    note = $("#note").val();
    if(name == "") addBorderRed("name");
    if(address == "") addBorderRed("address");
    if(name != "" && address != ""){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsProviderAdd", name : name, sklad : sklad, address : address, note : note},
            success: function(data) {
                windowNotification("Поставщик успешно добавлен", "Хорошо");
                settingsLoad("providers");
            }
        });
    }

}
function settingsProviderDelete(id){   // Удаление поставщика
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsProviderDelete", id : id},
        success: function(data) {
            settingsLoad("providers");
        }
    });
}
function settingsWorkersListLoad(type, id){     // Загрузка списка сотрудников данного типа
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsWorkersListLoad", type : type},
        success: function(data) {
            settingsCleanColumn(4);
            history.pushState(null, null, SERVER + "cp/settings/workers/" + type);
            catD = type;
            listItemActive("settings_workers", type);
            $("#settings_col_3").html(data);
            $("#settings_col_3").css("display", "inline-block");
            $("#settings_col_3").addClass("settings_col_thin");
            if(id !== undefined && id != "") settingsWorkerLoad(id);
        }
    });
}
function settingsWorkerLoad(id){      // Загрузка данного сотрудника
    if(id == "add") settingsWorkerLoadAdd();
    else $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsWorkerLoad", id : id},
        success: function(data) {
            mas = data.split(SEP);
            history.pushState(null, null, SERVER + "cp/settings/workers/" + mas[0] + "/" + id);
            listItemActive("settings_workers_list", id);
            $("#settings_col_4").html(mas[1]);
            $("#settings_col_4").css("display", "inline-block");
            $("#settings_col_4").addClass("settings_col_fat");
            addPhoneMask("phone");
            inputDecimal();
            inputNumber();
            selectLoad();
        }
    });
}
function settingsWorkerChange(id){    // Изменение данных сотрудника
    name = $("#name").val();
    surname = $("#surname").val();
    phone = $("#phone").val();
    mail = $("#mail").val();
    if(name == "") addBorderRed("name");
    if(surname == "") addBorderRed("surname");
    if(mail == "") addBorderRed("mail");

    flag = true;
    koef = 0;
    day_a = 1;
    day_z = 1;
    if(catD == 2){
        koef = $("#koef").val();
        if(koef == "" || koef.length > 6){
            addBorderRed("koef");
            flag = false;
        }
        day_a = $("#day_a").val();
        if(day_a == "" || day_a > 31 || day_a < 1){
            addBorderRed("day_a");
            flag = false;
        }
        day_z = $("#day_z").val();
        if(day_z == "" || day_z > 31 || day_z < 1){
            addBorderRed("day_z");
            flag = false;
        }
    }

    if($("#base_hidden").length > 0) base = $("#base_hidden").val(); else base = 0;

    root = "";
    if($("#tumbler_0").length > 0){
        root = "";
        for(i = 0; i < 11; i++) if($("#tumbler_" + i).hasClass("tumbler_active")) root += "1"; else root += "0";
    }

    if(name != "" && surname != "" && mail != "" && flag){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {
                methodName : "settingsWorkerChange",
                id : id,
                name : name,
                surname : surname,
                phone : phone,
                mail : mail,
                koef : koef,
                day_a : day_a,
                day_z : day_z,
                base : base,
                root : root
            },
            success: function(data) {
                if(data == -1) addBorderRed("mail");
                else{
                    settingsWorkersListLoad(data, id);
                    windowNotification("Данные успешно изменены", "Хорошо");
                }
            }
        });
    }
}
function settingsWorkerNewPass(id){    // Генерация нового пароля для пользователя
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsWorkerNewPass", id : id},
        success: function(data) {
            windowNotification("Новый пароль отправлен на почту", "Хорошо");
        }
    });
}
function settingsWorkerLoadAdd(){    // Загрузка формы добавления сотрудника
    getTemplateHTML("settings/worker_1_add.html", "settings_col_4");
    $("#settings_col_4").css("display", "inline-block");
    $("#settings_col_4").addClass("settings_col_fat");
    history.pushState(null, null, SERVER + "cp/settings/workers/" + catD + "/add");
}
function settingsWorkerAdd(){        // Добавление нового сотрудника
    type = catD;
    name = $("#name").val();
    surname = $("#surname").val();
    phone = $("#phone").val();
    mail = $("#mail").val();
    if(name == "") addBorderRed("name");
    if(surname == "") addBorderRed("surname");
    if(mail == "") addBorderRed("mail");
    if(name != "" && surname != "" && mail != ""){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsWorkerAdd", name : name, surname : surname, phone : phone, mail : mail, type : type},
            success: function(data) {
                if(data == -1) addBorderRed("mail");
                else{
                    settingsWorkersListLoad(type, data);
                    windowNotification("Пользователь успешно добавлен", "Хорошо");
                }
            }
        });
    }
}
function settingsWorkerDelete(id){   // Удаление сотрудника
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsWorkerDelete", id : id},
        success: function(data) {
            settingsWorkersListLoad(catD);
        }
    });
}
function settingsPayerLoad(id){     // Загрузка плательщика
    PAYER = id;
    if(id == "add") settingsPayerLoadAdd();
    else {
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsPayerLoad", id : id},
            success: function(data) {
                $("#settings_col_3_2").remove();
                mas = data.split(SEP);
                history.pushState(null, null, SERVER + "cp/settings/payers/" + id);
                listItemActive("settings_payers", id);
                $("#settings_col_3").html(mas[0]);
                $("#settings_col_3").css("display", "inline-block");
                $("#settings_col_3").addClass("settings_col_fat");
            }
        });
    }
}
function settingsPayerDelete(id){   // Удаление плательщика
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsPayerDelete", id : id},
        success: function(data) {
            settingsLoad("payers");
        }
    });
}
function settingsPayerPriotiryChange(){
    if($("#tumbler_2").hasClass("tumbler_active")) param = 1; else param = 0;
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsPayerPriotiryChange", id : PAYER, param : param},
        success: function(data) {

        }
    });
}
function settingsPayerLoadAdd(){   // Загрузка формы добавления плательщика
    getTemplateHTML("settings/payer_add.html", "settings_col_3");
    $("#settings_col_3").css("display", "inline-block");
    $("#settings_col_3").addClass("settings_col_fat");
    history.pushState(null, null, SERVER + "cp/settings/payers/add");
}
function settingsPayerAdd(){   // Добавление нового плательщика
    name = $("#name").val();
    if($("#tumbler_1").hasClass("tumbler_active")) code = 1; else code = 0;
    inn = $("#inn").val();
    if(name == "") addBorderRed("name");
    if(inn == "") addBorderRed("inn");
    if(name != "" && inn != ""){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsPayerAdd", name : name, code : code, inn : inn},
            success: function(data) {
                //alert(data);
                windowNotification("Плательщик успешно добавлен", "Хорошо");
                settingsLoad("payers");
            }
        });
    }

}
function settingsPayerChange(id){   // Изменение данных плательщика
    name = $("#name").val();
    if($("#tumbler_1").hasClass("tumbler_active")) code = 1; else code = 0;
    inn = $("#inn").val();
    if(name == "") addBorderRed("name");
    if(inn == "") addBorderRed("inn");
    if(name != "" && inn != ""){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsPayerChange", id : id, name : name, code : code, inn : inn},
            success: function(data) {
                //alert(data);
                windowNotification("Данные успешно изменены", "Хорошо");
            }
        });
    }

}
function settingsPayerRekChange(id, file){      // Изменение файла реквизитов
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsPayerRekChange", id : id, file : file},
        success: function(data) {
            location.reload();
        }
    });
}
function settingsCourierLoad(id){     // Загрузка курьера
    if(id == "add") settingsCourierLoadAdd();
    else {
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsCourierLoad", id : id},
            success: function(data) {
                history.pushState(null, null, SERVER + "cp/settings/couriers/" + id);
                listItemActive("settings_couriers", id);
                $("#settings_col_3").html(data);
                $("#settings_col_3").css("display", "inline-block");
                $("#settings_col_3").addClass("settings_col_fat");
                addPhoneMask("phone");
            }
        });
    }
}
function settingsCourierLoadAdd(){   // Загрузка формы добавления курьера
    getTemplateHTML("settings/courier_add.html", "settings_col_3");
    $("#settings_col_3").css("display", "inline-block");
    $("#settings_col_3").addClass("settings_col_fat");
    history.pushState(null, null, SERVER + "cp/settings/couriers/add");
}
function settingsCourierChange(id){   // Изменение данных курьера
    name = $("#name").val();
    surname = $("#surname").val();
    phone = $("#phone").val();
    mail = $("#mail").val();
    if(name == "") addBorderRed("name");
    if(surname == "") addBorderRed("surname");
    if(mail == "") addBorderRed("mail");
    if(name != "" && surname != "" && mail != ""){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {
                methodName : "settingsCourierChange",
                id : id,
                name : name,
                surname : surname,
                phone : phone,
                mail : mail
            },
            success: function(data) {
                windowNotification("Данные успешно изменены", "Хорошо");
            }
        });
    }

}
function settingsCourierAdd(){   // Добавление нового курьера
    name = $("#name").val();
    surname = $("#surname").val();
    phone = $("#phone").val();
    mail = $("#mail").val();
    if(name == "") addBorderRed("name");
    if(surname == "") addBorderRed("surname");
    if(mail == "") addBorderRed("mail");
    if(name != "" && surname != "" && mail != ""){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {
                methodName : "settingsCourierAdd",
                name : name,
                surname : surname,
                phone : phone,
                mail : mail
            },
            success: function(data) {
                if(data == -1) addBorderRed("mail");
                else{
                    settingsLoad("couriers");
                    windowNotification("Курьер успешно добавлен", "Хорошо");
                }
            }
        });
    }

}
function settingsCourierDelete(id){   // Удаление курьера
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsCourierDelete", id : id},
        success: function(data) {
            settingsLoad("couriers");
        }
    });
}
function settingsProductsListLoad(type, id){      // Загрузка списка параметров данного типа товаров
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsProductsListLoad", type : type},
        success: function(data) {
            settingsCleanColumn(4);
            history.pushState(null, null, SERVER + "cp/settings/products/" + type);
            catD = type;
            listItemActive("settings_products", type);
            $("#settings_col_3").html(data);
            $("#settings_col_3").css("display", "inline-block");
            $("#settings_col_3").addClass("settings_col_thin");
            if(id !== undefined && id != "") settingsProductLoad(id);
        }
    });
}
function settingsProductLoad(id){       // Загрузка значений данного параметра
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsProductLoad", id : id},
        success: function(data) {
            mas = data.split(SEP);
            history.pushState(null, null, SERVER + "cp/settings/products/" + mas[0] + "/" + id);
            listItemActive("settings_product_list", id);
            $("#settings_col_4").html(mas[1]);
            $("#settings_col_4").css("display", "inline-block");
            $("#settings_col_4").addClass("settings_col_thin");
        }
    });
}
function settingsProductParamActive(id){    // Выделение данного параметра
    listItemActive("settings_params_list", id);
}
function settingsProductParamDelete(id){   // Удаление данного параметра
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsProductParamDelete", id : id},
        success: function(data) {
            settingsProductLoad(data);
        }
    });
}
function settingsProductParamAdd(id){   // Добавление нового значения выбранного параметра
    val = $("#product_param").val();
    if(val.length > 0){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsProductParamAdd", id : id, val : val},
            success: function(data) {
                if(data == 1) settingsProductLoad(id);
                else addBorderRed("product_param");
            }
        });
    }
}
function settingsDeliveryLoad(id){     // Загрузка транспортной компании
    if(id == "add") settingsDeliveryLoadAdd();
    else {
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsDeliveryLoad", id : id},
            success: function(data) {
                history.pushState(null, null, SERVER + "cp/settings/delivery/" + id);
                listItemActive("settings_delivery", id);
                $("#settings_col_3").html(data);
                $("#settings_col_3").css("display", "inline-block");
                $("#settings_col_3").addClass("settings_col_fat");
            }
        });
    }
}
function settingsDeliveryLoadAdd(){   // Загрузка формы добавления транспортной компании
    getTemplateHTML("settings/delivery_add.html", "settings_col_3");
    $("#settings_col_3").css("display", "inline-block");
    $("#settings_col_3").addClass("settings_col_fat");
    history.pushState(null, null, SERVER + "cp/settings/delivery/add");
}
function settingsDeliveryChange(id){   // Изменение данных транспортной компании
    name = $("#name").val();
    address = $("#address").val();

    if(name == "") addBorderRed("name");
    if(address == "") addBorderRed("address");
    if(name != "" && address != ""){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsDeliveryChange", id : id, name : name, address : address},
            success: function(data) {
                windowNotification("Данные успешно изменены", "Хорошо");
                settingsLoad("delivery", id);
            }
        });
    }
}
function settingsDeliveryAdd(){   // Добавление новой транспортной компании
    name = $("#name").val();
    address = $("#address").val();
    if(name == "") addBorderRed("name");
    if(address == "") addBorderRed("address");
    if(name != "" && address != ""){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsDeliveryAdd", name : name, address : address},
            success: function(data) {
                windowNotification("Транспортная компания успешно добавлена", "Хорошо");
                settingsLoad("delivery");
            }
        });
    }

}
function settingsDeliveryDelete(id){   // Удаление транспортной компании
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsDeliveryDelete", id : id},
        success: function(data) {
            settingsLoad("delivery");
        }
    });
}
function settingsDeliveryMap(){     // Открывает карту с адресом компании
    address = $("#address").val();
    if(address.length > 0){
        windowMap(address);
    }
}
function settingsMassaProof(data){    // Проверяет файл массовой загрузки
    mas = data.split(SEP);
    $("#massa_file_0").val(mas[0]);
    $.ajax({
        url: "../../../../ajax/admin/excel.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsMassaProof", file : mas[0]},
        success: function(data) {
            $("#settings_massa_file_name").html(mas[1]);
            if(data == -1){
                $("#massa_file_label").html("Выбрать другой файл");
                $("#settings_massa_status").html("Неверный формат");
            }
            else {
                $("#massa_file_label").html("");
                $("#settings_massa_add").css("display", "block");
                $("#settings_massa_status").html("Товарных позиций: " + data);
            }
        }
    });

}
function settingsMassaAdd(){      // Добавление шин из файла
    file = $("#massa_file_0").val();
    $.ajax({
        url: "../../../../ajax/admin/excel.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsMassaAdd", file : file},
        success: function(data) {
            windowNotification("Успешно добавлено шин: " + data, "Хорошо");
            settingsLoad("massa");
        }
    });
}
function settingsFileValChange(that){      // Изменение верхней границы файла
    val = $(that).val();
    val = val.replace(/[^0-9]/g,'');
    $(that).val(val);
    if(val > 0){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsFileValChange", val : val}
        });
    }
}
function settingsOffsAdd(){        // Добавление нового основания для списания
    val = $("#so_val").val();
    if(val.length > 0) $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsOffsAdd", val : val},
        success: function(data){
            settingsLoad("offs");
        }
    });
}
function settingsOffsAdd2(id){        // Добавление другого основания списания в основные
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsOffsAdd2", id : id},
        success: function(data){
            settingsLoad("offs");
        }
    });
}
function settingsOffsDelete(id){    // Удаление основания списания
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsOffsDelete", id : id},
        success: function(data){
            settingsLoad("offs");
        }
    });
}
function settingsBaseColorChange(){     // Передача цвета из поля ввода в кружок
    color = $("#color").val();
    $("#color_circle").css("background-color", "#" + color);
}
function settingsBaseTimeProof(that){    // Проверка правильности введенного времени
    time = $(that).val();
    deleteBorderRed(that);
    mas = time.split(":");
    if(mas[0] < 0 || mas[0] > 23 || mas[1] < 0 || mas[1] > 59){
        time = "00:00";
        $(that).val(time);
        addBorderRed($(that).attr("id"));
    }
}
function settingsBaseAdd(){      // Добавление новой базы
    name = $("#name").val();
    color = $("#color").val();
    code = $("#code").val();
    address = $("#address").val();
    if($("#tumbler_1").hasClass("tumbler_active")) vydacha = 1; else vydacha = 0;
    time_1 = $("#time_1").val();
    time_2 = $("#time_2").val();

    if(name == "") addBorderRed("name");
    if(code == "") addBorderRed("code");
    if(address == "") addBorderRed("address");
    if(time_1 == "") addBorderRed("time_1");
    if(time_2 == "") addBorderRed("time_2");

    if($(".border_red").length == 0){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsBaseAdd", name : name, color : color, code : code, address : address, vydacha : vydacha, time_1 : time_1, time_2 : time_2},
            success: function(data){
                switch(data){
                    case "-1": addBorderRed("name"); break;
                    case "-2": addBorderRed("code"); break;
                    case "1" : settingsLoad("bases"); closeWindow(); break;
                }
            }
        });
    }
}
function settingsBaseLoad(id){
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsBaseLoad", id : id},
        success: function(data){
            history.pushState(null, null, SERVER + "cp/settings/bases/" + id);
            listItemActive("settings_bases", id);
            $("#settings_col_3").html(data);
            $("#settings_col_3").css("display", "inline-block");
            $("#settings_col_3").css("width", "457px");
            $("#settings_col_3").addClass("settings_col_fat");
        }
    });
}
function settingsBaseStorageDel(id){   // Удаление хранилища
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsBaseStorageDel", id : id},
        success: function(data){
            if(data == -1) windowNotification("Хранилище не пустое<span>Чтобы удалить хранилище, сначала переместите товар в другие места.</span>", "Ясно");
            else location.reload();
        }
    });
}
function settingsBaseDelete(id){     // Удаление базы
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsBaseDel", id : id},
        success: function(data){
            if(data == -1) windowNotification("В базе есть хранилища<span>Чтобы удалить базу, сначала удалите из нее все хранилища.</span>", "Ясно");
            else settingsLoad("bases");
        }
    });
}
function settingsBaseStorageAddVis(that){    // Смена видимости пунтка о вместимости хранилища
    if($(that).hasClass("tumbler_active")){
        $("#sa_1").css("display", "none");
        $("#sa_2").css("display", "block");
    }
    else{
        $("#sa_2").css("display", "none");
        $("#sa_1").css("display", "block");
    }
    $(".border_red").removeClass("border_red");
}
function settingsBaseStorageAdd(id){       // Добавление хранилища в базу
    if($("#tumbler_1").hasClass("tumbler_passive")){
        composite = 0;
        code = $("#code_1").val();
        count = $("#count").val();
        if(code == "") addBorderRed("code_1");
        if(count == "") addBorderRed("count");
    }
    else{
        composite = 1;
        code = $("#code_2").val();
        count = 0;
        if(code == "") addBorderRed("code_2");
    }
    name = $("#name").val();
    if(name == "") addBorderRed("name");
    if($(".border_red").length == 0){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsBaseStorageAdd", id : id, composite : composite, code : code, count : count, name : name},
            success: function(data){
                if(data == 1){
                    closeWindow();
                    settingsBaseLoad(id);
                }
                else{
                    if(composite == 0) addBorderRed("code_1");
                    else addBorderRed("code_2");
                }
            }
        });
    }
}
function settingsBaseStorageAdd2(id, id_2){       // Добавление хранилища в хранилище
    base = id;
    mother = id_2;
    composite = 0;
    code = $("#code").val();
    count = $("#count").val();
    name = $("#name").val();

    if(code == "") addBorderRed("code");
    if(count == "") addBorderRed("count");
    if(name == "") addBorderRed("name");
    if($(".border_red").length == 0){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsBaseStorageAdd2", base : base, mother : mother, composite : composite, code : code, count : count, name : name},
            success: function(data){
                if(data == 1){
                    closeWindow();
                    settingsBaseLoad(id);
                }
                else addBorderRed("code");
            }
        });
    }
}
function settingsBaseRedact(id){      // Редактирование базы
    name = $("#name").val();
    color = $("#color").val();
    code = $("#code").val();
    address = $("#address").val();
    if($("#tumbler_1").hasClass("tumbler_active")) vydacha = 1; else vydacha = 0;
    time_1 = $("#time_1").val();
    time_2 = $("#time_2").val();

    if(name == "") addBorderRed("name");
    if(code == "") addBorderRed("code");
    if(address == "") addBorderRed("address");
    if(time_1 == "") addBorderRed("time_1");
    if(time_2 == "") addBorderRed("time_2");

    if($(".border_red").length == 0){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsBaseRedact", id : id, name : name, color : color, code : code, address : address, vydacha : vydacha, time_1 : time_1, time_2 : time_2},
            success: function(data){
                settingsLoad("bases", id);
                closeWindow();
            }
        });
    }
}
function settingsBaseStorageRedact(id){       // Редактирование хранилища
    name = $("#name").val();
    code = $("#code").val();
    if($("#count").length != 0) count = $("#count").val(); else count = 0;
    if(code == "") addBorderRed("code");
    if(count == "") addBorderRed("count");
    if(name == "") addBorderRed("name");

    if($(".border_red").length == 0){
        $.ajax({
            url: "../../../../ajax/admin/settings.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "settingsBaseStorageRedact", id : id, code : code, count : count, name : name},
            success: function(data){
                switch(data){
                    case  "1": closeWindow(); location.reload(); break;
                    case "-1": addBorderRed("count"); break;
                    case "-2": addBorderRed("code"); break;
                }
            }
        });
    }
}
function settingsCashReportDownload(id){    // Скачивание любого отчета за день
    $.ajax({
        url: "../../../../ajax/admin/excel.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "cashReport", base : 0, id : id, param : 2},
        success: function(data) {
            getFile(data);
            console.log(data);
        }
    });
}
function settingsChangePriceSet(){
    grossMargin = $("#gross_price_margin").val();
    retailMargin = $("#retail_price_margin").val();
    let priceSet = {'gross':grossMargin, 'retail':retailMargin};
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsPriceSet", settingStr: JSON.stringify(priceSet)},
        success: function(data) {
            windowNotification(data, 'ОК');
        }
    });
}







