/**
*  2007-2020 PrestaShop
*
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

var af_product_list_selector = is_17 ? '#js-product-list' : '.'+af_product_list_class,
	locked_class = af_classes['icon-lock'],
	unlocked_class = af_classes['icon-unlock-alt'],
	unlocked_selector = '.'+unlocked_class.replace(/ /g, '.'),
	times_class = af_classes['u-times'],
	pagination_class = af_classes['pagination'],
	product_count_class = af_classes['product-count'],
	af_primary_filter = {trigger: '', url: ''},
	customThemeActions = {
		documentReady: function() {},
		updateContentAfter: function(jsonData){}
	};
if (!is_17) {
	var	pagination_holder_id = af_ids['pagination'],
		pagination_bottom_holder_id = af_ids['pagination_bottom'];
}

var af = {
	defineVariables: function() {
		$.extend(af, {
			// basic varibales
			blockAjax: false, resizeTimer: false, mouseLeaveTimer: false, qsTimer: false, cosCounter: 1,
			// misc variables
			isCompact: false,
			autoScroll: false,
			dimZeroMatches: parseInt($('#af_dim_zero_matches').val()),
			hideZeroMatches: parseInt($('#af_hide_zero_matches').val()),
			showCounters: parseInt($('#af_count_data').val()),
			includeGroup: parseInt($('#af_include_group').val()),
			dynamicParams: {
				f: parseInt($('#af_url_filters').val()),
				s: parseInt($('#af_url_sorting').val()),
				p: parseInt($('#af_url_page').val()),
			},
			isHorizontal: $('#af_layout').val() == 'horizontal',
			productItemSelector: '.'+(is_17 ? af_classes['js-product-miniature'] : af_classes['ajax_block_product']),
			noItemsClass: 'no-available-items',
			popstateURL: window.location.href,
			// basic $elements
			$listWrapper: $('.af_pl_wrapper'), // redefined later in setWrapper()
			$filterBlock: $('#amazzing_filter'),
			$selectedFilters: $('.selectedFilters'),
			$pageInput: $('#af_page'),
			$viewBtn: $('.viewFilteredProducts'),
			$dynamicContainer: $('#products').first(),
		});
		if (!af.$dynamicContainer.length) {
			af.$dynamicContainer = is_17 ? $('#content').length ? $('#content') : $('#main') : $('#center_column');
		}
	},
	documentReady: function() {
		af.defineVariables();
		customThemeActions.documentReady();
		af.setWrapper();
		af.bindEvents();
		af.toggleCompactView();
		af.prepareSliders();
		af.prepareCutOff();
		af.adjustFolderedStructure();
		af.prepareLoadMoreIfRequired();
		if (af.$listWrapper.length) {
			af.updateSelectedFilters();
			if ($('.customer-filter-option').length) {
				af.updateUrlAndVerifyParams();
			}
		}
		$('.af_filter').not('.has-slider').each(function() {
			// temporarily used for cases where there are checked options without matches
			af.checkAvailableItems($(this), $(this).hasClass('type-3'));
		});
	},
	prepareLoadMoreIfRequired: function() {
		if (load_more && af.$listWrapper.length) {
			$('.dynamic-product-count').html(af_product_count_text);
			$('.dynamic-loading.next').removeClass('hidden').insertAfter(af.$listWrapper);
			$('.dynamic-loading.prev').removeClass('hidden').insertBefore(af.$listWrapper);
			if (!show_load_more_btn) {
				$('.loadMore.next').addClass('hidden');
			}
			$('.loadMore').on('click', function(){
				var $parent = $(this).parent(), p = parseInt(af.$pageInput.val());
				if ($parent.hasClass('next')) {
					p++;
				} else { // prev
					$(this).data('p', $(this).data('p') - 1);
					if ($(this).data('p') < 2) {
						$parent.addClass('hidden');
					}
				}
				$parent.addClass('loading');
				af.$pageInput.val(p).change();
			});
			af.rememberScrollPosition();
			if ($('#af_p_type').val() == 3) {
				af.activateInfiniteScroll();
			}
		}
	},
	rememberScrollPosition: function() {
		$(window).on('unload', function() {
		// $(document).on('click', '.variant-links a', function(e){ e.preventDefault(); // debug
			var $item = $(document.activeElement).closest(af.productItemSelector);
			if ($item.length) { // user clicked on product item and goes to to product page
				var $currentItems = af.$listWrapper.find(af.productItemSelector),
					prevNum = $currentItems.index($item),
					npp = parseInt($('#af_nb_items').val()),
					scrolledPage = Math.floor(prevNum / npp),
					initialPage = $('.loadMore.prev').data('p') || 1,
					rememberPage = scrolledPage + initialPage,
					elPositionInPage = prevNum - (scrolledPage * npp),
					requiredScrollPosition = $currentItems.eq(elPositionInPage).offset().top - ($(window).height() / 2) + ($item.height() / 2);
				af.$pageInput.val(rememberPage);
				af.updateUrlAndVerifyParams(); // page param is updated in URL and it will be loaded when user clicks back
				window.scrollTo(0, requiredScrollPosition); // scroll position is restored when user clicks back
			}
		});
	},
	bindEvents: function() {
		af.filterEvents();
		af.themeEvents();
		af.browserEvents();
	},
	filterEvents: function() {
		af.$filterBlock.on('click', 'a[href="#"]', function(e){
			e.preventDefault();
		}).on('change', 'input, select', function() {
			if ($(this).data('notrigger')) {
				return;
			}
			var trigger = $(this).attr('id'),
				type = $(this).attr('type') || $(this).prop('tagName').toLowerCase(),
				$parent = $(this).closest('.af_filter'),
				updateList = !af.$viewBtn.data('active') || $(this).parent().hasClass('hidden_inputs');
			if ($parent.length) {
				trigger = $parent.attr('data-trigger');
				if (!$parent.hasClass('special') && !$parent.hasClass('has-slider')){
					af_primary_filter['trigger'] = trigger;
					af_primary_filter['url'] = $parent.attr('data-url');
				}
			}
			if (type == 'checkbox' || type == 'radio') {
				if (type == 'radio') {
					$parent.find('li.active').removeClass('active');
				}
				$(this).closest('li').toggleClass('active', $(this).prop('checked'));
			} else if (type == 'select' && !$(this).closest('.selector-with-customer-filter').hasClass('hidden') &&
				$(this).find('option[value="'+$(this).val()+'"]').hasClass('customer-filter')) {
				$(this).closest('.af_filter').find('.customer-filter-label').click();
				return;
			}
			if (trigger != 'af_page') {
				af.$pageInput.val(1);
			}
			if (!af.blockAjax) {
				if (updateList) {
					af.updateSelectedFilters();
					af.updateUrlAndVerifyParams();
				}
				af.loadProducts(trigger, updateList);
			}
		}).on('change', '.color_attribute input', function() {
			$(this).closest('.color_attribute').toggleClass('active', $(this).prop('checked'));
		}).on('click', '.customer-filter-label', function() {
			$(this).toggleClass('unlocked');
			var locked = !$(this).hasClass('unlocked'),
				iconClass = locked ? locked_class : unlocked_class,
				$input = $(this).find('input[type="hidden"]');
			$(this).find('a').first().attr('class', iconClass);
			if ($input.length) {
				var name = locked ? $input.data('name') : 'nosubmit';
				$input.attr('name', name).change();
			} else { // selects
				var val = locked ? $('option[id="'+$(this).data('id')+'"]').val() : 0;
				$(this).toggleClass('hidden-name', !locked).next().toggleClass('hidden', locked)
				.find('select').val(val).change();
			}
		});

		af.$selectedFilters.on('click', 'a', function(e){
			e.preventDefault();
			var $parentRow = $(this).parent(),
				$groupBlock = $('.af_filter[data-url="'+$parentRow.data('group')+'"]');
			if ($(this).hasClass('close')){
				if ($groupBlock.hasClass('type-1') || $groupBlock.hasClass('type-2')) {
					var $input = $groupBlock.find('input[data-url="'+$parentRow.data('url')+'"]');
					$input.prop('checked', false).change();
					if ($.fn.uniform) {
						$input.parent().removeClass('checked');
					}
				} else if ($groupBlock.hasClass('type-3')) {
					$groupBlock.find('select').val(0).change();
				} else if ($groupBlock.hasClass('has-slider')) {
					var type = $groupBlock.data('trigger');
					$('#'+type+'_from').val($('#'+type+'_min').val());
					$('#'+type+'_to').val($('#'+type+'_max').val()).change();
				}
			} else if ($(this).hasClass('all')){
				af.blockAjax = true;
				af.$selectedFilters.find('.cf').find('a').not(unlocked_selector).click();
				af.blockAjax = false;
				$('#af_nb_items').change(); // NOTE: if it is changed right after clicking on "view" button, list is updated
			} else if ($parentRow.hasClass('customer-filter-option')) {
				if ($groupBlock.hasClass('type-3')) { // select
					$groupBlock.find('.customer-filter-label').click();
				} else { // checkbox
					$groupBlock.find('.customer-filter[data-url="'+$parentRow.data('url')+'"]')
					.closest('.customer-filter-label').click();
				}
			}
		});

		if (af.dimZeroMatches && !af.hideZeroMatches) { // block visible checkboxes/radio with 0 matches
			af.$filterBlock.on('click', 'input.af.checkbox, input.af.radio', function(e){
				// prop checked becomes true for unchecked inputs, right after click
				if ($(this).prop('checked') && $(this).closest('li').hasClass('no-matches')) {
					e.preventDefault();
					$(this).prop('checked', false);
					if ($.fn.uniform) {
						$(this).parent().removeClass('checked').parent().removeClass('focus');
						if ($(this).hasClass('radio')) {
							var $parentBlock = $(this).closest('.af_filter'),
								url = $('.cf[data-group="'+$parentBlock.data('url')+'"]').data('url');
							// keep checked styles on radioboxes that were checked before
							$parentBlock.find('input[data-url="'+url+'"]').parent().addClass('checked').parent().addClass('focus');
						}
					}
				}
			});
		}

		// view btn
		af.$viewBtn.on('click', function(e){
			e.preventDefault();
			if ($(this).hasClass('loading')) {
				return;
			}
			$('#af_orderBy').change();
			if (af.isCompact) {
				$('.af-compact-overlay').click();
			}
		});

		// foldered
		$('.af-toggle-child').on('click', function() {
			$(this).closest('.af-parent-category').toggleClass('open');
		});

		// toggleable content
		$('.af_subtitle').not('.no-toggle').on('click', function(e) {
			e.preventDefault();
			var $filter = $(this).closest('.af_filter');
			if (!$filter.hasClass(af.noItemsClass)) {
				$filter.toggleClass('closed');
				if (!$filter.hasClass('closed')) {
					if ($filter.hasClass('type-3')) {
						try{$filter.find('.af-select').uniform()}catch(err){};
					} else if ($filter.hasClass('has-slider')) {
						af.updateSlider($filter.data('trigger'));
					}
					if (af.isCompact) {
						$filter.siblings('.af_filter').addClass('closed');
					} else if (af.isHorizontal) {
						af.autoCloseFilterContent($filter);
					}
				}
			}
			af.prepareCutOff();
		});
		af.quickSearch();
	},
	quickSearch: function() {
		af.$filterBlock.on('keyup', '.qsInput', function() {
			var $qsInput = $(this);
			clearTimeout(af.qsTimer);
			af.qsTimer = setTimeout(function() {
				var value = $qsInput.val().toLowerCase(),
					$options = $qsInput.closest('.af_filter_content').find('li');
				$options.removeClass('qs-hidden');
				if (value.length > 2) {
					$options.each(function() {
						if ($(this).children('label').children('.name').text().toLowerCase().indexOf(value) === -1) {
							$(this).addClass('qs-hidden');
						}
					});
				}
				$qsInput.toggleClass('has-value', value.length > 0).
				siblings('.qs-no-matches').toggleClass('hidden', !!$options.not('.qs-hidden, .no-matches').length);
				if ($qsInput.data('tree')) {
					$options.removeClass('qs-half-hidden').filter('.af-parent-category.qs-hidden').each(function() {
						if ($(this).find('li').not('.qs-hidden').length) {
							$(this).addClass('qs-half-hidden open');
						}
					});
				}
			}, 200);
		});
	},
	autoCloseFilterContent: function($filter) {
		$filter.off('mouseenter mouseleave').on('mouseenter', function() {
			clearTimeout(af.mouseLeaveTimer);
		}).on('mouseleave', function() {
			af.mouseLeaveTimer = setTimeout(function() {
				if (!$filter.hasClass('closed')) {
					$filter.find('.af_subtitle').click();
				}
				$filter.off('mouseenter mouseleave');
			}, 1000);
		});
		setTimeout(function() {
			af.onClickOutSide(
				$filter.find('.af_filter_content'),
				function() {$filter.addClass('closed');}
			);
		}, 10);
	},
	onClickOutSide: function($el, action) {
		var identifier = af.cosCounter++;
		$(document).off('click.'+identifier).on('click.'+identifier, function(e) {
			if (!$el.is(e.target) && $el.has(e.target).length === 0) {
				action();
				$(document).off('click.'+identifier);
			}
		});
	},
	themeEvents: function() {
		$(document).off('change', 'select[name="n"]').on('change', 'select[name="n"]', function() {
			$('#af_nb_items').val($(this).val()).change();
		});
		if (is_17) {
			$('body').off('click', '.js-search-link').on('click', '.select-list.js-search-link', function(e) {
				e.preventDefault();
				$(this).addClass('current').siblings().removeClass('current');
				// todo: consider cases when "order=" is not present in href
				var value = $(this).attr('href').split('order=')[1].split('&')[0].split('.');
					orderBy = value[1],
					orderWay = value[2],
					sortingName = $(this).text(),
					$title = $(this).closest('.products-sort-order').find('.select-title'),
					$htmlElementsInTitle = $title.find('*');
				af.applySorting(orderBy, orderWay);
				$title.html(sortingName).append($htmlElementsInTitle);
			});
		} else {
			$(document).off('change', '.selectProductSort').on('change', '.selectProductSort', function(e) {
				var splitted = $(this).val().split(':'),
					orderBy = splitted[0],
					orderWay = splitted[1];
				af.applySorting(orderBy, orderWay);
			});
		}
		$(document).on('click','.'+pagination_class+' a', function(e) {
			e.preventDefault();
			var page = 1;
			if ($(this).attr('href').indexOf('?') > -1) {
				var params = af.unserialize($(this).attr('href').split('?')[1], false);
				if (page_link_rewrite_text in params && params[page_link_rewrite_text] > 1) {
					page = params[page_link_rewrite_text];
				}
			}
			af.$pageInput.val(page).change();
		}).on('submit', '.showall', function(e) {
			e.preventDefault();
			var num = $(this).find('input[name="n"]').val(),
				$nSelect = $('select[name="n"]');
			if (!$nSelect.find('option[value="'+num+'"]').length) {
				var maxNum = $nSelect.find('option').last().val();
				if (parseInt(maxNum) >= parseInt(num)) {
					num = maxNum;
				} else {
					var newOptionHTML = '<option value="'+num+'">'+num+'</option>';
					$nSelect.append(newOptionHTML);
					try{$nSelect.uniform()}catch(err){};
				}
			}
			$nSelect.val(num).change();
		});
	},
	applySorting: function (orderBy, orderWay) {
		$('#af_orderBy').val(orderBy);
		$('#af_orderWay').val(orderWay).change();
	},
	prepareCompactView: function() {
		if ($('.af-compact-overlay').hasClass('ready')) {
			return;
		}
		// if ($('.compact-toggle.external').length) {
		// 	af.$filterBlock.find('.compact-toggle').remove();
		// }
		$('.compact-toggle, .af-compact-overlay').on('click', function(e){
			e.preventDefault();
			var $body = $('body');
			if (!$body.hasClass('show-filter')) {
				$body.data('scroll', $(window).scrollTop());
			}
			$body.toggleClass('show-filter');
			if ($body.hasClass('show-filter')) {
				$body.css('top', '-'+$body.data('scroll')+'px'); // block scrolling to top because of position: fixed
			} else {
				$body.css('top', '');
				window.scrollTo(0,+$body.data('scroll'));
			}
		}).addClass('ready');
		if ('ontouchstart' in document.documentElement) {
			var swipeThreshold = 75, xStart = 0, xEnd = 0,
				compactLeft = af.$filterBlock.hasClass('compact-offset-left');
			af.$filterBlock.on('touchstart', function (e) {
				xStart = e.originalEvent.touches[0].clientX;
				xEnd = xStart;
			}).on('touchmove',function (e) {
				xEnd = e.originalEvent.touches[0].clientX;
			}).on('touchend',function (e) {
				var diff = compactLeft ? xStart - xEnd : xEnd - xStart;
				if (diff > swipeThreshold && // detect swipe towards edge
					!$(e.target).closest('.af_filter').not('.closed').hasClass('has-slider')) { // no swiping on sliders
					$('body').removeClass('show-filter');
				} // TODO: else detect swipe from edge to open filter panel
			});
		}
	},
	toggleCompactView: function() {
		var isCompactBefore = af.isCompact;
		af.isCompact = af.$filterBlock.css('position') == 'fixed';
		af.autoScroll = af.isCompact ? 1 : parseInt($('#af_autoscroll').val());
		if (isCompactBefore != af.isCompact) {
			$('body').toggleClass('has-compact-filter', af.isCompact);
			var $afBlocks = af.$filterBlock.find('.af_filter'), $btnHolder = af.$filterBlock.find('.btn-holder');
			if (af.isCompact) {
				af.prepareCompactView();
				$afBlocks.filter('.closed').addClass('cl-orig');
				$afBlocks.filter('.foldered').addClass('fd-orig');
				$afBlocks.addClass('closed').filter('.folderable').addClass('foldered');
				af.$filterBlock.removeClass('horizontal-layout').before('<span class="af-orig hidden"></span>').appendTo('body');
				$('.af-compact-overlay').appendTo('body');
				$btnHolder.appendTo(af.$filterBlock); // avoid position:absolute + -webkit-overflow-scrolling: touch;
				setTimeout(function() {
					af.$filterBlock.addClass('animation-ready');
					if (typeof accordion == 'function') {af.$filterBlock.find('.block_content').stop().attr('style','');}
				}, 500);
			} else {
				af.$filterBlock.removeClass('animation-ready').toggleClass('horizontal-layout', af.isHorizontal);
				$('.af-orig').before(af.$filterBlock).before($('.af-compact-overlay')).remove();
				$btnHolder.insertAfter('#af_form'); // move it to original position for compatibility with accordion()
				$afBlocks.each(function(){
					$(this).toggleClass('closed', $(this).hasClass('cl-orig'))
					.toggleClass('foldered', $(this).hasClass('fd-orig'));
					if ($(this).hasClass('has-slider') && !$(this).hasClass('closed')) {
						af.updateSlider($(this).data('trigger'));
					}
				});
			}
			af.prepareCutOff();
		}
		af.toggleViewBtn();
	},
	toggleViewBtn: function() {
		var showBtn = $('#af_reload_action').val() == 2 || af.isCompact;
		af.$viewBtn.toggleClass('hidden', !showBtn).data('active', showBtn);
	},
	setWrapper: function() {
		if (!af.$listWrapper.length) {
			$(af_product_list_selector).first().wrap('<div class="af_pl_wrapper"></div>');
			af.$listWrapper = $('.af_pl_wrapper');
			if ($('#af_sf_position').val() == 1) {
				var $before = is_17 ? $('#products').first() : $('.sortPagiBar').first();
				af.$selectedFilters.addClass('inline').insertBefore($before);
			}
		}
	},
	loadProducts: function(trigger, updateList) {
		if (af.blockAjax) {
			return;
		}
		if (updateList && !$('.dynamic-loading').hasClass('loading')) {
			af.$listWrapper.animate({'opacity': 0.3}, 350);
		}
		if (af.$viewBtn.data('active')) {
			af.$viewBtn.addClass('loading');
			if (!updateList) {
				af.$viewBtn.addClass('btn-bounce').find('.af-total-count').html('...');
			}
		}
		if (af.dimZeroMatches) {
			$('option:selected:disabled').prop('disabled', false); // submit values from selected disabled options
		}
		var params = $('#af_form').serialize()+'&primary_filter='+af_primary_filter['trigger'];
		if (!updateList) {
			params += '&nb_items=0';
		} else if (!af.$listWrapper.length) {
			if (!is_17 && page_name != 'index') {
				window.location.reload();
				return;
			}
			params += '&layout_required=1';
			af.$dynamicContainer.animate({'opacity': 0.3}, 350);
		}
		if (load_more) {
			var $prevLoader = $('.dynamic-loading.prev');
			if (trigger == 'af_page') {
				if (!$prevLoader.length) {
					params += '&page_from=1';
				} else if ($prevLoader.hasClass('loading')) {
					params += '&page='+$('.loadMore.prev').data('p')+'&page_to='+af.$pageInput.val();
				} else {
					params += '&page_from='+$('.loadMore.prev').data('p');
				}
			} else {
				$prevLoader.remove();
			}
		}
		$.ajax({
			type: 'POST',
			url: af_ajax_path,
			dataType : 'json',
			data: {
				params: params,
				current_url: window.location.href,
				trigger: trigger,
			},
			success: function(r) {
				// console.dir(r);
				af.updateContent(r, trigger, updateList);
				if (af.$viewBtn.data('active')) {
					af.$viewBtn.removeClass('loading btn-bounce');
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	},
	updateContent: function(jsonData, trigger, updateList) {
		// var af_timeStart = new Date().getTime()/1000;
		if (updateList) {
			if ('layout' in jsonData) {
				af.updateListLayout(jsonData.layout);
			}
			af.updateProductList(jsonData, trigger);
			if (af.autoScroll && (!load_more || trigger != 'af_page')) {
				af.autoscrollToTopOfTheList();
			}
		}
		if (trigger != 'af_page') {
			$('.af-total-count').html(parseInt(jsonData.products_num));
			$('.'+af_classes['heading-counter']).html(af.utf8_decode(jsonData.product_total_text));
			af.updateFilteringBlocks(jsonData);
		}
		// var af_timeEnd = new Date().getTime()/1000;
		// af_timeEnd = af_timeEnd - af_timeStart
		// console.dir('all elements updated: '+af_timeEnd);
	},
	updateProductList: function(jsonData, trigger) {
		if (load_more && trigger == 'af_page') {
			var $result = $('<div>'+af.utf8_decode(jsonData.product_list_html)+'</div>'),
				$items = $result.find(af.productItemSelector);
			if ($('.dynamic-loading.prev').hasClass('loading')) {
				af.$listWrapper.find(af.productItemSelector).first().before($items);
			} else {
				af.$listWrapper.find(af.productItemSelector).last().after($items);
			}
			$('.dynamic-loading').removeClass('loading');
			if (is_17 && !$('#js-product-list-top').html()) {
				$('#js-product-list-top').replaceWith(af.utf8_decode(jsonData.product_list_top_html));
			}
		} else {
			$(af_product_list_selector).replaceWith(af.utf8_decode(jsonData.product_list_html));
			if (is_17) {
				$('#js-product-list-top').replaceWith(af.utf8_decode(jsonData.product_list_top_html));
				$('#js-product-list-bottom').replaceWith(af.utf8_decode(jsonData.product_list_bottom_html));
			} else {
				$('.'+product_count_class).remove();
				$('#'+pagination_holder_id).replaceWith(af.utf8_decode(jsonData.pagination_html));
				$('#'+pagination_bottom_holder_id).replaceWith(af.utf8_decode(jsonData.pagination_bottom_html));
			}
		}
		if (load_more) {
			var $countContainer = is_17 ? $('.dynamic-product-count') : $('.'+product_count_class);
			$countContainer.html(af.utf8_decode(jsonData.product_count_text));
			$('.loadMore.next').toggleClass('hidden', jsonData.hide_load_more_btn);
		}
		if (!is_17 && !jsonData.products_num) {
			$('#'+pagination_holder_id+', #'+pagination_bottom_holder_id+', .'+product_count_class).addClass('hidden');
		}
		var animationTime = is_17 ? 500 : 1000;
		$('.af_pl_wrapper').animate({'opacity': 1}, animationTime);
		af.updateListAfter(jsonData);
	},
	updateListAfter: function(jsonData) {
		if (typeof display == 'function' && $.totalStorage
			&& $.totalStorage('display') && $.totalStorage('display') != 'grid') {
			display($.totalStorage('display'));
		}
		try {
			if (is_17) {
				// prestashop.emit('updateProductList'); //todo: add configurable action
			} else {
				blockHover(); // defined in global.js
				compareButtonsStatusRefresh(); // defined in product-comparison.js
				totalCompareButtons(); //  defined in product-comparison.js
			}
		} catch(err) {};
		customThemeActions.updateContentAfter(jsonData);
		if (typeof updateContentAfter == 'function') updateContentAfter(jsonData); // retro compatibility
	},
	updateFilteringBlocks: function(jsonData) {
		//checkboxes, radioboxes
		$('input.af.checkbox, input.af.radio').each(function() {
			var id = $(this).attr('id'),
				$li = $(this).closest('li'),
				$filter_block = $li.closest('.af_filter');
			if (jsonData.count_data[id]) {
				$li.removeClass('no-matches');
				if (af.showCounters) {
					$li.find('.count').first().html(jsonData.count_data[id]);
				}
			} else if (!$li.hasClass('no-matches')){
				$li.addClass('no-matches');
				if (af.showCounters) {
					$li.find('.count').first().html('0');
				};
			}
			if (!$li.next().length && !$li.parent().hasClass('child-categories')) {
				af.checkAvailableItems($filter_block, false);
			}
		});
		af.prepareCutOff();
		af.adjustFolderedStructure();

		// selects
		$('.af-select').each(function() {
			var html = '', currentValue = $(this).val(), $filter_block = $(this).closest('.af_filter');
			$filter_block.find('.dynamic-select-options').children().each(function() {
				var id = $(this).data('id'),
					text = $(this).data('text'),
					val = $(this).data('value'),
					count = (id in jsonData.count_data) ? parseInt(jsonData.count_data[id]) : 0;
				if (count || val == currentValue || $(this).hasClass('customer-filter') || !af.hideZeroMatches) {
					html += '<option id="'+id+'" value="'+val+'" data-url="'+$(this).data('url')+'" data-text="'+text+'"';
					html += 'class="'+$(this).attr('class')+'"'+((af.dimZeroMatches && !count) ? ' disabled' : '')+'>';
					html += text+((af.showCounters && count)? ' ('+count+')' : '')+'</option>';
				}
			});
			$(this).children().first().nextAll().remove(); // keep only first option
			if (html) {
				$(this).append(html).val(currentValue);
				try{$(this).uniform()}catch(err){};
			}
			af.checkAvailableItems($filter_block, true);
		});
	},
	updateUrlAndVerifyParams: function() {
		var url = af.getStaticURL(),
			dynamicParams = af.prepareDynamicParams(true);
		if (!$.isEmptyObject(dynamicParams)) {
			af.verifyPrimaryFilter(dynamicParams);
			// dynamicParams = af.sortParams(dynamicParams, af_primary_filter['url']);
			url += (url.indexOf('?') > -1 ? '&' : '?')+decodeURIComponent($.param(dynamicParams, true));
		}
		if (url != window.location.href) {
			window.history.pushState(null, null, url);
			af.popstateURL = url;
		}
	},
	getStaticURL: function() {
		if (!af.staticURL) {
			var splittedUrl = decodeURIComponent(window.location.href).split('?'),
				url = splittedUrl[0].split('#')[0];
			if (splittedUrl.length == 2) {
				var staticParams = af.unserialize(splittedUrl[1], true);
				if (!$.isEmptyObject(staticParams)) {
					url += '?'+decodeURIComponent($.param(staticParams, true));
				}
			}
			af.staticURL = url;
		}
		return af.staticURL;
	},
	prepareDynamicParams: function() {
		var dynamicParams = {}, page = af.$pageInput.val();
		if (af.dynamicParams.f) {
			af.$selectedFilters.find('.cf').each(function() {
				if (!$(this).find(unlocked_selector).length) {
					var n = $(this).data('group'), v = $(this).data('url');
					if (n && v) {
						if (n in dynamicParams) {
							dynamicParams[n] += ','+v;
						} else {
							dynamicParams[n] = v;
						}
					}
				}
			});
		}
		if (af.dynamicParams.p && page > 1) {
			dynamicParams[page_link_rewrite_text] = page;
		}
		if (af.dynamicParams.s) {
			var order = {by: $('#af_orderBy').val(), way: $('#af_orderWay').val()};
			if (order.by+':'+order.way != $('#af_defaultSorting').val()) {
				if (is_17) {
					dynamicParams.order = 'product.'+order.by+'.'+order.way;
				} else {
					dynamicParams.orderby = order.by
					dynamicParams.orderway = order.way
				}
			}
		}
		return dynamicParams;
	},
	verifyPrimaryFilter: function(dynamicParams) {
		if (!(af_primary_filter['url'] in dynamicParams)) {
			for (var link_rewrite in dynamicParams){
				var multipleSelection = dynamicParams[link_rewrite].toString().indexOf(',') > -1,
					$primaryBlock = $('.af_filter[data-url="'+link_rewrite+'"]').not('.special');
				if ($primaryBlock.length &&
					(multipleSelection || !(af_primary_filter['url'] in dynamicParams))) {
					af_primary_filter['url'] = link_rewrite;
					af_primary_filter['trigger'] = $primaryBlock.data('trigger');
				}
			}
		}
	},
	sortParams: function(params, primary_param) {
		var sortedParams = {};
		if (primary_param && primary_param in params) {
			sortedParams[primary_param] = params[primary_param];
		}
		for (var n in params){
			sortedParams[n] = params[n];
		}
		return sortedParams;
	},
	autoscrollToTopOfTheList: function() {
		if (af.$listWrapper.length) {
			var wrapperOffset = af.$listWrapper.offset().top;
			if (af.isCompact || af.isOutOfViewPort(wrapperOffset)) {
				$('html, body').animate({scrollTop: wrapperOffset - 150}, 100);
			}
		}
	},
	isOutOfViewPort: function(wrapperOffset) {
		var $w = $(window), scrollTop = $w.scrollTop(), windowHeight = $w.height();
		return scrollTop > wrapperOffset || (scrollTop + windowHeight) < wrapperOffset;
	},
	checkAvailableItems: function($block, isSelect) {
		var noItems = !isSelect ? $block.find('li').not('.no-matches:not(.active)').length < 1
			: $block.find('option').not('.no-matches:not(:selected), .first').length < 1;
		$block.toggleClass(af.noItemsClass, noItems);
	},
	prepareCutOff: function() {
		$('.af_filter').not('.closed').find('.toggle-cut-off').each(function() {
			var $content = $(this).siblings('.af_filter_content'),
				$filter = $content.closest('.af_filter'),
				full = $filter.hasClass('full');
			$filter.removeClass('full');
			var expandable = $content.prop('scrollHeight') > $content.prop('offsetHeight');
			$filter.toggleClass('expandable', expandable).toggleClass('full', full);
			if (!$(this).hasClass('ready')) {
				$(this).on('click', function(e){
					e.preventDefault();
					$filter.toggleClass('full');
					if (!$filter.hasClass('full')){
						var contentTop = $content.offset().top,
							diff = $(window).scrollTop() - contentTop,
							time = diff > 500 ? parseInt(diff * 0.6) : 300;
						if (diff > 0) {
							$('html, body').animate({scrollTop: contentTop - 150}, time);
						}
					}
				}).addClass('ready');
			}
		});
	},
	adjustFolderedStructure: function() {
		$('.af_filter.foldered').find('.af-parent-category').each(function() {
			var $children = $(this).children('ul').find('li'),
				childrenWithMatches = $children.not('.no-matches').length,
				checkedChildren = $children.filter('.active').length;
			if (af.hideZeroMatches) {
				// hide foldered trigger if none of subcategories are available
				$(this).children('label').find('.af-toggle-child').toggleClass('hidden', !childrenWithMatches && !checkedChildren);
			}
			if (checkedChildren || (childrenWithMatches && $(this).hasClass('no-matches'))) {
				$(this).addClass('open');
				if (af.hideZeroMatches) {
					$(this).removeClass('no-matches');
				}
			}
		});
	},
	updateListLayout: function(layout_html) {
		if ($.contains(af.$dynamicContainer[0], af.$filterBlock[0])) {
			af.$filterBlock.insertBefore(af.$dynamicContainer);
		}
		af.$dynamicContainer.html(af.utf8_decode(layout_html)).animate({'opacity': 1}, 350);
		af.setWrapper();
		af.updateSelectedFilters();
		af.prepareLoadMoreIfRequired();
	},
	updateSelectedFilters: function() {
		var html = '',
			$customerFilterLabels = $('.customer-filter-label');
		$customerFilterLabels.each(function() {
			var url = $('#'+$(this).data('id')).data('url'),
				$f = $(this).closest('.af_filter'),
				groupURL = $f.data('url'),
				groupText = af.includeGroup ? $f.find('.af_subtitle').text()+': ' : '',
				text = groupText+$(this).find('.name').text(),
				unlocked = $(this).hasClass('unlocked'),
				divClass = 'customer-filter-option'+(unlocked ? ' unlocked' : ''),
				iClass = unlocked ? unlocked_class : locked_class;
			html += af.renderSelectedOption(url, groupURL, text, divClass, iClass);
		});
		$('.af_filter').each(function() {
			var groupURL = $(this).data('url'),
				groupText = af.includeGroup && !$(this).hasClass('special') ? $(this).find('.af_subtitle').text()+': ' : '';
				iClass = times_class+' close',
				hasSelection = $(this).find('.customer-filter-label').not('.unlocked').length > 0;
			if ($(this).hasClass('has-slider')) {
				var values = af.getSliderValues($(this).data('trigger'));
				if (values.from > values.min || values.to < values.max) {
					var rangeURL = values.from+'-'+values.to,
						prefix = $(this).find('.prefix').first().text(),
						suffix = $(this).find('.suffix').first().text(),
						text = groupText+prefix+af.formatNumber(values.from)+' - '+af.formatNumber(values.to)+suffix;
					html += af.renderSelectedOption(rangeURL, groupURL, text, 'slider-option', iClass);
					hasSelection = true;
				}
			} else {
				$(this).find('input, option:not(.first, .customer-filter)').filter(':checked, :selected').each(function() {
					var text = groupText+($(this).data('text') || $(this).closest('label').find('.name').text());
					html += af.renderSelectedOption($(this).data('url'), groupURL, text, '', iClass);
					hasSelection = true;
				});
			}
			$(this).toggleClass('has-selection', hasSelection);
		});
		af.$selectedFilters.find('.cf').remove();
		af.$selectedFilters.append(html).toggleClass('hidden', !html);
		if ($customerFilterLabels.length) {
			af.$selectedFilters.find('.all').toggleClass('hidden', !af.$selectedFilters.find('.cf').not('.unlocked').length);
		}
	},
	renderSelectedOption: function(url, group, text, divClass, iClass) {
		return '<div class="cf '+divClass+'" data-url="'+url+'" data-group="'+group+'">'+text+' <a href="#" class="'+iClass+'"></a></div>';
	},
	prepareSliders: function () {
		var $sliders = $('.af_slider');
		if (!$sliders.length) {
			return;
		}
		af.defineNumberSeparators();
		$sliders.each(function() {
			af.activateSlider($(this).data('type'));
		}).find('.slider_value').on('click', function() {
			if (!$(this).hasClass('edit')) {
				var mw = $(this).width()+'px',
					mh = $(this).height()+'px';
				$(this).addClass('edit').find('.input-text').css({'max-width': mw, 'max-height': mh}).focus();
			}
		}).find('.input-text').on('focusin', function() {
			$(this).data('val', $(this).val()).val('');
		}).on('focusout', function() {
			$(this).css({'max-width': '', 'max-height': ''}).closest('.slider_value').removeClass('edit');
			if ($(this).val() == '') {
				$(this).val($(this).data('val'));
			}
		}).on('change', function(e){
			var val = $(this).val(); val = val ? parseFloat(val) : 0; $(this).val(val);
			var type = $(this).closest('.af_slider').data('type'),
				values = af.getSliderValues(type);
			if (values.from < values.min || values.to > values.max || values.to < values.from){
				e.preventDefault();
				$(this).val($(this).data('val'));
				return false;
			}
			$(this).focusout();
			af.updateSlider(type, values);
		}).on('keydown', function(e){
			var code = e.keyCode;
			// allow: delete, backspace, tab, escape, enter, end, home, left, right
			if ($.inArray(code, [46,8,9,27,13,35,36,37,39]) !== -1) {
				return;
			}
			// stop keypress if it is not number (48-57+shift, 96-105) or dot (190+shift, 110)
			if ((e.shiftKey || ((code < 48 || code > 57) && code != 190)) && (code < 96 || code > 105) && code != 110) {
				e.preventDefault();
			}
		});
	},
	defineNumberSeparators: function() {
		af.sep = {thousand: $('#af_tho_sep').val(), decimal: $('#af_dec_sep').val()};
		if (af.sep.thousand == af.sep.decimal) {
			af.sep.decimal = af.sep.thousand == '.' ? ',' : '.';
		}
	},
	getSliderValues: function(type) {
		var values = {
			min : parseFloat($('#'+type+'_min').val()),
			max : parseFloat($('#'+type+'_max').val()),
			from : parseFloat($('#'+type+'_from').val()),
			to : parseFloat($('#'+type+'_to').val()),
		};
		return values;
	},
	updateSlider: function(type, values) {
		if (typeof values == 'undefined') {
			values = af.getSliderValues(type);
		}
		$('#'+type+'_slider').jRange('setValue', values.from+','+values.to);
	},
	activateSlider: function(type) {
		var values = af.getSliderValues(type),
			enabled = values.min != values.max;
		$('#'+type+'_slider').on('change', function(e) {
			e.preventDefault();
			e.stopPropagation();
			if (enabled) {
				$(this).trigger('updateNumbers');
			} else { // if min == max values are set to NaN,NaN
				$(this).val(values.min+','+values.max);
			}
		}).on('updateNumbers', function() {
			var v = $(this).val().split(','), from = v[0], to = v.length > 1 ? v[1] : v[0];
			$('.'+type+'_slider .from_display span.value').text(af.formatNumber(from)); $('#'+type+'_from').val(from);
			$('.'+type+'_slider .to_display span.value').text(af.formatNumber(to)); $('#'+type+'_to').val(to);
		}).jRange({
			from: values.min,
			to: values.max,
			disable: !enabled,
			width: '100%',
			step: 1,
			showScale: false,
			showLabels: false,
			snap: false,
			isRange: true,
			ondragend: function(value) {
				$('#'+type+'_max').change();
			},
			onbarclicked: function(value) {
				$('#'+type+'_max').change();
			},
		}).trigger('updateNumbers');
	},
	formatNumber: function(number) {
		if (af.sep.thousand) {
			number = number.toString().split('.'); // standard input uses . for decimals
			number[0] = number[0].replace(/\B(?=(\d{3})+(?!\d))/g, af.sep.thousand);
			return number.join(af.sep.decimal);
		} else {
			return number;
		}
	},
	activateInfiniteScroll: function() {
		var scrollTimer, $dl = $('.dynamic-loading.next'), $btn = $('.loadMore.next');
		$(window).on('scroll', function() {
			clearTimeout(scrollTimer);
			scrollTimer = setTimeout(function() {
				if (af.$listWrapper.length) {
					var viewportTop = $(window).scrollTop(),
						windowOffset = viewportTop + $(window).height(),
						listTop = af.$listWrapper.offset().top,
						listOffset = listTop + af.$listWrapper.outerHeight(),
						isInViewPort = listOffset > viewportTop && listTop < windowOffset;
					if (!$dl.hasClass('loading') && !$btn.hasClass('hidden')
						&& isInViewPort && (windowOffset + 700) > listOffset) {
						$btn.click();
					}
				}
			}, 50);
		});
	},
	unserialize: function(params, excludeDynamicParams) {
		params = params.split('&');
		var result = {}, dynamicParams = {order:0, orderby:0, orderway:0}; dynamicParams[page_link_rewrite_text] = 0;
		for (var i in params){
			var splitted = params[i].split('='), name = splitted[0];
			if (splitted.length == 2 && (!excludeDynamicParams ||
				!$('.af_filter[data-url="'+name+'"]').length && !(name in dynamicParams))) {
				result[name] = splitted[1];
			}
		}
		return result;
	},
	browserEvents: function() {
		$(window).on('popstate', function() { // when user clicks back/forward in browser
			if (window.location.href != af.popstateURL) {
				af.updateFiltersBasingOnURL(window.location.href);
			}
		}).on('resize', function() {
			clearTimeout(af.resizeTimer);
			af.resizeTimer = setTimeout(function() {af.toggleCompactView()}, 100);
		});
	},
	updateFiltersBasingOnURL: function(url) {
		window.location.reload();
		// TODO: dynamically update filters and product list basing on URL, or may be other global variable
		// var splittedUrl = decodeURIComponent(url).split('?');
		// if (splittedUrl.length == 2) {
		// 	$.each(af.unserialize(splittedUrl[1]), function(name, val){
		// 		if ($('.af_filter[data-url="'+name+'"]').length) {
		// 			console.dir(val);
		// 		}
		// 	});

		// }
	},
	utf8_decode: function(utfstr) {
		var res = '';
		for (var i = 0; i < utfstr.length;) {
			var c = utfstr.charCodeAt(i);
			if (c < 128) {
				res += String.fromCharCode(c);
				i++;
			} else if((c > 191) && (c < 224)) {
				var c1 = utfstr.charCodeAt(i+1);
				res += String.fromCharCode(((c & 31) << 6) | (c1 & 63));
				i += 2;
			} else {
				var c1 = utfstr.charCodeAt(i+1);
				var c2 = utfstr.charCodeAt(i+2);
				res += String.fromCharCode(((c & 15) << 12) | ((c1 & 63) << 6) | (c2 & 63));
				i += 3;
			}
		}
		return res;
	},
};

$(document).ready(function() {
	af.documentReady();
});
/* since 3.0.3 */
