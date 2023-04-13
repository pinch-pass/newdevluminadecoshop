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

{if false && isset($orderProducts) && count($orderProducts)}

<section class="page-product-box blockcrossselling wow animated fadeInLeft" style="visibility: visible;">


<div class="titlebordrtext1">
<h4 class="titleborderh5">
{if $page_name == 'product'}{l s='Customers who bought this product also bought:' mod='crossselling'}{else}{l s='We recommend' mod='crossselling'}{/if}
</h4>	
</div>
<div class="titleborderout1">
<div class="titleborder2"></div>	
</div>

<div id="crossselling_list" class="clearfix">

<ul id="crossselling_list_car" class="clearfix">

{foreach from=$orderProducts item='orderProduct' name=orderProduct}

<li class="product-box item">

<a class="lnk_img product-image" href="{$orderProduct.link|escape:'html':'UTF-8'}">
<img src="{$orderProduct.image}" alt="{$orderProduct.name|htmlspecialchars}" />
</a>

<h5 class="product-name">

<a href="{$orderProduct.link|escape:'html':'UTF-8'}">
{$orderProduct.name|truncate:22:' ...'|escape:'html':'UTF-8'}
</a>

</h5>


{if $crossDisplayPrice AND $orderProduct.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}

<p class="price_display">
<span class="price">{convertPrice price=$orderProduct.displayed_price}</span>
</p>


{/if}

</li>

{/foreach}

</ul>

</div>

</section>
{/if}
