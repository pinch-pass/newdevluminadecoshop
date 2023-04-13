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
use PrestaShopException;

class MeasureManager
{
    /** @var \Conseqs */
    private $module;

    /**
     * TriggerManager constructor.
     * @param \Conseqs $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * @param string $code
     * @param $name
     * @param string $sql
     * @param string $keyField
     * @param string $valueField
     * @param int $refresh
     * @return int
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function saveMeasure($code, $name, $sql, $keyField, $valueField, $refresh)
    {
        $conn = Db::getInstance();
        $refresh = (int)$refresh;
        $measureId = (int)$conn->getValue('SELECT id_measure FROM '._DB_PREFIX_."conseqs_measure WHERE code='" . pSQL($code) . "'");
        if ($measureId) {
            if (! $conn->update('conseqs_measure', [
                'sql' => pSQL($sql),
                'name' => pSQL($name),
                'key_field' => pSQL($keyField),
                'value_field' => pSQL($valueField),
                'refresh' => $refresh,
                'date_upd' => date('Y-m-d H:i:s'),
            ], "id_measure = $measureId")) {
                throw new PrestaShopException("Can't update measure $measureId: " . $conn->getMsgError());
            }
            return $measureId;
        }
        if (!$this->module->getLicensing()->canCreate('measure')) {
            throw new PrestaShopException('Limit reached');
        }
        if (!$conn->insert('conseqs_measure', [
            'code' => pSQL($code),
            'name' => pSQL($name),
            'sql' => pSQL($sql),
            'key_field' => pSQL($keyField),
            'value_field' => pSQL($valueField),
            'refresh' => $refresh,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ])) {
            throw new PrestaShopException("Can't create measure: " . $conn->getMsgError());
        }
        return (int)$conn->Insert_ID();
    }


    /**
     * Calculates values for specific measure
     *
     * @param $measure
     * @return bool
     * @throws PrestaShopException
     */
    public function updateMeasureValues($measure)
    {
        if (! $this->module->getLicensing()->canUpdateMeasure()) {
            throw new PrestaShopException("Invalid license key");
        }
        $conn = Db::getInstance();
        $sql = $measure['sql'];
        $ts = time();
        $id = (int)$measure['id'];
        $valueTable = _DB_PREFIX_ . 'conseqs_measure_value';
        $queueTable = _DB_PREFIX_ . 'conseqs_rule_measure_queue';
        $ruleMapTable = _DB_PREFIX_ . 'conseqs_rule_measure';
        $ruleTable = _DB_PREFIX_ . 'conseqs_rule';
        $key = $measure['keyField'];
        $value = $measure['valueField'];

        // calculate and insert new measure values
        $insert = "INSERT IGNORE INTO $valueTable(`id_measure`, `ts`, `key`, `value`)\nSELECT $id, $ts, `$key`, `$value`\nFROM ($sql) AS `inner`";
        if (! $conn->execute($insert)) {
            throw new PrestaShopException("Failed to update measure values for measure $id: " . $conn->getMsgError());
        }

        $prevTs = (int)$measure['timestamp'];
        if ($prevTs) {
            // 1) add new items
            $conn->execute("
              INSERT INTO $queueTable(`id_rule`, `id_measure`, `key`, `old_value`, `new_value`)
              SELECT r.id_rule, v.id_measure, v.key, null, v.value
              FROM $valueTable v
              INNER JOIN $ruleMapTable r ON (v.id_measure = r.id_measure)
              INNER JOIN $ruleTable rt ON (rt.id_rule = r.id_rule AND rt.active)
              LEFT JOIN $valueTable p ON (p.id_measure = v.id_measure AND p.`key` = v.`key` AND p.`ts` = $prevTs)
              WHERE v.id_measure = $id AND v.ts = $ts AND p.id_measure IS NULL
              ON DUPLICATE KEY UPDATE `new_value` = v.value
            ");
            // 2) find changed items
            $conn->execute("
              INSERT INTO $queueTable(`id_rule`, `id_measure`, `key`, `old_value`, `new_value`)
              SELECT r.id_rule, v.id_measure, v.key, p.value, v.value
              FROM $valueTable v
              INNER JOIN $ruleMapTable r ON (v.id_measure = r.id_measure)
              INNER JOIN $ruleTable rt ON (rt.id_rule = r.id_rule AND rt.active)
              INNER JOIN $valueTable p ON (p.id_measure = v.id_measure AND p.`key` = v.`key` AND p.`ts` = $prevTs)
              WHERE v.id_measure = $id AND v.ts = $ts AND v.value != p.value
              ON DUPLICATE KEY UPDATE `new_value` = v.value
            ");
            // 3) find deleted items
            $conn->execute("
              INSERT INTO $queueTable(`id_rule`, `id_measure`, `key`, `old_value`, `new_value`)
              SELECT r.id_rule, p.id_measure, p.key, p.value, null
              FROM $valueTable p
              INNER JOIN $ruleMapTable r ON (p.id_measure = r.id_measure)
              INNER JOIN $ruleTable rt ON (rt.id_rule = r.id_rule AND rt.active)
              LEFT JOIN $valueTable v ON (p.id_measure = v.id_measure AND p.`key` = v.`key` AND v.`ts` = $ts)
              WHERE p.id_measure = $id AND p.ts = $prevTs AND v.id_measure IS NULL 
              ON DUPLICATE KEY UPDATE `new_value` = null
            ");
        } else {
            // first run, we need to add everything into queue
            $this->initQueueForRule($id);
        }

        // delete data from queue that changed back to original value
        $conn->delete('conseqs_rule_measure_queue', "id_measure=$id AND old_value = new_value");

        // update timestamp information about new values
        $conn->update('conseqs_measure', [ 'ts' => $ts ], "id_measure = $id");

        // delete data for previous timestamps
        $conn->delete('conseqs_measure_value', "id_measure=$id AND ts!=$ts");
        
        return true;
    }


    /**
     * Update values for all measures in the system
     *
     * @param float $end
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function updateMeasuresValues($end)
    {
        $ts = time();
        foreach (Utils::getMeasures() as  $measure) {
            if (microtime(true) > $end) {
                // bail if we exceeded run time
                return;
            }
            $threshold = (int)$measure['timestamp'] + ((int)$measure['refresh'] * 3600);
            if ($ts > $threshold) {
                $this->updateMeasureValues($measure);
            }
        }
    }

    /**
     * Process measure queue
     *
     * @param RulesManager $rulesManager
     * @param float $end
     * @param int $measureId
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function processQueue(RulesManager $rulesManager, $end, $measureId = 0)
    {
        $conn = Db::getInstance();
        $queueTable = _DB_PREFIX_ . 'conseqs_rule_measure_queue';

        $measureId = (int)$measureId;
        $measureCond = $measureId ? "id_measure=$measureId" : "1";

        // release blocked items -- lock is older then 3 minutes
        $threshold = time() - 180;
        $conn->execute("UPDATE $queueTable SET processing = NULL, processing_ts=NULL WHERE processing IS NOT NULL AND processing_ts < $threshold");

        // lock key - random data
        $id = Utils::getRandomData();

        while (true) {
            if (microtime(true) > $end) {
                // bail if we consumed all the time
                return;
            }

            // lock timestamp
            $ts = (int)time();

            // 1. pick item from queue
            $conn->execute("UPDATE $queueTable SET processing = '$id', processing_ts=$ts WHERE processing IS NULL AND $measureCond LIMIT 1");
            $res = $conn->executeS("SELECT * FROM $queueTable WHERE processing = '$id'");
            if (! $res) {
                // nothing do process
                return;
            }
            $item = $res[0];

            // 2. process item
            try {
                $ruleId = (int)$item['id_rule'];
                $measureId = (int)$item['id_measure'];
                $key = $item['key'];
                $newValue = $this->normalizeValue($item['new_value']);
                $oldValue = $this->normalizeValue($item['old_value']);
                $rulesManager->dispatchMeasure($ruleId, Utils::getMeasureById($measureId), [
                    'key' => $key,
                    'newValue' => $newValue,
                    'oldValue' => $oldValue
                ]);
            } finally {
                // delete queue item
                $conn->execute("DELETE FROM $queueTable WHERE processing = '$id'");
            }
        }
    }

    /**
     * @param $id
     * @return bool
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    public function delete($id)
    {
        $conn = Db::getInstance();
        $id = (int)$id;
        $conn->delete('conseqs_measure', "id_measure = $id");
        $conn->delete('conseqs_measure_value', "id_measure = $id");
        $conn->delete('conseqs_rule_measure', "id_measure = $id");
        $conn->delete('conseqs_rule_measure_queue', "id_measure = $id");
        return true;
    }

    private function normalizeValue($input)
    {
        if (is_null($input)) {
            return null;
        }
        $intVal = (int)$input;
        $floatVal = (float)$input;
        if (floor($floatVal) === $intVal) {
            return $intVal;
        }
        return $floatVal;
    }

    /**
     * @param $measureId
     * @param int $ruleId
     * @throws PrestaShopException
     */
    public function initQueueForRule($measureId, $ruleId=0)
    {
        $valueTable = _DB_PREFIX_ . 'conseqs_measure_value';
        $queueTable = _DB_PREFIX_ . 'conseqs_rule_measure_queue';
        $ruleMapTable = _DB_PREFIX_ . 'conseqs_rule_measure';
        $ruleTable = _DB_PREFIX_ . 'conseqs_rule';
        $measureTable = _DB_PREFIX_ . 'conseqs_measure';
        $measureId = (int)$measureId;
        $ruleId = (int)$ruleId;
        $ruleCond = $ruleId ? "r.id_rule = $ruleId" : '1';
        $conn = Db::getInstance();
        $insert = ("
              INSERT INTO $queueTable(`id_rule`, `id_measure`, `key`, `old_value`, `new_value`)
              SELECT r.id_rule, v.id_measure, v.key, null, v.value
              FROM $valueTable v
              INNER JOIN $ruleMapTable r ON (v.id_measure = r.id_measure)
              INNER JOIN $ruleTable rt ON (rt.id_rule = r.id_rule AND rt.active)
              INNER JOIN $measureTable m ON (r.id_measure = m.id_measure)
              WHERE v.id_measure = $measureId 
                AND v.ts = m.ts 
                AND $ruleCond
              ON DUPLICATE KEY UPDATE `new_value` = v.value
            ");
        if (!$conn->execute($insert)) {
            throw new PrestaShopException("Failed to insert into measure queue: " . $conn->getMsgError());
        }
    }

}
