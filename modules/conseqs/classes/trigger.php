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

namespace Conseqs;
use Configuration;
use Conseqs\Parameters\DateParameterDefinition;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\Parameters\StringParameterDefinition;
use Context;
use Country;
use Currency;
use Customer;
use Dispatcher;
use Employee;
use Language;
use Shop;

abstract class Trigger
{
    private $modules = [];

    /**
     * @return string
     */
    public abstract function getName();

    /**
     * @return string
     */
    public abstract function getDescription();

    /**
     * @return Compatibility
     */
    public function getCompatibility()
    {
        return Compatibility::all();
    }

    /**
     * @return array
     */
    public final function getModulesRequirement()
    {
        return $this->modules;
    }

    /**
     * @param string $module
     */
    public function addModuleRequirement($module)
    {
        if (! in_array($module, $this->modules)) {
            $this->modules[] = $module;
        }
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        return ParameterDefinitions::none();
    }

    /**
     * @return ParameterDefinitions
     */
    public function getRuntimeModifierParameters()
    {
        return ParameterDefinitions::none();
    }


    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     * @throws \PrestaShopException
     */
    public final function getOutputParameters(ParameterValues $settings)
    {
        $definitions = new ParameterDefinitions();
        $this->registerOutputParameterDefinitions($settings, $definitions);

        // add context objects
        $definitions->addParameter('trigger.type', new SelectParameterDefinition($this->l('Trigger: type'), [
            'hook' => $this->l('Hook'),
            'measure' => $this->l('Measure')
        ]));
        $definitions->addParameter('trigger.name', new StringParameterDefinition($this->l('Trigger: name')));
        $definitions->addParameter('context.url', new StringParameterDefinition($this->l('Context: Current url')));
        $definitions->addParameter('context.controller', new StringParameterDefinition($this->l('Context: Current controller')));
        $definitions->addParameter('context.date', new DateParameterDefinition($this->l('Context: Current date')));
        $definitions->addParameter('context.time', new StringParameterDefinition($this->l('Context: Current time')));
        $definitions->addParameter('context.baseDir', new StringParameterDefinition($this->l('Context: Shop directory')));
        $definitions->addParameter('context.shop.email', new StringParameterDefinition($this->l('Context: Shop') . ': ' . $this->l('Shop email')));
        $definitions->addParameter('context.shop.url', new StringParameterDefinition($this->l('Context: Shop') . ': ' . $this->l('Shop url')));
        ObjectModelMetadata::addObjectParameterDefinitions('context.customer', $this->l('Context: Customer'), 'Customer', $definitions);
        ObjectModelMetadata::addObjectParameterDefinitions('context.language', $this->l('Context: Language'), 'Language', $definitions);
        ObjectModelMetadata::addObjectParameterDefinitions('context.country', $this->l('Context: Country'), 'Country', $definitions);
        ObjectModelMetadata::addObjectParameterDefinitions('context.currency', $this->l('Context: Currency'), 'Currency', $definitions);
        ObjectModelMetadata::addObjectParameterDefinitions('context.shop', $this->l('Context: Shop'), 'Shop', $definitions);
        ObjectModelMetadata::addObjectParameterDefinitions('context.employee', $this->l('Context: Employee'), 'Employee', $definitions);

        return $definitions;
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterDefinitions $definitions
     * @return mixed
     */
    public abstract function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions);


    /**
     * @param ParameterValues $settings
     * @param array $sourceParameters
     * @return bool
     */
    public function shouldTrigger(ParameterValues $settings, $sourceParameters)
    {
        return true;
    }

    /**
     * @param ParameterValues $settings
     * @param array $sourceParameters
     * @return ParameterValues
     * @throws \PrestaShopException
     * @throws \Adapter_Exception
     * @throws \ReflectionException
     */
    public final function getOutputParameterValues(ParameterValues $settings, $sourceParameters)
    {
        $values = new ParameterValues($this->getOutputParameters($settings));
        $this->collectOutputParameterValues($values, $settings, $sourceParameters);

        // add context
        $context = Context::getContext();
        $values->addParameter('trigger.type', $sourceParameters['type']);
        $values->addParameter('trigger.name', $this->getName());
        $values->addParameter('context.url', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $values->addParameter('context.controller', Dispatcher::getInstance()->getController());
        $values->addParameter('context.date', date('Y-m-d'));
        $values->addParameter('context.time', date('H:i:s'));
        $values->addParameter('context.baseDir', rtrim(_PS_ROOT_DIR_, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
        $values->addParameter('context.shop.email', Configuration::get('PS_SHOP_EMAIL'));
        $values->addParameter('context.shop.url', $context->link->getBaseLink($context->shop->id));
        ObjectModelMetadata::addObjectParameterValues('context.customer', $values, $context->customer ? $context->customer : new Customer());
        ObjectModelMetadata::addObjectParameterValues('context.language', $values, $context->language ? $context->language : new Language());
        ObjectModelMetadata::addObjectParameterValues('context.country', $values, $context->country ? $context->country : new Country());
        ObjectModelMetadata::addObjectParameterValues('context.currency', $values, $context->currency ? $context->currency : new Currency());
        ObjectModelMetadata::addObjectParameterValues('context.shop', $values, $context->shop ? $context->shop : new Shop(Configuration::get('PS_SHOP_DEFAULT')));
        ObjectModelMetadata::addObjectParameterValues('context.employee', $values, $context->employee ? $context->employee : new Employee());

        return $values;
    }

    /**
     * @param ParameterValues $values
     * @param ParameterValues $settings
     * @param $sourceParameters
     * @return mixed
     */
    public abstract function collectOutputParameterValues(ParameterValues $values, ParameterValues $settings, $sourceParameters);

    /**
     * @param int $id
     * @param ParameterValues $values
     * @param RulesManager $manager
     */
    public function register($id, ParameterValues $values, RulesManager $manager)
    {
        // no-op, to be overridden
    }

    /**
     * @param int $id
     * @param ParameterValues $values
     * @param RulesManager $manager
     */
    public function unregister($id, ParameterValues $values, RulesManager $manager)
    {
        // no-op, to be overridden
    }

    public function toJson()
    {
        $ret = [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'settings' => $this->getSettingsParameters()->toJson(),
        ];
        if ($this->getModulesRequirement()) {
            $ret['modules'] = $this->getModulesRequirement();
        }
        return $ret;
    }

    /**
     * @param $string
     * @return string
     */
    public function l($string)
    {
        // TODO:
        return $string;
    }
}