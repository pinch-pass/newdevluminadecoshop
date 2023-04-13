<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_7($object)
{
	Configuration::updateValue('iqitsearch_shower', 0);
	Configuration::updateValue('iqitsearch_categories', 0);
	Configuration::updateValue('iqitsearch_depth', 3);
	
	return true;
}
