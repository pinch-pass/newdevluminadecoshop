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

use \Db;
use \DbQuery;
use \Exception;
use \DateTime;

class Licensing
{
    private $validLicense = false;
    private $usageRule = null;
    private $usageMeasure = null;

    /**
     * Licensing constructor.
     * @param $licenseKey
     */
    public function __construct($licenseKey)
    {
        if ($licenseKey) {
            try {
                $decoded = base64_decode($licenseKey);
                $arr = json_decode($decoded, true);
                if (is_array($arr) && isset($arr['d']) && isset($arr['e']) && isset($arr['m']) && $arr['m'] === 'conseqs') {
                    $expires = DateTime::createFromFormat('Y-m-d', $arr['e']);
                    $now = new DateTime();
                    if ($now < $expires) {
                        $this->validLicense = true;
                    }
                }
            } catch (Exception $ignored) {}
        }
    }

    /**
     * Returns true, if entity of given type can be created
     *
     * @param string $type
     * @return bool
     */
    public function canCreate($type)
    {
        if ($this->validLicense) {
            return true;
        }
        return $this->getUsage($type) < $this->getLimit($type);
    }

    /**
     * Returns true, if rule can be executed
     *
     * @return bool
     */
    public function canRunRule()
    {
        if ($this->validLicense) {
            return true;
        }
        return $this->getUsage('rule') <= $this->getLimit('rule');
    }

    /**
     * Returns true, if rule can be executed
     *
     * @return bool
     */
    public function canUpdateMeasure()
    {
        if ($this->validLicense) {
            return true;
        }
        return $this->getUsage('measure') <= $this->getLimit('measure');
    }

    /**
     * @return array
     */
    public function getLimits()
    {
        return [
            'rule' => 2,
            'measure' => 1
        ];
    }

    /**
     * @param string $type entity type
     * @return int
     */
    private function getUsage($type)
    {
        switch ($type) {
            case 'rule':
                return $this->getRuleUsage();
            case 'measure':
                return $this->getMeasureUsage();
            default:
                return PHP_INT_MAX;
        }
    }

    /**
     * @return int
     */
    private function getRuleUsage()
    {
        if (is_null($this->usageRule)) {
            try {
                $this->usageRule = (int)Db::getInstance()->getValue((new DbQuery())
                    ->select('count(1)')
                    ->from('conseqs_rule')
                );
            } catch (Exception $e) {
                $this->usageRule = PHP_INT_MAX;
            }
        }
        return $this->usageRule;
    }

    /**
     * @return int
     */
    private function getMeasureUsage()
    {
        if (is_null($this->usageMeasure)) {
            try {
                $this->usageMeasure = (int)Db::getInstance()->getValue((new DbQuery())
                    ->select('count(1)')
                    ->from('conseqs_measure')
                );
            } catch (Exception $e) {
                $this->usageMeasure = PHP_INT_MAX;
            }
        }
        return $this->usageMeasure;
    }

    /**
     * @param string $type entity type
     * @return int
     */
    private function getLimit($type)
    {
        $limits = $this->getLimits();
        if (isset($limits[$type])) {
            return (int)$limits[$type];
        }
        return 0;
    }
}
