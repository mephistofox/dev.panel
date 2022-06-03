ymaps.ready(function () {

    $.post('/ajax/admin/attention.php?methodName=getCureers',{"methodName":"getCureers"},function (cureers) {
        var cureers = JSON.parse(cureers);
        for (let index = 0; index < cureers.length; index++) {
            const cureer = cureers[index];
            // console.log(cureer)
            $('select').append(`<option value="${cureer.id}">${cureer.name}</option>`)            
        }
    })

    window.myMap = new ymaps.Map("map", {
        center: [59.898756, 30.253938],
        zoom: 10
    }, {
        searchControlProvider: "yandex#search"
    })

    window.cureer_route = [];
    window.routes = []

    window.renderItem = function (item,key,value) {
        value = (key=='items') ? value.join('<br>'):value
        value = (key=='client') ? value.replaceAll('\+',' +'):value
        value = (key=='deal') ? value.replaceAll('P00000000',''):value
        item = item.replaceAll(`{{movement.${key}}}`,value)
        return item
    }

    window.appendRoute = function (e) {
        let id = $(e).data('id');
        if (id) {
            if ($(`#${$(e).attr('id')}_temp`).length<1) {
                $('#sortable').append(`<li class="route alert alert-custom input-group mb-3 item-active" data-type="${$(e).data('type')}" data-number="${$(e).data('number')}" data-id="${$(e).attr('id')}" id="${$(e).attr('id')}_temp"><div class="remove" onclick="removeRoute(this)"></div>${$(e).html()}</li>`)   
                $(`#${id}`).hide()
                if (window.dots[id]) {
                    window.dots[id].options.set('iconColor', "#177BC9") 
                }
            } else {
                $(`#${id}_temp`).remove();
                $(`#${id}`).show()
                window.dots[id].options.unset('iconColor')
            }
        }
    }
    
    window.removeRoute = function (e) {
        appendRoute($('#'+$(e).parent().attr('id').replace('_temp','')));
        $(e).parent().remove();
    }

    window.mapCreate = function(routing=false){
        myMap.geoObjects.removeAll()
        let clusterer = null
        clusterer = new ymaps.Clusterer(
            {
                preset: 'islands#invertedBlackClusterIcons',
                groupByCoordinates: true,
                gridSize: 80,
                clusterDisableClickZoom: true,
                clusterHideIconOnBalloonOpen: false,
                geoObjectHideIconOnBalloonOpen: false
            }
        ),

        getPointData = function (item_type,number,baloon,content,id) {
            return {
                balloonContentBody: baloon,
                balloonContentFooter: `<button onclick="window.appendRoute($('#${id}'))">Добавить в маршрут</button>`,
                clusterCaption: item_type+' <strong>'+number+'</strong>',
                iconContent:content
            };
        }

        window.dots = {}
        let dot_type = {'Доставка':'islands#greenStretchyIcon','Перемещение':'islands#darkOrangeDeliveryIcon','Отправка':'islands#redAirportCircleIcon'}
        window.myMap.geoObjects.remove(clusterer)
        myGeocoder = ymaps.geocode("г Санкт-Петербург, ул Калинина, д 5");
        window.myGeocoder.then( function (res) {myMap.geoObjects.add(new ymaps.Placemark(res.geoObjects.get(0).geometry.getCoordinates(),{iconContent:"База"},{preset:"islands#homeCircleIcon",iconColor: "#3b5998"}))})
        let elems = ['.orig','.route']
        for (let i = 0; i < elems.length; i++) {
            const e = elems[i];
            for (let index = 0; index < $(e).length; index++) {
                let id = $($(e)[index]).data('id')
                let type = $($(e)[index]).data('type')
                let number = $($(e)[index]).data('number')
                let item_type = $($(e)[index]).data('itemtype')
                let content = (type=='sale') ? number : ''
                let item_address = (type=='movement') ? $($(e)[index]).data('from') : $($(e)[index]).data('to');
                _ = (dots.hasOwnProperty(id)) ? clusterer.remove(dots) : false
                window.myGeocoder = ymaps.geocode('Санкт-Петербург, '+item_address);
                window.myGeocoder.then( function (res) {
                    if (!dots.hasOwnProperty(id)) {
                        window.dots[id] = new ymaps.Placemark(res.geoObjects.get(0).geometry.getCoordinates(),getPointData(item_type,number,$($(e)[index],content).html(),number,id),{preset:dot_type[item_type]})
                        window.dots[id].events.add("contextmenu", function () {window.appendRoute(`${id}`)})
                        clusterer.add(dots[id]);
                    }
                }, function (err) {
                    alert("Ошибка");
                });
            } 
        }
        window.myMap.geoObjects.add(clusterer);
    }
    
    window.itemsLoad = function(){
        $.post('/ajax/admin/attention.php?methodName=getMovements',{"methodName":"getMovements"},function (movements) {
            for (let index = 0; index < movements.length; index++) {
                const movement = movements[index];
                var item = $('#template-item').html();
                Object.entries(movement).filter(([key, value]) => item=renderItem(item,key,value));
                $('.main').append(item)
            }
            $('.main').find('td:empty').parent().remove()
        }).done(function() {
            selected = $('#datepicker').val();
            for (let index = 0; index < $('.item').length; index++) {
                let item = $($('.item')[index])
                let item_date = $($($('.item').children('.item-data').children('.item-date'))[index]).text();
                let item_timestamp = new Date(item_date.split('.').reverse().join("/")).getTime()/1000;
                let selected_date = new Date(selected.split('.').reverse().join("/")).getTime()/1000;
                if ($('#datepicker').val() != 'today') {
                    if (item_timestamp!==selected_date) {
                        item.hide()
                    }
                }
                if (window.routes.includes(item.attr('id'))) {
                    console.log(item.attr('id'),item.data('number'))
                    item.hide()
                }
            }
            $('.attention-col').find(".item:hidden").remove()
            window.mapCreate()
        })
    }

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
                console.log(items)
                console.log(data)
            })
        } else {
            $('select').css('background-color','#f8c291')
            $( "#dialog" ).dialog({"width":'730px'});
        }
    }

    window.loadTmpMovements = function(){
        $('#sortable').html('')
        $.post('/ajax/admin/attention.php?methodName=getTmpMovements',{"methodName":"getTmpMovements"}).done(function(movements){      
            for (let index = 0; index < movements.length; index++) {
                let movement = movements[index];
                let id = movement.id
                window.routes.push(id)
                if (window.dots) {
                    if (Object.keys(window.dots).includes(id)) {
                        window.dots[id].options.unset('iconColor')
                    }  
                }
            }
        })
        if ($('select').val() && $('select').val()!=='Не закреплён') {
            $.post('/ajax/admin/attention.php?methodName=getTmpMovements',{"methodName":"getTmpMovements","cureer":$('select').val()}).done(function(movements){
                for (let index = 0; index < movements.length; index++) {
                    var item = $('#template-route').html();
                    let movement = movements[index];
                    Object.entries(movement).filter(([key, value]) => item=renderItem(item,key,value));
                    $('#sortable').append(item.replace())
                    window.myMap.geoObjects.add(window.dots[movement.id])
                    setTimeout(function () {
                        window.dots[movement.id].options.set('iconColor','#177BC9')
                    },1000)
                }
                $('#sortable').find('td:empty').parent().remove()
            })
        }
        
    }

    $( "#datepicker" ).datepicker({
        showOn: "button",
        buttonText: "📅",
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
    window.loadTmpMovements()
    window.itemsLoad()
})