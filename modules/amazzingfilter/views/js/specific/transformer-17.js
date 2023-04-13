/**
*  2007-2020 PrestaShop
*
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
customThemeActions.updateContentAfter = function (jsonData) {
	prestashop.emit('updatedProductListDOM');
}
/* since 2.8.1 */
