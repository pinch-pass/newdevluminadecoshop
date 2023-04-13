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

namespace Conseqs\Triggers;

use Conseqs\Compatibility;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\ParameterDefinitions;
use Conseqs\Parameters\StringParameterDefinition;
use Conseqs\Trigger;
use Conseqs\RulesManager;
use Conseqs\ParameterValues;
use Conseqs\Utils;


class BeforeEmailSent extends Trigger
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Before email is sent');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Executed just before email is sent');
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
     * @param ParameterDefinitions $definitions
     * @throws \PrestaShopException
     */
    public function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions)
    {
        $definitions->addParameters([
            'language' => new SelectParameterDefinition($this->l('Language'), Utils::getLanguages()),
            'template' => new StringParameterDefinition($this->l('Email template')),
            'subject' => new StringParameterDefinition($this->l('Subject')),
            'to' => new StringParameterDefinition($this->l('To email address')),
            'toName' => new StringParameterDefinition($this->l('Recipient name (optional)')),
            'bcc' => new StringParameterDefinition($this->l('BCC')),
        ]);
    }

    /**
     * @param ParameterValues $values
     * @param ParameterValues $settings
     * @param $sourceParameters
     * @throws \PrestaShopException
     */
    public function collectOutputParameterValues(ParameterValues $values, ParameterValues $settings, $sourceParameters)
    {
        $parameters = $sourceParameters['parameters'];
        $values->addParameter('language', $parameters['idLang']);
        $values->addParameter('template', $parameters['template']);
        $values->addParameter('subject', $parameters['subject']);
        $values->addParameter('to', static::toString($parameters['to']));
        $values->addParameter('toName', static::toString($parameters['toName']));
        $values->addParameter('bcc', static::toString($parameters['bcc']));
    }
    
    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws \PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $manager->registerHook('actionEmailSendBefore');
    }

    /**
     * actionEmailSendBefore is available since prestashop 1.7.1, and thirtybees 1.0.8
     *
     * @return Compatibility
     */
    public function getCompatibility()
    {
        return new Compatibility("1.7.1.0", "1.0.8");
    }

    /**
     * @param $addr
     * @return string
     */
    private static function toString($addr)
    {
        if (is_array($addr)) {
            return implode(", ", $addr);
        }
        return $addr;
    }

}