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

abstract class Action
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
     * @return null | string[]
     */
    public function getAllowedTriggers()
    {
        return null;
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     */
    public abstract function getInputParameters(ParameterValues $settings);


    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @param RuntimeModifier $runtimeModifier
     * @return mixed
     */
    public abstract function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier);


    /**
     * @return array
     */
    public function toJson()
    {
        $ret = [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'settings' => $this->getSettingsParameters()->toJson(),
        ];
        $triggers = $this->getAllowedTriggers();
        if (! is_null($triggers)) {
            $ret['triggers'] = $triggers;
        }
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