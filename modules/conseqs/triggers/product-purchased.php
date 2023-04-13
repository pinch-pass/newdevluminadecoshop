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

use \Conseqs\ObjectModelMetadata;
use Conseqs\ParameterDefinitions;
use Conseqs\Parameters\NumberParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\RulesManager;
use Conseqs\Trigger;
use Order;
use OrderDetail;
use PrestaShopException;
use Product;
use ReflectionException;

class ProductPurchased extends Trigger
{
    const PRODUCT_ID = 'params.productId';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Product purchased');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Executed when specific product has been purchased by your customer');
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        return new ParameterDefinitions([
            static::PRODUCT_ID => new NumberParameterDefinition($this->l('Product ID'))
        ]);
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterDefinitions $definitions
     * @throws PrestaShopException
     */
    public function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions)
    {
        $definitions->addParameters([
            'quantity' => new NumberParameterDefinition($this->l('Purchased quantity')),
        ]);
        ObjectModelMetadata::addObjectParameterDefinitions('product', $this->l('Product'), 'Product', $definitions);
        ObjectModelMetadata::addObjectParameterDefinitions('order', $this->l('Order'), 'Order', $definitions);
    }

    /**
     * @param ParameterValues $values
     * @param ParameterValues $settings
     * @param $sourceParameters
     * @throws PrestaShopException
     * @throws ReflectionException
     */
    public function collectOutputParameterValues(ParameterValues $values, ParameterValues $settings, $sourceParameters)
    {
        $parameters = $sourceParameters['parameters'];
        /** @var OrderDetail $orderDetail */
        $orderDetail = $parameters['object'];

        $values->addParameter('quantity', $orderDetail->product_quantity);
        ObjectModelMetadata::addObjectParameterValues('product', $values, new Product($settings->getValue(static::PRODUCT_ID)));
        ObjectModelMetadata::addObjectParameterValues('order', $values, new Order($orderDetail->id_order));
    }

    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $manager->registerHook('actionObjectOrderDetailAddAfter');
    }

    /**
     * @param ParameterValues $settings
     * @param array $sourceParameters
     * @return bool
     * @throws PrestaShopException
     */
    public function shouldTrigger(ParameterValues $settings, $sourceParameters)
    {
        $parameters = $sourceParameters['parameters'];
        /** @var OrderDetail $orderDetail */
        $orderDetail = $parameters['object'];
        return (int)$orderDetail->product_id === (int)$settings->getValue(static::PRODUCT_ID);
    }
}