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
use Conseqs\Parameters\DateParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\Parameters\IntParameterDefinition;
use Conseqs\Parameters\NumberParameterDefinition;
use Conseqs\ParameterDefinition;

class GreaterThan extends Condition
{
    public function __construct()
    {
        parent::__construct($this->l('is greater than'), [
            IntParameterDefinition::TYPE,
            NumberParameterDefinition::TYPE,
            DateParameterDefinition::TYPE,
        ]);
    }

    /**
     * @param ParameterDefinition $parameterType
     * @param $fieldValue
     * @param ParameterValues $argValues
     * @param ParameterValues $inputParams
     * @return bool
     * @throws \PrestaShopException
     */
    public function execute(ParameterDefinition $parameterType, $fieldValue, ParameterValues $argValues, ParameterValues $inputParams)
    {
        return $fieldValue > $argValues->getValue(0);
    }

    /**
     * @param ParameterDefinition $fieldDefinition
     * @return ParameterDefinitions
     */
    public function getParameters(ParameterDefinition $fieldDefinition)
    {
        return new ParameterDefinitions([
            $fieldDefinition->cloneWithName($this->l('Value'))
        ]);
    }
}