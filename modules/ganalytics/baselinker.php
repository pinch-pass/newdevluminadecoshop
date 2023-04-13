<?php

/**
 * Plik wymiany danych z systemem Baselinker
 * @author Sewer Skrzypiński <info@baselinker.com>
 * @version 4
 * @package baselinker
 */

/** -----------------------------------------------------------------------------------------------------------
 * Ustawienia wpisywane przez sprzedawcę
 *	- Należy uzupełnić dane wprowadzając je między apostrofy
 */
$options['baselinker_pass'] = 'hpys04b3dtfle1vzfb9becb5ax5gfkal';		//hasło do komunikacji (dostępne w panelu Baselinkera w zakładce 'sklep internetowy')

$options['db_host'] = 'sql.sami.nazwa.pl';		//adres hosta bazy danych (najczęściej localhost)
$options['db_user'] = 'sami_luminadeco';		//użytkownik bazy danych
$options['db_pass'] = 'CxsBWK4BEU';		//hasło bazy danych
$options['db_name'] = 'sami_luminadeco';		//nazwa bazy danych
$options['db_prefix'] = '';			//prefiks tabel bazy danych - domyślnie pozostaw pusty aby wykryć automatycznie

$options['images_folder'] = 'https://luminadeco.pl/img/';  //adres folderu zawierającego zdjęcia produktów i producentów (rozpoczęty 'http://' , zakończony '/')
$options['images_mode'] = 1;			//sposób zapisywania obrazków w sklepie ("1" dla prestashop wersji >= 1.4.8, "0" dla wersji wcześniejszych)
$options['img_suffix'] = '';			//selektor rodzaju zdjęcia (w nazwie pliku doklejony do ID, np -large_default)

$options['language'] = 'pl';			//język opisów produktów (w przypadku sklepów wielojęzycznych)
$options['shop_id'] = '';			//identyfikator sklepu, używane tylko  w trybie multistore, domyślnie puste
$options['currency'] = 'PLN';			//waluta
$options['special_price'] = 1;			//czy używać ceny promocyjnej jeśli produkt jest w promocji? (0 - nie, 1 - tak)
$options['add_tax'] = 1;			// czy doliczać podatek do cen pobranych ze sklepu (0 - nie, 1 - tak)

$options['charset'] = 'UTF-8';			//zestaw znaków bazy danych (standardowo UTF-8)
$options['def_tax_rate'] = 23;			// domyślna stawka VAT
$options['customer_group_id'] = 2;		// grupa klientów używana do naliczania zniżek, itp.
$options['other_customer_groups'] = '';		// inne grupy klientów powiązane z nowym kontem
$options['show_all_images'] = 1;		// pełna lista zdjęć produktu w każdym wariancie = 1, tylko zdjęcia wariantu = 0
$options['compat_mode'] = 0;			// tryb kompatybilności ze starymi wersjami Presty.  Normalnie 0.
$options['customer_is_guest'] = 1;		// czy konto do zamówienia ma być tworzone jako 'gosć'  (0 - nie, 1 - tak)
$options['guest_email'] = '';			// jeśli wybrano opcję powyżej, adres email przypisany do konta gościa
						// (w miejsce rzeczywistego adresu podanego w zamówieniu)
$options['split_discounts'] = 0;		// kwota rabatu rozbita na wszystkie produkty zamówienia (1) lub jako osobna pozycja (0)
$options['stock_mvt'] = 0;			// rejestrowanie ruchu magazynowego przy zmianie statusu zamówienia w sklepie (0 = nie, inna wartość = ID pracownika)

date_default_timezone_set('Europe/Warsaw'); 
error_reporting(E_ERROR | E_WARNING);


/** -----------------------------------------------------------------------------------------------------------
 * Funkcje zarządzające komunikacją (przedrostek Conn_) oraz funkcje ułatwiające zapytania SQL (przedrostek DB_)
 *	- Jednakowe niezależnie od platformy
 *	- Nie należy edytować poniższego kodu
 */



/**
 * Definicja funkcji json_encode oraz json_decode dla PHP4 (istnieją domyślnie w PHP5.2), iconv() dla tablic, oraz array_walk_recursive()
 * Nie należy edytować. Credits goes to Steve http://usphp.com/manual/en/function.json-encode.php#82904
 */
if (!function_exists('json_encode'))
{
    function json_encode($a=false,$is_key=false)
    {if (is_null($a)) return 'null';if ($a === false) return 'false';if ($a === true) return 'true';
    if (is_scalar($a)){if(is_int($a)&&$is_key){return '"'.$a.'"';} if (is_float($a)){return floatval(str_replace(",", ".", strval($a)));}if (is_string($a)){
    static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
    return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';} else return $a;} $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a)){if (key($a) !== $i){$isList = false;break;}}
    $result = array(); if ($isList){foreach ($a as $v) $result[] = json_encode($v); return '[' . join(',', $result) . ']';}
    else {foreach ($a as $k => $v) $result[] = json_encode($k,true).':'.json_encode($v); return '{' . join(',', $result) . '}';}}
}
if (!function_exists('json_decode'))
{
    function json_decode($json, $assoc = true)
    {$comment = false; $out = '$x='; for ($i=0; $i<strlen($json); $i++) { if (!$comment) {if (($json[$i] == '{') || ($json[$i] == '['))
    $out .= ' array('; else if (($json[$i] == '}') || ($json[$i] == ']'))   $out .= ')'; else if ($json[$i] == ':')    $out .= '=>';
    else $out .= $json[$i]; } else $out .= $json[$i]; if ($json[$i] == '"' && $json[($i-1)]!="\\")    $comment = !$comment;} eval($out . ';'); return $x;}
}
if (!function_exists('array_walk_recursive'))
{
    function array_walk_recursive(&$input, $funcname, $userdata = "")
    {if (!is_callable($funcname)){return false;}if (!is_array($input)){return false;}foreach ($input AS $key => $value){
    if (is_array($input[$key])){array_walk_recursive($input[$key], $funcname, $userdata);}else{$saved_value = $value;
    if (!empty($userdata)){$funcname($value, $key, $userdata);}else{$funcname($value, $key);}if ($value != $saved_value)
    {$input[$key] = $value;}}}return true;}
}

function array_iconv(&$val, $key, $userdata)
{$val = iconv($userdata[0], $userdata[1], $val);}
function recursive_iconv($in_charset, $out_charset, $arr)
{if (!is_array($arr)){return iconv($in_charset, $out_charset, $arr);}$ret = $arr;
array_walk_recursive($ret, "array_iconv", array($in_charset, $out_charset));return $ret;}


/**
 * Funkcje wykonujące zapytania SQL
 */
function DB_Query($sql)
{
	global	$dbh;

	if (func_num_args() > 1)
	{
		$i = 0;

		foreach(func_get_args() as $val)
		{
			if ($i==0)
			{
				$i++; 
				continue;
			}

			if (is_array($val))
			{
				foreach ($val as $k => $v)
				{
					$sql = str_replace('{'.$k.'}', substr($dbh->quote($v), 1, -1), $sql);
				}
			}
			else
			{
				$sql = str_replace('{'.($i-1).'}', substr($dbh->quote($val), 1, -1), $sql);
			}

			$i++;
		}
	}

	if (!($sth = $dbh->prepare($sql)))
	{
		$err = $dbh->errorInfo();
		Conn_error('db_query', 'SQL error: ' . $err[2]);
	}

	if (!($sth->execute()))
	{
		$err = $sth->errorInfo();
		Conn_error('db_query', 'SQL error: ' . $err[2]);
	}

	return $sth;
}

function DB_Result($sth, $num = 0) { if (DB_NumRows($sth) > $num){return $sth->fetchColumn($num);} return false; }

function DB_Identity() { global $dbh; return $dbh->lastInsertId(); }

function DB_NumRows($sth) { return $sth->rowCount(); }

function DB_Fetch($sth) { return $sth->fetch(PDO::FETCH_ASSOC); }


/**
 * Funkcja obsługująca żądania i wysyłająca odpowiedź.
 * Zalecane jest pozostawienie funkcji w tej postaci niezależnie od platformy
 * @global array $options : tablica z ustawieniami ogólnymi
 */
function Conn_Init()
{
	global $options;

	//sprawdzanie poprawności hasła wymiany danych
	if(!isset($_POST['bl_pass']))
	{Conn_Error("no_password","Odwołanie do pliku bez podania hasła. Jest to poprawny komunikat jeśli plik integracyjny został otworzony w przeglądarce internetowej.");}
	elseif($options['baselinker_pass'] == "" || $options['baselinker_pass'] !== $_POST['bl_pass'])
	{Conn_Error("incorrect_password");}

	//zmiana kodowania danych wejściowych
	if($options['charset'] != "UTF-8")
	{
		foreach($_POST as $key => $val)
		{$_POST[$key] = iconv('UTF-8', $options['charset'].'//IGNORE', $val);}
	}

	//łączenie z bazą danych sklepu
	Shop_ConnectDatabase($_POST);

	//rozbijanie tablic z danymi
	if(isset($_POST['orders_ids'])){$_POST['orders_ids'] = explode(",", $_POST['orders_ids']);}
	if(isset($_POST['products_id'])){$_POST['products_id'] = explode(",", $_POST['products_id']);}
	if(isset($_POST['fields'])){$_POST['fields'] = explode(",", $_POST['fields']);}
	if(isset($_POST['products'])){$_POST['products'] = json_decode(stripslashes($_POST['products']), true);}

	//sprawdzanie czy podana metoda jest zaimplementowana
	if(function_exists("Shop_".$_POST['action']))
	{
		$method = "Shop_".$_POST['action'];
		Conn_SendResponse($method($_POST));
	}
	else
	{Conn_Error("unsupported_action", "No action: ".$_POST['action']);}
}


/**
 * Funkcja generująca odpowiedź do systemu w formacie JSON
 * @global array $options tablica z ustawieniami ogólnymi
 */
function Conn_SendResponse($response)
{
	global $options;

	//zmiana kodowania danych wyjściowych
	if($options['charset'] != "UTF-8" && count($response) > 0)
	{
		foreach($response as $key => $val)
		{$response[$key] = recursive_iconv($options['charset'], 'UTF-8//IGNORE', $val);}
	}

	print json_encode($response);
	exit();
}


/**
 * Funkcja wypisująca kominukat błędu w formacie JSON i kończąca skrypt
 * Zalecane jest pozostawienie funkcji w tej postaci niezależnie od platformy
 * @param string $error_code kod błędu (standardowe wartości: db_connect, db_query, no_action)
 * @param string $error_text opis błędu
 */
function Conn_Error($error_code, $error_text = '')
{
	print json_encode(array('error' => true, 'error_code' => $error_code, 'error_text' => $error_text));
	exit();
}


 /**
 * Ewentualne wczytanie dodatkowych funkcji z pliku baselinker_pm.php (BaseLinker Product Managment)
 * Zawarte w dodatkowym pliku funkcje rozszerzają możliwości integracji ze sklepem o funkcje pozwalające dodawać i zmieniać kategorie, produkty oraz warianty.
 * Obsługa tych funkcji jest wymagana przez niektóre moduły BaseLinkera (np. moduły integrujące system z programami typu ERP)
 * Plik baselinker_pm.php jest dostępny dla wybranych platform sklepów. Skontaktuj się z administratorem w cely uzyskania pliku.
 */
if(file_exists("baselinker_pm.php"))
{include("baselinker_pm.php");}


//inicjacja komunikacji
Conn_Init();




/** -----------------------------------------------------------------------------------------------------------
 * Funkcje obsługiwania żądań (przedrostek Shop_)
 *	- Zależne od platformy sklepu
 *	- Do edycji dla deweloperów
 */



 /**
 * Funkcja zwracająca wersję pliku wymiany danych
 * Przy tworzeniu pliku należy skonsultować numer wersji i nazwę platformy
 * z administracją systemu Baselinker
 * @param array $request tablica z żadaniem od systemu, w przypadku tej funkcji nie używana
 * @return array $response tablica z danymi platformy z polami:
 * 		platform => nazwa platformy
 * 		version => numer wersji pliku
 */
function Shop_FileVersion($request)
{
	$response['platform'] = "PrestaShop";
	$response['version'] = "4.1.97"; //wersja pliku integracyjnego, nie wersja sklepu!
	$response['standard'] = 4; //standard struktury pliku integracyjnego - obecny standard to 4.

	return $response;
}


/**
 * Funkcja zwracająca listę zaimplementowanych metod pliku
 * Zalecane jest pozostawienie funkcji w tej postaci niezależnie od platformy
 */
function Shop_SupportedMethods()
{
	$result = array();
	$methods = get_defined_functions();

	foreach($methods['user'] as $m)
	{
		if (stripos($m, 'shop_') === 0)
		{$result[] = substr($m,5);}
	}

	return $result;
}



 /**
 * Funkcja nawiązująca komunikację z bazą danych sklepu
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu, w przypadku tej funkcji nie używana
 * @return boolean wartość logiczna określajaca sukces połączenia z bazą danych
 */
function Shop_ConnectDatabase($request)
{
	global $options; //globalna tablica z ustawieniami
	global $dbh; // handler bazy danych

	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	// wydzielenie portu z nazwy hosta
	if (preg_match('/^\s*([\w\-\.]+):(\d+)\s*$/', $options['db_host'], $m))
	{
		$options['db_host'] = $m[1];
		$options['db_port'] = $m[2];
	}

	// wygenerowanie DSN
	$dsn = "mysql:dbname=${options['db_name']};host=${options['db_host']}";

	if ($options['db_port'])
	{
		$dsn .= ";port=${options['db_port']}";
	}

	// nawiązanie połączenia z bazą danych sklepu
	try {
		$dbh = new PDO($dsn, $options['db_user'], $options['db_pass']);
	} catch (Exception $ex) {
		Conn_Error('db_connection', $ex->getMessage());
	}

	if($options['charset'] == "UTF-8")
	{DB_Query("SET NAMES utf8");}

	//relaxed mode
	DB_Query("SET SESSION sql_mode = ''");

	//automatyczne wyszukiwanie prefiksu bazy danych
	if($dbp == "")
	{
		$unique_table = "product_attribute_combination"; //wyszukiwanie tabeli z unikalną nazwą
		$search_table = DB_Query("SHOW TABLES LIKE '%${unique_table}'");

		if(DB_NumRows($search_table) == 1)
		{$options['db_prefix'] = str_replace($unique_table, '', DB_Result($search_table)); $dbp = $options['db_prefix'];}
		else
		{Conn_Error("database_prefix");} //nie wykryto jednoznacznie prefiksu
	}

	//wybieranie języka polskiego z tabeli języków - zmianna $lang_id wykorzystywana później w zapytaniach
	$result = DB_Query("SELECT id_lang FROM `${dbp}lang` WHERE `iso_code` = '{0}'", $options['language']);
	if(DB_NumRows($result) > 0)
	{$options['lang_id'] = DB_Result($result);}
	else
	{$options['lang_id'] = 1;}

	//określanie czy istnieje osobna tabela ze stanami magazynowymi (zaawansowane zarządzanie magazynem w prestashop)
	{$options['table_stock'] = (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}stock_available'")) == 1);}

	//sprawdzanie, czy używać trybu multistore
	if ($options['shop_id'] == '' and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}shop'")))
	{
		$result = DB_Query("SELECT id_shop FROM `${dbp}shop` WHERE active = 1 and deleted = 0");

		if (DB_NumRows($result) >= 1)
		{
			$options['shop_id'] = DB_Result($result);
		}
	}

	// grupa sklepu
	if ($options['shop_id'] and $options['table_stock'] and DB_NumRows(DB_Query("SHOW FIELDS FROM `${dbp}stock_available` LIKE 'id_shop_group'"))) 
	{
		$result = DB_Query("SELECT * FROM `${dbp}shop` WHERE id_shop = {0}", $options['shop_id']);

		if ($shop = DB_Fetch($result))
		{
			if ($shop['id_shop_group'])
			{
				$options['shop_group_id'] = $shop['id_shop_group'];
			}
		}
	}

	//id magazynu
	if ($options['warehouse_id'] == '' and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}warehouse'")))
	{
		$sql = "SELECT w.id_warehouse FROM `${dbp}warehouse` w
			LEFT JOIN `${dbp}warehouse_shop` ws ON ws.id_warehouse = w.id_warehouse
			WHERE w.deleted = 0 ORDER BY (ws.id_shop = '{0}') DESC";
		$res = DB_Query($sql, $options['shop_id']);

		if (DB_NumRows($res))
		{
			$options['warehouse_id'] = DB_Result($res);
		}
	}

	$options['warehouse_id'] = (int)$options['warehouse_id'];


        //czy ceny zawierają podatek?
	if ($options['add_tax'] == '')
	{
	        $sql = "SELECT value FROM `${dbp}configuration` WHERE name = 'PS_TAX'" . ($options['shop_id'] ? " AND (isnull(id_shop) OR id_shop = {0} OR id_shop = 0)" : '') . (!$options['compat_mode'] ? " ORDER BY id_shop DESC LIMIT 1" : '');
        	$res = DB_Query($sql, (int)$options['shop_id']);
	        $options['add_tax'] = DB_Result($res) ? 1 : 0;
	}

	//przelicznik waluty
	$options['currency_conv'] = 1;

	if ($options['currency'] and $options['shop_id'])
	{
		$sql = "SELECT if(isnull(cs.conversion_rate), c.conversion_rate, cs.conversion_rate) conversion_rate, cs.id_currency
			FROM `${dbp}currency_shop` cs
			JOIN `${dbp}currency` c ON cs.id_currency = c.id_currency
			WHERE c.iso_code = '{0}'";

		if ($cr = DB_Fetch(DB_Query($sql, $options['currency'])))
		{
			$options['currency_conv'] = $cr['conversion_rate'] ? $cr['conversion_rate'] : 1;
			$options['currency_id'] = $cr['id_currency'];
		}
	}
}


 /**
 * Funkcja zwraca listę kategorii sklepowych
 * Zwracana tabela powinna być posortowana alfabetycznie
 * W nazwie kategorii podrzędnej powinna być zawrta nazwa nadkategorii - np "Komputery/Karty graficzne" zamiast "Karty graficzne"
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu, w przypadku tej funkcji nie używana
 * @return array $response tablica z listą kategori sklepowch w formacie:
 * 		id kategorii => nazwa kategorii
 */
function Shop_ProductsCategories($request)
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	$is_active = array();

	//pobieranie kategorii z bazy i zapisywanie do tabeli
	$sql = "SELECT c.id_category, c.id_parent, cl.name, c.active
		FROM `${dbp}category` c
		INNER JOIN `${dbp}category_lang` cl ON c.id_category = cl.id_category AND `id_lang` = '{0}'
		".(($options['shop_id']!="")?" AND `id_shop` = '{1}' WHERE id_shop_default = '{1}'":"");
	$res = DB_Query($sql, $options['lang_id'], $options['shop_id']);

	while($category = DB_Fetch($res))
	{
		$categories[$category['id_category']] = $category['name'];
		$parents[$category['id_category']] = $category['id_parent'];
		$is_active[$category['id_category']] = $category['active'];
	}

	//budowanie drzewa kategorii na podstawie tablicy
	$category_tree = array();
	foreach($categories as $id => $name)
	{
		if (!$is_active[$id]) { continue; } // pobieramy tylko kategorie aktywne

		$cat_name = "";
		$this_id = $id;
		$ancestors = array();

		while ($parents[$this_id] != 0 and $this_id != $parents[$this_id] and !isset($ancestors[$this_id]))
		{
			$ancestors[$this_id] = $parents[$this_id];
			$cat_name = $categories[$parents[$this_id]]."/".$cat_name;
			$this_id = $parents[$this_id];
		}

		$category_tree[$id] = $cat_name.$name;
	}

	//sortowanie alfabetycznie wg nazw
	asort($category_tree);

	// usuwanie pnia
	do {
		$trim = false;

		if (count($category_tree) > 1)
		{
			$ids = array_keys($category_tree);
			$first = array_shift($ids);
			$last = $ids[count($ids)-1];

			if (strpos($category_tree[$last], $category_tree[$first]) === 0)
			{
				$trim = true;

				foreach ($ids as $id)
				{
					$category_tree[$id] = substr($category_tree[$id], strlen($category_tree[$first])+1);
				}

				unset($category_tree[$first]);
			}
		}
	} while ($trim);

	return $category_tree;
}





 /**
 * Funkcja zwraca listę produktów z bazy sklepu
 * Zwracane liczby (np ceny) powinny mieć format typu: 123456798.12 (kropka oddziela część całkowitą, 2 miejsca po przecinku)
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu zawierająca pola:
 *		category_id => 			id kategori (wartość 'all' jeśli wszystkie przedmioty)
 *		filter_limit => 		limit zwróconych kategorii w formacie SQLowym ("ilość pomijanych, ilość pobieranych")
 *		filter_sort => 			wartość po której ma być sortowana lista produktów. Możliwe wartości:
 *								"id [ASC|DESC]", "name [ASC|DESC]", "quantity [ASC|DESC]", "price [ASC|DESC]"
 *		filter_id => 			ograniczenie wyników do konkretnego id produktu
 *		filter_ean => 			ograniczenie wyników do konkretnego ean
 *		filter_sku => 			ograniczenie wyników do konkretnego sku (numeru magazynowego)
 *		filter_name => 			filtr nazw przedmiotów (fragment szukanej nazwy lub puste pole)
 *		filter_price_from =>	dolne ograniczenie ceny (nie wyświetlane produkty z niższą ceną)
 *		filter_price_to =>		górne ograniczenie ceny
 *		filter_quantity_from =>	dolne ograniczenie ilości produktów
 *		filter_quantity_to =>	górne ograniczenie ilości produktów
 *		filter_available =>		wyświetlanie tylko produktów oznaczonych jako dostępne (wartość 1) lub niedostępne (0) lub wszystkich (pusta wartość)
 * @return array $response tablica z listą produktów w formacie:
 * 		id produktu =>
						'name' => nazwa produktu
						'quantity' => dostępna ilość
						'price' => cena w PLN
 */
function Shop_ProductsList($request)
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	//zmiana nazw kolumn na nazwy pól
	$request['filter_sort'] = str_replace(
		array("id", "name", "quantity", "price"),
		array("p.id_product", "pd.name",  "p.quantity", "p.price"),
		$request['filter_sort']);

	//pobieranie stawek podatków do dwóch tablic (obsługa różnej budowy bazy dla różnych wersji prestashop)
	$tax_rates_table = array(); $tax_rules_table = array();
	$tax_query = DB_Query("SELECT id_tax, rate FROM ${dbp}tax");
	while($tax = DB_Fetch($tax_query)){$tax_rates_table[$tax['id_tax']] = $tax['rate'];}
	$sql = "SELECT tr.id_tax_rules_group, t.rate
		FROM `${dbp}tax` t
		JOIN `${dbp}tax_rule` tr ON t.id_tax = tr.id_tax
		LEFT JOIN `${dbp}country` c ON c.id_country = tr.id_country
		ORDER BY c.iso_code = 'PL'";
	$tax_query = DB_Query($sql);
	while($tax = DB_Fetch($tax_query))	{$tax_rules_table[$tax['id_tax_rules_group']] = $tax['rate'];}

	// pobieranie produktow z bazy danych
	// podstawowy select:
	$sql = "SELECT DISTINCT p.id_product, ${options['currency_conv']}*p.price price, pd.name, p.quantity quantity, p.ean13,
		p.reference, p.id_tax_rules_group id_tax_rules_group
		FROM `${dbp}product` p
		INNER JOIN `${dbp}product_lang` pd ON pd.id_product = p.id_product AND pd.id_lang = {lang_id}"
		. (empty($options['shop_id']) ? '' : " AND (pd.id_shop = {shop_id} OR pd.id_shop = 0)") . "
		WHERE 1";

	// zawężenie do kategorii:
	if ($request['category_id'] != "all" && $request['category_id'] != "") // wybór kategorii
	{
		$sql = str_replace("`${dbp}product` p", "`${dbp}category_product` cp INNER JOIN `${dbp}product` p ON cp.id_product = p.id_product", $sql);
		$sql .= " AND cp.id_category = '{category_id}'";
	}

	// filtry:
	if($request['filter_id'] != "") {$sql .= " AND p.id_product = '{filter_id}'";} //filtrowanie id
	if($request['filter_ean'] != "") {$sql .= " AND p.ean13 LIKE '%{filter_ean}%'";} //filtrowanie ean
	if($request['filter_sku'] != "") {$sql .= " AND p.reference LIKE '%{filter_sku}%'";} //filtrowanie sku
	if($request['filter_name'] != "") {$sql .= " AND pd.name LIKE '%{filter_name}%'";} //filtrowanie nazwy
	if($request['filter_quantity_from'] != "") {$sql .= " AND p.quantity >= '{filter_quantity_from}'";} //filtrowanie ilości
	if($request['filter_quantity_to'] != "") {$sql .= " AND p.quantity <= '{filter_quantity_to}'";} //filtrowanie ilości
	if($request['filter_available'] != "") {$sql .= " AND p.active = '{filter_available}'";} //produkty dostępne/niedostępne
	
	// zwróć tylko dane dla podanych ID produktów
	if ($request['filter_ids_list'])
	{
		$pids = array();

		foreach (explode(',', $request['filter_ids_list']) as $pid)
		{
			if ($pid = (int)$pid)
			{
				$pids[] = $pid;
			}
		}

		if ($pids = implode(', ', $pids))
		{
			$sql .= " AND p.id_product IN ($pids)";
		}
	}

	if($request['filter_sort'] != "") {$sql .= " ORDER BY {filter_sort}";}

	// jeśli stany magazynowe pobierane z oddzielnej tabeli:
	if ($options['table_stock'])
	{
		// używamy quantity z tabeli stock_available
		$sql = preg_replace('/p\.quantity/s', $options['shop_group_id'] ? 'if(isnull(sa.quantity), if(isnull(sa0.quantity), p.quantity, sa0.quantity), sa.quantity)' : 'if(isnull(sa.quantity), p.quantity, sa.quantity)', $sql);
		$request['filter_sort'] = preg_replace('/p\.quantity/s', $options['shop_group_id'] ? 'if(isnull(sa.quantity), if(isnull(sa0.quantity), p.quantity, sa0.quantity), sa.quantity)' : 'if(isnull(sa.quantity), p.quantity, sa.quantity)', $request['filter_sort']);
		$sql = preg_replace('/^SELECT DISTINCT/', '$0 sa.id_shop,', $sql);
		// oraz doczepiamy ją do kwerendy
		$sql = preg_replace('/(\s+WHERE\s+)/s', " LEFT JOIN `${dbp}stock_available` sa ON sa.id_product = p.id_product AND sa.id_product_attribute = 0"
				    . (empty($options['shop_id'])?'':(" AND (sa.id_shop = {shop_id} OR sa.id_shop = 0) " . ($options['shop_group_id'] ? " AND sa.id_shop_group = ${options['shop_group_id']} LEFT JOIN `${dbp}stock_available` sa0 ON sa0.id_product = p.id_product AND sa0.id_product_attribute = 0  AND (sa0.id_shop = {shop_id} OR sa.id_shop = 0) AND sa0.id_shop_group = 0" : ''))) . '$1', $sql);
	}

	// ceny z oddzielnej tabeli
	if ($options['shop_id'] != '')
	{
		$sql = preg_replace('/(\s+WHERE\s+)/s', " JOIN `${dbp}product_shop` ps ON ps.id_product = p.id_product AND ps.id_shop = {shop_id} $1 ", $sql);
		$sql = str_replace('p.price', 'if(ps.price, ps.price, p.price)', $sql);
		$sql = str_replace('p.id_tax_rules_group', 'if(ps.id_tax_rules_group, ps.id_tax_rules_group, p.id_tax_rules_group)', $sql);

		// dopasowanie sortowania aby nie zgubić quantity dla shop_id == 0
		if ($options['table_stock'])
		{
			if (preg_match('/\sORDER\s+BY\s/i', $sql))
			{
				$sql = preg_replace('/(\sORDER\s+BY)(\s+.+)/i', '$1 sa.id_shop DESC, $2', $sql);
			}
			else
			{
				$sql .= ' ORDER BY p.id_product, sa.id_shop DESC';
			}
		}
	}

	// ograniczenie liczby wyników
	if($request['filter_limit'] != "") {$sql .= " LIMIT ${request['filter_limit']}";}

	$response = array();
	$result = DB_Query($sql, array(
		'lang_id' => $options['lang_id'],
		'shop_id' => $options['shop_id'],
		'category_id' => $request['category_id'],
		'filter_id' => $request['filter_id'],
		'filter_ean' => $request['filter_ean'],
		'filter_sku' => $request['filter_sku'],
		'filter_name' => $request['filter_name'],
		'filter_quantity_from' => $request['filter_quantity_from'],
		'filter_quantity_to' => $request['filter_quantity_to'],
		'filter_available' => $request['filter_available'],
		'filter_sort' => $request['filter_sort'],
	));

	while ($prod = DB_Fetch($result))
	{
		// jeśli kilka wpisów w stock_available, sumujemy stan magazynowy
		if (isset($response[$prod['id_product']]) and $prod['id_shop'])
		{
			$response[$prod['id_product']]['quantity'] += $prod['quantity'];
			continue;
		}

		$this_prod_id = $prod['id_product'];

		//pobieranie wysokosci podatku w zależności od budowy bazy
		if(isset($prod['id_tax']) && $prod['id_tax'] != 0)
		{$tax_rate = $tax_rates_table[$prod['id_tax']];}
		elseif(isset($prod['id_tax_rules_group']) && $prod['id_tax_rules_group'] != 0)
		{$tax_rate = $tax_rules_table[$prod['id_tax_rules_group']];}
		else
		{$tax_rate = $options['def_tax_rate'];}

		$prod['products_price'] = $prod['price'];

		//wyliczanie kwoty brutto i formatowanie ceny
		$prod['products_price'] = number_format($prod['products_price']*(1+($options['add_tax'] ? $tax_rate : 0)/100), 2, ".", "");

		//obsługa redukcji ceny
		if ($options['special_price'] == 1)
		{
			$sql = "SELECT * FROM `${dbp}specific_price`
				WHERE id_product = '{0}' AND (`to` >= now() OR `to` = 0) AND (`from` <= now() OR `from` = 0) AND from_quantity = 1
				". (empty($options['shop_id']) ? '' : "AND (id_shop = {1} OR id_shop = 0) ") . (!$options['compat_mode'] ? " AND id_cart = 0 AND id_customer = 0 " : '') . ($options['currency_id'] ? " AND (id_currency = ${options['currency_id']} OR id_currency = 0)" : '') . " AND id_group IN (0, {2}) LIMIT 1";
			$price_res = DB_Query($sql, $this_prod_id, (int)$options['shop_id'], (int)$options['customer_group_id']);

			while ($price = DB_Fetch($price_res))
			{
				$base_price = ($price['price'] > 0) ? ($options['add_tax'] ? ($price['price']*(1+($tax_rate/100))) : $price['price']) : $prod['products_price'];

				if ($price['reduction'] != '' && $price['reduction_type'] == 'percentage')
				{
					$prod['products_price'] = $base_price*(1-$price['reduction']);
				}
				elseif ($price['reduction'] != '' && $price['reduction_type'] == 'amount')
				{
					$prod['products_price'] = $base_price - $price['reduction'];
				}
			}

			// formatowanie ceny
			$prod['products_price'] = number_format($prod['products_price'], 2, '.', '');
		}


		//filtrowanie ceny
		if($request['filter_price_from'] != "" && $prod['products_price'] < $request['filter_price_from']) {continue;} //dolne ograniczenie ceny
		if($request['filter_price_to'] != "" && $prod['products_price'] > $request['filter_price_to']) {continue;} //górne ograniczenie ceny

		//dopisywanie produktu do tablicy wynikowej
		$response[$prod['id_product']] = array("ean" => $prod['ean13'], "sku" => $prod['reference'], "name" => $prod['name'], "quantity" => $prod['quantity'], "price" => $prod['products_price']);
	}

	return $response;
}





 /**
 * Funkcja zwraca szczegółowe dane wybranych produktów
 * Zwracane liczby (np ceny) powinny mieć format typu: 123456798.12 (kropka oddziela część całkowitą, 2 miejsca po przecinku)
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu zawierająca pola:
 *		products_id => 			tablica z numerami id produktów
 *		fields => 				tablica z nazwami pól do zwrócenia (jeśli pusta zwracany jest cały wynik)
 * @return array $response tablica z listą produktów w formacie:
 * 		id produktu =>
						'name' => nazwa produktu, 'ean' => Kod EAN, 'sku' => numer katalogowy, 'model' => nazwa modelu lub inny identyfikator np ISBN,
						'description' => opis produktu (może zawierać tagi HTML), 'description_extra1' => drugi opis produktu (np opis krótki) 'weight' => waga produktu w kg,
						'quantity' => dostępna ilość, 'man_name' => nazwa producenta, 'man_image' => pełny adres obrazka loga producenta,
						'category_id' => numer ID głównej kategorii, 'category_name' => nazwa kategori do której należy przedmiot, 'tax' => wielkość podatku w formie liczby (np 23)
						'price' => cena brutto w PLN,
						'images' => tablica z pełnymi adresami dodatkowych obrazków (pierwsze zdjęcie główne, reszta w odpowiedniej kolejności),
						'features' => tablica z opisem cech produktu. Poszczególny element tablicy zawiera nazwę i wartość cechy, np array('Rozdzielczość','Full HD')
						'variants' => tablica z wariantami produktu do wyboru (np kolor, rozmiar). Format pola opisany jest w kodzie poniżej
 */
function Shop_ProductsData($request)
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy
	$got_feature_product = false; // test na istnienie tabeli feature_product;

	//pobieranie stawek podatków do dwóch tablic (obsługa różnej budowy bazy dla różnych wersji prestashop)
	$tax_rates_table = array(); $tax_rules_table = array();

	// drzewo kategorii
	$all_cats = Shop_ProductsCategories($request);

	$tax_query = DB_Query("SELECT id_tax, rate FROM ${dbp}tax");
	while($tax = DB_Fetch($tax_query)){$tax_rates_table[$tax['id_tax']] = $tax['rate'];}
	$sql = "SELECT tr.id_tax_rules_group, t.rate
		FROM `${dbp}tax` t
		JOIN `${dbp}tax_rule` tr ON t.id_tax = tr.id_tax
		LEFT JOIN `${dbp}country` c ON c.id_country = tr.id_country
		ORDER BY c.iso_code = 'PL'";
	$tax_query = DB_Query($sql);
	while($tax = DB_Fetch($tax_query))	{$tax_rules_table[$tax['id_tax_rules_group']] = $tax['rate'];}

	//pobieranie danych produktów z bazy danych
	$sql = "SELECT DISTINCT p.*, pl.*, m.name as manufacturer".($options['table_stock']?(", sa.id_shop, ".($options['shop_group_id'] ? 'if(isnull(sa.quantity), if(isnull(sa0.quantity), p.quantity, sa0.quantity), sa.quantity)' : 'if(isnull(sa.quantity), p.quantity, sa.quantity)')." as quantity"):"").",
		${options['currency_conv']}*p.price price, p.id_tax_rules_group id_tax_rules_group
		FROM `${dbp}product` p
		".($options['table_stock']?"LEFT JOIN `${dbp}stock_available` sa ON sa.id_product = p.id_product AND sa.id_product_attribute = 0".(empty($options['shop_id'])?'':(" AND (sa.id_shop = {0} OR sa.id_shop = 0)".($options['shop_group_id'] ? " AND sa.id_shop_group = ${options['shop_group_id']} LEFT JOIN `${dbp}stock_available` sa0 ON sa0.id_product = p.id_product AND sa0.id_product_attribute = 0  AND (sa0.id_shop = {0} OR sa.id_shop = 0) AND sa0.id_shop_group = 0" : ''))):"")."
		INNER JOIN ${dbp}product_lang pl ON p.id_product = pl.id_product
		LEFT JOIN ${dbp}manufacturer m ON p.id_manufacturer = m.id_manufacturer
		WHERE
		p.id_product IN ({2}) AND
		pl.id_lang = '{1}'";
		if($options['shop_id']!=""){$sql.=" AND pl.id_shop = '{0}'";}

	// ceny z oddzielnej tabeli
	if ($options['shop_id'] != '')
	{
		$sql = preg_replace('/(\s+WHERE\s+)/s', " JOIN `${dbp}product_shop` ps ON ps.id_product = p.id_product AND ps.id_shop = {0} $1 ", $sql);
		$sql = str_replace('p.price', 'if(ps.price, ps.price, p.price)', $sql);
		$sql = str_replace('p.id_tax_rules_group', 'if(ps.id_tax_rules_group, ps.id_tax_rules_group, p.id_tax_rules_group)', $sql);
	}

	if ($options['table_stock'])
	{
		$sql = preg_replace('/^SELECT DISTINCT/', '$0 sa.id_shop,', $sql);
		$sql .= " ORDER BY p.id_product, sa.id_shop DESC";
	}

	$result = DB_Query($sql, $options['shop_id'], $options['lang_id'], implode(', ', $request['products_id']));
	while($prod = DB_Fetch($result))
	{
		// jeśli kilka wpisów w stock_available, sumujemy stan magazynowy
		if (isset($response[$prod['id_product']]) and $prod['id_shop'])
		{
			$response[$prod['id_product']]['quantity'] += $prod['quantity'];
			continue;
		}

		//pobieranie podstawowych danych o produkcie
		$p = array();
		$p['name'] = $prod['name'];
		$p['model'] = $prod['upc'];
		$p['ean'] = $prod['ean13'];
		$p['sku'] = $prod['reference'];
		$p['description'] = $prod['description'];
		$p['description_extra1'] = $prod['description_short'];
		$p['weight'] = $prod['weight'];
		$p['quantity'] = $prod['quantity'];
		$p['man_name'] = $prod['manufacturer'];
		$p['man_image'] = $options['images_folder']."m/".$prod['id_manufacturer'].".jpg";
		$p['images'] = array();

		if (!($p['category_id'] = $prod['id_category_default']))
		{
			$sql = "SELECT id_category FROM `${dbp}category_product`
				WHERE id_product = '{0}'
				ORDER BY position LIMIT 1";
			$p['category_id'] = (int)DB_Result(DB_Query($sql, $prod['id_product']));
		}

		$p['category_name'] = $all_cats[$p['category_id']];

		//pobieranie wysokosci podatku w zależności od budowy bazy
		if(isset($prod['id_tax']) && $prod['id_tax'] != 0)
		{$tax_rate = $tax_rates_table[$prod['id_tax']];}
		elseif(isset($prod['id_tax_rules_group']) && $prod['id_tax_rules_group'] != 0)
		{$tax_rate = $tax_rules_table[$prod['id_tax_rules_group']];}
		else
		{$tax_rate = $options['def_tax_rate'];}

		$p['tax'] = $tax_rate;

		$p['price_netto'] = $prod['price'];
		$p['price'] = $prod['price'];

		//wyliczanie ceny brutto
		$p['price'] = $p['price']*(1+($options['add_tax'] ? $tax_rate : 0)/100);

		$img_idx = array(); // identyfikatory obrazków wg pozycji w $p['images']

		//pobieranie obrazków
		$sql = "SELECT im.*
			FROM `${dbp}image` im
			" . (empty($options['shop_id']) ? '' :"JOIN `${dbp}image_shop` ims ON im.id_image = ims.id_image AND ims.id_shop IN (0, '{1}')") . "
			WHERE im.id_product = '{0}' ORDER BY `cover` DESC, `position` ASC";
		$imgs = DB_Query($sql, $prod['id_product'], $options['shop_id']);
		while($img = DB_Fetch($imgs))
		{
			$img_idx[count($p['images'])] = $img['id_image'];

			if($options['images_mode'] == 1)
			{$p['images'][] = $options['images_folder']."p/".implode("/",str_split($img['id_image']))."/".$img['id_image']."${options['img_suffix']}.jpg";}
			else
			{$p['images'][] = $options['images_folder']."p/".$prod['id_product']."-".$img['id_image'].".jpg";}
		}

		//pobieranie cech produktu (np. długość, kolor, rozmiar itp.), jeśli wersja sklepu to wspiera
		if ($got_feature_product or DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}feature_product'"))==1)
		{
			$got_feature_product = true;

			$sql = "SELECT *, GROUP_CONCAT(fvl.value SEPARATOR '|') value
				FROM `${dbp}feature_product` fp
				INNER JOIN `${dbp}feature` f ON f.id_feature = fp.id_feature
				INNER JOIN `${dbp}feature_lang` fl ON fl.id_feature = fp.id_feature
				INNER JOIN `${dbp}feature_value_lang` fvl ON fvl.id_feature_value = fp.id_feature_value
				WHERE fp.id_product = '{0}' AND fl.id_lang = '{1}' AND fvl.id_lang = '{1}'
				GROUP BY fp.id_feature";
			$sql .= DB_NumRows(DB_Query("SHOW COLUMNS FROM `${dbp}feature` LIKE 'position'")) ? ' ORDER BY f.position' : '';
			$features_res = DB_Query($sql, $prod['id_product'], $options['lang_id']);

			while($f = DB_Fetch($features_res))
			{
				$p['features'][] = array($f['name'], $f['value']);
			}
		}

		//pobieranie wariantów produktów - można pominąć jeśli platforma nie obsługuje wariantów do wyboru (np. kolor, rozmiar itp)
		//format tablicy wariantów:
		//		id_wariantu => array('full_name' => pełna nazwa produktu z wariantem, 'name' => nazwa wariantu bez nazwy produktu, 'price' => cena, 'quantity' => stan magazynowy)
		$p['variants'] = array();
		$vimages_in_main = array(); // zdjęcia wariantów obecne w galerii głównej

		$sql = "SELECT pa.* ".($options['table_stock']?",sum(if(isnull(sa.quantity), pa.quantity, sa.quantity)) as quantity":"")."
			FROM `${dbp}product_attribute` pa
			".($options['table_stock']?"INNER JOIN ${dbp}stock_available sa ON sa.id_product = pa.id_product AND sa.id_product_attribute = pa.id_product_attribute":"") . ($options['shop_id'] ? (" AND (sa.id_shop = '{1}' OR sa.id_shop = 0)" . ($options['shop_group_id'] ? " AND (sa.id_shop_group = ${options['shop_group_id']} OR sa.id_shop_group = 0)" : '')) : '') . "
			WHERE pa.id_product = '{0}'
			GROUP BY pa.id_product, pa.id_product_attribute";
		$variants = DB_Query($sql, $prod['id_product'], $options['shop_id']);

		while($v = DB_Fetch($variants))
		{
			//pobieranie nazw atrybutów danego wariantu (wariant może składać się z kilku atrybutów do wyboru, np: kolor czerwony, rozmiar XL (dwa atrybuty do wyboru utworzyły jeden wariant)
			$variant_name = '';
			$vfeatures = array();
			$variant_names_res = DB_Query("SELECT agl.name, al.name value
						FROM `${dbp}product_attribute` pa
						INNER JOIN `${dbp}product_attribute_combination` pac ON pac.id_product_attribute = pa.id_product_attribute
						INNER JOIN `${dbp}attribute_lang` al ON al.id_attribute = pac.id_attribute AND al.id_lang = {2}
						LEFT JOIN `${dbp}attribute` a ON a.id_attribute = pac.id_attribute
						LEFT JOIN `${dbp}attribute_group_lang` agl ON agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = {2}
						WHERE pa.id_product = '{0}' AND pac.id_product_attribute = '{1}'
						ORDER BY pac.id_attribute DESC",
						$prod['id_product'], $v['id_product_attribute'], $options['lang_id']);
			while ($attr = DB_Fetch($variant_names_res))
			{
				$variant_name .= " ".$attr['value'];
				$vfeatures[] = array($attr['name'], $attr['value']);
			}

			//obrazki wariantu
			$vimages = array();
			$sql = "SELECT id_image
				FROM `${dbp}product_attribute_image`
				WHERE id_product_attribute = {0}";
			$res = DB_Query($sql, $v['id_product_attribute']);

			while ($vimg = DB_Fetch($res))
			{
				foreach ($img_idx as $i => $id_image)
				{
					if ($id_image == $vimg['id_image'])
					{
						$vimages[] = $p['images'][$i];
						$vimages_in_main[] = $i;
						break;
					}
				}
			}

			$p['variants'][$v['id_product_attribute']] = array(
				'full_name' => $prod['name'].$variant_name,
				'name' => trim($variant_name),
				'price' => number_format($v['price']*(1+$tax_rate/100)+$p['price'], 2, '.', ''),
				'quantity' => $v['quantity'],
				'sku' => $v['reference'],
				'ean' => $v['ean13'],
				'images' => $vimages,
				'features' => $vfeatures,
			);
		}

		// usunięcie zdjęć wariantów z galerii głównej
		$vimages_in_main = $options['show_all_images'] ? array() : array_unique($vimages_in_main, SORT_NUMERIC);
		sort($vimages_in_main, SORT_NUMERIC);

		while (count($vimages_in_main))
		{
			unset($p['images'][array_pop($vimages_in_main)]);
		}
		
		//obsługa redukcji ceny
		if($options['special_price'] == 1)
		{
			$sql = "SELECT * FROM `${dbp}specific_price`
				WHERE id_product = '{0}' AND (`to` >= now() OR `to` = 0) AND (`from` <= now() OR `from` = 0) AND from_quantity = 1
				". (empty($options['shop_id']) ? '' : "AND (id_shop = {1} OR id_shop = 0) ") . (!$options['compat_mode'] ? " AND id_cart = 0 AND id_customer = 0 " : '') . ($options['currency_id'] ? " AND (id_currency = ${options['currency_id']} or id_currency = 0)" : '') . " AND id_group IN (0, {2}) LIMIT 1";
			$price_res = DB_Query($sql, $prod['id_product'], (int)$options['shop_id'], (int)$options['customer_group_id']);

			while ($price = DB_Fetch($price_res))
			{
				$base_price = ($price['price'] > 0) ? ($options['add_tax'] ? ($price['price']*(1+($tax_rate/100))) : $price['price']) : $p['price'];

				if ($price['reduction'] != '' && $price['reduction_type'] == 'percentage')
				{
					$p['price'] = $base_price*(1-$price['reduction']);

					foreach ($p['variants'] as $variant_id => $variant)
					{
						$p['variants'][$variant_id]['price'] = number_format((($price['price'] > 0) ? $price['price'] : $variant['price'])*(1-$price['reduction']), 2, '.', '');
					}
				}
				elseif ($price['reduction'] != '' && $price['reduction_type'] == 'amount')
				{
					$p['price'] = $base_price - $price['reduction'];

					foreach ($p['variants'] as $variant_id => $variant)
					{
						$p['variants'][$variant_id]['price'] = number_format((($price['price'] > 0) ? $price['price'] : $variant['price']) - $price['reduction'], 2, '.', '');
					}
				}
			}
		}

		// formatowanie ceny
		$p['price'] = number_format($p['price'], 2, ".", "");

		//pobieranie wartości dodatkowych tagów z zewnętrznego skryptu
		//wszystkie pola tablicy $p mogą zostać użyte w szablonie aukcji: Nowe pole np. $p['test'] będzie dostępne w szablonie aukcji jako tag [test]
		//poniższy warunek może pozostać identyczny niezależnie od platformy sklepu
		if(file_exists("baselinker_extra.php"))
		{
			//pobranie dodatkowych informacji z zewnętrznego pliku pliku.
			//Plik tworzony jest indywidualnie dla każdego sprzedawcy jeśli zgłosi potrzebę pobierania dodatkowych danych ze sklepu.
			//Pozwala to uniknąć ingerowania w standardowy plik baselinker.php
			include("baselinker_extra.php");
		}


		//wyrzucanie niepotrzebnych wartości jeśli określono pola do pobrania
		//poniższy kod może pozostać identyczny niezależnie od platformy sklepu
		if(isset($request['fields']) and !(count($request['fields']) == 1 && $request['fields'][0] == "") && !count($request['fields']) == 0)
		{
			$temp_p = array();
			foreach($request['fields'] as $field)
			{$temp_p[$field] = $p[$field];}
			$p = $temp_p;
		}

		$response[$prod['id_product']] = $p;
	}

	return $response;
}





 /**
 * Funkcja zwraca stan magazynowy wszystkich produktów i ich wariantów
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu, w przypadku tej funkcji nie używana
 * @return array $response tablica ze stanem magazynowym wszystkich produktów, w formacie:
 * 		id produktu => ID produktu jest kluczem tablicy, wartością jest tablica składająca się ze stanów wariantów
 *                             id wariantu => kluczem tablicy jest ID wariantu (0 w przypadku produktu głównego)
 *                             stan => wartościa jest stan magazynowy
 *          Przykład: array('432' => array('0' => 4, '543' => 2, '567' => 3)) - produkt ID 432, stan głównego produktu to 4, posiada dwa warianty (ID 543 i 563) o stanach 2 i 3.
 */
function Shop_ProductsQuantity($request)
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	$response = array();

	//pobieranie stanów magazynowych z bazy danych
	if ($options['table_stock'])  // jeśli używana jest oddzielna tabela stanów magazynowych
	{
		$sql = "SELECT p.id_product, sa.id_shop, " . ($options['shop_group_id'] ? 'if(isnull(sa.quantity), if(isnull(sa0.quantity), pa.quantity, sa0.quantity), sa.quantity) quantity,  if(isnull(sa.id_product_attribute), sa0.id_product_attribute, sa.id_product_attribute) id_product_attribute, if(isnull(sam.quantity), if(isnull(sam0.quantity), p.quantity, sam0.quantity), sam.quantity) main_quantity' : 'if(isnull(sa.quantity), pa.quantity, sa.quantity) quantity, if(isnull(sam.quantity), p.quantity, sam.quantity) main_quantity, sa.id_product_attribute') . " 
			FROM `${dbp}product` p
			/* stany produktów głównych */
			LEFT JOIN `${dbp}stock_available` sam ON sam.id_product = p.id_product AND sam.id_product_attribute = 0"
			. (($options['shop_id'] != '') ? " AND (sam.id_shop = '{0}' OR sam.id_shop = 0)" : '')
			. ($options['shop_group_id'] ? " AND sam.id_shop_group = ${options['shop_group_id']} LEFT JOIN `${dbp}stock_available` sam0 ON sam0.id_product = p.id_product AND sam0.id_product_attribute = 0 AND (sam0.id_shop = {0} OR sam0.id_shop = 0) AND sam0.id_shop_group = 0" : '') . "

			/* stany wariantów */
			LEFT JOIN `${dbp}product_attribute` pa ON pa.id_product = p.id_product
			LEFT JOIN `${dbp}stock_available` sa ON sa.id_product = p.id_product AND sa.id_product_attribute = pa.id_product_attribute "
			. (($options['shop_id'] != '') ? " AND (sa.id_shop = '{0}' OR sa.id_shop = 0)" : '')
			 . ($options['shop_group_id'] ? " AND sa.id_shop_group = ${options['shop_group_id']} LEFT JOIN `${dbp}stock_available` sa0 ON sa0.id_product = p.id_product AND sa0.id_product_attribute = pa.id_product_attribute  AND (sa0.id_shop = {0} OR sa0.id_shop = 0) AND sa0.id_shop_group = 0" : '');

		$sql .= ' ORDER BY p.id_product' . (($options['shop_id'] != '') ? ', sa.id_shop DESC' : '');
	}
	else // jeśli stany magazynowe trzymane są w tabelach produktów i wariantów produktów
	{
		$sql = "SELECT p.id_product, p.quantity main_quantity, pa.quantity, pa.id_product_attribute
			FROM `${dbp}product` p
			LEFT JOIN `${dbp}product_attribute` pa ON pa.id_product = p.id_product";
	}

	$result = DB_Query($sql, $options['shop_id']);

	while ($prod = DB_Fetch($result))
	{
		// dla bezwariantowego produktu, którego stan pobrany został bezpośrednio z tabeli products
		if (!$prod['id_product_attribute'] and isset($prod['main_quantity']))
		{
			$response[$prod['id_product']][0] = $prod['main_quantity'];
			continue;
		}

		// jeśli stan głównego produktu jest 0, podliczamy łączną liczbę wariantów
		if ($prod['id_product_attribute'] and $prod['main_quantity'] === 0)
		{
			$response[$prod['id_product']][0] += $prod['quantity'];
		}
		elseif (!isset($response[$prod['id_product']][0]))
		{
			$response[$prod['id_product']][0] = $prod['main_quantity'];
		}

		$response[$prod['id_product']][intval($prod['id_product_attribute'])] = $prod['quantity'];
	}

	return $response;
}



 /**
 * Funkcja ustawia stan magazynowy wybranych produktów i ich wariantów
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu:
 *		products => tablica zawierająca informacje o zmianach stanu produktu. Każdy element tablicy jest również tablicą składającą się z pól:
 *					product_id => ID produktu
 *					variant_id => ID wariantu (0 jeśli produkt główny)
 *					operation => rodzaj zmiany, dopuszczalne wartości to: 'set' (ustawia konkretny stan), 'change' (dodaje do stanu magazynowego, ujemna liczba w polu quantity zmniejszy stan o daną ilość sztuk, dodatnia zwiększy)
 *					quantity => zmiana stanu magazynowego (ilośc do ustawienia/zmniejszenia/zwiększenia zależnie od pola operation)
 * @return array $response tablica zawierajaca pole z ilością zmienionych produktów:
 * 		counter => ilość zmienionych produktów
 */
function Shop_ProductsQuantityUpdate($request)
{	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	$count = count($request['products']);

	while ($prod = array_shift($request['products']))
	{
		$prod['variant_id'] = (int)$prod['variant_id'];

		//ustawianie ilości bezwzględnie (dokładny nowy stan) lub względnie (zmniejszenie/zwiększenie)
		if($prod['operation'] == "set")
		{$new_quantity = (int)$prod['quantity'];}
		else
		{$new_quantity = "`quantity` + (".(int)$prod['quantity'].")";}

		//w zależności od ustawień prestashop stan magazynowy może być przechowywany w różnych tabelach
		if($options['table_stock'])
		{
			$sql = "UPDATE `${dbp}stock_available` SET `quantity` = ${new_quantity}
				WHERE id_product = '{0}' AND id_product_attribute = '{1}'
				" . (($options['shop_id'] != '') ? ("AND (id_shop = {2} OR id_shop = 0)" . ($options['shop_group_id'] ? " AND (id_shop_group = ${options['shop_group_id']} OR id_shop_group = 0)" : '')) : '') . "
				ORDER BY (out_of_stock = 0) DESC LIMIT 1";
			DB_Query($sql, $prod['product_id'], $prod['variant_id'], $options['shop_id']);

			// aktualizacja stanu produktu głównego
			if ($prod['variant_id'] and $prod['operation'] == 'change')
			{
				$sql = "SELECT id_stock_available, quantity
					FROM `${dbp}stock_available`
					WHERE id_product = '{0}' AND id_product_attribute = 0 AND quantity > 0"
					. (($options['shop_id'] != '') ? (" AND (id_shop = {1} OR id_shop = 0)" . ($options['shop_group_id'] ? " AND (id_shop_group = ${options['shop_group_id']} OR id_shop_group = 0)" : '')) : '');
				if ($sa = DB_Fetch(DB_Query($sql, $prod['product_id'], $options['shop_id'])))
				{
					$sql = "UPDATE `${dbp}stock_available` SET quantity = quantity+{0}
						WHERE id_stock_available = {1}" . ($options['shop_group_id'] ? " AND (id_shop_group = ${options['shop_group_id']} OR id_shop_group = 0)" : '');
					DB_Query($sql, $prod['quantity'], $sa['id_stock_available']);
				}
			}

			if (false and $prod['operation'] == 'change')
			{
				// aktualizacja stanów w magazynie
				$sql = "UPDATE `${dbp}stock` SET physical_quantity = if(cast(physical_quantity as signed) + {0} >= 0, physical_quantity + {0}, 0),
					usable_quantity = if(cast(usable_quantity as signed) + {0} >= 0, usable_quantity + {0}, 0)
					WHERE id_warehouse = {1} AND id_product = {2} AND id_product_attribute = {3}";
				DB_Query($sql, $prod['quantity'], $options['warehouse_id'], $prod['product_id'], (int)$prod['variant_id']);
			}
		}
		else
		{
			if($prod['variant_id'] == 0)
			{
				$sql = "UPDATE `${dbp}product` SET `quantity` = ${new_quantity} WHERE id_product = '{0}' LIMIT 1";
			}
			else
			{
				$sql = "UPDATE `${dbp}product_attribute` SET `quantity` = ${new_quantity}
					WHERE id_product = '{0}' AND id_product_attribute = '{1}' LIMIT 1";
			}

			DB_Query($sql, $prod['product_id'], $prod['variant_id']);
		}

		if (!$prod['variant_id'] and $prod['operation'] == 'change')
				{
			// czy produkt jest paczką?
			if (DB_Result(DB_Query("SELECT cache_is_pack FROM `${dbp}product` WHERE id_product = {0}", $prod['product_id'])))
					{
				// pobieramy wszystkie produkty/warianty składowe i ich ilości per pack
				$sql = "SELECT id_product_item product_id, id_product_attribute_item variant_id,
					quantity quantity, 'change' AS operation
					FROM `${dbp}pack` WHERE id_product_pack = {1}";
				$res = DB_Query($sql, $prod['quantity'], $prod['product_id']);

				// i dodajemy do kolejki QuantityUpdate
				while ($sub_prod = DB_Fetch($res))
					{
					$sub_prod['quantity'] *= $prod['quantity'];
					array_push($request['products'], $sub_prod);
				}
			}
		}
	}

	return array('counter' => $count);
}


 /**
 * Funkcja tworzy zamówienie w sklepie na podstawie nadesłanych danych
 * Jeśli funkcja otrzyuje na wejściu ID zamówienia, aktualizuje dane zamówienie zamiast tworzyć nowe
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu, zawiera informacje o zamówieniu w formacie:
 *		previous_order_id => ID zamówienia (jeśli pierwszy raz dodawane do sklepu, wartość jest pusta. Peśli było już wcześniej dodane, wartość zawiera poprzedni numer zamówienia)
 *		delivery_fullname, delivery_company, delivery_address, delivery_city, delivery_postcode, delivery_country => dane dotyczące adresu wysyłki
 *		invoice_fullname, invoice_company, invoice_address, invoice_city, invoice_postcode, invoice_country, invoice_nip => dane dotyczące adresu płatnika faktury
 *		phone => nr telefonu, email => adres email,
 *		delivery_method => nazwa sposóbu wysyłki, delivery_method_id => numer ID sposobu wysyłki, delivery_price => cena wysyłki
 *		user_comments => komentarz kupującego, currency => waluta zamówienia, status_id => status nowego zamówienia
 *              change_products_quantity => flaga (bool) informująca, czy po stworzeniu zamówienia zmniejszony ma zostać stan zakupionych produktów
 *		products => tablica z zakupionymi produktami w formacie:
 *				[] =>
 *						id => ID produktu
 *                                              variant_id => ID wariantu
 *						name => nazwa produktu (używana jeśli nie można pobrać jej z bazy na podstawie id)
 *						price => cena brutto w PLN
 *						currency => waluta
 *						quantity => zakupiona ilość
 *						attributes => tablica z atrybutami produktu w formacie:
 *									[] =>
 *											name =>	nazwa atrybutu (np. "kolor")
 *											value => wartość atybutu (np. "czerwony")
 *											price => różnica ceny dla tego produktu (np. "-10.00")
 *													 zmiana ceny jest już uwzględniona w cenie produktu
 * @return array $response tablica zawierająca numer nowego zamówienia:
 * 		'order_id' => numer utworzonego zamówienia
 */
function Shop_OrderAdd($request)
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	$cart_id = 0; // koszyk powiązany z zamówieniem

	//jeśli zamówienie jest ponownie dodawane do bazy sklepu, wcześniejsze dane są usuwane
	//przy ponownym dodawaniu zamówienia (aktualizowaniu), $request['previous_order_id'] zawiera poprzedni numer danego zamówienia w sklepie
	$reference = '';

	if($request['previous_order_id'] != "")
	{
		// usuwanie powiązanego koszyka
		$res = DB_Query("SELECT id_cart, reference FROM `${dbp}orders` WHERE id_order = {0}", $request['previous_order_id']);

		if ($prev_o = DB_Fetch($res))
		{
			$reference = $prev_o['reference'];
			$cart_id = $prev_o['id_cart'];
			DB_Query("DELETE FROM `${dbp}cart` WHERE id_cart = {0}", $cart_id);
			DB_Query("DELETE FROM `${dbp}cart_product` WHERE id_cart = {0}", $cart_id);
			DB_Query("DELETE FROM `${dbp}message` WHERE id_cart = {0} AND id_employee = 0 ORDER BY id_message DESC LIMIT 1", $prev_o['id_cart']);
		}

		// usuwanie trzonowych tabeli zamówienia
		$table_to_clear = array("orders", "order_history", "order_detail", "order_carrier");

		if ($options['compat_mode'])
		{
			array_pop($table_to_clear);
		}

		foreach($table_to_clear as $tbl)
		{DB_Query("DELETE FROM `${dbp}${tbl}` WHERE `id_order` = '{0}'", $request['previous_order_id']);}
	}

	// komentowanie części kwerend
	$cmo = $options['compat_mode'] ? '/*' : '';
	$cmc = $options['compat_mode'] ? '*/' : '';

	//usuwanie nicka allegro
	$invoice_fullname = preg_replace('/\s*\[.+?\]\s*/', '', $request['invoice_fullname']);
	$delivery_fullname = preg_replace('/\s*\[.+?\]\s*/', '', $request['delivery_fullname']);

	//wyciąganie imienia i nazwiska
	$invoice_fullname_exp = explode(" ",$invoice_fullname);
	$invoice_lastname = array_pop($invoice_fullname_exp);
	$invoice_firstname = implode(" ",$invoice_fullname_exp);

	//pobieranie id krajów
	$request['invoice_country'] = $request['invoice_country'] ? $request['invoice_country'] : 'PL';
	$request['delivery_country'] = $request['delivery_country'] ? $request['delivery_country'] : 'PL';

	$sql = "SELECT id_country FROM `${dbp}country` WHERE iso_code = '{0}' LIMIT 1";

	$res = DB_Query($sql, $request['invoice_country_code']);
	$invoice_country_id = (int)DB_Result($res);

	$res = DB_Query($sql, $request['delivery_country_code']);
	$delivery_country_id = (int)DB_Result($res);

	// pobieranie istniejącego klienta
	$customer_id = 0;
	$res = DB_Query("SELECT id_customer, secure_key FROM `${dbp}customer` WHERE email = '{0}' AND email <> ''", $request['email']);

	if(DB_NumRows($res) > 0)
	{
		$customer = DB_Fetch($res);
		$customer_id = $customer['id_customer'];
		$secure_key = $customer['secure_key'];
	}

	$invoice_address_id = $delivery_address_id = 0;

	// dla aktualizowanych zamówień sprawdzamy, czy adres już istnieje w bazie
	if ($customer_id)
	{
		$res = DB_Query("SELECT id_address FROM `${dbp}address` WHERE id_customer = '{0}' AND address1 = '{1}' AND company = '{2}'",
				$customer_id, $request['invoice_address'], $request['invoice_company']);
		$invoice_address_id = (int)DB_Result($res);
	}

	if (!$invoice_address_id)
	{
		//dodawanie adresu płatnika do tabeli address
		$sql = "INSERT INTO `${dbp}address` (
				`id_address` ,	`id_country` ,	`id_state` ,`id_customer` ,	`id_manufacturer` ,	`id_supplier` ,
				`alias` ,	`company` ,	`lastname` ,	`firstname` ,
				`address1` ,	`address2` ,	`postcode` ,	`city` ,
				`other` ,	`phone` ,	`phone_mobile` , `vat_number`,
				`date_add` ,	`date_upd` ,	`active` ,	`deleted`
				) VALUES (
				NULL , '{invoice_country_id}', NULL ,	'0', '0', '0',
				'{invoice_fullname}', '{invoice_company}', '{invoice_lastname}', '{invoice_firstname}',
				'{invoice_address}', '', '{invoice_postcode}', '{invoice_city}',
				NULL , '{phone}' , NULL , '{invoice_nip}',
				'{now}', '{now}', '1', '0'
				);";
		DB_Query($sql, array(
			'invoice_fullname' => $request['invoice_fullname'],
			'invoice_company' => $request['invoice_company'],
			'invoice_lastname' => $invoice_lastname,
			'invoice_firstname' => $invoice_firstname,
			'invoice_address' => $request['invoice_address'],
			'invoice_city' => $request['invoice_city'],
			'invoice_postcode' => $request['invoice_postcode'],
			'invoice_country_id' => $invoice_country_id,
			'phone' => $request['phone'],
			'invoice_nip' => $request['invoice_nip'],
			'now' => date('Y-m-d H:i:s'),
		));
		$invoice_address_id = DB_Identity();
	}

	//dodawanie adresu dostawy do tabeli address jeśli jest inny niż adres płatnika
	if($request['invoice_fullname'] == $request['delivery_fullname'] && $request['invoice_address'] == $request['delivery_address'] &&  $request['invoice_company'] == $request['delivery_company'])
	{$delivery_address_id = $invoice_address_id;}
	else
	{
		// dla aktualizowanych zamówień sprawdzamy, czy adres już istnieje w bazie
		if ($customer_id)
		{
			$res = DB_Query("SELECT id_address FROM `${dbp}address` WHERE id_customer = '{0}' AND address1 = '{1}' AND company = '{2}'",
					$customer_id, $request['delivery_address'], $request['delivery_company']);
			$delivery_address_id = (int)DB_Result($res);
		}

		if (!$delivery_address_id)
		{
			//wyciąganie imienia i nazwiska
			$delivery_fullname_exp = explode(" ",$delivery_fullname);
			$delivery_lastname = array_pop($delivery_fullname_exp);
			$delivery_firstname = implode(" ",$delivery_fullname_exp);

			//dodawanie adresu dostawy
			$sql = "INSERT INTO `${dbp}address` (
				`id_address` ,	`id_country` ,	`id_state` ,`id_customer` ,	`id_manufacturer` ,	`id_supplier` ,
				`alias` ,	`company` ,	`lastname` ,	`firstname` ,
				`address1` ,	`address2` ,	`postcode` ,	`city` ,
				`other` ,	`phone` ,	`phone_mobile` ,
				`date_add` ,	`date_upd` ,	`active` ,	`deleted`
				) VALUES (
				NULL , '{delivery_country_id}', NULL ,	'0', '0', '0',
				'{delivery_fullname}', '{delivery_company}', '{delivery_lastname}', '{delivery_firstname}',
				'{delivery_address}', '', '{delivery_postcode}', '{delivery_city}',
				NULL , '{phone}' , NULL ,
				'{now}', '{now}', '1', '0'
				);";
			DB_Query($sql, array(
				'delivery_fullname' => $request['delivery_fullname'],
				'delivery_company' => $request['delivery_company'],
				'delivery_lastname' => $delivery_lastname,
				'delivery_firstname' => $delivery_firstname,
				'delivery_address' => $request['delivery_address'],
				'delivery_city' => $request['delivery_city'],
				'delivery_postcode' => $request['delivery_postcode'],
				'delivery_country_id' => $delivery_country_id,
				'phone' => $request['phone'],
				'now' => date('Y-m-d H:i:s'),
			));
			$delivery_address_id = DB_Identity();
		}
	}

	if (!$customer_id)	
	{
		//klucz bezpieczeństwa klienta
		$secure_key = md5(microtime(true));
		$customer_firstname = empty($invoice_firstname) ? $delivery_firstname : $invoice_firstname;
		$customer_lastname = empty($invoice_lastname) ? $delivery_lastname : $invoice_lastname;

		$sql = "INSERT INTO `${dbp}customer`
			(`id_gender`, `id_default_group`, `firstname`, `lastname`, `email`, `passwd`, `last_passwd_gen`, ".(($options['shop_id'] != "")?"`id_shop`,":"")."
			`birthday`, `newsletter`, `ip_registration_newsletter`, `newsletter_date_add`, `optin`, `secure_key`,
			`active`, `deleted`, `date_add`, `date_upd`$cmo, `id_lang`, `company`, `is_guest`$cmc)
			VALUES
			(1, '{customer_group}', '{customer_firstname}', '{customer_lastname}', '{email}', 'allegro_user', '{now}', ".(($options['shop_id'] != "")?"'{shop_id}',":"")."
			'1970-01-01', 0, NULL, NULL, 1, '{secure_key}',
			1, 0, '{now}', '{now}'$cmo, '{lang_id}', '{invoice_company}', '{is_guest}'$cmc)";
		DB_Query($sql, array(
			'customer_group' => $options['customer_group_id'],
			'customer_firstname' => $customer_firstname,
			'customer_lastname' => $customer_lastname,
			'email' => ($options['customer_is_guest'] and $options['guest_email']) ? $options['guest_email'] : $request['email'],
			'now' => date('Y-m-d H:i:s'),
			'shop_id' => $options['shop_id'],
			'lang_id' => $options['lang_id'],
			'invoice_company' => $request['invoice_company'],
			'secure_key' => $secure_key,
			'is_guest' => $options['customer_is_guest'],
		));

		if ($customer_id = DB_Identity() and $options['customer_group_id'])
		{
			DB_Query("INSERT INTO `${dbp}customer_group` (id_customer, id_group) VALUES ({0}, {1})", $customer_id, $options['customer_group_id']);

			if (preg_match_all('/\d+/', $options['other_customer_groups'], $m))
			{
				foreach ($m[0] as $group_id)
				{
					DB_Query("INSERT INTO `${dbp}customer_group` (id_customer, id_group) VALUES ({0}, {1})", $customer_id, $group_id);
				}
			}
		}
	}

	//aktualizacja id klienta w adresach dostawy/płatności
	DB_Query("UPDATE `${dbp}address` SET id_customer = {0} WHERE id_address IN ({1}, {2})", $customer_id, $invoice_address_id, $delivery_address_id);

	//pobieranie numeru id waluty PLN
	$sql = "SELECT id_currency FROM ${dbp}currency WHERE iso_code LIKE '${request['currency']}' ";
	$res = DB_Query($sql);
	if(DB_NumRows($res) > 0)
	{
		$curr = DB_Fetch($res);
		$id_currency = $curr['id_currency'];
	}
	else
	{$id_currency = 1;}

	//pobieranie danych sposobu wysyłki
	$id_carrier = 0;
	$shipping_tax = $options['def_tax_rate'];

	if($request['delivery_method_id'])
	{
		$id_carrier = $request['delivery_method_id'];

		$sql = "SELECT t.rate FROM `${dbp}carrier` c
			INNER JOIN `${dbp}tax_rule` tr ON c.id_tax_rules_group = tr.id_tax_rules_group
			INNER JOIN `${dbp}tax` t ON t.id_tax = tr.id_tax
                        WHERE c.id_carrier = '{0}'
			LIMIT 1";
		$res = DB_Query($sql, $id_carrier);

		if (DB_NumRows($res))
		{
			$row = DB_Fetch($res);
			$shipping_tax = $row['rate'];
		}
	}

	//losowy numer referencyjny zamówienia
	$reference = $s = $reference ? $reference : strtoupper(substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 5)), 0, 9));

	//wygenerowanie koszyka
	$sql = "INSERT INTO `${dbp}cart`
		(${cmo}id_shop_group, id_shop, ${cmc}id_carrier, ${cmo}delivery_option, ${cmc}id_lang, id_address_delivery, id_address_invoice,
		id_currency, id_customer, id_guest, secure_key, recyclable, gift, gift_message, ${cmo}mobile_theme,
		allow_seperated_package, ${cmc}date_add, date_upd, id_cart)
		VALUES ($cmo{0}, '{1}', ${cmc}{2}, ${cmo}'{3}', ${cmc}{4}, {5}, {6}, {7}, {8}, 0, '{9}', 0, 0, '', ${cmo}0, 0, ${cmc}'{10}', '{10}', {11})";
	DB_Query($sql, 0, $options['shop_id'], $id_carrier, serialize(array($delivery_address_id => "$id_carrier,")), $options['lang_id'], $delivery_address_id, $invoice_address_id, $id_currency, $customer_id, $secure_key, date('Y-m-d H:i:s'), $cart_id ? $cart_id : 'null');
	$cart_id = $cart_id ? $cart_id : (int)DB_Identity();

	if ($request['want_invoice'])
	{
		// moduł tw_paragonfaktura
		if (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}tw_paragonfaktura'")))
		{
			DB_Query("INSERT INTO `${dbp}tw_paragonfaktura` (id_cart, document_type) VALUES ('{0}', 'Invoice')", $cart_id);
		}
	}

	//metoda płatności - domyślnie przelew
	$payment = $request['payment_method'] ? $request['payment_method'] : 'Bank wire';
	$payment_module = 'bankwire';

	if (preg_match('/pobrani|\Wcod\W/i', $request['delivery_method']) or $request['payment_method_cod'])
	{
		$payment = 'Za pobraniem';
		$payment_module = 'cashondelivery';
	}

	//uwzględnienie mapowania płatności
	if ($request['payment_method_id'])
	{
		if (!isset($payment_methods))
		{
			$payment_methods = Shop_PaymentMethodsList($request);
		}

		$payment_module = $request['payment_method_id'];

		if ($payment_methods[$payment_module])
		{
			$payment = $payment_methods[$payment_module];
		}
	}

	//dodanie zamowienia do tabeli orders
	$sql = "INSERT INTO `${dbp}orders` (
			`id_order` ,`id_carrier` ,`id_lang` ,`id_customer`$cmo, `reference`, `current_state`$cmc, ".(($options['shop_id'] != "")?"`id_shop`,":"")."
			`id_cart` ,`id_currency` ,`id_address_delivery` ,`id_address_invoice` ,
			`secure_key` ,`payment` ,`module` ,`recyclable` ,
			`gift` ,`gift_message` ,`shipping_number` ,`total_discounts` ,
			`total_paid` ,`total_paid_real` ,`total_products` ,`total_shipping` ,
			`total_wrapping` ,`invoice_number` ,`delivery_number` ,`invoice_date` ,
			`delivery_date` ,`valid` ,`date_add` ,`date_upd`
			)
			VALUES (
			'{previous_order_id}' , '{id_carrier}', '{lang_id}', '{customer_id}'$cmo, '{reference}', '{status_id}'$cmc, ".(($options['shop_id'] != "")?"'{shop_id}',":"")."
			 '{cart_id}', '{id_currency}', '{delivery_address_id}', '{invoice_address_id}',
			 '{secure_key}', '{payment}', '{payment_module}' , '0',
			 '0', NULL , NULL , '0.00',
			 '0.00', '0.00', '0.00', '0.00',
			 '0.00', '0', '0', '',
			 '', '0', '{now}', '{now}'
			);
			";
	$insert = DB_Query($sql, array(
		'previous_order_id' => $request['previous_order_id'],
		'id_carrier' => $id_carrier,
		'lang_id' => $options['lang_id'],
		'customer_id' => $customer_id,
		'reference' => $reference,
		'status_id' => $status_id,
		'shop_id' => $options['shop_id'],
		'cart_id' => $cart_id,
		'id_currency' => $id_currency,
		'delivery_address_id' => $delivery_address_id,
		'invoice_address_id' => $invoice_address_id,
		'secure_key' => $secure_key,
		'payment' => $payment,
		'payment_module' => $payment_module,
		'now' => date('Y-m-d H:i:s'),
	));
	if(!$insert){return "";}

	$this_order_id = DB_Identity(); //pobieranie numeru nowego zamówienia

	if (!$options['compat_mode'])
	{
		//dodawanie informacji o wysyłce
		$sql = "INSERT INTO `${dbp}order_carrier` (`id_order`, `id_carrier`, `id_order_invoice`, `weight`, `shipping_cost_tax_excl`, `shipping_cost_tax_incl`, `tracking_number`, `date_add`) VALUES
						({0}, {1}, 0, 0.000000, ".(100*$request['delivery_price']/(100+$shipping_tax)).", '{2}', '', '{3}');";
		DB_Query($sql, $this_order_id, $id_carrier, $request['delivery_price'], date('Y-m-d H:i:s'));
	}

	//dodawanie komentarza
	if($request['user_comments'] != "")
	{
		$sql = "INSERT INTO `${dbp}message` (`id_cart`, `id_customer`, `id_employee`, `id_order`, `message`, `private`, `date_add`) VALUES
				({0}, {2}, 0, {3}, '{1}', 0, '{4}');";
		DB_Query($sql, $cart_id, $request['user_comments'], $customer_id, $this_order_id, date('Y-m-d H:i:s'));

		// .. i dodatkowa tabela:
		if (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}customer_message'")))
		{
			$sql = "INSERT INTO `${dbp}customer_thread` (
				id_shop, id_lang, id_contact, id_customer, id_order, id_product, status, email, token, date_add, date_upd
				) VALUES ('{0}', '{1}', 0,    '{2}',       '{3}',    0,          'open', '{4}', '{5}', '{6}',    '{6}')";
			$ucres = DB_Query($sql, $options['shop_id'], $options['lang_id'], $customer_id, $this_order_id, $request['email'], 
				substr(preg_replace('/[^\d\w]/', '', base64_encode(md5(microtime(true)))), 0, 12), date('Y-m-d H:i:s'));

			if ($id_cust_th = DB_Identity($ucres))
			{
				$sql = "INSERT INTO `${dbp}customer_message` (
					id_customer_thread, id_employee, message, file_name, ip_address, user_agent, date_add, date_upd
					) VALUES ('{0}', 0, '{1}', '', '', '', '{2}', '{2}')";
				DB_Query($sql, $id_cust_th, $request['user_comments'], date('Y-m-d H:i:s'));
			}
		}
	}

	//obsługa produktów w zamówieniu
	$sum_products_price = 0;
	$sum_products_price_netto = 0;

	$ip = 0;

	// konsolidacja produktów o tym samym id
	while ($ip < count($request['products']))
	{
		$jp = $ip + 1;
		$prod0 = $request['products'][$ip];

		while ($jp < count($request['products']))
		{
			$prod1 = $request['products'][$jp];

			if ($prod0['id'] and $prod0['id'] == $prod1['id'] and $prod0['variant_id'] == $prod1['variant_id'] and $prod0['price'] == $prod1['price'])
			{
				$prod0['quantity'] += $prod1['quantity'];
				$request['products'][$ip] = $prod0;
				array_splice($request['products'], $jp, 1);
				continue;
			}

			$jp++;
		}

		$ip++;
	}

	if (!$options['compat_mode'])
	{
		$od_trg = DB_NumRows(DB_Query("SHOW FIELDS FROM `${dbp}order_detail` LIKE 'id_tax_rules_group'"));
	}
	else
	{
		$od_trg = true;
	}

	foreach ($request['products'] as $prod)
	{
		$id_tax = null;
		$sql = "SELECT p.*, pl.name as product_name$cmo, s.id_warehouse$cmc
			FROM `${dbp}product` p
			LEFT JOIN `${dbp}product_lang` pl ON p.id_product = pl.id_product AND pl.id_lang = '{0}'
			${cmo}LEFT JOIN `${dbp}stock` s ON s.id_product = p.id_product AND p.advanced_stock_management
			" . ($prod['variant_id'] ? " AND s.id_product_attribute = '{2}'" : '') . "$cmc
			WHERE p.id_product = '{1}' AND pl.id_lang = '{0}' ";
		$res = DB_Query($sql, $options['lang_id'], $prod['id'], $prod['variant_id']);

		// jeśli nie odnaleziono produktu po ID
		if (!($prod_data = DB_Fetch($res)))
		{
			// ustaw wartości domyślne
			$prod_data = array('quantity' => 0, 'weight' => 0);
		}

		if ($prod['variant_id'])
		{
			$sql = "SELECT GROUP_CONCAT(concat(agl.name, ': ', al.name) SEPARATOR  ', ')  variant_name
				FROM `${dbp}product_attribute_combination` pac
				JOIN `${dbp}attribute` a ON a.id_attribute = pac.id_attribute
				JOIN `${dbp}attribute_group_lang` agl ON agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = {0}
				JOIN `${dbp}attribute_lang` al ON al.id_attribute = pac.id_attribute AND al.id_lang = {0}
				WHERE id_product_attribute = {1}";
			$res = DB_Query($sql, $options['lang_id'], $prod['variant_id']);
			$prod_data['product_name'] .= ' '.(string)DB_Result($res);

			// pobieranie standardowych identyfikatorów wariantu
			$sql = "SELECT * FROM `${dbp}product_attribute` WHERE id_product = {0} AND id_product_attribute = {1}";
			$res = DB_Query($sql, $prod['id'], $prod['variant_id']);

			if ($var_data = DB_Fetch($res))
			{
				$prod_data['ean13'] = $var_data['ean13'] ? $var_data['ean13'] : $prod_data['ean13'];
				$prod_data['upc'] = $var_data['upc'] ? $var_data['upc'] : $prod_data['upc'];
				$prod_data['reference'] = $var_data['reference'] ? $var_data['reference'] : $prod_data['reference'];
			}
		}

		//pobieranie wysokosci podatku w zależności od budowy bazy
		if(isset($prod_data['id_tax']))
		{
			$tax = DB_Query("SELECT rate, id_tax FROM `${dbp}tax` WHERE id_tax = '{0}'", $prod_data['id_tax']);
			$tax = DB_Fetch($tax);
			$tax_rate = $tax['rate'];
			$id_tax = $tax['id_tax'];
		}
		elseif(isset($prod_data['id_tax_rules_group']))
		{
			$sql = "SELECT tr.id_tax_rules_group, t.id_tax, t.rate
				FROM `${dbp}tax` t
				JOIN `${dbp}tax_rule` tr ON t.id_tax = tr.id_tax
				LEFT JOIN `${dbp}country` c ON c.id_country = tr.id_country
				WHERE tr.id_tax_rules_group = '{0}'
				ORDER BY c.iso_code = '{1}' DESC LIMIT 1";

			$tax = DB_Query($sql, $prod_data['id_tax_rules_group'], $request['delivery_country_code']);
			$tax = DB_Fetch($tax);
			$tax_rate = $tax['rate'];
			$id_tax = $tax['id_tax'];
		}
		else
		{$tax_rate = $options['def_tax_rate'];}

		//obliczanie cen netto
		$final_price_netto = $prod['price'] / (1 + $tax_rate/100);

		//aktualizowanie zmiennych licząch sumy
		$sum_products_price += $prod['price'] * $prod['quantity'];
		$sum_products_price_netto += $final_price_netto * $prod['quantity'];

		//wybieranie nazwy z bazy lub nadesłanej
		if($prod_data['product_name'] == "")
		{$prod_data['product_name'] = $prod['name'];}

		//dodawanie produktu do zamowienia
		$sql = "INSERT INTO `${dbp}order_detail` (
				`id_order_detail` ,`id_order` ,`product_id` ,
				`product_attribute_id` ,`product_name` ,`product_quantity` ,
				`product_quantity_in_stock` ,`product_quantity_refunded` ,`product_quantity_return` ,
				`product_quantity_reinjected` ,`product_price` ,`product_quantity_discount` ,
				`product_ean13` ,`product_reference` ,`product_supplier_reference`, `product_upc`,
				`product_weight` ,`tax_name` ,`tax_rate`$cmo, `id_shop`, `id_warehouse`,
				`total_price_tax_incl`, `total_price_tax_excl`, `unit_price_tax_incl`, `unit_price_tax_excl`"
				. ($od_trg ? ", `id_tax_rules_group`" : '') . "$cmc
				)
				VALUES (
				NULL , '{this_order_id}', '{prod_id}',
                                '{variant_id}' , '{prod_name}', '{quantity}',
				'{qty_in_stock}', '0', '0',
				'0', '{final_price_netto}', '0.000000',
				'{ean}' , '{reference}' , NULL , '{upc}',
				'{weight}', '{tax_rate}%', '{tax_rate}'$cmo, '{shop_id}', '{id_warehouse}',
				'{total_price_tax_incl}', '{total_price_tax_excl}', '{unit_price_tax_incl}', '{unit_price_tax_excl}'"
				. ($od_trg ? ", {id_tax_rules_group}" : '') . " 
				$cmc);";
		DB_Query($sql, array(
			'this_order_id' => $this_order_id,
			'prod_id' => $prod['id'],
			'variant_id' => $prod['variant_id'],
			'prod_name' => str_replace("'","",$prod_data['product_name']),
			'quantity' => $prod['quantity'],
			'qty_in_stock' => $prod_data['quantity'],
			'final_price_netto' => $final_price_netto,
			'ean' => $prod_data['ean13'],
			'reference' => $prod_data['reference'] ? $prod_data['reference'] : $prod['sku'],
			'upc' => $prod_data['upc'],
			'weight' => $prod_data['weight'],
			'tax_rate' => $tax_rate,
			'shop_id' => $options['shop_id'],
			'id_warehouse' => $prod_data['id_warehouse'] ? $prod_data['id_warehouse'] : $options['warehouse_id'],
			'total_price_tax_incl' => $prod['price']*$prod['quantity'],
			'total_price_tax_excl' => $final_price_netto*$prod['quantity'],
			'unit_price_tax_incl' => $prod['price'],
			'unit_price_tax_excl' => $final_price_netto,
			'id_tax_rules_group' => (int)$prod_data['id_tax_rules_group'],
		));
		$this_order_products_id = DB_Identity();

		//wprowadzenie kwoty podatku do oddzielnej tabeli
		if (!empty($id_tax))
		{
			$unit_tax_amt = $prod['price']-($prod['price']/(1+$tax_rate/100));
			$sql = "INSERT INTO `${dbp}order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)
				VALUES  ('{0}', {1}, {2}, '{3}')";
			DB_Query($sql, $this_order_products_id, $id_tax, $unit_tax_amt, $unit_tax_amt*$prod['quantity']);
		}

		//dodawanie produktu do koszyka
		if ($prod['id']) // ID produktu jest częścią klucza więc nie dodajemy produktów bez przypisanego ID!
		{
			$sql = "INSERT INTO `${dbp}cart_product`
				(id_cart, id_product$cmo, id_address_delivery, id_shop$cmc, id_product_attribute, quantity, date_add)
				VALUES ({0}, {1}$cmo, {2}, '{3}'$cmc, {4}, {5}, '{6}')
				ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
			DB_Query($sql, $cart_id, $prod['id'], $delivery_address_id, $options['shop_id'], (int)$prod['variant_id'], $prod['quantity'], date('Y-m-d H:i:s'));
		}

                //zmniejszanie stanu magazynowego produktu (jeśli ustawiona flaga change_products_quantity)
                if($request['change_products_quantity'] == 1)
		{Shop_ProductsQuantityUpdate(array('products' => array(0 => array("product_id" => $prod['id'], "variant_id" => $prod['variant_id'], "operation" => "change", "quantity" => -1*$prod['quantity']))));}
	}

	$total_paid = $request['delivery_price']+$sum_products_price;

	//aktualizowanie tabeli orders
	$sql = "UPDATE `${dbp}orders` SET
		`total_paid` = '{0}',
		`total_paid_real` = '{0}',
		`total_products` = '{1}',
		`total_products_wt` = '{1}',
		`total_shipping` = '{2}'$cmo,
		`total_shipping_tax_incl` = '{2}',
		`total_shipping_tax_excl` = '{3}',
		`carrier_tax_rate` = '{4}',
		`total_paid_tax_incl` = '{0}',
		`total_paid_tax_excl` = '{5}'$cmc
		WHERE id_order = '{6}' LIMIT 1 ;";
	DB_Query($sql, $total_paid, $sum_products_price, $request['delivery_price'],
		100*$request['delivery_price']/(100+$shipping_tax),
		$shipping_tax, 100*$total_paid/(100+$shipping_tax), $this_order_id);

	//odnotowanie statusu zamówienia
	if ($request['status_id'])
	{
		Shop_OrderUpdate(array('orders_ids' => array($this_order_id), 'update_type' => 'status', 'update_value' => $request['status_id']));
	}

	$response = array("order_id" => $this_order_id);

	// dla dostawy do paczkomatu wypełniamy stosowną tabelę
	if ($cart_id and $request['delivery_point_name'] and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}paczkomatyinpost'")))
	{
		$request['delivery_point_name'] = preg_replace('/\s*paczkomat\s*/i', '', $request['delivery_point_name']);

		$sql = "INSERT INTO `${dbp}paczkomatyinpost`
			(id_cart, status, dispatch_order_id, status_date, packcode, paid,
			calculated_charge, customer_delivering_code, receiver_email,
			receiver_mobile, receiver_machine, receiver_machine_cod,
			packtype, self_send, sender_machine, reference_number, insurance,
			cod, cod_value, date_add, date_upd)
			VALUES
			({0}, 'UNDEFINED', '', '0000-00-00 00:00:00', '', 0,
			0, '', '{1}',
			'{2}', '{3}', '{4}',
			'', 0, '', '', 0,
			{5}, {6}, '{7}', '{7}')
			ON DUPLICATE KEY UPDATE receiver_machine = '{3}', receiver_machine_cod = '{4}', date_upd = '{7}'";
		DB_Query($sql, $cart_id,
			  $request['email'],
			  $request['phone'], ($payment_module == 'cashondelivery') ? '' : $request['delivery_point_name'],
			  ($payment_module == 'cashondelivery') ? $request['delivery_point_name'] : '',
			  ($payment_module == 'cashondelivery') ? 1 : 0, ($payment_module == 'cashondelivery') ? $total_paid : 0,
			  date('Y-m-d H:i:s'));
	}
	// paczka w ruchu
	elseif (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}sensbitpaczkawruchumap_cart'")) and preg_match('/(\w\w-[\d\-]+)/', $request['delivery_point_name'], $m))
	{
		$sql = "INSERT INTO `${dbp}sensbitpaczkawruchumap_cart` (id_cart, id_place, date_add, date_upd)
			VALUES ('{0}', '{1}', '{2}', '{2}')";
		DB_Query($sql, $cart_id, $m[1], date('Y-m-d H:i:s'));
	}
	elseif (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}sensbitpaczkawruchu_cart'")) and preg_match('/(\w\w-[\d\-]+)/', $request['delivery_point_name'], $m))
	{
		$sql = "INSERT INTO `${dbp}sensbitpaczkawruchu_cart` (id_cart, point, date_add, date_upd)
			VALUES ('{0}', '{1}', '{2}', '{2}')";
		DB_Query($sql, $cart_id, $m[1], date('Y-m-d H:i:s'));
	}

	return $response;
}





 /**
 * Funkcja pobiera zamówienia złożone w sklepie internetowym
 * Zwracane liczby (np ceny) powinny mieć format typu: 123456798.12 (kropka oddziela część całkowitą, 2 miejsca po przecinku)
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu, zawiera informacje o zamówieniu w formacie:
 *		time_from => czas od którego mają zastać pobrane zamówienia - format UNIX TIME
 *		id_from => ID od którego mają zastać pobrane zamówienia
 *		only_paid => flaga określająca czy pobierane mają być tylko zamówienia opłacone (0/1)
 * @return array $response tablica zawierająca dane zamówień:
 * 		id zamówienia => array:
 *						delivery_fullname, delivery_company, delivery_address, delivery_city, delivery_postcode, delivery_country => dane dotyczące adresu wysyłki
 *						invoice_fullname, invoice_company, invoice_address, invoice_city, invoice_postcode, invoice_country, invoice_nip => dane dotyczące adresu płatnika faktury
 *						phone => nr telefonu, email => adres email,
 *						date_add => data złożenia zamówienia,
 *						payment_method => nazwa metody płatności,
 *						user_comments => komentarz klienta do zamówienia,
 *						status_id => numer ID statusu zamówienia,
 *						delivery_method_id => numer ID metody wysyłki, delivery_method => nazwa metody wysyłki,
 *						delivery_price => cena wysyłki
 *						products => array:
 *									[] =>
 *										id => id produktu, variant_id => id wariantu produktu (0 jeśli produkt główny),
										name => nazwa produktu
 *										quantity => zakupiona ilość, price => cena sztuki brutto (uwzględniająca atrybuty)
 *										weight => waga produktu w kg, tax => wysokość podatku jako liczba z zakresu 0-100
 *										attributes => array: - tablica z wybieralnymi atrybutami produktów (jeśli istnieją)
 *														[] =>
 *															name => nazwa atrybutu (np 'kolor'),
 *															value => wartość atrubutu (np 'czerwony'),
 *															price => różnica w cenie w stosunku do ceny standardowe
 */
function Shop_OrdersGet($request)
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	$response = array();

	//formatowanie daty od której mają być pobrane zamówienia
	$time_from = date("Y-m-d H:i:s", (int)$request['time_from']);

	//sklep obsługuje paczkomaty?
	$owp_field = $owp_table = ''; // domyślnie nie obsługuje

	// dodatkowe tabele określające dokument sprzedaży
	$pdinvoicebillpro = DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}pdinvoicebillpro'"));
	$x13recieptorinvoice = DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}x13recieptorinvoice'")); // SIC!

	// moduł tw_paragonfaktura
	$tw_paragonfaktura = DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}tw_paragonfaktura'"));

	// invoice_or_bill
	$invoice_or_bill = DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}invoice_or_bill'"));

	if (ActiveModules('sensbitpaczkomatymap') and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}sensbitpaczkomatymap_cart'")))
	{
		$owp_field = ", sbic.parcel_locker_name delivery_point_name, sbip.address sensbitinpost_address";
		$owp_table = " LEFT JOIN `${dbp}sensbitpaczkomatymap_cart` sbic ON sbic.id_cart = o.id_cart AND ca.name LIKE '%paczkom%'LEFT JOIN `${dbp}sensbitpaczkomatymap_point` sbip ON sbip.name = sbic.parcel_locker_name";
	}
	elseif (ActiveModules('sensbitinpost') and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}sensbitinpost_cart'")))
	{
		$owp_field = ", sbic.parcel_locker_name delivery_point_name, sbip.address sensbitinpost_address";
		$owp_table = " LEFT JOIN `${dbp}sensbitinpost_cart` sbic ON sbic.id_cart = o.id_cart LEFT JOIN `${dbp}sensbitinpost_point` sbip ON sbip.name = sbic.parcel_locker_name";
	}
	elseif (ActiveModules('inpost') and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}inpost_parcel_lockers_orders'")))
	{
		$owp_field = ", pa.parcel_locker AS delivery_point_name, pa.phone_no phone3";
		$owp_table = " LEFT JOIN `${dbp}inpost_parcel_lockers_orders` pa ON pa.order_id = o.id_order";
	}
        elseif (ActiveModules('paczkomatyinpost') and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}paczkomatyinpost'")))
	{
		$owp_field = ", if(pa.receiver_machine_cod <> '', pa.receiver_machine_cod, pa.receiver_machine) AS delivery_point_name";
		$owp_table = " LEFT JOIN `${dbp}paczkomatyinpost` pa ON pa.id_cart = o.id_cart";
	}
        elseif (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}pakomato_orders'")))
	{
		$owp_field = ", pa.paczkomat AS delivery_point_name";
		$owp_table = " LEFT JOIN `${dbp}pakomato_orders` pa ON pa.id_order = o.id_order";
	}
        elseif (ActiveModules('pminpostpaczkomaty') and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}pminpostpaczkomatylist'")))
	{
		$owp_field = ", pa.machine AS delivery_point_name";
		// tabela może zawierać pole id_cart, id_order lub obydwa równolegle
		$res = DB_Query("SHOW FIELDS FROM `${dbp}pminpostpaczkomatylist` LIKE 'id_%'");
		$fields = array();

		if (DB_NumRows($res))
		{
			while ($row = DB_Fetch($res))
			{
				if (!preg_match('/order|cart/', $row['Field']))
				{
					continue;
				}

				// w przypadku kiedy tabela zawiera tylko id_order,
				// najprawdopodobniej odnosi się on do id koszyka w zamówieniu :)
				$fields[] = "pa.${row['Field']} = o." . ((DB_NumRows($res) == 1) ? 'id_cart' : $row['Field']);
			}

			$owp_table = " LEFT JOIN `${dbp}pminpostpaczkomatylist` pa ON " . implode(' OR ', $fields);
		}
	}
        elseif (ActiveModules('inpostpaczkomaty') and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}inpostpaczkomatylist'")))
	{
		$owp_field = ", pa.machine AS delivery_point_name, pa.address inpostpaczkomatylist_address";
		// tabela może zawierać pole id_cart, id_order lub obydwa równolegle
		$res = DB_Query("SHOW FIELDS FROM `${dbp}inpostpaczkomatylist` LIKE 'id_%'");
		$fields = array();

		if (DB_NumRows($res))
		{
			while ($row = DB_Fetch($res))
			{
				// w przypadku kiedy tabela zawiera tylko id_order,
				// najprawdopodobniej odnosi się on do id koszyka w zamówieniu :)
				$fields[] = "pa.${row['Field']} = o." . ((DB_NumRows($res) == 1) ? 'id_cart' : $row['Field']);
			}

			$owp_table = " LEFT JOIN `${dbp}inpostpaczkomatylist` pa ON " . implode(' OR ', $fields);
		}
	}

	// wysyłka na adres UP
        if (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}owppoczta_orders'")))
	{
		$owp_field .= ", owp.pni";
		$owp_table .= " LEFT JOIN `${dbp}owppoczta_orders` owp ON o.id_cart = owp.id_cart";
	}

	
        $check_phenadawca = DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}phenadawca'"));
        $check_az_e_pp = DB_NumRows(DB_Query("SHOW TABLES LIKE 'az_e_pp'"));

	// paczka w ruchu
	if (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}sensbitpaczkawruchumap_cart'")))
	{
		$owp_field .= ", spwrmc.id_place AS paczka_w_ruchu2";
		$owp_table .= " LEFT JOIN `${dbp}sensbitpaczkawruchumap_cart` spwrmc ON o.id_cart = spwrmc.id_cart";
	}
        elseif (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}paczkawruchu'")))
	{
		if (DB_NumRows(DB_Query("SHOW COLUMNS FROM `${dbp}paczkawruchu` LIKE 'id_order'")))
		{
			$owp_field .= ", pwr.poper AS paczka_w_ruchu";
			$owp_table .= " LEFT JOIN `${dbp}paczkawruchu` pwr ON o.id_order = pwr.id_order";

			// kiedy tabela obsługuje obydwa pola, jedno z nich może zawierać puste wartości
			if (DB_NumRows(DB_Query("SHOW COLUMNS FROM `${dbp}paczkawruchu` LIKE 'id_cart'")))
			{
				$owp_table .= " OR o.id_cart = pwr.id_cart";
			}
		}
		elseif (DB_NumRows(DB_Query("SHOW COLUMNS FROM `${dbp}paczkawruchu` LIKE 'id_cart'")))
		{
			$owp_field .= ", pwr.poper AS paczka_w_ruchu";
			$owp_table .= " LEFT JOIN `${dbp}paczkawruchu` pwr ON o.id_cart = pwr.id_cart";
		}
	}
	elseif (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}sensbitpaczkawruchu_cart'")))
	{
		$owp_field .= ", spwrc.point AS paczka_w_ruchu";
		$owp_table .= " LEFT JOIN `${dbp}sensbitpaczkawruchu_cart` spwrc ON o.id_cart = spwrc.id_cart";
	}

	// płatności PayU
	if (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}order_payu_payments'")))
	{
		$payu_field = ", if(payu.status = 'COMPLETED', 1, 0) paid";
		$payu_table = " LEFT JOIN `${dbp}order_payu_payments` payu ON o.id_order = payu.id_order";
	}
	elseif (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}prestacafepayu_transactions'")))
	{
		$payu_field = ", if(payu.transaction_total >= 0.01 AND payu.transaction_real_total = payu.transaction_total AND payu.transaction_status = 99, 1, 0) paid";
		$payu_table = " LEFT JOIN `${dbp}prestacafepayu_transactions` payu ON o.id_order = payu.id_order";
	}

	// płatności PayPal
	if (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}paypal_order'")))
	{
		if (DB_NumRows(DB_Query("SHOW FIELDS FROM `${dbp}paypal_order` LIKE 'total_paid'")))
		{
			$paypal_field = ", if(paypal.payment_status = 'Completed' AND paypal.total_paid > 0, 1, 0) paid_pp";
			$paypal_table = " LEFT JOIN `${dbp}paypal_order` paypal ON o.id_order = paypal.id_order";
		}
	}

	//zapytanie pobierające zamówienia od określonego czasu
	$sql = "SELECT o.*, c.email, ca.name delivery_method, UNIX_TIMESTAMP(o.date_add) time_purchased,
		a1.firstname delivery_firstname, a1.lastname delivery_lastname,
		a1.company delivery_company, a1.address1 delivery_address1, a1.address2 delivery_address2,
		a1.city delivery_city, a1.postcode delivery_postcode, cl1.name delivery_country,
		c1.iso_code delivery_country_code,
		if(isnull(s1.iso_code), s1.name, s1.iso_code) delivery_state,
		a1.phone phone, a1.phone_mobile phone_mobile, cu.iso_code currency,
		a2.firstname invoice_firstname, a2.lastname invoice_lastname, ca.external_module_name,
		a2.company invoice_company, a2.address1 invoice_address1, a2.address2 invoice_address2,
		c2.iso_code invoice_country_code,
		a2.city invoice_city, a2.postcode invoice_postcode, cl2.name invoice_country,
		if(isnull(s2.iso_code), s2.name, s2.iso_code) invoice_state,
		a1.other comment1, a2.other comment2, a2.dni,
		a2.phone phone2, a2.phone_mobile phone_mobile2, a2.vat_number $owp_field $payu_field $paypal_field
		FROM `${dbp}orders` o
		LEFT JOIN `${dbp}currency` cu ON cu.id_currency = o.id_currency
		LEFT JOIN `${dbp}address` a1 ON a1.id_address = o.id_address_delivery
		LEFT JOIN `${dbp}state` s1 ON s1.id_state = a1.id_state AND s1.id_country = a1.id_country AND s1.active
		LEFT JOIN `${dbp}country` c1 ON c1.id_country = a1.id_country
		LEFT JOIN `${dbp}country_lang` cl1 ON cl1.id_country = a1.id_country AND cl1.id_lang = '{0}'
		LEFT JOIN `${dbp}address` a2 ON a2.id_address = o.id_address_invoice
		LEFT JOIN `${dbp}state` s2 ON s2.id_state = a2.id_state AND s2.id_country = a2.id_country AND s2.active
		LEFT JOIN `${dbp}country` c2 ON c2.id_country = a2.id_country
		LEFT JOIN `${dbp}country_lang` cl2 ON cl2.id_country = a2.id_country AND cl2.id_lang = '{0}'
		LEFT JOIN `${dbp}customer` c ON c.id_customer = o.id_customer
		LEFT JOIN `${dbp}carrier` ca ON ca.id_carrier = o.id_carrier $owp_table $payu_table $paypal_table
		WHERE
		o.date_add < date_sub('".date('Y-m-d H:i:s')."', interval 2 minute) AND o.date_add > '{1}'
		".(($request['id_from'] != "")?" AND o.id_order > '{2}'":"")."
		";

	if($options['shop_id']!=""){$sql.=" AND o.id_shop = '${options['shop_id']}'";}

	$res = DB_Query($sql, $options['lang_id'], $time_from, $request['id_from']);

	while($order = DB_Fetch($res))
	{
		$o = array();
		$o['delivery_fullname'] = $order['delivery_firstname']." ".$order['delivery_lastname'];
		$o['delivery_company'] = $order['delivery_company'];
		$o['delivery_address'] = $order['delivery_address1']." ".$order['delivery_address2'];
		$o['delivery_city'] = $order['delivery_city'];
		$o['delivery_postcode'] = $order['delivery_postcode'];
		$o['delivery_country'] = $order['delivery_country'];
		$o['delivery_country_code'] = $order['delivery_country_code'];
		$o['delivery_state_code'] = $order['delivery_state'];
		$o['invoice_fullname'] = $order['invoice_firstname']." ".$order['invoice_lastname'];
		$o['invoice_company'] = $order['invoice_company'];
		$o['invoice_address'] = $order['invoice_address1']." ".$order['invoice_address2'];
		$o['invoice_city'] = $order['invoice_city'];
		$o['invoice_postcode'] = $order['invoice_postcode'];
		$o['invoice_country'] = $order['invoice_country'];
		$o['invoice_country_code'] = $order['invoice_country_code'];
		$o['invoice_state_code'] = $order['invoice_state'];
		$o['invoice_nip'] = $order['vat_number'] ? $order['vat_number'] : $order['dni'];
		$o['payment_method_cod'] = 0;

		// waluta zamówienia
		$o['currency'] = $order['currency'] ? $order['currency'] : 'PLN';

		// zaokrąglanie cen
		$decimals = 2; // domyślnie do 2 miejsc po przecinku

		$sql = "SELECT co.value, cu.*
			FROM `${dbp}configuration` co
			LEFT JOIN `${dbp}currency` cu ON cu.iso_code = '{0}' AND active = 1
			WHERE co.name = 'PS_PRICE_DISPLAY_PRECISION'";
		$result = DB_Query($sql, $o['currency']);

		if ($cur = DB_Fetch($result))
		{
			$decimals = (!isset($cur['decimals']) or $cur['decimals'] == 1) ? $cur['value'] : 0;
		}

		if ($order['round_mode'] == '0')
		{
			$rnd_fn = 'ceil';
		}
		elseif ($order['round_mode'] == '1')
		{
			$rnd_fn = 'floor';
		}
		else
		{
			$rnd_fn = 'round';
		}

		//czy zamówienie opłacone
		$order['paid'] = ($order['paid'] or $order['paid_pp']) ? 1 : 0;

		if (!($o['paid'] = (int)$order['paid']) and DB_NumRows(DB_Query("SHOW COLUMNS FROM `${dbp}order_state` LIKE 'paid'")))
		{
			$sql = "SELECT count(*)
				FROM `${dbp}order_history` oh
				LEFT JOIN `${dbp}order_state` os ON os.id_order_state = oh.id_order_state
				WHERE id_order = '{0}' AND paid = 1";
			$result = DB_Query($sql, $order['id_order']);

			if ($result and DB_NumRows($result))
			{
				$o['paid'] = DB_Result($result) ? 1 : 0;
			}

		}

		if ($order['module'] == 'przelewy24' and $order['total_paid_real'] >= 0.01)
		{
//			$o['paid'] = 1;
		}

		// wyjątek dla płatności za pobraniem
		if (strpos($order['module'], 'cashondelivery') !== false or preg_match('/pobran|przy odb/i', $order['payment']))
		{
			$o['paid'] = 0;
			$o['payment_method_cod'] = 1;
		}

		//pomijanie zamówienia jeśli w żadanie wyłącznie opłaconych zamówień
		if($request['only_paid'] == 1 && $o['paid'] != 1)
		{continue;}

		// czy klient chce fakturę
		if ($o['invoice_nip'] != '')
		{
			$invoice_nip = preg_replace('/\D/', '', $o['invoice_nip']);

			if (strlen($invoice_nip >= 10))
			{
				$o['want_invoice'] = 1;
			}
		}

		if (!$o['want_invoice'] and $pdinvoicebillpro)
		{
			$o['want_invoice'] = (int)DB_Result(DB_Query("SELECT chosen FROM `${dbp}pdinvoicebillpro` WHERE id_cart = '{0}'", $order['id_cart']));
		}

		if (!$o['want_invoice'] and $x13recieptorinvoice)
		{
			$o['want_invoice'] = (int)DB_Result(DB_Query("SELECT if(recieptorinvoice = 'invoice', 1, 0) FROM `${dbp}x13recieptorinvoice` WHERE id_cart = '{0}'", $order['id_cart']));
		}

		if (!$o['want_invoice'] and $tw_paragonfaktura)
		{
			$o['want_invoice'] = (int)DB_Result(DB_Query("SELECT if(document_type = 'Invoice', 1, 0) FROM `${dbp}tw_paragonfaktura` WHERE id_cart = '{0}'", $order['id_cart']));
		}

		if (!$o['want_invoice'] and $invoice_or_bill)
		{
			$o['want_invoice'] = (int)DB_Result(DB_Query("SELECT if(invoice_or_bill = 'Invoice', 1, 0) FROM `${dbp}invoice_or_bill` WHERE id_cart = '{0}' OR id_order = '{1}'", $order['id_cart'], $order['id_order']));
		}

		if(trim($order['phone_mobile']) != ""){$o['phone'] = $order['phone_mobile'];}
		elseif(trim($order['phone']) != ""){$o['phone'] = $order['phone'];}
		elseif(trim($order['phone_mobile2']) != ""){$o['phone'] = $order['phone_mobile2'];}
		elseif(trim($order['phone2']) != ""){$o['phone'] = $order['phone2'];}
		elseif(trim($order['phone3']) != ""){$o['phone'] = $order['phone3'];}

		$o['email'] = $order['email'];
		$o['date_add'] = $order['time_purchased'];
		$o['payment_method'] = str_replace(":","",$order['payment']);

		//komentarz do zamówienia
		$sql = "SELECT message FROM `${dbp}message` WHERE `id_order`= '${order['id_order']}' AND id_employee = '0' AND private = 0 ORDER BY date_add ASC LIMIT 1";
		$result = DB_Query($sql);
		if(DB_NumRows($result) > 0)
		{$o['user_comments'] = DB_Result($result, 0);}

		// czasem komentarz jest doklejony do adresu
		$o['user_comments'] .= "\n${order['comment1']}";

		if ($order['comment2'] != $order['comment1'])
		{
			$o['user_comments'] .= "\n${order['comment2']}";
		}

		$o['user_comments'] = trim($o['user_comments']);

		//status zamówienia
		$o['status_id'] = $order['current_state'];

		//sposób wysyłki
		$o['delivery_method_id'] = $order['id_carrier'];
		$o['delivery_method'] = $order['delivery_method'];
		$o['delivery_price'] = $rnd_fn($order['total_shipping']*(pow(10, $decimals)))/pow(10, $decimals);

		//paczkomat
		if (!empty($order['delivery_point_name']))
		{
			// wartość może być zakodowana base64 ...
			if (($decoded = base64_decode($order['delivery_point_name'], true)) !== false and (preg_match('/^\w+$/', $decoded) or preg_match('/^a:\d+/', $decoded)))
			{
				if (json_encode($decoded) !== false) // (rozkodowanie nie wygenerowało binarnych śmieci)
				{
					$order['delivery_point_name'] = $decoded;
				}
			}

			if ($unserialized = @unserialize($order['delivery_point_name'])) // ... i zawierać zaserializowaną tablicę
			{
				$order['delivery_point_name'] = $unserialized['name'];
			}

			$o['delivery_point_name'] = $order['delivery_point_name'];

			if ($order['sensbitinpost_address'])
			{
				if (preg_match('/^(.+?), (\d\d-\d{3}) (.+)$/', $order['sensbitinpost_address'], $m))
				{
					$o['delivery_point_address'] = $m[1];
					$o['delivery_point_postcode'] = $m[2];
					$o['delivery_point_city'] = $m[3];
				}
			}
		}
		elseif (($order['external_module_name'] or preg_match('/dhl.+?parcelshop/i', $o['delivery_method'])) and !$order['paczka_w_ruchu']) // paczkomat wg nazwy zwenętrznego modułu - oddzielny lookup
		{
			$sql = '';

			if ($order['external_module_name'] == 'inpost' and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}inpost_parcel_lockers_orders'")))
			{
				$sql = "SELECT parcel_locker FROM `${dbp}inpost_parcel_lockers_orders` WHERE order_id = '{0}' OR cart_id = '{1}' LIMIT 1";
			}
        		elseif ($order['external_module_name'] == 'paczkomatyinpost' and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}paczkomatyinpost'")))
			{
				$sql = "SELECT if(pa.receiver_machine_cod <> '', pa.receiver_machine_cod, pa.receiver_machine) FROM `${dbp}inpost_parcel_lockers_orders` WHERE order_id = '{0}' OR cart_id = '{1}' LIMIT 1";
			}
        		elseif ($order['external_module_name'] == 'pminpostpaczkomaty' and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}pminpostpaczkomatylist'")))
			{
				$sql = "SELECT machine FROM `${dbp}pminpostpaczkomatylist` WHERE id_order = '{0}' OR id_cart = '{1}' LIMIT 1";
			}
        		elseif ($order['external_module_name'] == 'inpostpaczkomaty' and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}inpostpaczkomatylist'")))
			{
				$sql = "SELECT machine FROM `${dbp}inpostpaczkomatylist` WHERE id_order = '{0}' OR id_cart = '{1}' LIMIT 1";
			}
			// numer ParcelShopu DHL
			elseif (($order['external_module_name'] == 'dhlassistant' or preg_match('/dhl.+?parcelshop/i', $o['delivery_method'])) and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}dhla_shipment'")))
			{
				$sql = "SELECT ParcelIdent FROM `${dbp}dhla_shipment` WHERE idSource = '{0}'
					UNION SELECT ParcelIdent FROM `${dbp}dhla_sourceorderadditionalparams` WHERE idSourceObject = '{1}'"; 
			}

			if ($sql)
			{
				$o['delivery_point_name'] = DB_Result(DB_Query($sql, $order['id_order'], $order['id_cart']));
			}
		}

		// moduł Globkurier
		if (!$o['delivery_point_name'] and preg_match('/inpost|paczk/i', $o['delivery_method']) and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}gk_terminal_pickup'")))
		{
				$o['delivery_point_name'] = DB_Result(DB_Query("SELECT code FROM `${dbp}gk_terminal_pickup` WHERE cart_id = '{0}' AND type = 'inpost'", $order['id_cart']));
		}
		// moduł FusionInpost
		if (!$o['delivery_point_name'] and ActiveModules('fusioninpost') and preg_match('/inpost|paczk/i', $o['delivery_method']) and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}fusioninpost'")))
		{
				$o['delivery_point_name'] = DB_Result(DB_Query("SELECT receiver_machine FROM `${dbp}fusioninpost` WHERE id_cart = '{0}' ORDER BY dispatch_order_id = '{1}' DESC", $order['id_cart'], $order['id_order']));
		}

		if ($order['pni']) // wysyłka na adres UP
		{
			$sql = "SELECT nazwa_placowki, ulica, miejscowosc, kod_pocztowy FROM `${dbp}placowki_poczty` WHERE PNI = ${order['pni']}";
			$result = DB_Query($sql);

			if ($up = DB_Fetch($result))
			{
				$o['delivery_point_name'] = $order['pni']; // $up['nazwa_placowki'];
				$o['delivery_point_address'] = $up['ulica'];
				$o['delivery_point_city'] = $up['miejscowosc'];
				$o['delivery_point_postcode'] = $up['kod_pocztowy'];
			}
		}
		elseif (!$o['delivery_point_name'] and $order['paczka_w_ruchu'])
		{
			$o['delivery_point_name'] = $order['paczka_w_ruchu'];
		}
		elseif (!$o['delivery_point_name'] and $order['paczka_w_ruchu2'])
		{
			$o['delivery_point_name'] = $order['paczka_w_ruchu2'];

			if (!isset($has_sensbit_pwr_point_list))
			{
				$addr_table = "${dbp}sensbitpaczkawruchu_point";

				if (!($has_sensbit_pwr_point_list =  DB_NumRows(DB_Query("SHOW TABLES LIKE '$addr_table'"))))
				{
					$addr_table = "${dbp}sensbitpaczkawruchumap_place";
					$has_sensbit_pwr_point_list =  DB_NumRows(DB_Query("SHOW TABLES LIKE '$addr_table'"));
				}
			}

			// dopełnienie adresu placówki
			if ($has_sensbit_pwr_point_list)
			{
				$sql = "SELECT * FROM `$addr_table` WHERE name = '{0}'";

				if ($addr = DB_Fetch(DB_Query($sql, $o['delivery_point_name'])))
				{
					if ($addr['id_point'] and preg_match('/^\d{6}$/', $addr['id_point']))
					{
						$o['delivery_point_name'] = $addr['id_point'];
						$o['delivery_point_address'] = $addr['name'];
					}
					elseif ($addr['id_place'] and preg_match('/^\d{6}$/', $addr['id_place']))
					{
						$o['delivery_point_name'] = $addr['id_place'];
						$o['delivery_point_address'] = $addr['name'];
					}

					$addr = explode(', ', $addr['address']);

					if (count($addr) > 1)
					{
						$o['delivery_point_city'] = array_pop($addr);
					}

					$o['delivery_point_address']  = ($o['delivery_point_address'] ? "${o['delivery_point_address']}, " : '') . implode(', ', $addr);
				}
			}
		}
	
		// moduł Poczta Polska - Odbiór w Punkcie v. 1.0.0 sens bit 	
		if (!$o['delivery_point_name'] and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}sensbitpocztapolskamap_cart'")))
		{
			$sql = "SELECT name, address
				FROM `${dbp}sensbitpocztapolskamap_cart` sppc
				JOIN `${dbp}sensbitpocztapolskamap_place` sppp ON sppc.id_place = sppp.id_place
				WHERE id_cart = {0}";
			$result = DB_Query($sql, $order['id_cart']);

			if ($dp = DB_Fetch($result))
			{
				$o['delivery_point_name'] = $dp['name'];
				$o['delivery_point_address'] = $dp['address'];

				if (preg_match('/^(.+?),\s(\d\d-\d{3})\s([^,]+)$/', $dp['address'], $m))
				{
					$o['delivery_point_address'] = $m[1];
					$o['delivery_point_postcode'] = $m[2];
					$o['delivery_point_city'] = $m[3];
				}
			}
		}
		elseif (!$o['delivery_point_name'] and DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}sensbitpocztapolska_cart'")))
		{
			$sql = "SELECT name, address
				FROM `${dbp}sensbitpocztapolska_cart` sppc
				JOIN `${dbp}sensbitpocztapolska_place` sppp ON sppc.id_place = sppp.id_place
				WHERE id_cart = {0}";
			$result = DB_Query($sql, $order['id_cart']);

			if ($dp = DB_Fetch($result))
			{
				$o['delivery_point_name'] = $dp['name'];
				$o['delivery_point_address'] = $dp['address'];

				if (preg_match('/^(.+?),\s(\d\d-\d{3})\s([^,]+)$/', $dp['address'], $m))
				{
					$o['delivery_point_address'] = $m[1];
					$o['delivery_point_postcode'] = $m[2];
					$o['delivery_point_city'] = $m[3];
				}
			}
		}

		//produkty zamówienia
		$o['products'] = array();

		if (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}order_detail_tax'")))
		{
			$sql = "SELECT od.*, t.rate AS tax_rate2 FROM `${dbp}order_detail` od
				LEFT JOIN `${dbp}order_detail_tax` odt ON od.id_order_detail = odt.id_order_detail
				LEFT JOIN `${dbp}tax` t ON t.id_tax = odt.id_tax
				WHERE `id_order`='${order['id_order']}'";
		}
		else
		{
			$sql = "SELECT * FROM `${dbp}order_detail` WHERE `id_order`='${order['id_order']}'";
		}

		// dedykacje, itp.
		if (!$options['compat_mode'])
		{
			$sql = str_replace(
				"FROM `${dbp}order_detail` od", ", cd.value customization
				FROM `${dbp}order_detail` od
				LEFT JOIN `${dbp}customization` cs ON cs.id_cart = '{0}' AND cs.id_product = od.product_id AND cs.id_product_attribute = od.product_attribute_id AND cs.in_cart = 1
				LEFT JOIN `${dbp}customized_data` cd ON cd.id_customization = cs.id_customization",
				$sql);
		}

		$result = DB_Query($sql, $order['id_cart']);
		$variable_tax = false;  // różny podatek dla różnych produktów
		$total_products = 0; // suma produktów zamówienia

		// odbiór w punkcie - wysyłka przez enadawca
		if (!$o['delivery_point_name'] and $check_phenadawca)
		{
			if (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}phenadawca_place'")))
			{
				$sql = "SELECT kod, miejscowosc, ulica, e.id_place
					FROM `${dbp}phenadawca` e
					LEFT JOIN `${dbp}phenadawca_place` ep
					ON e.id_place = ep.id_place
					WHERE e.id_order = '{0}'
					UNION
					SELECT kod, miejscowosc, ulica, ec.id_place
					FROM `${dbp}phenadawca_cart` ec
					LEFT JOIN `${dbp}phenadawca_place` ep
					ON ec.id_place = ep.id_place
					WHERE ec.id_cart = '{1}'";

				if ($place = DB_Fetch(DB_Query($sql, $order['id_order'], $order['id_cart'])))
				{
					$o['delivery_point_name'] = $place['id_place'];
					$o['delivery_point_address'] = $place['ulica'];
					$o['delivery_point_postcode'] = $place['kod'];
					$o['delivery_point_city'] = $place['miejscowosc'];
				}
			}
		}

		if (!$o['delivery_point_name'] and $check_az_e_pp)
		{
			$sql = "SELECT * FROM `az_e_pp` WHERE id_cart = '{0}'";

			if ($up = DB_Fetch(DB_Query($sql, $order['id_cart'])))
			{
				$o['delivery_point_name'] = $up['pni'];
				$o['delivery_point_address'] = $up['name'] . ' ' . $up['street'];
				$o['delivery_point_postcode'] = $up['zipcode'];
				$o['delivery_point_city'] = $up['city'];
			}
		}

		// uzupełnienie adresu punktu odbioru
		if ($o['delivery_point_name'] and !isset($o['delivery_point_address']))
		{
			if (isset($order['inpostpaczkomatylist_address']) and preg_match('/^([A-Z0-9]+);([^;]+);([^;]+);([^;]+)/', $order['inpostpaczkomatylist_address'], $m))
			{
				$o['delivery_point_address'] = trim($m[3] . ' ' . $m[4]);
				$o['delivery_point_city'] = $m[2];
			}
		}

		$prev_id_order_detail = 0;

		while($product = DB_Fetch($result))
		{
			$op = array();
			$op['id'] = $product['product_id'];
			$op['variant_id'] = (int)$product['product_attribute_id'];
			$op['name'] = $product['product_name'];
			$op['quantity'] = $product['product_quantity'];
			$op['ean'] = $product['product_ean13'];
			$op['sku'] = $product['product_reference'];

			//jeśli stawka VAT jest zapisana w oddzielnej tabeli, pobierz jej wartość
			$tax_rate = ($product['tax_rate'] >= 0.001) ? $product['tax_rate'] : (float)$product['tax_rate2'];

			if(isset($product['unit_price_tax_incl']))
			{$op['price'] = $product['unit_price_tax_incl'];}
			else
			{$op['price'] = number_format($product['product_price'] * (1+$tax_rate/100), 2, ".", "");}

			$op['attributes'] = array();
/*
			//naliczanie obniżek (procentowych bądź kwotowych)
			if (isset($product['reduction_percent']) and $product['reduction_percent'] >= 0.00001)
			{$op['price'] -= $op['price']*$product['reduction_percent']/100;}
			elseif (isset($product['reduction_amount']) and $product['reduction_amount'] >= 0.00001)
			{$op['price'] -= $product['reduction_amount'];}
*/

			//formatowanie ceny
			$op['price'] = $rnd_fn($op['price']*(pow(10, $decimals)))/pow(10, $decimals);

			$op['tax'] = $tax_rate;
			$op['weight'] = $product['product_weight'];

			if ($product['customization'])
			{
				if ($product['cust_type'] == 0) // upload
				{
			//		$product['customization'] = preg_replace('/img\/$/', 'upload/', $options['images_folder']) . $product['customization'];
				}

				$op['attributes'][] = array('name' => '', 'value' => $product['customization'], 'price' => 0);
			}

			if ($prev_id_order_detail == $product['id_order_detail']) // kolejne pole custome
			{
				// przerzucamy tylko atrybuty
				$o['products'][count($o['products'])-1]['attributes'] = array_merge($o['products'][count($o['products'])-1]['attributes'], $op['attributes']);
				continue;
			}

			$o['products'][] = $op;
			$prev_id_order_detail = $product['id_order_detail'];

			if ($variable_tax !== false and $variable_tax !== true and $varialble_tax != $op['tax'])
			{
				$op['variable_tax'] = true;
			}
			elseif ($variable_tax !== true)
			{
				$variable_tax = $op['tax'];
			}

			$total_products += $op['quantity']*$op['price'];
		}

		// suma rabatów - rozłożona na poszczególne pozycje zamówienia
		if ($options['split_discounts'] and isset($order['total_discounts_tax_incl']) and $order['total_discounts_tax_incl'] >= 0.01)
		{
			// współczynnik skalowania cen
			$price_scale_factor = (1-$order['total_discounts_tax_incl']/$total_products);
			$new_total_products = 0;

			// obniżenie cen w zamówieniu
			foreach ($o['products'] as $i => $op)
			{
				$op['price'] = number_format($price_scale_factor*$op['price'], 2, '.', '');
				$o['products'][$i] = $op;
				$new_total_products += $op['price']*$op['quantity'];
			}

			// na skutek błędów zaokrąglania pozostała reszta, którą trzeba doliczyć do któregoś produktu
			if ($total_diff = $total_products - $new_total_products - $order['total_discounts_tax_incl'] >= 0.01)
			{
				$diff_spilt = false; // czy reszta została już rozdzielona
				$min_qty_prod_id = $o['products'][0]['id']; // id produktu z najmniejszą ilością zakupionych sztuk

				foreach ($o['products'] as $i => $op)
				{
					// .. najlepiej do takiego, który zakupiony został w małej liczbie sztuk
					// lub liczbie sztuk pozwalającej na równe rozdzielenie reszty
					if ($op['quantity'] == 1 or !(round($total_diff*100)%$op['quantity']))
					{
						$o['products'][$i]['price'] += $total_diff/$op['quantity'];
						$diff_split = true;
						break;
					}

					if ($op['quantity'] < $o['products'][$min_qty_prod_id]['quantity'])
					{
						$min_qty_prod_id = $op['id'];
					}
				}

				// jeśli powyższe się nie uda, doklejamy różnicę do produktu z najmnieszą liczbą sztuk
				if (!$diff_split)
				{
					$o['products'][$min_qty_prod_id]['price'] += $total_diff/$op['quantity'];
				}
				
			}
		}
		//suma rabatów - dodajemy jako oddzielny produkt
		elseif (isset($order['total_discounts_tax_incl']) and $order['total_discounts_tax_incl'] >= 0.01)
		{
			$o['products'][] = array(
				'name' => 'Rabat',
				'quantity' => 1,
				'price' => $rnd_fn(-$order['total_discounts_tax_incl']*(pow(10, $decimals)))/pow(10, $decimals),
				'tax' => ($variable_tax === true) ? (($order['total_discounts_tax_excl'] >= 0.01) ? round(($order['total_discounts_tax_incl']/$order['total_discounts_tax_excl']-1)*100) : 0) : $op['tax']
			);
		}

		// cashback
		if (isset($order['cash_back']) and $order['cash_back'] >= 0.01)
		{
			$o['products'][] = array(
				'name' => 'Cashback',
				'quantity' => 1,
				'price' => $rnd_fn(-$order['cash_back']*(pow(10, $decimals)))/pow(10, $decimals),
				'tax' => 0,
			);
		}

		// koszty opakowania
		if ($order['total_wrapping_tax_incl'] and $order['total_wrapping_tax_incl'] > 0)
		{
			$o['products'][] = array(
				'name' => 'opakowanie',
				'quantity' => 1,
				'price' => $rnd_fn($order['total_wrapping_tax_incl']*(pow(10, $decimals)))/pow(10, $decimals),
				'tax' => $op['tax'] ? $op['tax'] : 0,
			);
		}

		// koszty płatności
		if (DB_NumRows(DB_Query("SHOW TABLES LIKE '${dbp}prestacafetransactionfees'")))
		{
			$sql = "SELECT fee FROM `${dbp}prestacafetransactionfees` WHERE id_order = '{0}'";

			if ($fee = DB_Result(DB_Query($sql, $order['id_order'])))
			{
				$o['products'][] = array(
					'name' => 'koszt płatności',
					'quantity' => 1,
					'price' => $fee,
					'tax' => $options['def_tax_rate'],
				);
			}
		}

		$response[$order['id_order']] = $o;
	}

	return $response;
}


 /**
 * Funkcja aktualizuje zamówienia wcześniej dodane do bazy
 * W przypadku zapisywania numeru nadawaczego, parametr orders_ids będzie przyjmował zawsze tablicę z jedną pozycją
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu, zawiera informacje o aktualizacji zamówienia w formacie:
 *		orders_ids => ID zamówień
 *		update_type => typ zmiany - 'status', `delivery_number`, lub 'paid' (aktualizacja statusu zamówienia, dodanie numeru nadawczego i dodanie/usunięcie wpłaty)
 *		update_value => aktualizowana wartość - ID statusu, numer nadawczy lub informacja o opłaceniu zamówienia (bool true/false)
 * @return array $response tablica zawierająca potwierdzenie zmiany:
 * 		'counter' => ilość zamówień w których dokonano zmiany (nawet jeśli zamówienie pozostało takie jak wcześniej)
 */
function Shop_OrderUpdate($request)
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	$counter = 0;

        //dla wszystkich nadesłanych numerów zamówień
        foreach($request['orders_ids'] as $order_id)
        {
	        if (intval($order_id) != $order_id)
	        {continue;}

	        //zmiana statusu
	        if($request['update_type'] == 'status')
	        {
			// sprawdzamy, czy status zamówienia pociąga za sobą zmianę statusu płatności
			$sql = "SELECT total_paid_real, os.paid, valid, current_state, osl.name status_name
				" . ($options['stock_mvt'] ? ", os.shipped about_to_ship, osc.shipped already_shipped" : '') . " 
				FROM `${dbp}orders` o
				LEFT JOIN `${dbp}order_state` os ON os.id_order_state = '{0}'
				LEFT JOIN `${dbp}order_state_lang` osl ON osl.id_order_state = os.id_order_state AND osl.id_lang = '{2}'
				" . ($options['stock_mvt'] ? "LEFT JOIN `${dbp}order_state` osc ON osc.id_order_state = o.current_state" : '') . " 
				WHERE o.id_order = {1}";
			$res = $options['compat_mode'] ? false : DB_Query($sql, $request['update_value'], $order_id, $options['lang_id']);

			if (!$options['compat_mode'] and DB_NumRows($res))
			{
				$o = DB_Fetch($res);

				// nastąpiła zmiana statusu
				if ($o['current_state'] != $request['update_value'])
				{
					$sql = "SELECT product_id, product_attribute_id, product_quantity, id_warehouse, id_shop
						FROM `${dbp}order_detail`
						WHERE id_order = {0}";

					if ($o['status_name'] == 'Anulowane')
					{
						// pobieramy wszystkie produkty zamówienia
						$res2 = DB_Query($sql, $order_id);

						// i po kolei aktualizujemy stan magazynowy
						while ($prod = DB_Fetch($res2))
						{
							if ($options['stock_mvt'])
							{
								DB_Query("UPDATE `${dbp}stock` SET physical_quantity = if(cast(physical_quantity as signed) + {0} >= 0, physical_quantity + {0}, 0),
									  usable_quantity = if(cast(usable_quantity as signed) + {0} >= 0, usable_quantity + {0}, 0)
									  WHERE (id_warehouse = {1} OR {1} = 0) AND id_product = {2} AND id_product_attribute = {3} LIMIT 1",
									  $prod['product_quantity'], $prod['id_warehouse'], $prod['product_id'], $prod['product_attribute_id']);
							}
							DB_Query("UPDATE `${dbp}stock_available` SET quantity = quantity + {0}
								  WHERE id_shop = {1} AND id_product = {2} AND id_product_attribute = {3} AND id_shop_group = 0 LIMIT 1",
								  $prod['product_quantity'], $prod['id_shop'], $prod['product_id'], $prod['product_attribute_id']);
						}
					}
					elseif ($options['stock_mvt'] and $o['about_to_ship'] and !$o['already_shipped'])
					{
						// pobieramy wszystkie produkty zamówienia
						$res2 = DB_Query($sql, $order_id);

						// i po kolei aktualizujemy stan magazynowy
						while ($prod = DB_Fetch($res2))
						{
							$quantity = -(int)$prod['product_quantity']; // całkowita ilość sztuk do odjęcia

							while ($quantity <> 0)
							{
								$adj_qty = $quantity; // ile sztuk dodać/odjąć w tej iteracji

								// pobieranie rekordu zawierającego największą liczbę sztuk dla tego produktu
								$sql = "SELECT id_stock, usable_quantity, price_te
									FROM `${dbp}stock`
									WHERE (id_warehouse = {0} OR {1} = 0) AND id_product = {1} AND id_product_attribute = {2}
									ORDER BY usable_quantity DESC LIMIT 1";
								$res = DB_Query($sql, $options['warehouse_id'], $prod['product_id'], (int)$prod['product_attribute_id']);

								if ($stock = DB_Fetch($res))
								{
									// nie można usunąć więcej niż jest na stanie
									if ($adj_qty < 0 and abs($adj_qty) > $stock['usable_quantity'])
									{
										$adj_qty = -$stock['usable_quantity'];
									}

									if ($adj_qty == 0) // produkt w magazynie jest już całkowicie wyzerowany
									{
										break;
									}

									// aktualizacja tabeli stock
									$sql = "UPDATE `${dbp}stock`
										SET
										physical_quantity = physical_quantity + {0},
										usable_quantity = usable_quantity + {0}
										WHERE id_stock = {1} LIMIT 1";
									DB_Query($sql, $adj_qty, $stock['id_stock']);

									// aktualizacja tabeli stock_mvt
									$sql = "INSERT INTO `${dbp}stock_mvt`
										(id_stock, id_order, id_supply_order, id_stock_mvt_reason,
										 id_employee, physical_quantity, date_add, sign, price_te, referer,
										employee_firstname, employee_lastname)
										SELECT {0}, {1}, {2}, {3},
										 {4}, {5}, '{6}', {7}, {8}, 0,
										 firstname, lastname FROM `${dbp}employee` WHERE id_employee = {4}";
									DB_Query($sql, $stock['id_stock'], (int)$prod['order_id'], 0, ($adj_qty < 0) ? 3 : 1,
										$options['stock_mvt'], abs($adj_qty), date('Y-m-d H:i:s'), ($adj_qty < 0) ? -1 : 1, $stock['price_te']);
								}

								$quantity -= $adj_qty;
							}
						}
					}
				}

				// zamówienie dotychczas nie opłacone ...
				if ($o['total_paid_real'] == 0 and $o['paid'])
				{
					// ... będzie teraz miało odnotowaną wpłatę
					Shop_OrderUpdate(array('orders_ids' => array($order_id), 'update_type' => 'paid', 'update_value' => 1));
				}
				elseif ($o['paid'] and $o['valid'] == 0)
				{
					DB_Query("UPDATE `${dbp}orders` SET valid = 1 WHERE id_order = '{0}'", $order_id);
				}
			}

			// właściwa aktualizacja statusu
			if (!$options['compat_mode'])
			{
				DB_Query("UPDATE ${dbp}orders SET `current_state` = '{0}' WHERE id_order = '{1}'", $request['update_value'], $order_id);
			}
			DB_Query("INSERT INTO `${dbp}order_history` (`id_order`, `id_order_state`, `date_add`) VALUES ('{0}', '{1}', CURRENT_TIMESTAMP());", $order_id, $request['update_value']);
	        }
	        //zapisanie numeru nadawczego
	        elseif($request['update_type'] == 'delivery_number')
	        {
	                DB_Query("UPDATE ${dbp}order_carrier SET `tracking_number` = '{0}' WHERE id_order = '{1}'", $request['update_value'], $order_id);
	                DB_Query("UPDATE ${dbp}orders SET `shipping_number` = '{0}' WHERE id_order = '{1}'", $request['update_value'], $order_id);
	        }
	        //zmiana wpłaty
	        elseif($request['update_type'] == 'paid')
	        {
	                if($request['update_value'] == true)
			{
				// zamówienie już opłacone - nie robimy nic
				if (DB_Result(DB_Query("SELECT count(*) FROM `${dbp}orders` WHERE id_order = '{0}' AND total_paid_real >= total_products_wt + total_shipping", $order_id)))
				{
					$counter++;
					continue;
				}

				DB_Query("UPDATE ${dbp}orders SET `total_paid_real` = `total_products_wt` + `total_shipping` - total_discounts_tax_incl, date_upd = '{0}', valid = 1 WHERE id_order = '{1}'", date('Y-m-d H:i:s'), $order_id);
				DB_Query("INSERT INTO `${dbp}order_payment` (order_reference, id_currency, amount, payment_method, date_add)
					  SELECT reference, id_currency, total_paid_real, payment, '{1}' FROM `${dbp}orders` WHERE id_order = '{0}'",
					  $order_id, date('Y-m-d H:i:s'));
			}
	                else
	                {DB_Query("UPDATE ${dbp}orders SET `total_paid_real` = 0 WHERE id_order = '{0}'", $order_id);}
	        }

	        $counter++;
        }

	return array('counter' => $counter);
}


 /**
 * Funkcja zwraca listę dostępnych statusów zamówień
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu, w przypadku tej funkcji nie używana
 * @return array $response tablica zawierająca dostępne statusy zamówień:
 * 		'status_id' => nazwa statusu
 */
function Shop_StatusesList($request)
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	$response = array();
	$sql = "SELECT * FROM `${dbp}order_state_lang` WHERE `id_lang` = '{0}'";
	$res = DB_Query($sql, $options['lang_id']);
	while($status = DB_Fetch($res))
	{$response[$status['id_order_state']] = $status['name'];}

	return $response;
}


 /**
 * Funkcja zwraca listę dostępnych sposobów wysyłki
 * @global array $options : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu, w przypadku tej funkcji nie używana
 * @return array $response tablica zawierająca dostępne sposoby_wysyłki:
 * 		'delivery_id' => nazwa wysyłki
 */
function Shop_DeliveryMethodsList($request)
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy

	$response = array();
	$sql = "SELECT * FROM `${dbp}carrier` WHERE deleted = 0";
	$res = DB_Query($sql);
	while($method = DB_Fetch($res))
	{
		if($method['name'] == "0"){$method['name'] = "Odbiór osobisty";} //podstawowy odbiór osobisty w sklepie nie ma wpisanej nazwy w bazie
		$response[$method['id_carrier']] = $method['name'];
	}

	return $response;
}

// pomocnicza funkcja zwracająca listę aktywnych modułów
// lub sprawdzająca, czy wskazany moduł jest aktywny
function ActiveModules($mod_name = '')
{
	global $options; //globalna tablica z ustawieniami
	$dbp = $options['db_prefix']; //Data Base Prefix - prefix tabel bazy
	static $modules;

	if (!is_array($modules))
	{
		$sql = "SELECT name
			FROM `${dbp}module` m";

		if ($options['shop_id'])
		{
			$sql .= " JOIN `${dbp}module_shop` ms ON m.id_module = ms.id_module AND ms.id_shop = '{0}'";
		}

		$sql .= " WHERE m.active = 1";
		$res = DB_Query($sql, $options['shop_id']);

		while ($mod = DB_Fetch($res))
		{
			$modules[$mod['name']] = 1;
		}
	}

	return $mod_name ? isset($modules[$mod_name]) : $modules;
}

 /**
 * Funkcja zwraca listę dostępnych metod płatności
 * @global array $bl_opt : tablica z ustawieniami ogólnymi z początku pliku
 * @param array $request tablica z żadaniem od systemu, w przypadku tej funkcji nie używana
 * @return array  tablica zawierająca dostępne sposoby płatności:
 * 		'payment_id' => 'nazwa płatności'
 */
function Shop_PaymentMethodsList($request)
{
	$root = _store_root();
	$payment_methods = array();

	include "$root/config/config.inc.php";

	// sprawdzamy po kolei wszysktie moduły
	foreach (glob("$root/modules/*") as $module)
	{
		if (preg_match('/\/([^\/]+)$/', $module, $m))
		{
			if (file_exists($inc_file = $module . "/${m[1]}.php"))
			{
				// odfiltrowujemy tylko moduły dotyczące płatności
				$buff = file_get_contents($inc_file);

				if (preg_match('/class (\w+) extends PaymentModule/m', $buff, $m))
				{
					include_once($inc_file);
					$pm = new $m[1];

					if (class_exists($m[1]))
					{
						$payment_methods[$pm->name] = $pm->displayName;
					}
				}
			}
		}
	}

	return ($payment_methods);
}

/* autodetekcja katalogu sklepu */
function _store_root()
{
	global $options;
	$look_for = 'modules'; // charakterystyczna ścieżka bezpośrednio w katalogu głównym sklepu

	if (empty($options['store_root']))
	{
		// przeszukujemy 3 poziomy w górę i w dół względem lokalizacji pliku integracyjnego
		for ($up = 0; $up < 4; $up++)
		{
			for ($down = 0; $down < 3; $down++)
			{
				$path = getcwd() . '/' . str_repeat('../', $up) . str_repeat('*/', $down) . $look_for;

				if (glob($path)) 
				{
					$path = glob($path);
					break 2;
				}
			}
		}

		if (is_array($path)) // znaleziono poszukiwany plik/katalog
		{
			$options['store_root'] = dirname($path[0]);
		}
	}

	return $options['store_root'];
}
?>
