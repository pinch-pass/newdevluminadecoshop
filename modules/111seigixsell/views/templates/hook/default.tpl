{*
Funkcje smarty

W ten sposób możesz wyświetlić nazwę cechy o ID = 1
{xsell_name id_feature=1}
    id_feature - ID grupy cech, dla której chcemy wyświetlić nazwę

Pełny przykład formatowania wartości na podstawie cechy
{xsell_label product=$product id_feature="1" display_name=false display_value=true separator=": " default=''}

    product - Obiekt (\Product) produktu, dla którego chcemy pobrać wartośći cechy
    id_feature - ID Grupy cechy dla której checmy wyświetlić wartość
    display_name - Czy wyświetlać nazwę grupy cech (domyślnie false)
    display_value - Czy wyświetlać wartość (domyślnie: true)
    separator - Separator dla rozdzielenia nazwy i wartośći ( tylko jeśli display_name i display_value są = true)
    default - domyślna wartość zwracana, jeśli nie znaleziono cechy w produkcie

Najprostszy przykład użycia:
    {xsell_label product=$product id_feature="1"}

*}
<div class="xsell {$xsell_container_class} xsell-{$xsell_shopversion}">
    <h3 class="page-product-heading">{$xsell_name}</h3>
    <div class="xsell-elements">
        {foreach $xsell_products as $product}
            <div class="xsell-element"
                 {if isset($xsell_adaptive) && $xsell_adaptive}
                    {if (!isset($hide_thumbnails) || !$hide_thumbnails) && (isset($hide_text) && $hide_text)}
                        style="width: {$xsell_thumb_x+10}px; height: {$xsell_thumb_y+20}px"
                    {/if}
                 {/if}
            >
                <a title="{$product->name|escape}" class="xsell-link" href="{$xsell->getProductLink($product->id, $product->link_rewrite)}">
                    {if !isset($hide_thumbnails) || !$hide_thumbnails}
                        {assign var="imageinfo" value=Product::getCover($product->id)}
                        <img class="xsell-image" {if isset($xsell_adaptive) && $xsell_adaptive}style="max-height: none;"{/if} width="{$xsell_thumb_x}" height="{$xsell_thumb_y}" src="{$link->getImageLink($product->link_rewrite, $imageinfo.id_image, $xsell_thumb)}">
                    {/if}

                    {if !isset($hide_text) || !$hide_text}
                    <span class="xsell-name">{$product->name|truncate:80}</span>
                    {/if}
                </a>
            </div>
        {/foreach}
    </div>
</div>
