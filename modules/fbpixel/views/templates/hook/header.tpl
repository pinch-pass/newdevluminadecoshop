{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2018 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}
{if Tools::getValue('ajax','false') == 'false' && (Tools::getValue('submitMessage','false') == 'false' && Tools::getValue('msgText','false') == 'false') && (Tools::getValue('add_comment','false')=='false' && Tools::getValue('action') != 'add_comment') && Tools::getValue('criterion','false') == 'false' && (Tools::getValue('controller') != 'productscomparison')}
{literal}
    <script>
        var prefix = '{/literal}{$prefix|escape:quotes}{literal}';
        var sufix = '{/literal}{$sufix|escape:quotes}{literal}';
        function getURLParameter(url, name) {
            return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
        }

        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');

        fbq('init', '{/literal}{Configuration::get('FBPIXEL_ID')}{literal}');
        {/literal}

        {if Configuration::get('FBPIXEL_PAGEVIEW')==1}
        fbq('track', 'PageView');
        {/if}

        {if Configuration::get('FBPIXEL_LEAD_N') == 1 && $track_newsletter == true}
        fbq('track', 'Lead', {
            content_name: 'newsletter subscription'
        });
        {/if}

        {if Configuration::get('FBPIXEL_LEAD')==1}
        {if Tools::getValue('controller')=='product'}
        {if Tools::getValue('id_product','false')!='false'}
        fbq('track', 'Lead');
        {/if}
        {/if}
        {/if}

        {if Configuration::get('FBPIXEL_REG')==1}
        {if isset($account_created)}
        fbq('track', "CompleteRegistration");
        {/if}
        {/if}


        {if Configuration::get('FBPIXEL_DPA')==1}
        {if Configuration::get('FBPIXEL_VCONTENT') == 1}
        {if Tools::getValue('id_product','false') != 'false' && $fbpixel_product != NULL}
        {assign var=product_price value=$fbpixel_product->getPrice(true, $smarty.const.NULL)}
        $(document).ready(function(){
            var ids=prefix+{if Configuration::get('FBPIXEL_ATTRID') == 1}$('#idCombination').val(){elseif Configuration::get('FBPIXEL_ATTRID') == 2}{Tools::getValue('id_product')}+'{Configuration::get('FBPIXEL_SEPSIGN','-')}'+$('#idCombination').val(){else}{Tools::getValue('id_product')}{/if}+sufix;
            fbq('track', 'ViewContent', {
                content_name: '{$fbpixel_product->name|escape:javascript}',
                content_ids: [ids],
                content_type: 'product',
                value: {$product_price|string_format:"%.2f"},
                currency: '{$fbpixel_currency}'
            });
        });
        {/if}
        {/if}
        {else}
        {if Configuration::get('FBPIXEL_VCONTENT') == 1}
        {if Tools::getValue('id_product','false') != 'false' && $fbpixel_product != NULL}
        {assign var=product_price value=$fbpixel_product->getPrice(true, $smarty.const.NULL)}
        $(document).ready(function(){
            var ids=prefix+{if Configuration::get('FBPIXEL_ATTRID') == 1}$('#idCombination').val(){elseif Configuration::get('FBPIXEL_ATTRID') == 2}{Tools::getValue('id_product')}+'{Configuration::get('FBPIXEL_SEPSIGN','-')}'+$('#idCombination').val(){else}{Tools::getValue('id_product')}{/if}+sufix;
            fbq('track', 'ViewContent', {
                content_name: '{$fbpixel_product->name|escape:javascript}',
                value: {$product_price|string_format:"%.2f"},
                currency: '{$fbpixel_currency}'
            });
        });
        {/if}
        {/if}
        {/if}

        {if Configuration::get('FBPIXEL_INITIATE')==1}
        {if Tools::getValue('controller')=='order' || Tools::getValue('controller')=='orderopc'}
        fbq('track', 'InitiateCheckout');
        {/if}
        {/if}
        {if Configuration::get('FBPIXEL_SEARCH')==1}
        {if Tools::getValue('controller')=='search'}
        fbq('track', 'Search');
        {/if}
        {/if}

        {if Configuration::get('FBPIXEL_DPA')==1}
            {if Configuration::get('FBPIXEL_ADDTOCART')==1}
                $(document).ready(function(){
                    $('{Configuration::get('FBPIXEL_ATC_L')}').click(function(){
                        {if Configuration::get('FBPIXEL_ATTRID')==1}
                        var idp = prefix+getURLParameter($(this).attr('href'),'ipa')+sufix;
                        {elseif Configuration::get('FBPIXEL_ATTRID')==2}
                        var idp = prefix+getURLParameter($(this).attr('href'),'id_product')+'{Configuration::get('FBPIXEL_SEPSIGN','-')}'+getURLParameter($(this).attr('href'),'ipa')+sufix;
                        {else}
                        var idp = prefix+getURLParameter($(this).attr('href'),'id_product')+sufix;
                        {/if}
                        var productPrice = $(this).parents('{Configuration::get('FBPIXEL_ATC_PC')}').find('{Configuration::get('FBPIXEL_ATC_PP')}').html();
                        if (productPrice == undefined) {
                            var productPrice = $(this).parents('li').find('.price').html();
                        }
                        if (productPrice == undefined) {
                            var productPrice = 0.000;
                        }
                        productPrice = productPrice.replace(/[^\d.\,-]/g, '');
                        productPrice = productPrice.replace(',','.');
                        if (productPrice[productPrice.length-1] === ".")
                        {
                            productPrice = productPrice.slice(0, -1);
                        }
                        {literal}fbq('track', 'AddToCart', {content_ids: idp, content_type: 'product', value: productPrice, currency: '{/literal}{$fbpixel_currency}'}{literal});{/literal}
                    });
                });
                {if $page_name=='product'}
                    $(document).ready(function(){
                        $('{Configuration::get('FBPIXEL_ATC_P')}').click(function(){
                            var productPagePrice = $(this).parents('body').find('{Configuration::get('FBPIXEL_ATC_PPP')}').html();
                            if (productPagePrice == undefined) {
                                var productPagePrice = 0.000;
                            }
                            productPagePrice = productPagePrice.replace(/[^\d.\,-]/g, '');
                            productPagePrice = productPagePrice.replace(',','.');
                            if (productPagePrice[productPagePrice.length-1] === ".")
                            {
                                productPagePrice = productPagePrice.slice(0, -1);
                            }
                            {literal}fbq('track', 'AddToCart', {content_ids: prefix+{/literal}{if Configuration::get('FBPIXEL_ATTRID') == 1}$('#idCombination').val(){elseif Configuration::get('FBPIXEL_ATTRID') == 2}{Tools::getValue('id_product')}+'{Configuration::get('FBPIXEL_SEPSIGN','-')}'+$('#idCombination').val(){else}{Tools::getValue('id_product')}{/if}{literal}+sufix, content_type: 'product', value: productPagePrice, currency: '{/literal}{$fbpixel_currency}'}{literal});{/literal}
                        });
                    });
                {/if}
            {/if}
        {else}
        {if Configuration::get('FBPIXEL_ADDTOCART')==1}
        $(document).ready(function(){
            $('{Configuration::get('FBPIXEL_ATC_P')}, {Configuration::get('FBPIXEL_ATC_L')}').click(function(){
                fbq('track', 'AddToCart');
            });
        });
        {/if}
        {/if}

        {if Configuration::get('FBPIXEL_WISHLIST')==1}
        $(document).ready(function(){
            $('#wishlist_button_nopop').click(function(){
                fbq('track', 'AddToWishlist');
            });
        });
        {/if}
        {literal}
    </script>
{/literal}
{/if}