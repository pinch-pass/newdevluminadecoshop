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

function upgrade_module_2_6_5($object)
{
    $object = $object;
    
    Configuration::updateValue('OPC_DEFAULT_CARRIER', '');
    
    Configuration::updateValue('OPC_REPLACE_AUTH_CONTROLLER', true);
    Configuration::updateValue('OPC_REPLACE_IDENTITY_CONTROLLER', true);
    Configuration::updateValue('OPC_REPLACE_ADDRESSES_CONTROLLER', true);

    Configuration::updateValue('OPC_REQUIRED_LOGIN_CUSTOMER', true);

    $sql = 'UPDATE `'._DB_PREFIX_.'opc_field_shop` fs INNER JOIN `';
    $sql .= _DB_PREFIX_.'opc_field` f ON fs.id_field = f.id_field SET fs.`group` = f.`object`';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);

    return true;
}
