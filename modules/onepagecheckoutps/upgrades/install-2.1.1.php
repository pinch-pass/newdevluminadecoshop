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

function upgrade_module_2_1_1($object)
{
    $object = $object;

    $sql = 'ALTER TABLE `'._DB_PREFIX_.'opc_payment` ADD `force_display` TINYINT(1) NOT NULL AFTER `name`';
    Db::getInstance()->execute($sql);

    return true;
}
