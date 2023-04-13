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

use Configuration;
use Conseqs\Compatibility;
use Conseqs\ObjectModelMetadata;
use Conseqs\ParameterDefinitions;
use Conseqs\Parameters\DateParameterDefinition;
use Conseqs\Parameters\IntParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\RulesManager;
use Conseqs\Trigger;
use Customer;
use DateInterval;
use DateTime;
use Module;
use Order;

class LoyaltyPointsExpiring extends Trigger
{
    const MEASURE_CODE = 'core.loyaltyExpiring';

    private $loyaltyInstalled;
    private $expiration;

    /**
     * LoyaltyPointsExpiring constructor.
     * @throws \PrestaShopException
     */
    public function __construct()
    {
        $this->addModuleRequirement('loyalty');
        $this->loyaltyInstalled = Module::isInstalled('loyalty') && Module::isEnabled('loyalty');
        if ($this->loyaltyInstalled) {
            $this->expiration = (int)Configuration::get('PS_LOYALTY_VALIDITY_PERIOD');
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Loyalty points are expiring');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This scheduled hook will be executed when loyalty points are about to expire');
    }

    /**
     * @return Compatibility
     */
    public function getCompatibility()
    {
        if ($this->loyaltyInstalled && $this->expiration) {
            return Compatibility::all();
        }
        return Compatibility::incompatible();
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        return new ParameterDefinitions([
            'days' => new IntParameterDefinition('Remaining days')
        ]);
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterDefinitions $definitions
     * @throws \PrestaShopException
     */
    public function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions)
    {
        $definitions->addParameters([
            'loyalty.points' => new IntParameterDefinition('Loyalty points: Amount'),
            'loyalty.expiration' => new DateParameterDefinition($this->l('Loyalty points: Expiration date')),
            'loyalty.totalPoints' => new IntParameterDefinition('Loyalty points: Total available'),
        ]);
        ObjectModelMetadata::addObjectParameterDefinitions('loyalty.customer', 'Loyalty: Customer', 'Customer', $definitions);
        ObjectModelMetadata::addObjectParameterDefinitions('loyalty.order', 'Loyalty: Order', 'Order', $definitions);
    }


    /**
     * @param ParameterValues $settings
     * @param array $sourceParameters
     * @return bool
     */
    public function shouldTrigger(ParameterValues $settings, $sourceParameters)
    {
        $oldValue = (int)$sourceParameters['parameters']['oldValue'];
        $newValue = (int)$sourceParameters['parameters']['newValue'];
        return $newValue && !$oldValue;
    }

    /**
     * @param ParameterValues $values
     * @param ParameterValues $settings
     * @param $sourceParameters
     * @throws \PrestaShopException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function collectOutputParameterValues(ParameterValues $values, ParameterValues $settings, $sourceParameters)
    {
        $parameters = $sourceParameters['parameters'];
        $key = (int)$parameters['key'];
        $clazz = $this->getLoyaltyModuleClass();
        $loyalty = new $clazz($key);
        $expiration = new DateTime($loyalty->date_add);
        $expiration->add(new DateInterval("P" .$this->expiration."D"));
        $totalPoints = call_user_func([$clazz, 'getPointsByCustomer'], $loyalty->id_customer);
        $values->addParameter('loyalty.points', (int)$loyalty->points);
        $values->addParameter('loyalty.expiration', $expiration);
        $values->addParameter('loyalty.totalPoints', $totalPoints);
        ObjectModelMetadata::addObjectParameterValues('loyalty.customer', $values, new Customer($loyalty->id_customer));
        ObjectModelMetadata::addObjectParameterValues('loyalty.order', $values, new Order($loyalty->id_order));
    }

    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws \PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $days = (int)$settings->getValue('days');
        $code = static::MEASURE_CODE . $days;
        $measureId = $manager->getModule()->getMeasureManager()->saveMeasure(
            $code,
            sprintf($this->l('Loyalty expiring in %s days'), $days),
            $this->getMeasureSql($days),
            'key',
            'value',
            3
        );
        $manager->registerMeasure($measureId);
    }

    /**
     * @param int $days
     * @return string
     */
    private function getMeasureSql($days)
    {
        $expiration = $this->expiration;
        $remaining = "($expiration - datediff(NOW(), date_add))";
        return (
            "SELECT id_loyalty as `key`, (($remaining < $days) AND ($remaining >= 0)) as `value`\n" .
            "FROM `"._DB_PREFIX_."loyalty`\n" .
            "WHERE id_loyalty_state = 2\n"
        );
    }

    /**
     * @return string LoyaltyModule class
     */
    private function getLoyaltyModuleClass()
    {
        require_once(_PS_MODULE_DIR_ . 'loyalty/LoyaltyModule.php');
        require_once(_PS_MODULE_DIR_ . 'loyalty/LoyaltyStateModule.php');
        return defined('_TB_VERSION_') ? 'LoyaltyModule\LoyaltyModule' : 'LoyaltyModule';
    }

}