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

namespace Conseqs\Actions;

use Conseqs\Action;
use Conseqs\ParameterDefinitions;
use Conseqs\ParameterValues;
use Conseqs\Utils;
use Conseqs\RuntimeModifier;
use Tools;

class Debug extends Action
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Debug');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This helper action will display debugging information');
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     */
    public function getInputParameters(ParameterValues $settings)
    {
        return ParameterDefinitions::none();
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @param RuntimeModifier $runtimeModifier
     * @return mixed
     * @throws \PrestaShopException
     */
    public function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier)
    {
        $data = $this->getContent($triggerOutput);
        $ajax = Tools::getValue('ajax') || !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        $type = $triggerOutput->getValue('trigger.type');
        if ($type === 'hook' && !$ajax) {
            echo "<html>";
            echo "<head>";
            echo "<link href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T\" crossorigin=\"anonymous\">";
            echo "</head>";
            echo "<body class='bootstrap'>";
            echo "<div class='container'>";
            echo "<h2>Conseqs debug</h2>";
            echo "<h3>Trigger: " . $data['trigger'] . "</h3>";
            echo "<br>";
            $this->printHtmlTable($data['data']);
            echo "</body>";
            echo "</html>";
            die();
        } else {
            $dir = Utils::getLogDirectory();
            $content = $this->toText($data);
            @file_put_contents($dir . 'conseqs-'.date('Ymd-His').'-' .Utils::getRandomData(). '.log', $content);
        }
    }

    private function printHtmlTable($rows)
    {
        echo "<table class='table'>";
        echo "<thead><tr><td>Unique identifier</td><td>Name</td><td>Type</td><td>Value</td></tr></thead>";
        echo "<tbody>";
        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>" . htmlentities($row[0]) . "</td>";
            echo "<td>" . htmlentities($row[1]) . "</td>";
            echo "<td>" . htmlentities($row[2]) . "</td>";
            echo "<td>" . htmlentities($row[3]) . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    }

    /**
     * @param ParameterValues $values
     * @return array
     * @throws \PrestaShopException
     */
    private function getContent(ParameterValues $values)
    {
        $definitions = $values->getDefinitions();
        $json = (array)$values->toJson();
        $cols = [
            'id' => 0,
            'name' => 0,
            'type' => 0,
            'value' => 0
        ];
        $data = [];

        foreach ($json as $key => $value) {
            $param = $definitions->getParameter($key);
            $value = $param->convertToString($value);
            $value = $param->getType() === 'select' ? $value . ' [' . $param->getValue($value) . ']' : $value;
            $name = $param->getName();
            $type = $param->getType();
            $data[] = [$key, $name, $type, $value];
            $cols['id'] = max($cols['id'], mb_strlen($key));
            $cols['name'] = max($cols['name'], mb_strlen($name));
            $cols['type'] = max($cols['type'], mb_strlen($type));
            $cols['value'] = max($cols['value'], mb_strlen($value));
        }

        return [
            'trigger' => $values->getValue('trigger.name'),
            'date' => date('Y-m-d H:i:s'),
            'data' => $data,
            'cols' => $cols
        ];

    }

    private function toText($data)
    {
        $cols = $data['cols'];

        $ret = "";
        $ret .= "Date: " . $data['date'] . "\n";
        $ret .= "Trigger: " . $data['trigger'] . "\n\n";
        $line = '+-' . str_repeat('-', $cols['id']) . '-+-' . str_repeat('-', $cols['name']) . '-+-' . str_repeat('-', $cols['type']) . '-+-' . str_repeat('-', $cols['value']) . "-+\n";
        $ret .= $line;
        foreach ($data['data'] as $row) {
            $ret .= '| ';
            $ret .= $this->pad($row[0], $cols['id']);
            $ret .= ' | ';
            $ret .= $this->pad($row[1], $cols['name']);
            $ret .= ' | ';
            $ret .= $this->pad($row[2], $cols['type']);
            $ret .= ' | ';
            $ret .= $this->pad($row[3], $cols['value']);
            $ret .= " |\n";
        }
        $ret .= $line;
        return $ret;
    }

    private function pad($value, $length) {
        $len = mb_strlen($value);
        return $value . str_repeat(' ', $length - $len);
    }
}