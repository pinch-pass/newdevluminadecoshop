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

class EmailAttachFile extends Action
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Email: attach file');
    }

    public function getAllowedTriggers()
    {
        return [
            'beforeEmailSent'
        ];
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Attaches custom file to email body');
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
     */
    public function getInputParameters(ParameterValues $settings)
    {
        return new ParameterDefinitions([
            'filename' => new StringParameterDefinition($this->l('Path to file')),
        ]);
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
        $file = $input->getValue('filename');
        if (! file_exists($file)) {
            throw new PrestaShopException("Can't attach file '$file' to email: file not exits");
        }
        $runtimeModifier->adjustParameter('fileAttachment', function($attachments) use ($file) {
            if (! $attachments) {
                $attachments = [];
            }
            if ($attachments && !is_array($attachments)) {
                $attachments = [ $attachments ];
            }
            $attachments[] = [
                'name' => basename($file),
                'content' => file_get_contents($file),
                'mime' => mime_content_type($file)
            ];
            return $attachments;
        });
    }

}
