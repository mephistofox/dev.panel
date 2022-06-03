function closeWindow(){    // Закрытие любого окна
    $(".windows").fadeOut(10);
    $("#window_background").fadeOut(10);
    $("#window_2").css("z-index", "101");
    $("#window_1_body").html("");
    $("#window_2_body").html("");
    var response = '';
    $.ajax({ type: "GET",   
                url: "/ajax/admin/col.php",   
                async: false,
                success : function(text)
                {
                    response = text;
                }
    });
    $('#sales_head_otl').text(response)
}
function windowOpenExitCabinet(){     // Открытие окна выхода из личного кабинета
    $("#window_background").fadeIn(1);
    $("#window_1_head").html("Покидаем систему управления");
    $("#window_1_footer_left").html("<div class = \"button_red\" onClick = \"exitCabinet();\">Выйти</div>");
    $("#window_1_footer_right").html("<div class = \"link_blue\" onClick = \"closeWindow();\">Остаться</div>");
    $("#window_1").fadeIn(1);
}
function windowNotification(data, button){     // Открытие окна с уведомлением
    $("#window_background").fadeIn(0);
    $("#window_1_head").html(data);
    $("#window_1_footer_left").html("<div class = 'button_green' onClick = 'closeWindow();buttonClick(this);'>" + button + "</div>");
    $("#window_1").fadeIn(0);
    if (data.search("успешно добавлен")>0) {
        closeWindow();
    }
}
function windowMap(address){                 // Адрес на карте
    $("#window_2_body").html(address);
    $("#window_2_footer").html("<div class = 'button_green' onClick = 'closeWindow();buttonClick(this);'>Закрыть</div>");
    $("#window_2").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowClientView(id){             // Открытие карточки клиента
    ID = id;
    $("#window_3_head").html("Карточка клиента");
    $.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "clientsLoad", id : id},
        success: function(data) {
            $("#window_3_body").html(data);
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'clientsChange(" + id + ");buttonClick(this);'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowClientAdd(){            // Окно добавления клиента
    $("#window_3_head").html("Новый клиент");
    getTemplateHTML("clients/client_add.html", "window_3_body", addPhoneMask());
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'clientsAdd();buttonClick(this);'>Добавить</div><checkbox style = 'margin-top: 20px; display: block;'><input id = 'checkbox_1' type = 'checkbox' /><label for = 'checkbox_1'>После добавления оформить продажу</label></checkbox>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowClientRedact(id){        // Окно редактирования клиента
    $("#window_3_head").html("Редактирование клиента");
    $.ajax({
        url: "../../../../ajax/admin/clients.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "clientsLoad2", id : id},
        success: function(data) {
            $("#window_3_body").html(data);
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'clientsRedact(" + id + ");buttonClick(this);'>Изменить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowClientSend(param){        // Окно рассылки сообщений клиентам
    $("#window_3_head").html("СМС сообщение");
    switch(param){
        case 1: $("#window_3_head").append("<span>всем клиентам</span>"); break;
        case 2: $("#window_3_head").append("<span>оптовым клиентам</span>"); break;
        case 3: $("#window_3_head").append("<span>розничным клиентам</span>"); break;
    }

    $("#window_3_body").html("<textarea id = 'message' class = 'textarea'></textarea>");
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'clientsSend(" + param + ");buttonClick(this);'>Разослать</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowColumnSettings(param){      // Открытие окна настройки отображения столбцов
    switch(param){
        case 1: text = "Услуги"  ; break;
        case 2: text = "Шины"    ; break;
        case 3: text = "Диски"   ; break;
        case 4: text = "Продукты"; break;
        case 5: text = "Движения"; break;
        case 6: text = "Продажи" ; break;
        case 7: text = "Операции"; break;
    }
    text += "<span>колонки с данными и их порядок</span>";
    $("#window_3_head").html(text);
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "getColumn", param : param},
        success: function(data) {
            COLUMN_LIST = "";
            $("#window_3_body").html(data);
            $("#window_3_body").sortable({
                handle: ".sort_item_drag",
                axis: "y",
                containment: "#window_3_body"
            });
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' id = 'sort_button' onClick = 'buttonClick(this);columnSave(" + param + ");'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowServiceAdd(){       // Окно добавления новой услуги
    $("#window_3_head").html("Новая услуга");
    $.ajax({
        url: "../../../../ajax/admin/services.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "servicesAddLoad"},
        success: function(data) {
            $("#window_3_body").html(data);
            defCountLoad();
            inputNumber();
            inputDecimal();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);servicesAdd();'>Добавить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowServiceView(id, name){     // Вывод услуги
    $("#window_3_head").html(name);
    $.ajax({
        url: "../../../../ajax/admin/services.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "servicesLoad", id : id},
        success: function(data) {
            $("#window_3_body").html(data);
            defCountLoad();
            inputNumber();
            radioimgLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);productSaleAdd(" + id + ", 4);'>Продать</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowServiceRedact(id){       // Окно редактирования услуги
    $("#window_3_head").html("Редактирование услуги");
    $.ajax({
        url: "../../../../ajax/admin/services.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "servicesRedactLoad", id : id},
        success: function(data) {
            $("#window_3_body").html(data);
            defCountLoad();
            inputNumber();
            inputDecimal();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);servicesRedact(" + id + ");'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowSeasonAdd(){       // Окно добавления нового сезонного хранения
    $("#window_3_head").html("Сезонное хранение резины");
    $.ajax({
        url: "../../../../ajax/admin/services.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "servicesSeasonAddLoad"},
        success: function(data) {
            $("#window_3_body").html(data);
            defCountLoad();
            inputNumber();
            inputDecimal();
            calenderActivate("calendar", 1, "");
            addPhoneMask("phone_add");
            selectLoad();
            doublebuttonLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);servicesSeasonAdd();'>Оформить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowTireView(id, payer, status){     // Вывод карточки шины
    if(payer == undefined) payer = 0;
    $.ajax({
        url: "../../../../ajax/admin/tires.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "tiresLoad", id : id, payer : payer},
        success: function(data) {
            mas = data.split(SEP);
            $("#window_3_head").html("Шина " + mas[0]);
            $("#window_3_body").html(mas[1]);
            //defCountLoad();
            //inputNumber();
            //radioimgLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);windowReceiptAdd2(" + id + ", 1);'>Принять</div>");
    $("#window_3_footer").append("<div class = 'button_yellow inline' style = 'margin-left: 25px;' onClick = 'buttonClick(this);windowDownAdd2(" + id + ", 1);'>Списать</div>");
    $("#window_3_footer").append("<div class = 'link_blue_3 inline' style = 'margin-left: 25px;' onClick = 'productSaleAdd(" + id + ", 1);'>Продать</div>");
    if(status > 0) $("#window_3_footer").append("<div class = 'button_orange inline' style = 'margin-left: 25px;' onClick = 'tireCodesPrint(" + id + ", " + payer + ");'>Коды маркировки</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
    
}
function windowDiskView(id){     // Вывод карточки диска
    $.ajax({
        url: "../../../../ajax/admin/disks.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "disksLoad", id : id},
        success: function(data) {
            mas = data.split(SEP);
            $("#window_3_head").html("Диск " + mas[0]);
            $("#window_3_body").html(mas[1]);
            //defCountLoad();
            //inputNumber();
            //radioimgLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);windowReceiptAdd2(" + id + ", 2);'>Принять</div>");
    $("#window_3_footer").append("<div class = 'button_yellow inline' style = 'margin-left: 25px;' onClick = 'buttonClick(this);windowDownAdd2(" + id + ", 2);'>Списать</div>");
    $("#window_3_footer").append("<div class = 'link_blue_3 inline' style = 'margin-left: 25px;' onClick = 'productSaleAdd(" + id + ", 2);'>Продать</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowProductView(id){     // Вывод карточки товара
    $.ajax({
        url: "../../../../ajax/admin/products.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "productsLoad", id : id},
        success: function(data) {
            mas = data.split(SEP);
            $("#window_3_head").html(mas[0]);
            $("#window_3_body").html(mas[1]);
            //defCountLoad();
            //inputNumber();
            //radioimgLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);windowReceiptAdd2(" + id + ", 3);'>Принять</div>");
    $("#window_3_footer").append("<div class = 'button_yellow inline' style = 'margin-left: 25px;' onClick = 'buttonClick(this);windowDownAdd2(" + id + ", 3);'>Списать</div>");
    $("#window_3_footer").append("<div class = 'link_blue_3 inline' style = 'margin-left: 25px;' onClick = 'productSaleAdd(" + id + ", 3);'>Продать</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowReceiptAdd(param){     // Открытие окна приемки
    $("#window_3_head").html("Приёмка");
    $("#window_3_body").html("<input type = 'text' id = 'barcode' onKeyUp = 'deleteBorderRed(this);' class = 'input height-28' style = 'width: 204px' placeholder = 'Штрих-код или артикул'><div class = 'right_receipt' onClick = 'windowPositionAdd(" + param + ");'>Новая позиция</div>");
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);barcodeArticleProof(1);'>Приход</div>");
    $("#window_3_footer").css("display", "block");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowReceiptAdd2(id, type, param){       // Открытие окна продолжения приемки либо пополнения
    $("#window_3_head").html("<div onClick = 'receiptPopolnChange(1, this);' class = 'head_type head_type_active'>Приёмка</div><div id = 'popoln_button' onClick = 'receiptPopolnChange(2, this);' class = 'head_type'>Пополнение</div>");

    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "receiptProductLoad", id : id, type : type},
        success: function(data) {
            $("#window_3_body").html(data);
            defCountLoad();
            inputNumber();
            selectLoad("#base_storage");
            selectLoad(".receipt_str");
            selectLoad("#storage_base");
            calenderActivate("calendar", 1, "");
            if(param !== undefined) $("#popoln_button").click();
            //radioimgLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline receiptAdd' onClick = 'buttonClick(this); receiptAdd(" + id + ", " + type + ");'>Принять</div><div class = 'button_green inline additionAdd' onClick = 'buttonClick(this); additionAdd(" + id + ", " + type + ");'>Принять</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowPositionAdd(param){   // Открытие окна добавления новой позиции
    $("#window_3_head").html("Новая позиция");
    PARAM_ACTIVE = 0;
    PHOTOS = SEP;
    GENERAL_PHOTO = 0;
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "positionAddLoad", param : param},
        success: function(data) {
            $("#window_3_body").html(data);
            doublebuttonLoad(productTypeChange);
            selectLoad("#base_storage");
            selectLoad(".pa_str");
            //defCountLoad();
            //inputNumber();
            //radioimgLoad();
        }
    });
    $("#window_3_footer").css("display", "none");
    $("#window_3_footer").html("<div class = 'button_green inline' id = 'position_add_button' onClick = 'buttonClick(this); productAdd();'>Добавить</div><checkbox style = 'margin-top: 20px; display: block;'><input id = 'checkbox_1' type = 'checkbox' /><label for = 'checkbox_1'>После добавления начать приёмку</label></checkbox>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowPositionPhoto(par){      // Открытие окна добавления фото к товару
    if(par === undefined){
        val = $("#doublebutton_1 > .active").html();
        switch(val){
            case "Шина": param = "шины"; break;
            case "Диск": param = "диска"; break;
            case "Товар": param = "товара"; break;
        }
    }
    else{
        switch(par){
            case 1: param = "шины"; break;
            case 2: param = "диска"; break;
            case 3: param = "товара"; break;
        }
    }
    $("#window_4_head").html("Фото " + param);

    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "positionAddLoadPhotos", photos : PHOTOS},
        success: function(data) {
            $("#window_4_body").html(data);
            addImgActivate(1);
            if($("#position_photos_right .load_images_item").length > 4) $("#img_type_add").css("display", "none");
            if(GENERAL_PHOTO != 0){
                $(".load_images_item").each(function(){
                    if($(this).children("filename").html() == GENERAL_PHOTO){
                        $(this).children("cover").css("visibility", "visible");
                        $(this).children("img").css("border", "2px solid #000000");
                    }

                })
            }
        }
    });

    $("#window_4_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);productPhotoClose();'>Хорошо</div>");
    $("#window_3").fadeOut(10);
    $("#window_4").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowTireRedact(id, payer){   // Открытие окна редактирования шины
    $("#window_3_head").html("Редактирование шины");
    PARAM_ACTIVE = 0;
    PHOTOS = SEP;
    GENERAL_PHOTO = 0;
    $.ajax({
        url: "../../../../ajax/admin/tires.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "tireLoadRedact", id : id, payer : payer},
        success: function(data) {
            mas = data.split("X-X-X");
            $("#window_3_body").html(mas[0]);
            if(mas[1] != "") PHOTOS = mas[1]; else PHOTOS = SEP;
            doublebuttonLoad(productTypeChange);
            //selectLoad("#base_storage");
            selectLoad(".pa_str");
            let buyout = $("#price_purchase").val();
            let grossCurrent = $("#price_wholesale").val();
            let retailCurrent = $("#price_sale").val();
            setCurrentState(buyout, grossCurrent, retailCurrent);
            //defCountLoad();
            //inputNumber();
            //radioimgLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' id = 'position_add_button' onClick = 'buttonClick(this);tiresRedact(" + id + ");'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function setCurrentState(b,g,r){
    globalBuyoutCurrent = b;
    globalGrossCurrent = g;
    globalRetailCurrent = r;
}
function windowDiskRedact(id){   // Открытие окна редактирования диска
    $("#window_3_head").html("Редактирование диска");
    PARAM_ACTIVE = 0;
    PHOTOS = SEP;
    GENERAL_PHOTO = 0;
    $.ajax({
        url: "../../../../ajax/admin/disks.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "diskLoadRedact", id : id},
        success: function(data) {
            mas = data.split("X-X-X");
            $("#window_3_body").html(mas[0]);
            if(mas[1] != "") PHOTOS = mas[1]; else PHOTOS = SEP;
            doublebuttonLoad(productTypeChange);
            //selectLoad("#base_storage");
            selectLoad(".pa_str");
            //defCountLoad();
            //inputNumber();
            //radioimgLoad();
            let buyout = $("#price_purchase").val();
            let grossCurrent = $("#price_wholesale").val();
            let retailCurrent = $("#price_sale").val();
            setCurrentState(buyout, grossCurrent, retailCurrent);
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' id = 'position_add_button' onClick = 'buttonClick(this);disksRedact(" + id + ");'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowProductRedact(id){   // Открытие окна редактирования товара
    $("#window_3_head").html("Редактирование товара");
    PARAM_ACTIVE = 0;
    PHOTOS = SEP;
    GENERAL_PHOTO = 0;
    $.ajax({
        url: "../../../../ajax/admin/products.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "productLoadRedact", id : id},
        success: function(data) {
            mas = data.split("X-X-X");
            $("#window_3_body").html(mas[0]);
            if(mas[1] != "") PHOTOS = mas[1]; else PHOTOS = SEP;
            doublebuttonLoad(productTypeChange);
            //selectLoad("#base_storage");
            selectLoad(".pa_str");
            //defCountLoad();
            //inputNumber();
            //radioimgLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' id = 'position_add_button' onClick = 'buttonClick(this);productsRedact(" + id + ");'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowTirePriceRedact(id, param){     // Смена стоимости шины
    switch(param){
        case 1: head = "Закупочная цена"; break;
        case 2: head = "Продажная цена"; break;
        case 3: head = "Оптовая цена"; break;
    }
    $("#window_4_head").html(head);
    val = $("#price_" + param).html();
    $("#window_4_body").html("<input id = 'price_change' type = 'text' class = 'input height-28' value = '" + val + "' />");
    $("#window_4_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);tiresPriceChange(" + id + ", " + param + ");'>Изменить</div>");
    $("#window_3").fadeOut(10);
    $("#window_4").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowDiskPriceRedact(id, param){     // Смена стоимости диска
    switch(param){
        case 1: head = "Закупочная цена"; break;
        case 2: head = "Продажная цена"; break;
        case 3: head = "Оптовая цена"; break;
    }
    $("#window_4_head").html(head);
    val = $("#price_" + param).html();
    $("#window_4_body").html("<input id = 'price_change' type = 'text' class = 'input height-28' value = '" + val + "' />");
    $("#window_4_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);disksPriceChange(" + id + ", " + param + ");'>Изменить</div>");
    $("#window_3").fadeOut(10);
    $("#window_4").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowProductPriceRedact(id, param){     // Смена стоимости товара
    switch(param){
        case 1: head = "Закупочная цена"; break;
        case 2: head = "Продажная цена"; break;
        case 3: head = "Оптовая цена"; break;
    }
    $("#window_4_head").html(head);
    val = $("#price_" + param).html();
    $("#window_4_body").html("<input id = 'price_change' type = 'text' class = 'input height-28' value = '" + val + "' />");
    $("#window_4_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);productsPriceChange(" + id + ", " + param + ");'>Изменить</div>");
    $("#window_3").fadeOut(10);
    $("#window_4").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowBaseAdd(){               // Окно добавления новой базы
    $("#window_3_head").html("Новая база");
    dop = function(){
        $('#time_1').mask("99:99");
        $('#time_2').mask("99:99");
    };
    getTemplateHTML("settings/base_add.html", "window_3_body", dop);
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); settingsBaseAdd();'>Добавить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowBaseStorageAdd(id){   // Окно добавления хранилища в базу
    $("#window_3_head").html("Новое хранилище");
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsBaseStorageAddLoad", id : id},
        success: function(data) {
            $("#window_3_body").html(data);
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); settingsBaseStorageAdd(" + id + ");'>Добавить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowBaseStorageAdd2(id, id_2){     // Окно добавления хранилища в хранилище
    $("#window_3_head").html("Новое хранилище");
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsBaseStorageAddLoad2", id : id, id_2 : id_2},
        success: function(data) {
            $("#window_3_body").html(data);
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); settingsBaseStorageAdd2(" + id + ", " + id_2 + ");'>Добавить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowBaseRedact(id){               // Окно редактирования базы
    $("#window_3_head").html("Редактирование базы");
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsBaseRedactLoad", id : id},
        success: function(data) {
            $("#window_3_body").html(data);
            $("#time_1").mask("99:99");
            $("#time_2").mask("99:99");
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); settingsBaseRedact(" + id + ");'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowStorageRedact(id){            // Окно редактирования хранилища
    $("#window_3_head").html("Редактирование хранилища");
    $.ajax({
        url: "../../../../ajax/admin/settings.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "settingsBaseStorageRedactLoad", id : id},
        success: function(data) {
            $("#window_3_body").html(data);
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); settingsBaseStorageRedact(" + id + ");'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowWarehousesTimeRedact(id){     // Окно редактирования времени выдачи у базы
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "loadBaseName", id : id},
        success: function(data) {
            $("#window_3_head").html("Часы работы пункта выдачи<span>" + data + "</span>");
        }
    });
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "loadBaseTime", id : id},
        success: function(data) {
            mas = data.split(SEP);
            $("#window_3_body").html("<div class = 'sba_str_right'><input type = 'text' value = '" + mas[0] + "' onkeyup = 'deleteBorderRed(this);' id = 'time_1' class = 'input height-28' onchange = 'warehousesBaseTimeProof(this);' style = 'width: 49px;'><span2> — </span2><input type = 'text' value = '" + mas[1] + "' onkeyup = 'deleteBorderRed(this);' id = 'time_2' class = 'input height-28' onchange = 'warehousesBaseTimeProof(this);' style = 'width: 49px;'></div>");
            $('#time_1').mask("99:99");
            $('#time_2').mask("99:99");
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); warehousesBaseTimeChange(" + id + ")'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowDownAdd(param){     // Открытие окна списания
    $("#window_3_head").html("Списание");
    $("#window_3_body").html("<input type = 'text' id = 'barcode' onKeyUp = 'deleteBorderRed(this);' class = 'input height-28' style = 'width: 204px' placeholder = 'Штрих-код или артикул'>");
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);barcodeArticleProof(2);'>Списание</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowDownAdd2(id, type){       // Открытие окна продолжения списания
    $("#window_3_head").html("Списание");
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "receiptDownLoad", id : id, type : type},
        success: function(data) {
            $("#window_3_body").html(data);
            defCountLoad();
            inputNumber();
            selectLoad("#base_storage");
            selectLoad(".receipt_str");
            selectLoad("storage_right");
            if(type < 3){
                if(STORAGE > 0){
                    $(".storage_str").each(function(){
                        if($(this).attr("data") == STORAGE) $(this).click();
                    })
                }
                else{
                    $(".storage_str").first().click();
                }
            }
            else{
                if(BASE > 0){
                    $(".storage_str").each(function(){
                        if($(this).attr("data") == BASE) $(this).click();
                    })
                }
                else{
                    $(".storage_str").first().click();
                }
            }


            //radioimgLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); downAdd(" + id + ", " + type + ");'>Списать</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowMovingAdd(id, type){      // Окно добавления перемещения
    $("#window_3_head").html("Перемещение");
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "movingAddLoad", id : id, type : type},
        success: function(data) {
            $("#window_3_body").html(data);
            defCountLoad();
            inputNumber();
            selectLoad();
            calenderActivate("calendar", 1, "");
            if(STORAGE > 0){
                $(".storage_str").each(function(){
                    if($(this).attr("data") == STORAGE) $(this).click();
                })
            }
            else{
                $(".storage_str").first().click();
            }
            //radioimgLoad();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); movingAdd(" + id + ", " + type + ");'>Отправить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowSaleAdd(contact){      // Окно добавления продажи
    SALE_STATUS = 0;
    $("#window_3_head").html("Новая сделка");
    if(contact === undefined) contact = 0;
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesSaleAddLoad", contact : contact},
        success: function(data) {
            mas = data.split(SEP);
            temp = mas[1];
            $("#window_3_body").html(mas[0]).css("margin-top", "10px");
            $("#sales_head_bases_2").children("item").first().click();
            selectLoad(".select_base");
            calenderActivate("sa_date_cal", 1, "");
            if(temp !== 'undefined'){
                temp = temp.split("%");
                for(i = 0; i < temp.length - 1; i++){
                    salesAddProductAdd(temp[i]);
                }
            }
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); salesSaleAdd();'>Создать заказ</div>");
    closeWindow();
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);

}
function windowSaleView(ID){      // Окно просмотра продажи
    SALE_ID = ID;
    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesSaleViewHead", id : ID},
        success: function(data) {
            if(data == "-1") location.reload();
            else{
                mas = data.split("%-%");
                $("#window_3_head").html(mas[0]);
                SALE_STATUS = mas[1];
            }
        }
    });

    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesSaleViewBody", id : ID},
        success: function(data) {
            $("#window_3_body").html(data).css("margin-top", "-7px");
            if($("#sale_status_2").val() < 3) selectLoad("#sw_oplata .select_base");
            else{
                $("#oplata_comment").attr("disabled", true);
                $("#skidka_percent").attr("disabled", true);
                $("#skidka_ruble").attr("disabled", true);
            }
            selectLoad("#sa_cureer .select_base");
            inputNumber();
            salesSaleSkidka(1);
            number = 1;
            sale_status = $("#sale_status_2").val();
            if(sale_status == 7){
                $("#sv_movements").remove();
                $("#sa_poluch").remove();
                $("#sv_client").remove();
                $("#sv_skidka_3").remove();
                $("#sv_skidka").remove();
                $("#sw_oplata").remove();
                $("#sa_cureer").remove();
                $(".pl cross").remove();
                $("#sa_cureer2").remove();

            }

            $(".pl_number").each(function(){
                $(this).html(number);
                number++;
            })
            if(SALE_STATUS > 2) $(".pl cross").css("display", "none"); 
        }
    });

    $.ajax({
        url: "../../../../ajax/admin/sales.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "salesSaleViewFooter", id : ID},
        success: function(data) {
            $("#window_3_footer").html(data);
        }
    });
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowTransactionView(ID){      // Окно просмотра кассовой операции
    $.ajax({
        url: "../../../../ajax/admin/transactions.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "transactionsViewHead", id : ID},
        success: function(data) {
            $("#window_3_head").html("Кассовая операция<span>K" + data + "</span>");
        }
    });

    $.ajax({
        url: "../../../../ajax/admin/transactions.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "transactionsViewBody", id : ID},
        success: function(data) {
            $("#window_3_body").html(data).css("margin-top", "-7px");
        }
    });
    $("#window_3_footer").html("");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowCashEarly(){             // Рано открывать кассу
    $.ajax({
        url: "../../../../ajax/admin/cash.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "cashTime"},
        success: function(data) {
            $("#window_3_head").html("Слишком рано<span>Kасса может быть открыта не раньше " + data + "</span>");
        }
    });
    $("#window_3_body").html("<div id = 'cash_early'></div>");
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); closeWindow();'>Ясно</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowOplataAdd(){     // Открытие окна оплаты
    $("#window_3_head").html("Принять оплату");
    $("#window_3_body").html("<input type = 'text' id = 'barcode' onKeyUp = 'deleteBorderRed(this); cashSaleProof();' class = 'input height-28' style = 'width: 204px' placeholder = 'Штрих-код или ID продажи'> <barcode style = 'margin-top: 5px;'></barcode>");
    $("#window_3_footer").html("");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowCashDownAdd(){             // Окно списания из кассы
    $("#window_3_head").html("Списание из кассы");
    $.ajax({
        url: "../../../../ajax/admin/cash.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "cashDownAdd"},
        success: function(data) {
            $("#window_3_body").html(data);
            inputNumber();
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this);cashDownAdd();'>Списать</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowCashClose(){          // Окно закрытия кассы
    date = $("#co_head_date").html();
    $("#window_3_head").html("Рабочая смена<span>" + date + "</span>");
    $.ajax({
        url: "../../../../ajax/admin/cash.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "cashCloseLoad"},
        success: function(data) {
            $("#window_3_body").html(data);
            inputNumber();
            start = $("#duration_s").val();
            getTimeDuration(start, "time_duration");
        }
    });
    $("#window_3_footer").html("<div id = 'co_right_button' style = 'float: none;' onClick = 'cashClose();'>Закрыть</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowMovementView(id){     // Окно открытия движения
    $("#window_3_head").html("Движение");
    $.ajax({
        url: "../../../../ajax/admin/movements.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "movementsMovementView", id : id},
        success: function(data) {
            mas = data.split(SEP);
            $("#window_3_body").html(mas[0]);
            $(".receipt_str_1").css("display", "none");
            $(".receipt_str_2").css("display", "none");
            $(".receipt_str_3").css("display", "none");
            $(".receipt_str_4").css("display", "none");

            $(".receipt_str_" + mas[1]).css("display", "block");

            if(mas[2] == 0) $(".moving_delete").remove();
            if(mas[3] == 1) $(".moving_redact").remove();

            selectLoad();
        }
    });
    $("#window_3_footer").html("");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowMovementRedactView(id){     // Окно редактирования движения
    $("#window_3_head").html("Движение");
    $.ajax({
        url: "../../../../ajax/admin/movements.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "movementsMovementRedactLoad", id : id},
        success: function(data) {
            mas = data.split(SEP);
            $("#window_3_body").html(mas[0]);
            $(".receipt_str_1").css("display", "none");
            $(".receipt_str_2").css("display", "none");
            $(".receipt_str_3").css("display", "none");
            $(".receipt_str_4").css("display", "none");

            $(".receipt_str_" + mas[1]).css("display", "block");

            selectLoad();

            $(".moving_redact").remove();
            $(".moving_delete").remove();

            calenderActivate("move_date_plan", "", "");
            $("#input_move_date_plan").val(mas[2]);
            if(mas[1] == 2 || mas[1] == 3){
                otkuda = mas[3];
                $(".storage_str").each(function(){
                    if($(this).attr("data_2") == otkuda) $(this).click();
                })
            }
        }
    });
    $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); movementRedact(" + id + ")'>Сохранить</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowCodesView(id, param, payer){    // Коды маркировки из движений(1), продаж(2), шин(3)
    $("#window_3_head").html("Коды маркировки");
    $("#window_3_body").html("Происходит загрузка кодов. Это может занять некоторое время");
    if(payer == undefined) payer = 0;
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "codesLoad", id : id, param : param, payer : payer},
        success: function(data) {
            $("#window_3_body").html(data);
        }
    });
    $("#window_3_footer").html("");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowCodesView2(id){    // Коды маркировки с изображениями
    $("#window_3_head").html("Коды маркировки");
    $("#window_3_body").html("Идет загрузка кодов. Пожалуйста, подождите");
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "codesLoad2", sale : id},
        success: function(data) {
            $("#window_3_linkbar").html("<b><a onclick='printSaleCodes("+id+")' style='margin-left: 5px; color: #0884af; text-decoration: underline'>Распечатать коды с продажи</a></b><br><br>");
            $("#window_3_body").html(data);
        }
    });
    $("#window_3_footer").html("");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}
function windowCodesArea(tire, payer, param){    // Приемка либо списание кодов маркировки
    $("#window_3_head").html("Коды маркировки");
    if(param == 1) $("#window_3_head").append(" ПРИЕМКА");
    else $("#window_3_head").append(" СПИСАНИЕ");
    $("#window_3_body").html("<textarea class = 'textarea' id = 'code_text' style = 'width: 470px; height: 300px;'></textarea>");
    if(param == 1) $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); tireCodesChange(" + tire + ", " + payer + ", " + param + ")'>Принять</div>");
    else $("#window_3_footer").html("<div class = 'button_green inline' onClick = 'buttonClick(this); tireCodesChange(" + tire + ", " + payer + ", " + param + ")'>Списать</div>");
    $("#window_3").fadeIn(10);
    $("#window_background").fadeIn(10);
}

