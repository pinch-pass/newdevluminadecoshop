<?php
/**
* 2007-2020 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2020 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

function upgrade_module_2_8_6($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $all_shop_ids = Shop::getShops(false, null, true);
    $module_obj->saveSettings('caching', array(), $all_shop_ids);
    $module_obj->registerHook('actionObjectAddAfter');
    $module_obj->registerHook('actionObjectDeleteAfter');
    $module_obj->registerHook('actionObjectUpdateAfter');
    return true;
}
