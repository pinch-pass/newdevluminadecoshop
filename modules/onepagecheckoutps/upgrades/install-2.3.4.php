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

function upgrade_module_2_3_4($object)
{
    $object = $object;

    //actualizamos a 64 caracteres el campo company
    if (version_compare(_PS_VERSION_, '1.5.1.0', '>')) {
        $update_company = 'UPDATE '._DB_PREFIX_.'opc_field';
        $update_company .= ' SET size = 64';
        $update_company .= ' WHERE name = "company"';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_company);
    }

    return true;
}
