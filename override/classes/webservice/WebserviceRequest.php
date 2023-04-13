<?php

    class WebserviceRequest extends WebserviceRequestCore{
        
        public static function getResources(){
            
            $resources=parent::getResources();
            
            $resources['product_price'] = array('description' => 'Update product price only', 'class' => 'ProductPrice', 'forbidden_method' => array('POST', 'DELETE'));
            $resources['product_accessory'] = array('description' => 'Product accessory', 'class' => 'ProductAccessory', 'forbidden_method' => array(''));
            ksort($resources);
            return $resources;
            
            
        }
    }

?>