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

if (php_sapi_name() === 'cli') {
    require_once __DIR__ . '/../../../../config/config.inc.php';
    try {
        $module = Module::getInstanceByName('conseqs');
        $module->getCron()->process();
    } catch (Exception $e) {
        die("Failed to start cron: ".$e);
    }
    exit;
}

class ConseqsCronModuleFrontController extends ModuleFrontController
{

    /** @var Conseqs */
    public $module;

    /**
     * @throws HTMLPurifier_Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();
        if (Tools::getValue('secure_key') == $this->module->getSettings()->getCronSecret()) {
            @set_time_limit(0);

            $interactive = !!Tools::getValue('sync');
            if (! $interactive) {
                ob_start();

                echo 'conseqs cron';
                header('Connection: close');
                header('Content-Length: ' . ob_get_length());

                // flush
                if (ob_get_length() > 0) {
                    ob_end_flush();
                }
                flush();

                // abort
                ignore_user_abort(true);
                if (function_exists('fastcgi_finish_request')) {
                    fastcgi_finish_request();
                }
            }
            $this->module->getCron()->process();
            die();
        }
        die('error');
    }

}
