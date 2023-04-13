{if isset($xsell_products) && count($xsell_products)}
<style>
.xsell-color-container {
display: inline-block;
}
.xsell-color-option {
height: 32px;
width: 32px;
display: inline-block;
border: 1px solid gray;
}
</style>
    <label class="one-line-label">{l s="Цвет"}:</label>
    {assign var="xsell_feature_id" value="9"}
    {assign var="xsell_featval_lang" value=FeatureValue::getFeatureValuesWithLang($lang_id, $xsell_feature_id)}

    {assign var="colors" value=[
        "216"=>"#000000",
        "217"=>"#ffffff",
        "218"=>"#C0C0C0",
        "219"=>"#FFD700",
        "465"=>"#c4868e",
        "466"=>"#f2f8fb",
        "467"=>"#B22222",
        "468"=>"#008000",
        "469"=>"#F5F5DC",
        "470"=>"#585855",
        "471"=>"#ffbf00",
        "472"=>"#FFC0CB",
        "749"=>"#dbe4eb",
        "1186"=>"#b56d42",
        "7185"=>"#D3D3D3",
        "31796"=>"#8B4513",
        "31902"=>"#ebaf4c",
        "45316"=>"#FFFF00",
        "47573"=>"#1E90FF"]}

    {foreach $xsell_products as $product_related}
        {foreach $product_related->getFeatures() as $xs_feature}
            {if $xs_feature.id_feature == $xsell_feature_id}
                <div class="xsell-color-container">
                {assign var="imageinfo" value=Product::getCover($product_related->id)}
                <a class="replaceonhover" data-gallery-image="{$link->getImageLink($product->link_rewrite, $imageinfo.id_image, 'thickbox_default')}" href="{$link->getProductLink($product_related->id, $product_related->link_rewrite)}">
                    <span class="xsell-color-option" style="background-color: {$colors[$xs_feature.id_feature_value]}"></span>
                    {foreach $xsell_featval_lang as $fvlang}
                        {if $xs_feature.id_feature_value == $fvlang.id_feature_value }
{*                            {$fvlang.value}*}
                            {break}
                        {/if}
                    {/foreach}
                </a>
                </div>
                {break}
            {/if}
        {/foreach}
    {/foreach}
{/if}