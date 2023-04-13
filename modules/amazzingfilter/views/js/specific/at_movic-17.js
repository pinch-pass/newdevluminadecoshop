/**
*  2007-2020 PrestaShop
*
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
customThemeActions.updateContentAfter = function (jsonData) {
    if (typeof $.LeoCustomAjax == 'function') {
        var leoCustomAjax = new $.LeoCustomAjax();
        leoCustomAjax.processAjax();
    }
    if ($('.af_pl_wrapper').find('.product_list').hasClass('list')) {
        $('.leo_list').addClass('selected').siblings().removeClass('selected');
    }
}
/* since 3.0.0 */
