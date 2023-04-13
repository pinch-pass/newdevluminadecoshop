<?php

class AdminSendpulsePreImportController extends ModuleAdminController{
	public function __construct(){
		$this->bootstrap = true;
		$this->name = 'sendpulse';
		$this->display = "list";
		$this->template_path = $this->getTemplatePath();
		parent::__construct();

		$this->meta_title = $this->l('Import from SendPulse');


		if(!$this->module->active){
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminDashboard'));
		}

	}

	public function getSendpulseCategories(){
		return false;
	}
	

	public function getTemplatePath(){
		return _PS_MODULE_DIR_.$this->name.'/views/templates/admin/';
	}
	
	public function initToolBarTitle(){
		$this->toolbar_title[] = $this->l('Select categories for import from SendPulse');
	}	

	public function initPageHeaderToolbar(){
		parent::initPageHeaderToolbar();
	}
	

	public function initContent(){
		parent::initContent();
        $this->context->smarty->assign(array(
            'categories'=>$this->getSendpulseCategories(),
            'modal_path'=>_PS_MODULE_DIR_.$this->name."/views/templates/admin/modal.tpl",
        ));
		$this->setTemplate("../../../../modules/{$this->name}/views/templates/admin/preimport.tpl");
	}
	
	public function setMedia(){
		parent::setMedia();
		$this->addJS("/modules/sendpulse/views/js/sendpulse.js");
	}
	
	
}

