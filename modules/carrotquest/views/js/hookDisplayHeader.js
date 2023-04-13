/**
 * 2017-2019 Carrot quest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author Carrot quest <support@carrotquest.io>
 * @copyright 2017-2019 Carrot quest
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

(function () {
    function Build(name, args) {
        return function () {
            window.carrotquestasync.push(name, arguments);
        }
    }

    if (typeof carrotquest === 'undefined') {
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = '//cdn.carrotquest.io/api.min.js';
        var x = document.getElementsByTagName('head')[0];
        x.appendChild(s);
        window.carrotquest = {};
        window.carrotquestasync = [];
        carrotquest.settings = {};
        var m = ['connect', 'track', 'identify', 'auth', 'open', 'onReady', 'addCallback', 'removeCallback', 'trackMessageInteraction'];
        for (var i = 0; i < m.length; i++) carrotquest[m[i]] = Build(m[i]);
    }
})();

function CarrotquestPrestashop() {
    var vm = this;

    vm.cartViewed = cartViewed;
    vm.carrotquest = window.carrotquest;
    vm.customerIdentify = customerIdentify;
    vm.carrotquestConnect = carrotquestConnect;
    vm.getCookie = getCookie;
    vm.orderCompleted = orderCompleted;
    vm.productViewed = productViewed;
    vm.setCookie = setCookie;

    init();

    function init() {
        vm.carrotquestConnect(vm.getCookie('carrotquest_api_key'));
    }

    // Просмотр корзины
    function cartViewed(products) {
        vm.carrotquest.track('$cart_viewed', {
            '$name': products.name,
            '$url': products.url,
            '$amount': products.amount,
            '$img': products.img
        });
    }

    function carrotquestConnect($key) {
        carrotquest.connect($key);
    }

    // Информация о пользователе
    function customerIdentify(customer) {
        var send = [];
        if (typeof customer.name !== 'undefined' && customer.name !== '') {
            send.push({op: 'update_or_create', key: '$name', value: customer.name});
        }
        if (typeof customer.email !== 'undefined' && customer.email !== '') {
            send.push({op: 'update_or_create', key: '$email', value: customer.email});
        }
        if (typeof customer.phone !== 'undefined' && customer.phone !== '') {
            send.push({op: 'update_or_create', key: '$phone', value: customer.phone});
        }
        vm.carrotquest.identify(send);

        if (typeof customer.user_id !== 'undefined' && customer.user_id !== '' && typeof customer.hash !== 'undefined' && customer.hash !== ''){
            vm.carrotquest.auth(customer.user_id, customer.hash);
        }
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    // Заказ оформлен
    function orderCompleted(products) {
        var send = {};
        send['$order_id'] = products.order_id;
        send['$order_id_human'] = products.order_id_human;
        send['$order_amount'] = products.order_amount;
        send['$items'] = products.items;
        vm.carrotquest.track('$order_completed', send);
        if (products.order_amount !== '') {
            var send = [
                {op: 'add', key: '$orders_count', value: 1},
                {op: 'add', key: '$revenue', value: products.order_amount},
                {op: 'update_or_create', key: '$last_payment', value: products.order_amount}
            ];
            vm.carrotquest.identify(send);
        }
    }

    // Просмотр товара
    function productViewed(product) {
        var send = {};
        if (product.name !== '') {
            send.$name = product.name;
        }
        if (product.url !== '') {
            send.$url = product.url;
        }
        if (product.amount !== '') {
            send.$amount = product.amount;
        }
        if (product.img !== '') {
            send.$img = product.img;
        }
        vm.carrotquest.track('$product_viewed', send);

        var send = [
            {op: 'union', key: '$viewed_products', value: product.name},
            {op: 'union', key: '$viewed_categories', value: product.category}
        ];
        vm.carrotquest.identify(send);
    }

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
}
