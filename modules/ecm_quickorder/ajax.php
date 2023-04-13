<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$context = Context::getContext();

$form_fields = array(
	'ecm_quickorder_hide_firstname' => Configuration::get('ecm_quickorder_hide_firstname'),
	'ecm_quickorder_hide_lastname' => Configuration::get('ecm_quickorder_hide_lastname'),
	'ecm_quickorder_hide_phone' => Configuration::get('ecm_quickorder_hide_phone'),
	'ecm_quickorder_hide_email' => Configuration::get('ecm_quickorder_hide_email'),
	'ecm_quickorder_hide_adv_block' => Configuration::get('ecm_quickorder_hide_adv_block'),
);
$ecm_quickorder = Module::getInstanceByName('ecm_quickorder');
$firstname = ($form_fields['ecm_quickorder_hide_firstname'] && !Tools::getValue('firstname'))?$ecm_quickorder->l('One'):Tools::getValue('firstname');
$phone = ($form_fields['ecm_quickorder_hide_phone'] && !Tools::getValue('phone'))?123456789:Tools::getValue('phone');
$quantity =  (Tools::getValue('quantity'))?(Tools::getValue('quantity')):1;
$params=array(
	'firstname'=> $firstname,
	'phone'=>$phone,
	'id_product'=>Tools::getValue('id_product'),
	'id_product_attribute'=>Tools::getValue('id_product_attribute')
);

if(Tools::getValue('form'))
	die($ecm_quickorder->ajaxHandler(intval(Tools::getValue('id_product')),intval(Tools::getValue('id_product_attribute'))));
else {

    $errors = [];

    if (Tools::isEmpty($params['firstname']) || !Validate::isName($params['firstname'])) {
        $errors[] = 'Имя обязательно для заполнения.';
    } else if (Tools::isEmpty($params['phone'])) {
        $errors[] = 'Телефон обязателен для заполнения';
    }

    $product = new Product($params['id_product']);
    if(!Validate::isLoadedObject($product)) {
        $errors[] = 'Некорректный товар';
    }

    if(count($errors) > 0) {
        die(Tools::jsonEncode(['success' => 0, 'errors' => $errors]));
    }

    $emails = explode(',', Configuration::get('ecm_quickorder_adm_emails'));
    $product_name = Product::getProductName($product->id);
    $product_link = $context->link->getProductLink($product);

    $subject = 'Купить в один клик';
    $mailParams = [
        '{firstname}' => $firstname,
        '{phone}' => $phone,
        '{product_name}' => $product_name,
        '{product_link}' => $product_link,
    ];
    foreach ($emails as $email) {
        if (!Mail::Send(
            (int)$context->cookie->id_lang,
            'oneclick',
            $subject,
            $mailParams,
            $email,
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'ecm_quickorder/mails/'
        )
        ) {
            $errors[] = 'Ошибка отправки уведомления';
            $result = ['success' => 0, 'errors' => $errors];
            die(Tools::jsonEncode($result));
        }
    }

    if(count($errors) <= 0) {
        $result = ['success' => 1, 'data' => $mailParams];
    } else {
        $result = ['success' => 0, 'errors' => $errors];
    }

    die(Tools::jsonEncode($result));
}