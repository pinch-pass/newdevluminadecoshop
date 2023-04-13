<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

//if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "google"))
//{
//	http_response_code(404);
//	die();
//}
require(dirname(__FILE__).'/../config/config.inc.php');
if(Tools::isSubmit('change')) {


	if(Configuration::get('lumina_use_new_images')) {
		Configuration::updateValue('lumina_use_new_images', 0);
		echo "Wyłączono (0)";
	} else {
		Configuration::updateValue('lumina_use_new_images', 1);
		echo "Włączono (1)";
	}
    
	
}

$img_set = Configuration::get('lumina_use_new_images');

echo "<form method='POST'>";
if($img_set == true)
    echo "Nowa metoda zdjęć jest włączona ({$img_set})";
else
    echo "Nowa metoda zdjęć jest wyłączona ({$img_set})";
echo "<br><button name='change'>Przełącz</button>";
echo "</form>";