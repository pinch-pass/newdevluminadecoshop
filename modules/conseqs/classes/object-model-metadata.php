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

namespace Conseqs;

use Configuration;
use Conseqs\Parameters\BooleanParameterDefinition;
use Conseqs\Parameters\DateParameterDefinition;
use Conseqs\Parameters\IntParameterDefinition;
use Conseqs\Parameters\NumberParameterDefinition;
use Conseqs\Parameters\SelectParameterDefinition;
use Conseqs\Parameters\StringParameterDefinition;
use Context;
use ObjectModel;
use Order;
use Cart;
use PrestaShopException;
use Product;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Tools;

class ObjectModelMetadata
{
    public static $objectModels = null;

    /**
     * @param $prefix
     * @param $labelPrefix
     * @param $objectModel
     * @param ParameterDefinitions $definitions
     * @throws PrestaShopException
     */
    public static function addObjectParameterDefinitions($prefix, $labelPrefix, $objectModel, ParameterDefinitions $definitions)
    {
        static::buildObjectParameterDefinitions($prefix, $labelPrefix, $objectModel, $definitions, true);
    }

    /**
     * @param $prefix
     * @param $labelPrefix
     * @param $objectModel
     * @param ParameterDefinitions $definitions
     * @param $includeSubmodels
     * @throws PrestaShopException
     */
    private static function buildObjectParameterDefinitions($prefix, $labelPrefix, $objectModel, ParameterDefinitions $definitions, $includeSubmodels)
    {
        $def = ObjectModel::getDefinition($objectModel);
        if ($prefix) {
            $prefix = $prefix . '.';
        }
        if ($labelPrefix) {
            $labelPrefix = $labelPrefix . ': ';
        }
        if ($def && isset($def['fields']) && is_array($def['fields'])) {
            if (isset($def['primary'])) {
                $definitions->addParameter($prefix . 'id', new IntParameterDefinition($labelPrefix . 'ID'));
            }
            $fields = $def['fields'];
            foreach ($fields as $field => $fieldDef) {
                $parameterDefinition = static::getObjectModelFieldParameter($field, $fieldDef, $labelPrefix);
                if ($parameterDefinition) {
                    $definitions->addParameter($prefix . $field, $parameterDefinition);
                }
            }
            if ($includeSubmodels) {
                foreach (static:: getSubmodels() as $field => $submodel) {
                    if (isset($fields[$field])) {
                        static::buildObjectParameterDefinitions($prefix . strtolower($submodel), $labelPrefix . $submodel, $submodel, $definitions, false);
                    }
                }
            }
        }
        if ($includeSubmodels) {
            if ($objectModel === 'Order') {
                static::addOrderSpecificParameterDefinitions($prefix, $labelPrefix, $definitions);
            }
            if ($objectModel === 'Cart') {
                static::addCartSpecificParameterDefinitions($prefix, $labelPrefix, $definitions);
            }
        }
    }


    /**
     * @param $prefix
     * @param ParameterValues $values
     * @param LazyObjectModel $model
     * @throws PrestaShopException
     */
    private static function addLazyObjectParameterValues($prefix, ParameterValues $values, LazyObjectModel $model)
    {
        if (!$model) {
            return;
        }
        $def = ObjectModel::getDefinition($model->getModelClass());
        if ($prefix) {
            $prefix = $prefix . '.';
        }
        $definitions = $values->getDefinitions();
        if ($def && isset($def['fields']) && is_array($def['fields'])) {
            if (isset($def['primary'])) {
                $values->addParameter($prefix . 'id', (int)$model->getId());
            }
            $fields = $def['fields'];
            foreach ($fields as $field => $fieldDef) {
                $key = $prefix . $field;
                if ($definitions->hasParameter($key)) {
                    $values->addLazyParameter($key, $field, [$model, 'getValue']);
                }
            }
        }
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public static function getModelForKey($key)
    {
        $models = static::getSubmodels();
        if (isset($models[$key])) {
            return $models[$key];
        }
        return null;
    }

    /**
     * @param $prefix
     * @param ParameterValues $values
     * @param ObjectModel $model
     * @throws PrestaShopException
     * @throws \ReflectionException
     */
    public static function addObjectParameterValues($prefix, ParameterValues $values, ObjectModel $model)
    {
        if (!$model) {
            return;
        }
        $def = ObjectModel::getDefinition($model);
        if ($prefix) {
            $prefix = $prefix . '.';
        }
        $definitions = $values->getDefinitions();
        if ($def && isset($def['fields']) && is_array($def['fields'])) {
            if (isset($def['primary'])) {
                $values->addParameter($prefix . 'id', (int)$model->id);
            }
            $fields = $def['fields'];
            foreach ($fields as $field => $fieldDef) {
                $key = $prefix . $field;
                if ($definitions->hasParameter($key)) {
                    $values->addParameter($key, $model->{$field});
                }
            }
            foreach (static:: getSubmodels() as $field => $submodel) {
                if (isset($fields[$field])) {
                    static::addLazyObjectParameterValues($prefix . strtolower($submodel), $values, new LazyObjectModel($submodel, $model->{$field}, static::getObjectModelLang($model)));
                }
            }
        }
        if ($model instanceof Order) {
            static::addOrderSpecificParameterValues($prefix, $values, $model);
        }
        if ($model instanceof Cart) {
            static::addCartSpecificParameterValues($prefix, $values, $model);
        }
    }

    /**
     * @return array
     */
    private static function getSubmodels()
    {
        return [
            'id_lang' => 'Language',
            'id_currency' => 'Currency',
            'id_employee' => 'Employee',
            'id_shop' => 'Shop',
            'id_zone' => 'Zone',
            'id_customer' => 'Customer',
            'id_default_group' => 'Group',
            'id_group' => 'Group',
            'id_category_default' => 'Category',
            'id_category' => 'Category',
            'id_cart_rule' => 'CartRule',
            'id_product' => 'Product',
            'id_order' => 'Order',
            'id_cart' => 'Cart',
            'id_country' => 'Country',
            'id_address' => 'Address',
            'id_address_delivery' => 'Address',
            'id_carrier' => 'Carrier',
            'id_product_attribute' => 'Combination',
            'id_image' => 'Image',
            'id_image_default' => 'Image',
            'id_supplier' => 'Supplier',
            'id_manufacturer' => 'Manufacturer',
        ];
    }

    /**
     * @param bool $refreshCache
     * @return array
     */
    public static function getObjectModels($refreshCache = false)
    {
        if (!static::$objectModels) {
            $cachePath = _PS_CACHE_DIR_ . '/conseqs-object-models.php';
            if (@is_readable($cachePath) && !$refreshCache) {
                require_once($cachePath);
            } else {
                $directory = new RecursiveDirectoryIterator(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'classes');
                $iterator = new RecursiveIteratorIterator($directory);
                static::$objectModels = [];
                foreach ($iterator as $path) {
                    $file = basename($path);
                    if (preg_match("/^.+\.php$/i", $file)) {
                        $className = str_replace(".php", "", $file);
                        if ($className !== "index") {
                            if (!class_exists($className)) {
                                require_once($path);
                            }
                            if (class_exists($className)) {
                                try {
                                    $reflection = new ReflectionClass($className);
                                    if ($reflection->isSubclassOf('ObjectModel') && !$reflection->isAbstract()) {
                                        static::$objectModels[$className] = ucwords(Utils::decamelize($className, ' '));
                                    }
                                } catch (\ReflectionException $exception) {
                                }
                            }
                        }
                    }
                }
                file_put_contents($cachePath, "<?php\n  static::\$objectModels=" . var_export(static::$objectModels, true) . ";\n");
                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate($cachePath);
                }
            }
        }
        return static::$objectModels;
    }

    /**
     *
     */
    public static function refreshObjectModels()
    {
        $cachePath = _PS_CACHE_DIR_ . '/conseqs-object-models.php';
        if (@is_readable($cachePath)) {
            // refresh cache every hour
            $interval = 60 * 60;
            if (time() > (filemtime($cachePath) + $interval)) {
                static::getObjectModels(true);
            }
        } else {
            static::getObjectModels(true);
        }
    }

    /**
     * @param string $name
     * @param array $fieldDef
     * @param string $labelPrefix
     * @return ParameterDefinition|null
     * @throws PrestaShopException
     */
    private static function getObjectModelFieldParameter($name, $fieldDef, $labelPrefix)
    {
        if (!$fieldDef || !isset($fieldDef['type'])) {
            return null;
        }

        $displayName = $labelPrefix . Utils::humanize($name);

        $fiedType = $fieldDef['type'];
        $typePrice = defined('ObjectModel::TYPE_PRICE') ? ObjectModel::TYPE_PRICE : -1;
        switch ($fiedType) {
            case ObjectModel::TYPE_INT:
                return new IntParameterDefinition($displayName);
            case ObjectModel::TYPE_BOOL:
                return new BooleanParameterDefinition($displayName);
            case ObjectModel::TYPE_FLOAT:
            case $typePrice:
                return new NumberParameterDefinition($displayName);
            case ObjectModel::TYPE_DATE:
                return new DateParameterDefinition($displayName);
            case ObjectModel::TYPE_STRING:
                if (isset($fieldDef['values'])) {
                    $values = array_combine($fieldDef['values'], $fieldDef['values']);
                    return new SelectParameterDefinition($displayName, $values);
                }
                return new StringParameterDefinition($displayName);
            case ObjectModel::TYPE_HTML:
            case ObjectModel::TYPE_NOTHING:
            case ObjectModel::TYPE_SQL:
                return new StringParameterDefinition($displayName);
            default:
                if (_PS_MODE_DEV_) {
                    throw new PrestaShopException("Unknown field type: $fiedType");
                }
                return new StringParameterDefinition($displayName);
        }
    }

    /**
     * @param ObjectModel $model
     * @return int|mixed
     * @throws \ReflectionException
     */
    private static function getObjectModelLang(ObjectModel $model)
    {
        if (defined('_TB_VERSION_')) {
            return $model->{'id_lang'};
        }
        $reflection = new ReflectionClass($model);
        $property = $reflection->getProperty('id_lang');
        $property->setAccessible(true);
        return (int)$property->getValue($model);
    }

    /**
     * @param $prefix
     * @param $labelPrefix
     * @param ParameterDefinitions $definitions
     */
    private static function addOrderSpecificParameterDefinitions($prefix, $labelPrefix, ParameterDefinitions $definitions)
    {
        $definitions->addParameters([
            $prefix . 'productsComma' => new StringParameterDefinition($labelPrefix . 'Products - Comma separated format'),
            $prefix . 'productsEmail' => new StringParameterDefinition($labelPrefix . 'Products - Email format')
        ]);
    }

    /**
     * @param $prefix
     * @param ParameterValues $values
     * @param Order $order
     */
    private static function addOrderSpecificParameterValues($prefix, ParameterValues $values, Order $order)
    {
        $values->addLazyParameter($prefix . 'productsComma', 'productsComma', function() use ($order) {
            $products = $order->getProducts();
            return implode(', ', array_column($products, 'product_name'));
        });
        $values->addLazyParameter($prefix . 'productsEmail', 'productsEmail', function() use ($order) {
            $context = Context::getContext();
            $iso = $context->language->iso_code;
            $templatePath = _PS_MAIL_DIR_ . $iso . DIRECTORY_SEPARATOR . 'order_conf_product_list.tpl';
            $themeTemplatePath = _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . $iso . DIRECTORY_SEPARATOR . 'order_conf_product_list.tpl';
            if (file_exists($themeTemplatePath)) {
                $templatePath = $themeTemplatePath;
            }
            $products = [];
            foreach ($order->getProducts() as $product) {
                $id = (int)$product['product_id'];
                $combination = $product['product_attribute_id'] ? (int)$product['product_attribute_id'] : null;
                $quantity = (int)$product['product_quantity'];
                $price = Product::getPriceStatic($id, true, $combination, 6, null, false, true, $quantity, false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                $products[] = [
                    'reference' => $product['reference'],
                    'name' => $product['product_name'] . (isset($product['attributes']) ? ' - ' . $product['attributes'] : ''),
                    'unit_price' => Tools::displayPrice($price, $context->currency, false),
                    'price' => Tools::displayPrice($price * $quantity, $context->currency, false),
                    'quantity' => $quantity,
                    'customization' => []
                ];

            }
            $template = $context->smarty->createTemplate($templatePath);
            $template->assign('list', $products);
            return $template->fetch();
        });
    }

    /**
     * @param $prefix
     * @param $labelPrefix
     * @param ParameterDefinitions $definitions
     */
    private static function addCartSpecificParameterDefinitions($prefix, $labelPrefix, ParameterDefinitions $definitions)
    {
        $definitions->addParameters([
            $prefix . 'productsComma' => new StringParameterDefinition($labelPrefix . 'Products - Comma separated format'),
            $prefix . 'productsEmail' => new StringParameterDefinition($labelPrefix . 'Products - Email format')
        ]);
    }

    /**
     * @param $prefix
     * @param ParameterValues $values
     * @param Cart $cart
     */
    private static function addCartSpecificParameterValues($prefix, ParameterValues $values, Cart $cart)
    {
        $values->addLazyParameter($prefix . 'productsComma', 'productsComma', function() use ($cart) {
            $products = $cart->getProducts();
            return implode(', ', array_column($products, 'name'));
        });
        $values->addLazyParameter($prefix . 'productsEmail', 'productsEmail', function() use ($cart) {
            $context = Context::getContext();
            $iso = $context->language->iso_code;
            $templatePath = _PS_MAIL_DIR_ . $iso . DIRECTORY_SEPARATOR . 'order_conf_product_list.tpl';
            $themeTemplatePath = _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . $iso . DIRECTORY_SEPARATOR . 'order_conf_product_list.tpl';
            if (file_exists($themeTemplatePath)) {
                $templatePath = $themeTemplatePath;
            }
            $products = [];
            foreach ($cart->getProducts() as $product) {
                $id = (int)$product['id_product'];
                $combination = $product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null;
                $quantity = (int)$product['quantity'];
                $price = Product::getPriceStatic($id, true, $combination, 6, null, false, true, $quantity, false, (int)$cart->id_customer, (int)$cart->id, (int)$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                $products[] = [
                    'reference' => $product['reference'],
                    'name' => $product['name'] . (isset($product['attributes']) ? ' - ' . $product['attributes'] : ''),
                    'unit_price' => Tools::displayPrice($price, $context->currency, false),
                    'price' => Tools::displayPrice($price * $quantity, $context->currency, false),
                    'quantity' => $quantity,
                    'customization' => []
                ];

            }
            $template = $context->smarty->createTemplate($templatePath);
            $template->assign('list', $products);
            return $template->fetch();
        });
    }
}