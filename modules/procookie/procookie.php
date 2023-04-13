<?php

if (!defined('_PS_VERSION_'))
	exit;

class ProCookie extends Module
{
	public function __construct()
	{
		$this->name = 'procookie';
		$this->tab = 'front_office_features';
		$this->version = '1.0.1';
		$this->author = 'Procoder';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Module EU Cookie Law (Notification Banner and Cookie Blocker).');
		$this->description = $this->l('Display Module EU Cookie Law (Notification Banner and Cookie Blocker).');
	}

	public function install()
	{
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		if (!parent::install() ||
			!$this->registerHook('displayHeader') ||
			!Configuration::updateValue('PS_OCC_COOKIE_TIMEOUT', 365) ||
			!Configuration::updateValue('PS_OCC_BAR_BG_COL', '#333333') ||
			!Configuration::updateValue('PS_OCC_BAR_CLOSE_BG_COL', '#cccccc') ||
			!Configuration::updateValue('PS_OCC_BAR_URL_BG_COL', '#cccccc') ||
			!Configuration::updateValue('PS_OCC_BAR_TEXT_COL', '#ffffff') ||
			!Configuration::updateValue('PS_OCC_BAR_CLOSE_TEXT_COL', '#333333') ||
			!Configuration::updateValue('PS_OCC_BAR_URL_TEXT_COL', '#333333') ||
			!Configuration::updateValue('PS_OCC_BAR_CLOSE_BG_HOVER_COL', '#cccccc') ||
			!Configuration::updateValue('PS_OCC_BAR_URL_BG_HOVER_COL', '#cccccc') ||
			!Configuration::updateValue('PS_OCC_BAR_CLOSE_TEXT_HOVER_COL', '#333333') ||
			!Configuration::updateValue('PS_OCC_BAR_URL_TEXT_HOVER_COL', '#333333') ||
			!Configuration::updateValue('PS_OCC_BAR_POSITION', 1) ||
			!Configuration::updateValue('PS_OCC_BAR_TEXT', 'Cookies ensure the proper operation of our website. By using it, you accept the use of cookies.') ||
            !Configuration::updateValue('PS_OCC_BAR_READ_MORE_TEXT', 'Read More') ||
            !Configuration::updateValue('PS_OCC_BAR_CLOSE_TEXT', 'Close') ||
			!Configuration::updateValue('PS_OCC_BAR_URL', '') ||
			!Configuration::updateValue('PS_OCC_BAR_OPACITY', '0.8') ||
			!Configuration::updateValue('PS_OCC_BAR_FONT_SIZE', '14px') ||
			!Configuration::updateValue('PS_OCC_BAR_AUTO_CLOSE_TIME', 0) ||
			!Configuration::updateValue('PS_OCC_TEST_MODE', false))
			return false;

		return true;
	}

	public function uninstall()
	{
		if (parent::uninstall() ||
			Configuration::deleteByName('PS_OCC_COOKIE_TIMEOUT') ||
			Configuration::deleteByName('PS_OCC_BAR_BG_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_CLOSE_BG_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_URL_BG_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_TEXT_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_CLOSE_TEXT_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_URL_TEXT_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_CLOSE_BG_HOVER_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_URL_BG_HOVER_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_CLOSE_TEXT_HOVER_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_URL_TEXT_HOVER_COL') ||
			Configuration::deleteByName('PS_OCC_BAR_POSITION') ||
			Configuration::deleteByName('PS_OCC_BAR_TEXT') ||
            Configuration::deleteByName('PS_OCC_BAR_READ_MORE_TEXT') ||
            Configuration::deleteByName('PS_OCC_BAR_CLOSE_TEXT') ||
			Configuration::deleteByName('PS_OCC_BAR_URL') ||
			Configuration::deleteByName('PS_OCC_BAR_OPACITY') ||
			Configuration::deleteByName('PS_OCC_BAR_FONT_SIZE') ||
			Configuration::deleteByName('PS_OCC_BAR_AUTO_CLOSE_TIME') ||
			Configuration::deleteByName('PS_OCC_TEST_MODE'))
			return true;

		return false;
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitModule'))
		{
			Configuration::updateValue('PS_OCC_COOKIE_TIMEOUT', (int)Tools::getValue('occ_cookie_timeout'));
			Configuration::updateValue('PS_OCC_BAR_BG_COL', Tools::getValue('occ_bar_background_colour'));
			Configuration::updateValue('PS_OCC_BAR_CLOSE_BG_COL', Tools::getValue('occ_bar_close_background_colour'));
			Configuration::updateValue('PS_OCC_BAR_CLOSE_BG_HOVER_COL', Tools::getValue('occ_bar_close_background_hover_colour'));
			Configuration::updateValue('PS_OCC_BAR_URL_BG_COL', Tools::getValue('occ_bar_url_background_colour'));
			Configuration::updateValue('PS_OCC_BAR_URL_BG_HOVER_COL', Tools::getValue('occ_bar_url_background_hover_colour'));
			Configuration::updateValue('PS_OCC_BAR_CLOSE_TEXT_COL', Tools::getValue('occ_bar_close_text_colour'));
			Configuration::updateValue('PS_OCC_BAR_CLOSE_TEXT_HOVER_COL', Tools::getValue('occ_bar_close_text_hover_colour'));
			Configuration::updateValue('PS_OCC_BAR_URL_TEXT_COL', Tools::getValue('occ_bar_url_text_colour'));
			Configuration::updateValue('PS_OCC_BAR_URL_TEXT_HOVER_COL', Tools::getValue('occ_bar_url_text_hover_colour'));
			Configuration::updateValue('PS_OCC_BAR_TEXT_COL', Tools::getValue('occ_bar_text_colour'));
			Configuration::updateValue('PS_OCC_BAR_POSITION', Tools::getValue('occ_bar_position'));
			Configuration::updateValue('PS_OCC_BAR_TEXT', Tools::getValue('occ_bar_text'));
			Configuration::updateValue('PS_OCC_BAR_READ_MORE_TEXT', Tools::getValue('occ_bar_read_more_text'));
			Configuration::updateValue('PS_OCC_BAR_CLOSE_TEXT', Tools::getValue('occ_bar_close_text'));
			Configuration::updateValue('PS_OCC_BAR_URL', Tools::getValue('occ_bar_url'));
			Configuration::updateValue('PS_OCC_BAR_OPACITY', Tools::getValue('occ_bar_opacity'));
			$auto_close = Tools::getValue('occ_bar_auto_close_time');
			if (empty($auto_close))
				$auto_close = 0;
			Configuration::updateValue('PS_OCC_BAR_AUTO_CLOSE_TIME', $auto_close);
			Configuration::updateValue('PS_OCC_BAR_FONT_SIZE', Tools::getValue('occ_bar_font_size'));
			Configuration::updateValue('PS_OCC_TEST_MODE', Tools::getValue('occ_test_mode'));
			$this->buildCss();
		}

		return $this->renderConfigurationForm();
	}

	public function renderConfigurationForm()
	{
		$inputs = array(
			array(
				'type' => 'radio',
				'label' => $this->l('Test Mode'),
				'desc' => $this->l('Enable test mode while you setup your module. Test mode forces the display of the banner on every page load.'),
				'name' => 'occ_test_mode',
				'required' => true,
				'class' => 't',
				'is_bool' => true,
				'values' => array(
					array(
						'id' => 'test_mode_true',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id'    => 'test_mode_fall',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				),
			),
			array(
				'type' => 'text',
				'label' => $this->l('Cookie Lifespan Timeout'),
				'name' => 'occ_cookie_timeout',
				'desc' => $this->l('How long it takes before the user will see the cookie consent banner again. (Default 365 days)')
			),
			array(
                'type' => 'textarea',
                'label' => $this->l('Consent Bar Text'),
                'name' => 'occ_bar_text'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Consent Bar Read More Button Text'),
                'name' => 'occ_bar_read_more_text'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Consent Bar Close Button Text'),
                'name' => 'occ_bar_close_text'
            ),
			array(
				'type' => 'text',
				'label' => $this->l('Consent Bar Read More Button Url'),
				'name' => 'occ_bar_url',
				'desc' => $this->l('Optional: This is used if you wish to provide a read more link within the consent bar.')
			),
			array(
				'type' => 'text',
				'label' => $this->l('Consent Bar Font Size'),
				'name' => 'occ_bar_font_size',
			),
			array(
				'type' => 'text',
				'label' => $this->l('Consent Bar Opacity'),
				'name' => 'occ_bar_opacity',
			),
			array(
				'type' => 'text',
				'label' => $this->l('Consent Bar Close After X seconds'),
				'name' => 'occ_bar_auto_close_time',
				'desc' => $this->l('if you wish the consent bar to close after a number of seconds set this value.')
			),
			array(
				'type' => 'radio',
				'label' => $this->l('Consent Bar Position'),
				'desc' => $this->l('Position the consent bar at either the top or bottom of your store.'),
				'name' => 'occ_bar_position',
				'required' => true,
				'class' => 't',
				'is_bool' => true,
				'values' => array(
					array(
						'id' => 'position_top',
						'value' => 1,
						'label' => $this->l('Top')
					),
					array(
						'id'    => 'position_bottom',
						'value' => 0,
						'label' => $this->l('Bottom')
					)
				),
			),
			array(
				'type' => 'color',
				'label' => $this->l('Consent Bar Background Colour'),
				'name' => 'occ_bar_background_colour',
			),
			array(
				'type' => 'color',
				'label' => $this->l('Consent Bar Text Colour'),
				'name' => 'occ_bar_text_colour',
			),
			array(
				'type' => 'color',
				'label' => $this->l('Close Button Background Colour'),
				'name' => 'occ_bar_close_background_colour',
			),
			array(
				'type' => 'color',
				'label' => $this->l('Close Button Background Hover Colour'),
				'name' => 'occ_bar_close_background_hover_colour',
			),
			array(
				'type' => 'color',
				'label' => $this->l('Close Button Text Colour'),
				'name' => 'occ_bar_close_text_colour',
			),
			array(
				'type' => 'color',
				'label' => $this->l('Close Button Text Hover Colour'),
				'name' => 'occ_bar_close_text_hover_colour',
			),
			array(
				'type' => 'color',
				'label' => $this->l('Read More Button Background Colour'),
				'name' => 'occ_bar_url_background_colour',
			),
			array(
				'type' => 'color',
				'label' => $this->l('Read More Button Background Hover Colour'),
				'name' => 'occ_bar_url_background_hover_colour',
			),
			array(
				'type' => 'color',
				'label' => $this->l('Read More Button Text Colour'),
				'name' => 'occ_bar_url_text_colour',
			),
			array(
				'type' => 'color',
				'label' => $this->l('Read More Button Text Hover Colour'),
				'name' => 'occ_bar_url_text_hover_colour',
			),
		);

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => $inputs,
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name
			.'&tab_module='.$this->tab
			.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'occ_cookie_timeout' => Tools::getValue('occ_cookie_timeout', Configuration::get('PS_OCC_COOKIE_TIMEOUT')),
			'occ_bar_position' => Tools::getValue('occ_bar_position', Configuration::get('PS_OCC_BAR_POSITION')),
			'occ_bar_background_colour' => Tools::getValue('occ_bar_background_colour', Configuration::get('PS_OCC_BAR_BG_COL')),
			'occ_bar_text_colour' => Tools::getValue('occ_bar_text_colour', Configuration::get('PS_OCC_BAR_TEXT_COL')),
			'occ_bar_url' => Tools::getValue('occ_bar_url', Configuration::get('PS_OCC_BAR_URL')),
			'occ_bar_text' => Tools::getValue('occ_bar_text', Configuration::get('PS_OCC_BAR_TEXT')),
			'occ_bar_read_more_text' => Tools::getValue('occ_bar_read_more_text', Configuration::get('PS_OCC_BAR_READ_MORE_TEXT')),
			'occ_bar_close_text' => Tools::getValue('occ_bar_close_text', Configuration::get('PS_OCC_BAR_CLOSE_TEXT')),
			'occ_bar_close_background_colour' => Tools::getValue('occ_bar_close_background_colour', Configuration::get('PS_OCC_BAR_CLOSE_BG_COL')),
			'occ_bar_url_background_colour' => Tools::getValue('occ_bar_url_background_colour', Configuration::get('PS_OCC_BAR_URL_BG_COL')),
			'occ_bar_close_text_colour' => Tools::getValue('occ_bar_close_text_colour', Configuration::get('PS_OCC_BAR_CLOSE_TEXT_COL')),
			'occ_bar_url_text_colour' => Tools::getValue('occ_bar_url_text_colour', Configuration::get('PS_OCC_BAR_URL_TEXT_COL')),
			'occ_bar_close_background_hover_colour' => Tools::getValue('occ_bar_close_background_hover_colour',
				Configuration::get('PS_OCC_BAR_CLOSE_BG_HOVER_COL')),
			'occ_bar_url_background_hover_colour' => Tools::getValue('occ_bar_url_background_hover_colour',
				Configuration::get('PS_OCC_BAR_URL_BG_HOVER_COL')),
			'occ_bar_close_text_hover_colour' => Tools::getValue('occ_bar_close_text_hover_colour', Configuration::get('PS_OCC_BAR_CLOSE_TEXT_HOVER_COL')),
			'occ_bar_url_text_hover_colour' => Tools::getValue('occ_bar_url_text_hover_colour', Configuration::get('PS_OCC_BAR_URL_TEXT_HOVER_COL')),
			'occ_bar_opacity' => Tools::getValue('occ_bar_opacity', Configuration::get('PS_OCC_BAR_OPACITY')),
			'occ_bar_auto_close_time' => Tools::getValue('occ_bar_auto_close_time', Configuration::get('PS_OCC_BAR_AUTO_CLOSE_TIME')),
			'occ_bar_font_size' => Tools::getValue('occ_bar_font_size', Configuration::get('PS_OCC_BAR_FONT_SIZE')),
			'occ_test_mode' => Tools::getValue('occ_test_mode', Configuration::get('PS_OCC_TEST_MODE'))
		);
	}
	
	public function hex2rgb($hex)
	{
		$hex = str_replace('#', '', $hex);
		if (Tools::strlen($hex) == 3)
		{
			$r = hexdec(Tools::substr($hex, 0, 1).Tools::substr($hex, 0, 1));
			$g = hexdec(Tools::substr($hex, 1, 1).Tools::substr($hex, 1, 1));
			$b = hexdec(Tools::substr($hex, 2, 1).Tools::substr($hex, 2, 1));
		}
		else
		{
			$r = hexdec(Tools::substr($hex, 0, 2));
			$g = hexdec(Tools::substr($hex, 2, 2));
			$b = hexdec(Tools::substr($hex, 4, 2));
		}
		$rgb = array($r, $g, $b);
		return implode(',', $rgb);
	}
	
	public function buildCss()
	{
		$position = Configuration::get('PS_OCC_BAR_POSITION') == 1 ? 'top: 0' : 'bottom: 0';
		$css = '
			.procookie {
				position: fixed;
				'.$position.';
				background: '.Configuration::get('PS_OCC_BAR_BG_COL').';
				color: '.Configuration::get('PS_OCC_BAR_TEXT_COL').';
				font-size: '.Configuration::get('PS_OCC_BAR_FONT_SIZE').';
				background-color: rgba('.$this->hex2rgb(Configuration::get('PS_OCC_BAR_BG_COL')).','.Configuration::get('PS_OCC_BAR_OPACITY').');
			}

			.procookie-close {
				background: '.Configuration::get('PS_OCC_BAR_CLOSE_BG_COL').';
				color: '.Configuration::get('PS_OCC_BAR_CLOSE_TEXT_COL').';
			}

			.procookie-close:hover {
				background: '.Configuration::get('PS_OCC_BAR_CLOSE_BG_HOVER_COL').';
				color: '.Configuration::get('PS_OCC_BAR_CLOSE_TEXT_HOVER_COL').';
			}

			.procookie-more {
				background: '.Configuration::get('PS_OCC_BAR_URL_BG_COL').';
				color: '.Configuration::get('PS_OCC_BAR_URL_TEXT_COL').';
			}

			.procookie-more:hover {
				background: '.Configuration::get('PS_OCC_BAR_URL_BG_HOVER_COL').';
				color: '.Configuration::get('PS_OCC_BAR_URL_TEXT_HOVER_COL').';
			}
		';
		return $css;
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCss(_MODULE_DIR_.$this->name.'/css/procookie.css');
		$this->context->controller->addJqueryPlugin('cooki-plugin');
//		$this->context->controller->addJqueryUI('ui.effect');
//		$this->context->controller->addJqueryUI('ui.effect-slide');
		$this->context->smarty->assign(array(
			'occ_timeout' => Configuration::get('PS_OCC_COOKIE_TIMEOUT'),
			'occ_test_mode' => Configuration::get('PS_OCC_TEST_MODE'),
			'occ_bar_url' => Configuration::get('PS_OCC_BAR_URL'),
			'occ_bar_text' => Configuration::get('PS_OCC_BAR_TEXT'),
            		'occ_bar_read_more_text' => Configuration::get('PS_OCC_BAR_READ_MORE_TEXT'),
		        'occ_bar_close_text' => Configuration::get('PS_OCC_BAR_CLOSE_TEXT'),
			'occ_bar_position' => Configuration::get('PS_OCC_BAR_POSITION'),
			'occ_bar_auto_close_time' => Configuration::get('PS_OCC_BAR_AUTO_CLOSE_TIME'),
			'occ_css' => $this->buildCss()
		));
		return $this->display(_PS_MODULE_DIR_.$this->name, 'views/templates/hook/procookie.tpl');
	}
}
