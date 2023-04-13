<?php
/**
 * Copyright (C) 2017-2019 Petr Hucik <petr@getdatakick.com>
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the DataKick Regular License version 1.0
 * For more information see LICENSE.txt file
 *
 * @author    Petr Hucik <petr@getdatakick.com>
 * @copyright 2017-2019 Petr Hucik
 * @license   Licensed under the DataKick Regular License version 1.0
 */

namespace Conseqs;
use Configuration;

class LazyObjectModel
{
    /** @var string  */
    private $modelClass;

    /** @var int */
    private $id;

    /** @var int */
    private $language;

    /** @var ObjectModel */
    private $instance = null;

    /**
     * ParameterDefinition constructor.
     * @param string $modelClass
     * @param int $id
     * @param int $language
     * @throws \PrestaShopException
     */
    public function __construct($modelClass, $id, $language)
    {
        $this->modelClass = $modelClass;
        $this->id = $id;
        $this->language = $language ? $language : Configuration::get('PS_LANG_DEFAULT');
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValue($field)
    {
        if (is_null($this->instance)) {
            $clazz = $this->getModelClass();
            $this->instance = new $clazz($this->id, $this->language);
        }
        return $this->instance->{$field};
    }
}