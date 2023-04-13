{*
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @category  PrestaShop
 * @category  Module
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2018 PresTeamShop
 * @license   see file: LICENSE.txt
*}

{if $total_order <= 0}
    <span id="free_order" class="alert alert-warning col-xs-12 text-center">{l s='Free Order.' mod='onepagecheckoutps'}</span>
    {*if $CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO}
        <div id="buttons_footer_review" class="row">
            <div class="end-xs col-xs-12 nopadding-xs">
                <button type="button" id="btn_place_order" class="btn btn-primary btn-lg pull-right" >
                    <i class="fa-pts fa-pts-shopping-cart fa-pts-1x"></i>
                    {l s='Checkout' mod='onepagecheckoutps'}
                </button>
            </div>
        </div>
    {/if*}
{else if !sizeof($payment_modules|@json_decode) && !sizeof($payment_modules_eu|@json_decode)}
    <p class="alert alert-warning col-xs-12 text-center">{l s='There are no payment methods available.' mod='onepagecheckoutps'}</p>
{else if !$is_logged and !$is_guest and $payment_need_register and $CONFIGS.OPC_SHOW_BUTTON_REGISTER}
    <p class="alert alert-info col-xs-12 text-center">{l s='You need to enter your data and address, to see payment methods.' mod='onepagecheckoutps'}</p>
{else}
    {literal}
    <script type="text/javascript">
        var total_order = {/literal}{$total_order|escape:'html':'UTF-8'}{literal};
        var content_payments = {/literal}{$payment_modules|escape:'quotes':'UTF-8'}{literal};
        var content_payments_eu = {/literal}{$payment_modules_eu|escape:'quotes':'UTF-8'}{literal};
        var payment_number = 0;
        var events_payments = new Array();

        if (OnePageCheckoutPS.PAYMENTS_WITHOUT_RADIO === true){
            initPaymentWithHTML();
        }else{
            initPayment();
        }

        //window.console.log(content_payments);

        function initPaymentWithHTML(){
            if(typeof content_payments !== typeof undefined){
                var $content = $("#payment_method_container");

                $content.html('');

                $.each(content_payments, function(k, module){
                    var module_html = module.html;

                    if($.isEmpty($.trim(module_html)) || module_html === false){
                        return;
                    }

                    events_payments[k] = new Array();

                    var $div_temporal = $('<div/>').append($('<div/>').html(module.html).text());

                    $div_temporal.appendTo($content);

                    $div_temporal.find('*').each(function(i, item){
                        if ($(this).hasClass('col-md-6'))
                            $(this).removeClass('col-md-6');

                        //quitamos los span que no tengan onclick
                        if($(item).is('span') && typeof $(item).attr('onclick') === typeof undefined){
                            return true;
                        }

                        if($(item).is('input[type=image], input[type=submit], input[type=button], a, button, span')){
                            var event = undefined;
                            if (typeof $(item).data('events') !== typeof undefined){
                                $.each($(item).data('events'), function(i, e){
                                    $.each(e, function(i, handler){
                                        if (handler['type'] == 'click'){
                                            event = handler['handler'];

                                            return false;
                                        }
                                    });
                                });
                            }

                            var onclick = undefined;
                            if (typeof $(item).attr('onclick') !== typeof undefined)
                            {
                                onclick = $(item).attr('onclick');
                            }

							//support payment module: mpmx
							if ($(item).attr('id') == 'botonMP'){
								onclick = "window.location = '" + $(item).attr('href') + "';";
								$(item).removeAttr('href');
							}

                            events_payments[k][i] = {'module_name' : module.name, 'element' : item, 'event' : event, 'onclick': onclick};

                            $(item).attr('onclick', '').unbind('click').click(function(event){
                                event.preventDefault();

                                Review.placeOrder({'validate_payment' : false, position_element : {'item_parent' : k, 'item_child' : i}});
                            });
                        }
                    });

                    //compatibilidad con modulo stripejs - PrestaShop + Ollie McFarlane - 1.0.2 Beta
                    if (module.name == 'stripejs' && module.author == 'PrestaShop + Ollie McFarlane'){
                        if (version_compare(module.version, '0.9.0', '<=')){
                            var _script = document.createElement('script');
                            _script.type = 'text/javascript';
                            _script.src = baseDir + 'modules/stripejs/views/js/stripe-prestashop.js';
                            $("body").append(_script);
                        }else{
                            var _script = document.createElement('script');
                            _script.type = 'text/javascript';
                            _script.src = baseDir + 'modules/stripejs/js/stripe-prestashop.js';
                            $("body").append(_script);
                        }
                    }
                });
            }
        }

        function initPayment(){
            if(typeof content_payments_eu !== typeof undefined){
                $.each(content_payments_eu, function(i, module) {
                    var action = module.action;

                    if ($.isEmpty(action) && !$.isEmpty(module.form)) {
                        if (module.name == 'paypalplus') {
                            action = 'iframe';
                        } else {
                            action = '$("#'+$(module.form).attr('id')+'").submit();';
                        }
                    }

                    if(!$.isEmpty(module.form)) {
                        if (module.name == 'paypalplus') {
                            $('<input/>').val(module.form).attr({'id' : 'iframe_payment_module_' + module.id}).hide().prependTo('#onepagecheckoutps_contenedor');
                        } else {
                            $(module.form).attr('id', module.name + '_container').prependTo('#onepagecheckoutps_contenedor #onepagecheckoutps_forms');
                        }
                    }

                    createPaymentModule(module, module.id, module.name, module.title, module.description, module.logo, action, '', '');
                });
            }

            if(typeof content_payments !== typeof undefined){
                $.each(content_payments, function(i, module){
					var module_html_ok = $('<div/>').html(module.html).text();
                    var $tmp = $('#opc_temporal').html(module_html_ok);

                    if (!$.isEmpty(module.url_payment)){
                        var module_title = module.title_opc;
                        var module_description = module.description_opc;

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, module.url_payment, '');

                        return;
                    }

                    if($.isEmpty($.trim(module.html)) || module.html === false)
                        return;

                    module_html_ok = module_html_ok.replace('&lt;noscript&gt;', '');
                    module_html_ok = module_html_ok.replace('&lt;/noscript&gt;', '');

                    if($.strpos(module_html_ok, "<iframe")){
                        $('<input/>').val(module_html_ok).attr({'id' : 'iframe_payment_module_' + module.id}).hide().prependTo('#onepagecheckoutps_contenedor');

						module_title = module.name;
						if (!$.isEmpty(module.title_opc))
                            module_title = module.title_opc;

						module_description = module.name;
                        if (!$.isEmpty(module.description_opc))
                            module_description = module.description_opc;

                        /*--------------------------------*/
                        if (!$.isEmpty(module.title_opc))
                            module_title = module.title_opc;

                        if (!$.isEmpty(module.description_opc))
                            module_description = module.description_opc;
                        /*--------------------------------*/

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, 'iframe', '');

                        return;
                    }

                    //compatibilidad con modulo PayNL
                    if ($tmp.find('.pnBlAnim').length > 0){
                        var payment = $tmp.find('.pnBlAnim');

                        var module_title = $(payment).find('table.pnLnkTbl').text();
                        var module_description = module_title;
                        var url_module_payment = "Fronted.createPopup(true, '', $('#onepagecheckoutps_forms #pnForm'), true, false, true, false);";
                        var onclick_module_payment = '';
                        var form = '';

                        $(payment).find('#pnSendBtn').click(function(){$('#pnForm').submit();});

                        ($(payment).find('#pnForm')).hide().prependTo('#onepagecheckoutps_contenedor #onepagecheckoutps_forms');

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, onclick_module_payment);
                    }else if ($tmp.find('.bloc_adresses').length > 0){
                        var payment = $tmp.find('.bloc_adresses');

                        var module_title = 'iDeal';
                        var module_description = module_title;
                        var url_module_payment = "Fronted.createPopup(true, '', $('#onepagecheckoutps_forms #pnForm'), true, false, true, false);";
                        var onclick_module_payment = '';
                        var form = '';

                        $(payment).find('#pnSendBtn').click(function(){$('#pnForm').submit();});

                        ($(payment).find('#pnForm')).hide().prependTo('#onepagecheckoutps_contenedor #onepagecheckoutps_forms');

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, onclick_module_payment);
                    }

                    //compatibilidad con modulo simplifycommerce
                    if (module.name == 'simplifycommerce'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('h3.pay-by-credit-card').text();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#simplify-payment-form #simplify-submit-button").trigger("click");';

                        $tmp.html('');

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', module_html_ok);

                        return;
                    }

                    //compatibilidad con modulo stripejs - PrestaShop + Ollie McFarlane - 1.0.2 Beta y NTS v2.1.4
                    if ((module.name == 'stripejs' && (module.author == 'PrestaShop + Ollie McFarlane')) || module.name == 'stripe_official'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('h3.stripe_title').text();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#stripe-payment-form .stripe-submit-button").trigger("click");Fronted.loadingBig(false);window.scrollTo(0, $("div#onepagecheckoutps").offset().top);';

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    //compatibilidad con modulo msstripe - Leighton Whiting - 2.0.2
                    if (module.name == 'msstripe'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('#click_msstripe img');
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#stripe_form #stripe_submit").trigger("click");Fronted.loadingBig(false);';

                        $tmp.html('');

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', module_html_ok);

                        return;
                    }

                    //compatibilidad con modulo stripepro - NTS - 4.1.1
                    if (module.name == 'stripepro' && $tmp.find('#stripe-payment-form').length > 0){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('h3.stripe_title').text();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#stripe-payment-form .stripe-submit-button").trigger("click");Fronted.loadingBig(false);';

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    //compatibilidad con modulo npaypalpro - PrestaShop - 1.3.7
                    if (module.name == 'npaypalpro'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('.paypalpro_title');
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#paypalpro-payment-form .paypalpro-submit-button").trigger("click");Fronted.loadingBig(false);';

                        $tmp.html('');

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', module_html_ok);

                        return;
                    }

                    /* compatibilidad con modulo paypalstandard - WebDevOverture - 2.0.16 */
                    if (module.name == 'paypalstandard') {
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('input[type="image"]').attr('alt');
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("#paypal_standard_button_container form").submit();';

                        $tmp.html('');

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', module_html_ok);

                        return;
                    }

                    //compatibilidad con modulo authorizeaim - PrestaShop - 1.5.7
                    if (module.name == 'authorizeaim'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('#click_authorizeaim').html();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("#authorizeaim_form")[0].submit();Fronted.loadingBig(false);';

                        $tmp.html('');

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', module_html_ok);

                        return;
                    }

                    //compatibilidad con modulo authorizedotnet - Presto-Changeo - 1.7.1
                    if (module.name == 'authorizedotnet'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('.accept_cards').html();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#adn_form #adn_submit").trigger("click");Fronted.loadingBig(false);';

                        $tmp.html('');

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', module_html_ok);

                        return;
                    }

                    //compatibilidad con modulo braintree
                    if (module.name == 'braintree' && $tmp.find('form').length > 0) {
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('.page-subheading').html();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#braintree_cc_submit .braintree_submit").trigger("click");';

                        if ($tmp.find('form#braintree_cc_submit_dropin').length > 0) {
                            url_module_payment = '$("form#braintree_cc_submit_dropin .dropin_submit").trigger("click");';
                        }

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    //compatibilidad con modulo pts_payplug
                    if (module.name == 'pts_payplug' && $tmp.find('form').length > 0){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('.pts_payplug').html();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = 'AppPPFront.createIntegratePayment();';

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    if (module.name == 'worldpay' && module.author == 'PrestaShop'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('.worldpay_title').html();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#worldpay-payment-form .worldpay-submit-button").trigger("click");Fronted.loadingBig(false);';

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    if (module.name == 'paypalplus'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : 'Paypal Plus';
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("#pppContinueButton").trigger("click");Fronted.loadingBig(false);';

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    //compatibilidad con modulo paytpv
                    if (module.name == 'paytpv' && $tmp.find('form#paytpvPaymentForm').length > 0){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : 'PayTPV';
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#paytpvPaymentForm #btnforg").trigger("click");Fronted.loadingBig(false);';

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    //compatibilidad con modulo firstdata v1.2.9
                    if (module.name == 'firstdata'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('.stripe_title').html();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form#firstdata_form #firstdata_submit").trigger("click");';

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    //compatibilidad con modulo monerisapi v1.8.7 - ZH Media
                    if (module.name == 'monerisapi'){
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('.monerisapi_title').html();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("#monerisapi .monerisapi-submit-button").trigger("click");Fronted.loadingBig(false);';

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    //compatibilidad con modulo pireospay v1.6.17 - 01generator.com
                    if (module.name == 'pireospay' && $tmp.find('form.pireospay_choice').length > 0) {
                        var module_title = !$.isEmpty(module.title_opc) ? module.title_opc : $tmp.find('form.pireospay_choice').html();
                        var module_description = !$.isEmpty(module.description_opc) ? module.description_opc : module_title;
                        var url_module_payment = '$("form.pireospay_choice button").trigger("click");';

                        $tmp.find('form.pireospay_choice button').hide();

                        createPaymentModule(module, module.id, module.name, module_title, module_description, module.url_image, url_module_payment, '', $tmp.children());

                        return;
                    }

                    payment_number = 0;
                    $tmp.find('.payment_module, .payment_module_lust, .mp-module, .openpay-payment-module').each(function(k, payment){
                        window.console.log(module.name, payment);
                        var module_html_ok = '';
                        var _a = $(payment).find('a').first();

                        var module_title = $(_a).attr('title');
                        var url_module_payment = $(_a).attr('href');
                        var url_image = module.url_image;
                        var onclick_module_payment = $(_a).length > 0 ? $(_a).get(0).getAttribute("onclick") : '';
                        var name_form = '';

                        var module_description = $(payment).text();
                        module_description = module_description.replace(/^\s+/g,'').replace(/\s+$/g,'');//trim

                        if ($.isEmpty(module_title) && $.isEmpty(module_description)){
                            module_description = $(payment).find('img');

                            if (typeof module_description === 'object'){
                                module_description = $(module_description).css({height : '100%', width: '100%'});
                            }
                        }

                        if(typeof module.additional[module.id + '_' + payment_number] !== typeof undefined){
                            if (!$.isEmpty(module.additional[module.id + '_' + payment_number].description)) {
                                module_description = module.additional[module.id + '_' + payment_number].description;
                                //module_title = module_description;
                            }

                            if ($.strpos(url_image, "default.png") || $.inArray(module.name, module.modules_external_image))
                                url_image = module.additional[module.id + '_' + payment_number].img;
                        }

                        if($.isEmpty(url_module_payment) || url_module_payment == '#' || url_module_payment == 'javascript:void(0)' || url_module_payment == 'javascript:void(0);' || url_module_payment == 'javascript: void(0);'){
                            if (!$.isEmpty(onclick_module_payment)){
                                onclick_module_payment = onclick_module_payment.replace(/^javascript:return/, '');
                                onclick_module_payment = onclick_module_payment.replace(/return false;/, '');

                                url_module_payment = onclick_module_payment;
                            }
                        }

                        if (module.name == 'sveawebpay'){
                            if ($(_a).hasClass('sveawebpayfaktura')){
                                url_image = baseDir + 'modules/' + module.name + '/img/invoice.png';
                            }
                            if ($(_a).hasClass('sveawebpaydelbetala')){
                                url_image = baseDir + 'modules/' + module.name + '/img/paymentplan.png';
                            }
                        }

                        if (module.name == 'itaushopline'){
                            url_module_payment = url_module_payment.replace(/abrir_janela_itaushopline\('/, '');
                            url_module_payment = url_module_payment.replace(/'\);/, '');
                        }

                        if (module.name == 'realexredirect'){
                            module_title = 'Realex Direct';
                            url_module_payment = $tmp.find('a').first().attr('href');
                        }

                        /*if (module.name == 'sequrapayment'){
                            url_image = $(_a).find('img').attr('src');
                            module_description = $(_a).find('.payment_desc-js').html();
                            module_title = $(_a).find('.payment_title-js').html();
                            url_module_payment = $(_a).attr('href');

                            createPaymentModule(module, module.id, module.name, module_title, module_description, url_image, url_module_payment, '');

                            return;
                        }*/

                        if (module.name == 'universalpay'){
                            var module_style = $(_a).css('background-image');
                                module_style = module_style.replace('url(','');
                                module_style = module_style.replace(')','');
                                module_style = module_style.replace(/"/g,'');

                            if ($.isUrlValid(module_style)){
                                url_image = module_style;
                            }
                        }

                        if (module.name == 'atos'){
                            module_description = $(payment).find('p.teaser').text();
                            module_title = module_description;
                            name_form = module.name;
                            url_module_payment = "$('#onepagecheckoutps_forms #form_" + name_form + " input[type=image]').trigger('click');";
                        }

                        if (module.name == 'paypalusa' || module.name == 'paypalmx'){
                            var _buttons = new Array('#paypal-express-checkout-btn-product', '#paypal-standard-btn', '#paypal-express-checkout-btn');

                            $.each(_buttons, function(i, item){
                                if ($tmp.find(item).length > 0){
                                    url_module_payment = '$("' + item + '").trigger("click");';

                                    if (!$.isEmpty(module_title))
                                        module_title = module_title;
                                    if (!$.isEmpty(module_description))
                                        module_description = module_description;

                                    if ($.isEmpty(module_title)){
                                        module_title = 'Paypal';
                                        module_description = module_title;
                                    }

                                    $tmp.find('form').css({'display' : 'none'});

                                    return true;
                                }
                            });
                        }

                        if (module.name == 'moneybookers'){
                            name_form = 'moneybookers';
                            if ($(payment).find('span').length > 0){
                                name_form = $(payment).find('span').html().toString();
                                name_form = name_form.replace(/\s/g,'').toLowerCase();
                            }

                            url_module_payment = "$('#onepagecheckoutps_forms #form_" + name_form + "').submit();";
                        }

                        //compatibilidad con modulo stripepayment - Fiestacode - 1.6.1
                        if (module.name == 'stripepayment'){
                            module_title = '';
                            module_description = $tmp.find('.mz_stripe').text();
                            url_module_payment = '$("form#stripeform .mz_stripe").trigger("click");Fronted.loadingBig(false);';
                        }

                        if (module.name == 'paypalpro'){
                            module_title = '';
                            module_description = $(payment).find('.accept_cards').html();

                            var callback = function(){
                                $('#pppro_form').show();
                            };

                            url_module_payment = "Fronted.showModal({name : 'opc_paypalpro', title : '"+module.title+"', title_icon : 'fa-credit-card', callback : "+callback+", content : $('#onepagecheckoutps_forms #pppro_form')});";
                        }

                        if (module.name == 'iyzicocheckoutform'){
                            var callback = function(){
                                toggleform();
                            };
                            url_module_payment = "Fronted.showModal({name : 'opc_iyzicocheckoutform', title : '"+module_title+"', title_icon : 'fa-credit-card', callback : "+callback+", content : $('#onepagecheckoutps #iyzipay-checkout-form')});";
                        }

                        if (module.name == 'payplug'){
                            if ($tmp.find('#form_payplug_payment').length > 0) {
                                $tmp.find('#form_payplug_payment').show();
                                module_html_ok = $tmp.find('#form_payplug_payment');

                                url_module_payment = 'callPayment($("#form_payplug_payment input[name=payplug_card]:checked").val());Fronted.loadingBig(false);';
                            } else if ($tmp.find('a.payplug').hasClass('call')) {
                                url_module_payment = 'callPayment("new_card");Fronted.loadingBig(false);';
                            } else {
                                url_module_payment = $tmp.find('a.payplug').attr('href');
                            }
                        }

                        if (module.name == 'paylater'){
                            module_description += '<div class="PmtSimulator" data-pmt-num-quota="4" data-pmt-max-ins="6" data-pmt-style="not_aplicable" data-pmt-type="3" data-pmt-discount="0" data-pmt-amount="'+total_order+'" data-pmt-expanded="no"></div>';
                        }

                        if (module.name == 'euplatesc'){
                            url_module_payment = 'javascript:document.euplatesc_form.submit()';
                        }

                        if (module.name == 'triveneto'){
                            url_module_payment = '$("#triveneto_form").submit();';
                        }

                        if (module.name == 'trz_yadpay'){
                            url_module_payment = '$("#YaadPay").submit();';
                        }

                        //fix paypal v325
                        if (module.name == 'paypal'){
                            if (url_module_payment == 'javascript:void(0)' || $.isEmpty(url_module_payment)) {
                                url_module_payment = "$('#onepagecheckoutps_forms #" + $tmp.find('form').attr('id') + "').submit();";
                            }
                        }

                        if (module.name == 'payzen'){
                            if (url_module_payment == 'javascript:void(0);')
                                url_module_payment = 'javascript:document.payzen_standard.submit()';
                        }

                        if (module.name == 'bestkit_2co'){
                            url_module_payment = '$("#twoco_form input[name=submit]").click()';
                        }

                        if (module.name == 'ogone'){
                            url_module_payment = 'document.forms["ogone_form"].submit()';
                        }

                        if (module.name == 'amzpayments'){
                            url_module_payment = '$("#payWithAmazonListDiv img, #payWithAmazonMainDiv img, #payWithAmazonMainDivAbove img").trigger("click");Fronted.loadingBig(false);';
                        }

                        if (module.name == 'paylike'){
                            url_module_payment = 'pay();Fronted.loadingBig(false);';
                        }

                        if (module.name == 'payme'){
                            url_module_payment = "javascript:AlignetVPOS2.openModal('https://vpayment.verifika.com/')";
                        }

                        if (module.name == 'khipupayment'){
                            url_image = $(payment).find('img').attr('src');
                        }

                        if (module.name == 'mdstripe'){
                            $('#onepagecheckoutps_contenedor #onepagecheckoutps_forms').append($tmp.html());
                            url_module_payment = '$("#mdstripe_payment_link").trigger("click");Fronted.loadingBig(false);';
                        }

                        if (module.name == 'etransactions'){
                            url_image = ($(_a).css('background-image')).replace('url(','').replace(')','').replace(/\"/gi, "");
                        }

                        if (module.name == 'stripepro') {
                            module_description = module_title;
                            url_module_payment = "Fronted.showModal({name: 'payment_modal_stripepro', type:'normal', title: OnePageCheckoutPS.Msg.confirm_payment_method, title_icon: 'fa-pts-credit-card', content : $('.stripe-payment-16'), close : true});";
                        }

                        if (module.name == 'stripepayment') {
                            module_title = $tmp.find('.wk_ps_stripe').text();
                            url_module_payment = "Fronted.showModal({name: 'payment_modal_stripepayment', type:'normal', title: OnePageCheckoutPS.Msg.confirm_payment_method, title_icon: 'fa-pts-credit-card', content : $('#stripeform'), close : true});";
                        }

                        if (module.name == 'clickcanarias'){
                            url_module_payment = '$("#clickcanarias_form").submit();';
                        }

                        if (module.name == 'senangpay'){
                            url_module_payment = '$("#senangpay-api-form").submit();';
                        }

                        if (module.name == 'ngpagarme'){
                            //url_image = url_image.replace('ngpagarme', 'ngpagarme_' + payment_number);
                            var url_image_tmp = $(payment).find('a').css('backgroundImage');
                            url_image_tmp = url_image_tmp.replace('url("', '');
                            url_image = url_image_tmp.replace('")', '');
                        }

                        if (module.name == 'heidelpay'){
                            var url_image_tmp = $(payment).find('span.icon').css('backgroundImage');
                            url_image_tmp = url_image_tmp.replace('url("', '');
                            url_image = url_image_tmp.replace('")', '');
                        }

                        if (module.name == 'systempay'){
                            url_image = $tmp.find(".systempay_payment_module img").attr('src');
                            url_module_payment = '$("#systempay_standard").submit();';
                        }
                        if (module.name == 'franfinance'){
                            var class_form = $(payment).find('a').attr('class');
                            name_form = class_form.replace(' ', '_');
                            url_module_payment = '$("#'+name_form+'").submit();';
                        }

                        /*--------------------------------*/
                        if (!$.isEmpty(module.title_opc)) {
                            module_title = module.title_opc;
                        }

                        if (!$.isEmpty(module.description_opc)) {
                            module_description = module.description_opc;
                        }

                        if (module_title == undefined) {
                            module_title = '';
                        }
                        if (module_description == undefined) {
                            module_description = '';
                        }
                        /*--------------------------------*/

                        if (!$.isEmpty(url_module_payment) || !$.isEmpty(onclick_module_payment)){
                            if (module.name == 'monerisapi'){
                                var $div_monerisapi = $('<div/>').attr({'id': 'div_monerisapi'});

                                $($div_monerisapi).append($(payment).find('.moneris_title'));
                                $($div_monerisapi).append($(payment).find('#monerisapi_form').prev().prev().attr('style', 'height: auto'));
                                $($div_monerisapi).append($(payment).find('#monerisapi_form'));
                                $($div_monerisapi).hide().prependTo('#onepagecheckoutps_contenedor #onepagecheckoutps_forms');
                            }else if (module.name == 'moneybookers'){
                                var form = $(payment).parent();

                                form.attr('id', 'form_' + name_form);
                                form.hide();
                                form.prependTo('#onepagecheckoutps_contenedor #onepagecheckoutps_forms');

                            }else if (module.name == 'ps_targetpay'){
                                var form = $(payment).next();

                                form.attr('id', 'form_' + name_form);
                                form.hide();
                                form.prependTo('#onepagecheckoutps_contenedor #onepagecheckoutps_forms');
                            } else if (module.name == 'franfinance') {
                                var form = $(payment).parents('.row').next().find('form');
                                form.attr('id', name_form);
                                form.hide();
                                form.prependTo('#onepagecheckoutps_contenedor #onepagecheckoutps_forms');

                                url_image = form.find('input[type=image]').attr('src');
                            }else{
                                var form = $tmp.find('form');

                                if(name_form != ''){
                                    form.attr('id', 'form_' + name_form);
                                    form.hide();
                                    form.prependTo('#onepagecheckoutps_contenedor #onepagecheckoutps_forms');
                                }

                                if (module.name != 'alphabnk'){
                                    if (form.length > 0){
                                        form.hide();
                                        form.prependTo('#onepagecheckoutps_contenedor #onepagecheckoutps_forms');

                                        if (url_module_payment == 'javascript:void(0)' || url_module_payment == '' || url_module_payment == '#'){
                                            if (typeof $('#onepagecheckoutps_forms #' + module.name + '_form')[0] !== 'undefined') {
                                                /*url_module_payment = "Fronted.showModal({name: 'payment_modal', type:'normal', title: '"+OnePageCheckoutPS.Msg.confirm_payment_method+"', content: $('#onepagecheckoutps_forms #" + module.name + "_form'), close : true});";*/
                                                url_module_payment = "$('#onepagecheckoutps_forms #" + module.name + "_form').submit();";
                                            }
                                        }
                                    }
                                }
                            }

                            createPaymentModule(module, module.id, module.name, module_title, module_description, url_image, url_module_payment, onclick_module_payment, module_html_ok);
                        }
                    });
                });
            }
        }

        function createPaymentModule(module, id_module, module_name, module_title, module_description, url_image, url_module_payment, onclick_module_payment, payment_content_html){
            url_module_payment = url_module_payment.replace(/^(modules)/, baseDir + 'modules'); //anade el http si le hace falta.
            url_module_payment = url_module_payment.replace(/^(\/modules)/, baseDir + 'modules'); //anade el http si le hace falta.
            url_module_payment = url_module_payment.replace(/(modules\/onepagecheckoutps\/)/, ''); //fix ie

            var _position = $.strpos(url_module_payment, "modules/");
            if (_position){
                url_module_payment = baseDir + url_module_payment.substr(_position);
            }

            if (!$.isEmpty(module.title))
                module_title = module.title;
            if (!$.isEmpty(module.description))
                module_description = module.description;

            var radio =
                $('<input/>')
                    .attr({
                        id: 'module_payment_' + id_module + '_' + payment_number,
                        name: 'method_payment',
                        class: 'payment_radio not_unifrom not_uniform ' + (typeof module.action !== typeof undefined ? 'payment_eu' : ''),
                        type: 'radio',
                        value: module_name,
                        checked: (Object.keys(content_payments).length == 1 ? true : false)
                    })
                    .change(PaymentPTS.change);
            var input =
                $('<input/>').attr({
                    type: 'hidden',
                    id: 'url_module_payment_' + id_module,
                    value: url_module_payment
                });

            if (!$.isEmpty(onclick_module_payment) && typeof onclick_module_payment == 'string'){
                onclick_module_payment = onclick_module_payment.replace('return', '');

                input.get(0).setAttribute('onclick', onclick_module_payment);
            }

            if (typeof module_description === 'string'){
                module_description = module_description.replace(module_title, '');
            }

            var div_container =
                $('<div/>')
                .attr({
                    class: 'row module_payment_container pts-vcenter',
                    for: 'module_payment_' + id_module + '_' + payment_number
                });

            var p_description = $('<p/>').html(module_description);

            var class_extra = '';
            if(module.additional[module.id + '_' + payment_number] != undefined){
                class_extra = module.additional[module.id + '_' + payment_number]['class'];
            }

            var image =
                $('<img>')
                .attr({
                    src: url_image,
                    title: module_title,
                    class: 'img-thumbnail img-responsive ' + class_extra
                });

            $('<div/>')
                .attr('class', 'payment_input col-xs-1')
                .append(radio)
                .append(input)
                .appendTo(div_container);

            $('<div/>')
                .attr('class', 'payment_image col-xs-2')//hidden-sm
                .append(image)
                .appendTo(div_container);

            $('<div/>')
                .attr('class', 'payment_content col-xs-9')
                .append('<span>'+module_title+'</span>')
                .append(p_description)
                .appendTo(div_container);

            if (!$.isEmpty(payment_content_html)) {
                AppOPC.jqOPC('<div/>')
                    .attr('id', 'payment_content_html_' + module.id + '_' + payment_number)
                    .attr('class', 'payment_content_html col-xs-11 col-xs-offset-1 hidden')
                    .html(payment_content_html)
                    .appendTo(div_container);
            }

            div_container.appendTo($('div#onepagecheckoutps #payment_method_container'));

            //compatibilidad con modulo stripejs - PrestaShop + Ollie McFarlane - 1.0.2 Beta
            if (module.name == 'stripejs' && module.author == 'PrestaShop + Ollie McFarlane'){
                if (version_compare(module.version, '0.9.0', '<=')){
                    var _script = document.createElement('script');
                    _script.type = 'text/javascript';
                    _script.src = baseDir + 'modules/stripejs/views/js/stripe-prestashop.js';
                    $("body").append(_script);
                }else{
                    var _script = document.createElement('script');
                    _script.type = 'text/javascript';
                    _script.src = baseDir + 'modules/stripejs/js/stripe-prestashop.js';
                    $("body").append(_script);
                }
            }

            payment_number+= 1;
        }
    </script>
    {/literal}

    <div id="opc_payment_methods">
        <div id="opc_payment_methods-content" class="hidden"><div id="HOOK_PAYMENT"></div></div>
        <div id="HOOK_TOP_PAYMENT" class="hidden">{$HOOK_TOP_PAYMENT|escape:'html':'UTF-8':false:true}</div>
        <div id="payment_method_container"></div>
    </div>

    {include file='./custom_html/payment.tpl'}
{/if}