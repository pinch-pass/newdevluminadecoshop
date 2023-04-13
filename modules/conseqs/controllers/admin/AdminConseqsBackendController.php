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

use Conseqs\Action;
use Conseqs\Condition;
use Conseqs\ObjectModelMetadata;
use Conseqs\ParameterValues;
use Conseqs\Trigger;
use Conseqs\Utils;
use Conseqs\Parameters\SelectParameterDefinition;

class AdminConseqsBackendController extends ModuleAdminController
{
    /** @var \Conseqs */
    public $module;

    /**
     * AdminConseqsBackendController constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        parent::__construct();
        $this->display = 'view';
        $this->bootstrap = false;
        $this->addCSS($this->getPath('views/css/back-0_5_0.css'));
        $this->addJs($this->module->getSettings()->getBackendAppUrl($this->module));
    }

    /**
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function display()
    {
        $settings = $this->module->getSettings();
        $cron = $this->module->getCron();
        $this->display_footer = false;
        $platform = 'prestashop';
        $platformVersion = _PS_VERSION_;
        if (defined('_TB_VERSION_')) {
            $platform = 'thirtybees';
            $platformVersion = _TB_VERSION_;
        }

        ObjectModelMetadata::refreshObjectModels();

        $conseqs = [
            'apiUrl' => $this->context->link->getAdminLink('AdminConseqsBackend'),
            'license' => $this->module->getSettings()->getLicense(),
            'limits' => $this->module->getLicensing()->getLimits(),
            'prefix' => $settings->getPrefix(),
            'activated' => $settings->isActivated(),
            'version' => $this->module->version,
            'cronUrl' => $cron->getUrl(),
            'cronActive' => $cron->isActive(),
            'platform' => $platform,
            'platformVersion' => $platformVersion,
            'versionCheck' => $settings->getCheckModuleVersion(),
            'settings' => $settings->get(),
            'translations' => $this->module->getBackTranslations(),
            'actions' => $this->getActions(),
            'triggers' => $this->getTriggers(),
            'conditions' => $this->getConditions(),
            'images' => rtrim(__PS_BASE_URI__, '/') . '/modules/conseqs/views/img/',
            'modules' => $this->getEnabledModules(),
        ];

        $apiUrl = $settings->getApiUrl();
        if ($apiUrl) {
            $conseqs['storeUrl'] = $apiUrl;
        }

        $this->context->smarty->assign([
            'help_link' => null,
            'title' => $this->l('Consequences'),
            'conseqs' => $conseqs,
        ]);
        parent::display();
    }

    /**
     * @param string $tpl_name
     * @return object|Smarty_Internal_Template
     */
    public function createTemplate($tpl_name)
    {
        if ($this->viewAccess() && $tpl_name === 'content.tpl') {
            $path = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/backend.tpl';
            return $this->context->smarty->createTemplate($path, $this->context->smarty);
        }
        return parent::createTemplate($tpl_name);
    }

    /**
     * @param $path
     * @return string
     */
    private function getPath($path)
    {
        return $this->module->getPath($path);
    }

    /**
     *
     */
    public function ajaxProcessCommand()
    {
        $error = null;
        $result = null;
        try {
            $result = $this->dispatchCommand(Tools::getValue('cmd'));
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        $this->reply($error, $result);
        die();
    }

    /**
     * @param $cmd
     * @return mixed
     * @throws Exception
     */
    private function dispatchCommand($cmd)
    {
        $payload = json_decode(Tools::getValue('payload'), true);
        switch ($cmd) {
            case 'setLatestVersion':
                return $this->setLatestVersion($payload);
            case 'setSettings':
                return $this->setSettings($payload);
            case 'accountActivated':
                return $this->accountActivated();
            case 'setLicense':
                return $this->setLicense($payload);
            case 'getActionParameters':
                return $this->getActionParameters($payload);
            case 'getTriggerOutputParameters':
                return $this->getTriggerOutputParameters($payload);
            case 'getConditionsArguments'    :
                return $this->getConditionsArguments($payload);
            case 'setActive':
                return $this->setActive($payload);
            case 'saveRule':
                return $this->saveRule($payload);
            case 'getRules':
                return $this->getRules();
            case 'getMeasures':
                return $this->getMeasures($payload);
            case 'loadRule':
                return $this->loadRule($payload);
            case 'deleteRule':
                return $this->deleteRule($payload);
            case 'deleteMeasure':
                return $this->deleteMeasure($payload);
            case 'updateMeasureValues':
                return $this->updateMeasureValues($payload);
            case 'testSql':
                return $this->testSql($payload);
            case 'saveMeasure':
                return $this->saveMeasure($payload);
            case 'getErrorLogs':
                return $this->getErrorLogs($payload);
            case 'clearErrorLogs':
                return $this->clearErrorLogs();
            default:
                throw new PrestaShopException("Unknown command $cmd");
        }
    }


    /**
     * @param $data
     * @return bool
     * @throws PrestaShopException
     */
    private function setLatestVersion($data)
    {
        if (!isset($data['version'])) {
            throw new PrestaShopException('Version not set');
        }
        if (!isset($data['ts'])) {
            throw new PrestaShopException('Timestamp not set');
        }
        $this->module->getSettings()->setCheckModuleVersion($data['version'], $data['ts'], $data['notes']);
        return true;
    }

    /**
     * @param $data
     * @return bool
     * @throws PrestaShopException
     */
    private function setSettings($data)
    {
        if (!isset($data['settings'])) {
            throw new PrestaShopException('Settings not set');
        }
        $this->module->getSettings()->set($data['settings']);
        return true;
    }

    /**
     * @return bool
     * @throws PrestaShopException
     */
    private function accountActivated()
    {
        $this->module->getSettings()->setActivated();
        return true;
    }

    /**
     * @param $data
     * @return bool
     * @throws Exception
     */
    private function setLicense($data)
    {
        if (! isset($data['license'])) {
            throw new Exception('License not set');
        }
        $this->module->getSettings()->setLicense($data['license']);
        return true;
    }


    /**
     * @param array $data
     *
     * @return object
     * @throws PrestaShopException
     */
    private function getTriggerOutputParameters($data)
    {
        return $this->getTriggerOutputParametersRaw($data)->toJson();
    }

    /**
     * @param $data
     * @return \Conseqs\ParameterDefinitions
     * @throws PrestaShopException
     */
    private function getTriggerOutputParametersRaw($data)
    {
        if (! isset($data['trigger']) || !is_string($data['trigger'])) {
            throw new PrestaShopException('trigger not set');
        }
        if (! isset($data['settings']) || !is_array($data['settings'])) {
            throw new PrestaShopException('settings not set');
        }
        $manager = $this->module->getRulesManager();
        $trigger = $manager->getTrigger($data['trigger']);
        $settings = new ParameterValues($trigger->getSettingsParameters());
        foreach ($data['settings'] as $key => $value) {
            $settings->addParameter($key, $value);
        }
        return $trigger->getOutputParameters($settings);
    }

    /**
     * @param $data
     * @return array
     * @throws PrestaShopException
     */
    private function getConditionsArguments($data)
    {
        $ret = [];
        $manager = $this->module->getRulesManager();
        $triggerOutputParameters = $this->getTriggerOutputParametersRaw($data);
        foreach ($data['conditions'] as $row) {
            if (! isset($row['condition'])) {
                throw new PrestaShopException('condition not set');
            }
            if (! isset($row['field'])) {
                throw new PrestaShopException('field not set');
            }
            $condition = $manager->getCondition($row['condition']);
            $parameter = $triggerOutputParameters->getParameter($row['field']);
            $row['arguments'] = $condition->getParameters($parameter)->toJson();
            $ret[] = $row;
        }
        return $ret;
    }

    /**
     * @param $data
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function deleteRule($data)
    {
        if (!isset($data['id'])) {
            throw new PrestaShopException('id not set');
        }
        $manager = $this->module->getRulesManager();
        $id = (int)$data['id'];
        return $manager->delete($id);
    }

    /**
     * @param $data
     * @return bool
     * @throws PrestaShopException
     */
    private function deleteMeasure($data)
    {
        if (!isset($data['id'])) {
            throw new PrestaShopException('id not set');
        }
        $manager = $this->module->getMeasureManager();
        $id = (int)$data['id'];
        return $manager->delete($id);
    }

    /**
     * @param $data
     * @throws PrestaShopException
     */
    private function updateMeasureValues($data)
    {
        if (!isset($data['id'])) {
            throw new PrestaShopException('id not set');
        }
        $manager = $this->module->getMeasureManager();
        $id = (int)$data['id'];
        $measure = Utils::getMeasureById($id);
        if (! $measure) {
            throw new PrestaShopException('Measure not found');
        }
        $manager->updateMeasureValues($measure);

        // help process queue
        $manager->processQueue($this->module->getRulesManager(), microtime(true) + 10, $id);
    }

    /**
     * @param $data
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function loadRule($data)
    {
        if (! isset($data['id'])) {
            throw new PrestaShopException('id not set');
        }
        $manager = $this->module->getRulesManager();
        SelectParameterDefinition::useStrictMode(false);

        $id = (int)$data['id'];
        $external = isset($data['external']) ? true : false;

        return $manager->loadRule($id, $external);
    }


    /**
     * @param $data
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function setActive($data)
    {
        if (! isset($data['id'])) {
            throw new PrestaShopException('id not set');
        }
        if (! isset($data['active'])) {
            throw new PrestaShopException('active not set');
        }
        $id = (int)$data['id'];
        $active = !!$data['active'];
        return !!Db::getInstance()->update('conseqs_rule', [
            'active' => $active
        ], "id_rule = $id");
    }

    /**
     * @param $data
     * @return bool
     * @throws PrestaShopException
     */
    private function saveRule($data)
    {
        $manager = $this->module->getRulesManager();
        if (! isset($data['id'])) {
            throw new PrestaShopException('id not set');
        }
        if (! isset($data['rule'])) {
            throw new PrestaShopException('rule not set');
        }
        if (! isset($data['name'])) {
            throw new PrestaShopException('name not set');
        }
        $id = $data['id'];
        if ($id === 'new' && !$this->module->getLicensing()->canCreate('rule')) {
            throw new PrestaShopException('Limit reached');
        }

        $rule = $data['rule'];
        $name = $data['name'];
        return $manager->save(
            $id,
            $name,
            $rule['trigger'],
            $rule['triggerSettings'],
            $rule['action'],
            $rule['actionSettings'],
            $rule['bindings'],
            $rule['conditions']
        );
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getRules()
    {
        $conn = Db::getInstance();
        $sql = (new DbQuery())
            ->select('*, UNIX_TIMESTAMP(last_executed) AS lastExecuted, UNIX_TIMESTAMP(date_add) AS dateAdd, UNIX_TIMESTAMP(date_upd) AS dateUpd')
            ->from('conseqs_rule')
            ->orderBy('id_rule');
        $res = $conn->executeS($sql);
        if (! $res) {
            return [];
        }
        return array_map(function($row) {
           return [
               'id' => (int)$row['id_rule'],
               'name' => $row['name'],
               'trigger' => $row['trigger_type'],
               'action' => $row['action_type'],
               'active' => !!$row['active'],
               'lastExecuted' => $row['lastExecuted'] ? (int)$row['lastExecuted'] : null,
               'dateAdd' => (int)$row['dateAdd'],
               'dateUpd' => (int)$row['dateUpd'],
           ];
        }, $res);
    }

    /**
     * @param $payload
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getMeasures($payload)
    {
        $external = isset($payload['external']) ? true : false;
        $conn = Db::getInstance();
        $data = $conn->executeS('SELECT id_measure, COUNT(1) AS `cnt` FROM '._DB_PREFIX_.'conseqs_rule_measure GROUP BY id_measure');
        $usages = $this->toKeyValue($data, 'id_measure', 'cnt');
        $data = $conn->executeS("
            SELECT v.id_measure, count(1) as `cnt`
            FROM "._DB_PREFIX_."conseqs_measure s
            INNER JOIN "._DB_PREFIX_."conseqs_measure_value v ON (s.id_measure = v.id_measure and s.ts = v.ts)
            GROUP BY v.id_measure
        ");
        $cardinality = $this->toKeyValue($data, 'id_measure', 'cnt');
        return array_map(function($measure) use ($usages, $cardinality, $external) {
            $id = $measure['id'];
            $measure['usage'] = isset($usages[$id]) ? $usages[$id] : 0;
            $measure['cardinality'] = isset($cardinality[$id]) ? $cardinality[$id] : 0;
            if ($external) {
                $measure['sql'] = Utils::toExternalSql($measure['sql']);
            }
            return $measure;
        }, array_values(Utils::getMeasures()));
    }

    /**
     * @param $array
     * @param $key
     * @param $value
     * @return array
     */
    private function toKeyValue($array, $key, $value)
    {
        $ret = [];
        if ($array) {
            foreach ($array as $row) {
                $id = (int)$row[$key];
                $cnt = (int)$row[$value];
                $ret[$id] = $cnt;
            }
        }
        return $ret;
    }

    /**
     * @param $data
     * @return array
     * @throws PrestaShopException
     */
    private function testSql($data)
    {
        if (! isset($data['payload']) || !is_string($data['payload'])) {
            throw new PrestaShopException('sql not set');
        }
        $sql = Utils::toInternalSql(base64_decode($data['payload'], false));
        if (! $sql) {
            throw new PrestaShopException("Failed to decode payload");
        }
        $normalized = $this->normalizeSql($sql);
        $conn = Db::getInstance();
        try {
            $ret = $conn->query($normalized);
            if (! $ret) {
                throw new PrestaShopException('Error');
            }

            $row = $ret->fetch();
            $columns = [];
            if ($row) {
                $columns = array_values(array_filter(array_keys($row), function($column) {
                    return !is_int($column);
                }));
                foreach ($columns as $column) {
                    if (! preg_match("/^[a-zA-Z0-9_]+$/", $column)) {
                        return [
                            'type' => 'failure',
                            'error' => "SQL contains complex expression without an alias. Fix it by changing expression to: $column AS alias"
                        ];
                    }
                }
            }
            $ret->closeCursor();
            $cardinality = (int)$conn->getValue("SELECT COUNT(1) FROM ($normalized) AS `inner`");
            return [
                'type' => 'success',
                'cardinality' => $cardinality,
                'columns' => $columns,
            ];
        } catch (Exception $e) {
            $error = $conn->getMsgError();
            if (! $error) {
                $error = $e->getMessage();
            }
            return [
               'type' => 'failure',
               'error' => $error
            ];
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws PrestaShopException
     */
    private function saveMeasure($data)
    {
        $manager = $this->module->getMeasureManager();
        foreach (['name', 'code', 'refresh', 'sql', 'keyField', 'valueField'] as $field) {
            if (!isset($data[$field])) {
                throw new PrestaShopException("$field is not set");
            }
        }
        $refresh = (int)$data['refresh'];
        if (! $refresh) {
            throw new PrestaShopException("Invalid refresh value");
        }
        $sql = Utils::toInternalSql(base64_decode($data['sql'], false));
        if (! $sql) {
            throw new PrestaShopException("Failed to decode payload");
        }
        // check that sql is valid
        $this->normalizeSql($sql);

        return $manager->saveMeasure(
            $data['code'],
            $data['name'],
            $sql,
            $data['keyField'],
            $data['valueField'],
            $refresh
        );
    }
    /**
     * @param array $data
     *
     * @return object
     * @throws PrestaShopException
     */
    private function getActionParameters($data)
    {
        if (! isset($data['action']) || !is_string($data['action'])) {
            throw new PrestaShopException('action not set');
        }
        if (! isset($data['settings']) || !is_array($data['settings'])) {
            throw new PrestaShopException('settings not set');
        }
        $manager = $this->module->getRulesManager();
        $action = $manager->getAction($data['action']);
        $settings = new ParameterValues($action->getSettingsParameters());
        foreach ($data['settings'] as $key => $value) {
            $settings->addParameter($key, $value);
        }
        $definitions = $action->getInputParameters($settings);
        return $definitions->toJson();
    }

    /**
     * @param $data
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getErrorLogs($data)
    {
        if (! isset($data['page'])) {
            throw new PrestaShopException('page not set');
        }
        $page = (int)$data['page'];
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $sql = (new DbQuery())
            ->select('e.*, UNIX_TIMESTAMP(e.date) AS `ts`, r.name as `rule_name`')
            ->from('conseqs_errors', 'e')
            ->leftJoin('conseqs_rule', 'r', '(r.id_rule = e.id_rule)')
            ->orderBy('e.date DESC')
            ->limit($pageSize, $offset);
        $conn = Db::getInstance();
        $data = $conn->executeS($sql);
        $errors = [];
        if ($data) {
            foreach ($data as $row) {
                $errors[] = [
                    'id' => (int)$row['id_error'],
                    'date' => (int)$row['ts'],
                    'ruleId' => (int)$row['id_rule'],
                    'ruleName' => $row['rule_name'],
                    'cron' => (int)$row['cron'],
                    'message' => $row['message'],
                    'file' => $row['file'],
                    'line' => (int)$row['line'],
                    'stacktrace' => $row['stacktrace'],
                ];
            }
        }
        $total = (int)$conn->getValue((new DbQuery())->select('COUNT(1)')->from('conseqs_errors'));
        return [
            'total' => $total,
            'errors' => $errors
        ];
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function clearErrorLogs()
    {
        return Db::getInstance()->delete('conseqs_errors');
    }

    /**
     * @param $error
     * @param $result
     */
    private function reply($error, $result)
    {
        if ($error) {
            echo json_encode(['success' => false, 'error' => $error]);
        } else {
            echo json_encode(['success' => true, 'result' => $result]);
        }
    }

    /**
     * @return array
     * @throws PrestaShopException
     */
    private function getTriggers()
    {
        $manager = $this->module->getRulesManager();
        $triggers = $manager->getTriggers();
        return array_map(function (Trigger $trigger) {
            return $trigger->toJson();
        }, $triggers);
    }

    /**
     * @return array
     * @throws PrestaShopException
     */
    private function getActions()
    {
        $manager = $this->module->getRulesManager();
        $actions = $manager->getActions();
        return array_map(function (Action $action) {
            return $action->toJson();
        }, $actions);
    }

    /**
     * @return array
     * @throws PrestaShopException
     */
    private function getConditions()
    {
        $manager = $this->module->getRulesManager();
        return array_map(function (Condition $cond) {
            return $cond->toJson();
        }, $manager->getConditions());
    }

    /**
     * @param $sql
     * @return string|string[]|null
     * @throws PrestaShopException
     */
    private function normalizeSql($sql)
    {
        $normalized = strtolower($sql);
        $normalized = preg_replace('/\s+/', " ", $normalized);
        $normalized = trim($normalized);

        if (strpos($normalized, 'select ') !== 0) {
            throw new PrestaShopException("Statement must start with SELECT");
        }
        if (strpos($normalized, ';') > -1) {
            throw new PrestaShopException("Statement must not contain semicolon");
        }
        return $normalized;
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getEnabledModules()
    {
        $ret = [];
        foreach (Module::getModulesInstalled() as $module) {
            if (Module::isEnabled($module['name'])) {
                $ret[] = $module['name'];
            }
        }
        return $ret;
    }

}
