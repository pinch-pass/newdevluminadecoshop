/*
 * 2007-2014 PrestaShop
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2014 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
//global variables
var responsiveflag = false;

$(document).ready(function() {
	highdpiInit();
	responsiveResize();
    storesOwl();
	$(window).resize(responsiveResize);
	if (navigator.userAgent.match(/Android/i)) {
		var viewport = document.querySelector('meta[name="viewport"]');
		viewport.setAttribute('content', 'initial-scale=1.0,maximum-scale=1.0,user-scalable=0,width=device-width,height=device-height');
		window.scrollTo(0, 1);
	}
	blockHover();
	if (typeof quickView !== 'undefined' && quickView)
		quick_view();
	dropDown();
	if (!!$.prototype.fancybox)
		$("a.iframe").fancybox({
			'type': 'iframe',
			'width': 600,
			'height': 600
		});

	if (typeof page_name != 'undefined' && !in_array(page_name, ['index', 'product'])) {
		bindGrid();

		$(document).on('change', '.selectProductSort', function(e) {
			if (typeof request != 'undefined' && request)
				var requestSortProducts = request;
			var splitData = $(this).val().split(':');
			if (typeof requestSortProducts != 'undefined' && requestSortProducts)
				document.location.href = requestSortProducts + ((requestSortProducts.indexOf('?') < 0) ? '?' : '&') + 'orderby=' + splitData[0] + '&orderway=' + splitData[1];
		});

		$(document).on('change', 'select[name="n"]', function() {
			$(this.form).submit();
		});

		$(document).on('change', 'select[name="manufacturer_list"], select[name="supplier_list"]', function() {
			if (this.value != '')
				location.href = this.value;
		});

		$(document).on('change', 'select[name="currency_payement"]', function() {
			setCurrency($(this).val());
		});
	}

	$(document).on('click', '.back', function(e) {
		e.preventDefault();
		history.back();
	});

	jQuery.curCSS = jQuery.css;
	if (!!$.prototype.cluetip)
		$('a.cluetip').cluetip({
			local: true,
			cursor: 'pointer',
			dropShadow: false,
			dropShadowSteps: 0,
			showTitle: false,
			tracking: true,
			sticky: false,
			mouseOutClose: true,
			fx: {
				open: 'fadeIn',
				openSpeed: 'fast'
			}
		}).css('opacity', 0.8);

	if (!!$.prototype.fancybox)
		$.extend($.fancybox.defaults.tpl, {
			closeBtn: '<a title="' + FancyboxI18nClose + '" class="fancybox-item fancybox-close" href="javascript:;"></a>',
			next: '<a title="' + FancyboxI18nNext + '" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
			prev: '<a title="' + FancyboxI18nPrev + '" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
		});
});

function highdpiInit() {
	if ($('.replace-2x').css('font-size') == "1px") {
		var els = $("img.replace-2x").get();
		for (var i = 0; i < els.length; i++) {
			src = els[i].src;
			extension = src.substr((src.lastIndexOf('.') + 1));
			src = src.replace("." + extension, "2x." + extension);

			var img = new Image();
			img.src = src;
			img.height != 0 ? els[i].src = src : els[i].src = els[i].src;
		}
	}
}

function storesOwl() {
    if( $('.stores__slide.owl-carousel').length > 0 ) {
        console.log('owl init');
        $('.stores__slide.owl-carousel').owlCarousel({
            autoplay: false,
            autoplayHoverPause: true,
            loop: false,
            smartSpeed: 1000,
            dots: true,
            nav: true,
            navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
            navClass: ['btn btn--rounded btn--carousel-nav prev', 'btn btn--rounded btn--carousel-nav next'],
            items:1
        });
    }
}

function responsiveResize() {
	if ($(document).width() <= 767 && responsiveflag == false) {
        $('#stickybar .search').appendTo('.iqitmegamenu-wrapper');
        $('.help').appendTo('.catalog');
		accordion('enable');
		accordionFooter('enable');
		responsiveflag = true;
	} else if ($(document).width() >= 768) {
		accordion('disable');
		accordionFooter('disable');
		responsiveflag = false;
	}
    if ($(document).width() <= 1200  && $(document).width() >= 768) {
        $('#stickybar .search').appendTo('.iqitmegamenu-wrapper');
        $('.help').appendTo('.helpes');
    } else if ($(document).width() >= 1200) {
        $('#stickybar .search').prependTo('.right-infos');
    }
}

function blockHover(status) {
	$(document).off('mouseenter').on('mouseenter', '.product_list.grid li.ajax_block_product .product-container', function(e) {

		if ('ontouchstart' in document.documentElement)
			return;
		if ($('body').find('.container').width() == 1170) {
			var pcHeight = $(this).parent().outerHeight();
			var pcPHeight = $(this).parent().find('.button-container').outerHeight() + $(this).parent().find('.comments_note').outerHeight() + $(this).parent().find('.functional-buttons').outerHeight();
			$(this).parent().addClass('hovered');
			$(this).parent().css('height', pcHeight + pcPHeight).css('margin-bottom', pcPHeight * (-1));
		}
	});

	$(document).off('mouseleave').on('mouseleave', '.product_list.grid li.ajax_block_product .product-container', function(e) {
		if ($('body').find('.container').width() == 1170)
			$(this).parent().removeClass('hovered').removeAttr('style');
	});
}

function quick_view() {
	$(document).on('click', '.quick-view:visible', function(e) {
		e.preventDefault();
		var url = this.rel;
		if (url.indexOf('?') != -1)
			url += '&';
		else
			url += '?';

		if (!!$.prototype.fancybox)
			$.fancybox({
				'padding': 0,
				'width': 1087,
				'height': 610,
				'type': 'iframe',
				'href': url + 'content_only=1'
			});
	});
}

function bindGrid() {
	var view = $.totalStorage('display');

	if (view && view != 'grid')
		display(view);
	else
		$('.display').find('li#grid').addClass('selected');

	$(document).on('click', '#grid', function(e) {
		e.preventDefault();
		display('grid');
	});

	$(document).on('click', '#list', function(e) {
		e.preventDefault();
		display('list');
	});
}

function display(view) {
	if (view == 'list') {
		$('ul.product_list').removeClass('grid').addClass('list row');
		$('.product_list > li').removeClass('col-xs-12 col-sm-6 col-md-4').addClass('col-xs-12');
		$('.product_list > li').each(function(index, element) {
			html = '';
			html = '<div class="product-container"><div class="row">';
			html += '<div class="left-block col-sm-4 col-md-4">' + $(element).find('.left-block1').html() + '</div>';
			html += '<div class="center-block col-sm-8 col-md-8">';
			html += '<div class="product-flags">' + $(element).find('.product-flags').html() + '</div>';
			html += '<div class="producth5title" itemprop="name">' + $(element).find('.producth5title').html() + '</div>';
			var rating = $(element).find('.comments_note').html(); // check : rating
			if (rating != null) {
				html += '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="comments_note">' + rating + '</div>';
			}
			html += '<p class="product-desc">' + $(element).find('.product-desc').html() + '</p>';
			var colorList = $(element).find('.color-list-container').html();
			if (colorList != null) {
				html += '<div class="color-list-container">' + colorList + '</div>';
			}
			var availability = $(element).find('.availability').html(); // check : catalog mode is enabled
			if (availability != null) {
				html += '<span class="availability">' + availability + '</span>';
			}
			html += '</div>';
			html += '<div class="right-block col-sm-8 col-md-8"><div class="right-block-content row">';
			var price = $(element).find('.content_price').html(); // check : catalog mode is enabled
			if (price != null) {
				html += '<div class="content_price col-sm-8 col-md-8">' + price + '</div>';
			}
			html += '<div class="button-container col-sm-8 col-md-8">' + $(element).find('.button-container').html() + '</div>';
			html += '<div class="functional-buttons clearfix col-sm-8">' + $(element).find('.functional-buttons').html() + '</div>';
			html += '</div>';
			html += '</div></div>';
			$(element).html(html);
		});
		$('.display').find('li#list').addClass('selected');
		$('.display').find('li#grid').removeAttr('class');
		$.totalStorage('display', 'list');
	} else {
		$('ul.product_list').removeClass('list').addClass('grid row');
		$('.product_list > li').removeClass('col-xs-12').addClass('col-xs-12 col-sm-6 col-md-4');
		$('.product_list > li').each(function(index, element) {
			html = '';
			html += '<div class="product-container">';
			html += '<div class="left-block">' + $(element).find('.left-block1').html() + '</div>';
			html += '<div class="right-block">';
			html += '<div class="product-flags">' + $(element).find('.product-flags').html() + '</div>';
			html += '<div class="producth5title" itemprop="name">' + $(element).find('.producth5title').html() + '</div>';
			var rating = $(element).find('.comments_note').html(); // check : rating
			if (rating != null) {
				html += '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="comments_note">' + rating + '</div>';
			}
			html += '<p itemprop="description" class="product-desc">' + $(element).find('.product-desc').html() + '</p>';
			var price = $(element).find('.content_price').html(); // check : catalog mode is enabled
			if (price != null) {
				html += '<div class="content_price">' + price + '</div>';
			}
			html += '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="button-container">' + $(element).find('.button-container').html() + '</div>';
			var colorList = $(element).find('.color-list-container').html();
			if (colorList != null) {
				html += '<div class="color-list-container">' + colorList + '</div>';
			}
			var availability = $(element).find('.availability').html(); // check : catalog mode is enabled
			if (availability != null) {
				html += '<span class="availability">' + availability + '</span>';
			}
			html += '</div>';

			html += '</div>';
			$(element).html(html);
		});
		$('.display').find('li#grid').addClass('selected');
		$('.display').find('li#list').removeAttr('class');
		$.totalStorage('display', 'grid');
	}
}

function dropDown() {
	elementClick = '#header .current';
	elementSlide = 'ul.toogle_content';
	activeClass = 'active';

	$(elementClick).on('click', function(e) {
		e.stopPropagation();
		var subUl = $(this).next(elementSlide);
		if (subUl.is(':hidden')) {
			subUl.slideDown();
			$(this).addClass(activeClass);
		} else {
			subUl.slideUp();
			$(this).removeClass(activeClass);
		}
		$(elementClick).not(this).next(elementSlide).slideUp();
		$(elementClick).not(this).removeClass(activeClass);
		e.preventDefault();
	});

	$(elementSlide).on('click', function(e) {
		e.stopPropagation();
	});

	$(document).on('click', function(e) {
		e.stopPropagation();
		var elementHide = $(elementClick).next(elementSlide);
		$(elementHide).slideUp();
		$(elementClick).removeClass('active');
	});
}

function accordionFooter(status) {
    if (status == 'enable') {
        $('#footer .footer-tog ').on('click', function() {
            $(this).toggleClass('active').find('.toggle-footer').stop().slideToggle('medium');
        })
        $('#footer').addClass('accordion').find('.toggle-footer').slideUp('fast');
    } else {
        $('#footer .footer-tog').removeClass('active').off().find('.toggle-footer').removeAttr('style').slideDown('fast');
        $('#footer').removeClass('accordion');
    }
}

function accordion(status) {
	leftColumnBlocks = $('#left_column');
	if (status == 'enable') {
		$('#right_column .block .title_block, #left_column .block .titlebordrtext1, #left_column #newsletter_block_left h4').on('click', function() {
			$(this).toggleClass('active').parent().find('.block_content').stop().slideToggle('medium');
		})
		$('#right_column, #left_column').addClass('accordion').find('.block .block_content').slideUp('fast');
	} else {
		$('#right_column .block .title_block, #left_column .block .title_block, #left_column #newsletter_block_left h4').removeClass('active').off().parent().find('.block_content').removeAttr('style').slideDown('fast');
		$('#left_column, #right_column').removeClass('accordion');
	}
}

$(document).on('click', '.stores__nav__link.list', function() {
    $('.stores__nav__link.list').addClass('stores__nav__link--active');
    $('.stores__nav__link.map').removeClass('stores__nav__link--active');

    $('.stores__list').show();
    $('.stores__map').hide();
})
$(document).on('click', '.stores__nav__link.map', function() {
    $('.stores__nav__link.list').removeClass('stores__nav__link--active');
    $('.stores__nav__link.map').addClass('stores__nav__link--active');
    $('.stores__list').hide();
    $('.stores__map').show();
})

$(document).on('click', '.layered_subtitle_heading', function() {
	$(this).next('.layered_filter_ul').toggle(display);
})

$(document).on('click', '.search-mobile', function() {
	$(this).toggleClass('active');
	$('.new-search').toggleClass('active');
})

$(document).on('click', '.mob-open__filter-link, .mob-filter__back', function(e) {
	e.preventDefault();

	$('#layered_block_left').toggleClass('active');
})

$(document).on('click', '#more-infos a', function(e) {
	e.preventDefault();

	if ($('#editorial_block_center').hasClass('active')) {
		$(this).text('Развернуть');
	} else {
		$(this).text('Свернуть');
	}

	$('#editorial_block_center').toggleClass('active');
})

$(document).on('click', '#iqitmegamenu-accordion .hide-me', function(e) {
	$('#iqitmegamenu-accordion').toggleClass('cbp-spmenu-open');
	$('.menus-mobile').toggleClass('active');
})

$(document).ready(function() {
	$('#delivery_phone_mobile').mask('+7(999)999-99-99');

	// if (canUseWebP()) {
	// 	$('#page').addClass('webp');
	// }
});


// function canUseWebP() {
//     var elem = document.createElement('canvas');

//     if (!!(elem.getContext && elem.getContext('2d'))) {
//         // was able or not to get WebP representation
//         return elem.toDataURL('image/webp').indexOf('data:image/webp') == 0;
//     }

//     // very old browser like IE 8, canvas not supported
//     return false;
// }

// $(window).resize(function() {
// 	var width = window.innerWidth;
// 	var a = document.getElementById('advantage');
// 	var b = document.getElementById('htmlcontent_top')

// 	if (width < 576) {
// 		swapNodes(a, b);
// 	}
// });

// // var width = window.innerWidth;
// // var a = document.getElementById('advantage');
// // var b = document.getElementById('htmlcontent_top')

// // if (width < 576) {
// // 	swapNodes(a, b);
// // }

// function swapNodes(a, b) {
// 	var aparent = a.parentNode;
// 	var asibling = a.nextSibling === b ? a : a.nextSibling;
// 	b.parentNode.insertBefore(a, b);
// 	aparent.insertBefore(b, asibling);
// }