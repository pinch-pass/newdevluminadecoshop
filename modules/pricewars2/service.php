<?php
	if(!extension_loaded('ionCube Loader'))
		die('Brak zainstalowanego lub niepoprawnie skonfigurowane rozszerzenie IonCube Loader na serwerze. Prosimy skontaktować się z administracją serwera.');
	define('PW2_XML_IN_PROGRESS', true);
	require_once('../../config/config.inc.php');
    
    // Dodatkowa, własna konfiguracja inicjalizacji
    if(file_exists(__DIR__ . '/service.init.php'))
        require_once(__DIR__ . '/service.init.php');

	if(isset($_GET['showerrors'])){
		ini_set('error_reporting', 'On');
		error_reporting(E_ALL);
	}
	if(class_exists('Context', false)) {
		$fc = new FrontController;
		$fc->init();
	}
	if( $mem_limit = (int) Configuration::get('PW2_MEM_LIMIT', null, 0, 0))
		ini_set("memory_limit", $mem_limit."M");
	
	if( $time_limit = (int) Configuration::get('PW2_SCRIPT_TIMEOUT', null, 0, 0))
		set_time_limit($time_limit);
	
	include_once('./pricewars2.php');
	
	try {
		$pricewars = new Pricewars2();
		$pricewars->generateXML();
	} catch (Exception $e){
		header("Content-type: text/plain");
		echo $e->getMessage();
	}