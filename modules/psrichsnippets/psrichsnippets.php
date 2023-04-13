<?php

if (!defined('_PS_VERSION_'))
	exit;

class psRichSnippets extends Module
{
	/* @var boolean error */
	protected $_errors = false;
	
	public function __construct()
	{
		$this->name = 'psrichsnippets';
		$this->tab = 'front_office_features';
		$this->version = '1.0.1';
		$this->author = 'Nemo';
		$this->need_instance = 0;

	 	parent::__construct();

		$this->displayName = $this->l('PS Rich Snippets and Breadcrumbs');
		$this->description = $this->l('Adds google rich snippets & breadcrumbs to your store.');
	}
	
	public function install()
	{
		if (!parent::install() OR
			!$this->registerHook('displayProductTab') OR
			!$this->registerHook('displayProductTabContent') OR
			!Configuration::deleteByName('PSRICHSNIPPETS_BREADONLY'))
			return false;
		return true;
	}
	
	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return true;
	}



	public function getContent(){
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		$this->_postProcess();


		$this->_displayForm();
		return	$this->_html;
	}
	


	private function _postProcess()
	{
		if (Tools::isSubmit('saveSettings')) // handles the basic config update
		{

			Configuration::updateValue('PSRICHSNIPPETS_BREADONLY', Tools::getValue('breadcrumbs_only'));



			if($this->_errors)
				$this->_html .= $this->displayError(implode($this->_errors, '<br />'));
			else $this->_html .= $this->displayConfirmation($this->l('Settings Updated'));
		}
	}


	private function _displayForm()
	{

		$this->_html .= '<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, \'script\', \'facebook-jssdk\'));</script>';
		$this->_html .= '<p>'.$this->l('Provided for FREE by').' <a style="text-decoration:underline" target="_blank" href="http://store.nemops.com#fromrichsnippets" title="store.nemops.com!">store.nemops.com!</a> '.$this->l('Would you like to support Nemo\'s Post Scriptum to get more free modules?').'</p>';



		$this->_html .= '<div class="fb-like-box" style="float:left" data-href="https://www.facebook.com/pages/Nemos-Post-Scriptum/358370864236645" data-width="250" data-colorscheme="light" data-show-faces="false" data-header="false" data-stream="false" data-show-border="false"></div>';

		$this->_html .= '
			<div  style="float:left">

			<form style="text-align:right" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="GSG68TUKQC24J">
			<input type="image" src="https://www.paypalobjects.com/en_US/IT/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			</div>';		


		$this->_html .= '<div class="clear">&nbsp;</div>';

		$this->_html .= '
			<form action="'.AdminController::$currentIndex.'&token='.Tools::getValue('token').'&configure='.$this->name.'" method="post" enctype="multipart/form-data">
				<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Module Configuration').'</legend>
		';

		// remove site name from title?
		$this->_html .= '

		<label>'.$this->l('Enable rich snippets breadcrumbs only').'</label>
		<div class="margin-form">
			<input type="checkbox" name="breadcrumbs_only" value="1" ' . ( Configuration::get('PSRICHSNIPPETS_BREADONLY') == 1 ? 'checked="checked"' : '' ) . '>
			<p class="hint">'.$this->l('If your theme already has rich snippets (like the default Prestashop 1.6 one) you should tick this box so that the module doesn\'t interfere with it.').'</p>
		</div>
		';

		/* Submit button */
		$this->_html .='<p class="center"><input type="submit" name="saveSettings" value="'.$this->l('Save').'" class="button"></p>';


		$this->_html .= '
				</fieldset>
			</form>';		
	}	

	public static function getProductPathForCrumbs($id_category, $path = '', $link_on_the_item = false, $category_type = 'products', Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$path = '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title">'.$path.'</span></span>';

		$id_category = (int)$id_category;
		if ($id_category == 1)
			return '<span class="navigation_end">'.$path.'</span>';

		$pipe = Configuration::get('PS_NAVIGATION_PIPE');
		if (empty($pipe))
			$pipe = '>';

		$full_path = '';
		if ($category_type === 'products')
		{
			$interval = Category::getInterval($id_category);
			$id_root_category = $context->shop->getCategory();
			$interval_root = Category::getInterval($id_root_category);
			if ($interval)
			{
				$sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
						FROM '._DB_PREFIX_.'category c
						LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category'.Shop::addSqlRestrictionOnLang('cl').')
						WHERE c.nleft <= '.$interval['nleft'].'
							AND c.nright >= '.$interval['nright'].'
							AND c.nleft >= '.$interval_root['nleft'].'
							AND c.nright <= '.$interval_root['nright'].'
							AND cl.id_lang = '.(int)$context->language->id.'
							AND c.active = 1
							AND c.level_depth > '.(int)$interval_root['level_depth'].'
						ORDER BY c.level_depth ASC';
				$categories = Db::getInstance()->executeS($sql);

				$n = 1;
				$n_categories = count($categories);
				foreach ($categories as $category)
				{
					$full_path .=
					(($n < $n_categories || $link_on_the_item) ? '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb"> <a itemprop="url" href="'.Tools::safeOutput($context->link->getCategoryLink((int)$category['id_category'], $category['link_rewrite'])).'" title="'.htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8').'"><span itemprop="title">' : '').
					htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8').
					(($n < $n_categories || $link_on_the_item) ? '</span></a> </span>' : '').
					(($n++ != $n_categories || !empty($path)) ? ' <span class="navigation-pipe"> '.$pipe.' </span> ' : '');
				}

				return $full_path.$path;
			}
		}
	}


	public function hookDisplayProductTab($params)
	{

		
		// Path for breadcrumbs
		if(!isset($params['product']))
		{
			if(!$id_product = Tools::getValue('id_product'))
				return $this->l('Missing product object. Set new object mode from the back office Configuration');

			$product = new Product($id_product, false, $this->context->language->id);
			$params['product'] = $product;
		}
		
		$this->context->smarty->assign('product', $params['product'] );

		

		$path = $this->getProductPathForCrumbs((int)$params['product']->id_category_default, $params['product']->name, true);
		if($path)
			$this->context->smarty->assign('psrichsnippets_path', $path);

		$this->context->smarty->assign(array(
			'ps_dimension_unit' => Configuration::get('PS_DIMENSION_UNIT'),
			'ps_weight_unit' => Configuration::get('PS_WEIGHT_UNIT'),
			'currencySign' => $this->context->currency->sign,
			'currencyRate' => $this->context->currency->conversion_rate,
			'currencyFormat' => $this->context->currency->format,
			'currencyBlank' => $this->context->currency->blank,
			'breadonly' =>  Configuration::get('PSRICHSNIPPETS_BREADONLY')
			));
		if (version_compare(constant('_PS_VERSION_'),'1.6','>'))
			return;
		else
			return $this->display(__FILE__, 'tab15.tpl');
		
	}
	
	public function hookDisplayProductTabContent($params)
	{

		if (version_compare(constant('_PS_VERSION_'),'1.6','>'))
			return $this->display(__FILE__, 'tabContent.tpl');
		else
			return $this->display(__FILE__, 'tabContent15.tpl');		
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'css/psrichsnippets.css', 'all');
		$this->context->controller->addJS($this->_path.'js/psrichsnippets.js', 'all');

		
	}

	
}
