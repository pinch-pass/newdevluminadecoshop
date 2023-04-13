<?php
/**
* 2020 SmartHub
*/

class WebserviceRequest extends WebserviceRequestCore
{
        
    public static function getResources(){
        
		include_once(_PS_MODULE_DIR_ .'SmartHub/classes/ProductPrice.php');
		
		$resources=parent::getResources();
	
		$resources['product_price'] = array('description' => 'Update product price only', 'class' => 'ProductPriceCoreClass', 'forbidden_method' => array('POST', 'DELETE'));
		ksort($resources);
		return $resources;
		
		
	}
}
