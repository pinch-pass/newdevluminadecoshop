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

abstract class ParameterDefinition
{
    /** @var string  */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $hint;

    /** @var  string uiBuilder*/
    private $uiBuilder = null;

    /**
     * ParameterDefinition constructor.
     * @param $type
     * @param $name
     * @param $hint
     * @param null $uiBuilder
     */
    public function __construct($type, $name, $hint)
    {
        $this->type = $type;
        $this->name = $name;
        $this->hint = $hint;
    }

    /**
     * @return array
     */
    public function toJson()
    {
        $ret = [
            'type' => $this->getType(),
            'name' => $this->getName(),
        ];
        $hint = $this->getHint();
        if ($hint) {
            $ret['hint'] = $hint;
        }
        $uiBuilder = $this->getUiBuilder();
        if ($uiBuilder) {
            $ret['uiBuilder'] = $uiBuilder;
        }
        return $ret;
    }

    /**
     * @param string $string
     * @return mixed
     */
    public abstract function convertFromString($string);

    /**
     * @param mixed $input
     * @return string
     */
    public abstract function convertToString($input);

    /**
     * @param $input
     * @return mixed
     */
    public function toJavascriptValue($input)
    {
        return $input;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * @return string
     */
    public function getUiBuilder()
    {
        return $this->uiBuilder;
    }

    /**
     * @param string $uiBuilder
     */
    public function setUiBuilder($uiBuilder)
    {
        $this->uiBuilder = $uiBuilder;
    }
    
    /**
     * @param $name
     * @param null $hint
     * @return ParameterDefinition
     */
    public function cloneWithName($name, $hint=null)
    {
        $cloned = clone $this;
        $cloned->name = $name;
        $cloned->hint = $hint;
        return $cloned;
    }

}