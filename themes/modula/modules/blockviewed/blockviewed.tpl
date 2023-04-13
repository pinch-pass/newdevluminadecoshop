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
<!-- Block Viewed products -->
<section id="blockviewed2" class="page-product-box blockproductscategory">
<div class="titlebordrtext1">
<div class="titleborderh5 titleborderh5-h4">
{l s='Viewed products' mod='blockviewed'}
</div>	
</div>
<div class="titleborderout1">
<div class="titleborder2"></div>	
</div> 

<div id="blockviewed_box" class="clearfix">

<ul class="autobx bxslider clearfix">
	
{foreach from=$productsViewedObj item='viewedProduct' name=myLoop}

<li class="product-box item">



<a href="{$viewedProduct->product_link|escape:'html':'UTF-8'}" class="lnk_img product-image">
<img src="{if isset($viewedProduct->id_image) && $viewedProduct->id_image}{$link->getImageLink($viewedProduct->link_rewrite, $viewedProduct->cover, 'home_default')}{else}{$img_prod_dir}{$lang_iso}-default-medium_default.jpg{/if}" alt="{$viewedProduct->legend|escape:'html':'UTF-8'}"/>
</a>
                

<div class="product-name h555">

<a href="{$viewedProduct->product_link|escape:'html':'UTF-8'}">{$viewedProduct->name|escape:'html':'UTF-8'}</a>

</div>

<div class="price_display">
    <span class="price">{convertPrice price=Product::getPriceStatic($viewedProduct->id)}</span>

    <div>
        {if StockAvailable::getQuantityAvailableByProduct($viewedProduct->id, $id_product_attribute) > 0}
            <i class="availability"></i>
            {l s='В наличии'}
        {else}
            <i class="availabilitynot"></i>
            {l s='Нет в наличии'}
        {/if}
    </div>
</div>

<br />

</li>

{/foreach}

</ul>

</div>
</section>