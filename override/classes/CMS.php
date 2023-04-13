<?php
class CMS extends CMSCore
{
	//add

	public $name;
	public $adress;
	public $phone;
	public $work_time;
	public $route_link;
	public $tour_link;
	public $map_link;

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
		'Игровая зона',
		'Профессиональная консультация'
		);
	//end add

	public static $definition = array(
		'table' => 'cms',
		'primary' => 'id_cms',
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			'id_cms_category' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'position' => 			array('type' => self::TYPE_INT),
			'indexation' =>     	array('type' => self::TYPE_BOOL),
			'active' => 			array('type' => self::TYPE_BOOL),

			'option_1' => 		array('type' => self::TYPE_BOOL), // Metro;
			'option_2' => 		array('type' => self::TYPE_BOOL), // Parking;
			'option_3' => 		array('type' => self::TYPE_BOOL), // Inviders;
			'option_4' => 		array('type' => self::TYPE_BOOL), // Art;
			'option_5' => 		array('type' => self::TYPE_BOOL), // Coffie;
			'option_6' => 		array('type' => self::TYPE_BOOL), // Toys;
			'option_7' => 		array('type' => self::TYPE_BOOL), // Consultation;
		

			/* Lang fields */
			'meta_description' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
			'meta_keywords' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
			'meta_title' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
			'link_rewrite' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
			'content' => 			array('type' => self::TYPE_HTML, 'lang' => true, 'size' => 3999999999999),
			
			//add
			'name' => 		array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			'adress' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			'phone' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			'work_time' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			'route_link' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			'tour_link' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			'map_link' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			//end add
		),
	);
	
	public static function getCMSContent($id_cms, $id_lang = null, $id_shop = null)
	{
		if (is_null($id_lang))
			$id_lang = (int)Configuration::get('PS_SHOP_DEFAULT');
		if (is_null($id_shop))
			$id_shop = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = '
			SELECT `content`, work_cost
			FROM `'._DB_PREFIX_.'cms_lang`
			WHERE `id_cms` = '.(int)$id_cms.' AND `id_lang` = '.(int)$id_lang.' AND `id_shop` = '.(int)$id_shop;

		return Db::getInstance()->getRow($sql);
	}
	
	public static function getCMSCategory($id_cms){
		
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT  `id_cms_category` 
															FROM  `'._DB_PREFIX_.'cms` 
															WHERE  `id_cms` = '.$id_cms);
	}

	


}