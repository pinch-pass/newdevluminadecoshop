<?php
class Store extends StoreCore
{
	//add

	public $option_1;
	public $option_2;
	public $option_3;
	public $option_4;
	public $option_5;
	public $option_6;
	public $option_7;


	public static $options = array(
		'Шаговая доступность от метро',
		'Бесплатная парковка у входа',
		'Пандус для инвалидов	',
		'Специалист по работе с дизайнерами',
		'Комфортная зона',
		'Развлечения для детей',
		'Профессиональная консультация'
		);
	//end add

	public static $definition = array(
        'table' => 'store',
        'primary' => 'id_store',
        'fields' => array(
            'id_country' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_state' =>        array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
            'name' =>            array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 256),
            'address1' =>        array('type' => self::TYPE_STRING, 'validate' => 'isAddress', 'required' => true, 'size' => 256),
            'address2' =>        array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 256),
            'postcode' =>        array('type' => self::TYPE_STRING, 'size' => 12),
            'city' =>            array('type' => self::TYPE_STRING, 'validate' => 'isCityName', 'required' => true, 'size' => 256),
            'latitude' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isCoordinate', 'size' => 13),
            'longitude' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isCoordinate', 'size' => 13),
            'hours' =>            array('type' => self::TYPE_STRING, 'validate' => 'isSerializedArray', 'size' => 65000),
            'phone' =>            array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 25),
            'fax' =>            array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 16),
            'note' =>            array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65000),
            'email' =>            array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
            'active' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'option_1' => 		array('type' => self::TYPE_BOOL), // Metro;
			'option_2' => 		array('type' => self::TYPE_BOOL), // Parking;
			'option_3' => 		array('type' => self::TYPE_BOOL), // Inviders;
			'option_4' => 		array('type' => self::TYPE_BOOL), // Art;
			'option_5' => 		array('type' => self::TYPE_BOOL), // Coffie;
			'option_6' => 		array('type' => self::TYPE_BOOL), // Toys;
			'option_7' => 		array('type' => self::TYPE_BOOL), // Consultation;
        ),
    );


}