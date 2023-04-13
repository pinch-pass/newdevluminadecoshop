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

{if !$register_customer}
    <div id="onepagecheckoutps_step_two_container" class="{$classes|escape:'htmlall':'UTF-8'} {if isset($IS_VIRTUAL_CART) && $IS_VIRTUAL_CART}hidden{/if}">
        <h5 class="onepagecheckoutps_p_step onepagecheckoutps_p_step_two">
            <i class="fa-pts fa-pts-truck fa-pts-3x"></i>
            {l s='Shipping method' mod='onepagecheckoutps'}
        </h5>
        <div id="onepagecheckoutps_step_two"></div>
    </div>
{/if}