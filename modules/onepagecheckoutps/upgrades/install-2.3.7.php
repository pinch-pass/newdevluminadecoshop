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

function upgrade_module_2_3_7($object)
{
    $object = $object;

    Configuration::updateValue('OPC_CONFIRM_ADDRESS', false);
    Configuration::updateValue('OPC_REMOVE_LINK_PRODUCTS', true);
    Configuration::updateValue('OPC_SHOW_ORDER_MESSAGE', true);

    return true;
}
