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

function upgrade_module_2_2_6($object)
{
    $object = $object;
    
    $json_networks = Configuration::get('OPC_SOCIAL_NETWORKS');
    $json_networks = Tools::jsonDecode($json_networks);

    $json_networks->paypal = array(
        'network'       => 'Paypal',
        'name_network'  => 'Paypal',
        'client_id'     => '',
        'client_secret' => '',
        'scope' => 'openid profile email address',
        'class_icon'    => 'fa-pts-paypal',
    );

    Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($json_networks));

    $sql = 'ALTER TABLE `'._DB_PREFIX_.'opc_field` ADD `capitalize` tinyint(1) NOT NULL';
    Db::getInstance()->execute($sql);

    return true;
}
