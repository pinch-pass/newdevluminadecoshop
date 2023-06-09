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
{strip}
{addJsDef baseDir=$content_dir}
{addJsDef baseUri=$base_uri}
{addJsDef static_token=$static_token}
{addJsDef token=$token}
{addJsDef priceDisplayPrecision=$priceDisplayPrecision*$currency->decimals}
{addJsDef priceDisplayMethod=$priceDisplay}
{addJsDef roundMode=$roundMode}
{addJsDef isLogged=$is_logged|intval}
{addJsDef isGuest=$is_guest|intval}
{addJsDef page_name=$page_name|escape:'html':'UTF-8'}
{addJsDef contentOnly=$content_only|boolval}
{if isset($cookie->id_lang)}
	{addJsDef id_lang=$cookie->id_lang|intval}
{/if}
{addJsDefL name=FancyboxI18nClose}{l s='Close'}{/addJsDefL}
{addJsDefL name=FancyboxI18nNext}{l s='Next'}{/addJsDefL}
{addJsDefL name=FancyboxI18nPrev}{l s='Previous'}{/addJsDefL}
{addJsDef usingSecureMode=Tools::usingSecureMode()|boolval}
{addJsDef ajaxsearch=Configuration::get('PS_SEARCH_AJAX')|boolval}
{addJsDef instantsearch=Configuration::get('PS_INSTANT_SEARCH')|boolval}
{addJsDef quickView=$quick_view|boolval}
{addJsDef displayList=Configuration::get('PS_GRID_PRODUCT')|boolval}
{/strip}
<script>
$(document).ready(function() {
	if (!!$.prototype.bxSlider)
		$('.autobx').bxSlider({
			minSlides: 2,
			maxSlides: 6,
			slideWidth: 270,
			slideMargin: 17,
			pager: false,
			nextText: '',
			prevText: '',
			moveSlides:1,
			infiniteLoop:false,
			hideControlOnEnd: true
		});
});
</script>
<script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@12.0.0/dist/lazyload.min.js" integrity="sha256-Hju/Km2VA4ESE7rKn14wlhjKloE2GZylMqClFnwLDxw=" crossorigin="anonymous"></script>
<script>
	var lazyLoadInstance = new LazyLoad( { elements_selector: ".lazy", load_delay: 300 } );
	lazyLoadInstance.loadAll();
</script>