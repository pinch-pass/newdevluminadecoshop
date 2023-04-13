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

{if isset($css_files)}
    {foreach from=$css_files key=css_uri item=media}
        <link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
    {/foreach}
{/if}
{if isset($js_files)}
    {foreach from=$js_files item=js_uri}
        <script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
    {/foreach}
{/if}

<script type="text/javascript">
    var txtProduct = '{l s='product' mod='onepagecheckoutps' js=1}';
    var txtProducts = '{l s='products' mod='onepagecheckoutps' js=1}';
    var orderUrl = '{$link->getPageLink('order', true)|escape:'htmlall':'UTF-8'}';
    var is_necessary_postcode = Boolean({if isset($is_necessary_postcode)}{$is_necessary_postcode|intval}{/if});
    var is_necessary_city = Boolean({if isset($is_necessary_city)}{$is_necessary_city|intval}{/if});
    var id_carrier_selected = '{if isset($id_carrier_selected)}{$id_carrier_selected|escape:'htmlall':'UTF-8'}{/if}';
    var module_carrier_selected = '{if isset($module_carrier_selected)}{$module_carrier_selected|escape:'htmlall':'UTF-8'}{/if}';

    var nacex_agcli = '{if isset($nacex_agcli)}{$nacex_agcli|escape:'htmlall':'UTF-8'}{/if}';

    {literal}
        if (!OnePageCheckoutPS.IS_LOGGED && is_necessary_postcode)
            $('div#onepagecheckoutps')
                .off('blur', 'input#delivery_postcode')
                .on('blur', 'input#delivery_postcode', function() {
                    Address.updateAddress({object: 'delivery', load_carriers: true})
                });
        if (!OnePageCheckoutPS.IS_LOGGED && is_necessary_city)
            $('div#onepagecheckoutps')
                .off('blur', 'input#delivery_city')
                .on('blur', 'input#delivery_city', function() {
                    Address.updateAddress({object: 'delivery', load_carriers: true})
                });
    {/literal}
</script>

{if isset($IS_VIRTUAL_CART) && $IS_VIRTUAL_CART}
    <input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
{else}
    <div id="carrier_area">
    <div id="shipping_container">
        {if ($hasError)}
            <p class="alert alert-warning">
                {foreach from=$errors key=k item="error" name="f_errors"}
                    -&nbsp;{$error|escape:'htmlall':'UTF-8'}
                    {if !$smarty.foreach.f_errors.last}<br/><br/>{/if}
                {/foreach}
            </p>
			<button class="btn btn-default pull-right btn-sm" type="button" onclick="Address.updateAddress({ldelim}object: 'delivery', update_cart: true, load_carriers: true{rdelim});">
				<i class="fa-pts fa-pts-refresh"></i>
				{l s='Reload' mod='onepagecheckoutps'}
			</button>
            <div class="clear"></div>
        {else}
            <div class="delivery_options_address delivery_options">
                {if isset($delivery_option_list)}
                    {foreach $cart->getDeliveryAddressesWithoutCarriers(true) as $address}
                        <p class="alert alert-warning" id="noCarrierWarning">
                            {if empty($address->alias)}
                                {l s='No carriers available.' mod='onepagecheckoutps'}
                            {else}
                                {l s='No carriers available for this address.' mod='onepagecheckoutps'}
                            {/if}
                        </p>
                    {foreachelse}
                        {foreach $delivery_option_list as $id_address => $option_list}
                            {*if isset($address_collection[$id_address])}
                                <p class="carrier_title">
                                    {l s='Choose a shipping option for this address:' mod='onepagecheckoutps'} {$address_collection[$id_address]->alias}
                                </p>
                            {/if*}
                            {foreach $option_list as $key => $option}
                            {if $key neq '0,'}
                                {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}
                                    {assign var='is_carrier_selected' value=true}
                                {else}
                                    {assign var='is_carrier_selected' value=false}
                                {/if}

                                {if $CONFIGS.OPC_SHIPPING_COMPATIBILITY and !$is_carrier_selected}
                                    {continue}
                                {/if}

                                <div class="delivery_option {if $is_carrier_selected}selected alert alert-info{/if}">
                                    <div class="row pts-vcenter col-xs-12 nopadding">
                                        <div class="col-xs-1">
                                            <input class="delivery_option_radio not_unifrom not_uniform {if $CONFIGS.OPC_SHIPPING_COMPATIBILITY}hidden{/if}" type="radio" name="delivery_option[{$id_address|intval}]" id="delivery_option_{$id_address|intval}_{$option@index|intval}" value="{$key|escape:'htmlall':'UTF-8'}" {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}checked="checked"{/if} />
                                        </div><!--
                                        --><div class="delivery_option_logo {if !$CONFIGS.OPC_SHOW_IMAGE_CARRIER && !$CONFIGS.OPC_SHOW_DESCRIPTION_CARRIER}col-xs-8{else} col-xs-2{/if}">
                                            {foreach $option.carrier_list as $carrier}
                                                {if ($CONFIGS.OPC_SHOW_IMAGE_CARRIER)}
                                                    {if $carrier.logo}
                                                        <img src="{$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.instance->name|escape:'htmlall':'UTF-8'}" class="img-thumbnail"/>
                                                    {else}
                                                        <img src="{$ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}shipping.png" class="img-thumbnail"/>
                                                    {/if}
                                                {else}
                                                    <div class="delivery_option_title">{$carrier.instance->name|escape:'htmlall':'UTF-8'}</div>
                                                    {if !$carrier@last} - {/if}
                                                {/if}

                                                {if $carrier.instance->external_module_name != ''}
                                                    <input type="hidden" class="module_carrier" name="{$carrier.instance->external_module_name|escape:'htmlall':'UTF-8'}" value="delivery_option_{$id_address|intval}_{$option@index|intval}" />
                                                    <input type="hidden" name="name_carrier" id="name_carrier_{$id_address|intval}_{$option@index|intval}" value="{$carrier.instance->name|escape:'htmlall':'UTF-8'}" />
                                                {/if}
                                            {/foreach}
                                        </div><!--
                                        {if $CONFIGS.OPC_SHOW_IMAGE_CARRIER || $CONFIGS.OPC_SHOW_DESCRIPTION_CARRIER}
                                        --><div class="carrier_delay col-xs-5">
                                            {foreach $option.carrier_list as $carrier}
                                                {if $CONFIGS.OPC_SHOW_IMAGE_CARRIER}
                                                    <div class="delivery_option_title">{$carrier.instance->name|escape:'htmlall':'UTF-8'}</div>
                                                {/if}
                                                {if $CONFIGS.OPC_SHOW_DESCRIPTION_CARRIER}
                                                    {if $option.unique_carrier}
                                                        {if isset($carrier.instance->delay[$cookie->id_lang])}
                                                            <div class="delivery_option_delay">
                                                                {if !empty($carrier.instance->estimate_days)}{$carrier.instance->estimate_days|escape:'htmlall':'UTF-8'} {l s='Days' mod='onepagecheckoutps'}{else}{$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}{/if}
                                                            </div>
                                                        {/if}
                                                    {/if}
                                                {/if}
                                            {/foreach}
                                            </div><!--
                                        {/if}
                                        --><div class="carrier_price col-xs-4">
                                            <div class="delivery_option_price text-right">
                                                {if $option.total_price_with_tax && (!isset($option.is_free) || (isset($option.is_free) && !$option.is_free)) && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
                                                    {if $use_taxes == 1}
                                                        {if $priceDisplay == 1}
                                                            {convertPrice price=$option.total_price_without_tax}
                                                            <span class="tax">
                                                                {if $display_tax_label}{l s='(tax excl.)' mod='onepagecheckoutps'}{/if}
                                                            </span>
                                                        {else}
                                                            {convertPrice price=$option.total_price_with_tax}
                                                            <span class="tax">
                                                                {if $display_tax_label} {l s='(tax incl.)' mod='onepagecheckoutps'}{/if}
                                                            </span>
                                                        {/if}
                                                    {else}
                                                        {convertPrice price=$option.total_price_without_tax}
                                                    {/if}
                                                {else}
                                                    {l s='Free!' mod='onepagecheckoutps'}
                                                {/if}
                                            </div>
                                        </div>
                                        {if $carrier.instance->external_module_name == 'boxberrydelivery'}
                                            <div id="boxberry_td_id" class="col-xs-12 text-right">
                                                <a class="select_pickup_point" onclick="boxberry.open(BoxberryDelivery.getBoxberryDetails);">{l s='Select pickup point' mod='onepagecheckoutps'}</a>
                                            </div>
                                        {/if}
                                        {if $carrier.instance->external_module_name != '' && isset($carrier.extra_info_carrier) && isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}
                                            <div class="extra_info_carrier pull-right" style="display:{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}block{else}none{/if}">
                                                {if not empty($carrier.extra_info_carrier)}
                                                    <span>{$carrier.extra_info_carrier|escape:'quotes':'UTF-8'}</span>
                                                    <br />
                                                    <a class="edit_pickup_point" onclick="Carrier.displayPopupModule_{$carrier.instance->external_module_name|escape:'htmlall':'UTF-8'}({$carrier.instance->id|intval})">{l s='Edit pickup point' mod='onepagecheckoutps'}</a>
                                                {else}
                                                    <a class="select_pickup_point" onclick="Carrier.displayPopupModule_{$carrier.instance->external_module_name|escape:'htmlall':'UTF-8'}({$carrier.instance->id|intval})">{l s='Select pickup point' mod='onepagecheckoutps'}</a>
                                                {/if}
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            {/if}
                            {/foreach}

                            {if !$CONFIGS.OPC_SHIPPING_COMPATIBILITY}
                                {if isset($HOOK_EXTRACARRIER_ADDR) && isset($HOOK_EXTRACARRIER_ADDR.$id_address)}
                                    <div class="hook_extracarrier" id="HOOK_EXTRACARRIER_{$id_address|intval}">
                                        {$HOOK_EXTRACARRIER_ADDR.$id_address|escape:'htmlall':'UTF-8':false:true}
                                        <div class="clear clearfix">&nbsp;</div>
                                    </div>
                                {/if}
                            {/if}
                        {/foreach}
                    {/foreach}
                {/if}
            </div>

            {if (isset($recyclablePackAllowed) && $recyclablePackAllowed) or (isset($giftAllowed) && $giftAllowed)}
                <div class="row">
                    {if $recyclablePackAllowed}
                        <div class="col-xs-12">
                            <label for="recyclable">
                                <input type="checkbox" name="recyclable" id="recyclable" value="1" {if $recyclable == 1}checked="checked"{/if} class="carrier_checkbox not_unifrom not_uniform"/>
                                {l s='I agree to receive my order in recycled packaging' mod='onepagecheckoutps'}
                            </label>
                        </div>
                    {/if}
                    {if $giftAllowed}
                        <div class="col-xs-12">
                            <label for="gift">
                                <input type="checkbox" name="gift" id="gift" value="1" {if $cart->gift == 1}checked="checked"{/if} class="carrier_checkbox not_unifrom not_uniform"/>
                                {l s='I would like the order to be gift-wrapped.' mod='onepagecheckoutps'}
                                &nbsp;
                                {if $gift_wrapping_price > 0}
                                    <span class="price" id="gift-price">
                                        ({l s='Additional cost of' mod='onepagecheckoutps'}
                                        {if $priceDisplay == 1}{convertPrice price=$total_wrapping_tax_exc_cost}{else}{convertPrice price=$total_wrapping_cost}{/if}
                                        {if $use_taxes}{if $priceDisplay == 1} {l s='(tax excl.)' mod='onepagecheckoutps'}{else} {l s='(tax incl.)' mod='onepagecheckoutps'}{/if}{/if})
                                    </span>
                                {/if}
                            </label>
                        </div>
                    {/if}
                </div>
            {/if}

            {if isset($giftAllowed) && $giftAllowed}
                <div class="row">
                    <div class="col-xs-12">
                        <p id="gift_div_opc" class="textarea {if $cart->gift != 1}hidden{/if}">
                            <label for="gift_message">{l s='If you wish, you can add a note to the gift:' mod='onepagecheckoutps'}</label>
                            <textarea rows="1" id="gift_message" name="gift_message" class="form-control">{$cart->gift_message|escape:'htmlall':'UTF-8'}</textarea>
                        </p>
                    </div>
                </div>
            {/if}

            {if !$CONFIGS.OPC_SHIPPING_COMPATIBILITY}
                <div id="HOOK_BEFORECARRIER">
                    {if isset($HOOK_BEFORECARRIER)}
                        {$HOOK_BEFORECARRIER|escape:'htmlall':'UTF-8':false:true}
                    {/if}
                </div>
            {/if}

            {if $CONFIGS.OPC_SHIPPING_COMPATIBILITY}
                <div class="clear clearfix"></div>
                <div class="row">
                    <div class="col-xs-12 btn-secondary" id="show_carrier_embed">
                        <span>
                            <i class="fa-pts fa-pts-refresh"></i>
                            {l s='Change shipping carrier' mod='onepagecheckoutps'}
                        </span>
                    </div>
                </div>
            {/if}
        {/if}
    </div>
    </div>
    {include file='./custom_html/carrier.tpl'}
{/if}