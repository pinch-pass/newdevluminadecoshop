$(document).ready(function() {
	$(document).on('click', '.oneclick-sew-by-order__button', (e) => {
		$.fancybox.close();
	});

	$(document).on('keyup', (e) => {
		if(e.keyCode == 27) {
			$.fancybox.close();
		}
	});

	$('body.product #add_to_cart').bind('hide', function(){
	    $('body.product .fancy_type_realty.add_onclick').hide();
	})
	$('body.product #add_to_cart').bind('show', function(){
	    $('body.product .fancy_type_realty.add_onclick').show();
	})
	if ($("body.product #add_to_cart").is(":visible") == true)
	    $('body.product .fancy_type_realty.add_onclick').show();
   	else
    	$('body.product .fancy_type_realty.add_onclick').hide();

	$(document).on('click', '#quick_order .product_quantity_up', function(e){
		e.preventDefault();
		fieldName = $(this).data('field-qty');
		var currentVal = parseInt($('#quick_order input[name='+fieldName+']').val());
		allowBuyWhenOutOfStock=$('#quick_order input[name=allowBuyWhenOutOfStock]').val();
		quantityAvailable=$('#quick_order input[name=quantityAvailable]').val();
		if (!allowBuyWhenOutOfStock && quantityAvailable > 0)
			quantityAvailableT = quantityAvailable;
		else
			quantityAvailableT = 100000000;
		if (!isNaN(currentVal) && currentVal < quantityAvailableT)
			$('#quick_order input[name='+fieldName+']').val(currentVal + 1).trigger('keyup');
		else
			$('#quick_order input[name='+fieldName+']').val(quantityAvailableT);

		$('#quick_order #quantity_wanted').change();
	});
	$(document).on('click', '#quick_order .product_quantity_down', function(e){
		e.preventDefault();
		fieldName = $(this).data('field-qty');
		var currentVal = parseInt($('#quick_order input[name='+fieldName+']').val());
		if (!isNaN(currentVal) && currentVal > 1)
			$('#quick_order input[name='+fieldName+']').val(currentVal - 1).trigger('keyup');
		else
			$('#quick_order input[name='+fieldName+']').val(1);

		$('#quick_order #quantity_wanted').change();
	});
	$(document).on('click', '#secondaryButtonee', function(e){
		$('.fancybox-close').click();
	});
	
	
   $(document).on('click', '.add_onclick', function(e){

   	    if($('#idCombination').is('input '))
   	    	$id_product_attribute =$('#idCombination').val();
   	    else
   	    	$id_product_attribute=0;

   	    if($(this).attr('data-idattribute'))
   	    $id_product_attribute=$(this).attr('data-idattribute');

		$.ajax({
			type: 'POST',
			url: baseDir + 'modules/ecm_quickorder/ajax.php?rand=' + new Date().getTime(),
			headers: { "cache-control": "no-cache" },
			data: 'form=true&id_product=' + $(this).attr('data-id')+'&id_product_attribute='+$id_product_attribute+'&id_lang='+id_lang,
			success: function(data)
			{
				if (data)
				{
					if (!!$.prototype.fancybox) {
						$.fancybox.open([
							{
								type: 'inline',
								autoScale: false,

								content: data
							}
						]);

						$(document).find('#ecm_quickorderform input[name=phone]').mask('+7 (999)-999-99-99');
						$(document).find('#ecm_quickorderform input[name=firstname]').mask('SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS', {
							'translation': {
								S: {pattern: /[A-Za-zА-Яа-я ]/}
							}
						});
					} else {
						alert(data);
					}
				}
			}
		});
   		return false;
   });
   
    $(document).on('submit', '#ecm_quickorderform', function(e){
		e.preventDefault();

		$(this).find('.modal-check').removeClass('error');
		if($(this).find('input[name=personal]').prop('checked') == false) {
			$(this).find('.modal-check').addClass('error');
			return;
		}

		var lastname = $(this).find('#lastname').val();
        $.ajax({
          	url: baseDir + 'modules/ecm_quickorder/ajax.php?rand=' + new Date().getTime(),
			type: "POST",
			beforeSend: function () {
				$("button#quickorder_button").attr("disabled", true);
			},
            data: {
                ajax: true,
                firstname: $(this).find('#firstname').val(),
                lastname:  (lastname)?lastname:'lastname',
                login_email:  $(this).find('#login_email').val(),
                phone:  $(this).find('#phone').val(),
                id_product:  $(this).find('#id_product').val(),
                id_product_attribute:  $(this).find('#id_product_attribute').val(),
                quantity:  $(this).find('#quantity_wanted').val()
            },
            dataType: "json",
            success: function(result) {
                if(result['success'] == 1){
					//$.fancybox.close();
					$('#error').hide();
					$('#quick_order .owner-oneclick__success').show();
					$('#quick_order #ecm_quickorderform').hide();
                } else if(result['errors']){
                    $('#error').show();
					let errors = '';
					result['errors'].forEach((item) => {
						errors += item + '<br/>';
					})
					$('#error div.alert').html(errors);
                }
				$("button#quickorder_button").attr("disabled", false);
            }
        });

    });


	//$(document).on('change', '#quick_order #quantity_wanted', function(){
	$('#quick_order #quantity_wanted').live('change', function () {
        qty = $('#quick_order input[name=qty]').val();
      	price = $('#quick_order #price_no').val();
     	$('#quick_order .count-price').text(qty+' шт. :');
		$('#quick_order .price.product-price').text(formatCurrency(parseFloat((price*qty)), currencyFormat, currencySign, currencyBlank));

	});


});

