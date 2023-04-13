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

function upgrade_module_2_3_0($object)
{
    $object = $object;
    
    $json_networks = Configuration::get('OPC_SOCIAL_NETWORKS');
    $json_networks = Tools::jsonDecode($json_networks);
    foreach ($json_networks as &$netword) {
        $netword->class_icon = str_replace('fa-pts-', '', $netword->class_icon);
    }

    Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($json_networks));

    return true;
}
