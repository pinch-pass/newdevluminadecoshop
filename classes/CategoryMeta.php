<?php

class CategoryMetaCore extends ObjectModel
{

    public $id;
    /** @var string name name */
    public $name;

    /** @var string message content */
    public $url;

    /** @var string message content */
    public $title;

    /** @var string message content */
    public $meta_description;

    /** @var string message content */
    public $keywords;

    /** @var string Object creation date */
    public $h1;

    /** @var string Object creation date */
    public $description;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'category_meta',
        'primary' => 'id_category',
        'fields' => array(
           'url' =>    array('type' => self::TYPE_STRING, 'lang' => false, 'required' => true, 'size' => 255),
           'title' =>    array('type' => self::TYPE_STRING, 'lang' => false, 'required' => false, 'size' => 255),
           'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => false, 'required' => false, 'size' => 255),
           'keywords' =>    array('type' => self::TYPE_STRING, 'lang' => false, 'required' => false, 'size' => 255),
           'h1' =>    array('type' => self::TYPE_STRING, 'lang' => false, 'required' => false, 'size' => 255),
           'description' =>    array('type' => self::TYPE_HTML, 'lang' => false, 'validate' => 'isCleanHtml'),
        ),
    );

    public static function getDescriptionClean($description)
    {
        return Tools::getDescriptionClean($description);
    }


}
