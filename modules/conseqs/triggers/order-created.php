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
use Conseqs\ParameterValues;
use Conseqs\RulesManager;
use Conseqs\Trigger;

class OrderCreated extends Trigger
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('New order created');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Executed when new order has been created');
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
     * @param ParameterDefinitions $definitions
     * @throws \PrestaShopException
     */
    public function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions)
    {
        ObjectModelMetadata::addObjectParameterDefinitions('order', $this->l('Order'), 'Order', $definitions);
    }

    /**
     * @param ParameterValues $values
     * @param ParameterValues $settings
     * @param $sourceParameters
     * @return void
     * @throws \PrestaShopException
     * @throws \ReflectionException
     */
    public function collectOutputParameterValues(ParameterValues $values, ParameterValues $settings, $sourceParameters)
    {
        $object = $sourceParameters['parameters']['object'];
        ObjectModelMetadata::addObjectParameterValues('order', $values, $object);
    }

    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws \PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $manager->registerHook('actionObjectOrderAddAfter');
    }


}