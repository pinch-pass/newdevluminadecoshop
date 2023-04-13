/**
*  2007-2020 PrestaShop
*
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

$(document).ready(function(){
    af.productItemSelector = '.ajax_block_product';
});
customThemeActions.updateContentAfter = function(r) {
    if (typeof $.LeoCustomAjax == 'function') {
        var leoCustomAjax = new $.LeoCustomAjax();
        leoCustomAjax.processAjax();
    }
    if (typeof callLeoFeature != 'undefined') {
        callLeoFeature();
    }
    if ($('.af_pl_wrapper').find('.product_list').hasClass('list')) {
        $('.leo_list').addClass('selected').siblings().removeClass('selected');
    }
};
/* since 3.0.3 */
