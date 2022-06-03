// Ajax
function test(){
    name = 1;
    phone = 2;
    $.ajax({
        url: "../../../../ajax/admin/none.php",
        dataType: "html",
        type: "POST",
        data: {methodName : "test", name : name, phone : phone},
        success: function(data) {
            alert(data);
        }
    });
}