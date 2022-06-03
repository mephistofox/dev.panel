<?php
    $map = '<script src="https://api-maps.yandex.ru/2.1/?load=package.standard&lang=ru-RU&apikey=4280c083-e2dc-4345-b347-7a13d40bc5d2" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $( "#sortable" ).sortable();
        } );
    </script>
    <script>
    $("#map").fadeOut(0)
    $("#route").fadeOut(0)
    var baloons = {};
    function deliveryMap(e){
            $("#map").fadeIn(0)
            $("#route").fadeIn(0)
            var add;
            ymaps.ready(function () {
                window.myMap = new ymaps.Map("map", {
                    center: [59.898756, 30.253938],
                    zoom: 10
                }, {
                    searchControlProvider: "yandex#search"
                })
                window.myGeocoder = ymaps.geocode("г Санкт-Петербург, ул Калинина, д 5");
                myGeocoder.then(
                    function (res) {
                        myMap.geoObjects.add(
                            new ymaps.Placemark(res.geoObjects.get(0).geometry.getCoordinates(),
                                { iconContent: "База" },
                                { 
                                    preset: "islands#homeCircleIcon",
                                    iconColor: "#3b5998"
                                }
                            )
                            );
                        // res.geoObjects.get(0).geometry.getCoordinates()
                    },
                    function (err) {
                        alert("Ошибка");
                    }
                );
                
            })
            
            window.add = function(e) {
                var id = $(e).attr("id");
                var type = $(e).children().children(".item-type").text();
                var number = $(e).children().children(".item-number").text();
                var address = $(e).children().children(".item-address").text();
                var manager = $(e).children().children(".item-manager").text();
                var client = $(e).children().children(".item-client").text();
                var items = $(e).children(".item-title").html();
                var bstyle = "islands#redDeliveryIcon";
                if (type.substr("Отправка")=="Отправка"){
                    bstyle = "islands#darkBlueAirportIcon"
                }
                if (type.substr("Доставка")=="Доставка"){
                    bstyle = "islands#greenStretchyIcon"
                }
                var myGeocoder = ymaps.geocode("Санкт-Петербург,"+address);
                myGeocoder.then(
                    function (res) {
                        window.myMap.geoObjects.add(
                            window.baloons[`${id}`] = new ymaps.Placemark(res.geoObjects.get(0).geometry.getCoordinates(),
                                { iconContent: `${number}` },
                                { preset: bstyle },
                            )
                        );
                        dateCreate(0);
                        var balloon = null;
                        baloons[`${id}`].events.add("click", function () {addRoute($(`#${id}`))});
                        baloons[`${id}`].events.add("contextmenu", function () {
                            if (!myMap.balloon.isOpen()) {
                                balloon = myMap.balloon.open(baloons[id].geometry.getCoordinates(), $(e).html());
                            }
                        });
                    },
                    function (err) {
                        console.log(err)
                    }
                );
            }
            
            window.addRoute = function(e,l=true) {
                if ($(e).data("id")) {
                    var id = $(e).data("id");
                    var cont = $("#"+id).html()
                } else {
                    var id = $(e).attr("id")
                    var cont = $(e).html()
                }

                if (!l) {
                    id = id.replace("_temp","");
                }
                var selectBaloonStyle = {};
                selectBaloonStyle[$(e).text().search("Перемещение")>1]="islands#redDeliveryIcon";
                selectBaloonStyle[$(e).text().search("Отправка")>1]="islands#darkBlueAirportIcon";
                selectBaloonStyle[$(e).text().search("Доставка")>1]="islands#greenStretchyIcon";
                
                var selected = "islands#blackWasteIcon";

                if (baloons[`${id}`].options.get("preset") == selected){
                    baloons[`${id}`].options.set("preset",selectBaloonStyle[true])
                } else {
                    baloons[`${id}`].options.set("preset",selected)
                }

                if (l) {
                    if ($(`#${id}_temp`).length){
                        $(`#${id}_temp`).remove()
                        $(`#${id}`).show()
                    }else{
                        $("#sortable").append(`<li class="ui-state-default" style="width:275px;" id="${id}_temp">${cont}</li>`)
                        $(`#${id}_temp`).prepend(`<img style="float:right;cursor:pointer;" src="https://img.icons8.com/plasticine/100/000000/filled-trash.png" width="12px" onclick="addRoute($(this).parent(),false);$(this).parent().remove();">`)
                    }
                }
            }          
        }
        function dateCreate(d) {
            $(this).switchClass("active-date","")
            var dater = new Date();
            yyyy = dater.getFullYear();
            mm = dater.getMonth() + 1; // Months start at 0!
            dd = dater.getDate() + d;

            if (dd < 10) dd = "0" + dd;
            if (mm < 10) mm = "0" + mm;

            date = dd + "." + mm + "." + yyyy
            for (var i = 0; i < $(".item").length; i++) {
                element = $(".item")[i]
                id = $(element).attr("id");
                console.log($($(element).children(".item-date")).text().search(date))
                if ($(element).text().search(date)<1){
                    $(element).parent().parent().hide();
                    window.myMap.geoObjects.remove(baloons[id]);
                }else{
                    window.add($(".item")[i]);
                    $(element).parent().parent().show();
                }
            }
        }

        $("cross").click(function(){
            $("#map").hide()
            $("#route").hide()
        })

        $(document).keyup(function(e) {
            if (e.key === "Escape") { // escape key maps to keycode `27`
                $("#map").hide()
                $("#route").hide()
            }
        });
    </script>';
    $ATTENTION = "
        <div id='route'><ul id='sortable'></ul><button onclick='saveDiv(route,Title)'>Скачать маршрут</button></div>
        <div id='map'></div>
        <div id = 'attention_right' onClick = 'attentionOpen();'></div>
        <div id = 'attention'>
            <div id = 'attention_map'>
                <div id = 'wrs_map'></div>
                <div id = 'attention_map_dest'></div>
            </div>
            <div id = 'attention_base'>
                <cross onClick = 'attentionClose();'></cross>
                <div id = 'attention_base_head'>
                    <item data = '1' onClick = 'attentionLoad(this);'>Движения</item>
                    <item data = '2' onClick = 'attentionLoad(this);'>Брони</item>
                    <br>
                    <a href='#' onclick='dateCreate(-1)'>Вчера </a>
                    <a href='#' onclick='dateCreate(0)'> Сегодня </a>
                    <a href='#' onclick='dateCreate(1)'> Завтра</a>
                </div>
                <div id = 'attention_base_body'></div>
            </div>

        </div>".$map;


?>