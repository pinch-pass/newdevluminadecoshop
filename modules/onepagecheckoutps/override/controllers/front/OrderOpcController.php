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

class OrderOpcController extends OrderOpcControllerCore
{
    public $guestAllowed = true;
    public $ssl = true;

    public $opc;
    public $is_active_module;

    public function init()
    {
        $this->opc = Module::getInstanceByName('onepagecheckoutps');

        $this->opc->initBeforeControllerOPC($this);

        parent::init();

        if (Validate::isLoadedObject($this->opc)
            && $this->opc->core->isModuleActive($this->opc->name)
        ) {
            $this->is_active_module = true;
        } else {
            $this->is_active_module = false;
        }

        if (!$this->opc->core->checkModulePTS()) {
            $this->is_active_module = false;
        }

        if (!$this->opc->core->isVisible()) {
            $this->is_active_module = false;
        }

        if (!$this->is_active_module) {
            return;
        }

        $this->opc->initAfterControllerOPC($this);
    }

    public function initContent()
    {
        parent::initContent();

        if (!$this->is_active_module) {
            return;
        }

        $this->opc->initContentControllerOPC($this);
    }

    public function postProcess()
    {
        parent::postProcess();

        if (!$this->is_active_module) {
            return;
        }

        $this->opc->postProcessControllerOPC($this);
    }

    public function setMedia()
    {
        parent::setMedia();

        if (!$this->is_active_module) {
            return;
        }

        $this->opc->setMediaControllerOPC();
    }

    protected function _assignAddress()
    {
        if (!$this->is_active_module) {
            parent::_assignAddress();
        }
    }

    protected function _assignCarrier()
    {
        if (!$this->is_active_module) {
            parent::_assignCarrier();
        }
    }

    protected function _assignPayment()
    {
        if (!$this->is_active_module) {
            parent::_assignPayment();
        }
    }

    public function opcAssignWrappingAndTOS()
    {
        $this->_assignWrappingAndTOS();
    }

    public function opcAssignSummaryInformations()
    {
        $this->_assignSummaryInformations();
    }

    public function opcUpdateMessage($message)
    {
        $this->_updateMessage($message);
    }

    public function updateCarrier()
    {
        $this->_processCarrier();

        return array('hasError' => !empty($this->errors), 'errors' => $this->errors);
    }
}
