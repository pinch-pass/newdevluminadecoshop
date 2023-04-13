<?php
class AdminSendpulsePreExportController extends ModuleAdminController{
	public $template_path;
	public $actions_available = array('export', 'sended');
	
	private $categories = false;
	
	public function __construct(){
		$this->bootstrap = true;
		$this->name = 'sendpulse';
		$this->display = "list";
		$this->template_path = $this->getTemplatePath();
		
		parent::__construct();

		$this->meta_title = $this->l('Export to SendPulse');


		if(!$this->module->active){
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminDashboard'));
		}
	}   

	public function initToolBarTitle(){
		$this->toolbar_title[] = $this->l('Select categories for export to SendPulse');
	}
	

	public function initPageHeaderToolbar(){
		parent::initPageHeaderToolbar();
	}


	public function postProcess(){
		if(Tools::isSubmit("submitBulkExportCategories")){
			$this->js_files;
			$this->categories = Tools::getValue("CategoriesBox");
		}
		parent::postProcess();
	}


	public function displayExportLink($token = null, $id, $name = null){
		if(is_file($this->template_path."link.tpl")){
			$tpl = $this->context->smarty->createTemplate($this->template_path."link.tpl");		
		}else{
			return;
		}
		if(!array_key_exists('Export', self::$cache_lang)){
			self::$cache_lang['Export'] = $this->l('Export');
		}
		
		$href = AdminController::$currentIndex.'&'.$this->identifier.'='.$id.'&export&action=export&token='.($token != null ? $token : $this->token);
		$tpl->assign(array(
			'href' =>$href, // AdminController::$currentIndex.'&'.$this->identifier.'='.$id.'&update='.$this->table.'&token='.($token != null ? $token : $this->token),
			'action' => self::$cache_lang['Export'],
			'id' => $id,
		));
		return $tpl->fetch();
	}
	

	public function getTemplatePath(){
		return _PS_MODULE_DIR_.$this->name.'/views/templates/admin/';
	}


	public function getLocalCategories(){
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$group = new Group(); 
		$groups = $group->getGroups($default_lang);
		
		if(!empty($groups)){
			foreach($groups as $k=>$g){
				$temp = new Group($g['id_group']);
				$groups[$k]['count'] = $temp->getCustomers(true);
			}
			unset($temp);
		}	
		return $groups;
	}
    
		
	public function initContent(){
		parent::initContent();
        $this->context->smarty->assign(array(
            'categories'=>$this->getLocalCategories(),
            'modal_path'=>_PS_MODULE_DIR_.$this->name."/views/templates/admin/modal.tpl",
        ));
		$this->setTemplate("../../../../modules/{$this->name}/views/templates/admin/preexport.tpl");
	}
	
	public function setMedia(){
		parent::setMedia();
		$this->addJS("/modules/sendpulse/views/js/sendpulse.js");
	}
	
	
}

