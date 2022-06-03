PRODUCT = 0;
function movementsStart(param){
    $.ajax({
        url: "../../../../ajax/admin/movements.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "movementsStart"},
        success: function(data) {
            $("#movements").html(data);
            butLoad();
            selectLoad();
            calenderActivate("calendar_1", undefined, "movementsSearch();");
            calenderActivate("calendar_2", undefined, "movementsSearch();");
            movementsCalenderActivate();
            if(param != undefined) movementsSearch(10, param);
            else movementsSearch();
        }
    });
}
function movementsSearch(param, param_2){    // Загрузка списка движений согласно поиску
    if(param == 1){
        if($("#number").children("triangle").hasClass("active2") || $("#number").children("triangle").hasClass("active")){
            if($("#number").children("triangle").hasClass("active2")) number = 1;
            else number = 2;
        }
        else number = 1;
    }
    else number = 0;
    if(param == 2){
        if($("#date").children("triangle").hasClass("active2") || $("#date").children("triangle").hasClass("active")){
            if($("#date").children("triangle").hasClass("active2")) date = 1;
            else date = 2;
        }
        else date = 1;
    }
    else date = 0;
    if(param == 3) action = param_2; else {if($("#action > input").length > 0) action = $("#action > input").val(); else action = -1;}
    if(param == 4){
        if($("#count").children("triangle").hasClass("active2") || $("#count").children("triangle").hasClass("active")){
            if($("#count").children("triangle").hasClass("active2")) count = 1;
            else count = 2;
        }
        else count = 1;
    }
    else count = 0;
    if(param == 5) kuda = param_2; else {if($("#kuda > input").length > 0) kuda = $("#kuda > input").val(); else kuda = -1;}
    if(param == 6) otkuda = param_2; else {if($("#otkuda > input").length > 0) otkuda = $("#otkuda > input").val(); else otkuda = -1;}
    if(param == 7) cureer = param_2; else {if($("#cureer > input").length > 0) cureer = $("#cureer > input").val(); else cureer = -1;}
    if(param == 8) base = param_2; else base = $("#movements_head_bases").children("item.active").attr("data");

    product = 0;
    if(param == 10){
        PRODUCT = param_2;
        if(PRODUCT == 0) $("#move_object").html("Выбрать");
        else $("#move_object").html(param_2 + "<circleCross2 onClick = 'movementsSearch(10,0);'></circleCross2>");
    }
    if(param == 11){
        if($("#date_plan").children("triangle").hasClass("active2") || $("#date_plan").children("triangle").hasClass("active")){
            if($("#date_plan").children("triangle").hasClass("active2")) date_plan = 1;
            else date_plan = 2;
        }
        else date_plan = 1;
    }
    else date_plan = 0;

    date_1 = $("#input_calendar_1").val();
    date_2 = $("#input_calendar_2").val();
    if(date_1 == "") date_1 = 0;
    if(date_2 == "") date_2 = 0;

    $.ajax({
        url: "../../../../ajax/admin/movements.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "movementsSearch",
            number : number,
            date : date,
            date_plan : date_plan,
            action : action,
            count : count,
            kuda : kuda,
            otkuda : otkuda,
            cureer : cureer,
            base : base,
            date_1 : date_1,
            date_2 : date_2,
            product : PRODUCT
        },
        success: function(data) {
            $("#movements_body_list").html(data);
            selectLoad(".movement_item");
            //copyReady();
        }
    });
}
function movementsBaseChange(that){        // Смена выбранной базы
    $("#movements_head_bases").children("item.active").removeClass("active");
    $(that).addClass("active");
}
function movementsDateTypeChange(that){
    $("#movements_head_dates").children("item.active").removeClass("active");
    $(that).addClass("active");
    param = $(that).attr("data");

    var today = new Date();
    if(param == 1){
        month = today.getMonth()+1;
        year = today.getFullYear();
        if(month < 10) month = "0" + month;
        $("#input_calendar_1").val("01." + month + "." + year);

        var tomorrow = new Date(today.getTime());
        var d = tomorrow.getDate();
        if(d < 10) d = "0" + d;

        $("#input_calendar_2").val(d + "." + month + "." + year);
    }
    if(param == 2){
        var tomorrow = new Date(today.getTime() - (24 * 60 * 60 * 1000));
        var d = tomorrow.getDate();
        var m = tomorrow.getMonth() + 1; //в js месяц отсчитывается с нуля
        var y = tomorrow.getFullYear();
        if(d < 10) d = "0" + d;
        if(m < 10) m = "0" + m;
        $("#input_calendar_1").val(d + "." + m + "." + y);
        $("#input_calendar_2").val(d + "." + m + "." + y);
    }
    if(param == 3){
        var tomorrow = new Date(today.getTime());
        var d = tomorrow.getDate();
        var m = tomorrow.getMonth() + 1; //в js месяц отсчитывается с нуля
        var y = tomorrow.getFullYear();
        if(d < 10) d = "0" + d;
        if(m < 10) m = "0" + m;
        $("#input_calendar_1").val(d + "." + m + "." + y);
        $("#input_calendar_2").val(d + "." + m + "." + y);
    }
     movementsSearch();
}
function movementsCalenderActivate(){    // При изменении значений даты активация поиска
    $("#input_calendar_1, #input_calendar_2").bind("change", function(){
        movementsSearch();
    });
}
function movementsReceiptConfirmation(id){     // Подтверждение приемки товара
    $.ajax({
        url: "../../../../ajax/admin/movements.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "movementsReceiptConfirmation", id : id},
        success: function(data) {
            movementsSearch();
        }
    });
}
function movementsDelete(id){       // Удаление движения
    $.ajax({
        url: "../../../../ajax/admin/movements.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "movementsDelete", id : id},
        success: function(data) {
            movementsSearch();
            closeWindow();
        }
    });
}
function movementRedact(id){        // Редактирование движения
    action_type = $("#movement_type").val();

    provider = 0;
    storage = 0;
    date_plan = 0;
    otkuda = 0;
    kuda = 0;
    osnovanie = 0;
    osnovanie_text = 0;
    cureer = 0;
    kuda_base = 0;

    if(action_type == 1){
        if($("#storage_hidden_id").val() == "-1") addBorderRed("storage_1");
        provider = $("#provider_hidden").val();
        storage = $("#storage_hidden_id").val();
        date_plan = $("#input_move_date_plan").val();
    }
    if(action_type == 2){
        otkuda = $(".storage_str_active").attr("data_2");
        date_plan = $("#input_move_date_plan").val();
        osnovanie = $("#osnovanie_hidden").val();
        if(osnovanie == -2) osnovanie_text = $("#osnovanie_textarea").val();
    }
    if(action_type == 3){
        if($("#storage_hidden_id").val() == "-1") addBorderRed("storage_1");
        otkuda = $(".storage_str_active").attr("data_2");
        kuda = $("#storage_hidden_id").val();
        date_plan = $("#input_move_date_plan").val();
        cureer = $("#cureer_2_hidden").val();
        kuda_base = $("#base_1_hidden").val();
    }
    if(action_type == 4){
        if($("#storage_hidden_id").val() == "-1") addBorderRed("storage_1");
        kuda = $("#storage_hidden_id").val();
        date_plan = $("#input_move_date_plan").val();
        cureer = $("#cureer_2_hidden").val();
        provider = $("#provider_hidden").val();
    }

    if($(".border_red").length == 0){
        $.ajax({
            url: "../../../../ajax/admin/movements.php",
            dataType: "html",
            type: "POST",
            data: {
                methodName : "movementRedact",
                id : id,
                provider : provider,
                storage : storage,
                date_plan : date_plan,
                otkuda : otkuda,
                kuda : kuda,
                osnovanie : osnovanie,
                osnovanie_text : osnovanie_text,
                cureer : cureer,
                kuda_base : kuda_base
            },
            success: function(data) {
                //alert(data);
                if(data == "1"){
                    movementsSearch();
                    closeWindow();
                }
                //movementsSearch();
                //closeWindow();
            }
        });
    }

}

