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

	<ul class="cbp-products-list {if $perline==12}cbp-products-list-one{/if} row ">
	{foreach from=$products item=product name=homeFeaturedProducts}
	<li class="ajax_block_product col-xs-{$perline} ">
		<div class="product-container clearfix">
		<div class="product-image-container">
			<a class="product_img_link"	href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" >
				<img class="replace-2x img-responsive img_0" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}"  />
			</a>
		</div>
		<div class="cbp-product-info">
			{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
			<a class="cbp-product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" >
				{$product.name|truncate:35:'...'|escape:'html':'UTF-8'}
			</a>
		{if $product.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
                         <div class="content_price">
                            <span  class="price product-price">{convertPrice price=$product.displayed_price}</span>
                            	{if isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                            	<span class="old-price product-price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>
								 {/if}
                        </div>
                        {/if}	</div></div>
	</li>	
	
	{/foreach}
</ul>