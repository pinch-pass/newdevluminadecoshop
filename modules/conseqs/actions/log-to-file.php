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

use Conseqs\Parameters\StringParameterDefinition;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use PrestaShopException;

class LogToFile extends Action
{
    // constant input parameters
    const FILENAME = 'filename';
    const LOG_LINE = 'line';


    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Log to file');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Writes one line to log file');
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        return ParameterDefinitions::none();
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     * @throws \PrestaShopException
     */
    public function getInputParameters(ParameterValues $settings)
    {
        $parameters = new ParameterDefinitions([
            static::FILENAME => new StringParameterDefinition($this->l('File name')),
            static::LOG_LINE => new StringParameterDefinition($this->l('Log content')),
        ]);
        return $parameters;
    }


    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @param RuntimeModifier $runtimeModifier
     * @throws PrestaShopException
     */
    public function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier)
    {
        // translate input parameters to file path
        $file = $input->getValue(static::FILENAME);
        if ($file[0] != '/') {
            // if filepath is not absolute, make it relative to ps root dir
            $file = rtrim(_PS_ROOT_DIR_, '/') . '/' . $file;
        }
        // append .log extension for security reason
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (! in_array($ext, ['log', 'txt', 'csv'])) {
            $file .= '.log';
        }
        $line = $input->getValue(static::LOG_LINE);
        $ret = @file_put_contents($file, $line . "\n", FILE_APPEND);
        if ($ret === false) {
            throw new PrestaShopException("Failed to write log line to file $file");
        }
    }

}
