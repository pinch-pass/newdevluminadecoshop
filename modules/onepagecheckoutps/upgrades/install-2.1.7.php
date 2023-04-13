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

function upgrade_module_2_1_7($object)
{
    $object = $object;

    Configuration::updateValue('OPC_SHOW_BUTTON_REGISTER', 1);
    Configuration::updateValue('OPC_SHOW_LIST_CITIES_GEONAMES', 0);
    Configuration::updateValue('OPC_AUTO_ADDRESS_GEONAMES', 0);
    Configuration::updateValue('OPC_PAYMENT_NEED_REGISTER', 'stripejs');

    $sql = 'ALTER TABLE `'._DB_PREFIX_.'opc_payment` ADD `name_image` varchar(100) NOT NULL AFTER `name`';
    Db::getInstance()->execute($sql);

    $sql = 'SELECT * FROM '._DB_PREFIX_.'opc_payment';
    $result = Db::getInstance()->executeS($sql);

    foreach ($result as $row) {
        $sql = 'UPDATE `'._DB_PREFIX_.'opc_payment` SET `name_image` = "'.$row['name'];
        $sql .= '.gif" WHERE id_payment = '.$row['id_payment'];
        Db::getInstance()->execute($sql);
    }

    return true;
}
