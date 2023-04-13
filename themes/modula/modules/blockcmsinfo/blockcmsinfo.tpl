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
{if $infos|@count > 0}
	<!-- MODULE Block cmsinfo -->
	<div id="cmsinfo_block" class="wow animated fadeInUp" style="visibility: visible;">
		{foreach from=$infos item=info}
			<div class="rte col-xs-12">{$info.text}</div>
		{/foreach}
	</div>
	<script>
		$('.main-cat .owl-carousel').owlCarousel({
			loop: true,
			margin: 28,
			nav: true,
			navText: '',
			autoplay: true,
			dotsEach: 2,
			autoplayTimeout: 2500,
			autoplayHoverPause: true,
			responsive: {
				0: {
					items: 1,
					stagePadding: 50,
					margin: 20,
					nav: false
				},
				600: {
					items: 2
				},
				1050: {
					items: 3
				},
				1650: {
					items: 4
				},
			}
		});
	</script>
	<!-- /MODULE Block cmsinfo -->
{/if}