<h3>{$xsell_name}</h3>
{$tpl_dir}
{foreach $xsell_products as $product}
{*    <a href="{$link->getProductLink($product->id, $product->link_rewrite)}">{$product->name}</a> <br>*}
{/foreach}