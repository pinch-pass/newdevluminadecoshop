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

use Db;
use Logger;

class Cron
{

    /** @var Settings  */
    private $settings;

    /** @var string  */
    private $url;

    /** @var bool  */
    private $running;

    /** @var MeasureManager  */
    private $measureManager;

    /** @var RulesManager  */
    private $rulesManager;

    /** @var ErrorHandler  */
    private $errorHandler;

    /**
     * Cron constructor.
     * @param \Conseqs $module
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function __construct($module)
    {
        $this->errorHandler = $module->getErrorHandler();
        $this->settings = $module->getSettings();
        $this->measureManager = $module->getMeasureManager();
        $this->rulesManager = $module->getRulesManager();
        $this->url = $module->getUrl('cron', [
            'secure_key' => $this->settings->getCronSecret()
        ]);
        $this->running = false;
    }

    /**
     * @return bool
     * @throws \PrestaShopException
     */
    public function isActive()
    {
        return (time() - $this->getLastExecution()) < (1 * 24 * 60 * 60);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return bool
     * @throws \PrestaShopException
     */
    public function getLastExecution()
    {
        return $this->settings->getLastCronEvent();
    }

    /**
     * @throws \PrestaShopException
     * @throws \HTMLPurifier_Exception
     */
    public function process()
    {
        // limit run time to 25 seconds max
        $runTime = 25;
        $end = microtime(true) + $runTime;

        @set_time_limit(0);
        $this->settings->markCron();
        $this->running = false;
        $conn = Db::getInstance();
        $res = $conn->executeS("SELECT GET_LOCK('conseqs_cron', 5) as `success`");
        $this->running = ($res && $res[0] && intval($res[0]['success']));
        if ($this->running) {
            $this->errorHandler->handleErrors("Cron", [$this, 'run'], [$end], ['cron' => true]);
        }
        $this->releaseLock();
    }

    /**
     * @param $end
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function run($end)
    {
        // update measures values
        $this->measureManager->updateMeasuresValues($end);

        // process values
        $this->measureManager->processQueue($this->rulesManager, $end);
    }


    /**
     * @throws \PrestaShopException
     */
    private function releaseLock()
    {
        if ($this->running) {
            $this->running = false;
            Db::getInstance()->execute("SELECT RELEASE_LOCK('conseqs_cron')");
        }
    }

}
