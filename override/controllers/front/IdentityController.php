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
class IdentityController extends IdentityControllerCore
{
    /*
    * module: onepagecheckoutps
    * date: 2020-06-01 19:07:22
    * version: 2.7.2
    */
    public function init()
    {
        if (Module::isInstalled('onepagecheckoutps')) {
            $opc = Module::getInstanceByName('onepagecheckoutps');
            if (Validate::isLoadedObject($opc) && $opc->active) {
                if ($opc->core->isVisible()) {
                    if ($opc->config_vars['OPC_REPLACE_IDENTITY_CONTROLLER']) {
                        $link = $this->context->link->getPageLink('order-opc');
                        $params = '?rc=1&rc_page=customer&'.$_SERVER['QUERY_STRING'];
                        Tools::redirectLink($link.$params);
                    }
                }
            }
        }
        parent::init();
    }
}
