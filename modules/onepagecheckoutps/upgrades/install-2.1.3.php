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

function upgrade_module_2_1_3($object)
{
    $object = $object;

    Configuration::updateValue('OPC_OVERRIDE_CSS', '');
    Configuration::updateValue('OPC_OVERRIDE_JS', '');

//    $sql = 'ALTER TABLE `'._DB_PREFIX_.'opc_payment_shop` DROP `force_display`;';
//    Db::getInstance()->execute($sql);

    Configuration::updateValue('OPC_ENABLE_DEBUG', '0');
    Configuration::updateValue('OPC_IP_DEBUG', '');

    return true;
}
