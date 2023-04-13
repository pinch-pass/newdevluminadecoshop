{*
	* 2007-2014 PrestaShop
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
	* DISCLAIMER
	*
	* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
	* versions in the future. If you wish to customize PrestaShop for your
	* needs please refer to http://www.prestashop.com for more information.
	*
	*  @author    PrestaShop SA <contact@prestashop.com>
	*  @copyright 2007-2014 PrestaShop SA
	*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
	*  International Registered Trademark & Property of PrestaShop SA
	*}

	<ul class="cbp-products-big flexslider_carousel row ">
	{foreach from=$products item=product name=homeFeaturedProducts}
	<li class="ajax_block_product col-xs-{$perline}">
		<div class="product-container">
		<div class="product-image-container">
			<a class="product_img_link"	href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" >
				<img class="replace-2x img-responsive img_0" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} />
			</a>
			<div class="product-flags">
				{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
				{if isset($product.online_only) && $product.online_only}
				<span class="online-label {if isset($product.new) && $product.new == 1}online-label2{/if}">{l s='Online only' mod='iqitmegamenu'}</span>
				{/if}
				{/if}
				{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
				{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
				<span class="sale-label">{l s='Reduced price!' mod='iqitmegamenu'}</span>
				{/if}
				{if isset($product.new) && $product.new == 1}
				<span class="new-label">{l s='New' mod='iqitmegamenu'}</span>
				{/if}
				{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
				<span class="sale-label">{l s='Sale!' mod='iqitmegamenu'}</span>
				{/if}
			</div>
			<div class="functional-buttons functional-buttons-grid clearfix">
				{if isset($quick_view) && $quick_view}
				<div class="quickview col-xs-6">
					<a class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}" title="{l s='Quick view' mod='iqitmegamenu'}">
						{l s='Quick view' mod='iqitmegamenu'}
					</a>
				</div>
				{/if}
				{hook h='displayProductListFunctionalButtons' product=$product}
				{if isset($comparator_max_item) && $comparator_max_item}
				<div class="compare col-xs-3">
					<a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare' mod='iqitmegamenu'}">{l s='Add to Compare' mod='iqitmegamenu'}</a>
				</div>
				{/if}	
			</div>
		</div>
			{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
			<a class="cbp-product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" >
				{$product.name|truncate:60:'...'|escape:'html':'UTF-8'}
			</a>
		{if $product.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
                         <div class="content_price">
                            <span  class="price product-price">{hook h="displayProductPriceBlock" product=$product type="before_price"} {convertPrice price=$product.displayed_price}</span>
                            	{if isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                            	{hook h="displayProductPriceBlock" product=$product type="old_price"}
                            	<span class="old-price product-price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>
								 {/if}
								 {hook h="displayProductPriceBlock" product=$product type="price"}
								
                        </div>
                         {hook h="displayProductPriceBlock" product=$product type='after_price'}
                        {/if}	</div>
	</li>	
	
	{/foreach}
</ul>