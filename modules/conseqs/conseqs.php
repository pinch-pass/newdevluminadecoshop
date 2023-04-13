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

require_once __DIR__ . '/app-translation.php';
require_once __DIR__ . '/classes/runtime-modifier.php';
require_once __DIR__ . '/classes/parameter-definition.php';
require_once __DIR__ . '/classes/parameter-values.php';
require_once __DIR__ . '/classes/parameter-definitions.php';
require_once __DIR__ . '/classes/object-model-metadata.php';
require_once __DIR__ . '/classes/utils.php';
require_once __DIR__ . '/classes/settings.php';
require_once __DIR__ . '/classes/trigger.php';
require_once __DIR__ . '/classes/action.php';
require_once __DIR__ . '/classes/condition.php';
require_once __DIR__ . '/classes/rules-manager.php';
require_once __DIR__ . '/classes/measure-manager.php';
require_once __DIR__ . '/classes/compatibility.php';
require_once __DIR__ . '/classes/lazy-object-model.php';
require_once __DIR__ . '/classes/migration-utils.php';
require_once __DIR__ . '/classes/cron.php';
require_once __DIR__ . '/classes/error-handler.php';
require_once __DIR__ . '/classes/licensing.php';
require_once __DIR__ . '/classes/parameters/boolean-parameter-definition.php';
require_once __DIR__ . '/classes/parameters/date-parameter-definition.php';
require_once __DIR__ . '/classes/parameters/int-parameter-definition.php';
require_once __DIR__ . '/classes/parameters/number-parameter-definition.php';
require_once __DIR__ . '/classes/parameters/select-parameter-definition.php';
require_once __DIR__ . '/classes/parameters/string-parameter-definition.php';

class Conseqs extends Module
{
    /** @var \Conseqs\Licensing */
    private $licensing = null;

    /** @var \Conseqs\Settings */
    private $settings = null;

    /** @var \Conseqs\MeasureManager */
    private $measureManager = null;

    /** @var \Conseqs\ErrorHandler */
    private $errorHandler = null;

    /**
     * Conseqs constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->name = 'conseqs';
        $this->tab = 'administration';
        $this->version = '0.5.0';
        $this->author = 'DataKick';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Consequences');
        $this->description = $this->l('');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * @param bool $createTables
     * @return bool
     * @throws Adapter_Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function install($createTables=true)
    {
        return (
            parent::install() &&
            $this->registerHook('actionRegisterKronaAction') &&
            $this->installDb($createTables) &&
            $this->installTab()
        );
    }

    /**
     * @param bool $dropTables
     * @return bool
     * @throws Adapter_Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function uninstall($dropTables=true)
    {
        $this->removeTab();
        $this->uninstallDb($dropTables);
        return parent::uninstall();
    }

    /**
     * @return bool
     * @throws Adapter_Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function reset() {
        return (
            $this->uninstall(false) &&
            $this->install(false)
        );
    }

    /**
     * @throws PrestaShopException
     */
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminConseqsBackend'));
    }

    /**
     * @param bool $check
     * @return \Conseqs\Settings
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getSettings($check = true)
    {
        if (!$this->settings) {
            $this->settings = new \Conseqs\Settings();
            $version = $this->settings->getVersion();
            if ($check && $version != $this->version) {
                if (version_compare($version, $this->version, '<')) {
                    $this->migrate();
                }
                $this->settings->setVersion($this->version);
            }
        }
        return $this->settings;
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function migrate()
    {
        $utils = new \Conseqs\MigrationUtils(Db::getInstance());
        if (! $utils->columnExists(_DB_PREFIX_ . 'conseqs_rule_condition', 'not')) {
            $this->executeSqlScript('condition-add-not-column', false);
        }
        if (! $utils->tableExists(_DB_PREFIX_ . 'conseqs_measure')) {
            $this->executeSqlScript('add-measure-tables', false);
        }
        if (! $utils->tableExists(_DB_PREFIX_ . 'conseqs_errors')) {
            $this->executeSqlScript('add-error-table', false);
        }
    }

    /**
     * @return \Conseqs\RulesManager
     * @throws PrestaShopException
     */
    public function getRulesManager()
    {
        return \Conseqs\RulesManager::getInstance($this);
    }

    /**
     * @return \Conseqs\MeasureManager
     */
    public function getMeasureManager()
    {
        if (!$this->measureManager) {
            $this->measureManager = new \Conseqs\MeasureManager($this);
        }
        return $this->measureManager;
    }

    /**
     * @return array
     */
    public function getBackTranslations()
    {
        $transl = new \Conseqs\AppTranslation($this);
        return $transl->getBackTranslations();
    }

    /**
     * @return int
     * @throws Adapter_Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminConseqsBackend';
        $tab->module = $this->name;
        $tab->id_parent = $this->getTabParent();
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Consequences';
        }
        return $tab->add();
    }

    /**
     * @return bool
     * @throws Adapter_Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function removeTab()
    {
        $tabId = Tab::getIdFromClassName('AdminConseqsBackend');
        if ($tabId) {
            $tab = new Tab($tabId);
            return $tab->delete();
        }
        return true;
    }

    /**
     * @return int
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getTabParent()
    {
        $parent = Tab::getIdFromClassName('AdminTools');
        if ($parent !== false) {
            return $parent;
        }
        return 0;
    }

    /**
     * @param string $relative
     * @return string
     */
    public function getPath($relative)
    {
        $uri = rtrim($this->getPathUri(), '/');
        $rel = ltrim($relative, '/');
        return "$uri/$rel";
    }

    /**
     * @param $create
     * @return bool
     */
    private function installDb($create)
    {
        if (! $create) {
            return true;
        }
        return $this->executeSqlScript('install');
    }

    /**
     * @param $drop
     * @return bool
     */
    private function uninstallDb($drop)
    {
         if (! $drop) {
            return true;
        }
        return $this->executeSqlScript('uninstall', false);
    }

    /**
     * @param $script
     * @param bool $check
     * @return bool
     */
    public function executeSqlScript($script, $check = true)
    {
        $file = dirname(__FILE__) . '/sql/' . $script . '.sql';
        if (!file_exists($file)) {
            return false;
        }
        $sql = file_get_contents($file);
        if (!$sql) {
            return false;
        }
        $sql = str_replace(['PREFIX_', 'ENGINE_TYPE', 'CHARSET_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_, 'utf8'], $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $statement) {
            $stmt = trim($statement);
            if ($stmt) {
                try {
                    if (!Db::getInstance()->execute($stmt)) {
                        PrestaShopLogger::addLog("conseqs: migration script $script: $stmt: error");
                        if ($check) {
                            return false;
                        }
                    }
                } catch (Exception $e) {
                    try {
                        PrestaShopLogger::addLog("conseqs: migration script $script: $stmt: exception");
                    } catch (Exception $e) {}
                    if ($check) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @return array
     */
    public function hookActionRegisterKronaAction()
    {
        return [
            'generic_1' => [
                'title'   => 'Generic action 1',
                'message' => 'This action can be triggered by conseqs module. Please change title and this message',
            ],
            'generic_2' => [
                'title'   => 'Generic action 2',
                'message' => 'This action can be triggered by conseqs module. Please change title and this message',
            ],
            'generic_3' => [
                'title'   => 'Generic action 3',
                'message' => 'This action can be triggered by conseqs module. Please change title and this message',
            ],
        ];
    }

    /**
     * @param $name
     * @param $args
     * @return mixed
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __call($name, $args)
    {
        if (strpos($name, 'hook') === 0) {
            if ($this->getLicensing()->canRunRule()) {
                return $this->getRulesManager()->dispatchHook($name, $args);
            }
        }
    }

    /**
     * @return \Conseqs\Licensing
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getLicensing()
    {
        if (! $this->licensing) {
            $this->licensing = new \Conseqs\Licensing($this->getSettings()->getLicense());
        }
        return $this->licensing;
    }

    /**
     * @return \Conseqs\ErrorHandler
     */
    public function getErrorHandler()
    {
        if (! $this->errorHandler) {
            $this->errorHandler = new \Conseqs\ErrorHandler();
        }
        return $this->errorHandler;
    }

    /**
     * @return \Conseqs\Cron
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getCron()
    {
        return new Conseqs\Cron($this);
    }

    /**
     * @param $controller
     * @param array $params
     * @return string
     * @throws PrestaShopException
     */
    public function getUrl($controller, $params=[])
    {
        return $this->context->link->getModuleLink($this->name, $controller, $params);
    }

}
