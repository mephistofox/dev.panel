PRODUCT = 0;
SALE_BASE = 0;
PRODUCT_NUMBER = 1;
PRICE_ALL = 0;
SALE_ID = 0;
TIMER = 0;
SALE_STATUS = 0;
function salesStart(){    // Загрузка продаж
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesStart"},
        success: function(data) {
            mas = data.split(SEP);
            $("#sales").html(mas[0]);
            butLoad();
            selectLoad();
            calenderActivate("calendar_1", undefined, "salesSearch();");
            calenderActivate("calendar_2", undefined, "salesSearch();");
            salesCalenderActivate();
            $("#button_today").click();
            if(mas[1] != 0) $("#base_num_" + mas[1]).click();
            salesSearch();

        }
    });
}
function salesSearch(param, param_2){    // Загрузка списка продаж согласно поиску
    if($("#number").length > 0 && $("#number").val().length > 1) number1 = $("#number").val(); else number1 = "";
    if(param == 2) status1 = param_2; else {if($("#status > input").length > 0) status1 = $("#status > input").val(); else status1 = -1;}
    if(param == 3) poluchenie1 = param_2; else {if($("#poluchenie > input").length > 0) poluchenie1 = $("#poluchenie > input").val(); else poluchenie1 = -1;}
    if(param == 4){
        if($("#date").children("triangle").hasClass("active2") || $("#date").children("triangle").hasClass("active")){
            if($("#date").children("triangle").hasClass("active2")) date1 = 1;
            else date1 = 2;
        }
        else date1 = 1;
    }
    else date1 = 0;
    if(param == 5){
        $("#base_sale_hidden").val(param_2);
        base_sale1 = param_2;
        $("#sales_head_bases_1 item").removeClass("active");
        $("#base_num_" + param_2).addClass("active");
        temp = $("#sales_head_bases_1 item.active").html();
        if(temp == "Все") temp = "База";
        $("#base_sale headline").html(temp);
    }  else {if($("#base_sale > input").length > 0) base_sale1 = $("#base_sale > input").val(); else base_sale1 = -1;}
    if($("#client").length > 0 && $("#client").val().length > 1) client1 = $("#client").val(); else client1 = "";
    if(param == 6) cureer1 = param_2; else {if($("#cureer > input").length > 0) cureer1 = $("#cureer > input").val(); else cureer1 = -1;}
    if(param == 7) delivery1 = param_2; else {if($("#delivery > input").length > 0) delivery1 = $("#delivery > input").val(); else delivery1 = -1;}
    if(param == 9){
        if($("#price_purchase").children("triangle").hasClass("active2") || $("#price_purchase").children("triangle").hasClass("active")){
            if($("#price_purchase").children("triangle").hasClass("active2")) price_purchase1 = 1;
            else price_purchase1 = 2;
        }
        else price_purchase1 = 1;
    }
    else price_purchase1 = 0;
    if(param == 10){
        if($("#price_sale").children("triangle").hasClass("active2") || $("#price_sale").children("triangle").hasClass("active")){
            if($("#price_sale").children("triangle").hasClass("active2")) price_sale1 = 1;
            else price_sale1 = 2;
        }
        else price_sale1 = 1;
    }
    else price_sale1 = 0;
    if(param == 11) oplata1 = param_2; else {if($("#oplata_hidden0").length > 0) oplata1 = $("#oplata_hidden0").val(); else oplata1 = -1;}
    if(param == 12){
        if($("#skidka_percent").children("triangle").hasClass("active2") || $("#skidka_percent").children("triangle").hasClass("active")){
            if($("#skidka_percent").children("triangle").hasClass("active2")) skidka_percent1 = 1;
            else skidka_percent1 = 2;
        }
        else skidka_percent1 = 1;
    }
    else skidka_percent1 = 0;
    if(param == 13){
        if($("#skidka_ruble").children("triangle").hasClass("active2") || $("#skidka_ruble").children("triangle").hasClass("active")){
            if($("#skidka_ruble").children("triangle").hasClass("active2")) skidka_ruble1 = 1;
            else skidka_ruble1 = 2;
        }
        else skidka_ruble1 = 1;
    }
    else skidka_ruble1 = 0;
    if(param == 14) manager1 = param_2; else {if($("#manager > input").length > 0) manager1 = $("#manager > input").val(); else manager1 = -1;}

    date_11 = $("#input_calendar_1").val();
    date_21 = $("#input_calendar_2").val();
    if(date_11 == "") date_11 = 0;
    if(date_21 == "") date_21 = 0;

    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "salesSearch",
            number : number1,
            status : status1,
            poluchenie : poluchenie1,
            date : date1,
            base_sale : base_sale1,
            client : client1,
            cureer : cureer1,
            delivery : delivery1,
            price_purchase : price_purchase1,
            price_sale : price_sale1,
            oplata : oplata1,
            skidka_percent : skidka_percent1,
            skidka_ruble : skidka_ruble1,
            manager : manager1,
            date_1 : date_11,
            date_2 : date_21
        },
        success: function(data) {
            //alert(data);
            $("#sales_body_list").html(data);
            //selectLoad(".sale_item");
            //copyReady();
        }
    });
}
function salesBaseChange(that){        // Смена выбранной базы
    $("#sales_head_bases_1").children("item.active").removeClass("active");
    $(that).addClass("active");
}
function salesDateTypeChange(that){
    $("#sales_head_dates").children("item.active").removeClass("active");
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
    salesSearch();
}
function salesCalenderActivate(){    // При изменении значений даты активация поиска
    $("#input_calendar_1, #input_calendar_2").bind("change", function(){
        salesSearch();
    });
}
function salesAddBaseChange(that){        // Смена выбранной базы при добавлении заказа
    $("#sales_head_bases_2").children("item.active").removeClass("active");
    $(that).addClass("active");
    SALE_BASE = $(that).attr("data");
    mas = new Array();
    $(".pl").each(function(){
        number = $(this).children(".pl_left").children("name").html();
        param = $(this).children("price_param").html();
        mas.push(number + "." + param);
    })
    if(mas.length > 0){
        $("#sa_products").html("");
        PRODUCT_NUMBER = 1;
        for(i = 0; i < mas.length; i++){
            salesAddProductAdd(mas[i]);
        }
    }
}
function salesBarcodeProof(that){   // Проверка содержимого поля штрих-кода
    val = $(that).val();
    if(val.length > 0) $("#sa_head > div").css("display", "block");
    else $("#sa_head > div").css("display", "none");
}
function salesAddProductAdd(param){     // Добавление товара к заказу
    if(param === undefined) barcode = $("#barcode").val() + ".0";
    else barcode = param;
    payer = ($("#payer_1_hidden").val())?$("#payer_1_hidden").val():$("#payer_1").val();
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesAddProductAdd", barcode : barcode, base : SALE_BASE, payer : payer},
        success: function(data) {
            //alert(data);
            mas = data.split(SEP);
            if(mas[2] == -1){
                addBorderOrange("barcode");
                $("#barcode").val("");
            }
            else{
                if(mas[0] == -1) addBorderRed("barcode");
                else{
                    $("#barcode").val("");
                    mas[1] = mas[1].replace("%NUMBER%", PRODUCT_NUMBER);
                    if($("#" + mas[0]).length == 0){
                        $("#sa_products").append(mas[1]);
                        PRODUCT_NUMBER++;
                        inputNumber();
                        salesAddCalculatePriceAll();
                        if(SALE_STATUS > 0) salesButtonSaveView();
                    }
                }
            }
            salesAddCalculatePriceAll();

            if(payer == "-1"){
                salesPayersTires(barcode);
            }

            //$("#sales_body_list").html(data);
            //selectLoad(".sale_item");
            //copyReady();
        }
    });

}
function salesPayersTires(barcode){     // Отдает список плательщиков, у которых есть этот товар. Иначе убирает товар.
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesPayersTires", barcode : barcode},
        success: function(data) {
            if(data != "-1"){
                $("#sa_payer_1").html("<span>Плательщик</span>" + data);
                selectLoad();
                count = 0;
                $("#payer_1").children("div").each(function(){
                    count++;
                })
                if(count == 1){
                    $("#payer_1").off().children("arrow").addClass("hide");
                }
            }
        }
    });
}
function salesPayerChange(that){
    payer = $(that).attr("data");
    $("#payer_1_hidden").val(payer);
    mas = [];
    $(".pl").each(function(){
        number = $(this).children(".pl_left").children("name").html();
        param = $(this).children("price_param").html();

        mas.push(number + "." + param);
    })
    if(mas.length > 0){
        $("#sa_products").html("");
        PRODUCT_NUMBER = 1;
        for(i = 0; i < mas.length; i++){
            salesAddProductAdd(mas[i]);
        }
    }
}
function salesButtonSaveView(){     // Заменяет все кнопки на кнопку Сохранить
    $("#window_3_footer").html("<div style = 'display: inline-block;' class = 'button_green inline' onClick = 'buttonClick(this);salesSaleProductListChange();'>Сохранить изменения</div>");
}
function salesSaleProductListChange(){     // Сохраняет изменения в товарах
    $(".pl").each(function(){
        id = $(this).attr("id");
        count = 0;
        count_need = $(this).children(".pl_right").children("input").val();
        if($("#" + id + "_storage").length > 0){
            $("#" + id + "_storage").children().each(function(){
                if($(this).hasClass("bases_item_active")) count += parseInt($(this).children("count").html());
            })
            if(count < count_need) $(this).children(".pl_right").children("input").addClass("border_red");
        }
    })

    count = 0;
    if($(".border_red").length == 0){
        let mas = new Array();
        $(".pl").each(function(){
            barcode = $(this).attr("id");
            count = $(this).children(".pl_right").children("input").val();
            param = $(this).children("price_param").html();
            let temp = {};
            temp.barcode = barcode;
            temp.count = count;
            temp.param = param;
            if(count == 0) flag = false;
            //if($(this).children(".pl_bottom").children(".pl_bottom_left").children(".bases_item_active").length == 0 && $(this).children(".pl_bottom").css("display") == "block") flag = false;
            let storage = new Array();
            $(this).children(".pl_bottom").children(".pl_bottom_left").children(".bases_item_active").each(function(){
                storage.push($(this).children("name").html());
            })
            //if($(this).children(".pl_bottom").css("display") == "block"){
                //temp.dop = 1;
                //temp.dop_base = $(this).children(".pl_bottom").children(".pl_bottom_left").children(".bases_item_active").children("name").html();
                //temp.dop_count = $(this).children(".pl_bottom").children(".pl_bottom_right").children("input").val();
            //}
            //else temp.dop = 0;
            temp.storage = storage;
            mas.push(temp);
            count++;
        })
        mas = JSON.stringify(mas);
        if(count > 0){
            $.ajax({
                url: "../../../../ajax/admin/sales.php",
                dataType: "html",
                type: "POST",
                data: {methodName : "salesSaleProductListChange", json : mas, id : SALE_ID},
                success: function(data) {
                    //$("#window_3").fadeOut();
                    salesSearch();
                    windowSaleView(SALE_ID);
                    //windowNotification("Данные в сделке были обновлены", "Хорошо");
                    //if(data != -1){
                    //    $("#window_3").fadeOut();
                    //    salesSearch();
                    //    windowNotification("Сделка " + data + " была успешно оформлена", "Хорошо");
                    //}
                    //else{
                    //    addBorderRed("client_phone");
                    //}
                }
            });
            //alert(mas);
        }
        else alert("Нет товаров");
    }
}
function salesAddProductDelete(that){  // Удаление продукта при добавлении сделки
    console.log($(that).parent())
    $(that).parent().remove();
    PRODUCT_NUMBER = 1;
    $(".pl_number").each(function(){
        $(this).html(PRODUCT_NUMBER);
        PRODUCT_NUMBER++;
    });
    salesAddCalculatePriceAll();
    if(SALE_STATUS > 0) salesButtonSaveView();
}
function salesAddProductCountChange(that){   // Изменение количества товара
    count = $(that).val();
    id = $(that).parent().parent().attr("id");
    $("#" + id + "_storage").children(".bases_item_active").removeClass("bases_item_active");
    //if(count == 0) count = 1;
    if(count.length > 1 && count[0] == 0) count = count.substr(1);
    if(count[0] == "-") count = count.substr(1);
    $("#window_3_footer .button_green").html("Собрать и забронировать");

    payer_tire_count = $("#" + id).children("payer_tire_count").html();
    if(payer_tire_count != "-1"){
        if(payer_tire_count < count){
            $(that).addClass("border_red");
        }
        else  $(that).removeClass("border_red");
    }

    price = $(that).parent().parent().children("price").html();
    price_new = count * price;
    price_new = priceTroyki(price_new);
    price = priceTroyki(price);
    $(that).parent().parent().children(".pl_center").children("price_all").html(price_new + " ₽");
    $(that).parent().parent().children(".pl_center").children("span").html(count + " x " + price);

    salesAddCalculatePriceAll();

    $(that).val(count);
    if(SALE_STATUS > 0) salesButtonSaveView();

}
function salesAddCalculatePriceAll(){     // Расчет общей стоимости
    PRICE_ALL = 0;
    $(".pl .pl_center price_all").each(function(){
        price = $(this).html();
        price = price.replace(" ","");
        price = price.replace(" ","");
        price = price.replace(" ","");
        price = price.replace(" ","");
        price = price.replace("₽","");
        price = parseInt(price, 10);
        PRICE_ALL += price;
    })
    if(PRICE_ALL == 0) $("#sa_price").html("");
    else $("#sa_price").html(priceTroyki(PRICE_ALL) + " ₽");
}
function salesAddProductBaseProductAdd(that){    // Выбор хранилищ из которых продавать товар
    id = $(that).parent().attr("id");
    count = 0;

    count_need = $(that).parent().parent().parent().children(".pl_right").children("input").val();
    $(that).parent().parent().parent().children(".pl_right").children("input").removeClass("border_red");

    //Здесь что-то не работает
    payer_tire_count = $("#" + id).parent().parent().children("payer_tire_count").html();
    //alert(payer_tire_count + " " + count_need);
    if(payer_tire_count != "-1"){
        if(payer_tire_count < count_need){
            $(that).parent().parent().parent().children(".pl_right").children("input").addClass("border_red");
        }
    }

    $(that).parent().children().each(function(){
        if($(this).hasClass("bases_item_active")) count += parseInt($(this).children("count").html());
    })
    if($(that).hasClass("bases_item_active")) $(that).removeClass("bases_item_active");
    else{
        if(count < count_need) $(that).addClass("bases_item_active");

    }
    if(SALE_STATUS > 0) salesButtonSaveView();
    //count_base = $(that).children("count").html();
    //count_need = $(that).parent().parent().children(".pl_bottom_right").children("input").val();
    //if(parseInt(count_base, 10) >= parseInt(count_need, 10)){
    //    $(that).parent().children(".bases_item").removeClass("bases_item_active");
    //    $(that).addClass("bases_item_active");
    //}
}
//function salesAddProductChangeDopCount(that){    // Изменение количества товара, который нужно привезти из другой базы
//    count = $(that).val();
//    if(count == 0) count = 1;
//    if(count.length > 1 && count[0] == 0) count = count.substr(1);
//    if(count[0] == "-") count = count.substr(1);
//    $(that).val(count);
//    $(that).parent().parent().children(".pl_bottom_left").children(".bases_item").removeClass("bases_item_active");
//}
function salesAddPoluch(param){      // Выбор типа получения товара
    if(param != 2)$.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesAddPoluch", param : param},
        success: function(data) {
            $("#sa_poluch_dop").html(data);
            selectLoad("#sa_poluch_dop .select_base");

        }
    });
    else $("#sa_poluch_dop").html("");
    if(param >= 3){
        $("#sa_cureer").css("display", "block");
    }
    else $("#sa_cureer").css("display", "none");
    $('#saveAddress').show()
}
function salesSaleAdd(){       // Создание новой продажи
    payer_1 = ($("#payer_1_hidden").val())?$("#payer_1_hidden").val():$("#payer_1").val();
    payer_2 = ($("#payer_2_hidden").val())?$("#payer_2_hidden").val():$("#payer_2").val();
    client = $("#client_hidden").val();
    client_contact = $("#client_phone").val();
    poluch = $("#poluch_hidden").val();
    date_plan = $("#input_sa_date_cal").val();
    info = $("#inform").val();
    switch(poluch){
        case "1": poluch_desc = $("#base_sale_param_hidden").val(); break;
        case "2": poluch_desc = "123"; break;
        case "3": poluch_desc = $("#address").val(); break;
        case "4": poluch_desc = $("#tk_param_hidden").val(); break;
        default: poluch_desc = -1;
    }
    if(poluch == -1) addBorderRed("poluch");
    if(poluch_desc == -1 || poluch_desc == ""){
        switch(poluch){
            case "1": addBorderRed("base_sale_param"); break;
            case "3": addBorderRed("address"); break;
            case "4": addBorderRed("tk_param"); break;
        }
    }
    $("#sales_head_otl").remove();
    $(".pl").each(function(){
        id = $(this).attr("id");
        count = 0;
        count_need = $(this).children(".pl_right").children("input").val();
        if($("#" + id + "_storage").length > 0){
            $("#" + id + "_storage").children().each(function(){
                if($(this).hasClass("bases_item_active")) count += parseInt($(this).children("count").html());
            })
            if(count < count_need) $(this).children(".pl_right").children("input").addClass("border_red");
        }
    })
    /*if(payer_1 == -1) addBorderRed("payer_1");*/
    if(payer_2 == -1) addBorderRed("payer_2");
    if(client == -1) addBorderRed("client");
    if(client_contact == "") addBorderRed("client_phone");
    if($(".border_red").length == 0){
        let obj = {};
        obj.payer_1 = payer_1;
        obj.payer_2 = payer_2;
        obj.client = client;
        obj.inform = info;
        obj.date_plan = date_plan;
        obj.client_contact = client_contact;
        obj.poluch = poluch;
        obj.poluch_desc = poluch_desc;
        obj.cureer = $("#cureer_hidden").val();
        obj.base = $("#sales_head_bases_2 .active").attr("data");
        flag = true;
        let mas = new Array();
        $(".pl").each(function(){
            barcode = $(this).attr("id");
            count = $(this).children(".pl_right").children("input").val();
            param = $(this).children("price_param").html();
            let temp = {};
            temp.barcode = barcode;
            temp.count = count;
            temp.param = param;
            if(count == 0) flag = false;
            //if($(this).children(".pl_bottom").children(".pl_bottom_left").children(".bases_item_active").length == 0 && $(this).children(".pl_bottom").css("display") == "block") flag = false;
            let storage = new Array();
            $(this).children(".pl_bottom").children(".pl_bottom_left").children(".bases_item_active").each(function(){
                storage.push($(this).children("name").html());
            })
            //if($(this).children(".pl_bottom").css("display") == "block"){
                //temp.dop = 1;
                //temp.dop_base = $(this).children(".pl_bottom").children(".pl_bottom_left").children(".bases_item_active").children("name").html();
                //temp.dop_count = $(this).children(".pl_bottom").children(".pl_bottom_right").children("input").val();
            //}
            //else temp.dop = 0;
            temp.storage = storage;
            mas.push(temp);
        })
        //mas = JSON.stringify(mas);
        if(mas.length == 0) flag = false;
        obj.mas = mas;
        let json = JSON.stringify(obj);
        if(flag){
            $.ajax({
                url: "../../../../ajax/admin/sales.php",
                dataType: "html",
                type: "POST",
                data: {methodName : "salesSaleAdd", json : json},
                success: function(data) {
                    //alert(data);
                    if(data != -1){
                        $("#window_3").fadeOut();
                        salesSearch();
                        windowNotification("Сделка " + data + " была успешно оформлена", "Хорошо");
                    }
                    else{
                        addBorderRed("client_phone");
                    }
                }
            });
            $.ajax({
                url: "../../../../ajax/admin/clear.php",
                dataType: "html",
                type: "POST",
                success: function(data) {
                    $('#sales_head_otl').remove()
                }
            });
        }

    }

}
function saleViewInfoLog(that){     // Переключение между информацией о сделке и логами по ней
    $(".sale_head_bottom_active").removeClass("sale_head_bottom_active");
    $(that).addClass("sale_head_bottom_active");
    if($(that).attr("data") == 1){
        $("#sale_info").css("display", "block");
        $("#sale_logs").css("display", "none");
    }
    else {
        $("#sale_info").css("display", "none");
        $("#sale_logs").css("display", "block");
    }
}
function salesViewProductDelete(that){  // Удаление продукта при просмотре сделки
    if($(".pl").length > 1){
        id = $(that).parent().attr("data");
        $(that).parent().remove();
        PRODUCT_NUMBER = 1;
        $(".pl_number").each(function(){
            $(this).html(PRODUCT_NUMBER);
            PRODUCT_NUMBER++;
        });
        salesAddCalculatePriceAll();
        $.ajax({
            url: "../../../../ajax/admin/sales.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "salesViewProductDelete", id : id, sale : SALE_ID},
            success: function(data) {
                //alert(data);
                if(SALE_STATUS > 0) salesButtonSaveView();
            }
        });
    }
}
function salesReceiptConfirmation(id, that = 0){     // Подтверждение приемки товара из карточки сделки
    $.ajax({
        url: "../../../../ajax/admin/movements.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "movementsReceiptConfirmation", id : id},
        success: function(data) {
            //$(that).parent().html("<div>" + data + "<gal2></gal2></div>");
            //if($(".button_movements").length == 0) $("#window_3_footer .button_green").css("display", "inline-block");
            windowSaleView(SALE_ID);
        }
    });
}
function salesSaleAddressChange2(that){     // Изменение адреса доставки в уже готовой заявке
    setTimeout(function(){
        val = $(that).val();
        type = $('headline').text()
        $.ajax({
            url: "../../../../ajax/admin/sales.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "salesSaleAddressChange2", id : SALE_ID, address : val}
        });
    }, 1000);
}

function changeAddressSale(id,address,poluch,delivery=false){     // Изменение адреса доставки в уже готовой заявке
    console.log({"id": id, "address": address, "type": poluch, "delivery":delivery})
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesSaleAddressChange2", "id": id, "address": address, "type": poluch, "delivery":delivery}
    });
}

function saveAddress(id){     // Изменение адреса доставки в уже готовой заявке
    let typeOfPoluch = $('#poluch_hidden').val()
    switch (typeOfPoluch) {
        case '1':
            poluch = 'Пункт выдачи'
            address = $(`#base_sale_param>div[data="${$('#base_sale_param_hidden').val()}"]`).text()
            changeAddressSale(id,address,poluch)
            break;
        case '3':
            poluch = 'Доставка'
            address = $('#address').val()
            changeAddressSale(id,address,poluch)
            break;
        case '4':
            poluch = 'Доставка ТК'
            address = $(`#tk_param>div[data="${$('#tk_param_hidden').val()}"]`).text()
            delivery = $('#tk_param_hidden').val()
            changeAddressSale(id,address,poluch,delivery)
            break;
    }
}

function salesSaleSkidka(param){        // Расчет новой цены и скидок
    price_0 = $("#price_0").val();
    if(param == 1){
        value = $("#skidka_percent").val();
        if(value == "") value = 0;
        if(String(parseInt(value, 10)) === String(value)){
            value = parseInt(value, 10);
            if(value > 100) value = 100;
            if(value < 0) value = 0;
            $("#skidka_percent").val(value);
            val2 = price_0*value/100;
            $("#skidka_ruble").val(val2);
        }
    }
    else{
        value = $("#skidka_ruble").val();
        if(value == "") value = 0;
        if(value[0] == 0) value = value.substr(1);
        if(String(parseInt(value, 10)) === String(value)){
            value = parseInt(value, 10);
            if(value > price_0) value = price_0;
            if(value < 0) value = 0;
            $("#skidka_ruble").val(value);
            val2 = value*100/price_0;
            val2 = Math.floor(val2 * 100) / 100;
            $("#skidka_percent").val(val2);
        }
    }
    if($("#skidka_ruble").val() == 0) salesSaleSkidkaDel();
    if($("#skidka_percent").val() != 3) $("#sv_skidka_3").removeClass("active");
    else $("#sv_skidka_3").addClass("active");
    salesSaleSummCalc();
    salesSaleSummSave();
}
function salesSaleSkidkaDel(){       // Удаление скидки
    if($("#sale_status_2").val() < 3){
        $("#skidka_percent").val("");
        $("#skidka_ruble").val("");
        $("#sv_skidka_3").removeClass("active");
        salesSaleSummCalc();
        salesSaleSummSave();
    }
}
function salesSaleSummCalc(param){        // Пересчет цен с учетом скидок, вариантов оплаты и тд
    price_0 = $("#price_0").val();
    skidka = $("#skidka_ruble").val();
    if(param === undefined) param = $("#oplata_hidden").val();

    if(param > 2) $("#oplata_comment").css("display", "block"); else $("#oplata_comment").css("display", "none");

    if(skidka == "" && param != 2) text = priceTroyki(price_0) + " ₽";
    else {
        price = price_0 - skidka;
        if(param == 2) price *= 1.02;
        price = Math.floor(price);
        text = "<strike_text>" + priceTroyki(price_0) + " ₽</strike_text>&nbsp;&nbsp;" + priceTroyki(price) + " ₽";
    }
    $("#sa_price").html(text);
}
function salesSaleSkidka3(that){         // Скидка постоянного покупателя 3%
    if($("#sale_status_2").val() < 3){
        if($(that).hasClass("active")){
            $(that).removeClass("active");
            $("#skidka_percent").val(0);
            salesSaleSkidka(1);
        }
        else{
            $(that).addClass("active");
            $("#skidka_percent").val(3);
            salesSaleSkidka(1);
        }
    }
}
function salesSaleSummSave(param){          // Сохранение данных по скидкам и вариантам оплаты
    skidka_ruble = $("#skidka_ruble").val();
    skidka_percent = $("#skidka_percent").val();
    if(param === undefined) oplata = $("#oplata_hidden").val();
    else oplata = param;
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "saleSaleSummSave", id : SALE_ID, skidka_ruble : skidka_ruble, skidka_percent : skidka_percent, oplata : oplata}
    });
}
function printSaleCodes(sale_id){
    $.ajax({
        url: "../../../../ajax/admin/pdf.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "printSaleCodes", id : sale_id},
        success: function(data) {
            //alert(data);
            getFile("../../temp/" + data + ".pdf");
            //printJS("temp/" + data + ".pdf");
        }
    });
}
function salesSaleStatusChange(that){       // Смена статуса у сделки
    var name = $(that).html();
    if(name == "Товарный чек"){
        payer_1 = $("#payer_1").val();
        payer_2 = $("#payer_2").val();
        $.ajax({
            url: "../../../../ajax/admin/pdf.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "getSalePDF", id : SALE_ID, p1: payer_1, p2: payer_2},
            success: function(data) {
                //alert(data);
                getFile("../../temp/" + data + ".pdf");
                //printJS("temp/" + data + ".pdf");
            }
        });
    }
    else {
        if(name == "Принять оплату"){
            if(!$('sv_movements').is(':empty') && $('.button_movements').length){
                $('.button_movements').click()
            }
            if($("#oplata_hidden").val() == 0) addBorderRed("oplata");
            else salesSaleOplataStart(name)
        }
        else{
            $.ajax({
                url: "../../../../ajax/admin/sales.php",
                dataType: "html",
                type: "POST",
                data: {methodName : "salesSaleStatusChange", id : SALE_ID, name : name},
                success: function(data) {
                    if(name == "Принять оплату"){
                        closeWindow();
                        cashSearch();
                    }
                    else{
                        windowSaleView(SALE_ID);
                        salesSearch();
                    }
                    $("#attention_base_head .active").each(function(){
                        data = $(this).attr("data");
                        attentionLoad(data);
                    });

                }
            });
        }
    }
}
function salesSalesCureerChange(that){       // Выбор курьера
    sale_status = $("#sale_status_2").val();
    if(sale_status > 3){
        cureer = $(that).attr("data");
        $.ajax({
            url: "../../../../ajax/admin/sales.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "salesSalesCureerChange", id : SALE_ID, cureer : cureer},
            success: function(data) {
                windowSaleView(SALE_ID);
            }
        });
    }
}
function salesSaleCureerDel(){     // Удаление курьера
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesSaleCureerDel", id : SALE_ID,},
        success: function(data) {
            windowSaleView(SALE_ID);
        }
    });
}
function salesSaleDeleteStart(id){   // Старт процесса удаления заказа
    $("#sale_head_delete_cancel").css("display", "inline-block");
    $("#sale_head_delete_time").html("9").css("display", "inline-block");
    time = 8;
    TIMER = setTimeout(function tick(){
        if(time > 0){
            $("#sale_head_delete_time").html(time);
            time--;
            TIMER = setTimeout(tick, 1000, time);
        }
        else salesSaleDeleteFinish(id);
    }, 1000, time);
}
function salesSaleDeleteFinish(id){    // Конец удаления заказа
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesSaleDeleteFinish", id : id},
        success: function(data) {
            //alert(data);
            closeWindow();
            salesSearch();
        }
    });
}
function salesSaleDeleteCancel(){     // Отмена удаления заказа
    clearTimeout(TIMER);
    $("#sale_head_delete_cancel").css("display", "none");
    $("#sale_head_delete_time").css("display", "none");
}
function salesSaleOplataStart(name){   // Старт процесса приема оплаты
    $("#sale_bottom_oplata_cancel").css("display", "inline-block");
    $("#sale_bottom_oplata_time").html("9").css("display", "inline-block");
    time = 8;
    TIMER = setTimeout(function tick(){
        if(time > 0){
            $("#sale_bottom_oplata_time").html(time);
            time--;
            TIMER = setTimeout(tick, 1000, time);
        }
        else {
            $.ajax({
                url: "../../../../ajax/admin/sales.php",
                dataType: "html",
                type: "POST",
                data: {methodName : "salesSaleStatusChange", id : SALE_ID, name : "Принять оплату"},
                success: function(data) {
                    //alert(data);
                    if(name == "Принять оплату"){
                        closeWindow();
                        cashSearch();
                    }
                    attentionLoad(2);
                }
            });
        }
    }, 1000, time);
}
function salesSaleOplataCancel(){     // Отмена удаления заказа
    clearTimeout(TIMER);
    $("#sale_bottom_oplata_cancel").css("display", "none");
    $("#sale_bottom_oplata_time").css("display", "none");
}
function salesOplataCommentChange(that){     // Изменение комментария к оплате
    text = $(that).val();
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesOplataCommentChange", id : SALE_ID, text : text}
    });
}
function salesDateOpen(){                   // Открывает данные для ввода даты сделки
    $("#sa_date_cal").css("display", "block");
    $("#sa_date_link").css("display", "none");
}
function getPriceSet(){
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "json",
        type: "POST",
        async: false,
        data: {methodName : "getDiscount"},
        success: function(data) {
            globDiscounts = JSON.stringify(data);
            //alert(globDiscounts);
        }
    });

}

function salesSaleGrossMargin(){
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        type: "POST",
        async: false,
        data: {methodName : "saleAtGross", id: SALE_ID},
        success: function(data) {
            //alert(data);
            pData = JSON.parse(data);
            //alert(SALE_ID);
            $("#sa_price").html(Number.parseInt(pData['gross']));
            $("#skidka_ruble").val(pData['discount']);
            salesSaleSkidka(2);
        }
    });
}














