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
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public $guestAllowed = true;
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public $ssl = true;
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public $opc;
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public $is_active_module;
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
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
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public function initContent()
    {
        parent::initContent();
        if (!$this->is_active_module) {
            return;
        }
        $this->opc->initContentControllerOPC($this);
    }
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public function postProcess()
    {
        parent::postProcess();
        if (!$this->is_active_module) {
            return;
        }
        $this->opc->postProcessControllerOPC($this);
    }
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public function setMedia()
    {
        parent::setMedia();
        if (!$this->is_active_module) {
            return;
        }
        $this->opc->setMediaControllerOPC();
    }
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    protected function _assignAddress()
    {
        if (!$this->is_active_module) {
            parent::_assignAddress();
        }
    }
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    protected function _assignCarrier()
    {
        if (!$this->is_active_module) {
            parent::_assignCarrier();
        }
    }
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    protected function _assignPayment()
    {
        if (!$this->is_active_module) {
            parent::_assignPayment();
        }
    }
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public function opcAssignWrappingAndTOS()
    {
        $this->_assignWrappingAndTOS();
    }
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public function opcAssignSummaryInformations()
    {
        $this->_assignSummaryInformations();
    }
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public function opcUpdateMessage($message)
    {
        $this->_updateMessage($message);
    }
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public function updateCarrier()
    {
        $this->_processCarrier();
        return array('hasError' => !empty($this->errors), 'errors' => $this->errors);
    }
}
