<div id="cartTotalAmountZen" style="display: none;" >
    {$p24_zencard_products_with_tax}
</div>
<div style="display: none;" id="currencySignZen"> {$currency_sign}</div>
<div style="display: none;" id="zenCards"></div>
<div style="display: none;" id="zenDiscount"></div>
<script>

    /**
     *  Loading ZenCard Script
     */
    jQuery(document).ready(function () {

        // Catching all requests and update basket amounts and ZenCard coupon after getting response.
        (function() {
            var origOpen = XMLHttpRequest.prototype.open;
            XMLHttpRequest.prototype.open = function() {
                this.addEventListener( 'load', function() {
                    if( !this.dontTriggerListener ) {
                        loadZenCardBox(false);
                        window.dispatchEvent(new Event('resize'));
                    }
                } );
                origOpen.apply( this, arguments );
            };
        })();

        jQuery("#proceedPaymentLink").parent().attr('disabled', true);
        if (typeof Zencard === "undefined") {

            var script = document.createElement("script");
            script.type = "text/javascript";
            var html = '{$p24_zencard_script}';
            var scriptData = jQuery.parseHTML(html);

            script.src = jQuery(scriptData[0]).attr("src");
            script.setAttribute(
                    "data-zencard-mtoken",
                    jQuery(scriptData[0]).attr("data-zencard-mtoken")
            );

            script.onload = loadZenCardBox;

            if (script.readyState) {  //IE
                script.onreadystatechange = function () {
                    if (script.readyState === "loaded" || script.readyState === "complete") {
                        script.onreadystatechange = null;
                        loadZenCardBox();
                    }
                };
            } else {  //Others
                script.onload = loadZenCardBox;
            }

            var zenCards = document.getElementById("zenCards");
            zenCards.appendChild(script);
        } else {
            loadZenCardBox(false);
        }
    });
    
    function customWriteAmountWithDiscount(amountWithDiscountObj) {
            var price = amountWithDiscountObj.major + "." + amountWithDiscountObj.minor;
            jQuery.ajax({
                url: window.location.host + "?action=shippingCosts&fc=module&module=przelewy24&controller=zenCardAjax",
                xhrFields: {
                    dontTriggerListener: true
                },
                async: false,
                success: function (data) {
                    if (data !== undefined && data !== null) {
                        price = (+price + +data).toFixed(2);
                    }
                }
            });
            price = price.toString().replace(/\./g, ',');
            var currencySignZen = jQuery('#currencySignZen').html();
            var output = [price, currencySignZen];
            if (amountWithDiscountObj.hasDiscount()) {
                output.push(' <span class="zen-card-small-description">' + '(z rabatem ZenCard)' + '</span>');
            }
            jQuery('#total_price, .price.cart_block_total.ajax_block_cart_total, #amount.price, #amount_1.price, #amount_2.price').html(output.join(''));
    }

    function getCartTotalAmountZen(callback) {
        jQuery.ajax({
            url: '{$get_cart_total_amount_url}',
            method: "POST",
            success: function( response ) {
                jQuery("#cartTotalAmountZen").html( parseFloat( response ) );
            },
            complete: function() {
                if( callback )
                    callback();
            },
            xhrFields:  {
                dontTriggerListener: true
            }
        });
    }

    function loadZenCardBox(listen) {

        getCartTotalAmountZen( function() {
            Zencard.run(function () {

                if (typeof listen === "undefined" || typeof listen === "object") {
                    Zencard.listen('discountEvent', function discountEventShopHandler(eventData) {
                        // listen
                    });
                }

                Zencard.config({
                    couponElementPath: '#zenCards',
                    basketAmountPath: '#cartTotalAmountZen',
                    amountWithDiscountPath: '#zenDiscount',
                    writeAmountWithDiscount: customWriteAmountWithDiscount
                });

                Zencard.go();
            });

            jQuery("#p24-zen-loader").css('display', 'none');
            jQuery("#proceedPaymentLink").parent().attr('disabled', false);
        });
    }
</script>