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
use Conseqs\Parameters\BooleanParameterDefinition;
use Conseqs\Parameters\DateParameterDefinition;
use Conseqs\Parameters\IntParameterDefinition;
use Conseqs\Parameters\NumberParameterDefinition;
use Conseqs\Parameters\StringParameterDefinition;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\ParameterDefinition;

class Equals extends Condition
{
    public function __construct()
    {
        parent::__construct($this->l('is equal to'), [
            BooleanParameterDefinition::TYPE,
            DateParameterDefinition::TYPE,
            IntParameterDefinition::TYPE,
            NumberParameterDefinition::TYPE,
            SelectParameterDefinition::TYPE,
            StringParameterDefinition::TYPE,
        ]);
    }

    public function getParameters(ParameterDefinition $fieldDefinition)
    {
        return new ParameterDefinitions([
            $fieldDefinition->cloneWithName($this->l('Value'))
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
        return $fieldValue == $argValues->getValue(0);
    }
}