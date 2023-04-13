<?php
include(dirname(__FILE__) . '/../../../../config/config.inc.php');
include(dirname(__FILE__) . '/../../../../init.php');


$response = [];
$context = Context::getContext();
$data = Tools::getAllValues();

if(isset($data['name']) && isset($data['phone'])) {
    if(Mail::send(
        $context->language->id,
        'callback',
        Mail::l('Обратный звонок'),
        array(
            '{name}' => $data['name'],
            '{phone}' => $data['phone']
        ),
        Configuration::get('PFSPREORDER_ACCOUNT_EMAIL')
    )) {
        $response['status'] = 'success';
    } else {
        $response['status'] = 'error';
    }
} else {
    $response['status'] = 'error';
}

echo json_encode($response);
exit();

