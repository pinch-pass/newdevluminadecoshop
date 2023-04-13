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

use Conseqs\ParameterDefinitions;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\RulesManager;
use Conseqs\Trigger;
use Db;
use DbQuery;
use PrestaShopDatabaseException;
use PrestaShopException;

class AfterConseqsRule extends Trigger
{
    const RULE_ID = 'ruleId';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('After Conseqs Rule');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This trigger is executed immediately after another conseqs rule. You can use it to chain your rules');
    }

    /**
     * @return ParameterDefinitions
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getSettingsParameters()
    {
        $settings = new ParameterDefinitions();
        $settings->addParameter(static::RULE_ID, new SelectParameterDefinition($this->l('Rule'), static::getRules()));
        return $settings;
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterDefinitions $definitions
     * @throws PrestaShopException
     */
    public function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions)
    {
        $ruleId = (int)$settings->getValue(static::RULE_ID);
        $ruleManager = RulesManager::getInstance();
        $rule = $ruleManager->loadRule($ruleId, false, false);
        /** @var ParameterDefinitions $triggerOutput */
        $triggerOutput = $rule['triggerOutput'];
        foreach ($triggerOutput->getParameters() as $parameter) {
            $definitions->addParameter($parameter, $triggerOutput->getParameter($parameter));
        }
    }

    /**
     * @param ParameterValues $values
     * @param ParameterValues $settings
     * @param $sourceParameters
     */
    public function collectOutputParameterValues(ParameterValues $values, ParameterValues $settings, $sourceParameters)
    {
        $parameters = $sourceParameters['parameters'];
        $values->setFrom($parameters['triggerOutput']);
    }

    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $manager->registerHook('actionAfterConseqsRule');
    }

    /**
     * @param ParameterValues $settings
     * @param array $sourceParameters
     * @return bool
     * @throws PrestaShopException
     */
    public function shouldTrigger(ParameterValues $settings, $sourceParameters)
    {
        $parameters = $sourceParameters['parameters'];
        $rule = $sourceParameters['rule'];
        $thisRuleId = (int)$rule['id'];
        $actualRuleId = (int)$parameters['ruleId'];
        $settingsRuleId = (int)$settings->getValue(static::RULE_ID);
        if ($thisRuleId === $settingsRuleId) {
            return false;
        }
        return $actualRuleId === $settingsRuleId;
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private static function getRules()
    {
        $sql = (new DbQuery())
            ->select('r.id_rule, r.name')
            ->from('conseqs_rule', 'r');
        $data = Db::getInstance()->executeS($sql);
        $ret = [];
        foreach ($data as $row) {
            $id = (int)$row['id_rule'];
            $name = $row['name'];
            $ret[$id] = $name;
        }
        return $ret;
    }

}