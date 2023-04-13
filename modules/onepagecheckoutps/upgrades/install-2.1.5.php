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

function upgrade_module_2_1_5($object)
{
    $object = $object;

    Configuration::updateValue('OPC_DEFAULT_PAYMENT_METHOD', '');
    Configuration::updateValue('OPC_SHOW_AVAILABILITY', 0);
    Configuration::updateValue('OPC_SHOW_ZOOM_IMAGE_PRODUCT', 1);

    $scope_google = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
    $json_networks = array(
        'facebook' => array(
            'network'       => 'Facebook',
            'name_network'  => 'Facebook',
            'client_id'     => '',
            'client_secret' => '',
            'scope'         => 'email,public_profile',
            'class_icon'    => 'fa-pts-facebook'
        ),
        'google' => array(
            'network'       => 'Google',
            'name_network'  => 'Google',
            'client_id'     => '',
            'client_secret' => '',
            'scope' => $scope_google,
            'class_icon'    => 'fa-pts-google'
        )
    );
    Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($json_networks));

    return true;
}
