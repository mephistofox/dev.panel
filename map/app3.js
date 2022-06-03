let filters = {};
let items = {};
let routes = {};
let route_ids = [];
let dots = {};

$(document).keyup(function(e) {
    if (e.key === "Escape") { // escape key maps to keycode `27`
        $("#map",parent.document).css("left","-100%")
    }
});

ymaps.ready(function () {
    $.post('/ajax/admin/attention.php?methodName=getCureers',{"methodName":"getCureers"},function (cureers) {
        var cureers = JSON.parse(cureers);
        for (let index = 0; index < cureers.length; index++) {
            const cureer = cureers[index];
            $('select').append(`<option value="${cureer.id}">${cureer.name}</option>`)            
        }
    })

    window.myMap = new ymaps.Map("map", {
        center: [59.898756, 30.253938],
        zoom: 10
    }, {
        searchControlProvider: "yandex#search"
    })

// init map
    window.mapInit = function () {
        window.getPointData = function (item_type,number,baloon,content,id) {
            return {
                balloonContentBody: baloon,
                balloonContentFooter: `<button onclick="window.appendRoute($('#${id}'))">Добавить в маршрут</button>`,
                clusterCaption: item_type+' <strong>'+number+'</strong>',
                iconContent:content
            };
        }
    }

// render item
    window.renderItem = function (item,key,value) {
        value = (key=='items') ? value.join('<br>'):value
        value = (key=='client') ? value.replaceAll('\+',' +'):value
        value = (key=='deal') ? value.replaceAll('P00000000',''):value
        item = item.replaceAll(`{{movement.${key}}}`,value)
        return (item)
    }

// create map marker
    window.createMarker = function (item) {
        let dot_type = {'Доставка':'islands#greenStretchyIcon','Перемещение':'islands#darkOrangeDeliveryIcon','Отправка':'islands#redAirportCircleIcon'}
        let item_address = (item.data=='movement') ? item.from : item.to;
        let template = $('#template-route').html()
        Object.entries(item).filter(([key, value]) => template=renderItem(template,key,value));

        item_data = {
            'id': item.id,
            'number': item.index,
            'type': item.type,
            'dot_type': dot_type[item.type],
            'address':item_address,
            'template':template
        }

        let coords = [item.coords.split(' ')[1],item.coords.split(' ')[0]]
        dots[item.id] = new ymaps.Placemark(coords,window.getPointData(item.type,item.index,$(`#temp *[data-id="${item.id}"]`).html(),item.index,item.id),{preset:dot_type[item.type]})
        dots[item.id].events.add("contextmenu", function () {window.appendRoute($(`#${item.id}`))})
    }

// load items by filters
    window.loadItems = function(callback=false) { 
        window.clusterer = new ymaps.Clusterer(
            {
                preset: 'islands#invertedBlackClusterIcons',
                groupByCoordinates: true,
                gridSize: 80,
                clusterDisableClickZoom: true,
                clusterHideIconOnBalloonOpen: false,
                geoObjectHideIconOnBalloonOpen: false
            }
        )
        myMap.geoObjects.removeAll()
        $('.item').hide()
        $('.item_reserved').hide()
        $('.attention-col').find(".item:hidden").remove()
        $('.attention-col').find(".item_reserved:hidden").remove()
        $.post('/ajax/admin/attention.php?methodName=getCureers',{"methodName":"getCureers"},function (cureers) {
            var cureers = JSON.parse(cureers);
            for (let index = 0; index < cureers.length; index++) {
                const cureer = cureers[index];
                routes[cureer.id] = []
                $.post('/ajax/admin/attention.php?methodName=getTmpMovements',{"methodName":"getTmpMovements","cureer":cureer.id}).done(function(movements){      
                    for (let index = 0; index < movements.length; index++) {
                        if (movements[index]) {
                            if (!routes[cureer.id].includes(movements[index])) {
                                routes[cureer.id].push(movements[index])
                            }
                            if (!route_ids.includes(movements[index].id)) {
                                route_ids.push(movements[index].id)
                            }
                        }
                    }
                })
            }
        }).done(function () {
            $.get('/ajax/admin/attention.php?methodName=getMovements',{"methodName":"getMovements"},function (movements) {
                for (let index = 0; index < movements.length; index++) {
                    const movement = movements[index];
                    var item = $('#template-item').html();
                    items[movement.id] = movement
                    window.createMarker(items[movement.id])
                    Object.entries(items[movement.id]).filter(([key, value]) => item=renderItem(item,key,value));
                    $('#temp').append(item)
                    if (!route_ids.includes(movement.id)) {
                        if (!$('.main #'+item.id).length) {
                            $('.main').append(item)
                        }
                    }
                }
                $('.attention-col').find(".item:hidden").remove()
                $('.main').find('td:empty').parent().remove()
                $('#temp').find('td:empty').parent().remove()
            }).done(function () {
                setTimeout(function () {
                    for (const key in dots) {
                        const route = items[key];
                        const dot = dots[key];
                        if (!route_ids.includes(key)) {
                            dot.options.unset('iconColor')
                            if ($('#datepicker').val() != 'today') {
                                if (route.item_date == $('#datepicker').val()) {
                                    dot.events.add("contextmenu", function () {window.appendRoute($(`#${item.id}`))})
                                    window.clusterer.add(dot)
                                } else {
                                    $(`.item[data-id="${key}"]`).hide()
                                }
                            }else{
                                window.clusterer.add(dot)
                                $(`.item[data-id="${key}"]`).show()
                            }
                        }
                        if ($('select').val()!='Не закреплён') {
                            for (let index = 0; index < routes[$('select').val()].length; index++) {
                                const element = routes[$('select').val()][index];
                                if (element.id == key) {
                                    dot.options.set('iconColor','#177BC9')
                                    dot.events.add("contextmenu", function () {window.appendRoute($(`#${item.id}`))})
                                    window.clusterer.add(dot)
                                }
                            }
                        }
                    }
                    window.myMap.geoObjects.add(clusterer)
                    $('select').val()!='Не закреплён'?window.loadRoute():false
                },1000)
            })
        })
    }

// load routes
    window.loadRoute = function() {
        $('#sortable').html('')
        let cureer_id = $('select').val()
        $('#sortable').html('')
        for (let index = 0; index < routes[cureer_id].length; index++) {
            const route = routes[cureer_id][index];
            let item = $('#template-route').html()
            Object.entries(route).filter(([key, value]) => item=renderItem(item,key,value));
            $('#sortable').append(item)
        }
        $('#sortable').find('td:empty').parent().remove()       
    }

// date sort
    window.dateSort = function() {
        myMap.geoObjects.removeAll()
            for (const key in items) {
                let route = items[key]
                let dot = dots[key]
                if (route.item_date != $('#datepicker').val()) {
                    window.clusterer.remove(dot)
                    $(`.item[data-id="${route.id}"]`).hide()
                }
                $('.attention-col').find(".item:hidden").remove()
            }
        myMap.geoObjects.add(clusterer)
    }

    window.appendRoute = function (e) {
        let id = $(e).data('id');
        if (id) {
            if ($(`#${$(e).attr('id')}_temp`).length<1) {
                $('#sortable').append(`<li class="route alert alert-custom input-group mb-3 item-active" data-type="${$(e).data('type')}" data-number="${$(e).data('number')}" data-id="${$(e).attr('id')}" id="${$(e).attr('id')}_temp"><div class="remove" onclick="removeRoute(this)"></div>${$(e).html()}</li>`)   
                $(`.item[data-id="${id}"]`).hide()
                if (dots[id]) {
                    dots[id].options.set('iconColor', "#177BC9") 
                }
            } else {
                $(`#${id}_temp`).remove();
                $(`.item[data-id="${id}"]`).show()
                dots[id].options.unset('iconColor')
            }
        }
    }
    window.removeRoute = function (e) {
        appendRoute($('#'+$(e).parent().attr('id').replace('_temp','')));
        $(e).parent().remove();
    }
    window.addTask = function () {
        let address = $('.task-address').val()
        let description = $('#description').val()
        let tmp = $('#template-task').html().replace('{{movement.address}}',address).replace('{{movement.description}}',description)
        $('#sortable').append(tmp)
    }

// set tmp movements 
    window.setTmpMovements = function () {
        if ($('select').val()!='Не закреплён') {
            let items = [];
            let cureer = $('select').val()
            for (let index = 0; index < $(".route").length; index++) {
                const element = $($(".route")[index]);
                let number = element.data('number').replace('P','');
                let type = element.data('type');
                let id = element.data('id');
                items.push({"id":id, "number":number, "cureer":cureer, "type":type})
            }
            $.post('/ajax/admin/attention.php?methodName=createTmpMovements',{"methodName":"createTmpMovements","items":items,"cureer":cureer},function(data){

            })
        } else {
            $('select').css('background-color','#f8c291')
            $( "#dialog" ).dialog({
                "width":'730px',
            });
        }
    }

    window.itemsLoadReserved = function(callback=false){
        let managers = []
        $('#cureers').hide()
        $('.main').append('<select class="custom-select" id="managers" onchange="window.sortReserved()"><option default value="Не закреплён" style="visibility: hidden;">Выбрать менеджера</option></select>')
        $.post('/ajax/admin/attention.php?methodName=getReserved',{"methodName":"getReserved"},function (movements) {
            $('.orig').remove()
            for (let index = 0; index < movements.length; index++) {
                const movement = movements[index];
                var cont = '';
                
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
                        <td class="manager-name">${movement.manager}</td>
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
        }).done(function (movements) {
            movements.forEach(movement => {
                if (!managers.includes(movement.manager)) {
                    managers.push(movement.manager)
                }
            })
        }).done(function(){
            managers.forEach(manager => {
                $('#managers').append(`<option value="${manager}">${manager}</option>`)
            })
        })
        
    }

    window.sortReserved = function () {
        $('.item_reserved').show()
        for (let index = 0; index < $('.item_reserved').length; index++) {
            const item = $($($('.item_reserved')[index]).find('.manager-name')).text();
            if (item != $('#managers').val()) {
                $($('.item_reserved')[index]).hide()
            }
        }
    }

    $( "#datepicker" ).datepicker({
        showOn: "button",
        buttonText: "📅",
        dateFormat: 'dd.mm.yy'
    });

    window.setCureer = function () {
        let ims = [];
        
        for (let index = 0; index < $('li.route').length; index++) {
            let item = items[$($('li.route')[index]).data('id')]
            if (item) {
                let number = item.index
                let table = item.data
                let cureer = $( "select option:selected" ).text();
                let cid = $('select').val();
                ims.push({'number':number.replace('P',''),'cureer':cureer,'cureer_id':cid,'table':table})     
            }     
        }
        $.post('/ajax/admin/attention.php?methodName=setCureers',{"methodName":"setCureers","items":ims},function (d) {
            
        })
        $('#sortable').html('')
    }

    window.printRoute = function (){
        let styles = $('head').html()
        let content = $('#sortable>.route')
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

    window.createTireLoadList = function () {
        let styles = $('head').html()
        var MainWindow = window.open('', '', 'height=1920,width=1080');
        MainWindow.document.write('<html><head><title>Маршрут</title>');
        MainWindow.document.write(styles);
        MainWindow.document.write('</head><body>');
        let full_content = ''
        for (let index = 0; index < $('.route').length; index++) {
            let route_number = $($('.route')[index]).data('number')
            const element = $($($('.route')[index]).find('.attention-items')).html().split('<br>');
            if (!$($('.route')[index]).data('base')) {
                for (let index = 0; index < element.length; index++) {
                    const el = element[index];
                    if (el.search('Доставка')<0) {
                        full_content=full_content+`<div class="item alert alert-custom input-group mb-3 item-active" role="alert"><b>${route_number}</b>    ${el.replace(' (','<br>(')}</div>` 
                    }
                }
            }
        }
        MainWindow.document.write(full_content);  
        MainWindow.document.write('</body></html>');
        setTimeout(function () {
            MainWindow.print();
        }, 2000)
        $('h5').remove()
        return true;
    }

    $( "#sortable" ).sortable();
    window.mapInit()
    window.loadItems()
})