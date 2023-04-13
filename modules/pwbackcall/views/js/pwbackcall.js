$(function() {
    const maskPhone = '+7 (999)-999-99-99';
    let completePhone = false;
    $('#uipw-form_call_modal input[name=phone]').mask(maskPhone, {
        onComplete: function(cep) {
            completePhone = true;
        }
    });
    $('#uipw-form_call_modal input[name=name]').mask('SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS', {'translation': {
            S: {pattern: /[A-Za-zА-Яа-я ]/}
        }
    });

    $(document).on('click', '#call-pop #mod-bg-call', (e) => {
        if($(e.target).is('#mod-bg-call')) {
            $('#mod').fadeOut('400');
            $('#popup').fadeOut('400');
            $('#call-pop').fadeOut('400');
            $('.sew-by-order-utp').fadeOut('400');
            $('.product__modal').fadeOut('400');
        }
    });

    $(document).on('keyup', (e) => {
        if(e.keyCode == 27) {
            $('#mod').fadeOut('400');
            $('#popup').fadeOut('400');
            $('#call-pop').fadeOut('400');
            $('.sew-by-order-utp').fadeOut('400');
            $('.product__modal').fadeOut('400');
        }
    });

    $('#open-call-back').click(function(event) {
        $('#call-pop').fadeIn('400');
        $('#sew-by-call-modal').fadeIn('400');
    });

    $('.btn-close-modal, #closeModal').click(function(event) {
        event.preventDefault();
        $('#mod').fadeOut('400');
        $('#popup').fadeOut('400');
        $('#call-pop').fadeOut('400');
        $('.sew-by-order-utp').fadeOut('400');
        $('.product__modal').fadeOut('400');
    });

    $('#uipw-form_call_modal').on('submit', function (e) {
        e.preventDefault();

        $(this).find('.modal-check').removeClass('error');
        if($(this).find('input[name=personal]').prop('checked') == false) {
            $(this).find('.modal-check').addClass('error');
            return;
        }

        $(this).find('input[name=phone]').removeClass('error');
        if(completePhone === false) {
            $(this).find('input[name=phone]').addClass('error');
            return;
        }

        var url = $(this).attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: $(this).serialize(),
            success: function (result) {
                if (result.status == 1) {

                    $('#sew-by-call-modal .owner-feedbacks__success').removeClass('visuallyhidden');
                    $('#uipw-form_call_modal').hide();

					$(".uipw-modal_form_fields input[type=text], .uipw-modal_form_fields textarea").val('');//Очистить поля формы

                    if (typeof PwBackCallJs === 'function') {
                        PwBackCallJs();
                    }
                } else {
                    $.each(result.errors, function (field, error) {
                        $('#call_'+field+'_err').show();
                        $('#uipw-form_call_modal input[name='+field+']').addClass('error');
                    });
                }
            },
            dataType: 'json'
        });
    });
});