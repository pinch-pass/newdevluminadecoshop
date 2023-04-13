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

namespace Conseqs\Actions;

use Tools;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\Parameters\StringParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;

class Redirect extends Action
{
    const URL = 'url';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Redirect');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Redirects user to some url');
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     */
    public function getInputParameters(ParameterValues $settings)
    {
        return new ParameterDefinitions([
            static::URL => new StringParameterDefinition($this->l('Url'))
        ]);
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @param RuntimeModifier $runtimeModifier
     * @throws \PrestaShopException
     */
    public function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier)
    {
        Tools::redirect($input->getValue(static::URL));
    }
}