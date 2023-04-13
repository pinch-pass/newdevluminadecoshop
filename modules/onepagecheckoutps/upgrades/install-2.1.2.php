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

function upgrade_module_2_1_2($object)
{
    $object = $object;
    Configuration::updateValue('OPC_ALREADY_REGISTER_BUTTON', '');
    Configuration::updateValue('OPC_ALREADY_REGISTER_BUTTON_TEXT', '');
    Configuration::updateValue('OPC_THEME_LOGIN_BUTTON', '');
    Configuration::updateValue('OPC_THEME_LOGIN_BUTTON_TEXT', '');
    Configuration::updateValue('OPC_THEME_VOUCHER_BUTTON', '');
    Configuration::updateValue('OPC_THEME_VOUCHER_BUTTON_TEXT', '');
    Configuration::updateValue('OPC_BACKGROUND_BUTTON_FOOTER', '');
    Configuration::updateValue('OPC_THEME_BORDER_BUTTON_FOOTER', '');
    Configuration::updateValue('OPC_THEME_SELECTED_TEXT_COLOR', '');
    Configuration::updateValue('OPC_CONFIRMATION_BUTTON_FLOAT', 1);

//    $sql = 'ALTER TABLE `'._DB_PREFIX_.'opc_payment` ADD `force_display` TINYINT(1) NOT NULL AFTER `name`';
//    Db::getInstance()->execute($sql);

    return true;
}
