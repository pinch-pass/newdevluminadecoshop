<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class ecm_quickorder extends Module
{
    const PREFIX = 'ecm_';
    public function __construct()
    {
        $this->name = 'ecm_quickorder';
        $this->tab = 'checkout';
        $this->version = '1.2';
        $this->author = 'Elcommerce';
        $this->need_instance = 0;

        $this->bootstrap = true;

        $this->active = Configuration::get($this->name . '_active');
        $this->use_custom_hook = Configuration::get($this->name . '_custom_hook');
        $this->carrier_id = Configuration::get(self::PREFIX . $this->name);
        parent::__construct();
        $this->yn = array(array('id' => '1', 'value' => true, 'label' => $this->l('Yes')),
            array('id' => '0', 'value' => false, 'label' => $this->l('No')));
        $this->displayName = $this->l('Order in one click');
        $this->description = $this->l('Adds a button for quick checkout on product and category page');
        $this->confirmUninstall = $this->l('Do you really want to uninstall the module?');
        $this->ps_versions_compliancy = array('min' => '1.6.1', 'max' => '1.6.99.99');
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayProductListFunctionalButtons')
            || !$this->registerHook('displayProductListFunctionalButtons2')
            || !$this->registerHook('displayProductButtons')
            || !$this->registerHook('displayQuickOrderButton')
            //|| !Configuration::updateValue('ECM_IDORDERSTATE', 1)
             || !$this->addState()
            || !$this->installCarrier()

        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->unregisterHook('displayHeader')
            || !$this->unregisterHook('displayProductListFunctionalButtons')
            || !$this->unregisterHook('displayProductListFunctionalButtons2')
            || !$this->unregisterHook('displayProductButtons')
            || !$this->unregisterHook('displayQuickOrderButton')
            || !$this->deleteCarriers()
            || !Configuration::deleteByName('ECM_IDORDERSTATE')

        ) {
            return false;
        }

        return true;
    }
    protected function deleteCarriers()
    {

        if ($this->carrier_id) {
            $carrier = new Carrier($this->carrier_id);
            $carrier->delete();
        }
        return true;
    }

    private function installCarrier()
    {
        $carrier = $this->addCarrier();
        $this->addZones($carrier);
        $this->addGroups($carrier);
        $this->addRanges($carrier);
        return true;
    }

    protected function addCarrier()
    {
        $carrier = new Carrier();

        $carrier->name = 'One click carrier';
        $carrier->active = 0;
        $carrier->range_behavior = 1;
        $carrier->need_range = 1;
        $carrier->shipping_external = true;
        $carrier->shipping_handling = false;
        $carrier->is_module = true;
        $carrier->external_module_name = $this->name;
        $carrier->shipping_method = 1;
        $carrier->grade = 8; // 0-slow, 9-fast
        $delay = array('uk' => 'від 1 до 5 днів', 'ru' => 'от 1 до 5 дней', 'en' => '1-5 day');

        foreach (Language::getLanguages(true) as $language) {
            if (array_key_exists($language['iso_code'], $delay)) {$carrier->delay[$language['id_lang']] = $delay[$language['iso_code']];} else { $carrier->delay[$language['id_lang']] = $delay['en'];}
        }

        if ($carrier->add() == true) {
            @copy(dirname(__FILE__) . '/views/img/' . $this->name . '.jpg', _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg'); //assign carrier logo
            Configuration::updateValue(self::PREFIX . $this->name, $carrier->id);

            return $carrier;
        }

        return false;
    }

    protected function addGroups($carrier)
    {
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group) {$groups_ids[] = $group['id_group'];}
        $carrier->setGroups($groups_ids);
    }

    protected function addRanges($carrier)
    {
        $price_list = array();
        $range_weight = new RangeWeight();
        $range_weight->id_carrier = $carrier->id;
        $range_weight->delimiter1 = 0;
        $range_weight->delimiter2 = 30;
        $range_weight->add();
        foreach ($carrier->getZones() as $zone) {
            $price_list[] = array(
                'id_range_price' => null,
                'id_range_weight' => (int) $range_weight->id,
                'id_carrier' => (int) $carrier->id,
                'id_zone' => (int) $zone['id_zone'],
                'price' => 0,
            );
        }
        $carrier->addDeliveryPrice($price_list, true);
    }

    protected function addZones($carrier)
    {
        $zones = Zone::getZones();
        foreach ($zones as $zone) {$carrier->addZone($zone['id_zone']);}
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addJqueryPlugin(array('scrollTo', 'fancybox'));
        $this->context->controller->addCSS($this->_path . 'views/css/style.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/quickorder.js');
		$this->context->controller->addJS(_MODULE_DIR_ .'ecm_simcheck/views/js/maskedinput.js');
    }

    public function hookdisplayProductListFunctionalButtons($params)
    {
        if (!$this->active || $this->use_custom_hook) {
            return;
        }

        $product = $params['product'];
        $out_of_stock = Product::isAvailableWhenOutOfStock((int) $product['out_of_stock']);
        $this->context->smarty->assign(array(
            'product' => $product,
            'out_of_stock' => $out_of_stock,
        ));
        return $this->display(__FILE__, 'views/templates/front/ecm_quickorder.tpl');
    }
    public function hookdisplayProductListFunctionalButtons2($params)
    {
        return $this->hookdisplayProductListFunctionalButtons($params);
    }
    public function hookdisplayProductButtons($params)
    {
        // if (!$this->active || $this->use_custom_hook) {
        //     return;
        // }

        $product = $params['product'];
        $in_stock = StockAvailable::getQuantityAvailableByProduct((int) $product->id, (int) $product->cache_default_attribute);
        $out_of_stock = Product::isAvailableWhenOutOfStock((int) $product->out_of_stock);
        if ($in_stock <= 0 && !$out_of_stock) {
            return;
        }

        $this->context->smarty->assign(array(
            'product' => $product,
            'out_of_stock' => $out_of_stock,
            'in_stock' => intval($in_stock),
        ));
        return $this->display(__FILE__, 'views/templates/front/ecm_quickorderbutton.tpl');

    }
    public function hookdisplayQuickOrderButton($params)
    {
        //d($this->use_custom_hook);
        if (!$this->active || !$this->use_custom_hook) {
            return;
        }

        $product = $params['product'];
        if(is_array($product))$product = new Product($product['id_product']);
        $product = (is_array($product)) ?: (object)$product;
        //d($product);
        $in_stock = StockAvailable::getQuantityAvailableByProduct((int) $product->id, (int) $product->cache_default_attribute);
        $out_of_stock = Product::isAvailableWhenOutOfStock((int) $product->out_of_stock);
        if ($in_stock <= 0 && !$out_of_stock) {
            return;
        }

        $this->context->smarty->assign(array(
            'product' => $product,
            'out_of_stock' => $out_of_stock,
            'in_stock' => intval($in_stock),
        ));
        return $this->display(__FILE__, 'views/templates/front/ecm_quickorderbutton.tpl');
    }

    public function ajaxHandler($id_product, $id_product_attribute)
    {

        $product_obj = $this->getProduct((int) $id_product);
        $allow_oosp = Product::isAvailableWhenOutOfStock((int) $product_obj['out_of_stock']);
        if (!$id_product_attribute) {
            $id_product_attribute = ($product_obj['cache_default_attribute']) ? $product_obj['cache_default_attribute'] : 0;
        }
        $in_stock = StockAvailable::getQuantityAvailableByProduct((int) $id_product, (int) $id_product_attribute);
        if ($in_stock > 0) {
            $product_obj['quantity'] = $in_stock;
        }
        $price = Product::getPriceStatic((int) $id_product, true, (int) $id_product_attribute);
        if ($price) {
            $product_obj['price'] = $price;
        }
        $this->context->smarty->assign(
            $this->getConfigFormValues()
        );
        $this->context->smarty->assign(
            array(
                'product' => $product_obj,
                'id_product_attribute' => $id_product_attribute,
                'allow_oosp' => $allow_oosp,
                'stock' => $in_stock,
                'logged' => $this->context->customer->isLogged(),
                'firstname' => ($this->context->customer->logged ? $this->context->customer->firstname : false),
                'lastname' => ($this->context->customer->logged ? $this->context->customer->lastname : false),
                'email' => ($this->context->customer->logged ? $this->context->customer->email : false),

            )
        );
        return $this->display(__FILE__, 'views/templates/front/formproduct.tpl');
    }
    protected function getProduct($id_product)
    {
        $id_lang = $this->context->cookie->id_lang;
        $sql = 'SELECT p.*, product_shop.*, stock.`out_of_stock` out_of_stock, pl.`description`, pl.`description_short`,
						pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
						p.`ean13`, p.`upc`, image_shop.`id_image` id_image, il.`legend`
					FROM `' . _DB_PREFIX_ . 'product` p
					LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
						p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
					)
					' . Shop::addSqlAssociation('product', 'p') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
						ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->context->shop->id . ')
					LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
					' . Product::sqlStock('p', 0) . '
					WHERE p.id_product = ' . (int) $id_product;

        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        if (!$row) {
            return false;
        }
        return Product::getProductProperties($id_lang, $row);
    }

    public function hookAjaxCall($params)
    {
        $pass = Tools::encrypt($password = Tools::passwdGen(MIN_PASSWD_LENGTH));
        $status = array();

        if (!Tools::isEmpty($params['firstname']) && Validate::isName($params['firstname']) &&
            !Tools::isEmpty($params['lastname']) && Validate::isName($params['lastname']) &&
            Validate::isEmail($params['email']) && !Tools::isEmpty($params['phone']) &&
            !Tools::isEmpty($params['id_product'])) {
            $id_customer = Customer::customerExists($params['email'], true);
            if (!$id_customer) {
                if ($params['email']) {
                    Mail::Send(
                        $this->context->cookie->id_lang,
                        'ecm_quick_mail',
                        Mail::l('Your login and password', $this->context->cookie->id_lang),
                        array('{firstname}' => $params['firstname'], '{lastname}' => $params['lastname'], '{email}' => $params['email'], '{pass}' => $password),
                        $params['email'],
                        null, null, null, null, null, dirname(__FILE__) . '/mails/', false, $this->context->shop->id
                    );
                }
                $customer = new Customer();
                $customer->firstname = $params['firstname'];
                $customer->lastname = $params['lastname'];
                $customer->email = $params['email'];
                $customer->passwd = $pass;
                $customer->active = 1;
                $customer->logged = 1;
                $customer->phone = $params['phone'];
                //d($customer);
                $customer->add();
                $id_customer = $customer->id;

            }
            $id_address = $this->createAddress($params, $id_customer);
            $customer = new Customer($id_customer);
            $product = $this->getProduct((int) $params['id_product']);
            $in_stock = StockAvailable::getQuantityAvailableByProduct((int) $params['id_product'], (int) $params['id_product_attribute']);
            if ($in_stock > 0) {
                $product['quantity'] = $in_stock;
            }

            $out_of_stock = Product::isAvailableWhenOutOfStock((int) $product['out_of_stock']);

            if (!$out_of_stock && $params['quantity'] > $in_stock) {
                $status['error'] = sprintf($this->l('You must add %d maximum quantity or product is out of stock'), $in_stock);
            } else {

                $id_product_attribute = $params['id_product_attribute'];
                if (!$id_product_attribute) {
                    $id_product_attribute = (!$params['id_product_attribute'] && $product['cache_default_attribute']) ? $product['cache_default_attribute'] : 0;
                }

                $order = $this->createOrder($id_customer, $id_address, $this->carrier_id, $params['quantity'], $params['id_product'], $id_product_attribute);
				
				$id = $id_product_attribute ? $params['id_product'].'c'.$id_product_attribute : $params['id_product'];
                ob_start();
                $this->context->smarty->assign(array('error' => 'ok', 'id_product' => $id, 'value' => $order['value'], 'order' => $order));
                $status['ok'] = $this->display(__FILE__, 'views/templates/front/error.tpl');
                ob_flush();
            }
        } else if (Tools::isEmpty($params['firstname']) || !Validate::isName($params['firstname'])) {
            $status['error'] = $this->l('firstname is required.');
        } else if (Tools::isEmpty($params['lastname']) || !Validate::isName($params['lastname'])) {
            $status['error'] = $this->l('lastname is required.');
        } else if (Tools::isEmpty($params['phone'])) {
            $status['error'] = $this->l('phone is required.');
        } else if (!Validate::isEmail($params['email'])) {
            $status['error'] = $this->l('email is invalid.');
        } else {
            $status['error'] = 1;
        }

        return Tools::jsonEncode($status);
    }
    protected function createAddress($params, $id_customer)
    {
        $address = new Address;
        $address->id_country = (int) Country::getDefaultCountryId();
        $address->id_state = 1;
        $address->id_customer = $id_customer;
        $address->alias = $this->l('Address');
        $address->company = '';
        $address->lastname = $params['lastname'];
        $address->firstname = $params['firstname'];
        $address->address1 = $this->l('Address Unknown');
        $address->city = $this->l('City Unknown');
        $address->active = 1;
        $address->phone = $params['phone'] == '' ? $this->l('Phone Unknown') : $params['phone'];
        $address->phone_mobile = $params['phone'] == '' ? $this->l('Phone Unknown') : $params['phone'];
        $address->add();
        return $address->id;
    }

    protected function createOrder($id_customer, $id_address, $id_carrier, $qty, $id_product, $id_product_attribute)
    {

        $current_state = Configuration::get('ECM_IDORDERSTATE');
        $module = 'ecm_buyme';
        $currency = new Currency((int) $this->context->cookie->id_currency);
        $this->context->currency = $currency;
        $this->context->customer = new Customer($id_customer);
        $this->context->cart = new Cart();
        $this->context->cart->recyclable = $this->context->cart->gift = 0;
        $this->context->cart->secure_key = $this->context->customer->secure_key;
        $this->context->cart->id_address_delivery = $id_address;
        $this->context->cart->id_address_invoice = $id_address;
        $this->context->cart->id_currency = $this->context->currency->id;
        $this->context->cart->id_lang = (int) $this->context->language->id;
        $this->context->cart->id_carrier = $id_carrier;
        $this->context->cart->id_customer = $id_customer;
        $this->context->cart->id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        $this->context->cart->setNoMultishipping();
        $this->context->cart->add();
        $id_cart = (int) $this->context->cart->id;
        $this->context->cart->updateQty($qty, $id_product, $id_product_attribute);
        $this->context->cart->update();
        //d( $qty);
        $params = ['cart' => $this->context->cart, 'force' => true];
        Module::HookExec('ActionCartSave', $params);

        $total = $this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $total_ti = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $discount = $this->context->cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS);
        $discount_ti = $this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
        $paid = Tools::ps_round((float) $this->context->cart->getOrderTotal(false, Cart::BOTH_WITHOUT_SHIPPING), 2);
        $paid_ti = Tools::ps_round((float) $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), 2);
        $this->context->order = new Order();
        $this->context->order->id_address_delivery = $id_address;
        $this->context->order->id_address_invoice = $id_address;
        $this->context->order->id_currency = $this->context->currency->id;
        $this->context->order->id_lang = (int) $this->context->language->id;
        $this->context->order->id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        $this->context->order->id_customer = $id_customer;
        $this->context->order->id_cart = $id_cart;
        $this->context->order->id_carrier = $id_carrier;
        $this->context->order->payment = $this->l('One click order');
        $this->context->order->module = $module;
        $this->context->order->recyclable = 0;
        $this->context->order->total_discounts = $this->context->order->total_discounts_tax_incl = $discount_ti;
        $this->context->order->total_discounts_tax_excl = $discount;
        $this->context->order->total_shipping = $this->context->cart->getPackageShippingCost($id_carrier);
        $this->context->order->total_shipping_tax_incl = $this->context->cart->getPackageShippingCost($id_carrier);
        $this->context->order->total_shipping_tax_excl = $this->context->cart->getPackageShippingCost($id_carrier, false);
        $this->context->order->total_paid = $this->context->order->total_paid_tax_incl = $paid_ti;
        $this->context->order->total_paid_tax_excl = $paid;
        $this->context->order->total_products = $total;
        $this->context->order->total_products_wt = $total_ti;
        $this->context->order->current_state = $current_state;
        $this->context->order->total_paid_real = 0;
        $this->context->order->conversion_rate = 1;
        $this->context->order->secure_key = $this->context->customer->secure_key;
        $this->context->order->add();
        $reference = 'reference'; //Configuration::get('ecm_oneclick_order_ref')

        switch ($reference) {
            case 'reference':
                do {
                    $reference = Order::generateReference();
                } while (Order::getByReference($reference)->count());
                $this->context->order->reference = $reference;
                break;
            case 'id_cart':
                $this->context->order->reference = $this->context->order->id_cart;
                break;
            case 'id_order':
                $this->context->order->reference = $this->context->order->id;
                break;
        }
        $this->context->order->update();
        $id_order = $this->context->order->id;
        $OrderDetail = new OrderDetail();
        $OrderDetail->createList($this->context->order, $this->context->cart, $current_state, $this->context->cart->getProducts(), 0);

        $cartRules = $this->context->cart->getCartRules();
        if ($cartRules) {
            foreach ($cartRules as $rule) {
                $cart_rule = new CartRule($rule['id_cart_rule']);
                $values = array(
                    'tax_incl' => $cart_rule->getContextualValue(true),
                    'tax_excl' => $cart_rule->getContextualValue(false),
                );
                $OrderCartRule = new OrderCartRule();
                $OrderCartRule->id_order = $id_order;
                $OrderCartRule->id_cart_rule = $rule['id_cart_rule'];
                $OrderCartRule->name = $rule['name'];
                $OrderCartRule->value = $values['tax_incl'];
                $OrderCartRule->value_tax_excl = $values['tax_excl'];
                $OrderCartRule->save();
            }
        }

        $OrderCarrier = new OrderCarrier();
        $OrderCarrier->id_order = $id_order;
        $OrderCarrier->id_carrier = $id_carrier;
        $OrderCarrier->shipping_cost_tax_incl = $this->context->order->total_shipping_tax_incl;
        $OrderCarrier->shipping_cost_tax_excl = $this->context->order->total_shipping_tax_excl;
        $OrderCarrier->add();

        $OrderHistory = new OrderHistory();
        $OrderHistory->id_order = (int) $id_order;
        $OrderHistory->id_employee = (int) @$this->context->employee->id ?: 1;
        $OrderHistory->changeIdOrderState($current_state, $id_order, true);
        $OrderHistory->addWithemail();
        $params['order'] = $this->context->order;
        $params['customer'] = $this->context->customer;
        $params['currency'] = new Currency($this->context->order->id_currency);
        $params['cart'] = $this->context->cart;
        $params['orderStatus'] = new OrderState($current_state);
        if (Module::isInstalled('ecm_smssender') && Module::isEnabled('ecm_smssender')) {
            require_once _PS_MODULE_DIR_ . 'ecm_smssender/classes/turbosms.php';
            require_once _PS_MODULE_DIR_ . 'ecm_smssender/classes/message.php';
            $login = Configuration::get('ECM_SMSSENDER_ACCOUNT');
            $pwd = Configuration::get('ECM_SMSSENDER_ACCOUNT_PASSWORD');
            $sender = Configuration::get('ECM_SMSSENDER_ACCOUNT_ALFA');
            $phone = Configuration::get($this->name . '_adm_phone');
            $smssender = new Client($login, $pwd, $sender);
            $message = $this->l('Order in 1-click id №').$id_order.$this->l(' added in your shop');
            if($phone)
            $smssender->send($phone, $message);
        }
        Module::HookExec('ActionValidateOrder', $params);
        $product = new Product($id_product);
		return  [
            'value' => $this->context->order->total_products,
            'transactionId' => $this->context->order->reference,
            'transactionTotal' => $this->context->order->total_products,
            'sku' => $product->reference,
            'name' => $product->name[$this->context->order->id_lang],
            'price' => $this->context->order->total_products,
            'quantity' => $qty,
        ];
    }
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
                        'type' => 'switch',
                        'label' => $this->l('Module active'),
                        'name' => $this->name . '_active',
                        'is_bool' => true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Hide firstname'),
                        'name' => $this->name . '_hide_firstname',
                        'is_bool' => true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Hide lastname'),
                        'name' => $this->name . '_hide_lastname',
                        'is_bool' => true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Hide phone'),
                        'name' => $this->name . '_hide_phone',
                        'is_bool' => true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Hide email'),
                        'name' => $this->name . '_hide_email',
                        'is_bool' => true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Hide advansed block'),
                        'name' => $this->name . '_hide_adv_block',
                        'is_bool' => true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Custom hook'),
                        'name' => $this->name . '_custom_hook',
                        'desc' => $this->l('Insert this code') . ' {hook h="displayQuickOrderButton" product=$product}	 ' . $this->l('in needed place in your product.tpl file if checked this option'),
                        'is_bool' => true,
                        'values' => $this->yn,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Admin shop phone'),
                        'desc' => $this->l('Phone for order notification shop\'s admin. This option worked, if module ecm_smssender installed and enabled'),
                        'name' => $this->name . '_adm_phone',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Email для уведомлений'),
                        'desc' => $this->l('Список email через запятую'),
                        'name' => $this->name . '_adm_emails',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $form_fields = array(
            $this->name . '_active' => Configuration::get($this->name . '_active'),
            $this->name . '_hide_firstname' => Configuration::get($this->name . '_hide_firstname'),
            $this->name . '_hide_lastname' => Configuration::get($this->name . '_hide_lastname'),
            $this->name . '_hide_phone' => Configuration::get($this->name . '_hide_phone'),
            $this->name . '_hide_email' => Configuration::get($this->name . '_hide_email'),
            $this->name . '_hide_adv_block' => Configuration::get($this->name . '_hide_adv_block'),
            $this->name . '_custom_hook' => Configuration::get($this->name . '_custom_hook'),
            $this->name . '_adm_phone' => Configuration::get($this->name . '_adm_phone'),
            $this->name . '_adm_emails' => Configuration::get($this->name . '_adm_emails'),
        );
        return $form_fields;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {

            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitecm_quickorder')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = '';

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
        $helper->submit_action = 'submitecm_quickorder';
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
    private function addState()
    {
        $sql = "SELECT `id_order_state` FROM " . _DB_PREFIX_ . "order_state_lang
        WHERE
        `id_lang` = '" . (int) Configuration::get('PS_LANG_DEFAULT') . "' AND
        `name` = 'QuickOrder'
        ";
        $id_order_state = Db::getInstance()->getValue($sql);
        if (!$id_order_state) {
            $color = sprintf('#%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255));
            Db::getInstance()->Execute("
                INSERT INTO `" . _DB_PREFIX_ . "order_state` (`unremovable`,`color`)
                VALUES (0,'" . $color . "')
                ");
            $id_order_state = Db::getInstance()->Insert_ID();
            Db::getInstance()->Execute("
                INSERT INTO `" . _DB_PREFIX_ . "order_state_lang` (`id_order_state`,`id_lang`,`name`)
                VALUES (" . $id_order_state . "," . (int) Configuration::get('PS_LANG_DEFAULT') . ", '" . pSQL('QuickOrder') . "')
                ");
        }
        if ($id_order_state) {
            Configuration::updateValue('ECM_IDORDERSTATE', $id_order_state);
        }

        return true;
    }

}
