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

{if isset($cms) && !isset($cms_category)}
{if !$cms->active}

<br />

<div id="admin-action-cms">

<p>
<span>{l s='This CMS page is not visible to your customers.'}</span>
<input type="hidden" id="admin-action-cms-id" value="{$cms->id}" />
<input type="submit" value="{l s='Publish'}" name="publish_button" class="button btn btn-default"/>
<input type="submit" value="{l s='Back'}" name="lnk_view" class="button btn btn-default"/>
</p>

<div class="clear" ></div>

<p id="admin-action-result"></p>
</p>

</div>
{/if}
{if $cms->id_cms_category != 2 }
<div class="rte{if $content_only} content_only{/if}">
{$cms->content}
</div>
{/if}
{if $cms->id_cms_category == 2 }
<div class="store-detail">
<h1>{$meta_title}</h1>

      <div class="stores__slide stores__slide-full owl-carousel">
      {$cms->content|replace:'<p>':''|replace:'</p>':''}

      </div>
      <div class="stores__list"> 
        <div class="stores__list-item"> 
          <div class="stores__slide" id="map">

          </div>
          <div class="stores__info">
            <p class="stores__info-name"> <img src="{$img_dir}store-metro-1.svg" alt="">{$cms->name}</p>
            <p class="stores__info-adress">{$cms->adress}</p>
            <p class="stores__info-telephone">Телефон:<a href="tel:{$cms->phone}">{$cms->phone}</a></p>
            <p class="stores__info-worktimme">
              Время работы:<span> {$cms->work_time}</p>
            <div class="stores__info-services">

            {foreach from=CMS::$options name=foo key=k item=option}
           
                {if isset($cms->option_{$smarty.foreach.foo.iteration}) && $cms->option_{$smarty.foreach.foo.iteration}==1}
                 <div class="tooltip">
                  <p class="tooltiptext"><span class="ul-arrow"></span>{$option}</p>
                    <img src="{$img_dir}{$smarty.foreach.foo.iteration}.svg" alt="">
                    </div>
                {/if}
                
            {/foreach}
            </div>
            {if isset($cms->route_link)}
            <div class="stores__info-buttons"> 
              <a href="{$cms->route_link}" class="route"><img src="{$img_dir}placeholder.svg" alt="">Построить маршрут</a>
            </div>
            {/if}
          </div>
        </div>
      </div>
    {if isset($cms->tour_link)}
        {if $cms->tour_link ==""}
        {else}
            <div class="stores__tour">
                <p class="h1">3D тур по магазину</p>
                <iframe src="{$cms->tour_link}" width="100%" height="500" frameborder="0" allowfullscreen="true" style="position:relative;"></iframe>
            </div>
        {/if}
    {/if}
</div>
{/if}
{elseif isset($cms_category)}

<div class="block-cms">

<h1><a href="{if $cms_category->id eq 1}{$base_dir}{else}{$link->getCMSCategoryLink($cms_category->id, $cms_category->link_rewrite)}{/if}">
{$cms_category->name|escape:'html':'UTF-8'}</a></h1>

{if isset($sub_category) && !empty($sub_category)}	

<p class="title_block">{l s='List of sub categories in %s:' sprintf=$cms_category->name}</p>

<ul class="bullet">
{foreach from=$sub_category item=subcategory}

<li><a href="{$link->getCMSCategoryLink($subcategory.id_cms_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|escape:'html':'UTF-8'}</a></li>

{/foreach}

</ul>
{/if}

{if isset($cms_pages) && !empty($cms_pages)}

<p class="title_block">{l s='List of pages in %s:' sprintf=$cms_category->name}</p>

<ul class="bullet">

{foreach from=$cms_pages item=cmspages}
<li><a href="{$link->getCMSLink($cmspages.id_cms, $cmspages.link_rewrite)|escape:'html':'UTF-8'}">{$cmspages.meta_title|escape:'html':'UTF-8'}</a></li>
{/foreach}

</ul>
{/if}

</div>
{else}

<div class="alert alert-danger">
{l s='This page does not exist.'}
</div>

{/if}

<br />

{strip}
{if isset($smarty.get.ad) && $smarty.get.ad}
{addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{if isset($smarty.get.adtoken) && $smarty.get.adtoken}
{addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{/strip}