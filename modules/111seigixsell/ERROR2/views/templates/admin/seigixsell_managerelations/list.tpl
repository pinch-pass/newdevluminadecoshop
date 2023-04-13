<h1>Lista produktów</h1>
<p>Poniżej znajduje lista produktów które zawierają akcesoria, lub są akcesoriami w module X-Sell</p>
<div style="border: 1px solid #acacac; background-color: #f9f9f9;padding: 20px;">
    {foreach $xsell_ids as $product}
        <p>
            {$product.id} - {Product::getProductName($product.id)}
            <br>
            <a target="_blank" href="{$link->getAdminLink('AdminSeigixsellManagerelations')}&id_product={$product.id}">edytuj relacje</a>
            <a target="_blank" href="{$link->getProductLink($product.id)}">podgląd produktu</a>
        </p>
    {/foreach}
</div>
