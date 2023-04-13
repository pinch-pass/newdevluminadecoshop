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

function upgrade_module_2_8_5($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $module_obj->prepareDatabaseTables();
    $upd_rows = array();
    $selectors_data = array('iconclass' => 'af_classes', 'themeclass' => 'af_classes', 'themeid' => 'af_ids');
    $prev_settings_rows = $module_obj->db->executeS('SELECT * FROM '._DB_PREFIX_.'af_general_settings');
    foreach ($prev_settings_rows as $r) {
        $settings = $r['settings'] ? Tools::jsonDecode($r['settings'], true) : array();
        $upd_settings = array();
        foreach ($module_obj->getSettingsFields('general', false) as $name => $field) {
            $upd_settings['general'][$name] = isset($settings[$name]) ? $settings[$name] : $field['value'];
        }
        foreach ($selectors_data as $key => $prev_key) {
            foreach (array_keys($module_obj->getSelectors($key)) as $name) {
                $upd_settings[$key][$name] = isset($settings[$prev_key][$name]) ? $settings[$prev_key][$name] : $name;
            }
        }
        foreach ($upd_settings as $type => $settings) {
            $upd_rows[] = '('.(int)$r['id_shop'].', \''.pSQL($type).'\', \''.pSQL(Tools::jsonEncode($settings)).'\')';
        }
    }
    if ($upd_rows) {
        $module_obj->db->execute('REPLACE INTO '._DB_PREFIX_.'af_settings VALUES '.implode(', ', $upd_rows));
    }
    return true;
}
