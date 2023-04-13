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
	<!-- Block search module TOP -->
	<div id="search_block_top_contentm" class="col-xs-12 {if isset($iqitsearch_shower) && $iqitsearch_shower}iqit-search-cm{else}iqit-search-ncm{/if}">
	{if isset($iqitsearch_shower) && $iqitsearch_shower} <div class="iqit-search-shower">

	<div class="iqit-search-shower-i"><i class="icon icon-search"></i></div>{/if}
	<div id="search_block_top" class="search_block_top {if isset($iqitsearch_shower) && $iqitsearch_shower}iqit-search-c{else}iqit-search{/if}">
		
		<form method="get" action="{$link->getPageLink('search', true, null, null, false, null, true)|escape:'html':'UTF-8'}" id="searchbox">

			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="orderby" value="position" />
			<input type="hidden" name="orderway" value="desc" />
			<div class="search_query_container {if isset($blockCategTree)}search-w-selector{/if}">
			{if isset($blockCategTree)}
			<div class="search-cat-selector"><select class="form-control search-cat-select" name="search_query_cat">
			<option value="0">{l s='All categories' mod='blocksearch_mod'}</option>
			{foreach from=$blockCategTree.children item=child name=blockCategTree}
					{include file="./category-tree-branch.tpl" node=$child main=true}
			{/foreach}
			</select></div>
			{else}
			<input type="hidden" name="search-cat-select" value="0" class="search-cat-select" />
			{/if}


			<input class="search_query form-control" type="text" id="search_query_top" name="search_query" placeholder="{l s='Search' mod='blocksearch_mod'}" value="{$search_query|escape:'htmlall':'UTF-8'|stripslashes}" />
			</div>
			<button type="submit" name="submit_search" class="button-search">
				<span>{l s='Search' mod='blocksearch_mod'}</span>
			</button>
		</form>
	{if isset($iqitsearch_shower) && $iqitsearch_shower}</div>{/if}</div></div>


	<!-- /Block search module TOP -->
