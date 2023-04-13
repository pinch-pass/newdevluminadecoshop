<script type="text/javascript">
	function p24placeHtmlLinkPartCalc(product_ammount, part_count, part_cost) {
		$('#left_payment_parts').remove();
		if (part_cost > 0) {
			$('<div class="product_attributes clearfix p24-raty-calc" id="left_payment_parts"><a target="_blank" style="font-size:14px;" ' +
			  ' href="https://secure.przelewy24.pl/kalkulator_raty/index.html?ammount=' +
			  product_ammount + '"> <img width="70" style="margin-right: 5px;" src="https://secure.przelewy24.pl/kalkulator_raty/img/logoAR.jpg">' + part_count + ' {l s='installments x ~' mod='przelewy24'}' + part_cost + ' zł </a></div>')
			.insertAfter('.content_prices');
		}
	}

	function updatePartLink() {
		$('#left_payment_parts a').css('opacity', 0.3);
		var item_count = parseFloat($('#quantity_wanted').val().replace(',','.'));
		$.ajax('{$p24_ajax_url}', {
			method: 'POST', type: 'POST',
			data: { action: "calculatePart", amount: {$product_ammount} * item_count },
			error: function() {
				$('#left_payment_parts').remove();
			},
			success: function(response) {
				var result = JSON.parse(response);
				p24placeHtmlLinkPartCalc({$product_ammount} * item_count, result.ileRat, result.kwotaRaty);
			},
		});
	}

	$(document).ready(function() {
		p24placeHtmlLinkPartCalc('{$product_ammount}', '{$part_count}', '{$part_cost}');
		$('.product_quantity_up,.product_quantity_down').click(function(){ window.setTimeout(updatePartLink,50); });
		$('#quantity_wanted').change(function(){ updatePartLink(); });
	});
</script>