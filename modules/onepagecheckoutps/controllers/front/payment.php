<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2019 PresTeamShop
 * @license   see file: LICENSE.txt
 * @category  PrestaShop
 * @category  Module
 */

class OnePageCheckoutPSPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public $display_column_right = false;
    public $module_payment;


    public function init()
    {
        parent::init();

        $this->module_payment = Module::getInstanceByName(Tools::getValue('pm'));
    }

    public function initContent()
    {
        parent::initContent();

        if (Validate::isLoadedObject($this->module_payment)) {
            $result = '';

            if (method_exists($this->module_payment, 'hookPayment')) {
                $result = $this->module_payment->hookPayment(array('cart' => $this->context->cart));
            } elseif (method_exists($this->module_payment, 'hookDisplayPayment')) {
                $result = $this->module_payment->hookDisplayPayment(array('cart' => $this->context->cart));
            }

            $this->context->smarty->assign('HOOK_PAYMENT_METHOD', $result);

            $this->setTemplate('payment_execution.tpl');
        }
    }

    public function postProcess()
    {
        $this->context->smarty->assign('page_name', 'order-opc');
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS($this->module->getPathUri().'views/css/front/payment.css', 'all');
        $this->addCSS($this->module->getPathUri().'views/css/front/override.css', 'all');
    }
}
