let page;

function tiresStart(){   // Загрузка общей плашки
    
    $.ajax({
        url: "../../../../ajax/admin/tires.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "tiresStart"},
        success: function(data) {
            $("#tires").html(data);
            butLoad();
            selectLoad();
            selectTableLoad();
            tiresSearch();
            hideZeroPositions($('.form-check-input'))
        }
    });
}
function getPriceSet(){
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "json",
        type: "POST",
        async: false,
        data: {methodName : "getPriceSet"},
        success: function(data) {
            globalPriceSet = JSON.stringify(data);
            //alert(globDiscounts);
        }
    });

}

function searchByText(e){
    for (let index = 0; index < $('.tire-name').length; index++) {
        const element = $('.tire-name')[index];
        if ($(element).text().toLowerCase().search($(e).val().toLowerCase()) == -1){
            $($('#tires_body_list br')[index]).hide()
            $(element).parent().hide()
        } else {
            $($('#tires_body_list br')[index]).show()
            $(element).parent().show()
        }
    }
}

function hideZeroPositions(e){
    for (let index = 0; index < $('.count-col').length; index++) {
        const element = $('.count-col')[index];
        if(e.checked==true){
            if (parseInt($(element).text())<4){
                $(element).parent().hide()
                $($('#tires_body_list br')[index]).hide()
            }
            $('.form-check-label').addClass('changeColor')
            $('.form-check-label span').text('Показать меньше 4')
        } else {
            $(element).parent().show()
            $($('#tires_body_list br')[index]).show()
            $('.form-check-label').removeClass('changeColor')
            $('.form-check-label span').text('Скрыть меньше 4')
        }
    }
}

function tireCopy(e){   // Загрузка общей плашки
    if (!navigator.clipboard) {
        fallbackCopyTextToClipboard($(e).data('cont'));
        return;
      }
    navigator.clipboard.writeText($(e).data('cont')).then(function() {
        console.log('Async: Copying to clipboard was successful!');
    })
}

function paginator() {
    let active;
    $.post('/ajax/admin/tires.php', {"methodName": "tiresCol"},function(data){
        let arr = Array.from({length: parseInt(data)}, (_, i) => i + 1)
        arr.forEach(element => {
            $('#paginate').append(`<button type="button" class="btn btn-danger page ${active}" onclick="page=${element};tiresSearch();activeCheck(this);"><b>`+element+`</b></button>`)
        });
    })
}

function activeCheck(e) {
    $('.page').removeClass('active')
    $(e).addClass('active')
}

function tiresSearch(param, param_2){    // Загрузка списка услуг согласно поиску
    if(param == 1){
        if($("#article").children("triangle").hasClass("active2") || $("#article").children("triangle").hasClass("active")){
            if($("#article").children("triangle").hasClass("active2")) article = 1;
            else article = 2;
        }
        else article = 1;
    }
    else article = 0;
    if(param == 2){
        if(count==1){
            count=2;
        }else{
            count=1;
        }
    }
    else count = 0;
    if(param == 3){
        if($("#price_purchase").children("triangle").hasClass("active2") || $("#price_purchase").children("triangle").hasClass("active")){
            if($("#price_purchase").children("triangle").hasClass("active2")) price_purchase = 1;
            else price_purchase = 2;
        }
        else price_purchase = 1;
    }
    else price_purchase = 0;
    if(param == 4){
        if($("#price_sale").children("triangle").hasClass("active2") || $("#price_sale").children("triangle").hasClass("active")){
            if($("#price_sale").children("triangle").hasClass("active2")) price_sale = 1;
            else price_sale = 2;
        }
        else price_sale = 1;
    }
    else price_sale = 0;
    if(param == 5){
        if($("#price_wholesale").children("triangle").hasClass("active2") || $("#price_wholesale").children("triangle").hasClass("active")){
            if($("#price_wholesale").children("triangle").hasClass("active2")) price_wholesale = 1;
            else price_wholesale = 2;
        }
        else price_wholesale = 1;
    }
    else price_wholesale = 0;
    if(param == 6) season = param_2; else season = $("#season > input").val();
    if(param == 7) w = param_2; else w = $("#w > input").val();
    if(param == 8) h = param_2; else h = $("#h > input").val();
    if(param == 9) rft = param_2; else rft = $("#rft > input").val();
    if(param == 10) spike = param_2; else spike = $("#spike > input").val();
    if(param == 11) cargo = param_2; else cargo = $("#cargo > input").val();
    if(param == 12) brand = param_2; else brand = $("#brand > input").val();
    r = $("#r > input").val();
    if($("#model").length > 0 && $("#model").val().length > 1) model = $("#model").val(); else model = "";
    if($("#nagr").length > 0 && $("#nagr").val().length > 1) nagr = $("#nagr").val(); else nagr = "";
    if($("#resist").length > 0 && $("#resist").val().length > 1) resist = $("#resist").val(); else resist = "";
    if (!page) {
        page = 1;
    }
    $.ajax({
        url: "../../../../ajax/admin/tires.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "tiresSearch",
            article : article,
            count : count,
            price_purchase : price_purchase,
            price_sale : price_sale,
            price_wholesale : price_wholesale,
            season : season,
            w : w,
            h : h,
            rft : rft,
            spike : spike,
            cargo : cargo,
            brand : brand,
            r : r,
            model : model,
            nagr : nagr,
            resist : resist,
            page : page
        },
        success: function(data) {
            $("#tires_body_list").html(data);
            selectLoad(".tire_item");
            hideZeroPositions($('.form-check-input'))
            //copyReady();
        }
    });
}

function search(w,h,r){
    $.ajax({
        url: "../../../../ajax/admin/tires.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName: "tiresSearch",
            article: 0,
            count: 0,
            price_purchase: 0,
            price_sale: 0,
            price_wholesale: 0,
            season: -1,
            w: w,
            h: h,
            rft: -1,
            spike: -1,
            cargo: -1,
            brand: -1,
            r: r,
            model: "",
            nagr: "",
            resist: "",
            page: page
        },
        success: function(data) {
            $("#tires_body_list").html(data);
            selectLoad(".tire_item");
            hideZeroPositions($('.form-check-input'))
        },
        
    });
}

function tiresRadiusSelect(that){      // Выбор радиуса шины
    text = $(that).html();
    text = text.replace("R", "");
    param = $("#r_hidden").val();
    if($(that).hasClass("active")){
        param = param.replace(text + SEP, "");
        $(that).removeClass("active");
    }
    else {
        param = param + text + SEP;
        $(that).addClass("active");
    }
    $("#r_hidden").val(param);
}
function tiresPriceChange(id, param){        // Изменение цены шины
    price = $("#price_change").val();
    if(price == "") addBorderRed("price_change");
    else {
        $.ajax({
            url: "../../../../ajax/admin/tires.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "tiresPriceChange", id : id, param : param, price : price},
            success: function(data){
                tiresSearch();
            }
        });
        $("#price_" + param).html(price);
        $("#window_4").fadeOut();
        $("#window_3").fadeIn();
    }
}
function tiresAvailableView(that){        // Показывает наличие на складе
    if($(that).hasClass("active")){
        $(that).removeClass("active");
        $(that).parent().children("rightcol").css("display", "none");
    }
    else{
        $(".tire_count_str span").removeClass("active");
        $(".tire_count_str").children("rightcol").css("display", "none");
        $(that).addClass("active");
        $(that).parent().children("rightcol").css("display", "inline-block");
    }
}
function tiresAvailableHide(that){        // Скрывает наличие на складе
    $(that).parent().parent().children("span").removeClass("active");
    $(that).parent().css("display", "none");
}

function tiresRedact(id){       // Редактирование шины
    tire_brand = $("#tire_brand").val();
    tire_model = $("#tire_model").val();
    tire_nagr = $("#tire_nagr").val();
    tire_resist = $("#tire_resist").val();
    tire_w = $("#w_tire_hidden").val();
    tire_h = $("#h_tire_hidden").val();
    tire_r = $("#r_tire_hidden").val();
    switch($("#doublebutton_2 > .active").html()){
        case "<i>Зима</i>": tire_season = 0; break;
        case "<i>Лето</i>": tire_season = 1; break;
        case "<i>Всесезон</i>": tire_season = 2; break;
    }
    if($("#tumbler_rft").hasClass("tumbler_active")) tire_rft = 1; else tire_rft = 0;
    if($("#tumbler_spike").hasClass("tumbler_active")) tire_spike = 1; else tire_spike = 0;
    if($("#tumbler_cargo").hasClass("tumbler_active")) tire_cargo = 1; else tire_cargo = 0;
    tire_payer = $("#payer_hidden").val();

    price_purchase = $("#price_purchase").val();
    price_wholesale = $("#price_wholesale").val();
    price_sale = $("#price_sale").val();

    if(tire_brand == "") addBorderRed("tire_brand");
    if(tire_model == "") addBorderRed("tire_model");
    if(tire_nagr == "") addBorderRed("tire_nagr");
    if(tire_resist == "") addBorderRed("tire_resist");
    if(tire_w == -1) addBorderRed("w_tire");
    if(tire_h == -1) addBorderRed("h_tire");
    if(tire_r == -1) addBorderRed("r_tire");
    if(price_purchase == "") addBorderRed("price_purchase");
    if(price_wholesale == "") addBorderRed("price_wholesale");
    if(price_sale == "") addBorderRed("price_sale");
    if(tire_payer == -1) addBorderRed("payer");

    if($(".border_red").length == 0) $.ajax({
        url: "../../../../ajax/admin/tires.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "tiresRedact",
            id : id,

            tire_brand : tire_brand,
            tire_model : tire_model,
            tire_nagr : tire_nagr,
            tire_resist : tire_resist,
            tire_w : tire_w,
            tire_h : tire_h,
            tire_r : tire_r,
            tire_season : tire_season,
            tire_rft : tire_rft,
            tire_spike : tire_spike,
            tire_cargo : tire_cargo,
            tire_payer : tire_payer,

            price_purchase : price_purchase,
            price_wholesale : price_wholesale,
            price_sale : price_sale,

            photos : PHOTOS,
            general_photo : GENERAL_PHOTO,
            b: globalBuyoutCurrent,
            g: globalGrossCurrent,
            r: globalRetailCurrent
        },
        success: function(data) {
            //alert(data);
            location.href = SERVER + "cp/tires";
        }
    });

}
function tiresCRUDCheck(val){
    let min = $(val).attr('min');
    let currentVal = $(val).val().replace(' ', '');
    $(val).val(currentVal);
    let pType = $(val).attr('id');
    let pText = '';
    switch (pType){
        case 'price_wholesale': pText = 'оптовая'; break;
        case 'price_sale': pText = 'розничная'; break;
    }
    if(currentVal < min){
        alert('Минимальная '+pText+' цена: '+min);
        $(val).val(min);
    }
}
function tiresCalculateGRPrices(val){
    let buyout = $("#price_purchase").val();
    //alert(buyout);
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        type: "POST",
        data: {methodName : "calculateGRPrices", buyout: buyout},
        success: function(data) {
            //alert(data);
            let prices = JSON.parse(data);
            $("#price_wholesale").attr('min', prices['minGross']);
            $("#price_wholesale").val(prices['minGross']);
            $("#price_sale").attr('min', prices['minRetail']);
            $("#price_sale").val(prices['minRetail']);
        },
        error: function (jqXHR, exception){
            alert('Error '+jqXHR + ': '+exception);
        }
    });
}

function viewCustomTireLogs(rid){
    let commonBlock = document.getElementById('tireCommon');

    if (getComputedStyle(commonBlock).display == 'none') {
        $("#tireCommon").css("display", "block");
        $(".sc_str").css("display", 'block');
        $(".sc_head").css("display", 'block');
        $(".sc_str_2").css("display", 'block');
        $("#tire_count").css("display", "block");
        $("#tireLogs").css("display", "none");
        $("#sc_changelog").removeClass('active');
        $("#sc_changelog").text('Логи');
    }else{
        $("#tireCommon").css("display", "none");
        $(".sc_str").css("display", 'none');
        $(".sc_head").css("display", 'none');
        $(".sc_str_2").css("display", 'none');
        $("#tire_count").css("display", "none");
        $("#tireLogs").css("display", "block");
        $("#sc_changelog").addClass('active');
        $("#sc_changelog").text('Закрыть логи');
        $.ajax({
            url: "../../../../ajax/admin/tires.php",
            type: "POST",
            data: {methodName : "getCustomTireLogs", id: rid},
            success: function(data) {
                $("#customLogsBody").append(data);
            },
            error: function (jqXHR, exception){
                alert(jqXHR.status + ': '+jqXHR.statusText);
            }
        });
    }

}
function tiresCodeWrite(that){     // Открывает блок ввода кодов шин
    if($(that).attr("data_2") == 1) $("#codes_back").css("display", "block");
    else $("#codes_back").css("display", "none");
}
function tireCodesPrint(id, payer){        // Печать всех кодов маркировки
    $.ajax({
        url: "../../../../ajax/admin/pdf.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "tireCodesPrint", id : id, payer : payer},
        success: function(data){
            //alert(data);
            getFile("../../temp/" + data + ".pdf");
        }
    });
}
function tireCodesChange(tire, payer, param){      // Приемка либо списание шин
    code_str = $("#code_text").val();
    mas = code_str.split(", ");
    code = "";
    for(i = 0; i < mas.length; i++) code = code + mas[i] + "%-%";
    code = code.replace("<", "&lt;");
    $.ajax({
        url: "../../../../ajax/admin/tires.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "tireCodesChange", tire : tire, payer : payer, code : code, param : param},
        success: function(data){
            alert(data);
        }
    });
}
