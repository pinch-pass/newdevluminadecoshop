/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2019 PresTeamShop
 * @license   see file: LICENSE.txt
 * @category  PrestaShop
 * @category  Module
 */

$(function(){
    AppOPC.init();
    OPC_External_Validation.init();

    function toggleIcon(e) {
        $(e.target)
            .prev('.panel-heading')
            .find(".more-less")
            .toggleClass('fa-pts-angle-down fa-pts-angle-up');
    }
    $('#panel_addresses_customer').on('hidden.bs.collapse', toggleIcon);
    $('#panel_addresses_customer').on('shown.bs.collapse', toggleIcon);

    //$('#checkbox_create_account_guest').trigger('click');
});

var AppOPC = {
    $opc: false,
    $opc_step_one: false,
    $opc_step_two: false,
    $opc_step_three: false,
    $opc_step_review: false,
    initialized: false,
    load_offer: true,
    is_valid_opc: false,
    is_valid_form_login: false,
    is_valid_form_customer: false,
    is_valid_form_address_delivery: false,
    is_valid_form_address_invoice: false,
    jqOPC: typeof $jqOPC === typeof undefined ? $ : $jqOPC,
    m4gpdr: false,
    init: function(){
        $(document).on('click', 'button#btn-copy_invoice_address_to_delivery', AppOPC.copyInvoiceAddressToDelivery);

        AppOPC.initialized = true;
        AppOPC.$opc = $('#onepagecheckoutps');
        AppOPC.$opc_step_one = $('#onepagecheckoutps div#onepagecheckoutps_step_one');
        AppOPC.$opc_step_two = $('#onepagecheckoutps div#onepagecheckoutps_step_two');
        AppOPC.$opc_step_three = $('#onepagecheckoutps div#onepagecheckoutps_step_three');
        AppOPC.$opc_step_review = $('#onepagecheckoutps div#onepagecheckoutps_step_review');

        if (typeof OnePageCheckoutPS !== typeof undefined)
        {
            if (typeof paypal_ec_canceled !== typeof undefined && paypal_ec_canceled) {
                window.location = orderOpcUrl;
                return false;
            }

            if (typeof jeoquery !== typeof undefined) {
                jeoquery.defaultCountryCode = OnePageCheckoutPS.iso_code_country_delivery_default;
                jeoquery.defaultLanguage = OnePageCheckoutPS.LANG_ISO;
                jeoquery.defaultData.lang = OnePageCheckoutPS.LANG_ISO;
            }

            //Se anade funcionalidad para eliminar espacios vacios de principio y fin de la cadena
            AppOPC.$opc_step_one.find('input:text, input[type="email"]').on('blur', function(e) {
                var value       = $(e.currentTarget).val();
                var new_value   = value.trim();

                $(e.currentTarget).val(new_value);
            });

            //launch validate fields
            if (typeof $.formUtils !== typeof undefined && typeof $.validate !== typeof undefined){
                $.formUtils.loadModules('prestashop.js, security.js, brazil.js', OnePageCheckoutPS.ONEPAGECHECKOUTPS_DIR + 'views/js/lib/form-validator/');
                $.validate({
                    form: 'div#onepagecheckoutps #form_login, div#onepagecheckoutps #form_customer, div#onepagecheckoutps #form_address_delivery, div#onepagecheckoutps #form_address_invoice',
                    validateHiddenInputs: true,
                    language : messageValidate,
                    onError: function ($form) {
                        if ($form.attr('id') == 'form_login') {
                            AppOPC.is_valid_form_login = false;
                        } else if ($form.attr('id') == 'form_customer') {
                            AppOPC.is_valid_form_customer = false;
                        } else if ($form.attr('id') == 'form_address_delivery') {
                            AppOPC.is_valid_form_address_delivery = false;
                        } else if ($form.attr('id') == 'form_address_invoice') {
                            AppOPC.is_valid_form_address_invoice = false;
                        }
                    },
                    onSuccess: function ($form) {
                        if ($form.attr('id') == 'form_login') {
                            AppOPC.is_valid_form_login = true;
                        } else if ($form.attr('id') == 'form_customer') {
                            AppOPC.is_valid_form_customer = true;
                        } else if ($form.attr('id') == 'form_address_delivery') {
                            AppOPC.is_valid_form_address_delivery = true;
                        } else if ($form.attr('id') == 'form_address_invoice') {
                            AppOPC.is_valid_form_address_invoice = true;
                        }

                        return false;
                    }
                });
            }

            $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE)
                .css({
                    margin: 0
                })
                .addClass('opc_center_column ' + AppOPC.$opc.find('#rc_page').val())
                .removeClass('col-sm-push-3');

            Address.launch();
            Fronted.launch();
            if (!OnePageCheckoutPS.REGISTER_CUSTOMER)
            {
                $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE).css({width: '100%'});

                Carrier.launch();
                PaymentPTS.launch();
                Review.launch();
            }

            if (typeof $.fn.datepicker !== typeof undefined) {
                AppOPC.$opc_step_one.find('input[data-validation*="isBirthDate"], input[data-validation*="isDate"]').datepicker({
                    dateFormat: OnePageCheckoutPS.date_format_language,
                    //cuando necesita des-habilitar dias anteriores al actual
                    //minDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    yearRange: '-100:+0',
                    isRTL: OnePageCheckoutPS.IS_RTL
                });
            }
        }else{//redirect to checkout if have he option OPC_REDIRECT_DIRECTLY_TO_OPC actived
            if ($('a.standard-checkout, .cart_navigation .btn.pull-right, .cart_navigation .btnsns.pull-right, a.proceso_compra').length > 0) {
                $('a.standard-checkout, .cart_navigation .btn.pull-right, .cart_navigation .btnsns.pull-right, a.proceso_compra').attr('href', baseDir + 'index.php?controller=order-opc&checkout=1');
            } else if($('button.btn-continue').length > 0) {
                $('button.btn-continue').attr('onclick', 'window.location=\''+baseDir + 'index.php?controller=order-opc&checkout=1\'');
            }

            $('form#voucher').attr('action', baseDir + 'index.php?controller=order-opc');

            var href_delete_voucher = $('a.price_discount_delete').attr('href');
            if (typeof(href_delete_voucher) != 'undefined'){
                href_delete_voucher = href_delete_voucher.split('?');
                $('a.price_discount_delete').attr('href', baseDir + 'index.php?controller=order-opc&' + href_delete_voucher[1]);
            }
        }
    },
    copyInvoiceAddressToDelivery: function() {
        var $invoice_fields = $('#panel_address_invoice #form_address_invoice').find('.invoice');
        var $form_delivery = $('#panel_address_delivery #form_address_delivery');

        $.each($invoice_fields, function(i, elem) {
            var field_name  = $(elem).data('field-name');
            var value       = $(elem).val();

            if (field_name != 'id' && $form_delivery.find('[data-field-name="'+field_name+'"]').length > 0) {
                $form_delivery.find('[data-field-name="'+field_name+'"]').val(value);
            }
        });
    }
}

var Fronted = {
    launch: function(){
        $('div#onepagecheckoutps #opc_show_login').click(function(){
            Fronted.showModal({type:'normal', title:$('#opc_login').attr('title'), title_icon:'fa-pts-user', content:$('#opc_login')});
        });

        AppOPC.$opc.find('#opc_login').on('click', '#btn_login', Fronted.loginCustomer);

        AppOPC.$opc.on('click', '#btn_continue_shopping', function(){
            var link = AppOPC.$opc.find('#btn_continue_shopping').attr('data-link');
            if (typeof link === typeof undefined) {
                link = baseDir;
            }
            window.location = link;
        });

        AppOPC.$opc.find('#opc_login #txt_login_password').keypress(function(e){
            var code = (e.keyCode ? e.keyCode : e.which);

            if (code == 13)
                Fronted.loginCustomer();
        });

        //evita el guest checkout cuando solo quiere registrarse o iniciar sesion "rc=1".
        if (AppOPC.$opc_step_review.length <= 0) {
            var $create_account_guest = AppOPC.$opc_step_one.find('#field_customer_checkbox_create_account_guest');
            if ($create_account_guest.length > 0) {
                $create_account_guest.hide();

                if (!$create_account_guest.find('#checkbox_create_account_guest').is(':checked')) {
                    $create_account_guest.find('#checkbox_create_account_guest').trigger('click');
                }
            }
        }

        //evita copiar, pegar y cortar en los campos de confirmacion.
        /*AppOPC.$opc_step_one.find('#customer_conf_email, #customer_conf_passwd').bind("cut copy paste", function(e) {
            e.preventDefault();
        });*/
    },
    openCMS: function(params){
        var param = $.extend({}, {
            id_cms: ''
        }, params);

        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
            dataType: 'html',
            action: 'loadCMS',
            id_cms: param.id_cms
        };

        var _json = {
            data: data,
            beforeSend: function() {
                Fronted.loadingBig(true);
            },
            success: function(html) {
                if (!$.isEmpty(html)){
                    Fronted.showModal({name: 'cms_modal', content: html});
                }
            },
            complete: function(){
                Fronted.loadingBig(false);
            }
        };
        $.makeRequest(_json);
    },
    loading: function(show, selector){
        if (show) {
            Fronted.loadingBig(true);
        }
    },
    loadingBig: function(show){
        if (show && !AppOPC.$opc.find('> .row').hasClass('.opc_overlay')) {
            AppOPC.$opc.find('> .row').addClass('opc_overlay');

            if ($(window).width() >= 1024) {
                AppOPC.$opc.find('.loading_big').show();
            } else {
                $('div#opc_loading').remove();
                $('body').append('<div id="opc_loading">'+OnePageCheckoutPS.Msg.processing_purchase+'<i class="fa-pts fa-pts-spin fa-pts-refresh"></i></div>');
            }
        } else {
            AppOPC.$opc.find('> .row').removeClass('opc_overlay');

            if ($(window).width() >= 1024) {
                AppOPC.$opc.find('.loading_big').hide();
            } else {
                $('div#opc_loading').remove();
            }
        }
    },
    showModal: function(params){
        var param = $.extend({}, {
            name: 'opc_modal',
            type: 'normal',
            title: '',
            title_icon: '',
            message: '',
            content: '',
            close: true,
            button_ok: false,
            button_close: false,
            size: '',
            callback: '',
            callback_ok: '',
            callback_close: ''
        }, params);

        $('#'+param.name).remove();

        //var windows_width = $(window).width();

        var parent_content = '';
        if (typeof param.content === 'object'){
            parent_content = param.content.parent();
        }

        var $modal = $('<div/>').attr({id:param.name, 'class':'modal fade', role:'dialog', 'href':'#'}).click(function(){
			$('#'+param.name).hide();
return false;
                });
        var $modal_dialog = $('<div/>').attr({'class':'modal-dialog ' + param.size}).click(function(){
			$('#'+param.name).hide();
return false;
                });;
        var $modal_header = $('<div/>').attr({'class':'modal-header'}).click(function(){
			$('#'+param.name).hide();
return false;
                });
        var $modal_content = $('<div/>').attr({'class':'modal-content'}).click(function(){
			$('#'+param.name).hide();
return false;
                });;
        var $modal_body = $('<div/>').attr({'class':'modal-body'}).click(function(){
			$('#'+param.name).hide();
return false;
                });;
        var $modal_footer = $('<div/>').attr({'class':'modal-footer'});
        var $modal_button_close = $('<button/>')
                .attr({type:'button', 'class':'close'})
                .click(function(){
                    //$('#'+param.name).modal('hide');
			$('#'+param.name).hide();
return false;
                })
                .append('<i class="fa-pts fa-pts-close"></i>');
        var $modal_button_ok_footer = $('<button/>')
            .attr({type:'button', 'class':'btn btn-primary'})
            .click(function(){
                if (typeof param.callback_ok !== typeof undefined && typeof param.callback_ok === 'function') {
                    if (!param.callback_ok()) {
                        return false;
                    }
                    //$('#'+param.name).modal('hide');
$('#'+param.name).hide();
                }
            })
            .append('OK');
        var $modal_button_close_footer = $('<button/>')
            .attr({type:'button', 'class':'btn btn-default'})
            .click(function(){
                //$('#'+param.name).modal('hide');
            })
            .append(OnePageCheckoutPS.Msg.close);
        var $modal_title = '';

        if (typeof param.message === 'array'){
            var message_html = '';
            $.each(param.message, function(i, message){
                message_html += '- ' + message + '<br/>';
            });
            param.message =  message_html;
        }

        if (param.type == 'error'){
            $modal_title = $('<span/>')
                .attr({'class':'panel-title'})
                .append(param.close ? $modal_button_close : '')
                .append('<i class="fa-pts fa-pts-times-circle fa-pts-2x" style="color:red"></i>')
                .append(param.message);
        }else if (param.type == 'warning'){
            $modal_title = $('<span/>')
                .attr({'class':'panel-title'})
                .append(param.close ? $modal_button_close : '')
                .append('<i class="fa-pts fa-pts-warning fa-pts-2x" style="color:orange"></i>')
                .append(param.message);
        }
        else{
            $modal_title = $('<span/>')
                .attr({'class':'panel-title'})
                .append(param.close ? $modal_button_close : '')
                .append('<i class="fa-pts '+param.title_icon+' fa-pts-1x"></i>')
                .append(param.title);
        }

        $modal_header.append($modal_title);
        $modal_content.append($modal_header);

        if (param.type == 'normal'){
            if (typeof param.content === 'object'){
                param.content.removeClass('hidden').appendTo($modal_body);
            }else{
                $modal_body.append(param.content);
            }

            $modal_content.append($modal_body);

            if (param.button_close){
                $modal_footer.append($modal_button_close_footer);
                $modal_content.append($modal_footer);
            }
            if (param.button_ok){
                $modal_footer.append($modal_button_ok_footer);
                $modal_content.append($modal_footer);
            }
        }

        $modal_dialog.append($modal_content);
        $modal.append($modal_dialog);

        $modal.on('hide.bs.modal', function(){
            if (!param.close){
                return false;
            } else {
                if (typeof param.callback_close !== typeof undefined && typeof param.callback_close === 'function') {
                    if (!param.callback_close()) {
                        return false;
                    }
                }

                if (!$.isEmpty(parent_content)) {
                    param.content.appendTo(parent_content).addClass('hidden');
                }

                $('body').removeClass('modal-open');
            }
        });

        AppOPC.$opc.prepend($modal);

        $('#'+param.name).modal('show');

        if (!$('#'+param.name).hasClass('in')) {
            $('#'+param.name).addClass('in').css({display : 'block'});
        }

        var modalResize = function() {
            var paddingTop = 0
            var windows_height = $(window).height();

            if (windows_height > $modal_dialog.height() && (AppOPC.$opc.height() / 2) > $modal_dialog.height()) {
                paddingTop = (windows_height - $modal_dialog.height()) / 2;
            }

            $('#'+param.name).css({
                paddingTop: paddingTop
            });
        };
        modalResize();

        $(window).on('resize', function(){
            modalResize();
        });

        Fronted.loadingBig(false);

        if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
            param.callback();

        //fix problem with module: pakkelabels_shipping
        $('.pakkelabels_modal-backdrop').remove();

        window.scrollTo(0, $('div#onepagecheckoutps').offset().top);
    },
    loginCustomer: function(){
        var email = $('#opc_login #txt_login_email').val();
        var password = $('#opc_login #txt_login_password').val();
        var login_success = false;

        var data = {
            is_ajax: true,
            action: 'loginCustomer',
            email: email,
            password: password
        };

        Fronted.validateOPC({valid_form_login: true});

        if (AppOPC.is_valid_opc) {
            //no its use makeRequest because dont work.. error weird.
            $.ajax({
                type: 'POST',
                url: orderOpcUrl + '?rand=' + new Date().getTime(),
                cache: false,
                dataType: 'json',
                data: data,
                beforeSend: function() {
                    $('#opc_login #btn_login').attr('disabled', 'true');
                    $('#opc_login .alert').empty().addClass('hidden');
                },
                success: function(json) {
                    if(json.success) {
                        if ($('div#onepagecheckoutps #onepagecheckoutps_step_review_container').length > 0) {
                            window.parent.location.reload();
                        } else {
                            if (parseInt($('.shopping_cart .ajax_cart_quantity').text()) > 0){
                                window.parent.location = orderOpcUrl;
                            } else {
                                window.parent.location = baseDir;
                            }
                        }

                        login_success = true;
                    } else {
                        if(json.errors){
                            $('#opc_login .alert').html('&bullet; ' + json.errors.join('<br>&bullet; ')).removeClass('hidden');
                        }
                    }
                },
                complete: function(){
                    if (!login_success) {
                        $('#opc_login #btn_login').removeAttr('disabled');
                    }
                }
            });
        }
    },
    removeUniform : function (params){
        var param = $.extend({}, {
            'parent_control' : 'div#onepagecheckoutps',
            errors : {}
        }, params);

        if (typeof $.uniform !== 'undefined' && typeof $.uniform.restore !== 'undefined') {
            $.uniform.restore(param.parent_control + ' select');
            $.uniform.restore(param.parent_control + ' input');
            $.uniform.restore(param.parent_control + ' a.button');
            $.uniform.restore(param.parent_control + ' button');
            $.uniform.restore(param.parent_control + ' textarea');
        }

        if (typeof $(param.parent_control + ' select').select_unstyle !== 'undefined') {
            $(param.parent_control + ' select').select_unstyle();
        }

        if (typeof $(param.parent_control + ' select').selectBox !== 'undefined') {
            $(param.parent_control + ' select').selectBox('destroy');
        }

        if (typeof $(param.parent_control + ' select').selectBox !== 'undefined') {
            $(param.parent_control + ' select').selectBox('destroy');
        }
    },
	openWindow: function (url){
		var LeftPosition = (screen.width) ? (screen.width-700)/2 : 0;
		var TopPosition = (screen.height) ? (screen.height-500)/2 : 0;
		window.open(url,'','height=500,width=600,top='+(TopPosition-10)+',left='+LeftPosition+',toolbar=no,directories=no,status=no,menubar=no,modal=yes,scrollbars=yes');
	},
    validateOPC: function(params){
        var param = $.extend({}, {
            valid_form_login: false,
            valid_form_customer: false,
            valid_form_address_delivery: false,
            valid_form_address_invoice: false,
            valid_carrier: false,
            valid_payment: false,
            valid_condition: false,
            valid_privacy: false,
            valid_gdpr: false
        }, params);

        AppOPC.is_valid_opc = true;

        if (param.valid_form_login) {
            AppOPC.$opc.find('#form_login').submit();
        }
        if (param.valid_form_customer) {
            if (($('div#onepagecheckoutps #field_customer_checkbox_change_passwd input[name="checkbox_change_passwd"]').length > 0
                && !$('div#onepagecheckoutps #field_customer_checkbox_change_passwd input[name="checkbox_change_passwd"]').is(':checked'))
                || ($('div#onepagecheckoutps #field_customer_checkbox_create_account input[name="checkbox_create_account"]').length > 0
                && !$('div#onepagecheckoutps #field_customer_checkbox_create_account input[name="checkbox_create_account"]').is(':checked'))
                || ($('div#onepagecheckoutps #field_customer_checkbox_create_account_guest input[name="checkbox_create_account_guest"]').length > 0
                && !$('div#onepagecheckoutps #field_customer_checkbox_create_account_guest input[name="checkbox_create_account_guest"]').is(':checked'))
            ) {
                $('#onepagecheckoutps input[type="password"]').val('');
            }
            AppOPC.$opc.find('#form_customer').submit();
        }
        if (AppOPC.$opc.find('#form_address_delivery').length > 0 && (!OnePageCheckoutPS.IS_VIRTUAL_CART || OnePageCheckoutPS.CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL) && param.valid_form_address_delivery) {
            AppOPC.$opc.find('#form_address_delivery').submit();
        }
        if (Address.isSetInvoice() && param.valid_form_address_invoice) {
            AppOPC.$opc.find('#form_address_invoice').submit();
        }

        if (param.valid_form_login && !AppOPC.is_valid_form_login) {
            AppOPC.is_valid_opc = false;
        }
        if (param.valid_form_customer && !AppOPC.is_valid_form_customer) {
            AppOPC.is_valid_opc = false;
        }
        if (AppOPC.$opc.find('#form_address_delivery').length > 0
            && (!OnePageCheckoutPS.IS_VIRTUAL_CART || OnePageCheckoutPS.CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL)
            && param.valid_form_address_delivery
            && !AppOPC.is_valid_form_address_delivery
        ) {
            AppOPC.is_valid_opc = false;

            if (OnePageCheckoutPS.IS_LOGGED) {
                AppOPC.$opc_step_one.find('#delivery_address_container #form_address_delivery').show(400);
                AppOPC.$opc_step_one.find('.addresses_customer_container.delivery').hide(400);
            }
        }
        if (Address.isSetInvoice() && param.valid_form_address_invoice && !AppOPC.is_valid_form_address_invoice) {
            AppOPC.is_valid_opc = false;

            if (OnePageCheckoutPS.IS_LOGGED) {
                AppOPC.$opc_step_one.find('#invoice_address_container #form_address_invoice').show(400);
                AppOPC.$opc_step_one.find('.addresses_customer_container.invoice').hide(400);
            }
        }

        if (AppOPC.is_valid_opc) {
            if (param.valid_carrier) {
                AppOPC.$opc_step_two.removeClass('alert alert-warning');

                //validate shipping
                if (AppOPC.$opc_step_two.find('.delivery_options_address').length >= 0 && !OnePageCheckoutPS.IS_VIRTUAL_CART) {
                    var id_carrier = AppOPC.$opc_step_two.find('.delivery_option_radio:checked').val();

                    if (!$.isEmpty(id_carrier)){
                        Carrier.id_delivery_option_selected = id_carrier;

                        AppOPC.is_valid_opc = true;
                    }else{
                        Carrier.id_delivery_option_selected = null;
                        AppOPC.$opc_step_two.find('#shipping_container').addClass('alert alert-warning');

                        Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.shipping_method_required});

                        AppOPC.is_valid_opc = false;
                    }
                }
            }
        }

        if (AppOPC.is_valid_opc) {
            if (param.valid_payment && !OnePageCheckoutPS.CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO) {
                AppOPC.$opc_step_three.removeClass('alert alert-warning');

                //validate payments
                if (AppOPC.$opc_step_three.find('#free_order').length <= 0) {
                    var payment = AppOPC.$opc_step_three.find('input[name="method_payment"]:checked');

                    if (payment.length > 0){
                        PaymentPTS.id_payment_selected = $(payment).attr('id');

                        AppOPC.is_valid_opc = true;
                    }else{
                        PaymentPTS.id_payment_selected = '';

                        //support module payment: Pay
                        if (!$.isEmpty($('#securepay_cardNo').val()) &&
                            !$.isEmpty($('#securepay_cardSecurityCode').val()) &&
                            !$.isEmpty($('#securepay_cardExpireMonth').val()) &&
                            !$.isEmpty($('#securepay_cardExpireYear').val())
                        ) {
                            AppOPC.is_valid_opc = true;
                        } else {
                            AppOPC.$opc_step_three.addClass('alert alert-warning');

                            Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.payment_method_required});

                            AppOPC.is_valid_opc = false;
                        }
                    }
                }
            }
        }

        if (AppOPC.is_valid_opc) {
            if (param.valid_condition) {
                AppOPC.$opc.find('#div_cgv').removeClass('alert alert-warning');
                AppOPC.$opc.find('#onepagecheckoutps_step_review_container #div_privacy_policy').css('padding-left','0px');

                //terms conditions
                if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_TERMS_CONDITIONS
                    && (AppOPC.$opc.find('#cgv').length > 0 && !AppOPC.$opc.find('#cgv').is(':checked'))
                ){
                    AppOPC.$opc.find('#div_cgv').addClass('alert alert-warning').css('padding-left','15px');
                    AppOPC.$opc.find('#onepagecheckoutps_step_review_container #div_privacy_policy').css('padding-left','15px');

                    Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_terms_and_conditions});

                    AppOPC.is_valid_opc = false;
                }
            }
        }

        if (AppOPC.is_valid_opc) {
            if (param.valid_privacy) {
                AppOPC.$opc.find('#div_privacy_policy').removeClass('alert alert-warning');
                AppOPC.$opc.find('#onepagecheckoutps_step_review_container #div_cgv').css('padding-left','0px');
                AppOPC.$opc.find('#checkbox_create_invoice_address').css('margin-left','0px');

                //privacy policy
                if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_PRIVACY_POLICY
                    && (!OnePageCheckoutPS.IS_LOGGED || (OnePageCheckoutPS.IS_LOGGED && OnePageCheckoutPS.CONFIGS.OPC_REQUIRE_PP_BEFORE_BUY))
                    && (AppOPC.$opc.find('#privacy_policy').length > 0 && !AppOPC.$opc.find('#privacy_policy').is(':checked'))
                ){
                    AppOPC.$opc.find('#div_privacy_policy').addClass('alert alert-warning').css('padding-left','15px');
                    AppOPC.$opc.find('#div_cgv').css('padding-left','15px');
                    AppOPC.$opc.find('#checkbox_create_invoice_address').css('margin-left','12px');


                    Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_privacy_policy});

                    AppOPC.is_valid_opc = false;
                }
            }
        }

        if (AppOPC.is_valid_opc) {
            if (param.valid_gdpr) {
                //GDPR - PrestaShop
                if (typeof message_psgdpr !== typeof undefined) {
                    AppOPC.$opc.find('#gdpr_consent').removeClass('alert alert-warning');

                    if (message_psgdpr && !AppOPC.$opc.find('#gdpr_consent_checkbox').is(':checked')){
                        AppOPC.$opc.find('#gdpr_consent').addClass('alert alert-warning');

                        Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_privacy_policy});

                        AppOPC.is_valid_opc = false;
                    }
                }
                //dm_gdpr - v1.1.7 - David Mrozek
                if (AppOPC.$opc.find('#dm_gdpr_active').length > 0){
                    AppOPC.$opc.find('#dm_gdpr_active').removeClass('alert alert-warning');

                    if (!AppOPC.$opc.find('#dm_gdpr_active').is(':checked')){
                        AppOPC.$opc.find('#dm_gdpr_active').parents('div#hook_create_account').addClass('alert alert-warning');
                        window.scrollTo(0, $('#dm_gdpr_active').parents('div#hook_create_account').offset().top);

                        Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_privacy_policy});

                        AppOPC.is_valid_opc = false;
                    }
                }
                //artfreegdpr - v1.0.7 - Arte e Informatica
                if (AppOPC.$opc.find('#freegdpr-consent').length > 0){
                    AppOPC.$opc.find('#freegdpr-consent').removeClass('alert alert-warning');

                    if (!AppOPC.$opc.find('#freegdpr-consent').is(':checked')){
                        AppOPC.$opc.find('#freegdpr-consent').parents('div#hook_create_account').addClass('alert alert-warning');
                        window.scrollTo(0, $('#freegdpr-consent').parents('div#hook_create_account').offset().top);

                        Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_privacy_policy});

                        AppOPC.is_valid_opc = false;
                    }
                }
            }
        }

        if (!AppOPC.is_valid_opc) {
            AppOPC.$opc.find('#btn_place_order').removeAttr('disabled');
        }
    }
}

var Address = {
    id_customer: 0,
    id_address_delivery: 0,
    id_address_invoice: 0,
    delivery_vat_number: false,
    invoice_vat_number: false,
    initEventsChangeCountry: function(object) {
        if ((object === 'delivery' && OnePageCheckoutPS.CONFIGS.OPC_INSERT_ISO_CODE_IN_DELIV_DNI)
            || (object === 'invoice' && OnePageCheckoutPS.CONFIGS.OPC_INSERT_ISO_CODE_IN_INVOI_DNI)
        ) {
            Address.insertCountryISOCode(object);
        }

        Address.isNeedDniByCountryId({object: object});
        Address.isNeedPostCodeByCountryId({object: object});
        Address.updateState({object: object});
        Address.initPostCodeGeonames({object: object});
    },
    launch: function(){
        var rc_page = AppOPC.$opc.find('#rc_page').val();

        if (OnePageCheckoutPS.IS_LOGGED) {
            Address.id_customer = AppOPC.$opc_step_one.find('#customer_id').val();
        }

        AppOPC.$opc_step_one
            .on('click', '.container_card .header_card, .container_card .content_card', function(item) {
                if (!$.isEmpty(rc_page)) {
                    return false;
                }

                var $addresses_customer_container = $(item.currentTarget).parents('.addresses_customer_container');
                var object = $addresses_customer_container.data('object');

                //Soporte a modulo: Carrier Pickup Store - PresTeamShop
                if (typeof id_carrierpickupstore !== typeof undefined
                    && AppOPC.$opc_step_two.find('.delivery_option_radio[value!="'+id_carrierpickupstore+',"]').length === 0
                    && object === 'delivery'
                ) {
                    return false;
                }

                if ($(item.currentTarget).parents('.container_card').hasClass('selected')) {
                    return;
                }

                var id_address = $(item.currentTarget).parents('.address_card').data('id-address');

                AppOPC.$opc_step_one.find('#'+object+'_id').val(id_address);

                if (object == 'delivery') {
                    Address.id_address_delivery = id_address;
                } else if (object == 'invoice') {
                    Address.id_address_invoice = id_address;
                }

                var callback = function () {
                    Address.updateAddress({object: object, id_address: id_address, update_cart: true, load_addresses: true, load_carriers: true});
                }
                Address.load({object: object, id_address: id_address, callback: callback});
            })
            .on('click', '.container_card .choose_address', function(item){
                var id_address = $(item.currentTarget).data('id-address');
                AppOPC.$opc_step_one.find('.addresses_customer_container #address_card_'+id_address+' .content_card').trigger('click');
            })
            .on('click', '#address_card_new_content span', function(item){
                var object = $(item.currentTarget).parents('.addresses_customer_container').data('object');

                AppOPC.$opc_step_one.find('#'+object+'_address_container #form_address_'+object).show(400);
                AppOPC.$opc_step_one.find('.addresses_customer_container.'+object).hide(400);

                Address.clearFormByObject(object);
            })
            .on('click', '.address_card .edit_address', function(item){
                var id_address = $(item.currentTarget).data('id-address');
                var object = $(item.currentTarget).parents('.addresses_customer_container').data('object');

                $(item.currentTarget).prop('disabled', true).addClass('disabled');

                AppOPC.$opc_step_one.find('#'+object+'_id').val(id_address);
                AppOPC.$opc_step_one.find('#'+object+'_address_container #form_address_'+object).show(400);
                AppOPC.$opc_step_one.find('.addresses_customer_container.'+object).hide(400);

                Address.load({object: object, id_address: id_address});
            })
            .on('click', '.address_card .delete_address', function(item){
                $(item.currentTarget).prop('disabled', true).addClass('disabled');

                var id_address = $(item.currentTarget).data('id-address');
                var object = $(item.currentTarget).parents('.addresses_customer_container').data('object');

                if (!Address.removeAddress({id_address: id_address, object: object})) {
                    $(item.currentTarget).prop('disabled', false).removeClass('disabled');
                }
            })
            .on('click', '#btn_update_address_delivery', function(){
                var callback = function() {
                    if (!OnePageCheckoutPS.IS_GUEST) {
                        AppOPC.$opc_step_one.find('#delivery_address_container #form_address_delivery').hide(400);
                        AppOPC.$opc_step_one.find('.addresses_customer_container.delivery').show(400);

                        Address.loadAddressesCustomer({object: 'delivery'});
                    }
                };

                if (AppOPC.$opc_step_one.find('#delivery_address_container .addresses_customer_container .address_card:not(#address_card_new)').length <= 0) {
                    Address.updateAddress({object: 'delivery', load_carriers: true, callback: callback, update_cart: true});
                } else {
                    var id_edited_address   = AppOPC.$opc_step_one.find('#panel_address_delivery #delivery_id').val();
                    var load_carriers       = false;

                    if (AppOPC.$opc_step_one.find('#panel_address_delivery .address_card[data-id-address="'+id_edited_address+'"] > .container_card').hasClass('selected')) {
                        load_carriers = true;
                    }
                    Address.updateAddress({object: 'delivery', load_carriers: load_carriers, callback: callback});
                }
            })
            .on('click', '#btn_update_address_invoice', function(){
                var callback = function() {
                    if (!OnePageCheckoutPS.IS_GUEST) {
                        AppOPC.$opc_step_one.find('#invoice_address_container #form_address_invoice').hide(400);
                        AppOPC.$opc_step_one.find('.addresses_customer_container.invoice').show(400);

                        Address.loadAddressesCustomer({object: 'invoice'});
                    }
                };

                if (AppOPC.$opc_step_one.find('#invoice_address_container .addresses_customer_container .address_card:not(#address_card_new)').length <= 0) {
                    Address.updateAddress({object: 'invoice', load_carriers: true, callback: callback, update_cart: true});
                } else {
                    var id_edited_address   = AppOPC.$opc_step_one.find('#panel_address_invoice #invoice_id').val();
                    var load_carriers       = false;

                    if (AppOPC.$opc_step_one.find('#panel_address_invoice .address_card[data-id-address="'+id_edited_address+'"] > .container_card').hasClass('selected')) {
                        load_carriers = true;
                    }

                    Address.updateAddress({object: 'invoice', load_carriers: load_carriers, callback: callback});
                }
            })
            .on('click', '#btn_cancel_address_delivery', function(){
                AppOPC.$opc_step_one.find('#delivery_address_container #form_address_delivery').hide(400);
                AppOPC.$opc_step_one.find('.addresses_customer_container.delivery').show(400);

                Address.clearFormByObject('delivery');
                Address.load({object: 'delivery'});
            })
            .on('click', '#btn_cancel_address_invoice', function(){
                AppOPC.$opc_step_one.find('#invoice_address_container #form_address_invoice').hide(400);
                AppOPC.$opc_step_one.find('.addresses_customer_container.invoice').show(400);

                Address.clearFormByObject('invoice');
                Address.load({object: 'invoice'});
            })
            .on('click', 'input#checkbox_create_account_guest', Address.checkGuestAccount)
            .on('click', 'input#checkbox_create_account', Address.checkGuestAccount)
            .on('click', 'input#checkbox_change_passwd', Address.checkGuestAccount)
            .on('keyup','.search_address',function (event){
                var text = $(event.currentTarget).val();

                if ($.isEmpty(text))
                    AppOPC.$opc_step_one.find('.addresses_customer_container .address_card').show();
                else {
                    AppOPC.$opc_step_one.find('.addresses_customer_container .container_card:ptsContains(' + text + ')').parents('.address_card:not(#address_card_new)').show();
                    AppOPC.$opc_step_one.find('.addresses_customer_container .container_card:not(:ptsContains(' + text + '))').parents('.address_card:not(#address_card_new)').hide();
                }
            })
            .on('blur', 'input#delivery_dni', function() {
                if (OnePageCheckoutPS.CONFIGS.OPC_INSERT_ISO_CODE_IN_DELIV_DNI) {
                    Address.insertCountryISOCode('delivery');
                }
            })
            .on('blur', 'input#invoice_dni', function() {
                if (OnePageCheckoutPS.CONFIGS.OPC_INSERT_ISO_CODE_IN_INVOI_DNI) {
                    Address.insertCountryISOCode('invoice');
                }
            });
        AppOPC.$opc
            .on('click', '#btn_save_customer', Address.createCustomer)
            .on('blur', '#customer_email', Address.checkEmailCustomer)
            .on("click", "#div_privacy_policy span.read", function(){
                Fronted.openCMS({id_cms : OnePageCheckoutPS.CONFIGS.OPC_ID_CMS_PRIVACY_POLICY});
            });

        $(document).on('blur', '#onepagecheckoutps input[data-field-name="address1"]', Address.cleanSpecialCharacterAddress);

        if (!OnePageCheckoutPS.IS_LOGGED) {
            AppOPC.$opc_step_one.find('#delivery_address_container #form_address_delivery').show();
            AppOPC.$opc_step_one.find('#invoice_address_container #form_address_invoice').show();

            AppOPC.$opc_step_one.find('.addresses_customer_container').hide();
        }

        Address.checkGuestAccount();

        $('div#onepagecheckoutps #field_customer_id').addClass('hidden');

        //just allow lang with weird characters
        if ($.inArray(OnePageCheckoutPS.LANG_ISO, OnePageCheckoutPS.LANG_ISO_ALLOW) == 0) {
            $('#customer_firstname, #customer_lastname').validName();
        }

        //evita espacios al inicio y final en los campos del registro.
        AppOPC.$opc_step_one.find('input.customer, input.delivery, input.invoice, #customer_conf_passwd, #customer_conf_email').on('paste', function(e){
            var $element = $(e.currentTarget);
            setTimeout(function () {
                $element.val($.trim($element.val()));
            }, 100);
        });

        AppOPC.$opc_step_one.find('.container_help_invoice u').click(function(){
            $('#onepagecheckoutps_step_one #li_invoice_address a').trigger('click');
        });

        //validation brazil cpf
        if ($('#onepagecheckoutps_step_one #br_document_cpf').length > 0) {
            $('#onepagecheckoutps_step_one #br_document_cpf').attr('data-validation', 'cpf').removeClass('validate');
        }

        //support module: rg_chilexpress v1.4.1 (Rolige)
        if (typeof rg_chilexpress !== typeof undefined && typeof rg_chilexpress.cities !== typeof undefined && rg_chilexpress.cities.length > 0) {
            $('#delivery_city').prop('autocomplete', 'off');
            $('#delivery_city').typeahead(
            {
                name: $(this).attr('id'),
                local: {},
                showHintOnFocus: false,
                source: function (query, process) {
                    process($.map(rg_chilexpress.cities, function (item) {
                        return [{
                            name: item.name,
                            value: item.name,
                            details: item
                        }];
                    }));
                },
                minLength: 2,
                updater: function (item) {
                    return item.value;
                }
            });
        //support module: rg_starken v1.0.4 (Rolige)
        } else if (typeof rg_starken !== typeof undefined && typeof rg_starken.cities !== typeof undefined && rg_starken.cities.length > 0) {
            $('#delivery_city').prop('autocomplete', 'off');
            $('#delivery_city').typeahead(
            {
                name: $(this).attr('id'),
                local: {},
                showHintOnFocus: false,
                source: function (query, process) {
                    process($.map(rg_starken.cities, function (item) {
                        return [{
                            name: item.name,
                            value: item.name,
                            details: item
                        }];
                    }));
                },
                minLength: 2,
                updater: function (item) {
                    return item.value;
                }
            });
        }

        //suppport module: ets_advancedcaptcha - v1.1.3 - ETS-Soft.
        if (typeof ets_captcha_load !== typeof undefined) {
            ets_captcha_load($('#hook_create_account'));
        }

        //support module: psgdpr - v1.0.0
        AppOPC.$opc_step_one.find('#hook_create_account #psgdpr-consent').parents('.form-group').remove();

        Address.load({object: 'customer'});
        Address.loadAutocompleteAddress();

        if (OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL || !OnePageCheckoutPS.IS_VIRTUAL_CART) {
            $('div#onepagecheckoutps #delivery_postcode').validPostcode();
            if ($.inArray(OnePageCheckoutPS.LANG_ISO, OnePageCheckoutPS.LANG_ISO_ALLOW) == 0){
                $('div#onepagecheckoutps #delivery_firstname, div#onepagecheckoutps #delivery_lastname').validName();
                $('div#onepagecheckoutps #delivery_address1, div#onepagecheckoutps #delivery_address2, div#onepagecheckoutps #delivery_city').validAddress();
            }

            $('div#onepagecheckoutps #field_delivery_id').addClass('hidden');

            Address.initPostCodeGeonames({object: 'delivery'});

            $('div#onepagecheckoutps')
                .on('change', '#delivery_city', function(){
                    $('#delivery_city_list').val('');
                })
                .on('change', 'select#delivery_id_state', function(event){
                    if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_LIST_CITIES_GEONAMES) {
                        AppOPC.$opc_step_one.find('#delivery_city').val('').trigger('reset');
                    }

                    Address.getCitiesByState({object: 'delivery'});

                    $(event.currentTarget).validate();

                    if (!OnePageCheckoutPS.IS_LOGGED) {
                        Address.updateAddress({object: 'delivery', load_carriers: true});
                    }
                })
                .on('change', 'select#delivery_id_country', function(event){
                    Address.initEventsChangeCountry('delivery');

                    if (typeof event.originalEvent !== typeof undefined && AppOPC.$opc.find('input#delivery_postcode').length > 0 && !$.isEmpty(AppOPC.$opc.find('input#invoice_postcode').val())) {
                        AppOPC.$opc.find('input#delivery_postcode').validate();
                    }

                    if (!OnePageCheckoutPS.IS_LOGGED) {
                        Address.updateAddress({object: 'delivery', load_carriers: true});
                    }
                });

            if (!OnePageCheckoutPS.IS_LOGGED || OnePageCheckoutPS.IS_GUEST) {
                var callback = function () {
                    Address.updateAddress({
                        object: 'delivery',
                        load_carriers: true
                    });
                }
                Address.load({object: 'delivery', callback: callback});
            } else {
                var callback = function() {
                    Carrier.getByCountry();
                };
                Address.loadAddressesCustomer({object: 'delivery', callback: callback});
            }
        }

        if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS) {
            if (OnePageCheckoutPS.CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS && !OnePageCheckoutPS.IS_LOGGED && !OnePageCheckoutPS.IS_GUEST) {
                Address.updateAddress({object: 'invoice', update_cart: true});
            }

            if (typeof $.totalStorageOPC !== typeof undefined) {
                if ($.totalStorageOPC('create_invoice_address_'+OnePageCheckoutPS.id_shop)) {
                    $('div#onepagecheckoutps #checkbox_create_invoice_address').attr('checked', 'true');
                }
            }

            $('div#onepagecheckoutps #invoice_postcode').validPostcode();
            if ($.inArray(OnePageCheckoutPS.LANG_ISO, OnePageCheckoutPS.LANG_ISO_ALLOW) == 0){
                $('div#onepagecheckoutps #invoice_firstname, div#onepagecheckoutps #invoice_lastname').validName();
                $('div#onepagecheckoutps #invoice_address1, div#onepagecheckoutps #invoice_address2, div#onepagecheckoutps #invoice_city').validAddress();
            }

            $('div#onepagecheckoutps #field_invoice_id').addClass('hidden');

            $('div#onepagecheckoutps').on('click', 'input#checkbox_create_invoice_address', function(event){
                Address.checkNeedInvoice();

                if ($(event.currentTarget).is(':checked') && !OnePageCheckoutPS.IS_LOGGED) {
                    Address.updateAddress({object: 'invoice', update_cart: true});
                } else {
                    Address.removeAddressInvoice();
                }
            });

            Address.checkNeedInvoice();
            Address.initPostCodeGeonames({object: 'invoice'});

            $('div#onepagecheckoutps')
                .on('change', '#invoice_city', function(){
                    $('#invoice_city_list').val('');
                })
                .on('change', 'select#invoice_id_state', function(event){
                    if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_LIST_CITIES_GEONAMES) {
                        AppOPC.$opc_step_one.find('#invoice_city').val('').trigger('reset');
                    }

                    Address.getCitiesByState({object: 'invoice'});

                    $(event.currentTarget).validate();
                })
                .on('change', 'select#invoice_id_country', function(event){
                    Address.initEventsChangeCountry('invoice');

                    if (typeof event.originalEvent !== typeof undefined && AppOPC.$opc.find('input#invoice_postcode').length > 0 && !$.isEmpty(AppOPC.$opc.find('input#invoice_postcode').val())) {
                        AppOPC.$opc.find('input#invoice_postcode').validate();
                    }

                    if (!OnePageCheckoutPS.IS_LOGGED && OnePageCheckoutPS.PS_TAX_ADDRESS_TYPE == 'id_address_invoice') {
                        Address.updateAddress({object: 'invoice', load_payments: true});
                    }
                });

            if (!OnePageCheckoutPS.IS_LOGGED || OnePageCheckoutPS.IS_GUEST) {
                Address.load({object: 'invoice'});
            }
        }

        if (OnePageCheckoutPS.IS_VIRTUAL_CART && !OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL) {
            PaymentPTS.getByCountry();
        }
    },
    insertCountryISOCode: function(type) {
        var input   = AppOPC.$opc_step_one.find('input#'+type+'_dni');
        if (input.length === 0) {
            return false;
        }

        var value   = input.val();
        var country_prefix = AppOPC.$opc_step_one.find('select#' + type + '_id_country > option:selected').data('iso-code');

        if (typeof country_prefix === typeof undefined) {
            country_prefix = '';
        }

        if (value.length > 1) {
            var current_country_iso_code    = $.totalStorageOPC(type + '_current_country_iso_code');

            if (!$.isEmpty(current_country_iso_code) && value.indexOf(current_country_iso_code) >= 0) {
                value = value.replace(current_country_iso_code, country_prefix);
            } else {
                value = country_prefix + value;
            }

            $.totalStorageOPC(type + '_current_country_iso_code', country_prefix);
            input.val(value);
        }
    },
    initPostCodeGeonames: function(params){
        var param = $.extend({}, {
            object: 'delivery'
        }, params);

        if (OnePageCheckoutPS.CONFIGS.OPC_AUTO_ADDRESS_GEONAMES && AppOPC.$opc_step_one.find('#'+param.object+'_postcode').length > 0){
            var $id_country = $('#onepagecheckoutps_step_one #'+param.object+'_id_country');
            var iso_code_country = '';

            if ($id_country.length > 0) {
                iso_code_country = $id_country.find('option:selected').data('iso-code');
            } else {
                iso_code_country = OnePageCheckoutPS.iso_code_country_delivery_default;
            }

            $('#onepagecheckoutps_step_one #'+param.object+'_postcode').jeoPostCodeAutoComplete({
                country: iso_code_country,
                callback: function(data){
                    $('#onepagecheckoutps_step_one #'+param.object+'_postcode').val(data.postalCode);
                    $('#onepagecheckoutps_step_one #'+param.object+'_city_list').val(data.name);
                    $('#onepagecheckoutps_step_one #'+param.object+'_city').val(data.name);

                    if ($('#onepagecheckoutps_step_one #'+param.object+'_id_state [data-text="'+data.adminName2+'"]').length <= 0) {
                        $('#onepagecheckoutps_step_one #'+param.object+'_id_state [data-iso-code="'+data.countryCode + '-' + data.adminCode2+'"]').attr('selected', 'true');
                    } else {
                        $('#onepagecheckoutps_step_one #'+param.object+'_id_state [data-text="'+data.adminName2+'"]').attr('selected', 'true');
                    }

                    if (typeof is_necessary_postcode !== typeof undefined && is_necessary_postcode) {
                        $('#onepagecheckoutps_step_one #'+param.object+'_postcode').trigger('blur');
                    } else if(typeof is_necessary_city !== typeof undefined && is_necessary_city) {
                        $('#onepagecheckoutps_step_one #'+param.object+'_city').trigger('blur');
                    }

                    if (typeof is_necessary_postcode !== typeof undefined
                        && !is_necessary_postcode
                        && typeof is_necessary_postcode !== typeof undefined
                        && !is_necessary_postcode)
                    {
                        $('#onepagecheckoutps_step_one #'+param.object+'_id_state [data-text="'+data.adminName2+'"]').trigger('change');
                    }
                }
            });
        }
    },
    getCityByPostCode: function(params){
        var param = $.extend({}, {
            object: 'delivery'
        }, params);

        if (1==2) {
            var $city_list = $('#onepagecheckoutps_step_one #'+param.object+'_city_list');

            if ($city_list.length <= 0 || ($city_list.length > 0 && !$city_list.is(':visible'))) {
                var $id_country = $('#onepagecheckoutps_step_one #'+param.object+'_id_country');
                var $postcode = $('#onepagecheckoutps_step_one #'+param.object+'_postcode');
                var $city = $('#onepagecheckoutps_step_one #'+param.object+'_city');

                if ($postcode.length > 0 && $city.length > 0) {
                    $postcode.jeoPostalCodeLookup({
                        country: $id_country.find('option:selected').data('iso-code'),
                        target: $city
                    });
                }
            }
        }
    },
    getCitiesByState: function(params){
        var param = $.extend({}, {
            object: 'delivery'
        }, params);

        if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_LIST_CITIES_GEONAMES) {
            var $id_country = AppOPC.$opc_step_one.find('#'+param.object+'_id_country');
            var $id_state = AppOPC.$opc_step_one.find('#'+param.object+'_id_state');
            var iso_code_country = '';

            if ($id_country.length > 0) {
                iso_code_country = $id_country.find('option:selected').data('iso-code');
            } else {
                iso_code_country = OnePageCheckoutPS.iso_code_country_delivery_default;
            }

            var name_state = $.trim($id_state.find('option:selected').data('text'));

            if ($id_state.length > 0 && !$.isEmpty(name_state)) {
                var cities = Array();

                jeoquery.getGeoNames(
                  'search',
                  {
                      q: name_state,
                      country: iso_code_country,
                      featureClass: 'P',
                      style: 'full'
                  },
                  function(data){
                    //ordenar array de objetos por una propiedad en especifico
                    function dynamicSort(property) {
                        var sortOrder = 1;
                        if(property[0] === "-") {
                            sortOrder = -1;
                            property = property.substr(1);
                        }
                        return function (a,b) {
                            var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
                            return result * sortOrder;
                        }
                    }

                    $.each(data.geonames, function(i, item){
                        if ($.inArray(item.name, cities) == -1) {
                            cities.push({name: $.trim(item.name), postcode: item.adminCode3});
                        }
                    });
                    cities.sort(dynamicSort('name'));

                    var $city_list = AppOPC.$opc_step_one.find('#'+param.object+'_city_list');
                    if ($city_list.length <= 0) {
                        $city_list = $('<select/>')
                            .attr({
                                id: param.object+'_city_list',
                                class: 'form-control input-sm not_unifrom not_uniform'
                            })
                            .on('change', function(event){
                                var option_selected = $(event.currentTarget).find('option:selected');

                                AppOPC.$opc_step_one.find('#'+param.object+'_city').val($(option_selected).attr('value')).trigger('blur');
                                AppOPC.$opc_step_one.find('#'+param.object+'_postcode').val($(option_selected).attr('data-postcode')).validate();
                            }
                        );
                    } else {
                        $city_list.empty().hide();
                    }

                    var current_city = AppOPC.$opc_step_one.find('#'+param.object+'_city').val();

                    var $option = $('<option/>')
                        .attr({
                            value: ''
                        }).append('--');
                    $option.appendTo($city_list);
                    $.each(cities, function(i, city) {
                        var $option = $('<option/>')
                            .attr({
                                'value': city.name,
                                'data-postcode': city.postcode
                            }).append(city.name);

                        $option.appendTo($city_list);
                    });

                    AppOPC.$opc_step_one.find('#field_'+param.object+'_city').append($city_list);

                    //si existe la ciudad, la seleccionamos.
                    if ($city_list.find('option[value="'+current_city+'"]').length > 0) {
                        $city_list.val(current_city);
                    }

                     $city_list.show();
                });
            } else {
                AppOPC.$opc_step_one.find('#'+param.object+'_city_list').hide();
            }
        }
    },
    loadAddressesCustomer: function(params){
        var param = $.extend({}, {
            object: 'delivery',
            callback: ''
        }, params);

        if (OnePageCheckoutPS.IS_LOGGED) {
            if (param.object == 'delivery') {
                if (OnePageCheckoutPS.IS_VIRTUAL_CART && !OnePageCheckoutPS.CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL) {
                    return false;
                }
            }
            if (param.object == 'invoice') {
                if (!Address.isSetInvoice()) {
                    return false;
                }
            }

            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'loadAddressesCustomer',
                object: param.object,
                rc_page: AppOPC.$opc.find('#rc_page').val()
            };
            var _json = {
                data: data,
                success: function(json) {
                    var callback = null;

                    if(typeof json.html !== typeof undefined) {
                        AppOPC.$opc_step_one.find('.addresses_customer_container.'+param.object).html(json.html).show(400);
                        AppOPC.$opc_step_one.find('#'+param.object+'_address_container #form_address_'+param.object).hide(400);
                    }

                    if (param.object == 'delivery') {
                        Address.id_address_delivery = json.id_address;
                        AppOPC.$opc_step_one.find('#delivery_id').val(json.id_address);

                        callback = function() {
                            if (json.addresses.length > 0) {
                                Fronted.validateOPC({valid_form_address_delivery: true});

                                if (!AppOPC.is_valid_form_address_delivery) {
                                    AppOPC.$opc_step_one.find('#'+param.object+'_address_container #form_address_'+param.object).show(400);
                                    AppOPC.$opc_step_one.find('.addresses_customer_container.'+param.object).hide(400);
                                }
                            }
                        }
                    } else if (param.object == 'invoice') {
                        Address.id_address_invoice = json.id_address;
                        AppOPC.$opc_step_one.find('#invoice_id').val(json.id_address);

                        callback = function() {
                            if (json.addresses.length > 0) {
                                Fronted.validateOPC({valid_form_address_invoice: true});

                                if (!AppOPC.is_valid_form_address_invoice) {
                                    AppOPC.$opc_step_one.find('#'+param.object+'_address_container #form_address_'+param.object).show(400);
                                    AppOPC.$opc_step_one.find('.addresses_customer_container.'+param.object).hide(400);
                                }
                            }
                        }
                    }

                    if (json.id_address !== 0) {
                        Address.load({object: param.object, id_address: json.id_address/*, callback: callback*/});
                    }
                },
                complete: function() {
                    setTimeout(function() {
                        //calcula el alto del contenido de las tarjetas para colocar colocarles un alto igual a todas.
                        //-----------------------------------------------
                        var height_card_max = 0;
                        $.each($('.addresses_customer_container .address_card:not(#address_card_new) .content_card ul'), function(i, card) {
                            var height_card = $(card).innerHeight() + 5;

                            if (height_card_max < height_card) {
                                height_card_max = height_card;
                            }
                        });
                        $('.addresses_customer_container .address_card:not(#address_card_new) .content_card').css({'height': height_card_max});
                        //-----------------------------------------------
                        //calcula el alto del titulo de las tarjetas para colocar colocarles un alto igual a todas.
                        //-----------------------------------------------
                        var height_header_card_max = 0;
                        $.each($('.addresses_customer_container .address_card:not(#address_card_new) .header_card'), function(i, header_card) {
                            var height_header_card = $(header_card).innerHeight();

                            if (height_header_card_max < height_header_card) {
                                height_header_card_max = height_header_card;
                            }
                        });
                        $('.addresses_customer_container .address_card:not(#address_card_new) .header_card').css({'height': height_header_card_max});
                        //-----------------------------------------------
                    }, 800);

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function') {
                        param.callback();
                    }
                }
            };
            $.makeRequest(_json);
        } else {
            if (typeof param.callback !== typeof undefined && typeof param.callback === 'function') {
                param.callback();
            }
        }

        return true;
    },
    createCustomer: function(){
        Fronted.validateOPC({valid_form_customer: true, valid_privacy: true, valid_gdpr: true, valid_form_address_delivery: true, valid_form_address_invoice: true});

        if (AppOPC.is_valid_opc){
            var fields = Review.getFields();

            var _extra_data = Review.getFieldsExtra({});
            var _data = $.extend({}, _extra_data, {
                'url_call'				: orderOpcUrl + '?rand=' + new Date().getTime(),
                'is_ajax'               : true,
                'dataType'              : 'json',
                'action'                : (OnePageCheckoutPS.IS_LOGGED ? 'placeOrder' : 'createCustomerAjax'),
                'id_customer'           : (!$.isEmpty(AppOPC.$opc_step_one.find('#customer_id').val()) ? AppOPC.$opc_step_one.find('#customer_id').val() : ''),
                'id_address_delivery'   : Address.id_address_delivery,
                'id_address_invoice'    : !$.isEmpty(Address.id_address_invoice) ? Address.id_address_invoice : Address.id_address_delivery,
                'is_new_customer'       : (AppOPC.$opc_step_one.find('#checkbox_create_account_guest').is(':checked') ? 0 : 1),
                'fields_opc'            : JSON.stringify(fields),
                'is_set_invoice'        : Address.isSetInvoice(),
                'psgdpr-consent'        : true,//support module: psgdpr - v1.0.0 - PrestaShop
                'g_recaptcha_response'  : $('#g-recaptcha-response').val(),//support module: recaptcha - v1.2.2 - Charlie
                'g-recaptcha-response'  : $('#g-recaptcha-response').val()//support module: recaptcha - v1.2.3 - Charlie
            });

            var _json = {
                data: _data,
                beforeSend: function() {
                    Fronted.loading(true, '#onepagecheckoutps_step_one_container');
                },
                success: function(data) {
                    if (typeof data.token !== typeof undefined && typeof token !== typeof undefined) {
                        token = data.token;
                    }
                    if (typeof data.static_token !== typeof undefined && typeof static_token !== typeof undefined) {
                        static_token = data.static_token;
                    }

                    if (data.isSaved && (!OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED || $('#checkbox_create_account_guest').is(':checked'))){
                        AppOPC.$opc_step_one.find('#customer_id').val(data.id_customer);
                        AppOPC.$opc_step_one.find('#customer_email, #customer_conf_email, #customer_passwd, #customer_conf_passwd').attr({'disabled': 'true', 'data-validation-optional' : 'true'});

                        $('#div_onepagecheckoutps_login, #field_customer_passwd, #field_customer_conf_passwd, #field_customer_email, #field_customer_conf_email, div#onepagecheckoutps #onepagecheckoutps_step_one_container .account_creation, #field_choice_group_customer').addClass('hidden');
                    }

                    if (data.hasError){
                        Fronted.showModal({type:'error', message : '&bullet; ' + data.errors.join('<br>&bullet; ')});
                    } else if (data.hasWarning){
                        Fronted.showModal({type:'warning', message : '&bullet; ' + data.warnings.join('<br>&bullet; ')});
                    } else if (typeof data.redirect !== typeof undefined) {
                        window.parent.location = data.redirect;
                    } else{
                        if (!OnePageCheckoutPS.IS_LOGGED || OnePageCheckoutPS.IS_GUEST) {
                            var $shipping_cart = $('.shopping_cart .ajax_cart_quantity');

                            if ($shipping_cart.length <= 0) {
                                $shipping_cart = $('#shopping_cart .ajax_cart_quantity');
                            }

                            /* Compatibilidad thema leodigital version 1.0 */
                            if ($shipping_cart.length <= 0) {
                                $shipping_cart = $('#top-sliding-cart .ajax_cart_quantity');
                            }
                            /* End */

                            if (parseInt($shipping_cart.text()) > 0){
                                var orderOpcUrlTMP = orderOpcUrl;
                                if (OnePageCheckoutPS.CONFIGS.OPC_REDIRECT_DIRECTLY_TO_OPC) {
                                    orderOpcUrlTMP += '?checkout=1';
                                }

                                window.parent.location = orderOpcUrlTMP;
                            } else {
                                window.parent.location = myaccountUrl;
                            }

                            $('div#onepagecheckoutps #btn_save_customer').attr('disabled', 'true');
                        } else {
                            location.reload();
                            /*
                            Address.loadAddressesCustomer({object: 'delivery'});
                            Address.loadAddressesCustomer({object: 'invoice'});
                            */
                        }
                    }
                },
                complete: function(){
                    Fronted.loading(false, '#onepagecheckoutps_step_one_container');
                    Fronted.loadingBig(false);
                }
            };

            var callback = function() {
                $.makeRequest(_json);
            }
            supportModuleGDPR(callback);
        }
    },
    load: function(params){
        var param = $.extend({}, {
            object: '',
            id_address: false,
            callback: ''
        }, params);

        if (param.object == 'customer') {
            if (!OnePageCheckoutPS.IS_LOGGED && !OnePageCheckoutPS.IS_GUEST) {
                return false;
            }
        }
        if (param.object == 'delivery') {
            if (OnePageCheckoutPS.IS_VIRTUAL_CART && !OnePageCheckoutPS.CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL) {
                return false;
            }
        }
        if (param.object == 'invoice') {
            if (!Address.isSetInvoice()) {
                return false;
            }
        }

        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
            action: 'loadAddress',
            object: param.object,
            id_address: param.id_address
        };
        var _json = {
            data: data,
            beforeSend: function() {},
            success: function(json) {
                if(!$.isEmpty(json.customer.id) || !$.isEmpty(json.address.id)) {
                    Address.id_customer = $.isEmpty(json.customer.id) ? 0 : json.customer.id;

                    if (param.object == 'delivery') {
                        Address.id_address_delivery = $.isEmpty(json.address.id) ? 0 : json.address.id;
                    } else if (param.object == 'invoice') {
                        Address.id_address_invoice = $.isEmpty(json.address.id) ? 0 : json.address.id;

                        if (!OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL && OnePageCheckoutPS.IS_VIRTUAL_CART) {
                            Address.id_address_delivery = Address.id_address_invoice;
                        }
                    }

                    if (OnePageCheckoutPS.IS_LOGGED || OnePageCheckoutPS.IS_GUEST) {
                        var object_load = '.'+param.object+',.customer';

                        //load customer, delivery or invoice data
                        $('div#onepagecheckoutps #onepagecheckoutps_step_one').find(object_load).each(function(i, field){
                            var $field = $(field);
                            var name = $field.data('field-name');
                            var default_value = $field.data('default-value');
                            var object = '';

                            if ($field.hasClass('customer')){
                                var value = json.customer[name];
                                object = 'customer';
                            }else if ($field.hasClass('delivery')){
                                var value = json.address[name];
                                object = 'delivery';
                            }else if ($field.hasClass('invoice')){
                                var value = json.address[name];
                                object = 'invoice';
                            }

                            if (object === 'customer' && name === 'passwd') {
                                return;
                            }

                            if (object == 'invoice' && !OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS){
                                AppOPC.$opc_step_one.find('#invoice_id').val('');

                                return;
                            }

                            if (value == '0000-00-00') {
                                value = '';
                            }

                            if ($field.is(':checkbox')){
                                if (parseInt(value)) {
                                    $field.attr('checked', 'true');
                                } else {
                                    $field.removeAttr('checked');
                                }
                            } else if ($field.is(':radio')){
                                if ($field.val() == value) {
                                    $field.attr('checked', 'true');
                                }
                            } else {
                                if (name == 'birthday'){
                                    if (!$.isEmpty(value)) {
                                        var date_value = value.split('-');
                                        var date_string = OnePageCheckoutPS.date_format_language.replace('dd', date_value[2]);
                                        date_string = date_string.replace('mm', date_value[1]);
                                        date_string = date_string.replace('yy', date_value[0]);

                                        $field.val(date_string);
                                    }
                                }else if (name != 'email') {
                                    $field.val(value);
                                }

                                //do not show values by default on input text
                                if ($field.is(':text')) {
                                    if (value == default_value) {
                                        $field.val('');
                                    }
                                }
                            }

                            if (name == 'email'){
                                if (OnePageCheckoutPS.IS_LOGGED) {
                                    $field.attr('disabled', 'true');
                                } else {
                                    $('div#onepagecheckoutps #onepagecheckoutps_step_one #customer_conf_email').val($field.val());
                                }
                            }
                        });
                    }

                    if (param.object == 'delivery' || param.object == 'invoice') {
                        Address.isNeedDniByCountryId({object: param.object});
                        Address.isNeedPostCodeByCountryId({object: param.object});

                        if (OnePageCheckoutPS.IS_LOGGED || OnePageCheckoutPS.IS_GUEST) {
                            Address.updateState({object: param.object, id_state_default: json.address['id_state']});
                        } else {
                            Address.updateState({object: param.object});
                        }
                    }
                } else {
                    Address.isNeedDniByCountryId({object: param.object});
                    Address.isNeedPostCodeByCountryId({object: param.object});
                    Address.updateState({object: param.object});
                }
            },
            complete: function(){
                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function') {
                    param.callback();
                }
            }
        };
        $.makeRequest(_json);
    },
    loadAutocompleteAddress: function() {
        if (OnePageCheckoutPS.CONFIGS.OPC_AUTOCOMPLETE_GOOGLE_ADDRESS && !$.isEmpty(OnePageCheckoutPS.CONFIGS.OPC_GOOGLE_API_KEY) && typeof google.maps.places !== typeof undefined) {
            if ($('#delivery_address1').length > 0)
            {
                Address.autocomplete_delivery = new google.maps.places.Autocomplete(
                    (document.getElementById('delivery_address1')),
                    {types: ['geocode']}
                );
                google.maps.event.addListener(Address.autocomplete_delivery, 'place_changed', function() {
                    Address.fillInAddress('delivery', Address.autocomplete_delivery);
                });
            }

            if ($('#invoice_address1').length > 0)
            {
                Address.autocomplete_invoice = new google.maps.places.Autocomplete(
                    (document.getElementById('invoice_address1')),
                    {types: ['geocode']}
                );

                google.maps.event.addListener(Address.autocomplete_invoice, 'place_changed', function() {
                    Address.fillInAddress('invoice', Address.autocomplete_invoice);
                });
            }
        }
    },
    fillInAddress: function(address, autocomplete) {
        Address.componentForm = {
            administrative_area_level_1: {index: 0, type: 'select', field: address + '_id_state'},
            administrative_area_level_2: {index: 1, type: 'select', field: address + '_id_state'},
            administrative_area_level_3: {index: 2, type: 'select', field: address + '_id_state'},
            country: {index: 3, type: 'select', field: address + '_id_country'},
            locality: {index: 4, type: 'long_name', field: address + '_city'},
            postal_code: {index: 5, type: 'long_name', field: address + '_postcode'},
            street_number: {index: 6, type: 'short_name', field: address + '_address1'},
            route: {index: 7, type: 'long_name', field: address + '_address1'},
            premise: {index: 8, type: 'short_name', field: address + '_address1'}
        };

        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();
        //reset
        $.each(Address.componentForm, function(c, component) {
            if (component.type !== 'select' && component.field != (address + '_address1')) {
                $('#' + component.field).val('');
            }
        });

        var components = [];
        var found_address = false;
        var components_state = [];

        $.each(place.address_components, function(a, component) {
            if (typeof Address.componentForm[component.types[0]] !== typeof undefined) {
                var field = Address.componentForm[component.types[0]].field;
                var type = Address.componentForm[component.types[0]].type;
                var index = Address.componentForm[component.types[0]].index;

                if (component.types[0] == 'street_number' || component.types[0] == 'route' || component.types[0] == 'administrative_area_level_3') {
                    found_address = true;
                }

                components[index] = {
                    field: field,
                    type: type,
                    name: component.types[0],
                    short_name: component.short_name,
                    long_name: component.long_name,
                    value: (typeof component[type] !== typeof undefined) ? component[type] : component.long_name
                };
            }
        });

        $.each(components, function(c, component) {
            if (typeof component !== typeof undefined) {
                if (component.type === 'select') {
                    if (component.name === 'country') {
                        $('#' + address + '_id_country option').prop('selected', false);
                        $('#' + address + '_id_country option[data-iso-code="' + component.short_name + '"]').prop('selected', true);
                        $('#' + address + '_id_country').trigger('change');
                    } else if (typeof $('#' + address + '_id_state')[0] !== typeof undefined) {
                        components_state.push(component)

                        Address.callBackState = function() {
                            var id_state = '';

                            $.each(components_state, function(c, component_state) {
                                if ($('#' + address + '_id_state option[data-iso-code="' + component_state.short_name + '"]').length > 0) {
                                    id_state = $('#' + address + '_id_state option[data-iso-code="' + component_state.short_name + '"]').val();

                                    return false;
                                }else if ($('#' + address + '_id_state option[data-text="' + component_state.value + '"]').length > 0) {
                                    id_state = $('#' + address + '_id_state option[data-text="' + component_state.value + '"]').val();

                                    return false;
                                }
                            });
                            if (!$.isEmpty(id_state)) {
                                $('#' + address + '_id_state option').prop('selected', false);
                                $('#' + address + '_id_state').val(id_state);
                            }
                        }
                    }
                } else {
                    var tmp_value = $('#' + component.field).val();

                    if (component.field == (address + '_address1') && !$.isEmpty(tmp_value)) {
                        if (OnePageCheckoutPS.CONFIGS.OPC_SUGGESTED_ADDRESS_GOOGLE) {
                            $('#' + address + '_address1').val(tmp_value);
                        } else {
                            $('#' + address + '_address1').val(place.name);
                        }
                    } else {
                        $('#' + component.field).val(component.value).validate();
                    }
                }
            }
        });

        if (!found_address) {
            $('#' + address + '_address1').val(place.name);
        }

        //dispatch inputs events
        if (typeof is_necessary_postcode !== typeof undefined && is_necessary_postcode) {
            $('#onepagecheckoutps_step_one #'+address+'_postcode').trigger('blur');
        } else if(typeof is_necessary_city !== typeof undefined && is_necessary_city) {
            $('#onepagecheckoutps_step_one #'+address+'_city').trigger('blur');
        }
    },
    geolocate: function(event) {
        $(event.currentTarget).off('focus');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = new google.maps.LatLng(
                position.coords.latitude, position.coords.longitude);
                autocomplete.setBounds(new google.maps.LatLngBounds(geolocation,
                    geolocation));
            });
        }
    },
    updateState: function(params) {
        var param = $.extend({}, {
            object: '',
            id_state_default: '',
            id_country: ''
        }, params);

        var states = null;
        if (!$.isEmpty(param.object)) {
            var $id_country = $('div#onepagecheckoutps select#' + param.object + '_id_country');
            var $id_state = $('div#onepagecheckoutps select#' + param.object + '_id_state');
            var id_country = null;

            if ($id_country.length > 0) {
                id_country = $id_country.val();
            } else {
                if (param.object == 'delivery') {
                    id_country = OnePageCheckoutPS.id_country_delivery_default;
                } else if (param.object == 'invoice') {
                    id_country = OnePageCheckoutPS.id_country_invoice_default;
                }
            }

			var states = countriesJS[id_country];

            //delete states
            $id_state.find('option').remove();

            if (!$.isEmpty(states)) {
                //empty option
                var $option = $('<option/>')
                    .attr({
                        value: '',
                    }).append('--');
                 $option.appendTo($id_state);

                $.each(states, function(i, state) {
                    var $option = $('<option/>')
                        .attr({
                            'data-text': state.name,
                            'data-iso-code': state.iso_code,
                            value: state.id,
                        }).append(state.name);

                    if (param.id_state_default == state.id) {
                        $option.attr('selected', 'true');
                    }

                    $option.appendTo($id_state);
                });

                if (typeof Address.callBackState === 'function') {
                    Address.callBackState();
                } else {
                    //auto select state.
                    if ($.isEmpty($id_state.find('option:selected').val())){
                        var default_value = $id_state.attr('data-default-value');

                        if (default_value === '0' || (!$.isEmpty(default_value) && $id_state.find('option[value='+default_value+']').length <= 0)) {
                            $id_state.find(':eq(1)').attr('selected', 'true');
                        } else if ($.isEmpty(default_value)) {
                            $id_state.find(':eq(0)').attr('selected', 'true');
                        } else {
                            if (typeof forze_select_state !== typeof undefined && forze_select_state) {
                                $id_state.val(default_value);
                            }
                        }
                    }
                }

                if (param.object == 'delivery' || (param.object == 'invoice' && Address.isSetInvoice())) {
                    $id_state.attr('data-validation', 'required').addClass('required');
                }
                $('div#onepagecheckoutps #field_' + param.object + '_id_state').find('sup').html('*');
                $('div#onepagecheckoutps #field_' + param.object + '_id_state').show();
            } else {
                $id_state.removeAttr('data-validation').removeClass('required');
                $('div#onepagecheckoutps #field_' + param.object + '_id_state').find('sup').html('');
                $('div#onepagecheckoutps #field_' + param.object + '_id_state').hide();
            }

            Address.getCitiesByState({object: param.object});
        }
    },
    checkNeedInvoice: function(params){
        var param = $.extend({}, {
        }, params);

        if (Address.isSetInvoice()) {
            Address.isNeedDniByCountryId({object: 'invoice'});
            Address.updateState({object: 'invoice'});
            Address.loadAddressesCustomer({object: 'invoice'});

            AppOPC.$opc_step_one.find('#invoice_address_container').css('height', 'auto').addClass('in');

            $('div#onepagecheckoutps #panel_address_invoice').removeClass('hidden');

            $('div#onepagecheckoutps #invoice_address_container .invoice.required').each(function(i, item){
                $(item).removeAttr('data-validation-optional');
            });

            if (typeof $.totalStorageOPC !== typeof undefined) {
                $.totalStorageOPC('create_invoice_address_'+OnePageCheckoutPS.id_shop, true);
            }
        }else{
            $('div#onepagecheckoutps #panel_address_invoice').addClass('hidden');

            $('div#onepagecheckoutps #invoice_address_container .invoice.required').each(function(i, item){
                $(item).attr('data-validation-optional', 'true').trigger('reset');
            });

            if (typeof $.totalStorageOPC !== typeof undefined) {
                $.totalStorageOPC('create_invoice_address_'+OnePageCheckoutPS.id_shop, false);
            }
        }
    },
    togglePasswordRequired: function(toggle_elem) {
        if ($('div#onepagecheckoutps').find(toggle_elem).is(':checked')){
            $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd, div#onepagecheckoutps #field_customer_current_passwd')
                .fadeIn()
                .addClass('required');
            $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup, div#onepagecheckoutps #field_customer_current_passwd sup').html('*');
            $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd, div#onepagecheckoutps #customer_current_passwd').removeAttr('data-validation-optional')
                .val('');
        }else{
            $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd, div#onepagecheckoutps #field_customer_current_passwd')
                .fadeOut()
                .removeClass('required')
                .trigger('reset');
            $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup, div#onepagecheckoutps #field_customer_current_passwd sup').html('');
            $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd, div#onepagecheckoutps #customer_current_passwd').attr('data-validation-optional', 'true')
                .val('');  
        }
    },
    checkGuestAccount: function(){
        if ($('#field_customer_current_passwd').length > 0) {
            Address.togglePasswordRequired('#checkbox_change_passwd');
        } else {
            if (OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED){
                Address.togglePasswordRequired('#checkbox_create_account_guest');
            }else{
                if (OnePageCheckoutPS.CONFIGS.OPC_REQUEST_PASSWORD && OnePageCheckoutPS.CONFIGS.OPC_OPTION_AUTOGENERATE_PASSWORD){
                    Address.togglePasswordRequired('#checkbox_create_account');
                }
            }
        }
    },
    isSetInvoice: function() {
        if (((OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS && OnePageCheckoutPS.CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS) || $('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked'))
            && $('#panel_addresses_customer').length > 0
        ) {
            return true;
        }

        return false;
    },
    isNeedDniByCountryId: function(params){
        var param = $.extend({}, {
            object: ''
        }, params);

        if (!$.isEmpty(param.object)){
            var id_country = null;
            var $id_country = $('#onepagecheckoutps_step_one select#' + param.object + '_id_country');

            if ($id_country.length > 0) {
                id_country = $id_country.val();
            } else {
                if (param.object == 'delivery') {
                    id_country = OnePageCheckoutPS.id_country_delivery_default;
                } else if (param.object == 'invoice') {
                    id_country = OnePageCheckoutPS.id_country_invoice_default;
                }
            }

            if (!$.isEmpty(id_country) && typeof countriesJS !== typeof undefined && $('#field_' + param.object + '_dni').length > 0){
                if (countriesNeedIDNumber[id_country]){
                    if ((param.object === 'invoice' && Address.isSetInvoice())
                            || param.object === 'delivery') {
                        $('#field_' + param.object + '_dni').addClass('required').show();
                        $('#field_' + param.object + '_dni sup').html('*');
                        $('#' + param.object + '_dni').removeAttr('data-validation-optional').addClass('required');
                    } else {
                        $('#field_' + param.object + '_dni').removeClass('required').hide();
                        $('#field_' + param.object + '_dni sup').html('');
                        $('#' + param.object + '_dni').attr('data-validation-optional', 'true').removeClass('required');
                    }
                } else {
                    if ($('#' + param.object + '_dni').attr('data-required') == '0'){
                        $('#field_' + param.object + '_dni').removeClass('required');
                        $('#field_' + param.object + '_dni sup').html('');
                        $('#' + param.object + '_dni').attr('data-validation-optional', 'true').removeClass('required');
                    }
                }
            }
        }
    },
	isNeedPostCodeByCountryId: function(params){
        var param = $.extend({}, {
            object: ''
        }, params);

        if (!$.isEmpty(param.object)){
            var $id_country = AppOPC.$opc.find('select#' + param.object + '_id_country');
            var $postcode = AppOPC.$opc.find('#' + param.object + '_postcode');

            if ($id_country.length > 0) {
                id_country = $id_country.val();
            } else {
                if (param.object == 'delivery') {
                    id_country = OnePageCheckoutPS.id_country_delivery_default;
                } else if (param.object == 'invoice') {
                    id_country = OnePageCheckoutPS.id_country_invoice_default;
                }
            }

            if (typeof id_country !== typeof undefined) {
                if (!$.isEmpty(id_country) && typeof countriesJS !== typeof undefined && $postcode.length > 0) {
                    $postcode_field = AppOPC.$opc.find('#field_' + param.object + '_postcode');

                    if (!$.isEmpty(countriesNeedZipCode[id_country])){
                        var format = countriesNeedZipCode[id_country];
                        format = format.replace(/N/g, '0');
                        format = format.replace(/L/g, 'A');
                        format = format.replace(/C/g, countriesIsoCode[id_country]);
                        $postcode.attr({'data-default-value': format, 'placeholder': format});
                    }

                    if ($postcode.data('required')) {
                        $postcode_field.addClass('required').show();
                        $postcode_field.find('sup').html('*');

                        if (param.object === 'delivery' || (param.object === 'invoice' && AppOPC.$opc.find('#checkbox_create_invoice_address').is(':checked'))) {
                            $postcode.removeAttr('data-validation-optional').addClass('required');
                        }
                    } else {
                        $postcode_field.removeClass('required');
                        $postcode_field.find('sup').html('');
                        $postcode.attr('data-validation-optional', 'true').removeClass('required');
                    }
                }
            }
        }
    },
    checkEmailCustomer: function(e){
        var email = $(e.currentTarget).val();
        /* Valida si el cliente esta logueado y el campo email deshabilitado */
        if ($(e.currentTarget).prop('disabled') && OnePageCheckoutPS.IS_LOGGED) {
            return true;
        }
        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
			dataType: 'html',
            action: 'checkRegisteredCustomerEmail',
            email: email
        };

        if (!$.isEmpty(email) && $.isEmail(email)){
            var _json = {
                data: data,
                success: function(id_customer) {
                    var callback = function(){
                        AppOPC.$opc.find('#form_login #txt_login_email').val(AppOPC.$opc.find('#customer_email').val());
                        AppOPC.$opc.find('#email_check_modal .modal-footer').append('<button type="button" class="btn btn-primary" onclick="$(\'div#onepagecheckoutps button.close\').trigger(\'click\');$(\'div#onepagecheckoutps #opc_show_login\').trigger(\'click\')" style="margin-left: 15px;">'+OnePageCheckoutPS.Msg.login_customer+'</button>');
                    }
                    var callback_close = function() {
                        $(e.currentTarget).val('').trigger('reset').focus();
                        AppOPC.$opc.find('#customer_conf_email').val('').trigger('reset');

                        return true;
                    }

                    if (id_customer != 0){
                        Fronted.showModal({name: 'email_check_modal', type:'normal', content : OnePageCheckoutPS.Msg.error_registered_email, button_close: true, callback_close: callback_close, callback: callback});
                    }
                }
            };
            $.makeRequest(_json);
        }
    },
    clearFormByObject: function(object){
        AppOPC.$opc_step_one.find('#form_address_'+object).trigger('reset');
        AppOPC.$opc_step_one.find('.addresses_customer_container .edit_address').removeAttr('disabled').removeClass('disabled');

        Address.initEventsChangeCountry(object);
    },
    updateAddress: function(params){
        var param = $.extend({}, {
            object: '',
            id_address: '',
            load_carriers: false,
            load_payments: false,
            load_review: false,
            load_addresses: false,
            update_cart: false,
            callback: ''
        }, params);

        if (OnePageCheckoutPS.IS_LOGGED) {
            if (param.object == 'delivery') {
                Fronted.validateOPC({valid_form_address_delivery: true});
            } else if (param.object == 'invoice') {
                Fronted.validateOPC({valid_form_address_invoice: true});
            }
        } else {
            AppOPC.is_valid_opc = true;
        }

        if (AppOPC.is_valid_opc) {
            var fields = Review.getFields({object: param.object});
            var rc_page = AppOPC.$opc.find('#rc_page').val();

            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'updateAddress',
                dataType: 'json',
                id_customer: AppOPC.$opc.find('#customer_id').val(),
                id_address: (!$.isEmpty(param.id_address) ? param.id_address : AppOPC.$opc.find('#'+param.object+'_id').val()),
                object: param.object,
                update_cart: param.update_cart,
                is_set_invoice: Address.isSetInvoice(),
                fields: JSON.stringify(fields),
                rc_page: rc_page
            };

            var _json = {
                data: data,
                beforeSend: function() {
                    Fronted.loading(true, '#onepagecheckoutps_step_one_container');
                },
                success: function(json) {
                    if (typeof json.id_address_delivery !== typeof undefined) {
                        Address.id_address_delivery = json.id_address_delivery;
                    }
                    if (typeof json.id_address_invoice !== typeof undefined) {
                        Address.id_address_invoice = json.id_address_invoice;
                    }

                    if (param.load_addresses) {
                        Address.loadAddressesCustomer({object: param.object, id_address: param.id_address});
                    }
                    if (param.load_carriers && !OnePageCheckoutPS.IS_VIRTUAL_CART) {
                        Carrier.getByCountry();
                    }
                    if (param.load_payments || (OnePageCheckoutPS.IS_VIRTUAL_CART && param.load_carriers)) {
                        PaymentPTS.getByCountry();
                    }
                    if (param.load_review && !param.load_payments) {
                        Review.display();
                    }
                },
                complete: function(){
                    Fronted.loading(false, '#onepagecheckoutps_step_one_container');
                    if (AppOPC.$opc_step_review.length <= 0 || !param.load_carriers) {
                        Fronted.loadingBig(false);
                    }

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();
                }
            };
            $.makeRequest(_json);
        }
    },
    removeAddress: function(param){
        var alias_address = AppOPC.$opc_step_one.find('#address_card_'+param.id_address+' .header_card span').text().trim();

        if (confirm(OnePageCheckoutPS.Msg.confirm_remove_address+' "'+alias_address+'"')) {
            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'removeAddress',
                dataType: 'json',
                id_address: param.id_address
            };

            var _json = {
                data: data,
                beforeSend: function() {
                    Fronted.loading(true, '#onepagecheckoutps_step_one_container');
                },
                success: function() {
                    Address.loadAddressesCustomer({object: param.object});
                },
                complete: function(){
                    Fronted.loading(false, '#onepagecheckoutps_step_one_container');
                    Fronted.loadingBig(false);
                }
            };
            $.makeRequest(_json);

            return true;
        }

        return false;
    },
    removeAddressInvoice: function(params){
        var param = $.extend({}, {
            callback: ''
        }, params);

        if (!$('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked')){
            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'removeAddressInvoice',
                dataType: 'html'
            };

            var _json = {
                data: data,
                beforeSend: function() {
                    Fronted.loading(true, '#onepagecheckoutps_step_one_container');
                },
                success: function() {
                    Carrier.getByCountry();
                },
                complete: function(){
                    Fronted.loading(false, '#onepagecheckoutps_step_one_container');
                    if (AppOPC.$opc_step_review.length <= 0) {
                        Fronted.loadingBig(false);
                    }

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();
                }
            };
            $.makeRequest(_json);
        }
    },
    cleanSpecialCharacterAddress: function(e) {
        var data = $(e.currentTarget);
        var value = data.val();
        var reg = '^[^!<>?=+@{}_$%]+$';
        var array_characters = reg.split("");

        $.each(array_characters, function(key, char) {
            for (var i = 0;i < value.length; i++) {
                value = value.replace(char, ' ');
            }

            $(e.currentTarget).val(value);
        });
    }
}

var Carrier = {
    id_delivery_option_selected : 0,
    getIdCarrierSelected: function () {
        var id_carrier = AppOPC.$opc_step_two.find('.delivery_option_radio:checked').val();
        id_carrier = id_carrier.replace(',', '');

        return parseInt(id_carrier);
    },
    launch: function(){
        if (!OnePageCheckoutPS.IS_VIRTUAL_CART) {
            $('div#onepagecheckoutps #gift_message').empty();

            if (!OnePageCheckoutPS.CONFIGS.OPC_SHIPPING_COMPATIBILITY) {
                AppOPC.$opc_step_two
                    .on('click', '.delivery_option .delivery_option_logo', function(event){
                        var $option_radio = $(event.currentTarget).parents('.delivery_option').find('.delivery_option_radio');
                        if (!$option_radio.is(':checked')) {
                            $option_radio.attr('checked', true).trigger('change');
                        }
                    })
                    .on('click', '.delivery_option .carrier_delay', function(event){
                        var $option_radio = $(event.currentTarget).parents('.delivery_option').find('.delivery_option_radio');
                        if (!$option_radio.is(':checked')) {
                            if ($(event.currentTarget).find('#selulozenka, #paczkomatyinpost_selected, .btn.btn-warning').length <= 0) {//support module 'ulozenka'
                                $option_radio.attr('checked', true).trigger('change');
                            }
                        }
                    })
                    .on('click', '.delivery_option .carrier_price', function(event){
                        var $option_radio = $(event.currentTarget).parents('.delivery_option').find('.delivery_option_radio');
                        if (!$option_radio.is(':checked')) {
                            $option_radio.attr('checked', true).trigger('change');
                        }
                    })
                    .on('change', '.delivery_option_radio', function (event){
                        /* compatibilidad modulo envoimoinscher - v3.3.8 - par Boxtal */
                        if ($(event.currentTarget).prop('disabled')) {
                            return false;
                        }
                        //if ($('#onepagecheckoutps_step_two_container div.delivery_date').length <= 0){//support with deliverydate
                            $('div#onepagecheckoutps #onepagecheckoutps_step_two .delivery_option').removeClass('selected alert alert-info');
                            $(this).parents('.delivery_option').addClass('selected alert alert-info');

                            //support carrier module: packetery (Packetery, Ltd. v1.17)
                            if (AppOPC.$opc_step_two.find('.packetery_prestashop_branch_list').length > 0) {
                                AppOPC.$opc_step_two.find('.packetery_prestashop_branch_list .packetery-branch-list > select').val('');
                                AppOPC.$opc_step_two.find('.packetery_prestashop_branch_list').hide();
                                AppOPC.$opc_step_two.find('.delivery_option_radio:checked').parents('.delivery_option').next('.packetery_prestashop_branch_list').show();
                            }

                            Carrier.update({delivery_option_selected: $(event.currentTarget), load_carriers: true, load_payments: false, load_review: false});
                        //}
                    })
                    //support carrier module: packetery
                    .on('change.packetery', 'select[name=pobocka]', function(){
                        if ($('#onepagecheckoutps_step_two_container div.packetery_prestashop_branch_list').length > 0){
                            $('div#onepagecheckoutps #onepagecheckoutps_step_two_container select[name=pobocka]').off('change.packetery');
                            Carrier.update({load_carriers: false, load_payments: true, load_review: true, callback: window.updateCarrierSelectionAndGift});
                        }
                    });
            }

            AppOPC.$opc_step_two
                .on('change', '#recyclable', Carrier.update)
                .on('blur', '#gift_message', Carrier.update)
                .on('blur', '#id_planning_delivery_slot', Carrier.update)//support module planningdeliverycarrier
                .on('click', '#gift', function (event){
                    Carrier.update({load_payments : true});

                    if ($(event.currentTarget).is(':checked'))
                        $('div#onepagecheckoutps #gift_div_opc').removeClass('hidden');
                    else
                        $('div#onepagecheckoutps #gift_div_opc').addClass('hidden');
                });

            if (OnePageCheckoutPS.CONFIGS.OPC_SHIPPING_COMPATIBILITY) {
                $('#opc_payment_methods').on('change', '.delivery_options input.delivery_option_radio', function(e) {
                    updateCarrierSelectionAndGift();
                });

                $(document).on('click', '#show_carrier_embed', function() {
                    AppOPC.$opc_step_two.empty();
                    AppOPC.$opc_step_two.html('<div class="row"><div class="col-xs-12 btn-secondary" id="show_carrier_embed"><span><i class="fa-pts fa-pts-check-square-o"></i>&nbsp;'+OnePageCheckoutPS.Msg.choose_carrier_embed+'</span></div></div>');

                    updateCarrierSelectionAndGift();

                    $('#opc_payment_methods').show(400);
                    AppOPC.$opc.find('#onepagecheckoutps_header, #onepagecheckoutps_contenedor').hide(400);
                });
                $('#opc_payment_methods').on('click', '#hide_carrier_embed', function() {
                    updateCarrierSelectionAndGift({load_carrier: true});

                    $('#opc_payment_methods').hide(400);
                    AppOPC.$opc.find('#onepagecheckoutps_header, #onepagecheckoutps_contenedor').show(400);
                });
            }
        }
    },
    getByCountry: function(params){
        var param = $.extend({}, {
            callback: '',
            reset_carrier_embed: true
        }, params);

        if (OnePageCheckoutPS.REGISTER_CUSTOMER)
            return;

        if (!OnePageCheckoutPS.IS_VIRTUAL_CART){
            var extra_params = '';
            $.each(document.location.search.substr(1).split('&'),function(c,q){
                if (q != undefined && q != ''){
                    var i = q.split('=');
                    if ($.isArray(i)){
                        extra_params += '&' + i[0].toString();
                        if (i[1].toString() != undefined)
                            extra_params += '=' + i[1].toString();
                    }
                }
            });

            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime() + extra_params,
                is_ajax: true,
                action: 'loadCarrier',
                dataType: 'html'
            };

            var _json = {
                data: data,
                beforeSend: function() {
                    Fronted.loading(true, '#onepagecheckoutps_step_two_container');

                    if (!OnePageCheckoutPS.CONFIGS.OPC_SHIPPING_COMPATIBILITY) {
                        //support carrier module "packetery"
                        if (typeof window.prestashopPacketeryInitialized !== typeof undefined) {
                            window.prestashopPacketeryInitialized = undefined;
                        }
                    }
                },
                success: function(html) {
                    if (!$.isEmpty(html)) {

                        if (OnePageCheckoutPS.CONFIGS.OPC_SHIPPING_COMPATIBILITY && param.reset_carrier_embed) {
                            var $content_carrier = $('<div/>').html(html);

                            Fronted.removeUniform({parent_control: '#opc_payment_methods'});

                            if ($content_carrier.find('.alert.alert-warning').length <= 0) {
                                AppOPC.$opc_step_two.empty();
                                AppOPC.$opc_step_two.html('<div class="row"><div class="col-xs-12 btn-secondary" id="show_carrier_embed"><span><i class="fa-pts fa-pts-check-square-o"></i>&nbsp;'+OnePageCheckoutPS.Msg.choose_carrier_embed+'</span></div></div>');

                                PaymentPTS.getByCountry();

                                return;
                            }
                        }

                        AppOPC.$opc_step_two.html(html);

                        if (AppOPC.$opc_step_two.find('#gift').is(':checked')) {
                            AppOPC.$opc_step_two.find('#gift_div_opc').show();
                        }

                        if (!OnePageCheckoutPS.CONFIGS.OPC_SHIPPING_COMPATIBILITY) {
                            if (typeof id_carrier_selected !== typeof undefined) {
                                AppOPC.$opc_step_two.find('.delivery_option_radio[value="'+id_carrier_selected+',"]').attr('checked', true);
                            }

                            //support carrier module: packetery (Packetery, Ltd. v1.17)
                            if (AppOPC.$opc_step_two.find('.packetery_prestashop_branch_list').length > 0) {
                                AppOPC.$opc_step_two.find('.packetery_prestashop_branch_list').hide();

                                AppOPC.$opc_step_two.find('.delivery_option_radio:checked').parents('.delivery_option').next('.packetery_prestashop_branch_list').show();
                            }

                            //suppot module deliverydays
                            if(AppOPC.$opc_step_two.find('#deliverydays_day option').length > 1){
                                AppOPC.$opc_step_two.find('#deliverydays_day option:eq(1)').attr('selected', 'true');
                            }

                            if (AppOPC.$opc_step_two.find('.delivery_option_radio').length > 0) {
                                Carrier.update({load_payments: true});
                            } else {
                                PaymentPTS.getByCountry();
                            }
                        } else {
                            PaymentPTS.getByCountry();
                        }
                    }
                },
                complete: function(){
                    Fronted.loading(false, '#onepagecheckoutps_step_two_container');

                    Fronted.removeUniform();

                    if (!OnePageCheckoutPS.CONFIGS.OPC_SHIPPING_COMPATIBILITY) {
                        //support module inPost
                        if (typeof EE_INPOST_MODULE_SETTINGS !== typeof undefined) {
                            var valF = AppOPC.$opc_step_two.find('.delivery_option_radio:checked').val();
                            var parcelLockersSelected = valF.slice(0, -1) == EE_INPOST_MODULE_SETTINGS.parcelsLockers;
                            var parcelLockersCodSelected = valF.slice(0, -1) == EE_INPOST_MODULE_SETTINGS.parcelsLockersCod;

                            if (parcelLockersSelected || parcelLockersCodSelected) {
                                $('#select_parcel_container').show();
                                $('#phone_no_parcel').show();

                                AppOPC.$opc_step_two.find('#select_parcel').on('click', function(){
                                    selectedDelivery(valF);
                                });
                            }
                        }

                        //support module vcpostnorddk
                        if (typeof AllInOne !== 'undefined' && typeof AllInOne.Main !== 'undefined') {
                            AllInOne.Main.initPickupButton();
                            AllInOne.Main.checkSelectedCarrier();
                            AllInOne.Main.initSimpleAdditionalOptions();
                        }

                        if (typeof GLS !== typeof undefined) {
                            GLS.is_loaded = false;
                            GLS.init();
                        }
                    }

                    $(document).trigger('opc-load-carrier:completed', {});

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function') {
                        param.callback();
                    }
                }
            };
            $.makeRequest(_json);
        }else{
            PaymentPTS.getByCountry();
        }
    },
    update: function(params){
        var param = $.extend({}, {
            delivery_option_selected: $('div#onepagecheckoutps .delivery_option_radio:checked'),
            load_carriers: false,
            load_payments: false,
            load_review: true,
            callback: ''
        }, params);

        if (!OnePageCheckoutPS.IS_VIRTUAL_CART){
            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'updateCarrier',
                recyclable: ($('#recyclable').is(':checked') ? $('#recyclable').val() : ''),
                gift: ($('#gift').is(':checked') ? $('#gift').val() : ''),
                gift_message: (!$.isEmpty($('#gift_message').val()) ? $('#gift_message').val() : '')
            };

            if ($(param.delivery_option_selected).length > 0)
                data[$(param.delivery_option_selected).attr('name')] = $(param.delivery_option_selected).val();

            $('#onepagecheckoutps_step_two input[type="checkbox"]:not(.customer, .delivery, .invoice), #onepagecheckoutps_step_two input[type="text"]:not(.customer, .delivery, .invoice),#onepagecheckoutps_step_two input[type="hidden"]:not(.customer, .delivery, .invoice), #onepagecheckoutps_step_two select:not(.customer, .delivery, .invoice)').each(function(i, input){
                var name = $(input).attr('name');
                var value = $(input).val();

                if (!$.isEmpty(name))
                    data[name] = value;
            });

            var _json = {
                data: data,
                beforeSend: function() {
                    Fronted.loading(true, '#onepagecheckoutps_step_two_container');
                },
                success: function(json) {
                    if (json.hasError){
                        Fronted.showModal({type:'error', message : json.errors});
                    } else if (json.hasWarning){
                        Fronted.showModal({type:'warning', message : json.warnings});
                    }
                },
                complete: function(){
                    Fronted.loading(false, '#onepagecheckoutps_step_two_container');

                    if ( typeof mustCheckOffer !== 'undefined' && event_dispatcher !== undefined && event_dispatcher === 'carrier' && AppOPC.load_offer ) {
                        AppOPC.load_offer = false;
                        mustCheckOffer = undefined;
                        checkOffer(function() {
                            //Fronted.closeDialog();
                        });
                    }

                    if(param.load_carriers)
                        Carrier.getByCountry();
                    if(param.load_payments)
                        PaymentPTS.getByCountry();
                    if(param.load_review && !param.load_payments)
                        Review.display();

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();

                    $(document).trigger('opc-update-carrier:completed', {});
                }
            };
            $.makeRequest(_json);
        }
    },
//    displayPopupModule_socolissimo: function(id_carrier){
//        if ($('#onepagecheckoutps_step_one').hasClass('customer_loaded')){
//            if($('#onepagecheckoutps_step_two div.hook_extracarrier > #soFr').length > 0){
//                $('#onepagecheckoutps_step_two div.hook_extracarrier > #soFr').attr('src',  baseDir+'modules/socolissimo/redirect.php' + serialiseInput(soInputs)).show();
//
//                Fronted.createPopup(true, '', $('#onepagecheckoutps_step_two div.hook_extracarrier > #soFr'), false, false, true, 'Carrier.getByCountry();');
//
//                $('#dialog_opc').css({width: '600px'});
//
//                Fronted.centerDialog(true);
//            }
//        }else{
//            if (confirm(OnePageCheckoutPS.Msg.select_pickup_point)){
//                Address.createCustomer('Carrier.displayPopupModule_socolissimo()');
//            }
//        }
//    },
    displayPopupModule_mondialrelay: function(id_carrier){
        if (typeof PS_MRDisplayWidget !== typeof undefined && typeof displayPickupPlace !== typeof undefined) {
            displayPickupPlace = function (info) {
                $('#onepagecheckoutps_step_two .delivery_option.selected .extra_info_carrier :not(a)').remove();
                $('#onepagecheckoutps_step_two .delivery_option.selected .extra_info_carrier a')
                    .removeClass('select_pickup_point')
                    .addClass('edit_pickup_point')
                    .html(info);
            };
            PS_MRDisplayWidget(id_carrier);
        }
    },
    displayPopupModule_correos: function(id_carrier){
        var $content_correos = ''
        if($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #correos_content').length > 0){
            $content_correos = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #correos_content');
            if (!OnePageCheckoutPS.IS_LOGGED)
                $content_correos.find('#correos_email').val('');

            var callback = function(){
                $('div#onepagecheckoutps #correos_content #oficinas_correos_content, div#onepagecheckoutps #correos_content #correosOffices_content').show();

                if (typeof Correos !== typeof undefined && typeof Correos.postcode_from_map !== typeof undefined && !$.isEmpty(Correos.postcode_from_map)) {
                    $('#correos_postcode').val(Correos.postcode_from_map);
                }

                if (typeof Correos !== typeof undefined) {
                    Correos.getOffices();
                } else {
                    GetcorreosPoint();
                }

                if (!$.isEmpty($('#customer_email').val()) && $.isEmpty($('#correos_email').val())) {
                    $('#correos_email').val($('#customer_email').val());
                }
                if (!$.isEmpty($('#delivery_phone_mobile').val()) && $.isEmpty($('#correos_mobile').val())) {
                    $('#correos_mobile').val($('#delivery_phone_mobile').val());
                }
            };
            var callback_ok = function(){
                if (typeof Correos !== typeof undefined) {
                    if ($('#correosOfficesSelect option').length > 0) {
                        if (!Correos.is_validMobile($('#correos_mobile').val())) {
                            alert(CorreosMessage.officeMobileError);
                            return false;
                        }
                        if (!Correos.is_validEmail($('#correos_email').val())) {
                            alert(CorreosMessage.officeEmailError);
                            return false;
                        }
                    }

                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .delivery_option_radio:checked').trigger('change');
                    Correos.updateOfficeInfo();
                } else {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .delivery_option_radio:checked').trigger('change');
                    update_recoger();
                }

                return true;
            };

            Fronted.showModal({name:'opc_correos', content: $content_correos, size: 'modal-lg', button_ok: true, callback: callback, callback_ok: callback_ok});
        }else{
            $content_correos = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #message_no_office_error');

            Fronted.showModal({name:'opc_correos', content: $content_correos});
        }
    },
    displayPopupModule_kiala: function(id_carrier){
        var $content = ''
        if($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #kialapicker').length > 0){
            $content = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #kialapicker');
            var callback = function(){
                $('div#onepagecheckoutps #kialapicker').show();
            };

            Fronted.showModal({name:'opc_kiala', content: $content, size: 'modal-lg', button_close: true, callback: callback});
        }
    },
    displayPopupModule_furgonetka: function(id_carrier){
        if (typeof getCarrier == 'function') {
            callback_furgonetka = function(properties) {
                $('#onepagecheckoutps_step_two .delivery_option.selected .extra_info_carrier span').remove();
                $('#onepagecheckoutps_step_two .delivery_option.selected .extra_info_carrier').prepend('<span>'+properties.name+'</span>');

                setReceiverPointData(properties, false);
            }
            var carrier = getCarrier(id_carrier);

            new Map({city: address['city'], street: address['street'], service: getCarrierServices(carrier), elementId: carrier.id_carrier, callback: callback_furgonetka}).view();
        }
    },
//    displayPopupModule_kialasmall: function(id_carrier){
//        $content = $('#onepagecheckoutps_step_two #kiala');
//
//        if ($('#onepagecheckoutps_step_one').hasClass('customer_loaded')){
//            if($content.length > 0){
//                Fronted.createPopup(true, '', $content, false, false, true, 'Carrier.getByCountry();');
//
//                Fronted.centerDialog(true);
//            }
//        }else{
//            if (confirm(OnePageCheckoutPS.Msg.select_pickup_point)){
//                Address.createCustomer('Carrier.displayPopupModule_kialasmall()');
//            }
//        }
//    },
    displayPopupModule_mycollectionplaces: function(){
        var $content = ''
        if($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #myCollectionPlacesContent').length > 0){
            $content = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #myCollectionPlacesContent');
            var callback = function(){
                $('div#onepagecheckoutps #myCollectionPlacesContent').show();
                eval($('#myCollectionPlacesContent a.button_small').attr('href'));
            };
            var callback_close = function(){
                if (typeof mycollpsaveCarrier != typeof undefined){
                    mycollpsaveCarrier();
                    Carrier.getByCountry();
                }
                return true;
            };

            Fronted.showModal({name:'opc_mycollectionplaces', content: $content, size: 'modal-lg', button_close: true, callback: callback, callback_close: callback_close});
        }
    },
    displayPopupModule_yupick: function(id_carrier){
        var $content = ''
        if($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #oficinas_yupick_content').length > 0){
            $content = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #oficinas_yupick_content');

            var callback = function(){
                $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #oficinas_yupick_content').show();

                if (!OnePageCheckoutPS.IS_LOGGED) {
                    $content.find('#yupick_type_alert_email').val('');
                }

                if (!$.isEmpty($('#customer_email').val()) && $.isEmpty($('#yupick_type_alert_email').val())) {
                    $('#yupick_type_alert_email').val($('#customer_email').val());
                }
                if (!$.isEmpty($('#delivery_phone_mobile').val()) && $.isEmpty($('#yupick_type_alert_phone').val())) {
                    $('#yupick_type_alert_phone').val($('#delivery_phone_mobile').val());
                }

                GetYupickPoint();
            };
            var callback_close = function(){
                $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .delivery_option_radio:checked').trigger('change');
                //yupick_update_recoger();
                return true;
            };

            Fronted.showModal({name:'opc_yupick', content: $content, size: 'modal-lg', button_close: true, callback: callback, callback_close: callback_close});
        }
    },
    displayPopupModule_nacex: function(id_carrier){
        var LeftPosition = (screen.width) ? (screen.width-700)/2 : 0;
    	var TopPosition = (screen.height) ? (screen.height-500)/2 : 0;
        var url = baseDir + '/modules/nacex/nxShop.php?host=www.nacex.es&cp=' + $('#delivery_postcode').val() + '&clientes=' + nacex_agcli;

        modalWin(url);
    },
    displayPopupModule_chronopost: function(id_carrier){
        $content = $('#onepagecheckoutps_step_two #chronorelais_container');

        Fronted.showModal({name:'opc_chronopost', content: $content, size: 'modal-lg', button_close: true, callback_close: function(){Carrier.getByCountry();return true;}});

        toggleRelaisMap(cust_address_clean, cust_codePostal, cust_city);
    }
//    displayPopupModule_indabox: function(id_carrier){
//        $content = $('#onepagecheckoutps_step_two #indabox');
//
//        Fronted.createPopup(true, '', $content, true, true, true, 'Carrier.getByCountry();');
//
//        Fronted.centerDialog(true);
//    }
}

var PaymentPTS = {
    id_payment_selected: '',
    launch: function(){
        $("div#onepagecheckoutps #onepagecheckoutps_step_three")
            .on('click', '.module_payment_container', function(event){
                if (!$(event.target).hasClass('payment_radio'))
                    $(event.currentTarget).find('.payment_radio').attr('checked', true).trigger('change');
            })
            .on("change", "input[name=method_payment]", function(event){
                var $payment_module = $(event.currentTarget);
                var $payment_module_url = $payment_module.next();
                var name_module = $payment_module.val();

                $('div#onepagecheckoutps #onepagecheckoutps_step_review .extra_fee').addClass('hidden');
                $('div#onepagecheckoutps #onepagecheckoutps_step_review .extra_fee_tax').addClass('hidden');

                $.each(payment_modules_fee, function(name_module_fee, payment){
                    var various_payment = false;
                    var name_module_alt = name_module + '_' + payment.id;

                    if (name_module_alt == name_module_fee) {
                        //support module custompaymentmethod
                        if ($.strpos(name_module_fee, 'custompaymentmethod') !== false) {
                            var url_payment = $payment_module_url.val();
                            var arr_url_payment = url_payment.split('=');
                            var id_payment = arr_url_payment[1];

                            if (id_payment == payment.id) {
                                various_payment = true;
                            }
                        }
                    }

                    if (name_module == name_module_fee || various_payment){
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review .extra_fee').removeClass('hidden');
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_label').text(payment.label_fee);
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_price').text(payment.fee);
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_total_price_label').text(payment.label_total);
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_total_price').text(payment.total_fee);

                        if (typeof payment.fee_tax !== typeof undefined && !$.isEmpty(payment.fee_tax)) {
                            $('div#onepagecheckoutps #onepagecheckoutps_step_review .extra_fee_tax').removeClass('hidden');
                            $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_tax_label').text(payment.label_fee_tax);
                            $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_tax_price').text(payment.fee_tax);
                        }

                        return false;
                    }
                });

                if ($('div#onepagecheckoutps #payment_method_container input[id^=module_payment_' + cod_id_module_payment + ']').is(':checked')){
                    $('div#onepagecheckoutps .cod_fee').show();
                }else{
                    $('div#onepagecheckoutps .cod_fee').hide();
                }

                if ($('div#onepagecheckoutps #payment_method_container input[id^=module_payment_' + bnkplus_id_module_payment + ']').is(':checked')){
                    $('div#onepagecheckoutps .bnkplus_discount').show();
                }else{
                    $('div#onepagecheckoutps .bnkplus_discount').hide();
                }

                if ($('div#onepagecheckoutps #payment_method_container input[id^=module_payment_' + paypal_id_module_payment + ']').is(':checked')){
                    $('div#onepagecheckoutps .paypal_fee').show();
                }else{
                    $('div#onepagecheckoutps .paypal_fee').hide();
                }

                if ($('div#onepagecheckoutps #payment_method_container input[id^=module_payment_' + sequra_id_module_payment + ']').first().is(':checked')){
                    $('div#onepagecheckoutps .sequra_fee').show();
                }else{
                    $('div#onepagecheckoutps .sequra_fee').hide();
                }

                PaymentPTS.id_payment_selected = $(this).attr('id');

                $('div#onepagecheckoutps #onepagecheckoutps_step_three .module_payment_container').removeClass('selected alert alert-info');
                $('div#onepagecheckoutps #onepagecheckoutps_step_three .payment_content_html').addClass('hidden');
                $(this).parent().parent().addClass('selected alert alert-info').find('.payment_content_html').removeClass('hidden');

                //compatibilidad con modulo: paymentdiscountssurcharges v1.0.1 de Silbersaiten
                if (typeof pds_path !== typeof undefined) {
                    $.post(pds_path + 'ajax.php', {module: name_module}, function() {});
                }
            });
    },
    getByCountry: function(params){
        var param = $.extend({}, {
            callback: '',
            show_loading: true
        }, params);

        if (OnePageCheckoutPS.REGISTER_CUSTOMER) {
            return;
        }

        if (!OnePageCheckoutPS.IS_VIRTUAL_CART) {
            if ($('div#onepagecheckoutps #onepagecheckoutps_step_two').find('.delivery_option_radio').length <= 0 && have_ship_to_pay) {
                $('div#onepagecheckoutps #onepagecheckoutps_step_three').html('<p class="alert alert-warning col-xs-12">'+OnePageCheckoutPS.Msg.shipping_method_required+'</p>');

                Review.display();
                return;
            }
        }

        var extra_params = '';
        $.each(document.location.search.substr(1).split('&'),function(c,q){
            if (q != undefined && q != ''){
                var i = q.split('=');
                if ($.isArray(i)){
                    extra_params += '&' + i[0].toString();
                    if (i[1].toString() != undefined)
                        extra_params += '=' + i[1].toString();
                }
            }
        });

        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime() + extra_params,
            is_ajax: true,
            dataType: 'html',
            action: 'loadPayment'
        };

        var _json = {
            data: data,
            beforeSend: function() {
                if(param.show_loading) {
                    Fronted.loading(true, '#onepagecheckoutps_step_three_container');
                }

                if ($('#onepagecheckoutps_step_three #braintree_cc_submit').length > 0) {
                    if (typeof $.totalStorageOPC !== typeof undefined) {
                        $.totalStorageOPC('braintree-card-number', $('#onepagecheckoutps_step_three #braintree_cc_submit #card_number').val());
                        $.totalStorageOPC('braintree-card-cvc', $('#onepagecheckoutps_step_three #braintree_cc_submit #cvv').val());
                        $.totalStorageOPC('braintree-card-expiry-month', $('#onepagecheckoutps_step_three #braintree_cc_submit select[name="expiration_month"]').val());
                        $.totalStorageOPC('braintree-card-expiry-year', $('#onepagecheckoutps_step_three #braintree_cc_submit select[name="expiration_year"]').val());
                    }
                }
            },
            success: function(html) {
                $('div#onepagecheckoutps #onepagecheckoutps_forms').html('');
                $('div#onepagecheckoutps #onepagecheckoutps_step_three').html(html);

                if (!$.isEmpty(PaymentPTS.id_payment_selected)){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three #payment_method_container #' + PaymentPTS.id_payment_selected).parent().parent().trigger('click');
                } else if ($('#onepagecheckoutps_step_three #payment_method_container .module_payment_container').length == 1){
                    $('#onepagecheckoutps_step_three #payment_method_container .module_payment_container').trigger('click');
                } else if (!$.isEmpty(OnePageCheckoutPS.CONFIGS.OPC_DEFAULT_PAYMENT_METHOD)){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three #payment_method_container [value="'+ OnePageCheckoutPS.CONFIGS.OPC_DEFAULT_PAYMENT_METHOD + '"]').parent().parent().trigger('click');
                }

                $('div#onepagecheckoutps #onepagecheckoutps_step_three .module_payment_container.selected').find('.payment_content_html').removeClass('hidden');

                //support module paypalpro
                if(typeof $('#pppro_form') !== typeof undefined && !OnePageCheckoutPS.IS_LOGGED)
                    $('#pppro_form #pppro_cc_fname, #pppro_form #pppro_cc_lname').val('');
            },
            complete: function(){
                /*if (!$.isEmpty($('.braintree-payment-errors').html())) {
                    Fronted.showModal({type:'error', message : $('.braintree-payment-errors').html()});
                }*/

                if ($('#onepagecheckoutps_step_three #authorizeaim_form').length > 0) {
                    var full_name = $('#onepagecheckoutps_step_one #customer_firstname').val() + ' ' + $('#onepagecheckoutps_step_one #customer_lastname').val();
                    $('#onepagecheckoutps_step_three #authorizeaim_form #fullname').val(full_name);
                }

                if (!OnePageCheckoutPS.IS_LOGGED && $('#onepagecheckoutps_step_three #adn_form').length > 0) {
                    $('#onepagecheckoutps_step_three #adn_form #adn_cc_fname').val($('#onepagecheckoutps_step_one #customer_firstname').val());
                    $('#onepagecheckoutps_step_three #adn_form #adn_cc_lname').val($('#onepagecheckoutps_step_one #customer_lastname').val());
                    $('#onepagecheckoutps_step_three #adn_form input[name=adn_cc_address]').val('');
                    $('#onepagecheckoutps_step_three #adn_form input[name=adn_cc_city]').val('');
                    $('#onepagecheckoutps_step_three #adn_form input[name=adn_cc_zip]').val('');
                }

                if ($('#onepagecheckoutps_step_three #braintree_cc_submit').length > 0) {
                    if (typeof $.totalStorageOPC !== typeof undefined) {
                        $('#onepagecheckoutps_step_three #braintree_cc_submit #card_number').val($.totalStorageOPC('braintree-card-number'));
                        $('#onepagecheckoutps_step_three #braintree_cc_submit #cvv').val($.totalStorageOPC('braintree-card-cvc'));
                        $('#onepagecheckoutps_step_three #braintree_cc_submit select[name="expiration_month"]').val($.totalStorageOPC('braintree-card-expiry-month'));
                        $('#onepagecheckoutps_step_three #braintree_cc_submit select[name="expiration_year"]').val($.totalStorageOPC('braintree-card-expiry-year'));
                    }
                }

                if (typeof pmtSimulator !== typeof undefined){
                    pmtSimulator.simulator_app.load_jquery();
                }

                //support module soflexibilite
                //if (typeof initSoFlexibiliteEngine !== typeof undefined) {
                    //initSoFlexibiliteEngine();
                //}

                if(param.show_loading) {
                    Fronted.loading(false, '#onepagecheckoutps_step_three_container');
                }

                Fronted.removeUniform();

                $(document).trigger('opc-load-payment:completed', {});

                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function') {
                    param.callback();
                } else {
                    Review.display();
                }
            }
        };
        $.makeRequest(_json);
    },
    change: function(){
        if ( !AppOPC.load_offer || typeof mustCheckOffer === 'undefined' || (event_dispatcher !== undefined && event_dispatcher !== 'payment_method') ) {
//            Payment.validateSelected();
        } else {
            AppOPC.load_offer = false;
            checkOffer(function() {
//                PaymentPTS.validateSelected();
            });
        }
    }
}

var Review = {
    message_order: '',
    launch: function(){

        AppOPC.$opc.on('click', '#btn_place_order', function (){
            if (parseInt(OnePageCheckoutPS.CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO) && $('div#onepagecheckoutps #onepagecheckoutps_step_three #free_order').length <= 0) {
                window.scrollTo(0, $('#onepagecheckoutps').offset().top);
                $('#onepagecheckoutps_step_three').addClass('alert alert-warning');
                return false;
            }else{
                Review.placeOrder();
            }
        })
        .on("change", '#cgv', function(e) {
            if ( typeof mustCheckOffer !== 'undefined' && event_dispatcher !== undefined && event_dispatcher === 'terms' && AppOPC.load_offer ) {
                if ( $(e.target).is(':checked') ) {
                    if ( !offerApplied ) {
                        AppOPC.load_offer = false;
                        checkOffer(function() {
                            $(e.target).unbind('change');
                            //Fronted.closeDialog();
                        });
                    }
                }
            }
        })
        .on("click", "#div_cgv span.read", function(){
            Fronted.openCMS({id_cms : OnePageCheckoutPS.CONFIGS.OPC_ID_CMS_TEMRS_CONDITIONS});
        });

        $("#onepagecheckoutps_step_review")
            .on("click", ".voucher_name", Review.addVoucher)
            .on("click", "#submitAddDiscount", Review.processDiscount)

            .on("click", "#payment_paypal_express_checkout", function(){
                $('#paypal_payment_form').submit();
            })
            .on('keypress', '.cart_quantity_input', function(){
                AppOPC.$opc.find('#btn_place_order').attr('disabled', 'true');
            })
            .on("blur", "#div_leave_message #message", function(){
                Review.message_order = $(this).val();
            });

        //Compatibilidad con modulo premiumflatrate (v2.1.2 - by idnovate)
        $(document).on("change", 'select#premiumflatrate_options, label#premiumflatrate_options_radio', Review.display);
    },
    display: function(params){
        var param = $.extend({}, {
            callback: ''
        }, params);

        if (OnePageCheckoutPS.REGISTER_CUSTOMER) {
            return;
        }

        if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_TERMS_CONDITIONS) {
            var privacy_policy = AppOPC.$opc.find('#privacy_policy').is(':checked');
        }
        if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_PRIVACY_POLICY) {
            var cgv = AppOPC.$opc.find('#cgv').is(':checked');
        }

        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
            dataType: 'html',
            action: 'loadReview'
        };

        var _json = {
            data: data,
            beforeSend: function() {
                Fronted.loading(true, '#onepagecheckoutps_step_review_container');

                //se quita del complete para colocarse en el beforeSend, pues despues de cargado el resumen de carrito
                //ya no tiene las direcciones, solo antes.
                if (typeof ajaxCart !== typeof undefined && typeof ajaxCart.refresh !== typeof undefined) {
                    if (typeof pc_cart === typeof undefined) {
                        //corrige el problema de productos duplicados en el carrito del top.
                        $('#header #cart_block_list .products dt, #header .cart_block_list .products dt').remove();
                        $('#header #cart_block_list .products dd, #header .cart_block_list .products dd').remove();
                        $('#side_cart_block #cart_block_list .products dt, #side_cart_block .cart_block_list .products dt').remove();
                        $('#side_cart_block #cart_block_list .products dd, #side_cart_block .cart_block_list .products dd').remove();
                        $('#sns_header_bottom .cart_block_list .products dt').remove();

                        ajaxCart.refresh();
                    }
                }
            },
            success: function(html) {
                $("div#onepagecheckoutps #onepagecheckoutps_step_review").html(html);

                if (typeof summary_opc === typeof undefined && !OnePageCheckoutPS.CONFIGS.OPC_COMPATIBILITY_REVIEW) {
                    window.parent.location.reload();
                }
                if (AppOPC.$opc_step_review.find('#order-detail-content .cart_item').length <= 0 ) {
                    window.parent.location.reload();
                }
cgv = true;
                if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_TERMS_CONDITIONS && cgv) {
                    AppOPC.$opc.find('#cgv').attr('checked', 'true');
                }
                if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_PRIVACY_POLICY && privacy_policy) {
                    AppOPC.$opc.find('#privacy_policy').attr('checked', 'true');
                }

                $('div#onepagecheckoutps input[name="method_payment"]:checked').trigger('change');
            },
            complete: function(){
//                Fronted.loading(false, '#onepagecheckoutps_step_review_container');
                Fronted.loadingBig(false);

                //if no exist carriers, do not show cost shipping
                if (!OnePageCheckoutPS.IS_VIRTUAL_CART) {
                    if (AppOPC.$opc_step_two.find('.delivery_options .delivery_option').length <= 0) {
                        AppOPC.$opc_step_review.find('#remaining_amount_free_shipping').hide();
                        AppOPC.$opc_step_review.find('.item_total:not(.cart_total_product, .cart_total_voucher, .cart_discount)').hide();
//                        AppOPC.$opc_step_review.find('#list-voucher-allowed').hide();
                    }
                }

                //remove express checkout paypal on review
                $('#container_express_checkout').remove();

                if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT) {
                    //image zoom on product list.
                    $('div#onepagecheckoutps #order-detail-content .cart_item a > img').mouseenter(function(event){
                        $('div#onepagecheckoutps #order-detail-content .image_zoom').hide();
                        $(event.currentTarget).parents('.image_product').find('.image_zoom').show();
                    });
                    $('div#onepagecheckoutps #order-detail-content .image_zoom').click(function(event){
                        $(event.currentTarget).toggle();
                    });
                    $('div#onepagecheckoutps #order-detail-content .image_zoom').hover(function(event){
                        $(event.currentTarget).show();
                    }, function(event){
                        $(event.currentTarget).hide();
                    });
                }

                //Se comenta esto para cumplicar con las politicas de alemania y suecia y posiblemente otros paises.
                //if (typeof $.totalStorageOPC !== typeof undefined) {
                //    if ($.totalStorageOPC('cms_terms_condifitions')) {
                //        AppOPC.$opc.find('#cgv').attr('checked', 'true');
                //    }
                //}

                var intervalLoadJavaScriptReview = setInterval(
                    function() {
                        loadJavaScriptReview();
                        clearInterval(intervalLoadJavaScriptReview);
                    }
                    , (typeof csoc_prefix !== 'undefined' ? 5001 : 0));

                //last minute opc
                if ( typeof mustCheckOffer !== 'undefined' && event_dispatcher !== undefined && event_dispatcher === 'init' && AppOPC.load_offer ) {
                    AppOPC.load_offer = false;
                    mustCheckOffer = undefined;

                    setTimeout(checkOffer, time_load_offer * 1000);
                }

                if (OnePageCheckoutPS.CONFIGS.OPC_CONFIRMATION_BUTTON_FLOAT && !OnePageCheckoutPS.CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO){
                    var $container_float_review = $("div#onepagecheckoutps div#onepagecheckoutps_step_review #container_float_review");
                    var $container_float_review_point = $("div#onepagecheckoutps div#onepagecheckoutps_step_review #container_float_review_point");

                    $(window).scroll(function() {
                        var time_out = setTimeout(function() {
                            if (AppOPC.$opc.find('.loading_big').is(':visible')) {
                                $container_float_review.removeClass('stick_buttons_footer');
                            } else {
                                if (!$container_float_review_point.visible() && $(window).height() > 640) {
                                    if ($container_float_review_point.offset().top > $(window).scrollTop()){
                                        $container_float_review.addClass('stick_buttons_footer').css({width : $('#onepagecheckoutps_step_review').outerWidth()});
                                    }
                                } else {
                                    $container_float_review.removeClass('stick_buttons_footer').removeAttr('style');
                                }
                            }
                            clearTimeout(time_out);
                        }, 400);
                    });

                    $(window).resize(function(){
                        $(window).trigger('scroll');
                    });
                    $(window).trigger('scroll');
                }

                if (typeof FB !== typeof undefined && typeof FB.XFBML.parse == 'function') {
                    FB.XFBML.parse();
                }

                if (!$.isEmpty(Review.message_order)) {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_review_container #message').val(Review.message_order);
                }

                if (typeof getAppliedOffers !== typeof undefined && typeof getAppliedOffers === 'function') {
                     getAppliedOffers();
                }

                //support module: configurator
                if (typeof cartDetails !== 'undefined') {
                    if (typeof orderSummaryHandler === 'object' && typeof orderSummaryHandler.init === 'function') {
                        orderSummaryHandler.init("order", cartDetails);
                    }
                }

                //support module: customtextdesign
                if (typeof ctd_special !== typeof undefined) {
                    $('div#onepagecheckoutps div#onepagecheckoutps_step_review div[id^=product_' + ctd_special + '_').hide();
                }
                //support module: productcomposer - v1.8.4 - Tuni-Soft
                if (typeof pc_cart !== typeof undefined) {
                    pc_cart.init();
                    if (AppOPC.$opc_step_review.find('.cart_item.customization .pc_selection').length > 0) {
                        $.each(AppOPC.$opc_step_review.find('.cart_item.customization .pc_selection'), function(i, item){
                            var src = $(item).find('.pc_image').attr('src');
                            var $item_product = $(item).parents('.cart_item.customization').prev();

                            $(item).find('.pc_image').parents('.cart_item.customization').prev().find('.img-thumbnail').attr('src', src);
                            $(item).find('.pc_image').parents('.cart_item.customization').prev().find('.image_zoom img').attr('src', src);

                            var pc_price = $(item).data('unitprice');
                            var pc_total = $(item).data('totalprice');

                            $($item_product).find('[id*="product_price_"] .price').text(pc_price);
                            $($item_product).find('[id*="total_product_price_"]').text(pc_total);
                        });
                    }
                }

                //$('#btn_continue_shopping').attr('data-link', document.referrer);

                Fronted.removeUniform();

                /* Compatibilidad con el módulo darique - V2.4.16 de Silbersaiten */
                if ($('#dariqueWrapper').length === 0 && typeof dariqueModule !== typeof undefined) {
                    dariqueModule.refreshing = false;
                    dariqueModule.existsDariqueWrapper = 0;
                    dariqueModule.refreshPresents($('#onepagecheckoutps #container_float_review_point'));
                }

                $(document).trigger('opc-load-review:completed', {});

                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                    param.callback();
            }
        };
        $.makeRequest(_json);
    },
    addVoucher: function(event) {
        var code = $(event.currentTarget).attr('data-code');
        $('#discount_name').val(code);
        Review.processDiscount();
    },
    processDiscount: function(params) {
        var p = $.extend({}, {
            id_discount: null,
            action: 'add'
        }, params);

        if($.isEmpty(p.action)) return;

        if(p.action != 'delete'){
            if($.isEmpty($('#discount_name').val()))
                return;
        }

		var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
            action: 'processDiscount',
			action_discount: p.action,
			discount_name: $('#discount_name').val(),
			id_discount: p.id_discount
        };

		var _json = {
            data: data,
            beforeSend: function(){
                Fronted.loading(true, '#onepagecheckoutps_step_review_container');
                $('#onepagecheckoutps_step_review #submitAddDiscount').attr('disabled', true);
            },
            success: function(json) {
				if (json.hasError){
                    Fronted.loading(false, '#onepagecheckoutps_step_review_container');
                    Fronted.showModal({type:'error', message : '&bullet; ' + json.errors.join('<br>&bullet; ')});
                } else if (json.hasWarning){
                    Fronted.loading(false, '#onepagecheckoutps_step_review_container');
                    Fronted.showModal({type:'warning', message : '&bullet; ' + json.warnings.join('<br>&bullet; ')});
                } else {
                    if (OnePageCheckoutPS.IS_VIRTUAL_CART){
                        PaymentPTS.getByCountry();
                    }else{
                        Carrier.getByCountry();
                    }
                }
            },
            complete: function(){
                $('#onepagecheckoutps_step_review #submitAddDiscount').attr('disabled', false);
            }
        };
        $.makeRequest(_json);
    },
    getFields: function(params){
        var param = $.extend({}, {
            object: ''
        }, params);

        var fields = Array();

        var $paypalpro_payment_form = $('#onepagecheckoutps_step_three #paypalpro-payment-form');

        var $selector = $('div#onepagecheckoutps div#onepagecheckoutps_step_one .customer, \n\
            div#onepagecheckoutps div#onepagecheckoutps_step_one .delivery, \n\
            div#onepagecheckoutps div#onepagecheckoutps_step_one .invoice');

        if (param.object == 'customer') {
            $selector = AppOPC.$opc_step_one.find('.customer');
        } else if (param.object == 'delivery') {
            $selector = AppOPC.$opc_step_one.find('.delivery');
        } else if (param.object == 'invoice') {
            $selector = AppOPC.$opc_step_one.find('.invoice');
        }

        $selector.each(function(i, field){
            if ($(field).is('span'))
                return true;

            var name = $(field).attr('data-field-name');
            var value = '';
            var object = '';

            if ($.isEmpty(name))
                return true;

            if ($(field).hasClass('customer')){
                object = 'customer';
            }else if ($(field).hasClass('delivery')){
                object = 'delivery';
            }else if ($(field).hasClass('invoice')){
                object = 'invoice';
            }

            if (object == 'invoice' && $('div#onepagecheckoutps #checkbox_create_invoice_address').length > 0) {
                if (!$('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked'))
                    return true;
            }

            if (($('div#onepagecheckoutps #field_customer_checkbox_change_passwd input[name="checkbox_change_passwd"]').length > 0
                && !$('div#onepagecheckoutps #field_customer_checkbox_change_passwd input[name="checkbox_change_passwd"]').is(':checked'))
                || ($('div#onepagecheckoutps #field_customer_checkbox_create_account input[name="checkbox_create_account"]').length > 0
                && !$('div#onepagecheckoutps #field_customer_checkbox_create_account input[name="checkbox_create_account"]').is(':checked'))
                || ($('div#onepagecheckoutps #field_customer_checkbox_create_account_guest input[name="checkbox_create_account_guest"]').length > 0
                && !$('div#onepagecheckoutps #field_customer_checkbox_create_account_guest input[name="checkbox_create_account_guest"]').is(':checked'))
            ) {
                if (name == 'current_passwd' || name == 'passwd_confirmation' || name == 'passwd') {
                    return true;
                }
            }

            if (!$.isEmpty(object)){
                if ($(field).is(':checkbox')){
                    value = $(field).is(':checked') ? 1 : 0;
                }else if ($(field).is(':radio')){
                    var tmp_value = $('input[name="' + name + '"]:checked').val();
                    if (typeof tmp_value !== typeof undefined)
                        value = tmp_value;
                }else{
                    value = $(field).val();

                    if (value === null)
                        value = '';
                }

                if ($.strpos(value, '\\')){
                    value = addslashes(value);
                }

                if ($.strpos(value, '\n')){
                    value = value.replace(/\n/gi, '\\n');
                }

                if (!$.isEmpty(value) && typeof value == 'string'){
                    value = value.replace(/\"/g, '\'');
                }

                value = $.trim(value);

                if ($.isEmpty(value) && $(field).data('required') == 1) {
                    value = $(field).data('default-value');
                }

                fields.push({'object' : object, 'name' : name, 'value' : value});

                //support payment module: StripeJS
                if (typeof stripe_billing_address !== typeof undefined && object == 'invoice') {
                    stripe_billing_address[name] = value;

                    if (name == 'id_country') {
                        stripe_billing_address['country'] = $(field).find('option:selected').data('text');
                    }
                }

                //support payment module: nPaypalPro - prestashop - 1.3.7
                if(object == 'customer' && $paypalpro_payment_form.length > 0) {
                    if (name == 'firstname') {
                        $paypalpro_payment_form.find('.paypalpro-firstname').val(value);
                    }
                    if (name == 'lastname') {
                        $paypalpro_payment_form.find('.paypalpro-lastname').val(value);
                    }
                }
            }
        });

        return fields;
    },
    getFieldsExtra: function(_data){
        $('div#onepagecheckoutps input[type="text"]:not(.customer, .delivery, .invoice), div#onepagecheckoutps input[type="hidden"]:not(.customer, .delivery, .invoice), div#onepagecheckoutps select:not(.customer, .delivery, .invoice)').each(function(i, input){
            var name = $(input).attr('name');
            var value = $(input).val();

            if (name == 'action' || name === 'controller' || (name === 'module' && value === 'sisow') || (name === 'fc' && value === 'module')) {
                return true;
            }

            //compatibilidad modulo eydatepicker
            if (name == 'shipping_date_raw')
                name = 'shipping_date';

            if (!$.isEmpty(name))
                _data[name] = value;
        });

        $('div#onepagecheckoutps input[type="checkbox"]:not(.customer, .delivery, .invoice)').each(function(i, input){
            var name = $(input).attr('name');
            var value = $(input).is(':checked') ? $(input).val() : '';

            if (!$.isEmpty(name))
                _data[name] = value;
        });

        $('div#onepagecheckoutps input[type="radio"]:not(.customer, .delivery, .invoice):checked').each(function(i, input){
            var name = $(input).attr('name');
            var value = $(input).val();

            if (!$.isEmpty(name))
                _data[name] = value;
        });

        delete _data['id_customer'];
        _data['id_customer'];
        _data['id_customer'];

        return _data;
    },
    placeOrder: function(params){
        var param = $.extend({}, {
            validate_payment: true,
            position_element: null
        }, params);

        Fronted.removeUniform();

        if (!OPC_External_Validation.execute('review:placeOrder')){
            return false;
        }

        if (OnePageCheckoutPS.IS_LOGGED) {
            if (AppOPC.$opc.find('#form_address_delivery').is(':visible') || AppOPC.$opc.find('#form_address_invoice').is(':visible')) {
                Fronted.showModal({type:'warning', message : OnePageCheckoutPS.Msg.finalize_address_update});
                return false;
            }

            if (!OnePageCheckoutPS.IS_VIRTUAL_CART || OnePageCheckoutPS.CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL) {
                if (AppOPC.$opc_step_one.find('#delivery_address_container .address_card').length <= 1) {
                    Fronted.showModal({type:'warning', message : OnePageCheckoutPS.Msg.need_add_delivery_address});
                    return false;
                }
                if (AppOPC.$opc.find('.addresses_customer_container.delivery .container_card.selected').length <= 0) {
                    Fronted.showModal({type:'warning', message : OnePageCheckoutPS.Msg.select_delivery_address});
                    return false;
                }
            }

            if (Address.isSetInvoice() && AppOPC.$opc.find('.addresses_customer_container.invoice .container_card.selected').length <= 0) {
                Fronted.showModal({type:'warning', message : OnePageCheckoutPS.Msg.select_invoice_address});
                return false;
            }
        }

        AppOPC.$opc.find('#btn_place_order').attr('disabled', 'true');

        Fronted.validateOPC({
            valid_form_customer: true,
            valid_form_address_delivery: true,
            valid_form_address_invoice: true,
            valid_carrier: true,
            valid_payment: true,
            valid_privacy: true,
            valid_gdpr: true,
            valid_condition: true
        });

        if (AppOPC.is_valid_opc) {
            if (!OnePageCheckoutPS.IS_LOGGED || OnePageCheckoutPS.IS_GUEST) {
                var fields = Review.getFields();
            } else {
                var fields = Review.getFields({object: 'customer'});
            }

            if (fields) {
                //support module: correos. Carrier international.
                if (typeof CorreosConfig !== typeof undefined && !OnePageCheckoutPS.IS_VIRTUAL_CART) {
                    if ($.inArray(Carrier.getIdCarrierSelected().toString(), CorreosConfig.carrierInternacional) == 0) {
                        if (AppOPC.$opc_step_two.find('#cr_international_mobile').length > 0 && $.isEmpty(AppOPC.$opc_step_two.find('#cr_international_mobile').val())) {
                            var phone_mobile = $('#delivery_phone_mobile').val();

                            if ($.isEmpty(phone_mobile)) {
                                phone_mobile = $('#delivery_phone').val();
                            }

                            AppOPC.$opc_step_two.find('#cr_international_mobile').val(phone_mobile).trigger('change');
                        }
                    }
                }

                //support module yupik
                if ($('#yupick_type_alert_phone').length > 0 && $('#onepagecheckoutps_step_one #delivery_phone_mobile').length > 0) {
                    $('#yupick_type_alert_phone').val($('#onepagecheckoutps_step_one #delivery_phone_mobile').val());
                }
                if ($('#yupick_type_alert_email').length > 0 && $('#onepagecheckoutps_step_one #customer_email').length > 0) {
                    $('#yupick_type_alert_email').val($('#onepagecheckoutps_step_one #customer_email').val());
                }

                var _extra_data = Review.getFieldsExtra({});
                var _data = $.extend({}, _extra_data, {
                    'url_call'				: orderOpcUrl + '?rand=' + new Date().getTime(),
                    'is_ajax'               : true,
                    'action'                : 'placeOrder',
                    'id_customer'           : (!$.isEmpty(AppOPC.$opc_step_one.find('#customer_id').val()) ? AppOPC.$opc_step_one.find('#customer_id').val() : ''),
                    'id_address_delivery'   : Address.id_address_delivery,
                    'id_address_invoice'    : !$.isEmpty(Address.id_address_invoice) ? Address.id_address_invoice : Address.id_address_delivery,
                    'fields_opc'            : JSON.stringify(fields),
                    'message'               : (!$.isEmpty(AppOPC.$opc_step_review.find('#message').val()) ? AppOPC.$opc_step_review.find('#message').val() : ''),
                    'is_new_customer'       : (AppOPC.$opc_step_one.find('#checkbox_create_account_guest').is(':checked') ? 0 : 1),
                    'token'                 : static_token,
                    'psgdpr-consent'        : true,//support module: psgdpr - v1.0.0 - PrestaShop
                    'g_recaptcha_response'  : $('#g-recaptcha-response').val(),//support module: recaptcha - v1.2.2 - Charlie
                    'g-recaptcha-response'  : $('#g-recaptcha-response').val()//support module: recaptcha - v1.2.3 - Charlie
                });

                var callback_load_address = '';

                var _json = {
                    data: _data,
                    beforeSend: function() {
                        Fronted.loadingBig(true);
                        window.scrollTo(0, AppOPC.$opc.outerHeight() / 3);
                    },
                    success: function(data) {
                        if (typeof data.token !== typeof undefined && typeof token !== typeof undefined) {
                            token = data.token;
                        }
                        if (typeof data.static_token !== typeof undefined && typeof static_token !== typeof undefined) {
                            static_token = data.static_token;
                        }

                        if (data.isSaved && (!OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED || $('#checkbox_create_account_guest').is(':checked'))){
                            AppOPC.$opc_step_one.find('#customer_email, #customer_conf_email, #customer_passwd, #customer_conf_passwd')
                                .attr({
                                    'disabled': 'true',
                                    'data-validation-optional' : 'true'
                                })
                                .addClass('disabled')
                                .trigger('reset');

                            $('#div_onepagecheckoutps_login, #field_customer_passwd, #field_customer_conf_passwd, div#onepagecheckoutps #onepagecheckoutps_step_one_container .account_creation, #field_choice_group_customer, #field_customer_checkbox_create_account, #field_customer_checkbox_create_account_guest').addClass('hidden');

                            AppOPC.$opc_step_one.find('#div_save_customer').remove();
                            AppOPC.$opc_step_one.find('#div_create_invoide_address').remove();
                            AppOPC.$opc_step_one.find('#opc_show_login').remove();
                            AppOPC.$opc_step_one.find('#label_help_invoice').remove();
                            AppOPC.$opc_step_one.find('#div_privacy_policy').remove();

                            AppOPC.$opc_step_one.find('#action_address_delivery').removeClass('hidden');
                            AppOPC.$opc_step_one.find('#action_address_delivery').removeClass('hidden');

                            OnePageCheckoutPS.IS_LOGGED = data.isSaved;
                            OnePageCheckoutPS.IS_GUEST = data.isGuest;
                        }

                        if (data.hasError){
                            Fronted.showModal({type:'error', message : '&bullet; ' + data.errors.join('<br>&bullet; ')});
                        } else if (data.hasWarning){
                            Fronted.showModal({type:'warning', message : '&bullet; ' + data.warnings.join('<br>&bullet; ')});
                        } else {
                            Address.id_customer = data.id_customer;
                            Address.id_address_delivery = data.id_address_delivery;
                            Address.id_address_invoice = data.id_address_invoice;

                            AppOPC.$opc_step_one.find('#customer_id').val(Address.id_customer);
                            AppOPC.$opc_step_one.find('#delivery_id').val(Address.id_address_delivery);
                            AppOPC.$opc_step_one.find('#invoice_id').val(Address.id_address_invoice);

                            callback_load_address = function() {
                                //plugin last minute offer
                                if ( !AppOPC.load_offer || typeof mustCheckOffer === 'undefined' || (event_dispatcher !== undefined && event_dispatcher !== 'confirm') ) {
                                    window['checkOffer'] = function(callback) {
                                        callback();
                                    };
                                }

                                if($('div#onepagecheckoutps #onepagecheckoutps_step_three #free_order').length > 0){
                                    confirmFreeOrder();
                                    return;
                                }

                                //support module payment: Pay
                                if (!$.isEmpty($('#securepay_cardNo').val()) &&
                                    !$.isEmpty($('#securepay_cardSecurityCode').val()) &&
                                    !$.isEmpty($('#securepay_cardExpireMonth').val()) &&
                                    !$.isEmpty($('#securepay_cardExpireYear').val()))
                                {
                                    CardpaySubmit();
                                    return;
                                }

                                var callback_placeorder = '';
                                if(param.validate_payment === true){
                                    var callback_placeorder = function(){
                                        var radio_method_payment = $('div#onepagecheckoutps #onepagecheckoutps_step_three #payment_method_container #' + PaymentPTS.id_payment_selected + ':checked');
                                        var input_url_method_payment = $(radio_method_payment).next();

                                        var id_payment = $(radio_method_payment).attr('id').split('_')[2];
                                        var name_payment = $(radio_method_payment).val();
                                        var url_payment = $(input_url_method_payment).val();

                                        if (name_payment === 'pts_payplug') {
                                            var $pp_credit_card = $('div#onepagecheckoutps div#onepagecheckoutps_step_three input[name="pp_credit_card"]:checked');
                                            if (typeof configs_pts_payplug !== typeof undefined && configs_pts_payplug.PP_SAVE_CARD && $pp_credit_card.length > 0) {
                                                var card_selected = $pp_credit_card.val();

                                                if (card_selected !== 'new_card') {
                                                    url_payment = "$('#onepagecheckoutps div#onepagecheckoutps_contenedor > .pp_payment_module button#btn-pay_save_card').trigger('click')";
                                                }
                                            }
                                        } else if (name_payment === 'paysera' && $('#payseraPaySubmit > span').length > 0) {
                                            url_payment = "$('#payseraPaySubmit > span').trigger('click')";
                                        }

                                        var onclick_payment = $(input_url_method_payment).length > 0 ? $(input_url_method_payment).get(0).getAttribute("onclick") : '';

                                        if ($.isEmpty(id_payment) && $.isEmpty(url_payment) && $.isEmpty(onclick_payment))
                                            return;

                                        if(!$.isEmpty(onclick_payment)){
                                            if (name_payment == 'klikandpay' || name_payment == 'banesto' || name_payment == 'paypal'){

                                                if(!eval(onclick_payment))
                                                    return;
                                            }
                                        }

                                        checkOffer(function() {
                                            if (url_payment == 'iframe'){//compatibilidad con paypal integral.
                                                $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE).html($('#iframe_payment_module_' + id_payment).val());
                                                window.scrollTo(0, $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE).offset().top);
                                                Fronted.loadingBig(false);
                                            } else if (!$.isUrlValid(url_payment)){
                                                //compatibilidad con modulo bbva
                                                if (name_payment == 'bbva'){
                                                    var peticion_bbva = $('#bbva_form input').val();
                                                    var _tmp = peticion_bbva.split('key=');
                                                    var old_secure_key = _tmp[1].substr(0, 32);
                                                    peticion_bbva = peticion_bbva.replace(old_secure_key, data.secure_key);

                                                    $('#bbva_form input').val(peticion_bbva);
                                                }

                                                eval(url_payment);

                                                if (name_payment === 'redsys' && typeof url_payment !== typeof undefined) {
                                                    Fronted.loadingBig(false);
                                                }

                                                return false;
                                            }else{
                                                //redireccion automatica a la pagina del modulo, ya que por su forma de construccion no es posible mostrarlo en un iframe
                                                var arr_payments_without_popup = payments_without_popup.split(',');

                                                if ($.inArray(name_payment, arr_payments_without_popup) >= 0 || $(radio_method_payment).hasClass('payment_eu')) {
                                                    window.location = url_payment;

                                                    return false;
                                                }

                                                if ($.strpos(url_payment, '?pm='+name_payment)) {
                                                    var _json = {
                                                        data: {
                                                            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                                                            is_ajax: true,
                                                            dataType: 'html',
                                                            action: 'getContentPayment',
                                                            name_payment: name_payment
                                                        },
                                                        success: function(html) {
                                                            $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE + ' #onepagecheckoutps').removeAttr('class');
                                                            $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE + ' #onepagecheckoutps').children(':not(#btn_other_payments)').remove();

                                                            //agrega el total de carrito de manera informativa
                                                            var label_total_paid_opc = AppOPC.$opc_step_review.find('.cart_total_price.total_price span#extra_fee_total_price_label').text().trim();
                                                            var total_paid_opc = AppOPC.$opc_step_review.find('.cart_total_price.total_price span#extra_fee_total_price').text().trim();
                                                            if (!$.isEmpty(label_total_paid_opc) && !$.isEmpty(total_paid_opc)) {
                                                                var $div_total_paid_opt = $('<div/>').attr('class', 'alert alert-info text-center').html(label_total_paid_opc + ' ' + total_paid_opc);
                                                                $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE + ' #onepagecheckoutps').append($div_total_paid_opt);
                                                            }

                                                            $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE + ' #onepagecheckoutps').append(html).removeAttr('class');

                                                            AppOPC.$opc.find('#btn_other_payments').removeClass('hidden');

                                                            window.scrollTo(0, $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE).offset().top);

                                                            //Support module: postfinancecw - customweb ltd - v2.1.266
                                                            if ($.strpos(name_payment, 'postfinancecw') !== false) {
                                                                var _script = document.createElement('script');
                                                                _script.type = 'text/javascript';
                                                                _script.src = baseDir + '/modules/postfinancecw/js/frontend.js';
                                                                $("body").append(_script);
                                                            }
                                                        }
                                                    };
                                                    $.makeRequest(_json);

                                                    return false;
                                                }/* else if ($.inArray(name_payment, arr_payments_without_popup) >= 0 || $(radio_method_payment).hasClass('payment_eu')) {
                                                    window.location = url_payment;

                                                    return false;
                                                }*/

                                                _callbackCheckout = function() {
                                                    var arr_payment_with_content_only = ['braintreejs'];

                                                    if ($.inArray(name_payment, arr_payment_with_content_only) != -1) {
                                                        AppOPC.jqOPC('<div/>').load(url_payment+'?content_only=1', function(){
                                                            $(this).find('.page-heading, #cart_navigation').remove();

                                                            Fronted.showModal({name: 'payment_modal_'+name_payment, type:'normal', title: OnePageCheckoutPS.Msg.confirm_payment_method, title_icon: 'fa-pts-credit-card', content : $(this).html(), close : true});
                                                        });
                                                    } else {
                                                        AppOPC.jqOPC('<div/>').load(url_payment, function(){
                                                            //redirecciona a metodos de pagos que se realicen por fuera de la tienda.
                                                            var $that = $(this).find(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE);

                                                            if (!OnePageCheckoutPS.CONFIGS.OPC_SHOW_POPUP_PAYMENT){
                                                                if (($that.find('input[type=submit]').length == 1 || $that.find('button[type=submit]').length == 1)){
                                                                    $that.hide().appendTo('body');
                                                                    $that.find('input[type=submit]').attr('onclick','').trigger('click');
                                                                    $that.find('button[type=submit]').attr('onclick','').trigger('click');

                                                                    return false;
                                                                }

                                                                //support module: add_gopay_new
                                                                if (($that.find('#submit_payment').length == 1)){
                                                                    $that.hide().appendTo('body');
                                                                    $that.find('#submit_payment').trigger('click');

                                                                    return false;
                                                                }
                                                            }

                                                            //limpiamos el html devuelto por el metodo de pago para no colocar basura
                                                            $that.find('h2, h1').first().remove();
                                                            $that.find('.breadcrumb').remove();
                                                            $that.find('#order_step').remove();
                                                            $that.find('#currency_payment').hide(); //remueve el select de las divisas que hace recargar la web.
                                                            $that.find(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE).attr('style', 'width: auto!important');
                                                            $that.find('button[type="submit"]').removeAttr('class').addClass('button btn btn-primary');

                                                            $.each($that.find('a'), function(i, a){
                                                               if ($.strpos($(a).attr('href'), 'step=3'))
                                                                $(a).remove();
                                                            });

                                                            //elimina esta clase del input del carrito, para que salgan de nuevo los popup para seleccionar punto de envio.
                                                            $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container .module_carrier').each(function(i, carrier){
                                                                var id_delivery_option_selected = $(carrier).val();
                                                                $('#' + id_delivery_option_selected).removeClass('point_selected');
                                                            });

                                                            Fronted.showModal({name: 'payment_modal', type:'normal', title: OnePageCheckoutPS.Msg.confirm_payment_method, title_icon: 'fa-pts-credit-card', content : $that, close : true});
                                                        });
                                                    }

                                                    if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_POPUP_PAYMENT){
                                                        Address.load({object: 'customer'});

                                                        //recarga las listas de las direcciones
                                                        if (OnePageCheckoutPS.IS_LOGGED && !OnePageCheckoutPS.CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO) {
                                                            Address.loadAddressesCustomer({object: 'delivery'});
                                                            Address.loadAddressesCustomer({object: 'invoice'});
                                                        }
                                                    }
                                                }

                                                if ( !AppOPC.load_offer || typeof mustCheckOffer === 'undefined' || (event_dispatcher !== undefined && event_dispatcher !== 'confirm') ) {
                                                    _callbackCheckout();
                                                }
                                            }
                                        });
                                    }
                                }else{
                                    var callback_placeorder = function(){
                                        $.each(events_payments, function(k, items){
                                            if (param.position_element.item_parent == k){
                                                $.each(items, function(i, item){
                                                    if (param.position_element.item_child == i){
                                                        //support paypal
                                                        if (item.module_name == 'paypal'){
                                                            if ($(item.element).attr('id') == 'paypal_process_payment') {
                                                                $("#onepagecheckoutps_step_three #paypal_payment_form, #onepagecheckoutps_step_three #paypal_payment_form_payment")[0].submit();
                                                                return false;
                                                            }
                                                        }

                                                        $(item.element).attr('onclick', '').unbind('click');

                                                        if (typeof item.event !== typeof undefined){
                                                            $(item.element).click(item.event);
                                                        }

                                                        if (typeof item.onclick !== typeof undefined)
                                                        {
                                                            $(item.element).attr('onclick', item.onclick);
                                                        }

                                                        $('div#onepagecheckoutps #onepagecheckoutps_step_three form').on('submit', function(event) {
                                                            $(event.target)[0].submit();
                                                            event.preventDefault();
                                                            event.stopPropagation();
                                                        });

                                                        if ($(item.element).is('a, span')){
                                                            $(item.element)[0].click();
                                                        }else{
                                                            $(item.element).click();
                                                        }

                                                        Fronted.loadingBig(false);

                                                        return false;
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }

                                var force_load_payments = false;

                                //recarga de nuevo los metodos de pago para actualizar los formularios que tengan datos del cliente por defecto.
                                if ((data.isSaved && !OnePageCheckoutPS.CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO) || force_load_payments)
                                {
                                    var arr_exception_modules = ['authorizedotnet', 'firstdata', 'stripe_official'];
                                    var name_payment = AppOPC.$opc_step_three.find('.payment_radio:checked').val();

                                    if ($.inArray(name_payment, arr_exception_modules) == -1) {
                                        PaymentPTS.getByCountry({show_loading: false, callback: callback_placeorder});
                                    } else {
                                        if (typeof callback_placeorder === 'function') {
                                            callback_placeorder();
                                        }
                                    }
                                }
                                else {
                                    if (typeof callback_placeorder === 'function') {
                                        callback_placeorder();
                                    }
                                }

                            }
                        }
                    },
                    complete: function() {
                        if (typeof $.totalStorageOPC !== typeof undefined) {
                            $.totalStorageOPC('create_invoice_address_'+OnePageCheckoutPS.id_shop, false);
                        }

                        AppOPC.$opc.find('#btn_place_order').removeAttr('disabled');

                        if (typeof callback_load_address === 'function') {
                            callback_load_address();
                        }
                    },
                    error: function(data){
                        alert(data);
                        Fronted.loadingBig(false);
                        AppOPC.$opc.find('#btn_place_order').removeAttr('disabled');
                    }
                };

                var callback = function() {
                    $.makeRequest(_json);
                }

                if ((OnePageCheckoutPS.CONFIGS.OPC_CONFIRM_ADDRESS && !OnePageCheckoutPS.IS_VIRTUAL_CART) && module_carrier_selected != 'mondialrelay') {
                    var address = AppOPC.$opc_step_one.find('#delivery_address1').length > 0 ? AppOPC.$opc_step_one.find('#delivery_address1').val() : '';
                    var address2 = AppOPC.$opc_step_one.find('#delivery_address2').length > 0 ? ', ' + AppOPC.$opc_step_one.find('#delivery_address2').val() : '';
                    var postcode = AppOPC.$opc_step_one.find('#delivery_postcode').length > 0 ? ', ' + AppOPC.$opc_step_one.find('#delivery_postcode').val() : '';
                    var city = AppOPC.$opc_step_one.find('#delivery_city').length > 0 ? ', ' + AppOPC.$opc_step_one.find('#delivery_city').val() : '';
                    var state = AppOPC.$opc_step_one.find('#delivery_id_state option').length > 0 ? ' (' + AppOPC.$opc_step_one.find('#delivery_id_state option:selected').data('text') + ')' : '';
                    var customer_address = '<b>' + address + address2 + postcode + city + state + '</b>';
                    customer_address = OnePageCheckoutPS.Msg.message_validate_address.replace('%address%', customer_address);

                    Fronted.showModal({
                        name: 'modal_confirm_address',
                        type:'normal',
                        title: OnePageCheckoutPS.Msg.validate_address,
                        title_icon: 'fa-pts-map-marker ',
                        content : customer_address,
                        button_close: true,
                        button_ok: true,
                        callback_ok: function(){
                            supportModuleGDPR(callback);
                            return true;
                        },
                        callback_close: function() {
                            AppOPC.$opc.find('#btn_place_order').removeAttr('disabled');
                            return true;
                        }
                    });
                } else {
                    supportModuleGDPR(callback);
                }
            }
        }

        return true;
    }
}

var OPC_External_Validation = {
    validations: [],
    init: function(){
        OPC_External_Validation.validations['review:placeOrder'] = Array();

        // <editor-fold defaultstate="collapsed" desc="validations">
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            if (typeof carrier_selector !== typeof undefined && typeof sf_carriers_id !== typeof undefined && typeof sf_delivery_mode_is_selected !== typeof undefined) {
                var selected_carrier = $(carrier_selector + ':checked').length ?
                    $(carrier_selector + ':checked') : $(carrier_selector + '[checked=checked]');
                var result = selected_carrier.val().match('^([0-9]*)')[1];

                if (!in_array(result, sf_carriers_id)){
                    return (true);
                }

                if (!sf_delivery_mode_is_selected){
                    alert($('#must-select').val());
                    return false;
                }
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            if ($('#deliverydays_day option').length > 0) {
                if($.isEmpty($('#deliverydays_day').val())){
                    alert(OnePageCheckoutPS.Msg.select_date_shipping);

                    return false;
                }
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            if ($('#shipping_date').length > 0) {
                if($.isEmpty($('#shipping_date').val())){
                    alert(OnePageCheckoutPS.Msg.select_date_shipping);
                    return false;
                }
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            /* support planningdeliverybycarrier */
            if ($('#day_slots #date_delivery').length > 0) {
                if($.isEmpty($('#day_slots #date_delivery').val())){
                    alert(OnePageCheckoutPS.Msg.select_date_shipping);
                    return false;
                }
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            if ($('#day_slots #id_planning_delivery_slot').length > 0) {
                if($('#day_slots #id_planning_delivery_slot').val() == '-'){
                    alert(OnePageCheckoutPS.Msg.select_date_shipping);
                    return false;
                }
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            if ($('#onepagecheckoutps_step_two .delivery_option.selected div.extra_info_carrier a.select_pickup_point').length > 0){
                if (AppOPC.$opc_step_two.find('#relay_point_selected_box').length <= 0) {
                    alert(OnePageCheckoutPS.Msg.need_select_pickup_point);

                    $('#onepagecheckoutps_step_two .delivery_option.selected div.extra_info_carrier a.select_pickup_point').trigger('click');

                    return false;
                }
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            if ($('#onepagecheckoutps_step_two .delivery_option.selected #correospaq').length > 0 &&
                $('#onepagecheckoutps_step_two .delivery_option.selected #correospaq #selectedpaq_code').val() == '')
            {
                if (typeof Correos !== typeof undefined) {
                    Correos.callAlert(CorreosMessage.noPaqsSelected);
                } else {
                    alert(OnePageCheckoutPS.Msg.need_select_pickup_point);
                }

                return false;
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            if ($('#onepagecheckoutps_step_two .packetery-branch-list select').length > 0 &&
                $('#onepagecheckoutps_step_two .packetery-branch-list select').val() == '' &&
                Boolean($('.delivery_option_radio:checked').attr('packetery-initialized')))
            {
                alert(OnePageCheckoutPS.Msg.need_select_pickup_point);

                return false;
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            //support carrier module - shaim_baliknapostu v1.7.4
            if ($('#onepagecheckoutps_step_two .hook_extracarrier #najdi_postu').length > 0)
            {
                if ($('#onepagecheckoutps_step_two .hook_extracarrier #najdi_postu').is(':visible')) {
                    alert(OnePageCheckoutPS.Msg.need_select_pickup_point);

                    return false;
                }
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            //support module: carrierpickupstore
            if (typeof id_carrierpickupstore !== typeof undefined) {
                if (AppOPC.$opc_step_two.find('.delivery_option.selected .delivery_option_radio[value=\'' + id_carrierpickupstore + ',\']').is(':checked')) {
                    if (AppOPC.$opc_step_two.find('.opt_id_store').val() == '0') {
                        alert(OnePageCheckoutPS.Msg.need_select_pickup_point);

                        return false;
                    }
                }
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            //support module: deliverydateswizard
            if (typeof ddw !== typeof undefined) {
                var ddw_error = false;

                if ($("input[name='chk_timeslot']").length > 0 &&  $("input[name='chk_timeslot']").is(":checked") == false)
                    ddw_error = true;

                if (ddw.$input_ddw_order_date.val() == '0000-00-00 00:00:00' || ddw.$input_ddw_order_date.val() == '')
                    ddw_error = true;

                if (ddw_error && ddw.required == 1){
                    ddw.showRequiredError();

                    return false;
                }
            }
        });
        OPC_External_Validation.validations['review:placeOrder'].push(function() {
            //support module: envoimoinscher
            if (typeof Emc !== typeof undefined) {
                if (typeof Emc.validateCarrierForm !== typeof undefined) {
                    if (!Emc.validateCarrierForm(true)) {
                        return false;
                    }
                }
            }
        });
        // </editor-fold>
    },
    execute: function(step) {
        var is_valid = true;

        if (typeof OPC_External_Validation.validations[step] !== typeof undefined) {
            $.each(OPC_External_Validation.validations[step], function(i, external_validation) {
                if (external_validation() === false) {
                    is_valid = false;

                    return false;
                }
            });
        }

        return is_valid;
    }
}

function updateExtraCarrier(id_delivery_option, id_address)
{
	$.ajax({
		type: 'POST',
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		cache: false,
		dataType : "json",
		data: 'is_ajax=true'
			+'&action=updateExtraCarrier'
			+'&id_address='+id_address
			+'&id_delivery_option='+id_delivery_option
			+'&token='+static_token
			+'&allow_refresh=1',
		success: function(jsonData)
		{
			$('#HOOK_EXTRACARRIER_'+id_address).html(jsonData['content']);
		}
	});
}

function confirmFreeOrder()
{
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		cache: false,
		dataType : "html",
		data: 'ajax=true&method=makeFreeOrder&token=' + static_token ,
		success: function(html)
		{
			AppOPC.$opc.find('#btn_place_order').removeClass('disabled');
			var array_split = html.split(':');
			if (array_split[0] == 'freeorder')
			{
				if (!$('#checkbox_create_account_guest').is(':checked') && !OnePageCheckoutPS.IS_LOGGED)
					document.location.href = OnePageCheckoutPS.GUEST_TRACKING_URL+'?id_order='+encodeURIComponent(array_split[1])+'&email='+encodeURIComponent(array_split[2]);
				else
					document.location.href = OnePageCheckoutPS.HISTORY_URL;
			}else{
                            //Fronted.closeDialog();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log('ERROR AJAX: ' + textStatus, errorThrown);
        }
	});
}

function supportModuleGDPR(callback) {
    //support module: m4gdpr - v1.1.2 - PrestaAddons
    if (typeof m4gdprConsent !== typeof undefined && !AppOPC.m4gpdr && (!OnePageCheckoutPS.IS_LOGGED || OnePageCheckoutPS.IS_GUEST)) {
        $.each(m4gdprConsent.elements, function (i, element) {
            if (element.element == 'onepagecheckoutps') {
                function disableSubmitButton() {
                    $('.vex-dialog-buttons button[type="submit"]').prop('disabled', true);
                }
                function enableSubmitButton() {
                    $('.vex-dialog-buttons button[type="submit"]').prop('disabled', false);
                }
                function isAllChecked(id) {
                    var allChecked = true;
                    $('#m4gdpr-dialog-' + id + ' input[type="checkbox"]').each(function() {
                        if ($(this).hasClass('required') && false == $(this).prop('checked')) {
                            allChecked = false;
                        }
                    });

                    return allChecked;
                }

                var buttons = [
                    jQuery.extend({}, vex.dialog.buttons.YES, {
                        text: element.count ? m4gdprConsent.buttonText.submit : m4gdprConsent.buttonText.accept
                    })
                ];

                if (element.count) {
                    buttons.push(
                        jQuery.extend({}, vex.dialog.buttons.NO, {
                            text: m4gdprConsent.buttonText.cancel, click: function() {
                                AppOPC.$opc.find('#btn_place_order').removeAttr('disabled');
                                this.value = false;
                                this.close();
                                return false;
                            }
                        })
                    );
                }

                var vexDialog = vex.dialog.open({
                    unsafeMessage: m4gdprConsent.message,
                    input: (element && element.input) ? element.input.join('') : '',
                    buttons: buttons,
                    className: m4gdprConsent.className,
                    afterOpen: function() {
                        $('#m4gdpr-dialog-' + element.id).on('change', 'input[type="checkbox"]', function() {
                            if (isAllChecked(element.id)) {
                                enableSubmitButton();
                            } else {
                                disableSubmitButton();
                            }
                        });
                    },
                    onSubmit: function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();

                        if (!isAllChecked(element.id)) {
                            disableSubmitButton();
                            return false;
                        } else {
                            var params = $('.vex-dialog-form').serializeArray();

                            params.push({'name': 'from', 'value': AppOPC.$opc_step_one.find('#customer_email').val()});

                            $.post(m4gdprBaseUri + 'index.php', params);

                            AppOPC.m4gpdr = true;

                            vexDialog.close();
                            callback();
                        }
                    }
                });

                if (isAllChecked(element.id)) {
                    enableSubmitButton();
                } else {
                    disableSubmitButton();
                }
            }
        });

        return false;
    }

    callback();
}

function updateCarrierSelectionAndGift(params) {
    var param = $.extend({}, {
        load_carrier: false
    }, params);

    var $delivery_selected = $('#opc_payment_methods input.delivery_option_radio:checked');

    if ($delivery_selected.length > 0) {
        var data = {
            ajax: true,
            method: 'updateCarrierAndGetPayments',
            token: static_token,
            recyclable: '',
            gift_message: '',
            gift: ''
        };

        data[$delivery_selected.attr('name')] = $delivery_selected.val();

        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: orderOpcUrl + '?rand=' + new Date().getTime(),
            dataType : "json",
            data: data,
            beforeSend: function() {
                Fronted.loadingBig(true);
            },
            success: function(json) {
                $('#opc_payment_methods').children('.alert').remove();
                $('#opc_payment_methods #carrier_area').replaceWith(json.carrier_data.carrier_block);
                $('#opc_payment_methods #HOOK_BEFORECARRIER').html(json.HOOK_BEFORECARRIER);

                $('#opc_payment_methods #opc_delivery_methods #cgv').remove();

                setTimeout(function(){
                    Fronted.removeUniform({parent_control: '#opc_payment_methods'});
                }, 3000);
            },
            complete: function() {
                if (param.load_carrier) {
                    Carrier.getByCountry({reset_carrier_embed: false});
                }

                Fronted.loadingBig(false);
            }
        });
    }
}
function updateCarrierList(){}
function updatePaymentMethods(){}
function updatePaymentMethodsDisplay(){}
function cleanSelectAddressDelivery(){}

//compatibilidad modulo crosselling
function loadJavaScriptReview(){
    $(function(){
        if ($('#crossselling_list_car').length > 0) {
            if (!!$.prototype.bxSlider) {
                $('#crossselling_list_car').bxSlider({
                    minSlides: 2,
                    maxSlides: 6,
                    slideWidth: 178,
                    slideMargin: 20,
                    pager: false,
                    nextText: '',
                    prevText: '',
                    moveSlides:1,
                    infiniteLoop:false,
                    hideControlOnEnd: true
                });
            }
        }
//        if($('#crossselling_list').length > 0)
//        {
//        	//init the serialScroll for thumbs
//        	cs_serialScrollNbImages = $('#crossselling_list li').length;
//        	cs_serialScrollNbImagesDisplayed = 5;
//        	cs_serialScrollActualImagesIndex = 0;
//        	$('#crossselling_list').serialScroll({
//        		items:'li',
//        		prev:'a#crossselling_scroll_left',
//        		next:'a#crossselling_scroll_right',
//        		axis:'x',
//        		offset:0,
//        		stop:true,
//        		onBefore:cs_serialScrollFixLock,
//        		duration:300,
//        		step: 1,
//        		lazy:true,
//        		lock: false,
//        		force:false,
//        		cycle:false
//        	});
//        	$('#crossselling_list').trigger( 'goto', [ (typeof cs_middle !== 'undefined' ? cs_middle : middle)-3] );
//        }

//        $('#onepagecheckoutps_step_review #gift-products_block .ajax_add_to_cart_button').die('click');

//
            $('#onepagecheckoutps_step_review .ajax_add_to_cart_button').unbind('click').click(function(event){
                var idProduct = 0;

                if (!$.isEmpty($(event.currentTarget).attr('data-id-product')))
                    idProduct = $(event.currentTarget).attr('data-id-product');
                else
                    idProduct =  $(this).attr('rel').replace('ajax_id_product_', '');

                if ($('#onepagecheckoutps_step_review #gift-products_block').length > 0){
                    event.preventDefault();
                    window.location = $(event.currentTarget).attr('href');

                    return false;
                }

                if (!$.isEmpty(idProduct)){
                    ajaxCart.add(idProduct, null, false, this);
                    Carrier.getByCountry();

                    return false;
                }
            });
//        }

        $('#onepagecheckoutps_step_review .ajax_add_to_cart_button').css({visibility: 'visible'});

        //compatibilidad con modulo CheckoutFields
        if (typeof checkoutfields !== 'undefined')
            checkoutfields.bindAjaxSave();

        //compatibilidad con modulo paragonfaktura
        $('#pfform input').click(function(){
            var value = $('#pfform input:checked').val();
            var id_cart = $('#pfform #pf_id').val();
            $.ajax({
              type: "POST",
              url: "modules/paragonfaktura/save.php",
              data: { value: value, id_cart: id_cart }
            }).done(function( msg ) {

            });
		});
    });
}

function opc_callback_error_payment(name_module, params) {
    if (name_module == 'braintree') {
        Fronted.loadingBig(false);

        if (typeof params.errorMsg !== typeof undefined && params.errorMsg) {
            Fronted.showModal({type: 'warning', message: params.msg});
        }
    }
}

//compatibilidad con modulo nacex.
function modalWin(url) {
	var LeftPosition = (screen.width) ? (screen.width-700)/2 : 0;
  	var TopPosition = (screen.height) ? (screen.height-500)/2 : 0;
	window.open(url,'','height=550,width=820,top='+(TopPosition-10)+',left='+LeftPosition+',toolbar=no,directories=no,status=no,menubar=no,scrollbars=si,resizable=no,location=no,modal=yes');
}
function seleccionadoNacexShop(tipo, txt) {
    setDatosSession(txt);

    $('#' + Carrier.id_delivery_option_selected).addClass('point_selected');
}
function setDatosSession(txt){
    $.ajax({
		type: 'POST',
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		data: 'action=setFieldsNacex&is_ajax=true&txt=' + txt + '&token=' + static_token,
		success: function(){
			Carrier.getByCountry();
		}
    });
}
//support module payment: Sveawebpay
function getAddressSveawebpay(){
    var ssn = $("#sveawebpay_security_number").val();
    var md5v = $("#sveawebpay_md5").val();

    $.get(baseDir + 'modules/sveawebpay/sveagetaddress.php', {ssn: ssn, md5:hex_md5(ssn+md5v), email:'', iscompany:false, sveatype: 1, country: 'SE', isinvoice: true, quickcall: true},
        function(data){
            if(data!='-1')
            {
                var parts = data.split('*');
                var names = parts[0].split(' ');
                var lastname = $.trim(names[0]);
                var firstname = '';
                var address_one=$.trim(parts[1]);
                var address_two=$.trim(parts[2]);
                if(address_one=='')
                {
                    address_one=address_two;
                    address_two='';
                }
                for(var i in names)
                {
                    if(i!=0)
                    {
                        firstname = firstname + ' ' + names[i];
                        firstname = firstname.replace(",", "");
                    }
                }
                lastname = lastname.replace(",", "");
                $('#customer_firstname').val($.trim(firstname));
                $('#customer_lastname').val(lastname);
                $('#delivery_address1').val(address_one);
                $('#delivery_address2').val(address_two);
                $('#delivery_postcode').val($.trim(parts[3]));
                $('#delivery_city').val($.trim(parts[4]));
                $('#delivery_dni').val(ssn);
            }
	});
}

function reloadPage(){
	location.reload();
}
function addslashes(str) {
  //  discuss at: http://phpjs.org/functions/addslashes/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Ates Goral (http://magnetiq.com)
  // improved by: marrtins
  // improved by: Nate
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  //   input by: Denny Wardhana
  //   example 1: addslashes("kevin's birthday");
  //   returns 1: "kevin\\'s birthday"

  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}

function version_compare(v1, v2, operator) { // eslint-disable-line camelcase
  //       discuss at: http://locutus.io/php/version_compare/
  //      original by: Philippe Jausions (http://pear.php.net/user/jausions)
  //      original by: Aidan Lister (http://aidanlister.com/)
  // reimplemented by: Kankrelune (http://www.webfaktory.info/)
  //      improved by: Brett Zamir (http://brett-zamir.me)
  //      improved by: Scott Baker
  //      improved by: Theriault (https://github.com/Theriault)
  //        example 1: version_compare('8.2.5rc', '8.2.5a')
  //        returns 1: 1
  //        example 2: version_compare('8.2.50', '8.2.52', '<')
  //        returns 2: true
  //        example 3: version_compare('5.3.0-dev', '5.3.0')
  //        returns 3: -1
  //        example 4: version_compare('4.1.0.52','4.01.0.51')
  //        returns 4: 1

  // Important: compare must be initialized at 0.
  var i
  var x
  var compare = 0

  var vm = {
    'dev': -6,
    'alpha': -5,
    'a': -5,
    'beta': -4,
    'b': -4,
    'RC': -3,
    'rc': -3,
    '#': -2,
    'p': 1,
    'pl': 1
  }

  var _prepVersion = function (v) {
    v = ('' + v).replace(/[_\-+]/g, '.')
    v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.')
    return (!v.length ? [-8] : v.split('.'))
  }

  var _numVersion = function (v) {
    return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10))
  }

  v1 = _prepVersion(v1)
  v2 = _prepVersion(v2)
  x = Math.max(v1.length, v2.length)
  for (i = 0; i < x; i++) {
    if (v1[i] === v2[i]) {
      continue
    }
    v1[i] = _numVersion(v1[i])
    v2[i] = _numVersion(v2[i])
    if (v1[i] < v2[i]) {
      compare = -1
      break
    } else if (v1[i] > v2[i]) {
      compare = 1
      break
    }
  }
  if (!operator) {
    return compare
  }

  switch (operator) {
    case '>':
    case 'gt':
      return (compare > 0)
    case '>=':
    case 'ge':
      return (compare >= 0)
    case '<=':
    case 'le':
      return (compare <= 0)
    case '===':
    case '=':
    case 'eq':
      return (compare === 0)
    case '<>':
    case '!==':
    case 'ne':
      return (compare !== 0)
    case '':
    case '<':
    case 'lt':
      return (compare < 0)
    default:
      return null
  }
}

jQuery.expr[':'].ptsContains = function(a, i, m) { return jQuery(a).text().toUpperCase() .indexOf(m[3].toUpperCase()) >= 0; };

var reload_init_opc = setInterval(function(){
    if (typeof AppOPC !== typeof undefined){
        if(!AppOPC.initialized)
            AppOPC.init();
        else
            clearInterval(reload_init_opc)
    }
}, 2000);

var remove_uniform_aux = false;
var remove_uniform = setInterval(function(){
    if(!remove_uniform_aux){
        Fronted.removeUniform();
        remove_uniform_aux = true;
    }else
        clearInterval(remove_uniform)
}, 10000);