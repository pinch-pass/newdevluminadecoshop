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
use DateTime;

class DateParameterDefinition extends ParameterDefinition
{
    const TYPE = 'date';

    /**
     * DateParameterDefinition constructor.
     * @param string $name
     * @param null $hint
     */
    public function __construct($name, $hint=null)
    {
        parent::__construct(static::TYPE, $name, $hint);
    }

    /**
     * @param $string
     * @return DateTime
     * @throws \Exception
     */
    public function convertFromString($string)
    {
        if ($string && $string != '0000-00-00' && (bool)strtotime($string)) {
            $date = new DateTime($string);
            $string = $this->convertToString($date);
            return new DateTime($string);
        } else {
            return null;
        }
    }

    /**
     * @param DateTime $input
     * @return string
     * @throws \Exception
     */
    public function convertToString($input)
    {
        if ($input) {
            if (is_string($input)) {
                $input = $this->convertFromString($input);
            }
            if ($input && $input instanceof DateTime) {
                return $input->format('Y-m-d');
            }
        }
        return "";
    }

    /**
     * @param $input
     * @return DateTime|mixed
     * @throws \Exception
     */
    public function toJavascriptValue($input)
    {
        return $this->convertToString($input);
    }
}