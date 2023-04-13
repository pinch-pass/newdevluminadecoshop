{foreach $xsell_groups as $xsellgroup}
    <h4>Powiązania w ramach grupy: <i>{$xsellgroup->name}</i></h4>
    {foreach xsellrelation::getRelatedProducts($xsellgroup->id, $xsell_id_product) as $item}
        <label><input name="delete_relation_a[{$xsellgroup->id}][]" value="{$item}" type="checkbox"> Produkt ID {$item} /
            {* (jest wyświetlany w karcie obecnie edytowanego produktu) *}
        </label><br>
    {foreachelse}
        Brak powiązań
    {/foreach}
{*    {foreach xsellrelation::getRelatedProductsReverse($xsellgroup->id, $xsell_id_product) as $item}*}
{*        <label><input name="delete_relation_b" value="{$item}" type="checkbox"> Usuń powiązanie {$item} (obecnie edytowany produkt wyświetla się w karcie tego)</label><br>*}
{*    {foreachelse}*}
{*        Brak powiązań*}
{*    {/foreach}*}

{/foreach}