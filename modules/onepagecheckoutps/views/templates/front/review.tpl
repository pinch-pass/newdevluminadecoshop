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

<script type="text/javascript">
    var summary_opc = {ldelim}
        'products': {$products|json_encode},
        'gift_products': {$gift_products|json_encode},
        'discounts': {$discounts|json_encode},
        'total_discounts': {$total_discounts|floatval},
        'total_discounts_tax_exc': {$total_discounts_tax_exc|floatval},
        'total_wrapping': {$total_wrapping|floatval},
        'total_wrapping_tax_exc': {$total_wrapping_tax_exc|floatval},
        'total_shipping': {$total_shipping|floatval},
        'total_shipping_tax_exc': {$total_shipping_tax_exc|floatval},
        'total_products_wt': {$total_products_wt|floatval},
        'total_products': {$total_products|floatval},
        'total_price': {$total_price|floatval},
        'total_tax': {$total_tax|floatval},
        'total_price_without_tax': {$total_price_without_tax|floatval}
    {rdelim};
</script>

{if $CONFIGS.OPC_SHOW_REMAINING_FREE_SHIPPING}
    {if $total_shipping_tax_exc > 0 && !$IS_VIRTUAL_CART && $free_ship > 0}
        <div class="row" id="remaining_amount_free_shipping">
            {assign var="sfree_shipping" value={displayPrice price=$free_ship}}
            {assign var="percent_free_shipping" value=(100-(($free_ship * 100) / $free_ship_preferences))}

            <div class="col-xs-12 text-center" id="remaining_amount_free_shipping-text">
                {l s='You must add' mod='onepagecheckoutps'} <span>{$sfree_shipping|escape:'htmlall':'UTF-8'}</span> {l s='to the cart to have' mod='onepagecheckoutps'}  <span>{l s='free shipping' mod='onepagecheckoutps'}</span>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-2">
                    <b>{displayPrice price=0}</b>
                </div>
                <div class="progress col-xs-8 nopadding">
                    <div class="progress-bar progress-bar-{if $percent_free_shipping > 50}success{else}warning{/if}" role="progressbar" style="width: {$percent_free_shipping|floatval|string_format:"%.2f"|cat:'%'}">&nbsp;</div>
                </div>
                <div class="col-xs-2 text-right">
                    <b>{displayPrice price=$free_ship_preferences}</b>
                </div>
            </div>
        </div>
    {/if}
{/if}

{hook h="displayBeforeShoppingCartBlock"}

<div id="header-order-detail-content" class="row hidden-xs hidden-sm">
    <div class="col-md-{if $CONFIGS.OPC_SHOW_UNIT_PRICE}4{else}6{/if} col-md-offset-1">
        <h5>{l s='Description' mod='onepagecheckoutps'}</h5>
    </div>
    {if $CONFIGS.OPC_SHOW_UNIT_PRICE}
        <div class="col-md-2">
            <h5 class="text-right">{l s='Unit price' mod='onepagecheckoutps'}</h5>
        </div>
    {/if}
    <div class="col-md-3">
        <h5 class="text-center">{l s='Qty' mod='onepagecheckoutps'}</h5>
    </div>
    <div class="col-md-2">
        <h5 class="text-right">{l s='Total' mod='onepagecheckoutps'}</h5>
    </div>
</div>
<div id="order-detail-content">
    {foreach from=$products|@sortby:'name' item=product}
        {assign var='productId' value=$product.id_product}
        {assign var='productAttributeId' value=$product.id_product_attribute}
        {assign var='quantityDisplayed' value=0}
        {assign var='odd' value=$product@iteration%2}
        {assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId) or count($gift_products)}
        {* Display the product line *}
        {if isset($product.productmega)}
            {foreach from=$product.productmega item=mega name=productMegas}
                {include file="./review_product_line_megaproduct.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first mega=$mega}
            {/foreach}
        {else}
			{if isset($attributewizardpro)}
				{include file="./review_product_line_awp.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first}
			{else}
				{include file="./review_product_line.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first}
			{/if}
        {/if}
        {if isset($customizedDatas.$productId.$productAttributeId)}
            {assign var='custom_data' value=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery]}
            {foreach from=$custom_data item='custom_item' key='id_customization' name='f_custom_data'}
                <div id="product_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$id_customization|intval}_{$product.id_address_delivery|intval}" class="row cart_item product_customization_for_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$product.id_address_delivery|intval}{if $odd} odd{else} even{/if} customization alternate_item {if $product@last && $custom_item@last && !count($gift_products)}last_item{/if}">
                    <div class="custom-information">
                        <div class="col-md-6 col-xs-12 col-md-offset-1 col-xs-offset-0">
                            {foreach $custom_item.datas as $type => $custom_data}
                                {if $type == $CUSTOMIZE_FILE}
                                    <div class="customizationUploaded">
                                        <ul class="customizationUploaded">
                                            {foreach $custom_data as $picture}
                                                <li><img src="{$pic_dir|escape:'htmlall':'UTF-8'}{$picture.value|escape:'htmlall':'UTF-8'}_small" alt="" class="customizationUploaded" /></li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                {elseif $type == $CUSTOMIZE_TEXTFIELD}
                                    <ul class="typedText">
                                        {foreach $custom_data as $textField}
                                            <li>
                                                {if $textField.name eq '@PP@'}
                                                    {assign var='text_field_value' value=urldecode($textField.value)}
                                                    {assign var='text_field_value' value=$text_field_value|json_decode:true}

                                                    <a href="https://pitchprint.net/admin/orders/{$text_field_value.projectId|escape:'htmlall':'UTF-8'}" target="_blank">
                                                        <img src="https://s3.amazonaws.com/pitchprint.rsc/images/previews/{$text_field_value.projectId|escape:'htmlall':'UTF-8'}_{$text_field_value.numPages|intval}.jpg" width="150"/>
                                                    </a>
                                                {else}
                                                    {if $textField.name}
                                                        {$textField.name|escape:'htmlall':'UTF-8'}
                                                    {else}
                                                        {l s='Text #' mod='onepagecheckoutps'}{$textField@index+1|escape:'htmlall':'UTF-8'}
                                                    {/if}
                                                    : {$textField.value|escape:'htmlall':'UTF-8':false:true}
                                                {/if}
                                            </li>
                                        {/foreach}
                                    </ul>
                                {/if}
                            {/foreach}
                        </div>
                        <div class="col-md-3 col-xs-12">
                            <div class="row text-center">
                                <div class="cart_quantity nopadding-xs">
                                    {assign var='id_customization' value=$id_customization}
                                    {assign var='product_quantity' value=$custom_item.quantity|intval}
                                    {include file="./review_product_line_update_quantity.tpl"}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {* If it exists also some uncustomized products *}
                {assign var='quantityDisplayed' value=$quantityDisplayed+$custom_item.quantity}
            {/foreach}
            {if $product.quantity-$quantityDisplayed > 0}{include file="./review_product_line.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first}{/if}
        {/if}
    {/foreach}
    {assign var='last_was_odd' value=$product@iteration%2}
	{foreach from=$gift_products|@sortby:'name' item=product}
        {assign var='productId' value=$product.id_product}
        {assign var='productAttributeId' value=$product.id_product_attribute}
        {assign var='quantityDisplayed' value=0}
        {assign var='odd' value=($product@iteration+$last_was_odd)%2}
        {assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
        {assign var='cannotModify' value=1}
        {* Display the gift product line *}
        {include file="./review_product_line.tpl" productLast=$product@last productFirst=$product@first}
    {/foreach}

    <div class="nopadding order_total_items">
        {if $CONFIGS.OPC_SHOW_TOTAL_PRODUCT}
            {assign var='value_total_products' value=$total_products}
            {if $use_taxes and not $priceDisplay}
                {assign var='value_total_products' value=$total_products_wt}
            {/if}
            <div class="row middle item_total cart_total_price cart_total_product end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right row end-xs">
                        {l s='Total products' mod='onepagecheckoutps'}:
                        {* {if $use_taxes}
                            {if $priceDisplay}
                                {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>{/if}
                            {else}
                                {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax incl.)' mod='onepagecheckoutps'}</span>{/if}
                            {/if}
                        {/if} *}
                    </span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="total_product">
                        {displayPrice price=$value_total_products}
                    </span>
                </div>
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_SHIPPING && !$IS_VIRTUAL_CART}
            <div class="row middle item_total cart_total_delivery end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right row end-xs">
                        {l s='Total shipping' mod='onepagecheckoutps'}:
                        {* {if $total_shipping_tax_exc <= 0 && !$IS_VIRTUAL_CART} *}
{*                            {l s='Shipping' mod='onepagecheckoutps'}*}
                        {* {elseif $use_taxes and $priceDisplay}
                            {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>{/if}
                        {elseif $use_taxes and not $priceDisplay}
                            {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax incl.)' mod='onepagecheckoutps'}</span>{/if}
                        {/if} *}
                    </span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="total_shipping">
                        {if $total_shipping_tax_exc <= 0 && !$IS_VIRTUAL_CART}
                            {l s='Free Shipping!' mod='onepagecheckoutps'}
                        {elseif $use_taxes and not $priceDisplay}
                            {displayPrice price=$total_shipping}
                        {else}
                            {displayPrice price=$total_shipping_tax_exc}
                        {/if}
                    </span>
                </div>
            </div>
        {/if}
        {if sizeof($discounts)}
            {foreach $discounts as $discount}
                <div class="row middle item_total cart_discount end-xs" id="cart_discount_{$discount.id_discount|intval}">
                    <div class="col-xs-8 col-md-10 text-right">
                        {if strlen($discount.code)}
                            <i class="fa-pts fa-pts-trash-o cart_quantity_delete"
                               onclick="Review.processDiscount({ldelim}'id_discount' : {$discount.id_discount|intval}, 'action' : 'delete'{rdelim})"></i>
                        {/if}
                        <span class="bold cart_discount_name text-right">
                            {$discount.name|escape:'htmlall':'UTF-8'}:
                        </span>
                    </div>
                    <div class="col-xs-4 col-md-2 cart_discount_price text-right">
                        <span class="price-discount price">
                            {if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}
                        </span>
                    </div>
                </div>
            {/foreach}
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_DISCOUNT}
            <div class="row middle item_total cart_total_voucher {if $total_discounts eq 0}hidden{/if} end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right row end-xs">
                        {l s='Total vouchers' mod='onepagecheckoutps'}
                        {if $use_taxes}
                            {if $priceDisplay}
                                {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>{/if}
                            {else}
                                {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax incl.)' mod='onepagecheckoutps'}</span>{/if}
                            {/if}
                        {/if}
                        :
                    </span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price-discount price" id="total_discount">
                        {if $use_taxes && !$priceDisplay}
                            {assign var='total_discounts_negative' value=$total_discounts * -1}
                        {else}
                            {assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
                        {/if}
                        {displayPrice price=$total_discounts_negative}
                    </span>
                </div>
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_WRAPPING}
            <div class="row middle item_total cart_total_voucher {if $total_wrapping eq 0}hidden{/if} end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right row end-xs">
                        {l s='Total gift-wrapping' mod='onepagecheckoutps'}
                        {if $use_taxes and $priceDisplay}
                            {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>{/if}
                        {elseif $use_taxes and not $priceDisplay}
                            {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax incl.)' mod='onepagecheckoutps'}</span>{/if}
                        {/if}:
                    </span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price-discount price" id="total_discount">
                        {if $use_taxes}
                            {if $priceDisplay}
                                {displayPrice price=$total_wrapping_tax_exc}
                            {else}
                                {displayPrice price=$total_wrapping}
                            {/if}
                        {else}
                            {displayPrice price=$total_wrapping_tax_exc}
                        {/if}
                    </span>
                </div>
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_WITHOUT_TAX}
            {if $use_taxes}
                <div class="row middle item_total cart_total_price total_without_tax end-xs">
                    <div class="col-xs-8 col-md-10 text-right">
                        <span class="bold text-right row end-xs">
                            {l s='Total' mod='onepagecheckoutps'}
                            <span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>:
                        </span>
                    </div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <span class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</span>
                    </div>
                </div>
            {/if}
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_TAX}
            <div class="row middle item_total cart_total_tax end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total tax' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="total_tax">{displayPrice price=$total_tax}</span>
                </div>
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_PRICE}
            <div class="row middle item_total cart_total_price total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total amount of your purchase' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="bold price" id="total_price">
                        {if $use_taxes}{displayPrice price=$total_price}{else}{displayPrice price=$total_price_without_tax}{/if}
                    </span>
                </div>
            </div>
        {/if}
        {if isset($COD_FEE)}
            <div class="row middle item_total cod_fee cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='COD Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="price_cod_fee">{displayPrice price=$COD_FEE}</span>
                </div>
            </div>
            <div class="row middle item_total cod_fee cart_total_price total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total + COD Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    {math assign="total_price_cod" equation='a + b' a=$total_price b=$COD_FEE}
                    <span class="price" id="total_price">{displayPrice price=$total_price_cod}</span>
                </div>
            </div>
        {/if}
        {if isset($BNKPLUS_DISCOUNT)}
            <div class="row middle item_total bnkplus_discount cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Discount Bank Wire' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="price_bnkplus_discount">{displayPrice price=$BNKPLUS_DISCOUNT}</span>
                </div>
            </div>
            <div class="row middle item_total cart_total_price total_price bnkplus_discount end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total - Discount Bank Wire' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    {math assign="total_price_bnkplus" equation='a - b' a=$total_price b=$BNKPLUS_DISCOUNT}
                    <span class="price" id="total_price">{displayPrice price=$total_price_bnkplus}</span>
                </div>
            </div>
        {/if}
        {if isset($PAYPAL_FEE)}
            <div class="row middle item_total paypal_fee cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Paypal Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="price_paypal_fee">{displayPrice price=$PAYPAL_FEE}</span>
                </div>
            </div>
            <div class="row middle item_total cart_total_price total_price paypal_fee end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total + Paypal Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    {math assign="total_price_paypal" equation='a + b' a=$total_price b=$PAYPAL_FEE}
                    <span class="price" id="total_price">{displayPrice price=$total_price_paypal}</span>
                </div>
            </div>
        {/if}
        {if isset($SEQURA_FEE)}
            <div class="row middle item_total sequra_fee cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Administration fees payment in 7 days' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="price_sequra_fee">{displayPrice price=$SEQURA_FEE}</span>
                </div>
            </div>
            <div class="row middle item_total cart_total_price total_price sequra_fee end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total fees incl' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    {math assign="total_price_sequra" equation='a + b' a=$total_price b=$SEQURA_FEE}
                    <span class="price" id="total_price">{displayPrice price=$total_price_sequra}</span>
                </div>
            </div>
        {/if}
        <div class="row middle item_total extra_fee_tax cart_total_price end-xs hidden">
            <div class="col-xs-8 col-md-10 text-right">
                <span class="bold text-right" id="extra_fee_tax_label"></span>
            </div>
            <div class="col-xs-4 col-md-2 text-right">
                <span class="price" id="extra_fee_tax_price"></span>
            </div>
        </div>
        <div class="row middle item_total extra_fee cart_total_price end-xs hidden">
            <div class="col-xs-8 col-md-10 text-right">
                <span class="bold text-right" id="extra_fee_label"></span>
            </div>
            <div class="col-xs-4 col-md-2 text-right">
                <span class="price" id="extra_fee_price"></span>
            </div>
        </div>
        <div class="row middle item_total cart_total_price total_price extra_fee end-xs hidden">
            <div class="col-xs-8 col-md-10 text-right">
                <span class="bold text-right" id="extra_fee_total_price_label">{l s='Total amount of your purchase' mod='onepagecheckoutps'}:</span>
            </div>
            <div class="col-xs-4 col-md-2 text-right">
                <span class="price" id="extra_fee_total_price">{if $use_taxes}{displayPrice price=$total_price}{else}{displayPrice price=$total_price_without_tax}{/if}</span>
            </div>
        </div>
        {if $voucherAllowed && $CONFIGS.OPC_SHOW_VOUCHER_BOX}
            <div class="cart_total_price row" id="list-voucher-allowed">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-12 col-md-7">
                            <a class="collapse-button promo-code-button" data-toggle="collapse" href="#promo-code" aria-expanded="false" aria-controls="promo-code">
                                {l s='Do you have a promotional code?' mod='onepagecheckoutps'}
                            </a>
                            <div class="promo-code in" id="promo-code" aria-expanded="true" style="">
                                <input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='Promo code' mod='onepagecheckoutps'}"/>
                                <button type="button" id="submitAddDiscount" name="submitAddDiscount" class="btn btn-default btn-small">
                                    {l s='Add' mod='onepagecheckoutps'}
                                </button>
                            </div>
                        </div>
                        {if $displayVouchers}
                        <div class="col-xs-12 col-md-5">
                            <div id="display_cart_vouchers">
                                <p>
                                    {l s='Take advantage of our exclusive offers:' mod='onepagecheckoutps'}
                                </p>
                                <ul class="js-discount">
                                    {foreach $displayVouchers as $voucher}
                                        <li class="cart-summary-line">
                                            <i class="fa-pts fa-pts-caret-right"></i>
                                            <span class="voucher_name" data-code="{$voucher.code|escape:'htmlall':'UTF-8'}">{$voucher.code|escape:'htmlall':'UTF-8'}</span>
                                             - {$voucher.name|escape:'htmlall':'UTF-8'}
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>

            {*<div class="row cart_total_price" id="list-voucher-allowed">
                <div class="col-xs-12 col-md-8 nopadding">
                    <div class="row col-xs-8 col-md-8 pts-vcenter">
                        <div class="col-xs-6 col-sm-5 col-md-5">
                            <span class="bold">{l s='Voucher' mod='onepagecheckoutps'}</span>
                        </div><!--
                        --><div class="col-xs-6 col-sm-7 col-md-7 nopadding-xs">
                            <input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name|escape:'htmlall':'UTF-8'}{/if}" />
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <span type="button" id="submitAddDiscount" name="submitAddDiscount" class="btn btn-default btn-small">
                            {l s='Add' mod='onepagecheckoutps'}
                        </span>
                    </div>
                </div>
                {if $displayVouchers}
                    <div class="col-xs-12 col-md col-lg-4">
                        <div id="display_cart_vouchers">
                            <ul>
                                {foreach $displayVouchers as $voucher}
                                    <li>
                                        <span data-code="{$voucher.code|escape:'htmlall':'UTF-8'}" class="voucher_name">
                                            <i class="fa-pts fa-pts-caret-right"></i>
                                            {$voucher.code|escape:'htmlall':'UTF-8'} - {$voucher.name|escape:'htmlall':'UTF-8'}
                                        </span>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                {/if}
            </div>*}
        {/if}
    </div>
</div>