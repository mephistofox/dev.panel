var add;
ymaps.ready(function () {
    var myMap = new ymaps.Map("map", {
        center: [59.898756, 30.253938],
        zoom: 10
    }, {
        searchControlProvider: 'yandex#search'
    })
    var myGeocoder = ymaps.geocode("г Санкт-Петербург, ул Калинина, д 5");
    myGeocoder.then(
        function (res) {
            myMap.geoObjects.add(
                new ymaps.Placemark(res.geoObjects.get(0).geometry.getCoordinates(),
                    { iconContent: 'База' },
                    { preset: 'islands#greenStretchyIcon' }
                )
            );
            // res.geoObjects.get(0).geometry.getCoordinates()
        },
        function (err) {
            alert('Ошибка');
        }
    );
    
    window.add = function () {
        var myGeocoder = ymaps.geocode($('#address').val());
        myGeocoder.then(
            function (res) {
                myMap.geoObjects.add(
                    new ymaps.Placemark(res.geoObjects.get(0).geometry.getCoordinates(),
                        { iconContent: 'asdas' },
                        { preset: 'islands#greenStretchyIcon' },
                        {balloonContent: 'цвет <strong>воды пляжа бонди</strong>'}
                    )
                );
            },
            function (err) {
                alert('Ошибка');
            }
        );
    }
})