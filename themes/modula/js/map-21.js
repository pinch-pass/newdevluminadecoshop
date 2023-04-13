ymaps.ready(function () {
    var myMap = new ymaps.Map('map', {
            center: [55.751574, 37.573856],
            zoom: 8
        }, {
            searchControlProvider: 'yandex#search'
        }),

        // Создаём макет содержимого.
        MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
            '<div style="color: #FFFFFF; font-weight: bold;">$[properties.iconContent]</div>'
        ),

        myPlacemarkWith = new ymaps.Placemark([55.827865, 37.489714], {
            hintContent: 'Собственный значок метки с контентом',
            balloonContent: '<div class=\"stores__info\" style=\"width:97%;margin:10px;margin-right:0px;padding:0;\"><p class=\"stores__info-name\"> <img src=\"https://luminadecoshop.ru/themes/modula/img/store-metro-3.svg\" alt=\"\"><span>Войковская</span></p><p class=\"stores__info-adress\">Ленинградское шоссе, д.25, 4-й этаж, павильон 4F06 (ТЦ Family Room)</p><p class=\"stores__info-telephone\">Телефон:<a href=\"tel:+74954775116\">+7 (495) 477-51-16</a></p><p class=\"stores__info-worktimme\">Время работы:<span> Пн-Вс - 10:00 - 22:00</p><div class=\"stores__info-services\"><img src=\"https://luminadecoshop.ru/themes/modula/img/1.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/2.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/3.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/4.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/5.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/6.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/7.svg\" width=\"45px\"></div></div>',}, {
            // Опции.
            // Необходимо указать данный тип макета.
            iconLayout: 'default#image',
            // Своё изображение иконки метки.
            iconImageHref: '../img/myIcon.svg',
            // Размеры метки.
            iconImageSize: [50, 66],
            // Смещение левого верхнего угла иконки относительно
            // её "ножки" (точки привязки).
            iconImageOffset: [-26, -56]
        })



    myMap.geoObjects
        .add(myPlacemarkWith)

});