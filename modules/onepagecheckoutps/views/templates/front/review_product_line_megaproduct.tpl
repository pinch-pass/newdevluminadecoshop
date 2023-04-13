{*
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop
*
* @category  PrestaShop
* @category  Module
* @author    PresTeamShop.com <support@presteamshop.com>
* @copyright 2011-2018 PresTeamShop
* @license   see file: LICENSE.txt
*}

<div class="row {if isset($productLast) and $productLast && (not isset($ignoreProductLast) or !$ignoreProductLast)}last_item{elseif isset($productFirst) and $productFirst}first_item{/if} {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}alternate_item{/if} cart_item address_{$product.id_address_delivery|intval}"
     id="megaproduct_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$mega.id_megacart|intval}">
	<div class="col-md-1 col-xs-3 text-center nopadding-xs">
		<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">
			<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'htmlall':'UTF-8'}"
				 alt="{$product.name|escape:'htmlall':'UTF-8'}" class="img-thumbnail" />
            {if $CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT}<i class="fa-pts fa-pts-search fa-pts-1x"></i>{/if}
		</a>
        {if $CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT}
            <div class="image_zoom">
                <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'htmlall':'UTF-8'}"
                     alt="{$product.name|escape:'htmlall':'UTF-8'}"/>
            </div>
        {/if}
	</div>
	<div class="col-md-{if $CONFIGS.OPC_SHOW_UNIT_PRICE}4{else}6{/if} col-xs-9 cart_description">
		<p class="s_title_block">
            {if !$CONFIGS.OPC_REMOVE_LINK_PRODUCTS}
                <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">
            {/if}
                {$product.name|escape:'htmlall':'UTF-8'}
            {if !$CONFIGS.OPC_REMOVE_LINK_PRODUCTS}
                </a>
            {/if}
        </p>

		<span class="product_attributes">
			{if (isset($product.attributes) && $product.attributes) || (isset($mega.extraAttrLong) && $mega.extraAttrLong)}
				<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'htmlall':'UTF-8'}">{if isset($product.attributes)}{$product.attributes|escape:'htmlall':'UTF-8'}{/if}{if isset($mega.extraAttrLong)}{$mega.extraAttrLong|escape:'htmlall':'UTF-8':false:true}{/if}</a>
			{/if}
			<br/>
			<i>{$mega.measure|escape:'htmlall':'UTF-8'}</i>
			{if isset($mega.personalization) && $mega.personalization neq ''}
				<br/>
				<div class="mp-personalization">{$mega.personalization|escape:'quotes':'UTF-8'}</div>
			{/if}
		</span>

		{if $product.reference and $CONFIGS.OPC_SHOW_REFERENCE}
			<span class="product_reference">
				{l s='Ref.' mod='onepagecheckoutps'}&nbsp;{$product.reference|escape:'htmlall':'UTF-8'}
			</span>
		{/if}

		{if $product.weight neq 0 and $CONFIGS.OPC_SHOW_WEIGHT}
			<span class="product_weight">
				{l s='Weight' mod='onepagecheckoutps'}&nbsp;:&nbsp;{$product.weight|string_format:"%.3f"|escape:'htmlall':'UTF-8'}{$PS_WEIGHT_UNIT}
			</span>
		{/if}

        {if $onepagecheckoutps->isProductFreeShipping($product.id_product)}
            <b>{l s='Product with free shipping.' mod='onepagecheckoutps'}</b>
        {/if}
	</div>

	<div class="visible-xs visible-sm row clear"></div>

	{if $CONFIGS.OPC_SHOW_UNIT_PRICE}
		<div class="col-xs-3 text-right visible-xs visible-sm">
			<label><b>{l s='Unit price' mod='onepagecheckoutps'}:</b></label>
		</div>
		<div class="col-md-2 col-xs-9 text-right text-left-xs text-left-sm">
			<span>
				{if !$priceDisplay}
					{convertPrice price=$mega.pricewt}
				{else}
					{convertPrice price=$mega.price}
				{/if}
			</span>
		</div>
	{/if}

	<div class="visible-xs visible-sm row clear"></div>

	<div class="col-xs-3 text-right visible-xs visible-sm">
		<label><b>{l s='Quantity' mod='onepagecheckoutps'}:</b></label>
	</div>
	<div class="col-md-3 col-xs-9 text-right">
		<div class="row middle-xs start-xs center-md">
			<div class="col-xs-12 cart_quantity nopadding-xs">
				<div id="cart_quantity_button" class="cart_quantity_button">
					<input type="hidden" value="{$mega.quantity|intval}"
						name="megaquantity_{$product.id_product|intval}_{$product.id_product_attribut|intval}_0_{$product.id_address_delivery|intval}_hidden_{$mega.id_megacart|intval}" />
					<div class="row end-md start-xs pts-vcenter">
						<div class="input-group input-group-sm">
							{if isset($cannotModify) AND $cannotModify == 1}
								<input type="text" autocomplete="off" class="cart_quantity_input form-control input-number text-center disabled"
									value="{$mega.quantity|intval}"
									name="megaquantity_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.id_address_delivery|intval}_{$mega.id_megacart|intval}"
									disabled="disabled"/>
							{else}
								<span class="input-group-btn">
									<button type="button" class="btn btn-default btn-number megacart_quantity_down button-minus"
											{if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
											{else}
												disabled="disabled"
											{/if}
											data-type="minus" id="megacart_quantity_down_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.id_address_delivery|intval}_{$mega.id_megacart|intval}">
										<i class="fa-pts fa-pts-minus"></i>
									</button>
								</span>
								<input type="text" autocomplete="off" class="cart_quantity_input form-control input-number text-center disabled"
									value="{$mega.quantity|intval}"
									name="megaquantity_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.id_address_delivery|intval}_{$mega.id_megacart|intval}"/>
								<span class="input-group-btn">
									<button type="button" class="btn btn-default btn-number megacart_quantity_up button-plus" data-type="plus"
											id="megacart_quantity_up_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.id_address_delivery|intval}_{$mega.id_megacart|intval}">
										<i class="fa-pts fa-pts-plus"></i>
									</button>
								</span>
							{/if}
						</div>
						<div class="input-group input-group-sm center-xs nopadding">
							<span class="input-group-btn">
								<a type="button" class="megacart_quantity_delete btn-number"
								   id="{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$mega.id_megacart|intval}"
								   href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;id_megacart={$mega.id_megacart|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'htmlall':'UTF-8'}">
									<i class="fa-pts fa-pts-trash-o"></i>
								</a>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="visible-xs visible-sm row clear"></div>

	<div class="col-xs-3 text-right visible-xs visible-sm">
		<label><b>{l s='Total' mod='onepagecheckoutps'}:</b></label>
	</div>
	<div class="col-md-2 col-xs-9 text-right text-left-xs text-left-sm">
		<span class="price" id="total_product_price_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<span class="gift-icon">{l s='Gift!' mod='onepagecheckoutps'}</span>
			{else}
				{if !$priceDisplay}
					{displayPrice price=$mega.totalwt}
				{else}
					{displayPrice price=$mega.total}
				{/if}
			{/if}
		</span>
	</div>
</div>