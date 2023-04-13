{if $error=="ok"}
<div class="success">
<p class="title"></i>{l s='Спасибо!' mod='ecm_quickorder'}</p>
<p class="desck">{l s='Ваш заказ №' mod='ecm_quickorder'}{$order.transactionId} {l s=' принят' mod='ecm_quickorder'}</p>
<p class="desck">{l s='Наш менеджер свяжется с вами в ближайшее время для уточнения деталей заказа' mod='ecm_quickorder'}</p>
{*<p class="desck2">{l s='Thank you for your attention to our store!' mod='ecm_quickorder'}</p>*}
</div>

<script>
$(function(){
  $(".add_onclick").attr("disabled", true);
});
{literal}
$('.box-cart-bottom').after('<p class="label-danger">{/literal}{l s='Вы уже оформили заказ в 1 клик' mod='ecm_quickorder'} <strong>№ {$order.transactionId}.</strong> {l s='Если хотите оформить новый, пожалуйста обновите страницу' mod='ecm_quickorder'}{literal}</p>');{/literal}
var order = {$order|@json_encode}
{literal}
window.dataLayer = window.dataLayer || [];
dataLayer.push({
  'event': 'purchase', 
   'transactionId': order.transactionId,
   'transactionAffiliation': 'One Click Order',
   'transactionTotal': order.transactionTotal,
   'transactionProducts': [{
       'sku': order.sku,
       'name': order.name,
       'price': order.price,
       'quantity': order.quantity
   }]
});
{/literal}

</script>

{/if}