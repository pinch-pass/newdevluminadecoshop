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

use Combination;
use Configuration;
use Conseqs\ObjectModelMetadata;
use Conseqs\ParameterDefinitions;
use Conseqs\Parameters\IntParameterDefinition;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\RulesManager;
use Conseqs\Trigger;
use Product;

class StockQuantityChanged extends Trigger
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Stock quantity changed');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Executed after stock quantity changed');
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
     * @return ParameterDefinitions
     * @throws \PrestaShopException
     */
    public function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions)
    {
        $definitions->addParameter('params.type', new SelectParameterDefinition('Target type', [
            'product' => $this->l('Product'),
            'combination' => $this->l('Combination'),
        ]));
        $definitions->addParameter('params.quantity', new IntParameterDefinition($this->l('New quantity'), $this->l('New product or combination quantity')));

        ObjectModelMetadata::addObjectParameterDefinitions('product', $this->l('Product'), 'Product', $definitions);
        ObjectModelMetadata::addObjectParameterDefinitions('combination', $this->l('Combination'), 'Combination', $definitions);

        return $definitions;
    }

    /**
     * @param ParameterValues $values
     * @param ParameterValues $settings
     * @param $sourceParameters
     * @throws \Adapter_Exception
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \ReflectionException
     */
    public function collectOutputParameterValues(ParameterValues $values, ParameterValues $settings, $sourceParameters)
    {
        $lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $params = $sourceParameters['parameters'];
        $combinationId = (int)$params['id_product_attribute'];
        $productId = (int)$params['id_product'];
        $quantity = (int)$params['quantity'];
        $values->addParameter('params.type', $combinationId ? 'combination' : 'product');
        $values->addParameter('params.quantity', $quantity);

        ObjectModelMetadata::addObjectParameterValues('product', $values, new Product($productId, false, $lang));
        ObjectModelMetadata::addObjectParameterValues('combination', $values, new Combination($combinationId, $lang));
    }

    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws \PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $manager->registerHook('actionUpdateQuantity');
    }


}