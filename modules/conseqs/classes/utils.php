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

use Context;
use Currency;
use Db;
use DbQuery;
use Group;
use Language;
use PrestaShopDatabaseException;
use PrestaShopException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Shop;

class Utils
{

    /**
     * @return array
     */
    public static function getEmailTemplates()
    {
        static $emails = [];
        if (!$emails) {
            foreach (static::getEmailTemplatePaths() as $basePath) {
                if (is_dir($basePath)) {
                    $directory = new RecursiveDirectoryIterator($basePath);
                    $iterator = new RecursiveIteratorIterator($directory);
                    foreach ($iterator as $path) {
                        $file = basename($path);
                        if (preg_match("/^.+\.html$/i", $file)) {
                            $template = str_replace(".html", "", $file);
                            $emails[$template][] = (string)$path;
                        } else if (preg_match("/^.+\.txt$/i", $file)) {
                            $template = str_replace(".txt", "", $file);
                            $emails[$template][] = (string)$path;
                        }
                    }
                }
            }
        }
        return $emails;
    }

    /**
     * @param $path
     * @return string[]
     */
    public static function getEmailTemplateParameters($path)
    {
        $content = file_get_contents($path);
        return static::getPlaceholders($content);
    }

    /**
     * @param $content
     * @return string[]
     */
    public static function getPlaceholders($content)
    {
        $matches = [];
        if (preg_match_all("/({[\.a-zA-Z0-9_]+})/", $content, $matches)) {
            return $matches[0];
        }
        return [];
    }


    /**
     * @param string $string
     * @param ParameterValues $values
     * @return string
     * @throws PrestaShopException
     */
    public static function interpolateValues($string, $values)
    {
        foreach (Utils::getPlaceholders($string) as $parameter) {
            $key = str_replace('}', '', str_replace('{', '', $parameter));
            if ($values->hasValue($key)) {
                $value = $values->getValueAsString($key);
                $string = str_replace($parameter, $value, $string);
            }
        }
        return $string;
    }

    /**
     * @return array
     */
    public static function getEmailTemplatePaths()
    {
        $rootDir = rtrim(_PS_ROOT_DIR_, DIRECTORY_SEPARATOR);
        $themeDir = rtrim(_PS_THEME_DIR_, DIRECTORY_SEPARATOR);
        return [
            $rootDir . '/mails/',
            $themeDir . '/mails/'
        ];
    }

    /**
     * @param $input
     * @param string $delimiter
     * @return string
     */
    public static function decamelize($input, $delimiter = '_')
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', $delimiter . '$0', $input)), $delimiter);
    }

    /**
     * @param $input
     * @param string $delim
     * @param bool $capitalizeFirstLetter
     * @return string
     */
    public static function camelize($input, $delim = '-', $capitalizeFirstLetter = false)
    {
        $exploded_str = explode($delim, $input);
        $exploded_str_camel = array_map('ucwords', $exploded_str);
        $ret = implode('', $exploded_str_camel);
        return $capitalizeFirstLetter ? $ret : lcfirst($ret);
    }

    /**
     * @param $input
     * @param string $delim
     * @return string
     */
    public static function humanize($input, $delim = '_')
    {
        $input = strtolower($input);
        if ($input == 'date_add') {
            return 'Date created';
        }
        if ($input == 'date_upd') {
            return 'Date updated';
        }
        $exploded = array_map(function ($word) {
            $word = trim($word);
            if (in_array($word, ['id', 'ip', 'http'])) {
                return strtoupper($word);
            }

            return ucfirst($word);
        }, explode($delim, $input));
        return implode(' ', $exploded);
    }


    /**
     * @return array
     * @throws PrestaShopException
     */
    public static function getLanguages()
    {
        static $languages = [];
        if (! $languages) {
            foreach (Language::getLanguages(false) as $l) {
                $languages[(int)$l['id_lang']] = $l['name'];
            }
        }
        return $languages;
    }

    /**
     * @return array
     * @throws PrestaShopException
     */
    public static function getGroups()
    {
        static $groups = [];
        if (! $groups) {
            foreach (Group::getGroups(Context::getContext()->language->id) as $g) {
                $groups[(int)$g['id_group']] = $g['name'];
            }
        }
        return $groups;
    }

    /**
     * @return array
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public static function getCurrencies()
    {
        static $currencies = [];
        if (! $currencies) {
            foreach (Currency::getCurrencies(false, false) as $c) {
                $currencies[(int)$c['id_currency']] = $c['name'];
            }
        }
        return $currencies;
    }

    /**
     * @return array
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public static function getShops()
    {
        static $shops = [];
        if (! $shops) {
            foreach (Shop::getShops(true) as $c) {
                $shops[(int)$c['id_shop']] = $c['name'];
            }
        }
        return $shops;
    }

    /**
     * @param bool $force
     * @return array
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public static function getMeasures($force=false)
    {
        static $measures = [];
        if ($force || ! $measures) {
            $sql = (new DbQuery())
                ->select('*')
                ->from('conseqs_measure');
            $data = Db::getInstance()->executeS($sql);
            foreach ($data as $row) {
                $code = $row['code'];
                $measures[$code] = [
                    'id'  => (int)$row['id_measure'],
                    'code'  => $code,
                    'name'  => $row['name'],
                    'sql'  => $row['sql'],
                    'keyField'  => $row['key_field'],
                    'valueField'  => $row['value_field'],
                    'refresh'  => (int)$row['refresh'],
                    'timestamp' => (int)$row['ts'],
                ];
            }
        }
        return $measures;
    }

    /**
     * @return string
     */
    public static function getRandomData()
    {
        try {
            if (function_exists('random_bytes')) {
                return bin2hex(random_bytes(16));
            }
            if (function_exists('openssl_random_pseudo_bytes')) {
                return bin2hex(openssl_random_pseudo_bytes(16));
            }
        } catch (\Exception $e) {}
        return bin2hex(substr(hex2bin(sha1(uniqid('rd' . rand(), true))), 0, 16));
    }

    /**
     * @param int $id
     * @return array
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public static function getMeasureById($id)
    {
        $id = (int)$id;
        $measures = static::getMeasures();
        foreach ($measures as $measure) {
            if ($measure['id'] === $id) {
                return $measure;
            }
        }
        throw new PrestaShopException(sprintf('Measure with id %s not found', $id));
    }

    /**
     * @param $sql
     * @return mixed
     */
    public static function toInternalSql($sql)
    {
        return str_replace("<PREFIX>", _DB_PREFIX_, $sql);
    }

    /**
     * @param $sql
     * @return mixed
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function toExternalSql($sql)
    {
        if (preg_match_all('/\b' . _DB_PREFIX_ . '\w+\b/', $sql, $matches)) {
            if ($matches && isset($matches[0]) && $matches[0]) {
                $tables = "'" . implode("', '", array_map('pSQL', array_unique($matches[0]))) . "'";
                $ret = Db::getInstance()->executeS("SELECT table_name FROM INFORMATION_SCHEMA.TABLES WHERE table_schema=database() AND table_name in ($tables)");
                if ($ret) {
                    $len = strlen(_DB_PREFIX_);
                    foreach ($ret as $row) {
                        $table = $row['table_name'];
                        $replace = '<PREFIX>' . substr($table, $len);
                        $sql = str_replace($table, $replace, $sql);
                    }
                }
            }
        }
        return $sql;
    }

    /**
     * @return string
     */
    public static function getLogDirectory()
    {
        $base = rtrim(_PS_ROOT_DIR_, '/') . '/';
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $dir = $base . 'var/logs/';
        } else {
            $dir = $base. 'log/';
        }
        if (is_dir($dir)) {
            return $dir;
        }
        @mkdir($dir);
        if (is_dir($dir)) {
            return $dir;
        }

        $dir = rtrim(_PS_CACHE_DIR_, '/') . '/';
        return is_dir($dir) ? $dir : $base;
    }
}