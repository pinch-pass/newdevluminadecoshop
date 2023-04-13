<?php

/*
 * Stworzono przez SEIGI http://pl.seigi.eu/
 * Wszelkie prawa zastrzeżone.
 * Zabrania się modyfikacji używania i udostępniania kodu bez zgody lub odpowiedniej licencji.
 * Utworzono  : 2018-06-14
 * Author     : SEIGI - Grzegorz Zawadzki <kontakt@seigi.eu>
 */

require '../../config/config.inc.php';
require './seigiyandexcsv.php';
$fc = new FrontController;
$fc->init();
$x = new seigiyandexcsv();
$filename = __DIR__ . '/temp/without_lumnina.csv';
$x->generujCSV($filename, ' AND id_manufacturer != 4 ');
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=without_lumnina.csv");
echo file_get_contents($filename);