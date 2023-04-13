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

class RuntimeModifier
{
    private $return = null;
    private $args;

    public function __construct(&$args = null)
    {
        $this->args = &$args;
    }

    /**
     * @param $value
     */
    public function setReturnValue($value)
    {
        $this->return = $value;
    }

    /**
     * @param $value
     * @param $callable
     */
    public function mergeReturnValue($value, $callable)
    {
        $this->return = call_user_func($callable, $this->return, $value);
    }

    /**
     * @return null
     */
    public function returnValue()
    {
        return $this->return;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setParameter($key, $value)
    {
        if (!is_null($this->args) && array_key_exists($key, $this->args)) {
            $this->args[$key] = $value;
        }
    }

    /**
     * @param $key
     * @param $callable
     */
    public function adjustParameter($key, $callable)
    {
        if (!is_null($this->args) && array_key_exists($key, $this->args)) {
            $this->args[$key] = call_user_func($callable, $this->args[$key]);
        }
    }
}