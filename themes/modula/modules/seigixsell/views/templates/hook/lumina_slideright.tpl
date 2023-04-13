{if isset($xsell_products) && count($xsell_products)}
    <a class="xsell_button_{$xsell_id_group} button button-exclusive exclusive xsell_button_color"><span>{$xsell_name}</span></a>

    <div class="xsell_wrap_{$xsell_id_group}">
    <div class="xsell_wrapin_{$xsell_id_group}">
        <div id="xsell_slide{$xsell_id_group}">
            <a class="xsell_button">&times;</a>
            <h4>{$xsell_name}</h4>
            <div class="xsell_items">
                {foreach $xsell_products as $xsproduct}
                    <div class="xsell-right-slide col-xs-6{if $xsproduct->id == $product->id} active{/if}">
                        <a href="{$link->getProductLink($xsproduct->id, $xsproduct->link_rewrite)}">
                            {assign var="imageinfo" value=Product::getCover($xsproduct->id)}
                            <img class="img-responsive" src="{$link->getImageLink($xsproduct->link_rewrite, $imageinfo.id_image, 'medium_default')}">
                            <div class="product-name">{$xsproduct->name}</div>
                            <div class="price product-price">{convertPrice price=$xsproduct->getPrice()}</div>
                        </a>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
    </div>

    <style>
        .xsell_wrap_{$xsell_id_group} {
            position: fixed;
            left: 100%;
            top: 0;
            height: 100vh;
            z-index: 5010;
            border: 1px solid black;
        }
        .xsell_wrap_in{$xsell_id_group} {
            position: relative;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
            border: 1px solid black;
        }

        #xsell_slide{$xsell_id_group} {
            position: absolute;
            bottom: 0;
            right: -100vw;
            overflow-y: auto;
            width: 100vw;
            height: 100vh;
            max-width: 100vw;
            background: white;
            transition: right 1s;
            padding: 10px;
        }
        .xsell_button_{$xsell_id_group} {
            margin-top: 12px;
        }
        .xsell_wrap_{$xsell_id_group}.slideout #xsell_slide{$xsell_id_group} {
            transition: right 1s;
            right: 0;
        }
        @media (min-width: 576px) {
            .xsell_wrap_in{$xsell_id_group} {
                width: 390px;
            }
            #xsell_slide{$xsell_id_group} {
                right: -390px;
                width: 390px;
                border-left: 1px solid #d3d3d3;
            }
        }

        #xsell_slide{$xsell_id_group} img {
            display: inline-block;
        }
        #xsell_slide{$xsell_id_group} .xsell-right-slide {
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }
        #xsell_slide{$xsell_id_group} .xsell-right-slide.active {
            border: 1px solid #d3d3d3;
        }
        #xsell_slide{$xsell_id_group} .xsell_items {
            display: flex;
            flex-wrap: wrap;
        }
        #xsell_slide{$xsell_id_group} .xsell_items .xsell-right-slide {
            width: 45%;
            margin: 0px 2% 20px;
            padding: 10px;

        }
        #xsell_slide{$xsell_id_group} .xsell_items .xsell-right-slide .price.product-price {
            margin: 10px 0 17px 0;
            line-height: 23px;
        }
        #xsell_slide{$xsell_id_group} .xsell_items .xsell-right-slide div.product-name {
            margin-bottom: 0;
        }

        #xsell_slide{$xsell_id_group} .xsell_button {
            cursor: pointer;
            font-size: 2em;
            margin-top: 0;
            float: right;
            margin: 19px 11px 0 0;
        }
        #xsell_slide{$xsell_id_group} h4 {
            border-bottom: 1px solid #d3d3d3;
            padding: 10px 10px 20px;
            text-transform: uppercase;
        }
    </style>

    <script type="text/javascript">
        $('.xsell_wrap_{$xsell_id_group}').appendTo(document.body);
        $('.xsell_button_{$xsell_id_group}, .xsell_wrap_{$xsell_id_group} .xsell_button').click(function () {
            $('.xsell_wrap_{$xsell_id_group}').toggleClass('slideout');
        });

        function recalcOffsetXsell_{$xsell_id_group}() {
            let curh = $('#stickynav').height() + 1;
            $('#xsell_slide{$xsell_id_group}').css({
                height: 'calc(100vh - ' + curh + 'px)'
            });

        }
        if ($(window).width() > 767) {
            $(window).resize(recalcOffsetXsell_{$xsell_id_group});
            $(window).scroll(recalcOffsetXsell_{$xsell_id_group});
            recalcOffsetXsell_{$xsell_id_group}();
        }
    </script>
{/if}