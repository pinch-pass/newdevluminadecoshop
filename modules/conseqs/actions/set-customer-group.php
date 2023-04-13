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
use Conseqs\Utils;
use PrestaShopException;
use Validate;
use Customer;
use Group;

class SetCustomerGroup extends Action
{
    const MODE = 'mode';
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Assign customer to group');
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
        return $this->l('This action will change product quantity on stock');
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     * @throws PrestaShopException
     */
    public function getInputParameters(ParameterValues $settings)
    {
        return new ParameterDefinitions([
            'params.customer' => new IntParameterDefinition($this->l('Customer ID')),
            'params.group' => new SelectParameterDefinition($this->l('Group'), Utils::getGroups()),
        ]);
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @param RuntimeModifier $runtimeModifier
     * @return mixed
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier)
    {
        $customerId = (int)$input->getValue('params.customer');
        $groupId = (int)$input->getValue('params.group');
        $customer = new Customer($customerId);
        if (! Validate::isLoadedObject($customer)) {
            throw new PrestaShopException("Customer with id $customerId not found");
        }
        $group = new Group($groupId);
        if (! Validate::isLoadedObject($group)) {
            throw new PrestaShopException("group with id $groupId not found");
        }
        $groups = $customer->getGroups();
        if (! in_array($groupId, $groups)) {
            $groups[] = $groupId;
            $customer->updateGroup($groups);
        }
        if ($customer->id_default_group != $groupId) {
            $customer->id_default_group = $groupId;
            $customer->update();
        }
    }
}