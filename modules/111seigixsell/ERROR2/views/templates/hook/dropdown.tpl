<div class="xsell {$xsell_container_class} xsell-{$xsell_shopversion}">
    <div class="xsell-elements-dropdown">
        <select id="{$xsell_container_class}-select">
            <option value="">{$xsell_name}</option>
            {foreach $xsell_products as $product}
                <option value="{$xsell->getProductLink($product->id, $product->link_rewrite)}">{$product->name|truncate:80}</option>
            {/foreach}
        </select>
    </div>
</div>
<script type="text/javascript">
    document.getElementById('{$xsell_container_class}-select').onchange = function() {
        var index = this.selectedIndex;
        var url = this.children[index].value;
        if(url && window.location.href != url) {
            window.location.href = url;
        }
    }

</script>
