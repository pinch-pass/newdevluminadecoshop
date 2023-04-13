<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_6($object)
{
	$object->registerHook('displayAfterIqitMegamenu');
	Configuration::updateValue('iqitsearch_hook', 1);
	
	$text = array(Context::getContext()->language->id => '<p style="text-align: center;"><em class="icon icon-plane"> </em>  International shipping     <em class="icon icon-plane">  </em>Secure payment</p>');
	Configuration::updateValue('iqitsearch_text', $text, true);

	return true;
}
