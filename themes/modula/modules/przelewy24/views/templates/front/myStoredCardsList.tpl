{capture name=path}<a
    href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='przelewy24'}</a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    <span class="navigation_page">{l s='My stored cards' mod='przelewy24'}</span>{/capture}

<script type="text/javascript">

    var payInShopScriptRequested = false;

    function onResize() {
        if ($(window).width() <= 640) {
            $('.payMethodList').addClass('mobile');
        } else {
            $('.payMethodList').removeClass('mobile');
        }
    }

    $(window).resize(function () {
        onResize();
    });

    function payInShopSuccess(status) {
        console.log("Success");
        console.log(status);
        var expires = String($("#P24_expYear").val()) + String($("#P24_expMonth").val());
        $.ajax({
            url: '{$p24_ajax_url}',
            method: 'POST',
            type: 'POST',
            data: {
                action: 'cardRegisterQuery',
                ccToken: status.ccToken,
                ReceiptText: status.ReceiptText,
                expires: expires
            },
            error: function () {
                payInShopFailure();
            },
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status == true) {
                    window.location.reload(false);
                } else {
                    $('#P24FormArea').html("<span class='info'>{l s='Registering payment card failed. The card has been registered.' mod='przelewy24'}</span>");

                    setTimeout(function () {
                            $('#P24FormArea:not(:visible)').slideDown();
                            hidePayJsPopup();
                        }
                        , 2000);
                    P24_Transaction = undefined;
                }
            }
        });
    }

    function payInShopFailure(status) {
        $('#P24FormArea').html("<span class='info'>{l s='Registering payment card failed. Try again or check your card data.' mod='przelewy24'}</span>");

        setTimeout(function () {
                $('#P24FormArea:not(:visible)').slideDown();
                hidePayJsPopup();
            }
            , 2000);
        P24_Transaction = undefined;
    }

    function requestJsAjaxCard() {

        $.ajax({
            url: '{$p24_ajax_url}',
            method: 'POST',
            type: 'POST',
            data: {
                action: 'cardRegister',
                p24_session_id: '{$p24_session_id}',
            },
            error: function (xhr, textStatus, err) {
                payInShopFailure();
            },
            success: function (response) {
                var data = JSON.parse(response);
                console.log(data);
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
                    .attr('data-successCallback', 'payInShopSuccess')
                    .attr('data-failureCallback', 'payInShopFailure')
                    .attr('data-dictionary', dictionary)
                    .attr('data-client-id', '{$clientId}')
                    .attr('data-sign', data.p24sign)
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

    function hidePayJsPopup() {
        $('#P24FormAreaHolder').fadeOut();
        $('#proceedPaymentLink:not(:visible)').closest('a').fadeIn();
    }

    function showAddCardPopUp() {
        $('#P24FormAreaHolder').appendTo('body');
        $('#proceedPaymentLink').closest('a').fadeOut();

        $('#P24FormAreaHolder').fadeIn();
        if (typeof P24_Transaction != 'object') {
            requestJsAjaxCard();
        }
    }

</script>
<style>
    .ccbox {
        background: #fbfbfb;
        border: 1px solid #d6d4d4;
        padding: 14px 18px 13px;
        margin-bottom: 30px;
        line-height: 23px;
        width: 49%;
        display: inline-block;
    }

    .ccbox:nth-child(odd) {
        margin-left: 1%;
    }

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
        border-color: #af0d1d #e94a59 #89000d;
        border-color: #af0d1d #e94a59 #89000d #af0d1d;
        border: 1px solid;
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

    .bank-box.recurring .bank-logo > span {
        background-color: #fff;
        background-color: rgba(255, 255, 255, 0.5);
    }


</style>

<h1 class="page-heading">{l s='My stored cards' mod='przelewy24'}</h1>
<form method="post">
    <div class="checkbox">
        <label class="control-label">
            {l s='Do not memorize payment cards, which I pay' mod='przelewy24'}
            <input type="checkbox" name="ccards_forget" value="1"{if $ccards_forget == 1} checked="checked"{/if} />
        </label>
        <button type="submit" name="submit" class="btn btn-default button button-small" value="submit"
                style="position: absolute; margin: -5px 0 0 10px;"><span>{l s='Save' mod='przelewy24'}</span></button>
    </div>
</form>

{foreach $ccards as $ccard}
    {if $ccard@first}
        <p>{l s='Your credit cards are listed below.' mod='przelewy24'}</p>
    {/if}
    <div class="ccbox">
        <h1 class="page-heading">{$ccard.card_type}</h1>
        <p>{$ccard.mask}</p>
        <p>{$ccard.expires|substr:0:2}/{$ccard.expires|substr:2:2}</p>
        <a class="btn btn-default button button-small"
           href="{$link->getModuleLink('przelewy24', 'paymentRecurring')|escape:'html':'UTF-8'}?cardrm={$ccard.id}"
           onclick="return confirm('{l s='Are you sure?' js=1 mod='przelewy24'}');"
           title="{l s='Delete' mod='przelewy24'}">
            <span>{l s='Delete' mod='przelewy24'}<i class="icon-remove right"></i></span>
        </a>
    </div>
    {foreachelse}
    <h3>{l s='Credit cards not found' mod='przelewy24'}</h3>
{/foreach}
<ul class="footer_links clearfix" style="border-top:0px; ">
    <li>
        <a class="btn btn-default button button-small" id="addPaymentCard" href="#" onclick="showAddCardPopUp();">
            <span>
                <i class="icon-plus"></i> {l s='Add payment card' mod='przelewy24'}
            </span>
        </a>
    </li>
</ul>
<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-default button button-small"
           href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span><i
                        class="icon-chevron-left"></i> {l s='Back to your account' mod='przelewy24'}</span></a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{$base_dir}"><span><i
                        class="icon-chevron-left"></i> {l s='Home' mod='przelewy24'}</span></a>
    </li>
</ul>
<div id="P24FormAreaHolder" onclick="hidePayJsPopup();" style="display: none">
    <div onclick="arguments[0].stopPropagation();" id="P24FormArea" class="popup"></div>
</div>