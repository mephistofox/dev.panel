function productsStart(){   // Загрузка общей плашки
    $.ajax({
        url: "../../../../ajax/admin/products.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "productsStart"},
        success: function(data) {
            $("#products").html(data);
            butLoad();
            selectLoad();
            selectTableLoad();
            productsSearch();

        }
    });
}
function productsSearch(param, param_2){    // Загрузка списка услуг согласно поиску
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
    if($("#name").length > 0 && $("#name").val().length > 1) name = $("#name").val(); else name = "";
    if($("#params").length > 0 && $("#params").val().length > 1) params = $("#params").val(); else params = "";
    if($("#note").length > 0 && $("#note").val().length > 1) note = $("#note").val(); else note = "";

    $.ajax({
        url: "../../../../ajax/admin/products.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "productsSearch",
            article : article,
            count : count,
            price_purchase : price_purchase,
            price_sale : price_sale,
            price_wholesale : price_wholesale,
            name : name,
            params : params,
            note : note
        },
        success: function(data) {
            //alert(data);
            $("#products_body_list").html(data);
            selectLoad(".product_item");
            //copyReady();     Активация правой кнопки мыши
        }
    });
}
function productsRadiusSelect(that){      // Выбор радиуса шины
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
function productsPriceChange(id, param){
    price = $("#price_change").val();
    if(price == "") addBorderRed("price_change");
    else {
        $.ajax({
            url: "../../../../ajax/admin/products.php",
            dataType: "html",
            type: "POST",
            data: {methodName : "productsPriceChange", id : id, param : param, price : price},
            success: function(data){
                productsSearch();
            }
        });
        $("#price_" + param).html(price);
        $("#window_4").fadeOut();
        $("#window_3").fadeIn();
    }
}
function productsRedact(id){       // Редактирование товара
    product_name = $("#product_name").val();
    product_params = $("#product_params").val();
    product_note = $("#product_note").val();

    price_purchase = $("#price_purchase").val();
    price_wholesale = $("#price_wholesale").val();
    price_sale = $("#price_sale").val();

    if(product_name == "") addBorderRed("product_name");
    if(product_params == "") addBorderRed("product_params");
    if(product_note == "") addBorderRed("product_note");

    if($(".border_red").length == 0) $.ajax({
        url: "../../../../ajax/admin/products.php",
        dataType: "html",
        type: "POST",
        data: {
            methodName : "productsRedact",
            id : id,

            product_name : product_name,
            product_params : product_params,
            product_note : product_note,

            price_purchase : price_purchase,
            price_wholesale : price_wholesale,
            price_sale : price_sale,

            photos : PHOTOS,
            general_photo : GENERAL_PHOTO
        },
        success: function(data) {
            //alert(data);
            location.href = SERVER + "cp/products";
        }
    });

}



