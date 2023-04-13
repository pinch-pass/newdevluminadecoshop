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
*  @author PrestaShop SA
<contact@prestashop.com>
	*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


{include file="$tpl_dir./errors.tpl"}

{if $errors|@count == 0}

	{if !isset($priceDisplayPrecision)}

		{assign var='priceDisplayPrecision' value=2}

	{/if}


	{if !$priceDisplay || $priceDisplay == 2}

		{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}

		{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}

	{elseif $priceDisplay == 1}

		{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}

		{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}

	{/if}
	<div class="primary_block row" itemscope itemtype="http://schema.org/Product">
		<div class="scrolim col-xs-12">
			{include file="$tpl_dir./breadcrumb.tpl"}
		</div>
		{if !$content_only}
			<div class="container">

				<div class="top-hr"></div>

			</div>
		{/if}

		{if isset($adminActionDisplay) && $adminActionDisplay}
			<div id="admin-action">

				<p>
					{l s='This product is not visible to your customers.'}
					<input type="hidden" id="admin-action-product-id" value="{$product->
					id}"/>
					<input type="submit" value="{l s='Publish'}" name="publish_button" class="exclusive"/>

					<input type="submit" value="{l s='Back'}" name="lnk_view" class="exclusive"/>

				</p>

				<p id="admin-action-result"></p>

			</div>
		{/if}


		{if isset($confirmation) && $confirmation}
			<p class="confirmation">{$confirmation}</p>
		{/if}
		<!-- left infos-->
		<div class="pb-center-column col-xs-12 col-sm-4 mobile">
			<div itemprop="name" class="title-h1">{$product->name|escape:'html':'UTF-8'}</div>
			<hr class="active breakleft">
			<p id="product_reference">
				<label>Артикул:</label>
				<span class="editable" itemprop="sku">{if !isset($groups)}{$product->reference|escape:'html':'UTF-8'}{/if}</span>
			</p>
		</div>
		<div class="pb-left-column col-xs-12 col-sm-4 col-md-5">

			<!-- product img-->

			<div id="image-block" class="clearfix">
				{if $product->new}
				<span class="new-box">

					<span class="new-label">
						<!-- {l s='New'} -->
					</span>

				</span>
				{/if}


				{if $product->on_sale}
				<span class="sale-box no-print">

					<span class="sale-label"></span>

				</span>
				{elseif $product->specificPrice && $product->specificPrice.reduction && $productPriceWithoutReduction > $productPrice}
				<span class="discount">{l s='Reduced price!'}</span>
				{/if}


				{if $have_image}
					<span id="view_full_size">
						{if $jqZoomEnabled && $have_image && !$content_only}
							<a class="jqzoom" rel="gal1" href="{$link->
								getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" itemprop="url">
								<img id="bigpic" itemprop="image" src="{$link->
								getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}"/>
							</a>
						{else}
							<img id="bigpic" itemprop="image" src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" width="{$largeSize.width}" height="{$largeSize.height}"/>
						{/if}
					</span>
				{else}
					<span id="view_full_size">
						<img itemprop="image" src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" width="{$largeSize.width}" height="{$largeSize.height}"/>
						{if !$content_only}
							<span class="span_link">{l s='View larger'}</span>
						{/if}
					</span>
				{/if}
			</div>

			<!-- end image-block -->
			{if isset($images) && count($images) > 0}
			<!-- thumbnails -->
			<div id="views_block" class="clearfix {if isset($images) && count($images) < 2}hidden{/if}">
				{if isset($images) && count($images) > 4}
				<span class="view_scroll_spacer_left">

					<a id="view_scroll_left" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Previous'}</a>

				</span>
				{/if}
				<div id="thumbs_list">

					<ul id="thumbs_list_frame">
						{if isset($images)}

								{foreach from=$images item=image name=thumbnails}

									{assign var=imageIds value="`$product->id`-`$image.id_image`"}

									{if !empty($image.legend)}

										{assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}

									{else}

										{assign var=imageTitle value=$product->name|escape:'html':'UTF-8'}

									{/if}
						<li id="thumbnail_{$image.id_image}"{if $smarty.foreach.thumbnails.last} class="last"{/if}>
							{if $jqZoomEnabled && $have_image && !$content_only}
								<a href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}" 
									data-fancybox-group="other-views" class="fancybox{if $image.id_image == $cover.id_image} shown{/if}" rel="{literal}{{/literal}gallery: 'gal1', smallimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}',largeimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}'{literal}}{/literal}">
									<img class="img-responsive" id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'cart_default')|escape:'html':'UTF-8'}" alt="{$imageTitle}" height="{$cartSize.height}" width="{$cartSize.width}" itemprop="image"/></a>
							{else}
								<a href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}" 
									data-fancybox-group="other-views" class="fancybox{if $image.id_image == $cover.id_image} shown{/if}">
									<img class="img-responsive" id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'cart_default')|escape:'html':'UTF-8'}" 
									alt="{$imageTitle}" height="{$cartSize.height}" width="{$cartSize.width}" itemprop="image"/>
								</a>
							{/if}
						</li>
						{/foreach}

							{/if}
					</ul>

				</div>

				<!-- end thumbs_list -->
				{if isset($images) && count($images) > 4}
				<span class="view_scroll_spacer_right">
					<a id="view_scroll_right" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Next'}</a>
				</span>
				{/if}
			</div>
			<!-- end views-block -->

			<!-- end thumbnails -->
			{/if}

			{if isset($images) && count($images) > 1}
			<p class="resetimg clear no-print">

				<span id="wrapResetImages" style="display: none;">

					<a href="{$link->
						getProductLink($product)|escape:'html':'UTF-8'}" name="resetImages"> <i class="icon-repeat"></i>
						{l s='Display all pictures'}
					</a>

				</span>

			</p>
			{/if}
		</div>

		<!-- end pb-left-column -->

		<!-- end left infos-->

		<!-- center infos -->

		<div class="pb-center-column col-xs-12 col-sm-4">

			<h1 itemprop="name" class="pc-only">{$product->name|escape:'html':'UTF-8'}</h1>

			<hr class="active breakleft">

			<div class="top_availability" clearfix>
				<p>
					{if $product->quantity > 0 || $allow_oosp}
					<i class="availability"></i>
					{$product->quantity|intval} {l s='шт.'} {l s='на складе'}
				{else}
					<i class="availabilitynot"></i>
					{l s='Нет в наличии'}
					{/if}
				</p>
				{if false && $PS_STOCK_MANAGEMENT}
				<!-- availability -->
				<p id="availability_statut"{if ($product->
					quantity
					<= 0 && !$product->
						available_later && $allow_oosp) || ($product->quantity > 0 && !$product->available_now) || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
						<span id="availability_label">{l s='Availability:'}</span>
						<span id="availability_value"{if $product->
							quantity
							<= 0} class="warning_inline"{/if}>
								{if $product->quantity
								<= 0}
								{if $allow_oosp}
									{$product->
									available_later}
								{else}
									{l s='This product is no longer in stock'}
								{/if}
							{else}
								{$product->available_now}
							{/if}
								</span>
							</p>
							<p class="warning_inline"
					   id="last_quantities"{if ($product->
								quantity > $last_qties || $product->quantity
								<= 0) || $allow_oosp || !$product->
									available_for_order || $PS_CATALOG_MODE} style="display: none"{/if} >{l s='Warning: Last items in stock!'}
								</p>
								{/if}
								<p id="availability_date"{if ($product->
									quantity > 0) || !$product->available_for_order || $PS_CATALOG_MODE || !isset($product->available_date) || $product->available_date
									< $smarty.now|date_format:'%Y-%m-%d'} style="display: none;"{/if}>
										<span id="availability_date_label">{l s='Availability date:'}</span>
										<span id="availability_date_value">{dateFormat date=$product->available_date full=false}</span>
									</p>
									{if $product->online_only}
									<p class="online_only">{l s='Online only'}</p>
									{/if}
								</div>

								<p id="product_reference"{if empty($product->reference) || !$product->reference} style="display: none;"{/if} class="pc-only">
									<label>{l s='Model'}</label>
									<span class="editable" itemprop="sku">
										{if !isset($groups)}{$product->reference|escape:'html':'UTF-8'}{/if}
									</span>

								</p>
								{capture name=condition}

				{if $product->condition == 'new'}{l s='New'}

				{elseif $product->condition == 'used'}{l s='Used'}

				{elseif $product->condition == 'refurbished'}{l s='Refurbished'}

				{/if}

			{/capture}
								<!--<p id="product_condition"{if !$product->
								condition} style="display: none;"{/if}>
								<label>{l s='Condition'}</label>

								<span class="editable" itemprop="condition">{$smarty.capture.condition}</span>

							</p>
							-->

			{if ($display_qties == 1 && !$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && $product->available_for_order)}
							<!-- number of item in stock -->
							<p id="pQuantityAvailable"{if $product->
								quantity
								<= 0} style="display: none;"{/if}>
									<span id="quantityAvailable">{$product->quantity|intval}</span>
									<span {if $product->
										quantity > 1} style="display: none;"{/if} id="quantityAvailableTxt">{l s='Item'}
									</span>
									<span {if $product->
										quantity == 1} style="display: none;"{/if} id="quantityAvailableTxtMultiple">{l s='Items'}
									</span>
								</p>
								{/if}

			{*
                        {if $product->description_short || $packItems|@count > 0}
                        *}
								<div id="short_description_block">
									{if $product->description_short}
									<div id="short_description_content" class="rte align_justify"
						 itemprop="description">{$product->description_short}</div>
									{/if}
				{if $product->description}
									<p class="buttons_bottom_block">
										<a href="javascript:{ldelim}{rdelim}" class="button">{l s='More details'}</a>
									</p>
									{/if}

				{if isset($HOOK_EXTRA_RIGHT) && $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if}

				{* Yandex start*}
{* <div class="ym-review"> <a class="ym-review__link" href="https://clck.yandex.ru/redir/dtype=stred/pid=47/cid=73582/path=dynamic.88x31/*https://market.yandex.ru/shop--dami-domo-osveshchenie/557087/reviews" target="_blank" rel="nofollow"><div class="ym-review__section ym-review__section_stats"><div class="ym-review__section ym-review__section_raiting"><div class="ym-review__rating"> <span class="ym-review__star ym-review__star_active"></span> <span class="ym-review__star"></span> <span class="ym-review__star"></span> <span class="ym-review__star"></span> <span class="ym-review__star"></span></div></div><div class="ym-review__section ym-review__section_logo"> <img class="ym-review__logo" src="data:image/png;base64,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" alt="Яндекс.Маркет"></div></div> </a></div> *}
									{* Yandex stop*}
									<!--{if $packItems|@count >
									0}
									<div class="short_description_pack">
										<h3>{l s='Pack content'}</h3>
										{foreach from=$packItems item=packItem}
										<div class="pack_content">
											{$packItem.pack_quantity} x
											<a href="{$link->
												getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)|escape:'html':'UTF-8'}">{$packItem.name|escape:'html':'UTF-8'}
											</a>
											<p>{$packItem.description_short}</p>
										</div>
										{/foreach}
									</div>
									{/if}-->
								</div>
								<!-- end short_description_block -->
								{*
                        {/if}
            *}
								<!-- Out of stock hook -->

								<div id="oosHook"{if $product->
									quantity > 0} style="display: none;"{/if}>
				{$HOOK_PRODUCT_OOS}
								</div>
								{if !$content_only}
								<!-- usefull links-->{/if}</div>

							<!-- end center infos-->

							<!-- pb-right-column-->

							<div class="pb-right-column col-xs-12 col-sm-4 col-md-3">
								{if ($product->show_price && !isset($restricted_country_mode)) || isset($groups) || $product->reference || (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
								<!-- add to cart form-->
								<form id="buy_block"{if $PS_CATALOG_MODE && !isset($groups) && $product->quantity > 0} class="hidden"{/if}  action="{$link->getPageLink('cart')|escape:'html':'UTF-8'}" method="post">
									<!-- hidden datas -->
									<p class="hidden">
										<input type="hidden" name="token" value="{$static_token}"/>
										<input type="hidden" name="id_product" value="{$product->id|intval}"  id="product_page_product_id"/>
										<input type="hidden" name="add" value="1"/>
										<input type="hidden" name="id_product_attribute" id="idCombination" value=""/>
									</p>
									<div class="box-info-product">
										<meta itemprop="brand" content="{$product->manufacturer_name}">
										{if $product->ean13}
											<meta itemprop="gtin" content="{$product->ean13}">
										{/if}
										<div class="content_prices clearfix">
											{if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
											<!-- prices -->
											<div class="price">
												<p class="our_price_display" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
													{if $product->quantity > 0}
														<link itemprop="availability" href="http://schema.org/InStock"/>
													{/if}
										{if $priceDisplay >= 0 && $priceDisplay<= 2}
											<span id="our_price_display">{convertPrice price=$productPrice}</span>
													<!--
												{if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) || !isset($display_tax_label))}
												{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
												{/if}-->
													<meta itemprop="price" content="{$productPrice|round:2}"/>
													<meta itemprop="url" content="{$product->
													getLink()|escape:'html':'UTF-8'}"/>
													<meta itemprop="priceCurrency" content="{$currency->
													iso_code}"/>
													<meta itemprop="priceValidUntil" content="{($smarty.now+3600*24)|date_format:'%Y-%m-%d'}" />
													{/if}
												</p>

												<p id="old_price"{if (!$product->
													specificPrice || !$product->specificPrice.reduction) && $group_reduction == 0} class="hidden"{/if}>
										{if $priceDisplay >= 0 && $priceDisplay
													<= 2}
											<span id="old_price_display">
														{if $productPriceWithoutReduction > $productPrice}{convertPrice price=$productPriceWithoutReduction}{/if}
													</span>
													<!-- {if false && $tax_enabled && $display_tax_label == 1}{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}{/if} -->{/if}</p>
												{if $priceDisplay == 2}
												<br/>
												<span id="pretaxe_price">
													<span id="pretaxe_price_display">
														{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}
													</span>
													{*{l s='tax excl.'}*}
												</span>
												{/if}
											</div>
											<!-- end prices -->
											<p id="reduction_amount" {if !$product->
												specificPrice || $product->specificPrice.reduction_type != 'amount' || $product->specificPrice.reduction|floatval ==0} style="display:none"{/if}>
												<span id="reduction_amount_display">
													{if $product->specificPrice && $product->specificPrice.reduction_type == 'amount' && $product->specificPrice.reduction|intval !=0}
	-{convertPrice price=$productPriceWithoutReduction-$productPrice|floatval}
{/if}
												</span>
											</p>
											{if $packItems|@count && $productPrice
											< $product->
												getNoPackPrice()}
												<p class="pack_price">
													{l s='Instead of'}
													<span
												style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span>
												</p>
												{/if}

								{if $product->ecotax != 0}
												<p class="price-ecotax">
													{l s='Include'}
													<span
												id="ecotax_price_display">
														{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}
													</span>
													{l s='For green tax'}
										{if $product->specificPrice && $product->specificPrice.reduction}
													<br/>
													{l s='(not impacted by the discount)'}
										{/if}
												</p>
												{/if}

								{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
									{math equation="pprice / punit_price"  pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
												<p class="unit-price">
													<span
												id="unit_price_display">{convertPrice price=$unit_price}</span>
													{l s='per'} {$product->unity|escape:'html':'UTF-8'}
												</p>
												{/if}
							{/if} {*close if for show price*}
												<div class="clear"></div>

											</div>

											<!-- end content_prices -->

											<div class="product_attributes clearfix">

												<!-- quantity wanted -->

                                                {hook h="displaySeigiXsell" group="4"}
							                    {hook h="displaySeigiXsell" group="6" include_current=1}

												{if !$PS_CATALOG_MODE}
												<p id="quantity_wanted_p"{if (!$allow_oosp && $product->
													quantity
													<= 0) || !$product->
														available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
														<label>{l s='Quantity:'}</label>
														<a href="#" data-field-qty="qty"
									   class="btn btn-default button-minus product_quantity_down">
															<span> <i class="icon-minus"></i>
															</span>
														</a>
														<input type="text" name="qty" id="quantity_wanted" class="text"
										   value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->
														minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}"/>
														<a href="#" data-field-qty="qty"
									   class="btn btn-default button-plus product_quantity_up ">
															<span>
																<i class="icon-plus"></i>
															</span>
														</a>
														<span class="clearfix"></span>
													</p>
													{/if}
													<!-- minimal quantity wanted -->

													<p id="minimal_quantity_wanted_p"{if $product->
														minimal_quantity
														<= 1 || !$product->
															available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
								{l s='This product is not sold individually. You must select at least'}
															<b
										id="minimal_quantity_label">{$product->minimal_quantity}</b>
															{l s='quantity for this product.'}
														</p>
														{if isset($groups)}
														<!-- attributes -->
														<div id="attributes">

															<div class="clearfix"></div>
															{foreach from=$groups key=id_attribute_group item=group}
										{if $group.attributes|@count}
															<fieldset class="attribute_fieldset">
																<label class="attribute_label"
													   {if $group.group_type != 'color' && $group.group_type != 'radio'}for="group_{$id_attribute_group|intval}"{/if}>
																	{$group.name|escape:'html':'UTF-8'}
													:&nbsp;
																</label>
																{assign var="groupName" value="group_$id_attribute_group"}
																<div class="attribute_list">
																	{if ($group.group_type == 'select')}
																	<select name="{$groupName}"
																id="group_{$id_attribute_group|intval}"
																class="form-control attribute_select no-print">
																		{foreach from=$group.attributes key=id_attribute item=group_attribute}
																		<option value="{$id_attribute|intval}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if}
																		title="{$group_attribute|escape:'html':'UTF-8'}">{$group_attribute|escape:'html':'UTF-8'}</option>
																		{/foreach}
																	</select>
																	{elseif ($group.group_type == 'color')}
																	<ul id="color_to_pick_list" class="clearfix">
																		{assign var="default_colorpicker" value=""}
															{foreach from=$group.attributes key=id_attribute item=group_attribute}
																		<li{if $group.default == $id_attribute} class="selected"{/if}>
																			<a href="{$link->
																				getProductLink($product)|escape:'html':'UTF-8'}"
																	   id="color_{$id_attribute|intval}"
																	   name="{$colors.$id_attribute.name|escape:'html':'UTF-8'}"
																	   class="color_pick{if ($group.default == $id_attribute)} selected{/if}"
																	   style="background: {$colors.$id_attribute.value|escape:'html':'UTF-8'};"
																	   title="{$colors.$id_attribute.name|escape:'html':'UTF-8'}">
																		{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
																				<img src="{$img_col_dir}{$id_attribute|intval}.jpg"
																				 alt="{$colors.$id_attribute.name|escape:'html':'UTF-8'}"
																				 width="20" height="20"/>
																				{/if}
																			</a>
																		</li>
																		{if ($group.default == $id_attribute)}
																	{$default_colorpicker = $id_attribute}
																{/if}
															{/foreach}
																	</ul>
																	<input type="hidden" class="color_pick_hidden"
															   name="{$groupName|escape:'html':'UTF-8'}"
															   value="{$default_colorpicker|intval}"/>
																	{elseif ($group.group_type == 'radio')}
																	<ul>
																		{foreach from=$group.attributes key=id_attribute item=group_attribute}
																		<li>
																			<input type="radio" class="attribute_radio"
																		   name="{$groupName|escape:'html':'UTF-8'}"
																		   value="{$id_attribute}" {if ($group.default == $id_attribute)} checked="checked"{/if} />
																			<span>{$group_attribute|escape:'html':'UTF-8'}</span>
																		</li>
																		{/foreach}
																	</ul>
																	{/if}
																</div>

																<!-- end attribute_list -->

															</fieldset>
															{/if}
									{/foreach}
														</div>
														<!-- end attributes -->{/if}</div>
													<!-- end product_attributes -->

													<div class="box-cart-bottom">

														<div {if (!$allow_oosp && $product->
															quantity
															<= 0) || !$product->
																available_for_order || (isset($restricted_country_mode) && $restricted_country_mode) || $PS_CATALOG_MODE} class="unvisible"{/if}>
																<p id="add_to_cart" class="buttons_bottom_block no-print">
																	<button type="submit" name="Submit" class="exclusive">
																		<span>
																			{* <i class="icon icon-shopping-cart"></i>
																			&nbsp;*}В корзину
																		</span>
																	</button>
																</p>

															</div>
															{if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}{$HOOK_PRODUCT_ACTIONS}{/if} <strong></strong>
														</div>
														<!-- end box-cart-bottom -->

													</div>

													<!-- end box-info-product -->

												</form>
												{/if}
											</div>
											<script>$(".b1c").val("Купить в 1 клик");</script>
											{*<img class="our_assets" src="{$img_dir}nashi-preimushchestva.png" alt="Наши Преимущества"/>*}

											<!-- end pb-right-column-->
											<div class="our_assets">
												<div class="dostavkin">
													<img src="/img/dostavka.svg" alt="">
													<p>Бесплатная доставка</p>
												</div>
												<div class="showrooms">
													<img src="/img/showrooms.svg" alt="">
													<p>4 Шоу-рума в г.Москва</p>
												</div>
												<div class="vozvrats">
													<img src="/img/vozvrats.svg" alt="">
													<p>Возврат до 30 дней</p>
												</div>
												<div class="garantys">
													<img src="/img/policy.svg" alt="">
													<p>Гарантия<br> 2 года</p>
												</div>
											</div>
										</div>
										<!-- end primary_block -->
										{if isset($product) && $product->description}
	<div id="more_info_sheets" class="sheets align_justify">
		<!-- full description -->
		<div id="idTab1">{$product->description}</div>
	</div>
	{/if}
	{if !$content_only}
		<ul id="more_info_tabs" class="idTabs idTabsShort clearfix">

			{if $features}
				<li><a id="more_info_tab_data_sheet" href="#idTab2">{l s='Data sheet'}</a></li>{/if}

			{if false && $product->description}
				<li><a id="more_info_tab_more_info" href="#idTab1">{l s='More info'}</a></li>{/if}

			{if $attachments}
				<li><a id="more_info_tab_attachments" href="#idTab9">{l s='Download'}</a></li>{/if}

			{*{if isset($accessories) AND $accessories}<li><a href="#idTab4">{l s='Accessories'}</a></li>{/if}*}

			{if isset($product) && $product->customizable}
				<li><a href="#idTab10">{l s='Product customization'}</a></li>
			{/if}

			{$HOOK_PRODUCT_TAB}

		</ul>
		<div id="more_info_sheets" class="sheets align_justify">




			{if isset($features) && $features}


											<!-- product's features -->
											<div id="idTab2" class="row">
												{capture name=some_content assign=popText1}{l s='Kolor i materiał'}{/capture}
				{capture name=some_content assign=popText2}{l s='Dane techniczne'}{/capture}
				{capture name=some_content assign=popText3}{l s='Rozmiar'}{/capture}
				{capture name=some_content assign=popText4}{l s='Informacje dodatkowe'}{/capture}
				{assign var=s_groups value=[
				$popText1 => [9,10,11,12,13,27],
				$popText2 => [14,15,16,28,18,19,35,40,46],
				$popText3 => [3,20,2,1,21,22,48,47,29],
				$popText4 => [23,24,6,4,25,26,98,43,36,34,30]
				]}

				{assign var=blacklist value=[]}
				{foreach $s_groups as $sgrp_name => $svalues}
					<div class="col-xs-12 col-md-6 block_tad_one">
						<table>
							<tbody>
								<tr>
									<td colspan="2" class="tdcoll">{$sgrp_name}</td>
								</tr>
								{foreach from=$features item=feature}
									{if isset($feature.value) && in_array($feature.id_feature, $svalues)}
									{append var=blacklist value=$feature.id_feature}
										<tr>
											<td>{$feature.name|escape:'htmlall':'UTF-8'}</td>
											<td>{$feature.value|escape:'htmlall':'UTF-8'}</td>
										</tr>
									{/if}
								{/foreach}
								{if $popText4 == $sgrp_name}
									<tr>
										<td>Бренд</td>
										<td>Lumina Deco</td>
									</tr>
									<tr>
										<td>Страна</td>
										<td>Польша</td>
									</tr>
								{/if}
							</tbody>
						</table>
					</div>
				{/foreach}
												<!-- <div class="col-xs-12 col-md-6 block_tad_one" style="clear:left;">
												-->
												<!-- <table>
												-->
												<!-- <tbody>
												-->
												<!-- <tr>
												<td colspan="2" class="tdcoll">Inne</td>
											</tr>
											-->
											<!-- {foreach from=$features item=feature} -->
											<!-- {if isset($feature.value) && !in_array($feature.id_feature, $blacklist)} -->
											<!-- <tr>
											<td>{$feature.name|escape:'htmlall':'UTF-8'}</td>
											<td>{$feature.value|escape:'htmlall':'UTF-8'}</td>
										</tr>
										-->
										<!-- {/if}	 -->
										<!-- {/foreach} -->
										<!-- </tbody>
										-->
										<!-- </table>
										-->
										<!-- </div>
										-->
				{/if}
									</div>
									{if isset($attachments) && $attachments}
									<ul id="idTab9" class="bullet">
										{foreach from=$attachments item=attachment}
										<li>
											<a href="{$link->
												getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">
												<span class="downfile"></span>
												{$attachment.name|escape:'htmlall':'UTF-8'}
											</a>
											<br>
											<br>{$attachment.description|escape:'htmlall':'UTF-8'}</li>
										{/foreach}
									</ul>
									{/if}
									<!-- Customizable products -->
									{if isset($product) && $product->customizable}
									<div id="idTab10" class="bullet customization_block">

										<form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data"
							  id="customizationForm" class="clearfix">

											<p class="infoCustomizable">
												{l s='After saving your customized product, remember to add it to your cart.'}

								{if $product->uploadable_files}
												<br/>
												{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
											</p>
											{if $product->uploadable_files|intval}
											<div class="customizableProductsFile">

												<h3>{l s='Pictures'}</h3>

												<ul id="uploadable_files" class="clearfix">
													{counter start=0 assign='customizationField'}

										{foreach from=$customizationFields item='field' name='customizationFields'}

											{if $field.type == 0}
													<li class="customizationUploadLine{if $field.required} required{/if}">
														{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}

													{if isset($pictures.$key)}
														<div class="customizationUploadBrowse">

															<img src="{$pic_dir}{$pictures.$key}_small_default" alt=""/>

															<a href="{$link->
																getProductDeletePictureLink($product, $field.id_customization_field)}"
															   title="{l s='Delete'}">
																<img src="{$img_dir}icon/delete.gif"
																	 alt="{l s='Delete'}"
																	 class="customization_delete_icon" width="11"
																	 height="13"/>

															</a>

														</div>
														{/if}
														<div class="customizationUploadBrowse">

															<label class="customizationUploadBrowseDescription">
																{if !empty($field.name)}{$field.name}{else}{l s='Please select an image file from your hard drive'}{/if}{if $field.required} <sup>*</sup>
																{/if}
															</label>

															<input type="file" name="file{$field.id_customization_field}"
															   id="img{$customizationField}"
															   class="customization_block_input {if isset($pictures.$key)}filled{/if}"/>

														</div>

													</li>
													{counter}

											{/if}

										{/foreach}
												</ul>

											</div>
											{/if}


							{if $product->text_fields|intval}
											<div class="customizableProductsText">

												<h3>{l s='Text'}</h3>

												<ul id="text_fields">
													{counter start=0 assign='customizationField'}

										{foreach from=$customizationFields item='field' name='customizationFields'}

											{if $field.type == 1}
													<li class="customizationUploadLine{if $field.required} required{/if}">

														<label for="textField{$customizationField}">
															{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field} {if !empty($field.name)}{$field.name}{/if}{if $field.required} <sup>*</sup>
															{/if}
														</label>

														<textarea type="text"
															  name="textField{$field.id_customization_field}"
															  id="textField{$customizationField}" rows="1" cols="40"
															  class="customization_block_input"/>
														{if isset($textFields.$key)}{$textFields.$key|stripslashes}{/if}
													</textarea>

												</li>
												{counter}

											{/if}

										{/foreach}
											</ul>

										</div>
										{/if}
										<p id="customizedDatas">

											<input type="hidden" name="quantityBackup" id="quantityBackup" value=""/>

											<input type="hidden" name="submitCustomizedDatas" value="1"/>

											<input type="button" class="button" value="{l s='Save'}"
									   onclick="javascript:saveCustomization()"/>

											<span id="ajax-loader" style="display:none">
												<img src="{$img_ps_dir}loader.gif"
																				 alt="loader"/>
											</span>

										</p>

									</form>

									<p class="clear required">
										<sup>*</sup>
										{l s='required fields'}
									</p>

								</div>
								{/if}



				{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}{$HOOK_PRODUCT_TAB_CONTENT}{/if}
							</div>

						</div>
						{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
						<!-- quantity discount -->
						<ul class="idTabs clearfix">

							<li>
								<a href="#discount" style="cursor: pointer" class="selected">{l s='Quantity discount'}</a>
							</li>

						</ul>
						<div id="quantityDiscount">

							<table class="std">

								<thead>

									<tr>

										<th>{l s='product'}</th>

										<th>{l s='from (qty)'}</th>

										<th>{l s='discount'}</th>

									</tr>

								</thead>

								<tbody>
									{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
									<tr id="quantityDiscount_{$quantity_discount.id_product_attribute}">

										<td>
											{if (isset($quantity_discount.attributes) && ($quantity_discount.attributes))}

									{$product->getProductName($quantity_discount.id_product, $quantity_discount.id_product_attribute)}
								{else}
									{$product->getProductName($quantity_discount.id_product)}
								{/if}
										</td>
										<td>{$quantity_discount.quantity|intval}</td>
										<td>
											{if $quantity_discount.price >= 0 OR $quantity_discount.reduction_type == 'amount'}
									-{convertPrice price=$quantity_discount.real_value|floatval}
								{else}
									-{$quantity_discount.real_value|floatval}%
								{/if}
										</td>
									</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
						{/if}
		{if isset($accessories) AND $accessories}
						<!-- accessories -->
						<section id="last_viewed" class="page-product-box blockproductscategory">
							<div class="titlebordrtext1">
								<div class="titleborderh5 titleborderh5-h4">{l s='Customers bought also'}</div>
							</div>
							<div class="titleborderout1">
								<div class="titleborder2"></div>
							</div>
							<div id="similiar_products" class="clearfix">
								<ul class="autobx bxslider clearfix">
									{foreach from=$accessories item=accessory name=accessories_list}
							{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
									<li class="product-box item">
										<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" class="lnk_img product-image">
											<img src="{$link->
											getImageLink($accessory.link_rewrite, $accessory.id_image, 'home_default')}"
										 alt="{$accessory.legend|escape:'html':'UTF-8'}"/>
										</a>
										<h5 class="product-name">
											<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}">
												{$accessory.name|truncate:20:'...':true|escape:'htmlall':'UTF-8'}
											</a>
										</h5>
										<div class="price_display">
											<span class="price">{convertPrice price=$accessory.price}</span>
											<div class="price_sub">
												{if $accessory.quantity > 0}
													<i class="avail"></i> {l s='В наличии'}
												{else}
													<i class="avail_not"></i> {l s='Нет в наличии'}
												{/if}
											</div>
										</div>
										<br/>
									</li>
									{/foreach}
								</ul>
							</div>
						</section>
						{/if}
		{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}
						<script>
			if (document.getElementById("last_viewed")) {
				document.getElementById("blockviewed2").style.display = "none";
			}
		</script>
						<!-- description and features -->
						{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments)}
					</div>
					{/if}

		{if isset($packItems) && $packItems|@count > 0}
					<section id="blockpack">
						<h3 class="page-product-heading">{l s='Pack content'}</h3>
						{include file="$tpl_dir./product-list.tpl" products=$packItems}
					</section>
					{/if}
	{/if}

	{strip}
	{strip}
		{if isset($smarty.get.ad) && $smarty.get.ad}
			{addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
		{/if}
		{if isset($smarty.get.adtoken) && $smarty.get.adtoken}
			{addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
		{/if}
		{addJsDef allowBuyWhenOutOfStock=$allow_oosp|boolval}
		{addJsDef availableNowValue=$product->available_now|escape:'quotes':'UTF-8'}
		{addJsDef availableLaterValue=$product->available_later|escape:'quotes':'UTF-8'}
		{addJsDef attribute_anchor_separator=$attribute_anchor_separator|addslashes}
		{addJsDef attributesCombinations=$attributesCombinations}
		{addJsDef currencySign=$currencySign|html_entity_decode:2:"UTF-8"}
		{addJsDef currencyRate=$currencyRate|floatval}
		{addJsDef currencyFormat=$currencyFormat|intval}
		{addJsDef currencyBlank=$currencyBlank|intval}
		{addJsDef currentDate=$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}
		{if isset($combinations) && $combinations}
			{addJsDef combinations=$combinations}
			{addJsDef combinationsFromController=$combinations}
			{addJsDef displayDiscountPrice=$display_discount_price}
			{addJsDefL name='upToTxt'}{l s='Up to' js=1}{/addJsDefL}
		{/if}
		{if isset($combinationImages) && $combinationImages}
			{addJsDef combinationImages=$combinationImages}
		{/if}
		{addJsDef customizationFields=$customizationFields}
		{addJsDef default_eco_tax=$product->ecotax|floatval}
		{addJsDef displayPrice=$priceDisplay|intval}
		{addJsDef ecotaxTax_rate=$ecotaxTax_rate|floatval}
		{addJsDef group_reduction=$group_reduction}
		{if isset($cover.id_image_only)}
			{addJsDef idDefaultImage=$cover.id_image_only|intval}
		{else}
			{addJsDef idDefaultImage=0}
		{/if}
		{addJsDef img_ps_dir=$img_ps_dir}
		{addJsDef img_prod_dir=$img_prod_dir}
		{addJsDef id_product=$product->id|intval}
		{addJsDef jqZoomEnabled=$jqZoomEnabled|boolval}
		{addJsDef maxQuantityToAllowDisplayOfLastQuantityMessage=$last_qties|intval}
		{addJsDef minimalQuantity=$product->minimal_quantity|intval}
		{addJsDef noTaxForThisProduct=$no_tax|boolval}
		{addJsDef oosHookJsCodeFunctions=Array()}
		{addJsDef productHasAttributes=isset($groups)|boolval}
		{addJsDef productPriceTaxExcluded=($product->getPriceWithoutReduct(true)|default:'null' - $product->ecotax)|floatval}
		{addJsDef productBasePriceTaxExcluded=($product->base_price - $product->ecotax)|floatval}
		{addJsDef productReference=$product->reference|escape:'html':'UTF-8'}
		{addJsDef productAvailableForOrder=$product->available_for_order|boolval}
		{addJsDef productPriceWithoutReduction=$productPriceWithoutReduction|floatval}
		{addJsDef productPrice=$productPrice|floatval}
		{addJsDef productUnitPriceRatio=$product->unit_price_ratio|floatval}
		{addJsDef productShowPrice=(!$PS_CATALOG_MODE && $product->show_price)|boolval}
		{addJsDef PS_CATALOG_MODE=$PS_CATALOG_MODE}
		{if $product->specificPrice && $product->specificPrice|@count}
			{addJsDef product_specific_price=$product->specificPrice}
		{else}
			{addJsDef product_specific_price=array()}
		{/if}
		{if $display_qties == 1 && $product->quantity}
			{addJsDef quantityAvailable=$product->quantity}
		{else}
			{addJsDef quantityAvailable=0}
		{/if}
		{addJsDef quantitiesDisplayAllowed=$display_qties|boolval}
		{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'percentage'}
			{addJsDef reduction_percent=$product->specificPrice.reduction*100|floatval}
		{else}
			{addJsDef reduction_percent=0}
		{/if}
		{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'amount'}
			{addJsDef reduction_price=$product->specificPrice.reduction|floatval}
		{else}
			{addJsDef reduction_price=0}
		{/if}
		{if $product->specificPrice && $product->specificPrice.price}
			{addJsDef specific_price=$product->specificPrice.price|floatval}
		{else}
			{addJsDef specific_price=0}
		{/if}
		{addJsDef specific_currency=($product->specificPrice && $product->specificPrice.id_currency)|boolval} {* TODO: remove if always false *}
		{addJsDef stock_management=$stock_management|intval}
		{addJsDef taxRate=$tax_rate|floatval}
		{addJsDefL name=doesntExist}{l s='This combination does not exist for this product. Please select another combination.' js=1}{/addJsDefL}
		{addJsDefL name=doesntExistNoMore}{l s='This product is no longer in stock' js=1}{/addJsDefL}
		{addJsDefL name=doesntExistNoMoreBut}{l s='with those attributes but is available with others.' js=1}{/addJsDefL}
		{addJsDefL name=fieldRequired}{l s='Please fill in all the required fields before saving your customization.' js=1}{/addJsDefL}
		{addJsDefL name=uploading_in_progress}{l s='Uploading in progress, please be patient.' js=1}{/addJsDefL}
	{/strip}
{/if}
