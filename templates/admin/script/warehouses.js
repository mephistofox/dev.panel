BASE = 0;
STORAGE = 0;
function warehousesStart(){    // Загрузка стартовой
    $.ajax({
        url: "../../../../ajax/admin/warehouses.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "warehousesStart"},
        success: function(data) {
            mas = data.split(SEP);
            $("#warehouses").html(mas[0]);
            if(catC == "") warehouseBaseActiveChange(mas[1], mas[2]);
            else $("#base_" + catC).click();
        }
    });
}
function warehouseBaseActiveChange(id, color, that){    // Загрузка базы
    if(BASE != 0 && BASE != id) catE = "";
    if(BASE != id) BASE = id;

    catC = id;
    //catD = "";
    //catE = "";
    $("#warehouses_bases_list > item").css("background", "linear-gradient(180deg, #EEEEEE 0%, #D8D8D8 100%)");
    $("#warehouses_bases_list > item").css("box-shadow", "1px 1px 3px rgba(0, 0, 0, 0.5)");
    $("#warehouses_bases_list > item").css("color", "#000000");
    if(that === undefined){
        $("#warehouses_bases_list > item").first().css("background", "#" + color);
        $("#warehouses_bases_list > item").first().css("box-shadow", "inset 0px 2px 3px rgba(0, 0, 0, 0.5)");
        $("#warehouses_bases_list > item").first().css("color", "#ffffff");
    }
    else{
        $(that).css("background", "#" + color);
        $(that).css("box-shadow", "inset 0px 2px 3px rgba(0, 0, 0, 0.5)");
        $(that).css("color", "#ffffff");
    }
    $.ajax({
        url: "../../../../ajax/admin/warehouses.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "warehousesBaseLoad", id : id},
        success: function(data) {
            mas = data.split(SEP);
            $("#warehouses_middle_info").html(mas[0]);
            $("#warehouses_middle_count").html(mas[1]);
            history.pushState(null, null, SERVER + "cp/warehouses/" + id);

            if(catD == "") warehousesLoad(1);
            else{
                switch(catD){
                    case "tires" : warehousesLoad(1); break;
                    case "disks" : warehousesLoad(2); break;
                    case "products" : warehousesLoad(3); break;
                    case "services" : warehousesLoad(4); break;
                }
            }

        }
    });

}
function warehousesLoad(param){      // Загрузка шин, дисков, товаров и услуг
    $("#warehouses_bottom_head > item").removeClass("active");
    $("#param_" + param).addClass("active");
    $.ajax({
        url: "../../../../ajax/admin/warehouses.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "warehousesLoad", base : BASE, param : param},
        success: function(data) {
            switch(param){
                case 1: history.pushState(null, null, SERVER + "cp/warehouses/" + BASE + "/tires"); break;
                case 2: history.pushState(null, null, SERVER + "cp/warehouses/" + BASE + "/disks"); break;
                case 3: history.pushState(null, null, SERVER + "cp/warehouses/" + BASE + "/products"); break;
                case 4: history.pushState(null, null, SERVER + "cp/warehouses/" + BASE + "/services"); break;
            }
            if(param < 3){
                mas = data.split(SEP);
                $("#warehouses_bottom_all").css("display", "none");
                $("#warehouses_bottom_left").html(mas[0]).css("display", "inline-block");
                //$("#warehouses_bottom_right").html(mas[1]).css("display", "inline-block");
                $("percent_left").css("background", mas[1]);
                //alert(catE);
                if(catE == "") $(".storage").first().click();
                else{
                    warehousesStorageLoad(catE, param);
                }
            }
            if(param > 2){
                $("#warehouses_bottom_left").css("display", "none");
                $("#warehouses_bottom_right").css("display", "none");
                $("#warehouses_bottom_all").html(data).css("display", "block");
                butLoad();
                if(param == 3){
                    warehousesProductsSearch();
                }
            }
        }
    });
}
function warehousesServicesSave(){   // Сохранение данных по доступным услугам
    str = "";
    $("checkbox > input").each(function(){
        str += $(this).attr("id");
        str += ":";
        if($(this).prop("checked") == true) str += 1; else str += 0;
        str += SEP;
    });
    $.ajax({
        url: "../../../../ajax/admin/warehouses.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "warehousesServicesSave", base : BASE, str : str},
        success: function(data) {
            windowNotification("Настройки услуг для данной базы успешно сохранены!", "Хорошо");
        }
    });
}
function warehousesProductsSearch(param){       // Загрузка продуктов согласно поиска
    if(param == 1){
        if($("#count").children("triangle").hasClass("active2") || $("#count").children("triangle").hasClass("active")){
            if($("#count").children("triangle").hasClass("active2")) count = 1;
            else count = 2;
        }
        else count = 1;
    }
    else count = 0;
    if($("#name").val().length > 2) name = $("#name").val(); else name = "";
    $.ajax({
        url: "../../../../ajax/admin/warehouses.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "warehousesProductsSearch", base : BASE, name : name, count : count},
        success: function(data) {
            //alert(data);
            $("#warehouse_product_bottom_list").html(data);
        }
    });
}
function warehousesStorageLoad(id, param){         // Загрузка выбранного хранилища
    STORAGE = id;
    catE = id;
    if($("#storage_" + id).hasClass("storage")){
        $(".storage_active").removeClass("storage_active");
        $(".storage_son_active").removeClass("storage_son_active");
        $("#storage_" + id).addClass("storage_active");
        $(".storage_son").css("display", "none");
    }
    else{
       $(".storage_son_active").removeClass("storage_son_active");
       $("#storage_" + id).addClass("storage_son_active");
    }
    if(param === undefined) param = 1;
    $.ajax({
        url: "../../../../ajax/admin/warehouses.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "warehousesStorageLoad", id : id, param : param},
        success: function(data) {
            //alert(data);
            mas = data.split(SEP);
            $("#warehouses_bottom_right").html(mas[0]).css("display", "inline-block");
            if(mas[1] == 0) $(".change_visible").css("display", "none"); else $(".change_visible").css("display", "block");
            butLoad();
            if(param == 2){
                history.pushState(null, null, SERVER + "cp/warehouses/" + BASE + "/disks/" + id);
                warehousesDisksSearch();
            }
            else{
                warehousesTiresSearch();
                history.pushState(null, null, SERVER + "cp/warehouses/" + BASE + "/tires/" + id);
            }
        }
    });

}
function warehousesStorageCompositeOpen(id){     // Открывает вложенные хранилища
    $(".storage_active").removeClass("storage_active");
    $(".storage_son").css("display", "none");
    $("#storage_" + id).addClass("storage_active");
    $("#storage_" + id + " > .storage_son").css("display", "block");
}
function warehousesTiresSearch(param){     // Загрузка шин согласно поиска
    if(param == 1){
        if($("#count").children("triangle").hasClass("active2") || $("#count").children("triangle").hasClass("active")){
            if($("#count").children("triangle").hasClass("active2")) count = 1;
            else count = 2;
        }
        else count = 1;
    }
    else count = 0;
    if($("#name").val().length > 2) name = $("#name").val(); else name = "";
    $.ajax({
        url: "../../../../ajax/admin/warehouses.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "warehousesTiresSearch", storage : STORAGE, name : name, count : count},
        success: function(data) {
            //alert(data);
            $("#storage_middle_list").html(data);

        }
    });
}
function warehousesDisksSearch(param){     // Загрузка дисков согласно поиска
    if(param == 1){
        if($("#count").children("triangle").hasClass("active2") || $("#count").children("triangle").hasClass("active")){
            if($("#count").children("triangle").hasClass("active2")) count = 1;
            else count = 2;
        }
        else count = 1;
    }
    else count = 0;
    if($("#name").val().length > 2) name = $("#name").val(); else name = "";
    $.ajax({
        url: "../../../../ajax/admin/warehouses.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "warehousesDisksSearch", storage : STORAGE, name : name, count : count},
        success: function(data) {
            //alert(data);
            $("#storage_middle_list").html(data);

        }
    });
}
function warehousesTireLoad(that){       // Зажимает плашку с шиной
    $(".storage_middle_list_item_active").removeClass("storage_middle_list_item_active");
    $(".action").css("visibility", "hidden");
    $(that).addClass("storage_middle_list_item_active");
    $(that).children(".action").css("visibility", "visible");

    $(".storage_middle_list_item2").css("display", "none");

    id = $(that).attr("data");
    $("#tire_block_bottom_" + id).css("display", "block");
}
function warehousesBaseTimeProof(that){    // Проверка правильности введенного времени
    time = $(that).val();
    deleteBorderRed(that);
    mas = time.split(":");
    if(mas[0] < 0 || mas[0] > 23 || mas[1] < 0 || mas[1] > 59){
        time = "00:00";
        $(that).val(time);
        addBorderRed($(that).attr("id"));
    }
}
function warehousesBaseTimeChange(id){
    time_1 = $("#time_1").val();
    time_2 = $("#time_2").val();
    if(time_1 == "") addBorderRed("time_1");
    if(time_2 == "") addBorderRed("time_2");
    if($(".border_red").length == 0){
        $.ajax({
            url: "../../../../ajax/admin/warehouses.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "warehousesBaseTimeChange", id : id, time_1 : time_1, time_2 : time_2},
            success: function(data) {
                closeWindow();
                $("#base_" + id).click();
            }
        });
    }
}







