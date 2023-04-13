<p class="cart_navigation clearfix">
{if $product.quantity > 0}
	<a href="#" data-id="{$product.id_product}" class="fancy_type_realty add_onclick"> {l s='Quick order' mod='ecm_quickorder'}</a>
{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
     <a href="{$product.link|escape:'html':'UTF-8'}" class="fancy_type_realty"> {l s='Quick order' mod='ecm_quickorder'}</a>
{else}
    <span class="fancy_type_realty"> {l s='Quick order' mod='ecm_quickorder'}</span>
{/if}
</p>
