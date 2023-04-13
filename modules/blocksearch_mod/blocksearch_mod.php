<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockSearch_mod extends Module
{
	public function __construct()
	{
		$this->name = 'blocksearch_mod';
		$this->tab = 'search_filter';
		$this->version = '1.8';
		$this->author = 'IQIT-COMMERCE.COM';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Header search and custom content module');
		$this->description = $this->l('Adds a block with a quick search field and custom html content.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{	
		$text = array($this->context->language->id => '<p style="text-align: center;"><em class="icon icon-plane"> </em>  International shipping     <em class="icon icon-plane">  </em>Secure payment</p>');
		Configuration::updateValue('iqitsearch_text', $text, true);

		Configuration::updateValue('iqitsearch_hook', 1);
		Configuration::updateValue('iqitsearch_shower', 0);

		Configuration::updateValue('iqitsearch_categories', 1);
		Configuration::updateValue('iqitsearch_depth', 3);
		

		$this->_clearCache('blocksearch-top_modm.tpl');
		$this->_clearCache('blocksearch-top_mod.tpl');
		$this->_clearCache('blocksearch-top_modn');
		$this->_clearCache('blocksearch-top_modn2');

		if((bool)Module::isEnabled('iqitmegamenu'))
            {
              		$iqitmegamenu = Module::getInstanceByName('iqitmegamenu');
                	$iqitmegamenu->clearMenuCache();
            }

		if (!parent::install() || !$this->registerHook('displayNav') || !$this->registerHook('iqitMobileSearch') || !$this->registerHook('header') || !$this->registerHook('displayAfterIqitMegamenu'))
			return false;
		return true;
	}

		public function uninstall() 
	{
	
		return (Configuration::deleteByName('iqitsearch_hook') AND Configuration::deleteByName('iqitsearch_shower') AND Configuration::deleteByName('iqitsearch_text') AND Configuration::deleteByName('iqitsearch_categories') AND Configuration::deleteByName('iqitsearch_depth') AND parent::uninstall());
	}
	
	public function hookHeader($params)
	{
		if (Configuration::get('PS_SEARCH_AJAX'))
			$this->context->controller->addJqueryPlugin('autocomplete');
		$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
		$this->context->controller->addCSS(($this->_path).'blocksearch_mod.css', 'all');
		if (Configuration::get('PS_SEARCH_AJAX') || Configuration::get('PS_INSTANT_SEARCH'))
		{
			Media::addJsDef(array('search_url' => $this->context->link->getPageLink('search', Tools::usingSecureMode())));
			$this->context->controller->addJS(($this->_path).'blocksearch_mod.js');
		}

		

		return $this->display(__FILE__, 'blocksearch_modh.tpl');
	}

	public function hookDisplayAfterIqitMegamenu($params)
	{
		if(!Configuration::get('iqitsearch_hook'))
		{


		if (Tools::getValue('search_query') || !$this->isCached('blocksearch-top_modm.tpl', $this->getCacheId()))
		{
			$this->calculHookCommon($params);
			$this->smarty->assign(array(
				'blocksearch_type' => 'top',
				'iqitsearch_shower' => Configuration::get('iqitsearch_shower'),
				'search_query' => (string)Tools::getValue('search_query')
				)
			);
		}
		return $this->display(__FILE__, 'blocksearch-top_modm.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
		}
	}

	public function hookIqitMobileSearch($params)
	{
		
		if (Tools::getValue('search_query') || !$this->isCached('blocksearch-mobile.tpl', $this->getCacheId()))
		{
			$this->calculHookCommon($params);
			$this->smarty->assign(array(
				'blocksearch_type' => 'top',
				'search_query' => (string)Tools::getValue('search_query')
				)
			);
		}
		return $this->display(__FILE__, 'blocksearch-mobile.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
	}

	public function hookdisplayNav($params)
	{
		Media::addJsDef(array('blocksearch_type' => 'top'));
		Media::addJsDef(array('PS_CATALOG_MODE' => (bool)Configuration::get('PS_CATALOG_MODE')));

		if (Configuration::get('iqitsearch_hook') && Configuration::get('iqitsearch_hook') == 1)
		{
			if (Tools::getValue('search_query') || !$this->isCached('blocksearch-top_mod.tpl', $this->getCacheId()))
			{
				$this->calculHookCommon($params);
				$this->smarty->assign(array(
					'blocksearch_type' => 'top',
					'iqitsearch_text' => Configuration::get('iqitsearch_text', $this->context->language->id),
					'iqitsearch_shower' => Configuration::get('iqitsearch_shower'),
					'search_query' => (string)Tools::getValue('search_query')
					)
				);
			}
		}
		else{

			if (Tools::getValue('search_query') || !$this->isCached('blocksearch-top_mod.tpl', $this->getCacheId()))
			{
				$this->smarty->assign(array(
					'iqitsearch_text' => Configuration::get('iqitsearch_text', $this->context->language->id),
					'blocksearch_empty' => true,
					)
				);
			}

		}
		return $this->display(__FILE__, 'blocksearch-top_mod.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
	}

	



	private function calculHookCommon($params)
	{	
		  	$categories_selector = Configuration::get('iqitsearch_categories');

		  	if($categories_selector)
		  	{
			$range = '';
			$maxdepth = (int)Configuration::get('iqitsearch_depth');
			$homecat = (int)Configuration::get('PS_HOME_CATEGORY');

			if($homecat == 0)
				$homecat = (int)Configuration::get('PS_ROOT_CATEGORY');


			$resultIds = array();
			$resultParents = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.id_parent, c.id_category, cl.name
			FROM `'._DB_PREFIX_.'category` c
			INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
			INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$this->context->shop->id.')
			WHERE (c.`active` = 1 OR c.`id_category` = '.(int)$homecat.')
			AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
			'.((int)$maxdepth != 0 ? ' AND `level_depth` <= '.(int)$maxdepth : '').'
			AND c.id_category IN (
				SELECT id_category
				FROM `'._DB_PREFIX_.'category_group`
				WHERE `id_group` IN ('.pSQL(implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id))).')
			)
			ORDER BY `level_depth` ASC, cs.`position` ASC');
			foreach ($result as &$row)
			{
				$resultParents[$row['id_parent']][] = &$row;
				$resultIds[$row['id_category']] = &$row;
			}
			$blockCategTree = $this->getTree($resultParents, $resultIds, $maxdepth, $homecat);
			$this->smarty->assign('blockCategTree', $blockCategTree);

			}
	


		$this->smarty->assign(array(
			'ENT_QUOTES' =>		ENT_QUOTES,
			'search_ssl' =>		Tools::usingSecureMode(),
			'ajaxsearch' =>		Configuration::get('PS_SEARCH_AJAX'),
			'instantsearch' =>	Configuration::get('PS_INSTANT_SEARCH'),
			'self' =>			dirname(__FILE__),
		));

		return true;
	}

	public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
	{
		if (is_null($id_category))
			$id_category = $this->context->shop->getCategory();
		$children = array();
		if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
		if (isset($resultIds[$id_category])){
			$name = $resultIds[$id_category]['name'];
		} 
		else
			$name = '';
		
		
		$return = array(
			'id' => $id_category,
			'name' => $name,
			'children' => $children,
			'currentDepth' => $currentDepth - 1
		);
		return $return;

	}

	public function getContent()
	{
		// If we try to update the settings
		$output = '';
		if (isset($_POST['submitModule']))
		{	
			Configuration::updateValue('iqitsearch_hook', Tools::getValue('iqitsearch_hook'));
			Configuration::updateValue('iqitsearch_categories', Tools::getValue('iqitsearch_categories'));
			Configuration::updateValue('iqitsearch_depth', Tools::getValue('iqitsearch_depth'));
			Configuration::updateValue('iqitsearch_shower', Tools::getValue('iqitsearch_shower'));


			$message_trads = array();
			foreach ($_POST as $key => $value)
				if (preg_match('/iqitsearch_text_/i', $key))
				{
					$id_lang = preg_split('/iqitsearch_text_/i', $key);
					$message_trads[(int)$id_lang[1]] = $value;
				}
				Configuration::updateValue('iqitsearch_text', $message_trads, true);
			
			$this->_clearCache('blocksearch-top_modm.tpl');
			$this->_clearCache('blocksearch-top_mod.tpl');

			if((bool)Module::isEnabled('iqitmegamenu'))
            {
              		$iqitmegamenu = Module::getInstanceByName('iqitmegamenu');
                	$iqitmegamenu->clearMenuCache();
            }

			$output .= $this->displayConfirmation($this->l('Configuration updated'));

		}

		$output .= $this->renderForm();

		return $output;
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Blocksearch module'),
					'icon' => 'icon-link'
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Hook for search input'),
						'name' => 'iqitsearch_hook',
						'options' => array(
							'query' => array(
							array(
								'id_option' => 1,
								'name' => $this->l('displayTop')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('Menu bar')
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Hide input and show search icon'),
						'desc' => $this->l('Input field will be hidden, it will be showed after click on search icon. Use that option only when: you have vartiant-2 selected for blockcart and blockuserinfo in thmeeeditor or you use menu bar as hook for search'),
						'name' => 'iqitsearch_shower',
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
						'label' => $this->l('Categories selector'),
						'name' => 'iqitsearch_categories',
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
						'label' => $this->l('Categories depth'),
						'name' => 'iqitsearch_depth',
						'options' => array(
							'query' => array(
							array(
								'id_option' => 2,
								'name' => 2
								),
							array(
								'id_option' => 3,
								'name' => 3
								),
							array(
								'id_option' => 4,
								'name' => 4
								),
								array(
								'id_option' => 5,
								'name' => 5
								)
							),                           
    						'id' => 'id_option',                          
    						'name' => 'name'
    						)
					),
					array(
							'type' => 'textarea',
							'lang' => true,
							'autoload_rte' => true,
							'label' => $this->l('Custom html content'),
							'name' => 'iqitsearch_text',
							'desc' => $this->l('Custom text information')	
							),
				),
				'submit' => array(
					'name' => 'submitModule',
					'title' => $this->l('Save')
				)
			),
		);
		
		if (Shop::isFeatureActive())
			$fields_form['form']['description'] = $this->l('The modifications will be applied to').' '.(Shop::getContext() == Shop::CONTEXT_SHOP ? $this->l('shop').' '.$this->context->shop->name : $this->l('all shops'));
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;		
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);
		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{

		$var =  array();

		$var['iqitsearch_hook'] = Tools::getValue('iqitsearch_hook', Configuration::get('iqitsearch_hook'));
		$var['iqitsearch_categories'] = Tools::getValue('iqitsearch_categories', Configuration::get('iqitsearch_categories'));
		$var['iqitsearch_depth'] = Tools::getValue('iqitsearch_depth', Configuration::get('iqitsearch_depth'));
		$var['iqitsearch_shower'] = Tools::getValue('iqitsearch_shower', Configuration::get('iqitsearch_shower'));


		foreach (Language::getLanguages(false) as $lang)
				$var['iqitsearch_text'][(int)$lang['id_lang']] = Tools::getValue('iqitsearch_text_'.(int)$lang['id_lang'], Configuration::get('iqitsearch_text', (int)$lang['id_lang']));

		return $var;

	}

}

