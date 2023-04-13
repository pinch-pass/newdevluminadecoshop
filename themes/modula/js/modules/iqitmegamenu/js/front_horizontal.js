/**
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/


var cbpHorizontalMenu;



$(document).ready(function(){

	cbpHorizontalMenu = (function() {

	var menuId = '#cbp-hrmenu',
		$listItems = $( menuId + '> ul > li'  ),
		$menuItems = $listItems.children( 'a, .cbp-main-link' ), 
		$innerTabs = $( menuId + ' .cbp-hrsub-tabs-names li > a'  ),
		$body = $( 'body' ),
		current = -1;
		currentlevel = -1;

	function init() {
		
		var isTouchDevice = 'ontouchstart' in document.documentElement;
		if( isTouchDevice ) {
			$menuItems.on( 'mouseover', open );
		}
		else{
			$menuItems.hoverIntent( {
			over: open,
			out: dnthing,
			interval: 30
		} );
		}

		$listItems.on( 'mouseover', function( event ) { event.stopPropagation(); } );

		$innerTabs.click(function(event){
  			event.preventDefault();
  			link = $(this).data('link');
  			if (typeof link != 'undefined') {
  				window.location.href = link;
			}
  	
		});

		$innerTabs.hover( function(){
    	  $(this).tab('show');
   		});
	}

	var setCurrent = function(strName) {
        current = strName;
    };

   	function dnthing( event ) {
   		return false;
   	}

	function open( event ) {

		$othemenuitem = $('#cbp-hrmenu1').find('.cbp-hropen');


		$othemenuitem.find('.cbp-hrsub').removeClass('cbp-show');
		$othemenuitem.removeClass( 'cbp-hropen' );

		cbpVerticalmenu.setCurrent(-1);

		var $item = $( event.currentTarget ).parent( 'li' ),
			idx = $item.index();

		
		$submenu = $item.find('.cbp-hrsub');

		if(current == idx )
			return;

		$submenu.removeClass('cbp-notfit');
		$submenu.removeClass('cbp-show');

		if( current !== -1 ) {
			$listItems.eq( current ).removeClass( 'cbp-hropen' );
		}

		if( current === idx ) {
			$item.removeClass( 'cbp-hropen' );
			current = -1;
			
		}
		else {
			$submenu.addClass( 'cbp-show' );
			iqitmenuwidth = $(menuId).width();
			iqititemposition = $item.position().left;
			
			if (typeof iqit_inlineh != 'undefined') {
				if(($('#desktop-header').width()-iqititemposition)<$submenu.width())
					$submenu.addClass( 'cbp-notfit' );
			}
			else{
				if((iqitmenuwidth-iqititemposition)<$submenu.width())
					$submenu.addClass( 'cbp-notfit' );
			}			
			
			
			
			$item.addClass( 'cbp-hropen' );
			current = idx;
			$body.off( 'mouseover' ).on( 'mouseover', close );
		}

		

		return false;

	}

	function close( event ) {
		$listItems.eq( current ).removeClass( 'cbp-hropen' );
		current = -1;
	}

	return { init : init,
			  setCurrent: setCurrent
			};

})();

	cbpHorizontalMenu.init();

});

