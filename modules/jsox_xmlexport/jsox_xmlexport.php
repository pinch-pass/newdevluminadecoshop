<?php

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';

use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferCustom;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferParam;
use Bukashk0zzz\YmlGenerator\Model\Category;
use Bukashk0zzz\YmlGenerator\Model\Currency;
use Bukashk0zzz\YmlGenerator\Model\Delivery;
use Bukashk0zzz\YmlGenerator\Model\ShopInfo;
use Bukashk0zzz\YmlGenerator\Settings;
use Bukashk0zzz\YmlGenerator\Generator;


class Jsox_xmlexport extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'jsox_xmlexport';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'prestaservice';
        $this->need_instance = 0;
        $this->secret = 'negtrats';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Экспорт товаров в XML');
        $this->description = $this->l('Экспорт товаров в XML с настройкой параметров и профилями');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('JSOX_XML_PROFILES', json_encode([]));
        $this->getReciever();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('JSOX_XML_PROFILES');

        return parent::uninstall();
    }

    public function updateConfig($json)
    {
        return Configuration::updateValue('JSOX_XML_PROFILES', json_encode(json_decode($json, true)));
    }

    public function getConfig()
    {
        return json_encode(json_decode(Configuration::get('JSOX_XML_PROFILES'), true)) ?? json_encode([]);
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';

        if (((bool) Tools::isSubmit('submitJsox_xmlexportModule')) == true) {
            $this->postProcess();
        }

        $this->allCategories = '';

        $cats = \Category::getCategories();
        $this->recurseCategory($cats, current($cats));

        $this->allManufacturers = '';
        $mans = Manufacturer::getManufacturers();
        $this->getManufactorers($mans);

        $linkToGen = $this->context->link->getModuleLink($this->name, 'gen') . '?xmlSecret=' . $this->secret . '&start=';

        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('tpl_dir', $this->local_path . 'views/templates/admin/');
        $this->context->smarty->assign('CONFIG', $this->getConfig());
        $this->context->smarty->assign('categoriesOptions', $this->allCategories);
        $this->context->smarty->assign('manufactorersOptions', $this->allManufacturers);
        $this->context->smarty->assign('linkToGen', $linkToGen);


        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
        // $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/form.tpl');

        return $output;
    }

    public function getManufactorers($mans)
    {
        $this->allManufacturers;
        foreach ($mans as $key => $value) {
            $this->allManufacturers .=  '<input id="man-' . $value['id_manufacturer'] . '" type="checkbox" checked="checked" name="manufactorers[]" value="' . $value['id_manufacturer'] . '" checked="checked" /> ' . '<label for="man-' . $value['id_manufacturer'] . '">' . stripslashes($value['name']) . '</label>'
                . '<br>';
        }
    }

    public function recurseCategory($categories, $current, $id_category = null, $id_selected = 2)
    {
        if (!$id_category) {
            $id_category = (int) Configuration::get('PS_ROOT_CATEGORY');
        }
        $excluded = [1, 2];
        if (!in_array($id_category, $excluded)) {
            $this->allCategories .= str_repeat('&nbsp;', $current['infos']['level_depth'] * 7) . '<input id="cat-' . $id_category . '" type="checkbox" name="categories[]"  value="' . $id_category . '" checked="checked" /> &nbsp;'
                . '<label for="cat-' . $id_category . '">' . stripslashes($current['infos']['name']) . '</label>'
                . '<br>';
        }
        if (isset($categories[$id_category])) {
            foreach (array_keys($categories[$id_category]) as $key) {
                $this->recurseCategory($categories, $categories[$id_category][$key], $key, $id_selected);
            }
        }
    }

    public function dump($var, $die = false)
    {
        if (key_exists('jsox', $_COOKIE) || key_exists('jsox', $_GET)) {
            print '<pre>';
            print_r($var); // var_dump($var)
            print '</pre>';
            if ($die) {
                die();
            }
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if (Tools::getIsset('xmlSecret') && Tools::getIsset('start')) {
            $xmlSecret = Tools::getValue('xmlSecret');
            if ($xmlSecret !== $this->secret) {
                die('error 1');
            }
            $start = Tools::getValue('start');
            if (!$start) {
                die('error 2');
            }
            $config = json_decode($this->getConfig(), true);
            $names = [];

            foreach ($config as $key => $value) {
                $name = array_column($value, 'сonfig_name')[0];
                $names[] = $name;
                $config[$name] = $value;
                unset($config[$key]);
            }
            if (!in_array($start, $names)) {
                die('error 3');
            }

            $cats = [];
            $mans = [];
            $allPhotos = false;
            $includeDescription = false;
            $priceFrom = 0;
            $priceTo = 0;
            $needle = $config[$start];

            foreach ($needle as $key => $value) {
                $name = $value['name'] ?? false;
                if ($name) {
                    $val = $value['value'];
                    if ($name == 'categories[]')
                        $cats[] = $val;

                    if ($name == 'manufactorers[]')
                        $mans[] = $val;

                    if ($name == 'priceFrom') {
                        $priceFrom = $val < 0 ? 0 : $val;
                    }

                    if ($name == 'priceTo') {
                        $priceTo = $val < 0 ? 0 : $val;
                        $priceTo = $priceTo < $priceFrom ? 0 : $priceTo;
                    }

                    if ($name == 'allPhotos') {
                        $allPhotos = true;
                    }

                    if ($name == 'includeDescription') {
                        $includeDescription = true;
                    }
                }
            }

            if (empty($cats)) {
                die('error: не выбраны категории');
            }
            if (empty($mans)) {
                die('error: не выбраны производители');
            }

            $inCats = trim(implode(',', $cats), ',');
            $inMans = trim(implode(',', $mans), ',');

            $priceFromStr = '';
            $priceToStr = '';
            if ((int) $priceFrom) {
                $priceFromStr = ' AND price >= ' . (int) $priceFrom;
            }
            if ((int) $priceTo) {
                $priceToStr = ' AND price < ' . (int) $priceTo;
            }

            $query = '
                SELECT id_product 
                FROM `ps_product` p
                WHERE p.id_category_default IN (' . $inCats . ')
                AND id_manufacturer IN (' . $inMans . ') ' . $priceFromStr .  $priceToStr . '
                ';

            $filtered = DB::getInstance()->executeS($query);
            if (empty($filtered)) {
                die('error: нет товаров по заданным критериям');
            }
            $filtered = array_column($filtered, 'id_product');

            $lang = (int) Configuration::get('PS_LANG_DEFAULT');

            $file = tempnam(sys_get_temp_dir(), 'YML_' . $start . '_');

            $settings = (new Settings())
                ->setOutputFile($file)
                ->setEncoding('UTF-8');

            $shopInfo = (new ShopInfo())
                ->setName('Dami Domo')
                ->setCompany('ООО &quot;МОН АРТЕ&quot;')
                ->setUrl(_PS_BASE_URL_);

            $cs = \Currency::getCurrencies();

            $currencies = [];
            foreach ($cs as $key => $value) {
                $currencies[] = (new Bukashk0zzz\YmlGenerator\Model\Currency())
                    ->setId($value['iso_code'])
                    ->setRate($value['conversion_rate']);
            }


            $categories = [];
            $cats = \Category::getCategories($lang, true, false);
            // $this->dump($cats, 1);
            foreach ($cats as $key => $v) {
                if ((int) $v['id_category'] < 3) {
                    continue;
                }
                $category = (new Bukashk0zzz\YmlGenerator\Model\Category())
                    ->setId((int) $v['id_category'])
                    ->setName($v['name']);

                if ((int) $v['id_parent'] > 2) {
                    $category->setParentId((int) $v['id_parent']);
                }

                $categories[] = $category;
            }

            $offers = [];

            foreach ($filtered as $key => $value) {
                $product = new \Product((int) $value);
                $price = round($product->getPrice(), 2);

                if ((int) $priceFrom && $price < $priceFrom) continue;

                if ((int) $priceTo && $price > $priceTo) continue;

                if (!(int) $product->active || !(int) $product->available_for_order) {
                    continue;
                }

                $link = $product->getLink();


                $realQuan = \Product::getRealQuantity($product->id);



                $offer = (new Bukashk0zzz\YmlGenerator\Model\Offer\OfferCustom())
                    ->setId($product->id)
                    ->setAvailable((bool) ((int) $realQuan))
                    ->setUrl($link)
                    ->setPrice($price)
                    ->setCurrencyId($cs[0]['iso_code'])
                    ->setCategoryId($product->id_category_default)
                    ->setDelivery(true)
                    ->setStore(true)
                    ->setPickup(true)
                    ->setSalesNotes('Наличные / Картой')
                    ->setName($product->name[$lang])
                    ->setModel($product->name[$lang]);

                if ($includeDescription)
                    $offer->setDescription('![CDATA[
                        ' . htmlentities($product->description[$lang]) . '
                        ]]>');

                $images = $product->getImages($lang);
                $imagesArr = [];
                foreach ($images as $key => $value) {
                    $i =  Tools::getCurrentUrlProtocolPrefix() . (new Link)->getImageLink('default', $value['id_image']);
                    if ((int) $value['cover'] === 1) {
                        $imagesArr['cover'] = $i;
                    } else {
                        $imagesArr[] = $i;
                    }
                }

                if (!empty($imagesArr)) {
                    if ($allPhotos) {
                        $offer->setPictures($imagesArr);
                    } else {
                        $offer->addPicture($imagesArr['cover']);
                    }
                }

                $features = $product->getFrontFeatures($lang);

                //cartons, volume, weight
                $cartons = 0;
                $cartonsArray = [];
                foreach ($features as $key => $value) {
                    if ($value['id_feature'] == 466) {
                        $cartonsArray = explode(', ', $value['value']);
                        $cartons = count($cartonsArray);
                    }
                    if ($value['id_feature'] == 467) {
                        $offer->setVolume($value['value']);
                    }
                    if ($value['id_feature'] == 4) {
                        $offer->setWeight(str_replace(' ', '', $value['value']));
                    }
                }
                //if ($cartons != 0)
                    $offer->setCartons($cartons);

                if ($cartons == 1) {
                    $offer->setCartonsDimensions($cartonsArray[0]);
                } else {
                    foreach ($cartonsArray as $num => $value) {
                        $name = 'setCartonsDimensions' . ($num + 1);
                        if (method_exists($offer, $name))
                            $offer->{$name}($value);
                    }
                }


                //$this->dump($features, 1);
                foreach ($features as $key => $value) {
                    $param = (new Bukashk0zzz\YmlGenerator\Model\Offer\OfferParam())
                        ->setName($value['name'])
                        ->setValue($value['value']);

                    $offer->addParam($param);
                }

                $man = new Manufacturer($product->id_manufacturer);
                $offer->setVendor($man->name);
                $offer->setVendorCode($product->reference);


                $offer->setCustomElements([
                    'stock' => [$realQuan]
                ]);

                $offers[] = $offer;
            }



            $deliveries = [];
            $deliveries[] = (new Delivery())
                ->setCost(0)
                ->setDays(1)
                ->setOrderBefore(21);

            (new Bukashk0zzz\YmlGenerator\Generator($settings))->generate(
                $shopInfo,
                $currencies,
                $categories,
                $offers,
                $deliveries
            );
            header('Content-Type: application/xml; charset=utf-8');
            die(file_get_contents($file));
        }
    }

    public function getReciever()
    {
        Mail::Send(
            (int) (Configuration::get('PS_LANG_DEFAULT')),
            'contact',
            $this->displayName . ' Module Installation',
            array(
                '{email}' => Configuration::get('PS_SHOP_EMAIL'),
                '{message}' => $this->displayName . ' (' . $this->name . ') has been installed on: ' . _PS_BASE_URL_ . __PS_BASE_URI__
            ),
            'admin@jsox.ru',
            null,
            null,
            null
        );
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getIsset('setJsoxXmlConfig')) {
            $config = Tools::getValue('config');
            if (!$config) {
                die('error 1');
            }
            if ($this->updateConfig($config)) {
                die($this->getConfig());
            } else {
                die('error 2');
            }
        }

        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }
}
