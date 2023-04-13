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
use Conseqs\Parameters\SelectParameterDefinition;
use PrestaShopException;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use Module;
use Hook;

class GenzoKronaAction extends Action
{
    private $kronaInstalled;

    public function __construct()
    {
        $this->kronaInstalled = Module::isInstalled('genzo_krona') && Module::isEnabled('genzo_krona');
        $this->addModuleRequirement('genzo_krona');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Trigger Krona Action');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This action will trigger krona action');
    }

    /**
     * @return Compatibility
     */
    public function getCompatibility()
    {
        if ($this->kronaInstalled) {
            return Compatibility::thirtybeesOnly();
        }
        return Compatibility::incompatible();
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     * @throws PrestaShopException
     * @throws \Adapter_Exception
     * @throws \PrestaShopDatabaseException
     */
    public function getInputParameters(ParameterValues $settings)
    {
        if ($this->kronaInstalled) {
            return new ParameterDefinitions([
                'action' => new SelectParameterDefinition($this->l('Krona action'), $this->getKronaActionSelectValues()),
                'customer' => new IntParameterDefinition($this->l('Customer/Player ID')),
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
        $actionId = (int)$input->getValue('action');
        $customerId = (int)$input->getValue('customer');
        if ($actionId && $customerId) {
            $actions = $this->getKronaActions();
            if (isset($actions[$actionId])) {
                $action = $actions[$actionId];
                $data = [
                    'module_name' => $action['module'],
                    'action_name' => $action['key'],
                    'id_customer' => $customerId
                ];
                Hook::exec('actionExecuteKronaAction', $data);
            }
        }
    }


    /**
     * @return array|false|\PDOStatement|null
     * @throws PrestaShopException
     * @throws \Adapter_Exception
     * @throws \PrestaShopDatabaseException
     */
    private function getKronaActions()
    {
        // load module
        Module::getInstanceByName('genzo_krona');
        $actions = \KronaModule\Action::getAllActions();
        if ($actions) {
            $ret = [];
            foreach ($actions as $action) {
                $id = (int)$action['id_action'];
                $ret[$id] = $action;
            }
            return $ret;
        }
        return [];
    }

    /**
     * @return array
     * @throws PrestaShopException
     * @throws \Adapter_Exception
     * @throws \PrestaShopDatabaseException
     */
    private function getKronaActionSelectValues()
    {
        return array_map(function($action) {
            return '[' . $action['module']. '] ' . $action['title'];
        }, $this->getKronaActions());
    }
}