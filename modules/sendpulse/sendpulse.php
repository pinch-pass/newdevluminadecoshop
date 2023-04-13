<?php   
if(!defined('_PS_VERSION_')){
	exit;
}

/*
	CustomerCore::getByEmail, customerExists
	GroupCore::getGroups, getCustomers, searchByName

*/

class Sendpulse extends Module{
	public function __construct(){
		$this->name = 'sendpulse';
		$this->version = '1.0.0';
		$this->author = 'SendPulse';
		$this->need_instance = 1;
		$this->ps_versions_compliancy = array('min'=>'1.6.0', 'max'=>_PS_VERSION_);
		$this->bootstrap = true;
		$this->tab = 'emailing';
		

		parent::__construct();

		$this->displayName = $this->l('SendPulse');
		$this->description = $this->l('Import/export to/from SendPulse.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		
	}

	public function getAPI($id=false, $key=false, $type){
		require_once(dirname(__FILE__).'/classes/sendpulseInterface.php');
		require_once(dirname(__FILE__).'/classes/sendpulse.php');	
		if(!$id || !$key){
			$api = new SendpulseApi(Configuration::get('sendpulse_id'), Configuration::get('sendpulse_secret_key'), "session", false, "return");
		}else{
			$api = new SendpulseApi($id, $key, "session", false, "return");
		}
		return $api;
	}


    public function hookDisplayBackOfficeHeader(){
    	
	}

	public function hookDisplayLeftColumn($params){
		$this->context->smarty->assign(
			array(
				'my_module_name' => Configuration::get('MYMODULE_NAME'),
				'my_module_link' => $this->context->link->getModuleLink('mymodule', 'display')
			)
		);
		return $this->display(__FILE__, 'mymodule.tpl');
	}


	public function hookDisplayHeader(){
	}

	public function hookExportToSendpulse($params){

	}


	public function getContent(){
		$output = null;
		if (Tools::isSubmit('submit'.$this->name)){
			$sendpulse_id = strval(Tools::getValue('sendpulse_id'));
			$sendpulse_secret_key = strval(Tools::getValue('sendpulse_secret_key'));
			$sendpulse_only_subscribers = strval(Tools::getValue('sendpulse_only_subscribers'));			
			 
			$api = $this->getAPI($sendpulse_id, $sendpulse_secret_key, "return");
			
			if(!$sendpulse_id || empty($sendpulse_id) || !Validate::isString($sendpulse_id) 
				|| !$sendpulse_secret_key || empty($sendpulse_secret_key) || !Validate::isString($sendpulse_secret_key) || ($api->error)){
				
				$output .= $this->displayError($this->l('Invalid ID or Secret Key'));
			}else{
				Configuration::updateValue('sendpulse_id', $sendpulse_id);
				Configuration::updateValue('sendpulse_secret_key', $sendpulse_secret_key);
				Configuration::updateValue('sendpulse_only_subscribers', $sendpulse_only_subscribers);
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->displayForm();
	}
	
	public function addGroup($name=""){
		if(!Validate::isString($name)){
			throw new PrestaShopException($this->l("cannot insert customer category \"$name\""));
		}
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$group = new Group(); //getGroups();
		$cg = $group->searchByName($name);
		if($cg and isset($cg['id_group'])){
			return $cg['id_group'];
		}
		
		$group->price_display_method = "0";
		$group->name = array($default_lang=>$name);
		if($group->add(true, true)){
			return $group->id;
		}
		
		throw new PrestaShopException($this->l("Cannot insert customer category \"$name\""));
		//searchByName
	}
	
	//user data, goups ids
	public function addUser($user=array(), $groups=array()){ //remove
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		if(!Validate::isString($user['name'])){
			//throw new PrestaShopException($this->l("cannot insert customer category \"$name\""));
		}
		$customer = new Customer();
		$cc = $customer->searchByName($user['name']);
		if($cc and isset($cc['id_customer'])){
			return $cc['id_customer'];
		}	
		
		$customer->firstname = $user['name'];
		$customer->lastname = $user['name'];
		$customer->email = $user['email'];
		$customer->passwd = $user['email'];
		$customer->add();
		$customer->updateGroup($groups);
		
		exit("time to add");
	}
	
	
	//форма настроек
	public function displayForm(){
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('SendPulse Settings'),
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('SendPulse ID'),
					'name' => 'sendpulse_id',
					'size' => 20,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Secret key'),
					'name' => 'sendpulse_secret_key',
					'size' => 20,
					'required' => true
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Export only subscribers'),
					'name' => 'sendpulse',
					'size' => 20,
                    'values' => array(
                        'query' => array(
                            array(
                                'id' => 'only_subscribers',
                                'name' => "",
                                'val' => true,
                                'checked' => ''
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
					'required' => false
				)										
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);

		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;		// false -> remove toolbar
		$helper->toolbar_scroll = true;	  // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);

		// Load current value
		$helper->fields_value['sendpulse_id'] = Configuration::get('sendpulse_id');
		$helper->fields_value['sendpulse_secret_key'] = Configuration::get('sendpulse_secret_key');
		$helper->fields_value['sendpulse_only_subscribers'] = Configuration::get('sendpulse_only_subscribers');
		
		return $helper->generateForm($fields_form);
	}


	public function alterTable($method){
		switch ($method) {
			case 'add':
				$sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'product_lang ADD `custom_field` TEXT NOT NULL';
			break;
			case 'remove':
				$sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'product_lang DROP COLUMN `custom_field`';
			break;
		}
		 
		if(!Db::getInstance()->Execute($sql)){
			return false;
		}
		return true;
	}

	public function installTab($parent, $class_name, $name){
		$tab = new Tab();
		
		$tab->id_parent = (int)Tab::getIdFromClassName($parent);
		$tab->name = array();
		foreach(Language::getLanguages(true) as $lang){
		    $tab->name[$lang['id_lang']]  = $name;
		}
		$tab->class_name = $class_name;
		$tab->module = $this->name;
		$tab->active = 1;
		return $tab->add();
	}
	

	public function removeTabs(){
		$tab = new Tab();


		$tabs = $tab::getCollectionFromModule($this->name);
		if(!empty($tabs)){
			foreach($tabs as $tab){
				if(in_array($tab->class_name, array("AdminSendpulsePreExport", "AdminSendpulsePreImport"))){
					$tab->delete();
				}
			}
			return true;
		}
		return false;

	}	
	

	public function install(){

		
		if(!parent::install()){
			return false;
		}
		$this->installTab('AdminParentCustomer', 'AdminSendpulsePreExport', $this->l('Export to SendPulse'));
		$this->installTab('AdminParentCustomer', 'AdminSendpulsePreImport', $this->l('Import from SendPulse'));
		$this->registerHook("displayBackOfficeHeader");
		return true;
	}

	public function uninstall(){
		if(!parent::uninstall() 
			|| !Configuration::deleteByName('MYMODULE_NAME')
			|| !$this->removeTabs()
			){
			return false;
		}
		return true;
	}

	
	



	
}
