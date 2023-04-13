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
	*  @author    PrestaShop SA <contact@prestashop.com>
	*  @copyright 2007-2014 PrestaShop SA
	*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
	*  International Registered Trademark & Property of PrestaShop SA
	*}


	{if $node.type==1}
	<div data-element-type="1" data-depth="{$node.depth}" data-element-id="{$node.elementId}" class="row menu_row menu-element {if $node.depth==0} first_rows{/if} menu-element-id-{$node.elementId}">
		{elseif $node.type==2}
		<div data-element-type="2" data-depth="{$node.depth}" data-width="{$node.width}" data-contenttype="{$node.contentType}" data-element-id="{$node.elementId}" class="col-xs-{$node.width} menu_column menu-element menu-element-id-{$node.elementId}">
			{/if}
		
			<div class="action-buttons-container">
				<button type="button" class="btn btn-primary add-row-action" >{l s='Add Row' mod='iqitmegamenu'}</button>
				<button type="button" class="btn btn-success add-column-action" >{l s='Add Column' mod='iqitmegamenu'}</button>
				<button type="button" class="btn btn-danger remove-element-action" ><i class="icon-trash"></i> </button>
			</div>
			<div class="dragger-handle btn btn-danger"><i class="icon-arrows "></i></a></div>

			{if $node.type==2}
				{include file="./column_content.tpl" node=$node}
			{/if}

			{if isset($node.children) && $node.children|@count > 0}
			{foreach from=$node.children item=child name=categoryTreeBranch}
			{include file="./submenu_content.tpl" node=$child }
			{/foreach}
			{/if}
		</div>
