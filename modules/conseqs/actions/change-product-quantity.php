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

use Conseqs\Parameters\IntParameterDefinition;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\RuntimeModifier;
use PrestaShopException;
use Conseqs\Action;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use StockAvailable;

class ChangeProductQuantity extends Action
{
    const MODE = 'mode';
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Change product quantity');
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        return new ParameterDefinitions([
           static::MODE => new SelectParameterDefinition($this->l('Update mode'), [
               'set' => $this->l('Set quantity'),
               'adjust' => $this->l('Adjust by'),
           ])
        ]);
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This action will change product quantity on stock');
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     * @throws PrestaShopException
     */
    public function getInputParameters(ParameterValues $settings)
    {
        $mode = $settings->getValue(static::MODE);
        $ret = new ParameterDefinitions([
            'params.product' => new IntParameterDefinition($this->l('Product ID')),
            'params.combination' => new IntParameterDefinition($this->l('Combination ID')),
        ]);
        if ($mode === 'set') {
            $ret->addParameter('params.quantity', new IntParameterDefinition($this->l('New quantity')));
        } else {
            $ret->addParameter('params.by', new IntParameterDefinition($this->l('Change quantity by')));
        }
        return $ret;
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterValues $input
     * @param ParameterValues $triggerOutput
     * @param RuntimeModifier $runtimeModifier
     * @throws PrestaShopException
     * @throws \Adapter_Exception
     * @throws \PrestaShopDatabaseException
     */
    public function execute(ParameterValues $settings, ParameterValues $input, ParameterValues $triggerOutput, RuntimeModifier $runtimeModifier)
    {
        $productId = $input->getValue('params.product');
        $combinationId = $input->getValue('params.combination');
        $mode = $settings->getValue(static::MODE);
        if ($mode === 'set') {
            $quantity = $input->getValue('params.quantity');
            StockAvailable::setQuantity($productId, $combinationId, $quantity);
        } else {
            $by = $input->getValue('params.by');
            StockAvailable::updateQuantity($productId, $combinationId, $by);
        }
    }
}