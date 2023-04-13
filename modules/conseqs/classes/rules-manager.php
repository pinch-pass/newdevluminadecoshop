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

use PrestaShopDatabaseException;
use PrestaShopException;
use Hook;
use Db;
use DbQuery;

class RulesManager
{
    /** @var RulesManager */
    static $instance;

    /** @var \Conseqs */
    private $module;

    /** @var array */
    private $triggers;

    /** @var array */
    private $actions;

    /** @var array */
    private $conditions;

    private $requirements = null;

    /** @var array list of rules currently in loading status */
    private $loading = [];


    /**
     * @param \Conseqs $module
     * @return RulesManager
     * @throws PrestaShopException
     */
    public static function getInstance($module = null)
    {
        if (! static::$instance) {
            if ($module) {
                static::$instance = new static($module);
            } else {
                throw new PrestaShopException('Rules Manager instance not set');
            }
        }
        return static::$instance;
    }

    /**
     * TriggerManager constructor.
     * @param \Conseqs $module
     */
    private function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * @param array $rule
     * @param array $triggerParameters
     * @param RuntimeModifier $runtimeModifier
     */
    public function runRule($rule, $triggerParameters, RuntimeModifier $runtimeModifier)
    {
        $id = (int) $rule['id_rule'];
        $name = "Rule #$id";
        $sourceParameters = $triggerParameters;
        $sourceParameters['rule'] = $rule;
        $this->module->getErrorHandler()->handleErrors($name, [$this, 'runRuleInternal'], [$rule, $sourceParameters, $runtimeModifier], ['id_rule' => $id]);
    }

    /**
     * @param $rule
     * @param $triggerParameters
     * @param RuntimeModifier $runtimeModifier
     * @throws PrestaShopException
     * @throws \Adapter_Exception
     * @throws \PrestaShopDatabaseException
     * @throws \ReflectionException
     */
    public function runRuleInternal($rule, $triggerParameters, RuntimeModifier $runtimeModifier)
    {
        if (! $this->module->getLicensing()->canRunRule()) {
            throw new PrestaShopException("Invalid license key");
        }

        $ruleId = (int)$rule['id_rule'];

        // 1) resolve trigger type
        $trigger = $this->getTrigger($rule['trigger_type']);

        // 2) resolve action type
        $action = $this->getAction($rule['action_type']);

        // 3) prepare settings
        $triggerSettingsValues = new ParameterValues($trigger->getSettingsParameters());
        $actionSettingsValues = new ParameterValues($action->getSettingsParameters());
        $settingRawValues = Db::getInstance()->executeS((new DbQuery())
            ->select('*')
            ->from('conseqs_rule_settings')
            ->where('id_rule = ' . $ruleId)
        );
        foreach ($settingRawValues as $row) {
            $key = $row['key'];
            $value = $row['value'];
            $target = $row['target'];
            if ($target === 'action') {
                $actionSettingsValues->addParameter($key, $value);
            } else if ($target === 'trigger') {
                $triggerSettingsValues->addParameter($key, $value);
            }
        }
        // 4) check if trigger should be executed
        if (! $trigger->shouldTrigger($triggerSettingsValues, $triggerParameters)) {
            return;
        }

        // 5) get trigger output parameters
        $triggerOutput = $trigger->getOutputParameterValues($triggerSettingsValues, $triggerParameters);

        // 6) execute conditions
        if (! $this->executeConditions($ruleId, $trigger->getOutputParameters($triggerSettingsValues), $triggerOutput)) {
            return;
        }

        $this->markRun($ruleId);

        // 7) bind output parameters to action input parameters
        $actionInputValues = $this->getActionParameterValues($ruleId, $triggerOutput, $action->getInputParameters($actionSettingsValues));

        // 8) execute action with action input parameters
        $action->execute($actionSettingsValues, $actionInputValues, $triggerOutput, $runtimeModifier);

        // 9) execute all chained actions
        Hook::exec('actionAfterConseqsRule', [
            'ruleId' => $ruleId,
            'triggerOutput' => $triggerOutput,
        ]);
    }

    /**
     * @param $ruleId
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function markRun($ruleId)
    {
        Db::getInstance()->update('conseqs_rule', [
            'last_executed' => date('Y-m-d H:i:s'),
        ], "id_rule = $ruleId");
    }

    /**
     * Registers new trigger
     *
     * @param string|int $ruleId
     * @param string $name
     * @param string $triggerType
     * @param array $rawTriggerSettingsValues
     * @param string $actionType
     * @param array $rawActionSettingsValues
     * @param array $bindings
     * @param array $conditionGroups
     * @return bool
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function save($ruleId, $name, $triggerType, $rawTriggerSettingsValues, $actionType, $rawActionSettingsValues, $bindings, $conditionGroups)
    {
        // get trigger
        $trigger = $this->getTrigger($triggerType);
        $triggerSettingsValues = $this->getParameterValues($rawTriggerSettingsValues, $trigger->getSettingsParameters());

        // get action
        $action = $this->getAction($actionType);
        $actionSettingsValues = $this->getParameterValues($rawActionSettingsValues, $action->getSettingsParameters());

        $conn = Db::getInstance();
        $isNew = $ruleId === "new";
        if ($isNew) {
            if (!$this->module->getLicensing()->canCreate('rule')) {
                throw new PrestaShopException('Limit reached');
            }
            // create rule
            if (!$conn->insert('conseqs_rule', [
                'name' => pSQL($name),
                'trigger_type' => pSQL($triggerType),
                'action_type' => pSQL($actionType),
                'active' => true,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s'),
            ])) {
                throw new PrestaShopException("Can't create rule: " . $conn->getMsgError());
            }
            $ruleId = (int)$conn->Insert_ID();
        } else {
            // update rule
            $ruleId = (int)$ruleId;
            $exists = $conn->getValue('SELECT 1 FROM '._DB_PREFIX_."conseqs_rule WHERE id_rule = $ruleId");
            if (! $exists) {
                throw new PrestaShopException("Rule with id $ruleId not found");
            }
            if (! $conn->update('conseqs_rule', [
                'name' => pSQL($name),
                'trigger_type' => pSQL($triggerType),
                'action_type' => pSQL($actionType),
                'date_upd' => date('Y-m-d H:i:s'),
            ], "id_rule = $ruleId")) {
                throw new PrestaShopException("Can't update rule $ruleId: " . $conn->getMsgError());
            };
            $this->deleteRuleSettings($ruleId);
        }

        // save trigger settings
        $this->saveSettingValues($ruleId, 'trigger', $triggerSettingsValues);

        // save action settings
        $this->saveSettingValues($ruleId, 'action', $actionSettingsValues);

        // save bindings
        $this->saveBindings($ruleId, $bindings);

        // save conditions
        $this->saveConditions($ruleId, $conditionGroups, $trigger->getOutputParameters($triggerSettingsValues));

        // register trigger
        $this->requirements = [
            'hooks' => [],
            'measures' => [],
        ];
        $trigger->register($ruleId, $triggerSettingsValues, $this);

        // register all trigger hooks
        foreach ($this->requirements['hooks'] as $hook) {
            $this->module->registerHook($hook);
            $hookId = (int)Hook::getIdByName($hook);
            $conn->insert('conseqs_rule_hook', [
                'id_rule' => $ruleId,
                'id_hook' => $hookId
            ]);
        }
        foreach ($this->requirements['measures'] as $measureId) {
            $conn->insert('conseqs_rule_measure', [
                'id_rule' => $ruleId,
                'id_measure' => (int)$measureId
            ]);
            if ($isNew) {
                $this->module->getMeasureManager()->initQueueForRule($measureId, $ruleId);
            }
        }
        $this->requirements = null;

        return true;
    }

    /**
     * @param $ruleId
     * @return bool
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function delete($ruleId)
    {
        $ruleId = (int)$ruleId;
        $this->deleteRuleSettings($ruleId);
        Db::getInstance()->delete('conseqs_rule_measure_queue', "id_rule = $ruleId");
        return Db::getInstance()->delete('conseqs_rule', "id_rule = $ruleId");
    }

    /**
     * @param $ruleId
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function deleteRuleSettings($ruleId)
    {
        $ruleId = (int)$ruleId;
        $conn = Db::getInstance();
        $conn->delete('conseqs_rule_settings', "id_rule = $ruleId");
        $conn->delete('conseqs_rule_hook', "id_rule = $ruleId");
        $conn->delete('conseqs_rule_measure', "id_rule = $ruleId");
        $conn->delete('conseqs_rule_binding', "id_rule = $ruleId");
        $conn->delete('conseqs_rule_condition_group', "id_rule = $ruleId");
        $conn->delete('conseqs_rule_condition', "id_rule = $ruleId");
        $conn->delete('conseqs_rule_condition_argument', "id_rule = $ruleId");
    }

    /**
     * Unregisters trigger
     *
     * @param int $id
     */
    public function unregister($id)
    {
        // TODO
    }

    /**
     * @param string $hookName
     * @throws PrestaShopException
     */
    public function registerHook($hookName)
    {
        if (!$this->requirements) {
            throw new PrestaShopException("Invariant exception: registerHook called from wrong place");
        }
        $this->requirements['hooks'][] = $hookName;
    }

    /**
     * @param int $measureId
     * @throws PrestaShopException
     */
    public function registerMeasure($measureId)
    {
        if (!$this->requirements) {
            throw new PrestaShopException("Invariant exception: registerMeasure called from wrong place");
        }
        $this->requirements['measures'][] = $measureId;
    }

    /**
     * @param string $hookName
     * @param array $args
     * @return mixed
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function dispatchHook($hookName, $args)
    {
        if ($this->module->getLicensing()->canRunRule()) {
            $hookId = (int)Hook::getIdByName(str_replace('hook', '', $hookName));
            if ($hookId) {
                $sql = (new DbQuery())
                    ->select('r.*')
                    ->from('conseqs_rule', 'r')
                    ->innerJoin('conseqs_rule_hook', 'rh', 'r.id_rule = rh.id_rule')
                    ->where('rh.id_hook = ' . $hookId)
                    ->where('r.active');
                $rules = Db::getInstance()->executeS($sql);
                if (is_array($rules)) {
                    $modifier = new RuntimeModifier($args[0]);
                    foreach ($rules as $rule) {
                        $this->runRule($rule, [
                            'type' => 'hook',
                            'hook' => $hookName,
                            'parameters' => $args[0],
                        ], $modifier);
                    }
                    return $modifier->returnValue();
                }
            }
        }
    }

    /**
     * @param $ruleId
     * @param $measure
     * @param $args
     * @throws \PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function dispatchMeasure($ruleId, $measure, $args)
    {
        if ($ruleId && $this->module->getLicensing()->canRunRule()) {
            $rules = $this->getActiveRules();
            if (isset($rules[$ruleId])) {
                $rule = $rules[$ruleId];
                $modifier = new RuntimeModifier();
                $this->runRule($rule, [
                    'type' => 'measure',
                    'measure' => $measure,
                    'parameters' => $args
                ], $modifier);
            }
        }
    }

    /**
     * @return array
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function getActiveRules()
    {
        static $rules;
        if (is_null($rules)) {
            $rules = [];
            $sql = (new DbQuery())
                ->select('r.*')
                ->from('conseqs_rule', 'r')
                ->where('r.active');
            $data = Db::getInstance()->executeS($sql);
            if ($data) {
                foreach ($data as $row) {
                    $id = (int)$row['id_rule'];
                    $rules[$id] = $row;
                }
            }
        }
        return $rules;
    }

    /**
     * @param string $name
     * @return Trigger
     * @throws PrestaShopException
     */
    public function getTrigger($name)
    {
        $triggers = $this->getTriggers();
        if (!isset($triggers[$name])) {
            throw new PrestaShopException("Trigger $name does not exists");
        }
        return $triggers[$name];
    }

    /**
     * @return Trigger[]
     * @throws PrestaShopException
     */
    public function getTriggers()
    {
        if (!$this->triggers) {
            $files = glob(__DIR__ . "/../triggers/*.php");
            $this->triggers = [];
            foreach ($files as $path) {
                $name = basename($path);
                if ($name === 'index.php') {
                    continue;
                }
                $code = lcfirst(Utils::camelize(str_replace('.php', '', $name)));
                $className = '\Conseqs\Triggers\\' . Utils::camelize($code);
                if (!class_exists($className)) {
                    require_once($path);
                }
                if (!class_exists($className)) {
                    throw new PrestaShopException("$path does not contain $className");
                }
                /** @var Trigger $instance */
                $instance = new $className();
                if ($instance->getCompatibility()->isCompatible()) {
                    $this->triggers[$code] = $instance;
                }
            }
            $ret = Hook::exec('actionGetConseqsTriggers', [], null, true);
            if ($ret) {
                foreach ($ret as $moduleKey => $triggers) {
                    foreach ($triggers as $trigger) {
                        $trigger->addModuleRequirement($moduleKey);
                    }
                    $this->triggers = array_merge($this->triggers, $triggers);
                }
            }
        }
        return $this->triggers;
    }

    /**
     * @param string $name
     * @return Action
     * @throws PrestaShopException
     */
    public function getAction($name)
    {
        $actions = $this->getActions();
        if (!isset($actions[$name])) {
            throw new PrestaShopException("Action $name does not exists");
        }
        return $actions[$name];
    }

    /**
     * @return Action[]
     * @throws PrestaShopException
     */
    public function getActions()
    {
        if (!$this->actions) {
            $files = glob(__DIR__ . "/../actions/*.php");
            $this->actions = [];
            foreach ($files as $path) {
                $name = basename($path);
                if ($name === 'index.php') {
                    continue;
                }
                $code = lcfirst(Utils::camelize(str_replace('.php', '', $name)));
                $className = '\Conseqs\Actions\\' . Utils::camelize($code);
                if (!class_exists($className)) {
                    require_once($path);
                }
                if (!class_exists($className)) {
                    throw new PrestaShopException("$path does not contain $className");
                }
                /** @var Action $instance */
                $instance = new $className();
                if ($instance->getCompatibility()->isCompatible()) {
                    $this->actions[$code] = $instance;
                }
            }
            $ret = Hook::exec('actionGetConseqsActions', [], null, true);
            if ($ret) {
                foreach ($ret as $moduleKey => $actions) {
                    foreach ($actions as $action) {
                        $action->addModuleRequirement($moduleKey);
                    }
                    $this->actions = array_merge($this->actions, $actions);
                }
            }
        }
        return $this->actions;
    }


    /**
     * @param string $name
     * @return Condition
     * @throws PrestaShopException
     */
    public function getCondition($name)
    {
        $conditions = $this->getConditions();
        if (!isset($conditions[$name])) {
            throw new PrestaShopException("Condition $name does not exists");
        }
        return $conditions[$name];
    }

    /**
     * @return Condition[]
     * @throws PrestaShopException
     */
    public function getConditions()
    {
        if (!$this->conditions) {
            $files = glob(__DIR__ . "/../conditions/*.php");
            $this->triggers = [];
            foreach ($files as $path) {
                $name = basename($path);
                if ($name === 'index.php') {
                    continue;
                }
                $code = lcfirst(Utils::camelize(str_replace('.php', '', $name)));
                $className = '\Conseqs\Conditions\\' . Utils::camelize($code);
                if (!class_exists($className)) {
                    require_once($path);
                }
                if (!class_exists($className)) {
                    throw new PrestaShopException("$path does not contain $className");
                }
                $this->conditions[$code] = new $className();
            }
        }
        return $this->conditions;
    }

    /**
     * @param array $rawValues
     * @param ParameterDefinitions $definitions
     * @return ParameterValues
     * @throws PrestaShopException
     */
    public function getParameterValues($rawValues, ParameterDefinitions $definitions)
    {
        $values = new ParameterValues($definitions);
        foreach ($rawValues as $key => $value) {
            $values->addParameter($key, $value);
        }
        return $values;
    }

    /**
     * @param int $ruleId
     * @param string $target
     * @param ParameterValues $values
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    private function saveSettingValues($ruleId, $target, ParameterValues $values)
    {
        $insertData = [];
        foreach ($values->serialize() as $key => $value) {
            $insertData[] = [
                'id_rule' => $ruleId,
                'target' => $target,
                'key' => $key,
                'value' => pSQL($value, true)
            ];
        }

        if ($insertData) {
            $conn = Db::getInstance();
            if (!$conn->insert('conseqs_rule_settings', $insertData)) {
                throw new PrestaShopException("Can't save $target parameters: " . $conn->getMsgError());
            }
        }

    }

    /**
     * @param int $ruleId
     * @param array $bindings
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    private function saveBindings($ruleId, $bindings)
    {
        $insertData = [];
        foreach ($bindings as $key => $binding) {
            $insertData[] = [
                'id_rule' => $ruleId,
                'key' => $key,
                'method' => pSQL($binding['method']),
                'value' => pSQL($binding['value'], true),
            ];
        }

        if ($insertData) {
            $conn = Db::getInstance();
            if (!$conn->insert('conseqs_rule_binding', $insertData)) {
                throw new PrestaShopException("Can't save bindings: " . $conn->getMsgError());
            }
        }

    }

    /**
     * @param int $ruleId
     * @param $conditionGroups
     * @param ParameterDefinitions $fields
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    private function saveConditions($ruleId, $conditionGroups, ParameterDefinitions $fields)
    {
        $conn = Db::getInstance();
        foreach ($conditionGroups as $group) {
            $groupData = [ 'id_rule' => $ruleId ];
            if (! $conn->insert('conseqs_rule_condition_group', $groupData)) {
                throw new PrestaShopException("Can't save condition group: " . $conn->getMsgError());
            }
            $groupId = (int)$conn->Insert_ID();
            foreach ($group as $cond) {
                $insertData = [
                    'id_rule' => $ruleId,
                    'id_condition_group' => $groupId,
                    'key' => $cond['key'],
                    'not' => !!$cond['not'],
                    'condition' => pSQL($cond['condition']),
                ];
                if (!$conn->insert('conseqs_rule_condition', $insertData)) {
                    throw new PrestaShopException("Can't save condition: " . $conn->getMsgError());
                }
                $conditionId = (int)$conn->Insert_ID();
                $condition = $this->getCondition($cond['condition']);
                $argDefs = $condition->getParameters($fields->getParameter($cond['key']))->getDefinitions();
                $argVals = $cond['arguments'];
                if (count($argDefs) !== count($argVals)) {
                    throw new PrestaShopException("Cant' save arguments: invalid count");
                }
                $details = [];
                for ($i = 0; $i < count($argDefs); $i++) {
                    $argDef = $argDefs[$i];
                    $argVal = $argVals[$i];
                    $details[] = [
                        'id_condition' => $conditionId,
                        'id_rule' => $ruleId,
                        'position' => $i,
                        'value' => pSQL($argDef->convertToString($argVal)),
                   ];
                }
                if ($details) {
                    if (!$conn->insert('conseqs_rule_condition_argument', $details)) {
                        throw new PrestaShopException("Can't save condition details: " . $conn->getMsgError());
                    }
                }
            }
        }

    }

    /**
     * @param int $ruleId
     * @param ParameterValues $triggerOutput
     * @param ParameterDefinitions $actionInputParameters
     * @return ParameterValues
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    private function getActionParameterValues($ruleId, ParameterValues $triggerOutput, ParameterDefinitions $actionInputParameters)
    {
        $values = new ParameterValues($actionInputParameters);
        $sql = (new DbQuery())
            ->select('*')
            ->from('conseqs_rule_binding')
            ->where('id_rule = ' . $ruleId);
        $bindings = Db::getInstance()->executeS($sql);
        foreach ($bindings as $row) {
            $method = $row['method'];
            $parameter = $row['key'];
            $value = $row['value'];
            switch ($method) {
                case 'constant':
                    $values->addParameter($parameter, $value);
                    break;
                case 'input':
                    $values->addParameter($parameter, $triggerOutput->getValueAsString($value));
                    break;
                case 'interpolate':
                    $values->addParameter($parameter, Utils::interpolateValues($value, $triggerOutput));
                    break;
                default:
                    throw new PrestaShopException("Invalid binding method $method");
            }
        }
        return $values;
    }

    /**
     * @param int $ruleId
     * @param ParameterDefinitions $definitions
     * @param ParameterValues $triggerOutput
     * @return boolean
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    private function executeConditions($ruleId, ParameterDefinitions $definitions, ParameterValues $triggerOutput)
    {
        $groups = $this->loadConditions($ruleId);

        if (! $groups) {
            return true;
        }

        // conditions groups uses OR operator -- return true, if at least one group evaluates to true
        foreach ($groups as $group) {
            if ($this->executeConditionGroup($group, $definitions, $triggerOutput)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Conditions within group uses AND operator -- return true if ALL conditions evaluates to true
     *
     * @param $group
     * @param ParameterDefinitions $definitions
     * @param ParameterValues $triggerOutput
     * @return bool
     * @throws PrestaShopException
     */
    private function executeConditionGroup($group, ParameterDefinitions $definitions, ParameterValues $triggerOutput)
    {
        foreach ($group as $cond) {
            /** @var Condition $condition */
            $condition = $cond['condition'];
            $rawParams = $cond['params'];
            $field = $cond['field'];
            $expect = !$cond['not'];
            $fieldType = $definitions->getParameter($field);
            $argDefs = $condition->getParameters($fieldType);
            $arguments = new ParameterValues($argDefs);
            foreach ($rawParams as $id => $param) {
                $arguments->addParameter($id, $param);
            }
            $fieldValue = $triggerOutput->getValue($field);
            if ($expect != $condition->execute($fieldType, $fieldValue, $arguments, $triggerOutput)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $ruleId
     * @return array
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function loadConditions($ruleId)
    {
        $sql = (new DbQuery())
            ->select('c.id_condition_group, c.id_condition, c.condition, c.key as field, c.`not` as `not`, d.value, (d.id_condition IS NOT NULL) as `arg_exists`')
            ->from('conseqs_rule_condition_group', 'g')
            ->innerJoin('conseqs_rule_condition', 'c', 'c.id_condition_group = g.id_condition_group')
            ->leftJoin('conseqs_rule_condition_argument', 'd', 'c.id_condition = d.id_condition')
            ->where('g.id_rule = ' . $ruleId)
            ->orderBy('c.id_condition_group, c.id_condition, d.position');
        $ret = Db::getInstance()->executeS($sql);
        $groups = [];
        foreach ($ret as $row) {
            $groupId = (int)$row['id_condition_group'];
            $conditionId = (int)$row['id_condition'];
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = [];
            }
            if (!isset($groups[$groupId][$conditionId])) {
                $groups[$groupId][$conditionId] = [
                    'cond' => $row['condition'],
                    'condition' => $this->getCondition($row['condition']),
                    'not' => !!$row['not'],
                    'field' => $row['field'],
                    'params' => []
                ];
            }
            if (!!$row['arg_exists']) {
                $value = $row['value'];
                $groups[$groupId][$conditionId]['params'][] = $value;
            }
        }
        return $groups;
    }

    /**
     * @return \Conseqs
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Loads rule with given ID
     *
     * @param $id
     * @param bool $external
     * @param bool $json
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function loadRule($id, $external=false, $json=true)
    {
        if (isset($this->loading[$id])) {
            throw new PrestaShopException('Loop detected');
        }
        $this->loading[$id] = $id;
        try {
            return $this->loadRuleInternal($id, $external, $json);
        } finally {
            unset($this->loading[$id]);
        }
    }

    /**
     * @param $id
     * @param bool $external
     * @param bool $json
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function loadRuleInternal($id, $external, $json)
    {
        $sql = (new DbQuery())
            ->select('r.*')
            ->from('conseqs_rule', 'r')
            ->where("r.id_rule = $id");
        $conn = Db::getInstance();
        $row = $conn->getRow($sql);
        if (!$row || $row['id_rule'] != $id) {
            throw new PrestaShopException('Rule not found');
        }

        $trigger = $this->getTrigger($row['trigger_type']);
        $triggerSettings = new ParameterValues($trigger->getSettingsParameters());

        $action = $this->getAction($row['action_type']);
        $actionSettings = new ParameterValues($action->getSettingsParameters());

        $settingRawValues = $conn->executeS((new DbQuery())
            ->select('*')
            ->from('conseqs_rule_settings')
            ->where('id_rule = ' . $id));
        foreach ($settingRawValues as $r) {
            if ($r['target'] === 'trigger') {
                $triggerSettings->addParameter($r['key'], $r['value']);
            }
            if ($r['target'] === 'action') {
                $actionSettings->addParameter($r['key'], $r['value']);
            }
        }
        $triggerOutput = $trigger->getOutputParameters($triggerSettings);
        $actionInput = $action->getInputParameters($actionSettings);

        $bindings = [];
        $vals = new ParameterValues($actionInput);
        $sql = (new DbQuery())
            ->select('*')
            ->from('conseqs_rule_binding')
            ->where('id_rule = ' . $id);
        foreach ($conn->executeS($sql) as $r) {
            $method = $r['method'];
            $parameter = $r['key'];
            $value = $r['value'];
            if ($method === 'constant') {
                $vals->addParameter($parameter, $value);
                $value = $vals->getValue($parameter);
            }
            $bindings[$parameter] = [
                'method' => $method,
                'value' => $value
            ];
        }

        $conditions = [];
        foreach ($this->loadConditions($id) as $group) {
            $gr = [];
            foreach ($group as $condArr) {
                /** @var Condition $cond */
                $cond = $condArr['condition'];
                $params = $cond->getParameters($triggerOutput->getParameter($condArr['field']));
                $paramValues = new ParameterValues($params);
                foreach ($condArr['params'] as $key => $param) {
                    $paramValues->addParameter((int)$key, $param);
                }
                $gr[] = [
                    'condition' => $condArr['cond'],
                    'not' => $condArr['not'],
                    'key' => $condArr['field'],
                    'arguments' => $json ? (array)$paramValues->toJson() : $paramValues
                ];
            }
            $conditions[] = $gr;
        }

        return [
            'name' => $row['name'],
            'rule' => [
                'id' => $id,
                'trigger' => $row['trigger_type'],
                'triggerSettings' => $json ? $triggerSettings->toJson($external) : $triggerSettings,
                'action' => $row['action_type'],
                'actionSettings' => $json ? $actionSettings->toJson($external) : $actionSettings,
                'bindings' => $bindings,
                'conditions' => $conditions
            ],
            'triggerOutput' => $json ? $triggerOutput->toJson() : $triggerOutput,
            'actionInput' => $json ? $actionInput->toJson() : $actionInput
        ];
    }

}