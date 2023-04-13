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

function upgrade_module_2_0_5($object)
{
    Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opc_field_cart` (
		`id_field` int(10) NOT NULL,
		`id_cart` int(10) NOT NULL,
		`id_option` int(10) NULL,
		`value` varchar(255) NULL,
		PRIMARY KEY (`id_field`, `id_cart`)
		)
		ENGINE=MyISAM DEFAULT CHARSET=utf8;');

    $object->registerHook('displayAdminOrder');

    return true;
}
