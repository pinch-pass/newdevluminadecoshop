<?php
include(dirname(__FILE__) . '/../../../../config/config.inc.php');
include(dirname(__FILE__) . '/../../../../init.php');

$name = trim($_POST["name"]);
$surname = trim($_POST["surname"]);
$phone =  trim($_POST["phone"]);
$phone = preg_replace('/[^0-9]/', '', $phone);
//$phone = (int)$phone;
// $email = trim($_POST["email"]);
$product = trim($_POST["product"]);
$formname = trim($_POST["formname"]);
$messenger = trim($_POST["radio-messenger"]);
$message_text = trim($_POST["message_text"]);
//$user_name = trim($_POST["user_name"]);

$return_data = array();
if (condition){
    $return_data['status'] = 'success';
//    $query = "INSERT INTO ps_pfspreorder SET  form_name='$formname', status='НОВОЕ', messenger='$messenger', product='$product', fname='$name', lname='$surname',  other='$message_text', phone='$phone'";
//    Db::getInstance()->Execute($query);

    $recepient = "prestageneration@gmail.com";
    $sitename = "rizdvo";
    $message = "Имя: $name \nТелефон: $phone \nСообщение: $message_text \nТовар: $product " ;
    $pagetitle = $formname;
    mail($recepient, $pagetitle, $message, "Content-type: text/plain; charset=\"utf-8\"\n From: $recepient");
} else {
    $return_data['status'] = 'info';
}

echo json_encode($return_data);
exit();

