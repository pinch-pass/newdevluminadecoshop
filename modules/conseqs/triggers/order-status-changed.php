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

use Configuration;
use Conseqs\ObjectModelMetadata;
use Conseqs\ParameterDefinitions;
use Conseqs\ParameterValues;
use Conseqs\RulesManager;
use Conseqs\Trigger;
use Order;
use OrderState;

class OrderStatusChanged extends Trigger
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Order status change');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Trigger is executed when order status change');
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
        ObjectModelMetadata::addObjectParameterDefinitions('oldStatus', $this->l('Old status'), 'OrderState', $definitions);
        ObjectModelMetadata::addObjectParameterDefinitions('newStatus', $this->l('New status'), 'OrderState', $definitions);
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
        $lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $parameters = $sourceParameters['parameters'];
        $order = new Order((int)$parameters['id_order'], $lang);
        $newStatus = new OrderState((int)$parameters['newOrderStatus']->id, $lang);
        $oldStatus = new OrderState((int)$order->current_state, $lang);
        ObjectModelMetadata::addObjectParameterValues('order', $values, $order);
        ObjectModelMetadata::addObjectParameterValues('oldStatus', $values, $oldStatus);
        ObjectModelMetadata::addObjectParameterValues('newStatus', $values, $newStatus);
    }

    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws \PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $manager->registerHook('actionOrderStatusUpdate');
    }


}