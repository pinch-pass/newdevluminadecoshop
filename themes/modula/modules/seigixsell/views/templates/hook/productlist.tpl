<section class="page-product-box blockproductscategory">

    <div class="titlebordrtext1">
        <h4 class="titleborderh5">
            {$xsell_name}
        </h4>
    </div>
    <div class="titleborderout1">
        <div class="titleborder2"></div>
    </div>

    <div id="productscategory_list" class="clearfix">

        <ul id="bxslider1" class="bxslider clearfix">
            {foreach $xsell_products as $categoryProduct}
            <li class="product-box item">
                <a href="{$link->getProductLink($categoryProduct->id, $categoryProduct->link_rewrite, $categoryProduct->category, $categoryProduct->ean13)}" class="lnk_img product-image">
                    {assign var="imageinfo" value=Product::getCover($categoryProduct->id)}
                    <img src="{$link->getImageLink($categoryProduct->link_rewrite, $imageinfo.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct->name|htmlspecialchars}" />
                </a>
                <h5 class="product-name">
                    <a href="{$link->getProductLink($categoryProduct->id, $categoryProduct->link_rewrite, $categoryProduct->category, $categoryProduct->ean13)|escape:'html':'UTF-8'}">{$categoryProduct->name|truncate:30:' ...'|escape:'html':'UTF-8'}</a>
                </h5>
                <br />
            </li>
            {/foreach}
        </ul>

    </div>
</section>