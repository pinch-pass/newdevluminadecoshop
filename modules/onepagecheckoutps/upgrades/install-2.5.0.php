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

function upgrade_module_2_5_0($object)
{
    $update_field = 'UPDATE '._DB_PREFIX_.'opc_field';
    $update_field .= ' SET type_control = "textbox"';
    $update_field .= ' WHERE id_field = 13';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_field);

    $update_field = 'UPDATE '._DB_PREFIX_.'opc_field';
    $update_field .= ' SET type_control = "textbox"';
    $update_field .= ' WHERE id_field = 29';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_field);

    $object->deleteEmptyAddressesOPC();

    return true;
}
