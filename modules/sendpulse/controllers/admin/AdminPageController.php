<?php
class AdminPageController extends ModuleAdminController{

	public function __construct(){
		echo Tools::getValue('id_product');
	}   


	public function initContent(){
		parent::initContent();
		$smarty = $this->context->smarty;
		$smarty->assign('test', 'test1');
	}
}

