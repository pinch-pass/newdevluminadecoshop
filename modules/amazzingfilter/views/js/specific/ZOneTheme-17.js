/**
*  2007-2020 PrestaShop
*
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
customThemeActions.documentReady = function() {
	af.gridColumns = $('#js-product-list').data('grid-columns');
}
customThemeActions.updateContentAfter = function(jsonData) {
	// based on 'updateProductList' in /themes/ZOneTheme/_dev/js/listing.js
	var $gridOptions =  $('#product_display_control').find('a'),
		storage = window.localStorage || window.sessionStorage;
	if (storage && storage.productListView) {
		var $opt = $gridOptions.filter('[data-view="'+storage.productListView+'"]');
		if ($opt.length && !$opt.hasClass('selected')) {
			$opt.click();
		}
	}
	if (af.gridColumns) {
		$('#js-product-list').find('.js-product-list-view')
		.removeClass('columns-2 columns-3 columns-4 columns-5').addClass(af.gridColumns);
	}
	$('.products-sort-order').find('.js-search-link').addClass('select-list');
}
$(window).on('load', function() {
	// override evens bound in /themes/ZOneTheme/_dev/js/listing.js searchFiterFacets()
	$('body').off('click', '.js-search-link');
	$('.products-sort-order').find('.js-search-link').addClass('select-list');
	// af.$filterBlock.find('select').data('uniformed', 1); // block uniform update
});
/* since 2.8.8 */
