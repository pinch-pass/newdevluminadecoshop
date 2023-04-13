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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $products}
    {if !$refresh}
        <div class="wishlistLinkTop">
            {*        <a id="hideSendWishlist" class="button_account icon"  href="#" onclick="WishlistVisibility('wishlistLinkTop', 'SendWishlist'); return false;" rel="nofollow" title="{l s='Close send this wishlist' mod='blockwishlist'}">
            <i class="icon-trash"></i>
        </a>
        <ul class="clearfix display_list">
            <li>
                <a  id="hideBoughtProducts" class="button_account" href="#" onclick="WishlistVisibility('wlp_bought', 'BoughtProducts'); return false;" title="{l s='Hide products' mod='blockwishlist'}">
                    {l s='Hide products' mod='blockwishlist'}
                </a>
                <a id="showBoughtProducts" class="button_account" href="#" onclick="WishlistVisibility('wlp_bought', 'BoughtProducts'); return false;" title="{l s='Show products' mod='blockwishlist'}">
                    {l s='Show products' mod='blockwishlist'}
                </a>
            </li>
            {if count($productsBoughts)}
                <li>
                    <a id="hideBoughtProductsInfos" class="button_account" href="#" onclick="WishlistVisibility('wlp_bought_infos', 'BoughtProductsInfos'); return false;" title="{l s='Hide products' mod='blockwishlist'}">
                        {l s="Hide bought product's info" mod='blockwishlist'}
                    </a>
                    <a id="showBoughtProductsInfos" class="button_account" href="#" onclick="WishlistVisibility('wlp_bought_infos', 'BoughtProductsInfos'); return false;" title="{l s='Show products' mod='blockwishlist'}">
                        {l s="Show bought product's info" mod='blockwishlist'}
                    </a>
                </li>
            {/if}
        </ul>
        <p class="wishlisturl form-group">
            <label>{l s='Permalink' mod='blockwishlist'}:</label>
            <input type="text" class="form-control" value="{$link->getModuleLink('blockwishlist', 'view', ['token' => $token_wish])|escape:'html':'UTF-8'}" readonly/>
        </p>
        <p class="submit">
            <a id="showSendWishlist" class="btn btn-default button button-medium" href="#" onclick="WishlistVisibility('wl_send', 'SendWishlist'); return false;" title="{l s='Send this wishlist' mod='blockwishlist'}">
                <span>{l s='Send this wishlist' mod='blockwishlist'}</span>
            </a>
        </p>*}
    {/if}
    <div class="wlp_bought catalog-right">
        {assign var='nbItemsPerLine' value=4}
        {assign var='nbItemsPerLineTablet' value=3}
        {assign var='nbLi' value=$products|@count}
        {math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
        {math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
        <div class="catalog-right_prod catalog-right_block">
            {foreach from=$products item=product name=i}
                {math equation="(total%perLine)" total=$smarty.foreach.i.total perLine=$nbItemsPerLine assign=totModulo}
                {math equation="(total%perLineT)" total=$smarty.foreach.i.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
                {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
                {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
                <div id="wlp_{$product.id_product}_{$product.id_product_attribute}" class="product-box">
                    <p class="form-group" style="display:none">
                        <label for="priority_{$product.id_product}_{$product.id_product_attribute}">
                            {l s='Priority' mod='blockwishlist'}:
                        </label>
                        <select id="priority_{$product.id_product}_{$product.id_product_attribute}" class="form-control grey">
                            <option value="0"{if $product.priority eq 0} selected="selected"{/if}>
                                {l s='High' mod='blockwishlist'}
                            </option>
                            <option value="1"{if $product.priority eq 1} selected="selected"{/if}>
                                {l s='Medium' mod='blockwishlist'}
                            </option>
                            <option value="2"{if $product.priority eq 2} selected="selected"{/if}>
                                {l s='Low' mod='blockwishlist'}
                            </option>
                        </select>
                        <input type="hidden" id="quantity_{$product.id_product}_{$product.id_product_attribute}" value="{$product.quantity|intval}" size="3"/>
                    </p>
                    <a href="javascript:;" onclick="WishlistProductManage('wlp_bought', 'delete', '{$id_wishlist}', '{$product.id_product}', '{$product.id_product_attribute}', $('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(), $('#priority_{$product.id_product}_{$product.id_product_attribute}').val());" title="{l s='Delete' mod='blockwishlist'}" class="delitem">
                        <div class="product-box_trash">
                            <svg viewBox="-57 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="m156.371094 30.90625h85.570312v14.398438h30.902344v-16.414063c.003906-15.929687-12.949219-28.890625-28.871094-28.890625h-89.632812c-15.921875 0-28.875 12.960938-28.875 28.890625v16.414063h30.90625zm0 0"></path>
                                <path d="m344.210938 167.75h-290.109376c-7.949218 0-14.207031 6.78125-13.566406 14.707031l24.253906 299.90625c1.351563 16.742188 15.316407 29.636719 32.09375 29.636719h204.542969c16.777344 0 30.742188-12.894531 32.09375-29.640625l24.253907-299.902344c.644531-7.925781-5.613282-14.707031-13.5625-14.707031zm-219.863282 312.261719c-.324218.019531-.648437.03125-.96875.03125-8.101562 0-14.902344-6.308594-15.40625-14.503907l-15.199218-246.207031c-.523438-8.519531 5.957031-15.851562 14.472656-16.375 8.488281-.515625 15.851562 5.949219 16.375 14.472657l15.195312 246.207031c.527344 8.519531-5.953125 15.847656-14.46875 16.375zm90.433594-15.421875c0 8.53125-6.917969 15.449218-15.453125 15.449218s-15.453125-6.917968-15.453125-15.449218v-246.210938c0-8.535156 6.917969-15.453125 15.453125-15.453125 8.53125 0 15.453125 6.917969 15.453125 15.453125zm90.757812-245.300782-14.511718 246.207032c-.480469 8.210937-7.292969 14.542968-15.410156 14.542968-.304688 0-.613282-.007812-.921876-.023437-8.519531-.503906-15.019531-7.816406-14.515624-16.335937l14.507812-246.210938c.5-8.519531 7.789062-15.019531 16.332031-14.515625 8.519531.5 15.019531 7.816406 14.519531 16.335937zm0 0"></path>
                                <path d="m397.648438 120.0625-10.148438-30.421875c-2.675781-8.019531-10.183594-13.429687-18.640625-13.429687h-339.410156c-8.453125 0-15.964844 5.410156-18.636719 13.429687l-10.148438 30.421875c-1.957031 5.867188.589844 11.851562 5.34375 14.835938 1.9375 1.214843 4.230469 1.945312 6.75 1.945312h372.796876c2.519531 0 4.816406-.730469 6.75-1.949219 4.753906-2.984375 7.300781-8.96875 5.34375-14.832031zm0 0"></path>
                            </svg>
                        </div>
                    </a>
                    <div class="product-box_over">
                        <div class="product-box_view">
                            <a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}" title="{l s='Product detail' mod='blockwishlist'}" class="product-box_top">
                                <img src="{$link->getImageLink($product.link_rewrite, $product.cover, 'home_default')|escape:'html':'UTF-8'}" class="replace-2x img-responsive product-box_img"></a>
                            {if isset($quick_view) && $quick_view}
                                {*<a class="product-box_fast-view quick-view" href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}" title="{l s='Quick view' mod='blockwishlist'}" rel="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}"></a>*}
                            {/if}
                            
                        </div>
                        <a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}" class="product-box_name">
                            {$product.name|escape:'html':'UTF-8'}
                        </a>
                        <div class="product-box_manufacturer"></div>
                        <div class="product-box_bottom">
                            {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                <div class="product-box_left" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                    <div>
                                        <div  itemprop="price" class="product-box_new">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</div>
                                    </div>
                                </div>
                            {/if}
                            <div class="product-box_right">
                                <a href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='blockwishlist'}" data-id-product="{$product.id_product|intval}" class="product-box_basket ajax_add_to_cart_button">{l s='Купить' mod='blockwishlist'}</a>
                            </div>
                            <div class="product-box_hidden">
                                <a href="javascript:;" onclick="WishlistProductManage('wlp_bought', 'delete', '{$id_wishlist}', '{$product.id_product}', '{$product.id_product_attribute}', $('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(), $('#priority_{$product.id_product}_{$product.id_product_attribute}').val());" title="{l s='Delete' mod='blockwishlist'}" class="product-box_favorite in_wishlist">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="25" viewBox="0 0 26 25">
                                        <path d="M22.375,3.938h-.062a7.111,7.111,0,0,0-5.937,3.25,7.111,7.111,0,0,0-5.937-3.25h-.062a7.066,7.066,0,0,0-7,7.063,15.214,15.214,0,0,0,2.988,8.294,52.354,52.354,0,0,0,10.012,9.644,52.355,52.355,0,0,0,10.013-9.644A15.214,15.214,0,0,0,29.375,11,7.066,7.066,0,0,0,22.375,3.938Zm2.6,14.325a47.937,47.937,0,0,1-8.6,8.475,48.009,48.009,0,0,1-8.6-8.481A13.483,13.483,0,0,1,5.125,11a5.3,5.3,0,0,1,5.263-5.306h.056a5.24,5.24,0,0,1,2.569.675,5.461,5.461,0,0,1,1.9,1.781,1.756,1.756,0,0,0,2.937,0,5.516,5.516,0,0,1,1.9-1.781,5.24,5.24,0,0,1,2.569-.675h.056A5.3,5.3,0,0,1,27.637,11,13.654,13.654,0,0,1,24.975,18.263Z" transform="translate(-3.375 -3.938)"></path>
                                    </svg>
                                    <span>{l s='удалить' mod='blockwishlist'}</span>
                                </a>
                                {*<div class="product-box_compare">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19.301" height="16" viewBox="0 0 19.301 16">
                                        <g transform="translate(-61 -638)">
                                            <path d="M18.8,45.491H6.9v-.47A1.252,1.252,0,0,0,5.654,43.77H3.712a1.252,1.252,0,0,0-1.251,1.251v.47H.5a.5.5,0,0,0,0,1H2.461v.47a1.252,1.252,0,0,0,1.251,1.251H5.654A1.252,1.252,0,0,0,6.9,46.962v-.47H18.8a.5.5,0,0,0,0-1ZM5.9,46.962a.251.251,0,0,1-.25.25H3.712a.251.251,0,0,1-.25-.25V45.021a.251.251,0,0,1,.25-.25H5.654a.251.251,0,0,1,.25.25Z" transform="translate(61 594.23)"></path>
                                            <path d="M18.8,193.348H16.839v-.47a1.252,1.252,0,0,0-1.251-1.251H13.647a1.252,1.252,0,0,0-1.251,1.251v.47H.5a.5.5,0,1,0,0,1H12.4v.47a1.252,1.252,0,0,0,1.251,1.251h1.941a1.252,1.252,0,0,0,1.251-1.251v-.47H18.8a.5.5,0,0,0,0-1Zm-2.962,1.471a.251.251,0,0,1-.25.25H13.647a.251.251,0,0,1-.25-.25v-1.941a.251.251,0,0,1,.25-.25h1.941a.251.251,0,0,1,.25.25Z" transform="translate(61 451.948)"></path>
                                            <path d="M18.8,352.074h-8.38v-.47a1.252,1.252,0,0,0-1.251-1.251H7.228A1.252,1.252,0,0,0,5.977,351.6v.47H.5a.5.5,0,1,0,0,1H5.977v.47A1.252,1.252,0,0,0,7.228,354.8H9.169a1.252,1.252,0,0,0,1.251-1.251v-.47H18.8a.5.5,0,0,0,0-1ZM9.42,353.545a.251.251,0,0,1-.25.25H7.228a.251.251,0,0,1-.25-.25V351.6a.251.251,0,0,1,.25-.25H9.169a.251.251,0,0,1,.25.25v1.941Z" transform="translate(61 299.204)"></path>
                                        </g>
                                    </svg>
                                    <span class="compare"><a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}" title="{l s='сравнение' mod='blockwishlist'}">{l s='сравнение' mod='blockwishlist'}</a></span>
                                </div>*}
                            </div>
                        </div>
                    </div>
                </div>
                {*<li id="wlp_{$product.id_product}_{$product.id_product_attribute}"
                    class="col-xs-12 col-sm-4 col-md-3 {if $smarty.foreach.i.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.i.iteration%$nbItemsPerLine == 1} first-in-line{/if} {if $smarty.foreach.i.iteration > ($smarty.foreach.i.total - $totModulo)}last-line{/if} {if $smarty.foreach.i.iteration%$nbItemsPerLineTablet == 0}last-item-of-tablet-line{elseif $smarty.foreach.i.iteration%$nbItemsPerLineTablet == 1}first-item-of-tablet-line{/if} {if $smarty.foreach.i.iteration > ($smarty.foreach.i.total - $totModuloTablet)}last-tablet-line{/if}">
                    <div class="row">
                        <div class="col-xs-6 col-sm-12">
                            <div class="product_image">
                                <a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}" title="{l s='Product detail' mod='blockwishlist'}">
                                    <img class="replace-2x img-responsive"  src="{$link->getImageLink($product.link_rewrite, $product.cover, 'home_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}"/>
                                </a>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-12">
                            <div class="product_infos">
                                <a class="lnkdel" href="javascript:;" onclick="WishlistProductManage('wlp_bought', 'delete', '{$id_wishlist}', '{$product.id_product}', '{$product.id_product_attribute}', $('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(), $('#priority_{$product.id_product}_{$product.id_product_attribute}').val());" title="{l s='Delete' mod='blockwishlist'}">
                                    <i class="icon-trash"></i>
                                </a>

                                <p id="s_title" class="product-name">
                                    {$product.name|truncate:30:'...'|escape:'html':'UTF-8'}
                                    {if isset($product.attributes_small)}
                                        <small>
                                            <a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}" title="{l s='Product detail' mod='blockwishlist'}">
                                                {$product.attributes_small|escape:'html':'UTF-8'}
                                            </a>
                                        </small>
                                    {/if}
                                </p>
                                {*<div class="wishlist_product_detail">
                                    <p class="form-group">
                                        <label for="quantity_{$product.id_product}_{$product.id_product_attribute}">
                                            {l s='Quantity' mod='blockwishlist'}:
                                        </label>
                                        <input type="text" class="form-control grey" id="quantity_{$product.id_product}_{$product.id_product_attribute}" value="{$product.quantity|intval}" size="3"/>
                                    </p>

                                    <p class="form-group">
                                        <label for="priority_{$product.id_product}_{$product.id_product_attribute}">
                                            {l s='Priority' mod='blockwishlist'}:
                                        </label>
                                        <select id="priority_{$product.id_product}_{$product.id_product_attribute}" class="form-control grey">
                                            <option value="0"{if $product.priority eq 0} selected="selected"{/if}>
                                                {l s='High' mod='blockwishlist'}
                                            </option>
                                            <option value="1"{if $product.priority eq 1} selected="selected"{/if}>
                                                {l s='Medium' mod='blockwishlist'}
                                            </option>
                                            <option value="2"{if $product.priority eq 2} selected="selected"{/if}>
                                                {l s='Low' mod='blockwishlist'}
                                            </option>
                                        </select>
                                    </p>
                                </div>*}
                                {*<div class="btn_action">
                                    <a class="btn btn-default button button-medium"  href="javascript:;" onclick="WishlistProductManage('wlp_bought_{$product.id_product_attribute}', 'update', '{$id_wishlist}', '{$product.id_product}', '{$product.id_product_attribute}', $('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(), $('#priority_{$product.id_product}_{$product.id_product_attribute}').val());" title="{l s='Save' mod='blockwishlist'}">
                                        <span>{l s='Save' mod='blockwishlist'}</span>
                                    </a>
                                </div>*}
                            {*</div>
                        </div>
                    </div>
                </li>*}
            {/foreach}
        </div>
    </div>
    {if !$refresh}
        <form method="post" class="wl_send box unvisible" onsubmit="return (false);">
            <fieldset>
                <div class="required form-group">
                    <label for="email1">{l s='Email' mod='blockwishlist'}1 <sup>*</sup></label>
                    <input type="text" name="email1" id="email1" class="form-control"/>
                </div>
                {section name=i loop=11 start=2}
                    <div class="form-group">
                        <label for="email{$smarty.section.i.index}">{l s='Email' mod='blockwishlist'}{$smarty.section.i.index}</label>
                        <input type="text" name="email{$smarty.section.i.index}" id="email{$smarty.section.i.index}"
                               class="form-control"/>
                    </div>
                {/section}
                <div class="submit">
                    <button class="btn btn-default button button-small" type="submit" name="submitWishlist"
                            onclick="WishlistSend('wl_send', '{$id_wishlist}', 'email');">
                        <span>{l s='Send' mod='blockwishlist'}</span>
                    </button>
                </div>
                <p class="required">
                    <sup>*</sup> {l s='Required field' mod='blockwishlist'}
                </p>
            </fieldset>
        </form>
        {if count($productsBoughts)}
            <table class="wlp_bought_infos unvisible table table-bordered table-responsive">
                <thead>
                <tr>
                    <th class="first_item">{l s='Product' mod='blockwishlist'}</th>
                    <th class="item">{l s='Quantity' mod='blockwishlist'}</th>
                    <th class="item">{l s='Offered by' mod='blockwishlist'}</th>
                    <th class="last_item">{l s='Date' mod='blockwishlist'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$productsBoughts item=product name=i}
                    {foreach from=$product.bought item=bought name=j}
                        {if $bought.quantity > 0}
                            <tr>
                                <td class="first_item">
									<span style="float:left;">
										<img
                                                src="{$link->getImageLink($product.link_rewrite, $product.cover, 'small')|escape:'html':'UTF-8'}"
                                                alt="{$product.name|escape:'html':'UTF-8'}"/>
									</span>			
									<span style="float:left;">
										{$product.name|truncate:40:'...'|escape:'html':'UTF-8'}
                                        {if isset($product.attributes_small)}
                                            <br/>
                                            <i>{$product.attributes_small|escape:'html':'UTF-8'}</i>
                                        {/if}
									</span>
                                </td>
                                <td class="item align_center">
                                    {$bought.quantity|intval}
                                </td>
                                <td class="item align_center">
                                    {$bought.firstname} {$bought.lastname}
                                </td>
                                <td class="last_item align_center">
                                    {$bought.date_add|date_format:"%Y-%m-%d"}
                                </td>
                            </tr>
                        {/if}
                    {/foreach}
                {/foreach}
                </tbody>
            </table>
        {/if}
    {/if}
{else}
    <p class="alert alert-warning">
        {l s='No products' mod='blockwishlist'}
    </p>
{/if}