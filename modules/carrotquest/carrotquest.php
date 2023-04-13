<?php
/**
 * 2017-2019 Carrot quest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author Carrot quest <support@carrotquest.io>
 * @copyright 2017-2019 Carrot quest
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Carrotquest extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'carrotquest';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.1';
        $this->author = 'Carrot quest';
        $this->need_instance = 1;
        $this->module_key = 'bc763b8410db68547b1b7412c32aa40a';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Carrot quest');
        $this->description = $this->l('Carrot quest is a customer service, combining all instruments for marketing automation, sales and communications for your web app. Goal is to increase first and second sales.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateValue('CARROTQUEST_API_KEY', false);
        Configuration::updateValue('CARROTQUEST_USER_AUTH_KEY', false);
        Configuration::updateValue('CARROTQUEST_API_SECRET', false);

        return parent::install() &&
            /**
             * $this->registerHook('actionAuthentication') &&
             */
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayFooterProduct') &&
            $this->registerHook('actionCartSave') &&
            $this->registerHook('displayShoppingCart') &&
            $this->registerHook('displayOrderConfirmation');
    }

    public function uninstall()
    {
        Configuration::deleteByName('CARROTQUEST_API_KEY');
        Configuration::deleteByName('CARROTQUEST_USER_AUTH_KEY');
        Configuration::deleteByName('CARROTQUEST_API_SECRET');
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {

        $this->context->smarty->assign('module_dir', $this->_path);
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitCarrotquestModule')) == true) {
            $result = $this->postProcess();
            if (!$result) {
                $this->context->smarty->assign('error', "Couldn't update settings");
            } else {
                $this->context->smarty->assign('confirmation', "Settings updated");
            }
        }
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCarrotquestModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 5,
                        'type' => 'text',
                        // 'prefix' => '',
                        'desc' => $this->l(''),
                        'name' => 'CARROTQUEST_API_KEY',
                        'label' => $this->l('API Key'),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        // 'prefix' => '',
                        'desc' => $this->l(''),
                        'name' => 'CARROTQUEST_API_SECRET',
                        'label' => $this->l('API Secret'),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        // 'prefix' => '',
                        'desc' => $this->l(''),
                        'name' => 'CARROTQUEST_USER_AUTH_KEY',
                        'label' => $this->l('User Auth Key'),
                    ),
                    array(
                        'col' => 1,
                        'type' => 'switch',
                        'desc' => $this->l(''),
                        'name' => 'CARROTQUEST_AUTH',
                        'label' => $this->l('Send User ID to Carrot quest'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', array(), 'Admin.Global')
                            )
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save', array(), 'Admin.Global'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CARROTQUEST_API_KEY' => Configuration::get('CARROTQUEST_API_KEY', null),
            'CARROTQUEST_USER_AUTH_KEY' => Configuration::get('CARROTQUEST_USER_AUTH_KEY', null),
            'CARROTQUEST_API_SECRET' => Configuration::get('CARROTQUEST_API_SECRET', null),
            'CARROTQUEST_AUTH' => Configuration::get('CARROTQUEST_AUTH', false)
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        $success = true;
        foreach (array_keys($form_values) as $key) {
            try {
                $result = Configuration::updateValue($key, Tools::getValue($key));
                if (!$result) {
                    $success = false;
                }
            } catch (Exception $e) {
                $success = false;
            }
        }
        return $success;
    }

    public function searchInArray($array, $key, $value)
    {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return $k;
            }
        }
        return null;
    }

    /**
     * Запуск JS из Hook после загрузки страницы (Ждем carrotquest.connect)
     */
    public function runJS($func, $data)
    {
        $this->context->smarty->assign('func', $func);
        $this->context->smarty->assign('data', $data);

        return $this->display(__FILE__, 'carrotquest.tpl');
    }

    // Page loaded
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addJS(($this->_path) . 'views/js/hookDisplayHeader.js');
        $key = Configuration::get('CARROTQUEST_API_KEY');
        $auth_on = Configuration::get('CARROTQUEST_AUTH');
        setcookie('carrotquest_api_key', $key, time() + 30, '/');
        if ($this->context->controller->authRedirection === 'my-account') {
            // var_dump($this);
        }
        if ($this->context->customer->id !== null) {
            if (!isset($this->context->cookie->carrotquest_identify)
                || $this->context->cookie->carrotquest_identify !== $this->context->customer->id) {
                $address = new Address((int)$this->context->cart->id_address_invoice);

                $data = array(
                    'name' => $this->context->customer->firstname . ' ' . $this->context->customer->lastname,
                    'email' => $this->context->customer->email,
                    'phone' => (!empty($address->phone) ? $address->phone : $address->phone_mobile)
                );

                if ($auth_on) {
                    $userauthkey = Configuration::get('CARROTQUEST_USER_AUTH_KEY');
                    $hash = hash_hmac('sha256', $this->context->customer->id, $userauthkey);

                    $data = array(
                        'user_id' => $this->context->customer->id,
                        'hash' => $hash
                    );
                }

                $value = json_encode($data);

                $this->context->cookie->__set('carrotquest_identify', $this->context->customer->id, time() + (86400 * 90), '/');

                return Carrotquest::runJS("customerIdentify", $value);
                // return Carrotquest::runJS("carrotquestPrestashop.customerIdentify(" . $data . ");");
            }
        }

        return Carrotquest::runJS("", null);
    }

    // Product view
    public function hookDisplayFooterProduct()
    {
        $data = (object)array();

        $product_id = Tools::getValue('id_product');
        $product = new Product($product_id);

        $data->id = $product->id;
        $data->name = array_pop($product->name);

        $category = new Category((int)$product->id_category_default, (int)$this->context->language->id);

        $data->category = $category->name;
        $data->url = $this->context->link->getProductLink($product_id);
        $data->amount = round($product->price);

        $cover = Product::getCover($product_id);
        $image = new Image($cover['id_image']);

        if ($image->getExistingImgPath() !== '' && $image->getExistingImgPath() !== false) {
            $data->img = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . ".jpg";
        }

        $txt = json_encode($data);
        return Carrotquest::runJS("productViewed", $txt);
    }

    // Корзина изменена
    public function hookActionCartSave($params)
    {
        if (Tools::getValue('add') === "1" && $this->context->cookie->id_cart) {
            $data = (object)array();
            $product_id = Tools::getValue('id_product');
            $product = new Product($product_id);
            $data->{'$name'} = array_pop($product->name);
            $data->{'$url'} = $this->context->link->getProductLink($product_id);
            $data->{'$amount'} = round($product->price);
            $cover = Product::getCover($product_id);
            $image = new Image($cover['id_image']);
            if ($image->getExistingImgPath() !== '' && $image->getExistingImgPath() !== false) {
                $data->{'$img'} = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . ".jpg";
            }

            $txt = json_encode($data);

            $carrotquest_uid = $_COOKIE["carrotquest_uid"];
            $carrotquest_key = Configuration::get('CARROTQUEST_API_KEY');
            $carrotquest_secret = Configuration::get('CARROTQUEST_API_SECRET');

            $url = "https://api.carrotquest.io/v1/users/" . $carrotquest_uid . "/events";

            $postdata = array(
                'event' => '$cart_added',
                'params' => $txt,
                'auth_token' => "app." . $carrotquest_key . "." . $carrotquest_secret,
            );
            $context = stream_context_create(
                array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => 'Content-Type: application/x-www-form-urlencoded',
                        'content' => http_build_query($postdata),
                    )
                )
            );
            $result = Tools::file_get_contents($url, false, $context);
        }
        if ($this->context->controller instanceof CartController) {
            /* сохраняем свойства */
            $carrotquest_uid = $_COOKIE["carrotquest_uid"];
            $carrotquest_key = Configuration::get('CARROTQUEST_API_KEY');
            $carrotquest_secret = Configuration::get('CARROTQUEST_API_SECRET');
            $url = "https://api.carrotquest.io/v1/users/" . $carrotquest_uid . "/props";

            $cart_products = $this->context->cart->getProducts();
            $items = array();
            $amount = 0;
            foreach ($cart_products as $cart_product) {
                array_push($items, $cart_product['name']);
                $amount = $amount + $cart_product['price'] * $cart_product['quantity'];
            }
            $operations = array(
                array(
                    'op' => 'update_or_create',
                    'key' => '$cart_items',
                    'value' => json_encode($items)
                ),
                array(
                    'op' => 'update_or_create',
                    'key' => '$cart_amount',
                    'value' => round($amount)
                )
            );
            $postdata = array(
                'operations' => json_encode($operations),
                'auth_token' => "app." . $carrotquest_key . "." . $carrotquest_secret,
            );
            $context = stream_context_create(
                array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => 'Content-Type: application/x-www-form-urlencoded',
                        'content' => http_build_query($postdata),
                    )
                )
            );
            $result = Tools::file_get_contents($url, false, $context);
        } elseif ($this->context->controller instanceof OrderController) {
            $carrotquest_uid = $_COOKIE["carrotquest_uid"];
            $carrotquest_key = Configuration::get('CARROTQUEST_API_KEY');
            $carrotquest_secret = Configuration::get('CARROTQUEST_API_SECRET');
            $url = "http://api.carrotquest.io/v1/users/" . $carrotquest_uid . "/props";

            $operations = array();
            if ($this->context->customer->firstname && $this->context->customer->lastname) {
                array_push($operations, array('op' => 'update_or_create', 'key' => '$name',
                    'value' => $this->context->customer->firstname . ' ' . $this->context->customer->lastname));
            }
            if ($this->context->customer->email) {
                array_push($operations, array('op' => 'update_or_create', 'key' => '$email',
                    'value' => $this->context->customer->email));
            }
            $address = new Address((int)$this->context->cart->id_address_invoice);
            $phone = (!empty($address->phone) ? $address->phone : $address->phone_mobile);
            if ($phone) {
                array_push($operations, array('op' => 'update_or_create', 'key' => '$phone', 'value' => $phone));
            }
            $postdata = array(
                'operations' => json_encode($operations),
                'auth_token' => "app." . $carrotquest_key . "." . $carrotquest_secret,
            );
            $context = stream_context_create(
                array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => 'Content-Type: application/x-www-form-urlencoded',
                        'content' => http_build_query($postdata),
                    )
                )
            );
            $result = Tools::file_get_contents($url, false, $context);
        }
    }

    // Просмотр корзины
    public function hookDisplayShoppingCart()
    {
        //$this->context->controller->addJS(($this->_path) . 'views/js/hookDisplayHeader.js');
        $cart_products = $this->context->cart->getProducts();
        // var_dump($cart_products);
        $names = array();
        $urls = array();
        $amounts = array();
        $imgs = array();
        foreach ($cart_products as $cart_product) {
            array_push($names, $cart_product['name']);
            array_push(
                $urls,
                $this->context->link->getProductLink(
                    $cart_product['id_product'],
                    $cart_product['prewrite'],
                    $cart_product['crewrite']
                )
            );
            array_push($amounts, round($cart_product['price']));
            $cover = Product::getCover($cart_product['id_product']);
            $image = new Image($cover['id_image']);
            if ($image->getExistingImgPath() !== '' && $image->getExistingImgPath() !== false) {
                array_push($imgs, _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . ".jpg");
            } else {
                array_push($imgs, '');
            }
        }
        $data = (object)array('name' => $names, 'url' => $urls, 'amount' => $amounts, 'img' => $imgs);
        // var_dump($data);
        $txt = json_encode($data);
        return Carrotquest::runJS("cartViewed", $txt);
        // return Carrotquest::runJS("carrotquestPrestashop.cartViewed('" . $data . "');");
    }

    // Заказ оформлен
    public function hookDisplayOrderConfirmation($params)
    {

        $data = (object)array();
        $order = $params['order'];

        $data->order_id = $order->id;
        $data->order_id_human = $order->reference;
        $data->order_amount = round($order->total_paid);

        $cart_id = $order->id_cart;
        $cart = new Cart($cart_id);

        $cart_products = $cart->getProducts();
        $products = array();
        foreach ($cart_products as $product) {
            // array_push($products, $product['id_product']);
            array_push($products, $product['name']);
        }
        $data->items = $products;
        // var_dump($data);
        $txt = json_encode($data);

        /* Очистка корзины */
        $carrotquest_uid = $_COOKIE["carrotquest_uid"];
        $carrotquest_key = Configuration::get('CARROTQUEST_API_KEY');
        $carrotquest_secret = Configuration::get('CARROTQUEST_API_SECRET');
        $url = "http://api.carrotquest.io/v1/users/" . $carrotquest_uid . "/props";

        $operations = array(
            array(
                'op' => 'delete',
                'key' => '$cart_items',
                'value' => 0
            ),
            array(
                'op' => 'delete',
                'key' => '$cart_amount',
                'value' => 0
            )
        );
        $postdata = array(
            'operations' => json_encode($operations),
            'auth_token' => "app." . $carrotquest_key . "." . $carrotquest_secret,
        );
        $context = stream_context_create(
            array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => http_build_query($postdata),
                )
            )
        );
        $result = Tools::file_get_contents($url, false, $context);

        //Carrotquest::runJS("log", json_encode($params));
        return Carrotquest::runJS("orderCompleted", $txt);
        // return Carrotquest::runJS("carrotquestPrestashop.orderCompleted('" . $data . "');");
    }
}
