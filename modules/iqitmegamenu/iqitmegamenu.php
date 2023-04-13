<?php
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
*/

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'iqitmegamenu/models/IqitMenuTab.php');
include_once(_PS_MODULE_DIR_.'iqitmegamenu/models/IqitMenuHtml.php');
include_once(_PS_MODULE_DIR_.'iqitmegamenu/models/IqitMenuLinks.php');

class IqitMegaMenu extends Module
{
	protected $config_form = false;
	private $_html = '';
	private $user_groups;
	private $hor_sm_order;
	private $ver_position = 0;


	private $pattern = '/^([A-Z_]*)[0-9]+/';
	private $spacer_size = '5';

	public function __construct()
	{
		$this->name = 'iqitmegamenu';
		$this->tab = 'front_office_features';
		$this->version = '1.1.1';
		$this->author = 'IQIT-COMMERCE.COM';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);
		$this->module_key = '';
		$this->iqitdevmode = false;

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();
		
		$this->displayName = $this->l('IQITMEGAMENU - Advanced navigation drag and drop builder' );
		$this->description = $this->l('Advanced megamenu creator');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

		$this->config_name = 'iqitmegamenu';
		$this->ver_position = Configuration::get($this->config_name.'_ver_position');
		$this->defaults_mobile = array(
			'mobile_menu' => 'HOME0,CAT3,CAT26',
			'mobile_menu_style' => 1,
			'mobile_menu_depth' => 3,
			'hor_mb_bg' => '#000000',
			'hor_mb_txt' => '#ffffff',
			'hor_mb_border' => '1;1;#cecece',
			'hor_mb_c_bg' => '#ffffff',
			'hor_mb_csl_bg' => '#f8f8f8',
			'hor_mb_c_border' => '1;1;#cecece',
			'hor_mb_c_borderi' => '1;1;#cecece',
			'hor_mb_c_txt' => '#777777',
			'hor_mb_c_txth' => '#777777',
			'hor_mb_c_lhbg' => '#e5e5e5',
			'hor_mb_c_plus' => '#777777',
			'hor_mb_c_plusbg' => '#ffffff',
			);
		$this->defaults_horizontal = array(
			'hor_width' => 1,
			'hor_sw_width' => 1,
			'hor_s_width' => 1,
			'hor_sticky' => 1,
			'hor_s_transparent' => 1,
			'hor_animation' => 2,
			'hor_center' => 0,
			'hor_arrow' => 1,
			'hor_icon_position' => 0,
			'hor_maxwidth' => 400,
			'hor_s_arrow' => 1,
			'hor_bg_color' => '#000000',
			'hor_bg_image' => '',
			'hor_bg_repeat' => 0,
			'hor_border_top' => '1;0;#cecece',
			'hor_border_bottom' => '1;0;#cecece',
			'hor_border_sides' => '1;0;#cecece',
			'hor_border_inner' => '1;0;#cecece',
			'hor_link_txt_color' => '#ffffff',
			'hor_link_htxt_color' => '#000000',
			'hor_link_hbg_color' => '#fafafa',
			'hor_legend_txt_color' => '#ffffff',
			'hor_legend_bg_color' => '#CA5058',
			'hor_lineheight' => 45,
			'hor_link_padding' => 14,
			'hor_link_paddingb' => 20,
			'hor_link_bold' => 0,
			'hor_link_italics' => 0,
			'hor_link_uppercase' => 1,
			'hor_link_fontsize' => 14,
			'hor_link_fontsizeb' => 14,
			'hor_icon_fontsize' => 14,
			'hor_search_bg_color' => '#ffffff',
			'hor_search_txt' => '#777777',
			'hor_search_border' => '1;1;#cecece',
			'hor_search_height' => 30,
			'hor_search_width' => 200,
			'hor_sm_order' => 0,
			'hor_sm_bg_color' => '#ffffff',
			'hor_sm_bg_image' => '',
			'hor_sm_bg_repeat' => 0,
			'hor_sm_boxshadow' => 1,
			'hor_sm_border_top' => '1;1;#cecece',
			'hor_sm_border_bottom' => '1;1;#cecece',
			'hor_sm_border_sides' => '1;1;#cecece',
			'hor_sm_border_inner' => '1;1;#cecece',
			'hor_sm_tab_txt_color' => '#777777',
			'hor_sm_tab_bg_color' => '#F9F9F9',
			'hor_sm_tab_htxt_color' => '#777777',
			'hor_sm_tab_hbg_color' => '#ffffff',
			'hor_titlep_fontsize' => 13,
			'hor_titlep_color' => '#777777',
			'hor_titlep_colorh' => '#333333',
			'hor_titlep_uppercase' => 1,
			'hor_titlep_bold' => 1,
			'hor_titlep_border'	=> 1,
			'hor_titlep_borders' => '1;1;#cecece',
			'hor_subtxt_fontsize' => 12,
			'hor_subtxt_color' => '#777777',
			'hor_subtxt_colorh' => '#333333',
			'hor_subtxt_arrow' => 1,
			'hor_subtxt_level' => 1,
			'hor_custom_css' => '',
			);
		$this->defaults_vertical = array(
			'ver_position' => 0,
			'ver_animation' => 2,
			'ver_arrow' => 1,
			'ver_s_arrow' => 1,
			'ver_boxshadow' => 1,
			'ver_title_bg' => '#000000',
			'ver_title_bgh' => '#282828',
			'ver_title_txt' => '#ffffff',
			'ver_title_txth' => '#ffffff',

			'ver_title_size' => 14,
			'ver_title_height' => 45,
			'ver_title_bold' => 0,
			'ver_title_uppercase' => 1,

			'ver_bg_color' => 'transparent',
			'ver_bg_image' => '',
			'ver_bg_repeat' => 0,
			'ver_border_top' => '1;0;#cecece',
			'ver_border_bottom' => '1;1;#cecece',
			'ver_border_sides' => '1;1;#cecece',
			'ver_border_inner' => '1;1;#cecece',
			'ver_link_txt_color' => '#777777',
			'ver_link_htxt_color' => '#000000',
			'ver_link_hbg_color' => '#fafafa',
			'ver_legend_txt_color' => '#ffffff',
			'ver_legend_bg_color' => '#CA5058',
			'ver_link_padding' => 20,
			'ver_link_bold' => 0,
			'ver_link_italics' => 0,
			'ver_link_uppercase' => 1,
			'ver_link_fontsize' => 14,
			'ver_icon_fontsize' => 14,
			);

	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install()
	{
		if (parent::install() &&
			$this->registerHook('header') &&
			$this->registerHook('backOfficeHeader') &&
			$this->registerHook('maxHeader') &&
			$this->registerHook('iqitMegaMenu') &&
			$this->registerHook('displayLeftColumn') &&
			$this->registerHook('actionObjectCategoryUpdateAfter') &&
			$this->registerHook('actionObjectCategoryDeleteAfter') &&
			$this->registerHook('actionObjectCategoryAddAfter') &&
			$this->registerHook('actionObjectCmsUpdateAfter') &&
			$this->registerHook('actionObjectCmsDeleteAfter') &&
			$this->registerHook('actionObjectCmsAddAfter') &&
			$this->registerHook('actionObjectSupplierUpdateAfter') &&
			$this->registerHook('actionObjectSupplierDeleteAfter') &&
			$this->registerHook('actionObjectSupplierAddAfter') &&
			$this->registerHook('actionObjectManufacturerUpdateAfter') &&
			$this->registerHook('actionObjectManufacturerDeleteAfter') &&
			$this->registerHook('actionObjectManufacturerAddAfter') &&
			$this->registerHook('actionObjectProductUpdateAfter') &&
			$this->registerHook('actionObjectProductDeleteAfter') &&
			$this->registerHook('actionObjectProductAddAfter') &&
			$this->registerHook('categoryUpdate') &&
			$this->registerHook('actionShopDataDuplication') &&
			$this->createTables())
		{
			$this->installSamples();
			$this->setDefaults();
			$this->generateCss();
			return true;
		}
		else return false;
	}

	public function uninstall()
	{
		foreach ($this->defaults_mobile as $default => $value)
				Configuration::deleteByName($this->config_name.'_'.$default);

		foreach ($this->defaults_horizontal as $default => $value)
				Configuration::deleteByName($this->config_name.'_'.$default);

		foreach ($this->defaults_vertical as $default => $value)
				Configuration::deleteByName($this->config_name.'_'.$default);

		return parent::uninstall() && $this->deleteTables();
	}

	/**
	 * Creates tables
	 */
	protected function createTables()
	{
		/* tabs */
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'iqitmegamenu_tabs_shop` (
				`id_tab` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_tab`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* tabs configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'iqitmegamenu_tabs` (
			  `id_tab` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `menu_type` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `active_label` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `position` int(10) unsigned NOT NULL DEFAULT \'0\',
			  `url_type` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `id_url` varchar(64) NULL,
			  `icon_type` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `icon_class` varchar(64) NULL,
			  `icon` varchar(255) NULL,
			  `legend_icon` varchar(64) NULL,
			  `new_window` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `float` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `bg_color` varchar(64) NULL,
			  `txt_color` varchar(64) NULL,
			  `h_bg_color` varchar(64) NULL,
			  `h_txt_color` varchar(64) NULL,
			  `labelbg_color` varchar(64) NULL,
			  `labeltxt_color` varchar(64) NULL,
			  `submenu_content` text NULL,
			  `submenu_type` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `submenu_width` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `submenu_bg_color` varchar(64) NULL,
			  `submenu_image` varchar(64) NULL,
			  `submenu_repeat` tinyint(1) unsigned NULL DEFAULT \'0\',
			  `submenu_bg_position` tinyint(1) unsigned NULL DEFAULT \'0\',		  
			  `submenu_link_color` varchar(64) NULL,
			  `submenu_hover_color` varchar(64) NULL,
			  `submenu_title_color` varchar(64) NULL,
			  `submenu_title_colorh` varchar(64) NULL,
			  `submenu_titleb_color` varchar(64) NULL,
			  `submenu_border_t` varchar(64) NULL,
			  `submenu_border_r` varchar(64) NULL,
			  `submenu_border_b` varchar(64) NULL,
			  `submenu_border_l` varchar(64) NULL,
			  `submenu_border_i` varchar(64) NULL,
			  `group_access` TEXT NOT NULL,
			  PRIMARY KEY (`id_tab`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* tabs lang configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'iqitmegamenu_tabs_lang` (
			  `id_tab` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `label` varchar(255) NULL,
			  `url` varchar(255) NULL,
			  PRIMARY KEY (`id_tab`,`id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');



		/* custom links */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'iqitmenulinks` (
			`id_iqitmenulinks` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_shop` INT(11) UNSIGNED NOT NULL,
			`new_window` TINYINT( 1 ) NOT NULL,
			INDEX (`id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* custom links lang */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'iqitmenulinks_lang` (
			`id_iqitmenulinks` INT(11) UNSIGNED NOT NULL,
			`id_lang` INT(11) UNSIGNED NOT NULL,
			`id_shop` INT(11) UNSIGNED NOT NULL,
			`label` VARCHAR( 128 ) NOT NULL ,
			`link` VARCHAR( 128 ) NOT NULL ,
			INDEX ( `id_iqitmenulinks` , `id_lang`, `id_shop`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* custom html */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'iqitmegamenu_html` (
			`id_html` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_shop` INT(11) UNSIGNED NOT NULL,
			INDEX (`id_html`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'iqitmegamenu_htmlc` (
			  `id_html` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) NOT NULL ,
			  PRIMARY KEY (`id_html`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* custom html lang */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'iqitmegamenu_htmlc_lang` (
			`id_html` INT(11) UNSIGNED NOT NULL,
			`id_lang` INT(11) UNSIGNED NOT NULL,
			`id_shop` INT(11) UNSIGNED NOT NULL,
			`html` text NULL,
			INDEX ( `id_html` , `id_lang`, `id_shop`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		return $res;
	}

	/**
	 * deletes tables
	 */
	protected function deleteTables()
	{

		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `'._DB_PREFIX_.'iqitmegamenu_tabs_shop`, `'._DB_PREFIX_.'iqitmegamenu_tabs`, `'._DB_PREFIX_.'iqitmegamenu_tabs_lang`, `'._DB_PREFIX_.'iqitmenulinks`, `'._DB_PREFIX_.'iqitmenulinks_lang`, `'._DB_PREFIX_.'iqitmegamenu_html`, `'._DB_PREFIX_.'iqitmegamenu_htmlc`, `'._DB_PREFIX_.'iqitmegamenu_htmlc_lang`;
		');
	}

	public function setDefaults()
	{
		foreach ($this->defaults_mobile as $default => $value)
		{
			Configuration::updateValue($this->config_name.'_'.$default, $value);
		}
		foreach ($this->defaults_horizontal as $default => $value)
		{
			Configuration::updateValue($this->config_name.'_'.$default, $value);
		}
		foreach ($this->defaults_vertical as $default => $value)
		{
			Configuration::updateValue($this->config_name.'_'.$default, $value);
		}
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{	
		
		if (Tools::getValue('controller') != 'AdminModules' && Tools::getValue('configure') != $this->name)
			return;

		if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return $this->getWarningMultishopHtml();
        }

		Media::addJsDef(array('iqitsearch_url' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name)); 
		$this->context->controller->addJS($this->_path . 'js/jquery.auto-complete.js');

		$this->context->controller->addJS($this->_path.'js/back.js');
		$this->context->controller->addCSS($this->_path.'css/back.css');

		$this->context->controller->addJS($this->_path.'js/fontawesome-iconpicker.min.js');
		$this->context->controller->addCSS($this->_path.'css/fontawesome-iconpicker.min.css');

		$this->context->controller->addJqueryUI('ui.sortable');

		$this->_html .= '<script type="text/javascript">
			$(function() {
				var $myHorizontalTabs = $("#tabs1");
				$myHorizontalTabs.sortable({
					opacity: 0.6,
					cursor: "move",
					update: function() {
						var order = $(this).sortable("serialize") + "&action=updateHorizontalTabsPosition&ajax=true";
						$.post("' . $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '", order);
						}
					});
				$myHorizontalTabs.hover(function() {
					$(this).css("cursor","move");
					},
					function() {
					$(this).css("cursor","auto");
				});
			
				var $myVerticalTabs = $("#tabs2");
				$myVerticalTabs.sortable({
					opacity: 0.6,
					cursor: "move",
					update: function() {
						var order = $(this).sortable("serialize") + "&action=updateVerticalTabsPosition&ajax=true";
						$.post("' . $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '", order);
						}
					});
				$myVerticalTabs.hover(function() {
					$(this).css("cursor","move");
					},
					function() {
					$(this).css("cursor","auto");
				});
			});
		</script>';

		$id_lang = (int)Context::getContext()->language->id;
		$languages = $this->context->controller->getLanguages();
		$default_language = (int)Configuration::get('PS_LANG_DEFAULT');

		$labels = Tools::getValue('label') ? array_filter(Tools::getValue('label'), 'strlen') : array();
		$links_label = Tools::getValue('link') ? array_filter(Tools::getValue('link'), 'strlen') : array();
		$spacer = str_repeat('&nbsp;', $this->spacer_size);
		$divLangName = 'link_label';

		$update_cache = false;

	
		if (Tools::isSubmit('addTab') || (Tools::isSubmit('id_tab')  && !Tools::isSubmit('submitAddTab') && IqitMenuTab::tabExists((int)Tools::getValue('id_tab'))))
			return $this->_html .= $this->renderAddForm();
		elseif(Tools::isSubmit('submitAddTab') || Tools::isSubmit('delete_id_tab') || Tools::isSubmit('duplicateTab') || Tools::isSubmit('duplicateTabC'))
		{

			
			if(!Tools::isSubmit('back_to_configuration'))
			{
				if ($this->_postValidation())
				{
					$this->_postProcess();
				}
			}
			
			$this->generateCss();
			$this->clearMenuCache();
			$update_cache = true;
		}
		elseif (Tools::isSubmit('addCustomHtml') || (Tools::isSubmit('id_html')  && !Tools::isSubmit('submitAddHtml') && IqitMenuHtml::htmlExists((int)Tools::getValue('id_html'))))
			return $this->_html .= $this->renderAddHtmlForm();
		elseif(Tools::isSubmit('submitAddHtml') || Tools::isSubmit('delete_id_html'))
		{
			if(!Tools::isSubmit('back_to_configuration'))
			{
				if ($this->_postValidationHtml())
				{
					$this->_postProcessHtml();
				}
			
		}
		$update_cache = true;
		$this->clearMenuCache();
		}
		elseif (Tools::isSubmit('exportConfiguration'))
		{
						
			$var =  array();

			foreach ($this->defaults_horizontal as $default => $value) 
					$var[$default] = Configuration::get($this->config_name.'_'.$default);

			foreach ($this->defaults_vertical as $default => $value) 
					$var[$default] = Configuration::get($this->config_name.'_'.$default);

			foreach ($this->defaults_mobile as $default => $value) 
			{
				if ($default == 'mobile_menu') 
					continue;
				else
					$var[$default] = Configuration::get($this->config_name.'_'.$default);
			}
		

			$file_name = 'iqitmegamenu_'.time().'.csv';
			$fd = fopen($this->getLocalPath().$file_name, 'w+');
			file_put_contents($this->getLocalPath().'export/'.$file_name, print_r(serialize($var), true));
			fclose($fd);
			Tools::redirect(_PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/export/'.$file_name);
		}

		elseif (Tools::isSubmit('importConfiguration'))
		{

			if(isset($_FILES['uploadConfig']) && isset($_FILES['uploadConfig']['tmp_name']))
			{
				$str = file_get_contents($_FILES['uploadConfig']['tmp_name']);
				$arr = unserialize($str);


				foreach ($arr as $default => $value)
						if(isset($value))
							Configuration::updateValue($this->config_name.'_'.$default, $value);					
						

					$this->generateCss();

					if (isset($errors) AND $errors!='')
						$this->_html .= $this -> displayError($errors);
					else
						$this->_html .= $this -> displayConfirmation($this->l('Configuration imported'));
				}
				else
					$this->_html .= $this -> displayError($this->l('No config file'));				

			}
			elseif (Tools::isSubmit('exportTabs')) {

            $var = IqitMenuTab::getAllShopTabs();

            $file_name = 'iqitmegamenu_tabs_' . time() . '.csv';
            $fd = fopen($this->getLocalPath() . $file_name, 'w+');
            file_put_contents($this->getLocalPath() . 'export/' . $file_name, print_r(serialize($var), true));
            fclose($fd);
            Tools::redirect(_PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/' . $this->name . '/export/' . $file_name);

        } elseif (Tools::isSubmit('importTabs')) {

            if (isset($_FILES['uploadTabs']) && isset($_FILES['uploadTabs']['tmp_name'])) {
                $str = Tools::file_get_contents($_FILES['uploadTabs']['tmp_name']);
                $arr = unserialize($str);

                foreach ($arr as $id_tab => $tab) {
                    if (Validate::isLoadedObject($tab)) {
                        $tab->add();
                    }
                }

                $this->_html .= $this->displayConfirmation($this->l('Tabs imported'));
            } else {
                $this->_html .= $this->displayError($this->l('No tabs data file'));
            }

        }
		
		elseif (Tools::isSubmit('submitHorizonalMenuConfig'))
		{
			foreach ($this->defaults_horizontal as $default => $value) 
			{	
				if($default == 'hor_search_border' || $default == 'hor_titlep_borders' || $default == 'hor_border_top' ||  $default == 'hor_border_bottom' || $default == 'hor_border_sides' || $default == 'hor_border_inner' || $default == 'hor_sm_border_top' ||  $default == 'hor_sm_border_bottom' || $default == 'hor_sm_border_sides' || $default == 'hor_sm_border_inner')
					Configuration::updateValue($this->config_name.'_'.$default, Tools::getValue($default.'_width').';'.Tools::getValue($default.'_type').';'.Tools::getValue($default.'_color'));
				elseif($default == 'hor_custom_css')
				{
					if (isset($_POST[$default]))
						Configuration::updateValue($this->config_name.'_'.$default, $_POST[$default]);			
				}
				else
				Configuration::updateValue($this->config_name.'_'.$default, (Tools::getValue($default)));
			}

			$this->generateCss();
			$update_cache = true;
			$this->clearMenuCache();
		}
		elseif (Tools::isSubmit('submitVerticalMenuConfig'))
		{
			foreach ($this->defaults_vertical as $default => $value)
			{
				if($default == 'ver_border_top' ||  $default == 'ver_border_bottom' || $default == 'ver_border_sides' || $default == 'ver_border_inner')
					Configuration::updateValue($this->config_name.'_'.$default, Tools::getValue($default.'_width').';'.Tools::getValue($default.'_type').';'.Tools::getValue($default.'_color'));
				else
				Configuration::updateValue($this->config_name.'_'.$default, (Tools::getValue($default)));
			}

			$this->generateCss();
			$update_cache = true;
			$this->clearMenuCache();
		}
		elseif (Tools::isSubmit('submitMobileMenu'))
		{
			$errors_update_shops = array();
			$items = Tools::getValue('items');
			$shops = Shop::getContextListShopID();
		

			foreach ($shops as $shop_id)
			{
				$shop_group_id = Shop::getGroupFromShop($shop_id);
				$updated = true;

				if (count($shops) == 1)
				{
					if (is_array($items) && count($items))
		 				$updated = Configuration::updateValue($this->config_name.'_mobile_menu', (string)implode(',', $items), false, (int)$shop_group_id, (int)$shop_id);
		 			else
		 				$updated = Configuration::updateValue($this->config_name.'_mobile_menu', '', false, (int)$shop_group_id, (int)$shop_id);
		 		}

	 			if (!$updated)
	 			{
	 				$shop = new Shop($shop_id);
	 				$errors_update_shops[] =  $shop->name;
	 			}

			}

			foreach ($this->defaults_mobile as $default => $value) 
			{	
				if($default == 'hor_mb_border' || $default == 'hor_mb_c_border' || $default == 'hor_mb_c_borderi')
					Configuration::updateValue($this->config_name.'_'.$default, Tools::getValue($default.'_width').';'.Tools::getValue($default.'_type').';'.Tools::getValue($default.'_color'));
				elseif ($default == 'mobile_menu') 
					continue;
				else
					Configuration::updateValue($this->config_name.'_'.$default, (Tools::getValue($default)));
			}

 			if (!count($errors_update_shops))
				$this->_html .= $this->displayConfirmation($this->l('The settings have been updated.'));
			else
				$this->_html .= $this->displayError(sprintf($this->l('Unable to update settings for the following shop(s): %s'), implode(', ', $errors_update_shops)));

			$this->generateCss();
			$update_cache = true;
			$this->clearMenuCache();
		}
		elseif (Tools::isSubmit('submitBlocktopmenuLinks') || Tools::isSubmit('submitupdateiqitmenulinks') )
		{	
			
			$errors_add_link = array();

			foreach ($languages as $key => $val)
			{
				$links_label[$val['id_lang']] = Tools::getValue('link_'.(int)$val['id_lang']);
				$labels[$val['id_lang']] = Tools::getValue('label_'.(int)$val['id_lang']);
			}

		

			$count_links_label = count($links_label);
			$count_label = count($labels);

			if ($count_links_label || $count_label)
			{
				if (!$count_links_label)
					$this->_html .= $this->displayError($this->l('Please complete the "Link" field.'));
				elseif (!$count_label)
					$this->_html .= $this->displayError($this->l('Please add a label.'));
				elseif (!isset($labels[$default_language]))
					$this->_html .= $this->displayError($this->l('Please add a label for your default language.'));
				else
				{
					$shops = Shop::getContextListShopID();

					foreach ($shops as $shop_id)
					{	
						if(Tools::getValue('id_iqitmenulinks') > 0)
							$added = IqitMenuLinks::update($links_label, $labels,  Tools::getValue('new_window', 0), (int)$shop_id, Tools::getValue('id_iqitmenulinks'));
						else
						{
							$added = IqitMenuLinks::add($links_label, $labels,  Tools::getValue('new_window', 0), (int)$shop_id);

							if (!$added)
						{
							$shop = new Shop($shop_id);
 							$errors_add_link[] =  $shop->name;
						}
						}
					}

					if (!count($errors_add_link))
						$this->_html .= $this->displayConfirmation($this->l('The link has been added.'));
					else
						$this->_html .= $this->displayError(sprintf($this->l('Unable to add link for the following shop(s): %s'), implode(', ', $errors_add_link)));
				}
			}
			$update_cache = true;
			$this->clearMenuCache();
			

		}
		elseif (Tools::isSubmit('deleteiqitmenulinks'))
		{	
			$errors_delete_link = array();
			$id_iqitmenulinks = Tools::getValue('id_iqitmenulinks', 0);
			$shops = Shop::getContextListShopID();

			foreach ($shops as $shop_id)
			{
				$deleted = IqitMenuLinks::remove($id_iqitmenulinks, (int)$shop_id);
				Configuration::updateValue($this->config_name.'_mobile_menu', str_replace(array('LNK'.$id_iqitmenulinks.',', 'LNK'.$id_iqitmenulinks), '', Configuration::get($this->config_name.'_mobile_menu')));

				if (!$deleted)
				{
					$shop = new Shop($shop_id);
					$errors_delete_link[] =  $shop->name;
				}

			}

			if (!count($errors_delete_link))
				$this->_html .= $this->displayConfirmation($this->l('The link has been removed.'));
			else
				$this->_html .= $this->displayError(sprintf($this->l('Unable to remove link for the following shop(s): %s'), implode(', ', $errors_delete_link)));

			$update_cache = true;
			$this->clearMenuCache();
		}
		
		$this->_html .= '<div class="list-wrapper list-wrapper-horizontal">'.$this->renderTabsLinks(1).'</div>';
		$this->_html .= '<div class="list-wrapper list-wrapper-vertical">'.$this->renderTabsLinks(2).'</div>';
		$this->_html .= '<div class="list-wrapper list-wrapper-submenutabs">'.$this->renderTabsLinks(3).'</div>';
		$this->_html .= '<div class="list-wrapper list-wrapper-html">'.$this->renderHtmlContents().'</div>';
		$this->_html .= $this->renderForm();

		return $this->_html; 
	}

	private function _postValidation()
	{
		$errors = array();

		/* Validation for tab */
		if (Tools::isSubmit('submitAddTab'))
		{
			/* If edit : checks id_tab */
			if (Tools::isSubmit('id_tab'))
			{
				if (!Validate::isInt(Tools::getValue('id_tab')) && !IqitMenuTab::tabExists(Tools::getValue('id_tab')))
					$errors[] = $this->l('Invalid id_tab');
			}
			if (!Validate::isInt(Tools::getValue('position')) || (Tools::getValue('position') < 0))
				$errors[] = $this->l('Invalid tab position.');

			/* Checks title/description/*/
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::strlen(Tools::getValue('title_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The title is too long.');
				if (Tools::strlen(Tools::getValue('label_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The label is too long.');
			
			}

			/* Checks title/description for default lang */
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('title_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The title is not set.');
		} 
		/* Validation for deletion */
		elseif (Tools::isSubmit('delete_id_tab') && (!Validate::isInt(Tools::getValue('delete_id_tab')) || !IqitMenuTab::tabExists((int)Tools::getValue('delete_id_tab'))))
			$errors[] = $this->l('Invalid id_tab');

		/* Display errors if needed */
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));

			return false;
		}

		/* Returns if validation is ok */

		return true;
	}

	private function _postProcess()
	{
		$errors = array();

		/* Processes tab */
		if (Tools::isSubmit('submitAddTab'))
		{
			/* Sets ID if needed */
			if (Tools::getValue('id_tab'))
			{ 
				$tab = new IqitMenuTab((int)Tools::getValue('id_tab'));
				if (!Validate::isLoadedObject($tab))
				{
					$this->_html .= $this->displayError($this->l('Invalid id_tab'));

					return false;
				}
			}
			else{
				$tab = new IqitMenuTab();
				$tab->menu_type = Tools::getValue('menu_type');
				$tab->position = IqitMenuTab::getNextPosition(Tools::getValue('menu_type'));
				}
			
			//vals
			
			$tab->active = Tools::getValue('active');
			$tab->active_label = Tools::getValue('active_label');

			
			$tab->url_type = Tools::getValue('url_type');
			$tab->id_url = Tools::getValue('id_url');
			$tab->icon_type = Tools::getValue('icon_type');
			$tab->icon = Tools::getValue('icon');
			$tab->icon_class = Tools::getValue('icon_class');
			$tab->legend_icon = Tools::getValue('legend_icon');
			$tab->new_window = Tools::getValue('new_window');
			$tab->float = Tools::getValue('float');
	
			//colors
			$tab->bg_color = Tools::getValue('bg_color');
			$tab->txt_color = Tools::getValue('txt_color');
			$tab->h_bg_color = Tools::getValue('h_bg_color');
			$tab->h_txt_color = Tools::getValue('h_txt_color');
			$tab->labelbg_color = Tools::getValue('labelbg_color');
			$tab->labeltxt_color = Tools::getValue('labeltxt_color');

			//submenu
			$submenu_type = Tools::getValue('submenu_type');

			$tab->submenu_type = $submenu_type;
			$tab->submenu_width = Tools::getValue('submenu_width');
			$tab->submenu_bg_color = Tools::getValue('submenu_bg_color');
			$tab->submenu_image = Tools::getValue('submenu_image');
			$tab->submenu_repeat = Tools::getValue('submenu_repeat');
			$tab->submenu_bg_position = Tools::getValue('submenu_bg_position');
			$tab->submenu_link_color = Tools::getValue('submenu_link_color');
			$tab->submenu_hover_color = Tools::getValue('submenu_hover_color');

			$tab->submenu_title_color = Tools::getValue('submenu_title_color');
			$tab->submenu_title_colorh = Tools::getValue('submenu_title_colorh');
			$tab->submenu_titleb_color = Tools::getValue('submenu_titleb_color');
			$tab->submenu_border_t = Tools::getValue('submenu_border_t');
			$tab->submenu_border_r = Tools::getValue('submenu_border_r');
			$tab->submenu_border_b = Tools::getValue('submenu_border_b');
			$tab->submenu_border_l = Tools::getValue('submenu_border_l');
			$tab->submenu_border_i = Tools::getValue('submenu_border_i');

			$id_shop_list = Tools::getValue('checkBoxShopAsso_iqitmegamenu_tabs');

            if (isset($id_shop_list) && !empty($id_shop_list)) {
                $tab->id_shop_list = $id_shop_list;
            } else {
                $tab->id_shop_list =  (int)Context::getContext()->shop->id;
            }

            $groups = Group::getGroups($this->context->language->id);
            $groupBox = Tools::getValue('groupBox', array());
            $group_access = array();

            if (!$groupBox) {
                foreach ($groups as $group) {
                    $group_access[$group['id_group']] = false;
                }
            } else {
                foreach ($groups as $group) {
                    $group_access[$group['id_group']] = in_array($group['id_group'], $groupBox);
                }
            }

            $tab->group_access = serialize($group_access);

			$tab->submenu_content = '';

			if($submenu_type==1)
			{
				if (is_array(Tools::getValue('items')) && count(Tools::getValue('items')))
		 				$tab->submenu_content = (string)implode(',', Tools::getValue('items'));
		 			else
		 				$tab->submenu_content = '';
			}

			if($submenu_type==2)
			{	
		 		$tab->submenu_content = urldecode(Tools::getValue('submenu-elements'));
			}		

			/* Sets each langue fields */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				$tab->title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
				$tab->label[$language['id_lang']] = Tools::getValue('label_'.$language['id_lang']);
				$tab->url[$language['id_lang']] = Tools::getValue('url_'.$language['id_lang']);
				

			}

			/* Processes if no errors  */
			if (!$errors)
			{
				/* Adds */
				if (!Tools::getValue('id_tab'))
				{
					if (!$tab->add())
						$errors[] = $this->displayError($this->l('The tab could not be added.'));
				}
				/* Update */
				elseif (!$tab->update())
					$errors[] = $this->displayError($this->l('The tab could not be updated.'));
				$this->clearMenuCache();
			}
		} /* Deletes */
		elseif (Tools::isSubmit('delete_id_tab'))
		{
			$tab = new IqitMenuTab((int)Tools::getValue('delete_id_tab'));
			$res = $tab->delete();
			$this->clearMenuCache();
			if (!$res)
				$this->_html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		}
		elseif (Tools::isSubmit('duplicateTab'))
		{	

		
			$this->duplicateMultistoreTab((int)Tools::getValue('duplicateTab'));
			$this->generateCss();
			$this->clearMenuCache();
		
			//Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		}

		elseif (Tools::isSubmit('duplicateTabC'))
		{	

		
			$this->duplicateTab((int)Tools::getValue('duplicateTabC'));
			$this->generateCss();
			$this->clearMenuCache();
		
			//Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		}
		
		$this->generateCss();
		$this->clearMenuCache();

		/* Display errors if needed */
		if (count($errors))
			$this->_html .= $this->displayError(implode('<br />', $errors));
		elseif (Tools::isSubmit('submitAddTab') && Tools::getValue('id_tab'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		elseif (Tools::isSubmit('submitAddTab'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
	}

	private function _postValidationHtml()
	{
		$errors = array();

		/* Validation for tab */
		if (Tools::isSubmit('submitAddHtml'))
		{
			/* If edit : checks id_tab */
			if (Tools::isSubmit('id_html'))
			{
				if (!Validate::isInt(Tools::getValue('id_html')) && !IqitMenuHtml::htmlExists(Tools::getValue('id_html')))
					$errors[] = $this->l('Invalid id_html');
			}

			if (!Tools::strlen(Tools::getValue('title')))
					$errors[] = $this->l('Title is not set');

			/* Checks title/description for default lang */
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('html_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The html is not set');
		} 
		/* Validation for deletion */
		elseif (Tools::isSubmit('delete_id_html') && (!Validate::isInt(Tools::getValue('delete_id_html')) || !IqitMenuHtml::htmlExists((int)Tools::getValue('delete_id_html'))))
			$errors[] = $this->l('Invalid id_html');

		/* Display errors if needed */
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));

			return false;
		}

		/* Returns if validation is ok */

		return true;
	}

	private function _postProcessHtml()
	{
		$errors = array();

		/* Processes tab */
		if (Tools::isSubmit('submitAddHtml'))
		{
			/* Sets ID if needed */
			if (Tools::getValue('id_html'))
			{ 
				$tab = new IqitMenuHtml((int)Tools::getValue('id_html'));
				if (!Validate::isLoadedObject($tab))
				{
					$this->_html .= $this->displayError($this->l('Invalid id_tab'));

					return false;
				}
			}
			else
				$tab = new IqitMenuHtml();

			
			$tab->title = Tools::getValue('title');
		
			/* Sets each langue fields */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
				$tab->html[$language['id_lang']] = Tools::getValue('html_'.$language['id_lang']);
				
	

			/* Processes if no errors  */
			if (!$errors)
			{
				/* Adds */
				if (!Tools::getValue('id_html'))
				{
					if (!$tab->add())
						$errors[] = $this->displayError($this->l('The html content could not be added.'));
				}
				/* Update */
				elseif (!$tab->update())
					$errors[] = $this->displayError($this->l('The html could not be updated.'));
				$this->clearMenuCache();
			}
		} /* Deletes */
		elseif (Tools::isSubmit('delete_id_html'))
		{
			$tab = new IqitMenuHtml((int)Tools::getValue('delete_id_html'));
			$res = $tab->delete();
			$this->clearMenuCache();
			if (!$res)
				$this->_html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		}
			
		/* Display errors if needed */
		if (count($errors))
			$this->_html .= $this->displayError(implode('<br />', $errors));
		elseif (Tools::isSubmit('submitAddTab') && Tools::getValue('id_tab'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		elseif (Tools::isSubmit('submitAddTab'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
	}

	public function renderAddForm()
	{	

		if(Tools::getValue('menu_type') == 3)
		{
			$options_type = array(
				array(
					'id_option' => 2,
					'name' => $this->l('Grid submenu')
					)
				);
		}
		else{
			$options_type = array(
				array(
					'id_option' => 2,
					'name' => $this->l('Grid submenu')
					),
				array(
					'id_option' => 1,
					'name' => $this->l('Predefinied tabs')
					),
				array(
					'id_option' => 0,
					'name' => $this->l('Hidden')
					)
				);
		}

		$columns_width = array(
			array(
				'id_option' => 1,
				'name' => $this->l('1/12')
				),
			array(
				'id_option' => 2,
				'name' => $this->l('2/12')
				),
			array(
				'id_option' => 3,
				'name' => $this->l('3/12')
				),
			array(
				'id_option' => 4,
				'name' => $this->l('4/12')
				),
			array(
				'id_option' => 5,
				'name' => $this->l('5/12')
				),
			array(
				'id_option' => 6,
				'name' => $this->l('6/12')
				),
			array(
				'id_option' => 7,
				'name' => $this->l('7/12')
				),
			array(
				'id_option' => 8,
				'name' => $this->l('8/12')
				),
			array(
				'id_option' => 9,
				'name' => $this->l('9/12')
				),
			array(
				'id_option' => 10,
				'name' => $this->l('10/12')
				),
			array(
				'id_option' => 11,
				'name' => $this->l('11/12')
				),
			array(
				'id_option' => 12,
				'name' => $this->l('12/12')
				),
			);

		$unidentified = new Group(Configuration::get('PS_UNIDENTIFIED_GROUP'));
        $guest = new Group(Configuration::get('PS_GUEST_GROUP'));
        $default = new Group(Configuration::get('PS_CUSTOMER_GROUP'));

        $unidentified_group_information = sprintf($this->l('%s - All people without a valid customer account.'), '<b>' . $unidentified->name[$this->context->language->id] . '</b>');
        $guest_group_information = sprintf($this->l('%s - Customer who placed an order with the guest checkout.'), '<b>' . $guest->name[$this->context->language->id] . '</b>');
        $default_group_information = sprintf($this->l('%s - All people who have created an account on this site.'), '<b>' . $default->name[$this->context->language->id] . '</b>');

		$fields_form = array(
			'form' => array(
				'tab_name' => 'main_tab',
				'legend' => array(
					'title' => $this->l('Add tab'),
					'icon' => 'icon-cogs',
					'id' => 'fff'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Active'),
						'name' => 'active',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Title'),
						'name' => 'title',
						'desc' => $this->l('Main title of tab'),
						'lang' => true,
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Hide title'),
						'desc' => $this->l('Useful if you want to create tab like home link'),
						'name' => 'active_label',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'select',
						'label' => $this->l('Icon type'),
						'name' => 'icon_type',
						'options' => array(
							'query' => array(
								array(
								'id_option' => 1,
								'name' => $this->l('Font icon class name')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('Image icon')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
					),
					array(
						'type' => 'icon_selector',
						'label' => $this->l('Icon class'),
						'name' => 'icon_class',
						'desc' => $this->l('For example: "icon-star". You can use font awesome icons here'),
						'preffix_wrapper' => 'icon-class-wrapper',
						'wrapper_hidden' => true,
						'suffix_wrapper' => true,
					),
					array(
					'type' => 'image_upload',
					'label' => $this->l('Icon'),
					'name' => 'icon',
					'preffix_wrapper' => 'image-icon-wrapper',
					'wrapper_hidden' => true,
					'suffix_wrapper' => true,
					),
					
				
					array(
						'type' => 'select',
						'label' => $this->l('Url type'),
						'name' => 'url_type',
						'options' => array(
							'query' => array(
							array(
								'id_option' => 2,
								'name' => $this->l('No url')
								),	
							array(
								'id_option' => 1,
								'name' => $this->l('Custom url')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('Content url(category, cms, etc)')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
					),
					array(
						'type' => 'custom_select',
						'label' =>$this->l('System url'),
						'name' => 'id_url',
						'choices' => $this->renderChoicesSelect(true, 'id_url'),
						'preffix_wrapper' => 'system-url-wrapper',
						'wrapper_hidden' => true,
						'suffix_wrapper' => true,
						),
					array(
						'type' => 'text',
						'label' => $this->l('Custom url'),
						'name' => 'url',
						'desc' => $this->l('Should be full url with http:// prefix'),
						'lang' => true,
						'preffix_wrapper' => 'custom-url-wrapper',
						'wrapper_hidden' => true,
						'suffix_wrapper' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Legend'),
						'name' => 'label',
						'desc' => $this->l('Additional text showed in tooltip'),
						'lang' => true,
					),
						array(
						'type' => 'icon_selector',
						'label' => $this->l('Legend icon class'),
						'name' => 'legend_icon',
						'desc' => $this->l('For example: "icon-star". You can use font awesome icons here'),
					),	
					array(
						'type' => 'switch',
						'label' => $this->l('New window'),
						'name' => 'new_window',
						'hide' => (Tools::getValue('menu_type') == 3 ? true : false),
						'is_bool' => true,
						'desc' => $this->l('Open link in new window'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Float right'),
						'hide' => (Tools::getValue('menu_type') == 3 ? true : false),
						'name' => 'float',
						'is_bool' => true,
						'desc' => $this->l('Position menu on right side of menu. If center option of menu is enabled it do not take effect'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link background color'),
						'name' => 'bg_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link text color'),
						'name' => 'txt_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link hover background color'),
						'name' => 'h_bg_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link hover text color'),
						'name' => 'h_txt_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Legend background color'),
						'name' => 'labelbg_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Legend text color'),
						'name' => 'labeltxt_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
					),
					array(
						'type' => 'select',
						'label' => $this->l('Submenu type and status'),
						'name' => 'submenu_type',
						'options' => array(
							'query' => $options_type,
							'id' => 'id_option',
							'name' => 'name'
							)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Submenu width'),
						'name' => 'submenu_width',
						'hide' => (Tools::getValue('menu_type') == 3 ? true : false),
						'options' => array(
							'query' => $columns_width,
							'id' => 'id_option',
							'name' => 'name'
							)
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Content'),
						'name' => 'submenu_content',
						'autoload_rte' => false,
						'hide' => true,
						'lang' => false,
					),
					array(
						'type' => 'color',
						'wrapper_hidden' => true,
						'row_title' => $this->l('Optional submenu style'),
						'preffix_wrapper' => 'cssstyle-submenu',
						'accordion_wrapper' => 'cssstyle-submenu-inner',
						'label' => $this->l('Submenu background color'),
						'name' => 'submenu_bg_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						),
					array(
					'type' => 'image_upload',
					'label' => $this->l('Background image'),
					'name' => 'submenu_image',
					),
					array(
						'type' => 'select',
						'label' => $this->l('Submenu Background repeat'),
						'name' => 'submenu_repeat',
						'options' => array(
							'query' => array(array(
								'id_option' => 3,
								'name' => $this->l('Repeat XY')
								),
							array(
								'id_option' => 2,
								'name' => $this->l('Repeat X')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('Repeat Y')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No repeat')
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Submenu Background position'),
						'name' => 'submenu_bg_position',
						'options' => array(
							'query' => array(
							array(
								'id_option' => 8,
								'name' => $this->l('left top')
								),
							array(
								'id_option' => 7,
								'name' => $this->l('left center')
								),
							array(
								'id_option' => 6,
								'name' => $this->l('left bottom')
								),
							array(
								'id_option' => 5,
								'name' => $this->l('right top')
								),
							array(
								'id_option' => 4,
								'name' => $this->l('right center')
								),
							array(
								'id_option' => 3,
								'name' => $this->l('right bottom')
								),
							array(
								'id_option' => 2,
								'name' => $this->l('center top')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('center center')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('center bottom')
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
						'type' => 'color',
						'label' => $this->l('Title color'),
						'name' => 'submenu_title_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Title hover color'),
						'name' => 'submenu_title_colorh',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Title border color'),
						'name' => 'submenu_titleb_color',
						'desc' => $this->l('Optional field. If not set default color will be used.'),
						'size' => 30,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Submenu link color'),
						'name' => 'submenu_link_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Submenu link hover color'),
						'name' => 'submenu_hover_color',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Submenu border top color'),
						'name' => 'submenu_border_t',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Submenu border right color'),
						'name' => 'submenu_border_r',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Submenu border bottom color'),
						'name' => 'submenu_border_b',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Submenu border left color'),
						'name' => 'submenu_border_l',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Submenu inner border color'),
						'name' => 'submenu_border_i',
						'desc' => $this->l('Optional field. If not set default color will be used'),
						'size' => 30,
						'suffix_a_wrapper' => true,
						'suffix_wrapper' => true,
						),
					array(
							'type' => 'grid_creator',
							'label' => '',
							'col' => 12,
							'preffix_wrapper' => 'grid-submenu',
							'wrapper_hidden' => true,
							'name' => 'grid_creator',
							'suffix_wrapper' => true,
						),
					array(
							'type' => 'tabs_choice',
							'label' => '',
							'name' => 'tabs_choice',
							'lang' => true,
							'suffix_wrapper' => true,
							'preffix_wrapper' => 'tabs-submenu',
							'wrapper_hidden' => true,
						),
					array(
                        'type' => 'group',
                        'label' => $this->l('Group access'),
                        'name' => 'groupBox',
                        'values' => Group::getGroups(Context::getContext()->language->id),
                        'info_introduction' => $this->l('You now have three default customer groups.'),
                        'unidentified' => $unidentified_group_information,
                        'guest' => $guest_group_information,
                        'customer' => $default_group_information,
                        'hint' => $this->l('Mark all of the customer groups which you would like to have access to this menu tab.'),
                    ),

				),
				'submit' => array(
					'title' => $this->l('Save'),
				),

			),
		);

			// Display this field only if multistore option is enabled AND there are several stores configured
        if (Shop::isFeatureActive()) {
            $fields_form['form']['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }
		
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'menu_type');

		$selected_tabs = '';
		$submenu_content = '';
		$submenu_content_format = array();
		if (Tools::isSubmit('id_tab') && IqitMenuTab::tabExists((int)Tools::getValue('id_tab')))
		{
			$tab = new IqitMenuTab((int)Tools::getValue('id_tab'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_tab');

			if($tab->submenu_type==1)
			$selected_tabs = $tab->submenu_content;

			if($tab->submenu_type==2 && $tab->submenu_content!='')
			{
				$submenu_content = $tab->submenu_content;
				$submenu_content_format = $this->buildSubmenuTree(json_decode($tab->submenu_content, true), false);
			}
			

		}


		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = 'iqitmegamenu_tabs';
		$helper->show_cancel_button = true;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->id = (int) Tools::getValue('id_tab');
		$helper->identifier = 'id_tab';
		$helper->submit_action = 'submitAddTab';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'module_path' => $this->_path,
			'va_links_select' => $this->renderChoicesSelect(null, null, 'select-links-ids'),
			'custom_html_select' => $this->renderCustomHtmlSelect(),
			'manufacturers_select' => $this->renderManufacturersSelect(),
			'categories_select' => $this->renderCategoriesSelect(false),
			'choices_tabs' => $this->renderChoicesTabsSelect(),
			'selected_tabs' => $this->renderSelectedTabsSelect($selected_tabs),
			'submenu_content' => htmlentities($submenu_content, ENT_COMPAT, 'UTF-8'),
			'submenu_content_format' => $submenu_content_format,
			'image_baseurl' => $this->_path.'images/'
		);


		$helper->override_folder = '/';

		return $helper->generateForm(array($fields_form));
	}


	public function getAddFieldsValues()
	{
		$fields = array();

		$fields['menu_type'] = (int)Tools::getValue('menu_type');


		$fields['active'] = true;
		$fields['active_label'] = false;
		$fields['icon'] = '';
		$fields['icon_class'] = '';
		$fields['legend_icon'] = '';
		

		$fields['url_type'] = 0;
		$fields['icon_type'] = 0;
		$fields['id_url'] = 0;
	

		$fields['new_window'] = false;
		$fields['float'] = false;

		$fields['bg_color'] = '';
		$fields['txt_color'] = '';
		$fields['h_bg_color'] = '';
		$fields['h_txt_color'] = '';
		$fields['labelbg_color'] = '';
		$fields['labeltxt_color'] = '';

		//submenu
		$fields['submenu_type'] = 0;
		$fields['submenu_content'] = '';
		$fields['submenu_width'] = 12;
		$fields['submenu_bg_color'] = '';
		$fields['submenu_image'] = '';
		$fields['submenu_repeat'] = '';
		$fields['submenu_bg_position'] = '';
		$fields['submenu_link_color'] = '';
		$fields['submenu_hover_color'] = '';

		$fields['submenu_title_color'] = '';
		$fields['submenu_title_colorh'] = '';
		$fields['submenu_titleb_color'] = '';
		$fields['submenu_border_t'] = '';
		$fields['submenu_border_r'] = '';
		$fields['submenu_border_b'] = '';
		$fields['submenu_border_l'] = '';
		$fields['submenu_border_i'] = '';


		if (Tools::isSubmit('id_tab') && IqitMenuTab::tabExists((int)Tools::getValue('id_tab')))
		{
			$tab = new IqitMenuTab((int)Tools::getValue('id_tab'));
			$fields['id_tab'] = (int)Tools::getValue('id_tab', $tab->id);
			$fields['active'] = $tab->active;
			$fields['active_label'] = $tab->active_label;


			$fields['url_type'] = $tab->url_type;
			$fields['icon_type'] = $tab->icon_type;
			$fields['id_url'] = $tab->id_url;
			

			$fields['new_window'] = $tab->new_window;
			$fields['float'] = $tab->float;

			$fields['icon'] = $tab->icon;
			$fields['icon_class'] = $tab->icon_class;
			$fields['legend_icon'] = $tab->legend_icon;
			$fields['bg_color'] =  $tab->bg_color;
			$fields['txt_color'] =  $tab->txt_color;
			$fields['h_bg_color'] =  $tab->h_bg_color;
			$fields['h_txt_color'] =  $tab->h_txt_color;
			$fields['labelbg_color'] =  $tab->labelbg_color;
			$fields['labeltxt_color'] =  $tab->labeltxt_color;

			//submenu
			$fields['submenu_type'] = $tab->submenu_type;
			$fields['submenu_content'] =  $tab->submenu_content;
			$fields['submenu_width'] = $tab->submenu_width;
			$fields['submenu_bg_color'] =  $tab->submenu_bg_color;
			$fields['submenu_image'] = $tab->submenu_image;
			$fields['submenu_repeat'] = $tab->submenu_repeat;
			$fields['submenu_bg_position'] = $tab->submenu_bg_position;
			$fields['submenu_link_color'] = $tab->submenu_link_color;
			$fields['submenu_hover_color'] = $tab->submenu_hover_color;

			$fields['submenu_title_color'] = $tab->submenu_title_color;
			$fields['submenu_title_colorh'] = $tab->submenu_title_colorh;
			$fields['submenu_titleb_color'] = $tab->submenu_titleb_color;
			$fields['submenu_border_t'] = $tab->submenu_border_t;
			$fields['submenu_border_r'] = $tab->submenu_border_r;
			$fields['submenu_border_b'] = $tab->submenu_border_b;
			$fields['submenu_border_l'] = $tab->submenu_border_l;
			$fields['submenu_border_i'] = $tab->submenu_border_i;
			
			$group_access = unserialize($tab->group_access);

            foreach ($group_access as $group => $value) {
                $fields['groupBox_' . $group] = $value;
            }

        } else {
            $tab = new IqitMenuTab();

            $groups = Group::getGroups($this->context->language->id);

            foreach ($groups as $group) {
                $fields['groupBox_' . $group['id_group']] = true;
            }
        }

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {

            $fields['title'][$lang['id_lang']] = Tools::getValue('title_' . (int) $lang['id_lang'], $tab->title[$lang['id_lang']]);
            $fields['url'][$lang['id_lang']] = Tools::getValue('url_' . (int) $lang['id_lang'], $tab->url[$lang['id_lang']]);
            $fields['label'][$lang['id_lang']] = Tools::getValue('label_' . (int) $lang['id_lang'], $tab->label[$lang['id_lang']]);
        }

        return $fields;
    }

	public function renderAddHtmlForm()
	{	

		$fields_form = array(
			'form' => array(
				'tab_name' => 'main_tab',
				'legend' => array(
					'title' => $this->l('Add custom html'),
					'icon' => 'icon-cogs',
					'id' => 'fff'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Name'),
						'name' => 'title',
						'desc' => $this->l('Custom html name, Only for backoffice purposes'),
						'lang' => false,
					),
					array(
					'type' => 'textarea',
					'label' => $this->l('Html content'),
					'name' => 'html',
					'lang' => true,
					'autoload_rte' => true,
					'desc' => $this->l('Custom html content which you can later select in submenu'),
					'cols' => 60,
					'rows' => 30
				),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
				'buttons' => array(
				'button' => array(
					'name' => 'back_to_configuration',
					'type' => 'submit',
					'icon' => 'process-icon-back',
					'class' => 'btn btn-default pull-left',
					'title' => $this->l('Back')
					),)

			),
		);
		
		
		if (Tools::isSubmit('id_html') && IqitMenuHtml::htmlExists((int)Tools::getValue('id_html')))
		{
			$tab = new IqitMenuHtml((int)Tools::getValue('id_html'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_html');	

		}

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitAddHtml';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddHtmlFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'module_path' => $this->_path,
			'image_baseurl' => $this->_path.'images/'
		);


		$helper->override_folder = '/';

		return $helper->generateForm(array($fields_form));
	}

	public function getAddHtmlFieldsValues()
	{
		$fields = array();

		$fields['title'] = '';

		if (Tools::isSubmit('id_html') && IqitMenuHtml::htmlExists((int)Tools::getValue('id_html')))
		{
			$html = new IqitMenuHtml((int)Tools::getValue('id_html'));
			$fields['id_html'] = (int)Tools::getValue('id_html', $html->id);
			$fields['title'] = $html->title;
			
		}
		else
			$html = new IqitMenuHtml();
		
		$languages = Language::getLanguages(false);

		foreach ($languages as $lang)
			$fields['html'][$lang['id_lang']] = Tools::getValue('html_'.(int)$lang['id_lang'], $html->html[$lang['id_lang']]);

		return $fields;
	}



	public function buildSubmenuTree(array $dataset, $frontend = false, $cssgenerator = false) 
	{
		$id_lang = (int)Context::getContext()->language->id;

		$tree = array();
		foreach ($dataset as $id=>&$node) {
			if($cssgenerator)
			{

				//set style
				if(isset($node['content_s']['br_top_st']))
					$node['content_s']['br_top_st'] = $this->convertBorderType($node['content_s']['br_top_st']);

				if(isset($node['content_s']['br_right_st']))
					$node['content_s']['br_right_st'] = $this->convertBorderType($node['content_s']['br_right_st']);

				if(isset($node['content_s']['br_bottom_st']))
					$node['content_s']['br_bottom_st'] = $this->convertBorderType($node['content_s']['br_bottom_st']);

				if(isset($node['content_s']['br_left_st']))
					$node['content_s']['br_left_st'] = $this->convertBorderType($node['content_s']['br_left_st']);

			}
			if($frontend)
			{
				

				if(isset($node['content_s']['title'][$id_lang]) && $node['content_s']['title'][$id_lang]!='')
					$node['content_s']['title'] = $node['content_s']['title'][$id_lang];
				else
					unset($node['content_s']['title']);

				if(isset($node['content_s']['href'][$id_lang]) && $node['content_s']['href'][$id_lang]!='')
					$node['content_s']['href'] = $node['content_s']['href'][$id_lang];
				else
					unset($node['content_s']['href']);

				if(isset($node['content_s']['legend'][$id_lang]) && $node['content_s']['legend'][$id_lang]!='')
					$node['content_s']['legend'] = $node['content_s']['legend'][$id_lang];
				else
					unset($node['content_s']['legend']);


			//set variouse links
				if(isset($node['contentType'])){


					switch ($node['contentType']) {
						case 1:
						if(isset($node['content']['ids']))
						{	
							$customhtml = new IqitMenuHtml((int)$node['content']['ids'], $id_lang);

							if (Validate::isLoadedObject($customhtml))
							{
								$node['content']['ids'] = $customhtml->html;
							}
						}	
						break;
						case 2:
						if(isset($node['content']['ids']))
						{	
							if($node['content']['treep'])
								$node['content']['depth']++;

							foreach ($node['content']['ids'] as $key=>$category)
							{
								$node['content']['ids'][$key] = $this->generateCategoriesMenu2(Category::getNestedCategories($node['content']['ids'][$key], $id_lang, true, $this->user_groups, true, '', $this->hor_sm_order), false, $node['content']['depth'], 1, $node['content']['sublimit'], 0);
							}
						}
						break;
						case 3:
						if(isset($node['content']['ids']))
						{		
							foreach ($node['content']['ids'] as $key=>$link) {
								$node['content']['ids'][$key] = $this->transformToLink($link, true);
							}
						}
						break;
						case 6:
						if(isset($node['content']['source'][$id_lang]) && $node['content']['source'][$id_lang]!='')
							$node['content']['source'] = $node['content']['source'][$id_lang];
						else
							unset($node['content']['source']);

						if(isset($node['content']['href'][$id_lang]) && $node['content']['href'][$id_lang]!='')
							$node['content']['href'] = $node['content']['href'][$id_lang];
						else
							unset($node['content']['href']);

						if (isset($node['content']['alt'][$id_lang]) && $node['content']['alt'][$id_lang] != '') {
                                $node['content']['alt'] = $node['content']['alt'][$id_lang];
                        } else {
                                unset($node['content']['alt']);
                        }

						break;
					}

				}

			}

			if(isset($node['contentType']) && $node['contentType'] == 4 )
			{
				if(isset($node['content']['ids']) && !empty($node['content']['ids'])){
					$node['content']['ids'] = $this->getProducts($node['content']['ids']);

				}
			}

			if ($node['parentId'] === 0) {
				$tree[$id] = &$node;
			} else {
				if (!isset($dataset[$node['parentId']]['children'])) 
					$dataset[$node['parentId']]['children'] = array();
				$dataset[$node['parentId']]['children'][$id] = &$node;
			}
			
		}
		

		$tree = $this->sortArrayTree($tree);
		return $tree;
	}
	
public function sortArrayTree($passedTree)
{
	
	usort($passedTree,array($this, 'sortByPosition'));

	foreach ($passedTree as $key => $subtree) {

		if( !empty( $subtree['children'] ) )
		{	
			$passedTree[$key]['children'] = $this->sortArrayTree($subtree['children']);
			
		}
	}	
	
	return $passedTree;
	}

	public function sortByPosition($a, $b) 
	{
		return $a['position'] - $b['position'];
	}


	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm()
	{
		$fields_form_global =  array(
			'form' => array(
				'tab_name' => 'main_tab',
				'legend' => array(
				'title' => $this->l('Horizontal menu'),
				'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Width of menu'),
						'name' => 'hor_width',
						'options' => array(
							'query' => array(
								array(
								'id_option' => 1,
								'name' => $this->l('Content')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('Full width')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
					array(
						'type' => 'select',
						'label' => $this->l('Submenu wrapper width'),
						'name' => 'hor_sw_width',
						'desc' => $this->l('If "Width of menu" is full width submenu will be full width. Note: Individual submenu width will be not used'),
						'options' => array(
							'query' => array(
								array(
								'id_option' => 1,
								'name' => $this->l('Content')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('Full width')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
					array(
						'type' => 'select',
						'label' => $this->l('Submenu content width'),
						'desc' => $this->l('If full width submenu content will be full width too when wrapper is fullwidth. When wrapper is content width this setting do not change anything'),
						'name' => 'hor_s_width',
						'options' => array(
							'query' => array(
								array(
								'id_option' => 1,
								'name' => $this->l('Content')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('Full width')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
					array(
						'type' => 'switch',
						'label' => $this->l('Sticky menu'),
						'desc' => $this->l('If enabled menu will stick to the same spot in the viewport as your users scroll down your page.'),
						'name' => 'hor_sticky',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Transparency on sticky menu'),
						'desc' => $this->l('Menu will be transparent on scroll'),
						'name' => 'hor_s_transparent',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'select',
						'label' => $this->l('Submenu animation'),
						'desc' => $this->l('You can show instantly(no animation) or with fade animation'),
						'name' => 'hor_animation',
						'options' => array(
							'query' => array(
							array(
								'id_option' => 3,
								'name' => $this->l('Fade and slide from top')
								),
							array(
								'id_option' => 2,
								'name' => $this->l('Fade and slide from bottom')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('Fade')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No animation')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
						array(
						'type' => 'switch',
						'label' => $this->l('Center menu elements'),
						'desc' => $this->l('Main menu links will be centered'),
						'name' => 'hor_center',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Arrow submenu inticator'),
						'desc' => $this->l('Show arrow icon if submenu exist'),
						'name' => 'hor_arrow',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Submenu with arrow'),
						'desc' => $this->l('If there will be arrow beetwen submenu and main link'),
						'name' => 'hor_s_arrow',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Main menu height'),
						'name' => 'hor_lineheight',
						'upper_separator' => true,
						'row_title' => $this->l('Main menu design options'),
					),
					array(
						'type' => 'color',
						'label' => $this->l('Background color'),
						'name' => 'hor_bg_color',
						'desc' => $this->l('Main menu backgrund color'),
						'size' => 30,
					),
					array(
					'type' => 'image_upload',
					'label' => $this->l('Background image'),
					'name' => 'hor_bg_image',
					),
					array(
						'type' => 'select',
						'label' => $this->l('Main menu backgrund repeat'),
						'name' => 'hor_bg_repeat',
						'options' => array(
							'query' => array(array(
								'id_option' => 3,
								'name' => $this->l('Repeat XY')
								),
							array(
								'id_option' => 2,
								'name' => $this->l('Repeat X')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('Repeat Y')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No repeat')
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Menu bar top border '),
						'name' => 'hor_border_top',
						),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Menu bar bottom border '),
						'name' => 'hor_border_bottom',
						),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Menu bar left and right border'),
						'name' => 'hor_border_sides',
						),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Inner border'),
						'name' => 'hor_border_inner',
						),
					array(
						'type' => 'text',
						'label' => $this->l('Main link font size'),
						'name' => 'hor_link_fontsize',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Main link left and right padding'),
						'name' => 'hor_link_padding',
						'desc' => $this->l('You can fit more elements thans to decrease of this value'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Main link font size(resolution above 1320px)'),
						'name' => 'hor_link_fontsizeb',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Main link left and right padding(resolution above 1320px)'),
						'name' => 'hor_link_paddingb',
						'desc' => $this->l('You can fit more elements thans to decrease of this value'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Main link max-width'),
						'desc' => $this->l('Helpfull if you have long tabs name and you want to make them two line'),
						'name' => 'hor_maxwidth',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Icon font size'),
						'name' => 'hor_icon_fontsize',
					),
					array(
						'type' => 'select',
						'label' => $this->l('Icon position'),
						'name' => 'hor_icon_position',
						'options' => array(
							'query' => array(
								array(
								'id_option' => 1,
								'name' => $this->l('Above text')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('With text')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
					array(
						'type' => 'switch',
						'label' => $this->l('Main links bold'),
						'name' => 'hor_link_bold',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Main links italics'),
						'name' => 'hor_link_italics',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Main links uppercase'),
						'name' => 'hor_link_uppercase',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link text color'),
						'name' => 'hor_link_txt_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link hover text color'),
						'name' => 'hor_link_htxt_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link hover background color'),
						'name' => 'hor_link_hbg_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Legends text color'),
						'name' => 'hor_legend_txt_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Legends background color'),
						'name' => 'hor_legend_bg_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Search bg color'),
						'name' => 'hor_search_bg_color',
						'size' => 30,
						'upper_separator' => true,
						'row_title' => $this->l('Search'),
						'desc' => $this->l('If search bar is set to megamenu then color will be added. You can change search postion in blocksearch_mod'),
						),
					array(
						'type' => 'color',
						'label' => $this->l('Search text color'),
						'name' => 'hor_search_txt',
						'size' => 30,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Search height(px)'),
						'name' => 'hor_search_height',
						'size' => 30,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Search width(px)'),
						'name' => 'hor_search_width',
						'desc' => $this->l('If you use search with categories selector set at least 250'),
						'size' => 30,
					),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Search border'),
						'name' => 'hor_search_border',
						),
					array(
						'type' => 'textarea',
						'label' => $this->l('Custom CSS code'),
						'id' =>'codeEditor',
						'name' => 'hor_custom_css',
						'upper_separator' => true,
						'row_title' => $this->l('Custom css'),
						),


				),
				'submit' => array(
					'name' => 'submitHorizonalMenuConfig',
					'title' => $this->l('Save'),
				),
			),
		);	

		$fields_form_submenustyl =  array(
			'form' => array(
				'tab_name' => 'submenudesign_tab',
				'legend' => array(
				'title' => $this->l('Submenu design and options'),
				'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Subcategories order'),
						'name' => 'hor_sm_order',
						'desc' => $this->l('Affects horizontal, vertical and mobile menu'),
						'options' => array(
							'query' => array(
							array(
								'id_option' => 1,
								'name' => $this->l('Alphabetical')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('Default')
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
						'type' => 'color',
						'label' => $this->l('Background color'),
						'name' => 'hor_sm_bg_color',
						'desc' => $this->l('Submenu backgrund color'),
						'size' => 30,
					),
					array(
					'type' => 'image_upload',
					'label' => $this->l('Background image'),
					'name' => 'hor_sm_bg_image',
					),
					array(
						'type' => 'select',
						'label' => $this->l('Submenu backgrund repeat'),
						'name' => 'hor_sm_bg_repeat',
						'options' => array(
							'query' => array(array(
								'id_option' => 3,
								'name' => $this->l('Repeat XY')
								),
							array(
								'id_option' => 2,
								'name' => $this->l('Repeat X')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('Repeat Y')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No repeat')
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
						'type' => 'select',
						'label' => $this->l('Submenu box-shadow'),
						'name' => 'hor_sm_boxshadow',
						'options' => array(
							'query' => array(
							array(
								'id_option' => 1,
								'name' => $this->l('Default box shadow effect')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No box shadow')
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Submenu top border '),
						'name' => 'hor_sm_border_top',
						),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Submenu bottom border '),
						'name' => 'hor_sm_border_bottom',
						),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Submenu left and right border'),
						'name' => 'hor_sm_border_sides',
						),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Inner border'),
						'name' => 'hor_sm_border_inner',
						),
					array(
						'type' => 'color',
						'label' => $this->l('Predefinied tab text color'),
						'name' => 'hor_sm_tab_txt_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Predefinied tab bg color'),
						'name' => 'hor_sm_tab_bg_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Predefinied tab hover text color'),
						'name' => 'hor_sm_tab_htxt_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Predefinied tab hover background color'),
						'name' => 'hor_sm_tab_hbg_color',
						'size' => 30,
					),
					array(
						'type' => 'text',
						'upper_separator' => true,
						'row_title' => $this->l('Submenu columns titles or parent categories'),
						'label' => $this->l('Font size'),
						'name' => 'hor_titlep_fontsize',
					),
					array(
						'type' => 'color',
						'label' => $this->l('Color'),
						'name' => 'hor_titlep_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Hover color'),
						'name' => 'hor_titlep_colorh',
						'size' => 30,
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Title Uppercase'),
						'name' => 'hor_titlep_uppercase',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Title bold'),
						'name' => 'hor_titlep_bold',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Bottom border'),
						'name' => 'hor_titlep_border',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Border bottom style'),
						'name' => 'hor_titlep_borders',
						),
					array(
						'type' => 'text',
						'upper_separator' => true,
						'row_title' => $this->l('Default text options'),
						'label' => $this->l('Font size'),
						'name' => 'hor_subtxt_fontsize',
					),
						array(
						'type' => 'color',
						'label' => $this->l('Color'),
						'name' => 'hor_subtxt_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Hover color'),
						'name' => 'hor_subtxt_colorh',
						'size' => 30,
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Listing arrow'),
						'name' => 'hor_subtxt_arrow',
						'desc' => $this->l('Show arrow in decorated list of categories or links'),
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
				),
				'submit' => array(
					'name' => 'submitHorizonalMenuConfig',
					'title' => $this->l('Save'),
				),
			),
		);

		$fields_form_vertical =  array(
			'form' => array(
				'tab_name' => 'vertical_tab',
				'legend' => array(
				'title' => $this->l('Vertical menu'),
				'icon' => 'icon-cogs',
				),
				'input' => array(
				array(
						'type' => 'select',
						'label' => $this->l('Position and status'),
						'desc' => $this->l('Make shure you enabled left column in theme if you want to use DisplayLeftColumnhook'),
						'name' => 'ver_position',
						'options' => array(
							'query' => array(
							array(
								'id_option' => 6,
								'name' => $this->l('As menu for sidebar header option enabled')
								),
							array(
								'id_option' => 5,
								'name' => $this->l('IqitContentCreator(index page) + horizontal menu(other pages)')
								),
							array(
								'id_option' => 4,
								'name' => $this->l('IqitContentCreator(index page) + left column(other pages)')
								),
							array(
								'id_option' => 3,
								'name' => $this->l('On horizontal menu(not expanded on homepage)')
								),
							array(
								'id_option' => 2,
								'name' => $this->l('On horizontal menu(expanded on homepage)')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('On Left column(displayLeftColumn Hook)')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('Disabled')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
				array(
						'type' => 'select',
						'label' => $this->l('Submenu animation'),
						'desc' => $this->l('You can show instantly(no animation) or with fade animation'),
						'name' => 'ver_animation',
						'options' => array(
							'query' => array(
							array(
								'id_option' => 1,
								'name' => $this->l('Fade')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No animation')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
				array(
						'type' => 'select',
						'label' => $this->l('Boxshadow if on topbar'),
						'name' => 'ver_boxshadow',
						'options' => array(
							'query' => array(
							array(
								'id_option' => 1,
								'name' => $this->l('Default box shadow effect')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No box shadow')
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Arrow submenu inticator'),
						'desc' => $this->l('Show arrow icon if submenu exist'),
						'name' => 'ver_arrow',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Submenu with arrow'),
						'desc' => $this->l('If there will be arrow beetwen submenu and main link'),
						'name' => 'ver_s_arrow',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'color',
						'label' => $this->l('Title background color'),
						'name' => 'ver_title_bg',
						'desc' => $this->l('Title with "navigation" text'),
						'size' => 30,
						'row_title' => $this->l('Vertical menu design options'),
					),
					array(
						'type' => 'color',
						'label' => $this->l('Title text color'),
						'name' => 'ver_title_txt',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Title hover background color'),
						'name' => 'ver_title_bgh',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Title hover text color'),
						'name' => 'ver_title_txth',
						'size' => 30,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Title font size'),
						'name' => 'ver_title_size',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Title line height'),
						'name' => 'ver_title_height',
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Title bold'),
						'name' => 'ver_title_bold',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Title uppercase'),
						'name' => 'ver_title_uppercase',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'color',
						'label' => $this->l('Background color'),
						'name' => 'ver_bg_color',
						'desc' => $this->l('Main menu backgrund color'),
						'size' => 30,
					),
					array(
					'type' => 'image_upload',
					'label' => $this->l('Background image'),
					'name' => 'ver_bg_image',
					),
					array(
						'type' => 'select',
						'label' => $this->l('Main menu backgrund repeat'),
						'name' => 'ver_bg_repeat',
						'options' => array(
							'query' => array(array(
								'id_option' => 3,
								'name' => $this->l('Repeat XY')
								),
							array(
								'id_option' => 2,
								'name' => $this->l('Repeat X')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('Repeat Y')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No repeat')
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Menu bar top border '),
						'name' => 'ver_border_top',
						),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Menu bar bottom border '),
						'name' => 'ver_border_bottom',
						),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Menu bar left and right border'),
						'name' => 'ver_border_sides',
						),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Inner border'),
						'name' => 'ver_border_inner',
						),
					array(
						'type' => 'text',
						'label' => $this->l('Main link font size'),
						'name' => 'ver_link_fontsize',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Icon font size'),
						'name' => 'ver_icon_fontsize',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Main link top and bottom padding'),
						'name' => 'ver_link_padding',
						'desc' => $this->l('You can fit more elements thans to decrease of this value'),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Main links bold'),
						'name' => 'ver_link_bold',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Main links italics'),
						'name' => 'ver_link_italics',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Main links uppercase'),
						'name' => 'ver_link_uppercase',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link text color'),
						'name' => 'ver_link_txt_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link hover text color'),
						'name' => 'ver_link_htxt_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main link hover background color'),
						'name' => 'ver_link_hbg_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Legends text color'),
						'name' => 'ver_legend_txt_color',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Legends background color'),
						'name' => 'ver_legend_bg_color',
						'size' => 30,
					),



				),
				'submit' => array(
					'name' => 'submitVerticalMenuConfig',
					'title' => $this->l('Save'),
				),
			),
		);

		$fields_form_submenutabs =  array(
			'form' => array(
				'tab_name' => 'submenutabs_tab',
				'legend' => array(
				'title' => $this->l('Predefinied submenu tabs'),
				'icon' => 'icon-cogs',
				),
					'input' => array(
					array(
							'type' => 'custom_info',
							'label' => $this->l('You can select this tabs in vertical or horizontal menu'),
							'name' => '',
						),
				),
			),
		);

		$fields_form_html =  array(
			'form' => array(
				'tab_name' => 'customhtml_tab',
				'legend' => array(
				'title' => $this->l('Predefinied custom html content'),
				'icon' => 'icon-cogs',
				),
					'input' => array(
					array(
							'type' => 'custom_info',
							'label' => $this->l('You can select this html content in submenu'),
							'name' => '',
						),
				),
			),
		);


		$fields_form_mobile =  array(
			'form' => array(
				'tab_name' => 'mobile_tab',
				'legend' => array(
				'title' => $this->l('Mobile menu'),
				'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Mobile menu type'),
						'name' => 'mobile_menu_style',
						'desc' => $this->l('Push menu will float from left site,'),
						'options' => array(
							'query' => array(
							array(
								'id_option' => 0,
								'name' => $this->l('Push menu')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
					array(
						'type' => 'select',
						'label' => $this->l('Depth limit'),
						'name' => 'mobile_menu_depth',
						'desc' => $this->l('Push menu will float from left site,'),
						'options' => array(
							'query' => array(
								array(
								'id_option' => 4,
								'name' => $this->l('4')
								),
								array(
								'id_option' => 3,
								'name' => $this->l('3')
								),
								array(
								'id_option' => 2,
								'name' => $this->l('2')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('1')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
					array(
							'type' => 'link_choice',
							'label' => '',
							'name' => 'link',
							'lang' => true,
						),
					array(
						'row_title' => $this->l('Design options'),
						'type' => 'color',
						'label' => $this->l('Main bar background color'),
						'name' => 'hor_mb_bg',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Main bar text color'),
						'name' => 'hor_mb_txt',
						'size' => 30,
					),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Main bar border'),
						'name' => 'hor_mb_border',
					),
					array(
						'type' => 'color',
						'label' => $this->l('First level of menu background color'),
						'name' => 'hor_mb_c_bg',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Other level of menu background color'),
						'name' => 'hor_mb_csl_bg',
						'size' => 30,
					),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Content border'),
						'name' => 'hor_mb_c_border',
					),
					array(
						'type' => 'border_generator',
						'label' => $this->l('Content inner border'),
						'name' => 'hor_mb_c_borderi',
					),
					array(
						'type' => 'color',
						'label' => $this->l('Content link color'),
						'name' => 'hor_mb_c_txt',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Content link hover color'),
						'name' => 'hor_mb_c_txth',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Content link hover background color'),
						'name' => 'hor_mb_c_lhbg',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Plus icon background color'),
						'name' => 'hor_mb_c_plusbg',
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Plus icon color'),
						'name' => 'hor_mb_c_plus',
						'size' => 30,
					),
				),
				'submit' => array(
					'name' => 'submitMobileMenu',
					'title' => $this->l('Save'),
				),
			),
		);


		$fields_form_custom =  array(
			'form' => array(
				'tab_name' => 'customlinks_tab',
				'assigned_list' =>  $this->renderListCustomLinks(),
				'legend' => array(
					'title' => $this->l('Custom links'),
					'icon' => 'icon-link'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Label'),
						'name' => 'label',
						'lang' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Link'),
						'name' => 'link',
						'lang' => true,
					),
					array(
						'type' => 'switch',
						'label' => $this->l('New window'),
						'name' => 'new_window',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					)
				),
				'submit' => array(
					'name' => 'submitBlocktopmenuLinks',
					'title' => $this->l('Add')
				)
			),
		);

		$fields_form_ietool = array(
            'form' => array(
                'tab_name' => 'ietool_tab',
                'legend' => array(
                    'title' => $this->l('Import/export'),
                    'icon' => 'icon-download',
                ),
                'input' => array(
                    array(
                        'type' => 'ietool',
                        'label' => $this->l('Import export tool'),
                        'name' => 'ietool-name',
                    ),
                ),
            ),

        );

		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitiqitmegamenuModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		if (Tools::getIsset('updateiqitmenulinks') && !Tools::getValue('updateiqitmenulinks'))
			$fields_form_custom['form']['submit'] = array(
				'name' => 'submitupdateiqitmenulinks',
				'title' => $this->l('Update')
			);

		if (Tools::getIsset('updateiqitmenulinks') && (int)Tools::getValue('id_iqitmenulinks') > 0)	
		{
			$fields_form_custom['form']['input'][] = array('type' => 'hidden', 'name' => 'updatelink');
			$fields_form_custom['form']['input'][] = array('type' => 'hidden', 'name' => 'id_iqitmenulinks');
		}
	


		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'module_path' => $this->_path,
			'id_language' => $this->context->language->id,
			'choices' => $this->renderChoicesSelect(null, null, null, true),
			'selected_links' => $this->makeMenuOptionMobile(),
		);

		return $helper->generateForm(array($fields_form_global, $fields_form_vertical, $fields_form_mobile, $fields_form_submenutabs, $fields_form_submenustyl,  $fields_form_html, $fields_form_ietool, $fields_form_custom));
	}




	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFormValues()
	{
		$var = array();

		if (Tools::getIsset('updateiqitmenulinks') && (int)Tools::getValue('id_iqitmenulinks') > 0)	
		{
			
			$var['id_iqitmenulinks'] = (int)Tools::getValue('id_iqitmenulinks');
			$var['updatelink'] = true;

			$llink = IqitMenuLinks::getLinkById((int)Tools::getValue('id_iqitmenulinks'));

			foreach (Language::getLanguages(false) as $lang)
			{
				$var['label'][(int)$lang['id_lang']] = $llink['label'][(int)$lang['id_lang']];
				$var['link'][(int)$lang['id_lang']] = $llink['link'][(int)$lang['id_lang']];
			}
			$var['new_window'] = 1;
		}
		else{
			foreach (Language::getLanguages(false) as $lang)
			{
			$var['link'][(int)$lang['id_lang']] = '';
			$var['label'][(int)$lang['id_lang']] = '';
			}
			$var['new_window'] = false;
		}

		foreach ($this->defaults_mobile as $default => $value)
		{
			if($default == 'hor_mb_border' || $default == 'hor_mb_c_border' || $default == 'hor_mb_c_borderi')
			{	
				$tmpborder =  explode(';', Configuration::get($this->config_name.'_'.$default));
				
				$var[$default]['width'] = $tmpborder[0];
				$var[$default]['type'] = $tmpborder[1];
				$var[$default]['color'] = $tmpborder[2];
			}
			else
				$var[$default] = Configuration::get($this->config_name.'_'.$default);
		}
		foreach ($this->defaults_horizontal as $default => $value)
		{	
			if($default == 'hor_search_border' ||  $default == 'hor_titlep_borders' || $default == 'hor_border_top' || $default == 'hor_border_bottom' || $default == 'hor_border_sides' || $default == 'hor_border_inner' || $default == 'hor_sm_border_top' ||  $default == 'hor_sm_border_bottom' || $default == 'hor_sm_border_sides' || $default == 'hor_sm_border_inner')
			{	
				$tmpborder =  explode(';', Configuration::get($this->config_name.'_'.$default));
				
				$var[$default]['width'] = $tmpborder[0];
				$var[$default]['type'] = $tmpborder[1];
				$var[$default]['color'] = $tmpborder[2];
			}
			else
			$var[$default] = Configuration::get($this->config_name.'_'.$default);
		}
		foreach ($this->defaults_vertical as $default => $value)
		{
			if($default == 'ver_border_top' ||  $default == 'ver_border_bottom' || $default == 'ver_border_sides' || $default == 'ver_border_inner')
			{	
				$tmpborder =  explode(';', Configuration::get($this->config_name.'_'.$default));
				
				$var[$default]['width'] = $tmpborder[0];
				$var[$default]['type'] = $tmpborder[1];
				$var[$default]['color'] = $tmpborder[2];
			}
			else
			$var[$default] = Configuration::get($this->config_name.'_'.$default);
		}
		
		return $var;

	}

	public function generateCss()
	{
		$css = '';

		$menubgimage = Configuration::get($this->config_name.'_hor_bg_image');
		$vermenubgimage = Configuration::get($this->config_name.'_ver_bg_image');
		$submenubgimage = Configuration::get($this->config_name.'_hor_sm_bg_image');

		//horizontal menu style
		$css .= '
		#iqitmegamenu-horizontal{
		'.$this->convertBorder(Configuration::get($this->config_name.'_hor_border_top'), 'top').'
		'.$this->convertBorder(Configuration::get($this->config_name.'_hor_border_bottom'), 'bottom').'
		'.$this->convertBorder(Configuration::get($this->config_name.'_hor_border_sides'), 'side').'
		background-color: '.Configuration::get($this->config_name.'_hor_bg_color').';
		';
		if($menubgimage!='')
		$css .= 'background-image: url('.$menubgimage.'); background-repeat: '.$this->convertBgRepeat(Configuration::get($this->config_name.'_hor_bg_repeat')).';';
		$css .= '}'.PHP_EOL;		


		$css .= '
		.cbp-horizontal .cbp-legend{
			background-color: '.Configuration::get($this->config_name.'_hor_legend_bg_color').';
			color: '.Configuration::get($this->config_name.'_hor_legend_txt_color').';	
		}
		.cbp-horizontal .cbp-legend .cbp-legend-arrow{
			color: '.Configuration::get($this->config_name.'_hor_legend_bg_color').';	
		}
		.cbp-horizontal > ul > li.cbp-hropen > a, .cbp-horizontal > ul > li.cbp-hropen > a:hover{
			background-color: '.Configuration::get($this->config_name.'_hor_link_hbg_color').';
			color: '.Configuration::get($this->config_name.'_hor_link_htxt_color').';	
		}
		#iqitmegamenu-horizontal .iqit-search-shower-i .icon-search{
			color: '.Configuration::get($this->config_name.'_hor_link_txt_color').';
			line-height: '.Configuration::get($this->config_name.'_hor_lineheight').'px;
		}
		.cbp-horizontal > ul > li > a, .cbp-horizontal > ul > li > span.cbp-main-link{
		color: '.Configuration::get($this->config_name.'_hor_link_txt_color').';
		line-height: '.Configuration::get($this->config_name.'_hor_lineheight').'px;
		padding-left: '.Configuration::get($this->config_name.'_hor_link_padding').'px;
		max-width: '.Configuration::get($this->config_name.'_hor_maxwidth').'px;
		padding-right: '.Configuration::get($this->config_name.'_hor_link_padding').'px;
		'.(Configuration::get($this->config_name.'_hor_link_bold') ? 'font-weight: bold;' : '').'
		'.(Configuration::get($this->config_name.'_hor_link_italics') ? 'font-style: italic;' : '').'
		'.(Configuration::get($this->config_name.'_hor_link_uppercase') ? 'text-transform: uppercase;' : '').'
		font-size: '.Configuration::get($this->config_name.'_hor_link_fontsize').'px;
		'.$this->convertBorder(Configuration::get($this->config_name.'_hor_border_inner'), 'left').'
		}
		#iqitmegamenu-horizontal #search_block_top_contentm .iqit-search-shower-i{	
			padding-left: '.Configuration::get($this->config_name.'_hor_link_padding').'px;
			padding-right: '.Configuration::get($this->config_name.'_hor_link_padding').'px;
		}
		.cbp-horizontal .cbp-tab-title{
			line-height: '.(Configuration::get($this->config_name.'_hor_link_fontsize')+1).'px;
		}
		@media (min-width: 1320px){
		.cbp-horizontal .cbp-tab-title{
			line-height: '.(Configuration::get($this->config_name.'_hor_link_fontsizeb')+1).'px;
		}
		.cbp-horizontal > ul > li > a, .cbp-horizontal > ul > li > span.cbp-main-link{
			font-size: '.Configuration::get($this->config_name.'_hor_link_fontsizeb').'px;
			padding-left: '.Configuration::get($this->config_name.'_hor_link_paddingb').'px;
			padding-right: '.Configuration::get($this->config_name.'_hor_link_paddingb').'px;
		}
		#iqitmegamenu-horizontal #search_block_top_contentm .iqit-search-shower-i{	
			padding-left: '.Configuration::get($this->config_name.'_hor_link_paddingb').'px;
			padding-right: '.Configuration::get($this->config_name.'_hor_link_paddingb').'px;
		}
		}
		.cbp-vertical-on-top .cbp-vertical-title{
			line-height: '.Configuration::get($this->config_name.'_hor_lineheight').'px;
		}
		#iqitmegamenu-horizontal #search_block_top_contentm{
				width: '.(Configuration::get($this->config_name.'_hor_search_width')+10).'px;
		}
		#iqitmegamenu-horizontal #search_block_top{
				width: '.Configuration::get($this->config_name.'_hor_search_width').'px;
		}
		#iqitmegamenu-horizontal #search_block_top .search_query{
			line-height: '.(Configuration::get($this->config_name.'_hor_search_height')).'px;
			height: '.(Configuration::get($this->config_name.'_hor_search_height')).'px;
			color: '.Configuration::get($this->config_name.'_hor_search_txt').' !important;
			background-color: '.Configuration::get($this->config_name.'_hor_search_bg_color').';
			top: 0px;
			bottom: 0px;
			margin: auto;
			'.$this->convertBorder(Configuration::get($this->config_name.'_hor_search_border'), 'all', 0, '!important').'
		}
		#iqitmegamenu-horizontal #search_block_top .search-cat-select{
					background-color: '.Configuration::get($this->config_name.'_hor_search_bg_color').';
		}
		#iqitmegamenu-horizontal .search-cat-selector .selector span:after{
				'.$this->convertBorder(Configuration::get($this->config_name.'_hor_search_border'), 'right').'
		}
		#iqitmegamenu-horizontal #search_block_top .search-cat-selector .selector span:after{
			line-height: '.(Configuration::get($this->config_name.'_hor_search_height')+ 2).'px;
		}
		#iqitmegamenu-horizontal #search_block_top .search-cat-select, #iqitmegamenu-horizontal  #search_block_top .search-cat-selector .selector, #iqitmegamenu-horizontal #search_block_top .search-cat-selector .selector span{ line-height: '.(Configuration::get($this->config_name.'_hor_search_height')).'px;
			height: '.(Configuration::get($this->config_name.'_hor_search_height')).'px;}
		#iqitmegamenu-horizontal #search_block_top .search-cat-selector .selector span{	color: '.Configuration::get($this->config_name.'_hor_search_txt').' !important;}
		#iqitmegamenu-horizontal #search_block_top_contentm{
			height: '.Configuration::get($this->config_name.'_hor_lineheight').'px!important;
		}
		#iqitmegamenu-horizontal #search_block_top{
			height: '.(Configuration::get($this->config_name.'_hor_search_height')).'px;
		}
		#iqitmegamenu-horizontal #search_block_top .search_query::-webkit-input-placeholder {
		color: '.Configuration::get($this->config_name.'_hor_search_txt').' !important;
			}
		#iqitmegamenu-horizontal #search_block_top .search_query:-moz-placeholder {
			color: '.Configuration::get($this->config_name.'_hor_search_txt').' !important;
		}
		#iqitmegamenu-horizontal #search_block_top .search_query::-moz-placeholder {
			color: '.Configuration::get($this->config_name.'_hor_search_txt').' !important;
		}
		#iqitmegamenu-horizontal #search_block_top .search_query:-ms-input-placeholder {
			color: '.Configuration::get($this->config_name.'_hor_search_txt').' !important;
		}
		#iqitmegamenu-horizontal #search_block_top .button-search:before{
			color: '.Configuration::get($this->config_name.'_hor_search_txt').' !important;
		}
		#iqitmegamenu-horizontal #search_block_top .button-search{
			top: 0px;
			line-height: '.(Configuration::get($this->config_name.'_hor_search_height')).'px;
		}
		'.PHP_EOL;



		$css .= '.cbp-horizontal > ul > li > a .cbp-mainlink-icon, .cbp-horizontal > ul > li > a .cbp-mainlink-iicon{
			font-size: '.Configuration::get($this->config_name.'_hor_icon_fontsize').'px;
			max-height: '.Configuration::get($this->config_name.'_hor_icon_fontsize').'px;
		}
		#search_block_top_contentm .iqit-search-shower-i .icon-search{
			font-size: '.Configuration::get($this->config_name.'_hor_icon_fontsize').'px;
		}';

		$css .= '
		.cbp-hrmenu  .cbp-hrsub-inner, .cbp-hrmenu ul.cbp-hrsub-level2 {
		'.$this->convertBorder(Configuration::get($this->config_name.'_hor_sm_border_top'), 'top').'
		'.$this->convertBorder(Configuration::get($this->config_name.'_hor_sm_border_bottom'), 'bottom').'
		'.$this->convertBorder(Configuration::get($this->config_name.'_hor_sm_border_sides'), 'side').'
		background-color: '.Configuration::get($this->config_name.'_hor_sm_bg_color').';
		';
		if($submenubgimage!='')
		$css .= 'background-image: url('.$submenubgimage.'); background-repeat: '.$this->convertBgRepeat(Configuration::get($this->config_name.'_hor_sm_bg_repeat')).';';
		$css .= '}'.PHP_EOL;

		
		$borderarow = Configuration::get($this->config_name.'_hor_sm_border_top');
		$borderarowexplode = explode(';', $borderarow);

		$borderarowsides = Configuration::get($this->config_name.'_hor_sm_border_sides');

		$css .= '.cbp-hrmenu .cbp-triangle-top{ 
			border-bottom-color: '.Configuration::get($this->config_name.'_hor_sm_bg_color').';
			top: '.$borderarowexplode[0].'px;
		}
		.cbp-hrmenu .cbp-triangle-left, #columns .cbp-hrmenu .cbp-triangle-left{
			border-color: transparent;
			border-right-color: '.Configuration::get($this->config_name.'_hor_sm_bg_color').';
			left: '.$borderarowsides[0].'px;
		}
		.cbp-hrmenu .cbp-triangle-top-back{'.$this->convertBorder($borderarow, 'bottom', 1).'}
		.cbp-hrmenu .cbp-triangle-left-back, #columns .cbp-hrmenu .cbp-triangle-left-back{'.$this->convertBorder($borderarowsides, 'right', 2).'}
		'.PHP_EOL;
		$borderinner = Configuration::get($this->config_name.'_hor_sm_border_inner');
		$borderinnerexplode = explode(';', $borderinner);
		
		$boxshadow = Configuration::get($this->config_name.'_hor_sm_boxshadow');
		
		$css .=	'
		.cbp-hrmenu .menu_column {
		border-color: '.$borderinnerexplode[2].';
		}
		';

		if($boxshadow){
		$css .=	'.cbp-hrmenu .cbp-hrsub-inner, .cbp-hrmenu ul.cbp-hrsub-level2 {
			-webkit-box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
			-moz-box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
			box-shadow:  0 2px 10px rgba(0, 0, 0, 0.15);
		}
		';
		}

		



		$css .= '
		.cbp-hrmenu .cbp-hrsub-tabs-names li .cbp-inner-border-hider{
			width: '.$borderinnerexplode[0].'px;
			right: '.-(int)($borderinnerexplode[0]).'px;
		}'.PHP_EOL;

		$css .= '.cbp-hrmenu .cbp-hrsub-tabs-names li{
			'.$this->convertBorder($borderinner, 'bottom').'
		}
		.cbp-hrmenu .cbp-tab-pane{
			'.$this->convertBorder($borderinner, 'left').'
		}
		.is_rtl .cbp-hrmenu .cbp-tab-pane{
			'.$this->convertBorder($borderinner, 'right').'
		}
		'.PHP_EOL;

		if(Configuration::get($this->config_name.'_hor_s_width') && !Configuration::get($this->config_name.'_hor_sw_width') && !Configuration::get($this->config_name.'_hor_width') )
			$css .= '.cbp-hrmenu .cbp-hrsub-tabs-names li{
				'.$this->convertBorder($borderinner, 'left').'
		}';


		$css .=	'
		.cbp-hrmenu .cbp-hrsub-inner .cbp-column-title, .cbp-hrmenu .cbp-hrsub-inner a.cbp-column-title:link {
		font-size: '.Configuration::get($this->config_name.'_hor_titlep_fontsize').'px;
		line-height: '.(Configuration::get($this->config_name.'_hor_titlep_fontsize')+4).'px;
		color: '.Configuration::get($this->config_name.'_hor_titlep_color').';
		'.(Configuration::get($this->config_name.'_hor_titlep_uppercase') ? 'text-transform: uppercase;' : '').'
		'.(Configuration::get($this->config_name.'_hor_titlep_bold') ? 'font-weight: bold;' : '').'
		}
		.cbp-hrmenu .cbp-hrsub-inner a.cbp-column-title:hover {
			color: '.Configuration::get($this->config_name.'_hor_titlep_colorh').';
		}
		'.PHP_EOL;

		if(Configuration::get($this->config_name.'_hor_titlep_border'))
			$css .=	'.cbp-hrmenu .cbp-hrsub-inner .cbp-column-title{
				padding-bottom: 6px;
				'.$this->convertBorder(Configuration::get($this->config_name.'_hor_titlep_borders'), 'bottom').'
			}'.PHP_EOL;


		$css .=	'.cbp-hrmenu .cbp-hrsub-inner{
		font-size: '.Configuration::get($this->config_name.'_hor_subtxt_fontsize').'px;
		line-height: '.(Configuration::get($this->config_name.'_hor_subtxt_fontsize')+4).'px;
		color: '.Configuration::get($this->config_name.'_hor_subtxt_color').'; 
		}
		.cbp-hrmenu .cbp-hrsub-inner a, .cbp-hrmenu .cbp-hrsub-inner a:link{
			color: '.Configuration::get($this->config_name.'_hor_subtxt_color').'; 
		}
		.cbp-hrmenu .cbp-hrsub-inner a:hover{
			color: '.Configuration::get($this->config_name.'_hor_subtxt_colorh').'; 
		}
		'.PHP_EOL;


		if(!Configuration::get($this->config_name.'_hor_subtxt_arrow'))
			$css .=	'.cbp-hrmenu .cbp-links li a:before{
			display: none;
			}
			.cbp-hrmenu .cbp-links li, .cbp-hrmenu .cbp-links li a {
				padding-left: 0px;
			}

		'.PHP_EOL;

		if(!Configuration::get($this->config_name.'_hor_subtxt_level'))
			$css .=	'.cbp-hrmenu .cbp-links li.cbp-hrsub-haslevel2 > a:after{
			display: none;
			}
		'.PHP_EOL;



		if(Configuration::get($this->config_name.'_hor_icon_position'))
			$css .= '.cbp-horizontal > ul > li > a .cbp-mainlink-icon, .cbp-horizontal > ul > li > a .cbp-mainlink-iicon{
			display: block;
			margin: 0 auto;
			text-align: center;
			position: absolute;
			left: 0px;
			right: 0px;
			top: 10px;
		}
		.cbp-horizontal > ul > li > a, .cbp-horizontal > ul > li > span.cbp-main-link{
			padding-top: 35px;
			line-height: '.(Configuration::get($this->config_name.'_hor_lineheight')-12).'px;
		}
		.cbp-horizontal .cbp-tab-title{
			position: static;
		}
		';


		//vertical menu style

		$css .= '
		.cbp-vertical-title{
			background-color: '.Configuration::get($this->config_name.'_ver_title_bg').';
			color: '.Configuration::get($this->config_name.'_ver_title_txt').';
			font-size: '.Configuration::get($this->config_name.'_ver_title_size').'px;
			line-height: '.Configuration::get($this->config_name.'_ver_title_height').'px;
			'.(Configuration::get($this->config_name.'_ver_title_bold') ? 'font-weight: bold;' : '').'		
			'.(Configuration::get($this->config_name.'_ver_title_uppercase') ? 'text-transform: uppercase;' : '').'		
		}
		.cbp-vertical-title:hover{
			background-color: '.Configuration::get($this->config_name.'_ver_title_bgh').';
			color: '.Configuration::get($this->config_name.'_ver_title_txth').';	
		}'.PHP_EOL;

		$css .= '
		.cbp-hrmenu.cbp-vertical > ul{
		'.$this->convertBorder(Configuration::get($this->config_name.'_ver_border_top'), 'top').'
		'.$this->convertBorder(Configuration::get($this->config_name.'_ver_border_bottom'), 'bottom').'
		'.$this->convertBorder(Configuration::get($this->config_name.'_ver_border_sides'), 'side').'
		background-color: '.Configuration::get($this->config_name.'_ver_bg_color').';
		';
		if($vermenubgimage!='')
		$css .= 'background-image: url('.$vermenubgimage.'); background-repeat: '.$this->convertBgRepeat(Configuration::get($this->config_name.'_ver_bg_repeat')).';';
		$css .= '}'.PHP_EOL;		

		$css .= '
		.cbp-vertical .cbp-legend{
			background-color: '.Configuration::get($this->config_name.'_ver_legend_bg_color').';
			color: '.Configuration::get($this->config_name.'_ver_legend_txt_color').';	
		}
		.cbp-vertical .cbp-legend .cbp-legend-arrow{
			color: '.Configuration::get($this->config_name.'_ver_legend_bg_color').';	
		}
		.cbp-vertical > ul > li.cbp-hropen > a, .cbp-vertical > ul > li.cbp-hropen > a:hover{
			background-color: '.Configuration::get($this->config_name.'_ver_link_hbg_color').';
			color: '.Configuration::get($this->config_name.'_ver_link_htxt_color').';	
		}
		.cbp-vertical > ul > li > a, .cbp-vertical > ul > li > span.cbp-main-link{
		color: '.Configuration::get($this->config_name.'_ver_link_txt_color').';
		padding-top: '.Configuration::get($this->config_name.'_ver_link_padding').'px;
		padding-bottom: '.Configuration::get($this->config_name.'_ver_link_padding').'px;
		'.(Configuration::get($this->config_name.'_ver_link_bold') ? 'font-weight: bold;' : '').'
		'.(Configuration::get($this->config_name.'_ver_link_italics') ? 'font-style: italic;' : '').'
		'.(Configuration::get($this->config_name.'_ver_link_uppercase') ? 'text-transform: uppercase;' : '').'
		font-size: '.Configuration::get($this->config_name.'_ver_link_fontsize').'px;
		'.$this->convertBorder(Configuration::get($this->config_name.'_ver_border_inner'), 'top').'
		}'.PHP_EOL;

		$css .= '.cbp-vertical > ul > li > a .cbp-mainlink-icon, .cbp-vertical > ul > li > a .cbp-mainlink-iicon{
			font-size: '.Configuration::get($this->config_name.'_ver_icon_fontsize').'px;
			max-height: '.Configuration::get($this->config_name.'_ver_icon_fontsize').'px;
		}';

		if(Configuration::get($this->config_name.'_ver_boxshadow')){
		$css .=	'.cbp-vertical-on-top .cbp-hrmenu.cbp-vertical > ul{
			-webkit-box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
			-moz-box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
			box-shadow:  0 2px 10px rgba(0, 0, 0, 0.15);
		}
		';
		}

		if(Configuration::get($this->config_name.'_hor_center'))
			$css .=	'
			
@media (min-width: 1000px) {
  #center-layered-nav.stick_layered .layeredSortBy {
    display: none; }
  #center-layered-nav.stick_layered .container {
    text-align: center; }
  #center-layered-nav.stick_layered #layered_block_left {
    display: inline-block;
    text-align: left; } }
			';	

		


		$tabs = array();
		$tabsV = array();
		$tabs = IqitMenuTab::getTabsFrontend(1, true);
		$tabsV = IqitMenuTab::getTabsFrontend(2, true);
		$tabs = array_merge($tabs, $tabsV);

		foreach ($tabs as $key => $tab)
		{
			if($tabs[$key]['bg_color']!='' || $tabs[$key]['txt_color']!='')
			$css .= '.cbp-hrmenu > ul > li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' > a, .cbp-hrmenu > ul > li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' > span.cbp-main-link
			{
				'.($tabs[$key]['bg_color']!='' ? 'background-color: '.$tabs[$key]['bg_color'].';' : '').'
				'.($tabs[$key]['txt_color']!='' ? 'color: '.$tabs[$key]['txt_color'].';' : '').'
			}'.PHP_EOL;

			if($tabs[$key]['h_bg_color']!='' || $tabs[$key]['h_txt_color']!='')
			$css .= '.cbp-hrmenu > ul > li.cbp-hropen.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' > a, .cbp-hrmenu > ul > li.cbp-hropen.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' > a:hover
			{
				'.($tabs[$key]['h_bg_color']!='' ? 'background-color: '.$tabs[$key]['h_bg_color'].';' : '').'
				'.($tabs[$key]['h_txt_color']!='' ? 'color: '.$tabs[$key]['h_txt_color'].';' : '').'
			}'.PHP_EOL;

			if($tabs[$key]['labeltxt_color']!='' || $tabs[$key]['labelbg_color']!='')
			$css .= '.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-legend-main{
				'.($tabs[$key]['labelbg_color']!='' ? 'background-color: '.$tabs[$key]['labelbg_color'].';' : '').'
				'.($tabs[$key]['labeltxt_color']!='' ? 'color: '.$tabs[$key]['labeltxt_color'].';' : '').'
			}'.PHP_EOL;

			if($tabs[$key]['labelbg_color']!='')
			$css .= '.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-legend-main .cbp-legend-arrow{
				'.($tabs[$key]['labelbg_color']!='' ? 'color: '.$tabs[$key]['labelbg_color'].';' : '').'
			}'.PHP_EOL;

			if($tabs[$key]['submenu_bg_color']!='')
			$css .= '.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-triangle-top{
				border-bottom-color: '.$tabs[$key]['submenu_bg_color'].';
			}';

			if($tabs[$key]['submenu_bg_color']!='' || $tabs[$key]['submenu_image']!='')
			$css .= '.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-hrsub-inner {
				'.($tabs[$key]['submenu_bg_color']!='' ? 'background-color: '.$tabs[$key]['submenu_bg_color'].';' : '').'
				'.($tabs[$key]['submenu_image']!='' ? 'background-image: url('.$tabs[$key]['submenu_image'].');' : '').'
				background-repeat: '.$this->convertBgRepeat($tabs[$key]['submenu_repeat']).';
				background-position: '.$this->convertBgPosition($tabs[$key]['submenu_bg_position']).';

			}
			.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' ul.cbp-hrsub-level2 {
				'.($tabs[$key]['submenu_bg_color']!='' ? 'background-color: '.$tabs[$key]['submenu_bg_color'].';' : '').'
			}
			';

		//custom submenu style

		if($tabs[$key]['submenu_title_color']!='')
		$css .=	'
		.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-menu-column-inner .cbp-column-title, .cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-menu-column-inner a.cbp-column-title:link {
			color: '.$tabs[$key]['submenu_title_color'].';
		}'.PHP_EOL;

		if($tabs[$key]['submenu_title_colorh']!='')
		$css .=	'.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-menu-column-inner a.cbp-column-title:hover {
			color: '.$tabs[$key]['submenu_title_colorh'].';
		}'.PHP_EOL;

		if($tabs[$key]['submenu_titleb_color']!='')
		$css .=	'.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-menu-column-inner .cbp-column-title {
			border-color: '.$tabs[$key]['submenu_titleb_color'].';
		}'.PHP_EOL;


		if($tabs[$key]['submenu_link_color']!='')
					$css .= '.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].'  .cbp-menu-column-inner a:link, .cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-menu-column-inner a, .cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-menu-column-inner {
						color: '.$tabs[$key]['submenu_link_color'].';
					}'.PHP_EOL;

		if($tabs[$key]['submenu_hover_color']!='')
					$css .= '.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].'  .cbp-menu-column-inner a:hover {
						color: '.$tabs[$key]['submenu_hover_color'].';
					}'.PHP_EOL;


		if($tabs[$key]['submenu_border_t']!='')
		$css .=	'
		.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-hrsub-inner{
			border-top-color: '.$tabs[$key]['submenu_border_t'].';
		}
		.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-triangle-top-back{
			border-bottom-color: '.$tabs[$key]['submenu_border_t'].';
		}
		'.PHP_EOL;

		if($tabs[$key]['submenu_border_r']!='')
		$css .=	'
		.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-hrsub-inner{
			border-right-color: '.$tabs[$key]['submenu_border_r'].';
		}'.PHP_EOL;

		if($tabs[$key]['submenu_border_b']!='')
		$css .=	'
		.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-hrsub-inner{
			border-bottom-color: '.$tabs[$key]['submenu_border_b'].';
		}'.PHP_EOL;

		if($tabs[$key]['submenu_border_l']!='')
		$css .=	'
		.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-hrsub-inner{
			border-left-color: '.$tabs[$key]['submenu_border_l'].';
		}'.PHP_EOL;

		if($tabs[$key]['submenu_border_i']!='')
		$css .=	'
		.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-hrsub-inner .menu_column, .cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-hrsub-tabs-names li, 
		.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-hrsub-inner .cbp-tab-pane
		{
			border-color: '.$tabs[$key]['submenu_border_i'].';
		}'.PHP_EOL;

		if(Configuration::get($this->config_name.'_hor_s_width') && !Configuration::get($this->config_name.'_hor_sw_width') && !Configuration::get($this->config_name.'_hor_width') )
			$css .= '.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab'].' .cbp-hrsub-tabs-names li{
				border-left-color: '.$tabs[$key]['submenu_border_i'].';
		}';


		//custom submenu columns style
			if($tab['submenu_type'] == 2)
				if (strlen(($tab['submenu_content'])))
					$css .=  $this->generateSubmenuCss($this->buildSubmenuTree(json_decode($tab['submenu_content'], true), true, true), '.cbp-hrmenu li.cbp-hrmenu-tab-'.$tabs[$key]['id_tab']);

				
		}

		$css .= '.cbp-hrmenu .cbp-hrsub-inner .cbp-tabs-names li a
			{
				background-color: '.Configuration::get($this->config_name.'_hor_sm_tab_bg_color').';
				color: '.Configuration::get($this->config_name.'_hor_sm_tab_txt_color').';
			}
			.cbp-hrmenu .cbp-submenu-it-indicator{ color: '.Configuration::get($this->config_name.'_hor_sm_tab_txt_color').';}
			'.PHP_EOL;

		$css .= '.cbp-tabs-names li a:hover, .cbp-hrmenu .cbp-hrsub-tabs-names li.active a,
			.cbp-tabs-names li .cbp-inner-border-hider
			{
				background-color: '.Configuration::get($this->config_name.'_hor_sm_tab_hbg_color').';
				color: '.Configuration::get($this->config_name.'_hor_sm_tab_htxt_color').';

			}
			.cbp-hrmenu li.active .cbp-submenu-it-indicator{ color: '.Configuration::get($this->config_name.'_hor_sm_tab_htxt_color').';}
			'.PHP_EOL;

		$tabs = IqitMenuTab::getTabsFrontend(3, true);

		foreach ($tabs as $key => $tab)
		{
			if($tabs[$key]['bg_color']!='' || $tabs[$key]['txt_color']!='')
			$css .= '.cbp-hrmenu .cbp-hrsub-inner .cbp-tabs-names li.innertab-'.$tabs[$key]['id_tab'].' a
			{
				'.($tabs[$key]['bg_color']!='' ? 'background-color: '.$tabs[$key]['bg_color'].';' : '').'
				'.($tabs[$key]['txt_color']!='' ? 'color: '.$tabs[$key]['txt_color'].';' : '').'
			}
			.cbp-hrmenu li.innertab-'.$tabs[$key]['id_tab'].' .cbp-submenu-it-indicator{ 	'.($tabs[$key]['txt_color']!='' ? 'color: '.$tabs[$key]['txt_color'].';' : '').'}
			'.PHP_EOL;

			if($tabs[$key]['h_bg_color']!='' || $tabs[$key]['h_txt_color']!='')
			$css .= '.cbp-tabs-names li.innertab-'.$tabs[$key]['id_tab'].' a:hover, .cbp-hrmenu .cbp-hrsub-tabs-names li.active.innertab-'.$tabs[$key]['id_tab'].' a,
			.cbp-tabs-names li.innertab-'.$tabs[$key]['id_tab'].' .cbp-inner-border-hider
			{
				'.($tabs[$key]['h_bg_color']!='' ? 'background-color: '.$tabs[$key]['h_bg_color'].';' : '').'
				'.($tabs[$key]['h_txt_color']!='' ? 'color: '.$tabs[$key]['h_txt_color'].';' : '').'
			}
			.cbp-hrmenu li.innertab-'.$tabs[$key]['id_tab'].'.active .cbp-submenu-it-indicator{'.($tabs[$key]['h_txt_color']!='' ? 'color: '.$tabs[$key]['h_txt_color'].';' : '').'}
			'.PHP_EOL;

			if($tabs[$key]['labeltxt_color']!='' || $tabs[$key]['labelbg_color']!='')
			$css .= '.cbp-hrmenu li.innertab-'.$tabs[$key]['id_tab'].' .cbp-legend-inner{
				'.($tabs[$key]['labelbg_color']!='' ? 'background-color: '.$tabs[$key]['labelbg_color'].';' : '').'
				'.($tabs[$key]['labeltxt_color']!='' ? 'color: '.$tabs[$key]['labeltxt_color'].';' : '').'
			}'.PHP_EOL;

			if($tabs[$key]['labelbg_color']!='')
			$css .= '.cbp-hrmenu li.innertab-'.$tabs[$key]['id_tab'].' .cbp-legend-inner .cbp-legend-arrow{
				'.($tabs[$key]['labelbg_color']!='' ? 'color: '.$tabs[$key]['labelbg_color'].';' : '').'
			}'.PHP_EOL;

			if($tabs[$key]['labelbg_color']!='')
			$css .= '.cbp-hrmenu li.innertab-'.$tabs[$key]['id_tab'].' .cbp-legend-inner .cbp-legend-arrow{
				'.($tabs[$key]['labelbg_color']!='' ? 'color: '.$tabs[$key]['labelbg_color'].';' : '').'
			}'.PHP_EOL;

			if($tabs[$key]['submenu_bg_color']!='' || $tabs[$key]['submenu_image']!='')
			$css .= '.cbp-hrmenu .innertabcontent-'.$tabs[$key]['id_tab'].'{
				'.($tabs[$key]['submenu_bg_color']!='' ? 'background-color: '.$tabs[$key]['submenu_bg_color'].';' : '').'
				'.($tabs[$key]['submenu_image']!='' ? 'background-image: url('.$tabs[$key]['submenu_image'].');' : '').'
				background-repeat: '.$this->convertBgRepeat($tabs[$key]['submenu_repeat']).';
				background-position: '.$this->convertBgPosition($tabs[$key]['submenu_bg_position']).';
			}
			.cbp-hrmenu .innertabcontent-'.$tabs[$key]['id_tab'].' ul.cbp-hrsub-level2 {
				'.($tabs[$key]['submenu_bg_color']!='' ? 'background-color: '.$tabs[$key]['submenu_bg_color'].'!important;' : '').'
			}
			';

			if($tab['submenu_type'] == 2)
				if (strlen(($tab['submenu_content'])))
					$css .=  $this->generateSubmenuCss($this->buildSubmenuTree(json_decode($tab['submenu_content'], true), true, true), '.cbp-hrmenu .innertabcontent-'.$tabs[$key]['id_tab']);


		}

		$hor_mb_border = Configuration::get($this->config_name.'_hor_mb_border');

		$hor_mb_c_border = Configuration::get($this->config_name.'_hor_mb_c_border');

		$css .= '#iqitmegamenu-mobile #iqitmegamenu-shower
			{
				background-color: '.Configuration::get($this->config_name.'_hor_mb_bg').';
				color: '.Configuration::get($this->config_name.'_hor_mb_txt').';
				'.$this->convertBorder($hor_mb_border, 'all').'
	
			}
			#iqitmegamenu-mobile .iqitmegamenu-icon{
				color: '.Configuration::get($this->config_name.'_hor_mb_bg').';
				background-color: '.Configuration::get($this->config_name.'_hor_mb_txt').';
			}

			#iqitmegamenu-accordion{
				background-color: '.Configuration::get($this->config_name.'_hor_mb_c_bg').';
				color: '.Configuration::get($this->config_name.'_hor_mb_c_txt').';
				'.$this->convertBorder($hor_mb_c_border, 'bottom').'
				'.$this->convertBorder($hor_mb_c_border, 'left').'
				'.$this->convertBorder($hor_mb_c_border, 'right').'
			}
			#iqitmegamenu-accordion{
				background-color: '.Configuration::get($this->config_name.'_hor_mb_c_bg').';
				color: '.Configuration::get($this->config_name.'_hor_mb_c_txt').';
			}
			#iqitmegamenu-mobile .iqitmegamenu-accordion > li ul{
				background-color: '.Configuration::get($this->config_name.'_hor_mb_csl_bg').';
			}
			#iqitmegamenu-accordion.cbp-spmenu > li ul, #cbp-close-mobile{
				background-color: '.Configuration::get($this->config_name.'_hor_mb_csl_bg').';
			}
			 #cbp-close-mobile, #cbp-close-mobile:active, #cbp-close-mobile:hover {
				color: '.Configuration::get($this->config_name.'_hor_mb_c_txt').';
			}
			#iqitmegamenu-mobile .iqitmegamenu-accordion > li ul a{
					'.$this->convertBorder(Configuration::get($this->config_name.'_hor_mb_c_borderi'), 'top').'
			}
			#iqitmegamenu-mobile .iqitmegamenu-accordion > li{
				'.$this->convertBorder(Configuration::get($this->config_name.'_hor_mb_c_borderi'), 'bottom').'
			}
			.cbp-spmenu-vertical a{
					'.$this->convertBorder(Configuration::get($this->config_name.'_hor_mb_c_borderi'), 'bottom').'
			}
			#iqitmegamenu-accordion.cbp-spmenu > li ul div.responsiveInykator{
				color: '.Configuration::get($this->config_name.'_hor_mb_c_txt').';
			}
			#iqitmegamenu-mobile .iqitmegamenu-accordion li a, .cbp-spmenu a{
				color: '.Configuration::get($this->config_name.'_hor_mb_c_txt').';
			}
			#iqitmegamenu-mobile .iqitmegamenu-accordion li a:hover{
				color: '.Configuration::get($this->config_name.'_hor_mb_c_txth').';
				background-color: '.Configuration::get($this->config_name.'_hor_mb_c_lhbg').';
				padding-left: 10px;
			}
			.cbp-spmenu a:hover{
				color: '.Configuration::get($this->config_name.'_hor_mb_c_txth').';
				background-color: '.Configuration::get($this->config_name.'_hor_mb_c_lhbg').';

			}

			#iqitmegamenu-accordion div.responsiveInykator{
				color: '.Configuration::get($this->config_name.'_hor_mb_c_plus').';
			}

			'.PHP_EOL;



		$css .= Configuration::get($this->config_name.'_hor_custom_css');

		$css  = trim(preg_replace('/\s+/', ' ', $css));

		if (Shop::getContext() == Shop::CONTEXT_GROUP)
			$my_file = $this->local_path.'css/iqitmegamenu_g_'.(int)$this->context->shop->getContextShopGroupID().'.css';
		elseif (Shop::getContext() == Shop::CONTEXT_SHOP)
			$my_file = $this->local_path.'css/iqitmegamenu_s_'.(int)$this->context->shop->getContextShopID().'.css';
		
		$fh = fopen($my_file, 'w') or die("can't open file");
		fwrite($fh, $css);
		fclose($fh);
	}
	
	public function generateSubmenuCss($submenu, $parent)
	{	
			$css = ''.PHP_EOL;
			foreach ($submenu as $key => $element)
			{	
				

				if(
				isset($element['content_s']['bg_color']) ||
				isset($element['content_s']['br_top_st']) ||
				isset($element['content_s']['br_right_st']) ||
				isset($element['content_s']['br_bottom_st']) ||
				isset($element['content_s']['br_left_st']) ||
				isset($element['content_s']['c_m_t']) ||
				isset($element['content_s']['c_m_r']) ||
				isset($element['content_s']['c_m_b']) ||
				isset($element['content_s']['c_m_l'])
				)
				$css .= $parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner{
					'.(isset($element['content_s']['bg_color']) && $element['content_s']['bg_color'] != '' ? 'background-color: '.$element['content_s']['bg_color'].';' : '').'
					'.(isset($element['content_s']['br_top_st']) && $element['content_s']['br_top_st'] != '' ? 'border-top-style: '.$element['content_s']['br_top_st'].';' : '').'
					'.(isset($element['content_s']['br_top_wh']) && $element['content_s']['br_top_wh'] != '' ? 'border-top-width: '.$element['content_s']['br_top_wh'].'px;' : '').'
					'.(isset($element['content_s']['br_right_st']) && $element['content_s']['br_right_st'] != '' ? 'border-right-style: '.$element['content_s']['br_right_st'].';' : '').'
					'.(isset($element['content_s']['br_right_wh']) && $element['content_s']['br_right_wh'] != '' ? 'border-right-width: '.$element['content_s']['br_right_wh'].'px;' : '').'
					'.(isset($element['content_s']['br_bottom_st']) && $element['content_s']['br_bottom_st'] != '' ? 'border-bottom-style: '.$element['content_s']['br_bottom_st'].';' : '').'
					'.(isset($element['content_s']['br_bottom_wh']) && $element['content_s']['br_bottom_wh'] != '' ? 'border-bottom-width: '.$element['content_s']['br_bottom_wh'].'px;' : '').'
					'.(isset($element['content_s']['br_left_st']) && $element['content_s']['br_left_st'] != '' ? 'border-left-style: '.$element['content_s']['br_left_st'].';' : '').'
					'.(isset($element['content_s']['br_left_wh']) && $element['content_s']['br_left_wh'] != '' ? 'border-left-width: '.$element['content_s']['br_left_wh'].'px;' : '').'

					'.(isset($element['content_s']['br_top_c']) && $element['content_s']['br_top_c'] != '' ? 'border-top-color: '.$element['content_s']['br_top_c'].';' : '').'
					'.(isset($element['content_s']['br_right_c']) && $element['content_s']['br_right_c'] != '' ? 'border-right-color: '.$element['content_s']['br_right_c'].';' : '').'
					'.(isset($element['content_s']['br_bottom_c']) && $element['content_s']['br_bottom_c'] != '' ? 'border-bottom-color: '.$element['content_s']['br_bottom_c'].';' : '').'	
					'.(isset($element['content_s']['br_left_c']) && $element['content_s']['br_left_c'] != '' ? 'border-left-color: '.$element['content_s']['br_left_c'].';' : '').'

					'.(isset($element['content_s']['c_m_t']) ? 'margin-top: -10px;' : '').'
					'.(isset($element['content_s']['c_m_r']) ? 'margin-right: -10px;' : '').'
					'.(isset($element['content_s']['c_m_b']) ? 'margin-bottom: -10px;' : '').'
					'.(isset($element['content_s']['c_m_l']) ? 'margin-left: -10px;' : '').'

					'.(isset($element['content_s']['c_p_t']) ? 'padding-top: 10px;' : '').'
					'.(isset($element['content_s']['c_p_r']) ? 'padding-right: 10px;' : '').'
					'.(isset($element['content_s']['c_p_b']) ? 'padding-bottom: 10px;' : '').'
					'.(isset($element['content_s']['c_p_l']) ? 'padding-left: 10px;' : '').'

				}  
				'.$parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner ul.cbp-hrsub-level2 {
				'.(isset($element['content_s']['bg_color']) && $element['content_s']['bg_color'] != '' ? 'background-color: '.$element['content_s']['bg_color'].'!important;' : '').'
					}

				'.PHP_EOL;

				if(
					isset($element['content_s']['legend_bg']) ||
					isset($element['content_s']['legend_txt']) 
				){
					$css .= $parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner .cbp-legend-inner{
						'.(isset($element['content_s']['legend_bg']) && $element['content_s']['legend_bg'] != '' ? 'background-color: '.$element['content_s']['legend_bg'].';' : '').'
						'.(isset($element['content_s']['legend_txt']) && $element['content_s']['legend_txt'] != '' ? 'color: '.$element['content_s']['legend_txt'].';' : '').'

					}'.PHP_EOL;

					$css .= $parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner .cbp-legend-arrow{
						'.(isset($element['content_s']['legend_bg']) && $element['content_s']['legend_bg'] != '' ? 'color: '.$element['content_s']['legend_bg'].';' : '').'

					}'.PHP_EOL;

				}
				

				if(	isset($element['content_s']['title_color']))
					$css .= $parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner .cbp-column-title{
						color: '.$element['content_s']['title_color'].' !important;
					}'.PHP_EOL;

				if(	isset($element['content_s']['title_colorh']))
					$css .= $parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner .cbp-column-title:hover{
						color: '.$element['content_s']['title_colorh'].' !important;
					}'.PHP_EOL;

				if(	isset($element['content_s']['title_borderc']))
					$css .= $parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner .cbp-column-title{
						border-color: '.$element['content_s']['title_borderc'].' !important;
					}'.PHP_EOL;

				if(	isset($element['content_s']['txt_color']))
					$css .= $parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner a:link, '.$parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner a,'.$parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner {
						color: '.$element['content_s']['txt_color'].';
					}'.PHP_EOL;

				if(	isset($element['content_s']['txt_colorh']))
					$css .= $parent. ' .menu-element-id-'.$element['elementId'].' > .cbp-menu-column-inner a:hover {
						color: '.$element['content_s']['txt_colorh'].';
					}'.PHP_EOL;
				

				if(isset($element['content']['absolute']))
					$css .= $parent. ' .menu-element-id-'.$element['elementId'].'{
						'.(isset($element['content']['i_a_t']) && $element['content']['i_a_t'] != '' ? 'top: '.$element['content']['i_a_t'].'px;' : '').'
						'.(isset($element['content']['i_a_r']) && $element['content']['i_a_r'] != '' ? 'right: '.$element['content']['i_a_r'].'px;' : '').'
						'.(isset($element['content']['i_a_b']) && $element['content']['i_a_b'] != '' ? 'bottom: '.$element['content']['i_a_b'].'px;' : '').'
						'.(isset($element['content']['i_a_l']) && $element['content']['i_a_l'] != '' ? 'left: '.$element['content']['i_a_l'].'px;' : '').'
					}'.PHP_EOL;

				

				if(isset($element['children']))
					$css .= $this->generateSubmenuCss($element['children'], $parent);
			}

			return $css;

	}	
	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader()
	{
		


		$this->context->controller->addJS($this->_path.'/js/classie.js');
		$this->context->controller->addJS($this->_path.'/js/front_horizontal.js');

			$this->context->controller->addJS($this->_path.'/js/front_vertical.js');

		if(Configuration::get($this->config_name.'_hor_sticky'))	
			$this->context->controller->addJS($this->_path.'/js/front_sticky.js');

		$this->context->controller->addJS($this->_path.'/js/mlpushmenu.js');

		

		$this->context->controller->addCSS($this->_path.'/css/front.css');
		if (Shop::getContext() == Shop::CONTEXT_GROUP)
		$this->context->controller->addCSS(($this->_path).'css/iqitmegamenu_g_'.(int)$this->context->shop->getContextShopGroupID().'.css', 'all');
		elseif (Shop::getContext() == Shop::CONTEXT_SHOP)
		$this->context->controller->addCSS(($this->_path).'css/iqitmegamenu_s_'.(int)$this->context->shop->getContextShopID().'.css', 'all');	

	


	}

	public function hookBackOfficeHeader()
	{
		$html = '';

		return $html;

		
	}



    public function hookDisplayLeftColumn($params)
    {

  
    	if($this->ver_position == 1 || ($this->ver_position == 4 && (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index')))
		{
		
		$this->user_groups =  ($this->context->customer->isLogged() ? $this->context->customer->getGroups() : array(Configuration::get('PS_UNIDENTIFIED_GROUP')));
		$this->hor_sm_order = (Configuration::get($this->config_name.'_hor_sm_order') == 1 ? ' ORDER BY c.`level_depth` ASC, cl.`name` ASC' : '');
		

		if (!$this->isCached('iqitmegamenu_vertical.tpl', $this->getCacheId()))
		{
		$this->_prepareHookVertical($params);
		}

		return $this->display(__FILE__, 'iqitmegamenu_vertical.tpl', $this->getCacheId());
		}

    }

    public function hookIqitContentCreator($params) 
	{
		$this->user_groups =  ($this->context->customer->isLogged() ? $this->context->customer->getGroups() : array(Configuration::get('PS_UNIDENTIFIED_GROUP')));
		$this->hor_sm_order = (Configuration::get($this->config_name.'_hor_sm_order') == 1 ? ' ORDER BY c.`level_depth` ASC, cl.`name` ASC' : '');
		

		if (!$this->isCached('iqitmegamenu_vertical.tpl', $this->getCacheId()))
		{
			$this->_prepareHookVertical($params);
		}

		return $this->display(__FILE__, 'iqitmegamenu_vertical.tpl', $this->getCacheId());
	}	

    public function hookiqitMegaMenu($params)
    {	
    	$sw_width = Configuration::get($this->config_name.'_hor_sw_width');
			if(!$sw_width)
				Media::addJsDef(array('iqitmegamenu_swwidth' => true));
			else
				Media::addJsDef(array('iqitmegamenu_swwidth' => false));

    	if(Configuration::get($this->config_name.'_hor_width'))
    	{

    		$this->user_groups =  ($this->context->customer->isLogged() ? $this->context->customer->getGroups() : array(Configuration::get('PS_UNIDENTIFIED_GROUP')));
			$this->hor_sm_order = (Configuration::get($this->config_name.'_hor_sm_order') == 1 ? ' ORDER BY c.`level_depth` ASC, cl.`name` ASC' : '');
			$pos = $this->ver_position;
			$cache = 10;
				if( $pos == 5 && (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index') )
			$cache = 5;

    		if (!$this->isCached('iqitmegamenu.tpl', $this->getCacheId($cache)))
    		{
    			$this->_prepareHook($params);
    			
    			if( $pos == 2 || $pos == 3 || $pos == 6 || ($pos == 5 && (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index')))
    			{
    				$this->_prepareHookVertical($params, $pos);

    			}
    		}
    		return $this->display(__FILE__, 'iqitmegamenu.tpl', $this->getCacheId($cache));
    	}
    }



	public function hookmaxHeader($params)
	{	
		if(!Configuration::get($this->config_name.'_hor_width'))
		{
		
		$this->user_groups =  ($this->context->customer->isLogged() ? $this->context->customer->getGroups() : array(Configuration::get('PS_UNIDENTIFIED_GROUP')));
		$this->hor_sm_order = (Configuration::get($this->config_name.'_hor_sm_order') == 1 ? ' ORDER BY c.`level_depth` ASC, cl.`name` ASC' : '');
		$pos = $this->ver_position;
		$cache = 10;
		if( $pos == 5 && (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index') )
			$cache = 5;

		if (!$this->isCached('iqitmegamenu.tpl', $this->getCacheId($cache )))
    		{
    			$this->_prepareHook($params);
      			if( $pos == 2 || $pos == 3 || $pos == 6 || ($pos == 5 && (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index')))
    			{
    				$this->_prepareHookVertical($params, $pos);

    			}
    		}

		return $this->display(__FILE__, 'iqitmegamenu.tpl', $this->getCacheId($cache ));
		}
	}

	public function getCacheId($cache = 10)
	{
		return parent::getCacheId().'|'.(int)$cache;
	}

	public function hookDisplayTop($params){
		return $this->hookiqitMegaMenu($params);
	}


	public function _prepareHookVertical($params, $pos = 0){

			if($pos == 5)
				$pos = 3;

			$menu_settings_v = array(
				'ver_animation' => Configuration::get($this->config_name.'_ver_animation'),
				'ver_arrow' => Configuration::get($this->config_name.'_ver_arrow'),
				'ver_position' => $pos,
				'ver_s_arrow' => Configuration::get($this->config_name.'_ver_s_arrow')
				);
			
			$this->smarty->assign(array(
				'menu_settings_v' => $menu_settings_v,
				'vertical_menu' => $this->makeMegaMenu(2),
			));
		
		
	}


	public function _prepareHook($params){

		$sw_width = Configuration::get($this->config_name.'_hor_sw_width');

			$menu_settings = array(
				'hor_width' => Configuration::get($this->config_name.'_hor_width'),
				'hor_sw_width' => $sw_width,
				'hor_s_width' => Configuration::get($this->config_name.'_hor_s_width'),
				'hor_sticky' => Configuration::get($this->config_name.'_hor_sticky'),
				'hor_s_transparent' => Configuration::get($this->config_name.'_hor_s_transparent'),
				'hor_animation' => Configuration::get($this->config_name.'_hor_animation'),
				'hor_center' => Configuration::get($this->config_name.'_hor_center'),
				'hor_arrow' => Configuration::get($this->config_name.'_hor_arrow'),
				'hor_s_arrow' => Configuration::get($this->config_name.'_hor_s_arrow')
				);
			
			$this->smarty->assign(array(
				'mobile_menu_style' => 0,
				'mobile_menu' => $this->makeMenuMobile(),
				'menu_settings' => $menu_settings,
				'horizontal_menu' => $this->makeMegaMenu(1),
				'this_path' => $this->_path

			));
		
		
	}
	

	public function renderSelectedTabsSelect($tabs)
	{

		$id_shop = (int)Context::getContext()->shop->id;
		$id_lang = (int)Context::getContext()->language->id;

		$html = '<select name="items[]" id="items" multiple="multiple" style="width: 300px; height: 160px;" >';

		if (strlen($tabs))
		{
			$tabs = explode(',',$tabs);

			foreach($tabs as $tab_id) { 
			   	$tab = new IqitMenuTab($tab_id, $id_lang, $id_shop);
				$html .= '<option selected="selected" value="'.$tab->id.'">'.$tab->title.'(id: '.$tab->id.')</option>'; 
			}

		}
	
		$html .= '</select>';
		return $html;
	}

	public function renderChoicesTabsSelect()
	{

		$tabs = array();
		$tabs = IqitMenuTab::getTabs(3);

		$html = '<select name="availableItems" id="availableItems" multiple="multiple" style="width: 300px; height: 160px;" >';
		
		foreach ($tabs as $tab)
			$html .= '<option value="'.$tab['id_tab'].'">'.$tab['title'].'(id: '.$tab['id_tab'].')</option>';
	
		$html .= '</select>';
		return $html;
	}

	public function renderManufacturersSelect()
	{
		$return_manufacturers = array();

		$manufacturers = Manufacturer::getManufacturers(false, $this->context->language->id);
		foreach ($manufacturers as $key=>$manufacturer)
		{
			$return_manufacturers[$key]['name'] = $manufacturer['name'];
			$return_manufacturers[$key]['id'] =  $manufacturer['id_manufacturer'];
		}

		return $return_manufacturers;
	}

	public function renderCategoriesSelect($frontend)
	{
		$return_categories = array();

		$shops_to_get = Shop::getContextListShopID();

		foreach ($shops_to_get as $shop_id)
			$return_categories = $this->generateCategoriesOption2(Category::GetNestedCategories(null, (int)$this->context->language->id, true), $frontend);


		return $return_categories;
	}

	public function renderCustomHtmlSelect()
	{	
		$custom_html = array();
		
		$id_lang = (int)Context::getContext()->language->id;

		$custom_html = IqitMenuHtml::getHtmls();

		return $custom_html;

	}

	public function renderChoicesSelect($single = false, $name = null, $class = null, $mobile = false)
	{
		$spacer = str_repeat('&nbsp;', $this->spacer_size);
		$items = array();



		$html = '<select '.($class ? 'class="'.$class.'"' : '').' '.($mobile ? 'id="availableItems"' : '').' '.($name ? 'name="'.$name.'" id="'.$name.'"' : '').' '.($single ? '' : 'multiple="multiple" style="width: 300px; height: 160px;"').'>';
		$html .= '<option value="HOME0">'.$this->l('Homepage').'</option>';
		$html .= '<optgroup label="'.$this->l('CMS').'">';
		$html .= $this->getCMSOptions(0, 1, $this->context->language->id, $items, $single);
		$html .= '</optgroup>';

		// BEGIN SUPPLIER
		$html .= '<optgroup label="'.$this->l('Supplier').'">';
		// Option to show all Suppliers
		$html .= '<option value="ALLSUP0">'.$this->l('All suppliers').'</option>';
		$suppliers = Supplier::getSuppliers(false, $this->context->language->id);
		foreach ($suppliers as $supplier)
			if (!in_array('SUP'.$supplier['id_supplier'], $items))
				$html .= '<option value="SUP'.$supplier['id_supplier'].'">'.$spacer.$supplier['name'].'</option>';
		$html .= '</optgroup>';

		// BEGIN Manufacturer
		$html .= '<optgroup label="'.$this->l('Manufacturer').'">';
		// Option to show all Manufacturers
		$html .= '<option value="ALLMAN0">'.$this->l('All manufacturers').'</option>';
		$manufacturers = Manufacturer::getManufacturers(false, $this->context->language->id);
		foreach ($manufacturers as $manufacturer)
			if (!in_array('MAN'.$manufacturer['id_manufacturer'], $items))
				$html .= '<option value="MAN'.$manufacturer['id_manufacturer'].'">'.$spacer.$manufacturer['name'].'</option>';
		$html .= '</optgroup>';

		// BEGIN Categories
		$shop = new Shop((int)Shop::getContextShopID());
		$html .= '<optgroup label="'.$this->l('Categories').'">';

		$shops_to_get = Shop::getContextListShopID();

		foreach ($shops_to_get as $shop_id)
					$html .= $this->generateCategoriesOption(Category::GetNestedCategories(null, (int)$this->context->language->id, true), $single);
				$html .= '</optgroup>';

		// BEGIN Shops
		if (Shop::isFeatureActive())
		{
			$html .= '<optgroup label="'.$this->l('Shops').'">';
			$shops = Shop::getShopsCollection();
			foreach ($shops as $shop)
			{
				if (!$shop->setUrl() && !$shop->getBaseURL())
					continue;

				if (!in_array('SHOP'.(int)$shop->id, $items))
					$html .= '<option value="SHOP'.(int)$shop->id.'">'.$spacer.$shop->name.'</option>';
			}
			$html .= '</optgroup>';
		}

		// BEGIN Products
		if($mobile)
		{
		$html .= '<optgroup label="'.$this->l('Products').'">';
		$html .= '<option value="PRODUCT" style="font-style:italic">'.$spacer.$this->l('Choose product ID').'</option>';
		$html .= '</optgroup>';
		}

		// BEGIN Menu Top Links
		$html .= '<optgroup label="'.$this->l('Custom links').'">';
		$links = IqitMenuLinks::gets($this->context->language->id, null, (int)Shop::getContextShopID());
		foreach ($links as $link)
		{
			if ($link['label'] == '')
			{
				$default_language = Configuration::get('PS_LANG_DEFAULT');
				$link = IqitMenuLinks::get($link['id_iqitmenulinks'], $default_language, (int)Shop::getContextShopID());
				if (!in_array('LNK'.(int)$link[0]['id_iqitmenulinks'], $items))
					$html .= '<option value="LNK'.(int)$link[0]['id_iqitmenulinks'].'">'.$spacer.Tools::safeOutput($link[0]['label']).'</option>';
			}
			elseif (!in_array('LNK'.(int)$link['id_iqitmenulinks'], $items))
				$html .= '<option value="LNK'.(int)$link['id_iqitmenulinks'].'">'.$spacer.Tools::safeOutput($link['label']).'</option>';
		}
		$html .= '</optgroup>';
		$html .= '</select>';
		return $html;
	}

	private function getMenuItems()
	{
		$items = Tools::getValue('items');
		if (is_array($items) && count($items))
			return $items;
		else
		{
			$shops = Shop::getContextListShopID();
			$conf = null;

			if (count($shops) > 1)
			{
				foreach ($shops as $key => $shop_id)
				{
					$shop_group_id = Shop::getGroupFromShop($shop_id);
					$conf .= (string)($key > 1 ? ',' : '').Configuration::get($this->config_name.'_mobile_menu', null, $shop_group_id, $shop_id);
				}
			}
			else
			{
				$shop_id = (int)$shops[0];
				$shop_group_id = Shop::getGroupFromShop($shop_id);
				$conf = Configuration::get($this->config_name.'_mobile_menu', null, $shop_group_id, $shop_id);
			}

			if (strlen($conf))
				return explode(',', $conf);
			else
				return array();
		}
	}


	private function makeMenuOptionMobile()
	{
		$id_shop = (int)Shop::getContextShopID();

		$menu_item = $this->getMenuItems();
		$id_lang = (int)$this->context->language->id;

		$html = '<select multiple="multiple" name="items[]" id="items" style="width: 300px; height: 160px;">';
		foreach ($menu_item as $item)
		{
			if (!$item)
				continue;

			preg_match($this->pattern, $item, $values);
			$id = (int)substr($item, strlen($values[1]), strlen($item));

			switch (substr($item, 0, strlen($values[1])))
			{
				case 'CAT':
					$category = new Category((int)$id, (int)$id_lang);
					if (Validate::isLoadedObject($category))
						$html .= '<option selected="selected" value="CAT'.$id.'">'.$category->name.'</option>'.PHP_EOL;
					break;

				 case 'CMS_CAT':
                    $category = new CMSCategory((int) $id, (int) $id_lang);
                    if (Validate::isLoadedObject($category)) {
                        $html .= '<option selected="selected" value="CMS_CAT' . $id . '">' . $category->name . '</option>' . PHP_EOL;
                    }

                    break;  


				case 'PRD':
					$product = new Product((int)$id, true, (int)$id_lang);
					if (Validate::isLoadedObject($product))
						$html .= '<option selected="selected" value="PRD'.$id.'">'.$product->name.'</option>'.PHP_EOL;
					break;

				case 'CMS':
					$cms = new CMS((int)$id, (int)$id_lang);
					if (Validate::isLoadedObject($cms))
						$html .= '<option selected="selected" value="CMS'.$id.'">'.$cms->page_title.'</option>'.PHP_EOL;
					break;

					// Case to handle the option to show all Manufacturers
				case 'ALLMAN':
					$html .= '<option selected="selected" value="ALLMAN0">'.$this->l('All manufacturers').'</option>'.PHP_EOL;
					break;

				case 'MAN':
					$manufacturer = new Manufacturer((int)$id, (int)$id_lang);
					if (Validate::isLoadedObject($manufacturer))
						$html .= '<option selected="selected" value="MAN'.$id.'">'.$manufacturer->name.'</option>'.PHP_EOL;
					break;

				// Case to handle the option to show all Suppliers
				case 'ALLSUP':
					$html .= '<option selected="selected" value="ALLSUP0">'.$this->l('All suppliers').'</option>'.PHP_EOL;
					break;

				case 'HOME':
					$html .= '<option selected="selected" value="HOME0">'.$this->l('Homepage').'</option>'.PHP_EOL;
					break;

				case 'SUP':
					$supplier = new Supplier((int)$id, (int)$id_lang);
					if (Validate::isLoadedObject($supplier))
						$html .= '<option selected="selected" value="SUP'.$id.'">'.$supplier->name.'</option>'.PHP_EOL;
					break;

				case 'LNK':
					$link = IqitMenuLinks::get((int)$id, (int)$id_lang, (int)$id_shop);
					if (count($link))
					{
						if (!isset($link[0]['label']) || ($link[0]['label'] == ''))
						{
							$default_language = Configuration::get('PS_LANG_DEFAULT');
							$link = IqitMenuLinks::get($link[0]['id_iqitmenulinks'], (int)$default_language, (int)Shop::getContextShopID());
						}
						$html .= '<option selected="selected" value="LNK'.(int)$link[0]['id_iqitmenulinks'].'">'.Tools::safeOutput($link[0]['label']).'</option>';
					}
					break;

				case 'SHOP':
					$shop = new Shop((int)$id);
					if (Validate::isLoadedObject($shop))
						$html .= '<option selected="selected" value="SHOP'.(int)$id.'">'.$shop->name.'</option>'.PHP_EOL;
					break;
			}
		}
		return $html.'</select>';
	}

	

	private function getCMSOptions($parent = 0, $depth = 1, $id_lang = false, $items_to_skip = null, $single = false, $id_shop = false)
	{
		$html = '';
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$id_shop = ($id_shop !== false) ? $id_shop : Context::getContext()->shop->id;
		$categories = $this->getCMSCategories(false, (int)$parent, (int)$id_lang, (int)$id_shop);
		$pages = $this->getCMSPages((int)$parent, (int)$id_lang, (int)$id_shop);

		$spacer = str_repeat('&nbsp;', $this->spacer_size * (int)$depth);

		  foreach ($categories as $category) {
            if (isset($items_to_skip) && !in_array('CMS_CAT' . $category['id_cms_category'], $items_to_skip)) {
                $html .= '<option value="CMS_CAT' . $category['id_cms_category'] . '" style="font-weight: bold;">' . $spacer . $category['name'] . '</option>';
            }

            $html .= $this->getCMSOptions($category['id_cms_category'], (int) $depth + 1, (int) $id_lang, $items_to_skip, $single);
        }

		foreach ($pages as $page)
			if (isset($items_to_skip) && !in_array('CMS'.$page['id_cms'], $items_to_skip))
				$html .= '<option value="CMS'.$page['id_cms'].'">'.$spacer.$page['meta_title'].'</option>';

		return $html;
	}

	protected function getCMSMenuItems($parent, $depth = 1, $id_lang = false)
    {   
        $cmspages = '';
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

        if ($depth > 3) {
            return;
        }

        $categories = $this->getCMSCategories(false, (int)$parent, (int)$id_lang);
        $pages = $this->getCMSPages((int)$parent);

        if (count($categories) || count($pages)) {
            $cmspages .= '<ul>';

            foreach ($categories as $category) {
                $cat = new CMSCategory((int)$category['id_cms_category'], (int)$id_lang);

                $cmspages .=  '<li>';
                $cmspages .=  '<a href="'.Tools::HtmlEntitiesUTF8($cat->getLink()).'">'.$category['name'].'</a>';
                $cmspages .= $this->getCMSMenuItems($category['id_cms_category'], (int)$depth + 1);
                $cmspages .= '</li>';
            }

            foreach ($pages as $page) {
                $cms = new CMS($page['id_cms'], (int)$id_lang);
                $links = $cms->getLinks((int)$id_lang, array((int)$cms->id));

                $cmspages .=  '<li>';
                $cmspages .= '<a href="'.$links[0]['link'].'">'.$cms->meta_title.'</a>';
                $cmspages .=  '</li>';
            }

            $cmspages .=  '</ul>';
        }
        return $cmspages;
    }

	private function getCMSCategories($recursive = false, $parent = 1, $id_lang = false, $id_shop = false)
	{
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$id_shop = ($id_shop !== false) ? $id_shop : Context::getContext()->shop->id;
		$join_shop = '';
		$where_shop = '';

		if (Tools::version_compare(_PS_VERSION_, '1.6.0.12', '>=') == true)
		{
			$join_shop = ' INNER JOIN `'._DB_PREFIX_.'cms_category_shop` cs
			ON (bcp.`id_cms_category` = cs.`id_cms_category`)';
			$where_shop = ' AND cs.`id_shop` = '.(int)$id_shop.' AND cl.`id_shop` = '.(int)$id_shop;
		}

		if ($recursive === false)
		{
			$sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_category` bcp'.
				$join_shop.'
				INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.'
				AND bcp.`id_parent` = '.(int)$parent.
				$where_shop;

			return Db::getInstance()->executeS($sql);
		}
		else
		{
			$sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_category` bcp'.
				$join_shop.'
				INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.'
				AND bcp.`id_parent` = '.(int)$parent.
				$where_shop;

			$results = Db::getInstance()->executeS($sql);
			foreach ($results as $result)
			{
				$sub_categories = $this->getCMSCategories(true, $result['id_cms_category'], (int)$id_lang);
				if ($sub_categories && count($sub_categories) > 0)
					$result['sub_categories'] = $sub_categories;
				$categories[] = $result;
			}

			return isset($categories) ? $categories : false;
		}

	}

	private function getCMSPages($id_cms_category, $id_lang = false, $id_shop = false)
	{
		$id_shop = ($id_shop !== false) ? (int)$id_shop : (int)Context::getContext()->shop->id;
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

		$where_shop = '';
		if (Tools::version_compare(_PS_VERSION_, '1.6.0.12', '>=') == true)
			$where_shop = ' AND cl.`id_shop` = '.(int)$id_shop;

		$sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
			FROM `'._DB_PREFIX_.'cms` c
			INNER JOIN `'._DB_PREFIX_.'cms_shop` cs
			ON (c.`id_cms` = cs.`id_cms`)
			INNER JOIN `'._DB_PREFIX_.'cms_lang` cl
			ON (c.`id_cms` = cl.`id_cms`)
			WHERE c.`id_cms_category` = '.(int)$id_cms_category.'
			AND cs.`id_shop` = '.(int)$id_shop.'
			AND cl.`id_lang` = '.(int)$id_lang.
			$where_shop.'
			AND c.`active` = 1
			ORDER BY `position`';

		return Db::getInstance()->executeS($sql);
	}
	private function generateCategoriesOption($categories, $single = false)
	{
		$html = '';

		foreach ($categories as $key => $category)
		{
		
				$shop = (object) Shop::getShop((int)$category['id_shop']);
				$html .= '<option value="CAT'.(int)$category['id_category'].'" '.($single && ($category['level_depth']==0 || $category['level_depth']==1) ? 'disabled' : '').'  >'
					.str_repeat('&nbsp;', $this->spacer_size * (int)$category['level_depth']).$category['name'].' ('.$shop->name.')</option>';
			

			if (isset($category['children']) && !empty($category['children']))
				$html .= $this->generateCategoriesOption($category['children'], $single);

		}
		return $html;
	}

	private function generateCategoriesOption2($categories, $frontend)
	{
		$return_categories = array();

		foreach ($categories as $key => $category)
		{
				$shop = (object) Shop::getShop((int)$category['id_shop']);

				$return_categories[$key]['id'] =  (int)$category['id_category'];
				$return_categories[$key]['name'] = (!$frontend ? str_repeat('&nbsp;', $this->spacer_size * (int)$category['level_depth']) : '').$category['name'].' ('.$shop->name.')';
			

			if (isset($category['children']) && !empty($category['children']))
				$return_categories[$key]['children'] = $this->generateCategoriesOption2($category['children'], $frontend);
		}

		return $return_categories;

	}

	public function getAddLinkFieldsValues()
	{
		$links_label_edit = '';
		$labels_edit = '';
		$new_window_edit = '';

	

		if (Tools::getValue('submitAddmodule'))
		{
			foreach (Language::getLanguages(false) as $lang)
			{
				$fields_values['label'][$lang['id_lang']] = '';
				$fields_values['link'][$lang['id_lang']] = '';
			}
		}
		else
			foreach (Language::getLanguages(false) as $lang)
			{
				$fields_values['label'][$lang['id_lang']] = Tools::getValue('label_'.(int)$lang['id_lang'], isset($labels_edit[$lang['id_lang']]) ? $labels_edit[$lang['id_lang']] : '');
				$fields_values['link'][$lang['id_lang']] = Tools::getValue('link_'.(int)$lang['id_lang'], isset($links_label_edit[$lang['id_lang']]) ? $links_label_edit[$lang['id_lang']] : '');
			}

		return $fields_values;
	}

	public function renderTabsLinks($menu_type)
	{
		$shops = Shop::getContextListShopID();
		$tabs = array();

		
		$tabs = IqitMenuTab::getTabs($menu_type);

		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'tabs' => $tabs,
				'iqitdevmode'=>$this->iqitdevmode,
				'menu_type' => $menu_type
			)
		);

		return $this->display(__FILE__, 'list.tpl');
	}

	public function renderHtmlContents(){

		$shops = Shop::getContextListShopID();
		$tabs = array();

		
		$tabs = IqitMenuHtml::getHtmls();

		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'tabs' => $tabs,
			)
		);

		return $this->display(__FILE__, 'listhtml.tpl');

	}


	public function renderListCustomLinks()
	{
		$shops = Shop::getContextListShopID();
		$links = array();

		foreach ($shops as $shop_id)
			$links = array_merge($links,  IqitMenuLinks::gets((int)$this->context->language->id, null, (int)$shop_id));

		$fields_list = array(
			'id_iqitmenulinks' => array(
				'title' => $this->l('Link ID'),
				'type' => 'text',
			),
			'name' => array(
				'title' => $this->l('Shop name'),
				'type' => 'text',
			),
			'label' => array(
				'title' => $this->l('Label'),
				'type' => 'text',
			),
			'link' => array(
				'title' => $this->l('Link'),
				'type' => 'link',
			)			
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_iqitmenulinks';
		$helper->table = 'iqitmenulinks';
		$helper->actions = array('delete', 'edit');
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->title = $this->l('Link list');
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		return $helper->generateList($links, $fields_list);
	}
	
	private function makeMegaMenu($menu_type)
	{
		
		$id_lang = (int)$this->context->language->id;
		$id_shop = (int)Shop::getContextShopID();

		$tabs = array();
		
		$tabs = IqitMenuTab::getTabsFrontend($menu_type, false);

		foreach ($tabs as $key => $tab)
		{
			if(!$tab['url_type']) 
			{	
				$trans = $this->transformToLink($tab['id_url'], true,  $id_lang, $id_shop);
				$tabs[$key]['url'] = $trans['href'];
			}
				
			if($tab['submenu_type']==1)
			{
				if (strlen(($tab['submenu_content'])))
				{
					$tab['submenu_content'] = explode(',',$tab['submenu_content']);

					 foreach ($tab['submenu_content'] as $tab_id) {
                        $innertab = new IqitMenuTab($tab_id, $id_lang, $id_shop);

                        if (!$innertab->verifyAccess()) {
                            unset($tab['submenu_content'][$tab_id]);
                            continue;
                        }

                        if (Tools::strlen(($innertab->submenu_content))) {
                            $innertab->submenu_content = $this->buildSubmenuTree(Tools::jsonDecode($innertab->submenu_content, true), true);
                        }

                        if (!$innertab->url_type) {
                            $trans = $this->transformToLink($innertab->id_url, true, $id_lang, $id_shop);
                            $innertab->url = $trans['href'];
                        }

                        $tabs[$key]['submenu_content_tabs'][$tab_id] = $innertab;
                    }

				}
			}
			if($tab['submenu_type']==2)
			{
      			if (strlen(($tab['submenu_content'])))
				{
				$tabs[$key]['submenu_content'] = $this->buildSubmenuTree(json_decode($tab['submenu_content'], true), true);
				}
			}
			
		}	

		return $tabs;
	}
 	
 	private function transformToLink($item, $simple, $id_lang = false, $id_shop = false)
 	{	
 		$id_shop = ($id_shop !== false) ? (int)$id_shop : (int)Context::getContext()->shop->id;
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

 		$return_link = array();

 		if (!$item)
 			return;

 			preg_match($this->pattern, $item, $value);
			$id = (int)substr($item, strlen($value[1]), strlen($item));

			switch (substr($item, 0, strlen($value[1])))
			{
				case 'CAT':
				if($simple)
				{
					$cat = new Category($id, $id_lang);
					$link = Tools::HtmlEntitiesUTF8($cat->getLink());

					$return_link['title'] = $cat->name;
					$return_link['href'] =  $link;
				}
					break;

				case 'PRD':
					$product = new Product((int)$id, true, (int)$id_lang);
					if (!is_null($product->id))
					{
						$return_link['title'] = Tools::HtmlEntitiesUTF8($product->getLink());
						$return_link['href'] =  $product->name;
					}
					break;

				 case 'CMS_CAT':
                    $category = new CMSCategory((int)$id, (int)$id_lang);
                  	if (!is_null($category->id)){
                  		$return_link['title'] = $category->name;
						$return_link['href'] =  Tools::HtmlEntitiesUTF8($category->getLink());
                    }
                    break;

				case 'CMS':
					$cms = CMS::getLinks((int)$id_lang, array($id));
					if (count($cms))
					{
						$return_link['title'] = Tools::safeOutput($cms[0]['meta_title']);
						$return_link['href'] =  Tools::HtmlEntitiesUTF8($cms[0]['link']);
					}
					break;

				// Case to handle the option to show all Manufacturers
				case 'ALLMAN':
					$link = new Link;
					$return_link['title'] = $this->l('All manufacturers');
					$return_link['href'] =  $link->getPageLink('manufacturer');
					break;

				case 'MAN':
					$manufacturer = new Manufacturer((int)$id, (int)$id_lang);
					if (!is_null($manufacturer->id))
					{
						if (intval(Configuration::get('PS_REWRITING_SETTINGS')))
							$manufacturer->link_rewrite = Tools::link_rewrite($manufacturer->name);
						else
							$manufacturer->link_rewrite = 0;
						$link = new Link;
						$return_link['title'] = Tools::safeOutput($manufacturer->name);
						$return_link['href'] =  Tools::HtmlEntitiesUTF8($link->getManufacturerLink((int)$id, $manufacturer->link_rewrite));
					}
					break;

				// Case to handle the option to show all Suppliers
				case 'ALLSUP':
					$link = new Link;
					$return_link['title'] = $this->l('All suppliers');
					$return_link['href'] =  $link->getPageLink('supplier');
					break;

				case 'HOME':
					$link = new Link;
					$return_link['title'] = $this->l('Home');
					$return_link['href'] =  $link->getPageLink('index');
					break;

				case 'SUP':
					
					$supplier = new Supplier((int)$id, (int)$id_lang);
					if (!is_null($supplier->id))
					{
						$link = new Link;
						$return_link['title'] = $supplier->name;
						$return_link['href'] =  Tools::HtmlEntitiesUTF8($link->getSupplierLink((int)$id, $supplier->link_rewrite));
					}
					break;

				case 'SHOP':
					
					$shop = new Shop((int)$id);
					if (Validate::isLoadedObject($shop))
					{
						$link = new Link;
						$return_link['title'] = $shop->name;
						$return_link['href'] =  Tools::HtmlEntitiesUTF8($shop->getBaseURL());
					}
					break;
				case 'LNK':
					$link = IqitMenuLinks::get((int)$id, (int)$id_lang, (int)$id_shop);
					if (count($link))
					{
						if (!isset($link[0]['label']) || ($link[0]['label'] == ''))
						{
							$default_language = Configuration::get('PS_LANG_DEFAULT');
							$link = IqitMenuLinks::get($link[0]['id_linksmenutop'], $default_language, (int)Shop::getContextShopID());
						}
						$return_link['title'] = Tools::safeOutput($link[0]['label']);
						$return_link['href'] =  Tools::HtmlEntitiesUTF8($link[0]['link']);
					}
					break;
			}
		return $return_link;
 	}

	private function makeMenuMobile()
	{
		$menu_items = $this->getMenuItems();
		$id_lang = (int)$this->context->language->id;
		$id_shop = (int)Shop::getContextShopID();
		$mobile_menu = '';
		$depth_limit = Configuration::get($this->config_name.'_mobile_menu_depth');
		foreach ($menu_items as $item)
		{
			if (!$item)
				continue;

			preg_match($this->pattern, $item, $value);
			$id = (int)substr($item, strlen($value[1]), strlen($item));

			switch (substr($item, 0, strlen($value[1])))
			{
				case 'CAT':
					$mobile_menu .= $this->generateCategoriesMenu(Category::getNestedCategories($id, $id_lang, true, $this->user_groups, true, '', $this->hor_sm_order), $depth_limit);
					break;

				case 'PRD':
					$product = new Product((int)$id, true, (int)$id_lang);
					if (!is_null($product->id))
						$mobile_menu .= '<li><a href="'.Tools::HtmlEntitiesUTF8($product->getLink()).'" title="'.$product->name.'">'.$product->name.'</a></li>'.PHP_EOL;
					break;

				 case 'CMS_CAT':
                    $category = new CMSCategory((int)$id, (int)$id_lang);
                    if (count($category)) {
                        $mobile_menu .= '<li><a href="'.Tools::HtmlEntitiesUTF8($category->getLink()).'" title="'.Tools::safeOutput($category->name).'">'.Tools::safeOutput($category->name).'</a>';
                        $mobile_menu .= $this->getCMSMenuItems($category->id);
                        $mobile_menu .='</li>'.PHP_EOL;
                    }
                    break;

				case 'CMS':
					$cms = CMS::getLinks((int)$id_lang, array($id));
					if (count($cms))
						$mobile_menu .= '<li><a href="'.Tools::HtmlEntitiesUTF8($cms[0]['link']).'" title="'.Tools::safeOutput($cms[0]['page_title']).'">'.Tools::safeOutput($cms[0]['page_title']).'</a></li>'.PHP_EOL;
					break;

				// Case to handle the option to show all Manufacturers
				case 'ALLMAN':
					$link = new Link;
					$mobile_menu .= '<li><a href="'.$link->getPageLink('manufacturer').'" title="'.$this->l('All manufacturers').'">'.$this->l('All manufacturers').'</a><ul>'.PHP_EOL;
					$manufacturers = Manufacturer::getManufacturers();
					foreach ($manufacturers as $key => $manufacturer)
						$mobile_menu .= '<li><a href="'.$link->getManufacturerLink((int)$manufacturer['id_manufacturer'], $manufacturer['link_rewrite']).'" title="'.Tools::safeOutput($manufacturer['name']).'">'.Tools::safeOutput($manufacturer['name']).'</a></li>'.PHP_EOL;
					$mobile_menu .= '</ul>';
					break;

				case 'MAN':
					$manufacturer = new Manufacturer((int)$id, (int)$id_lang);
					if (!is_null($manufacturer->id))
					{
						if (intval(Configuration::get('PS_REWRITING_SETTINGS')))
							$manufacturer->link_rewrite = Tools::link_rewrite($manufacturer->name);
						else
							$manufacturer->link_rewrite = 0;
						$link = new Link;
						$mobile_menu .= '<li><a href="'.Tools::HtmlEntitiesUTF8($link->getManufacturerLink((int)$id, $manufacturer->link_rewrite)).'" title="'.Tools::safeOutput($manufacturer->name).'">'.Tools::safeOutput($manufacturer->name).'</a></li>'.PHP_EOL;
					}
					break;

				// Case to handle the option to show all Suppliers
				case 'ALLSUP':
					$link = new Link;
					$mobile_menu .= '<li><a href="'.$link->getPageLink('supplier').'" title="'.$this->l('All suppliers').'">'.$this->l('All suppliers').'</a><ul>'.PHP_EOL;
					$suppliers = Supplier::getSuppliers();
					foreach ($suppliers as $key => $supplier)
						$mobile_menu .= '<li><a href="'.$link->getSupplierLink((int)$supplier['id_supplier'], $supplier['link_rewrite']).'" title="'.Tools::safeOutput($supplier['name']).'">'.Tools::safeOutput($supplier['name']).'</a></li>'.PHP_EOL;
					$mobile_menu .= '</ul>';
					break;

				case 'HOME':
					$link = new Link;
					$mobile_menu .= '<li><a href="'.$link->getPageLink('index').'" title="'.$this->l('Home').'">'.$this->l('Home').'</a></li>'.PHP_EOL;
					break;

				case 'SUP':
					
					$supplier = new Supplier((int)$id, (int)$id_lang);
					if (!is_null($supplier->id))
					{
						$link = new Link;
						$mobile_menu .= '<li><a href="'.Tools::HtmlEntitiesUTF8($link->getSupplierLink((int)$id, $supplier->link_rewrite)).'" title="'.$supplier->name.'">'.$supplier->name.'</a></li>'.PHP_EOL;
					}
					break;

				case 'SHOP':
					
					$shop = new Shop((int)$id);
					if (Validate::isLoadedObject($shop))
					{
						$link = new Link;
						$mobile_menu .= '<li><a href="'.Tools::HtmlEntitiesUTF8($shop->getBaseURL()).'" title="'.$shop->name.'">'.$shop->name.'</a></li>'.PHP_EOL;
					}
					break;
				case 'LNK':
					$link = IqitMenuLinks::get((int)$id, (int)$id_lang, (int)$id_shop);
					if (count($link))
					{
						if (!isset($link[0]['label']) || ($link[0]['label'] == ''))
						{
							$default_language = Configuration::get('PS_LANG_DEFAULT');
							$link = IqitMenuLinks::get($link[0]['id_linksmenutop'], $default_language, (int)Shop::getContextShopID());
						}
						$mobile_menu .= '<li><a href="'.Tools::HtmlEntitiesUTF8($link[0]['link']).'"'.(($link[0]['new_window']) ? ' onclick="return !window.open(this.href);"': '').' title="'.Tools::safeOutput($link[0]['label']).'">'.Tools::safeOutput($link[0]['label']).'</a></li>'.PHP_EOL;
					}
					break;
			}
		}

		return $mobile_menu;
	}

	private function generateCategoriesMenu($categories, $depth_limit, $depth = 0)
	{
		$html = '';

		foreach ($categories as $key => $category)
		{	

			if ($depth >= $depth_limit)
				return;

			if ($category['level_depth'] > 1)
			{
				$cat = new Category($category['id_category']);
				$link = Tools::HtmlEntitiesUTF8($cat->getLink());
			}
			else
				$link = $this->context->link->getPageLink('index');

			$html .= '<li>';
			$html .= '<a href="'.$link.'" title="'.$category['name'].'">'.$category['name'].'</a>';

			if ($depth + 1 < $depth_limit)
				{

			if (isset($category['children']) && !empty($category['children']))
			{
				$html .= '<ul>';
				$html .= $this->generateCategoriesMenu($category['children'], $depth_limit, $depth + 1);

				$html .= '</ul>';
			}
			}

			$html .= '</li>';
		}

		return $html;
	}

	private function generateCategoriesMenu2($categories, $subcats = false, $detph_limit, $current_depth, $subcat_limit, $subcat_count, $previus_depth = 0)
	{
		$return_categories = array();
		
		foreach ($categories as $key => $category)
		{

			if ($current_depth > $detph_limit)
				return;

			if ($subcat_count >= $subcat_limit)
				return $return_categories;
	
			
			if ($category['level_depth'] > 1)
			{
				$cat = new Category($category['id_category']);
				$link = Tools::HtmlEntitiesUTF8($cat->getLink());
			}
			else
				$link = $this->context->link->getPageLink('index');

			if($subcats)
			{
				$return_categories[$key]['title'] = $category['name'];
				$return_categories[$key]['href'] =  $link;
			}
			else{
				$return_categories['title'] = $category['name'];
				$return_categories['href'] =  $link;
			}
			

			if (isset($category['children']) && !empty($category['children']))
			{
				if($subcats)
					$return_categories[$key]['children'] = $this->generateCategoriesMenu2($category['children'], true, $detph_limit, $current_depth + 1, $subcat_limit, 0, $current_depth);
				else
					$return_categories['children'] = $this->generateCategoriesMenu2($category['children'], true, $detph_limit, $current_depth + 1, $subcat_limit, 0, $current_depth);

			}
			$subcat_count++;
			

			
		}

		return $return_categories;
	}

	public function convertBgRepeat($value) {

			switch($value) {
				case 3 :
					$repeat_option = 'repeat';
					break;
				case 2 :
					$repeat_option = 'repeat-x';
					break;
				case 1 :
					$repeat_option = 'repeat-y';
					break;
				default :
					$repeat_option = 'no-repeat';
			}
			return  $repeat_option;
	}

	public function convertBorderType($type) 
	{
			$border_type = 'none';

			switch($type) {
				case 5 :
					$border_type = 'groove';
					break;
				case 4 :
					$border_type = 'double';
					break;
				case 3 :
					$border_type = 'dotted';
					break;
				case 2 :
					$border_type = 'dashed';
					break;
				case 1 :
					$border_type = 'solid';
					break;
				default :
					$border_type = 'none';
			}

		return $border_type;
	}


	public function convertBorder($value, $position, $triangle = 0, $imp = '') 
	{
			$tmpborder =  explode(';', $value);
				
			$width = $tmpborder[0];
			$type = $tmpborder[1];
			$color = $tmpborder[2];



			switch($type) {
				case 5 :
					$border_type = 'groove';
					break;
				case 4 :
					$border_type = 'double';
					break;
				case 3 :
					$border_type = 'dotted';
					break;
				case 2 :
					$border_type = 'dashed';
					break;
				case 1 :
					$border_type = 'solid';
					break;
				default :
					$border_type= 'none';
			}

			$border_code = '';

			if(isset($color) && $color!='')
			{
				if($triangle == 1)
				{
					$border_code = 'left: '.-$width.'px; border-bottom: '.(12+$width).'px '.$border_type.' '.$color.'; border-left: '.(12+$width).'px '.$border_type.' transparent; border-right: '.(12+$width).'px '.$border_type.' transparent;';
				}
				elseif($triangle == 2)
				{
					$border_code = 'left: '.-(12+$width).'px; border-right: '.(12+$width).'px '.$border_type.' '.$color.'; border-bottom: '.(12+$width).'px '.$border_type.' transparent; border-left: '.(12+$width).'px '.$border_type.' transparent;';
				}
				else{
				if($position=='side')
				{ 
					$border_code = 'border-left: '.$width.'px '.$border_type.' '.$color.' '.$imp.';'.PHP_EOL;
					$border_code .= 'border-right: '.$width.'px '.$border_type.' '.$color.' '.$imp.';';
				}
				elseif($position=='all')
					$border_code = 'border: '.$width.'px '.$border_type.' '.$color.' '.$imp.';';
				else
					$border_code = 'border-'.$position.': '.$width.'px '.$border_type.' '.$color.' '.$imp.';';
			}
			}

			return  $border_code;
	}

	public function convertBgPosition($value) {

			switch($value) {
				case 8 :
					$position_option = 'left top';
					break;
				case 7 :
					$position_option = 'left center';
					break;
				case 6 :
					$position_option = 'left bottom';
					break;
				case 5 :
					$position_option = 'right top';
					break;
				case 4 :
					$position_option = 'right center';
					break;
				case 3 :
					$position_option = 'right bottom';
					break;
				case 2 :
					$position_option = 'center top';
					break;
				case 1 :
					$position_option = 'center center';
					break;
				default :
					$position_option = 'center bottom';
			}
			return  $position_option;
	}

	public function getProducts($ids)
	{
			$product_ids = join(',', $ids);

			if (Group::isFeatureActive())
				{
					$sql_groups_join = '
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = product_shop.id_category_default
						AND cp.id_product = product_shop.id_product)
					LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.`id_category` = cg.`id_category`)';
					$groups = FrontController::getCurrentCustomerGroups();
					$sql_groups_where = 'AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '='.(int)Group::getCurrent()->id);
				}

				$selected_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT DISTINCT p.id_product, pl.name, pl.link_rewrite, p.reference, i.id_image, product_shop.show_price,
						cl.link_rewrite category, p.ean13, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
						DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new
					FROM '._DB_PREFIX_.'product p
					'.Shop::addSqlAssociation('product', 'p').
					(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
					ON (p.`id_product` = pa.`id_product`)
					'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
					'.Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop) :  Product::sqlStock('p', 'product', false,
						$this->context->shop)).'
					LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl').')
					LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'
						.Shop::addSqlRestrictionOnLang('cl').')
					LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product)
					'.(Group::isFeatureActive() ? $sql_groups_join : '').'
					WHERE p.id_product IN ('.$product_ids.')
					AND pl.id_lang = '.(int)$this->context->language->id.'
					AND cl.id_lang = '.(int)$this->context->language->id.'
					AND i.cover = 1
					AND product_shop.active = 1
					'.(Group::isFeatureActive() ? $sql_groups_where : '').
					' ORDER BY FIELD(p.id_product, '.$product_ids.')'
					);



				$tax_calc = Product::getTaxCalculationMethod();
				$final_products_list = array();

				foreach ($selected_products as &$selected_product)
				{
					$usetax = false;
					$selected_product['id_product'] = (int)$selected_product['id_product'];
					$selected_product['image'] = $this->context->link->getImageLink($selected_product['link_rewrite'],
						(int)$selected_product['id_product'].'-'.(int)$selected_product['id_image'], ImageType::getFormatedName('home'));
					$selected_product['link'] = $this->context->link->getProductLink((int)$selected_product['id_product'], $selected_product['link_rewrite'],
						$selected_product['category'], $selected_product['ean13']);
					if (($tax_calc == 0 || $tax_calc == 2)){
						$usetax = true;
						$selected_product['displayed_price'] = Product::getPriceStatic((int)$selected_product['id_product'], true, null);
						$selected_product['price_tax_exc'] = Product::getPriceStatic((int)$selected_product['id_product'], false, null);
						}
					elseif ($tax_calc == 1){
						$selected_product['displayed_price'] = Product::getPriceStatic((int)$selected_product['id_product'], false, null);
						$selected_product['price_tax_exc'] = $selected_product['displayed_price'];
					}
					$selected_product['price_without_reduction'] = Product::getPriceStatic(
				(int)$selected_product['id_product'],
				$usetax,
				((isset($selected_product['id_product_attribute']) && !empty($selected_product['id_product_attribute'])) ? (int)$selected_product['id_product_attribute'] : null),
				6,
				null,
				false,
				false
			);

					$selected_product['reduction'] = Product::getPriceStatic(
			(int)$selected_product['id_product'],
			$usetax,
			((isset($selected_product['id_product_attribute']) && !empty($selected_product['id_product_attribute'])) ? (int)$selected_product['id_product_attribute'] : null),
			6,
			null,
			true,
			true,
			1,
			true,
			null,
			null,
			null,
			$specific_prices
		);
					$selected_product['allow_oosp'] = Product::isAvailableWhenOutOfStock((int)$selected_product['out_of_stock']);

					if (!isset($final_products_list[$selected_product['id_product'].'-'.$selected_product['id_image']]))
						$final_products_list[$selected_product['id_product'].'-'.$selected_product['id_image']] = $selected_product;
				}

				return $final_products_list;
	
	}


	public function hookActionObjectCategoryAddAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectCategoryUpdateAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectCategoryDeleteAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectCmsUpdateAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectCmsDeleteAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectCmsAddAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectSupplierUpdateAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectSupplierDeleteAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectSupplierAddAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectManufacturerUpdateAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectManufacturerDeleteAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectManufacturerAddAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectProductUpdateAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectProductDeleteAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectProductAddAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookCategoryUpdate($params)
	{
		$this->clearMenuCache();
	}

	public function clearMenuCache()
	{
		$this->_clearCache('iqitmegamenu.tpl');
		$this->_clearCache('iqitmegamenu_vertical.tpl');
		
	}

	private function installSamples()
	{
		$languages = Language::getLanguages(false);
        $group_access = array();
        $groups = Group::getGroups(Context::getContext()->language->id);

        foreach ($groups as $group) {
            $group_access[$group['id_group']] = true;
        }

        $group_access = serialize($group_access);


		
		$tab = new IqitMenuTab();
		$tab->menu_type = 1;
		$tab->position = IqitMenuTab::getNextPosition(1);

		$tab->active = 1;
		$tab->active_label = 1;
		$tab->url_type = 0;
		$tab->id_url = 'HOME0';
		$tab->icon_type = 1;
		$tab->icon_class = 'icon-home';
		$tab->bg_color = '#474747';

		$tab->new_window = 0;
		$tab->float = 0;

		$tab->submenu_type = 0;
		$tab->submenu_width = 12;
		 $tab->group_access = $group_access;
	
		foreach ($languages as $language)
			{
				$tab->title[$language['id_lang']] = 'Home';
			}
		
		$tab->add();

		$tab = new IqitMenuTab();
		$tab->menu_type = 1;
		$tab->position = IqitMenuTab::getNextPosition(1);

		$tab->active = 1;
		$tab->active_label = 0;
		$tab->url_type = 2;
		$tab->icon_type = 1;

		$tab->new_window = 0;
		$tab->float = 0;

		$tab->submenu_type = 0;
		$tab->submenu_width = 12;
		 $tab->group_access = $group_access;
	
		foreach ($languages as $language)
			{
				$tab->title[$language['id_lang']] = 'Sample tab';
			}
		
		$tab->add();

			
	}

	private function duplicateMultistoreTab($id_tab)
	{

	}

	private function duplicateTab($id_tab)
	{	
		$id_shop = (int) Context::getContext()->shop->id;
        $tab = new IqitMenuTab($id_tab);

        $newobject = $tab->duplicateObject();
	}

	public function ajaxProcessSearchProducts()
    {
    	header('Content-Type: application/json');

        $query = Tools::getValue('q', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $excludeVirtuals = false;
        $exclude_packs = false;

        $context = Context::getContext();

        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
        FROM `' . _DB_PREFIX_ . 'product` p
        ' . Shop::addSqlAssociation('product', 'p') . '
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'image` image
        ON (image.`id_product` = p.`id_product` AND image.cover=1)
        LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $context->language->id . ')
        WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\') AND p.`active` = 1' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
            ($excludeVirtuals ? 'AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);

        if ($items && ($excludeIds || strpos($_SERVER['HTTP_REFERER'], 'AdminScenes') !== false)) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . '|' . (int) ($item['id_product']) . "\n";
            }
        } elseif ($items) {
            // packs
            $results = array();
            foreach ($items as $item) {
                // check if product have combination

                $product = array(
                    'id' => (int) ($item['id_product']),
                    'name' => $item['name'],
                    'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                    'image' => str_replace('http://', Tools::getShopProtocol(), $context->link->getImageLink($item['link_rewrite'], $item['id_image'], ImageType::getFormatedName('medium'))),
                );
                array_push($results, $product);
            }
            $results = array_values($results);
            echo Tools::jsonEncode($results);
        } else {
            Tools::jsonEncode(new stdClass);
        }
    }

    public function ajaxProcessUpdateHorizontalTabsPosition()
    {
        $tabs = Tools::getValue('tabs');

        foreach ($tabs as $position => $id_tab) {
            $res = Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'iqitmegamenu_tabs` SET `position` = ' . (int) $position . '
            WHERE `id_tab` = ' . (int) $id_tab . ' AND menu_type = 1');

        }
        $this->clearMenuCache();
    }

    public function ajaxProcessupdateVerticalTabsPosition()
    {
        $tabs = Tools::getValue('tabs');

        foreach ($tabs as $position => $id_tab) {
            $res = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'iqitmegamenu_tabs` SET `position` = ' . (int) $position . '
            WHERE `id_tab` = ' . (int) $id_tab . ' AND menu_type = 2');

        }

        $this->clearMenuCache();
    }

    protected function getWarningMultishopHtml()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return '<p class="alert alert-warning">' .
            $this->l('You cannot manage module from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit') .
                '</p>';
        } else {
            return '';
        }

    }

}
