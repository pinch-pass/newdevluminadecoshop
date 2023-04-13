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
use Configuration;
use Conseqs\Utils;
use Exception;

class StringParameterDefinition extends ParameterDefinition
{
    const TYPE = 'string';

    /**
     * StringParameterDefinition constructor.
     * @param string $name
     * @param null $hint
     */
    public function __construct($name, $hint=null)
    {
        parent::__construct(static::TYPE, $name, $hint);
    }

    /**
     * @param $string
     * @return int
     */
    public function convertFromString($string)
    {
        if ($this->getUiBuilder() === 'sqlStatement') {
            return Utils::toInternalSql($string);
        }
        return $string;
    }

    /**
     * @param int $input
     * @return string
     * @throws \PrestaShopException
     */
    public function convertToString($input)
    {
        if (is_string($input)) {
            if ($this->getUiBuilder() === 'sqlStatement') {
                return Utils::toInternalSql($input);
            }
            return $input;
        }
        if (is_array($input)) {
            $lang = Configuration::get('PS_LANG_DEFAULT');
            if (array_key_exists($lang, $input)) {
                return $input[$lang];
            }
            return implode(', ', $input);
        }
        try {
            return "$input";
        } catch (Exception $e) {
            return '';
        }
    }
}