<?php
/**
* 2020 SmartHub
*
* 
*/
    class ProductPriceCoreClass extends ObjectModel{   


        /** @var string Object last modification date */
        
        public $date_upd;
        public $id_product;
        public $price;
        public $active;
//        public $quantity;
        
        
        public static $definition = array(
            'table' => 'product',
            'primary' => 'id_product',
            'fields' => array(
                                'id_shop_default'           =>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                                'active'                    =>  array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),
                                'price'                     =>  array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'required' => true),
                               // 'prestashop_product_id'  =>  array('type' => self::TYPE_INT,  'required' => true),
                                'date_upd'                  =>  array('type'  => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDate'),
//                                'quantity'                  =>  array('type' => self::TYPE_HTML, 'shop' => false, 'validate' => 'isInt', 'required' => true),
                                ) 
            );
        protected $webserviceParameters = array();
    }
 
