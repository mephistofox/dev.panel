function servicesStart(){   // Загрузка общей плашки
    $.ajax({
        url: "../../../../ajax/admin/services.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "servicesStart"},
        success: function(data) {
            $("#services").html(data);
            selectLoad();
            butLoad();
            servicesSearch();
        }
    });
}
function servicesSearch(param){    // Загрузка списка услуг согласно поиску
    if($("#name").length > 0 && $("#name").val().length > 2) name = $("#name").val(); else name = "";
    if($("#description").length > 0 && $("#description").val().length > 2) description = $("#description").val(); else description = "";
    if($("#note").length > 0 && $("#note").val().length > 2) note = $("#note").val(); else note = "";
    if(param == 1){
        if($("#article").children("triangle").hasClass("active2") || $("#article").children("triangle").hasClass("active")){
            if($("#article").children("triangle").hasClass("active2")) article = 1;
            else article = 2;
        }
        else article = 1;
    }
    else article = 0;
    if(param == 2){
        if($("#price_purchase").children("triangle").hasClass("active2") || $("#price_purchase").children("triangle").hasClass("active")){
            if($("#price_purchase").children("triangle").hasClass("active2")) price = 1;
            else price = 2;
        }
        else price = 1;
    }
    else price = 0;

    $.ajax({
        url: "../../../../ajax/admin/services.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "servicesSearch",
            name : name,
            description : description,
            note : note,
            article : article,
            price : price
        },
        success: function(data) {
            $("#services_body_list").html(data);
        }
    });
}
function servicesAddTypeChange(){    // Переключает видимость у типов машин при добавлении услуги
    if($("#checkbox_1").prop("checked")){
        $("#cc_str_1").css("display", "none");
        $("#cc_str_2").css("display", "inline-block");
    }
    else {
        $("#cc_str_1").css("display", "inline-block");
        $("#cc_str_2").css("display", "none");
    }
}
function servicesAdd(){         // Добавление новой услуги
    barcode = $("#barcode_add").val();
    name = $("#name_add").val();
    description = $("#description_add").val();
    note = $("#note_add").val();
    count = $("#defcount_1").val();
    if($("#checkbox_1").prop("checked")){
        price_1 = $("#price_1_add").val();
        price_2 = $("#price_2_add").val();
        price_3 = $("#price_3_add").val();

        if(price_1 == "") addBorderRed("price_1_add");
        if(price_2 == "") addBorderRed("price_2_add");
        if(price_3 == "") addBorderRed("price_3_add");

        type_auto = 1;
    }
    else {
        price_1 = $("#price_add").val();
        price_2 = 0;
        price_3 = 0;
        if(price_1 == "") addBorderRed("price_add");

        type_auto = 0;
    }

    if(barcode == "") addBorderRed("barcode_add");
    if(name == "") addBorderRed("name_add");
    if(description == "") addBorderRed("description_add");

    if($(".border_red").length == 0) $.ajax({
        url: "../../../../ajax/admin/services.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "servicesAdd",
            barcode : barcode,
            name : name,
            description : description,
            note : note,
            count : count,
            type_auto : type_auto,
            price_1 : price_1,
            price_2 : price_2,
            price_3 : price_3
        },
        success: function(data) {
            servicesSearch();
            closeWindow();
        }
    });
}
function servicesRedact(id){         // Редактирование услуги
    barcode = $("#barcode_add").val();
    name = $("#name_add").val();
    description = $("#description_add").val();
    note = $("#note_add").val();
    count = $("#defcount_1").val();
    if($("#checkbox_1").prop("checked")){
        price_1 = $("#price_1_add").val();
        price_2 = $("#price_2_add").val();
        price_3 = $("#price_3_add").val();

        if(price_1 == "") addBorderRed("price_1_add");
        if(price_2 == "") addBorderRed("price_2_add");
        if(price_3 == "") addBorderRed("price_3_add");

        type_auto = 1;
    }
    else {
        price_1 = $("#price_add").val();
        price_2 = 0;
        price_3 = 0;
        if(price_1 == "") addBorderRed("price_add");

        type_auto = 0;
    }

    if(barcode == "") addBorderRed("barcode_add");
    if(name == "") addBorderRed("name_add");
    if(description == "") addBorderRed("description_add");

    if($(".border_red").length == 0) $.ajax({
        url: "../../../../ajax/admin/services.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "servicesRedact",
            id : id,
            barcode : barcode,
            name : name,
            description : description,
            note : note,
            count : count,
            type_auto : type_auto,
            price_1 : price_1,
            price_2 : price_2,
            price_3 : price_3
        },
        success: function(data) {
            servicesSearch();
            closeWindow();
        }
    });
}
function servicesSeasonAdd(){        // Учет сезонного хранения
    date = $("#input_calendar").val();
    fio = $("#fio_add").val();
    phone = $("#phone_add").val();
    price = $("#price_add").val();
    shink = $("#shinka_add").val();
    desc = "";
    if($("#tumbler_1").hasClass("tumbler_active")){
        desc += "Резина " + $("#defcount_1").val() + "шт.";
        if($("#doublebutton_1 .active").html() == "Зима") desc += " зима"; else desc += " лето";
        desc += " " + $("#rezina_add").val();
        desc += " " + $("#diametr_add headline").html();
    }
    if($("#tumbler_2").hasClass("tumbler_active")){
        desc += "\nДиски " + $("#defcount_1").val() + "шт.";
        desc += " " + $("#disk_add").val();
        desc += " " + $("#diametr_add headline").html();
    }
    $.ajax({
        url: "../../../../ajax/admin/services.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "servicesSeasonAdd", date : date, fio : fio, phone : phone, desc : desc, price : price, shink : shink},
        success: function(data) {
            $("#window_3").fadeOut();
            //alert(data);
            getFile("../../temp/" + data);
            windowNotification("Сезонное хранение успешно добавлено!", "Хорошо");
        }
    });
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "productSaleAdd", id : price, type : 5, param : 0},
        success: function(data) {
            //alert(data);
        }
    });
}
















