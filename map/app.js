$(function () {
    function dateSelect() {
        $('.date-selector').width($('.btn-group').width())
        $('.date-selector').toggle()
    }
    dateSelect();
    $('.date-selector-btn').click(function () {
        dateSelect();
    })

    ymaps.ready(function () {
        $( "#sortable" ).sortable();
        window.myMap = new ymaps.Map("map", {
            center: [59.898756, 30.253938],
            zoom: 10
        }, {
            searchControlProvider: "yandex#search"
        })
        window.mapDotsInit()

        $.post('/ajax/admin/attention.php?methodName=getTmpMovements',{"methodName":"getTmpMovements"},function (movements) {
            for (let index = 0; index < movements.length; index++) {
               if (movements[index]) {
                id = movements[index].id
                $(`#${id}`).hide();
                myMap.geoObjects.remove(window.dots[id])
                console.log(movements);
               }
            }
        })
    })

// load cureers list
    $.post('/ajax/admin/attention.php?methodName=getCureers',{"methodName":"getCureers"},function (cureers) {
        var cureers = JSON.parse(cureers);
        for (let index = 0; index < cureers.length; index++) {
            const cureer = cureers[index];
            // console.log(cureer)
            $('select').append(`<option value="${cureer.id}">${cureer.name}</option>`)            
        }
    })

// Route methods
    window.appendRoute = function (e) {
        let id = $(e).attr('id');
        if (id) {
            if ($(`#${$(e).attr('id')}_temp`).length<1) {
                $('#sortable').append(`<li class="item alert alert-custom input-group mb-3 item-active" data-type="${$(e).data('type')}" data-num="${$(e).data('num')}" id="${$(e).attr('id')}_temp" role="alert"><div class="remove" onclick="removeRoute(this)"></div>${$(e).html()}</li>`)   
                if (window.dots[id]) {
                    myMap.geoObjects.add(window.dots[id]).options.set('iconColor', "#177BC9")
                }
            } else {
                $(`#${id}_temp`).remove();
                window.dots[id].options.unset('iconColor')
            }
        }
    }
    window.removeRoute = function (e) {
        // console.log()
        appendRoute($('#'+$(e).parent().attr('id').replace('_temp','')));
        $(e).parent().remove();
        $($('#'+$(e).parent().attr('id').replace('_temp',''))).switchClass('item-active')

    }

// Initiate date for items
    window.itemsDateLoad = function(){
        var el = $('.orig')
        for (let index = 0; index < el.length; index++) {
            element = $(el[index]).data('date');
            selected = $('#datepicker').val();
            if (element != selected) {
                $(el[index]).remove()
            }
            $($("#"+$(el[index]).attr('id'))[1]).remove()
        }
    }
    window.dateChange = function(){
        window.itemsLoad(function () {
            for (const key in window.dots) {
                const element = window.dots[key];
                window.myMap.geoObjects.removeAll()
            }
            setTimeout(window.itemsDateLoad(),500)
            window.mapDotsInit()
            window.loadTmpMovements()
        })
    }

// Set cureer for movement
    window.setCureer = function () {
        let items = [];
        for (let index = 0; index < $('li.item').length; index++) {
            const item = $($('li.item')[index]);
            // console.log(item)
            let number = item.data('num').replace('P','');
            var type = item.data('type');
            var table;
            if (type == 'Перемещение') {
                let cureer = $( "select option:selected" ).text();
                let cid = $('select').val();
                items.push({'number':number.replace('P',''),'cureer':cureer,'cureer_id':cid,'table':'movement'})
            } else {
                let cureer = $( "select option:selected" ).text();
                let cid = $('select').val();
                items.push({'number':number.replace('P',''),'cureer':cureer,'cureer_id':cid,'table':'sale'})
            }
            
        }
        $.post('/ajax/admin/attention.php?methodName=setCureers',{"methodName":"setCureers","items":items},function (d) {
            console.log(d)
        })
    }

//  Temporary movements
    window.loadTmpMovements = function(){
        if ($('.route').length) {
            $('.route').remove()
            $('.item').show()
        }
        $.post('/ajax/admin/attention.php?methodName=getTmpMovements',{"methodName":"getTmpMovements"},function (movements) {
            for (let index = 0; index < movements.length; index++) {
               if (movements[index]) {
                    id = movements[index].id
                    $(`#${id}`).hide();
               }
            }
        })
        $.post('/ajax/admin/attention.php?methodName=getTmpMovements',{"methodName":"getTmpMovements","cureer":$('select').val()},function (movements) {
            window.myMap.geoObjects.removeAll()
            for (let index = 0; index < movements.length; index++) {
                if (movements[index]) {
                    id = movements[index].id
                    if (id) {
                        e = movements[index];
                        // console.log(e)

                        if (window.dots) {
                            if (window.dots[id]) {
                                window.dots[id].options.set('iconColor', "#177BC9") 
                                $('#sortable').append(`<li class="item alert alert-custom input-group mb-3 item-active"  data-type="${e.data}" data-num="${e.index}" id="${id}_temp" role="alert"><div class="remove" onclick="removeRoute(this)"></div>${$(`#${id}`).html()}</li>`)       
                            }
                        }
                    }
                }    
            }
        })
    }
    window.setTmpMovements = function () {
        if ($('select').val()!='Не закреплён') {
            let elems = $("li.item");
            items = [];
            cureer = $('select').val()
            for (let index = 0; index < elems.length; index++) {
                const element = elems[index];
                let type = $(element).data('type');
                // console.log(type)
                if (type == 'Перемещение') {
                    t = 'movement'
                } else {
                    t = 'sale'
                }
                items.push({"id":`${$(element).attr('id').replace('_temp','')}`,"number":`${$(element).data('num').replace('P','')}`,"cureer":`${cureer}`,"type":`${t}`})
            }
            $.post('/ajax/admin/attention.php?methodName=createTmpMovements',{"methodName":"createTmpMovements","items":items,"cureer":cureer},function(data){
                // console.log(data)
            })
        } else {
            $('select').css('background-color','#f8c291')
            $( "#dialog" ).dialog({
                "width":'730px',
            });
        }
    }

// Load reserved items
    window.itemsLoadReserved = function(callback=false){
        let managers = []
        $.post('/ajax/admin/attention.php?methodName=getReserved',{"methodName":"getReserved"},function (movements) {
            $('.orig').remove()
            for (let index = 0; index < movements.length; index++) {
                const movement = movements[index];
                var cont = '';
                if (!managers.includes(movement.manager)) {
                    managers.push(movement.manager)
                }
                if (movement.type == 'Перемещение') {
                    var move_to = movement.from
                } else {
                    var move_to = movement.to
                    movement.store = false
                }
                
                
                if (movement.index) {
                    cont += `<tr>
                        <td class="attention-content-title">Номер</td>
                        <td>${movement.index}</td>
                    </tr>`
                }

                if (movement.deal) {
                    if(movement.deal != "P00000000"){    
                        cont += `<tr>
                            <td class="attention-content-title">К заказу</td>
                            <td>${movement.deal}</td>
                        </tr>`
                    }
                }
                if (movement.manager) {
                    cont += `<tr>
                        <td class="attention-content-title">Менеджер</td>
                        <td>${movement.manager}</td>
                    </tr>`
                }
                if (movement.store) {
                    // console.log(movement.store)
                    cont += `<tr>
                        <td class="attention-content-title">Cклад</td>
                        <td>${movement.store}</td>
                    </tr>`
                }
                if (movement.provider) {
                    cont += `<tr>
                        <td class="attention-content-title">Транспортная</td>
                        <td>${movement.provider}</td>
                    </tr>`
                }
                if (movement.client_name) {
                    cont += `<tr>
                        <td class="attention-content-title">Клиент</td>
                        <td>${movement.client_name} ${movement.client_phone}</td>
                    </tr>`
                }
                if (movement.info) {
                    cont += `<tr>
                        <td class="attention-content-title">Инфо.</td>
                        <td>${movement.info}</td>
                    </tr>`
                }
                if (movement.info2) {
                    cont += `<tr>
                        <td class="attention-content-title">Доп инф.</td>
                        <td>${movement.info2}</td>
                    </tr>`
                }
                
                if (movement.from) {
                    cont += `<tr>
                        <td class="attention-content-title">Откуда</td>
                        <td>${movement.from}</td>
                    </tr>`
                }
                
                var items = '';
                for (let index = 0; index < movement.items.length; index++) {
                    const element = movement.items[index];
                    items+=`${element}<br>`
                }

                var today = new Date();
                var date = today.getDate()+'.'+(today.getMonth()+1)+'.'+today.getFullYear();
                
                dd = date.split(".");
                var dd = new Date( dd[2], dd[1], dd[0]).getTime()/1000;

                myDate = movement.item_date.split(".");
                var newDate = new Date( myDate[2], myDate[1], myDate[0]).getTime()/1000;

                var itemAlert = '';
                // console.log(newDate, dd)

                if (parseInt(dd)==parseInt(newDate)) {
                    var itemAlert = '<span class="badge badge-success blink_me">Сегодня!</span>'
                } 
                if (dd>newDate) {
                    var itemAlert = '<span class="badge badge-danger blink_me">Просрочено!</span>'
                }

                $('.main').append(
                    `<div id="${movement.id}" data-date="${movement.item_date}" data-type="${movement.type}" data-num="${movement.index}" data-address="${move_to}" class="item_reserved alert alert-custom input-group mb-3 item-active orig" role="alert">
                        <div  style="width:100%">
                        <span class="badge badge-warning">${movement.type}</span>
                        <span class="badge">${movement.item_date}</span>
                        ${itemAlert}
                        </div>
                        <table style="font-size:10px">${cont}</table>
                        <div class="alert alert-danger attention-items">
                            ${items}
                        </div>
                    </div>`
                )
                
            }
            if (callback) {
                callback.call()
                callback.call()
            }
            managers.forEach(manager => {
                console.log(manager)
            });
        })
    }

// Load items and dots
    window.mapDotsInit = function () {
        myMap.geoObjects.removeAll()
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

        window.dots = {};
        for (let index = 0; index < $('.orig').length; index++) {
            let e = $('.orig')[index];
            type = {
                'Доставка':'islands#greenStretchyIcon',
                'Перемещение':'islands#darkOrangeDeliveryIcon',
                'Отправка':'islands#redAirportCircleIcon'
            }
            let content;
            if ($(e).data('type')=='Доставка') {
                content = `${$(e).data('num')}`;
            }else{
                content = '';
            }
            
            window.myGeocoder = ymaps.geocode('Санкт-Петербург '+$(e).data('address'));
            myGeocoder.then(
                function (res) {
                    myMap.geoObjects.add(
                        window.dots[$(e).attr('id')] = new ymaps.Placemark(res.geoObjects.get(0).geometry.getCoordinates(),
                            { iconContent:  content},
                            { 
                                preset: type[$(e).data('type')],
                            }
                        )
                        
                    );
                    dots[$(e).attr('id')].events.add("click", function () {appendRoute($(`#${$(e).attr('id')}`))});
                    dots[$(e).attr('id')].events.add("contextmenu", function () {
                        if (!window.myMap.balloon.isOpen()) {
                            balloon = myMap.balloon.open(dots[$(e).attr('id')].geometry.getCoordinates(), $(e).html());
                        }
                    });
                },
                function (err) {
                    alert("Ошибка");
                }
            );  
        }
    }
    window.itemsLoad = function(callback=false){
        $.post('/ajax/admin/attention.php?methodName=getMovements',{"methodName":"getMovements"},function (movements) {
            $('.orig').remove()
            
            for (let index = 0; index < movements.length; index++) {
                const movement = movements[index];
                var cont = '';

                if (movement.type=='Перемещение') {
                    var move_to = movement.from
                } else {
                    var move_to = movement.to
                }

                if (movement.index) {
                    cont += `<tr>
                        <td class="attention-content-title">Номер</td>
                        <td>${movement.index}</td>
                    </tr>`
                }
                if (movement.deal) {
                    if(movement.deal != "P00000000"){    
                        cont += `<tr>
                            <td class="attention-content-title">К заказу</td>
                            <td>${movement.deal}</td>
                        </tr>`
                    }
                }
                if (movement.manager) {
                    cont += `<tr>
                        <td class="attention-content-title">Менеджер</td>
                        <td>${movement.manager}</td>
                    </tr>`
                }
                if (movement.from) {
                    cont += `<tr>
                        <td class="attention-content-title">Откуда</td>
                        <td>${movement.from}</td>
                    </tr>`
                }
                if (movement.store && movement.type!='Перемещение') {
                    cont += `<tr>
                        <td class="attention-content-title">Склад</td>
                        <td>${movement.store}</td>
                    </tr>`
                }
                if (movement.provider) {
                    cont += `<tr>
                        <td class="attention-content-title">Транспортная</td>
                        <td>${movement.provider}</td>
                    </tr>`
                }
                if (movement.client_name) {
                    cont += `<tr>
                        <td class="attention-content-title">Клиент</td>
                        <td>${movement.client_name} ${movement.client_phone}</td>
                    </tr>`
                }
                if (movement.info) {
                    cont += `<tr>
                        <td class="attention-content-title">Инфо.</td>
                        <td>${movement.info}</td>
                    </tr>`
                }
                if (movement.info2) {
                    cont += `<tr>
                        <td class="attention-content-title">Доп инф.</td>
                        <td>${movement.info2}</td>
                    </tr>`
                }
                
                if (movement.to) {
                    cont += `<tr>
                        <td class="attention-content-title">Куда</td>
                        <td>${movement.to}</td>
                    </tr>`
                }
                items = '';
                
                for (let index = 0; index < movement.items.length; index++) {
                    const element = movement.items[index];
                    items+=`${element}<br>`
                }

                $('.main').append(
                    `<div id="${movement.id}" data-date="${movement.item_date}" data-type="${movement.type}" data-num="${movement.index}" data-address="${move_to}" onclick="appendRoute(this);" class="item alert alert-custom input-group mb-3 item-active orig" role="alert">
                        <div  style="width:100%">
                            <span class="badge badge-warning">${movement.type}</span>
                            <span class="badge">${movement.item_date}</span>
                        </div>
                        <table style="font-size:10px">${cont}</table>
                        <div class="alert alert-danger attention-items">
                            ${items}
                        </div>
                    </div>`
                )
                
            }
            if (callback) {
                callback.call()
                callback.call()
            }
        })
    }

    $( "#datepicker" ).datepicker({
        showOn: "button",
        buttonImage: "https://img.icons8.com/ios-filled/18/000000/calendar--v1.png",
        buttonImageOnly: true,
        buttonText: "",
        dateFormat: 'dd.mm.yy'
    });

    $(document).keyup(function(e) {
        if (e.key === "Escape") { // escape key maps to keycode `27`
            $("#map",parent.document).css("left","-100%")
        }
    });

    window.printRoute = function (){
        let styles = $('head').html()
        let content = $('#sortable>.item')
        let cureer = $( "select option:selected" ).text()
        var MainWindow = window.open('', '', 'height=1920,width=1080');
        MainWindow.document.write('<html><head><title>Маршрут</title>');
        MainWindow.document.write(styles);
        MainWindow.document.write('</head><body>');
        let full_content = ''
        for (let index = 0; index < content.length; index++) {
            const element = $(content[index]).prepend(`<h5>${index+1}</h5>`).html();
            full_content=full_content+`<div class="item alert alert-custom input-group mb-3 item-active" role="alert">${element}</div>`
        }
        MainWindow.document.write(`<div id="cureer">${cureer}</div>`+full_content);
        MainWindow.document.write('</body></html>');
        setTimeout(function () {
            MainWindow.print();
        }, 2000)
        $('h5').remove()
        return true;
    }

    $( "#sortable" ).sortable();
    window.itemsLoad()
})

