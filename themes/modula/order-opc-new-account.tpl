<div id="opc_new_account" class="opc-main-block">
	<div id="opc_new_account-overlay" class="opc-overlay" style="display: none;"></div>
	<span class="heading-number">1</span> <h1 class="page-heading-on step-num">Контактные данные</h1>
	
	<form action="{$link->getPageLink('authentication', true)|escape:'html'}" method="post" id="new_account_form" class="std" autocomplete="on" autofill="on">
		<fieldset>
        	<div class="box" style=" padding-top: 0; ">
				<div id="opc_account_form">
				{$HOOK_CREATE_ACCOUNT_TOP}
				<!-- Error return block -->
				<div id="opc_account_errors" class="alert alert-danger" style="display:none;"></div>
				<!-- END Error return block -->
				<!-- Account -->
				<input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
				<input type="hidden" id="opc_id_customer" name="opc_id_customer" value="{if isset($guestInformations) && isset($guestInformations.id_customer) && $guestInformations.id_customer}{$guestInformations.id_customer}{else}0{/if}" />
				<input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="{if isset($guestInformations) && isset($guestInformations.id_address_delivery) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
				<input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="{if isset($guestInformations) && isset($guestInformations.id_address_delivery) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
				<div class="required form-group">
					<label for="firstname">{l s='First name'} <sup>*</sup></label>
					<input type="text" class="text form-control validate" id="customer_firstname" name="customer_firstname" onblur="$('#firstname').val($(this).val());" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.customer_firstname) && $guestInformations.customer_firstname}{$guestInformations.customer_firstname}{/if}" />
				</div>
				<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
					<label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
					<input type="text" class="text form-control validate" name="phone_mobile" id="phone_mobile" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile) && $guestInformations.phone_mobile}{$guestInformations.phone_mobile}{/if}" />
				</div>
				<div class="required text form-group">
					<label for="email">{l s='Email'} <sup>*</sup></label>
					<input type="text" class="text form-control validate" id="email" name="email" data-validate="isEmail" value="{if isset($guestInformations) && isset($guestInformations.email) && $guestInformations.email}{$guestInformations.email}{/if}" />
				</div>
				<div class="required password is_customer_param form-group" style="display: none;">
					<input type="password" class="text form-control validate" name="passwd" id="passwd" data-validate="isPasswd" value="djsak342" />
					<span class="form_info">{l s='(five characters min.)'}</span>
				</div>
				
				<div class="required form-group">
					<input type="hidden" class="form-control validate" id="customer_lastname" name="customer_lastname" onblur="$('#lastname').val($(this).val());" data-validate="isName" value="Неуказанная" />
				</div>
				<h3 class="page-subheading top-indent">Адрес</h3>
				{$stateExist = false}
				{$postCodeExist = false}
				{$dniExist = false}
				{foreach from=$dlv_all_fields item=field_name}
				{if $field_name eq "company" && $b2b_enable}
					<div class="text form-group">
						<label for="company">{l s='Company'}</label>
						<input type="text" class="text form-control validate" id="company" name="company" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.company) && $guestInformations.company}{$guestInformations.company}{/if}" />
					</div>
				{elseif $field_name eq "vat_number"}	
				<div id="vat_number_block" style="display:none;">
					<div class="form-group">
						<label for="vat_number">{l s='VAT number'}</label>
						<input type="text" class="text form-control" name="vat_number" id="vat_number" value="{if isset($guestInformations) && isset($guestInformations.vat_number) && $guestInformations.vat_number}{$guestInformations.vat_number}{/if}" />
					</div>
				</div>
				{elseif $field_name eq "dni"}
				{assign var='dniExist' value=true}
				<div class="required dni form-group">
					<label for="dni">{l s='Identification number'} <sup>*</sup></label>
					<input type="text" class="text form-control validate" name="dni" id="dni" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
					<span class="form_info">{l s='DNI / NIF / NIE'}</span>
				</div>
				{elseif $field_name eq "firstname"}
				<div class="required text form-group">
					<input type="hidden" class="text form-control validate" id="firstname" name="firstname" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.firstname) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" />
				</div>
				{elseif $field_name eq "lastname"}
				<div class="required text form-group">
					<input type="hidden" class="text form-control validate" id="lastname" name="lastname" data-validate="isName" value="Неуказанная" />
				</div>
				{elseif $field_name eq "address1"}
				<div class="required text form-group">
					<label for="city">{l s='City'} <sup>*</sup></label>
					<input type="text" class="text form-control validate" name="city" id="city" data-validate="isCityName" value="{if isset($guestInformations) && isset($guestInformations.city) && $guestInformations.city}{$guestInformations.city}{/if}" />
				</div>
				<div class="required text form-group">
					<label for="address1">Улица, дом, квартира <sup>*</sup></label>
					<input type="text" class="text form-control validate" name="address1" id="address1" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address1) && isset($guestInformations) && isset($guestInformations.address1) && $guestInformations.address1}{$guestInformations.address1}{/if}" />
				</div>
				{elseif $field_name eq "address2"}
				<div class="text is_customer_param form-group">
					<label for="address2">{l s='Address (Line 2)'}</label>
					<input type="text" class="text form-control validate" name="address2" id="address2" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address2) && isset($guestInformations) && isset($guestInformations.address2) && $guestInformations.address2}{$guestInformations.address2}{/if}" />
				</div>
				{elseif $field_name eq "postcode"}
				{$postCodeExist = true}
				<div class="required postcode text form-group">
					<label for="postcode">{l s='Zip/Postal code'} <sup>*</sup></label>
					<input type="text" class="text form-control validate" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
				</div>
				{elseif $field_name eq "country" || $field_name eq "Country:name"}
				<div class="required select form-group" style=" position: absolute; opacity: 0; z-index: -1;">
					<label for="id_country">{l s='Country'} <sup>*</sup></label>
					<select name="id_country" id="id_country" class="form-control"><option value="177" selected="selected">Россия</option> </select>
				</div>
				{elseif $field_name eq "state" || $field_name eq 'State:name'}
				{$stateExist = true}
				<div class="required id_state form-group" style="display:none;">
					<label for="id_state">{l s='State'} <sup>*</sup></label>
					<select name="id_state" id="id_state" class="form-control">
						<option value="">-</option>
					</select>
				</div>
				{/if}
				{/foreach}
				{if !$postCodeExist}
				<div class="required postcode form-group unvisible">
					<label for="postcode">{l s='Zip/Postal code'} <sup>*</sup></label>
					<input type="text" class="text form-control validate" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
				</div>
				{/if}				
				{if !$stateExist}
				<div class="required id_state form-group unvisible">
					<label for="id_state">{l s='State'} <sup>*</sup></label>
					<select name="id_state" id="id_state" class="form-control">
						<option value="">-</option>
					</select>
				</div>
				{/if}
				{if !$dniExist}
				<div class="required dni form-group">
					<label for="dni">{l s='Identification number'} <sup>*</sup></label>
					<input type="text" class="text form-control validate" name="dni" id="dni" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
					<span class="form_info">{l s='DNI / NIF / NIE'}</span>
				</div>
				{/if}
				<div class="form-group is_customer_param" style="display: none;">
					<label for="other">{l s='Additional information'}</label>
					<textarea class="form-control" name="other" id="other" cols="26" rows="7"></textarea>
				</div>
				{if isset($one_phone_at_least) && $one_phone_at_least}
					<p class="inline-infos required is_customer_param" style="display: none;">{l s='You must register at least one phone number.'}</p>
				{/if}								
				<div class="form-group is_customer_param" style="display: none;">
					<label for="phone">{l s='Home phone'}</label>
					<input type="text" class="text form-control validate" name="phone" id="phone"  data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone) && $guestInformations.phone}{$guestInformations.phone}{/if}" />
				</div>
				<input type="hidden" name="alias" id="alias" value="{l s='My address'}"/>

				<div class="checkbox" style="display: none;">
                	<label for="invoice_address">
					<input type="checkbox" name="invoice_address" id="invoice_address"{if (isset($smarty.post.invoice_address) && $smarty.post.invoice_address) || (isset($guestInformations) && isset($guestInformations.invoice_address) && $guestInformations.invoice_address)} checked="checked"{/if} autocomplete="off"/>
					{l s='Please use another address for invoice'}</label>
				</div>

				<div id="opc_invoice_address" class="is_customer_param">
					{assign var=stateExist value=false}
					{assign var=postCodeExist value=false}
					{assign var='dniExist' value=false}
					<h3 class="page-subheading top-indent">{l s='Invoice address'}</h3>
					{foreach from=$inv_all_fields item=field_name}
					{if $field_name eq "company" && $b2b_enable}
					<div class="form-group">
						<label for="company_invoice">{l s='Company'}</label>
						<input type="text" class="text form-control validate" id="company_invoice" name="company_invoice"  data-validate="isName" value="" />
					</div>
					{elseif $field_name eq "vat_number"}
					<div id="vat_number_block_invoice" class="is_customer_param" style="display:none;">
						<div class="form-group">
							<label for="vat_number_invoice">{l s='VAT number'}</label>
							<input type="text" class="form-control" id="vat_number_invoice" name="vat_number_invoice" value="" />
						</div>
					</div>
					{elseif $field_name eq "dni"}
					{assign var='dniExist' value=true}
					<div class="required form-group dni_invoice">
						<label for="dni">{l s='Identification number'} <sup>*</sup></label>
						<input type="text" class="text form-control validate" name="dni_invoice" id="dni_invoice" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni_invoice) && $guestInformations.dni_invoice}{$guestInformations.dni_invoice}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					</div>
					{elseif $field_name eq "firstname"}
					<div class="required form-group">
						<label for="firstname_invoice">{l s='First name'} <sup>*</sup></label>
						<input type="text" class="form-control validate" id="firstname_invoice" name="firstname_invoice" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.firstname_invoice) && $guestInformations.firstname_invoice}{$guestInformations.firstname_invoice}{/if}" />
					</div>
					{elseif $field_name eq "lastname"}
					<div class="required form-group">
						<label for="lastname_invoice">{l s='Last name'} <sup>*</sup></label>
						<input type="text" class="form-control validate" id="lastname_invoice" name="lastname_invoice" data-validate="isName" value="{if isset($guestInformations) && isset($guestInformations.lastname_invoice) && $guestInformations.lastname_invoice}{$guestInformations.lastname_invoice}{/if}" />
					</div>
					{elseif $field_name eq "address1"}
					<div class="required form-group">
						<label for="address1_invoice">{l s='Address'} <sup>*</sup></label>
						<input type="text" class="form-control validate" name="address1_invoice" id="address1_invoice" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address1_invoice) && isset($guestInformations) && isset($guestInformations.address1_invoice) && $guestInformations.address1_invoice}{$guestInformations.address1_invoice}{/if}" />
					</div>
					{elseif $field_name eq "address2"}
					<div class="form-group is_customer_param">
						<label for="address2_invoice">{l s='Address (Line 2)'}</label>
						<input type="text" class="form-control address" name="address2_invoice" id="address2_invoice" data-validate="isAddress" value="{if isset($guestInformations) && isset($guestInformations.address2_invoice) && isset($guestInformations) && isset($guestInformations.address2_invoice) && $guestInformations.address2_invoice}{$guestInformations.address2_invoice}{/if}" />
					</div>
					{elseif $field_name eq "postcode"}
					{$postCodeExist = true}
					<div class="required postcode_invoice form-group">
						<label for="postcode_invoice">{l s='Zip/Postal Code'} <sup>*</sup></label>
						<input type="text" class="form-control validate" name="postcode_invoice" id="postcode_invoice" data-validate="isPostCode" value="{if isset($guestInformations) && isset($guestInformations.postcode_invoice) && $guestInformations.postcode_invoice}{$guestInformations.postcode_invoice}{/if}" onkeyup="$('#postcode_invoice').val($('#postcode_invoice').val().toUpperCase());" />
					</div>
					{elseif $field_name eq "city"}
					<div class="required form-group">
						<label for="city_invoice">{l s='City'} <sup>*</sup></label>
						<input type="text" class="form-control validate" name="city_invoice" id="city_invoice" data-validate="isCityName" value="{if isset($guestInformations) && isset($guestInformations.city_invoice) && $guestInformations.city_invoice}{$guestInformations.city_invoice}{/if}" />
					</div>
					{elseif $field_name eq "country" || $field_name eq "Country:name"}
					<div class="required form-group">
						<label for="id_country_invoice">{l s='Country'} <sup>*</sup></label>
						<select name="id_country_invoice" id="id_country_invoice" class="form-control">
							<option value="">-</option>
							{foreach from=$countries item=v}
							<option value="{$v.id_country}"{if (isset($guestInformations) && isset($guestInformations.id_country_invoice) && $guestInformations.id_country_invoice == $v.id_country) || (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
							{/foreach}
						</select>
					</div>
					{elseif $field_name eq "state" || $field_name eq 'State:name'}
					{$stateExist = true}
					<div class="required id_state_invoice form-group" style="display:none;">
						<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
						<select name="id_state_invoice" id="id_state_invoice" class="form-control">
							<option value="">-</option>
						</select>
					</div>
					{/if}
					{/foreach}
					{if !$postCodeExist}
					<div class="required postcode_invoice form-group unvisible">
						<label for="postcode_invoice">{l s='Zip/Postal Code'} <sup>*</sup></label>
						<input type="text" class="form-control validate" name="postcode_invoice" id="postcode_invoice" data-validate="isPostCode" value="" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</div>
					{/if}					
					{if !$stateExist}
					<div class="required id_state_invoice form-group unvisible">
						<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
						<select name="id_state_invoice" id="id_state_invoice" class="form-control">
							<option value="">-</option>
						</select>
					</div>
					{/if}
					{if !$dniExist}
					<div class="required form-group dni_invoice">
						<label for="dni">{l s='Identification number'} <sup>*</sup></label>
						<input type="text" class="text form-control validate" name="dni_invoice" id="dni_invoice" data-validate="isDniLite" value="{if isset($guestInformations) && isset($guestInformations.dni_invoice) && $guestInformations.dni_invoice}{$guestInformations.dni_invoice}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					</div>
					{/if}
					<div class="form-group is_customer_param">
						<label for="other_invoice">{l s='Additional information'}</label>
						<textarea class="form-control" name="other_invoice" id="other_invoice" cols="26" rows="3"></textarea>
					</div>
					{if isset($one_phone_at_least) && $one_phone_at_least}
						<p class="inline-infos required is_customer_param">{l s='You must register at least one phone number.'}</p>
					{/if}					
					<div class="form-group is_customer_param">
						<label for="phone_invoice">{l s='Home phone'}</label>
						<input type="text" class="form-control validate" name="phone_invoice" id="phone_invoice" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_invoice) && $guestInformations.phone_invoice}{$guestInformations.phone_invoice}{/if}" />
					</div>
					<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
						<label for="phone_mobile_invoice">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
						<input type="text" class="form-control validate" name="phone_mobile_invoice" id="phone_mobile_invoice" data-validate="isPhoneNumber" value="{if isset($guestInformations) && isset($guestInformations.phone_mobile_invoice) && $guestInformations.phone_mobile_invoice}{$guestInformations.phone_mobile_invoice}{/if}" />
					</div>
					<input type="hidden" name="alias_invoice" id="alias_invoice" value="{l s='My Invoice address'}" />
				</div>
				{$HOOK_CREATE_ACCOUNT_FORM}
				<div class="submit opc-add-save clearfix">
                		<p class="required opc-required pull-right">
                            <sup>*</sup>{l s='Required field'}
                        </p>
                    <button type="submit" name="submitAccount" id="submitAccount" class="btn btn-default button button-medium"><span>{l s='Save'} <i class="icon-long-arrow-right right"></i></span></button>
                    
				</div>
				<div style="display: none;" id="opc_account_saved" class="alert alert-success">
					Контактные данные успешно сохранены!
				</div>
				<!-- END Account -->
			</div>
            </div>
		</fieldset>
	</form>
</div>
{strip}
{if isset($guestInformations) && isset($guestInformations.id_state) && $guestInformations.id_state}
	{addJsDef idSelectedState=$guestInformations.id_state|intval}
{else}
	{addJsDef idSelectedState=false}
{/if}
{if isset($guestInformations) && isset($guestInformations.id_state_invoice) && $guestInformations.id_state_invoice}
	{addJsDef idSelectedStateInvoice=$guestInformations.id_state_invoice|intval}
{else}
	{addJsDef idSelectedStateInvoice=false}
{/if}
{if isset($guestInformations) && isset($guestInformations.id_country) && $guestInformations.id_country}
	{addJsDef idSelectedCountry=$guestInformations.id_country|intval}
{else}
	{addJsDef idSelectedCountry=false}
{/if}
{if isset($guestInformations) && isset($guestInformations.id_country_invoice) && $guestInformations.id_country_invoice}
	{addJsDef idSelectedCountryInvoice=$guestInformations.id_country_invoice|intval}
{else}
	{addJsDef idSelectedCountryInvoice=false}
{/if}
{if isset($countries)}
	{addJsDef countries=$countries}
{/if}
{if isset($vatnumber_ajax_call) && $vatnumber_ajax_call}
	{addJsDef vatnumber_ajax_call=$vatnumber_ajax_call}
{/if}
{/strip}