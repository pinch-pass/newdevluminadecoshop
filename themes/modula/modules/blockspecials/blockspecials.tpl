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

<!-- MODULE Block specials -->

<div id="special_block_right" class="block specials_block">


<div class="titlebordrtext1">
<div class="titleborderh4">
{*<a href="{$link->getPageLink('prices-drop')|escape:'html':'UTF-8'}">*}
{l s='Specials' mod='blockspecials'}
{*</a>*}
</div>	
</div>
<div class="titleborderout1">
<div class="titleborder1"></div>	
</div>


<div class="block_content specials-block">

{if $special}

<ul>

<li class="clearfix">


<a class="products-block-image" href="{$special.link|escape:'html':'UTF-8'}">
<img class="replace-2x img-responsive" src="{$link->getImageLink($special.link_rewrite, $special.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$special.legend|escape:'html':'UTF-8'}"/>
</a>

 <div class="product-content">

<div>

<a class="product-name" href="{$special.link|escape:'html':'UTF-8'}">
{$special.name|escape:'html':'UTF-8'}
</a>
</div>

{hook h='displayProductListReviews' product=$special}

{if isset($special.description_short) && $special.description_short}

<p class="product-description">
{$special.description_short|strip_tags:'UTF-8'|truncate:50}
</p>

{/if}

<div class="price-box">

{if !$PS_CATALOG_MODE}

<span class="old-price">
{if !$priceDisplay}
{displayWtPrice p=$special.price_without_reduction}{else}{displayWtPrice p=$priceWithoutReduction_tax_excl}
{/if}
</span>

<span class="price special-price">
{if !$priceDisplay}
{displayWtPrice p=$special.price}{else}{displayWtPrice p=$special.price_tax_exc}
{/if}
</span>


{/if}

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

</div>

</li>

</ul>

<div>

<a class="btn btn-default button button-menu" href="{$link->getPageLink('prices-drop')|escape:'html':'UTF-8'}">
<span>{l s='All specials' mod='blockspecials'}</span>
</a>

</div>

{else}

<div>{l s='No specials at this time.' mod='blockspecials'}</div>
{/if}

</div>

	{* <div class="hidden-xs hidden-sm">
		<div class="titlebordrtext1 LD_catalog">
			<h4 class="titleborderh4">
				{l s='КАТАЛОГИ' mod='blockspecials'}
			</h4>	
		</div>
		<div class="titleborderout1 LD_catalog">
			<div class="titleborder1"></div>	
		</div>
		<div class="LD_catalog">	
			<a target="_blank" href="{$base_uri}LUMINA DECO 2018_2019 CRYSTAL.pdf"><img style="width:100%;margin-bottom: 5px;" src="{$tpl_uri}img/LUMINA DECO 2018_2019 CRYSTAL.jpg"></a>
			<a target="_blank" href="{$base_uri}LUMINA DECO 2018_2019 MODERN LOFT.pdf"><img style="width:100%;margin-bottom: 5px;" src="{$tpl_uri}img/LUMINA DECO 2018_2019 MODERN LOFT.jpg"></a>
			<a target="_blank" href="{$base_uri}LUMINA DECO 2018_2019 BESTSELLERS.pdf"><img style="width:100%" src="{$tpl_uri}img/LUMINA DECO 2018_2019 BESTSELLERS.jpg"></a>
		</div>
	</div> *}

</div>
<!-- /MODULE Block specials -->


