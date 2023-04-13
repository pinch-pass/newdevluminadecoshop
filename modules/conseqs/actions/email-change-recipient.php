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

use Conseqs\Parameters\StringParameterDefinition;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use PrestaShopException;

class EmailChangeRecipient extends Action
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Email: change recipient');
    }

    public function getAllowedTriggers()
    {
        return [
            'beforeEmailSent'
        ];
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Modifies email recipient');
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        return ParameterDefinitions::none();
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     */
    public function getInputParameters(ParameterValues $settings)
    {
        return new ParameterDefinitions([
            'toEmail' => new StringParameterDefinition($this->l('New email address')),
            'toName' => new StringParameterDefinition($this->l('Recipient name'))
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
        $runtimeModifier->setParameter('to', $input->getValue('toEmail'));
        $runtimeModifier->setParameter('toName', $input->getValue('toName'));
    }

}
