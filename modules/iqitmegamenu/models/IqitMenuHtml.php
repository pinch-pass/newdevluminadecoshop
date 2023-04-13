<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class IqitMenuHtml extends ObjectModel
{
	public $title;
	public $html;


	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'iqitmegamenu_htmlc',
		'primary' => 'id_html',
		'multilang' => true,
		'fields' => array(
			'title' 		=>		array('type' => self::TYPE_STRING,  'validate' => 'isCleanHtml', 'size' => 255),
	
			//submenu
			'html' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),

	
		)
	);

	public	function __construct($id_html = null, $id_lang = null, $id_shop = null, Context $context = null)
	{
		parent::__construct($id_html, $id_lang, $id_shop);
	}

	public function add($autodate = true, $null_values = false)
	{
		
		$context = Context::getContext();
		$id_shop = $context->shop->id;

		$res = parent::add($autodate, $null_values);
		$res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'iqitmegamenu_html` (`id_html`, `id_shop`)
			VALUES('.(int)$this->id.', '.(int)$id_shop.')'
		);
		return $res;
	
	}

	public function delete()
	{
		
		$res = true;

		$tab = new IqitMenuHtml((int)$this->id);

		$res &= Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'iqitmegamenu_html`
			WHERE `id_html` = '.(int)$this->id
		);

		$res &= parent::delete();
		return $res;
	}

	public static function getHtmls()
	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		$id_lang = $context->language->id;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_html` as id_html, hss.`title`, hssl.`html`
			FROM '._DB_PREFIX_.'iqitmegamenu_html hs
			LEFT JOIN '._DB_PREFIX_.'iqitmegamenu_htmlc hss ON (hs.id_html = hss.id_html)
			LEFT JOIN '._DB_PREFIX_.'iqitmegamenu_htmlc_lang hssl ON (hs.id_html = hssl.id_html)
			WHERE hs.id_shop = '.(int)$id_shop.' 
			AND hssl.id_lang = '.(int)$id_lang
		);
	}


	public static function htmlExists($id_html)
	{
		$req = 'SELECT hs.`id_html` as id_html
				FROM `'._DB_PREFIX_.'iqitmegamenu_html` hs
				WHERE hs.`id_html` = '.(int)$id_html;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

		return ($row);
	}





}
