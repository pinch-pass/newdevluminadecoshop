/**
*  2007-2020 PrestaShop
*
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

var newJsLinkClass = 'js-search-link-new';
function updateJsLinks() {
	$('.js-search-link').removeClass('js-search-link').addClass(newJsLinkClass);
}
function bindActionsToNewJsLinks() {
	$('body').off('click', '.js-search-link').on('click', '.'+newJsLinkClass, function(e) {
		e.preventDefault();
		$(this).addClass('current').siblings().removeClass('current');
		if ($(this).closest('.products-sort-order').length) {  // Sorting
			var value = $(this).attr('href').split('order=')[1];
				splitted = value.split('.'),
				orderBy = splitted[1],
				orderWay = splitted[2].split('&')[0];
			$('#af_orderBy').val(orderBy);
			$('#af_orderWay').val(orderWay).change();
		} else if ($(this).closest('.products-nb-per-page').length) {  // number of products per page
			var npp = parseInt($(this).attr('href').split('resultsPerPage=')[1]);
			$('#af_nb_items').val(npp).change();
		} else if ($(this).closest('.view-switcher').length) {  // grid/list
		   updateListViewParam();
		   $('#af_orderWay').change();
	   }
	});
}
function updateListViewParam() {
	var view = $('.view-switcher').find('.current').data('view') || 'grid';
	$('input[name="listView"]').val(view);
}
customThemeActions.documentReady = function() {
	$('.hidden_inputs').append('<input type="hidden" name="listView">');
	updateJsLinks();
	bindActionsToNewJsLinks();
	updateListViewParam();
	$('.dynamic-loading.infinite-scroll').find('.dynamic-product-count').addClass('hidden').
	siblings('.loading-indicator').html('<i class="fa fa-circle-o-notch fa-spin"></i>');
};
customThemeActions.updateContentAfter = function (jsonData) {
	updateJsLinks();
	prestashop.emit('afterUpdateProductList');
};
/* since 2.8.7 */
