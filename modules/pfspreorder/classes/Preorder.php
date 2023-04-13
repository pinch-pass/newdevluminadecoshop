<?php

class Preorder extends ObjectModel
{
    public $id_pfspreorder;
    public $form_name;
    public $product;
    public $phone;
    public $fname;
    public $lname;
    public $status;
    public $created;
    public $other;
    public static $definition = [
        'table' => 'pfspreorder',
        'primary' => 'id_pfspreorder',
        'fields' => [
            'form_name' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => true],
            'product' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => true],
            'status' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => true],
            'phone' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => true],
            'fname' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => true],
            'lname' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => true],
            'other' => ['type' => self::TYPE_STRING, 'validate' => 'isAnything', 'required' => true],
            'created' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];
}
