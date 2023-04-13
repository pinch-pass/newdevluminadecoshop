<?php
/* 
 * Stworzono przez SEIGI http://pl.seigi.eu/
 * Wszelkie prawa zastrzeżone.
 * Zabrania się modyfikacji używania i udostępniania kodu bez zgody lub odpowiedniej licencji.
 * Utworzono  : 2022-03-09 09:15:34
 * Author     : SEIGI - Grzegorz Zawadzki <kontakt@seigi.eu>
 */

if(Configuration::get('SEI_BLOCK_seigixsell') == 1 ||  !extension_loaded('ionCube Loader') || ioncube_loader_iversion() < 100202 || PHP_VERSION_ID < 50600 || PHP_VERSION_ID > 70499){

	class seigixsell extends Module {
		public function __construct()
		{
			$this->name = 'seigixsell';
			$this->version = 'x.x.x';
			$this->author = 'SEIGI Grzegorz Zawadzki';
			$this->need_instance = 1;
			parent::__construct();
			$this->displayName = $this->l( 'seigixsell' );
			$this->description = $this->l( '' );
		}
		
		public function install() {
			// Blokada jest tylko po to, ażeby klient napewno odinstalował moduł. 
			// Znając życie, to nikt nie przeczyta tego do końca, a procedury instalacyjne muszę zostać wykonane
			Configuration::updateGlobalValue('SEI_BLOCK_seigixsell', 1);
			return parent::install();
		}
		public function uninstall() {
			Configuration::deleteByName('SEI_BLOCK_seigixsell');
			return parent::uninstall();
		}
		
		public function getContent() {
			
			$minimum_io_v = '10.2.2';

			$is_ok = true;
			$return = '';
			$return .= '<div style="padding: 15px;">';
			$return .= '<h1>Nie można uruchomić modułu.</h1>';
			$return .= '<p>Prosimy o zapoznanie się z wymagniami modułu <a href="http://docs.seigi.eu/Podstawowe_informacje/Ioncube/index.html">Więcej informacji na temat wymagań zanajduje się pod tym adresem</a></p>';
			$ic_version = 0;
            if(!function_exists('ioncube_loader_iversion') || !function_exists('ioncube_loader_version')){
                $ic_message = '<b style="color: red;">BŁĄD: IonCube jest albo bardzo bardzo stary, albo w ogóle nie jest zainstalowany';
                $is_ok = false;
            }else if(intval('100202') > ioncube_loader_iversion()){
                $ic_version = ioncube_loader_iversion();
                $ic_message = '<b style="color: red;">BŁĄD: Zainstalowana wersja IonCube Loader jest zbyt stara.';
                $is_ok = false;
            } else {
                $ic_version = ioncube_loader_iversion();
                $ic_message = '<b style="color: green;">OK: Wersja IonCube Loader jest odpowiednia';
            }


           if(PHP_VERSION_ID < intval('50600')){
                $php_message = '<b style="color: red;">BŁĄD: Wersja jest zbyt stara. ';
                $is_ok = false;
            } else {
                $php_message = '<b style="color: green;">OK: Wersja jest odpowiednia';

            }

			$return .= '<table class="table"">
                <thead>
                
                <tr>
                <th>&nbsp;</th>
                <th>Wymagana</th>
                <th>Zainstalowana</th>
                <th>Status</th>
               </tr>
                </thead>
               <tr>
                <td>Ioncube</td>
                <td>'.$minimum_io_v.'</td>
                <td>'.implode('.', array_map('intval',str_split(str_pad($ic_version, 6, 0, STR_PAD_LEFT),2))).'</td>
                <td>'. $ic_message .'</td>
               </tr>
               <tr>
                <td>PHP</td>
                <td>5.6.0</td>
                <td>'.phpversion(). '</td>
                <td>'.$php_message.'</td>
               </tr>



</table>';

			$test_xyz = function($domain) use (&$return){
                $return .= "<p>Testing {$domain} / IP: ".gethostbyname($domain).': ';
                $return .= (Tools::file_get_contents('http://'.$domain.'/xyz.txt') == 'xyz' ? '<span style="color: green">Connection OK</span>' : '<span style="color: red">Not xyz - connection error</span>');
                $return .= "</p>";
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
	require_once(dirname(__FILE__). '/seigixsell.inc.php');
}