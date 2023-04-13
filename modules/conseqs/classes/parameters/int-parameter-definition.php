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

class IntParameterDefinition extends ParameterDefinition
{
    const TYPE = 'int';

    /**
     * IntParameterDefinition constructor.
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
        return (int)$string;
    }

    /**
     * @param int $input
     * @return string
     */
    public function convertToString($input)
    {
        if (is_null($input)) {
            return "";
        }
        return "$input";
    }
}