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

function upgrade_module_2_2_1($object)
{
    $object = $object;
    
    $json_networks = Tools::jsonDecode(Configuration::get('OPC_SOCIAL_NETWORKS'));

    $json_networks->facebook->class_icon = 'fa-pts-facebook';
    $json_networks->google->class_icon = 'fa-pts-google';

    Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($json_networks));

    if (file_exists(dirname(__FILE__).'/../lib/upload_update.php')) {
        unlink(dirname(__FILE__).'/../lib/upload_update.php');
    }
    if (file_exists(dirname(__FILE__).'/../log/get_logs.php')) {
        unlink(dirname(__FILE__).'/../log/get_logs.php');
    }
    if (file_exists(dirname(__FILE__).'/../actions.php')) {
        unlink(dirname(__FILE__).'/../actions.php');
    }

    return true;
}
