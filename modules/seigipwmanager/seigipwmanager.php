<?php
/* 
 * Stworzono przez SEIGI http://pl.seigi.eu/
 * Wszelkie prawa zastrzeżone.
 * Zabrania się modyfikacji używania i udostępniania kodu bez zgody lub odpowiedniej licencji.
 * Utworzono  : Dec 29, 2015
 * Author     : SEIGI - Grzegorz Zawadzki <kontakt@seigi.eu>
 */

if(Configuration::get('SEI_BLOCK_seigipwmanager') == 1 ||  !extension_loaded('ionCube Loader') || ioncube_loader_iversion() < 100202 || PHP_VERSION_ID < 50600 || PHP_VERSION_ID > 70399){

	class seigipwmanager extends Module {
		public function __construct()
		{
			$this->name = 'seigipwmanager';
			$this->version = 'x.x.x';
			$this->author = 'SEIGI Grzegorz Zawadzki';
			$this->need_instance = 1;
			parent::__construct();
			$this->displayName = $this->l( 'seigipwmanager' );
			$this->description = $this->l( '' );
		}
		
		public function install() {
			// Blokada jest tylko po to, ażeby klient napewno odinstalował moduł. 
			// Znając życie, to nikt nie przeczyta tego do końca, a procedury instalacyjne muszę zostać wykonane
			Configuration::updateGlobalValue('SEI_BLOCK_seigipwmanager', 1);
			return parent::install();
		}
		public function uninstall() {
			Configuration::deleteByName('SEI_BLOCK_seigipwmanager');
			return parent::uninstall();
		}
		
		public function getContent() {
			
			$minimum_io_v = '10.2.2';
			
			$php_min = implode('.',array_map('intval',str_split(ioncube_loader_iversion(), 2)));
			$is_ok = true;
			$return = '';

			$return .= '<div style="padding: 15px;">';
					
			$return .= '<h1>Nie można uruchomić modułu.</h1>';
			$return .= '<p>Prosimy o zapoznanie się z wymagniami modułu<a href="http://docs.seigi.eu/Podstawowe_informacje/Ioncube/index.html">Więcej informacji na temat wymagań zanajduje się pod tym adresem</a></p>';
			$return .= '<h3>Moduł IonCube - Loader. Wymagana wersja '.$minimum_io_v.'</h3> <p>';
			if(!function_exists('ioncube_loader_iversion') || !function_exists('ioncube_loader_version')){
				$return .= '<b style="color: red;">BŁĄD: IonCube jest albo baardzo bardzo stary, albo w ogóle nie jest zainstalowany. Wymagana wersja to '.$minimum_io_v;
				$is_ok = false;
			}else if(100202 > ioncube_loader_iversion()){
				$return .= '<b style="color: red;">BŁĄD: Wersja '.ioncube_loader_version(). ' jest zbyt stara. Prosimy zainstalować IonCube do wersji '.$minimum_io_v.' minimum.';
				$is_ok = false;
			} else {
				$return .= '<b style="color: green;">OK: Wersja '.ioncube_loader_version(). ' jest odpowiednia';

			}
			$return .= '</b></p>';
			
			$return .= '<h3>PHP - Wymagana wersja 5.6</h3> <p>';	
			if(PHP_VERSION_ID < 50600){
				$return .= '<b style="color: red;">BŁĄD: Wersja '.phpversion(). ' jest zbyt stara. ';
				$is_ok = false;
			} else {
				$return .= '<b style="color: green;">OK: Wersja '.phpversion(). ' jest odpowiednia';

			}
			$return .= '</b></p>';
			
			$test_xyz = function($domain) use (&$return){
				$output .= "<p>Testing {$domain} / IP: ".gethostbyname($domain).': ';
				$output .= (Tools::file_get_contents('http://'.$domain.'/xyz.txt') == 'xyz' ? '<span style="color: green">Connection OK</span>' : '<span style="color: red">Not xyz - connection error</span>');
				$output .= "</p>";
			};
			
			$return .= $test_xyz('pl.seigi.eu');
			$return .= $test_xyz('s1.license.seigi.eu');
			$return .= $test_xyz('s2.license.seigi.eu');

			if($is_ok && Configuration::get('SEI_BLOCK_seigirequestreview') == 1){
				$return .= '<span style="color: orange">Wszystko wydaje się być OK. Prosimy odinstalować i zainstalować moduł ponownie!!!!!!!!</span>';
			} else {
				$return .= '<b style="color: red">Prosimy poprawić błędy/problemy i zainstalować ponownie moduł. Prosimy odinstalować i zainstalować moduł ponownie</b>';
			}
			$return .= '</div>';

			return $return;
		}
		
	}

} else {
	require_once(dirname(__FILE__). '/seigipwmanager.inc.php');
}