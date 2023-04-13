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

<section class="footer-block col-xs-12 col-sm-4">
	<div class="h444"><a title="" rel="nofollow">{l s="Strefa klienta"}</a></div>
	<div class="block_content toggle-footer">
		<ul class="bullet">
			<li><a href="{$link->getPageLink('contact')|escape:'html':'UTF-8'}">{l s="Formularz kontaktowy"}</a></li>
			<li><a href="{$link->getCMSLink(8)}" title="" rel="nofollow">{l s="Sposoby płatności"}</a></li>
			<li><a href="{$link->getCMSLink(9)}" title="" rel="nofollow">{l s="Dostawa"}</a></li>
			<li><a href="{$link->getCMSLink(10)}" title="" rel="nofollow">{l s="Шоу-румы"}</a></li>
			<li><a href="{$link->getCMSLink(11)}" title="" rel="nofollow">{l s="Formularz zwrotu"}</a></li>
			<li><a href="{$link->getCMSLink(12)}" title="" rel="nofollow">{l s="Reklamacje"}</a></li>
		</ul>
	</div>
</section>
