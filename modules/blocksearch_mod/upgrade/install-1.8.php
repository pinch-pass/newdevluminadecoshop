<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_8($object)
{
	$object->registerHook('iqitMobileSearch');
	return true;
}
