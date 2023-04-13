{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $page_name =='index'}
<!-- Module HomeSlider -->
    {if isset($homeslider_slides)}
 <div class="homepage-slider">
        <div class="container">
          <div class="homepage-slider__list owl-carousel">
		  {foreach from=$homeslider_slides item=slide}
              {if $slide.active}
                  <li class="homepage-slider__item">
                      <picture>
                          {*<source media="(min-width:678px) and (max-width:1100px)" srcset="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`homeslider/images/t-`$slide.image|escape:'htmlall':'UTF-8'`")}">*}
                          <source media="(min-width:992px) and (max-width:1199px)" srcset="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`homeslider/images/t-Banner{$slide.id_slide}.jpg")}">
                          <source media="(min-width:768px) and (max-width:991px)" srcset="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`homeslider/images/mt-Banner{$slide.id_slide}.jpg")}">
                          <source media="(min-width:0px) and (max-width:767px)" srcset="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`homeslider/images/m-Banner{$slide.id_slide}.jpg")}">
                          <img src="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`homeslider/images/`$slide.image|escape:'htmlall':'UTF-8'`")}"{if isset($slide.size) && $slide.size} {$slide.size}{else} width="100%" height="100%"{/if} alt="{$slide.legend|escape:'htmlall':'UTF-8'}" />
                      </picture>
                      <a href="{$slide.url|escape:'html':'UTF-8'}">
                      </a>
                  </li>
              {/if}
			{/foreach}
          </div>
        </div>
      </div>
      
	  {/if}
{/if}
 