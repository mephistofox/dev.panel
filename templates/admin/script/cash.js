var CURRENT_BASE = 0;
function cashStart(){    // Загрузка кассы
    $.ajax({
        url: "../../../../ajax/admin/cash.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "cashStart"},
        success: function(data) {
            mas = data.split(SEP);
            $("#cash").html(mas[0]);
            butLoad();
            selectLoad();
            inputNumber();
            getTime("cd_time_time");
            CURRENT_BASE = mas[1];
            cashSearch();
        }
    });
}
function cashSearch(param, param_2){    // Загрузка кассовых операций
    $.ajax({
        url: "../../../../ajax/admin/cash.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "cashSearch",
            base : CURRENT_BASE
        },
        success: function(data) {
            //alert(data);
            mas = data.split(SEP);
            $("#co_left_body").html(mas[0]);
            $("#co_right_body").html(mas[1]);
            //selectLoad(".sale_item");
            //copyReady();
        }
    });
}
function cashDefault(){              // Показывает данные для открытия смены
    $(".cs_start_hidden").css("display", "block");
    $("#cd_button_1").css("display", "none");
    //$("#cd_button_2").css("display", "inline-block");
}
function cashOpen(){              // Открытие кассы
    razmen = $("#cd_razmen").val();
    pass = $("#cd_password").val();
    if(pass.length > 0)$.ajax({
        url: "../../../../ajax/admin/cash.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "cashOpen", razmen : razmen, pass : pass, base : CURRENT_BASE},
        success: function(data) {
            //alert(data);
            switch(data){
                case "-1": $("#cd_password").addClass("border_red"); break;
                case "-2": windowCashEarly(); break;
                case "1" : location.reload(); break;
            }
            //$("#cash_body_list").html(data);
            //selectLoad(".sale_item");
            //copyReady();
        }
    });
}
function cashBaseChange(id){    // Открытие кассы другой базы
    CURRENT_BASE = id;
    if(USER_TYPE == 1){
        $.cookie("CURRENT_BASE", CURRENT_BASE, { expires: 7, path: '/' });
        location.reload();
    }
}
function cashSaleProof(){       // Проверка баркода продажи
    barcode = $("#barcode").val();
    if(barcode.length > 7){
        $.ajax({
            url: "../../../../ajax/admin/cash.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "cashSaleProof", barcode : barcode},
            success: function(data) {
                //console.log(data);
                if(data == 0) $("#barcode").addClass("border_red");
                else windowSaleView(data);
            }
        });
    }

}
function cashDownAdd(){    // Добавление списания
    summa = $("#cd_summa").val();
    reason = $("#cd_reason").val();
    pass = $("#cd_password").val();
    //alert(CURRENT_BASE);
    if(summa == 0 || summa == "") $("#cd_summa").addClass("border_red");
    if(reason == "") $("#cd_reason").addClass("border_red");
    if(pass.length == 0) $("#cd_password").addClass("border_red");
    if($(".border_red").length == 0) $.ajax({
            url: "../../../../ajax/admin/cash.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "cashDownAdd2", summa : summa, reason : reason, pass : pass, base : CURRENT_BASE},
            success: function(data) {
                alert(data);
                if(data == 0) $("#cd_password").addClass("border_red");
                else {
                    //closeWindow();
                    cashSearch();
                }
            }
        });
}
function cashClose(){     // Закрытие кассы
    if($("#bases_hidden").length > 0) base_dop = $("#bases_hidden").val();
    else base_dop = 0;
    razmen = $("#cd_razmen").val();
    pass = $("#cd_password").val();
    if(razmen == "") $("#cd_razmen").addClass("border_red");
    if(pass.length == 0) $("#cd_password").addClass("border_red");
    if($("#checkbox_scach").is(":checked")) check = 1; else check = 0;
    if($(".border_red").length == 0){
        $.ajax({
            url: "../../../../ajax/admin/cash.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "cashClose", razmen : razmen, pass : pass, check : check, base : CURRENT_BASE},
            success: function(data) {
                //alert(data);
                if(data == 0) $("#cd_password").addClass("border_red");
                else {
                    if(check == 1){
                        $.ajax({
                            url: "../../../../ajax/admin/excel.php",
                            dataType: "html",
                            type: "POST",
                            data: {methodName : "cashReport", base : CURRENT_BASE, id : 0, param : 1},
                            success: function(data) {
                                //alert(data);
                                getFile(data);
                                cashStart();
                                closeWindow();
                            }
                        });
                    }
                    else{
                        cashStart();
                        closeWindow();
                    }
                }
            }
        });

    }
}
function cashRightOsnovOpen(param){     // Выбор платежей либо оснований
    if(param == 1){
        $(".cr_item2").css("display", "none");
        $(".cr_item1").css("display", "block");
    }
    else{
        $(".cr_item1").css("display", "none");
        $(".cr_item2").css("display", "block");
    }

}




