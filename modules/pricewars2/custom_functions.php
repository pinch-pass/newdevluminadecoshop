<?php
// Plik działa tylko w wersji Premium
// Jeśli chcemy, aby ten plik działał musimy mu zmienić nazwę na custom_functions.php. Pozwoli to uniknąć nadpisania zmodyfikowanego pliku podczas aktualizacji modułu.
// (a tak serio, to wystarczy że w sklepie będzie zdefiniowana funkcja z tą nazwą, byle byłaby zadeklarowana przed generowaniem XML :) )
	
/**
 * Funkcja modyfikująca obiekt tuż przed jego wygenerowaniem do XML 
 * @param stdClass $product Obiekt produktu
 * @param int $id_xml ID XML'a który jest właśnie przetwarzany, przydatne do warunków
 * @param int $id_lang ID języka dla presty.
 * @param string $service Typ XML'a według którego generowana jest oferta
 * @param string $settings Tablica z ustawieniami
 * @return void
 */
function pricewars2_product(\pricewars2\product $product, $id_xml, $id_lang, $service, $settings, $module){
	// ta funkcja jest też dostępna pod hakiem: actionPricewarsBeforeOutput
	if(empty($product->manufacturer))
		$product->manufacturer = 'Lumina Deco';
	
}