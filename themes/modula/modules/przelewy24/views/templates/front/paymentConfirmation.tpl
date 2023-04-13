{capture name=path}{l s='Pay with Przelewy24' mod='przelewy24'}{/capture}
{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div style="clear:both"></div>
<script type="text/javascript">
    function formSubmit(description) {
        var form = document.getElementById('przelewy24Form');

        if ($('#przelewy24FormRecuring input[name=p24_cc]').val() > 0) {
            form = document.getElementById('przelewy24FormRecuring');
        }

        if ($('#p24_regulation_accept').length) {
            $(form).find('input[name=p24_regulation_accept]').val($('#p24_regulation_accept').length ? 1 : 0);
        }

        if (description != undefined) {
            $('#orderDescription').val(description);
        }

        form.submit();
    }


    function proceedPayment() {

        {if $p24_validationRequired == 2 && empty($get_order_id)}
        $('#proceedPaymentLink').closest('a').addClass('disabled').attr('disabled', 1);
        $.ajax('{$p24_ajax_url}', {
            method: 'POST', type: 'POST',
            data: {
                action: 'makeOrder',
                cart_id: '{$cartId}'
            },
            error: function () {
                formSubmit();
            },
            success: function (response) {
                var data = JSON.parse(response);
                formSubmit(data.description);
            }
        });
        {else}
        formSubmit();
        {/if}
    }

    {if $accept_in_shop}
    $(document).ready(function () {
        if ($('#p24_regulation_accept').length) {
            $('#p24_regulation_accept').click(function () {
                update_p24_regulation_accept_checkbox();
            });
            update_p24_regulation_accept_checkbox();
        }
    });

    function update_p24_regulation_accept_checkbox() {
        if ($('#p24_regulation_accept:checked').length) {
            $('input[name=p24_cc]').removeAttr('disabled');
            $('#przelewy24FormRecuring,#przelewy24Form').css('opacity', 1);
            if ($('input[name=p24_cc]:checked').length == 0) {
                $('input[name=p24_cc][value=0]').click().trigger('change');
            }
            $('input[name=p24_cc]:checked:first').click().trigger('change');
            if ($('input[name=p24_cc][value=ajax_card]').length == 0) {
                $('#proceedPaymentLink').closest('a').fadeIn();
            }
        } else {
            $('input[name=p24_cc]').attr('disabled', true);
            $('#przelewy24FormRecuring,#przelewy24Form').css('opacity', 0.4);
            $('#P24AjaxCardPaymentJs,#P24AjaxCardPaymentCss,#P24FormContainer').remove();
            $('#P24FormArea').hide();
            $('#proceedPaymentLink').closest('a').hide();
        }
    }

    {/if}

    function payInShopSuccess(status) {
        $.ajax('{$p24_url_status}', {
            method: 'POST', type: 'POST',
            data: {
                action: 'trnVerify',
                p24_session_id: '{$p24_session_id}',
                p24_order_id: status,
            },
            error: function () {
                payInShopFailure();
            },
            success: function (response) {
                window.location = '{$p24_url_return}';
            }
        });

    }

    function payInShopFailure() {
        $('#P24FormArea').html("<span class='info'>{l s='Script loading failure. Try again or choose another payment method.' mod='przelewy24'}</span>");
        $('#P24FormArea:not(:visible)').slideDown();
        P24_Transaction = undefined;
        window.location = '{$p24_url_return}{if $get_order_id}?order_id={$get_order_id}{/if}';
    }
    var payInShopScriptRequested = false;

    function setP24method(method) {
        $('form#przelewy24Form input[name=p24_method]').val(parseInt(method) > 0 ? parseInt(method) : "");
    }

    function setP24recurringId(id) {
        $('form#przelewy24FormRecuring input[name=p24_cc]').val(parseInt(id) > 0 ? parseInt(id) : "");
    }

    function onResize() {
        if ($(window).width() <= 640) {
            $('.payMethodList').addClass('mobile');
        } else {
            $('.payMethodList').removeClass('mobile');
        }
    }

    $(document).ready(function () {
        $('head').append('<link rel="stylesheet" href="{$p24_css}" type="text/css" />');

        $('input[name=p24_cc]:visible').change(function () {

            setP24recurringId($(this).val());

            if ($(this).val() == 'last_method') {
                $('#przelewy24lastmethod_img').removeClass('inactive');
                setP24method($(this).attr('data-method'));
            } else {
                $('#przelewy24lastmethod_img').addClass('inactive');
                setP24method("");
            }

            if ($(this).val() == '0') {
                $('.payMethodList:not(:visible)').slideDown();
                setP24method("");
                if ($('.payMethodList .bank-box.selected').length) {
                    setP24method($('.payMethodList .bank-box.selected:first').attr('data-id'));
                }
            } else {
                $('.payMethodList:visible').slideUp();
            }

            if (parseInt($(this).val()) > 0) {
                setP24method("");
            }

            if ($(this).val() == 'ajax_card') {
                setP24method("");
                $('#proceedPaymentLink').closest('a').fadeOut();
                if (typeof P24_Transaction == 'object') {
                    $('#P24FormArea').slideDown();
                } else {
                    requestJsAjaxCard();
                }
            } else {
                $('#P24FormArea').slideUp();
                if ($(this).is(':not(:disabled)')) {
                    $('#proceedPaymentLink:not(:visible)').closest('a').fadeIn();
                }
            }
        });
        if ($('input[name=p24_cc]:checked').length == 0) {
            $('input[name=p24_cc]:visible:first').click();
        }
        $('input[name=p24_cc]:checked').trigger('change');

        $('.bank-box').click(function () {
            $('.bank-box').removeClass('selected').addClass('inactive');
            $(this).addClass('selected').removeClass('inactive');
            setP24method($(this).attr('data-id'));
            setP24recurringId($(this).attr('data-cc'));
        });
        onResize();
    });

    $(window).resize(function () {
        onResize();
    });

    function requestJsAjaxCard() {
        $.ajax('{$p24_ajax_url}', {
            method: 'POST', type: 'POST',
            data: {
                action: 'trnRegister',
                p24_session_id: '{$p24_session_id}' {if $get_order_id},
                order_id: '{$get_order_id}' {/if}},
            error: function () {
                payInShopFailure();
            },
            success: function (response) {
                var data = JSON.parse(response);
                var dictionary = '{ldelim}' +
                        '"cardHolderLabel":"{l s='Cardholder name' mod='przelewy24'}", ' +
                        '"cardNumberLabel":"{l s='Card number' mod='przelewy24'}", ' +
                        '"cvvLabel":"{l s='cvv' mod='przelewy24'}", ' +
                        '"expDateLabel":"{l s='Expiry date' mod='przelewy24'}", ' +
                        '"payButtonCaption":"{l s='Confirm' mod='przelewy24'}", ' +
                        '"threeDSAuthMessage":"{l s='Click here to continue shopping' mod='przelewy24'}"' +
                        '{rdelim}';
                $('#P24FormArea').html("");
                $("<div></div>")
                        .attr('id', 'P24FormContainer')
                        .attr('data-sign', '{$p24_sign}')
                        .attr('data-successCallback', 'payInShopSuccess')
                        .attr('data-failureCallback', 'payInShopFailure')
                        .attr('data-dictionary', dictionary)
                        .addClass('loading')
                        .appendTo('#P24FormArea')
                        .parent().slideDown()
                ;
                if (document.createStyleSheet) {
                    document.createStyleSheet(data.p24cssURL);
                } else {
                    $('head').append('<link rel="stylesheet" type="text/css" href="' + data.p24cssURL + '" />');
                }
                if (!payInShopScriptRequested) {
                    $.getScript(data.p24jsURL, function () {
                        P24_Transaction.init();
                        $('#P24FormContainer').removeClass('loading');
                        payInShopScriptRequested = false;
                        window.setTimeout(function () {
                            $('#P24FormContainer button').on('click', function () {
                                if (P24_Card.validate()) {
                                    $(this).hide().after('<div class="loading"></div>');
                                }
                            });
                        }, 1000);
                    });
                }
                payInShopScriptRequested = true;
            }
        });
    }

    function showPayJsPopup() {
        setP24method("");
        $('#P24FormAreaHolder').appendTo('body');
        $('#proceedPaymentLink').closest('a').fadeOut();

        $('#P24FormAreaHolder').fadeIn();
        if (typeof P24_Transaction != 'object') {
            requestJsAjaxCard();
        }
    }
    function hidePayJsPopup() {
        $('#P24FormAreaHolder').fadeOut();
        $('#proceedPaymentLink:not(:visible)').closest('a').fadeIn();

    }

    function p24_regulation_accept_checked(){
        $('#przelewy24Form input[name=p24_regulation_accept]').val($('#p24_regulation_accept:checked').length);
        $('#przelewy24FormRecuring input[name=p24_regulation_accept]').val($('#p24_regulation_accept:checked').length);
    }
</script>

<style>
    #P24FormAreaHolder {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 10000;
    }

    #P24FormAreaHolder > #P24FormArea.popup {
        float: none;
        box-shadow: 0 0 40px #000;
        margin: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -100px;
        margin-left: -175px;
    }

    #P24FormArea {
        background: lightgray;
        background: linear-gradient(to bottom, #ddd 0%, #bbb 100%);
        border-radius: 10px;
        padding: 1em;
        margin-bottom: 0.5em;
        width: 350px;
        min-height: 200px;
    }

    @media screen and (min-width: 992px) {
        #P24FormArea {
            float: right;
        }
    }

    .loading {
        background: transparent url({$base_url}img/loadingAnimation.gif) center center no-repeat;
        min-height: 60px;
    }

    #P24_cardHolder, #P24_cardNumber {
        padding-left: 0.3em;
    }

    #P24FormContainer {
        min-height: 170px;
    }

    #P24FormContainer button:hover {
        background: #3aa04c;
        background: linear-gradient(to bottom, #3aa04c 0%, #3aa04a 100%);
    }

    #P24FormContainer button {
        border-radius: 4px;
        font-size: 20px;
        line-height: 24px;
        color: #fff;
        padding: 0;
        font-weight: bold;
        background: #43b754;
        background: linear-gradient(to bottom, #43b754 0%, #41b757 2%, #41b854 4%, #43b756 6%, #41b354 38%, #44b355 40%, #45af55 66%, #41ae53 74%, #42ac52 91%, #41ae55 94%, #43ab54 96%, #42ac52 100%);
        border: 1px solid;
        border-color: #399a49 #247f32 #1a6d27 #399a49;
        padding: 11px 15px 10px 15px;
        margin: 10px 0 0 100px;
    }

    #P24FormContainer button:hover {
        background: #e94a59;
        background: linear-gradient(to bottom, #f87582 0%, #e94a59 100%);
    }

    #P24FormContainer button {
        border-radius: 4px;
        font-size: 20px;
        line-height: 24px;
        color: #fff;
        padding: 0;
        font-weight: bold;
        background: #db2032;
        background: linear-gradient(to bottom, #e94a59 0%, #db2032 100%);
        border: 1px solid;
        border-color: #af0d1d #e94a59 #89000d #af0d1d;
        padding: 11px 15px 10px 15px;
        margin: 10px auto 0 auto;
        display: block;
    }

    #P24FormContainer input {
        border: none;
        border-radius: 4px;
        height: 23px;
    }

    #P24FormArea span.info {
        margin: 3em 0;
        display: block;
        text-align: center;
    }

    #uniform-p24_regulation_accept {
        display: inline-block;
    }

    #przelewy24lastmethod_img.inactive, a.bank-box.inactive {
        opacity: 0.5;
        -webkit-filter: grayscale(1);
        -moz-filter: grayscale(1);
        -ms-filter: grayscale(1);
        -o-filter: grayscale(1);
        filter: grayscale(1);
        filter: gray;
    }

    label {
        cursor: pointer;
        cursor: hand;
    }

    a.bank-box.selected:before {
        font-family: FontAwesome;
        content: "\f00c";
        float: right;
        font-size: 4em;
        color: #db2032;
        margin: -8px -8px 0 0;
    }

    @font-face {
        font-family: 'FontAwesome';
        src: url("{$base_url}modules/przelewy24/css/fontawesome-webfont.eot?v=4.1.0");
        src: url("{$base_url}modules/przelewy24/css/fontawesome-webfont.eot?#iefix&v=4.1.0") format("embedded-opentype"),
        url("{$base_url}modules/przelewy24/css/fontawesome-webfont.woff?v=4.1.0") format("woff"),
        url("{$base_url}modules/przelewy24/css/fontawesome-webfont.ttf?v=4.1.0") format("truetype"),
        url("{$base_url}modules/przelewy24/css/fontawesome-webfont.svg?v=4.1.0#fontawesomeregular") format("svg");
        font-weight: normal;
        font-style: normal
    }

    .payMethodList li {
        display: inline-block;
        width: 20em;
    }

    .bank-box.recurring .bank-logo {
        padding-top: 23px;
        background: transparent url({$base_url}modules/przelewy24/img/cc_empty.png) center 2px no-repeat;
    }

    .bank-box.recurring .bank-logo > span {
        background-color: #fff;
        background-color: rgba(255, 255, 255, 0.5);
    }

    .bank-box.recurring .bank-logo-visa, .bank-box.recurring .bank-logo-93d207a5540aa38f404ae593385a7b64 { /* VISA */
        background: transparent url({$base_url}modules/przelewy24/img/cc_visa.png) center 2px no-repeat;
    }

    .bank-box.recurring .bank-logo-ecmc, .bank-box.recurring .bank-logo-b05c23fab98df11c755ba516f5df83c0 { /* ECMC */
        background: transparent url({$base_url}modules/przelewy24/img/cc_mastercard.png) center 2px no-repeat;
    }

    .bank-box.recurring .bank-logo-maestro, .bank-box.recurring .bank-logo-b4d6cac88f89a1862d9068f831eef183 { /* Maestro-Intl.*/
        background: transparent url({$base_url}modules/przelewy24/img/cc_maestro.png) center 2px no-repeat;
    }

    .bank-box.recurring .bank-logo-dc, .bank-box.recurring .bank-logo-8cf5364c4259be0f1a5010e052991c0e { /* Diners Club  */
        background: transparent url({$base_url}modules/przelewy24/img/cc_dinersclub.png) center 2px no-repeat;
    }

    .moreStuff, .lessStuff {
        text-align: center;
        border-bottom: 1px solid #ccc;
        width: 100%;
        max-width: 600px;
        margin-bottom: 2em;
        cursor: pointer;
        cursor: hand;
    }

    .moreStuff:hover, .lessStuff:hover {
        border-color: #bbb;
    }

    .moreStuff:hover:before, .lessStuff:hover:before {
        border-color: #bbb;
        background: #bbb;
    }

    .moreStuff:before, .lessStuff:before {
        font-family: FontAwesome;
        text-align: center;
        color: #fff;
        border: 2px solid #000;
        padding: 0.1em;
        border-radius: 3px;
        position: relative;
        top: 12px;
        cursor: pointer;
        cursor: hand;
        background: #000;
    }

    .moreStuff:before {
        content: "\f078    {l s='more payment methods' mod='przelewy24'}    \f078";
    }

    .lessStuff:before {
        content: "\f077     {l s='less payment methods' mod='przelewy24'}    \f077";
    }

    .bank-element {
        width: 137px;
        text-align: center;
    }

</style>

<div class="content box">
    <div class="content_in content_in_padding_top">
        <h2><a href="http://przelewy24.pl" target="_blank"><img src="{$modules_dir}przelewy24/img/logo.png"
                                                                alt="{l s='Payment confirmation' mod='przelewy24'}"/></a>&nbsp;{l s='Pay with Przelewy24' mod='przelewy24'}
        </h2>
        <hr/>
        {if $p24_show_summary}
            <h4>{l s='Transaction data:' mod='przelewy24'}</h4>
            <table>
			{*
                <tr>
                    <th>{l s='Order name' mod='przelewy24'}&nbsp;</th>
                    <td id="summary_opis">{$p24_description}</td>
                </tr>
				*}
                <tr>
                    <th>{l s='Customer' mod='przelewy24'}</th>
                    <td>{$p24_client}</td>
                </tr>
                <tr>
                    <th>{l s='E-mail' mod='przelewy24'}</th>
                    <td>{$p24_email}</td>
                </tr>
                <tr>
                    <th>{l s='Amount' mod='przelewy24'}</th>
                    <td id="p24-summary-amount">
                        {$p24_classic_amount}

                        {if !empty($extracharge_amount) }
                            ({l s='Added extra charge to order' mod='przelewy24'}:
                            {$extracharge_amount})
                        {/if}

                        {if !empty($extradiscount_amount) }
                            ({l s='Added extra discount to order' mod='przelewy24'}:
                            {$extradiscount_amount})
                        {/if}
                    </td>
                </tr>
                {if isset($p24FreeOrder) and $p24FreeOrder==true}
                    <tr>
                        <td colspan="2">
                            <strong>
                                {l s='Created order' mod='przelewy24'}
                            </strong>
                        </td>
                    </tr>
                {/if}
            </table>
            <hr/>
        {/if}

        {if empty($p24FreeOrder) or $p24FreeOrder!=true}

            {if isset($productsNumber) && $productsNumber <= 0}
                <p style="font-size: 16px; line-height: 20px;">{l s='Your shopping cart is empty.' mod='przelewy24'}</p>
            {else}

                {if $accept_in_shop}
                    <p>
                        <label for="p24_regulation_accept" style="font-weight: normal;">
                            <input type="checkbox" name="p24_regulation_accept" id="p24_regulation_accept"
                                   style="display: inline-block;" onchange="p24_regulation_accept_checked();"/>
                            {$accept_in_shop_translation|unescape:"html"}
                        </label>
                    </p>
                    <hr/>
                {/if}

                {if $oneclick && empty($p24_method) && ($ccards_forget != 0)}
                    <p>
                        {l s='In case of choosing credit/debit card as pyment method, it\'s reference number will be saved for further payments.' mod='przelewy24'}
                    <div class="checkbox">
                        <label class="control-label">
                            {l s='Memorize payment cards, which I pay' mod='przelewy24'}
                            <input type="checkbox" name="ccards_forget"
                                   {if $default_remember_card}checked="checked"{/if}
                                   onclick="$.ajax({ url: '{$p24_ajax_url}', method: 'POST', type: 'POST', data: { action: 'cc_forget', value: !$(this).is(':checked') } })"/>
                        </label>
                    </div>
                    </p>
                {/if}
                {if $accept_in_shop_accepted}
                    {$accept_in_shop_accepted_val = 1}
                {else}
                    {$accept_in_shop_accepted_val = 0}
                {/if}
                <form action="{$p24_url}" method="post" id="przelewy24Form" name="przelewy24Form"
                      accept-charset="utf-8">
                    <input type="hidden" name="p24_merchant_id" value="{$p24_merchant_id}"/>
                    <input type="hidden" name="p24_session_id" value="{$p24_session_id}"/>
                    <input type="hidden" name="p24_pos_id" value="{$p24_pos_id}"/>
                    <input type="hidden" name="p24_amount" value="{$p24_amount}"/>
                    <input type="hidden" name="p24_currency" value="{$p24_currency}"/>

                    <input type="hidden" name="p24_description" value="{$p24_description}" id="orderDescription"/>
                    <input type="hidden" name="p24_email" value="{$p24_email}"/>
                    <input type="hidden" name="p24_client" value="{$p24_client}"/>
                    <input type="hidden" name="p24_address" value="{$p24_address}"/>
                    <input type="hidden" name="p24_zip" value="{$p24_zip}"/>
                    <input type="hidden" name="p24_city" value="{$p24_city}"/>
                    <input type="hidden" name="p24_country" value="{$p24_country}"/>
                    <input type="hidden" name="p24_language" value="{$p24_language}"/>

                    <input type="hidden" name="p24_method" value="{$p24_method}"/>
                    {if $payslow_enabled}<input type="hidden" name="p24_channel" value="16"/>{/if}
                    {if isset($custom_timelimit)}<input type="hidden" name="p24_time_limit"
                                                        value="{$custom_timelimit}"/>{/if}

                    <input type="hidden" name="p24_encoding" value="{$p24_encoding}"/>
                    <input type="hidden" name="p24_url_status" value="{$p24_url_status}"/>
                    <input type="hidden" name="p24_url_return" value="{$p24_url_return}"/>
                    <input type="hidden" name="p24_api_version" value="{$p24_api_version}"/>
                    <input type="hidden" name="p24_ecommerce" value="{$p24_ecommerce}"/>
                    <input type="hidden" name="p24_ecommerce2" value="{$p24_ecommerce2}"/>
                    <input type="hidden" name="p24_sign" value="{$p24_sign}"/>
                    <input type="hidden" name="p24_regulation_accept" value="{$accept_in_shop_accepted_val}"/>
                    <input type="hidden" name="p24_wait_for_result" value="{$p24_wait_for_result}"/>
                    <input type="hidden" name="p24_shipping" value="{$p24_shipping}"/>

                    {foreach $p24ProductItems as $name => $value}
                        <input type="hidden" name="{$name}" value="{$value}"/>
                    {/foreach}

                    <p style="color: red;" id="ajaxOutput"></p>
                </form>
                {if (empty($p24_cards) && !$pay_in_shop && !$last_pay_method) || $p24_method}
                    {* nie ma recurringu, zapisanej ostatniej metody, nie ma kart JS, nie ma metod płatności w sklepie - lub po prostu zawczasu ustalono metodę płatności *}
                    {if $p24_paymethod_list && !$p24_method}
                        <p>{l s='Choose payment method, then press "Confirm" to complete the payment process.' mod='przelewy24'}</p>
                    {else}
                        <p><strong>{l s='Your payment method:' mod='przelewy24'}</strong></p>
                        {include file="./_bank_element.tpl" bank_id=$p24_method bank_name=$p24_paymethod_all[$p24_method]}
                        <p>{l s='Press "Confirm" to confirm your order and be redirected to przelewy24.pl, where you can complete the payment process.' mod='przelewy24'}</p>
                    {/if}
                    {if !$p24_method}
                        {$hidePayments=false}
                    {/if}
                    {if ($oneclick && empty($p24_method) && ($ccards_forget != 1)) || $extracharge_enabled}
                        <hr>
                    {/if}
                {else}
                    {if ($oneclick && empty($p24_method) && ($ccards_forget != 1)) || $extracharge_enabled}
                        <hr>
                    {/if}
                    <form action="{$p24_recuring_url}" method="post" id="przelewy24FormRecuring"
                          name="przelewy24FormRecuring" accept-charset="utf-8">
                        <input type="hidden" name="p24_session_id" value="{$p24_session_id}"/>
                        <input type="hidden" name="p24_amount" value="{$p24_amount}"/>
                        <input type="hidden" name="p24_currency" value="{$p24_currency}"/>
                        <input type="hidden" name="p24_description" value="{$p24_description}" id="orderDescription"/>
                        <input type="hidden" name="p24_email" value="{$p24_email}"/>
                        <input type="hidden" name="p24_client" value="{$p24_client}"/>
                        <input type="hidden" name="p24_regulation_accept" value="{$accept_in_shop_accepted_val}"/>
                        <input type="hidden" name="p24_cc"/>
                    </form>
                {/if}
                {if $p24_paymethod_list && !$p24_method}
                    {* pokazuj listę banków w sklepie *}
                    {if $p24_paymethod_graphics}
                        {* lista graficzna *}
                        <div id="P24FormAreaHolder" onclick="hidePayJsPopup();" style="display: none">
                            <div onclick="arguments[0].stopPropagation();" id="P24FormArea" class="popup"></div>
                        </div>
                        <div class="payMethodList">
                            {$ignoreArr=array()}
                            {if $last_pay_method}
                                {include file="./_bank_icon.tpl" bank_id=$last_pay_method bank_name="Ostatnio używane"}
                                {$ignoreArr[]=$last_pay_method}
                            {/if}

                            {foreach from=$p24_cclist key=id item=card}
                                {include file="./_bank_icon.tpl" bank_id=$card.type|md5 bank_name=$card.type text=$card.mask|substr:-9 class="recurring" cc_id=$id}
                            {/foreach}

                            {if $pay_in_shop}
                                {$ignoreArr[]=140}
                                {$ignoreArr[]=142}
                                {$ignoreArr[]=145}
                                {include file="./_bank_icon.tpl" bank_id=145 bank_name=$p24_paymethod_all.145 onclick="showPayJsPopup()"}
                            {/if}

                            {foreach $p24_paymethod_first as $bank_id}
                                {include file="./_bank_icon.tpl" bank_id=$bank_id bank_name=$p24_paymethod_all.$bank_id notIn=$ignoreArr onclick=""}
                            {/foreach}
                            <div style="clear:both"></div>
                            {if $p24_paymethod_second|sizeof > 0}
                                <div class="morePayMethods" style="display: none">
                                    {foreach $p24_paymethod_second as $bank_id}
                                        {include file="./_bank_icon.tpl" bank_id=$bank_id bank_name=$p24_paymethod_all.$bank_id notIn=$ignoreArr}
                                    {/foreach}
                                    {foreach $p24_paymethod_all as $bank_id => $bank_name}
                                        {if !in_array($bank_id, $p24_paymethod_first) && !in_array($bank_id, $p24_paymethod_second)}
                                            {include file="./_bank_icon.tpl" bank_id=$bank_id bank_name=$bank_name notIn=$ignoreArr}
                                        {/if}
                                    {/foreach}
                                    <div style="clear:both"></div>
                                </div>
                                <div class="moreStuff"
                                     onclick="$(this).fadeOut(100);$('.lessStuff').fadeIn(); $('.morePayMethods').slideDown()"
                                     title="Pokaż więcej metod płatności"></div>
                                <div class="lessStuff"
                                     onclick="$(this).fadeOut(100);$('.moreStuff').fadeIn(); $('.morePayMethods').slideUp()"
                                     title="Pokaż mniej metod płatności" style="display:none"></div>
                            {/if}
                        </div>
                    {else}
                        {* lista tekstowa *}
                        <ul>
                            {$ignoreArr=array()}
                            {if $last_pay_method}
                                {$ignoreArr[]=$last_pay_method}
                                <li>
                                    <input type="radio" id="przelewy24lastmethod" name="p24_cc" value="last_method"
                                           data-method="{$last_pay_method}"/>
                                    <label for="przelewy24lastmethod"
                                           style="font-weight:normal;position:relative; top:-3px;">
                                        {l s='Choose last used payment method' mod='przelewy24'}
                                        <div class="bank-logo bank-logo-{$last_pay_method}"
                                             id="przelewy24lastmethod_img"
                                             style="display: inline-block; margin-bottom: -22px;">
                                    </label>
                                </li>
                            {/if}

                            {foreach from=$p24_cards key=id item=desc name=foo}
                                <li>
                                    <input type="radio" name="p24_cc" id="p24_cc_{$id}" value="{$id}"
                                           {if $smarty.foreach.foo.first}checked="checked"{/if} />
                                    <label for="p24_cc_{$id}"
                                           style="font-weight:normal;position:relative; top:-3px;">{$desc}
                                        <a href="{$p24_recuring_url}?cardrm={$id}">{l s='remove' mod='przelewy24'}</a>
                                    </label>
                                </li>
                            {/foreach}

                            {if $pay_in_shop}
                                {$ignoreArr[]=140}
                                {$ignoreArr[]=142}
                                {$ignoreArr[]=145}
                                <li>
                                    <input type="radio" id="przelewy24ajaxcard" name="p24_cc" value="ajax_card"/>
                                    <label for="przelewy24ajaxcard"
                                           style="font-weight:normal;position:relative; top:-3px;">
                                        {l s='Choose credit/debit card as payment method' mod='przelewy24'}
                                    </label>
                                    <div id="P24FormArea" style="display: none"></div>
                                </li>
                            {/if}

                            <li>
                                <input type="radio" id="przelewy24standard" name="p24_cc" value="0"/>
                                <label for="przelewy24standard" style="font-weight:normal;position:relative; top:-3px;">
                                    {l s='Choose another payment method.' mod='przelewy24'}
                                </label>
                            </li>
                        </ul>
                        <div style="margin: 0 3em; {if $hidePayments|default:true}display: none;{/if}"
                             class="payMethodList">
                            <ul>
                                {if count($p24_paymethod_first) > 0}
                                        {foreach $p24_paymethod_first as $bank_id}
                                            {include file="./_bank_item.tpl" bank_id=$bank_id bank_name=$p24_paymethod_all.$bank_id notIn=$ignoreArr}
                                        {/foreach}
                                {/if}
                                {if sizeof($p24_paymethod_second) > 0}
                                    <div class="morePayMethods" style="display: none">
                                        {foreach $p24_paymethod_second as $bank_id}
                                            {include file="./_bank_item.tpl" bank_id=$bank_id bank_name=$p24_paymethod_all.$bank_id notIn=$ignoreArr}
                                        {/foreach}
                                        {foreach $p24_paymethod_all as $bank_id => $bank_name}
                                            {if !in_array($bank_id, $p24_paymethod_first) && !in_array($bank_id, $p24_paymethod_second)}
                                                {include file="./_bank_item.tpl" bank_id=$bank_id bank_name=$bank_name notIn=$ignoreArr}
                                            {/if}
                                        {/foreach}
                                    </div>
                                    <div class="moreStuff"
                                         onclick="$(this).fadeOut(100);$('.morePayMethods').slideDown()"
                                         title="Pokaż więcej metod płatności"></div>
                                {/if}
                            </ul>
                        </div>
                    {/if} {* /if tekstowo/graficznie *}
                {/if} {* /if pokazuj listę banków w sklepie *}
            {/if} {* /if empty cart *}
        {/if}
    </div>

    {if empty($p24FreeOrder) or $p24FreeOrder!=true}
        <p class="cart_navigation cart_navigation_next"
           style="display: block !important; overflow: hidden; margin: 15px 0;">
            {if isset($productsNumber) && $productsNumber <= 0}
                <a href="{$base_dir_ssl}index.php" class="button-exclusive btn btn-default">
                    <i class="icon-chevron-left"></i>{l s='Return to shop' mod='przelewy24'}
                </a>
            {else}
                {if !$get_order_id}
                    <a href="{$back_url}?step=3"
                       class="{if $smarty.const._PS_VERSION_ >= 1.6 }button-exclusive btn btn-default{else}button_large{/if}">
                        <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='przelewy24'}
                    </a>
                {/if}
                <a class="{if $smarty.const._PS_VERSION_ >= 1.6 }button btn btn-default button-medium{else}exclusive_large{/if}"
                   href="javascript:{if $p24_validationRequired == 0}if(!($('#przelewy24FormRecuring input[name=p24_cc]').val() > 0)) $('#przelewy24Form')[0].submit(); else {/if}proceedPayment()">

				<span id="proceedPaymentLink">
					{l s='Confirm' mod='przelewy24'}&nbsp;<img src="{$modules_dir}przelewy24/img/ajax.gif"
                                                               style="display: none; width: 20px; height: 20px;"/>
				</span>
                </a>
            {/if}
        </p>
        <p style="text-align: right">
            {l s='Confirm order required to pay' mod='przelewy24'}
        </p>
    {else}
        <p class="cart_navigation cart_navigation_next"
           style="display: block !important; overflow: hidden; margin: 15px 0;">
            <a class="{if $smarty.const._PS_VERSION_ >= 1.6 }button btn btn-default button-medium{else}exclusive_large{/if}"
               href="{$url_history}">
               <span>
                    {l s='Order history' mod='przelewy24'}
               </span>
            </a>
        </p>
    {/if}
</div>

{if $ga_key}{include file="./_ga.tpl" ga_key=$ga_key}{/if}