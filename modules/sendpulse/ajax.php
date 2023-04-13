<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
$method = Tools::getValue('method');

$module_name = "sendpulse";
$mod = "nf";
if (Module::isInstalled($module_name) && Module::isEnabled($module_name)){
	$mod = Module::getInstanceByName($module_name);
	if(!$mod){
		exit("module not install");
	}
}


$msp = new MSP($mod);
switch($method){
    case 'export':
    	if($id = Tools::getValue("category_id")){
	    	$result = $msp->export($id, Tools::getValue("offset"));
    	}
	break;
	case 'import':
		$result = $msp->import();
	break;
	case 'preimport':
		$result = $msp->preImport();
	break;
    default:
    	$result = array("success"=>false, "message"=>"default action");
	break;
}

if(!$result){
	$result = array("success"=>false, "message"=>"ne rezult");
}
die(Tools::jsonEncode($result));



//use PrestaShop\PrestaShop\Adapter\ServiceLocator;

class MSP{
	private $lang = 1;
	private $per_page = 100;
	private $api = false;
	private $mod = false;
	
	public function __construct($mod){
		$this->mod = $mod;
		$this->lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$this->api = $this->getAPI();
		if(!$this->api){
			return array("success"=>false, "book_id"=>false, "message"=>"API ERROR");
		}
	}
	
	public function export($id=0, $offset){
		if(!$id){
			return false;
		}
		$book = $group = new Group($id);
		if(!$group){
			return array("success"=>false, "book_id"=>false, "message"=>$mod->l("Book not found"));
		}
		
		$customers = $group->getCustomers(false, $offset, 100);
		if(!$customers){
			return array("success"=>true, "book_id"=>$group->id, "message"=>"Book empty");
		}
		
		$sbooks = json_decode(json_encode($this->api->listAddressBooks()), true);
		$exist = false;
		$sbook_id = false;
		if($sbooks){
			foreach($sbooks as $sbook){
				if($sbook['name']==$book->name[$this->lang]){
					$sbook_id = $sbook['id'];
					break;
				}
			}
		}
		
		if(!$sbook_id){
			$temp = (array)$this->api->createAddressBook($book->name[$this->lang]);
			if(isset($temp['error_code'])){ //если бук уже есть на сервере, но его нету в таблице
				return array("success"=>false, "book_id"=>$group->id, "message"=>"Sommeting wrong...");				
			}else{ 
				$sbook_id = $temp['id'];
			}
		}
		$to_book = array();
		foreach($customers as $customer){
			if(Configuration::get('sendpulse_only_subscribers')){
				if(!$customer['newsletter']){
					continue;
				}
			}
			if($customer['email']){
				if($customer['firstname'] and $customer['lastname']){
					$c = array($this->mod->l("First name")=>$customer['firstname'], $this->mod->l("Last name")=>$customer['lastname']);
				}
				$to_book[] = array(
					"email"=>$customer['email'], 
					"variables"=>$c
				);			
			}
		}
		if(!empty($to_book) and $sbook_id){
			$result = $this->api->addEmails($sbook_id, $to_book);
			if(isset($result->result) and $result->result){
				return array("success"=>true, "book_id"=>$sbook_id);
			}else{
				sleep(1);
				ob_start();
				var_dump($result);
				$x = ob_get_clean();
				return array("success"=>false, "message"=>$x, "book_id"=>$sbook_id, "try_again"=>true);
			}
		}else{
			return array("success"=>true, "book_id"=>false, "message"=>$mod->l("empty book id or no contacts "));
		}
		return array("success"=>false, "meessage"=>("Something wrong"));
	}
	
	public function import(){
			
		$errors = "";	
		sleep(1);

		$category_id = Tools::getValue("category_id");
		$offset = Tools::getValue("offset");
		$group = new Group();
		$sbook = $this->api->getBookInfo($category_id);
		if(isset($sbook[0])){
			$sbook = $sbook[0];
		}
		
		if(!$sbook){
			return array("success"=>false, "book_id"=>$category_id, "message"=>$mod->l("Book not found"));
		}

		$sbook->name = mb_substr($sbook->name, 0, 31);

		$current_group = $group->searchByName($sbook->name);


		if($current_group){
			$group_id = $current_group['id_group'];
		}else{
			$group->name[$this->lang] = $sbook->name;
			$group->price_display_method = "0";
			$group->add();
			$group_id = $group->id;
		}


		if($sbook->all_email_qty>0){
			$semails = $this->api->getEmailsFromBook($category_id, $offset, 100);


			if($semails){
//				$crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing'); 
				foreach($semails as $semail){
					if(!Validate::isEmail($semail->email)){
						// || !Validate::isPasswd($semail->email)){
						continue;
					}
					$customer = new Customer();
					//TODO Добавить телефон
					$current = $customer->getByEmail($semail->email);
					

					$dummy = mb_substr(explode("@", $semail->email), 0, 31);
					
					$dummy_name = preg_replace("/[^a-zA-Zа-яА-Я]/", "", ucfirst($dummy[0]));
					$dummy_name = preg_replace('/[0-9]+/', '', $dummy_name);
					if(!$dummy_name){
						$dummy_name = "noname";
					}						
					
					if($current){

						try{
							$customer = new Customer($customer->id);
							if(isset($semail->variables)){
								$variables = get_object_vars($semail->variables);
								
								$variables[$this->mod->l('First name')] = preg_replace('/[0-9]+/', '', $variables[$this->mod->l('First name')]);
								$variables[$this->mod->l('Last name')] = preg_replace('/[0-9]+/', '', $variables[$this->mod->l('Last name')]);
								$variables[$this->mod->l('Full name')] = preg_replace('/[0-9]+/', '', $variables[$this->mod->l('Full name')]);																
								
								if(!isset($variables[$this->mod->l('First name')]) and isset($variables['имя'])){
									$variables[$this->mod->l('First name')] = preg_replace('/[0-9]+/', '', $variables['имя']);
									$variables[$this->mod->l('Last name')] = preg_replace('/[0-9]+/', '', $variables['имя']);
								}
								
								if(isset($variables[$this->mod->l('First name')]) and ($variables[$this->mod->l('First name')])){
									$current->firstname = (ucfirst($variables[$this->mod->l('First name')]));
								}else{
									$current->firstname = $dummy_name;
								}
						
								if(isset($variables[$this->mod->l('Last name')]) and ($variables[$this->mod->l('Last name')])){
									$current->lastname = (ucfirst($variables[$this->mod->l('Last name')]));
								}else{
									$current->lastname = $dummy_name;
								}
						
								if(isset($variables[$this->mod->l('Full name')])){
									$current->name = $variables[$this->mod->l('Full name')];
								}else{
									$current->name = $dummy_name;
								}
							}
							
							$current->addGroups(array($group_id));
							$current->newsletter = true;
							$current->update();
						}catch(Exception $e){
							$errors .= ("<span class='error'>(U) {$semail->email} - ".$e->getMessage()."</span><br>");
#							die(Tools::jsonEncode(array("success"=>false, "error"=>"wrong_name", "wrong_name"=>true,
#								"message"=>("{$semail->email} - error on update: ".$e->getMessage()))));	
						}
					
					}else{
						if(!$semail){
							break;
						}					
#						$dummy = explode("@", $semail->email);
#						$dummy_name = preg_replace("/[^a-zA-Zа-яА-Я]/", "", ucfirst($dummy[0]));
#						$dummy_name = preg_replace('/[0-9]+/', '', $dummy_name);
#						if(!$dummy_name){
#							$dummy_name = "noname";
#						}
						
						if(isset($semail->variables)){
							$variables = $semail->variables;
							$variables = get_object_vars($semail->variables);


							$variables[$this->mod->l('First name')] = preg_replace('/[0-9]+/', '', $variables[$this->mod->l('First name')]);
							$variables[$this->mod->l('Last name')] = preg_replace('/[0-9]+/', '', $variables[$this->mod->l('Last name')]);
							$variables[$this->mod->l('Full name')] = preg_replace('/[0-9]+/', '', $variables[$this->mod->l('Full name')]);																
							
							
							if(isset($variables[$this->mod->l('First name')]) and trim($variables[$this->mod->l('First name')])){
								$customer->firstname = (ucfirst($variables[$this->mod->l('First name')]));
							}else{
								$customer->firstname = $dummy_name;
							}
					
							if(isset($variables[$this->mod->l('Last name')])  and trim($variables[$this->mod->l('Last name')])){
								$customer->lastname = (ucfirst($variables[$this->mod->l('Last name')]));
							}else{
								$customer->lastname = $dummy_name;
							}
					
							if(isset($variables[$this->mod->l('Full name')])){
								$customer->name = $variables[$this->mod->l('Full name')];
							}else{
								$customer->name = $dummy_name;
							}
						
							$customer->email = $semail->email;
							$customer->passwd = md5($semail->email);
							
							
							$customer->id_default_group = $group_id;
							$customer->newsletter = true;
							try{	
								$customer->add();						
								$customer->addGroups(array($group_id));
							}catch(Exception $e){
								$errors .=("<span class='error'>(I1) {$semail->email} - ".$e->getMessage()."</span><br>");
#								die(Tools::jsonEncode(array("success"=>false, "wrong_name"=>true, "error"=>"wrong_name", 
#									"message"=>("{$semail->email} - error on insert: ".$e->getMessage()))));	
							}
						
						}else{
							$customer->firstname = $dummy_name;
							$customer->lastname = $dummy_name;
							$customer->name = $dummy_name;
							$customer->email = $semail->email;
							$customer->passwd = md5($semail->email);
							$customer->id_default_group = $group_id;
							$customer->newsletter = true;
							try{	
								$customer->add();						
								$customer->addGroups(array($group_id));
							}catch(Exception $e){
								$errors .=("<span class='error'>(I2) {$semail->email} - ".$e->getMessage()."</span><br>");
#								die(Tools::jsonEncode(array("success"=>false, "wrong_name"=>true, "error"=>"wrong_name", 
#									"message"=>("{$semail->email} -error on insert: ".$e->getMessage()))));	
							}												

						}
					}
				}
			
			}
		}
		return array("success"=>true, 
			"message"=>"category $category_id imported", 
			
			"errors"=>$errors
			);
		
		return array("success"=>true, "message"=>$x);
	}
	
	public function preImport(){
		$sbooks = json_decode(json_encode($this->api->listAddressBooks()), true);
		if($sbooks){
			return array("success"=>true, "data"=>$sbooks);
		}else{
			return array("success"=>false, "message"=>$this->mod->l("Books not found"));
		}
	}
	
	public function getAPI(){
		require_once(dirname(__FILE__).'/classes/sendpulseInterface.php');
		require_once(dirname(__FILE__).'/classes/sendpulse.php');	
		$api = new SendpulseApi(Configuration::get('sendpulse_id'), Configuration::get('sendpulse_secret_key'), "session", $this->mod);
		return $api;
	}
	
}
