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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if count($categoryProducts) > 0 && $categoryProducts !== false}

<section id="productscategory" class="page-product-box blockproductscategory">

<div class="titlebordrtext1">
<div class="titleborderh5 titleborderh5-h4">
{$categoryProducts|@count} {l s='other products in the same category:' mod='productscategory'}
</div>	
</div>
<div class="titleborderout1">
<div class="titleborder2"></div>	
</div> 

<div id="productscategory_list" class="clearfix">

<ul id="bxslider1" class="bxslider clearfix">

{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}

<li class="product-box item">

{*{if isset($categoryProduct.new) && $categoryProduct.new == 1}


<span class="new-box">
<span class="new-label">
<!-- {l s='New' mod='productscategory'} -->
</span>
</span>

{/if}

{if isset($categoryProduct.on_sale) && $categoryProduct.on_sale && isset($categoryProduct.show_price) && $categoryProduct.show_price && !$PS_CATALOG_MODE}
<span class="sale-box">
<span class="sale-label">
<!-- {l s='Sale !' mod='productscategory'} -->
</span>
</span>
{/if}*}


<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image"><img src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" /></a>
                

<div class="product-name h555">

<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)|escape:'html':'UTF-8'}">{$categoryProduct.name|escape:'html':'UTF-8'}</a>

</div>



{if $ProdDisplayPrice AND $categoryProduct.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}

<div class="price_display">

    {if isset($categoryProduct.specific_prices) && $categoryProduct.specific_prices}<span class="old-price">{displayWtPrice p=$categoryProduct.price_without_reduction}</span>{/if}

    <span class="price{if isset($categoryProduct.specific_prices) && $categoryProduct.specific_prices} special-price{/if}">{convertPrice price=$categoryProduct.displayed_price}</span>

    {if isset($categoryProduct.specific_prices.reduction) && $categoryProduct.specific_prices.reduction && $categoryProduct.specific_prices.reduction_type == 'percentage'}<span class="price-percent-reduction small">-{$categoryProduct.specific_prices.reduction * 100}%</span>{/if}

    <div>
        {if StockAvailable::getQuantityAvailableByProduct($viewedProduct->id, $id_product_attribute) > 0}
            <i class="availability"></i>
            {l s='В наличии'}
        {else}
            <i class="availabilitynot"></i>
            {l s='Нет в наличии'}
        {/if}
    </div>
</p>

{else}

<br />

{/if}


</li>

{/foreach}

</ul>

</div>
</section>
{/if}