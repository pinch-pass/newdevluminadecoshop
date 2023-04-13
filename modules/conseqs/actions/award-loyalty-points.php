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

use Conseqs\Compatibility;
use Conseqs\Parameters\IntParameterDefinition;
use PrestaShopException;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use Module;
use Customer;

class AwardLoyaltyPoints extends Action
{
    private $loyaltyInstalled;

    public function __construct()
    {
        $this->loyaltyInstalled = Module::isInstalled('loyalty') && Module::isEnabled('loyalty');
        $this->addModuleRequirement('loyalty');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Award loyalty points');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This action allows you to grant loyalty points to your customers');
    }

    /**
     * @return Compatibility
     */
    public function getCompatibility()
    {
        if ($this->loyaltyInstalled) {
            return Compatibility::all();
        }
        return Compatibility::incompatible();
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     */
    public function getInputParameters(ParameterValues $settings)
    {
        if ($this->loyaltyInstalled) {
            return new ParameterDefinitions([
                'params.customer' => new IntParameterDefinition($this->l('Customer ID')),
                'params.points' => new IntParameterDefinition($this->l('Awarded points')),
            ]);
        }
        return ParameterDefinitions::none();
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @param RuntimeModifier $runtimeModifier
     * @return mixed
     * @throws PrestaShopException
     * @throws \Adapter_Exception
     * @throws \PrestaShopDatabaseException
     */
    public function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier)
    {
        $customer = (int)$input->getValue('params.customer');
        if (! Customer::customerIdExistsStatic($customer)) {
            throw new PrestaShopException("Customer with id $customer not found");
        }
        $points = (int)$input->getValue('params.points');
        if ($points <= 0)  {
            throw new PrestaShopException("Invalid points $points");
        }
        // insert into tb_loyalty(id_loyalty_state, id_customer, points, date_add, date_upd) values (1, 10, 100, now(), now());
        $clazz = $this->getLoyaltyModuleClass();
        $loyalty = new $clazz();
        $loyalty->id_customer = $customer;
        $loyalty->points = $points;
        $loyalty->id_loyalty_state = 2;
        if ($loyalty->save() === false) {
            throw new PrestaShopException("Failed to save loyalty entry");
        }
    }

    private function getLoyaltyModuleClass()
    {
        require_once(_PS_MODULE_DIR_ . 'loyalty/LoyaltyModule.php');
        require_once(_PS_MODULE_DIR_ . 'loyalty/LoyaltyStateModule.php');
        return defined('_TB_VERSION_') ? 'LoyaltyModule\LoyaltyModule' : 'LoyaltyModule';
    }
}