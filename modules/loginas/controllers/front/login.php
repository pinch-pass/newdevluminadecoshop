<?php
/**
 * Copyright (C) 2017-2018 Petr Hucik <petr@getdatakick.com>
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@getdatakick.com so we can send you a copy immediately.
 *
 * @author    Petr Hucik <petr@getdatakick.com>
 * @copyright 2017-2018 Petr Hucik
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class LoginAsLoginModuleFrontController extends ModuleFrontControllerCore
{
    /** @var LoginAs */
    public $module;

    /**
     * @throws Adapter_Exception
     * @throws HTMLPurifier_Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();
        $customerId = (int)Tools::getValue('id_customer');
        $secret = Tools::getValue('secret');
        if ($customerId && $secret && $secret === $this->module->getSecret($customerId)) {
            $customer = new Customer($customerId);
            if (Validate::isLoadedObject($customer)) {
                if ($this->autoLogin($customer)) {
                    Tools::redirect($this->context->link->getPageLink('my-account', null, $this->context->language->id));
                }
            }
        }
        $this->setTemplate('unauthorized.tpl');
    }

    /**
     * @param Customer $customer
     * @return bool
     * @throws PrestaShopException
     */
    private function canLogin(Customer $customer)
    {
        return Customer::checkPassword($customer->id, $customer->passwd);
    }

    /**
     * @param Customer $customer
     * @return bool
     * @throws Adapter_Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function autoLogin(Customer $customer)
    {
        if ($this->canLogin($customer)) {
            Hook::exec('actionBeforeAuthentication');
            $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare : CompareProduct::getIdCompareByIdCustomer($customer->id);
            $this->context->cookie->id_customer = (int)($customer->id);
            $this->context->cookie->customer_lastname = $customer->lastname;
            $this->context->cookie->customer_firstname = $customer->firstname;
            $this->context->cookie->logged = 1;
            $this->context->cookie->is_guest = $customer->isGuest();
            $this->context->cookie->passwd = $customer->passwd;
            $this->context->cookie->email = $customer->email;

            // Add customer to the context
            $customer->logged = 1;
            $this->context->customer = $customer;

            $carts = Cart::getCustomerCarts($customer->id, true);
            if (count($carts)) {
                $cartData = array_shift($carts);
                $cart = new Cart((int)$cartData['id_cart']);
                $this->context->cart = $cart;
            }
            $this->context->cookie->id_cart = (int)$this->context->cart->id;

            $this->context->cookie->write();
            $this->context->cart->autosetProductAddress();
            Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);
            return $this->context->customer->isLogged(true);
        }
        return false;
    }
}
