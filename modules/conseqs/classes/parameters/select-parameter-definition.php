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

namespace Conseqs\Parameters;

use Conseqs\ParameterDefinition;
use PrestaShopException;

class SelectParameterDefinition extends ParameterDefinition
{
    const TYPE = 'select';

    /** @var array */
    private $values;

    static $strictMode = true;

    /**
     * SelectParameterDefinition constructor.
     * @param string $name
     * @param array $values
     * @param null $hint
     */
    public function __construct($name, $values, $hint=null)
    {
        parent::__construct(static::TYPE, $name, $hint);
        $this->values = $values;
    }

    /**
     * @param $strictMode
     */
    public static function useStrictMode($strictMode)
    {
        static::$strictMode = $strictMode;
    }

    /**
     * @param $string
     * @return mixed
     * @throws PrestaShopException
     */
    public function convertFromString($string)
    {
        if (array_key_exists($string, $this->values)) {
            return $string;
        }
        if (static::$strictMode) {
            $keys = implode(', ', array_keys($this->values));
            throw new PrestaShopException("Value '$string' is not a valid select key: [$keys]");
        }
        return '';
    }

    /**
     * @param $input
     * @return mixed
     * @throws PrestaShopException
     */
    public function convertToString($input)
    {
        return $this->convertFromString($input);
    }

    /**
     * @param $key
     * @return array
     */
    public function getValue($key)
    {
        return isset($this->values[$key]) ? $this->values[$key] : $key;
    }


    public function toJson()
    {
        $ret = parent::toJson();
        $ret['values'] = $this->values;
        return $ret;
    }
}