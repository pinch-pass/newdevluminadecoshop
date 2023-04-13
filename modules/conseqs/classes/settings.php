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

class Settings
{
    const BACKEND_APP_URL = 'CONSEQS_BACK_APP_URL';
    const API_URL = 'CONSEQS_API_URL';
    const SETTINGS = 'CONSEQS_SETTINGS';
    const VERSION = 'CONSEQS_VERSION';
    const ACTIVATED = 'CONSEQS_ACTIVATED';
    const LICENSE = 'CONSEQS_LICENSE';
    const CHECK_VERSION = 'CONSEQS_CHECK_VERSION';
    const SALT = 'CONSEQS_SALT';
    const CRON_TS = 'CONSEQS_CRON_TS';
    const PREFIX = 'CONSEQS_PREFIX';

    const DAY = 86400;

    private $data;

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        $this->data = self::getDefaultSettings();
        $stored = Configuration::getGlobalValue(self::SETTINGS);
        if ($stored) {
            $stored = json_decode($stored, true);
            if ($stored) {
                $this->data = self::mergeSettings($this->data, $stored);
            }
        }
    }

    /**
     * @return array
     */
    private static function getDefaultSettings()
    {
        return [
        ];
    }

    /**
     * @return string
     * @throws \PrestaShopException
     */
    public function getLicense() {
        return Configuration::getGlobalValue(self::LICENSE);
    }

    /**
     * @param $license
     * @return bool
     * @throws \HTMLPurifier_Exception
     * @throws \PrestaShopException
     */
    public function setLicense($license) {
        return Configuration::updateGlobalValue(self::LICENSE, $license);
    }


    /**
     * @return bool
     * @throws \PrestaShopException
     */
    public function init()
    {
        $settings = static::getDefaultSettings();
        return $this->set($settings);
    }

    /**
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function reset()
    {
        return $this->remove() && $this->init();
    }

    /**
     * @return bool
     */
    public function isActivated()
    {
        return (bool)Configuration::getGlobalValue(self::ACTIVATED);
    }

    /**
     * @return bool
     * @throws \PrestaShopException
     */
    public function setActivated()
    {
        return Configuration::updateGlobalValue(self::ACTIVATED, 1);
    }

    /**
     * @param \Conseqs $module
     * @return string
     */
    public function getBackendAppUrl($module)
    {
        $url = Configuration::getGlobalValue(self::BACKEND_APP_URL);
        if (!$url) {
            $version = self::getUnderscoredVersion($module);
            $url = $module->getPath("views/js/back-{$version}.js");
        }
        return $url;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return Configuration::getGlobalValue(self::API_URL);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return Configuration::getGlobalValue(self::VERSION);
    }

    /**
     * @param string $version
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function setVersion($version)
    {
        Configuration::deleteByName(self::CHECK_VERSION);
        Configuration::updateGlobalValue(self::VERSION, $version);
    }

    /**
     * @return array
     */
    public function getCheckModuleVersion()
    {
        $ret = Configuration::getGlobalValue(self::CHECK_VERSION);
        if ($ret) {
            return json_decode($ret, true);
        }
        return ['ts' => null, 'version' => null, 'notes' => null];
    }

    /**
     * @param $version
     * @param $ts
     * @param $notes
     * @throws \PrestaShopException
     */
    public function setCheckModuleVersion($version, $ts, $notes)
    {
        Configuration::updateGlobalValue(self::CHECK_VERSION, json_encode([
            'ts' => $ts,
            'version' => $version,
            'notes' => $notes,
        ]));
    }

    /**
     * @return string
     * @throws \PrestaShopException
     */
    public function getSalt()
    {
        $salt = Configuration::getGlobalValue(self::SALT);
        if (! $salt) {
            $salt = Utils::getRandomData();
            Configuration::updateGlobalValue(self::SALT, $salt);
        }
        return $salt;
    }

    /**
     * @return string
     * @throws \PrestaShopException
     */
    public function getPrefix()
    {
        $prefix = Configuration::getGlobalValue(self::PREFIX);
        if (! $prefix) {
            $prefix = Utils::getRandomData();
            Configuration::updateGlobalValue(self::PREFIX, $prefix);
        }
        return $prefix;
    }


    /**
     * @return string
     * @throws \PrestaShopException
     */
    public function getCronSecret()
    {
        return md5('cron' . $this->getSalt());
    }

    /**
     * @return int
     * @throws \PrestaShopException
     */
    public function getLastCronEvent()
    {
        return (int)Configuration::get(self::CRON_TS);
    }

    /**
     * @throws \HTMLPurifier_Exception
     * @throws \PrestaShopException
     */
    public function markCron()
    {
        Configuration::updateValue(self::CRON_TS, time());
    }

    /**
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function remove()
    {
        $this->data = null;
        Configuration::deleteByName(self::CHECK_VERSION);
        Configuration::deleteByName(self::SETTINGS);
        return true;
    }

    /**
     * @param null $path
     * @return array|mixed
     */
    public function get($path = null)
    {
        $value = $this->data;
        if (is_null($path)) {
            return $value;
        }
        foreach ($path as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                die('CONSEQS: setting not found: ' . implode($path, '>'));
            }
        }
        return $value;
    }

    /**
     * @param $value
     * @return bool
     * @throws \PrestaShopException
     */
    public function set($value)
    {
        $this->data = $value;
        return Configuration::updateGlobalValue(self::SETTINGS, json_encode($value));
    }

    /**
     * @param array $left
     * @param array $right
     * @return array
     */
    private static function mergeSettings($left, $right)
    {
        $ret = [];
        foreach ($left as $key => $value) {
            if (isset($right[$key])) {
                if (is_array($value) && self::isAssoc($value)) {
                    $value = self::mergeSettings($value, $right[$key]);
                } else {
                    $value = $right[$key];
                }
            }
            $ret[$key] = $value;
        }
        return $ret;
    }

    /**
     * @param array $arr
     * @return bool
     */
    private static function isAssoc(array $arr)
    {
        if (array() === $arr)
            return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param $module
     * @return mixed
     */
    private static function getUnderscoredVersion($module)
    {
        return str_replace('.', '_', $module->version);
    }

}
