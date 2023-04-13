/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

var ajax_action_path = window.location.href.split('#')[0]+'&ajax=1',
	productIds = [],
	productIdsTotal = 0;

$(document).ready(function() {

	$(document).on('click', 'a[href="#"], .list-group-item', function(e){
		e.preventDefault();
	}).on('click', '.saveMultipleSettings', function(){
		var $btn = $(this),
			$forms = $btn.closest('.tab-pane').find('form'),
			params = {action: 'SaveMultipleSettings', submitted_forms: {}},
			response = function(r) {
				if ('errors' in r) {
					prependErrors($forms.find('h3').first().next(), utf8_decode(r.errors));
				} else if (r.saved) {
					$.growl.notice({ title: '', message: savedTxt});
					if ($btn.hasClass('indexation')) {
						$btn.closest('.indexation-settings').removeClass('show-settings');
						if ($btn.hasClass('show-reminder')) {
							reindexReminder.appendTo($btn.closest('.tab-pane'));
							$btn.removeClass('show-reminder');
						}

					}
				}
			};
		$forms.each(function(){
			params['submitted_forms'][$(this).data('type')] = $(this).serialize();
			if ($(this).data('type') == 'general') {
				cachingSettings.toggleCombinationsAvailability();
			}
		}).find('.thrown-errors').remove();
		ajaxRequest(params, response);
	}).on('click', '.resetSelectors', function(){
		$(this).closest('form').find('input[type="text"]').each(function(){
			$(this).val($(this).attr('name'));
		});
	}).on('click', '.form-group.blocked', function(){
		alert('You should contact module developer to activate this option');
	});

	$('.menu-panel').find('.list-group-item').on('click', function(){
		$(this).addClass('active').siblings().removeClass('active');
		$($(this).attr('href')).addClass('active').siblings().removeClass('active');
	});

	$('.hookSelector').on('change', function(){
		var hookName = $(this).val(),
			params_string = 'action=UpdateHook&hook_name='+hookName;
		$('.special-hook-note').toggleClass('hidden', hookName != 'displayAmazzingFilter');
		$('#hook-settings').find('.ajax-warning').html('').addClass('hidden');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&'+params_string,
			dataType : 'json',
			success: function(r) {
				console.dir(r);
				if ('warning' in r) {
					$('#hook-settings').find('.ajax-warning').html(utf8_decode(r.warning)).removeClass('hidden');
				}
				if ('positions_form_html' in r) {
					$('.dynamic-positions').html(utf8_decode(r.positions_form_html));
					$.growl.notice({ title: '', message: savedTxt});
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	$('.toggleIndexationSettings').on('click', function(){
		$(this).parent().toggleClass('show-settings');
	});

	$(window).on('load', function() { // after related options have been toggled
		$('.indexation-settings').find('input').on('change', function() {
			if ($(this).attr('name') != 'auto') { // no need to reindex products if only this option was updated
				$('.saveMultipleSettings.indexation').addClass('show-reminder');
			}
		});
	})

	$('.indexProducts').on('click', function(){
		if ($(this).hasClass('disabled')) {
			alert('Please wait');
			return;
		}
		$('.indexProducts').addClass('disabled');
		$(this).removeClass('disabled').addClass('btn-success');
		var $parent = $(this).closest('.indexation-buttons');
		$parent.toggleClass('running');
		if (!$parent.hasClass('running')) {
			clearIndexationLayout();
			return;
		}
		$('.shop-indexation-data').addClass('running');
		var all_identifier = $(this).data('all-identifier') || 0;
		runAjaxProductIndexer(all_identifier);
	});

	$('.eraseIndex').on('click', function(){
		if (!confirm(areYouSureTxt)) {
			return;
		}
		var $indexationBox = $(this).closest('.shop-indexation-data'),
			id_shop = $indexationBox.data('shop');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=EraseIndex&id_shop='+id_shop,
			dataType : 'json',
			success: function(r){
				// console.dir(r);
				if(r.deleted) {
					$.growl.notice({ title: '', message: deletedTxt});
					$indexationBox.removeClass('complete').find('.count.indexed').html('0').siblings('.count.missing').html(r.missing);
					$('.indexation-warning').removeClass('hidden');
					$indexationBox.find('.progress-bar.indexation').css('width', '0%').attr('aria-valuenow', '0').html('');
				} else {
					$.growl.error({ title: '', message: errorTxt});
				}
			},
			error: function(r){
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	$('.af').on('click', '.editTemplate, .addTemplate', function(e){
		e.preventDefault();
		$('.scrollUp').click();
		var $btn = $(this); id = 0, controller = $(this).data('controller') || 'category';
		if ($(this).hasClass('editTemplate')){
			id = $btn.closest('.af_template').attr('data-id');
			controller = $(this).closest('.af_template').attr('data-controller');
		}
		var params_string = 'action=CallTemplateForm&template_controller='+controller+'&id_template='+id;
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&'+params_string,
			dataType : 'json',
			success: function(r) {
				if (!id) {
					$('.template-list.'+controller).prepend(utf8_decode(r.form_html));
				} else {
					$('.af_template[data-id="'+id+'"]').replaceWith(utf8_decode(r.form_html));
				}
				var $template = $('.af_template[data-id="'+r.id_template+'"]');
				prepareTemplate($template);
				$template.find('.template_settings').slideDown();
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	function prepareTemplate($template) {
		$template.on('click', '.removeFilter', function(){
			$(this).closest('.filter').remove();
		}).on('click', '.toggleFilterSettings', function(){
			$(this).closest('.filter').toggleClass('show-settings').siblings().removeClass('show-settings');
		}).on('change', '.f-type', function(){
			var $excOptions = $(this).closest('.filter').find('.type-exc');
			$excOptions.removeClass('hidden').filter('.not-for-'+$(this).val()).addClass('hidden');
		}).on('click', '.addNewFilter', function(){
			var params = 'action=ShowAvailableFilters',
				response = function(r) {
					$('#dynamic-popup').find('.dynamic-content').html(utf8_decode(r.content));
					$('#dynamic-popup').find('.modal-title').html(utf8_decode(r.title));
				};
			ajaxRequest(params, response);
		}).on('change', '[name="template_controller"]', function(){
			var controller = $(this).val();
			$template.find('.controller-option').addClass('hidden').filter('.'+controller).removeClass('hidden');
		}).tooltip({selector: '.label-tooltip'});

		$template.find('[name="template_controller"]').change();
		$template.find('.controller-settings').find('.basic-item').each(function(){
			updateSelectedOptionsTxt($(this));
			$(this).find('.opt.closed').each(function(){
				markCheckedChildren($(this));
			});
		});

		prepareFilters($template.find('.f-list'));
		activateSortable();
		relatedOptions.init();
	}

	function prepareFilters($filtersList) {
		$filtersList.find('.form-group.custom-name').find('.lang-'+id_language).find('input')
		.off('keyup').on('keyup', function(){
			var $nameHolder = $(this).closest('.filter').find('.name'),
				customName = $.trim($(this).val()) || $nameHolder.data('name');
			$nameHolder.html(customName);
		});
		$filtersList.find('.f-type').change();
		$filtersList.find('.nesting-lvl').off('change').on('change', function(){
			var allowTextBoxes = $(this).val() == '1',
				$tbOption = $(this).closest('.filter').find('.f-type').find('option[value="5"]');
			$tbOption.prop('disabled', !allowTextBoxes);
			if ($tbOption.is(':selected:disabled')) {
				$tbOption.parent().val('1').change(); // select checkbox if textbox is not available
			}
		}).change();
	}

	$('#dynamic-popup').on('click', '.close', function(){
		$('.dynamic-content, .dynamic-header-txt').html('');
	});
	$(document).on('click', '.addSelectedFilters', function(){
		if ($(this).hasClass('btn-blocked')) {
			return;
		}
		var keys = [];
		$('.filter-group-item.selected').not('.blocked').each(function(){
			keys.push($(this).data('key'));
		});
		var params = 'action=RenderFilterElements&keys='+keys.join(','),
			response = function(r) {
				var $list = $('.af_template.open').first().find('.f-list');
				$list.append(utf8_decode(r.html));
				prepareFilters($list);
				$('#dynamic-popup').find('.close').click();
			};
		ajaxRequest(params, response);
	});

	$(document).on('click', '.selectLanguage', function(){
		var idLang = $(this).data('lang');
		$(this).closest('form').find('.multilang').addClass('hidden').filter('.lang-'+idLang).removeClass('hidden');
	});

	$('.template-list').on('click', '.lock-toggle', function(e){
		var $parent = $(this).parent(),
			$tab = $(this).closest('.template-tab-content'),
			$counter = $(this).closest('.template_settings').find('.additional-settings-count');
		$parent.toggleClass('locked').toggleClass('unlocked').find('input').prop('checked', $parent.hasClass('unlocked'));
		$counter.html($tab.find('.unlocked').length);
	});

	function activateSortable() {
		$('.f-list.sortable').sortable({
			placeholder: 'sortable-filter-placeholder',
		});
	}

	$('.af').on('click', '.template-action', function(){
		var action = $(this).data('action'),
			$parent = $(this).closest('.af_template'),
			idTemplate = $parent.data('id');
		if (action == 'Delete' && !confirm(areYouSureTxt)) {
			return;
		}
		var params = 'action='+action+'Template&id_template='+idTemplate,
			response = function(r) {
				if ('errors' in r) {
					$parent.before(utf8_decode(r.errors));
				} else if (action == 'Delete' && r.success) {
					$parent.fadeOut(function(){$(this).remove()});
				} else if (action == 'Duplicate' && 'form_html' in r ) {
					$.growl.notice({ title: '', message: savedTxt});
					$parent.closest('.template-list').prepend(utf8_decode(r.form_html));
				}
			};
		$parent.prev('.thrown-errors').remove();
		ajaxRequest(params, response);
	});

	$('.af').on('click', '.scrollUp', function(){
		scrollUp($(this).closest('.af_template'));
	});

	function prependErrors($parent, errorsHTML) {
		$parent.prepend(errorsHTML);
		$('html, body').animate({scrollTop: $parent.offset().top - 150}, 300);
	}

	$('.template-list').on('click', '.template-tab-option', function(e){
		e.preventDefault();
		$(this).addClass('active').siblings('.template-tab-option').removeClass('active');
		$('.template-tab-content'+$(this).attr('href')).addClass('active').siblings('.template-tab-content').removeClass('active');
	}).on('click', '.saveTemplate', function(){
		$('.thrown-errors').remove();
		var $parent = $(this).closest('.af_template'),
			$form = $(this).closest('.template-form');
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=SaveTemplate',
			data: $form.serialize(),
			dataType : 'json',
			success: function(r) {
				if ('errors' in r) {
					prependErrors($parent, utf8_decode(r.errors));
				} else {
					var callBack = function() {
						var templateName = $('<div>'+$parent.find('input[name="template_name"]').val()+'</div>').text(), // extract only text
							controllerNew =  $parent.find('select[name="template_controller"]').val(),
							controllerPrev = $parent.data('controller');
						$parent.find('.template-name').find('h4').html(templateName);
						if (controllerNew != controllerPrev) {
							moveTemplate($parent, controllerNew);  // may be used in next versions
						}
					};
					scrollUp($parent, callBack);
					$.growl.notice({ title: '', message: savedTxt});
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	function scrollUp($template, callBack) {
		callBack = callBack || function(){};
		$template.find('.template_settings').slideUp(function(){
			$template.removeClass('open');
			$(this).html('');
			callBack();
		});
	}

	function moveTemplate($template, controller) { // may be used in next versions
		var $newContainer = $('.template-list.'+controller);
		if ($newContainer.hasClass('hidden')) {
			$template.slideUp(500, function(){
				$template.prependTo($newContainer);
			});
		} else {
			var placeholderHTML = '<div class="template-placeholder"></div>',
				id = $template.data('id');
			$newContainer.find('.af_template').each(function(i){
				if ($(this).data('id') < id) { // templates are sorted by ID DESC
					$(this).before(placeholderHTML);
					placeholderHTML = '';
				}
			});
			if (placeholderHTML) {
				$newContainer.prepend(placeholderHTML);
			}
		}
	}

	$('.af').on('click', '.saveAvailableCustomerFilters', function(){
		var data = $(this).closest('form').serialize();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=SaveAvailableCustomerFilters',
			data: data,
			dataType : 'json',
			success: function(r) {
				console.dir(r);
				if (r.success) {
					$.growl.notice({ title: '', message: savedTxt});
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	$(document).on('click', '.activateTemplate', function(e){
		e.preventDefault();
		$('.thrown-errors').remove();
		var id_template = $(this).closest('.af_template').attr('data-id'),
			active = $(this).hasClass('action-enabled') ? 0 : 1,
			$button = $(this);
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action=ToggleActiveStatus',
			dataType : 'json',
			data: {
				id_template: id_template,
				active: active,
			},
			success: function(r) {
				if ('errors' in r) {
					$button.closest('.af_template').before(utf8_decode(r.errors));
				} else if(r.success) {
					$button.toggleClass('action-enabled action-disabled');
					$button.find('input[name="active"]').val(active)
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	// ----- multiple options
	var parentSelector = '.basic-item', blockUpdateSelectedOptionsTxt = false;
	$('.template-list, .merged-list').on('click', '.selected-options-inline, .hideOptions', function(e){
		var $parent = $(this).closest(parentSelector);
		$parent.find('.available-options').toggleClass('hidden');
		$parent.find('.toggleIndicator').toggleClass('icon-rotate-180');
	}).on('click', '.toggleChildren', function(e){
		e.preventDefault();
		var $opt = $(this).closest('.opt');
		$opt.toggleClass('closed');
		markCheckedChildren($opt);
	}).on('click', '.checkChildren', function(){
		var $checkboxes = $(this).siblings('.opt-level').find('.opt-checkbox'),
			uncheck = $checkboxes.filter(':checked').length;
		$checkboxes.prop('checked', !uncheck).change();
		$('.opt.closed').each(function(){
			markCheckedChildren($(this));
		});
	}).on('change', '.opt-checkbox', function(){
		$(this).closest('label').toggleClass('checked', $(this).prop('checked'));
		updateSelectedOptionsTxt($(this).closest(parentSelector));
	}).on('click', '.opt-action', function(e){
		var $group = $(this).closest(parentSelector),
			action = $(this).data('bulk-action'),
			toggleOtherOption = $(this).data('toggle');
		switch (action) {
			case 'open':
			case 'close':
				var selector = action == 'open' ? '.opt.closed' : '.opt:not(.closed)';
				$group.find(selector).children('.opt-label').children('.toggleChildren').click();
				break;
			case 'check':
			case 'uncheck':
			case 'invert':
				blockUpdateSelectedOptionsTxt = true;
				$group.find('.opt-checkbox').each(function (){
					var checked = action == 'check' ? true : action == 'uncheck' ? false : !$(this).prop('checked');
					$(this).prop('checked', checked).change();
				});
				$('.opt.closed').each(function(){
					markCheckedChildren($(this));
				});
				blockUpdateSelectedOptionsTxt = false;
				updateSelectedOptionsTxt($group);
				break;
		}
		if (toggleOtherOption) {
			$(this).addClass('hidden');
			$(this).siblings('.opt-action[data-bulk-action="'+toggleOtherOption+'"]').removeClass('hidden');
		}
	}).on('change', '.toggleIDs', function(){
		$(this).closest(parentSelector).find('.opt-id').toggleClass('hidden', !$(this).prop('checked'));
	});

	function updateSelectedOptionsTxt($group) {
		if (blockUpdateSelectedOptionsTxt) {
			return;
		}
		var $checked = $group.find('.opt-checkbox:checked'), total = $checked.length, displayedNum = 7, extra = '';
		if ($group.find('.dynamic-name').length) {
			selectedTxt = [];
			$checked.each(function(){
				if (selectedTxt.length < displayedNum) {
					selectedTxt.push($(this).closest('.opt-label').find('.opt-name').text());
				} else {
					extra = ', ... + '+(total - displayedNum);
					return false;
				}
			});
			selectedTxt = selectedTxt.join(', ')+extra;
			$group.find('.item-names').html(selectedTxt);
			$group.find('.total-num').html(total);
		}
		$group.toggleClass('has-selection', !!total);
	}

	function markCheckedChildren($opt) {
		var childrenChecked = $opt.find('.opt-level').find('.opt-checkbox:checked').length,
			showNum = childrenChecked && $opt.hasClass('closed');
		$opt.children('.checked-num').toggleClass('hidden', !showNum).find('.dynamic-num').html(childrenChecked);
	}
	// ----- /multiple options

	$('.toggle-cron').on('click', function(){
		$(this).closest('.shop-indexation-data').toggleClass('show-cron')
		.closest('.grid-item').siblings().find('.shop-indexation-data').removeClass('show-cron');
	});

	$('.close-cron').on('click', function(){
		$(this).closest('.shop-indexation-data').removeClass('show-cron');
	});

	$('.install-override, .uninstall-override').on('click', function(){
		var $parent = $(this).closest('.override-item'),
			action = $(this).hasClass('install-override') ? 'addOverride' : 'removeOverride',
			override = $(this).data('override');
		$parent.find('.thrown-errors').remove();
		$.ajax({
			type: 'POST',
			url: ajax_action_path+'&action='+action+'&override='+override,
			dataType : 'json',
			success: function(r) {
				// console.dir(r);
				if ('errors' in r) {
					$parent.prepend(utf8_decode(r.errors));
				} else if (r.processed) {
					$.growl.notice({ title: '', message: savedTxt});
					$parent.toggleClass('installed not-installed');
				} else {
					$.growl.error({ title: '', message: errorTxt});
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	});

	function runAjaxProductIndexer(all_identifier) {
		$('.indexation-buttons').find('.thrown-errors').remove();
		var	params = '&action=RunProductIndexer&all_identifier='+all_identifier,
			successResponse = function (r) {
				if ('errors' in r) {
					$('.indexation-buttons').prepend(utf8_decode(r.errors));
					clearIndexationLayout();
				} else if ('indexation_data' in r) {
					$.each(r.indexation_data, function(id_shop, data) {
						var $indexationBox = $('.shop-indexation-data[data-shop="'+id_shop+'"]');
						$indexationBox.find('.count.indexed').html(data['indexed']).siblings('.count.missing').html(data['missing']);
						if ('indexation_process_data' in r && id_shop in r.indexation_process_data) {
							data = r.indexation_process_data[id_shop];
						}
						var total = data['missing'] + data['indexed'], w = Math.round(100 - (data['missing']/total) * 100);
						$indexationBox.toggleClass('complete', !data['missing']).find('.progress-bar.indexation')
						.css('width', w+'%').attr('aria-valuenow', w).html(data['indexed']+'/'+total);
					});
					if ($('.indexation-buttons').hasClass('running') && $('.shop-indexation-data').not('.complete').length) {
						runAjaxProductIndexer(all_identifier);
					} else {
						$('.indexation-warning').addClass('hidden');
						reindexReminder.removeAll();
						clearIndexationLayout();
					}
				}
			},
			errorResponse = function(r) {
				clearIndexationLayout();
			};
		ajaxRequest(params, successResponse, errorResponse);
	}

	function clearIndexationLayout() {
		$('#indexation').find('.running').removeClass('running');
		$('.progress-bar.indexation').css('width', '0').attr('aria-valuenow', '0').html('');
		$('.indexProducts').removeClass('btn-success').removeClass('disabled');
	}

	// merged params
	$('.mergedGroup').on('change', function(){
		var $parent = $(this).closest('.merged-params'),
			params = {action: 'getMergedItems', type: $parent.data('type'), id_group: $(this).val()},
			response = function(r) {
				if ('html' in r) {
					$parent.find('.merged-list').html(utf8_decode(r.html)).find('.basic-item').each(function(){
						updateSelectedOptionsTxt($(this));
					});
				}
			};
		ajaxRequest(params, response);
	}).change();
	$('.addMergedRow').on('click', function(){
		var $parent = $(this).closest('.merged-params'),
			params = {action: 'addMergedRow', type: $parent.data('type'), id_group: $parent.find('.mergedGroup').val()},
			response = function(r) {
				if ('html' in r) {
					$parent.find('.merged-list').append(utf8_decode(r.html));
					$parent.find('.no-matches').remove();
				}
			};
		ajaxRequest(params, response);
	});
	$('.merged-list').on('click', '.deleteMergedRow', function(){
		if (!confirm(areYouSureTxt)) {
			return;
		}
		var $row = $(this).closest('.merged-row'),
			params = {
				action: 'deleteMergedRow',
				type: $(this).closest('.merged-params').data('type'),
				id_merged:$row.data('id')
			},
			response = function(r) {
				if (r.deleted) {
					if (!$row.siblings('.merged-row').length) {
						$row.closest('.merged-params').find('.mergedGroup').change(); // display .no-matches
					} else {
						$row.remove();
					}
					$.growl.notice({ title: '', message: deletedTxt});
				}
			};
		ajaxRequest(params, response);
	}).on('click', '.saveMergedRow', function(){
		var $row = $(this).closest('.merged-row'),
			params = {action: 'saveMergedRow', data: $(this).closest('form').serialize()},
			response = function(r) {
				if (r.saved_id) {
					$row.data('id', r.saved_id).attr('data-id', r.saved_id).find('input[name="id_merged"]').val(r.saved_id);
					$row.find('.available-options').addClass('hidden');
					$.growl.notice({ title: '', message: savedTxt});
					reindexReminder.appendTo($row.closest('.tab-pane'));
				}
			};
		ajaxRequest(params, response);
	});

	var reindexReminder = {
		init: function() {
			$('.tab-pane').on('click', '.close-reminder', function() {
				$(this).closest('.reindex-reminder').remove();
			});
		},
		appendTo: function($parent) {
			var $r = $parent.find('.reindex-reminder');
			if ($r.length) {
				$r.removeClass('flashing'); setTimeout(function(){$r.addClass('flashing');}, 50);
			} else {
				$('.reindex-reminder.orig').clone().removeClass('orig hidden').appendTo($parent).addClass('flashing');
			}
		},
		removeAll: function() {
			$('.reindex-reminder').not('.orig').remove();
		}
	};
	reindexReminder.init();

	var relatedOptions = {
		init: function() {
			$('.has-related-options').not('.ready').each(function() {
				var $related = $(this).closest('form').find($(this).data('related'));
				if ($related.length) {
					$(this).find('input[type="text"]').on('keyup', function() {
						relatedOptions.toggle($related, parseInt($(this).val()));
					}).keyup();
					$(this).find('input:not([type="text"]), select').on('change', function() {
						relatedOptions.toggle($related, $(this).val());
					}).filter('input:checked, select').change();
				}
			}).addClass('ready');
		},
		toggle: function($els, value) {
			$els.filter('[class*=" hidden-on-"]').removeClass('hidden').filter('.hidden-on-'+value).addClass('hidden');
			$els.filter('[class*=" visible-on-"]').addClass('hidden').filter('.visible-on-'+value).removeClass('hidden');
		}
	}
	relatedOptions.init();

	$.each(['attribute', 'feature'], function(k, type) {
		$('.settings-item.merged'+type+'s').on('change', 'input', function(){
			$('#general-settings').find('.saveMultipleSettings').click();
			toggleMergedParams(type);
		});
	});

	function toggleMergedParams(type) {
		var $tab = $('.list-group-item[href="#merged-'+type+'s"]');
		if ($('#merged'+type+'s').is(':checked')) {
			$('html, body').animate({scrollTop: $tab.offset().top - 250}, 20);
			$tab.removeClass('hidden').addClass('active flashing');
			setTimeout(function(){$tab.removeClass('active flashing');}, 500);
		} else {
			$tab.addClass('hidden').removeClass('active flashing');
		}
	}

	// caching
	var cachingSettings = {
		updateInfo: function() {
			var params = {action: 'getCachingInfo'},
				response = function(r) {
					$.each(r.info, function(name, text) {
						var $note = $('.form-group.'+name).find('.field-note');
						if (!$note.length) {
							$note = $('<span class="field-note"></span>').appendTo('.form-group.'+name);
						}
						$note.html(utf8_decode(text));
					});
				};
			ajaxRequest(params, response);
		},
		toggleCombinationsAvailability: function() {
			var hide = !$('#combinationsexistence:checked, #combinationsstock:checked').length;
			$('#caching-settings').find('.form-group.comb_data').toggleClass('hidden', hide);
		},
		clear: function() {
			ajaxRequest({action: 'clearCache'}, cachingSettings.updateInfo);
		},
		init: function() {
			cachingSettings.toggleCombinationsAvailability();
			cachingSettings.updateInfo();
			$('.clearCache').on('click', function(){cachingSettings.clear();});
			if ($('#caching-settings').find('input[type="radio"][value="1"]:checked').length) {
				setInterval(function(){cachingSettings.updateInfo();}, 60000);
			}
		},
	};
	cachingSettings.init();

	//sort templates
	var templateSorting = {
		default: {by: 'date_add', way: 'desc'}, // date_add represents sorting by ID
		getParamName: function(controllerType) {
			return 'sort_'+controllerType+'_templates';
		},
		init: function() {
			$('.ts-current-option').on('click', function() {
				$(this).parent().toggleClass('show-options');
			});
			$('.ts-way').on('click', function() {
				$(this).children().toggleClass('hidden current');
				templateSorting.apply($(this));
			});
			$('.ts-by').on('click', function() {
				var txt = $(this).text();
				$(this).siblings().removeClass('current');
				$(this).addClass('current').closest('.template-sorting').removeClass('show-options').
				find('.ts-current-option').text(txt);
				templateSorting.apply($(this));
			});
			templateSorting.applyFromURL();
		},
		apply: function($el) {
			var $group = $el.closest('.template-group'),
				$templates = $group.find('.af_template'),
				order = {
					by: $group.find('.ts-by.current').data('by'),
					way: $group.find('.ts-way').find('.current').data('way'),
				},
				asc = order.way == 'asc';
			if (order.by == 'name') {
				$templates.sort(function(a, b) {
					var a = $(a).find('input[name="template_name"]').val().toUpperCase(),
						b = $(b).find('input[name="template_name"]').val().toUpperCase();
					return asc ? a.localeCompare(b) : b.localeCompare(a);
				});
			} else {
				$templates.sort(function(a, b) {
					return asc ? $(a).data('id') - $(b).data('id') : $(b).data('id') - $(a).data('id');
				});
			}
			$group.find('.template-list').prepend($templates);
			templateSorting.updateURL($group.find('.addTemplate').data('controller'), order);
		},
		updateURL: function(controllerType, order) {
			if (window.location.search) {
				var sortingParam = templateSorting.getParamName(controllerType),
					params = JSON.parse('{"'+decodeURI(window.location.search)
					.replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"')+'"}');
				delete params[sortingParam];
				if (order.way != templateSorting.default.way || order.by != templateSorting.default.by) {
					params[sortingParam] = order.by+':'+order.way;
				}
				var newURL = window.location.href.split('?')[0]+decodeURIComponent($.param(params, true));
				if (newURL != window.location.href) {
					window.history.pushState(null, null, newURL);
				}
			}
		},
		applyFromURL: function() {
			$('.template-sorting').each(function() {
				var $group = $(this).closest('.template-group'),
					controllerType = $group.find('.addTemplate').data('controller'),
					forcedSorting = getUrlParam(templateSorting.getParamName(controllerType)).split(':');
				if (forcedSorting.length == 2) {
					$(this).find('.ts-way').find('[data-way="'+forcedSorting[1]+'"]')
					.addClass('current').removeClass('hidden').siblings().addClass('hidden').removeClass('current');
					$(this).find('.ts-by[data-by="'+forcedSorting[0]+'"]').click();
				}
				$group.removeClass('not-ready');
			});
		},
	};
	templateSorting.init();

	function ajaxRequest(params, response, errorResponse){
		errorResponse = typeof errorResponse == 'undefined' ? function(r){} : errorResponse;
		$.ajax({
			type: 'POST',
			url: ajax_action_path,
			data: params,
			dataType : 'json',
			success: function(r) {
				console.dir(r);
				response(r);
				if ('notice' in r) {
					$.growl.notice({ title: '', message: utf8_decode(r.notice)});
				}
			},
			error: function(r) {
				console.warn($(r.responseText).text() || r.responseText);
				errorResponse(r);
			}
		});
	}

	var forcedTab = getUrlParam('tab');
	if (forcedTab) {
		$('.list-group-item[href="#'+forcedTab).click();
	}

	function getUrlParam(name) {
		return (location.search.split(name+'=')[1] || '').split('&')[0];
	}
});

function utf8_decode(utfstr) {
	var res = '';
	for (var i = 0; i < utfstr.length;) {
		var c = utfstr.charCodeAt(i);
		if (c < 128) {
			res += String.fromCharCode(c);
			i++;
		} else if ((c > 191) && (c < 224)) {
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
}
/* since 3.0.3 */
