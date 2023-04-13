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

<div class="row {*$product|icp:id_cart_product*} {if isset($productLast) and $productLast && (not isset($ignoreProductLast) or !$ignoreProductLast)}last_item{elseif isset($productFirst) and $productFirst}first_item{/if} {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}alternate_item{/if} cart_item address_{$product.id_address_delivery|intval}"
     id="product_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
    <div class="col-md-1 col-xs-3 text-center nopadding-xs image_product">
        <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">
            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'htmlall':'UTF-8'}"
                 alt="{$product.name|escape:'htmlall':'UTF-8'}" class="img-thumbnail" />
            {if $CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT}<i class="fa-pts fa-pts-search fa-pts-1x"></i>{/if}
        </a>
        {if $CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT}
            <div class="image_zoom">
                <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'htmlall':'UTF-8'}"
                     alt="{$product.name|escape:'htmlall':'UTF-8'}"/>
            </div>
        {/if}
    </div>
    <div class="col-md-{if $CONFIGS.OPC_SHOW_UNIT_PRICE}4{else}6{/if} col-xs-9 cart_description">
        <p class="s_title_block">
            {if !$CONFIGS.OPC_REMOVE_LINK_PRODUCTS}
                <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">
            {/if}
                {$product.name|escape:'htmlall':'UTF-8'}
            {if !$CONFIGS.OPC_REMOVE_LINK_PRODUCTS}
                </a>
            {/if}
        </p>

        {if isset($product.attributes) && $product.attributes}
            <span class="product_attributes">
                {*$product.attributes|escape:'htmlall':'UTF-8'*}
				{assign var=array_product_attributes value=", "|explode:$product.attributes}

				{foreach from=$array_product_attributes item='attribute'}
					{$attribute|escape:'htmlall':'UTF-8'}<br/>
				{/foreach}
            </span>
        {/if}

        {if $product.reference and $CONFIGS.OPC_SHOW_REFERENCE}
            <span class="product_reference row">
                {l s='Ref.' mod='onepagecheckoutps'}&nbsp;{$product.reference|escape:'htmlall':'UTF-8'}
            </span>
        {/if}

        {if $product.weight neq 0 and $CONFIGS.OPC_SHOW_WEIGHT}
            <span class="product_weight row">
                {l s='Weight' mod='onepagecheckoutps'}&nbsp;:&nbsp;{$product.weight|string_format:"%.3f"|escape:'htmlall':'UTF-8'}{$PS_WEIGHT_UNIT}
            </span>
        {/if}

        {if isset($product.productmega)}
            {foreach from=$product.productmega item=mega name=productMegas}
                {if isset($mega.extraAttrLong) && $mega.extraAttrLong}
                    <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'htmlall':'UTF-8'}">
                    {if isset($mega.extraAttrLong)}{$mega.extraAttrLong|escape:'quotes':'UTF-8'}{/if}
                </a>
            {/if}
            <br/>
            <i>{$mega.measure|escape:'htmlall':'UTF-8'}</i>
            {if isset($mega.personalization) && $mega.personalization neq ''}
                <br/>
                <div class="mp-personalization">{$mega.personalization|escape:'quotes':'UTF-8'}</div>
            {/if}
            {/foreach}
        {/if}

        {if $onepagecheckoutps->isProductFreeShipping($product.id_product)}
            <b>{l s='Product with free shipping.' mod='onepagecheckoutps'}</b>
        {/if}

        {if $PS_STOCK_MANAGEMENT and $CONFIGS.OPC_SHOW_AVAILABILITY}
            <div class="cart_avail">
                <span class="label{if $product.quantity_available <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.quantity_available <= 0} label-warning{else} label-success{/if}">
                    {if $product.quantity_available <= 0}
                        {if isset($product.allow_oosp) && $product.allow_oosp}
                            {if isset($product.available_later) && $product.available_later}
                                {$product.available_later|escape:'htmlall':'UTF-8'}
                            {else}
                                {l s='In Stock' mod='onepagecheckoutps'}
                            {/if}
                        {else}
                            {l s='Out of stock' mod='onepagecheckoutps'}
                        {/if}
                    {else}
                        {if isset($product.available_now) && $product.available_now}
                            {$product.available_now|escape:'htmlall':'UTF-8'}
                        {else}
                            {l s='In Stock' mod='onepagecheckoutps'}
                        {/if}
                    {/if}
                </span>
                {if !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
            </div>
        {/if}
    </div>

    <div class="visible-xs visible-sm row clear"></div>

    {if $CONFIGS.OPC_SHOW_UNIT_PRICE}
        <div class="col-md-2 col-xs-4 text-right text-left-xs text-left-sm">
            <span class="{*price*}" id="product_price_{$product.id_product|intval}_{$product.id_product_attribute|intval}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
                {if !empty($product.gift)}
                    <span class="gift-icon">{l s='Gift!' mod='onepagecheckoutps'}</span>
                {else}
                    {if !$priceDisplay}
                        <span class="price{if isset($product.is_discounted) && $product.is_discounted} special-price{/if}">{convertPrice price=$product.price_wt}</span>
                    {else}
                        <span class="price{if isset($product.is_discounted) && $product.is_discounted} special-price{/if}">{convertPrice price=$product.price}</span>
                    {/if}
                    {if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies}
                        <br/>
                        {if $product.price_without_specific_price neq 0}
                            <span class="old-price">
                                {convertPrice price=$product.price_without_specific_price}
                                {if !$priceDisplay}
                                    {if isset($product.reduction_type) && $product.reduction_type == 'amount'}
                                        {assign var='priceReduction' value=($product.price_wt - $product.price_without_specific_price)}
                                        {assign var='symbol' value=$currency->sign}
                                    {else}
                                        {assign var='priceReduction' value=(($product.price_without_specific_price - $product.price_wt)/$product.price_without_specific_price) * 100 * -1}
                                        {assign var='symbol' value='%'}
                                    {/if}
                                {else}
                                    {if isset($product.reduction_type) && $product.reduction_type == 'amount'}
                                        {assign var='priceReduction' value=($product.price - $product.price_without_specific_price)}
                                        {assign var='symbol' value=$currency->sign}
                                    {else}
                                        {assign var='priceReduction' value=(($product.price_without_specific_price - $product.price)/$product.price_without_specific_price) * 100 * -1}
                                        {assign var='symbol' value='%'}
                                    {/if}
                                {/if}
                            </span>
                            {if $priceReduction < 0}
                                <span class="price-percent-reduction small">
                                    {if $symbol == '%'}
                                        ({$priceReduction|string_format:"%.2f"|regex_replace:"/[^\d]0+$/":""}{$symbol|escape:'htmlall':'UTF-8'})
                                    {else}
                                        ({convertPrice price=$priceReduction})
                                    {/if}
                                </span>
                            {/if}
                        {/if}
                    {/if}
                {/if}
            </span>
        </div>
    {/if}

    <div class="col-md-3 col-xs-5 quantity-content">
        {if isset($cannotModify) AND $cannotModify == 1}
            <span>
                {if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
                    {$customizedDatas.$productId.$productAttributeId|@count|intval}
                {else}
                    {math assign="cart_quantity_displayed" equation='a - b' a=$product.cart_quantity b=$quantityDisplayed}
                    {$cart_quantity_displayed|escape:'htmlall':'UTF-8'}
                {/if}
            </span>
        {else}
            {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}
                <span id="cart_quantity_custom_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$product.id_address_delivery|intval}" class="quantity_custom">{$product.customizationQuantityTotal|escape:'htmlall':'UTF-8':false:true}</span>
            {/if}
            {if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}
                <div class="row text-center">
                    <div class="cart_quantity nopadding-xs">
                        {assign var='id_customization' value='nocustom'}
                        {assign var='product_quantity' value=$product.cart_quantity-$quantityDisplayed|intval}
                        {include file="./review_product_line_update_quantity.tpl"}
                    </div>
                </div>
            {/if}
        {/if}
    </div>

    <div class="col-xs-3 text-right visible-xs visible-sm">
        <label><b>{l s='Total' mod='onepagecheckoutps'}:</b></label>
    </div>
    <div class="price show product-price text-right">
        <span class="price" id="total_product_price_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
            {if !empty($product.gift)}
                <span class="gift-icon">{l s='Gift!' mod='onepagecheckoutps'}</span>
            {else}
                {if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
                    {if !$priceDisplay}
                        {displayPrice price=$product.total_customization_wt}
                    {else}
                        {displayPrice price=$product.total_customization}
                    {/if}
                {else}
                    {if !$priceDisplay}
                        {displayPrice price=$product.total_wt}
                    {else}
                        {displayPrice price=$product.total}
                    {/if}
                {/if}
            {/if}
        </span>
    </div>
</div>