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
	<div class="row menu_row menu-element {if $node.depth==0} first_rows{/if} menu-element-id-{$node.elementId}">
		{elseif $node.type==2}
		<div  class="col-xs-{$node.width} cbp-menu-column cbp-menu-element menu-element-id-{$node.elementId} {if $node.contentType==0}cbp-empty-column{/if}{if $node.contentType == 6 && isset($node.content.absolute)} cbp-absolute-column{/if}" >
			<div class="cbp-menu-column-inner">
			{/if}
			{if $node.type==2}

				{if isset($node.content_s.title)}
					{if isset($node.content_s.href)}
					<a href="{$node.content_s.href}" class="cbp-column-title{if  isset($node.content.view) && $node.content.view==2 && $node.contentType==3} cbp-column-title-inline{/if}">{$node.content_s.title} {if isset($node.content_s.legend)}<span class="label cbp-legend cbp-legend-inner">{$node.content_s.legend}<span class="cbp-legend-arrow"></span></span>{/if}</a>
					{else}
					<span class="cbp-column-title{if isset($node.content.view) && $node.content.view==2 && $node.contentType==3} cbp-column-title-inline{/if} transition-300">{$node.content_s.title} {if isset($node.content_s.legend)}<span class="label cbp-legend cbp-legend-inner">{$node.content_s.legend}<span class="cbp-legend-arrow"></span></span>{/if}</span>

					{/if}
				{/if}

				


				

				{if $node.contentType==1}
				
					{if isset($node.content.ids) && $node.content.ids}
						{$node.content.ids}
					{/if}

				{elseif $node.contentType==2}
				
					{if isset($node.content.ids)}

						{if $node.content.treep}
							<div class="row cbp-categories-row">
								{foreach from=$node.content.ids item=category}
								{if isset($category.title)}
									<div class="col-xs-{$node.content.line}">
										<a href="{$category.href}" class="cbp-column-title cbp-category-title">{$category.title}</a>
										{if isset($category.children)}{include file="./front_subcategory.tpl" categories=$category.children level=1}{/if}
									</div>
									{/if}
								{/foreach}
							</div>

						{else}
							<ul class="cbp-links cbp-category-tree">
								{foreach from=$node.content.ids item=category}
								{if isset($category.title)}
									<li {if isset($category.children)}class="cbp-hrsub-haslevel2"{/if}><div class="cbp-category-link-w"><a href="{$category.href}">{$category.title}</a>
										{if isset($category.children)}{include file="./front_subcategory.tpl" categories=$category.children level=2}{/if}</div>
									</li>
									{/if}
								{/foreach}
							</ul>	
						{/if}
					{/if}

				{elseif $node.contentType==3}
					
					{if isset($node.content.ids)} 
						<ul class="cbp-links cbp-valinks{if !$node.content.view} cbp-valinks-vertical{/if}{if $node.content.view==2} cbp-valinks-vertical cbp-valinks-vertical2{/if}">
							{foreach from=$node.content.ids item=va_link}
								{if isset($va_link.href) && isset($va_link.title)}
								<li><a href="{$va_link.href}">{$va_link.title}</a></li>
								{/if}
							{/foreach}
						</ul>	
					{/if}

				{elseif $node.contentType==4}

					{if isset($node.content.ids)}
						{if $node.content.view}
							{include file="./products_grid.tpl" products=$node.content.ids perline=$node.content.line}
						{else}
							{include file="./products_list.tpl" products=$node.content.ids perline=$node.content.line}
						{/if}
					{/if}

				{elseif $node.contentType==5}
					
					<ul class="cbp-manufacturers row">
						{foreach from=$node.content.ids item=manufacturer}
							{assign var="myfile" value="img/m/{$manufacturer|escape:'htmlall':'UTF-8'}-mf_image.jpg"}
							{if file_exists($myfile)}
							<li class="col-xs-{$node.content.line} transition-opacity-300">
								<a href="{$link->getmanufacturerLink($manufacturer)}" title="Manufacturer - {Manufacturer::getNameById($manufacturer)|escape:'htmlall':'UTF-8'}">
							<img src="{$img_manu_dir}{$manufacturer|escape:'htmlall':'UTF-8'}-mf_image.jpg" class="img-responsive logo_manufacturer " {if isset($manufacturerSize)} width="{$manufacturerSize.width}" height="{$manufacturerSize.height}"{/if} alt="Manufacturer - {Manufacturer::getNameById($manufacturer)|escape:'htmlall':'UTF-8'}" />
								</a>
							</li>
							{/if}
						{/foreach}
					</ul>	

				{elseif $node.contentType==6}

					{if isset($node.content.source)}
						{if isset($node.content.href)}<a href="{$node.content.href}">{/if}
							<img src="{$node.content.source}" class="img-responsive cbp-banner-image" {if isset($node.content.alt)}alt="{$node.content.alt|escape:'htmlall':'UTF-8'}"{/if}  />
						{if isset($node.content.href)}</a>{/if}
					{/if}

				{/if}

			{/if}


			

			

			{if isset($node.children) && $node.children|@count > 0}
			{foreach from=$node.children item=child name=categoryTreeBranch}
			{include file="./front_submenu_content.tpl" node=$child }
			{/foreach}
			{/if}
			{if $node.type==2}</div>{/if}
		</div>
