{*$product|@debug_print_var*}
<div id="quick_order">
	<div class="owner-oneclick__success" style="margin: 0 auto;display: none">
		<p class="oneclick-sew-by-order__heading">Ваша заявка принята!</p>
		<p class="-oneclicksew-by-order__desc">В ближайшее время с Вами свяжеться наш менеджер!</p>
		<img src="{$modules_dir}ecm_quickorder/img/check-mark.svg"/>
		<button class="oneclick-sew-by-order__button" id="closeModal" type="submit" >Продолжить покупки</button>
	</div>
<form action="" method="post" id="ecm_quickorderform" class="box ecm_box">
	<div class="row">
		<div class="ecm_col col-xs-12 col-sm-12 col-md-{if !$ecm_quickorder_hide_adv_block}6{else}12{/if}">

			   
				<fieldset>
			        <div class="ecm_form_content form_content clearfix ">
					 <h3 class="ecm_page-subheading">{l s='Купить в 1 клик' mod='ecm_quickorder'}</h3>
				<p class="subheading">{l s='Заполните данные ниже и наш менеджер свяжеться с вами для подтверждения заказа' mod='ecm_quickorder'}</p>
						{if !$ecm_quickorder_hide_firstname}
			            <div class="form-group">
			                <label for="name">{l s='Ваше имя' mod='ecm_quickorder'}</label>
			                <input class="is_required form-control ecm_form-control" type="text" id="firstname" name="firstname" value="{$firstname}" placeholder="{l s='Введите ваше имя' mod='ecm_quickorder'}" />
			            </div>
						{/if}
						{if !$ecm_quickorder_hide_lastname}
			            <div class="form-group">
			                <label for="name">{l s='family name' mod='ecm_quickorder'}*</label>
			                <input class="is_required form-control ecm_form-control" type="text" id="lastname" name="lastname" value="{$lastname}" />
			            </div>
						{/if}
						{if !$ecm_quickorder_hide_email}
			            <div class="form-group">
			                <label for="email" id="emailab">{l s='E-mail' mod='ecm_quickorder'}*</label>
			                <input class="is_required validate account_input form-control" data-validate="isEmail" type="text" id="login_email" name="login_email" value="{$email}" />
			            </div>
						{/if}
						{if !$ecm_quickorder_hide_phone}
			            <div class="form-group">
			                <label for="phone">{l s='Контактный телефон' mod='ecm_quickorder'}</label>
			                <input class="is_required form-control ecm_form-control phone phone-ic" type="text" id="phone" name="phone" value="" placeholder="{l s='Введите контактный телефон' mod='ecm_quickorder'}"/>
			            </div>
						{/if}
						<div class="modal-check">
							<label class="custom-checkbox">
								<input class="comparator" type="checkbox" name="personal" value="1">
								<p>Я ознакомился и принимаю условия <a href="{$link->getCMSLink(16)}">“Политики конфиденциальности”</a> и <a href="{$link->getCMSLink(17)}">“Пользовательского соглашения“</a></p>
							</label>
						</div>
			            <p class="submit">
			                <input id="id_product" name="id_product" type="hidden" value="{$product.id_product}">
			                <input id="id_product_attribute" name="id_product_attribute" type="hidden" value="{$id_product_attribute}">
			                <button type="submit" id="quickorder_button" name="submitecm_quickorder" class="btn btn-default"><div id="contentload"></div> <img src="{$img_dir}cursor.svg"/> {l s='Купить в один клик' mod='ecm_quickorder'}</button>
			            </p>
						{*<span style="color: #f13340;"> {l s='Fields marked with an asterisk are required' mod='ecm_quickorder'}</span>*}
			            <div class="bootstrap" id="error" style="margin-top: 15px;">
							<div class="alert alert-danger">

				        	</div>
						</div>
				  	</div>
					{*<div style="float:left; width: 70%; padding: 20px;"></div>*}
				</fieldset>

		 </div>
		 {if !$ecm_quickorder_hide_adv_block}
		 <div class="col-xs-12 col-sm-12 col-md-6">
			<div class="product-image-container">
					<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />

			</div>
					<h5 itemprop="name">
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
						<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
							{$product.name|escape:'html':'UTF-8'}
						</a>
					</h5>

						{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							<div class="content_price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
								{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
								   <input id="price_no" type="hidden" value="{$product.price}">
								   <label>{l s='Price' mod='ecm_quickorder'}</label>
								   	<span class="count-price">1 шт. :</span>
									<span itemprop="price" class="price product-price">
										{convertPrice price=$product.price}
									</span>
								{/if}
							</div>
						{/if}
					{if !$PS_CATALOG_MODE}
						<p id="quantity_wanted_p"{if (!$allow_oosp && $product.quantity <= 0) || !$product.available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
			                <input name="allowBuyWhenOutOfStock" type="hidden" value="{$product.allow_oosp|boolval}">
							{if $product.quantity}
								{addJsDef quantityAvailable=$product.quantity}
								<input name="quantityAvailable" type="hidden" value="{$product.quantity}">
							{else}
								<input name="quantityAvailable" type="hidden" value="0">
							{/if}
							<label for="quantity_wanted">{l s='Quantity' mod='ecm_quickorder'}</label>
							<input type="number" min="1" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product.minimal_quantity > 1}{$product.minimal_quantity}{else}1{/if}{/if}" />
							<a href="#" data-field-qty="qty" class="btn btn-default button-minus product_quantity_down">
								<span><i class="icon-minus"></i></span>
							</a>
							<a href="#" data-field-qty="qty" class="btn btn-default button-plus product_quantity_up">
								<span><i class="icon-plus"></i></span>
							</a>
							<span class="clearfix"></span>
						</p>
						{/if}


		</div>
		{/if}
	</div>
</form>
</div>
