<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Narzędzie diagnostyczne do modułu PriceWars 2 - seigi.eu</title>
</head>

<body>
<h1>Narzędzie diagnostyczne do modułu PriceWars 2 - więcej na <a href="http://seigi.eu">seigi.eu</a></h1>
<?php
/* 
 *	Bardzo prosty skrypt, który mam nadzieję pozwoli szybciej zidentyfikować problemy wynikające z konfiguracji serwera.
 *	
 *	@author SEIGI Grzegorz Zawadzki <kontakt@seigi.eu>
 *	@version 1.0
 *	@copyright SEIGI Grzegorz Zawadzki
 *
 */
require_once './../../config/config.inc.php';
$warning = $error = false;

$test_domains = array(
	'pl.seigi.eu',
	's1.license.seigi.eu',
	's2.license.seigi.eu',
);
$domains = array(
	'http://google.pl',
	'https://www.wp.pl/',
	'https://www.onet.pl/',
);

$could_connect_seigi = $could_connect_external = false;

echo "<h3>Serwery licencji</h3>";

$tr = array();

foreach($test_domains as $d) {
	test_xyz($d, $tr);
}

foreach($tr as $d){
	echo $d['text'];
	if($d['result']) {
		$could_connect_seigi = $d['result'];
	}
}

if(!$could_connect_seigi) {
	echo "<h2>Testy łączności</h2>";
	echo "<h3>Serwery ogólne</h3>";
	$r = array();
	foreach($domains as $d){
		$r[$d] = testConnection($d);
		if($r[$d]){
			$could_connect_external = true;
		}
	}
//	echo "<br>";
//	$could_connect_external &= testConnection('https://www.wp.pl/');
//	echo "<br>";
//	$could_connect_external &= testConnection('https://arena.pl/');
}else{
	$could_connect_external = true;
}

if(!function_exists('curl_init')) {
    d_info('Rozszerzenie CURL nie jest zainstalowane');
}

function test_xyz($domain, &$r) {
	$r[$domain]['text'] = "<h2>Testing {$domain}</h2>";
	$r[$domain]['text'] .= "<p>IP: ".gethostbyname($domain).' - ';
	$xyz = tools::file_get_contents('http://'.$domain.'/xyz.txt');
	$r[$domain]['text'] .= ($xyz == 'xyz' ? '<span style="color: green">Connection OK</span>' : '<span style="color: red">Not xyz - connection error</span>');
	$r[$domain]['text'] .= "</p>";
	$r[$domain]['result'] = ($xyz == 'xyz' ? true : false);
	return $r;
}


function testConnection($url, &$result = false){
    $curl_timeout = 3;
    if(@file_get_contents($url) === false){
        d_info('Próba otworzenia zasobu '.$url.' przez file_get_contents nie powiodła się');
        $error = error_get_last() ;
        d_warning($error['message']);
        if(function_exists('error_clear_last'))
            error_clear_last();
        if(function_exists('curl_init')) {
            d_info('(CURL) Próba połączenia za pomocą rozszerzenia CURL');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
            $curl_info = curl_getinfo($curl);
            $curl_result = curl_exec($curl);
            curl_close($curl);
            d_info('(CURL) Serwer zwrócił KOD HTTP: '. $curl_info['http_code']);
            if($curl_result === false){
                d_error( '(CURL) Połączenie nie udane. Nie można nawiązać połączenia z serwerem');    
                return false;
            } else {
                d_error( 'Twoja strona musi zezwalać na połączenie z zasobami zdalnymi. Musisz mieć zainstalowane i aktywne rozszerzenie curl i/lub aktywną opcję allow_url_fopen');    
                return true;
            }
        } else {
            d_error( 'Twoja strona musi zezwalać na połączenie z zasobami zdalnymi. Musisz mieć zainstalowane i aktywne rozszerzenie curl i/lub aktywną opcję allow_url_fopen');
            return false;
            
        }
    }
    d_ok('Próba otworzenia zasobu przez <b>file_get_contents</b> udana: '. $url);
    return true;
}


echo "<h3>Podsumowanie</h3>";

if($could_connect_seigi) {
	d_ok('Połączenie z serwerem licencji nawiązane');
} else {
	d_error( 'Nie można połączyć z serwerem licencji.');
}
if(!$could_connect_seigi && !$could_connect_external){
	d_error( 'Konfiguracja serwera nie pozwala na żadne połączenie zdalne z poziomu skryptu. Proszę o kontakt z administratorem serwera', true);
}elseif(!$could_connect_seigi){
	d_warning('Połączenie z zewnętrznymi serwerami działa poprawnie, natomiast nie udało połączyć się z serwerem licencji. Skontaktuj się z nami w celu wyjaśnienia sytuacji');
}

if(get_magic_quotes_gpc())
	d_error( 'Opcja magic_quotes_gpc jest aktywna. Należy ją wyłączyć aby moduł (zresztą i sklep też) działał poprawnie.');
echo "<h2>Testy systemowe</h2>";
		

foreach(get_loaded_extensions() as $ext){
	if(strpos($ext, 'XCache') !== false){
		d_warning('Wykryto zainstalowane rozszerzenie "'.$ext.'" które <b>może - ALE NIE MUSI</b> sprawiać problemy. XCache jest przestażały (ost. akt ~2014 roku), a w sieci są lepsze moduły cacheujące<br> Czasami problemy z tym modułem mogą zostać rozwiązane po aktualizacji IonCube do najnowszej wersji');
	}
}

/* if(ini_get('allow_url_fopen') != 1)
echo '<h1 style="color: red">UWAGA! allow_url_fopen musi być ustawione na 1. Prosimy o kontakt z administratorem serwera</h1>';
 */
if(!extension_loaded('mcrypt'))
	d_error ('UWAGA! Brak biblioteki mcrypt</h1>');

if(extension_loaded('suhosin') || (defined('SUHOSIN_PATCH') && constant("SUHOSIN_PATCH")))
	d_warning('Na serwerze jest obecne rozszerzenie Suhosin. Potrafi on sprawiać problemy przy generowaniu XML. <b>Tym ostrzeżeniem nie należy się przejmować, jeśli XML generuje się prawidłowo</b>');

if(!extension_loaded('ionCube Loader'))
	d_error ('UWAGA! Brak zainstalowanego rozszerzenia "<b>ionCube Loader</b>" na serwerze, który jest niezbędny aby korzystać z tego modułu. Jeśli chcesz korzystać z modułu i widzisz tę wiadomośc, to skontaktuj się z administratorem Twojego serwera i poproś o instalację/aktywację rozszerzenia.');
else {
	if(!function_exists('ioncube_loader_iversion')) {
		d_warning('Wykryto IONCUBE loader - nie wiadomo jednak jaka wersja. Zaktualizuj lub zainstaluj moduł w wersji najnowszej wersji lub minimum 10.2.2');
	} elseif (ioncube_loader_iversion() < 100202) {
		d_warning('Moduł IONCUBE Loader jest bardzo nieaktualny. Nasz moduł nie będzie pracował na Twoim serwerze. Zaktualizuj moduł IoncubeLoader do najnowszej wersji lub minimum 10.2.2, twoja wersja to: ' . ioncube_loader_version().' ('.ioncube_loader_iversion().')');
	} else {
		d_ok('Moduł Ioncube wydaje się być aktualny. Twoja wersja to: ' . ioncube_loader_version().' ('.ioncube_loader_iversion().')');
	}
}

d_info('PHP version: '. PHP_VERSION.' ('.PHP_VERSION_ID.')');


function d_info ($s) {
	echo '<div style="color: #225ebf; background-color: #fff; border: 1px solid #225ebf; padding: 4px; margin: 5px">INFO: '.$s.'</div>';
}
function d_error ($s, $rev = false) {
    if($rev)
        echo '<div style="color: #fff; background-color: red; border: 1px solid #000; padding: 4px; margin: 5px">BŁĄD: '.$s.'</div>';
    else
        echo '<div style="color: red; background-color: #fff; border: 1px solid red; padding: 4px; margin: 5px">BŁĄD: '.$s.'</div>';
}
function d_warning ($s) {
	echo '<div style="color: darkorange; background-color: #fff; border: 1px solid darkorange; padding: 4px; margin: 5px">OSTRZEŻENIE: '.$s.'</div>';
}
function d_ok ($s) {
	echo '<div style="color: green; background-color: #fff; border: 1px solid green; padding: 4px; margin: 5px">SUKCES: '.$s.'</div>';
}

?>
</body>
</html>