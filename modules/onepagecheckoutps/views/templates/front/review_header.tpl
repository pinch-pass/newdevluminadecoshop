{*
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @category  PrestaShop
 * @category  Module
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2019 PresTeamShop
 * @license   see file: LICENSE.txt
*}

{if isset($css_files)}
    {foreach from=$css_files key=css_uri item=media}
        <link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
    {/foreach}
{/if}
{if isset($js_files)}
    {foreach from=$js_files item=js_uri}
        <script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
    {/foreach}
{/if}

<script type="text/javascript">
    var payment_modules_fee = {$payment_modules_fee|escape:'quotes':'UTF-8'};

    var currencySign = '{$currencySign|escape:'html':'UTF-8'}';
    var currencyRate = '{$currencyRate|floatval}';
    var currencyFormat = '{$currencyFormat|intval}';
    var currencyBlank = '{$currencyBlank|intval}';
    var txtProduct = "{l s='product' mod='onepagecheckoutps' js=1}";
    var txtProducts = "{l s='products' mod='onepagecheckoutps' js=1}";
    var deliveryAddress = {$cart->id_address_delivery|intval};

    {literal}
    $(document).ready(function(){
        /*support some template that use live.*/
        $('.cart_quantity_up').off('click').die('click');
		$('.cart_quantity_down').off('click').die('click');
		$('.cart_quantity_delete').off('click').die('click');
    });
    {/literal}
</script>

<script type="text/javascript" src="{$js_dir|escape:'htmlall':'UTF-8'}cart-summary.js"></script>

<script type="text/javascript">
    {literal}

    setTimeout(function(){
        showLoadingAndAddEvent('.cart_quantity_up', function() {
            $('.cart_quantity_up').die('click').click(function(e) {
                e.preventDefault();

                //support module pproperties
                if (typeof ppCart !== typeof undefined) {
                    var icp = ppCart.getIcp($(this));
                    if (ppCart.qtyBehavior(ppCart.products[icp])) {
                        var input = $('input.cart_quantity_input' + ppCart.getIcpSelector(icp));
                        var currentVal = pp.parseFloat(input.val());
                        if (!isNaN(currentVal)) {
                            var newVal = pp.processUpDownQty(currentVal, ppCart.products[icp], 'up');
                            updateQty(newVal, true, input.get(0), true);
                        }

                        return;
                    }
                }

                upQuantity($(this).attr('id').replace('cart_quantity_up_', ''));
                $('#' + $(this).attr('id').replace('_up_', '_down_')).removeClass('disabled');
            });
        });
        showLoadingAndAddEvent('.cart_quantity_down', function(){
            $('.cart_quantity_down').die('click').click(function(e){
                e.preventDefault();

                //support module pproperties
                if (typeof ppCart !== typeof undefined) {
                    var icp = ppCart.getIcp($(this));
                    if (ppCart.qtyBehavior(ppCart.products[icp])) {
                        var input = $('input.cart_quantity_input' + ppCart.getIcpSelector(icp));
                        var currentVal = pp.parseFloat(input.val());
                        if (!isNaN(currentVal)) {
                            var newVal = pp.processUpDownQty(currentVal, ppCart.products[icp], 'down');
                            if (newVal > 0) {
                                updateQty(newVal, true, input.get(0), true);
                            }
                        }

                        return;
                    }
                }

                downQuantity($(this).attr('id').replace('cart_quantity_down_', ''));
            });
        });
        showLoadingAndAddEvent('.cart_quantity_delete', function(){
            $('.cart_quantity_delete').die('click').click(function(e){
                e.preventDefault();
                deleteProductFromSummary($(this).attr('id'));
            });
        });
        showLoadingAndAddEvent('.cart_quantity_input', function(){
            $('.cart_quantity_input').die().typeWatch({
                highlight: true, wait: 600, captureLength: 0, callback: function(val){
                    updateQty(val, true, this.el);
                }
            });
        });

        updateCartSummary = function (json){
            if (typeof json !== typeof undefined && json.products.length > 0){
                if (json.is_virtual_cart){
                    $('#onepagecheckoutps_step_two_container').remove();
                    $('#onepagecheckoutps_step_three_container').removeClass('col-md-6');

                    if (!OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL) {
                        AppOPC.$opc_step_one.find('#panel_address_delivery').remove();
                    }

                    OnePageCheckoutPS.IS_VIRTUAL_CART = true;

                    PaymentPTS.getByCountry();
                    Review.display();
                }else{
                    if (typeof json.load === typeof undefined){
                        Fronted.loading(true, '#onepagecheckoutps_step_review_container');

                        Carrier.getByCountry();
                    }
                }
            }
        }

        Fronted.loading(false, '#onepagecheckoutps_step_review_container');
        Fronted.loadingBig(false);
    }, 1000);

	function showLoadingAndAddEvent(selector, event){
		var $selector = $(selector);

		if ($selector.length > 0){
			var events = $._data($selector[0], "events");

            //solo entra en caso que no encuentre asignado los eventos, si no siga todo normal.
            if (typeof events === typeof undefined && typeof event == 'function'){
                event();
            }

			if (typeof events != typeof undefined && typeof ppCart === typeof undefined){
				$.each(events, function(type, events) {
					if (type === 'click') {
						var original_events = [];
						$.each(events, function(e, event) {
							original_events.push(event.handler);
						});

						var new_event = function(e) {
							e.preventDefault();

                            Fronted.loading(true, '#onepagecheckoutps_step_review_container');

							$(document).on('click', '.fancybox-close', function(){
                                 Fronted.loadingBig(false);
							});

							$(e.currentTarget).off(type, new_event).prop('disabled', true);
							$.each(original_events, function(o, original_event) {
								$(e.currentTarget).on('click', original_event).trigger(type);
							});
						};

						$selector.off(type).on(type, new_event);
					}
				});
			}
		}
	}

    {/literal}
</script>