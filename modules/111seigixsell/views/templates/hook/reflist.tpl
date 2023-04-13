<h3 class="page-product-heading">{$xsell_name}</h3>

{foreach $xsell_products as $product}
    <a href="{$link->getProductLink($product->id, $product->link_rewrite)}">
        {$product->reference}
    </a>
{/foreach}