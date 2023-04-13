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

function upgrade_module_2_0_7($object)
{
    $object = $object;
    
    Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'opc_social_customer` (
		`id` varchar(50) NOT NULL,
		`id_customer` int(10) NOT NULL,
		`network` varchar(50) NOT NULL,
		PRIMARY KEY (`id`)
		)
		ENGINE=MyISAM DEFAULT CHARSET=utf8;');

    $scope_google = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
    $json_networks = array(
        'facebook' => array(
            'network'       => 'Facebook',
            'name_network'  => 'Facebook',
            'client_id'     => '',
            'client_secret' => '',
            'scope'         => 'email,public_profile',
            'class_icon'    => 'fa-facebook'
        ),
        'google' => array(
            'network'       => 'Google',
            'name_network'  => 'Google',
            'client_id'     => '',
            'client_secret' => '',
            'scope' => $scope_google,
            'class_icon'    => 'fa-google'
        )
    );
    Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($json_networks));

    return true;
}
