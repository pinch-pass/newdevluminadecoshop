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

function upgrade_module_2_5_7($object)
{
    $sql = 'ALTER TABLE `'._DB_PREFIX_.'opc_field_lang` ADD `label` VARCHAR(255) NULL AFTER `description`';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);

    $object->registerHook('registerGDPRConsent');
    $object->registerHook('actionDeleteGDPRCustomer');

    $object->deleteEmptyAddressesOPC();

    return true;
}
