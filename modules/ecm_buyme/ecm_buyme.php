<?php

if(!defined('_CAN_LOAD_FILES_'))
exit;
//private $_html = '';
class ecm_buyme extends Module
{
	function __construct()
	{
		$this->name = 'ecm_buyme';
		$this->tab = 'checkout';
		$this->author = 'Elcommerce';
		$this->version = 0.6;

		parent::__construct();

		$this->displayName = $this->l("Кнопка быстрого заказа");
		$this->description = $this->l("модуль для установки скрипта Buyme");
	}

	function install()
	{
		return (parent::install() AND $this->registerHook('displayProductButtons'));
	}





	private
	function _settings()
	{
		$this->_html .= '
		<fieldset class="space">
		<legend><img src="../img/admin/cog.gif" alt="" class="middle" />Настройки</legend>
		<label>Email</label>
		<div class="margin-form">
		<input type="text" name="to" placeholder="email..." required value="'.Tools::getValue('to',Configuration::get('_TO_')).'"/>
		<p class="clear">адрес почты для отправки уведомления, несколько ящиков могут перечисляться через запятую</p>
		</div>

		<label>Передавать артикул в форму заказа</label>
		<div class="margin-form">
		<input type="checkbox" name="reference" value="1" ' . (Tools::getValue('reference',Configuration::get('_REF_'))? 'checked="checked" ' : '' ) . ' />
		<p class="clear"> Выберите "да" для включения.</p>
		</div>

		<label>Не показывать кнопку, если товара нет в наличии</label>
		<div class="margin-form">
		<input type="checkbox" name="zero" value="1" ' . (Tools::getValue('zero',Configuration::get('_ZERO_'))? 'checked="checked" ' : '' ) . ' />
		<p class="clear"> Выберите "да" для включения режима.</p>
		</div>

		<center><input type="submit" name="submitSETTING" value="Обновить" class="button" /></center>
		</fieldset>
		';
	}

	function hookdisplayProductButtons($params)
	{
		global $smarty;
		global $cookie;
		$zero = Configuration::get('_ZERO_');
		$product = new Product($_GET['id_product'], false, intval($cookie->id_lang));
		$art = $this->l("Код товара: ");
		if(Validate::isLoadedObject($product))      
			$smarty->assign('product_b1c', array($product->name,(Configuration::get('_REF_')?" ".$art.$product->reference:'')));
		if ($zero == 1)	$quantity = StockAvailable::getQuantityAvailableByProduct($_GET['id_product'], $id_product_attribute = null, $id_shop = null);
		else $quantity = 5;

		if(Validate::isLoadedObject($product)) $smarty->assign('product_available', $quantity);
		
		//$this->context->controller->addJS($this->_path.'js/buyme.js');

		return $this->display(__FILE__, 'buyme.tpl');
	}
	private
	function _displayabout()
	{

		$this->_html .= '
		<fieldset class="space">
		<legend><img src="../img/admin/email.gif" /> ' . $this->l('Информация') . '</legend>
		<div id="dev_div">
		<span><b>' . $this->l('Версия') . ':</b> ' . $this->version . '</span><br>
		<span><b>' . $this->l('Разработчик') . ':</b> <a class="link" href="mailto:A_Dovbenko@mail.ru" target="_blank">Savvato</a>

		<span><b>' . $this->l('Описание') . ':</b> <a class="link" href="http://elcommerce.com.ua" target="_blank">http://elcommerce.com.ua</a><br><br>
		<p style="text-align:center"><a href="http://elcommerce.com.ua/"><img src="http://elcommerce.com.ua/img/m/logo.png" alt="Электронный учет коммерческой деятельности" /></a>


		</div>
		</fieldset>
		';
	}
	function getContent()
	{
		$this->_html = '';
		if(Tools::isSubmit('submitSETTING') )
		{


			if($to = Tools::getValue('to')){
				Configuration::updateValue('_TO_', $to);
			}

			$ref = ((isset($_POST['reference'])) && ($_POST['reference'] == '1'))? 1 : 0;
			Configuration::updateValue('_REF_', $ref);
			$zero = ((isset($_POST['zero'])) && ($_POST['zero'] == '1'))? 1 : 0;
			Configuration::updateValue('_ZERO_', $zero);
			$this->_html .= '
			<div class="bootstrap">
			<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">×</button>
			Настройки успешно обновлены
			</div>
			</div>
			';
		}
		$this->_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		$this->_settings();
		$this->_displayabout();
		$this->_html .= '</form>';

		return $this->_html;
	}
}

?>
