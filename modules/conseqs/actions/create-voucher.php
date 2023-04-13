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

use Conseqs\Parameters\BooleanParameterDefinition;
use Conseqs\Parameters\IntParameterDefinition;
use Conseqs\Parameters\NumberParameterDefinition;
use Conseqs\Parameters\StringParameterDefinition;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\Utils;
use Conseqs\Action;
use Conseqs\RuntimeModifier;
use Conseqs\ParameterValues;
use Conseqs\ParameterDefinitions;
use PrestaShopException;
use CartRule;
use Customer;
use Language;

class CreateVoucher extends Action
{
    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Create voucher');
    }

    /**
     * @return ParameterDefinitions
     */
    public function getSettingsParameters()
    {
        return new ParameterDefinitions([
          'type' => new SelectParameterDefinition($this->l('Discount type'), [
              'amount' => $this->l('Amount'),
              'percent' => $this->l('Percentage')
          ])
        ]);
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('This action will create new cart rule for specific customer');
    }

    /**
     * @param ParameterValues $settings
     * @return ParameterDefinitions
     * @throws PrestaShopException
     */
    public function getInputParameters(ParameterValues $settings)
    {
        $parameters = new ParameterDefinitions([
            'params.customer' => new IntParameterDefinition($this->l('Customer ID')),
            'params.name' => new StringParameterDefinition($this->l('Voucher name')),
            'params.prefix' => new StringParameterDefinition($this->l('Code prefix')),
            'params.validity' => new IntParameterDefinition($this->l('Validity (days)')),

        ]);
        $type = $settings->getValue('type');
        if ($type === 'percent') {
            $parameters->addParameter('params.percent', new NumberParameterDefinition($this->l('Reduction [%]')));
        } else {
            $parameters->addParameter('params.currency', new SelectParameterDefinition($this->l('Currency'), Utils::getCurrencies()));
            $parameters->addParameter('params.amount', new NumberParameterDefinition($this->l('Amount')));
            $parameters->addParameter('params.tax', new BooleanParameterDefinition($this->l('Tax included')));
        }
        return $parameters;
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
        $type = $settings->getValue('type');

        $customer = (int)$input->getValue('params.customer');
        if (! Customer::customerIdExistsStatic($customer)) {
            throw new PrestaShopException("Customer with id $customer not found");
        }

        $prefix = $input->getValue('params.prefix');

        $validity = (int)$input->getValue('params.validity');
        if ($validity <= 0) {
            throw new PrestaShopException("Invalid voucher validity: $validity");
        }

        $name = $input->getValue('params.name');
        if (! $name) {
            $name = 'New voucher';
        }

        $cartRule = new CartRule();
        $cartRule->id_customer = $customer;
        $cartRule->date_from = date("Y-m-d h:i:s");
        $cartRule->date_to = date("Y-m-d h:i:s", strtotime("+$validity days"));
        $cartRule->name = [];
        foreach (Language::getIDs() as $language) {
            $cartRule->name[$language] = $name;
        }
        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = 1;
        $cartRule->priority = 1;
        $cartRule->partial_use = 1;
        $cartRule->highlight = 1;
        $cartRule->code = static::genRandomCode($prefix, 8);
        $cartRule->minimum_amount_tax = false;
        $cartRule->minimum_amount = 0;
        $cartRule->minimum_amount_shipping = 0;
        if ($type === 'percent') {
            $percent = (float)$input->getValue('params.percent');
            if ($percent <= 0.0 || $percent >= 100.0) {
                throw new PrestaShopException("Invalid percent reduction: $percent");
            }
            $cartRule->reduction_percent = (float)$percent;
            $cartRule->reduction_amount = 0;
            $cartRule->reduction_tax = 0;
        } else {
            $currency = $input->getValue('params.currency');
            $value = $input->getValue('params.amount');

            $cartRule->reduction_currency = $currency;
            $cartRule->reduction_percent = 0;
            $cartRule->reduction_amount = $value;
            $cartRule->reduction_tax = (int)$input->getValue('params.tax');
        }
        if (! $cartRule->add()) {
            throw new PrestaShopException('Failed to create cart rule');
        }
    }

    /**
     * create voucher for the customer .
     *
     * @param $prefix
     * @param int $length number of digit which you want to create random number voucher
     * @return string
     */
    public static function genRandomCode($prefix, $length)
    {
        $prefix = trim($prefix);
        if (! $prefix) {
           $prefix = 'CON';
        }
        if (strlen($prefix) > 3) {
            $prefix = substr($prefix, 0, 3);
        }
        $chars = str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $length);
        return $prefix . "-" . substr(str_shuffle($chars), 0, $length);
    }

}