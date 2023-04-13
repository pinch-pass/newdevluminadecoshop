$(document).ready(function() {
	$('#iqitmegamenu-accordion li:has(ul)').each(function() {
		$(this).prepend('<div class="responsiveInykator"></div>');
	});

	$(".responsiveInykator").on("click", function() {
		if ($(this).parent().children('ul').hasClass('active')) {
			$(this).parent().children('ul').toggleClass('active');
		} else {
			$('#iqitmegamenu-accordion ul').removeClass('active');

			$(this).parent().children('ul').toggleClass('active');
		}

		
		// if (false == $(this).parent().next().is(':visible')) {
		// 	$('#iqitmegamenu-accordion > ul').addClass('active');
		// }
		// // if ($(this).text() == "+")
		// // 	$(this).text("-");
		// // else
		// // 	$(this).text("+");
		// $(this).parent().children('ul').removeClass('active');
	});

	$('#iqitmegamenu-accordion').detach().appendTo('#header');

	var menuLeft = document.getElementById('iqitmegamenu-accordion'),
		showLeftPush = document.getElementById('iqitmegamenu-shower'),
		menuoverlay = document.getElementById('cbp-spmenu-overlay'),
		body = document.body;

	classie.addClass(body, 'cbp-spmenu-body');

	$('#iqitmegamenu-shower, #mh-menu').click(function() {
		classie.toggle(showLeftPush, 'active');
		classie.toggle(body, 'cbp-spmenu-push-toright');
		classie.toggle(menuLeft, 'cbp-spmenu-open');
		classie.toggle(menuoverlay, 'cbp-spmenu-overlay-show');
	});

	$('#cbp-spmenu-overlay, #cbp-close-mobile').click(function() {
		classie.toggle(this, 'active');
		classie.toggle(body, 'cbp-spmenu-push-toright');
		classie.toggle(menuLeft, 'cbp-spmenu-open');
		classie.toggle(menuoverlay, 'cbp-spmenu-overlay-show');
	});

	$('#iqitmegamenu-accordion li:has(ul)').each(function() {
		var text = $(this).children('a').attr('title');
		$(this).children('ul').prepend('<li class="back-menu mobile"> <span class="back-icon"></span> <span class="icon-cat">'+text+'</span></li>');
	});

	$('.back-menu .back-icon').on('click', function() {
		console.log(1);
		$(this).parent().parent().toggleClass('active');
	})
});