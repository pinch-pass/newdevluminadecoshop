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

<div id="address_card_new" class="address_card col-xs-12">
    <div class="container_card">
        <div id="address_card_new_content">
            <span>
                <i class="fa-pts fa-pts-plus"></i>
                {l s='Add new address' mod='onepagecheckoutps'}
            </span>
        </div>
    </div>
</div>
{if isset($addresses)}
    {if $addresses|count > 4}
        <div class="col-md-12" style="margin: 10px 0;">
            <input type="text" class="search_address form-control" placeholder="{l s='Search your address' mod='onepagecheckoutps'}" />
            <i class="fa-pts fa-pts-search icon_search_address"></i>
        </div>
    {/if}

    {foreach from=$addresses item='address' key='item'}
    <div id="address_card_{$address.id_address|intval}" data-id-address="{$address.id_address|intval}" class="address_card col-xs-12 {if $addresses|count > 1}col-md-6{else}col-md-12{/if}">
        <div class="container_card {if $address.id_address eq $id_address}selected alert alert-info{/if}">
            <div class="header_card">
                <span>
                    {$address.alias|escape:'html':'UTF-8'}
                </span>
                {if $id_address neq 0}
                    {if $address.id_address eq $id_address}
                        <i class="fa-pts fa-pts-check-circle-o"></i>
                    {else}
                        <i class="fa-pts fa-pts-circle-thin"></i>
                    {/if}
                {/if}
            </div>
            <div class="content_card">
                <ul>
                    <li class="full_name" style='font-weight: bold;'>{$address.firstname|escape:'html':'UTF-8'} {$address.lastname|escape:'html':'UTF-8'}</li>
                    {if $address.company neq ''}
                    <li class="company">{$address.company|escape:'html':'UTF-8'}</li>
                    {/if}
                    <li class="address1">{$address.address1|escape:'html':'UTF-8'}</li>
                    <li class="city_state_postcode">{if $address.city neq '.'}{$address.city|escape:'html':'UTF-8'}, {/if}{$address.state|escape:'html':'UTF-8'} {if $address.postcode neq 0}({$address.postcode|escape:'html':'UTF-8'}){/if}</li>
                    <li class="country">{$address.country|escape:'html':'UTF-8'}</li>
                    <li class="phone">
                        {if $address.phone neq ''}
                            <i class="fa-pts fa-pts-phone" style="font-size: 15px;"></i>&nbsp;{$address.phone|escape:'html':'UTF-8'}
                        {/if}
                        {if $address.phone_mobile neq ''}
                            {if $address.phone neq ''}&nbsp;/&nbsp;{/if}
                            <i class="fa-pts fa-pts-mobile" style="font-size: 15px;"></i>&nbsp;{$address.phone_mobile|escape:'html':'UTF-8'}
                        {/if}
                    </li>
                </ul>
            </div>
            <div class="footer_card">
                <div class="row">
                    <button type="button" data-id-address="{$address.id_address|intval}" class="edit_address btn btn-xs btn-default {if $address.id_address eq $id_address}col-xs-12{else}col-xs-6{/if}">
                        {l s='Edit' mod='onepagecheckoutps'}
                    </button>
                    {if $address.id_address neq $id_address}
                        <button type="button" data-id-address="{$address.id_address|intval}" class="delete_address btn btn-xs btn-default col-xs-6">
                            {l s='Delete' mod='onepagecheckoutps'}
                        </button>
                    {/if}
                </div>
                {if $id_address neq 0}
                    <div class="row">
                        {if $address.id_address neq $id_address}
                            <button type="button" data-id-address="{$address.id_address|intval}" style="margin-top: 6px;padding: 2px;text-transform: capitalize;" class="choose_address btn btn-sm btn-primary btn-block">
                                {l s='Choose' mod='onepagecheckoutps'}
                            </button>
                        {else}
                            <button type="button" style="margin-top: 6px;padding: 2px;text-transform: capitalize;" class="selected_address btn btn-sm btn-primary btn-block disabled">
                                {l s='Selected' mod='onepagecheckoutps'}
                            </button>
                        {/if}
                    </div>
                {/if}
            </div>
        </div>
    </div>
    {/foreach}
{/if}