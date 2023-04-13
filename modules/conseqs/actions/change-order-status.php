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

use Conseqs\Parameters\IntParameterDefinition;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use PrestaShopException;
use Context;
use Validate;
use Order;
use OrderState;

class ChangeOrderStatus extends Action
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Change order status');
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        return ParameterDefinitions::none();
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This action will change order status');
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     * @throws PrestaShopException
     */
    public function getInputParameters(ParameterValues $settings)
    {
        return new ParameterDefinitions([
            'params.order' => new IntParameterDefinition($this->l('Order ID')),
            'params.status' => new SelectParameterDefinition($this->l('New status'), static::getOrderStatuses()),
        ]);
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @throws PrestaShopException
     * @throws \Adapter_Exception
     * @throws \PrestaShopDatabaseException
     */
    public function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier)
    {
        $orderId = (int)$input->getValue('params.order');
        $statusId = (int)$input->getValue('params.status');
        $order = new Order($orderId);
        if (! Validate::isLoadedObject($order)) {
            throw new PrestaShopException("Order with id $orderId not found");
        }
        $status = new OrderState($statusId);
        if (! Validate::isLoadedObject($status)) {
            throw new PrestaShopException("Order status with id $statusId not found");
        }
        $employee = Context::getContext()->employee;
        $employeeId = $employee ? (int)$employee->id : 0;
        $order->setCurrentState($statusId, $employeeId);
    }

    /**
     * @return array
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    private static function getOrderStatuses()
    {
        $ret = [];
        $statuses = OrderState::getOrderStates(Context::getContext()->language->id);
        if ($statuses) {
            foreach ($statuses as $row) {
                $id = (int)$row['id_order_state'];
                $name = $row['name'];
                $ret[$id] = $name;
            }
        }
        return $ret;
    }
}