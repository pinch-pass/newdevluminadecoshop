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

use PrestaShopException;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\Parameters\StringParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;

class RaiseError extends Action
{
    const MESSAGE = 'message';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Raise Error');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This action will display error page with custom error message');
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     */
    public function getInputParameters(ParameterValues $settings)
    {
        return new ParameterDefinitions([
            static::MESSAGE => new StringParameterDefinition($this->l('Error message'))
        ]);
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @param RuntimeModifier $runtimeModifier
     * @throws PrestaShopException
     */
    public function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier)
    {
        $error = new PrestaShopException($input->getValue(static::MESSAGE));
        $error->displayMessage();
        die();
    }
}