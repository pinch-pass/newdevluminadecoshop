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

use Conseqs\ObjectModelMetadata;
use Conseqs\ParameterDefinitions;
use Conseqs\Parameters\NumberParameterDefinition;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\Parameters\StringParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\RulesManager;
use Conseqs\Trigger;
use Conseqs\Utils;

class MeasureValueChanged extends Trigger
{
    const MEASURE_CODE = 'measureCode';
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Measure value changed');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This hook is executed whenever measure value changes');
    }

    /**
     * @return ParameterDefinitions
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getSettingsParameters()
    {
        $settings = new ParameterDefinitions();
        $settings->addParameter(static::MEASURE_CODE, new SelectParameterDefinition($this->l('Measure'), static::getMeasures()));
        return $settings;
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterDefinitions $definitions
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions)
    {
        $measure = static::getMeasure($settings);
        if ($measure) {
            $definitions->addParameters([
                'measure.key' => new StringParameterDefinition($this->l('Measure: Unique Key')),
                'measure.oldValue' => new NumberParameterDefinition($this->l('Measure: Old value')),
                'measure.newValue' => new NumberParameterDefinition($this->l('Measure: New value')),
            ]);
            $model = ObjectModelMetadata::getModelForKey($measure['keyField']);
            if ($model) {
                ObjectModelMetadata::addObjectParameterDefinitions('measure.' . strtolower($model), $this->l('Measure: ') . $model, $model, $definitions);
            }
        }
    }

    /**
     * @param ParameterValues $values
     * @param ParameterValues $settings
     * @param $sourceParameters
     * @throws \PrestaShopException
     * @throws \ReflectionException
     */
    public function collectOutputParameterValues(ParameterValues $values, ParameterValues $settings, $sourceParameters)
    {
        $measure = static::getMeasure($settings);
        if ($measure) {
            $parameters = $sourceParameters['parameters'];
            $values->addParameter('measure.key', $parameters['key']);
            $values->addParameter('measure.oldValue', $parameters['oldValue']);
            $values->addParameter('measure.newValue', $parameters['newValue']);
            $model = ObjectModelMetadata::getModelForKey($measure['keyField']);
            if ($model) {
                $id = (int)$parameters['key'];
                ObjectModelMetadata::addObjectParameterValues('measure.' . strtolower($model), $values, new $model($id));
            }
        }
    }

    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws \PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $measure = static::getMeasure($settings);
        if ($measure) {
            $manager->registerMeasure($measure['id']);
        }
    }

    /**
     * @param ParameterValues $settings
     * @return array
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private static function getMeasure(ParameterValues $settings)
    {
        $code = $settings->getValue(static::MEASURE_CODE);
        $measures = Utils::getMeasures();
        if (isset($measures[$code])) {
            return $measures[$code];
        }
        return null;
    }

    /**
     * @return array
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private static function getMeasures()
    {
        return array_map(function($measure) {
            return $measure['name'];
        }, Utils::getMeasures());
    }

}
