<form method="post" action="{$link->getAdminLink('AdminSeigixsellManagerelations')}&id_product={$xsell_id_product}">
<small>Obecnie edytowany produkt ID: {$xsell_id_product}</small>
<h1 style="margin-top: 0">{Product::getProductName($xsell_id_product)}</h1>

<h2>Produkty przypisane</h2>
<div style="border: 1px solid #acacac; background-color: #f9f9f9;padding: 20px;">
<p>Te produkty pokażą się jako akcesoria w obecnie edytowanym produkcie</p>
{foreach $xsell_groups as $xsellgroup}
    <h4>Powiązania w ramach grupy: <i>{$xsellgroup->name}</i></h4>
    {foreach xsellrelation::getRelatedProducts($xsellgroup->id, $xsell_id_product) as $item}
        <input name="delete_relation_a[{$xsellgroup->id}][]" value="{$item}" type="checkbox"> Produkt ID {$item} -
        <a href="{$link->getAdminLink('AdminSeigixsellManagerelations')}&id_product={$item}">{Product::getProductName($item)}</a>
        <br>
    {foreachelse}
        Brak powiązań
    {/foreach}
{/foreach}
</div>

<h2>Przypisany do</h2>
<p>Ten produkt, pokaże się w następujących produktach jako akcesorium</p>
<div style="border: 1px solid #acacac; background-color: #f9f9f9;padding: 20px;">
{foreach $xsell_groups as $xsellgroup}
    <h4>Powiązania w ramach grupy: <i>{$xsellgroup->name}</i></h4>
    {foreach xsellrelation::getRelatedProductsReverse($xsellgroup->id, $xsell_id_product) as $item}
        <input name="delete_relation_b[{$xsellgroup->id}][]" value="{$item}" type="checkbox"> Usuń powiązanie {$item} -
        <a href="{$link->getAdminLink('AdminSeigixsellManagerelations')}&id_product={$item}">{Product::getProductName($item)}</a>
        <br>
        {foreachelse}
        Brak powiązań
    {/foreach}
{/foreach}
</div>
    <input type="submit" value="Update" class="btn button btn-default">
</form>
