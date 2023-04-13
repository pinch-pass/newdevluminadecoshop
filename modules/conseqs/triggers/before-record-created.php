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

namespace Conseqs\Triggers;

use Conseqs\ObjectModelMetadata;
use Conseqs\ParameterDefinitions;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\RulesManager;
use Conseqs\Trigger;
use Conseqs\Utils;

class BeforeRecordCreated extends Trigger
{
    const RECORD_TYPE = 'recordType';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Before record is created');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This hook is called just before new record is saved into the database');
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        $settings = new ParameterDefinitions();
        $settings->addParameter(static::RECORD_TYPE, new SelectParameterDefinition($this->l('Record type'), ObjectModelMetadata::getObjectModels()));
        return $settings;
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterDefinitions $definitions
     * @throws \PrestaShopException
     */
    public function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions)
    {
        $type = $settings->getValue(static::RECORD_TYPE);
        ObjectModelMetadata::addObjectParameterDefinitions(lcfirst($type), Utils::humanize($type), $type, $definitions);
    }

    /**
     * @param ParameterValues $values
     * @param ParameterValues $settings
     * @param $sourceParameters
     * @throws \PrestaShopException
     * @throws \ReflectionException
     */
    public function collectOutputParameterValues(ParameterValues $values, ParameterValues $settings, $sourceParameters)
    {
        $object = $sourceParameters['parameters']['object'];
        $type = $settings->getValue(static::RECORD_TYPE);
        ObjectModelMetadata::addObjectParameterValues(lcfirst($type), $values, $object);
    }

    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws \PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $recordType = $settings->getValue(static::RECORD_TYPE);
        $manager->registerHook('actionObject' . $recordType . 'AddBefore');
    }


}