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
{assign var="product_link" value=$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}
{if $awp_url_rewrite}
	{assign var="awp_product_link" value="?"}
{else}
	{assign var="awp_product_link" value="&"}
{/if}
{assign var="awp_product_link" value=$awp_product_link|cat:'ipa='|cat:$productAttributeId|cat:'&ins='|cat:$product.instructions_valid}
{if $product_link|strpos:'#' > 0}
	{assign var='amp_pos' value=$product_link|strpos:'#'}
	{assign var='product_link' value=$product_link|substr:0:$amp_pos}
{/if}
{assign var='product_link' value=$product_link|cat:$awp_product_link}

<div class="row {if isset($productLast) and $productLast && (not isset($ignoreProductLast) or !$ignoreProductLast)}last_item{elseif isset($productFirst) and $productFirst}first_item{/if} {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}alternate_item{/if} cart_item address_{$product.id_address_delivery|intval}"
     id="product_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.instructions_valid|intval}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
    <div class="col-md-1 col-xs-3 text-center nopadding-xs">
        <a href="{$product_link|escape:'htmlall':'UTF-8'}">
            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'htmlall':'UTF-8'}"
                 alt="{$product.name|escape:'htmlall':'UTF-8'}" class="img-thumbnail" />
        </a>
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

		{if isset($product.instructions) && $product.instructions}
			<a href="{$product_link|escape:'htmlall':'UTF-8'}">{$product.instructions|escape:'quotes':'UTF-8'}</a>
		{/if}

        {if $onepagecheckoutps->isProductFreeShipping($product.id_product)}
            <b>{l s='Product with free shipping.' mod='onepagecheckoutps'}</b>
        {/if}
    </div>

    <div class="visible-xs visible-sm row clear"></div>

    {if $CONFIGS.OPC_SHOW_UNIT_PRICE}
        <div class="col-xs-3 text-right visible-xs visible-sm">
            <label><b>{l s='Unit price' mod='onepagecheckoutps'}:</b></label>
        </div>
        <div class="col-md-2 col-xs-9 text-right text-left-xs text-left-sm">
            <span class="{*price*}" id="product_price_{$product.id_product|intval}_{$product.id_product_attribute|intval}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.instructions_valid|intval}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
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
                        <span class="old-price">
                            {if $product.price_without_specific_price neq 0}
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
                        </span>
                    {/if}
                {/if}
            </span>
        </div>
    {/if}

    <div class="visible-xs visible-sm row clear"></div>

    <div class="col-xs-3 text-right visible-xs visible-sm">
        <label><b>{l s='Quantity' mod='onepagecheckoutps'}:</b></label>
    </div>
    <div class="col-md-3 col-xs-9">
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
            <div class="row middle-xs start-xs center-md">
                <div class="col-xs-12 cart_quantity nopadding-xs">
                    {if not isset($customizedDatas.$productId.$productAttributeId)}
                        {assign var='id_customization' value='0'}
                        {assign var='product_quantity' value=$product.cart_quantity-$quantityDisplayed|intval}
                        {include file="./review_product_line_update_quantity_awp.tpl"}
                    {/if}
                </div>
            </div>
        {/if}
    </div>

    <div class="visible-xs visible-sm row clear"></div>

    <div class="col-xs-3 text-right visible-xs visible-sm">
        <label><b>{l s='Total' mod='onepagecheckoutps'}:</b></label>
    </div>
    <div class="col-md-2 col-xs-9 text-right text-left-xs text-left-sm">
        <span class="price" id="total_product_price_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$product.instructions_valid|intval}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
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

    {if isset($customizedDatas[$productId][$productAttributeId])}
        {assign var='custom_data' value=$customizedDatas[$productId][$productAttributeId][$product.id_address_delivery]}
        {foreach from=$custom_data item='custom_item' key='id_customization' name='f_custom_data'}
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
                                        {if $textField.name}
                                            {$textField.name|escape:'htmlall':'UTF-8'}
                                        {else}
                                            {l s='Text #' mod='onepagecheckoutps'}{$textField@index+1|escape:'htmlall':'UTF-8'}
                                        {/if}
                                        : {$textField.value|escape:'htmlall':'UTF-8':false:true}
                                    </li>
                                {/foreach}
                            </ul>
                        {/if}
                    {/foreach}
                </div>
                <div class="col-md-3 col-xs-12">
                    <div class="row middle-xs start-xs center-md">
                        <div class="col-xs-12 cart_quantity nopadding-xs">
                            {assign var='id_customization' value=$id_customization}
                            {assign var='product_quantity' value=$custom_item.quantity|intval}
                            {include file="./review_product_line_update_quantity_awp.tpl"}
                        </div>
                    </div>
                </div>
{*                    {if $smarty.foreach.f_custom_data.total gt 1 and $smarty.foreach.f_custom_data.last}*}
                {if $smarty.foreach.f_custom_data.last}
                    <div class="col-xs-12 col-md-10 last_custom hidden-xs"></div>
                {/if}
            </div>
        {/foreach}
    {/if}
</div>