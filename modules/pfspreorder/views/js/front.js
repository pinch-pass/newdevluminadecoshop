/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
$(document).ready(function () {
    $("#form-1").submit(function() {
  
        var data = $('#form-1').serialize();
        $.ajax({
            type: "POST",
            url: ajax_link,
            data: data,
            dataType: 'json',
            success: function (data) {
               
                if (data.status == 'success') {
                    // everything went alright, submit
                   // $('#form-1').submit();
                    $('#sew-by-order-modal .owner-feedbacks__success').removeClass('visuallyhidden');
                    $('#sew-by-order-modal .sew-by-order__form').addClass('visuallyhidden');
                } else if (data.status == 'info') {
                    return false;
                }
            }
        });
        return false;
    });

    $("#form-2").submit(function() {
      
        var data = $('#form-2').serialize();
        $.ajax({
            type: "POST",
            url: ajax_link,
            data: data,
            dataType: 'json',
            success: function (data) {
                if (data.status == 'success') {
                    // everything went alright, submit
                    // $('#form-1').submit();
                    $('#mod').fadeIn('400');
                    $('#report-on-stock').fadeIn('400');
                    $('#report-on-stock .owner-feedbacks__success').removeClass('visuallyhidden');

                } else if (data.status == 'info') {
                    return false;
                }
            }
        });
        return false;
    });

    $("#form-3").submit(function(e){
        e.preventDefault();

        $(this).find('.modal-check').removeClass('error');
        if($(this).find('input[name=personal]').prop('checked') == false) {
            $(this).find('.modal-check').addClass('error');
            return;
        }

        var data = $('#form-3').serialize();
        $.ajax({
            type: "POST",
            url: ajax_link_mail,
            data: data,
            dataType: 'json',
            success: function (data) {
                if (data.status == 'success') {
                    $('#sew-by-call-modal .owner-feedbacks__success').removeClass('visuallyhidden');
                    $('#sew-by-call-modal .sew-by-order__form').addClass('visuallyhidden');
                } else if (data.status == 'info') {
                    return false;
                }
            }
        });
        return false;
    });

});

$(document).ready(function () {
    $('#form-3 input[name=phone]').mask('+7 (999)-999-99-99');
    $('#form-3 input[name=name]').mask('SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS', {'translation': {
            S: {pattern: /[A-Za-zА-Яа-я]/}
        }
    });

    // Modals
    $(document).on('keyup', (e) => {
        if(e.keyCode == 27) {
            $('#mod').fadeOut('400');
            $('#popup').fadeOut('400');
            $('#call-pop').fadeOut('400');
            $('.sew-by-order-utp').fadeOut('400');
            $('.product__modal').fadeOut('400');
        }
    });

    $(document).on('click', '#call-pop #mod-bg-call', (e) => {
        console.log(e.target);
        if($(e.target).is('#mod-bg-call')) {
            $('#mod').fadeOut('400');
            $('#popup').fadeOut('400');
            $('#call-pop').fadeOut('400');
            $('.sew-by-order-utp').fadeOut('400');
            $('.product__modal').fadeOut('400');
        }
    });
    $('#sew-by-losses-link').click(function(event) {
        event.preventDefault();
        $('#sew-by-order-modal').fadeIn('400');
        $('#mod').fadeIn('400');
        

       // setModalPosition();
    });
    $('.dostavkin').click(function(event) {
            $('#dostavkin').fadeIn('400');
            $('#sew-by-utp').fadeIn('400');  
            $('#popup').fadeIn('400');
        //    setModalPosition();
    });
    $('.showrooms').click(function(event) {
        $('#showrooms').fadeIn('400');
        $('#sew-by-utp').fadeIn('400');
        $('#popup').fadeIn('400');
    //    setModalPosition();
    });
    $('.vozvrats').click(function(event) {
        $('#vozvrats').fadeIn('400');
        $('#sew-by-utp').fadeIn('400');
        $('#popup').fadeIn('400');
    //    setModalPosition();
    });
    $('.call-back').click(function(event) {
        console.log('callme');
        $('#call-pop').fadeIn('400');
        $('#sew-by-call-modal').fadeIn('400');
    });
    $('#report-on-stock-link').click(function(event) {
        event.preventDefault();
        $('#mod').fadeIn('400');
        $('#report-on-stock').fadeIn('400');

       // setModalPosition();
    });

    $('.btn-close-modal').click(function(event) {
        event.preventDefault();
        $('#mod').fadeOut('400');
        $('#popup').fadeOut('400');
        $('#call-pop').fadeOut('400');
        $('.sew-by-order-utp').fadeOut('400');
        $('.product__modal').fadeOut('400');
    });

    $('#secondaryButton').click(function(){
        $("#primaryButton").click();
    });
    $('#secondaryButtons').click(function(){
        $("#primaryButtons").click();
    });
    $('#thirdButton').click(function(){
        $("#primaryButtones").click();
    });
    function setModalPosition() {

        if($(window).scrollTop() > 180) {
            var pos = $(window).scrollTop();
            $('.product__modal').offset({top: pos + 80});
        } else {
            $('.product__modal').offset({top: 180});
        }
    }
})
$(document).on('click', '#mod-bg', function(e){
    $('.btn-close-modal').click();
});