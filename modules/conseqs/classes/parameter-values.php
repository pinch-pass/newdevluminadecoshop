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

use PrestaShopException;

class ParameterValues
{
    /** @var ParameterDefinitions */
    private $definitions;

    /**
     * @var mixed[]
     */
    private $parameterValues = [];

    /**
     * @var array lazy parameters
     */
    private $lazyParameters = [];

    /**
     * ParameterValues constructor.
     * @param ParameterDefinitions $definitions
     */
    public function __construct(ParameterDefinitions $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @param string $name
     * @param string $value
     * @throws PrestaShopException
     */
    public function addParameter($name, $value)
    {
        if ($this->definitions->hasParameter($name)) {
            $definition = $this->definitions->getParameter($name);
            if (!is_string($value)) {
                $this->parameterValues[$name] = $definition->convertToString($value);
            } else {
                if ($definition->getUiBuilder() === 'sqlStatement') {
                    $value = Utils::toInternalSql($value);
                }
                $this->parameterValues[$name] = $value;
            }
        } else {
            throw new PrestaShopException("Unknown parameter name $name");
        }
    }

    /**
     * @param string $name
     * @param string $field
     * @param $callable
     */
    public function addLazyParameter($name, $field, $callable)
    {
        $this->lazyParameters[$name] = [
            'field' => $field,
            'callable' => $callable
        ];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasValue($name)
    {
        return (array_key_exists($name, $this->parameterValues) || array_key_exists($name, $this->lazyParameters));
    }

    /**
     * @param string|int $name
     * @return mixed
     * @throws PrestaShopException
     */
    public function getValue($name)
    {
        $str = $this->getValueAsString($name);
        $definition = $this->definitions->getParameter($name);
        return $definition->convertFromString($str);
    }

    /**
     * @param $name
     * @return mixed
     * @throws PrestaShopException
     */
    public function getJavascriptValue($name)
    {
        $value = $this->getValue($name) ;
        $definition = $this->definitions->getParameter($name);
        return $definition->toJavascriptValue($value);
    }

    /**
     * @return array
     * @throws PrestaShopException
     */
    public function serialize()
    {
        $this->resolveLazyParameters();
        ksort($this->parameterValues);
        return $this->parameterValues;
    }

    /**
     * @param $name
     * @return string
     * @throws PrestaShopException
     */
    public function getValueAsString($name)
    {
        if (array_key_exists($name, $this->parameterValues)) {
            return $this->parameterValues[$name];
        }
        if (array_key_exists($name, $this->lazyParameters)) {
            return $this->resolveLazyParameter($name);
        }
        throw new PrestaShopException("Parameter value for '$name' does not exists");
    }

    /**
     * @param bool $external
     * @return object
     * @throws PrestaShopException
     */
    public function toJson($external = false)
    {
        $ret = [];
        $this->resolveLazyParameters();
        ksort($this->parameterValues);
        foreach ($this->parameterValues as $key => $_) {
            $value = $this->getJavascriptValue($key);
            if ($external) {
                $def = $this->definitions->getParameter($key);
                if ($def->getUiBuilder() === 'sqlStatement') {
                    $value = Utils::toExternalSql($value);
                }
            }
            $ret[$key] = $value;
        }
        return (object)$ret;
    }

    /**
     * @return ParameterDefinitions
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @throws PrestaShopException
     */
    private function resolveLazyParameters()
    {
        foreach ($this->lazyParameters as $key => $_) {
            if (! array_key_exists($key, $this->parameterValues)) {
                $this->resolveLazyParameter($key);
            }
        }
    }

    /**
     * @param $name string
     * @return mixed
     * @throws PrestaShopException
     */
    private function resolveLazyParameter($name)
    {
        $def = $this->lazyParameters[$name];
        $callable = $def['callable'];
        $ret = call_user_func($callable, $def['field']);
        if (is_null($ret)) {
            $this->parameterValues[$name] = null;
        } else {
            $param = $this->definitions->getParameter($name);
            if (is_string($ret)) {
                $ret = $param->convertFromString($ret);
            }
            $this->parameterValues[$name] = $param->convertToString($ret);
        }
        return $this->parameterValues[$name];
    }

    /**
     * @param ParameterValues $values
     */
    public function setFrom(ParameterValues $values)
    {
       $this->definitions = $values->definitions;
       $this->parameterValues = $values->parameterValues;
       $this->lazyParameters = $values->lazyParameters;
    }

}