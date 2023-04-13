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

class ParameterDefinitions
{
    /** @var ParameterDefinitions */
    private static $empty = null;

    /** @var ParameterDefinition[] */
    private $parameters = [];

    /** @var bool */
    private $numerical = true;

    /**
     * ParameterDefinitions constructor.
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->addParameters($parameters);
    }

    /**
     * @param array $parameters
     */
    public function addParameters($parameters)
    {
        foreach ($parameters as $key => $definition) {
           $this->addParameter($key, $definition);
        }
    }

    /**
     * @param string $name
     * @param ParameterDefinition $definition
     */
    public function addParameter($name, ParameterDefinition $definition)
    {
        if (! is_int($name)) {
           $this->numerical = false;
        }
        $this->parameters[$name] = $definition;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @param $name
     * @return ParameterDefinition
     * @throws \PrestaShopException
     */
    public function getParameter($name)
    {
        if (! $this->hasParameter($name)) {
            throw new \PrestaShopException("Parameter '$name'' not defined");
        }
        return $this->parameters[$name];
    }

    /**
     * @return string[]
     */
    public function getParameters()
    {
        return array_keys($this->parameters);
    }

    /**
     * @return ParameterDefinition[]
     */
    public function getDefinitions()
    {
        return array_values($this->parameters);
    }

    /**
     * @return object|array
     */
    public function toJson()
    {
        $ret = array_map(function(ParameterDefinition $parameter) {
            return $parameter->toJson();
        }, $this->parameters);
        return $this->numerical ? $ret : (object)$ret;
    }

    /**
     * @return ParameterDefinitions
     */
    public static function none()
    {
        if (! static::$empty) {
            static::$empty = new ParameterDefinitions();
        }
        return static::$empty;
    }


}
