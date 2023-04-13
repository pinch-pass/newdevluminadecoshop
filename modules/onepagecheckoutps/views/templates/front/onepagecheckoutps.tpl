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

{if $is_old_browser}
    <div class="alert alert-danger warning bold">
        {l s='You are using an older browser, please try a newer version or other web browser (Google Chrome, Mozilla Firefox, Safari, etc) to proceed with the purchase, thanks.' mod='onepagecheckoutps'}
    </div>
{else}
    {if (isset($no_products) && $no_products > 0) or $register_customer}
        {if !$register_customer}
            <style>
            {literal}
                #order-opc #left_column,
                #order-opc #right_column{
                    display: none !important;
                }
            {/literal}
            </style>
        {/if}
        <script type="text/javascript">
            var pts_static_token = '{$token|escape:'htmlall':'UTF-8'}';

            var orderOpcUrl = "{$link->getPageLink('order-opc', true)|escape:'htmlall':'UTF-8':false:true}";
            var myaccountUrl = "{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8':false:true}";
            var orderProcess = 0;
            var cod_id_module_payment = {if isset($cod_id_module_payment)}{$cod_id_module_payment|intval}{else}0{/if};
            var bnkplus_id_module_payment = {if isset($bnkplus_id_module_payment)}{$bnkplus_id_module_payment|intval}{else}0{/if};
            var paypal_id_module_payment = {if isset($paypal_id_module_payment)}{$paypal_id_module_payment|intval}{else}0{/if};
            var sequra_id_module_payment = {if isset($sequra_id_module_payment)}{$sequra_id_module_payment|intval}{else}0{/if};
            var payments_without_popup = '{$CONFIGS.OPC_MODULES_WITHOUT_POPUP|escape:'htmlall':'UTF-8'}';
            var attributewizardpro = {if isset($attributewizardpro)}true{else}false{/if};
            var payment_modules_fee = {$payment_modules_fee|escape:'quotes':'UTF-8'};
            var have_ship_to_pay = {$have_ship_to_pay|intval};
            var message_psgdpr = {if isset($message_psgdpr)}true{else}false{/if};
            var paypal_ec_canceled = {if isset($paramsFront.paypal_ec_canceled)}true{else}false{/if};
            var OnePageCheckoutPS = {ldelim}
                REGISTER_CUSTOMER : {if $register_customer}true{else}false{/if},
                CONFIGS : {$CONFIGS_JS|escape:'quotes':'UTF-8'},
                ONEPAGECHECKOUTPS_DIR: '{$ONEPAGECHECKOUTPS_DIR|escape:'htmlall':'UTF-8'}',
                ONEPAGECHECKOUTPS_IMG: '{$ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}',
                ENABLE_INVOICE_ADDRESS: Boolean({$CONFIGS.OPC_ENABLE_INVOICE_ADDRESS|intval}),
                REQUIRED_INVOICE_ADDRESS: Boolean({$CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS|intval}),
                ENABLE_TERMS_CONDITIONS: Boolean({$CONFIGS.OPC_ENABLE_TERMS_CONDITIONS|intval}),
                ENABLE_PRIVACY_POLICY: Boolean({$CONFIGS.OPC_ENABLE_PRIVACY_POLICY|intval}),
                SHOW_DELIVERY_VIRTUAL: Boolean({$CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL|intval}),
                USE_SAME_NAME_CONTACT_DA: Boolean({$CONFIGS.OPC_USE_SAME_NAME_CONTACT_DA|intval}),
                USE_SAME_NAME_CONTACT_BA: Boolean({$CONFIGS.OPC_USE_SAME_NAME_CONTACT_BA|intval}),
                OPC_SHOW_POPUP_PAYMENT: Boolean({$CONFIGS.OPC_SHOW_POPUP_PAYMENT|intval}),
                PAYMENTS_WITHOUT_RADIO: Boolean({$CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO|intval}),
                IS_VIRTUAL_CART: Boolean({$IS_VIRTUAL_CART|intval}),
                IS_LOGGED: Boolean({$IS_LOGGED|intval}),
                IS_GUEST: Boolean({$IS_GUEST|intval}),
                id_address_delivery: {$id_address_delivery|intval},
                id_address_invoice: {$id_address_invoice|intval},
                id_shop: {$id_shop|intval},
                date_format_language: '{$date_format_language|escape:'htmlall':'UTF-8'}',
				id_country_delivery_default: {$id_country_delivery_default|intval},
				id_country_invoice_default: {$id_country_invoice_default|intval},
				iso_code_country_delivery_default: '{$iso_code_country_delivery_default|escape:'htmlall':'UTF-8'}',
				iso_code_country_invoice_default: '{$iso_code_country_invoice_default|escape:'htmlall':'UTF-8'}',
                PS_GUEST_CHECKOUT_ENABLED: Boolean({$PS_GUEST_CHECKOUT_ENABLED|intval}),
                LANG_ISO: '{$lang_iso|escape:'htmlall':'UTF-8'}',
                LANG_ISO_ALLOW : ['es', 'en', 'ca', 'br', 'eu', 'pt', 'eu', 'mx'],
                IS_NEED_INVOICE : Boolean({$is_need_invoice|intval}),
                GUEST_TRACKING_URL : '{$link->getPageLink("guest-tracking", true)|escape:'htmlall':'UTF-8'}',
                HISTORY_URL : '{$link->getPageLink("history", true)|escape:'htmlall':'UTF-8'}',
                IS_RTL : Boolean({$is_rtl|intval}),
                PS_TAX_ADDRESS_TYPE : '{$PS_TAX_ADDRESS_TYPE|escape:'htmlall':'UTF-8'}',
                url_controller_carrier: '{$link->getModuleLink("onepagecheckoutps", "carrier", [content_only => 1])|escape:'htmlall':'UTF-8'}',
                Msg: {ldelim}
                    there_are: "{l s='There are' mod='onepagecheckoutps' js=1}",
                    there_is: "{l s='There is' mod='onepagecheckoutps' js=1}",
                    error: "{l s='Error' mod='onepagecheckoutps' js=1}",
                    errors: "{l s='Errors' mod='onepagecheckoutps' js=1}",
                    field_required: "{l s='Required' mod='onepagecheckoutps' js=1}",
                    dialog_title: "{l s='Confirm Order' mod='onepagecheckoutps' js=1}",
                    no_payment_modules: "{l s='There are no payment methods available.' mod='onepagecheckoutps' js=1}",
                    validating: "{l s='Validating, please wait' mod='onepagecheckoutps' js=1}",
                    error_zipcode: "{l s='The Zip / Postal code is invalid' mod='onepagecheckoutps' js=1}",
                    error_registered_email: "{l s='An account is already registered with this e-mail' mod='onepagecheckoutps' js=1}",
                    error_registered_email_guest: "{l s='This email is already registered, you can login or fill form again.' mod='onepagecheckoutps' js=1}",
                    delivery_billing_not_equal: "{l s='Delivery address alias cannot be the same as billing address alias' mod='onepagecheckoutps' js=1}",
                    errors_trying_process_order: "{l s='The following error occurred while trying to process the order' mod='onepagecheckoutps' js=1}",
                    agree_terms_and_conditions: "{l s='You must agree to the terms of service before continuing.' mod='onepagecheckoutps' js=1}",
                    agree_privacy_policy: "{l s='You must agree to the privacy policy before continuing.' mod='onepagecheckoutps' js=1}",
                    fields_required_to_process_order: "{l s='You must complete the required information to process your order.' mod='onepagecheckoutps' js=1}",
                    check_fields_highlighted: "{l s='Check the fields that are highlighted and marked with an asterisk.' mod='onepagecheckoutps' js=1}",
                    error_number_format: "{l s='The format of the number entered is not valid.' mod='onepagecheckoutps' js=1}",
                    oops_failed: "{l s='Oops! Failed' mod='onepagecheckoutps' js=1}",
                    continue_with_step_3: "{l s='Continue with step 3.' mod='onepagecheckoutps' js=1}",
                    email_required: "{l s='Email address is required.' mod='onepagecheckoutps' js=1}",
                    email_invalid: "{l s='Invalid e-mail address.' mod='onepagecheckoutps' js=1}",
                    password_required: "{l s='Password is required.' mod='onepagecheckoutps' js=1}",
                    password_too_long: "{l s='Password is too long.' mod='onepagecheckoutps' js=1}",
                    password_invalid: "{l s='Invalid password.' mod='onepagecheckoutps' js=1}",
                    addresses_same: "{l s='You must select a different address for shipping and billing.' mod='onepagecheckoutps' js=1}",
                    create_new_address: "{l s='Are you sure you wish to add a new delivery address? You can use the current address and modify the information.' mod='onepagecheckoutps' js=1}",
                    cart_empty: "{l s='Your shopping cart is empty. You need to refresh the page to continue.' mod='onepagecheckoutps' js=1}",
                    dni_spain_invalid: "{l s='DNI/CIF/NIF is invalid.' mod='onepagecheckoutps' js=1}",
                    payment_method_required: "{l s='Please select a payment method to proceed.' mod='onepagecheckoutps' js=1}",
                    shipping_method_required: "{l s='Please select a shipping method to proceed.' mod='onepagecheckoutps' js=1}",
                    select_pickup_point: "{l s='To select a pick up point is necessary to complete your information and delivery address in the first step.' mod='onepagecheckoutps' js=1}",
                    need_select_pickup_point: "{l s='You need to select on shipping a pickup point to continue with the purchase.' mod='onepagecheckoutps' js=1}",
                    select_date_shipping: "{l s='Please select a date for shipping.' mod='onepagecheckoutps' js=1}",
                    confirm_payment_method: "{l s='Confirmation payment' mod='onepagecheckoutps' js=1}",
                    to_determinate: "{l s='To determinate' mod='onepagecheckoutps' js=1}",
                    login_customer: "{l s='Login' mod='onepagecheckoutps' js=1}",
                    processing_purchase: "{l s='Processing purchase' mod='onepagecheckoutps' js=1}",
                    validate_address: "{l s='Validate your address' mod='onepagecheckoutps' js=1}",
                    message_validate_address: "{l s='Your order will ship to: %address%. Is the address OK?' mod='onepagecheckoutps' js=1}",
                    close: "{l s='Close' mod='onepagecheckoutps' js=1}",
                    no_remove_address_delivery: "{l s='It is not possible to delete this address because it is being used as a invoice address.' mod='onepagecheckoutps' js=1}",
                    no_remove_address_invoice: "{l s='It is not possible to delete this address because it is being used as a delivery address.' mod='onepagecheckoutps' js=1}",
                    finalize_address_update: "{l s='You need to finish adding or editing your address to complete the purchase.' mod='onepagecheckoutps' js=1}",
                    need_add_delivery_address: "{l s='It is necessary to add a delivery address in order to finalize the purchase.' mod='onepagecheckoutps' js=1}",
                    select_delivery_address: "{l s='Please select a delivery address.' mod='onepagecheckoutps' js=1}",
                    select_invoice_address: "{l s='Please select a invoice address.' mod='onepagecheckoutps' js=1}",
                    confirm_remove_address: "{l s='Are you sure you want to delete this address?' mod='onepagecheckoutps' js=1}",
                    change_carrier_embed: "{l s='Change shipping carrier' mod='onepagecheckoutps' js=1}",
                    choose_carrier_embed: "{l s='Choose shipping carrier' mod='onepagecheckoutps' js=1}"
                {rdelim}
            {rdelim};
            var messageValidate = {ldelim}
                errorGlobal         : "{l s='This is not a valid.' mod='onepagecheckoutps' js=1}",
                errorIsName         : "{l s='This is not a valid name.' mod='onepagecheckoutps' js=1}",
                errorIsEmail        : "{l s='This is not a valid email address.' mod='onepagecheckoutps' js=1}",
                errorIsPostCode     : "{l s='This is not a valid post code.' mod='onepagecheckoutps' js=1}",
                errorIsAddress      : "{l s='This is not a valid address.' mod='onepagecheckoutps' js=1}",
                errorIsCityName     : "{l s='This is not a valid city.' mod='onepagecheckoutps' js=1}",
                isMessage           : "{l s='This is not a valid message.' mod='onepagecheckoutps' js=1}",
                errorIsDniLite      : "{l s='This is not a valid document identifier.' mod='onepagecheckoutps' js=1}",
                errorIsPhoneNumber  : "{l s='This is not a valid phone.' mod='onepagecheckoutps' js=1}",
                errorIsPasswd       : "{l s='This is not a valid password. Minimum 5 characters.' mod='onepagecheckoutps' js=1}",
                errorisBirthDate    : "{l s='This is not a valid birthdate.' mod='onepagecheckoutps' js=1}",
                errorisDate			: "{l s='This is not a valid date.' mod='onepagecheckoutps' js=1}",
                badUrl              : "{l s='This is not a valid url.' mod='onepagecheckoutps' js=1} ex: http://www.domain.com",
                badInt              : "{l s='This is not a valid.' mod='onepagecheckoutps' js=1}",
                notConfirmed        : "{l s='The values do not match.' mod='onepagecheckoutps' js=1}",
                lengthTooLongStart  : "{l s='It is only possible enter' mod='onepagecheckoutps' js=1} ",
                lengthTooShortStart : "{l s='The input value is shorter than ' mod='onepagecheckoutps' js=1} ",
                lengthBadEnd        : " {l s='characters.' mod='onepagecheckoutps' js=1}",
                requiredField       : " {l s='This is a required field.' mod='onepagecheckoutps' js=1}"
            {rdelim};

            var countriesJS = {$countries|escape:'quotes':'UTF-8'};
            var countriesNeedIDNumber = {$countriesNeedIDNumber|escape:'quotes':'UTF-8'};
            var countriesNeedZipCode = {$countriesNeedZipCode|escape:'quotes':'UTF-8'};
            var countriesIsoCode = {$countriesIsoCode|escape:'quotes':'UTF-8'};
        </script>

        <div id="onepagecheckoutps" class="pts bootstrap {if $register_customer}rc{/if}">
            <input type="hidden" id="logged" value="{$logged|intval}" />

            <button id="btn_other_payments" type="button" class="btn btn-md btn-default hidden" onclick="location.reload()">
                <i class="fa-pts fa-pts-exchange"></i>
                {l s='Choose another payment method' mod='onepagecheckoutps'}
            </button>

            <div class="loading_big">
                <div class="loader">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>

            {hook h='emailVerificationOPC'}

            <div class="row">
                {if $CONFIGS.OPC_SHIPPING_COMPATIBILITY}
                    <div id="opc_payment_methods" style="display: none;">
                        {include file="$tpl_dir./order-carrier.tpl"}
                        <div class="row">
                            <div class="col-xs-12">
                                <button type="button" name="processCarrier" id="hide_carrier_embed" class="btn btn-primary">
                                    {l s='Continue' mod='onepagecheckoutps'}
                                    <i class="fa-pts fa-pts-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                {/if}

                {* {if !$register_customer}
                    <div id="onepagecheckoutps_header" class="col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div id="div_onepagecheckoutps_info" class="{if $IS_LOGGED}col-md-7{/if} col-sm-12 col-xs-12">
                                <h2>{l s='Quick Checkout' mod='onepagecheckoutps'}</h2>
                                <h4>{l s='Complete the following fields to process your order.' mod='onepagecheckoutps'}</h4>
                            </div><!--
                            -->{if $IS_LOGGED and !$IS_GUEST}<div id="div_onepagecheckoutps_login" class="col-md-5 col-sm-12 col-xs-12">
                                <div class="row end-md text-right">
									<p>
										<i class="fa-pts fa-pts-lock fa-pts-1x"></i>
										{l s='Welcome' mod='onepagecheckoutps'},&nbsp;
										<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">
											<b>{$customer_info->firstname|escape:'htmlall':'UTF-8'} {$customer_info->lastname|escape:'htmlall':'UTF-8'}</b>
										</a>
										<a id="btn_logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'htmlall':'UTF-8'}" title="{l s='Log me out' mod='onepagecheckoutps'}" class="btn btn-default btn-xs">
											<i class="fa-pts fa-pts-sign-out fa-pts-1x"></i>
											{l s='Log out' mod='onepagecheckoutps'}
										</a>
									</p>
                                </div>
                            </div>{/if}
                        </div>
                    </div>
                {/if} *}

                <div class="row">
                    {include file='./custom_html/header.tpl'}
                </div>

                {if isset($CONTROLLER_ERRORS)}
                    <div class="alert alert-danger">
                        <ul>
                        {foreach from=$CONTROLLER_ERRORS item=error}
                            <li>{$error|escape:"htmlall"}</li>
                        {/foreach}
                        </ul>
                    </div>
                {/if}
                <div id="onepagecheckoutps_contenedor" class="col-md-12 col-sm-12 col-xs-12">
                    <div id="onepagecheckoutps_forms" class="hidden"></div>
                    <div id="opc_temporal" class="hidden"></div>
                    <div id="opc_temporal_popup" class="hidden"></div>

                    <div id="opc_container">
                        {if !$IS_LOGGED or $IS_GUEST}
                            <div id="opc_login" class="hidden" title="{l s='Login' mod='onepagecheckoutps'}">
                                <div class="login-box">
                                    {if !$isLogged && $opc_social_networks}
                                        <section id="opc_social_networks">
                                            {foreach from=$opc_social_networks key='name' item='network'}
                                                {if $network->client_id neq '' && $network->client_secret neq '' && $network->enable}
                                                    <button type="button" class="btn btn-sm btn-{$name|escape:'html':'UTF-8'}" onclick="Fronted.openWindow('{$link->getModuleLink('onepagecheckoutps', 'login', ['sv' => $network->network])|escape:'htmlall':'UTF-8'}', true)">
                                                        {if $network->name_network eq 'Google'}
                                                            <img src="{$ONEPAGECHECKOUTPS_IMG|escape:'html':'UTF-8'}social/btn_google.png" alt="google">
                                                        {elseif $network->name_network eq 'Biocryptology'}
                                                            <img src="{$ONEPAGECHECKOUTPS_IMG|escape:'html':'UTF-8'}social/btn_biocryptology.png" alt="biocryptology">
                                                        {else}
                                                            <i class="fa-pts fa-pts-1x fa-pts-{$network->class_icon|escape:'html':'UTF-8'}"></i>
                                                        {/if}
                                                        {$network->name_network|escape:'html':'UTF-8'}
                                                    </button>
                                                {/if}
                                            {/foreach}
                                        </section>
                                        <br/>
                                    {/if}
                                    <form id="form_login" autocomplete="off">
                                        <div class="form-group input-group margin-bottom-sm">
                                            <span class="input-group-addon"><i class="fa-pts fa-pts-envelope-o fa-pts-fw"></i></span>
                                            <input id="txt_login_email" class="form-control" type="text" placeholder="{l s='E-mail' mod='onepagecheckoutps'}" data-validation="isEmail" />
                                        </div>
                                        <div class="form-group input-group margin-bottom-sm">
                                            <span class="input-group-addon"><i class="fa-pts fa-pts-key fa-pts-fw"></i></span>
                                            <input id="txt_login_password" class="form-control" type="password" placeholder="{l s='Password' mod='onepagecheckoutps'}" data-validation="length" data-validation-length="min5" />
                                        </div>

                                        <div class="alert alert-warning hidden"></div>

                                        <button type="button" id="btn_login" class="btn btn-primary btn-block">
                                            <i class="fa-pts fa-pts-lock fa-pts-lg"></i>
                                            {l s='Login' mod='onepagecheckoutps'}
                                        </button>

                                        <p class="forget_password">
                                            <a href="{$link->getPageLink('password')|escape:'htmlall':'UTF-8'}">{l s='Forgot your password?' mod='onepagecheckoutps'}</a>
                                        </p>
                                    </form>
                                </div>
                            </div>
                        {/if}
                        {foreach from=$position_steps item=column}
                            <div class="{$column.classes|escape:'htmlall':'UTF-8'} nopadding">
                                <div class="row">
                                    {foreach from=$column.rows item=row}
                                        {include file='./steps/'|cat:$row.name_step|cat:'.tpl' classes=$row.classes configs=$CONFIGS}
                                    {/foreach}
                                </div>
                            </div>
                        {/foreach}
                        <div class="col-xs-12 clear clearfix">
                            {if $CONFIGS.OPC_ENABLE_HOOK_SHOPPING_CART && !$CONFIGS.OPC_COMPATIBILITY_REVIEW && $CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO}
                                <div id="HOOK_SHOPPING_CART" class="row">{$HOOK_SHOPPING_CART|escape:'html':'UTF-8':false:true}</div>
                                <p class="cart_navigation_extra row">
                                    <span id="HOOK_SHOPPING_CART_EXTRA">{$HOOK_SHOPPING_CART_EXTRA|escape:'html':'UTF-8':false:true}</span>
                                </p>
                            {/if}
                        </div>
                    </div>
                </div>

                <div class="row">
                    {include file='./custom_html/footer.tpl'}
                </div>

                <div class="clear clearfix"></div>
            </div>
        </div>
    {else}
        {include file="$tpl_dir./shopping-cart.tpl" empty=""}
    {/if}

    {if isset($show_account_buttons) && $show_account_buttons}
        <ul class="footer_links clearfix">
            <li>
                <a class="btn btn-sm btn-primary button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
                    <span>
                        <i class="icon-chevron-left"></i> {l s='Back to Your Account' mod='onepagecheckoutps'}
                    </span>
                </a>
            </li>
        </ul>
    {/if}
{/if}