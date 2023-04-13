/**
*  2007-2020 PrestaShop
*
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
if (typeof customThemeActions != 'undefined') {
	customThemeActions = {
		documentReady: function() {
			var activateSliderOrig = af.activateSlider;
			af.activateSlider = function(type) {
				setTimeout(function() {
					activateSliderOrig(type);
				}, 300); // fix for overlapping slider because of #left_column {transition: all .3s...}
			}
		},
		updateContentAfter: function(jsonData){
			$([stlazyloading, highdpiInit]).each(function(i, f) {
				if (typeof f == 'function') {
					f();
				}
			});
		},
	};
}
/* since 2.8.7 */
