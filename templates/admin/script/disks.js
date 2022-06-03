function disksStart(){   // Загрузка общей плашки
    $.ajax({
        url: "../../../../ajax/admin/disks.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "disksStart"},
        success: function(data) {
            $("#disks").html(data);
            butLoad();
            selectLoad();
            selectTableLoad();
            disksSearch();

        }
    });
}
function disksSearch(param, param_2){    // Загрузка списка услуг согласно поиску
    if(param == 1){
        if($("#article").children("triangle").hasClass("active2") || $("#article").children("triangle").hasClass("active")){
            if($("#article").children("triangle").hasClass("active2")) article = 1;
            else article = 2;
        }
        else article = 1;
    }
    else article = 0;
    if(param == 2){
        if($("#count").children("triangle").hasClass("active2") || $("#count").children("triangle").hasClass("active")){
            if($("#count").children("triangle").hasClass("active2")) count = 1;
            else count = 2;
        }
        else count = 1;
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
    if(param == 6) w = param_2; else {if($("#w > input").length > 0) w = $("#w > input").val(); else w = -1;}
    if($("#r > input").length > 0) r = $("#r > input").val(); else r = "";
    if(param == 8) hole = param_2; else {if($("#hole > input").length > 0) hole = $("#hole > input").val(); else hole = -1;}
    if(param == 9) bolt = param_2; else {if($("#bolt > input").length > 0) bolt = $("#bolt > input").val(); else bolt = -1;}
    if(param == 10) vylet = param_2; else {if($("#vylet > input").length > 0) vylet = $("#vylet > input").val(); else vylet = -1;}
    if(param == 11) hub = param_2; else {if($("#hub > input").length > 0) hub = $("#hub > input").val(); else hub = -1;}
    if(param == 12) color = param_2; else {if($("#color > input").length > 0) color = $("#color > input").val(); else color = -1;}  

    if($("#nomenclature").length > 0 && $("#nomenclature").val().length > 1) nomenclature = $("#nomenclature").val(); else nomenclature = "";

    $.ajax({
        url: "../../../../ajax/admin/disks.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "disksSearch",
            article : article,
            count : count,
            price_purchase : price_purchase,
            price_sale : price_sale,
            price_wholesale : price_wholesale,
            w : w,
            r : r,
            hole : hole,
            bolt : bolt,
            vylet : vylet,
            hub : hub,
            color : color,
            nomenclature : nomenclature
        },
        success: function(data) {
            $("#disks_body_list").html(data);
            selectLoad(".disk_item");
            //copyReady();
        }
    });
}
function disksRadiusSelect(that){      // Выбор радиуса шины
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
function disksPriceChange(id, param){
    price = $("#price_change").val();
    if(price == "") addBorderRed("price_change");
    else {
        $.ajax({
            url: "../../../../ajax/admin/disks.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "disksPriceChange", id : id, param : param, price : price},
            success: function(data){
                disksSearch();
            }
        });
        $("#price_" + param).html(price);
        $("#window_4").fadeOut();
        $("#window_3").fadeIn();
    }
}
function disksAvailableView(that){
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
function disksAvailableHide(that){
    $(that).parent().parent().children("span").removeClass("active");
    $(that).parent().css("display", "none");
}
function disksRedact(id){       // Редактирование диска
    disk_nomenclature = $("#disk_nomenclature").val();
    disk_bolt = $("#disk_bolt").val();
    disk_vylet = $("#disk_vylet").val();
    disk_hub = $("#disk_hub").val();
    disk_w = $("#w_disk_hidden").val();
    disk_r = $("#r_disk_hidden").val();
    disk_hole = $("#hole_hidden").val();
    disk_color = $("#color_hidden").val();

    price_purchase = $("#price_purchase").val();
    price_wholesale = $("#price_wholesale").val();
    price_sale = $("#price_sale").val();

    if(disk_nomenclature == "") addBorderRed("disk_nomenclature");
        if(disk_bolt == "") addBorderRed("disk_bolt");
        if(disk_vylet == "") addBorderRed("disk_vylet");
        if(disk_hub == "") addBorderRed("disk_hub");
        if(disk_w == -1) addBorderRed("w_disk");
        if(disk_r == -1) addBorderRed("r_disk");
        if(disk_hole == -1) addBorderRed("hole");
        if(disk_color == -1) addBorderRed("color");

    if($(".border_red").length == 0) $.ajax({
        url: "../../../../ajax/admin/disks.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "disksRedact",
            id : id,

            disk_nomenclature : disk_nomenclature,
            disk_bolt : disk_bolt,
            disk_vylet : disk_vylet,
            disk_hub : disk_hub,
            disk_w : disk_w,
            disk_r : disk_r,
            disk_hole : disk_hole,
            disk_color : disk_color,

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
            location.href = SERVER + "cp/disks";
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
function rimsCRUDCheck(val){
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
function rimsCalculateGRPrices(val){
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
function viewCustomRimLogs(rid){
    let commonBlock = document.getElementById('rimCommon');

    if (getComputedStyle(commonBlock).display == 'none') {
        $("#rimCommon").css("display", "block");
        $(".sc_str").css("display", 'block');
        $(".sc_head").css("display", 'block');
        $(".sc_str_2").css("display", 'block');
        $("#tire_count").css("display", "block");
        $("#rimLogs").css("display", "none");
        $("#sc_changelog").removeClass('active');
        $("#sc_changelog").text('Логи');
    }else{
        $("#rimCommon").css("display", "none");
        $(".sc_str").css("display", 'none');
        $(".sc_head").css("display", 'none');
        $(".sc_str_2").css("display", 'none');
        $("#tire_count").css("display", "none");
        $("#rimLogs").css("display", "block");
        $("#sc_changelog").addClass('active');
        $("#sc_changelog").text('Закрыть логи');
        $.ajax({
            url: "../../../../ajax/admin/disks.php",
            type: "POST",
            data: {methodName : "getCustomRimLogs", id: rid},
            success: function(data) {
                $("#customLogsBody").append(data);
            },
            error: function (jqXHR, exception){
                alert(jqXHR.status + ': '+jqXHR.statusText);
            }
        });


    }

}