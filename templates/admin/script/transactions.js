PRODUCT = 0;
SALE_BASE = 0;
PRODUCT_NUMBER = 1;
PRICE_ALL = 0;
SALE_ID = 0;
BASE_SALE = -1;
function transactionsStart(){    // Загрузка продаж
    $.ajax({
        url: "../../../../ajax/admin/transactions.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "transactionsStart"},
        success: function(data) {
            $("#transactions").html(data);
            butLoad();
            selectLoad();
            calenderActivate("calendar_1", undefined, "transactionsSearch();");
            calenderActivate("calendar_2", undefined, "transactionsSearch();");
            transactionsCalenderActivate();
            transactionsSearch();
        }
    });
}
function transactionsSearch(param, param_2){    // Загрузка списка операций согласно поиску
    if($("#number").length > 0 && $("#number").val().length > 1) number = $("#number").val(); else number = "";
    if(param == 2){
        if($("#date").children("triangle").hasClass("active2") || $("#date").children("triangle").hasClass("active")){
            if($("#date").children("triangle").hasClass("active2")) date = 1;
            else date = 2;
        }
        else date = 1;
    }
    else date = 0;
    if(param == 3){
        if($("#summa").children("triangle").hasClass("active2") || $("#summa").children("triangle").hasClass("active")){
            if($("#summa").children("triangle").hasClass("active2")) summa = 1;
            else summa = 2;
        }
        else summa = 1;
    }
    else summa = 0;
    if(param == 4) type = param_2; else {if($("#type > input").length > 0) type = $("#type > input").val(); else type = -1;}
    if(param == 5) cashier = param_2; else {if($("#cashier > input").length > 0) cashier = $("#cashier > input").val(); else cashier = -1;}
    if(param == 6) oplata = param_2; else {if($("#oplata > input").length > 0) oplata = $("#oplata > input").val(); else oplata = -1;}
    if(param == 7) client = param_2; else {if($("#client > input").length > 0) client = $("#client > input").val(); else client = -1;}
    if(param == 8) base = param_2; else base = BASE_SALE;

    date_1 = $("#input_calendar_1").val();
    date_2 = $("#input_calendar_2").val();
    if(date_1 == "") date_1 = 0;
    if(date_2 == "") date_2 = 0;

    $.ajax({
        url: "../../../../ajax/admin/transactions.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "transactionsSearch",
            number : number,
            date : date,
            summa : summa,
            type : type,
            cashier : cashier,
            oplata : oplata,
            client : client,
            date_1 : date_1,
            date_2 : date_2,
            base : base
        },
        success: function(data) {
            //alert(data);
            $("#transactions_body_list").html(data);
            //selectLoad(".sale_item");
            //copyReady();
        }
    });
}
function transactionsBaseChange(that){        // Смена выбранной базы
    $("#transactions_head_bases_1").children("item.active").removeClass("active");
    $(that).addClass("active");
    BASE_SALE = $(that).attr("data");
}
function transactionsDateTypeChange(that){     // Смена даты
    $("#transactions_head_dates").children("item.active").removeClass("active");
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
    transactionsSearch();
}
function transactionsCalenderActivate(){    // При изменении значений даты активация поиска
    $("#input_calendar_1, #input_calendar_2").bind("change", function(){
        transactionsSearch();
    });
}






