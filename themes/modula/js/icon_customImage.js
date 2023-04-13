ymaps.ready(function () {
    var myMap = new ymaps.Map('map', {
            center: [55.751574, 37.573856],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        }),

        // Создаём макет содержимого.
        MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
            '<div style="color: #FFFFFF; font-weight: bold;">$[properties.iconContent]</div>'
        ),

        myPlacemark = new ymaps.Placemark([55.729641, 37.734023], {
            hintContent: 'Собственный значок метки',
            balloonContent: '<div class=\"stores__info\" style=\"width:97%;margin:10px;margin-right:0px;padding:0;\"><p class=\"stores__info-name\"> <img src=\"https://luminadecoshop.ru/themes/modula/img/store-metro-1.svg\" alt=\"\"><span>Нижегородская</span></p><p class=\"stores__info-adress\">Рязанский проспект, д.2, к.3, 1-й этаж, павильон 111 (ТЦ Декоратор)</p><p class=\"stores__info-telephone\">Телефон:<a href=\"tel:+74954775047\">+7 (495) 477-50-47</a></p><p class=\"stores__info-worktimme\">Время работы:<span> Пн-Вс - 10:00 - 22:00</p><div class=\"stores__info-services\" style=\"margin-bottom:10px;\"><img src=\"https://luminadecoshop.ru/themes/modula/img/1.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/2.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/3.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/4.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/5.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/6.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/7.svg\" width=\"45px\"></div><div class=\"stores__info-buttons\" style=\"flex-direction:column;\"> <a href=\"https://luminadecoshop.ru/content/19-ryazanskiy\"class=\"more\" style=\"margin-bottom:20px;justify-content:center;\">Подробнее<img src=\"https://luminadecoshop.ru/themes/modula/img/store-arrow.svg\" alt=\"\"></a><a href=\"https://yandex.eu/maps/213/moscow/?ll=37.592687%2C55.689390&mode=routes&rtext=~55.729397%2C37.734524&rtt=auto&ruri=~ymapsbm1%3A%2F%2Forg%3Foid%3D140713522724&z=12\" class=\"route\" style=\"justify-content:center;\" ><img src=\"https://luminadecoshop.ru/themes/modula/img/placeholder.svg\" alt=\"\"><span>Построить маршрут</span></p></div></div>'}, {
            // Опции.
            // Необходимо указать данный тип макета.
            iconLayout: 'default#image',
            // Своё изображение иконки метки.
            iconImageHref: 'img/myIcon.svg',
            // Размеры метки.
            iconImageSize: [50, 66],
            // Смещение левого верхнего угла иконки относительно
            // её "ножки" (точки привязки).
            iconImageOffset: [-26, -56]
        }),

        myPlacemarkWithContent = new ymaps.Placemark([55.629523, 37.425589], {
            hintContent: 'Собственный значок метки с контентом',
            balloonContent: '<div class=\"stores__info\" style=\"width:97%;margin:10px;margin-right:0px;padding:0;\"><p class=\"stores__info-name\"> <img src=\"https://luminadecoshop.ru/themes/modula/img/store-metro-2.svg\" alt=\"\"><span>Румянцево</span></p><p class=\"stores__info-adress\">Центральная ул., 70Б, д. Румянцево, этаж 3</p><p class=\"stores__info-telephone\">Телефон:<a href=\"tel:+74954775105\">+7 (495) 477-51-05</a></p><p class=\"stores__info-worktimme\">Время работы:<span> Пн-Вс - 10:00 - 22:00</p><div class=\"stores__info-services\" style=\"margin-bottom:20px;\"><img src=\"https://luminadecoshop.ru/themes/modula/img/1.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/2.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/3.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/4.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/5.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/6.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/7.svg\" width=\"45px\"></div><div class=\"stores__info-buttons\" style=\"flex-direction:column;\"> <a href=\"https://luminadecoshop.ru/content/20-kievskiy\"class=\"more\" style=\"margin-bottom:20px;justify-content:center;\">Подробнее<img src=\"https://luminadecoshop.ru/themes/modula/img/store-arrow.svg\" alt=\"\"></a><a href=\"https://yandex.eu/maps/213/moscow/?indoorLevel=1&ll=37.423976%2C55.629605&mode=routes&rtext=~55.629745%2C37.423084&rtt=auto&ruri=~ymapsbm1%3A%2F%2Forg%3Foid%3D103670552412&z=12\" class=\"route\" style=\"justify-content:center;\" ><img src=\"https://luminadecoshop.ru/themes/modula/img/placeholder.svg\" alt=\"\"><span>Построить маршрут</span></p></div></div>',

        }, {
            // Опции.
            // Необходимо указать данный тип макета.
            iconLayout: 'default#image',
            // Своё изображение иконки метки.
            iconImageHref: 'img/myIcon.svg',
            // Размеры метки.
            iconImageSize: [50, 66],
            // Смещение левого верхнего угла иконки относительно
            // её "ножки" (точки привязки).
            iconImageOffset: [-26, -56]
        }),

        myPlacemarkWith = new ymaps.Placemark([55.827865, 37.489714], {
            hintContent: 'Собственный значок метки с контентом',
            balloonContent: '<div class=\"stores__info\" style=\"width:97%;margin:10px;margin-right:0px;padding:0;\"><p class=\"stores__info-name\"> <img src=\"https://luminadecoshop.ru/themes/modula/img/store-metro-3.svg\" alt=\"\"><span>Войковская</span></p><p class=\"stores__info-adress\">Ленинградское шоссе, д.25, 4-й этаж, павильон 4F06 (ТЦ Family Room)</p><p class=\"stores__info-telephone\">Телефон:<a href=\"tel:+74954775116\">+7 (495) 477-51-16</a></p><p class=\"stores__info-worktimme\">Время работы:<span> Пн-Вс - 10:00 - 22:00</p><div class=\"stores__info-services\" style=\"margin-bottom:20px;\"><img src=\"https://luminadecoshop.ru/themes/modula/img/1.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/2.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/3.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/4.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/5.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/6.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/7.svg\" width=\"45px\"></div><div class=\"stores__info-buttons\" style=\"flex-direction:column;\"> <a href=\"https://luminadecoshop.ru/content/21-leningradskoe\"class=\"more\" style=\"margin-bottom:20px;justify-content:center;\">Подробнее<img src=\"https://luminadecoshop.ru/themes/modula/img/store-arrow.svg\" alt=\"\"></a><a href=\"https://yandex.eu/maps/213/moscow/?indoorLevel=4&ll=37.489433%2C55.828040&mode=routes&rtext=~55.828145%2C37.489464&rtt=auto&ruri=~ymapsbm1%3A%2F%2Forg%3Foid%3D128712819429&z=12\" class=\"route\" style=\"justify-content:center;\" ><img src=\"https://luminadecoshop.ru/themes/modula/img/placeholder.svg\" alt=\"\"><span>Построить маршрут</span></p></div></div>',

        }, {
            // Опции.
            // Необходимо указать данный тип макета.
            iconLayout: 'default#image',
            // Своё изображение иконки метки.
            iconImageHref: 'img/myIcon.svg',
            // Размеры метки.
            iconImageSize: [50, 66],
            // Смещение левого верхнего угла иконки относительно
            // её "ножки" (точки привязки).
            iconImageOffset: [-26, -56]
        }),

        myPlacemarkContent = new ymaps.Placemark([55.658806, 37.430598], {
            hintContent: 'Собственный значок метки с контентом',
            balloonContent: '<div class=\"stores__info\" style=\"width:97%;margin:10px;margin-right:0px;padding:0;\"><p class=\"stores__info-name\"> <img src=\"https://luminadecoshop.ru/themes/modula/img/store-metro-4.svg\" alt=\"\"><span>Говорово</span></p><p class=\"stores__info-adress\">МКАД, 47-й километр, вл31с1 (ТЦ Лента)</p><p class=\"stores__info-telephone\">Телефон:<a href=\"tel:+74951509257\">+7 (495) 150-92-57</a></p><p class=\"stores__info-worktimme\">Время работы:<span> Пн-Вс - 10:00 - 22:00</p><div class=\"stores__info-services\" style=\"margin-bottom:20px;\"><img src=\"https://luminadecoshop.ru/themes/modula/img/1.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/2.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/3.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/4.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/5.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/6.svg\" width=\"45px\"><img src=\"https://luminadecoshop.ru/themes/modula/img/7.svg\" width=\"45px\"></div><div class=\"stores__info-buttons\" style=\"flex-direction:column;\"> <a href=\"https://luminadecoshop.ru/content/22-lumina-deco-na-mkad\"class=\"more\" style=\"margin-bottom:20px;justify-content:center;\">Подробнее<img src=\"https://luminadecoshop.ru/themes/modula/img/store-arrow.svg\" alt=\"\"></a><a href=\"https://yandex.eu/maps/213/moscow/?ll=37.430777%2C55.658774&mode=routes&rtext=~55.658774%2C37.430777&rtt=auto&ruri=~ymapsbm1%3A%2F%2Forg%3Foid%3D63103926822&z=12\" class=\"route\" style=\"justify-content:center;\" ><img src=\"https://luminadecoshop.ru/themes/modula/img/placeholder.svg\" alt=\"\"><span>Построить маршрут</span></p></div></div>',

        }, {
            // Опции.
            // Необходимо указать данный тип макета.
            iconLayout: 'default#image',
            // Своё изображение иконки метки.
            iconImageHref: 'img/myIcon.svg',
            // Размеры метки.
            iconImageSize: [50, 66],
            // Смещение левого верхнего угла иконки относительно
            // её "ножки" (точки привязки).
            iconImageOffset: [-26, -56]
        });



    myMap.geoObjects
        .add(myPlacemark)
        .add(myPlacemarkWith)
        .add(myPlacemarkContent)
        .add(myPlacemarkWithContent);
});