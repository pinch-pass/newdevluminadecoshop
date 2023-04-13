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

use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\Action;
use Conseqs\Utils;
use Conseqs\RuntimeModifier;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use PrestaShopException;

class EmailChangeTemplate extends Action
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Email: change email template');
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
        return $this->l('Changes template used to render email content');
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
        $keys = array_keys(Utils::getEmailTemplates());
        $templates = array_combine($keys, $keys);
        return new ParameterDefinitions([
            'template' => new SelectParameterDefinition($this->l('New template'), $templates),
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
        $runtimeModifier->setParameter('template', $input->getValue('template'));
    }

}
