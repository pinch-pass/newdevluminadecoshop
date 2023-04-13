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

abstract class Condition
{
    /** @var string  */
    private $name;

    /** @var string[] */
    private $types;

    /**
     * ParameterDefinition constructor.
     * @param string $name
     * @param string[] $types
     */
    public function __construct($name, $types)
    {
        $this->name = $name;
        $this->types = $types;
    }

    /**
     * @param ParameterDefinition $fieldDefinition
     * @return ParameterDefinitions
     */
    public abstract function getParameters(ParameterDefinition $fieldDefinition);


    /**
     * @param ParameterDefinition $fieldDefinition
     * @param mixed $fieldValue
     * @param ParameterValues $argValues
     * @param ParameterValues $inputParams
     * @return boolean
     */
    public abstract function execute(ParameterDefinition $fieldDefinition, $fieldValue, ParameterValues $argValues, ParameterValues $inputParams);

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param $string
     * @return string
     */
    public function l($string)
    {
        // TODO:
        return $string;
    }

    /**
     * @return array
     */
    public function toJson()
    {
        return [
            'name' => $this->getName(),
            'types' => $this->getTypes()
        ];
    }

}