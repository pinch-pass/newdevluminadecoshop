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

{assign var='rc_page' value=false}
{if $register_customer and isset($smarty.get.rc_page)}
    {assign var='rc_page' value=$smarty.get.rc_page}
{/if}
<input type="hidden" id="rc_page" name="rc_page" value="{$rc_page|escape:'html':'UTF-8'}" />

{if !$register_customer or !$rc_page or $rc_page eq 'customer'}
    {if isset($OPC_FIELDS[$OPC_GLOBALS->object->customer])}
        <h5 class="onepagecheckoutps_p_step onepagecheckoutps_p_step_one">
            <i class="fa-pts fa-pts-user fa-pts-3x"></i>
            {l s='Your data' mod='onepagecheckoutps'}
            {if !$isLogged or $isGuest}
                <button type="button" id="opc_show_login" class="btn btn-primary btn-xs pull-right" >
                    {l s='Already registered?' mod='onepagecheckoutps'}
                </button>
            {/if}
        </h5>

        {* Support module: sociallogin *}
        {if isset($social_networks) and !$isLogged}
            <section id="module_sociallogin">
                {$i = 1}
                {foreach from=$social_networks item=item key=k}
                    {if $item.complete_config}
                        <button type="button" class="btn btn-social{if $button}-icon{/if} {if $size != 'st'}btn-{$size|escape:'html':'UTF-8'}{/if} btn-{$item.icon_class|escape:'html':'UTF-8'}" onclick="window.open('{$item.connect|escape:'html':'UTF-8'}', {if $popup}'_blank'{else}'_self'{/if}, 'menubar=no, status=no, copyhistory=no, width=640, height=640, top=220, left=640')">
                            <i class="fa-pts fa-pts-{$item.fa_icon|escape:'html':'UTF-8'}"></i>
                            {if !$button}
                                {if $sign_in}{l s='Sign in with' mod='onepagecheckoutps'}{/if}
                                {$item.name|escape:'html':'UTF-8'|capitalize}
                            {/if}
                        </button>
                        {$i = $i + 1}
                    {/if}
                {/foreach}
            </section>
        {/if}

        {if !$isLogged && $opc_social_networks && ($opc_social_networks->facebook->enable || $opc_social_networks->google->enable || $opc_social_networks->paypal->enable || $opc_social_networks->biocryptology->enable)}
            <section id="opc_social_networks">
                <h5>{l s='Login using your social networks' mod='onepagecheckoutps'}</h5>
                {foreach from=$opc_social_networks key='name' item='network'}
                    {if $network->enable && $network->client_id neq '' && $network->client_secret neq ''}
                        <button type="button" class="btn btn-sm btn-{$name|escape:'html':'UTF-8'}" onclick="Fronted.openWindow('{$link->getModuleLink('onepagecheckoutps', 'login', ['sv' => $network->name_network])|escape:'htmlall':'UTF-8'}', true)">
                            {if $network->name_network eq 'Google'}
                                <img src="{$ONEPAGECHECKOUTPS_IMG|escape:'html':'UTF-8'}social/btn_google.png" alt="google">
                            {elseif $network->name_network eq 'Biocryptology'}
                                <img src="{$ONEPAGECHECKOUTPS_IMG|escape:'html':'UTF-8'}social/btn_biocryptology.png" alt="biocryptology">
                            {else}
                                <i class="fa-pts fa-pts-2x fa-pts-{$network->class_icon|escape:'html':'UTF-8'}"></i>
                            {/if}
                            {$network->name_network|escape:'html':'UTF-8'}
                        </button>
                    {/if}
                {/foreach}
            </section>
        {/if}

        {if isset($HOOK_CREATE_ACCOUNT_TOP) and (!$isLogged or ($isLogged and $isGuest))}
            <div id="hook_create_account_top" class="col-xs-12">
                {$HOOK_CREATE_ACCOUNT_TOP|escape:'htmlall':'UTF-8':false:true}
            </div>
        {/if}

        <section id="customer_container">
            {if isset($sveawebpay_md5)}
                <div class="form-group col-xs-12 clear clearfix">
                   <label for="sveawebpay_security_number">
                       {l s='Social security number' mod='onepagecheckoutps'}:
                   </label>
                   <input id="sveawebpay_md5" name="sveawebpay_md5" type="hidden" value="{$sveawebpay_md5|escape:'html':'UTF-8'}"/>
                   <input id="sveawebpay_security_number" name="sveawebpay_security_number" type="text" class="form-control input-sm not_unifrom not_uniform" onblur="getAddressSveawebpay()" />
                </div>
            {/if}

            <form id="form_customer" autocomplete="on">
                {foreach from=$OPC_FIELDS[$OPC_GLOBALS->object->customer] item='fields' name='f_row_fields'}
                    <div class="row">
                        {foreach from=$fields item='field' name='f_fields'}
                            {include file="./controls.tpl" field=$field cant_fields=$smarty.foreach.f_fields.total}
                        {/foreach}
                    </div>
                {/foreach}
            </form>
        </section>
    {/if}

    {if isset($HOOK_CREATE_ACCOUNT_FORM) and (!$isLogged or ($isLogged and $isGuest))}
        <div id="hook_create_account" class="col-xs-12 hidden">
            <form id="form_hook_create_account">
                {$HOOK_CREATE_ACCOUNT_FORM|escape:'htmlall':'UTF-8':false:true}
            </form>
        </div>
    {/if}

    {if isset($HOOK_CUSTOMER_IDENTITY_FORM) and $isLogged and !$isGuest}
        <div id="hook_customer_identity" class="col-xs-12">
            {$HOOK_CUSTOMER_IDENTITY_FORM|escape:'htmlall':'UTF-8':false:true}
        </div>
    {/if}

    {if $isLogged and !$isGuest and ((!$register_customer and $CONFIGS.OPC_SHOW_BUTTON_REGISTER) or $rc_page eq 'customer')}
        <div id="div_save_customer" class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="fields_required col-xs-12 clear clearfix">
                        <span>{l s='The fields with red asterisks(*) are required.' mod='onepagecheckoutps'}</span>
                    </div>
                </div>
                <button type="button" id="btn_save_customer" class="btn btn-primary pull-right btn-block">
                    <i class="fa-pts fa-pts-save fa-pts-lg"></i>
                    {l s='Save information' mod='onepagecheckoutps'}
                </button>
            </div>
        </div>
    {/if}
{/if}

{if !$register_customer or $rc_page eq 'address' or (!$isLogged and $PS_REGISTRATION_PROCESS_TYPE eq 1)}
    <div id="panel_addresses_customer" class="panel-group col-xs-12">
        {if isset($OPC_FIELDS[$OPC_GLOBALS->object->delivery]) && sizeof($OPC_FIELDS[$OPC_GLOBALS->object->delivery]) > 1 && (($CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL && $IS_VIRTUAL_CART) or !$IS_VIRTUAL_CART)}
            <div id="panel_address_delivery" class="panel panel-default">
                <div class="panel-heading hidden">
                    <h5 class="panel-title">
                        <a data-toggle="collapse" href="#delivery_address_container">
                            <i class="more-less fa-pts fa-pts-angle-up pull-right"></i>
                            {if $register_customer and $isLogged}
                                {l s='Your delivery addresses' mod='onepagecheckoutps'}
                            {else}
                                {l s='Select your delivery address' mod='onepagecheckoutps'}
                            {/if}
                        </a>
                    </h5>
                </div>
                <div id="delivery_address_container" class="panel-collapse collapse in">
                    <div class="row addresses_customer_container delivery" data-object="delivery"></div>

                    <form id="form_address_delivery" autocomplete="on" style="display: none;">
                        <div class="fields_container">
                            {foreach from=$OPC_FIELDS[$OPC_GLOBALS->object->delivery] item='fields' name='f_row_fields'}
                                <div class="row">
                                    {foreach from=$fields item='field' name='f_fields'}
                                        {include file="./controls.tpl" field=$field cant_fields=$smarty.foreach.f_fields.total}
                                    {/foreach}
                                </div>
                            {/foreach}

                            <div class="row">
                                <div class="fields_required col-xs-12 clear clearfix">
                                    <span>{l s='The fields with red asterisks(*) are required.' mod='onepagecheckoutps'}</span>
                                </div>
                            </div>

                            <div id="action_address_delivery" class="row {if !$IS_LOGGED}hidden{/if}">
                                <div class="col-sm-5 col-xs-12">
                                    {if !$IS_GUEST}
                                    <button type="reset" id="btn_cancel_address_delivery" class="btn btn-link btn-block">
                                        <i class="fa-pts fa-pts-reply"></i>
                                        {l s='Cancel' mod='onepagecheckoutps'}
                                    </button>
                                    {/if}
                                </div>
                                <div class="col-sm-7 col-xs-12">
                                    <button type="button" id="btn_update_address_delivery" class="btn btn-primary btn-block">
                                        <i class="fa-pts fa-pts-save fa-pts-lg"></i>
                                        {l s='Update' mod='onepagecheckoutps'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        {else}
            <input type="hidden" id="delivery_id" value="{$id_address_delivery|intval}"/>
        {/if}

        {*{if !$IS_LOGGED  || ($IS_GUEST && $IS_LOGGED)}
            <button type="button" class="btn btn-info" style="max-width: 100%;white-space: normal;" id="btn-copy_invoice_address_to_delivery">
                <i class="fa-pts fa-pts-copy"></i>
                {l s='Copy invoice address to the delivery address' mod='onepagecheckoutps'}
            </button>
            <br>
        {/if}*}

        {if !$register_customer or ($register_customer and !$isLogged) or ($register_customer and !$rc_page) or ($rc_page eq 'address')}
            {if isset($OPC_FIELDS[$OPC_GLOBALS->object->invoice]) && sizeof($OPC_FIELDS[$OPC_GLOBALS->object->invoice]) > 1 && $CONFIGS.OPC_ENABLE_INVOICE_ADDRESS}
                {if !$CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS}
                    <label for="checkbox_create_invoice_address" class="{if $isLogged and $register_customer and $rc_page eq 'address'}hidden{/if}">
                        <input type="checkbox" name="checkbox_create_invoice_address" id="checkbox_create_invoice_address" class="input_checkbox not_unifrom not_uniform" {if $isLogged and $register_customer and $rc_page eq 'address'}checked{/if}/>
                        {l s='I want to set another address for my invoice.' mod='onepagecheckoutps'}
                    </label>
                {/if}
                <div id="panel_address_invoice" class="panel panel-default hidden">
                    <div class="panel-heading">
                        <h5 class="panel-title">
                            <a data-toggle="collapse" href="#invoice_address_container">
                                <i class="more-less fa-pts fa-pts-angle-up pull-right"></i>
                                {if $register_customer and $isLogged}
                                    {l s='Your invoice addresses' mod='onepagecheckoutps'}
                                {else}
                                    {l s='Select your invoice address' mod='onepagecheckoutps'}
                                {/if}
                            </a>
                        </h5>
                    </div>
                    <div id="invoice_address_container" class="panel-collapse collapse">
                        <div class="row addresses_customer_container invoice" data-object="invoice"></div>

                        <form id="form_address_invoice" autocomplete="on" style="display: none;">
                            <div class="fields_container">
                                {foreach from=$OPC_FIELDS[$OPC_GLOBALS->object->invoice] item='fields' name='f_row_fields'}
                                    <div class="row">
                                        {foreach from=$fields item='field' name='f_fields'}
                                            {include file="./controls.tpl" field=$field cant_fields=$smarty.foreach.f_fields.total}
                                        {/foreach}
                                    </div>
                                {/foreach}

                                <div class="row">
                                    <div class="fields_required col-xs-12 clear clearfix">
                                        <span>{l s='The fields with red asterisks(*) are required.' mod='onepagecheckoutps'}</span>
                                    </div>
                                </div>

                                <div id="action_address_invoice" class="row {if !$IS_LOGGED}hidden{/if}">
                                    <div class="col-sm-5 col-xs-12">
                                        {if !$IS_GUEST}
                                        <button type="reset" id="btn_cancel_address_invoice" class="btn btn-link btn-block">
                                            <i class="fa-pts fa-pts-reply"></i>
                                            {l s='Cancel' mod='onepagecheckoutps'}
                                        </button>
                                        {/if}
                                    </div>
                                    <div class="col-sm-7 col-xs-12">
                                        <button type="button" id="btn_update_address_invoice" class="btn btn-primary btn-block">
                                            <i class="fa-pts fa-pts-save fa-pts-lg"></i>
                                            {l s='Update' mod='onepagecheckoutps'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            {else}
                <input type="hidden" id="invoice_id" value="{$id_address_invoice|intval}"/>
            {/if}
        {/if}
    </div>
{/if}

{if (!$IS_LOGGED || $IS_GUEST) && $CONFIGS.OPC_ENABLE_PRIVACY_POLICY}
    <div id="div_privacy_policy" class="col-xs-12">
        <p id="p_privacy_policy">
            <label for="privacy_policy">
                <input type="checkbox" class="not_unifrom not_uniform" name="privacy_policy" id="privacy_policy" value="1" {if $checkedTOS}checked="checked"{/if}/>
                {l s='I have read and accept the Privacy Policy.' mod='onepagecheckoutps'}
                <span class="read">{l s='(read)' mod='onepagecheckoutps'}</span>
            </label>
        </p>
    </div>
{/if}

{*support module: psgdpr - v1.0.0 - PrestaShop*}
{if isset($message_psgdpr)}
    <div id="gdpr_consent" class="col-xs-12">
        <label for="gdpr_consent_checkbox">
            <input type="checkbox" class="not_unifrom not_uniform" name="psgdpr_consent_checkbox" id="gdpr_consent_checkbox"/>
            {$message_psgdpr|escape:'html':'UTF-8':false:true}
        </label>
    </div>
{/if}

{if (!$IS_LOGGED && $CONFIGS.OPC_SHOW_BUTTON_REGISTER) or ($register_customer and !$rc_page)}
    <div id="div_save_customer" class="row">
        <div class="col-xs-12">
            <button type="button" id="btn_save_customer" class="btn btn-primary pull-right btn-block">
                <i class="fa-pts fa-pts-save fa-pts-lg"></i>
                {l s='Save information' mod='onepagecheckoutps'}
            </button>
        </div>
    </div>
{/if}

<div class="row">
    {include file='./custom_html/address.tpl'}
</div>