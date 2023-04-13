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
	
	if($id_xml == 1){
		// Jeśli chcemy, aby warunek był użyty tylko dla jednego XML'a o ID 1
	
		// Aby pominąć produkt z eksportu, należy wyrzucić wyjatek.
		throw new \pricewars2\pricewarsSkipException('Nie chce tego produktu z własnej, niewymuszonej woli :)');
	
	}
	// Wymusza availability dla danego produktu na 1. Dostępne wartośći to 1,3,7,14,99 (w przypadku ceneo)
	$product->availability_force;
	
	// UWAGA Ta wartość zostanie nadpisana przez availability force lub też przez funkcje decydujące o stanie availabilty. Używamy tylko availability force jak wyżej
	$product->availability;
	
	// Realna ilość dostępnego produktu w sklepie (pobierana z bazy)
	$product->quantity;
	
	// Quantity Overwrite - Wartość eksportowana w XML, chyba że jest ustawiona na false, wtedy pobierana jest wartość realna.
	$product->quantity_o; 
	
	// Status dla google: <g:availability>
	$product->google_state;
	
	// Inne dostępne pola: 
	// var_dump($product);
	
}