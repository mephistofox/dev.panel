PARAM_ACTIVE = 0;
PHOTOS = 0;
GENERAL_PHOTO = 0;
STORAGE = 0;
CONTACT = 0;
CLIENT = 0;
ADDITION_TYPE = 1;
BASE_STORAGE = 0;
function itemMenuActive(name){     // Делает пункт меню активным
    $("#" + name).addClass("menu_left_item_active");
}
function exitCabinet(){      // Выход из личного кабинета
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "exitCabinet"},
        success: function(data) {
            if(data == 1) location.href = SERVER + "login";
        }
    });
}
function clearBasket(){       // Приемка товара
    $.ajax({
        url: "../../../../ajax/admin/clear.php",
        dataType: "html",
        type: "POST",
        success: function(data) {
            $('#sales_head_otl').remove()
        }
    });

}
function listItemActive(mother, name){      // В заданном списке делает пункт меню активным
    $("#" + mother).find(".list_item").removeClass("list_item_active");
    $("#" + mother).find("cross").css("display", "none");
    $("#" + mother + "_" + name).addClass("list_item_active");
    $("#" + mother + "_cross_" + name).css("display", "block");
}
function getTemplateHTML(url, name, param){            // Получение файла шаблона
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "getTemplateHTML", url : url},
        success: function(data) {
            if(param === undefined) $("#" + name).html(data);
            else $("#" + name).append(data);
            addPhoneMask("phone");
            addPhoneMask("phone_add");
            addPhoneMask("contact_phone");
        }
    });
}
function docLoad(that, param){    // Получение адреса загруженного документа
    file_data = $(that).prop("files")[0];
    var form_data = new FormData();
    form_data.append("file", file_data);
    form_data.append("methodName", "docLoad");
    form_data.append("param", param);
    docSend(form_data, param);
}
function docSend(form_data, param){       // Отправка документа на сервер
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "text",
        type: "POST",
        data: form_data,
        contentType: false,
        processData: false,
        success: function(data){
            mas = data.split(SEP);
            switch(param){
                case 1: settingsLoad("season"); break;
                case 2: settingsMassaProof(data); break;
                case 3: settingsPayerRekChange(PAYER, mas[0]); break;
            }
        }
    });
}
function copyReady(){     // Отключает стандартное контекстное меню и делает новое
    document.oncontextmenu = function() {return false;}; // Запрет стандартного контекстного меню
    $(".tire_item, .disk_item, .product_item").mousedown(function(event) {
        $('.context-menu').remove();
        if (event.which === 3)  {        // Проверяем нажата ли именно правая кнопка мыши:
            var target = $(event.target);     // Получаем элемент на котором был совершен клик:
            id = target.attr("id");

            $('<div/>', {     // Создаем меню:
                class: 'context-menu'
            })
            .css({
                left: event.pageX+'px', // Задаем позицию меню на X
                top: event.pageY+'px' // Задаем позицию меню по Y
            })
            .appendTo('body')
            .append(
                 $('<ul/>').append("<li><div onClick = 'copy(\"" + id + "\");'>Копировать</div></li>")
                                //.append('<li><a href="#">Add element</a></li>')
                   )
            .show('fast'); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
         }
    });
}
function copy(id){     // Копирование иноформации из строки
    mas = id.split("_");
    id = mas[1];
    param = mas[0];
    $(".context-menu").hide("fast");
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "copy", id : id, param : param},
        success: function(data) {
            copyToClipboard(data);
        }
    });
}
function copyToClipboard(str) {    // Копирует заданную строку в буфер обмена
    var area = document.createElement('textarea');

    document.body.appendChild(area);
    area.value = str;
    area.select();
    document.execCommand("copy");
    document.body.removeChild(area);
}
function productTypeChange(){    // Смена вида продукта при добавлении
    val = $("#doublebutton_1").children(".active").html();
    if(PARAM_ACTIVE == 1){
        tire = "none";
        disk = "none";
        product = "none";
        switch(val){
            case "Шина": tire = "block"; break;
            case "Диск": disk = "block"; break;
            case "Товар": product = "block"; break;
        }
        $("#pa_tire").css("display", tire);
        $("#pa_disk").css("display", disk);
        $("#pa_product").css("display", product);
        $(".border_red").removeClass("border_red");
    }
}
function productPropusk(){       // Открытие добавления нового продукта
    val = $("#doublebutton_1 > .active").html();
    switch(val){
        case "Шина": param = 1; break;
        case "Диск": param = 2; break;
        case "Товар": param = 3; break;
    }
    $("#propusk").css("display", "none");
    $("#pa_head").css("border-bottom", "7px solid #A7A7A7");
    $("#pa_price").css("display", "block");
    $("#window_3_footer").css("display", "block");
    PARAM_ACTIVE = 1;
    tire = "none";
    disk = "none";
    product = "none";
    switch(param){
        case 1: tire = "block"; break;
        case 2: disk = "block"; break;
        case 3: product = "block"; break;
    }
    $("#pa_tire").css("display", tire);
    $("#pa_disk").css("display", disk);
    $("#pa_product").css("display", product);
}
function productPhotoClose(){    // Закрытие окна добавления фотографий, открытие окна добавления продукта
    $("#window_4").fadeOut();
    $("#window_3").fadeIn();
}
function productPhotoAdd(data){  // Добавление изображения
    mas = data.split(SEP);
    //alert(mas[0]);
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "getTemplateHTML", url : "position_img_add.html"},
        success: function(data) {
            data = data.split("%SERVER%").join(SERVER);
            data = data.split("%URL%").join("temp/" + mas[0]);
            $("#position_photos_right").append(data);
            PHOTOS += "temp/" + mas[0] + SEP;
            if($("#position_photos_right .load_images_item").length > 4) $("#img_type_add").css("display", "none");
            if($("#position_photos_right .load_images_item").length == 1){
                GENERAL_PHOTO = "temp/" + mas[0];
                $("#position_photos_right .load_images_item").children("cover").css("visibility", "visible");
                $("#position_photos_right .load_images_item").children("img").css("border", "2px solid #000000");
            }
        }
    });
}
function productGeneralPhotoChange(that){    // Смена главного изображения
    $(".load_images_item").children("cover").css("visibility", "hidden");
    $(".load_images_item").children("img").css("border", "1px solid #979797");
    $(that).parent().children("cover").css("visibility", "visible");
    $(that).css("border", "2px solid #000000");
    name = $(that).parent().children("filename").html();
    GENERAL_PHOTO = name;
    console.log(GENERAL_PHOTO);
}
function productPhotoDelete(that){     // Удаление изображения при добавлении продукта
    name = $(that).parent().children("filename").html();
    if($(that).css("border") == "2px solid #000000") GENERAL_PHOTO = 0;
    $(that).parent().remove();
    deleteImg(name);
    PHOTOS = PHOTOS.replace(name + SEP, "");
    if($("#position_photos_right .load_images_item").length < 5) $("#img_type_add").css("display", "block");
}
function productAdd(){         // Добавление нового товара
    switch($("#doublebutton_1 > .active").html()){
        case "Шина": param = 1; break;
        case "Диск": param = 2; break;
        case "Товар": param = 3; break;
    }

    barcode = $("#barcode").val();

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
    tire_payer = $("#payer_hidden").val();
    //tire_code = $("#codes").val();

    if($("#tumbler_rft").hasClass("tumbler_active")) tire_rft = 1; else tire_rft = 0;
    if($("#tumbler_spike").hasClass("tumbler_active")) tire_spike = 1; else tire_spike = 0;
    if($("#tumbler_cargo").hasClass("tumbler_active")) tire_cargo = 1; else tire_cargo = 0;

    disk_nomenclature = $("#disk_nomenclature").val();
    disk_bolt = $("#disk_bolt").val();
    disk_vylet = $("#disk_vylet").val();
    disk_hub = $("#disk_hub").val();
    disk_w = $("#w_disk_hidden").val();
    disk_r = $("#r_disk_hidden").val();
    disk_hole = $("#hole_hidden").val();
    disk_color = $("#color_hidden").val();

    product_name = $("#product_name").val();
    product_params = $("#product_params").val();
    product_note = $("#product_note").val();

    price_purchase = $("#price_purchase").val();
    price_wholesale = $("#price_wholesale").val();
    price_sale = $("#price_sale").val();

    if(param == 1){
        if(tire_brand == "") addBorderRed("tire_brand");
        if(tire_model == "") addBorderRed("tire_model");
        if(tire_nagr == "") addBorderRed("tire_nagr");
        if(tire_resist == "") addBorderRed("tire_resist");
        if(tire_w == -1) addBorderRed("w_tire");
        if(tire_h == -1) addBorderRed("h_tire");
        if(tire_r == -1) addBorderRed("r_tire");        
        if(tire_payer == -1) addBorderRed("payer");
        //if($("#codes_back").css("display") == "block" && tire_code == "") addBorderRed("codes");
    }
    if(param == 2){
        if(disk_nomenclature == "") addBorderRed("disk_nomenclature");
        if(disk_bolt == "") addBorderRed("disk_bolt");
        if(disk_vylet == "") addBorderRed("disk_vylet");
        if(disk_hub == "") addBorderRed("disk_hub");
        if(disk_w == -1) addBorderRed("w_disk");
        if(disk_r == -1) addBorderRed("r_disk");
        if(disk_hole == -1) addBorderRed("hole");
        if(disk_color == -1) addBorderRed("color");
    }
    if(param == 3){
        if(product_name == "") addBorderRed("product_name");
        if(product_params == "") addBorderRed("product_params");
        if(product_note == "") addBorderRed("product_note");
    }

    if(price_purchase == "") addBorderRed("price_purchase");
    if(price_wholesale == "") addBorderRed("price_wholesale");
    if(price_sale == "") addBorderRed("price_sale");

    //if(barcode == "") addBorderRed("barcode");

    if($("#checkbox_1").prop("checked")) receipt = 1; else receipt = 0;

    if($(".border_red").length == 0) $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "productAdd",
            param : param,

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
            //tire_code : tire_code,

            disk_nomenclature : disk_nomenclature,
            disk_bolt : disk_bolt,
            disk_vylet : disk_vylet,
            disk_hub : disk_hub,
            disk_w : disk_w,
            disk_r : disk_r,
            disk_hole : disk_hole,
            disk_color : disk_color,

            product_name : product_name,
            product_params : product_params,
            product_note : product_note,

            price_purchase : price_purchase,
            price_wholesale : price_wholesale,
            price_sale : price_sale,
            barcode : barcode,

            photos : PHOTOS,
            general_photo : GENERAL_PHOTO
        },
        success: function(data) {
            if(data == -1) addBorderRed("barcode");
            else{
                if(receipt == 1){
                    windowReceiptAdd2(data, param);
                }
                else{
                    switch(param){
                        case 1 : location.href = SERVER + "cp/tires"; break;
                        case 2 : location.href = SERVER + "cp/disks"; break;
                        case 3 : location.href = SERVER + "cp/products"; break;
                    }
                }
            }
        }
    });
}
function productPhotoShow(){     // Показывает изображения товара
    if($("#product_photos").css("display") == "none"){
        $("#product_photo_title").css("color", "#000000");
        $("#product_photo_title").css("border-bottom", "none");
    }
    else{
        $("#product_photo_title").css("color", "#32648B");
        $("#product_photo_title").css("border-bottom", "1px dotted #32648B");
    }
    $("#product_photos").slideToggle(400);
}
function productOtherShow(){     // Показывает разновидности товара
    if($("#product_other").css("display") == "none"){
        $("#product_other_title").css("color", "#000000");
        $("#product_other_title").css("border-bottom", "none");
    }
    else{
        $("#product_other_title").css("color", "#32648B");
        $("#product_other_title").css("border-bottom", "1px dotted #32648B");
    }
    $("#product_other").slideToggle(400);
}
function deleteImg(name){                         // Удаление изображения
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "deleteImg", name : name}
    });
}
function addImgActivate(param){                   // Добавление возможности загрузки изображений на лету
    var dropZone = $(".img_back");

    dropZone.on('dragenter', function (e){e.preventDefault();});

    dropZone.on('dragover', function (e){e.preventDefault();});

    dropZone.on('drop', function (e){
        var name = $(this).parent().attr("id");
        name = name.replace("_add", "");
        e.preventDefault();
        var file = e.originalEvent.dataTransfer.files;
        var form_data = new FormData();
        form_data.append("file", file[0]);
        form_data.append("methodName", "imgLoad");
        imgSend(form_data, param, name);
     });
}
function imgLoad(that, param){                    // Получение адреса загруженного изображения
    file_data = $(that).prop("files")[0];
    name = $(that).attr("id");
    var form_data = new FormData();
    form_data.append("file", file_data);
    form_data.append("methodName", "imgLoad");
    imgSend(form_data, param, name);
}
function imgSend(form_data, param, name){         // Отправка изображения на сервер
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "text",
        type: "POST",
        data: form_data,
        contentType: false,
        processData: false,
        success: function(data){
            //alert(data);
            switch(param){
                case 1: productPhotoAdd(data); break;    // Фотографии в Добавлении типа у шаблона
                //case 2: theoryAddImgLoad(data);    break;    // Фотографии в Редактировании статьи
                //case 3: accountAddImage(data); break; // Фотография в аккаунте
            }
        }
    });
}
function getTemplateHTML(url, block, dop){    // Загружает файл шаблона в выбранный блок
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "getTemplateHTML", url : url},
        success: function(data) {
            $("#" + block).html(data);
            if(dop !== undefined) dop();
        }
    });
}
function barcodeArticleProof(param){               // Проверяет, существует ли данный штрих код либо артикул
    code = $("#barcode").val();
    if(code.length > 0) $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "barcodeArticleProof", code : code},
        success: function(data) {
            if(data == 0) addBorderRed("barcode");
            else{
                mas = data.split(SEP);
                if(param == 1) windowReceiptAdd2(mas[0], mas[1]);
                if(param == 2) windowDownAdd2(mas[0], mas[1]);
            }
        }
    });
    else addBorderRed("barcode");
}
function baseStorageProof(that){     // Проверка, хватает ли места в хранилище
    var id = $(that).attr("data");
    count = $("#defcount_1").val();
    BASE_STORAGE = id;
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "baseStorageCount", id : id, count : count},
        success: function(data) {
            //id = $(that).attr("data");
            if(data == 0){
                $("#storage_hidden_id").val("-1");
                addBorderRed("storage_1");
            }
            else{
                $("#storage_hidden_id").val(id);
                addBorderGreen("storage_1");
            }
        }
    });
}
function baseStorageLoad(that){     // Загрузка хранилищ выбранной базы
    id = $(that).attr("data");
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "baseStorageLoad", id : id},
        success: function(data) {
            $("#storage_base").html(data);
            $("#storage_hidden_id").val("-1");
            selectLoad("#storage_base");
        }
    });
}
function calculateGRPrices(val){
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
function productCRUDCheck(val){
    let min = $(val).attr('min');
    let currentVal = $(val).val().replace(' ', '');
    $(val).val(currentVal);
    let pType = $(val).attr('id');
    let pText = '';
    switch (pType){
        case 'price_wholesale': pText = 'оптовая'; break;
        case 'price_sale': pText = 'розничная'; break;
    }
    if(Number.parseInt(currentVal) < Number.parseInt(min)){
        alert('Минимальная '+pText+' цена: '+min);
        $(val).val(min);
    }
    if(currentVal == ''){
        $(val).val(min);
    }
}
function receiptAdd(id, type){       // Приемка товара
    provider = $("#provider_hidden").val();
    count = $("#defcount_1").val();
    price = $("#price_purchase").val();
    gross = $("#price_wholesale").val();
    retail = $("#price_sale").val();
    base = $("#base_1_hidden").val();
    storage = $("#storage_hidden_id").val();
    if(type == 3) storage = -2;

    if(provider == -1) addBorderRed("provider");
    if(price == "") addBorderRed("price");
    if(base == -1) addBorderRed("base_1");
    if(storage == -1) addBorderRed("storage_1");

    code = 0;
    payer = 0;

    if(type == 1){
        payer = $("#payer_hidden").val();
        if(payer == -1 ) addBorderRed("payer");
        if(type == 1 && $("#codes_back").css("display") == "block"){
            code_str = $("#codes").val();    //.replace(/\s+/g, '')
            if(count > 0){
                mas = code_str.split(", ");
                if(mas.length == count){
                    code = "";
                    for(i = 0; i < count; i++) code = code + mas[i] + "%-%";
                    code = code.replace("<", "&lt;");
                }
                else addBorderRed("codes");
            }
            else{
                if(code_str = "") addBorderRed("codes");
            }

        }
    }



    if($(".border_red").length == 0 && storage != -1){
        $.ajax({
            url: "../../../../ajax/admin/none.php",
            dataType: "html",
            type: "POST",
            data: {
                methodName : "receiptAdd",
                id : id,
                type : type,
                provider : provider,
                count : count,
                price : price,
                gross: gross,
                retail: retail,
                base : base,
                storage : storage,
                payer : payer,
                code : code
            },
            success: function(data) {
                //alert(data);
                if(data == 0) alert("Введены некоторые коды, которые уже есть в системе");
                else{
                    switch(type){
                        case 1 : location.href = SERVER + "cp/tires"; break;
                        case 2 : location.href = SERVER + "cp/disks"; break;
                        case 3 : location.href = SERVER + "cp/products"; break;
                    }
                }
            }
        });
    }

}
function clearBasket(){       // Приемка товара
    $.ajax({
        url: "../../../../ajax/admin/clear.php",
        dataType: "html",
        type: "POST",
        success: function(data) {
            $('#sales_head_otl').remove()
        }
    });

}
function additionAdd(id, type){       // Пополнение
    provider = $("#provider_hidden").val();
    cureer = $("#cureer_hidden").val();
    count = $("#defcount_1").val();
    price = $("#price_purchase").val();
    gross = $("#price_wholesale").val();
    retail = $("#price_sale").val();
    base = $("#base_1_hidden").val();
    storage = $("#storage_hidden_id").val();
    if(type == 3) storage = -2;
    if(ADDITION_TYPE == 2){
        if(CONTACT == 0){
            addBorderRed("client");
            addBorderRed("client_phone");
        }
    }

    information = $("#information").val();
    date = $("#calendar input").val();

    if(provider == -1) addBorderRed("provider");
    //if(cureer == -1) addBorderRed("cureer");
    if(price == "") addBorderRed("price");
    if(base == -1) addBorderRed("base_1");
    if(storage == -1) addBorderRed("storage_1");
    //alert(CONTACT);
    if($(".border_red").length == 0 && storage != -1){
        $.ajax({
            url: "../../../../ajax/admin/none.php",
            dataType: "html",
            type: "POST",
            data: {
                methodName : "additionAdd",
                id : id,
                type : type,
                provider : provider,
                cureer : cureer,
                count : count,
                price : price,
                gross: gross,
                retail: retail,
                base : base,
                storage : storage,
                addition_type : ADDITION_TYPE,
                contact : CONTACT,
                information : information,
                date : date
            },
            success: function(data) {
                //alert(data);
                switch(type){
                    case 1 : location.href = SERVER + "cp/tires"; break;
                    case 2 : location.href = SERVER + "cp/disks"; break;
                    case 3 : location.href = SERVER + "cp/products"; break;
                }
            }
        });
    }
}
function productStorageClick(that){
    $(".storage_str_active").removeClass("storage_str_active");
    $(that).addClass("storage_str_active");
    $(".border_red").removeClass("border_red");
    STORAGE = $(that).attr("data");
}
function downAdd(id, type){
    count = $("#defcount_1").val();
    if(STORAGE == 0 && $(".storage_str").length > 0){
        STORAGE = $(".storage_str").first().attr("data");
    }
    $(".storage_str").each(function(){
        if($(this).attr("data") == STORAGE){
            if(parseInt($(this).children("span1").html()) < count) $(this).addClass("border_red");
        }
    });
    if($("#osnovanie_hidden").val() == "-1") addBorderRed("osnovanie");
    if($("#osnovanie_hidden").val() == "-2") info = $("#osnovanie_textarea").val(); else info = $("#osnovanie_hidden").val();
    if(info == ""){
        addBorderRed("osnovanie");
        addBorderRed("osnovanie_textarea");
    }
    //alert(STORAGE);
    if($(".border_red").length == 0 && STORAGE != 0) $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "downAdd", id : id, type : type, count : count, storage : STORAGE, info : info},
        success: function(data) {
            //alert(data);
            closeWindow();
        }
    });

}
function downAddOsnovanie(param){           // Открытие поля для ввода основания списания
    $("#osnovanie_hidden").val(param);
    if(param == -2){
        $("#osnovanie_textarea").css("display", "block");
    }
    else {
        $("#osnovanie_textarea").css("display", "none");
    }

}
function receiptPopolnChange(param, that){     // Смена между приемкой и пополнением
    $(".head_type_active").removeClass("head_type_active");
    $(that).addClass("head_type_active");
    if(param == 2){
        $(".receiptAdd").css("display", "none");
        $(".additionAdd").css("display", "inline-block");
        $(".receipt_str_1").css("display", "none");
        $(".receipt_str_2").css("display", "block");
    }
    else {
        $(".receipt_str_1").css("display", "block");
        $(".receipt_str_2").css("display", "none");
        $(".storage_client_type").first().click();
        $(".receiptAdd").css("display", "inline-block");
        $(".additionAdd").css("display", "none");
        deleteBorderRed("#client_phone");
        deleteBorderRed("#client");
    }
}
function storageClientChange(param, that){     // Смена между складом или под клиента
    $(".storage_client_type_active").removeClass("storage_client_type_active");
    $(that).addClass("storage_client_type_active");
    ADDITION_TYPE = param;
    if(param == 2) $(".receipt_str_3").css("display", "block");
    else $(".receipt_str_3").css("display", "none");
}
function movingAdd(id, type){
    count = $("#defcount_1").val();
    if(STORAGE == 0 && $(".storage_str").length > 0){
        STORAGE = $(".storage_str").first().attr("data");
    }
    $(".storage_str").each(function(){
        if($(this).attr("data") == STORAGE){
            if(parseInt($(this).children("span1").html()) < count) $(this).addClass("border_red");
        }
    });
    cureer = $("#cureer_hidden").val();
    //if(cureer == -1) addBorderRed("cureer");

    date = $("#input_calendar").val();

    if($(".border_red").length == 0 && STORAGE != 0 && BASE_STORAGE != 0) $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "movingAdd", id : id, type : type, date_plan : date, count : count, storage : STORAGE, kuda : BASE_STORAGE, cureer : cureer},
        success: function(data) {
            //alert(data);
            closeWindow();
        }
    });

}
function productSaleAdd(id, type){       // Добавление товара в отложенные покупки
    param = 0;
    count = 0;
    if(type == 4){
        if($("radioimg").length > 0){
            if($("#radioimg_1").is(":checked")) param = 1;
            if($("#radioimg_2").is(":checked")) param = 2;
            if($("#radioimg_3").is(":checked")) param = 3;
        }
        count = $("#defcount_1").val();

    }
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "productSaleAdd", id : id, type : type, param : param, count : count},
        success: function(data) {
            $("#window_3").fadeOut();
            if(data == 1) windowNotification("Продукт успешно добавлен в отложенные покупки!", "Хорошо");
            else windowNotification("Такой продукт уже есть в отложенных", "Хорошо");
        }
    });
    var response = '';
    $.ajax({ type: "GET",   
        url: "/ajax/admin/col.php",   
        async: false,
        success : function(text)
        {
            response = text;
        }
    });
    $('.btn-basket').html("<div id='sales_head_otl' class='sales_head_otl'>"+response+"</div>")
}
function productDelete(pId, type, payer){      // Удаление товара
    if(payer == undefined) payer = 0;
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "productDelete", id : pId, type : type, payer : payer},
        success: function(data) {
            //alert(data);
            $("#window_3").fadeOut();
            location.reload();
            //windowNotification("Товар удален!", "Хорошо");
        }
    });
}
function codeImgReload(id, that){        // Обновление фотографии у кода
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "codeImgReload", id : id},
        success: function(data) {
            $(that).html("Картинка обновилась");
            //mas = data.split("%-%");
            //windowCodesView(mas[0], 3, mas[1]);
        }
    });
}
