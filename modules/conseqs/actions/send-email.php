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
use Language;
use Conseqs\Utils;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use Mail;

class SendEmail extends Action
{
    // settings
    const TEMPLATE = 'template';

    // constant input parameters
    const LANGUAGE = 'language';
    const SUBJECT = 'subject';
    const RECIPIENT = 'recipient';


    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Send email');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Sends email using specific email template');
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        return new ParameterDefinitions([
            static::TEMPLATE => new SelectParameterDefinition($this->l('Email template'), static::getEmailTemplates())
        ]);
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     * @throws \PrestaShopException
     */
    public function getInputParameters(ParameterValues $settings)
    {
        $parameters = new ParameterDefinitions([
            static::RECIPIENT => new StringParameterDefinition($this->l('Email address')),
            static::SUBJECT => new StringParameterDefinition($this->l('Subject')),
            static::LANGUAGE => new SelectParameterDefinition($this->l('Language'), static::getLanguages()),
        ]);

        $emailParameters = static::getEmailParameters($settings->getValue(static::TEMPLATE));
        foreach ($emailParameters as $key => $value) {
            $parameters->addParameter($key, new StringParameterDefinition($value));
        }
        return $parameters;
    }


    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @param RuntimeModifier $runtimeModifier
     * @throws \PrestaShopException
     */
    public function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier)
    {
        // translate input parameters to email variables
        $template = $settings->getValue(static::TEMPLATE);
        $emailParameters = static::getEmailParameters($template);
        $templateVars = [];
        foreach ($emailParameters as $key => $templateName) {
            $templateVars[$templateName] = $input->getValue($key);
        }

        $languageId = (int)$input->getValue(static::LANGUAGE);
        $subject = $input->getValue(static::SUBJECT);
        $to = $input->getValue(static::RECIPIENT);

        // send email
        Mail::Send($languageId, $template, $subject, $templateVars, $to);
    }

    /**
     * @return array
     */
    private static function getEmailTemplates()
    {
        $keys = array_keys(Utils::getEmailTemplates());
        return array_combine($keys, $keys);
    }

    /**
     * @return array
     */
    private static function getLanguages()
    {
        $ret = [];
        try {
            $languages = Language::getLanguages();
            foreach ($languages as $language) {
                $ret[$language['id_lang']] = $language['name'];
            }
        } catch (\PrestaShopException $e) {
        }
        return $ret;
    }

    /**
     * @param string $template
     * @return array
     */
    private static function getEmailParameters($template)
    {
        $templates = Utils::getEmailTemplates();
        $parameters = [];
        if (isset($templates[$template])) {
            foreach ($templates[$template] as $filepath) {
                $templateParameters = Utils::getEmailTemplateParameters($filepath);
                foreach ($templateParameters as $parameter) {
                    $key = 'email.' . Utils::camelize(str_replace('}', '', str_replace('{', '', $parameter)), '_', false);
                    $parameters[$key] = $parameter;
                }
            }
        }
        $provided = ['{shop_name}', '{shop_url}', '{shop_logo}', '{my_account_url}', '{guest_tracking_url}', '{history_url}', '{color}'];
        return array_diff($parameters, $provided);
    }
}