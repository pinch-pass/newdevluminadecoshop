/**
 * @author [JSox.ru]
 * @email [admin@jsox.ru]
 * @create date 2020-08-14 20:03:13
 * @modify date 2020-08-14 20:03:13
 * @desc [description]
 */

const jsox_many_works = (jmw = {
    modulePath: '/modules/jsox_manyworks/',
    init: () => {
        jmw.onStart();
        jmw.replaceToWebpImgs();
        jmw.showRoomsHandler();
        jmw.phoneNumbersHandler();
        jmw.addToIgitMegaMenu();
        JsoxContactBlock.init();
    },
    onStart: () => {
        $('.our_assets p').each(function () {
            var html = $(this).html();
            // $(this).html(html.replace('<br>', ''));
            $(this).html(html.replace('', ''));
        });
        var forbesImg = $('.bottom_forbes a img');
        forbesImg.hover(
            function () {
                var hover = forbesImg.data('hover_src');
                forbesImg.attr('src', hover);
            },
            function () {
                var main = forbesImg.data('main_src');
                forbesImg.attr('src', main);
            }
        );
    },
    addToIgitMegaMenu: function () {
        var lastItem = $('#iqitmegamenu-accordion ul li:last');
        var cloned = lastItem.clone();
        cloned
            .find('a')
            .text('Сотрудничество')
            .attr('style', 'padding-left: 17px !important;')
            .attr('title', 'Сотрудничество')
            .attr('href', '/content/18-sotrudichestvo');
        lastItem.after(cloned);
    },
    phoneNumbersHandler: function () {
        $('.phone_number').each(function () {
            var html = $(this).html();
            var number = html.replace(/[^+0-9]/gim, '');
            $(this).wrap(
                $('<a>', {
                    href: 'tel:' + number,
                    style: 'color: inherit !important;',
                })
            );
            console.log(number);
        });
    },
    showRoomsHandler: function () {
        var cmsSR = $('#cms .show_rooms_photo');
        if (!cmsSR.length) return;

        cmsSR.each(function (key, value) {
            key++;
            var id = 'show-room-gallery-' + key;
            var _this = $(value);

            // _this.wrap(
            //     $('<div>', {
            //         class: 'main-cat',
            //     })
            // );

            _this.addClass('owl-carousel');
            _this.addClass('owl-custom');
            _this.attr('id', id);

            for (let imgNum = 1; imgNum < 7; imgNum++) {
                var $img = $('<img>', {
                    src:
                        jmw.modulePath +
                        'images/showrooms/' +
                        key +
                        '/' +
                        imgNum +
                        '.jpg',
                });
                _this.append($img);
            }

            $('#' + id).owlCarousel({
                loop: true,
                items: 1,
                margin: 10,
                nav: true,
                navText: '',
                autoplay: false,
                dotsEach: 1,
                autoplayTimeout: 2500,
                autoplayHoverPause: true,
                itemsDesktop: false,
                itemsDesktopSmall: false,
                itemsTablet: false,
                itemsMobile: false,
                autoHeight: true,
            });
        });
    },
    replaceToWebpImgs: function () {
        console.log('canUseWebP - ' + canUseWebP());
        if (!canUseWebP()) return;
        $('.main-cat__categories .main-cat_box img').each(function () {
            var src = $(this).attr('src');
            var fileNameIndex = src.lastIndexOf('/') + 1;
            var filename = src.substr(fileNameIndex);

            var newSrc =
                jmw.modulePath + 'webp/' + filename.replace('.jpg', '.webp');

            $(this).attr('src', newSrc);
        });
    },
});

const JsoxContactBlock = (JCB = {
    secret: jsox_contact_block_secret,
    ajaxPostUrl: '?jsox_contact_block=true&secret=' + jsox_contact_block_secret,

    init: function () {
        var contact_form = JsoxContactBlock.getFormHtml();
        $('#cms.cms-18 #center_column').append(contact_form);
        JsoxContactBlock.bind();
    },
    getFormHtml: function () {
        var html = $(jsox_contact_block_html);
        return html;
    },
    bind: function () {
        $('.js-send_form_opt').click(function () {
            JsoxContactBlock.sendForm();
        });
    },
    sendForm: function () {
        var form_opt = $('.form_opt');
        var error_email = $('.error_email');
        var hover = $('.begin-cooperation-container .hover');
        hover.css('display', 'flex');

        error_email.hide();

        var data = {};
        $.each(form_opt.serializeArray(), function (_, kv) {
            data[kv.name] = kv.value;
        });

        if (!validateEmail(data.email)) {
            error_email.slideDown();
            hover.css('display', 'none');
            return;
        }

        hover.html(
            $('<h3>', {
                text: 'Отправляем данные...',
            })
        );

        $.ajax({
            url: JsoxContactBlock.ajaxPostUrl,
            type: 'POST',
            dataType: 'json',
            data: form_opt.serialize(),
            success: function (result) {
                hover.html(
                    $('<h3>', {
                        text: result.answer,
                    })
                );
            },
            error: function (xhr, resp, text) {
                console.log(xhr, resp, text);
                hover.html(
                    $('<p>', {
                        text: text,
                    })
                );
            },
        });
    },
});

function canUseWebP() {
    var elem = document.createElement('canvas');
    if (!!(elem.getContext && elem.getContext('2d')))
        return elem.toDataURL('image/webp').indexOf('data:image/webp') == 0;

    return false;
}

document.addEventListener(
    'DOMContentLoaded',
    function () {
        jsox_many_works.init();
        setTimeout(() => {
            YaGoals.init();
        }, 1000);
    },
    false
);

function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

const YaGoals = {
    init: function () {
        YaGoals.orderConfirmation();
        YaGoals.oneClickOrder();
        YaGoals.addToCart();
        YaGoals.addToFavorite();
        YaGoals.clickPhonesEmails();
    },
    _get: function (key) {
        var url = new URL(window.location.href);
        return url.searchParams.get(key);
    },
    orderConfirmation: function () {
        if ($('body#order-confirmation').length > 0) {
            var goalParams = {
                id_order: YaGoals._get('id_order') || null,
            };
            YaGoals.send('thanks_for_buy', goalParams);
        }
    },
    oneClickOrder: function () {
        $('.b1c-form .b1c-submit-area input').click(function () {
            YaGoals.send('one_click_order');
        });
    },
    addToFavorite: function () {
        $('#wishlist_button').click(function () {
            var goalParams = {
                from: 'page',
            };
            YaGoals.send('add_to_favorite', goalParams);
        });
        $('.addToWishlist').click(function () {
            var goalParams = {
                from: 'list',
            };
            YaGoals.send('add_to_favorite', goalParams);
        });
    },
    clickPhonesEmails: function () {
        $('#contact_pc .phone_top a').click(function () {
            YaGoals.send('click_phone_top_left');
        });
        $('.header-mobil__add #contact a').click(function () {
            YaGoals.send('click_phone_top_left');
        });
        $('.phone_bottom a').click(function () {
            YaGoals.send('click_phone_top_left');
        });
        $('.email_bottom a').click(function () {
            YaGoals.send('click_email_bottom');
        });
    },
    addToCart: function () {
        $('.ajax_add_to_cart_button').click(function () {
            var goalParams = {
                id_product: $(this).attr('data-id-product') || null,
                from: 'list',
            };
            YaGoals.send('add_to_cart', goalParams);
        });
        $('#add_to_cart button').click(function () {
            var goalParams = {
                from: 'page',
            };
            YaGoals.send('add_to_cart', goalParams);
        });
    },
    send: function (name, params = {}) {
        var res = yaCounter52456633.reachGoal(name, params);
        console.log('Sent Goal to Yandex: ', name, params, res);
    },
};
