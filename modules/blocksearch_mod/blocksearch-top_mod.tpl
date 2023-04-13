{*
	* 2007-2012 PrestaShop
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
	*  @copyright  2007-2012 PrestaShop SA
	*  @version  Release: $Revision: 7331 $
	*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
	*  International Registered Trademark & Property of PrestaShop SA
	*}

{if isset($warehouse_vars.header_style) && ($warehouse_vars.header_style == 2 || $warehouse_vars.header_style == 3)}

{else}

		<!-- Block search module TOP -->
		<div class="search container">
		
			{*{$iqitsearch_text}*}
			
				<form  class="search__form" method="get" action="{$link->getPageLink('search', true, null, null, false, null, true)|escape:'html':'UTF-8'}" id="searchbox">
					<input type="hidden" name="controller" value="search" />
					<input type="hidden" name="orderby" value="position" />
					<input type="hidden" name="orderway" value="desc" />

					<input class="search__input search_query form-control" type="text" id="search_query_top" name="search_query" placeholder="{l s='Искать товары...' mod='blocksearch_mod'}" value="{$search_query|escape:'htmlall':'UTF-8'|stripslashes}" />
					
					<div class="search_query_container {if isset($blockCategTree)}search-w-selector{/if}">

					{if isset($blockCategTree)}

					<div class="search-cat-selector search__category">
						<select class="form-control search-cat-select" name="search_query_cat" style="width: 100%;">
							<option class="search__category-heading" value="0">{l s='Категории' mod='blocksearch_mod'}</option>
							{foreach from=$blockCategTree.children item=child name=blockCategTree}
									{include file="./category-tree-branch.tpl" node=$child main=true}
							{/foreach}
						</select>
					</div>
					{else}
					<input type="hidden" name="search-cat-select" value="0" class="search-cat-select" />
					{/if}
					</div>

					<button type="submit" name="submit_search" class="search__btn">
						<img class="search__btn-img" src="{$img_dir}search.svg" alt="icon search" width="20" height="20">
						<span class="search__btn-title">Найти</span>
					</button>

				</form>
		</div>

{/if}

	<!-- /Block search module TOP -->