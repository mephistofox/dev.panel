CUREER_NUM = 0;
MARKER = [];
POINTS_ACTIVE = 0;
function attentionOpen(){               // Открытие поля Требует внимания
    $("#attention").animate({"marginLeft": "0px"}, 500);
    $("#attention_base_head item").first().click();
    timeToday1();
}
function timeToday1(){
    $(".item_date_today").css("display", "block");
    setTimeout(timeToday2, 1000);
}
function timeToday2(){
    $(".item_date_today").css("display", "none");
    setTimeout(timeToday1, 500);
}
function attentionClose(){              // Закрытие поля Требует внимания
    $("#attention").animate({"marginLeft": "-310px"}, 500);
    $("#attention_map").css("display", "none");
}
function attentionLoad(that){       // Загрузка Движения либо брони
    if(that == 1) param = 1;
    if(that == 2) param = 2;
    if(that == 3) param = 3;
    if(that != 1 && that != 2 && that != 3){
        param = $(that).attr("data");
        $("#attention_base_head item").removeClass("active");
        $(that).addClass("active");
    }
    $("#attention_map").css("display", "none");
    $.ajax({
        url: "../../../../ajax/admin/attention.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "attentionLoad", param : param},
        success: function(data) {
            $("#attention_base_body").html(data);
            selectLoad("#attention_base_body");
        }
    });
}
function attentionBaseOpen(){     // Открывает блок с выбором базы при выборе курьера
    $("#att_base").css("display", "block");
}
function attentionMapLoad(that){     // Открывает блок с картой
    baseId = $(that).attr("data");
    baseCode = $(that).attr("data_2");
    //$("#attention_map").html("");
    $("#attention_map").css("display", "block");
    $("#attention_base_body item").off();
    $("#attention_base_body item").prop("onclick", null);
    $("#attention_base_body item").children().prop("onclick", null);
    CUREER_NUM = 0;
    $("#attention_base_body item").each(function(){
        $(this).removeClass("active").removeClass("passive").removeClass("selected").children("num").html("");
        dest = $(this).children("dest").html();
        mas = dest.split(" - ");
        //if(mas[0] == baseCode) $(this).addClass("active");
        //else $(this).addClass("passive");
        $(this).addClass("active");
    });
    $("#attention_base_body item.active").on("click", attentionItemClick);
    //$("#attention_base_body item").on("click", attentionItemClick);

    icon = [];
    icon[0] = SERVER + "templates/img/pimp/pcolor0.png";
    icon[1] = SERVER + "templates/img/pimp/pcolor1.png";
    icon[2] = SERVER + "templates/img/pimp/pcolor2.png";
    icon[3] = SERVER + "templates/img/pimp/pcolor3.png";
    icon[4] = SERVER + "templates/img/pimp/pcolor4.png";
    icon[5] = SERVER + "templates/img/pimp/pcolor5.png";
    icon[6] = SERVER + "templates/img/pimp/pcolor6.png";
    icon[7] = SERVER + "templates/img/pimp/pcolor7.png";
    icon[8] = SERVER + "templates/img/pimp/pcolor8.png";
    icon[9] = SERVER + "templates/img/pimp/pcolor9.png";
    icon[10] = SERVER + "templates/img/pimp/pcolor10.png";

    $.ajax({
        url: "../../../../../ajax/admin/attention.php",
        dataType: "html",
        type: "POST",
        data: { methodName : "getRouteMap"},
        success: function(data) {
            //alert(data);
            //temp = data.split("$$$");
            //er = temp[1].split("%-%");
            //if(er[0] != 0){
            //    text = "";
            //    for(i = 1; i <= er[0]; i++) text += er[i] + "%-%";
            //    $("#window_5").css("display", "none");
            //    windowOpenRouteAddressesChange(text, temp[0]);
            //}
            //else{
            //    if(data == 0) alert("Не выбран адрес");
            //    else{
            //        mas = temp[0].split("%-%");
            //        mapGenerate(mas);
            //    }
            //}
            mas = data.split("%-%");
            mapGenerate(mas);

        }
    });


}
function mapGenerate(mas){    // Генерация карты на основании адресов из массива
    const map_route = new google.maps.Map(document.getElementById("wrs_map"), {
        zoom: 4,
    });

    var markers = [];

    count = mas[0];
    j = 1;
    for(i = 0; i < count; i++){
        temp = [mas[j], mas[j+1], mas[j+2]];
        j = j+3;
        markers.push(temp);
    }

    var markersBounds = new google.maps.LatLngBounds();

    for (var i = 0; i < markers.length; i++) {
        var markerPosition = new google.maps.LatLng(markers[i][1], markers[i][2]);
        markersBounds.extend(markerPosition);

        MARKER[i] = new google.maps.Marker({
            position: markerPosition,
            map: map_route,
            title: markers[i][0],
            icon: {
                url: icon[0],
                scaledSize: new google.maps.Size(37, 52)
            }
        });
    }
    map_route.setCenter(markersBounds.getCenter(), map_route.fitBounds(markersBounds));

    $("#wrs_map").css("display", "block");

    CURRENT_ICON = 1;

    for (var i = 0; i < MARKER.length; i++){
        marker = MARKER[i];

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                //alert(marker.icon);
                //alert(i);
                if(marker.icon.url == icon[CURRENT_ICON]){
                    marker.setIcon({
                        url: icon[0],
                        scaledSize: new google.maps.Size(37, 52)
                    });
                }
                else{
                    marker.setIcon({
                        url: icon[CURRENT_ICON],
                        scaledSize: new google.maps.Size(26, 37)
                    });
                }

            }
        })(marker, i));
    }
}
function attentionItemClick(){     // Клик по плашке
    if(!$(this).hasClass("selected")){
        CUREER_NUM++;
        $(this).addClass("selected");
        $(this).children("num").html(CUREER_NUM);

        id = $(this).children("id").html();
        param = $(this).children("par").html();

        $.ajax({
            url: "../../../../ajax/admin/attention.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "attentionLoadData", id : id, param : param, num : CUREER_NUM},
            success: function(data) {
                $("#attention_map_button").remove();
                $("#attention_map_dest").append(data);
                $("#attention_map_dest").append("<div id = 'attention_map_button' onClick = 'attentionCureerNaz();'>Утвердить</div>");
                //attentionItemAllProof();
            }
        });


    }
}
//function attentionItemAllProof(){    // Проверка что все возможные плашки отмечены
//    if($("#attention_base_body item.active").length == $("#attention_base_body item.selected").length && $("#attention_map_button").length == 0){
//        $("#attention_map").append("<div id = 'attention_map_button' onClick = 'attentionCureerNaz();'>Утвердить</div>");
//    }
//}
function attentionCureerNaz(){     // Назначение курьера
    cureer = $("#cureer_at_hidden").val();
    str = "";
    $("#attention_base_body item.selected").each(function(){
        str = str + $(this).children("par").html() + "." +  $(this).children("id").html() + "%-%";
    })
    $.ajax({
        url: "../../../../ajax/admin/attention.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "attentionCureerNaz", cureer : cureer, str : str},
        success: function(data) {
            //alert(data);
            attentionLoad(1);
        }
    });
}
function attentionBaseDop(that){       // Активирует либо деактивирует другие базы
    attentionItemAllProof();
    if($(that).is(":checked")){
        base = $(that).parent().parent().children("baseCode").html();
        $(".attention_map_addr").each(function(){
            if($(this).children("baseCode").html() == base){
                $(this).children("checkbox").children("input").prop("checked", true);
            }
        })
        $("#attention_base_body item.passive").each(function(){
            temp = $(this).children("dest").html();
            mas = temp.split(" - ");
            if(mas[0] == base){
                $(this).addClass("active").removeClass("passive").on("click", attentionItemClick);
            }
        })
    }
    else{
        base = $(that).parent().parent().children("baseCode").html();
        $(".attention_map_addr").each(function(){
            if($(this).children("baseCode").html() == base){
                $(this).children("checkbox").children("input").prop("checked", false);
            }
        })
    }
}

