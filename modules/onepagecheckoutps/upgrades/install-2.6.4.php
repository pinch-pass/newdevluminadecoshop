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

function upgrade_module_2_6_4($object)
{
    $object = $object;
    
    Configuration::updateValue('OPC_SUGGESTED_ADDRESS_GOOGLE', true);

    Configuration::updateValue('OPC_INSERT_ISO_CODE_IN_DELIV_DNI', 0);
    Configuration::updateValue('OPC_INSERT_ISO_CODE_IN_INVOI_DNI', 0);
    
    return true;
}
