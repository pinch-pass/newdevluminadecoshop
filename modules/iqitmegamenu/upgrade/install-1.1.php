<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1($object)
{
	Configuration::updateValue('iqitmegamenu_hor_search_width', 200);
	$object->registerHook('iqitMegaMenu');
	return true;
}
