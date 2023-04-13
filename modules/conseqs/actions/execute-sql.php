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
use Conseqs\Utils;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use Db;

class ExecuteSql extends Action
{
    // settings
    const STATEMENT = 'statement';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Execute custom SQL');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Executes custom SQL statement');
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        $statement = new StringParameterDefinition($this->l('SQL statement'), $this->l('Statement to be executed'));
        $statement->setUiBuilder('sqlStatement');
        return new ParameterDefinitions([
            static::STATEMENT => $statement
        ]);
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     * @throws \PrestaShopException
     */
    public function getInputParameters(ParameterValues $settings)
    {
        $statement = $settings->getValue(static::STATEMENT);
        $parameters = new ParameterDefinitions([
        ]);
        if ($statement) {
            $placeholders = static::getPlaceholders($statement);
            foreach ($placeholders as $key => $placeholder) {
                $parameters->addParameter($key, new StringParameterDefinition($placeholder));
            }
        }
        return $parameters;
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
        $statement = Utils::toInternalSql($settings->getValue(static::STATEMENT));
        $placeholders = static::getPlaceholders($statement);
        foreach ($placeholders as $key => $placeholder) {
            $statement = str_replace($placeholder, pSQL($input->getValue($key)), $statement);
        }
        if ($statement) {
            Db::getInstance()->execute($statement);
        }
    }

    private static function getPlaceholders($statement)
    {
        $placeholders = Utils::getPlaceholders($statement);
        $ret = [];
        foreach ($placeholders as $placeholder) {
            $key = 'placeholder.' . Utils::camelize(str_replace('}', '', str_replace('{', '', $placeholder)), '_', false);
            $ret[$key] = $placeholder;
        }
        return $ret;

    }
}