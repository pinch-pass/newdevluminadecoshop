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

namespace Conseqs\Conditions;

use Conseqs\Condition;
use Conseqs\ParameterDefinitions;
use Conseqs\ParameterValues;
use Conseqs\Parameters\StringParameterDefinition;
use Conseqs\ParameterDefinition;

class StartsWith extends Condition
{
    public function __construct()
    {
        parent::__construct($this->l('starts with'), [
            StringParameterDefinition::TYPE,
        ]);
    }

    /**
     * @param ParameterDefinition $fieldDefinition
     * @param $fieldValue
     * @param ParameterValues $argValues
     * @param ParameterValues $inputParams
     * @return bool
     * @throws \PrestaShopException
     */
    public function execute(ParameterDefinition $fieldDefinition, $fieldValue, ParameterValues $argValues, ParameterValues $inputParams)
    {
        return strpos($fieldValue, $argValues->getValue(0)) === 0;
    }

    /**
     * @param ParameterDefinition $fieldDefinition
     * @return ParameterDefinitions
     * return ParameterDefinitions
     */
    public function getParameters(ParameterDefinition $fieldDefinition)
    {
        return new ParameterDefinitions([
            new StringParameterDefinition($this->l('Prefix'))
        ]);
    }
}