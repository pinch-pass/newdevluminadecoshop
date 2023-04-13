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

{if $show_option_allow_separate_package}
    <p>
        <input type="checkbox" class="not_unifrom not_uniform" name="allow_seperated_package" id="allow_seperated_package" {if $cart->allow_seperated_package}checked="checked"{/if} />
        <label for="allow_seperated_package">{l s='Send the available products first' mod='onepagecheckoutps'}</label>
    </p>
{/if}

<div class="row clear clearfix"></div>

{if $CONFIGS.OPC_SHOW_ORDER_MESSAGE}
    <div id="div_leave_message">
        <p>{l s='If you would like to add a comment about your order, please write it below.' mod='onepagecheckoutps'}</p>
        <textarea name="message" id="message" class="form-control" rows="2">{strip}
        {if isset($oldMessage)}{$oldMessage|escape:'htmlall':'UTF-8'}{/if}
        {/strip}</textarea>
    </div>
{else}
    <hr class="separate-terms">
{/if}

{if $is_logged && $CONFIGS.OPC_ENABLE_PRIVACY_POLICY && $CONFIGS.OPC_REQUIRE_PP_BEFORE_BUY}
    <div id="div_privacy_policy">
        <p id="p_privacy_policy">
            <label for="privacy_policy">
                <input type="checkbox" class="not_unifrom not_uniform" name="privacy_policy" id="privacy_policy" value="1" {if $checkedTOS}checked="checked"{/if}/>
                {l s='I have read and accept the Privacy Policy.' mod='onepagecheckoutps'}
                <span class="read">{l s='(read)' mod='onepagecheckoutps'}</span>
            </label>
        </p>
    </div>
{/if}

{if $CONFIGS.OPC_ENABLE_TERMS_CONDITIONS}
    <div id="div_cgv">
        <p id="p_cgv">
            <label for="cgv">
                <input type="checkbox" class="not_unifrom not_uniform" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if}/>
                {l s='I agree to the terms of service.' mod='onepagecheckoutps'}
            </label>
            <span class="read">{l s='(read)' mod='onepagecheckoutps'}</span>
        </p>
    </div>
{/if}

<span id="container_float_review_point"></span>

<div id="container_float_review">
    {if !$CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO || $total_price <= 0}
        <div id="buttons_footer_review" class="row">
            {if $CONFIGS.OPC_SHOW_LINK_CONTINUE_SHOPPING}
                <div class="start-xs col-xs-12 col-md-4 nopadding">
                    <button type="button" id="btn_continue_shopping" class="btn btn-default pull-left"
                            {if not empty($CONFIGS.OPC_LINK_CONTINUE_SHOPPING)}data-link="{$CONFIGS.OPC_LINK_CONTINUE_SHOPPING|escape:'htmlall':'UTF-8'}"{/if}>
                        <i class="fa-pts fa-pts-chevron-left fa-pts-1x"></i>
                        {l s='Continue shopping' mod='onepagecheckoutps'}
                    </button>
                </div>
            {/if}
            <div class="end-xs col-xs-12 col-md-4 {if !$CONFIGS.OPC_SHOW_LINK_CONTINUE_SHOPPING}col-md-push-8{else}col-md-push-4{/if} col-sm-offset-0 nopadding">
                <button type="button" id="btn_place_order" class="btn btn-primary btn-lg pull-right" >
                    <i class="fa-pts fa-pts-shopping-cart fa-pts-1x"></i>
                    {l s='Checkout' mod='onepagecheckoutps'}
                </button>
            </div>
        </div>
    {/if}
</div>

{if $CONFIGS.OPC_ENABLE_HOOK_SHOPPING_CART && !$CONFIGS.OPC_COMPATIBILITY_REVIEW && !$CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO}
    <div id="HOOK_SHOPPING_CART" class="row">{$HOOK_SHOPPING_CART|escape:'html':'UTF-8':false:true}</div>
    <p class="cart_navigation_extra row">
        <span id="HOOK_SHOPPING_CART_EXTRA">{$HOOK_SHOPPING_CART_EXTRA|escape:'html':'UTF-8':false:true}</span>
    </p>
{/if}

<div class="row">
    {include file='./custom_html/review.tpl'}
</div>