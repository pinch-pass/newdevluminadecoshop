<div class="box" style="overflow: auto;">
	<h2><a href="http://przelewy24.pl" target="_blank"><img src="{$modules_dir}przelewy24/img/logo.png" alt="{l s='Pay with Przelewy24' mod='przelewy24'}"/></a>&nbsp;{l s='Pay with Przelewy24' mod='przelewy24'}</h2>
	<p>{l s='Your payment was not confirmed by Przelewy24. If you want to retry press button "Retry".' mod='przelewy24'}</p>
	<p class="cart_navigation">
		<a class="exclusive_large" href="{$p24_retryPaymentUrl}">
			<span id="proceedPaymentLink">
				{l s='Retry' mod='przelewy24'}
			</span>
		</a>
	</p>
</div>

{if $extracharge_text && $extracharge}
<script>
	$(document).ready(function() {
		$('.totalprice.item').before(
				'<tr class="item" id="extracharge">' +
				'<td colspan="1"><strong>{$extracharge_text}</strong></td>' +
				'<td colspan="4">{$extracharge}</td>' +
				'</tr>');
	});
</script>
{/if}

<script>
	$(document).ready(function () {
		var $orderDetail = $('#order-detail-content');
		var str = $orderDetail.html();
		var regex = /Rabat Zencard (.*)<\//gi;
		str = str.replace(regex, 'Rabat Zencard <\/');
		$orderDetail.html(str);
	});
</script>