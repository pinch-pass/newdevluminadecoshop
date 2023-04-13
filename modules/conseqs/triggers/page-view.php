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

use Category;
use Conseqs\ObjectModelMetadata;
use Conseqs\ParameterDefinitions;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\ParameterValues;
use Conseqs\RulesManager;
use Conseqs\Trigger;
use Context;
use Db;
use DbQuery;
use Product;
use Tools;

class PageView extends Trigger
{
    const CONTROLLER = 'controller';

    /**
     * @return string
     */
    public function getName()
    {
        return $this->l('Page view');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->l('Executed when specific page is displayed');
    }

    /**
     * @return ParameterDefinitions
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getSettingsParameters()
    {
        return new ParameterDefinitions([
            static::CONTROLLER => new SelectParameterDefinition($this->l('Page'), static::getMetas())
        ]);
    }

    /**
     * @param ParameterValues $settings
     * @param ParameterDefinitions $definitions
     * @throws \PrestaShopException
     */
    public function registerOutputParameterDefinitions(ParameterValues $settings, ParameterDefinitions $definitions)
    {
        $controller = $settings->getValue(static::CONTROLLER);
        $this->addControllerSpecificParameters($controller, $definitions);
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
        $controller = $settings->getValue(static::CONTROLLER);
        $this->addControllerSpecificParameterValues($controller, $values);
    }

    /**
     * @param int $id
     * @param ParameterValues $settings
     * @param RulesManager $manager
     * @throws \PrestaShopException
     */
    public function register($id, ParameterValues $settings, RulesManager $manager)
    {
        $manager->registerHook('displayHeader');
    }

    /**
     * @param ParameterValues $settings
     * @param array $sourceParameters
     * @return bool
     * @throws \PrestaShopException
     */
    public function shouldTrigger(ParameterValues $settings, $sourceParameters)
    {
        $controller = $settings->getValue(static::CONTROLLER);
        return Context::getContext()->controller->php_self == $controller;
    }


    /**
     * @return array|null
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private static function getMetas()
    {
        static $ret = null;
        if (! $ret) {
            $langId = (int)Context::getContext()->language->id;
            $metas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                (new DbQuery())
                    ->select('DISTINCT m.page, ml.title')
                    ->from('meta', 'm')
                    ->leftJoin('meta_lang', 'ml', 'm.`id_meta` = ml.`id_meta`')
                    ->where("ml.id_lang IS NULL OR (ml.id_lang = $langId)")
                    ->orderBy('`page` ASC')
            );
            $ret = [];
            foreach ($metas as $meta) {
                $page = $meta['page'];
                $title = $meta['title'];
                if (!$title) {
                    $title = ucfirst($page);
                }
                if (strpos($title, 'module-') === 0) {
                    $split = explode('-', $title);
                    array_shift($split);
                    $module = array_shift($split);
                    $title = $module . ': ' . implode('-', $split);
                }
                $ret[$page] = $title;
            }
        }
        return $ret;
    }

    /**
     * @param $controller
     * @param ParameterDefinitions $definitions
     * @throws \PrestaShopException
     */
    private function addControllerSpecificParameters($controller, ParameterDefinitions $definitions)
    {
        if ($controller === 'product') {
            ObjectModelMetadata::addObjectParameterDefinitions('product', $this->l('Product'), 'Product', $definitions);
        }
        if ($controller === 'category') {
            ObjectModelMetadata::addObjectParameterDefinitions('category', $this->l('Category'), 'Category', $definitions);
        }
    }

    /**
     * @param $controller
     * @param ParameterValues $values
     * @throws \PrestaShopException
     * @throws \ReflectionException
     */
    private function addControllerSpecificParameterValues($controller, ParameterValues $values)
    {
        if ($controller === 'product') {
            $product = new Product((int)Tools::getValue('id_product'), false, Context::getContext()->language->id);
            ObjectModelMetadata::addObjectParameterValues($controller, $values, $product);
        }
        if ($controller === 'category') {
            $category = new Category((int)Tools::getValue('id_category'), Context::getContext()->language->id);
            ObjectModelMetadata::addObjectParameterValues($controller, $values, $category);
        }
    }

}