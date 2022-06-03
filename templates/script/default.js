function tumblerChange(id){           // Смена тумблера
    if($("#tumbler_" + id).hasClass("tumbler_passive")){
        $("#tumbler_" + id).children(".tumbler_circle").animate({
            marginLeft: "21px",
        }, 200);
        $("#tumbler_" + id).addClass("tumbler_active");
        $("#tumbler_" + id).removeClass("tumbler_passive");
    }
    else {
        $("#tumbler_" + id).children(".tumbler_circle").animate({
            marginLeft: "1px",
        }, 200);
        $("#tumbler_" + id).removeClass("tumbler_active");
        $("#tumbler_" + id).addClass("tumbler_passive");
    }
}

$(document).keyup(function(e) {
    if (e.key === "Escape") { // escape key maps to keycode `27`
        $("#window_background").fadeOut(10);
        $(".windows").fadeOut(10);
        attentionClose();
   }
});

function selectLoad(param){           // Загрузка функционала селекторов
    //if(param !== undefined) $name = $(param + " .select");
    //else $name = $(".select");
    $name = $(".select");
    $name.off();

    $name.on("click", function(){
        var id = $(this).attr('id');
        $("#" + id).children("arrow").toggleClass("active", 400);
        $("#" + id).children("headline").toggleClass("active", 400);
        $("#" + id).toggleClass("active", 400);
        $("#" + id).children("div").slideToggle(400);
        if($("#" + id).children("arrow").hasClass("active")) $("#" + id).css("z-index", "100"); else setTimeout(function(){$("#" + id).css("z-index", "10");}, 400);
    });
    

    $name.children("div").on("click", function(){
        name = $(this).html();
        data = $(this).attr("data");
        id = $(this).parent().attr("id");
        $("#" + id + "_hidden").val(data);
        $("#" + id).children("headline").html(name);
        deleteBorderRed("#" + id);
    })

    //$name.click(function(){
    //    var id = $(this).attr('id');
    //    $("#" + id).children("arrow").toggleClass("active", 400);
    //    $("#" + id).children("headline").toggleClass("active", 400);
    //    $("#" + id).toggleClass("active", 400);
    //    $("#" + id).children("div").slideToggle(400);
    //    if($("#" + id).children("arrow").hasClass("active")) $("#" + id).css("z-index", "100"); else setTimeout(function(){$("#" + id).css("z-index", "10");}, 400);
    //});
    //$name.children("div").click(function(){
    //    name = $(this).html();
    //    data = $(this).attr("data");
    //    id = $(this).parent().attr("id");
    //    $("#" + id + "_hidden").val(data);
    //    $("#" + id).children("headline").html(name);
    //    deleteBorderRed("#" + id);
    //    //$("#" + id).removeClass("border_red");
    //});
}
function selectTableLoad(){           // Загрузка функционала селекторов, ракрывающихся в таблицу
    $(".select_table > arrow").click(function(){
        $(this).toggleClass("active", 400);
        $(this).parent().children("headline").toggleClass("active", 400);
        $(this).parent().toggleClass("active", 400);
        $(this).parent().children("container").slideToggle(400, function() {
            if ($(this).is(':visible'))
                $(this).css('display','inline-block');
        });
    });
    $(".select_table > container > cross").click(function(){
        $(this).parent().parent().children("headline").toggleClass("active", 400);
        $(this).parent().parent().children("arrow").toggleClass("active", 400);
        $(this).parent().parent().toggleClass("active", 400);
        $(this).parent().parent().children("container").slideToggle(400, function() {
            if ($(this).parent().is(':visible'))
                $(this).parent().css('display','inline-block');
        });
    });
    $(".select_table > headline").click(function(){
        $(this).toggleClass("active", 400);
        $(this).parent().children("arrow").toggleClass("active", 400);
        $(this).parent().toggleClass("active", 400);
        $(this).parent().children("container").slideToggle(400, function() {
            if ($(this).is(':visible'))
                $(this).css('display','inline-block');
        });
    });
}
function butLoad(){                   // Загрузка функционала кнопки со стрелочкой
    $(".but").click(function(){
        var id = $(this).attr('id');
        if($("#" + id).children("triangle").hasClass("active")){
            $("#" + id).children("triangle").toggleClass("active2", 400);
        }
        else {
            $(".but").children("triangle").removeClass("active");
            $(".but").children("triangle").removeClass("active2");
            $("#" + id).children("triangle").addClass("active");
        }
    });
}
function defCountLoad(){              // Загрузка функционала инпута с + и -
    $("defcount > minus").click(function(){
        var id = $(this).parent().children("input").attr('id');
        count = $("#" + id).val();
        if(count > 1) count--;
        $("#" + id).val(count);
    });
    $("defcount > plus").click(function(){
        var id = $(this).parent().children("input").attr('id');
        count = $("#" + id).val();
        if(count < 100) count++;
        $("#" + id).val(count);
    });
    $("defcount > input").change(function(){
        count = $(this).val();
        if(count < 1) count = 1;
        $(this).val(count);
    })
}
function radioimgLoad(){              // Загрузка функционала radio с картинкой и описанием
    $("radioimg").click(function(){
        $(this).children("input").prop("checked", true);
    })
}
function doublebuttonLoad(func){      // Загрузка функционала двойной кнопки
    $("doublebutton > div").click(function(){
        if(!$(this).hasClass("active")){
            $(this).parent().children("div").removeClass("active");
            $(this).addClass("active");
            if(func !== undefined) func();
        }
    })
}
function title(name){                 // Задает TITLE у страницы
    $("title").text(name);
}
function titleOld(){                  // Возвращает текущий TITLE
    return $("title").text();
}
function getFile(url) {               // Скачивание любого файла
    var link_url = document.createElement("a");

    link_url.download = url.substring((url.lastIndexOf("/") + 1), url.length);
    link_url.href = url;
    document.body.appendChild(link_url);
    link_url.click();
    document.body.removeChild(link_url);
    delete link_url;
}
function addPhoneMask(element){       // Добавляет маску телефона полю ввода
    if(element === undefined){
        setTimeout(function(){
            $(".phone").mask("+79999999999");
        }, 500);
    }
    else $("#" + element).mask("+79999999999");
}
function calenderActivate(id, param, param_2){ // Активация календаря
    $("#" + id).html("<input type = 'text' id = 'input_" + id + "' class = 'input' onChange = '" + param_2 + "$(this).removeClass(\"border_red\");' /><div class = 'calendar_image' onClick = '$(\"#input_" + id + "\").datepicker(\"show\");'></div>");

    if(param === undefined){
        $("#input_" + id).datepicker({
            firstDay: 1,
            dateFormat: "dd.mm.yy",
            dayNames: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
            dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            monthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"]
        });
    }
    else {
        $("#input_" + id).datepicker({
            firstDay: 1,
            minDate: new Date((new Date()).valueOf()),
            dateFormat: "dd.mm.yy",
            dayNames: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
            dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            monthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"]
        });
        var date = new Date();
        var dd = date.getDate();
        if(dd < 10) dd = "0" + dd;
        var mm = date.getMonth() + 1;
        if(mm < 10) mm = "0" + mm;
        var yyyy = date.getFullYear();
        $("#input_" + id).val(dd + "." + mm + "." + yyyy);
    }
}
function addBorderRed(name){          // Добавление красной границы
    //alert($("#" + name).css("border-right-width"));
    if($("#" + name).css("border-right-width") != "undefined" && $("#" + name).css("border-right-width") != "0px") $("#" + name).addClass("border_red");
    else{
        $("#" + name).addClass("border_red");
        $("#" + name).css("box-shadow", "0px 1px 4px #D0021B");
    }
}
function addBorderGreen(name){        // Добавление зеленой границы
    if($("#" + name).css("border-right-width") != "undefined" && $("#" + name).css("border-right-width") != "0px") $("#" + name).addClass("border_green");
    else $("#" + name).css("box-shadow", "0px 1px 4px #6DD400");
}
function addBorderOrange(name){          // Добавление оранжевой границы
    //alert($("#" + name).css("border-right-width"));
    if($("#" + name).css("border-right-width") != "undefined" && $("#" + name).css("border-right-width") != "0px") $("#" + name).addClass("border_orange");
    else{
        $("#" + name).addClass("border_orange");
        $("#" + name).css("box-shadow", "0px 1px 4px #FF9700");
    }
    setTimeout(function(){
        if($("#" + name).css("border-right-width") != "undefined" && $("#" + name).css("border-right-width") != "0px") $("#" + name).removeClass("border_orange");
        else{
            $("#" + name).removeClass("border_orange");
        }
    }, 500);
}
function deleteBorderRed(that){       // Удаление красной и зеленой обводки при наборе
    $(that).removeClass("border_red");
    $(that).removeClass("border_green");
    if($(that).hasClass("select")) $(that).css("box-shadow", "0px 1px 4px rgba(0, 0, 0, 0.5)");

}
function buttonClick(that){           // Изменение цвета при нажатии кнопки
    $(that).addClass("button_click");
    setTimeout(function(){
        $(that).removeClass("button_click");
    }, 100);
}
function inputDecimal(){              // Запрет на ввод всего кроме цифр, точки и запятой
    $(".decimal").keyup(function(){
        this.value = this.value.replace(",", ".");
        this.value = this.value.replace(/^\.|[^\d\.]|\.(?=.*\.)|^0+(?=\d)/g, '');
        this.value = this.value.replace(".", ",");
    });
}
function inputNumber(){               // Запрет на ввод всего кроме цифр
    $(".number").keyup(function(){
        this.value = this.value.replace(/[^0-9]/g,'');
    });
}
function priceTroyki(val){            // Разбивает число по тройкам
    val = val.toString().replace(/(\d)(?=(\d{3})+$)/g, '$1 ');
    return val;
}
function addressList(that){           // Загружает список подсказок по введенному адресу
    id = $(that).attr("id");
    val = $(that).val();
    if(val.length >= 3){
        $.ajax({
            url: "../../../../../ajax/admin/none.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "addressList", val : val},
            success: function(data) {
                width = $(that).css("width");
                $("#" + id + "_list").html(data);
                $("#" + id + "_list").css("width", width);
                $("#" + id + "_list").children().css("width", width);
                $(that).animate({borderBottomLeftRadius: 0, borderBottomRightRadius: 0}, 400);
                $("#" + id + "_list").slideDown(400);
            }
        });
    }
}
function addressChange(that){         // Выбор адреса из подсказок
    val = $(that).html();
    id = $(that).parent().attr("id");
    id = id.replace("_list", "");
    $("#" + id).val(val);
    $("#" + id).animate({borderBottomLeftRadius: 4, borderBottomRightRadius: 4}, 400);
    $("#" + id + "_list").slideUp(400);
}
function contactList(that){           // Загружает список подсказок по введенному телефону
    id = $(that).attr("id");
    val = $(that).val();
    if(val.length >= 3){
        $.ajax({
            url: "../../../../../ajax/admin/none.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "contactList", val : val},
            success: function(data) {
                width = $(that).css("width");
                $("#" + id + "_list").html(data);
                $("#" + id + "_list").css("width", width);
                $("#" + id + "_list").children().css("width", width);
                $(that).animate({borderBottomLeftRadius: 0, borderBottomRightRadius: 0}, 400);
                $("#" + id + "_list").slideDown(400);
            }
        });
    }
}
function contactList2(that){           // Загружает список подсказок по заданному клиенту
    id = $(that).attr("data");
    $.ajax({
        url: "../../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "contactList2", id : id},
        success: function(data) {
            width = $("#client_phone").css("width");
            $("#client_phone_list").html(data);
            $("#client_phone_list").css("width", width);
            $("#client_phone_list").children().css("width", width);
            $(that).animate({borderBottomLeftRadius: 0, borderBottomRightRadius: 0}, 400);
            $("#client_phone_list").slideDown(400);
        }
    });
}
function contactChange(that){         // Выбор телефона из подсказок
    val = $(that).html();
    id = $(that).parent().attr("id");
    id = id.replace("_list", "");
    $("#" + id).val(val);
    $("#" + id).animate({borderBottomLeftRadius: 4, borderBottomRightRadius: 4}, 400);
    $("#" + id + "_list").slideUp(400);
    data = $(that).attr("data");
    let address = $(that).data("address");
    if (address.length>0) {
        salesAddPoluch(3)
        setTimeout(function () {
            $('#address.input.height-23').val(address)
            $('#poluch_hidden').val(3)
            $('.select headline').text('Доставка')
        },250)
    }
    CLIENT = data;
    CONTACT = $(that).attr("data2");
    $("#client").children("div").each(function(){
        if($(this).attr("data") == CLIENT) {
            name = $(this).html();
            $("#client").children("headline").html(name);
        }
    })
    $("#client_hidden").val(CLIENT);
    deleteBorderRed("#client_phone");
    deleteBorderRed("#client");
}
function reasonList(that){          // Получение списка причин списания
    id = $(that).attr("id");
    val = $(that).val();
    if(val.length >= 3){
        $.ajax({
            url: "../../../../../ajax/admin/none.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "reasonList", val : val},
            success: function(data) {
                width = $(that).css("width");
                $("#" + id + "_list").html(data);
                $("#" + id + "_list").css("width", width);
                $("#" + id + "_list").children().css("width", width);
                $(that).animate({borderBottomLeftRadius: 0, borderBottomRightRadius: 0}, 400);
                $("#" + id + "_list").slideDown(400);
            }
        });
    }
}
function reasonChange(that){       // Выбор причины списания из подсказок
    val = $(that).html();
    id = $(that).parent().attr("id");
    id = id.replace("_list", "");
    $("#" + id).val(val);
    $("#" + id).animate({borderBottomLeftRadius: 4, borderBottomRightRadius: 4}, 400);
    $("#" + id + "_list").slideUp(400);
    data = $(that).attr("data");
    $(that).parent().html("");
}
function columnSave(param){           // Сохраняет настройки столбцов
    COLUMN_LIST = $("#window_3_body").sortable("serialize");
    check = "";
    switch(param){
        case 1: count = 5 ; break;
        case 2: count = 19; break;
        case 3: count = 14; break;
        case 4: count = 9 ; break;
        case 5: count = 14; break;
        case 6: count = 19; break;
        case 7: count = 8; break;
    }
    for(i = 0; i < count; i++) if($("#checkbox_" + i).prop("checked")) check += "1%-%"; else check += "0%-%";
    $.ajax({
        url: "../../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "columnSave", list : COLUMN_LIST, check : check, param : param},
        success: function(data) {
            closeWindow();
            location.reload();
        }
    });

}
function getTime(id){                 // Сохраняет в блок текущее время, меняющееся со временем
    var date = new Date();
    h = date.getHours();
    m = date.getMinutes();
    if(h < 10) h = "0" + h;
    if(m < 10) m = "0" + m;
    text = h + " <span>:</span> " + m;
    $("#" + id).html(text);
    setTimeout(getTime2, 700, id);
}
function getTime2(id){                 // Сохраняет в блок текущее время, меняющееся со временем
    var date = new Date();
    h = date.getHours();
    m = date.getMinutes();
    if(h < 10) h = "0" + h;
    if(m < 10) m = "0" + m;
    text = h + " <span style = 'opacity: 0;'>:</span> " + m;
    $("#" + id).html(text);
    setTimeout(getTime, 300, id);
}
function getTimeDuration(start, id){                 // Сохраняет в блок текущее время, меняющееся со временем
    var date = new Date();
    h = date.getHours();
    m = date.getMinutes();
    end = 3600*h+60*m;
    delta = end-start;
    h = Math.floor(delta/3600);
    delta = delta%3600;
    m = Math.floor(delta/60);
    if(h < 10) h = "0" + h;
    if(m < 10) m = "0" + m;
    text = h + "<span>:</span>" + m;
    $("#" + id).html(text);
    setTimeout(getTimeDuration2, 700, start, id);
}
function getTimeDuration2(start, id){                 // Сохраняет в блок текущее время, меняющееся со временем
    var date = new Date();
    h = date.getHours();
    m = date.getMinutes();
    end = 3600*h+60*m;
    delta = end-start;
    h = Math.floor(delta/3600);
    delta = delta%3600;
    m = Math.floor(delta/60);
    if(h < 10) h = "0" + h;
    if(m < 10) m = "0" + m;
    text = h + "<span style = 'opacity: 0;'>:</span>" + m;
    $("#" + id).html(text);
    setTimeout(getTimeDuration, 300, start, id);
}
function questionDelete(param){             // Спрашивает об удалении
    //checking user rights before asking questions
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "checkUserType"},
        success: function(data) {
            if(data == 1){
                if(param == 1){
                    $("#product_delete_2").css("display", "block");
                    $("#product_delete").css("display", "none");
                }
                else{
                    $("#product_delete_2").css("display", "none");
                    $("#product_delete").css("display", "block");
                }
            }else{
                alert('Недостаточно прав для выполнения этого действия');
            }
        }
    });
}

function questionUserDelete(param){             // Спрашивает об удалении
    //checking user rights before asking questions
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "checkUserType"},
        success: function(data) {
            if(data < 3){
                if(param == 1){
                    $("#product_delete_2").css("display", "block");
                    $("#product_delete").css("display", "none");
                }
                else{
                    $("#product_delete_2").css("display", "none");
                    $("#product_delete").css("display", "block");
                }
            }else{
                alert('Недостаточно прав для выполнения этого действия');
            }
        }
    });
}

function userDelete(id) {
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName: "clientRemove", client_id: id},
        success: function() {
            alert('Клиент был удален!')
            clientsSearch();
            closeWindow();
        }
    });
}




