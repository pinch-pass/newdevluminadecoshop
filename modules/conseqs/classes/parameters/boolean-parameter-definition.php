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

class BooleanParameterDefinition extends ParameterDefinition
{
    const TYPE = 'boolean';

    /**
     * BooleanParameterDefinition constructor.
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
        if (! $string) {
            return false;
        }
        if ($string === 'true') {
            return true;
        }
        if ($string === 'false') {
            return false;
        }
        return !!$string;
    }

    /**
     * @param int $input
     * @return string
     */
    public function convertToString($input)
    {
        $input = (int)$input;
        return "$input";
    }
}