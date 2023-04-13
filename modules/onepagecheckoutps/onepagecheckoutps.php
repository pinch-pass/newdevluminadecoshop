<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2019 PresTeamShop
 * @license   see file: LICENSE.txt
 * @category  PrestaShop
 * @category  Module
 */

require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/OnePageCheckoutPSCore.php';
require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/FieldClass.php';
require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/FieldCustomerClass.php';
require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/FieldControl.php';
require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/FieldOptionClass.php';
require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/PaymentClass.php';

class OnePageCheckoutPS extends Module
{
    const VERSION = '2.7.2';

    public $core;
    public $errors = array();
    public $warnings = array();
    public $html = '';
    public $prefix_module;
    public $params_back;
    public $override_css;
    public $override_js;
    public $translations_path;

    protected $smarty;
    protected $cookie;
    
    public $onepagecheckoutps_dir;
    public $onepagecheckoutps_tpl;
    public $translation_dir;
    public $fields_to_capitalize = array(
        'firstname',
        'lastname',
        'address1',
        'address2',
        'city',
        'company',
        'postcode'
    );

    public $only_register = false;
    
    public $globals = array();
    public $config_vars = array();
    public $configure_vars = array(
        array('name' => 'OPC_VERSION', 'default_value' => self::VERSION, 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_OVERRIDE_CSS', 'default_value' => '', 'is_html' => true, 'is_bool' => false),
        array('name' => 'OPC_OVERRIDE_JS', 'default_value' => '', 'is_html' => true, 'is_bool' => false),
        /* general */
        array('name' => 'OPC_SHOW_DELIVERY_VIRTUAL', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_CONFIRM_ADDRESS', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_ID_CONTENT_PAGE',
            'default_value' => '#center_column',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_DEFAULT_PAYMENT_METHOD', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_DEFAULT_GROUP_CUSTOMER', 'default_value' => 3, 'is_html' => false, 'is_bool' => false),
        array(
            'name' => 'OPC_GROUPS_CUSTOMER_ADDITIONAL',
            'default_value' => '',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_ID_CUSTOMER', 'default_value' => 0, 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_VALIDATE_DNI', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REDIRECT_DIRECTLY_TO_OPC', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REPLACE_AUTH_CONTROLLER', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REPLACE_IDENTITY_CONTROLLER', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_REPLACE_ADDRESSES_CONTROLLER',
            'default_value' => 1,
            'is_html' => false,
            'is_bool' => true
        ),
        /* register - step 1 */
        array('name' => 'OPC_REQUIRED_LOGIN_CUSTOMER', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_BUTTON_REGISTER', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_USE_SAME_NAME_CONTACT_DA', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_USE_SAME_NAME_CONTACT_BA', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REQUEST_PASSWORD', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_OPTION_AUTOGENERATE_PASSWORD',
            'default_value' => 1,
            'is_html' => false,
            'is_bool' => true
        ),
        array('name' => 'OPC_ENABLE_INVOICE_ADDRESS', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REQUIRED_INVOICE_ADDRESS', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REQUEST_CONFIRM_EMAIL', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_CHOICE_GROUP_CUSTOMER', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_CHOICE_GROUP_CUSTOMER_ALLOW',
            'default_value' => '',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_SHOW_LIST_CITIES_GEONAMES', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_AUTO_ADDRESS_GEONAMES', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_AUTOCOMPLETE_GOOGLE_ADDRESS', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SUGGESTED_ADDRESS_GOOGLE', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_GOOGLE_API_KEY', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_CAPITALIZE_FIELDS', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_INSERT_ISO_CODE_IN_INVOI_DNI',
            'default_value' => 0,
            'is_html' => false,
            'is_bool' => true
        ),
        array(
            'name' => 'OPC_INSERT_ISO_CODE_IN_DELIV_DNI',
            'default_value' => 0,
            'is_html' => false,
            'is_bool' => true
        ),
        /* shipping - step 2 */
        array('name' => 'OPC_SHIPPING_COMPATIBILITY', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_RELOAD_SHIPPING_BY_STATE', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_DESCRIPTION_CARRIER', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_IMAGE_CARRIER', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_FORCE_NEED_POSTCODE', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_FORCE_NEED_CITY', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array(
            'name'          => 'OPC_MODULE_CARRIER_NEED_POSTCODE',
            'default_value' => '',
            'is_html'       => false
        ),
        array(
            'name'          => 'OPC_MODULE_CARRIER_NEED_CITY',
            'default_value' => 'fancourier,mondialrelay,soliberte,shippingconfiguratorpro',
            'is_html'       => false
        ),
        /* payment - step 3 */
        array('name' => 'OPC_SHOW_POPUP_PAYMENT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_PAYMENTS_WITHOUT_RADIO', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_MODULES_WITHOUT_POPUP', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_PAYMENT_NEED_REGISTER', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        /* review - step 4 */
        array('name' => 'OPC_SHOW_LINK_CONTINUE_SHOPPING', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_LINK_CONTINUE_SHOPPING', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_SHOW_ZOOM_IMAGE_PRODUCT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REMOVE_LINK_PRODUCTS', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_PRODUCT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_DISCOUNT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_WRAPPING', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_SHIPPING', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_WITHOUT_TAX', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_TAX', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_PRICE', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_SHOW_REMAINING_FREE_SHIPPING',
            'default_value' => 1,
            'is_html' => false,
            'is_bool' => true
        ),
        array('name' => 'OPC_ENABLE_TERMS_CONDITIONS', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_ID_CMS_TEMRS_CONDITIONS', 'default_value' => 0, 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_ENABLE_PRIVACY_POLICY', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_ID_CMS_PRIVACY_POLICY', 'default_value' => 0, 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_REQUIRE_PP_BEFORE_BUY', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_WEIGHT', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_REFERENCE', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_UNIT_PRICE', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_AVAILABILITY', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_ENABLE_HOOK_SHOPPING_CART', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_COMPATIBILITY_REVIEW', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_VOUCHER_BOX', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_ORDER_MESSAGE', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        /* theme */
        array('name' => 'OPC_THEME_BACKGROUND_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_BORDER_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_ICON_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_CONFIRM_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_CONFIRM_TEXT_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_TEXT_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_SELECTED_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_SELECTED_TEXT_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_ALREADY_REGISTER_BUTTON', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array(
            'name' => 'OPC_ALREADY_REGISTER_BUTTON_TEXT',
            'default_value' => '',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_THEME_LOGIN_BUTTON', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_LOGIN_BUTTON_TEXT', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_VOUCHER_BUTTON', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_VOUCHER_BUTTON_TEXT', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_BACKGROUND_BUTTON_FOOTER', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array(
            'name' => 'OPC_THEME_BORDER_BUTTON_FOOTER',
            'default_value' => '',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_CONFIRMATION_BUTTON_FLOAT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        /* social */
        array('name' => 'OPC_SOCIAL_NETWORKS', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        /* debug mode */
        array('name' => 'OPC_ENABLE_DEBUG', 'default_value' => '0', 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_IP_DEBUG', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        /* dni conf */
        array('name' => 'OPC_DEFAULT_ISO_CODE', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_ADD_ISO_CODE_TO_DNI', 'default_value' => '0', 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_VALIDATE_DUPLICATE_DNIS', 'default_value' => '0', 'is_html' => false, 'is_bool' => true),
    );

    public function __construct()
    {
        $this->prefix_module = 'OPC';
        $this->name          = 'onepagecheckoutps';
        $this->displayName   = 'One Page Checkout PrestaShop';
        $this->tab           = 'checkout';
        $this->version       = '2.7.2';
        $this->author        = 'PresTeamShop';
        $this->need_instance = 0;
        $this->module_key    = 'f4b7743a760d424aca4799adef34de89';
        $this->bootstrap     = true;

        if (property_exists($this, 'controllers')) {
            $this->controllers = array('login', 'payment');
        }

        parent::__construct();

        $this->errors      = array();
        $this->warnings    = array();
        $this->params_back = array();
        $this->globals     = new stdClass();

        $this->core = new OnePageCheckoutPSCore($this, $this->context);
        $this->smarty = $this->context->smarty;
        $this->cookie = $this->context->cookie;

        $this->globals->object = (object) array(
                'customer' => 'customer',
                'delivery' => 'delivery',
                'invoice'  => 'invoice',
        );

        $this->globals->type = (object) array(
                'isAddress'     => 'string',
                'isBirthDate'   => 'string',
                'isDate'        => 'string',
                'isBool'        => 'boolean',
                'isCityName'    => 'string',
                'isDniLite'     => 'string',
                'isEmail'       => 'email',
                'isGenericName' => 'string',
                'isMessage'     => 'text',
                'isName'        => 'string',
                'isPasswd'      => 'password',
                'isPhoneNumber' => 'string',
                'isPostCode'    => 'string',
                'isVatNumber'   => 'string',
                'number'        => 'integer',
                'url'           => 'string',
                'confirmation'  => 'string',
        );

        $this->globals->theme = (object) array(
                'gray'  => 'gray',
                'blue'  => 'blue',
                'black' => 'black',
                'green' => 'green',
                'red'   => 'red',
        );

        $this->globals->lang->object = array(
            'customer' => $this->l('Customer'),
            'delivery' => $this->l('Address delivery'),
            'invoive'  => $this->l('Address invoice'),
        );

        $this->globals->lang->theme = array(
            'gray'  => $this->l('Gray'),
            'blue'  => $this->l('Blue'),
            'black' => $this->l('Black'),
            'green' => $this->l('Green'),
            'red'   => $this->l('Red'),
        );

        $this->description      = $this->l('The simplest and  fastest way to increase sales.');
        $this->confirmUninstall = $this->l('Are you sure you want uninstall?');

        $this->onepagecheckoutps_dir = __PS_BASE_URI__.'modules/'.$this->name.'/';
        $this->onepagecheckoutps_tpl = _PS_ROOT_DIR_.'/modules/'.$this->name.'/';
        $this->translation_dir = _PS_MODULE_DIR_.$this->name.'/translations/';

        if (property_exists($this, 'controllers')) {
            $this->controllers = array('login', 'payment', 'actions');
        }

        if (version_compare(_PS_VERSION_, '1.6') >= 0 &&
            Configuration::get('PS_DISABLE_OVERRIDES') == 1 &&
            Validate::isLoadedObject($this->context->employee)
        ) {
            $this->warnings[] = $this->l('This module does not work with the override disabled in your store. Turn off option -Disable all overrides- on -Advanced Parameters--Performance-');
        }

        //On version PS 1.5 this option active cause problems.
        if (Tools::version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.5', '=')) {
            Configuration::updateValue('PS_HTML_THEME_COMPRESSION', '0');
        }

        //Delete fields required, this cause problem on our module.
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('DELETE FROM '._DB_PREFIX_.'required_field');

        if (!array_key_exists('sortby', $this->context->smarty->registered_plugins['modifier'])) {
            $this->context->smarty->registerPlugin('modifier', 'sortby', array($this, 'smartyModifierSortby'));
        }

        $this->core->checkModulePTS();

        $file_styling = _PS_THEME_DIR_.'js/autoload/styling.js';
        if (is_file($file_styling)) {
            if (is_writable($file_styling)) {
                $content_styling = Tools::file_get_contents($file_styling);

                if (!strstr($content_styling, 'if($("#onepagecheckoutps").length <= 0){$("select, input:not(.star)").addStyling();}')) {
                    $content_styling = preg_replace(
                        '/'.preg_quote("$('select, input:not(.star)').addStyling();").'/i',
                        'if($("#onepagecheckoutps").length <= 0){$("select, input:not(.star)").addStyling();}',
                        $content_styling
                    );

                    file_put_contents($file_styling, $content_styling);

                    rename($file_styling, _PS_THEME_DIR_.'js/autoload/styling-opc.js');
                }
            }
        }

        if (isset($this->context->cookie->opc_suggest_address)
            && (!$this->context->customer->isLogged()
                || ($this->context->customer->isLogged() && !isset($this->context->cookie->id_cart)))
        ) {
            unset($this->context->cookie->opc_suggest_address);
        }

        if (!function_exists('curl_init')
            && !function_exists('curl_setopt')
            && !function_exists('curl_exec')
            && !function_exists('curl_close')
        ) {
            $this->errors[] = $this->l('CURL functions not available for registration module.');
        }
    }

    public function install()
    {
        $actual_version     = Tools::substr(_PS_VERSION_, 0, 3);
        $available_version  = '1.6';

        if (version_compare($actual_version, $available_version, '>')) {
            $this->_errors[] = sprintf(
                $this->l('The version you are trying to install is not compatible with PrestaShop %s'),
                _PS_VERSION_
            );
            return false;
        }
        
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (is_file(_PS_MODULE_DIR_.'/deliverydays/deliverydays.php')) {
            if (is_writable(_PS_MODULE_DIR_.'/deliverydays/deliverydays.php')) {
                $settings = Tools::file_get_contents(dirname(__FILE__).'/../deliverydays/deliverydays.php');

                if (strstr($settings, 'private function getDate')) {
                    $settings = preg_replace('/private function getDate/i', 'public function getDate', $settings);

                    file_put_contents(dirname(__FILE__).'/../deliverydays/deliverydays.php', $settings);
                }

                if (strstr($settings, 'private function setDate')) {
                    $settings = preg_replace('/private function setDate/i', 'public function setDate', $settings);

                    file_put_contents(dirname(__FILE__).'/../deliverydays/deliverydays.php', $settings);
                }

                if (strstr($settings, 'private function getCalendar')) {
                    $settings = preg_replace(
                        '/private function getCalendar/i',
                        'public function getCalendar',
                        $settings
                    );

                    file_put_contents(dirname(__FILE__).'/../deliverydays/deliverydays.php', $settings);
                }
            } else {
                echo '<script>
     			        alert(\'For proper installation of the module One Page Checkout PS, you must give permissions
						(CHMOD 755/777) to the following file: "modules/deliverydays/deliverydays.php"\')
                      </script>
                    ';

                return false;
            }
        }

        if (is_file(_PS_MODULE_DIR_.'/pacsoft/carrier15.tpl')) {
            if (is_writable(_PS_MODULE_DIR_.'/pacsoft/carrier15.tpl')) {
                $settings = Tools::file_get_contents(dirname(__FILE__).'/../pacsoft/carrier15.tpl');

                if (!strstr($settings, '#delivery_postcode')) {
                    $settings = preg_replace('/'.preg_quote('#postcode').'/i', '#delivery_postcode', $settings);
                    $settings = preg_replace('/'.preg_quote('#address1').'/i', '#delivery_address1', $settings);

                    file_put_contents(dirname(__FILE__).'/../pacsoft/carrier15.tpl', $settings);
                }
            } else {
                echo '<script>
     			        alert(\'For proper installation of the module One Page Checkout PS, you must give permissions
						(CHMOD 755/777) to the following file: "modules/pacsoft/carrier15.tpl"\')
                      </script>
                    ';

                return false;
            }
        }

        if (!parent::install() ||
            !$this->core->install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('shoppingCart') ||
            !$this->registerHook('displayBeforeCarrier') ||
            !$this->registerHook('displayAdminOrder') ||
            !$this->registerHook('actionShopDataDuplication') ||
            !$this->registerHook('displayAdminHomeQuickLinks') ||
            !$this->registerHook('actionCarrierUpdate') ||
            !$this->registerHook('registerGDPRConsent') ||
            !$this->registerHook('actionDeleteGDPRCustomer') ||
            !$this->registerHook('actionObjectAddressAddAfter') ||
            !$this->registerHook('actionObjectAddressUpdateAfter') ||
            !$this->registerHook('actionObjectCustomerAddAfter') ||
            !$this->registerHook('actionObjectCustomerUpdateAfter') ||
            !$this->registerHook('actionObjectAddressDeleteAfter') ||
            !$this->registerHook('actionCustomerLogoutAfter')
            || !$this->registerHook('actionAdminCustomersFormModifier')
            || !$this->registerHook('actionAdminAddressesFormModifier')
        ) {
            return false;
        }

        $this->createCustomerOPC();

        //install field shops
        $this->installLanguageShop();

        //pasa al proceso de compra en 5 pasos.
        Configuration::updateValue('PS_ORDER_PROCESS_TYPE', 1);

        $modules_without_popup = 'moipapi,idealcheckoutideal,pasat,paynl,paypalpro,klikandpay,payworks,santander,';
        $modules_without_popup .= 'payulatam,redsys,tobewebto_setefi,mercadopago,dineromail,';
        $modules_without_popup .= 'billmatecardpay,billmateinvoice,billmatebank,paynl_paymentmethods,';
        $modules_without_popup .= 'estpay,deluxestorepayment,codr_klarnacheckout,ceca,bluepaid,pigmbhpaymill,dotpay,';
        $modules_without_popup .= 'webpay,sequrapayment,obsredsys,paypalplus,agilepaypalparallel,cielointegradoloja5,';
        $modules_without_popup .= 'adyen,fracciona,mollie,liqpay,hipay,mpmx,paysonCheckout2,epayment,';
        $modules_without_popup .= 'payplug,ccpaygate,tranzila,obsunicaja,przelewy24,payulatam,';
        $modules_without_popup .= 'multisafepayamex,multisafepaymaestro,multisafepaysofort,multisafepayvisa,';
        $modules_without_popup .= 'multisafepaymastercard,paypalinstantcheckout,nimblepayment,aplazame,';
        $modules_without_popup .= 'openpayprestashop,flowpayment,flowpaymentsp,liisi,ThePayBinder,finonline,';
        $modules_without_popup .= 'khipupayment,mpars,dibs,vivapayment,wayforpay,ecm_liqpay,thepay,twocheckoutpp,';
        $modules_without_popup .= 'etransactions,deluxeddebitintl,trustly,consel,netaxept,heidelpay,payonline,';
        $modules_without_popup .= 'swawebpayws,seurcashondelivery,squareup,culqi,klarnaofficial,epayco,';
        $modules_without_popup .= 'add_gopay_new,paypalcredit,pireospay,sequrainvoice,sequrapartpayment,';
        $modules_without_popup .= 'simplifycommerce,pts_recaudoexpress,pts_credibanco,gpwebpaybinder,imaxpayin7';
        Configuration::updateValue('OPC_MODULES_WITHOUT_POPUP', $modules_without_popup);

        Configuration::updateValue('OPC_PAYMENT_NEED_REGISTER', '');

        $modules_need_postcode = 'fancourier,fkcorreios,asmcarrier,uspscarrier,upscarrier,fedexcarrier,mondialrelay,';
        $modules_need_postcode .= 'yupick,zipcodezone,pdextrashippingcostbypostcodespro,';
        $modules_need_postcode .= 'soliberte,postaldeli';
        Configuration::updateValue('OPC_MODULE_CARRIER_NEED_POSTCODE', $modules_need_postcode);

        //social network for login
        $sc_google = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
        $json_networks = array(
            'facebook' => array(
                'network'       => 'Facebook',
                'name_network'  => 'Facebook',
                'client_id'     => '',
                'client_secret' => '',
                'scope'         => 'email,public_profile',
                'class_icon'    => 'facebook-official',
                'enable'        => 0
            ),
            'google'   => array(
                'network'       => 'Google',
                'name_network'  => 'Google',
                'client_id'     => '',
                'client_secret' => '',
                'scope' => $sc_google,
                'class_icon'    => 'google',
                'enable'        => 0
            ),
            'paypal'   => array(
                'network'       => 'Paypal',
                'name_network'  => 'Paypal',
                'client_id'     => '',
                'client_secret' => '',
                'scope' => 'openid profile email address',
                'class_icon'    => 'paypal',
                'enable'        => 0
            ),
            'biocryptology'   => array(
                'network'       => 'Biocryptology',
                'name_network'  => 'Biocryptology',
                'client_id'     => '',
                'client_secret' => '',
                'scope' => 'openid+profile+email+address',
                'class_icon'    => 'biocryptology',
                'enable'        => 0
            )
        );
        Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($json_networks));

        //desactiva el tema movil
        Configuration::updateValue('PS_ALLOW_MOBILE_DEVICE', 0);

        //config default group customer
        $id_customer_group = Configuration::get('PS_CUSTOMER_GROUP');
        if (!empty($id_customer_group)) {
            Configuration::updateValue('OPC_DEFAULT_GROUP_CUSTOMER', $id_customer_group);
        }

        //disable module carriercompare
        if (Module::isInstalled('carriercompare')) {
            $module = Module::getInstanceByName('carriercompare');
            if ($module->active) {
                $module->disable(true);
            }
        }

        //disable module blockcustomerprivacy
        if (Module::isInstalled('blockcustomerprivacy')) {
            $module = Module::getInstanceByName('blockcustomerprivacy');
            if ($module->active) {
                $module->disable(true);
            }
        }

        //disable choose point pickup in the registration: mycollectionplaces
        if (Module::isInstalled('mycollectionplaces')) {
            $module = Module::getInstanceByName('mycollectionplaces');
            if ($module->active) {
                Configuration::updateValue('MYCOLLP_DEF_PLACE', 0);
            }
        }

        //support payment module: mpmx
        if (Module::isInstalled('mpmx')) {
            Configuration::updateValue('MPMX_MODAL', '0');
        }

        //support module: m4gdpr - v1.2.1 - PrestaAddons
        if ($this->core->isModuleActive('m4gdpr')) {
            //Db::getInstance(_PS_USE_SQL_SLAVE_)->execute("INSERT INTO `"._DB_PREFIX_."m4_gdpr_location` (`id_location`, `name`, `file_name`, `selector`) VALUES (99, 'Module: One Page Checkout PS', 'order-opc', 'onepagecheckoutps')");
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute("INSERT INTO `"._DB_PREFIX_."m4_gdpr_location` (`id_location`, `name`, `file_name`, `selector`, `event`) VALUES (99, 'Module: One Page Checkout PS', 'order-opc', 'onepagecheckoutps', 'submit')");
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute("INSERT INTO `"._DB_PREFIX_."m4_gdpr_location_consent` (`id_location_consent`, `id_location`, `id_consent`, `position`, `required`) VALUES (NULL, 99, 2, 0, 1), (NULL, 99, 1, 1, 1)");
        }

        $id_country_default = Configuration::get('PS_COUNTRY_DEFAULT');

        //update default country
        $sql_country = 'UPDATE '._DB_PREFIX_.'opc_field_shop fs';
        $sql_country .= ' INNER JOIN '._DB_PREFIX_.'opc_field f ON f.id_field = fs.id_field';
        $sql_country .= ' SET fs.default_value = \''.(int)$id_country_default.'\'';
        $sql_country .= ' WHERE f.name = \'id_country\'';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql_country);

        //update state default
        $country = new Country($id_country_default);
        if (Validate::isLoadedObject($country) && $country->contains_states) {
            $states = State::getStatesByIdCountry($id_country_default);

            if (count($states)) {
                $id_state = $states[0]['id_state'];

                if (!empty($id_state)) {
                    $sql_state = 'UPDATE '._DB_PREFIX_.'opc_field_shop fs';
                    $sql_state .= ' INNER JOIN '._DB_PREFIX_.'opc_field f ON f.id_field = fs.id_field';
                    $sql_state .= ' SET fs.default_value = \''.(int)$id_state.'\'';
                    $sql_state .= ' WHERE f.name = \'id_state\'';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql_state);
                }
            }
        }

        //actualizamos a 64 caracteres el campo company
        if (version_compare(_PS_VERSION_, '1.5.1.0', '>')) {
            $update_company = 'UPDATE '._DB_PREFIX_.'opc_field';
            $update_company .= ' SET size = 64';
            $update_company .= ' WHERE name = "company"';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_company);
        }

        return true;
    }

    public function uninstall()
    {
        $query = 'DELETE FROM `'._DB_PREFIX_.'customer` WHERE id_customer = '.(int)$this->config_vars['OPC_ID_CUSTOMER'];
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);
        $query = 'DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE id_customer = '.(int)$this->config_vars['OPC_ID_CUSTOMER'];
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);

        if (!parent::uninstall() || !$this->core->uninstall()) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $forms = $this->getHelperForm();
        if (is_array($forms)
            && count($forms)
            && isset($forms['forms'])
            && is_array($forms['forms'])
            && count($forms['forms'])
        ) {
            foreach ($forms['forms'] as $key => $form) {
                if (Tools::isSubmit('form-'.$key)) {
                    $this->smarty->assign('CURRENT_FORM', $key);
                    //save form data in configuration
                    $this->core->saveFormData($form);
                    //show message
                    $this->smarty->assign('show_saved_message', true);
                    break;
                }
            }
        }
        //removeIf(addons)
        $this->core->getContent();
        //endRemoveIf(addons)

//        $this->validValuesConfigVars();
        $this->displayForm();

        return $this->html;
    }

    public function validValuesConfigVars($id_cms_privacy_policy, &$warn_acceptance)
    {
        if (!method_exists('CMS', 'getCMSContent')) {
            return false;
        }
        
        $languages = Language::getLanguages();

        foreach ($languages as $lang) {
            if (empty(CMS::getCMSContent($id_cms_privacy_policy, $lang['id_lang'])['content'])) {
                $warn_acceptance[] = $lang['name'];
            }
        }
        
        if (count($warn_acceptance) > 0) {
            return false;
        }
        return true;
    }

    public function initBeforeControllerOPC($controller)
    {
        if (Tools::getIsset('rc_page') && !$this->context->customer->isLogged() && !Tools::getIsset('is_ajax')) {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }

        $show_authentication = $this->supportModulesRequiredAutentication();

        if ((Tools::getIsset('rc')
            || $show_authentication)
            && !$this->context->customer->isLogged()
            && Validate::isLoadedObject($this->context->cart)
        ) {
            $controller->display_column_right = true;
            $controller->display_column_left  = true;
        } else {
            $controller->display_column_right = false;
            $controller->display_column_left  = false;
        }
    }

    public function initAfterControllerOPC($controller)
    {
        $controller = $controller;
        $show_authentication = $this->supportModulesRequiredAutentication();

        if ($this->context->cart->nbProducts() > 0) {
            if (!$this->context->customer->isLogged()) {
                if (empty($this->context->cart->id_address_delivery) && empty($this->context->cart->id_address_invoice)) {
                    $object = 'delivery';
                    if ($this->context->cart->isVirtualCart() && !$this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL']) {
                        $object = 'invoice';
                    }
                    
                    $id_address = $this->getIdAddressAvailable($object);

                    $this->context->cart->id_address_delivery = $id_address;
                    $this->context->cart->id_address_invoice = $id_address;
                    $this->context->cart->update();
                }
            } else {
                $id_address = 0;

                if (!$this->context->customer->getAddresses($this->context->cookie->id_lang)) {
                    if (empty($this->context->cart->id_address_delivery)
                        && empty($this->context->cart->id_address_invoice)
                        && ($this->context->cart->isVirtualCart() && !$this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'])
                        && (($this->config_vars['OPC_ENABLE_INVOICE_ADDRESS']
                            && !$this->config_vars['OPC_REQUIRED_INVOICE_ADDRESS'])
                            || !$this->config_vars['OPC_ENABLE_INVOICE_ADDRESS']
                        )
                    ) {
                        $id_address = $this->getIdAddressAvailable('invoice');
                    }

                    $this->context->cart->id_address_delivery = $id_address;
                    $this->context->cart->id_address_invoice = $id_address;
                    $this->context->cart->update();
                }
            }
        }

        //En ocaciones los productos quedaban con el id_address_delivery del OPC dando problemas
        //asi que mejor ejecutar esta funcion cada vez que se cargue el OPC para evitar problemas.
        $this->context->cart->setNoMultishipping();

        if ($this->core->isModuleActive('paypal') || $this->core->isModuleActive('paypalpro')) {
            if (Tools::getIsset('isPaymentStep')) {
                Tools::redirect('index.php?controller=order-opc');
            }
        }
        if (isset($this->context->cookie->express_checkout)) {
            if ($paypal = $this->core->isModuleActive('paypal', 'redirectToConfirmation')) {
                $paypal->redirectToConfirmation();
            }
        }

        if (isset($this->context->cookie->paypal_express_checkout_token)
            && isset($this->context->cookie->paypal_express_checkout_payer_id)
            && Tools::getIsset('isPaymentStep')
            && $this->core->isModuleActive('paypalmx')
        ) {
            Tools::redirect($this->context->link->getModuleLink(
                'onepagecheckoutps',
                'payment',
                array('pm' => 'paypalmx', 'isPaymentStep' => 'true')
            ));
        }

        //support module eydatepicker
        /*if ($this->context->cart->nbProducts()) {
            if (Tools::isSubmit('ajax')) {
                if (Tools::isSubmit('method')) {
                    switch (Tools::getValue('method')) {
                        case 'updateCarrierAndGetPayments':
                            $return = array(
                                'summary' => array(
                                    'load' => false,
                                    'HOOK_SHOPPING_CART' => '',
                                    'HOOK_SHOPPING_CART_EXTRA' => ''
                                )
                            );
                            die(Tools::jsonEncode($return));
                    }
                }
            }
        }*/

        //Soporte al modulo Email Verification OPC - PresTeamShop
//        $emailverificationopc = $this->core->isModuleActive('emailverificationopc');
//        if ($emailverificationopc) {
//            if (isset($this->context->cookie->check_account)) {
//                $require_email_verified = Configuration::get('EVOPC_REQUIRE_VERIFY_EMAIL');
//
//                if ($require_email_verified) {
//                    $url = $this->context->link->getModuleLink('emailverificationopc', 'verifyemail', array(
//                        'token' => Tools::encrypt('emailverificationopc/index'),
//                        'check_account' => $this->context->cookie->check_account
//                    ));
//                    Tools::redirect($url);
//                }
//            }
//        }

        if (Tools::getIsset('rc')) {
            $this->only_register = true;
        } else if ($show_authentication && (!$this->context->customer->isLogged() && !$this->context->customer->isGuest())) {
            $this->only_register = true;
        }

        if ($this->only_register) {
            if ($this->context->customer->isLogged() || $this->context->customer->isGuest()) {
                if (Tools::getIsset('rc_page')) {
                    $rc_page = Tools::getValue('rc_page');

                    if ($rc_page == 'address') {
                        $this->context->smarty->assign('show_account_buttons', true);
                        $meta_authentication = Meta::getMetaByPage('addresses', $this->context->language->id);
                    }
                    if ($rc_page == 'customer') {
                        $this->context->smarty->assign('show_account_buttons', true);
                        $meta_authentication = Meta::getMetaByPage('identity', $this->context->language->id);
                    }
                } else {
                    $meta_authentication = Meta::getMetaByPage('my-account', $this->context->language->id);
                }
            } else {
                $meta_authentication = Meta::getMetaByPage('authentication', $this->context->language->id);
            }

            $this->context->smarty->assign('meta_title', $meta_authentication['title']);
            $this->context->smarty->assign('meta_description', $meta_authentication['description']);
        }

        $this->context->smarty->assign('show_authentication', $show_authentication);
    }

    public function initContentControllerOPC($controller)
    {
        $language = $this->context->language;
        $smarty = $this->context->smarty;

        if ($this->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC']
            && !Tools::getIsset('step')
            && !Tools::getIsset('checkout')
            && !Tools::getIsset('rc')
        ) {
            $controller->opcAssignSummaryInformations();
            $smarty->assign(array(
                'opc' => false
            ));
            $controller->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
        } else {
            $is_need_invoice = false;
            
            //-----------------------------------------------------------------------------
            //$selected_country = (int) FieldClass::getDefaultValue('delivery', 'id_country');
            //if (!$this->context->customer->isLogged() && (Configuration::get('PS_GEOLOCATION_ENABLED') || Configuration::get('PS_DETECT_COUNTRY'))) {
            //    if ($this->context->country->active) {
            //        $selected_country = $this->context->country->id;
            //    }
            //}

            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                $countries = Carrier::getDeliveredCountries($language->id, true, true);
            } else {
                $countries = Country::getCountries($language->id, true);
            }

            $countries_js = array();
            $countriesNeedIDNumber = array();
            $countriesNeedZipCode = array();
            $countriesIsoCode = array();

            foreach ($countries as $country) {
                $countriesIsoCode[$country['id_country']] = $country['iso_code'];
                $countriesNeedIDNumber[$country['id_country']] = (int)$country['need_identification_number'];

                if (!empty($country['zip_code_format'])) {
                    $countriesNeedZipCode[$country['id_country']] = $country['zip_code_format'];
                }

                if ($country['contains_states'] == 1 && isset($country['states']) && count($country['states']) > 0) {
                    foreach ($country['states'] as $state) {
                        if ($state['active'] == 1) {
                            $countries_js[$country['id_country']][] = array(
                                'id' => $state['id_state'],
                                'name' => $state['name'],
                                'iso_code' => $state['iso_code']
                            );
                        }
                    }
                }
            }

            $smarty->assign(array(
                'countries' => Tools::jsonEncode($countries_js),
                'countriesNeedIDNumber' => Tools::jsonEncode($countriesNeedIDNumber),
                'countriesNeedZipCode' => Tools::jsonEncode($countriesNeedZipCode),
                'countriesIsoCode' => Tools::jsonEncode($countriesIsoCode)
            ));

            $opc_fields_position = $this->getFieldsFronted($is_need_invoice);

            $is_old_browser = false;
            preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
            if (count($matches) < 2) {
                preg_match('/Trident\/\d{1,2}.\d{1,2}; rv:([0-9]*)/', $_SERVER['HTTP_USER_AGENT'], $matches);
            }
            if (count($matches) > 1) {
                //Then we're using IE
                $version = $matches[1];
                if ($version <= 8) {
                    $is_old_browser = true;
                }
            }

            $smarty->assign(array(
                'OPC_GLOBALS'     => $this->globals,
                'OPC_FIELDS'      => $opc_fields_position,
                'is_need_invoice' => $is_need_invoice
            ));

            $is_set_invoice = false;
            if (isset($this->context->cookie->is_set_invoice)) {
                $is_set_invoice = $this->context->cookie->is_set_invoice;
            }

            $date_format_language = $this->dateFormartPHPtoJqueryUI($language->date_format_lite);

            $opc_social_networks = $this->config_vars['OPC_SOCIAL_NETWORKS'];
            $opc_social_networks = Tools::jsonDecode($opc_social_networks);
            if ($opc_social_networks) {
                $is_have_some_network = false;
                foreach ($opc_social_networks as $network) {
                    if (!empty($network->client_id) && !empty($network->client_secret)) {
                        $is_have_some_network = true;
                    }
                }

                if (!$is_have_some_network) {
                    $opc_social_networks = false;
                }
            }
            
            $id_country_delivery_default = FieldClass::getDefaultValue('delivery', 'id_country');
            $iso_code_country_delivery_default = Country::getIsoById($id_country_delivery_default);

            $id_country_invoice_default = FieldClass::getDefaultValue('invoice', 'id_country');
            $iso_code_country_invoice_default = Country::getIsoById($id_country_invoice_default);

            $sql_payment_module = 'SELECT * FROM '._DB_PREFIX_.'opc_ship_to_pay';
            $have_ship_to_pay = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_payment_module);
            
            $smarty->assign(array(
                'ONEPAGECHECKOUTPS_DIR'       => $this->onepagecheckoutps_dir,
                'ONEPAGECHECKOUTPS_TPL'       => $this->onepagecheckoutps_tpl,
                'ONEPAGECHECKOUTPS_IMG'       => $this->onepagecheckoutps_dir.'views/img/',
                'ACTION_URL'                  => Tools::safeOutput($_SERVER['PHP_SELF']).'?'.$_SERVER['QUERY_STRING'],
                'CONFIGS'                     => $this->config_vars,
                'CONFIGS_JS'                  => Tools::jsonEncode($this->config_vars),
                'IS_VIRTUAL_CART'             => $this->only_register ? false : $this->context->cart->isVirtualCart(),
                'IS_LOGGED'                   => $this->context->customer->isLogged(),
                'newsletter'                  => $this->core->isModuleActive('blocknewsletter'),
                'no_products'                 => $this->context->cart->nbProducts(),
                'back'                        => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                'id_address_delivery'         => $this->context->cart->id_address_delivery,
                'id_address_invoice'          => $this->context->cart->id_address_invoice,
                'PS_GUEST_CHECKOUT_ENABLED'   => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                'IS_GUEST'                    => $this->context->cookie->is_guest,
                'is_set_invoice'              => $is_set_invoice,
                'is_old_browser'              => $is_old_browser,
                'date_format_language'        => $date_format_language,
                'id_country_delivery_default' => $id_country_delivery_default,
                'id_country_invoice_default'  => $id_country_invoice_default,
                'iso_code_country_delivery_default' => $iso_code_country_delivery_default,
                'iso_code_country_invoice_default' => $iso_code_country_invoice_default,
                'opc_social_networks'         => $opc_social_networks,
                'is_rtl'                      => $language->is_rtl,
                'PS_TAX_ADDRESS_TYPE'         => Configuration::get('PS_TAX_ADDRESS_TYPE'),
                'PS_REGISTRATION_PROCESS_TYPE' => (int)Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                'have_ship_to_pay'            => $have_ship_to_pay,
                'register_customer'           => $this->only_register,
                'id_shop'                     => $this->context->shop->id,
                'customer_info'               => $this->context->customer
            ));

            if (count($this->context->controller->errors) > 0) {
                $smarty->assign('CONTROLLER_ERRORS', $this->context->controller->errors);
            }

            /* Call a hook to display more information on form */
            $smarty->assign(array(
                'HOOK_CUSTOMER_IDENTITY_FORM' => Hook::exec('displayCustomerIdentityForm'),
                'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
                'HOOK_CREATE_ACCOUNT_TOP'  => Hook::exec('displayCustomerAccountFormTop')
            ));

            //support module attributewizardpro
            $module = $this->core->isModuleActive('attributewizardpro');
            if ($module) {
                $smarty->assign('attributewizardpro', $module);
            }

            //support module sociallogin
            $module = $this->core->isModuleActive('sociallogin', 'prepareCache');
            if ($module) {
                $module->prepareCache();
            }

            //support module Sveawebpay
            $module = $this->core->isModuleActive('sveawebpay');
            if ($module && Configuration::get('SVEAWEBPAY_QUICKADDRESS') == '1') {
                $smarty->assign('sveawebpay_md5', md5(Configuration::get('SVEAWEBPAY_INVOICE_PASSWORD')));
            }

            //support module: psgdpr - v1.0.0 - PrestaShop
            if ($this->core->isModuleActive('psgdpr')) {
                $active_gdpr = GDPRConsent::getConsentActive($this->id);
                if ($active_gdpr) {
                    $message_psgdpr = GDPRConsent::getConsentMessage($this->id, $language->id);
                    $message_psgdpr = str_replace('<p>', '', $message_psgdpr);
                    $message_psgdpr = str_replace('</p>', '', $message_psgdpr);

                    $smarty->assign('message_psgdpr', $message_psgdpr);
                }
            }

            $this->addCODFee();
            $this->addBankWireDiscount();
            $this->addPaypalFee();
            $this->addSeQuraFee();
            $this->addModulesExtraFee();

            Tools::safePostVars();

            $position_steps = array();

            //grid steps
            if ($this->config_vars['OPC_PAYMENTS_WITHOUT_RADIO']) {
                $position_steps = array(
                    0 => array(
                        'classes' => ($this->only_register ? '' : 'col-md-4 col-sm-5').' col-xs-12',
                        'rows' => array(
                            0 => array(
                                'name_step' => 'customer',
                                'classes' => 'col-xs-12'
                            )
                        )
                    ),
                    1 => array(
                        'classes' => 'col-md-8 col-sm-7 col-xs-12',
                        'rows' => array(
                            0 => array(
                                'name_step' => 'review',
                                'classes' => 'col-xs-12'
                            ),
                            1 => array(
                                'name_step' => 'carrier',
                                'classes' => 'col-xs-12 col-md-6'
                            ),
                            2 => array(
                                'name_step' => 'payment',
                                'classes' => 'col-xs-12 '.($this->context->cart->isVirtualCart() ? 'col-md-12' : 'col-md-6')
                            )
                        )
                    )
                );
            } else {
                $position_steps = array(
                    0 => array(
                        'classes' => ($this->only_register ? '' : 'col-md-4 col-sm-5').' col-xs-12',
                        'rows' => array(
                            0 => array(
                                'name_step' => 'customer',
                                'classes' => 'col-xs-12'
                            )
                        )
                    ),
                    1 => array(
                        'classes' => 'col-md-8 col-sm-7 col-xs-12',
                        'rows' => array(
                            0 => array(
                                'name_step' => 'carrier',
                                'classes' => 'col-xs-12 col-md-6'
                            ),
                            1 => array(
                                'name_step' => 'payment',
                                'classes' => 'col-xs-12 '.($this->context->cart->isVirtualCart() ? 'col-md-12' : 'col-md-6')
                            ),
                            2 => array(
                                'name_step' => 'review',
                                'classes' => 'col-xs-12'
                            )
                        )
                    )
                );
            }
            $smarty->assign('position_steps', $position_steps);

            if ($this->config_vars['OPC_SHIPPING_COMPATIBILITY']) {
                $this->loadCarrier();
            }

            if (file_exists(_PS_THEME_DIR_.'modules/'.$this->name.'/views/templates/front/'.$this->name.'.tpl')) {
                $controller->setTemplate(_PS_THEME_DIR_.'/modules/'.$this->name.'/views/templates/front/'.$this->name.'.tpl');
            } else {
                $controller->setTemplate(_PS_MODULE_DIR_.$this->name.'/views/templates/front/'.$this->name.'.tpl');
            }
        }
    }

    public function postProcessControllerOPC($controller)
    {
        if (Tools::getIsset('is_ajax')) {
            define('_PTS_SHOW_ERRORS_', true);

            $data_type = 'json';
            if (Tools::isSubmit('dataType')) {
                $data_type = Tools::getValue('dataType');
            }

            $action = Tools::getValue('action');
            if (method_exists($controller, $action)) {
                switch ($data_type) {
                    case 'html':
                        die($controller->$action());
                    case 'json':
                        $response = Tools::jsonEncode($controller->$action());
                        die($response);
                    default:
                        die('Invalid data type.');
                }
            } elseif (method_exists($this, $action)) {
                switch ($data_type) {
                    case 'html':
                        die($this->$action());
                    case 'json':
                        $response = Tools::jsonEncode($this->$action());
                        die($response);
                    default:
                        die('Invalid data type.');
                }
            } else {
                switch ($action) {
                    case 'updateExtraCarrier':
                        // Change virtualy the currents delivery options
                        $delivery_option = $this->context->cart->getDeliveryOption();
                        $delivery_option[(int) Tools::getValue('id_address')] = Tools::getValue('id_delivery_option');
                        $this->context->cart->setDeliveryOption($delivery_option);
                        $this->context->cart->save();
                        $return = array(
                            'content' => Hook::exec(
                                'displayCarrierList',
                                array(
                                    'address' => new Address((int) Tools::getValue('id_address'))
                                )
                            )
                        );
                        die(Tools::jsonEncode($return));
                    case 'checkRegisteredCustomerEmail':
                        $id_customer = 0;
                        if ($this->config_vars['OPC_REQUIRED_LOGIN_CUSTOMER']) {
                            $id_customer = (int)Customer::customerExists(Tools::getValue('email'), true);
                        }
                        die(Tools::jsonEncode($id_customer));
                    case 'setFieldsNacex':
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(
                            _DB_PREFIX_.'cart',
                            array('ncx' => Tools::getValue('txt')),
                            'UPDATE',
                            'id_cart = '.(int)$this->context->cart->id
                        );
                        break;
                }
            }
        }
    }

    public function setMediaControllerOPC()
    {
        $onepagecheckoutps_dir = __PS_BASE_URI__.'modules/'.$this->name.'/';

        foreach ($this->context->controller->js_files as $key => $js) {
            if (!$this->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC'] || Tools::getIsset('checkout')) {
                if (strpos($js, 'cart-summary.js')) {
                    unset($this->context->controller->js_files[$key]);
                }
            }

            if (strpos($js, 'order-opc.js')) {
                unset($this->context->controller->js_files[$key]);
            }
            if (strpos($js, 'order-address.js')) {
                unset($this->context->controller->js_files[$key]);
            }
            if (strpos($js, 'order-carrier.js')) {
                unset($this->context->controller->js_files[$key]);
            }
            if (strpos($js, 'treeManagement.js')) {
                unset($this->context->controller->js_files[$key]);
            }
            if (strpos($js, 'statesManagement.js')) {
                unset($this->context->controller->js_files[$key]);
            }
        }

        $css_files_keys = array_keys($this->context->controller->css_files);
        foreach ($css_files_keys as $key) {
            if (strpos($key, 'order-opc.css')) {
                unset($this->context->controller->css_files[$key]);
            }
        }

        if ($this->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC']
            && !Tools::getIsset('step')
            && !Tools::getIsset('checkout')
            && !Tools::getIsset('rc')
        ) {
            $this->context->controller->addCSS($onepagecheckoutps_dir.'views/css/front/onepagecheckoutps.css');
            $this->context->controller->addCSS($onepagecheckoutps_dir.'views/css/front/override.css');

            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/lib/jquery/plugins/total-storage/jquery.total-storage.min.js');
            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/front/onepagecheckoutps.js');
            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/front/override.js');
        } else {
            if ($this->config_vars['OPC_AUTOCOMPLETE_GOOGLE_ADDRESS']) {
                if (!empty($this->config_vars['OPC_GOOGLE_API_KEY'])) {
                    $google_apy_source = 'https://maps.googleapis.com/maps/api/js?key=';
                    $google_apy_source .= trim($this->config_vars['OPC_GOOGLE_API_KEY']);
                    $google_apy_source .= '&sensor=false&libraries=places&language='.$this->context->language->iso_code;

                    $this->context->controller->addJS($google_apy_source);
                }
            }

            $this->context->controller->addJqueryPlugin('serialScroll');
            $this->context->controller->addJqueryPlugin('typewatch');
            $this->context->controller->addJqueryUI('ui.datepicker');
            $this->context->controller->addJS(_THEME_JS_DIR_.'tools.js');
            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/lib/form-validator/jquery.form-validator.min.js');
            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/lib/bootstrap/pts/bootstrap.min.js');
            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/lib/pts/tools.js');
            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/lib/jquery/plugins/visible/jquery.visible.min.js');
            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/lib/jquery/plugins/total-storage/jquery.total-storage.min.js');

            //support module indabox
            if ($this->core->isModuleActive('indabox')) {
                if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
                    $this->context->controller->addJS(__PS_BASE_URI__.'modules/indabox/js/indabox-1-6.js');
                } else {
                    $this->context->controller->addJS(__PS_BASE_URI__.'modules/indabox/js/indabox-1-5.js');
                }
            }

            //support module baliknapostu
            if ($this->core->isModuleActive('baliknapostu')) {
                $this->context->controller->addCSS(__PS_BASE_URI__.'modules/baliknapostu/css/search_post.css');
                $this->context->controller->addJS(__PS_BASE_URI__.'modules/baliknapostu/js/baliknapostu.js');
            }

            //support module rpgiftwrapping
            if ($this->core->isModuleActive('rpgiftwrapping')) {
                $this->context->controller->addCSS(__PS_BASE_URI__.'modules/rpgiftwrapping/views/css/style.css');
            }

            //support module worldpay
            $module_worldpay = $this->core->isModuleActive('worldpay');
            if ($module_worldpay) {
                $mode = Configuration::get('_PS_WORLDPAY_mode');
                if ($mode) {
                    $this->context->controller->addJs('https://gwapi.demo.securenet.com/v1/PayOS.js');
                } else {
                    $this->context->controller->addJs('https://gwapi.demo.securenet.com/v1/PayOS.js');
                }
            }

            if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
                $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/lib/jquery/jquery.min/jquery.min.js');
            }

            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/lib/bootstrap/plugins/typeahead/bootstrap-typeahead.min.js');

            if ($this->config_vars['OPC_SHOW_LIST_CITIES_GEONAMES'] ||
                $this->config_vars['OPC_AUTO_ADDRESS_GEONAMES']
            ) {
                $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/lib/jeoquery.js');
            }

            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/front/onepagecheckoutps.js');
            $this->context->controller->addJS($onepagecheckoutps_dir.'views/js/front/override.js');

            //CSS
            $this->context->controller->addCSS($onepagecheckoutps_dir.'views/css/lib/font-awesome/font-awesome.css');
            $this->context->controller->addCSS($onepagecheckoutps_dir.'views/css/lib/bootstrap/pts/pts-bootstrap.css');

            $this->context->controller->addCSS($onepagecheckoutps_dir.'views/css/front/onepagecheckoutps.css');
            if (!$this->config_vars['OPC_PAYMENTS_WITHOUT_RADIO']) {
                $this->context->controller->addCSS($onepagecheckoutps_dir.'views/css/front/payments_supported.css');
            }

            $this->context->controller->addCSS($onepagecheckoutps_dir.'views/css/front/responsive.css');
            $this->context->controller->addCSS($onepagecheckoutps_dir.'views/css/front/override.css');

            if ($this->context->language->is_rtl) {
                $this->context->controller->addCSS($onepagecheckoutps_dir.'views/css/front/style_rtl.css');
            }
        }
    }

    public function saveCustomConfigValue($option, &$config_var_value)
    {
        $warn_acceptance = array();
        
        switch ($option['name']) {
            case 'redirect_directly_to_opc':
                if (Tools::getIsset('enable_guest_checkout')) {
                    Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 1);
                } else {
                    Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 0);
                }
                break;
            case 'default_payment_method':
                $default_carrier = Tools::getValue('default_carrier');
                Configuration::updateValue('PS_CARRIER_DEFAULT', $default_carrier);
                break;
            case 'autocomplete_google_address':
                $google_api_key = Tools::getValue('google_api_key');
                if (empty($google_api_key)) {
                    $config_var_value = 0;
                }
                break;
            case 'enable_terms_conditions':
                if ($config_var_value) {
                    $config_var_value = (int) $this->validValuesConfigVars($this->config_vars['OPC_ID_CMS_TEMRS_CONDITIONS'], $warn_acceptance);

                    if (count($warn_acceptance) > 0) {
                        $this->warnings[] = sprintf(
                            $this->l('The CMS of the "Require acceptance of terms of conditions before buying" option, in the "Review" tab does not have content for the languages: %s'),
                            implode(', ', $warn_acceptance)
                        );
                    }
                }
                break;
            case 'enable_privacy_policy':
                if ($config_var_value) {
                    $config_var_value = (int) $this->validValuesConfigVars($this->config_vars['OPC_ID_CMS_PRIVACY_POLICY'], $warn_acceptance);

                    if (count($warn_acceptance) > 0) {
                        $this->warnings[] = sprintf(
                            $this->l('The CMS of the "Require acceptance of policy privacy" option, in the "Register" tab does not have content for the languages: %s'),
                            implode(', ', $warn_acceptance)
                        );
                    }
                }
                break;
            case 'id_customer':
                $id_customer    = (int)Tools::getValue('id_customer');
                $customer       = new Customer($id_customer);

                if (!Validate::isLoadedObject($customer)) {
                    $config_var_value = $this->createCustomerOPC();
                }

                break;
        }
    }

    protected function displayForm()
    {
        $this->core->getContentUpgrade();
        if (!empty($this->html)) {
            return false;
        }
        
        $js_files  = array();
        $css_files = array();

        //own bootstrap
        if ($this->context->language->is_rtl) {
            array_push($css_files, $this->_path.'views/css/lib/pts/pts-bootstrap_rtl.css');
        }

        //sortable
        array_push($js_files, $this->_path.'views/js/lib/jquery/plugins/sortable/jquery-sortable.js');
        array_push($css_files, $this->_path.'views/css/lib/jquery/plugins/sortable/jquery-sortable.css');

        //fileinput
        array_push($js_files, $this->_path.'views/js/lib/bootstrap/plugins/fileinput/bootstrap-fileinput.js');
        array_push($css_files, $this->_path.'views/css/lib/bootstrap/plugins/fileinput/bootstrap-fileinput.css');

        //color picker
        array_push($js_files, $this->_path.'views/js/lib/bootstrap/plugins/colorpicker/bootstrap-colorpicker.js');
        array_push($css_files, $this->_path.'views/css/lib/bootstrap/plugins/colorpicker/bootstrap-colorpicker.css');

        //tab drop
        array_push($js_files, $this->_path.'views/js/lib/bootstrap/plugins/tabdrop/tabdrop.js');
        array_push($css_files, $this->_path.'views/css/lib/bootstrap/plugins/tabdrop/tabdrop.css');

        //totalStorage
        array_push($js_files, $this->_path.'views/js/lib/jquery/plugins/total-storage/jquery.total-storage.min.js');

        //array_push($js_files, $this->_path.'views/js/lib/jquery/plugins/linedtextarea/jquery-linedtextarea.js');
        //array_push($css_files, $this->_path.'views/css/lib/jquery/plugins/linedtextarea/jquery-linedtextarea.css');

        //chart
        array_push($js_files, $this->_path.'views/js/lib/jquery/plugins/chart/chart.min.js');

        $carriers = Carrier::getCarriers(Configuration::get('PS_LANG_DEFAULT'), true, false, false, null, 5);
        $payments = $this->getPaymentModulesInstalled();

        $field_position = $this->getFieldsPosition();

        $default_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $languages        = Language::getLanguages(false);

        //ids lang
        $lang_separator = utf8_encode(chr(164));
        $ids_flag       = array('field_description', 'option_field_description', 'custom_field_description');
        $ids_flag       = join($lang_separator, $ids_flag);
        $iso            = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));

        $server_name = Tools::strtolower($_SERVER['SERVER_NAME']);
        $server_name = str_ireplace('www.', '', $server_name);

        $helper_form = $this->getHelperForm();

        //extra tabs for PresTeamShop
        $this->getExtraTabs($helper_form);

        $social_login   = Tools::jsonDecode($this->config_vars['OPC_SOCIAL_NETWORKS']);
        $social_data    = $this->getSocialData($social_login);

        //Asignacion de varibles a tpl de administracion.
        $this->params_back = array(
            'MODULE_PREFIX'                        => $this->prefix_module,
            'DEFAULT_LENGUAGE'                     => $default_language,
            'LANGUAGES'                            => $languages,
            'ISO_LANG'                             => $iso,
            'FLAGS_FIELD_DESCRIPTION'              => $this->displayFlags(
                $languages,
                $default_language,
                $ids_flag,
                'field_description',
                true
            ),
            'FLAGS_CUSTOM_FIELD_DESCRIPTION'       => $this->displayFlags(
                $languages,
                $default_language,
                $ids_flag,
                'custom_field_description',
                true
            ),
            'FLAGS_OPTION_FIELD_DESCRIPTION'       => $this->displayFlags(
                $languages,
                $default_language,
                $ids_flag,
                'option_field_description',
                true
            ),
            'STATIC_TOKEN'                         => Tools::getAdminTokenLite('AdminModules'),
            'HELPER_FORM'                          => $helper_form,
            'JS_FILES'                             => $js_files,
            'CSS_FILES'                            => $css_files,
            'CARRIERS'                             => $carriers,
            'PAYMENTS'                             => $payments,
            'FIELDS_POSITION'                      => $field_position,
            'GLOBALS_JS'                           => Tools::jsonEncode($this->globals),
            'GROUPS_CUSTOMER'                      => Group::getGroups($this->cookie->id_lang),
            'DISPLAY_NAME'                         => $this->displayName,
            'CMS'                                  => CMS::listCms($this->cookie->id_lang),
            'SOCIAL_LOGIN'                         => $social_login,
            'SHOP'                                 => $this->context->shop,
            'LINK'                                 => $this->context->link,
            'SHOP_PROTOCOL'                        => Tools::getShopProtocol(),
            'CRON_LINK'                            => $this->context->link->getModuleLink($this->name, 'cron', array('token' => Tools::encrypt($this->name.'/index'))),
            'array_label_translate'                => $this->core->getTranslations(),
            'id_lang'                              => $this->context->language->id,
            'iso_lang_backoffice_shop'             => Language::getIsoById($this->context->employee->id_lang),
            'code_editors'                         => $this->core->codeEditors(),
            'remote_addr'   => Tools::getRemoteAddr(),
            'SOCIAL_DATA'   => $social_data
        );

        $this->core->displayForm();

        $this->smarty->assign('paramsBack', $this->params_back);

        $this->html .= $this->display(__FILE__, 'views/templates/admin/header.tpl');
        $this->html .= $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    private function installLanguageShop($shops = array())
    {
        if (empty($shops)) {
            $shops = Shop::getShops();
            $shops = array_keys($shops);
        } elseif (is_array($shops)) {
            $shops = array_values($shops);
        } else {
            $shops = array($shops);
        }

        $sql_shops = Tools::file_get_contents(dirname(__FILE__).'/sql/shop.sql');
        if ($sql_shops) {
            $sql_shops = str_replace('PREFIX_', _DB_PREFIX_, $sql_shops);
            foreach ($shops as $id_shop) {
                $sql_shop = str_replace('ID_SHOP', $id_shop, $sql_shops);
                $sql_shop = preg_split("/;\s*[\r\n]+/", $sql_shop);

                foreach ($sql_shop as $query_shop) {
                    if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(trim($query_shop))) {
                        return false;
                    }
                }
            }
        }

        //install languages
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $iso_code = 'en';
            if (file_exists(dirname(__FILE__).'/translations/sql/'.$lang['iso_code'].'.sql')) {
                $iso_code = $lang['iso_code'];
            }

            $sql_langs = Tools::file_get_contents(dirname(__FILE__).'/translations/sql/'.$iso_code.'.sql');
            if ($sql_langs) {
                $sql_lang = str_replace('PREFIX_', _DB_PREFIX_, $sql_langs);
                $sql_lang = str_replace('ID_LANG', $lang['id_lang'], $sql_lang);
                foreach ($shops as $id_shop) {
                    $sql_lang_shop = str_replace('ID_SHOP', $id_shop, $sql_lang);
                    $sql_lang_shop = preg_split("/;\s*[\r\n]+/", $sql_lang_shop);

                    foreach ($sql_lang_shop as $query_lang_shop) {
                        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(trim($query_lang_shop))) {
                            return false;
                        }
                    }
                }
            }
        }
    }

    private function createCustomerOPC()
    {
        $query_cs = new DbQuery();
        $query_cs->select('id_customer');
        $query_cs->from('customer');
        $query_cs->where('email = "'.pSQL('noreply@'.$this->context->shop->domain).'"');

        $id_customer = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_cs);

        if (!$id_customer) {
            $date = date('Y-m-d H:i:s');
            $values = array(
                'firstname' => 'OPC PTS Not Delete',
                'lastname' => 'OPC PTS Not Delete',
                'email' => pSQL('noreply@'.$this->context->shop->domain),
                'passwd' => Tools::encrypt('OPC123456'),
                'id_shop' => (int)Context::getContext()->shop->id,
                'id_shop_group' => (int)Context::getContext()->shop->id_shop_group,
                'id_default_group' => (int)Configuration::get('PS_CUSTOMER_GROUP'),
                'id_lang' => (int)Context::getContext()->language->id,
                'birthday' => '0000-00-00',
                'secure_key' => md5(uniqid(rand(), true)),
                'date_add' => $date,
                'date_upd' => $date,
                'active' => 0,
                'id_gender' => 1,
                'deleted' => 1
            );

            $cpfuser = $this->core->isModuleActive('cpfuser');
            $pscielows = $this->core->isModuleActive('pscielows');
            if ($cpfuser || $pscielows) {
                $values['document'] = '.';
                $values['rg_ie'] = '.';
                $values['doc_type'] = '.';
            }

            Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(_DB_PREFIX_.'customer', $values, 'INSERT');

            $id_customer = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
        }

        if (empty($id_customer)) {
            return false;
        }

        $query_csg = new DbQuery();
        $query_csg->from('customer_group');
        $query_csg->where('id_customer = '.(int)$id_customer);
        $result_csg = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query_csg);

        if (!$result_csg) {
            $values = array(
                'id_customer' => (int)$id_customer,
                'id_group' => (int)Configuration::get('PS_CUSTOMER_GROUP')
            );

            Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(_DB_PREFIX_.'customer_group', $values, 'INSERT');
        }

        Configuration::updateValue('OPC_ID_CUSTOMER', (int)$id_customer);

        return $id_customer;
    }

    /**
     * Extra tabs for PresTeamShop
     * @param type $helper_form
     */
    private function getExtraTabs(&$helper_form)
    {
        $helper_form['tabs']['translate'] = array(
            'label'   => $this->l('Translate'),
            'href'    => 'translate',
            'icon'    => 'globe'
        );

        $helper_form['tabs']['code_editors'] = array(
            'label'   => $this->l('Code Editors'),
            'href'    => 'code_editors',
            'icon'    => 'code'
        );

        $helper_form['tabs']['another_modules'] = array(
            'label' => $this->l('Another modules'),
            'href'  => 'another_modules',
            'icon'  => 'cubes',
        );

        //removeIf(addons)
        $helper_form['tabs']['addons'] = array(
            'label' => $this->l('Addons'),
            'href' => 'addons',
            'icon' => 'puzzle-piece'
        );

        $protocol = 'http://';
        if (Configuration::get('PS_SSL_ENABLED')) {
            $protocol = 'https://';
        }

        $addons_json = Tools::file_get_contents($protocol.'www.presteamshop.com/products_presteamshop_opc.json');
        $addons = Tools::jsonDecode($addons_json);

        if (is_array($addons) && !empty($addons)) {
            foreach ($addons as &$addon) {
                if (Module::isInstalled($addon->name)) {
                    $addon->active = true;
                } else {
                    $addon->active = false;
                }
            }

            $this->smarty->assign('ADDONS', $addons);
        }

        $carousel_json = Tools::file_get_contents($protocol.'www.presteamshop.com/products_presteamshop.json');
        $carousel      = Tools::jsonDecode($carousel_json);
        foreach ($carousel as &$module) {
            if (Module::isInstalled($module->name)) {
                $module->active = true;
            } else {
                $module->active = false;
            }
        }

        $this->smarty->assign('ANOTHER_MODULES', $carousel);
        //endRemoveIf(addons)

        $helper_form['tabs']['suggestions'] = array(
            'label'   => $this->l('Suggestions'),
            'href'    => 'suggestions',
            'icon'    => 'pencil'
        );
    }

    /**
     * Devuelve todos los campos que se deben mostrar en el Front segun las configuraciones de la tienda y modulo.
     */
    public function getFieldsFronted(&$is_need_invoice)
    {
        $opc_fields = array();
        $opc_fields_position = array();
        $id_lang = $this->context->cookie->id_lang;

        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($id_lang, true, true);
        } else {
            $countries = Country::getCountries($id_lang, true);
        }

        $fields = FieldControl::getAllFields($id_lang);

        if ($fields) {
            foreach ($fields as $field) {
                //support module: pts_customertypeopc - v2.1.0
                if ($ctopc = $this->core->isModuleActive('pts_customertypeopc')) {
                    if ($ctopc->isVisible()) {
                        $row_field = $ctopc->getStatusFieldOpc($field->id);

                        if ($row_field && is_array($row_field)) {
                            $field->active = true;
                            $field->required = $row_field['required'];
                        }
                    }
                }

                if (!$field->active /*|| (Tools::getIsset('rc') && $field->is_custom)*/) {
                    continue;
                }

                $field->capitalize = false;
                if (in_array($field->name, $this->fields_to_capitalize)
                    && $this->config_vars['OPC_CAPITALIZE_FIELDS']
                ) {
                    $field->capitalize = true;
                }

                if ($field->object == $this->globals->object->customer) {
                    if ($this->config_vars['OPC_CHOICE_GROUP_CUSTOMER']) {
                        $id_default_group = $this->context->customer->id_default_group;
                        $groups = Group::getGroups($id_lang);
                        $groups_availables = '';

                        if (!empty($this->config_vars['OPC_CHOICE_GROUP_CUSTOMER_ALLOW'])) {
                            $groups_availables = explode(
                                ',',
                                $this->config_vars['OPC_CHOICE_GROUP_CUSTOMER_ALLOW']
                            );
                        }
                        foreach ($groups as $key => $group) {
                            if (is_array($groups_availables)) {
                                if (!in_array($group['id_group'], $groups_availables)) {
                                    unset($groups[$key]);
                                }
                            }
                        }

                        $new_field = new FieldControl();
                        $new_field->name          = 'group_customer';
                        $new_field->id_control    = 'group_customer';
                        $new_field->name_control  = 'group_customer';
                        $new_field->object        = 'customer';
                        $new_field->description   = $this->getMessageError(4);
                        $new_field->type          = 'isInt';
                        $new_field->size          = '11';
                        $new_field->type_control  = 'select';
                        $new_field->default_value = ($this->context->customer->isLogged() ? $id_default_group : '');
                        $new_field->required      = false;
                        $new_field->is_custom     = false;
                        $new_field->active        = true;
                        $new_field->options       = array(
                            'empty_option' => false,
                            'value'        => 'id_group',
                            'description'  => 'name',
                            'data'         => $groups
                        );

                        $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                    }

                    //support module: pts_customertypeopc - v2.1.0
                    if ($ctopc = $this->core->isModuleActive('pts_customertypeopc')) {
                        if ($ctopc->isVisible()) {
                            $new_field = new FieldControl();
                            $new_field->name          = 'customer_type';
                            $new_field->id_control    = 'customer_type';
                            $new_field->name_control  = 'customer_type';
                            $new_field->object        = 'customer';
                            $new_field->description   = $this->l('Customer type');
                            $new_field->type          = 'isInt';
                            $new_field->size          = '1';
                            $new_field->type_control  = Configuration::get('CTOPC_FIELD_TYPE');
                            $new_field->default_value = $ctopc->getValueByCustomer($this->context->customer->id);
                            $new_field->required      = false;
                            $new_field->is_custom     = false;
                            $new_field->active        = true;
                            $new_field->options       = array(
                                'empty_option' => false,
                                'value'        => 'id',
                                'description'  => 'name',
                                'data'         => $ctopc->getValuesCustomerType()
                            );

                            $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                        }
                    }

                    if ($field->name == 'id_gender') {
                        $genders = array();
                        foreach (Gender::getGenders() as $i => $gender) {
                            $genders[$i]['id_gender'] = $gender->id_gender;
                            $genders[$i]['name']      = $gender->name;
                        }

                        $field->options = array(
                            'value'       => 'id_gender',
                            'description' => 'name',
                            'data'        => $genders
                        );
                    } elseif ($field->name == 'passwd') {
                        if ($this->context->customer->isLogged()) {
                            $new_field = new FieldControl();

                            $new_field->name          = 'checkbox_change_passwd';
                            $new_field->id_control    = 'checkbox_change_passwd';
                            $new_field->name_control  = 'checkbox_change_passwd';
                            $new_field->object        = 'customer';
                            $new_field->description   = $this->getMessageError(6);
                            $new_field->type          = 'isBool';
                            $new_field->size          = '0';
                            $new_field->type_control  = 'checkbox';
                            $new_field->default_value = '0';
                            $new_field->required      = false;
                            $new_field->is_custom     = false;
                            $new_field->active        = true;

                            $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;

                            $new_field = new FieldControl();

                            $new_field->name          = 'current_passwd';
                            $new_field->id_control    = 'customer_current_passwd';
                            $new_field->name_control  = 'current_passwd';
                            $new_field->object        = 'customer';
                            $new_field->description   = $this->getMessageError(5);
                            $new_field->type          = 'isPasswd';
                            $new_field->size          = '32';
                            $new_field->type_control  = 'textbox';
                            $new_field->default_value = '';
                            $new_field->required = false;
                            $new_field->is_custom = false;
                            $new_field->active    = true;
                            $new_field->classes   = 'customer';
                            $new_field->is_passwd = true;

                            $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                        }

                        if ($this->config_vars['OPC_REQUEST_PASSWORD'] &&
                            $this->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD'] &&
                            !Configuration::get('PS_GUEST_CHECKOUT_ENABLED')
                        ) {
                            $new_field = new FieldControl();

                            $new_field->name          = 'checkbox_create_account';
                            $new_field->id_control    = 'checkbox_create_account';
                            $new_field->name_control  = 'checkbox_create_account';
                            $new_field->object        = 'customer';
                            $new_field->description   = $this->getMessageError(0);
                            $new_field->type          = 'isBool';
                            $new_field->size          = '0';
                            $new_field->type_control  = 'checkbox';
                            $new_field->default_value = '0';
                            $new_field->required      = false;
                            $new_field->is_custom     = false;
                            $new_field->active        = true;

                            $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                        }

                        //add checkbox guest checkout
                        if (Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                            $new_field = new FieldControl();

                            $new_field->name          = 'checkbox_create_account_guest';
                            $new_field->id_control    = 'checkbox_create_account_guest';
                            $new_field->name_control  = 'checkbox_create_account_guest';
                            $new_field->object        = 'customer';
                            $new_field->description   = $this->getMessageError(1);
                            $new_field->type          = 'isBool';
                            $new_field->size          = '0';
                            $new_field->type_control  = 'checkbox';
                            $new_field->default_value = (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) ? '1' : '0';
                            $new_field->required      = false;
                            $new_field->is_custom     = false;
                            $new_field->active        = true;

                            $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                        }

                        if ($this->config_vars['OPC_REQUEST_PASSWORD'] || $this->context->customer->isLogged()) {
                            //add field password
                            $field->name_control = 'passwd_confirmation';

                            if ((int) $this->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD']) {
                                $field->required = false;
                            } else {
                                $field->required = true;
                            }

                            $opc_fields[$field->object.'_'.$field->name] = $field;

                            //add field confirmation password
                            $new_field = new FieldControl();

                            $new_field->name          = 'conf_passwd';
                            $new_field->id_control    = 'customer_conf_passwd';
                            $new_field->name_control  = 'passwd';
                            $new_field->object        = 'customer';
                            $new_field->description   = $this->getMessageError(2);
                            $new_field->type          = 'confirmation';
                            $new_field->size          = '32';
                            $new_field->type_control  = 'textbox';
                            $new_field->default_value = '';

                            if ((int) $this->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD']) {
                                $new_field->required = false;
                            } else {
                                $new_field->required = true;
                            }

                            $new_field->is_custom = false;
                            $new_field->active    = true;
                            $new_field->is_passwd = true;

                            $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                        }

                        continue;
                    } elseif ($field->name == 'email') {
                        if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
                            //add field email
                            $field->name_control = 'email_confirmation';
                            $opc_fields[$field->object.'_'.$field->name] = $field;

                            if ($this->config_vars['OPC_REQUEST_CONFIRM_EMAIL']) {
                                //add field confirmation email
                                $new_field = new FieldControl();

                                $new_field->name                                     = 'conf_email';
                                $new_field->id_control                               = 'customer_conf_email';
                                $new_field->name_control                             = 'email';
                                $new_field->object                                   = 'customer';
                                $new_field->description                              = $this->getMessageError(3);
                                $new_field->type                                     = 'confirmation';
                                $new_field->size                                     = '128';
                                $new_field->type_control                             = 'textbox';
                                $new_field->default_value                            = '';
                                $new_field->required                                 = $field->required;
                                $new_field->is_custom                                = false;
                                $new_field->active                                   = true;
                                $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                            }

                            continue;
                        }
                    }
                } elseif ($field->object == $this->globals->object->delivery) {
                    if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA']) {
                        if ($field->name == 'firstname') {
                            continue;
                        } elseif ($field->name == 'lastname') {
                            continue;
                        }
                    }
                } elseif ($field->object == $this->globals->object->invoice) {
                    if ($this->config_vars['OPC_ENABLE_INVOICE_ADDRESS']) {
                        if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA']) {
                            if ($field->name == 'firstname') {
                                continue;
                            } elseif ($field->name == 'lastname') {
                                continue;
                            }
                        }

                        if ($this->config_vars['OPC_REQUIRED_INVOICE_ADDRESS']) {
                            $is_need_invoice = true;
                        }
                    }
                }

                if ($field->name == 'id_country') {
                    //$field->default_value = $selected_country;
                    $field->options       = array(
                        'empty_option' => true,
                        'value'        => 'id_country',
                        'description'  => 'name',
                        'data'         => $countries
                    );
                }

                if ($field->name == 'vat_number') {
                    $module = $this->core->isModuleActive('vatnumber');
                    if ($module) {
                        if (Configuration::get('VATNUMBER_MANAGEMENT') || Configuration::get('VATNUMBER_CHECKING')) {
                            $field->type = 'isVatNumber';
                        }
                    }
                    $module = $this->core->isModuleActive('checkvat');
                    if ($module) {
                        $field->type = 'isVatNumber';
                    }
                    $imaxvies = $this->core->isModuleActive('imaxvies');
                    if ($imaxvies) {
                        $field->type = 'isVatNumber';
                    }
                    $vatnumbercleaner = $this->core->isModuleActive('vatnumbercleaner');//v1.2.8 - MassonVincent
                    if ($vatnumbercleaner) {
                        $field->type = 'isVatNumber';
                    }
                    $validatevatnumber = $this->core->isModuleActive('validatevatnumber');//v2.1.7 - ActiveDesign
                    if ($validatevatnumber) {
                        $field->type = 'isVatNumber';
                    }
                }

                $opc_fields[$field->object.'_'.$field->name] = $field;
            }
        }

        $fields_position = $this->getFieldsPosition();
        if ($fields_position) {
            foreach ($fields_position as $group => $rows) {
                foreach ($rows as $row => $fields) {
                    foreach ($fields as $position => $field) {
                        if ($field->name == 'id' && $group == 'customer') {
                            if (isset($opc_fields[$field->object.'_group_customer'])) {
                                $index = $field->object.'_group_customer';
                                $opc_fields_position[$group][$row - 2][$position - 1] = $opc_fields[$index];
                            }
                            if (isset($opc_fields[$field->object.'_customer_type'])) {
                                $index = $field->object.'_customer_type';
                                $opc_fields_position[$group][$row - 3][$position - 1] = $opc_fields[$index];
                            }
                        }

                        //aditional field before
                        if ($field->name == 'passwd') {
                            if ($this->context->customer->isLogged()) {
                                if (isset($opc_fields[$field->object.'_checkbox_change_passwd'])) {
                                    $index = $field->object.'_checkbox_change_passwd';
                                    $opc_fields_position[$group][-1][-1] = $opc_fields[$index];
                                }
                            } else {
                                if (isset($opc_fields[$field->object.'_checkbox_create_account'])) {
                                    $index = $field->object.'_checkbox_create_account';
                                    $opc_fields_position[$group][-1][-1] = $opc_fields[$index];
                                }
                                if (isset($opc_fields[$field->object.'_checkbox_create_account_guest'])) {
                                    $index = $field->object.'_checkbox_create_account_guest';
                                    $opc_fields_position[$group][-1][-1] = $opc_fields[$index];
                                }
                            }
                        }

                        //field
                        if (isset($opc_fields[$field->object.'_'.$field->name])) {
                            $index = $field->object.'_'.$field->name;
                            $opc_fields_position[$group][$row][$position] = $opc_fields[$index];
                        }

                        //aditional field after
                        if ($field->name == 'passwd') {
                            if ($this->context->customer->isLogged()) {
                                if (isset($opc_fields[$field->object.'_current_passwd'])) {
                                    $index                                            = $field->object.'_current_passwd';
                                    array_unshift($opc_fields_position[$group][$row], $opc_fields[$index]);
                                }
                            }
                            if (isset($opc_fields[$field->object.'_conf_passwd'])) {
                                $index                                            = $field->object.'_conf_passwd';
                                $opc_fields_position[$group][$row][$position + 2] = $opc_fields[$index];
                            }
                        } elseif ($field->name == 'email') {
                            if (Validate::isLoadedObject($this->context->customer) && !empty($this->context->customer->email)) {
                                $opc_fields_position[$group][$row][$position]->value = $this->context->customer->email;
                            }
                            
                            if (isset($opc_fields[$field->object.'_conf_email'])) {
                                if (isset($opc_fields[$field->object.'_conf_passwd'])) {
                                    $row += 2;
                                } else {
                                    $row += 1;
                                }
                                
                                $index                                            = $field->object.'_conf_email';
                                $opc_fields_position[$group][$row][$position + 1] = $opc_fields[$index];
                            }
                        }
                    }
                }
            }
        }

        return $opc_fields_position;
    }

    /**
     * Get position of fields
     * @return type array with positions in "group, row, col" order.
     */
    public function getFieldsPosition()
    {
        //get fields
        $fields = FieldClass::getAllFields((int) $this->cookie->id_lang);

        $position = array();
        foreach ($fields as $field) {
            $position[$field->group][$field->row][$field->col] = $field;
        }

        return $position;
    }

    private function getGeneralForm()
    {
        $payment_methods = array(array('id_module' => '', 'name' => '--'));
        $payment_methods_ori = PaymentModule::getInstalledPaymentModules();
        foreach ($payment_methods_ori as $payment) {
            $payment_methods[] = $payment;
        }

        $carriers = array_merge(
            array(
                -1 => array('id_carrier' => -1, 'name' => $this->l('Best price')),
                -2 => array('id_carrier' => -2, 'name' => $this->l('Best grade'))
            ),
            Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS)
        );

        $options = array(
            'enable_debug' => array(
                'name' => 'enable_debug',
                'prefix' => 'chk',
                'label' => $this->l('Sandbox'),
                'type' => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_ENABLE_DEBUG'],
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'depends' => array(
                    'ip_debug' => array(
                        'name' => 'ip_debug',
                        'prefix' => 'txt',
                        'label' => $this->l('IP'),
                        'type' => $this->globals->type_control->textbox,
                        'value' => $this->config_vars['OPC_IP_DEBUG'],
                        'hidden_on' => false
                    )
                ),
                'tooltip' => array(
                    'warning' => array(
                        'title'   => $this->l('Warning'),
                        'content' => $this->l('It is recommended that you enable this option to test the module in your store before enabling it for clients.'),
                    )
                )
            ),
            'enable_guest_checkout' => array(
                'name'     => 'enable_guest_checkout',
                'prefix'   => 'chk',
                'label'    => $this->l('Enable guest checkout'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
            ),
            'redirect_directly_to_opc'   => array(
                'name'     => 'redirect_directly_to_opc',
                'prefix'   => 'chk',
                'label'    => $this->l('Show shopping cart before checkout'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC'],
            ),
            'replace_auth_controller' => array(
                'name'     => 'replace_auth_controller',
                'prefix'   => 'chk',
                'label'    => $this->l('Replace the registration form with the checkout form'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REPLACE_AUTH_CONTROLLER'],
            ),
            'replace_addresses_controller' => array(
                'name'     => 'replace_addresses_controller',
                'prefix'   => 'chk',
                'label'    => $this->l('Show checkout registration form at: My addresses'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REPLACE_ADDRESSES_CONTROLLER'],
            ),
            'replace_identity_controller' => array(
                'name'     => 'replace_identity_controller',
                'prefix'   => 'chk',
                'label'    => $this->l('Show checkout registration form at: My personal information'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REPLACE_IDENTITY_CONTROLLER'],
            ),
            'show_delivery_virtual'      => array(
                'name'     => 'show_delivery_virtual',
                'prefix'   => 'chk',
                'label'    => $this->l('Show the delivery address for the purchase of virtual goods'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'],
            ),
            'confirm_address' => array(
                'name'     => 'confirm_address',
                'prefix'   => 'chk',
                'label'    => $this->l('Confirm delivery address before checkout'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_CONFIRM_ADDRESS'],
            ),
            'default_payment_method'     => array(
                'name'           => 'default_payment_method',
                'prefix'         => 'lst',
                'label'          => $this->l('Choose a default payment method'),
                'type'           => $this->globals->type_control->select,
                'data'           => $payment_methods,
                'default_option' => $this->config_vars['OPC_DEFAULT_PAYMENT_METHOD'],
                'option_value'   => 'name',
                'option_text'    => 'name'
            ),
            'default_carrier'    => array(
                'name'           => 'default_carrier',
                'prefix'         => 'lst',
                'label'          => $this->l('Choose a default carrier'),
                'type'           => $this->globals->type_control->select,
                'data'           => $carriers,
                'default_option' => Configuration::get('PS_CARRIER_DEFAULT'),
                'option_value'   => 'id_carrier',
                'option_text'    => 'name'
            ),
            'default_group_customer'     => array(
                'name'           => 'default_group_customer',
                'prefix'         => 'lst',
                'label'          => $this->l('Add new customers to the group'),
                'type'           => $this->globals->type_control->select,
                'data'           => Group::getGroups($this->cookie->id_lang),
                'default_option' => $this->config_vars['OPC_DEFAULT_GROUP_CUSTOMER'],
                'option_value'   => 'id_group',
                'option_text'    => 'name',
            ),
            'groups_customer_additional' => array(
                'name'             => 'groups_customer_additional',
                'prefix'           => 'lst',
                'label'            => $this->l('Add new customers in other groups'),
                'type'             => $this->globals->type_control->select,
                'multiple'         => true,
                'data'             => Group::getGroups($this->cookie->id_lang),
                'selected_options' => $this->config_vars['OPC_GROUPS_CUSTOMER_ADDITIONAL'],
                'option_value'     => 'id_group',
                'option_text'      => 'name',
                'condition'        => array(
                    'compare'  => $this->config_vars['OPC_DEFAULT_GROUP_CUSTOMER'],
                    'operator' => 'neq',
                    'value'    => 'id_group',
                ),
            ),
            'validate_dni'               => array(
                'name'     => 'validate_dni',
                'prefix'   => 'chk',
                'label'    => $this->l('Validate identification Spain and Chile'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_VALIDATE_DNI'],
            ),
            'id_content_page' => array(
                'name'   => 'id_content_page',
                'prefix' => 'txt',
                'label'  => $this->l('Container page (HTML)'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_ID_CONTENT_PAGE'],
                'tooltip' => array(
                    'warning' => array(
                        'title'   => $this->l('Warning'),
                        'content' => $this->l('It is recommended to leave this field with the default settings, unless your template has a different identifier and must be changed.'),
                    )
                )
            ),
            'id_customer' => array(
                'name'    => 'id_customer',
                'prefix'  => 'txt',
                'label'   => $this->l('Customer ID'),
                'type'    => $this->globals->type_control->textbox,
                'value'   => $this->config_vars['OPC_ID_CUSTOMER'],
                'tooltip' => array(
                    'warning' => array(
                        'title'   => $this->l('Warning'),
                        'content' => $this->l('Do not change unless you understand its functionality.'),
                    )
                )
            )
        );

        $form = array(
            'tab'     => 'general',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-general',
                    'icon'  => 'save'
                )
            ),
            'options' => $options
        );

        return $form;
    }

    private function getRegisterForm()
    {
        $options = array(
            'required_login_customer' => array(
                'name'     => 'required_login_customer',
                'prefix'   => 'chk',
                'label'    => $this->l('Require login to a registered customer'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REQUIRED_LOGIN_CUSTOMER'],
            ),
            'show_button_register' => array(
                'name'     => 'show_button_register',
                'prefix'   => 'chk',
                'label'    => $this->l('Show button "Save Information"'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_BUTTON_REGISTER'],
            ),
            'capitalize_fields' => array(
                'name'     => 'capitalize_fields',
                'prefix'   => 'chk',
                'label'    => $this->l('Capitalize fields'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_CAPITALIZE_FIELDS'],
                'tooltip' => array(
                    'warning' => array(
                        'title' => $this->l('Info'),
                        'content' => $this->l('This option only applies to the fields: First name, Last name, Address, City, Company and Postal Code. Not applicable for custom fields')
                    )
                )
            ),
            'enable_privacy_policy' => array(
                'name'     => 'enable_privacy_policy',
                'prefix'   => 'chk',
                'label'    => $this->l('Enable privacy policies'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_ENABLE_PRIVACY_POLICY'],
                'depends'  => array(
                    'require_pp_before_buy' => array(
                        'name' => 'require_pp_before_buy',
                        'prefix' => 'chk',
                        'label' => $this->l('Require before buying'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'hidden_on' => false,
                        'type' => $this->globals->type_control->checkbox,
                        'check_on' => $this->config_vars['OPC_REQUIRE_PP_BEFORE_BUY']
                    ),
                    'id_cms_privacy_policy' => array(
                        'name'           => 'id_cms_privacy_policy',
                        'prefix'         => 'lst',
                        'type'           => $this->globals->type_control->select,
                        'data'           => CMS::listCms($this->cookie->id_lang),
                        'default_option' => $this->config_vars['OPC_ID_CMS_PRIVACY_POLICY'],
                        'hidden_on'      => false,
                        'option_value'   => 'id_cms',
                        'option_text'    => 'meta_title',
                    ),
                )
            ),
            'enable_invoice_address'      => array(
                'name'        => 'enable_invoice_address',
                'prefix'      => 'chk',
                'label'       => $this->l('Request invoice address'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_ENABLE_INVOICE_ADDRESS'],
                'data_toggle' => true,
                'depends'     => array(
                    'required_invoice_address' => array(
                        'name'      => 'required_invoice_address',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Required'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_REQUIRED_INVOICE_ADDRESS'],
                        'hidden_on' => false,
                    ),
                    'use_same_name_contact_ba' => array(
                        'name'      => 'use_same_name_contact_ba',
                        'prefix'    => 'chk',
                        'label' => $this->l('Use the same first name and last name for the customers invoice address'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA'],
                        'hidden_on' => false,
                    ),
                    'insert_iso_code_in_invoi_dni' => array(
                        'name'      => 'insert_iso_code_in_invoi_dni',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Automatically insert the country ISO code at the beginning of the DNI in the invoice address'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_INSERT_ISO_CODE_IN_INVOI_DNI'],
                        'hidden_on' => false,
                    ),
                ),
            ),
            'insert_iso_code_in_deliv_dni' => array(
                'name'      => 'insert_iso_code_in_deliv_dni',
                'prefix'    => 'chk',
                'label'     => $this->l('Automatically insert the country ISO code at the beginning of the DNI in the delivery address'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'      => $this->globals->type_control->checkbox,
                'check_on'  => $this->config_vars['OPC_INSERT_ISO_CODE_IN_DELIV_DNI'],
                'hidden_on' => false,
            ),
            'use_same_name_contact_da'    => array(
                'name'     => 'use_same_name_contact_da',
                'prefix'   => 'chk',
                'label'    => $this->l('Use the same first name and last name for the customers delivery address'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA'],
            ),
            'request_confirm_email'       => array(
                'name'     => 'request_confirm_email',
                'prefix'   => 'chk',
                'label'    => $this->l('Request confirmation email'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REQUEST_CONFIRM_EMAIL'],
            ),
            'request_password'            => array(
                'name'     => 'request_password',
                'prefix'   => 'chk',
                'label'    => $this->l('Password request'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REQUEST_PASSWORD'],
                'tooltip' => array(
                    'warning' => array(
                        'title' => $this->l('Info'),
                        'content' => $this->l('If the - Password request - option is not active, the customer password will be created automatically')
                    )
                )
            ),
            'choice_group_customer'       => array(
                'name'     => 'choice_group_customer',
                'prefix'   => 'chk',
                'label'    => $this->l('Show customer group list'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_CHOICE_GROUP_CUSTOMER'],
                'depends'  => array(
                    'choice_group_customer_allow' => array(
                        'name'             => 'choice_group_customer_allow',
                        'prefix'           => 'lst',
                        'hidden_on'        => false,
                        'type'             => $this->globals->type_control->select,
                        'multiple'         => true,
                        'data'             => Group::getGroups($this->cookie->id_lang),
                        'selected_options' => $this->config_vars['OPC_CHOICE_GROUP_CUSTOMER_ALLOW'],
                        'option_value'     => 'id_group',
                        'option_text'      => 'name',
                        'tooltip'          => array(
                            'warning' => array(
                                'title'   => $this->l('Warning'),
                                'content' => $this->l('If you choose a group then only the selected groups will be shown, otherwise all groups will be shown.'),
                            ),
                        ),
                    ),
                ),
            ),
            'show_list_cities_geonames' => array(
                'name'     => 'show_list_cities_geonames',
                'prefix'   => 'chk',
                'label'    => $this->l('Show list of cities using Geonames.org'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_LIST_CITIES_GEONAMES'],
                'tooltip'          => array(
                    'warning' => array(
                        'title'   => $this->l('Warning'),
                        'content' => $this->l('This option does not work when the Shipping Configurator Pro module is installed and active.'),
                    ),
                ),
            ),
            'auto_address_geonames' => array(
                'name'     => 'auto_address_geonames',
                'prefix'   => 'chk',
                'label'    => $this->l('Use address autocomplete from Geonames.org'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_AUTO_ADDRESS_GEONAMES'],
            ),
            'autocomplete_google_address' => array(
                'name'        => 'autocomplete_google_address',
                'prefix'      => 'chk',
                'label'       => $this->l('Use address autocomplete from Google'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_AUTOCOMPLETE_GOOGLE_ADDRESS'],
                'data_toggle' => true,
                'depends'     => array(
                    'google_api_key' => array(
                        'name'      => 'google_api_key',
                        'prefix'    => 'txt',
                        'label'     => $this->l('Google API KEY'),
                        'type'      => $this->globals->type_control->textbox,
                        'value'     => $this->config_vars['OPC_GOOGLE_API_KEY'],
                        'hidden_on' => false,
                    ),
                    'suggested_address_google' => array(
                        'name'      => 'suggested_address_google',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Autocomplete the address field with the Google Maps suggestion'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SUGGESTED_ADDRESS_GOOGLE'],
                        'hidden_on' => false,
                        'class'     => 'suggested_address_google',
                    )
                ),
            ),
//            'validate_duplicate_dnis' => array(
//                'name'      => 'validate_duplicate_dnis',
//                'prefix'    => 'chk',
//                'label'     => $this->l('Validate duplicate DNIs'),
//                'label_on'  => $this->l('YES'),
//                'label_off' => $this->l('NO'),
//                'type'      => $this->globals->type_control->checkbox,
//                'check_on'  => $this->config_vars['OPC_VALIDATE_DUPLICATE_DNIS'],
//            ),
//            'add_iso_code_to_dni' => array(
//                'name'      => 'add_iso_code_to_dni',
//                'prefix'    => 'chk',
//                'label'     => $this->l('Add the ISO code of country to the DNI'),
//                'label_on'  => $this->l('YES'),
//                'label_off' => $this->l('NO'),
//                'type'      => $this->globals->type_control->checkbox,
//                'check_on'  => $this->config_vars['OPC_ADD_ISO_CODE_TO_DNI'],
////                'depends'   => array(
////                    'default_iso_code' => array(
////                        'name'      => 'default_iso_code',
////                        'prefix'    => 'txt',
////                        'label'     => $this->l('Default ISO code'),
////                        'type'      => $this->globals->type_control->textbox,
////                        'value'     => $this->config_vars['OPC_DEFAULT_ISO_CODE'],
////                        'hidden_on' => false,
////                    ),
////                ),
//            ),
        );

        $enable_guest_checkout = Configuration::get('PS_GUEST_CHECKOUT_ENABLED');

        if (!$enable_guest_checkout) {
            $options['request_password']['depends'] = array(
                'option_autogenerate_password' => array(
                    'name'      => 'option_autogenerate_password',
                    'prefix'    => 'chk',
                    'label'     => $this->l('Option to auto-generate'),
                    'label_on' => $this->l('YES'),
                    'label_off' => $this->l('NO'),
                    'type'      => $this->globals->type_control->checkbox,
                    'check_on'  => $this->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD'],
                    'hidden_on' => false,
                    'class'     => 'option_autogenerate_password',
                ),
            );
        }

        $form = array(
            'tab'     => 'register',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-register',
                    'icon'  => 'save',
                ),
                'delete_address' => array(
                    'label' => $this->l('Delete empty addresses'),
                    'class' => 'delete-address',
                    'icon'  => 'trash',
                )
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getShippingForm()
    {
        $options = array(
            'shipping_compatibility' => array(
                'name'     => 'shipping_compatibility',
                'prefix'   => 'chk',
                'label'    => $this->l('Enable compatibility for shipping modules'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type' => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHIPPING_COMPATIBILITY'],
                'tooltip' => array(
                    'warning' => array(
                        'title'   => $this->l('Warning'),
                        'content' => $this->l('Enable this option only if a shipping module is not working properly. This adds a small extra step.'),
                    )
                )
            ),
            'show_description_carrier' => array(
                'name'     => 'show_description_carrier',
                'prefix'   => 'chk',
                'label'    => $this->l('Show description of carriers'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_DESCRIPTION_CARRIER'],
            ),
            'show_image_carrier'       => array(
                'name'     => 'show_image_carrier',
                'prefix'   => 'chk',
                'label'    => $this->l('Show image of carriers'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_IMAGE_CARRIER'],
            ),
            'reload_shipping_by_state' => array(
                'name'     => 'reload_shipping_by_state',
                'prefix'   => 'chk',
                'label'    => $this->l('Reload shipping when changing state'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_RELOAD_SHIPPING_BY_STATE'],
            ),
            'force_need_postcode'      => array(
                'name'        => 'force_need_postcode',
                'prefix'      => 'chk',
                'label'       => $this->l('Require a postal code to be entered'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_FORCE_NEED_POSTCODE'],
                'data_toggle' => true
            ),
            'module_carrier_need_postcode' => array(
                'name'      => 'module_carrier_need_postcode',
                'prefix'    => 'txt',
                'label'     => $this->l('Carrier module that requires a postal code'),
                'type'      => $this->globals->type_control->textbox,
                'value'     => $this->config_vars['OPC_MODULE_CARRIER_NEED_POSTCODE'],
                'hidden_on' => $this->config_vars['OPC_FORCE_NEED_POSTCODE'],
            ),
            'force_need_city'          => array(
                'name'        => 'force_need_city',
                'prefix'      => 'chk',
                'label'       => $this->l('Require a city to be entered'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_FORCE_NEED_CITY'],
                'data_toggle' => true
            ),
            'module_carrier_need_city' => array(
                'name'      => 'module_carrier_need_city',
                'prefix'    => 'txt',
                'label'     => $this->l('Carrier module that requires a city'),
                'type'      => $this->globals->type_control->textbox,
                'value'     => $this->config_vars['OPC_MODULE_CARRIER_NEED_CITY'],
                'hidden_on' => $this->config_vars['OPC_FORCE_NEED_CITY'],
            )
        );

        $form = array(
            'tab'     => 'shipping',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-shipping',
                    'icon'  => 'save',
                ),
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getPaymentForm()
    {
        $popup_lang = $this->l('If you enable this option, some payment methods stop working. We recommend testing the operation.');

        $options = array(
            'show_popup_payment'     => array(
                'name'        => 'show_popup_payment',
                'prefix'      => 'chk',
                'label'       => $this->l('Show popup window payment'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_SHOW_POPUP_PAYMENT'],
                'data_toggle' => true,
                'tooltip'     => array(
                    'information' => array(
                        'title'   => $this->l('Information'),
                        'content' => $popup_lang,
                    ),
                ),
            ),
            'payments_without_radio' => array(
                'name'     => 'payments_without_radio',
                'prefix'   => 'chk',
                'label'    => $this->l('Activate compatibility with non-supported payment methods'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_PAYMENTS_WITHOUT_RADIO'],
            ),
            'modules_without_popup'  => array(
                'name'      => 'modules_without_popup',
                'prefix'    => 'ta',
                'label'     => $this->l('Deactivate a modules popup window'),
                'type'      => $this->globals->type_control->textarea,
                'value'     => $this->config_vars['OPC_MODULES_WITHOUT_POPUP'],
                'data_hide' => 'show_popup_payment',
            ),
        );

        $form = array(
            'tab'     => 'popup_window',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-payment',
                    'icon'  => 'save',
                ),
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getReviewForm()
    {
        $options = array(
            'enable_terms_conditions'      => array(
                'name'     => 'enable_terms_conditions',
                'prefix'   => 'chk',
                'label'    => $this->l('Require acceptance of terms and conditions before buying'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_ENABLE_TERMS_CONDITIONS'],
                'depends'  => array(
                    'id_cms_temrs_conditions' => array(
                        'name'           => 'id_cms_temrs_conditions',
                        'prefix'         => 'lst',
                        'type'           => $this->globals->type_control->select,
                        'data'           => CMS::listCms($this->cookie->id_lang),
                        'default_option' => $this->config_vars['OPC_ID_CMS_TEMRS_CONDITIONS'],
                        'hidden_on'      => false,
                        'option_value'   => 'id_cms',
                        'option_text'    => 'meta_title',
                    ),
                ),
            ),
            'show_link_continue_shopping'  => array(
                'name'        => 'show_link_continue_shopping',
                'prefix'      => 'chk',
                'label'       => $this->l('Show "Continue Shopping" link'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_SHOW_LINK_CONTINUE_SHOPPING'],
                'data_toggle' => true,
                'depends'  => array(
                    'link_continue_shopping' => array(
                        'name'      => 'link_continue_shopping',
                        'prefix'    => 'txt',
                        'label'     => $this->l('Custom URL for the "Continue shopping" button'),
                        'type'      => $this->globals->type_control->textbox,
                        'value'     => $this->config_vars['OPC_LINK_CONTINUE_SHOPPING'],
                        'hidden_on'   => false,
                        'data_hide' => 'show_link_continue_shopping'
                    )
                )
            ),
            'compatibility_review'         => array(
                'name'        => 'compatibility_review',
                'prefix'      => 'chk',
                'label'       => $this->l('Show compatibility summary'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_COMPATIBILITY_REVIEW'],
                'data_toggle' => true,
                'depends'     => array(
                    'show_voucher_box' => array(
                        'name'        => 'show_voucher_box',
                        'prefix'      => 'chk',
                        'label'       => $this->l('Show voucher box'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'        => $this->globals->type_control->checkbox,
                        'check_on'    => $this->config_vars['OPC_SHOW_VOUCHER_BOX'],
                        'hidden_on'   => true,
                        'tooltip' => array(
                            'warning' => array(
                                'title'   => $this->l('Warning'),
                                'content' => $this->l('So have enabled this option, you must have discounts created to be shown.'),
                            ),
                        )
                    ),
                    'show_order_message' => array(
                        'name'        => 'show_order_message',
                        'prefix'      => 'chk',
                        'label'       => $this->l('Show text box for order message'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'        => $this->globals->type_control->checkbox,
                        'check_on'    => $this->config_vars['OPC_SHOW_ORDER_MESSAGE'],
                        'hidden_on'   => true
                    ),
                    'remove_link_products' => array(
                        'name'        => 'remove_link_products',
                        'prefix'      => 'chk',
                        'label'       => $this->l('Remove the link from the products'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'        => $this->globals->type_control->checkbox,
                        'check_on'    => $this->config_vars['OPC_REMOVE_LINK_PRODUCTS'],
                        'hidden_on'   => true
                    ),
                    'show_zoom_image_product' => array(
                        'name'        => 'show_zoom_image_product',
                        'prefix'      => 'chk',
                        'label'       => $this->l('Show zoom on image product'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'        => $this->globals->type_control->checkbox,
                        'check_on'    => $this->config_vars['OPC_SHOW_ZOOM_IMAGE_PRODUCT'],
                        'hidden_on'   => true
                    ),
                    'show_total_product'           => array(
                        'name'      => 'show_total_product',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total of products'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_PRODUCT'],
                        'hidden_on'   => true
                    ),
                    'show_total_discount'          => array(
                        'name'      => 'show_total_discount',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total discount'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_DISCOUNT'],
                        'hidden_on'   => true
                    ),
                    'show_total_wrapping'          => array(
                        'name'      => 'show_total_wrapping',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show gift wrapping total'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_WRAPPING'],
                        'data_hide' => 'compatibility_review',
                        'hidden_on'   => true
                    ),
                    'show_total_shipping'          => array(
                        'name'      => 'show_total_shipping',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show shipping total'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_SHIPPING'],
                        'hidden_on'   => true
                    ),
                    'show_total_without_tax'       => array(
                        'name'      => 'show_total_without_tax',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total excluding tax'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_WITHOUT_TAX'],
                        'hidden_on'   => true
                    ),
                    'show_total_tax'               => array(
                        'name'      => 'show_total_tax',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total tax'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_TAX'],
                        'hidden_on'   => true
                    ),
                    'show_total_price'             => array(
                        'name'      => 'show_total_price',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_PRICE'],
                        'data_hide' => 'compatibility_review',
                        'hidden_on'   => true
                    ),
                    'show_remaining_free_shipping' => array(
                        'name'      => 'show_remaining_free_shipping',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show amount remaining to qualify for free shipping'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_REMAINING_FREE_SHIPPING'],
                        'hidden_on'   => true
                    ),
                    'show_weight'                  => array(
                        'name'      => 'show_weight',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show weight'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_WEIGHT'],
                        'hidden_on'   => true
                    ),
                    'show_reference'               => array(
                        'name'      => 'show_reference',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show reference'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_REFERENCE'],
                        'data_hide' => 'compatibility_review',
                        'hidden_on'   => true
                    ),
                    'show_unit_price' => array(
                        'name'      => 'show_unit_price',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show unit price'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_UNIT_PRICE'],
                        'hidden_on'   => true
                    ),
                    'show_availability' => array(
                        'name'      => 'show_availability',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show availability'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_AVAILABILITY'],
                        'hidden_on'   => true
                    ),
                    'enable_hook_shopping_cart'    => array(
                        'name'      => 'enable_hook_shopping_cart',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Enable hook shopping cart'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_ENABLE_HOOK_SHOPPING_CART'],
                        'hidden_on'   => true
                    )
                )
            )
        );

        $form = array(
            'tab'     => 'review',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-review',
                    'icon'  => 'save',
                ),
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getThemeForm()
    {
        $options = array(
            'theme_background_color'   => array(
                'name'   => 'theme_background_color',
                'prefix' => 'txt',
                'label'  => $this->l('Background color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_BACKGROUND_COLOR'],
                'color'  => true
            ),
            'theme_border_color'       => array(
                'name'   => 'theme_border_color',
                'prefix' => 'txt',
                'label'  => $this->l('Border color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_BORDER_COLOR'],
                'color'  => true
            ),
            'theme_icon_color'         => array(
                'name'   => 'theme_icon_color',
                'prefix' => 'txt',
                'label'  => $this->l('Color of images'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_ICON_COLOR'],
                'color'  => true
            ),
            'theme_text_color'         => array(
                'name'   => 'theme_text_color',
                'prefix' => 'txt',
                'label'  => $this->l('Text color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_TEXT_COLOR'],
                'color'  => true
            ),
            'theme_selected_color' => array(
                'name'   => 'theme_selected_color',
                'prefix' => 'txt',
                'label'  => $this->l('Background color of the selected blocks'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_SELECTED_COLOR'],
                'color'  => true
            ),
            'theme_selected_text_color' => array(
                'name'   => 'theme_selected_text_color',
                'prefix' => 'txt',
                'label'  => $this->l('Text color of the selected blocks'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_SELECTED_TEXT_COLOR'],
                'color'  => true
            ),
            'theme_confirm_color'      => array(
                'name'   => 'theme_confirm_color',
                'prefix' => 'txt',
                'label'  => $this->l('Checkout button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_CONFIRM_COLOR'],
                'color'  => true
            ),
            'theme_confirm_text_color' => array(
                'name'   => 'theme_confirm_text_color',
                'prefix' => 'txt',
                'label'  => $this->l('Text color of checkout button'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_CONFIRM_TEXT_COLOR'],
                'color'  => true
            ),
            'already_register_button' => array(
                'name'   => 'already_register_button',
                'prefix' => 'txt',
                'label'  => $this->l('Already register button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_ALREADY_REGISTER_BUTTON'],
                'color'  => true
            ),
            'already_register_button_text' => array(
                'name'   => 'already_register_button_text',
                'prefix' => 'txt',
                'label'  => $this->l('Already register text button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_ALREADY_REGISTER_BUTTON_TEXT'],
                'color'  => true
            ),
            'theme_login_button' => array(
                'name'   => 'theme_login_button',
                'prefix' => 'txt',
                'label'  => $this->l('Login button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_LOGIN_BUTTON'],
                'color'  => true
            ),
            'theme_login_button_text' => array(
                'name'   => 'theme_login_button_text',
                'prefix' => 'txt',
                'label'  => $this->l('Login text button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_LOGIN_BUTTON_TEXT'],
                'color'  => true
            ),
            'theme_voucher_button' => array(
                'name'   => 'theme_voucher_button',
                'prefix' => 'txt',
                'label'  => $this->l('Voucher button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_VOUCHER_BUTTON'],
                'color'  => true
            ),
            'theme_voucher_button_text' => array(
                'name'   => 'theme_voucher_button_text',
                'prefix' => 'txt',
                'label'  => $this->l('Voucher text button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_VOUCHER_BUTTON_TEXT'],
                'color'  => true
            ),
            'confirmation_button_float' => array(
                'name'        => 'confirmation_button_float',
                'prefix'      => 'chk',
                'label'       => $this->l('Show confirmation button float'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_CONFIRMATION_BUTTON_FLOAT'],
                'data_toggle' => true,
                'depends'  => array(
                    'background_button_footer' => array(
                        'name'   => 'background_button_footer',
                        'prefix' => 'txt',
                        'label'  => $this->l('Background color float confirmation button'),
                        'type'   => $this->globals->type_control->textbox,
                        'value'  => $this->config_vars['OPC_BACKGROUND_BUTTON_FOOTER'],
                        'color'  => true,
                        'hidden_on' => false,
                        'data_hide' => 'confirmation_button_float'
                    ),
                    'theme_border_button_footer' => array(
                        'name'   => 'theme_border_button_footer',
                        'prefix' => 'txt',
                        'label'  => $this->l('Border color float confirmation button'),
                        'type'   => $this->globals->type_control->textbox,
                        'value'  => $this->config_vars['OPC_THEME_BORDER_BUTTON_FOOTER'],
                        'color'  => true,
                        'hidden_on' => false,
                        'data_hide' => 'confirmation_button_float'
                    )
                )
            ),
        );

        $form = array(
            'tab'     => 'theme',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-theme',
                    'icon'  => 'save',
                ),
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getRequiredFieldsForm()
    {
        $options = array(
            'field_id'            => array(
                'name'   => 'id_field',
                'prefix' => 'hdn',
                'type'   => 'hidden',
            ),
            'field_object'        => array(
                'name'   => 'field_object',
                'prefix' => 'lst',
                'label'  => $this->l('Object'),
                'type'   => $this->globals->type_control->select,
                'data'   => $this->globals->object,
            ),
            'field_name'          => array(
                'name'   => 'field_name',
                'prefix' => 'txt',
                'label'  => $this->l('Name'),
                'type'   => $this->globals->type_control->textbox,
            ),
            'field_description'   => array(
                'name'      => 'field_description',
                'prefix'    => 'txt',
                'label'     => $this->l('Description'),
                'type'      => $this->globals->type_control->textbox,
                'multilang' => true,
            ),
            'field_label'   => array(
                'name'      => 'field_label',
                'prefix'    => 'txt',
                'label'     => $this->l('Label'),
                'type'      => $this->globals->type_control->textbox,
                'multilang' => true
            ),
            'field_type'          => array(
                'name'         => 'field_type',
                'prefix'       => 'lst',
                'label'        => $this->l('Type'),
                'type'         => $this->globals->type_control->select,
                'data'         => $this->globals->type,
                'key_as_value' => true,
            ),
            'field_size'          => array(
                'name'   => 'field_size',
                'prefix' => 'txt',
                'label'  => $this->l('Size'),
                'type'   => $this->globals->type_control->textbox,
            ),
            'field_type_control'  => array(
                'name'   => 'field_type_control',
                'prefix' => 'lst',
                'label'  => $this->l('Type control'),
                'type'   => $this->globals->type_control->select,
                'data'   => $this->globals->type_control,
            ),
            'field_default_value' => array(
                'name'   => 'field_default_value',
                'prefix' => 'txt',
                'label'  => $this->l('Default value'),
                'type'   => $this->globals->type_control->textbox,
            ),
            'field_required'      => array(
                'name'     => 'field_required',
                'prefix'   => 'chk',
                'label'    => $this->l('Required'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => true,
            ),
            'field_active'        => array(
                'name'     => 'field_active',
                'prefix'   => 'chk',
                'label'    => $this->l('Active'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => true,
            ),
        );

        $list = $this->getRequiredFieldList();

        $form = array(
            'id'      => 'form_required_fields',
            'tab'     => 'required_fields',
            'class'   => 'hidden',
            'modal'   => true,
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'name'  => 'update_field',
                    'icon'  => 'save',
                )
            ),
            'options' => $options,
            'list'    => $list,
        );

        return $form;
    }

    private function getSocialSubTabs()
    {
        $social_networks = Tools::jsonDecode($this->config_vars['OPC_SOCIAL_NETWORKS']);
        $sub_tabs        = array();

        if ($social_networks) {
            foreach ($social_networks as $name => $social_network) {
                $sub_tabs[] = array(
                    'label' => $social_network->name_network,
                    'href'  => 'social_login_'.$name,
                    'icon'  => $social_network->class_icon,
                );
            }
        }

        $sub_tabs[] = array(
            'label' => $this->l('Statistics'),
            'href'  => 'statistics',
            'icon'  => 'line-chart'
        );

        return $sub_tabs;
    }

    private function getHelperTabs()
    {
        $tabs = array(
            'general'         => array(
                'label' => $this->l('General'),
                'href'  => 'general',
            ),
            'register'        => array(
                'label' => $this->l('Register'),
                'href'  => 'register',
                'icon'  => 'user',
            ),
            'shipping'        => array(
                'label' => $this->l('Shipping'),
                'href'  => 'shipping',
                'icon'  => 'truck',
            ),
            'payment'         => array(
                'label'   => $this->l('Payment'),
                'href'    => 'payment',
                'icon'    => 'credit-card',
                'sub_tab' => array(
                    'popup_window' => array(
                        'label' => $this->l('Popup window'),
                        'href'  => 'popup_window',
                        'icon'  => 'external-link',
                    ),
                    'pay_methods'  => array(
                        'label' => $this->l('Pay methods'),
                        'href'  => 'pay_methods',
                        'icon'  => 'credit-card',
                    ),
                    'ship_pay'     => array(
                        'label' => $this->l('Ship to Pay'),
                        'href'  => 'ship_pay',
                        'icon'  => 'truck',
                    ),
                ),
            ),
            'review'          => array(
                'label' => $this->l('Review'),
                'href'  => 'review',
                'icon'  => 'check',
            ),
            'theme'           => array(
                'label' => $this->l('Theme'),
                'href'  => 'theme',
                'icon'  => 'paint-brush',
            ),
            'required_fields' => array(
                'label' => $this->l('Fields register'),
                'href'  => 'required_fields',
                'icon'  => 'pencil-square-o',
            ),
            'fields_position' => array(
                'label' => $this->l('Fields position'),
                'href'  => 'fields_position',
                'icon'  => 'arrows',
            ),
            'social_login'    => array(
                'label'   => $this->l('Social login'),
                'href'    => 'social_login',
                'icon'    => 'share-alt',
                'sub_tab' => $this->getSocialSubTabs(),
            ),
            'information'   => array(
                'label'   => $this->l('Information'),
                'href'    => 'information',
                'icon'    => 'info'
            )
        );

        return $tabs;
    }

    private function getHelperForm()
    {
        $tabs = $this->getHelperTabs();

        $general       = $this->getGeneralForm();
        $register      = $this->getRegisterForm();
        $shipping      = $this->getShippingForm();
        $payment_popup = $this->getPaymentForm();
        $review        = $this->getReviewForm();
        $theme         = $this->getThemeForm();

        $fields_register = $this->getRequiredFieldsForm();
        $form            = array(
            'title' => $this->l('Menu'),
            'tabs'  => $tabs,
            'forms' => array(
                'general'         => $general,
                'register'        => $register,
                'shipping'        => $shipping,
                'popup_window'    => $payment_popup,
                'review'          => $review,
                'theme'           => $theme,
                'fields_register' => $fields_register,
            ),
        );

        return $form;
    }

    private function getPaymentModulesInstalled()
    {
        //get payments
        $hook_payment = 'Payment';
        $query        = 'SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\'';
        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query)) {
            $hook_payment = 'displayPayment';
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
			FROM `'._DB_PREFIX_.'module` m
			LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON (hm.`id_module` = m.`id_module`
                AND hm.id_shop='.(int) $this->context->shop->id.')
            LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
			INNER JOIN `'._DB_PREFIX_.'module_shop` ms ON (m.`id_module` = ms.`id_module`
                AND ms.id_shop='.(int) $this->context->shop->id.')
            WHERE h.`name` = \''.pSQL($hook_payment).'\'
		');

        if ($result) {
            foreach ($result as &$row) {
                $row['force_display'] = 0;
                $row['name_image'] = $row['name'].'.gif';
                $row['test_mode'] = 0;
                $row['test_ip'] = '';

                $id_payment = PaymentClass::getIdPaymentBy('name', $row['name']);

                if (!empty($id_payment)) {
                    $payment = new PaymentClass($id_payment);
                    if (Validate::isLoadedObject($payment)) {
                        $row['data']['title']       = $payment->title;
                        $row['data']['description'] = $payment->description;

                        $path_image = dirname(__FILE__).'/views/img/payments/'.$payment->name_image;
                        if (!empty($payment->name_image) && file_exists($path_image)) {
                            $row['name_image'] = $payment->name_image;
                        }

                        $row['force_display'] = $payment->force_display;
                        $row['test_mode'] = $payment->test_mode;
                        $row['test_ip'] = $payment->test_ip;

                        $payment->id_module = $row['id_module'];
                        $payment->update();
                    }
                }
            }
        } else {
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE '._DB_PREFIX_.'opc_payment');
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE '._DB_PREFIX_.'opc_payment_lang');
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE '._DB_PREFIX_.'opc_payment_shop');
        }

        return $result;
    }

    public function getTotalConnections($network = null)
    {
        $sql = new DbQuery();
        $sql->select('count(id)');
        $sql->from('opc_social_network');

        if (!is_null($network)) {
            $sql->where('network = \''.$network.'\'');
        }

        return (int)Db::getInstance()->getValue($sql);
    }

    public function getSocialData($social_login)
    {
        $colors = array(
            'facebook'  => '#5C7FC9',
            'google'    => '#DC3C2A',
            'paypal'    => '#0195DA',
            'biocryptology' => '#DCDCDA'
        );

        $social_data = array(
            'labels'    => array(),
            'data'      => array(),
            'backgroundColor'   => array(),
            'total_connections' => $this->getTotalConnections()
        );

        if (!$social_data['total_connections']) {
            return array();
        }

        if ($social_login) {
            foreach ($social_login as $key => $data) {
                $social_data['labels'][$key]    = $data->network;
                $social_data['data'][$key]      = $this->getTotalConnections($key);
                $social_data['backgroundColor'][$key] = $colors[$key];
            }
        }

        return $social_data;
    }

    public function saveSocialLogin()
    {
        $data            = Tools::getValue('data');
        $social_networks = Tools::jsonDecode($this->config_vars['OPC_SOCIAL_NETWORKS']);

        foreach ($data['values'] as $key => $value) {
            $social_networks->{$data['social_network']}->{$key} = trim($value);
        }

        Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($social_networks));

        return array(
            'message_code' => $this->core->CODE_SUCCESS,
            'message'      => $this->l('Social login data updated successful')
        );
    }

    public function getOptionsByField()
    {
        $id_field = Tools::getValue('id_field');
        $options  = FieldOptionClass::getOptionsByIdField($id_field);
        //return result
        return array('message_code' => $this->core->CODE_SUCCESS, 'options' => $options);
    }

    public function saveOptionsByField()
    {
        $id_field = Tools::getValue('id_field');
        $options  = Tools::getValue('options');

        if (!empty($options)) {
            foreach ($options as $option) {
                if (empty($option['id_option']) || (int) $option['id_option'] === 0) {
                    $option['id_option'] = null;
                }

                $field_option = new FieldOptionClass($option['id_option']);

                $is_description_value_empty = false;
                $description_value = array();
                foreach ($option['description'] as $description) {
                    if (empty($description['value'])
                        && $description['id_lang'] == Configuration::get('PS_LANG_DEFAULT')
                    ) {
                        $is_description_value_empty = true;
                    }
                    $description_value[$description['id_lang']] = $description['value'];
                }

                $field_option->id_field    = $id_field;
                $field_option->value       = $option['value'];
                $field_option->description = $description_value;

                if (!$is_description_value_empty) {
                    $field_option->save();
                } else {
                    return array(
                        'message_code' => $this->core->CODE_ERROR,
                        'message' => $this->l('The options must have a description at least in the default language.')
                    );
                }
            }
        }

        $options_to_remove = Tools::getValue('options_to_remove');
        if (!empty($options_to_remove)) {
            foreach ($options_to_remove as $option_to_remove) {
                $field_option = new FieldOptionClass($option_to_remove);
                $field_option->delete();
            }
        }

        //return result
        return array('message_code' => $this->core->CODE_SUCCESS, 'message' => $this->l('Options updated successful.'));
    }

    public function getFieldsByObject()
    {
        $object_name = Tools::getValue('object');
        $fields_db   = FieldClass::getAllFields(
            $this->cookie->id_lang,
            $this->context->shop->id,
            $object_name,
            null,
            null,
            null,
            null,
            true
        );
        $fields = array();
        foreach ($fields_db as $field) {
            $fields[] = array(
                'id_field'    => $field->id,
                'name'        => $field->name,
                'description' => $field->description,
            );
        }
        //return result
        return array('message_code' => $this->core->CODE_SUCCESS, 'fields' => $fields);
    }

    /**
     * Save field positions
     */
    public function saveFieldsPosition()
    {
        //update positions
        $positions = Tools::getValue('positions');
        if (is_array($positions) && count($positions)) {
            foreach ($positions as $row => $cols) {
                if (is_array($cols) && count($cols)) {
                    foreach ($cols as $col => $data) {
                        $field        = new FieldClass($data['id_field']);
                        $field->group = $data['group'];
                        $field->row   = $row;
                        $field->col   = $col;
                        $field->save();
                    }
                }
            }
        }
        //return result
        return array('message_code' => $this->core->CODE_SUCCESS, 'message' => $this->l('Positions updated successful.'));
    }

    /**
     * Toggle required fieldstatus.
     * @return type array
     */
    public function toggleActiveField()
    {
        if (Tools::isSubmit('id_field')) {
            $field_class = new FieldClass((int) Tools::getValue('id_field'));

            if (Validate::isLoadedObject($field_class)) {
                $field_class->active = !$field_class->active;

                if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $field_class->setFieldsToUpdate(array('active' => 1));
                }

                if ($field_class->update()) {
                    return array(
                        'message_code' => $this->core->CODE_SUCCESS,
                        'message'      => $this->l('Field updated successful.'),
                    );
                }
            }
        }

        return array(
            'message_code' => $this->core->CODE_ERROR,
            'message'      => $this->l('An error occurred while trying to update.')
        );
    }

    /**
     * Toggle required fieldstatus.
     * @return type array
     */
    public function toggleRequiredField()
    {
        if (Tools::isSubmit('id_field')) {
            $field_class = new FieldClass((int) Tools::getValue('id_field'));

            if (Validate::isLoadedObject($field_class)) {
                $field_class->required = !$field_class->required;

                if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $field_class->setFieldsToUpdate(array('required' => 1));
                }

                if ($field_class->update()) {
                    return array(
                        'message_code' => $this->core->CODE_SUCCESS,
                        'message'      => $this->l('Field updated successful.'),
                    );
                }
            }
        }

        return array(
            'message_code' => $this->core->CODE_ERROR,
            'message'      => $this->l('An error occurred while trying to update.')
        );
    }

    /**
     * Remove associations of shipment and payment, then will create again from data form.
     * @return type array
     */
    public function updateShipToPay()
    {
        if (!Tools::isSubmit('payment_carrier')) {
            return array(
                'message_code' => $this->core->CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to update.')
            );
        }

        $carriers = Tools::getValue('payment_carrier');

        //Reset table asociations
        $query  = 'DELETE FROM '._DB_PREFIX_.'opc_ship_to_pay WHERE id_shop = '.(int) $this->context->shop->id;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);

        //Create new asociations from form
        $error = false;
        if ($result) {
            foreach ($carriers as $carrier) {
                if (isset($carrier['payments']) && is_array($carrier['payments']) && count($carrier['payments'])) {
                    foreach ($carrier['payments'] as $id_payment) {
                        $values = array(
                            'id_carrier'        => (int)$carrier['id_carrier'],
                            'id_payment_module' => (int)$id_payment,
                            'id_shop'           => (int)$this->context->shop->id,
                        );

                        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(_DB_PREFIX_.'opc_ship_to_pay', $values, 'INSERT')) {
                            $error = true;
                        }
                    }
                }
            }
        }

        if (!$error) {
            return array(
                'message_code' => $this->core->CODE_SUCCESS,
                'message'      => $this->l('The associations are updated correctly.')
            );
        } else {
            return array(
                'message_code' => $this->core->CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to update.')
            );
        }
    }

    /**
     * Get data of carriers-payment asociation
     * @return type array
     */
    public function getAssociationsShipToPay()
    {
        $query  = new DbQuery();
        $query->from('opc_ship_to_pay');
        $query->where('id_shop = '.(int) $this->context->shop->id);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return array('message_code' => $this->core->CODE_SUCCESS, 'carriers' => $result);
    }

    /**
     * Sort fields.
     * @return type array
     */
    public function updateFieldsPosition()
    {
        if (!Tools::isSubmit('order_fields')) {
            return array('message_code' => $this->core->CODE_ERROR, 'message' => $this->l('Error to update fields position'));
        }

        $order_fields = Tools::getValue('order_fields');
        $position     = 1;
        $errors_field = array();
        $message_code = $this->core->CODE_SUCCESS;

        if (is_array($order_fields) && count($order_fields)) {
            foreach ($order_fields as $id_field) {
                if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(
                    _DB_PREFIX_.'opc_field',
                    array('position' => (int)$position),
                    'UPDATE',
                    'id_field = '.(int)$id_field
                )
                ) {
                    $field_class    = new FieldClass((int) $id_field);
                    $errors_field[] = $field_class->name;
                }
                $position++;
            }
        }

        $message = $this->l('Sort positions of fields has been updated successful');
        if (count($errors_field)) {
            $fields       = implode(', ', $errors_field);
            $message      = $this->l('Error to update position for field(s)').': '.$fields;
            $message_code = $this->core->CODE_ERROR;
        }

        return array(
            'message_code' => $message_code,
            'message'      => $message,
        );
    }

    public function removeField()
    {
        $id_field = (int) Tools::getValue('id_field', null);
        if (empty($id_field) || (int) $id_field === 0) {
            return array('message_code' => $this->core->CODE_ERROR, 'message' => $this->l('No field selected to remove.'));
        }

        $field_class = new FieldClass($id_field);
        if ((int) $field_class->is_custom === 0) {
            return array('message_code' => $this->core->CODE_ERROR, 'message' => $this->l('Cannot remove this field.'));
        }

        if (!$field_class->delete()) {
            return array(
                'message_code' => $this->core->CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to remove.')
            );
        }

        return array('message_code' => $this->core->CODE_SUCCESS, 'message' => $this->l('Field remove successful.'));
    }

    /**
     * Save the field data.
     * @return type array
     */
    public function updateField()
    {
        if (!Tools::isSubmit('id_field')) {
            return array(
                'message_code' => $this->core->CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to update.')
            );
        }

        $id_field = (int) Tools::getValue('id_field', null);
        if (empty($id_field) || (int) $id_field === 0) {
            $id_field = null;
        }

        $field_class = new FieldClass($id_field);

        $validate_object = false;
        if (is_null($id_field)) {
            $field_class->is_custom = true;
        } else {
            $validate_object = $field_class->object;
        }

        if (is_null($id_field)) {
            $field_class->is_custom = true;
        }

        $multishop_fields = array(
            'default_value' => 1,
            'required'      => 1,
            'active'        => 1
        );

        //only if field is custom can update data.
        if ($field_class->is_custom) {
            $field_class->name         = Tools::getValue('name');
            $field_class->object       = Tools::getValue('object');

            if (empty($id_field) && $field_class->is_custom && Validate::isLoadedObject(FieldClass::getField(
                $this->context->language->id,
                $this->context->shop->id,
                $field_class->object,
                $field_class->name
            ))) {
                return array(
                    'message_code' => $this->core->CODE_ERROR,
                    'message'      => $this->l('A field with this name already exists, try a different name')
                );
            }
            
            $field_class->type         = Tools::getValue('type');
            $field_class->size         = (int) Tools::getValue('size');
            $field_class->type_control = Tools::getValue('type_control');
            //shop
            $field_class->group        = $field_class->object;
            $field_class->row          = (int) FieldClass::getLastRowByGroup($field_class->group) + 1;
            $field_class->col          = 0;

            $multishop_fields['group'] = 1;
            $multishop_fields['row'] = 1;
            $multishop_fields['col'] = 1;
        }

        $array_description = array();
        $array_label = array();
        $descriptions  = Tools::getValue('description');
        $labels = Tools::getValue('label');
        
        foreach ($descriptions as $description) {
            $array_description[$description['id_lang']] = $description['description'];
            $multishop_fields['description'][$description['id_lang']] = 1;
        }
        foreach ($labels as $label) {
            $array_label[$label['id_lang']] = $label['description'];
            $multishop_fields['label'][$label['id_lang']] = 1;
        }

        $field_class->description = $array_description;
        $field_class->label = $array_label;

        $default_value = Tools::getValue('default_value');
//		if ($field_class->type == $this->globals->type->string)
//			$default_value = Tools::substr($default_value, 0, $field_class->size);

        $field_class->default_value = $default_value;
        $field_class->required      = (int) Tools::getValue('required');
        $field_class->active        = (int) Tools::getValue('active');

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $field_class->setFieldsToUpdate($multishop_fields);
        }

        if ($field_class->validateFieldsLang(false) && $field_class->save()) {
            $result = array(
                'message_code'  => $this->core->CODE_SUCCESS,
                'message'       => $this->l('The field was successfully updated.'),
                'description'   => $array_description[$this->cookie->id_lang],
                'default_value' => $field_class->default_value,
            );

            if (is_null($id_field)) {
                $result['id_field'] = $field_class->id;
            } else if ($validate_object !== false && $field_class->object !== $validate_object) {
                $result['refresh_position'] = true;
            }
        } else {
            $result = array(
                'message_code' => $this->core->CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to update.'),
            );
        }

        return $result;
    }

    /**
     *
     * @param string $name
     * @return type
     */
    public function uploadImage()
    {
        $errors    = array();

        $name         = Tools::getValue('name');
        $id_module    = Tools::getValue('id_module');
        $force_display = Tools::getValue('force_display');
        $payment_data = Tools::getValue('payment_data');
        $test_mode   = Tools::getValue('test_mode');
        $test_ip     = Tools::getValue('test_ip');

        $id_payment = PaymentClass::getPaymentByName($name);
        $payment    = new PaymentClass($id_payment);

        if (!Validate::isLoadedObject($payment)) {
            $payment->name = $name;
            $payment->name_image = $name.'.gif';
        }

        $payment->id_module = $id_module;
        $payment->force_display = $force_display;
        $payment->test_mode = $test_mode;
        $payment->test_ip = $test_ip;

        /* update payment image */
        if (count($_FILES)) {
            foreach ($_FILES as $payment_name => $file) {
                $payment_name = $payment_name;

                if (!isset($file['tmp_name']) || is_null($file['tmp_name']) || empty($file['tmp_name'])) {
                    $errors[] = $this->l('Cannot add file because it did not sent');
                }

                if (!ImageManager::isRealImage($file['tmp_name'], $file['type']) && $file['type'] != 'image/png' && $file['type'] != 'image/gif') {
                    $errors[] = $this->l('Image extension not allowed');
                }

                if (empty($errors)) {
                    $path = '';
                    $path_backup = '';
                    $extension = Tools::substr($file['type'], 6);

                    if (!empty($payment->name_image)) {
                        $path = dirname(__FILE__).'/views/img/payments/'.$payment->name_image;
                        $path_backup = $path.'.backup';

                        if (file_exists($path)) {
                            rename($path, $path_backup);
                        }
                    }

                    $payment->name_image = $payment->name.'.'.$extension;
                    $path = dirname(__FILE__).'/views/img/payments/'.$payment->name_image;

                    if (move_uploaded_file($file['tmp_name'], $path)) {
                        if (!empty($path_backup) && file_exists($path_backup)) {
                            unlink($path_backup);
                        }

                        $payment->save();
                    } else {
                        if (!empty($path_backup)) {
                            rename($path_backup, Tools::substr($path_backup, 0, Tools::strlen($path_backup) - 7));
                        }
                        $errors[] = $this->l('Cannot copy the file');
                    }
                }
            }
        }

        if (Tools::isSubmit('payment_data')) {
            //save description
            $payment_data = Tools::jsonDecode($payment_data);

            if (is_array($payment_data) && count($payment_data)) {
                $title       = array();
                $description = array();
                foreach ($payment_data as $data) {
                    $title[$data->id_lang]       = $data->title;
                    $description[$data->id_lang] = $data->description;
                }

                $payment->title       = $title;
                $payment->description = $description;

                if (!$payment->save()) {
                    $errors[] = $this->l('An error has ocurred while trying save');
                }
            }
        }

        if (!empty($errors)) {
            return array('message_code' => $this->core->CODE_ERROR, 'message' => implode(', ', $errors));
        } else {
            return array(
                'message_code' => $this->core->CODE_SUCCESS,
                'name_image' => count($_FILES) ? $payment->name_image : '',
                'message'      => $this->l('Payment configuration has been updated successfully.'),
            );
        }
    }

    /**
     * List of provider packs
     * @return type array
     */
    public function getRequiredFieldList()
    {
        //get content field list
        $content = FieldClass::getAllFields(null, null, null, null, null, array(), 'f.id_field');

        //se quitan del listado de campos los ID, pues no tiene sentido mostrarlos.
        foreach ($content as $i => $item) {
            if ($item->name == 'id') {
                unset($content[$i]);
            }
        }
        
        $actions = array(
            'edit'   => array(
                'action_class' => 'Fields',
                'class'        => 'has-action nohover',
                'icon'         => 'edit',
                'title'        => $this->l('Edit'),
                'tooltip'      => $this->l('Edit'),
            ),
            'remove' => array(
                'action_class' => 'Fields',
                'class'        => 'has-action nohover',
                'icon'         => 'times',
                'title'        => $this->l('Remove'),
                'tooltip'      => $this->l('Remove'),
                'condition'    => array(
                    'field'      => 'is_custom',
                    'comparator' => '1',
                ),
            ),
        );

        $headers  = array(
            'name'          => $this->l('Name'),
            'object'        => $this->l('Object'),
            'description'   => $this->l('Description'),
            'label'         => $this->l('Label'),
            'default_value' => $this->l('Default value'),
            'required'      => $this->l('Required'),
            'active'        => $this->l('Active'),
            'actions'       => $this->l('Actions'),
        );

        $truncate = array(
            'description' => 30,
            'label' => 30
        );

        //use array with action_class (optional for var) and action (action name) for custom actions.
        $status = array(
            'required' => array(
                'action_class' => 'Fields',
                'action'       => 'toggleRequired',
                'class'        => 'has-action',
            ),
            'active'   => array(
                'action_class' => 'Fields',
                'action'       => 'toggleActive',
                'class'        => 'has-action',
            ),
        );

        $color = array(
            array(
                'by'     => 'object',
                'colors' => array(
                    'customer' => 'primary',
                    'delivery' => 'success',
                    'invoice'  => 'warning',
                )
            )
        );

        return array(
            'message_code' => $this->core->CODE_SUCCESS,
            'content'      => $content,
            'table'        => 'table-required-fields',
            'color'        => $color,
            'headers'      => $headers,
            'actions'      => $actions,
            'truncate'     => $truncate,
            'status'       => $status,
            'prefix_row'   => 'field',
        );
    }

    public function getFieldsForFormModifier(&$params, $id, $object)
    {
        $id_lang    = $this->context->language->id;
        $fields     = FieldClass::getAllFields($id_lang, null, $object, false, true, array(), 'fs.group, fs.row, fs.col', true);

        if ($fields) {
            foreach ($fields as $field) {
                if (array_key_exists($field->name, $params['fields'][0]['form']['input']) || $field->object !== $object) {
                    continue;
                }

                if ($object === 'customer') {
                    $params['fields_value'][$field->name.($field->type_control === 'checkbox' ? '_' : '')] = FieldCustomerClass::getFieldValue($field->id, $id);
                } else {
                    $params['fields_value'][$field->name.($field->type_control === 'checkbox' ? '_' : '')] = FieldCustomerClass::getFieldValue($field->id, null, $id);
                }

                $params['fields'][0]['form']['input'][$field->name] = array(
                    'type'  => ($field->type_control === 'textbox') ? 'text' : $field->type_control,
                    'label' => $field->description,
                    'name'  => $field->name,
                    'col'   => 4,
                );

                if ($field->type_control === 'select') {
                    $params['fields'][0]['form']['input'][$field->name]['options'] = array(
                        'query' => $field->options['data'],
                        'id'    => 'value',
                        'name'  => 'description'
                    );
                } elseif ($field->type_control === 'textbox') {
                    $params['fields'][0]['form']['input'][$field->name]['maxlength'] = $field->size;
                } elseif ($field->type_control === 'checkbox') {
                    $params['fields'][0]['form']['input'][$field->name]['values'] = array(
                        'id'    => $field->name,
                        'name'  => $field->name,
                        'query' => ''
                    );
                }
            }
        }
    }

    public function hookActionAdminCustomersFormModifier($params)
    {
        $id_customer = Tools::getValue('id_customer');
        $this->getFieldsForFormModifier($params, $id_customer, 'customer');
    }

    public function hookActionAdminAddressesFormModifier($params)
    {
        $id_address = Tools::getValue('id_address');
        $this->getFieldsForFormModifier($params, $id_address, 'delivery');
        $this->getFieldsForFormModifier($params, $id_address, 'invoice');
    }

    public function hookActionObjectAddressUpdateAfter()
    {
        if ($this->context->controller->controller_type !== 'admin') {
            return false;
        }

        $custom_fields  = array();
        $fields         = FieldClass::getAllFields(null, null, null, false, true, array(), 'fs.group, fs.row, fs.col', true);

        if ($fields) {
            foreach ($fields as $field) {
                if ($field->object === 'customer' || in_array($field->name, $custom_fields)) {
                    continue;
                }

                $custom_fields[] = $field->name;

                if ($field->type_control === 'checkbox') {
                    $field->value = (Tools::getValue($field->name.'_') === 'on') ? 1 : 0;
                } else {
                    $field->value = Tools::getValue($field->name);
                }

//                if (!empty($field->value)) {
                    $this->saveCustomFields($field, Tools::getValue('id_address'));
//                }
            }
        }
    }

    public function hookActionObjectAddressAddAfter($params)
    {
        $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'product WHERE is_virtual = 1';
        $virtual_products = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'product WHERE is_virtual = 0';
        $no_virtual_products = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        $object_address = 'delivery';
        if ($virtual_products > $no_virtual_products) {
            $object_address = 'invoice';
        }

        $values_customer_address = array(
            'id_customer' => $params['object']->id_customer,
            'id_address' => $params['object']->id,
            'object' => $object_address
        );
        Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(
            _DB_PREFIX_.'opc_customer_address',
            $values_customer_address,
            'INSERT'
        );

        $this->hookActionObjectAddressUpdateAfter();
    }

    public function hookActionObjectAddressDeleteAfter($params)
    {
        $query = 'DELETE FROM '._DB_PREFIX_.'opc_customer_address WHERE id_address = '.(int)$params['object']->id;
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);
    }

    public function hookActionObjectCustomerAddAfter()
    {
        $this->hookActionObjectCustomerUpdateAfter();
    }

    public function hookActionObjectCustomerUpdateAfter()
    {
        if ($this->context->controller->controller_type !== 'admin') {
            return false;
        }

        $fields = FieldClass::getAllFields(null, null, 'customer', false, true, array(), 'fs.group, fs.row, fs.col', true);

        if ($fields) {
            foreach ($fields as $field) {
                if ($field->type_control === 'checkbox') {
                    $field->value = (Tools::getValue($field->name.'_') === 'on') ? 1 : 0;
                } else {
                    $field->value = Tools::getValue($field->name);
                }

//                if (!empty($field->value)) {
                    $this->saveCustomFields($field);
//                }
            }
        }
    }

    public function hookShoppingCart($params)
    {
        if (!count($params['products'])) {
            return '<script>$(function(){
                $(".ulEmptyCartWarning").show();
            });</script>';
        }
    }

    public function hookDisplayAdminHomeQuickLinks()
    {
        $tk = Tools::getAdminTokenLite('AdminModules');
        echo '<li id="onepagecheckoutps_block">
            <a  style="background:#F8F8F8 url(\'../modules/'.$this->name.'/logo.png\') no-repeat 50% 20px"
				href="index.php?controller=adminmodules&configure='.$this->name.'&token='.$tk.'">
                <h4>'.$this->l($this->displayName).'</h4>
            </a>
        </li>';
    }

    public function hookHeader()
    {
        if (!$this->core->isModuleActive($this->name) || !$this->core->isVisible()) {
            return;
        }

        $this->context->smarty->assign('onepagecheckoutps', $this);

        if (isset(Context::getContext()->controller->php_self)
            && Context::getContext()->controller->php_self == 'order-opc'
        ) {
            if (!$this->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC']
                || ($this->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC'] && Tools::getIsset('checkout'))
                || ($this->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC'] && Tools::getIsset('rc'))
            ) {
                if ($this->context->cart->nbProducts() <= 0) {
                    $this->context->cart->id_address_delivery = 0;
                    $this->context->cart->id_address_invoice = 0;
                    $this->context->cart->update();
                    $this->context->cart->setDeliveryOption(null);
                }

                $params_front = array(
                    'CONFIGS' => $this->config_vars,
                );

                //fix paypal 3.4.5
                if (Tools::getIsset('paypal_ec_canceled') && Tools::getValue('paypal_ec_canceled') == 1) {
                    $params_front['paypal_ec_canceled'] = true;
                }

                //support module: productcomposer - v1.8.4 - TuniSoft
                $productcomposer = $this->core->isModuleActive('productcomposer');
                if ($productcomposer) {
                    if (method_exists(ComposerTools, 'getSpecialProduct')) {
                        $params_front['pc_special'] = (int)ComposerTools::getSpecialProduct();
                    }
                }

                $this->smarty->assign('paramsFront', $params_front);
                $html = $this->display(__FILE__, 'views/templates/front/theme.tpl');

                //compatibilidad con modulo nacex
                $module_nacex = $this->core->isModuleActive('nacex');
                if ($module_nacex) {
                    if ($module_nacex->isRegisteredInHook('displayBeforeCarrier')) {
                        //elimina la posicion hook para poder utilizar las funciones modificadas en js del checkout.
                        $module_nacex->unregisterHook(Hook::getIdByName('displayBeforeCarrier'));
                    }
                }

                //compatibilidad con modulo mondialrelay
                $module_mondialrelay = $this->core->isModuleActive('mondialrelay');
                if ($module_mondialrelay) {
                    if ($module_mondialrelay->isRegisteredInHook('displayPaymentTop')) {
                        $module_mondialrelay->unregisterHook(Hook::getIdByName('displayPaymentTop'));
                    }
                }

                //compatibilidad con modulo soliberte
                /*$module_soliberte = $this->core->isModuleActive('soliberte');
                if ($module_soliberte) {
                    if ($module_soliberte->isRegisteredInHook('displayHeader')
                        && $this->config_vars['OPC_AUTOCOMPLETE_GOOGLE_ADDRESS']
                    ) {
                        //elimina la posicion hook para poder utilizar las funciones modificadas en js del checkout.
                        $module_soliberte->unregisterHook(Hook::getIdByName('displayHeader'));
                    }
                }*/

                //support module - moip - Moip Labs - v2.1
                $module_moip = $this->core->isModuleActive('moip');
                if ($module_moip) {
                    if (Configuration::get('MOIP_ENVIRONMENT') == 'producao') {
                        $script_moip = 'https://www.moip.com.br/transparente/MoipWidget-v2.js';
                    } else {
                        $script_moip = 'https://desenvolvedor.moip.com.br/sandbox/transparente/MoipWidget-v2.js';
                    }
                    array_push($this->context->controller->js_files, $script_moip);
                }

                //compatibilidad con modulo aupost
                $module_aupost = $this->core->isModuleActive('aupost');
                if ($module_aupost) {
                    if (method_exists($module_aupost, 'hookExtraCarrier')) {
                        $module_aupost->hookExtraCarrier();
                    }
                }

                //compatibilidad con modulo mercadopago
                $module_mercadopago = $this->core->isModuleActive('mercadopago');
                if ($module_mercadopago) {
                    $this->context->controller->js_files[] = 'https://secure.mlstatic.com/modules/javascript/analytics.js';
                }

                //compatibilidad con modulo asmparcelshop
                $module_asmparcelshop = $this->core->isModuleActive('asmparcelshop');
                if ($module_asmparcelshop) {
                    $this->context->controller->js_files[] = $module_asmparcelshop->_path.'views/js/AsmParcelShop.js';
                }

                $module_seur = $this->core->isModuleActive('seur');

                //support with other templates
                foreach ($this->context->controller->js_files as $key => $js) {
                    if (strpos($js, '/modules/tonythemesettings/js/bootstrap.js') !== false) {
                        unset($this->context->controller->js_files[$key]);
                    } elseif (strpos($js, '/modules/leotempcp/bootstrap/js/bootstrap.js') !== false) {
                        unset($this->context->controller->js_files[$key]);
                    } elseif (strpos($js, '/modules/leotempcp/bootstrap/js/bootstrap.min.js') !== false) {
                        unset($this->context->controller->js_files[$key]);
                    } elseif (strpos($js, '/theme717/js/source/bootstrap.js') !== false) {
                        unset($this->context->controller->js_files[$key]);
                    } elseif (strpos($js, '/modules/tinymcepro/js/accordion.js') !== false) {
                        unset($this->context->controller->js_files[$key]);
                    } elseif (strpos($js, '10-bootstrap.min.js') !== false) {
                        if (_THEME_NAME_ != 'zro13') {
                            unset($this->context->controller->js_files[$key]);
                        }
                    }

                    if ($module_seur) {
                        if ($js == 'http://maps.google.com/maps/api/js?sensor=false') {
                            unset($this->context->controller->js_files[$key]);
                        } elseif ($js == 'https://maps.google.com/maps/api/js?sensor=false') {
                            unset($this->context->controller->js_files[$key]);
                        }
                    }
                }

                return $html;
            }
        }
    }

    public function hookDisplayBeforeCarrier($params)
    {
        //		if (Module::isInstalled('deliverydays'))
//		{
//			$module = Module::getInstanceByName('deliverydays');
//
//			if (Validate::isLoadedObject($module) && $module->active)
//			{
//				$module->getConfig();
//
//				if ($module->config[$module->shortName.'_days']) return $module->getCalendar($params['cart'], false);
//			}
//		}
    }

    public function hookActionShopDataDuplication($params)
    {
        $this->installLanguageShop($params['new_id_shop']);
    }

    public function hookDisplayAdminOrder($params)
    {
        $order = new Order($params['id_order']);

        $query = new DbQuery();
        $query->select('fc.value, fl.description field_description, fol.description option_description');
        $query->from('opc_field_customer', 'fc');
        $query->innerJoin('opc_field_lang', 'fl', 'fl.id_field = fc.id_field AND fl.id_lang = '.(int)$this->context->language->id);
        $query->leftJoin(
            'opc_field_option_lang',
            'fol',
            'fc.id_option = fol.id_field_option AND fol.id_lang = '.(int)$this->cookie->id_lang
        );
        $query->where('fc.id_customer = '.(int)$order->id_customer);
        $query->where('fc.id_address IN(0, '.(int)$order->id_address_delivery.', '.(int)$order->id_address_invoice.')');

        $field_options = Db::getInstance()->executeS($query);

        if (!count($field_options)) {
            return;
        }

        $this->smarty->assign(array(
            'field_options' => $field_options,
        ));

        return $this->display(__FILE__, 'views/templates/hook/order.tpl');
    }

    public function hookActionCarrierUpdate($params)
    {
        $id_carrier_old = $params['id_carrier'];
        $id_carrier_new = $params['carrier']->id;

        Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(
            _DB_PREFIX_.'opc_ship_to_pay',
            array('id_carrier' => (int)$id_carrier_new),
            'UPDATE',
            'id_carrier = '.(int)$id_carrier_old
        );
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (Validate::isLoadedObject($customer)) {
            $sql = "DELETE FROM "._DB_PREFIX_."opc_social_network WHERE id_customer = '".(int)$customer->id."'";
            if (Db::getInstance()->execute($sql)) {
                return Tools::jsonEncode(true);
            }
            return Tools::jsonEncode($this->l('One Page Checkout PS: Unable to erase the token when the client was registered by social networks.'));
        }
    }

    public function hookActionCustomerLogoutAfter($params)
    {
        if (isset($params['customer']) && !$params['customer']->isGuest()) {
            $opc_social_networks = $this->config_vars['OPC_SOCIAL_NETWORKS'];
            $opc_social_networks = Tools::jsonDecode($opc_social_networks);
            if ($opc_social_networks) {
                foreach ($opc_social_networks as $network) {
                    if ($network->enable == 1) {
                        $client = $this->getInstanceOauthClient($network->network);
                        if (($success = $client->Initialize())) {
                            if (($success = $client->Process())) {
                                $client->ResetAccessToken();
                                $client->Finalize($success);
                            }
                        }
                    }
                }
            }
        }
    }

    public function getMessageError($code_error)
    {
        $errors = array(
            0 => $this->l('I want to configure a custom password.'),
            1 => $this->l('Create an account and enjoy the benefits of a registered customer.'),
            2 => $this->l('Confirm password'),
            3 => $this->l('Confirm email'),
            4 => $this->l('Are you?'),
            5 => $this->l('Current Password'),
            6 => $this->l('I want to change my password')
        );

        if (key_exists($code_error, $errors)) {
            return $errors[$code_error];
        }

        return '';
    }

    /**
     * Return the content cms request.
     *
     * @return content html cms
     */
    public function loadCMS()
    {
        $html   = '';
        $id_cms = Tools::getValue('id_cms', '');

        $cms = new CMS($id_cms, $this->context->language->id);
        if (Validate::isLoadedObject($cms)) {
            $html = $cms->content;
        }

        return $html;
    }

    private function saveCustomFields(FieldClass $field, $id_address = null)
    {
        if ($this->context->controller->controller_type === 'front') {
            $id_customer    = $this->context->customer->id;
        } else {
            $id_customer    = Tools::getValue('id_customer');
        }

        if ($id_customer > 0) {
            $sql = new DbQuery();
            $sql->select('count(`id_field`)');
            $sql->from(FieldCustomerClass::$definition['table']);
            $sql->where('id_field = '.(int)$field->id);
            $sql->where('id_customer = '.(int)$id_customer);
            $sql->where('object =  \''.pSQL($field->object).'\'');

            if ($field->object !== 'customer') {
                $sql->where('id_address = '.(int)$id_address);
            }
            $result = Db::getInstance()->getValue($sql);

            $data = array(
                'id_option' => (int)FieldOptionClass::getIdOptionByIdFieldAndValue($field->id, $field->value),
                'value'     => pSQL($field->value),
                'date_upd'  => date('Y-m-d H:i:s')
            );

            if ($result) {
                $where = 'id_field = '.(int)$field->id.' AND id_customer = '.(int)$id_customer.' AND object = \''.pSQL($field->object).'\'';

                if ($field->object !== 'customer') {
                    $where .= ' AND id_address = '.(int)$id_address;
                }
                
                FieldCustomerClass::updateData($data, $where);
            } else {
                $data['id_field']       = (int)$field->id;
                $data['id_customer']    = $id_customer;
                $data['object']         = $field->object;

                if ($field->object !== 'customer') {
                    $data['id_address']     = $id_address;
                }
                
                FieldCustomerClass::insertData($data);
            }
        }
    }

    public function validateFields(
        $fields,
        &$customer,
        &$address_delivery,
        &$address_invoice,
        &$password,
        &$is_set_invoice,
        &$custom_fields = null
    ) {
        $fields_by_object = array();
        foreach ($fields as $field) {
            if ($field->name == 'id') {
                continue;
            }

            //Capitalize campos seleccionados.
            if (in_array($field->name, $this->fields_to_capitalize) && $this->config_vars['OPC_CAPITALIZE_FIELDS']) {
                $field->value = ucwords($field->value);
            }

            $field_db = FieldClass::getField(
                $this->context->language->id,
                $this->context->shop->id,
                $field->object,
                $field->name
            );

            if ($field_db) {
                $field_db->value = $field->value;
                $fields_by_object[$field->object][$field->name] = $field_db;

                //if custom, save options
                if ($field_db->is_custom) {
                    if (is_array($custom_fields)) {
                        $custom_fields[] = $field_db;
                    }
//                    $this->saveCustomFields($field_db);
                }
            }
        }
        
        foreach ($fields_by_object as $name_object => $fields) {
            if ($name_object == $this->globals->object->customer) {
                if (empty($customer)) {
                    $customer = new Customer();
                }

                $this->addFieldsRequired($fields, $name_object, $customer);
                $this->validateFieldsCustomer($fields, $customer, $password);
            } elseif ($name_object == $this->globals->object->delivery) {
                if (empty($address_delivery)) {
                    $address_delivery = new Address();
                }

                $this->addFieldsRequired($fields, $name_object, $address_delivery);
                $this->validateFieldsAddress($fields, $address_delivery);
            } elseif ($name_object == $this->globals->object->invoice) {
                if (empty($address_invoice)) {
                    $address_invoice = new Address();
                }

                $this->addFieldsRequired($fields, $name_object, $address_invoice);
                $this->validateFieldsAddress($fields, $address_invoice);

                $is_set_invoice = true;
            }
        }
    }

    public function createCustomerAjax()
    {
        $customer = null;
        $password = null;
        $results = array();
        $address_delivery = null;
        $address_invoice = null;
        $custom_fields = array();

        $fields = Tools::jsonDecode(Tools::getValue('fields_opc'));
        $is_set_invoice = (bool)Tools::getValue('is_set_invoice');

//        $id_address_delivery = (int)Tools::getValue('id_address_delivery');
//        $id_address_invoice  = (int)Tools::getValue('id_address_invoice');
//        if (empty($id_address_delivery)) {
//            $id_address_delivery = $this->context->cart->id_address_delivery;
//
//            if (empty($id_address_delivery)) {
//                $id_address_delivery = $this->getIdAddressAvailable('delivery');
//            }
//
//            $address_delivery = new Address($id_address_delivery);
//        }
//        if (empty($id_address_invoice)) {
//            $id_address_invoice = $this->context->cart->id_address_invoice;
//
//            if (empty($id_address_invoice)) {
//                $id_address_invoice = $this->getIdAddressAvailable('invoice');
//            }
//
//            $address_invoice  = $is_set_invoice ? new Address($id_address_invoice) : null;
//        }

        $this->validateFields($fields, $customer, $address_delivery, $address_invoice, $password, $is_set_invoice, $custom_fields);
        if (is_array($this->errors) && !count($this->errors)) {
            $this->createCustomer($customer, $address_delivery, $address_invoice, $password, $is_set_invoice);
            if (is_array($this->errors) && !count($this->errors)) {
                if (count($custom_fields) > 0) {
                    foreach ($custom_fields as $custom_field) {
                        $id_address = null;

                        if ($custom_field->object === 'delivery') {
                            $id_address = $address_delivery->id;
                        } elseif ($custom_field->object === 'invoice') {
                            $id_address = $address_invoice->id;
                        }

                        $this->saveCustomFields($custom_field, $id_address);
                    }
                }

                $results = array(
                    'isSaved'             => true,
                    'isGuest'             => $customer->is_guest,
                    'id_customer'         => (int) $customer->id,
                    'id_address_delivery' => !empty($address_delivery) ? $address_delivery->id : '',
                    'id_address_invoice'  => !empty($address_invoice) ? $address_invoice->id : '',
                    'static_token'        => Tools::getToken(false),
                    'token'               => Tools::getToken(),
                );

                /* Compatibilidad módulo emailverificationopc - PresTeamShop */
                $emailverificationopc = $this->core->isModuleActive('emailverificationopc');
                if ($emailverificationopc) {
                    if (!$emailverificationopc->validateEmailVerifiedCustomer($customer, true)) {
                        $results['redirect'] = $this->context->link->getModuleLink(
                            'emailverificationopc',
                            'verifyemail',
                            array('check_account' => $customer->id, 'token' => Tools::encrypt('emailverificationopc/index'))
                        );
                    }
                }
            }
        }

        $results['hasError'] = !empty($this->errors);
        $results['errors']   = $this->errors;

        return $results;
    }

    /**
     * Create & login customer.
     *
     * @param object &$customer
     * @param object &$address_delivery
     * @param object &$address_invoice
     * @param string $password
     * @param boolean $is_set_invoice
     */
    public function createCustomer(&$customer, &$address_delivery, &$address_invoice, $password, $is_set_invoice)
    {
        //Support to module 'checkvat' v1.6 from coeos.pro
        $checkvat = $this->core->isModuleActive('checkvat');
        if ($checkvat) {
            $vat_number = '';
            
            if ($is_set_invoice && is_object($address_invoice)) {
                $vat_number = $address_invoice->vat_number;
            } else if (is_object($address_delivery)) {
                $vat_number = $address_delivery->vat_number;
            }
            
            $_POST['vat_number'] = $vat_number;
            $_POST['submitAccount'] = true;
        }
        
        Hook::exec('actionBeforeSubmitAccount');

        if (count($this->context->controller->errors)) {
            $this->errors = $this->context->controller->errors;
        }

        if (Customer::customerExists($customer->email)) {
            if ($this->config_vars['OPC_REQUIRED_LOGIN_CUSTOMER']) {
                $this->errors[] = sprintf(
                    $this->l('The email %s is already in our database. If the information is correct, please login.'),
                    '<b>'.$customer->email.'</b>'
                );
            }
        }

//        $emailverificationopc = $this->core->isModuleActive('emailverificationopc');
//        if ($emailverificationopc) {
//            $email_verified = $emailverificationopc->validateEmailVerifiedCustomer($customer, true);
//
//            if (!$email_verified) {
//                $this->warnings[] = $this->l('The customer was created properly but can not log in the store until you verify your email address in the link sent to your email.');
//            }
//        }

        if ($carrierpickupstore = $this->core->isModuleActive('carrierpickupstore')) {
            $id_carrier_cps = $carrierpickupstore->getCarrier();

            if ((int)$this->context->cart->id_carrier === (int)$id_carrier_cps) {
                $carrier = new Carrier($id_carrier_cps, $this->context->language->id);
                $address_delivery->alias = $carrier->name;
            }
        }

        if (!is_null($address_delivery)) {
            if ($this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL']
                || ($this->context->cart->nbProducts() > 0 && !$this->context->cart->isVirtualCart())
            ) {
                $country = new Country($address_delivery->id_country, Configuration::get('PS_LANG_DEFAULT'));
                if (!Validate::isLoadedObject($country)) {
                    $this->errors[] = $this->l('Country cannot be loaded.');
                } elseif ((int) $country->contains_states && !(int) $address_delivery->id_state) {
                    $this->errors[] = $this->l('This country requires you to chose a State.');
                }
            }
        }

        if (!is_null($address_invoice) && $is_set_invoice) {
            $country_invoice = new Country($address_invoice->id_country, Configuration::get('PS_LANG_DEFAULT'));
            if (!Validate::isLoadedObject($country_invoice)) {
                $this->errors[] = $this->l('Country cannot be loaded.');
            } elseif ($this->config_vars['OPC_ENABLE_INVOICE_ADDRESS']
                && $is_set_invoice
                && (int) $country_invoice->contains_states
                && !(int) $address_invoice->id_state
            ) {
                $this->errors[] = $this->l('This country requires you to chose a State.');
            }
        }

        $errors = $customer->validateFields(false, true);
        if ($errors !== true) {
            $this->errors[] = $errors;
        }

        if (is_array($this->errors) && !count($this->errors) && is_array($this->warnings) && !count($this->warnings)) {
            //newsletter
            $module_newsletter = $this->core->isModuleActive('blocknewsletter');
            if ($module_newsletter) {
                $exist_registered = false;
                if (version_compare(_PS_VERSION_, '1.6.1.2', '>=')
                    && method_exists($module_newsletter, 'isNewsletterRegistered')
                ) {
                    if ($module_newsletter->isNewsletterRegistered($customer->email) > 0) {
                        $exist_registered = true;
                    }
                }

                if ($customer->newsletter == 1 || $exist_registered) {
                    $customer->newsletter = true;
                    $customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
                    $customer->newsletter_date_add        = pSQL(date('Y-m-d H:i:s'));

                    $module_newsletter->confirmSubscription($customer->email);
                }
            }

            //New Guest customer
            if (Tools::getIsset('is_new_customer') && Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                $customer->is_guest = Tools::getValue('is_new_customer');
            }

            if ($this->config_vars['OPC_CHOICE_GROUP_CUSTOMER'] && Tools::getIsset('group_customer')) {
                $customer->id_default_group = (int) Tools::getValue('group_customer');
            }

            if (!$customer->add()) {
                $this->errors[] = $this->l('An error occurred while creating your account.');
            } else {
                $customer->cleanGroups();

                if ($this->config_vars['OPC_CHOICE_GROUP_CUSTOMER']
                    && Tools::getIsset('group_customer')
                    && $group_customer = Tools::getValue('group_customer', '')
                ) {
                    $customer->addGroups(array((int)$group_customer));
                }

                if (!$customer->is_guest) {
                    $customer->addGroups(array((int) $this->config_vars['OPC_DEFAULT_GROUP_CUSTOMER']));
                } else {
                    $customer->addGroups(array((int) Configuration::get('PS_GUEST_GROUP')));
                }

                //Registro de grupos adicionales a clientes nuevos.
                $groups_customer_additional = $this->config_vars['OPC_GROUPS_CUSTOMER_ADDITIONAL'];
                if (!empty($groups_customer_additional)) {
                    $groups_customer_additional = explode(',', $groups_customer_additional);
                    if (is_array($groups_customer_additional)) {
                        $customer->addGroups($groups_customer_additional);
                    }
                }

                if (!is_null($address_delivery)) {
                    $address_delivery->id_customer = (int) $customer->id;

                    if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA']) {
                        $address_delivery->firstname = $customer->firstname;
                        $address_delivery->lastname  = $customer->lastname;
                    }

                    if (!$address_delivery->save()) {
                        $this->errors[] = $this->l('An error occurred while creating your delivery address.');
                    }
                }

                if (!is_null($address_invoice) && $is_set_invoice) {
                    if (empty($address_invoice->id_customer) ||
                        $address_invoice->id_customer == $this->config_vars['OPC_ID_CUSTOMER']
                    ) {
                        $address_invoice->id_customer = $customer->id;
                    }

                    if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA']) {
                        $address_invoice->firstname = $customer->firstname;
                        $address_invoice->lastname  = $customer->lastname;
                    }

                    if (!$address_invoice->save()) {
                        $this->errors[] = $this->l('An error occurred while creating your billing address.');
                    }

                    if (is_null($address_delivery)) {
                        $address_delivery = $address_invoice;
                    }
                }

                if (is_array($this->errors) && !count($this->errors)) {
                    if (!$customer->is_guest) {
                        $this->sendConfirmationMail($customer, $password);
                    }

                    $log_in = true;
                    
                    /* Compatibilidad módulo emailverificationopc - PresTeamShop */
                    $emailverificationopc = $this->core->isModuleActive('emailverificationopc');
                    if ($emailverificationopc) {
                        if (Tools::getIsset('sv')) {
                            Db::getInstance()->update(
                                'customer',
                                array('email_verified' => 1),
                                'id_customer='.(int)$customer->id
                            );
                        }

                        $log_in = $emailverificationopc->validateEmailVerifiedCustomer($customer, false);
                    }

                    if ($log_in) {
                        //loggin customer
                        $this->context->cookie->id_customer        = (int) $customer->id;
                        $this->context->cookie->customer_lastname  = $customer->lastname;
                        $this->context->cookie->customer_firstname = $customer->firstname;
                        $this->context->cookie->logged             = 1;
                        $customer->logged                          = 1;
                        $this->context->cookie->is_guest           = $customer->isGuest();
                        $this->context->cookie->passwd             = $customer->passwd;
                        $this->context->cookie->email              = $customer->email;

                        // Add customer to the context
                        $this->context->customer = $customer;

                        if (Configuration::get('PS_CART_FOLLOWING')
                            && (empty($this->context->cookie->id_cart)
                            || Cart::getNbProducts($this->context->cookie->id_cart) == 0)
                        ) {
                            $this->context->cookie->id_cart = (int) Cart::lastNoneOrderedCart($this->context->customer->id);
                        }

                        // Update cart address
                        $this->context->cart->id_customer         = (int) $customer->id;
                        $this->context->cart->secure_key          = $customer->secure_key;
                    }

                    if (!$is_set_invoice) {
                        if (!is_null($address_delivery)) {
                            if ($log_in) {
                                $this->context->cart->id_address_delivery = $address_delivery->id;
                                $this->context->cart->id_address_invoice = $address_delivery->id;
                            }

                            $this->updateCustomerAddress($customer->id, $address_delivery->id, 'delivery');
                        }
                    } else {
                        if (!is_null($address_delivery)) {
                            if ($log_in) {
                                $this->context->cart->id_address_delivery = $address_delivery->id;
                            }
                            $this->updateCustomerAddress($customer->id, $address_delivery->id, 'delivery');
                        }
                        
                        if (!is_null($address_invoice)) {
                            if ($log_in) {
                                $this->context->cart->id_address_invoice = $address_invoice->id;
                            }
                            $this->updateCustomerAddress($customer->id, $address_invoice->id, 'invoice');
                        }
                    }

                    if ($log_in) {
                        $this->context->cart->update();
                        $this->context->cart->setNoMultishipping();

                        $this->context->cookie->id_cart = (int) $this->context->cart->id;
                        $this->context->cookie->write();
                    }

                    if (!is_null($address_delivery)) {
                        $array_post = array_merge((array) $customer, (array) $address_delivery);
                    } else {
                        $array_post = (array) $customer;
                    }

                    foreach ($array_post as $key => $value) {
                        $_POST[$key] = $value;
                    }

                    $recargoequivalencia = $this->core->isModuleActive('recargoequivalencia');
                    if ($recargoequivalencia) {
                        if (array_key_exists('chkRecargoEquivalencia', $_POST)) {
                            $chkRecargoEquivalencia = Tools::getValue('chkRecargoEquivalencia');
                            if (empty($chkRecargoEquivalencia)) {
                                unset($_POST['chkRecargoEquivalencia']);
                            }
                        }
                    }

                    Hook::exec('actionCustomerAccountAdd', array(
                        '_POST'       => $_POST,
                        'newCustomer' => $customer,
                    ));
                }
            }
        }
    }

    /**
     * sendConfirmationMail
     * @param Customer $customer
     * @return bool
     */
    protected function sendConfirmationMail(Customer $customer, $password)
    {
        if (Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            Mail::Send(
                $this->context->language->id,
                'account',
                $this->l('Welcome!'),
                array('{firstname}' => $customer->firstname,
                    '{lastname}'  => $customer->lastname,
                    '{email}'     => $customer->email,
                    '{passwd}'    => $password
                ),
                $customer->email,
                $customer->firstname.' '.$customer->lastname
            );
        }
    }

    /**
     * Sing in customer
     *
     * @param object $customer
     */
    public function singInCustomer($customer)
    {
        if (Validate::isLoadedObject($customer)) {
            $emailverificationopc = $this->core->isModuleActive('emailverificationopc');
            if ($emailverificationopc) {
                $email_verified = $emailverificationopc->validateEmailVerifiedCustomer($customer);

                if (!$email_verified) {
                    $this->errors[] = sprintf(
                        $this->l('To sign in the store must verify your email address on the link sent to %s'),
                        $customer->email
                    );

                    return false;
                }
            }

            $this->context->cookie->id_compare         = isset($this->context->cookie->id_compare) ?
                $this->context->cookie->id_compare : CompareProduct::getIdCompareByIdCustomer($customer->id);
            $this->context->cookie->id_customer        = (int) $customer->id;
            $this->context->cookie->customer_lastname  = $customer->lastname;
            $this->context->cookie->customer_firstname = $customer->firstname;
            $this->context->cookie->logged             = 1;
            $customer->logged                          = 1;
            $this->context->cookie->is_guest           = $customer->isGuest();
            $this->context->cookie->passwd             = $customer->passwd;
            $this->context->cookie->email              = $customer->email;

            // Add customer to the context
            $this->context->customer = $customer;

            if (isset($this->context->cart) && is_object($this->context->cart)) {
                $id_cart = (int) Cart::lastNoneOrderedCart($this->context->customer->id);
                $nb_products = Cart::getNbProducts($this->context->cookie->id_cart);

                if (Configuration::get('PS_CART_FOLLOWING')
                    && $id_cart
                    && (empty($this->context->cookie->id_cart) ||
                        $nb_products == 0)
                ) {
                    $this->context->cart = new Cart($id_cart);
                } else {
                    $id_carrier                               = $this->context->cart->id_carrier;
                    $this->context->cart->id_carrier          = 0;
                    $this->context->cart->setDeliveryOption(null);
                    $this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int) $customer->id);
                    $this->context->cart->id_address_invoice  = Address::getFirstCustomerAddressId((int) $customer->id);
                }
                $this->context->cart->id_customer = (int) $customer->id;
                $this->context->cart->secure_key  = $customer->secure_key;

                if (isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                    $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
                    $this->context->cart->setDeliveryOption($delivery_option);
                }

                $this->context->cart->save();
                $this->context->cart->setNoMultishipping();
                
                $this->context->cookie->id_cart = (int) $this->context->cart->id;
                $this->context->cookie->write();
            }
            
            Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

            // Login information have changed, so we check if the cart rules still apply
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);

            return true;
        }

        return false;
    }

    /**
     * Check the email and password sent, then sing in customer
     *
     * @return array (boolean success, array errors)
     */
    public function loginCustomer()
    {
        $email    = Tools::getValue('email', '');
        $password = Tools::getValue('password', '');

        $is_logged = false;

        Hook::exec('actionBeforeAuthentication');

        if (!Validate::isEmail($email)) {
            $this->errors[] = $this->l('The email no is valid.');
        } elseif (!Validate::isPasswd($password)) {
            $this->l('The password no is valid.');
        } else {
            $customer = new Customer();

            $authentication = $customer->getByEmail(trim($email), trim($password));
            if (!$authentication || !$customer->id) {
                $this->errors[] = $this->l('The email or password is incorrect. Verify your information and try again.');
            } else {
                if (is_array($this->errors) && count($this->errors) == 0) {
                    $is_logged = $this->singInCustomer($customer);
                }
            }
        }

        $results = array(
            'success' => $is_logged,
            'errors'  => $this->errors,
        );

        return $results;
    }

    /**
     * Return customer addresses.
     *
     * @return array Addresses
     */
    public function getAddresses($id_customer, $object)
    {
        $id_lang = $this->context->language->id;
        $shareOrder = (bool)$this->context->shop->getGroup()->share_order;
        $sql = 'SELECT DISTINCT a.*, cl.`name` AS country, s.name AS state, s.iso_code AS state_iso
            FROM `'._DB_PREFIX_.'address` a
            LEFT JOIN `'._DB_PREFIX_.'opc_customer_address` ca ON
                (a.`id_address` = ca.`id_address` AND
                    a.`id_customer` = ca.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'country` c ON (a.`id_country` = c.`id_country`)
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
            LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`id_state`)
            '.($shareOrder ? '' : Shop::addSqlAssociation('country', 'c')).'
            WHERE
                `id_lang` = '.(int) $id_lang.' AND
                a.`id_customer` = '.(int)$id_customer.' AND
                a.`deleted` = 0 AND
                (ca.`object` = "'.pSQL($object).'" OR ca.`object` IS NULL)
            ORDER BY a.date_add DESC
            ';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $result;
    }

    private function updateCustomerAddress($id_customer, $id_address, $object)
    {
        $query = 'UPDATE '._DB_PREFIX_.'opc_customer_address '
            . 'SET id_customer = '.(int)$id_customer.', `object` = "'.pSQL($object).'" '
            .' WHERE id_address = '.(int)$id_address;
        Db::getInstance()->execute($query);
    }

    /**
     * Return the address of customer logged.
     *
     * @return array (id_address_delivery, id_address_invoice, addresses)
     */
    public function loadAddressesCustomer()
    {
        $object = Tools::getValue('object');
        $rc_page = Tools::getValue('rc_page');

        $id_address = (int)$this->context->cart->id_address_delivery;

        $addresses = array();
        if (Validate::isLoadedObject($this->context->customer) && !empty($this->context->customer->id)) {
            $addresses = $this->getAddresses($this->context->customer->id, $object);

            if ($object == 'invoice' && $addresses) {
                $update_address_invoice = true;
                foreach ($addresses as $address) {
                    $id_address = (int)$address['id_address'];

                    if ($id_address == $this->context->cart->id_address_invoice) {
                        $id_address = (int)$this->context->cart->id_address_invoice;
                        $update_address_invoice = false;
                        break;
                    }
                }

                if ($update_address_invoice) {
                    $this->context->cart->id_address_invoice = $id_address;
                    $this->context->cart->update();
                }

                /*if ($this->context->cart->id_address_delivery == $this->context->cart->id_address_invoice
                    && (!$this->context->cart->isVirtualCart() || $this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'])
                ) {
                    $id_address = 0;
                } else {
                    $id_address = (int)$this->context->cart->id_address_invoice;
                }*/
            }
        }

        if ($rc_page == 'address') {
            $id_address = 0;
        }

        $params = array(
            'id_address' => $id_address
        );

        if (count($addresses) > 0) {
            $params['addresses'] = $addresses;
        }

        $this->context->smarty->assign($params);

        if (file_exists(_PS_THEME_DIR_.'modules/onepagecheckoutps/views/templates/front/addresses.tpl')) {
            $html = $this->context->smarty->fetch(_PS_THEME_DIR_.'/modules/onepagecheckoutps/views/templates/front/addresses.tpl');
        } else {
            $html = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/addresses.tpl');
        }

        $params['html'] = $html;

        return $params;
    }

    /**
     * Re-use the address already created without a real customer.
     *
     * @return integer Id address available
     */
    public function getIdAddressAvailable($object = 'delivery')
    {
        /*$query = new DbQuery();
        $query->select('a.id_address');
        $query->from('address', 'a');
        $query->leftJoin(
            'opc_customer_address',
            'ca',
            'a.id_address = ca.id_address AND a.id_customer = ca.id_customer'
        );
        $query->where('a.id_customer = '.(int)$this->config_vars['OPC_ID_CUSTOMER']);
        $query->where('a.id_address NOT IN (SELECT id_address_delivery AS id_address FROM '._DB_PREFIX_.'cart WHERE id_address_delivery != 0 UNION SELECT id_address_invoice AS id_address FROM '._DB_PREFIX_.'cart WHERE id_address_delivery != 0)');
        $query->where('ca.object = "'.pSQL($object).'"');
        $query->where('a.deleted = 0');
        $query->where('a.active = 1');

        $id_address = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        if (!empty($id_address)) {
            if ($this->context->customer->isLogged()) {
                $values = array('id_customer' => (int)$this->context->customer->id);
                $where = 'id_address = '.(int)$id_address;

                Db::getInstance(_PS_USE_SQL_SLAVE_)->update('address', $values, $where);
            }
        } else {
            $id_address = $this->createAddress($object);
        }

        return $id_address;*/

        return $this->createAddress($object);
    }

    public function checkVATNumber()
    {
        $errors = array();
        $vat_number = Tools::getValue('vat_number', '');
        $id_address = $this->context->cart->id_address_delivery;

        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $id_address = $this->context->cart->id_address_invoice;
        }

        if (!empty($vat_number)) {
            $found_module = false;
            $address = new Address($id_address);

            $imaxvies = $this->core->isModuleActive('imaxvies');
            if ($imaxvies) {
                $found_module = true;
                if (Validate::isLoadedObject($address)) {
                    $iso_code = Country::getIsoById($address->id_country);
                    $resultadoVat = $imaxvies->checkVatNumber($iso_code, $vat_number);
                    if ($resultadoVat && !$resultadoVat->valid) {
                        $errors = array($this->l('The VAT number is not correct.'));
                    };
                }
            }

            $vatnumbercleaner = $this->core->isModuleActive('vatnumbercleaner');//v1.2.8 - MassonVincent
            if ($vatnumbercleaner) {
                $found_module = true;
                $verifications = $vatnumbercleaner->verificationsIdAddress(
                    $id_address,
                    $vat_number,
                    $address->id_country
                );
                if ($verifications['id_msg'] > 0) {
                    $errors = array($vatnumbercleaner->msgerror($verifications['id_msg']));
                }
            }

            $validatevatnumber = $this->core->isModuleActive('validatevatnumber');//v2.1.7 - ActiveDesign
            if ($validatevatnumber) {
                $found_module = true;
                $manual_mode = (int)Configuration::get('VALIDATEVATNUMBER_MANUAL_MODE');
                if ($manual_mode != 1) {
                    $result = $validatevatnumber->verifyCountryAndVat($address->id_country, $vat_number);
                    if ($result === true) {
                        $result = $validatevatnumber->verifyVatNumberOnline($vat_number);
                        if ($result !== true) {
                            $errors = array($result);
                        }
                    } else {
                        $errors = array($result);
                    }
                }
            }

            if (!$found_module && Configuration::get('VATNUMBER_MANAGEMENT')) {
                include_once _PS_MODULE_DIR_.'vatnumber/vatnumber.php';
                if (class_exists('VatNumber', false) && Configuration::get('VATNUMBER_CHECKING')) {
                    $errors = VatNumber::WebServiceCheck($vat_number);
                }
            }
        }
        
        if (empty($errors)) {
            if (!empty($id_address)) {
                $address = new Address($id_address);
                $address->vat_number = $vat_number;
                $address->save();
            }
        }

        return $errors;
    }

    /**
     * Create address with default values.
     *
     * @param int $id_customer
     * @return int id address created.
     */
    public function createAddress($object = 'delivery')
    {
        $values = array(
            'firstname'  => pSQL(FieldClass::getDefaultValue($object, 'firstname')),
            'lastname'   => pSQL(FieldClass::getDefaultValue($object, 'lastname')),
            'address1'   => pSQL(FieldClass::getDefaultValue($object, 'address1')),
            'city'       => pSQL(FieldClass::getDefaultValue($object, 'city')),
            'postcode'   => pSQL(FieldClass::getDefaultValue($object, 'postcode')),
            'id_country' => (int)FieldClass::getDefaultValue($object, 'id_country'),
            'id_state'   => (int)FieldClass::getDefaultValue($object, 'id_state'),
            'alias'      => pSQL(FieldClass::getDefaultValue($object, 'alias')),
            'date_add'   => date('Y-m-d H:i:s'),
            'date_upd'   => date('Y-m-d H:i:s')
        );

        $address            = new Address();
        $fields_db_required = $address->getFieldsRequiredDatabase();
        foreach ($fields_db_required as $field) {
            $values[$field['field_name']] = pSQL(FieldClass::getDefaultValue($object, $field['field_name']));
        }

        if (empty($values['id_country'])) {
            //$values['id_country'] = (int)Configuration::get('PS_COUNTRY_DEFAULT');

            if (!$this->context->customer->isLogged() && (Configuration::get('PS_GEOLOCATION_ENABLED') || Configuration::get('PS_DETECT_COUNTRY'))) {
                if ($this->context->country->active) {
                    $values['id_country'] = $this->context->country->id;
                }
            }
        }

        if (!empty($values['id_country'])) {
            $field_state = FieldClass::getField($this->context->cookie->id_lang, $this->context->shop->id, $object, 'id_state');
            if ($field_state->active == '0') {
                if (Country::containsStates((int) $values['id_country'])) {
                    $states = State::getStatesByIdCountry((int) $values['id_country']);
                    if (count($states)) {
                        $values['id_state'] = (int)$states[0]['id_state'];
                    }
                }
            }

            if (empty($values['postcode'])) {
                $country = new Country((int) $values['id_country']);
                if (Validate::isLoadedObject($country)) {
                    $values['postcode'] = str_replace(
                        'C',
                        $country->iso_code,
                        str_replace(
                            'N',
                            '0',
                            str_replace(
                                'L',
                                'A',
                                $country->zip_code_format
                            )
                        )
                    );
                }
            }
        }

        /*if (Module::isInstalled('cpfmodule')) {
            $module = Module::getInstanceByName('cpfmodule');
            if ($module->active) {
                $values['number'] = '.';
            }
        }*/

        if ($this->context->customer->isLogged() || $this->context->customer->isGuest()) {
            if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA'] && $object == 'delivery') {
                $values['firstname'] = pSQL($this->context->customer->firstname);
                $values['lastname']  = pSQL($this->context->customer->lastname);
            }

            if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA'] && $object == 'invoice') {
                $values['firstname'] = pSQL($this->context->customer->firstname);
                $values['lastname']  = pSQL($this->context->customer->lastname);
            }

            $values['id_customer'] = (int)$this->context->customer->id;
        } else {
            $values['id_customer'] = (int)$this->config_vars['OPC_ID_CUSTOMER'];
        }

        Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(_DB_PREFIX_.'address', $values, 'INSERT');

        $id_address = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();

        $values_customer_address = array(
            'id_customer' => $values['id_customer'],
            'id_address' => $id_address,
            'object' => $object
        );
        Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(
            _DB_PREFIX_.'opc_customer_address',
            $values_customer_address,
            'INSERT'
        );

        return $id_address;
    }

    /**
     * Support to module 'deliverydays' v1.7.1.0 from samdha.net
     *
     * The method setDate is called from hook header
     */
    public function supportModuleDeliveryDays()
    {
        $module = $this->core->isModuleActive('deliverydays', 'setDate');
        if ($module) {
            if (Tools::getIsset('deliverydays_day') || Tools::getIsset('deliverydays_timeframe')) {
                $module->setDate(
                    $this->context->cart,
                    Tools::getValue('deliverydays_day'),
                    Tools::getValue('deliverydays_timeframe')
                );
            }
        }
    }

    /**
     * Support to module 'cpfuser' v1.5 from Ederson Ferreira
     * Support to module 'djtalbrazilianregister' v1.0.3 from Djtal
     *
     * This code is set in the override AuthController
     */
    public function supportModuleCPFUser(&$customer)
    {
        $module = $this->core->isModuleActive('cpfuser');
        if ($module) {
            if (Tools::getValue('doc_type') == 1) {
                $post_doc = Tools::getValue('cnpj');
                $rg_id    = Tools::getValue('nie');
            } else {
                $post_doc = Tools::getValue('cpf');
                $rg_id    = Tools::getValue('rg');
            }

            $doc_number         = preg_replace('/[^0-9]/', '', $post_doc);
            $customer->document = $doc_number;

            $doc_rg_ie       = preg_replace('/[^0-9]/', '', $rg_id);
            $customer->rg_ie = $doc_rg_ie;

            $customer->doc_type = Tools::getValue('doc_type');

            if (Tools::getValue('validDoc') == 'false') {
                $this->errors[] = $this->l('Number of document invalid, please check.');
            }
        }

        $module = $this->core->isModuleActive('djtalbrazilianregister');
        if ($module && $this->context->customer->isLogged()) {
            $data = array(
                'id_customer' => (int)$customer->id
            );

            $doc_type = Tools::getValue('id_cp_mode', null);
            $doc_value = Tools::getValue('br_document', null);
            $br_document_cpf = Tools::getValue('br_document_cpf', null);
            $br_document_cnpj = Tools::getValue('br_document_cnpj', null);
            $rg = Tools::getValue('br_document_rg', null);
            $ie = Tools::getValue('br_document_ie', null);

            if (!is_null($doc_type) && !is_null($doc_value)) {
                $doc_value = preg_replace('/[^0-9]/', '', $doc_value);
                $data[pSQL($doc_type)] = pSQL($doc_value);
            }
            if (!is_null($br_document_cpf)) {
                $br_document_cpf = preg_replace('/[^0-9]/', '', $br_document_cpf);
                $data['cpf'] = pSQL($br_document_cpf);
            }
            if (!is_null($br_document_cnpj)) {
                $br_document_cnpj = preg_replace('/[^0-9]/', '', $br_document_cnpj);
                $data['cnpj'] = pSQL($br_document_cnpj);
            }
            if (!is_null($rg)) {
                $data['rg'] = pSQL($rg);
            }
            if (!is_null($ie)) {
                $data['ie'] = pSQL($ie);
            }

            $sql = 'SELECT * FROM '._DB_PREFIX_.'djtalbrazilianregister WHERE id_customer = '.(int)$customer->id;
            $exist_customer = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if ($exist_customer) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(_DB_PREFIX_.'djtalbrazilianregister', $data, 'UPDATE', 'id_customer = '.(int)$customer->id);
            } else {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(_DB_PREFIX_.'djtalbrazilianregister', $data, 'INSERT');
            }
        }
    }

    /**
     * Se colocan aca los modulos que requieren iniciar sesion primero antes de mostrar los demas pasos del checkout.
     */
    public function supportModulesRequiredAutentication()
    {
        if (!$this->context->customer->isLogged()) {
            $modules = array(
                //'bbcarrier', 'soliberte', 'pronesis_bancasella', 'checkvat',
                'kiala', 'imaxmailactivate', 'imaxvies', 'emailverificationopc'
            );

            foreach ($modules as $name_module) {
                if ($this->core->isModuleActive($name_module)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Support modules of shipping that use pick up.
     *
     * @param string $module
     * @param object &$carrier
     * @param boolean &$is_necessary_postcode
     * @param boolean &$is_necessary_city
     */
    private function supportModulesShipping($module, $address, &$carrier, &$is_necessary_postcode, &$is_necessary_city)
    {
        //remove message unused on validator prestashop.
        $address           = $address;
        $is_necessary_city = $is_necessary_city;
        $html = '';

        switch ($module) {
            case 'correos':
                $correos = $this->core->isModuleActive('correos');
                if ($correos) {
                    $html = '';

                    if (version_compare($correos->version, '3.0.0', '<=')) {
                        if (Configuration::get('CORREOS_RECOGIDA_CARRIER_ID') == $carrier['instance']->id
                            || Configuration::get('CORREOS_RECOGIDA48_CARRIER_ID') == $carrier['instance']->id
                            || Configuration::get('CORREOS_RECOGIDA72_CARRIER_ID') == $carrier['instance']->id
                        ) {
                            $query = new DbQuery();
                            $query->from('correos_recoger');
                            $query->where('cart_id = '.(int) $this->context->cart->id);

                            $info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
                            if ($info) {
                                if (!empty($info['info_oficina'])) {
                                    $info_detail = explode('|', $info['info_oficina']);

                                    $html .= $info_detail[0].'<br/>';
                                    $html .= $info_detail[1].'<br/>';
                                    $html .= $info_detail[2].' '.$info_detail[3];
                                }
                            }

                            $carrier['extra_info_carrier'] = $html;
                            $is_necessary_postcode         = true;
                        }
                    } elseif (version_compare($correos->version, '4.0.1', '>=')) {
                        //compatibilidad con version 4.0.4
                        require_once _PS_MODULE_DIR_.'/correos/classes/CorreosCommon.php';

                        $correos_carriers = CorreosCommon::getActiveCarriersByGroup();

                        if (in_array($carrier['instance']->id, $correos_carriers['carriers_office'])) {
                            $row = Db::getInstance()->getRow(
                                "SELECT `data` FROM `"._DB_PREFIX_."correos_request`
                                WHERE `id_cart` = ".(int) $this->context->cart->id." AND `id_carrier` IN (0, ".(int) $carrier['instance']->id.")"
                            );

                            if ($row) {
                                if (_PS_MAGIC_QUOTES_GPC_) {
                                    $row['data'] = str_replace("u00", "\u00", $row['data']);
                                }

                                $request_data = Tools::jsonDecode($row['data']);
                                $offices = $request_data->offices;

                                if ($offices) {
                                    foreach ($offices as $office) {
                                        if ($office->unidad == $request_data->id_collection_office) {
                                            $html .= $office->nombre.'<br/>';
                                            $html .= $office->direccion.'<br/>';
                                            $html .= $office->localidad.' '.$office->cp;

                                            continue;
                                        }
                                    }
                                }
                            }

                            $carrier['extra_info_carrier'] = $html;
                            $is_necessary_postcode         = true;
                        }
                    }

                    return true;
                }
                break;
            case 'kiala':
                if ($this->core->isModuleActive('kiala')) {
                    /*if (Configuration::get('_KIALA_CARRIER_ID_') == $carrier['instance']->id
                        || Configuration::get('KIALA_CARRIER_ID') == $carrier['instance']->id
                    ) {*/
                    $html = '';

                    $query = new DbQuery();
                    $query->from('cart_kiala');
                    $query->where('id_cart = '.(int) $this->context->cart->id);

                    $info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
                    if ($info) {
                        if (!empty($info['kpname'])) {
                            $html .= $info['kpname'].'<br/>';
                            $html .= $info['street'].'<br/>';
                            $html .= $info['city'].' '.$info['zip'];
                            $html .= $info['locationhint'].'<br/>';
                        }
                    }

                    $carrier['extra_info_carrier'] = $html;
                    $is_necessary_postcode         = true;
                    //}

                    return true;
                }
                break;
            case 'mycollectionplaces':
                if ($this->core->isModuleActive('mycollectionplaces')) {
                    if (Configuration::get('MYCOLLP_CARRIER_ID') == $carrier['instance']->id) {
                        $html = '';

                        $query = new DbQuery();
                        $query->from('mycollectionplaces_cart');
                        $query->where('id_cart = '.(int) $this->context->cart->id);

                        $info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
                        if ($info) {
                            if (!empty($info['selection'])) {
                                $id_place = explode('_', $info['selection']);
                                $id_place = $id_place[2];

                                $query = new DbQuery();
                                $query->from('mycollectionplaces_place');
                                $query->where('id_place = '.(int) $id_place);

                                $info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
                                if ($info) {
                                    $html .= $info['sign'];
                                }
                            }
                        }

                        $carrier['extra_info_carrier'] = $html;
                    }

                    return true;
                }
                break;
            case 'yupik':
                if ($this->core->isModuleActive('yupick')) {
                    if (Configuration::get('YUPICK_CARRIER_ID') == $carrier['instance']->id) {
                        $query = new DbQuery();
                        $query->from('yupick_delivery');
                        $query->where('id_cart = '.(int)$this->context->cart->id);

                        $info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
                        if ($info) {
                            if (!empty($info['office_info'])) {
                                $info_detail = explode('|', $info['office_info']);

                                $html .= $info_detail[0].'<br/>';
                                $html .= $info_detail[1].'<br/>';
                                $html .= $info_detail[2].' '.$info_detail[3].' '.$info_detail[4];
                            }
                        }

                        $carrier['extra_info_carrier'] = $html;

                        $is_necessary_postcode = true;
                    }

                    return true;
                }
                break;
            case 'mondialrelay':
                if ($this->core->isModuleActive('mondialrelay')) {
                    $html = '';

                    $query = new DbQuery();
                    $query->from('mr_selected');
                    $query->where('id_cart = '.(int)$this->context->cart->id);

                    $info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
                    if ($info && !empty($info['MR_Selected_CP'])) {
                        $html .= $info['MR_Selected_LgAdr1'].'<br/>';
                        $html .= $info['MR_Selected_LgAdr3'].'<br/>';
                        $html .= $info['MR_Selected_CP'].' '.$info['MR_Selected_Ville'].' '.$info['MR_Selected_Pays'];
                    }

                    $carrier['extra_info_carrier'] = $html;

                    return true;
                }
                break;
            case 'nacex':
//                if ($this->core->isModuleActive('nacex') && Configuration::get('NACEX_WS_IMPORTE') != 'NO') {
                if ($this->core->isModuleActive('nacex')) {
                    $html = '';

                    $datoscarrier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                        'SELECT * FROM '._DB_PREFIX_.'carrier AS c WHERE c.id_carrier = "'.(int)$carrier['instance']->id.'"'
                    );

                    if (isset($datoscarrier) && isset($datoscarrier[0])) {
                        if ($datoscarrier[0]['external_module_name'] == 'nacex'
                            && Tools::strtolower($datoscarrier[0]['ncx']) == 'nacexshop'
                        ) {
                            $query = new DbQuery();
                            $query->select('ncx');
                            $query->from('cart');
                            $query->where('id_cart = '.(int) $this->context->cart->id);

                            $info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

                            if ($info && Tools::strlen($info) > 5) {
                                $info_detail = explode('|', $info);

                                $html .= $info_detail[2].'<br/>';
                                $html .= $info_detail[3].'<br/>';
                                $html .= $info_detail[4].' '.$info_detail[5].' '.$info_detail[6].'<br/>';
                                $html .= $info_detail[7];
                            }

                            $carrier['extra_info_carrier'] = $html;

                            $this->context->smarty->assign('nacex_agcli', Configuration::get('NACEX_AGCLI'));
                        }
                        
                        $is_necessary_postcode = true;
                    }

                    return true;
                }
                break;
            case 'chronopost':
                if ($this->core->isModuleActive('chronopost')) {
                    if (Configuration::get('CHRONORELAIS_CARRIER_ID') == $carrier['instance']->id) {
                        $html = '';

                        $query = new DbQuery();
                        $query->select('id_pr');
                        $query->from('chrono_cart_relais');
                        $query->where('id_cart = '.(int)$this->context->cart->id);

                        $info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
                        if ($info) {
                            if (!empty($info['id_pr'])) {
                                include_once _PS_MODULE_DIR_.'chronopost/libraries/PointRelaisServiceWSService.php';

                                $ws = new PointRelaisServiceWSService();
                                $p = new rechercheBtAvecPFParIdChronopostA2Pas();
                                $p->id = $info['id_pr'];
                                $info_detail = $ws->rechercheBtAvecPFParIdChronopostA2Pas($p)->return;

                                $html .= $info_detail->nomEnseigne.'<br/>';
                                $html .= $info_detail->adresse1.'<br/>';
                                $html .= $info_detail->codePostal.' '.$info_detail->localite;
                            }
                        }

                        $carrier['extra_info_carrier'] = $html;
                        $is_necessary_postcode = true;
                    }

                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * Check the DNI Spain if is valid.
     *
     * @param string $dni
     * @param int $id_country
     * @return boolean
     */
    public function checkDni($dni, $id_country)
    {
        $iso_country = Country::getIsoById($id_country);

        if ($this->config_vars['OPC_VALIDATE_DNI']) {
            if ($iso_country == 'ES') {
                require_once dirname(__FILE__).'/lib/nif-nie-cif.php';

                return isValidIdNumber($dni);
            } else if ($iso_country == 'CL') {
                $dni = preg_replace('/[^k0-9]/i', '', $dni);
                $dv  = Tools::substr($dni, -1);
                $numero = Tools::substr($dni, 0, Tools::strlen($dni)-1);
                $i = 2;
                $suma = 0;
                foreach (array_reverse(str_split($numero)) as $v) {
                    if ($i==8) {
                        $i = 2;
                    }
                    $suma += $v * $i;
                    ++$i;
                }
                $dvr = 11 - ($suma % 11);
                if ($dvr == 11) {
                    $dvr = 0;
                }
                if ($dvr == 10) {
                    $dvr = 'K';
                }
                if ($dvr == Tools::strtoupper($dv)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return Validate::isDniLite($dni) ? true : false;
        }
    }

    /**
     * Save discount to cart.
     *
     * @return array (hasError, errors)
     */
    public function processDiscount()
    {
        if ($this->context->cart->nbProducts() > 0) {
            if (Tools::getValue('action_discount') == 'add' && Tools::getValue('discount_name')) {
                $code = trim(Tools::getValue('discount_name'));
                if (!Validate::isCleanHtml($code)) {
                    $this->errors[] = $this->l('Неверное имя промокода.');
                } else {
                    if (($cart_rule = new CartRule(CartRule::getIdByCode($code)))
                        && Validate::isLoadedObject($cart_rule)
                    ) {
                        if ($error = $cart_rule->checkValidity($this->context, false, true)) {
                            $this->errors[] = $error;
                        } else {
                            $this->context->cart->addCartRule($cart_rule->id);
                        }
                    } else {
                        $this->errors[] = $this->l('Неверное имя промокода.');
                    }
                }
            } elseif (Tools::getValue('action_discount') == 'delete'
                && ($id_cart_rule = (int) Tools::getValue('id_discount'))
                && Validate::isUnsignedId($id_cart_rule)
            ) {
                $this->context->cart->removeCartRule($id_cart_rule);
            }
        }

        return array('hasError' => !empty($this->errors), 'errors' => $this->errors);
    }

    private function addFieldsRequired(&$fields, $name_object, $object)
    {
        $fields_tmp = array();

        $fields_db_required = $object->getFieldsRequiredDatabase();
        $fields_object      = ObjectModel::getDefinition($object);

        foreach ($fields_db_required as $field) {
            array_push($fields_tmp, $field['field_name']);
        }

        foreach ($fields_object['fields'] as $name_field => $field) {
            if (isset($field['required']) && $field['required'] == 1) {
                array_push($fields_tmp, $name_field);
            }
        }

        array_push($fields_tmp, 'id_country');
        array_push($fields_tmp, 'id_state');

        $fields_db = FieldClass::getAllFields(
            $this->context->cookie->id_lang,
            null,
            $name_object,
            null,
            null,
            $fields_tmp
        );

        foreach ($fields_db as $field) {
            if (!isset($fields[$field->name])
                || (isset($fields[$field->name])
                    && empty($fields[$field->name]->value))
            ) {
                $field->value = $field->default_value;
                
                $fields[$field->name] = $field;
            }

            $fields[$field->name]->required = 1;
        }
    }

    private function validateFieldsCustomer(&$fields, &$customer, &$password)
    {
        foreach ($fields as $name => $field) {
            //support module: pts_customertypeopc - v2.1.0
            if ($ctopc = $this->core->isModuleActive('pts_customertypeopc')) {
                if ($ctopc->isVisible()) {
                    $row_field = $ctopc->getStatusFieldOpc($field->id);

                    if ($row_field && is_array($row_field)) {
                        $field->active = true;
                        $field->required = $row_field['required'];
                    }
                }
            }
            
            if ($field->type == 'url') {
                $field->type = 'isUrl';

                if (!empty($field->value) && Tools::substr($field->value, 0, 4) != 'http') {
                    $field->value = 'http://'.$field->value;
                }
            } elseif ($field->type == 'number') {
                $field->type = 'isInt';
            } elseif ($field->type == 'isDate' || $field->type == 'isBirthDate') {
                if (!empty($field->value)) {
                    $field->value = date('Y-m-d', strtotime(str_replace('/', '-', $field->value)));
                }
            }

            if ($name == 'passwd') {
                $password = $field->value;
                //if logged the password does not matter
                if ($this->context->customer->isLogged()/* || $this->context->customer->isGuest()*/) {
                    //unset($fields[$name]);
                    if (empty($field->value)) {
                        continue;
                    }
                } else {
                    if (!$this->config_vars['OPC_REQUEST_PASSWORD']
                        || ($this->config_vars['OPC_REQUEST_PASSWORD']
                            && $this->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD']
                            && empty($field->value))
                        || (Configuration::get('PS_GUEST_CHECKOUT_ENABLED')
                        && Tools::getValue('is_new_customer') == 1)
                    ) {
                        $password = Tools::passwdGen();
                    }
                }
                
                $field->value = Tools::encrypt($password);
            } elseif ($name == 'email') {
                if (empty($field->value)) {
                    $field->value = date('His').'@auto-generated.opc';
                }

                if (!$this->context->customer->isLogged()
                    && Customer::customerExists($field->value)
                    //&& !Configuration::get('PS_GUEST_CHECKOUT_ENABLED')
                    && Tools::getValue('is_new_customer') == 1
                    && $this->config_vars['OPC_REQUIRED_LOGIN_CUSTOMER']
                ) {
                    $this->errors[] = $this->l('An account using this email address has already been registered.');
                }
            } elseif ($name == 'current_passwd') {
                if (!empty($field->value)) {
                    $field->value = Tools::encrypt($field->value);
                    $customer->{$name} = $field->value;
                }
                
                continue;
            }

            $valid = call_user_func(array('Validate', $field->type), $field->value);

            //check field required
            if ($field->required == 1 && empty($field->value)) {
                $this->errors[] = sprintf(
                    $this->l('The field %s is required.'),
                    ObjectModel::displayFieldName(
                        $name,
                        get_class($customer),
                        true
                    )
                );
            } elseif (!empty($field->value) && !$valid) {
                $this->errors[] = sprintf(
                    $this->l('The field %s is invalid.'),
                    ObjectModel::displayFieldName(
                        $name,
                        get_class($customer),
                        true
                    )
                );
            }

            if ($field->active == 0 && !empty($customer->{$name})) {
                continue;
            }

            $customer->{$name} = $field->value;
        }

        $this->supportModuleCPFUser($customer);
    }

    private function validateFieldsAddress(&$fields, &$address)
    {
        foreach ($fields as $name => $field) {
            //support module: pts_customertypeopc - v2.1.0
            if ($ctopc = $this->core->isModuleActive('pts_customertypeopc')) {
                if ($ctopc->isVisible()) {
                    $row_field = $ctopc->getStatusFieldOpc($field->id);

                    if ($row_field && is_array($row_field)) {
                        $field->active = true;
                        $field->required = $row_field['required'];
                    }
                }
            }
            
            if ($field->type == 'url') {
                $field->type = 'isUrl';

                if (Tools::substr($field->value, 0, 4) != 'http') {
                    $field->value = 'http://'.$field->value;
                }
            } elseif ($field->type == 'number') {
                $field->type = 'isInt';
            } elseif ($field->type == 'isDate' || $field->type == 'isBirthDate') {
                $field->value = date('Y-m-d', strtotime(str_replace('/', '-', $field->value)));
            }

            $valid = call_user_func(array('Validate', $field->type), $field->value);

            //check field required
            if ($field->required == 1 && empty($field->value)) {
                if ($field->name != 'id_state') {
                    $this->errors[] = sprintf(
                        $this->l('The field %s is required.'),
                        ObjectModel::displayFieldName(
                            $name,
                            get_class($address),
                            true
                        )
                    );
                }
            } elseif (!empty($field->value) && !$valid) {
                //check field validated
                $this->errors[] = sprintf(
                    $this->l('The field %s is invalid.'),
                    ObjectModel::displayFieldName(
                        $name,
                        get_class($address),
                        true
                    )
                );
            }

            if ($field->active == 0 && !empty($address->{$name})) {
                continue;
            }

            $address->{$name} = $field->value;
        }

        if ($address->id_country) {
            // Check country
            if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country)) {
                $this->errors[] = $this->l('Country cannot be loaded.');
            }

            if ((int) $country->contains_states) {
                if (!(int) $address->id_state) {
                    $this->errors[] = $this->l('This country requires you to chose a State.');
                } else {
                    $state = new State((int)$address->id_state);

                    if (Validate::isLoadedObject($state) && $state->id_country != $country->id) {
                        $address->id_state = null;
                    }
                }
            } else {
                $address->id_state = null;
            }

            if (!$country->active) {
                $this->errors[] = $this->l('This country is not active.');
            }

            // Check zip code format
            if ($country->zip_code_format && !$country->checkZipCode($address->postcode)) {
                //this fix the problem if the field postcode is disabled.
                if (!empty($address->postcode) && $field->active == 1) {
                    $this->errors[] = sprintf(
                        $this->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'),
                        str_replace(
                            'C',
                            $country->iso_code,
                            str_replace(
                                'N',
                                '0',
                                str_replace(
                                    'L',
                                    'A',
                                    $country->zip_code_format
                                )
                            )
                        )
                    );
                } else {
                    $address->postcode = str_replace(
                        'C',
                        $country->iso_code,
                        str_replace(
                            'N',
                            '0',
                            str_replace(
                                'L',
                                'A',
                                $country->zip_code_format
                            )
                        )
                    );
                }
            } elseif (empty($address->postcode) && $country->need_zip_code) {
                $address->postcode = str_replace(
                    'C',
                    $country->iso_code,
                    str_replace(
                        'N',
                        '0',
                        str_replace(
                            'L',
                            'A',
                            $country->zip_code_format
                        )
                    )
                );
            }
            //$this->errors[] = $this->l('The Zip/Postal code is required.');
            // Check country DNI
            if (!empty($address->dni)) {
//                    if ($this->config_vars['OPC_ADD_ISO_CODE_TO_DNI']) {
//                        $iso_code = substr($address->dni, 0, 2);
//
//                        if (!Validate::isLanguageIsoCode($iso_code) || !Country::getByIso($iso_code)) {
//                            $iso_code = substr($address->dni, 0, 3);
//
//                            if (!Validate::isLanguageIsoCode($iso_code) || !Country::getByIso($iso_code)) {
//                                $iso_code = Country::getIsoById($address->id_country);
//                                $address->dni = $iso_code.$address->dni;
//                            }
//                        }
//                    }
//
//                    if ($this->config_vars['OPC_VALIDATE_DUPLICATE_DNIS']) {
//                        $sql = new DbQuery();
//                        $sql->select('count(*)');
//                        $sql->from('address');
//                        $sql->where('dni = "'.pSQL($address->dni).'"');
//
//                        if ((int)$this->context->customer->id > 0) {
//                            $sql->where('id_customer != '.(int) $this->context->customer->id);
//                        }
//
//                        $exist_dni = Db::getInstance()->getValue($sql);
//
//                        if ($exist_dni) {
//                            $this->errors[] = $this->l('The identification number is already registered.');
//                        }
//                    }

                if ($country->isNeedDni()
                    && (!$address->dni)
                    || !$this->checkDni($address->dni, $address->id_country)
                ) {
                    $this->errors[] = $this->l('The field identification number is invalid.');
                }
//					else
//					{
//						$query = new DbQuery();
//						$query->from('address');
//						$query->where(
//							'dni = "'.$address->dni.'"'.
//								($this->context->customer->isLogged() ? ' AND id_customer != '.$this->context->customer->id : '')
//						);
//						if (Db::getInstance()->executeS($query))
//							$this->errors[] = $this->l('The identification number has already been used.');
//					}
            } elseif (!$country->isNeedDni()) {
                $address->dni = null;
            }
        }

        if (!Validate::isDate($address->date_add)) {
            $address->date_add = date('Y-m-d H:i:s');
        }
        if (!Validate::isDate($address->date_upd)) {
            $address->date_upd = $address->date_add;
        }
    }

    public function isSameAddress($delivery_address, $invoice_address)
    {
        $is_same = true;

        if ($delivery_address->id_country != $invoice_address->id_country) {
            $is_same = false;
        }
        if ($delivery_address->id_state != $invoice_address->id_state) {
            $is_same = false;
        }
        if ($delivery_address->alias != $invoice_address->alias) {
            $is_same = false;
        }
        if ($delivery_address->company != $invoice_address->company) {
            $is_same = false;
        }
        if ($delivery_address->lastname != $invoice_address->lastname) {
            $is_same = false;
        }
        if ($delivery_address->firstname != $invoice_address->firstname) {
            $is_same = false;
        }
        if ($delivery_address->address1 != $invoice_address->address1) {
            $is_same = false;
        }
        if ($delivery_address->address2 != $invoice_address->address2) {
            $is_same = false;
        }
        if ($delivery_address->postcode != $invoice_address->postcode) {
            $is_same = false;
        }
        if ($delivery_address->city != $invoice_address->city) {
            $is_same = false;
        }
        if ($delivery_address->other != $invoice_address->other) {
            $is_same = false;
        }
        if ($delivery_address->phone != $invoice_address->phone) {
            $is_same = false;
        }
        if ($delivery_address->phone_mobile != $invoice_address->phone_mobile) {
            $is_same = false;
        }
        if ($delivery_address->dni != $invoice_address->dni) {
            $is_same = false;
        }

        return $is_same;
    }

    public function loadAddress()
    {
        $id_address = (int) Tools::getValue('id_address');
        $object = Tools::getValue('object');
        $update_cart = (int) Tools::getValue('update_cart');

        if (empty($id_address)) {
            if ($object == 'delivery') {
                $id_address = $this->context->cart->id_address_delivery;
            } else if ($object == 'invoice') {
                $id_address = $this->context->cart->id_address_invoice;
            }
        }

        $address = new Address((int) $id_address);

        if (Validate::isLoadedObject($address)) {
            //verifica no cargar una direccion de otro cliente.
            //error que pasaba en la tienda de laboutiquedelaslanas.com. No sabemos si era algun modulo o modificacion.
            if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
                if ($address->id_customer != $this->config_vars['OPC_ID_CUSTOMER']) {
                    $id_address = $this->getIdAddressAvailable($object);
                    $address = new Address((int) $id_address);

                    $this->context->cart->id_address_delivery = $id_address;
                    $this->context->cart->id_address_invoice = $id_address;
                    $this->context->cart->update();
                    $this->context->cart->setNoMultishipping();
                }
            }
            
            if ($update_cart) {
                if ($object == 'delivery') {
                    $this->context->cart->id_address_delivery = $id_address;
                } else if ($object == 'invoice') {
                    $this->context->cart->id_address_invoice = $id_address;
                }

                $this->context->cart->update();
                $this->context->cart->setNoMultishipping();
            }
        }

        //Consultar campos personalizados asociados a un carrito actual
        if (/*Validate::isLoadedObject($this->context->cart) && */$this->context->customer->id > 0) {
            $custom_fileds = FieldCustomerClass::getData(null, $this->context->customer->id);

            //Asigna los valores de los campos personalizados
            if ($custom_fileds) {
                foreach ($custom_fileds as $custom_field) {
                    if ($custom_field['object'] == 'customer') {
                        $this->context->customer->{$custom_field['name']} = $custom_field['value'];
                    } elseif ($custom_field['object'] == $object && (int)$custom_field['id_address'] === (int)$address->id) {
                        $address->{$custom_field['name']} = $custom_field['value'];
                    }
                }
            }
        }

        $result = array(
            'address' => $address,
            'customer' => $this->context->customer,
        );

        return $result;
    }

    /**
     * Load options shipping.
     *
     * @return array
     */
    public function loadCarrier()
    {
        $set_id_customer_opc = false;

        $js_files  = array();
        $css_files = array();

        if (!$this->context->cart->isVirtualCart()) {
            if ($this->context->customer->isLogged()) {
                $address_customer = $this->context->customer->getAddresses($this->context->cart->id_lang);
                if (empty($address_customer)) {
                    $this->errors = $this->l('It is necessary to create an address to be able to show the different shipping options.');
                }
            }

            if (is_array($this->errors) && !count($this->errors)) {
                $delivery_address = new Address($this->context->cart->id_address_delivery);

                if (Validate::isLoadedObject($delivery_address)) {
                    //assign opc customer to cookie, customer and cart to calculare fine the prices of carriers
                    if (empty($this->context->cookie->id_customer)) {
                        $module_exception = false;

                        if ($this->core->isModuleActive('bpostshm')) {
                            $module_exception = true;
                        }

                        if (!$module_exception) {
                            $this->context->cookie->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];

                            if (empty($this->context->customer->id)) {
                                $this->context->customer = new Customer($this->config_vars['OPC_ID_CUSTOMER']);
                                $this->context->customer->logged = 1;
                            }

                            if (empty($this->context->cart->id_customer)) {
                                $this->context->cart->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];
                            }

                            $set_id_customer_opc = true;
                        }

                        //Sopport module: correos - v4 o superior
                        //Esto soluciona el problema que se tenga que refrescar dos veces los transportes para poder
                        //mostrar la oficina correcta del codigo postal enviado.
                        if ($this->core->isModuleActive('correos')) {
                            if (!empty($this->context->cart->id_carrier)) {
                                CorreosCommon::getOfficies(
                                    $delivery_address->postcode,
                                    'front',
                                    $this->context->cart->id_carrier
                                );
                            }
                        }
                    }

                    if (empty($delivery_address->id_country)) {
                        $this->errors[] = $this->l('Select a country to show the different shipping options.');
                    } else if (empty($delivery_address->id_state)) {
                        if (Country::containsStates((int) $delivery_address->id_country)) {
                            $this->errors[] = $this->l('Select a state to show the different shipping options.');
                        }
                    }
                } else {
                    $this->l('This address is invalid. Sign out of session and login again.');
                }
            }

            if (is_array($this->errors) && !count($this->errors)) {
                $address              = new Address($this->context->cart->id_address_delivery);
                $carriers             = $this->context->cart->simulateCarriersOutput(null, true);
                $delivery_option      = $this->context->cart->getDeliveryOption(null, false, false);
                $delivery_option_list = $this->context->cart->getDeliveryOptionList();

                if (!$this->context->cart->getDeliveryOption(null, true)) {
                    $this->context->cart->setDeliveryOption($this->context->cart->getDeliveryOption());
                }
                
                if (!count($carriers) && !count($delivery_option_list)) {
                    $this->errors[] = $this->l('There are no shipping methods available for your address.');
                }

                $is_necessary_postcode = false;
                $is_necessary_city     = false;

                if (!$this->config_vars['OPC_SHIPPING_COMPATIBILITY']) {
                    //support module kiala. Without this code the info about pickup do not refresh.
                    if ($this->core->isModuleActive('kiala')) {
                        $vars = array();
                        Cart::addExtraCarriers($vars);
                    }

                    //support module custom: dbaterias_vtpower
                    $is_module_vtpower_active = false;
                    $have_product_vtpower = false;
                    $not_have_product_vtpower = false;
                    $dbaterias_vtpower = $this->core->isModuleActive('dbaterias_vtpower');
                    if ($dbaterias_vtpower) {
                        $products_cart = $this->context->cart->getProducts();
                        $is_module_vtpower_active = true;
                        $id_supplier_vtpower = Configuration::get('DBVTP_ID_SUPPLIER');

                        if (is_array($products_cart)) {
                            foreach ($products_cart as $product) {
                                if ($product['id_supplier'] == $id_supplier_vtpower) {
                                    $have_product_vtpower = true;
                                } else {
                                    $not_have_product_vtpower = true;
                                }
                            }
                        }
                    }
                }

                $delivery_option_list_tmp = array();
                foreach ($delivery_option_list as $id_address => $option_list) {
                    $option_list_tmp = array();
                    foreach ($option_list as $key => $option) {
                        $carrier_list_tmp = array();

                        foreach ($option['carrier_list'] as $id_carrier => $carrier) {
                            if (!$this->config_vars['OPC_SHIPPING_COMPATIBILITY']) {
                                //support module custom: dbaterias_vtpower
                                if ($is_module_vtpower_active) {
                                    if ($have_product_vtpower && !$not_have_product_vtpower) {
                                        if ($carrier['instance']->name != 'Mensajero') {
                                            unset($option_list[$key]);
                                            continue 2;
                                        } else {
                                            $delivery_option = array($address->id => $id_carrier.',');
                                            $this->context->cart->setDeliveryOption($delivery_option);
                                            $this->context->cart->update();
                                            $this->context->cart->setNoMultishipping();
                                        }
                                    } else if (($have_product_vtpower && $not_have_product_vtpower) || !$have_product_vtpower) {
                                        if ($carrier['instance']->name == 'Mensajero') {
                                            unset($option_list[$key]);
                                            continue 2;
                                        }
                                    }
                                }

                                //support module of shipping for pick up.
                                if (!empty($carrier['instance']->external_module_name)) {
                                    $this->supportModulesShipping(
                                        $carrier['instance']->external_module_name,
                                        $address,
                                        $carrier,
                                        $is_necessary_postcode,
                                        $is_necessary_city
                                    );
                                }

                                //support module estimateshippingcost - V2.0.5 - PresTeamShop
                                $estimateshippingcost = $this->core->isModuleActive('estimateshippingcost');
                                if ($estimateshippingcost) {
                                    if (version_compare($estimateshippingcost->version, '2.0.5') >= 0) {
                                        $option['carrier_list'][$id_carrier]['instance']->estimate_days = $estimateshippingcost->getEstimateDeliveryDays(
                                            $carrier['instance']->name,
                                            $delivery_address->id_country,
                                            $delivery_address->id_state,
                                            $delivery_address->city,
                                            $delivery_address->postcode
                                        );
                                    }
                                }
                                
                                $module = $this->core->isModuleActive('furgonetka');
                                if ($module) {
                                    $receiver_point_type = '';

                                    if ($carrier['instance']->inpost == 1) {
                                        $receiver_point_type = 'paczkomat';
                                    } elseif ($carrier['instance']->poczta == 1) {
                                        $receiver_point_type = 'placowka';
                                    } elseif ($carrier['instance']->ruch == 1) {
                                        $receiver_point_type = 'kiosk';
                                    }

                                    //support carrier method: furgonetka
                                    if (!empty($receiver_point_type)) {
                                        $sql = 'SELECT receiver_point_name FROM '._DB_PREFIX_.'cart WHERE receiver_point_type = "'.pSQL($receiver_point_type).'" AND id_cart = '.(int)$this->context->cart->id;
                                        $receiver_point_name = Db::getInstance()->getValue($sql);

                                        if (!empty($receiver_point_name)) {
                                            $carrier['extra_info_carrier'] = $receiver_point_name;
                                        } else {
                                            $carrier['extra_info_carrier'] = '';
                                        }

                                        $carrier['instance']->external_module_name = 'furgonetka';
                                    }
                                }
                            }

                            $carrier_list_tmp[$carrier['instance']->id] = $carrier;
                        }

                        $option['carrier_list'] = $carrier_list_tmp;
                        $option_list_tmp[$key]  = $option;
                    }
                    $delivery_option_list_tmp[$id_address] = $option_list_tmp;
                }

                $delivery_option_list = $delivery_option_list_tmp;

                if (!$is_necessary_postcode) {
                    if ($this->config_vars['OPC_FORCE_NEED_POSTCODE']) {
                        $is_necessary_postcode = true;
                    } else {
                        $carriers_postcode = explode(
                            ',',
                            $this->config_vars['OPC_MODULE_CARRIER_NEED_POSTCODE']
                        );
                        foreach ($carriers_postcode as $carrier) {
                            if ($this->core->isModuleActive($carrier)) {
                                $is_necessary_postcode = true;
                            }
                        }
                    }
                }

                if (!$is_necessary_city) {
                    if ($this->config_vars['OPC_FORCE_NEED_CITY']) {
                        $is_necessary_city = true;
                    } else {
                        $carriers_city = explode(',', $this->config_vars['OPC_MODULE_CARRIER_NEED_CITY']);

                        foreach ($carriers_city as $carrier) {
                            if ($this->core->isModuleActive($carrier)) {
                                $is_necessary_city = true;
                            }
                        }
                    }
                }

                if ($is_necessary_city) {
                    $default_value = FieldClass::getDefaultValue('delivery', 'city');

                    if (empty($address->city) || $address->city == $default_value) {
                        $this->errors = $this->l('You need to place a city to show shipping options.');
                    }
                }

                if ($is_necessary_postcode) {
                    $country = new Country($address->id_country);

                    if (Validate::isLoadedObject($country)) {
                        $default_value = FieldClass::getDefaultValue('delivery', 'postcode');

                        if (empty($default_value)) {
                            $default_value = str_replace(
                                'C',
                                $country->iso_code,
                                str_replace(
                                    'N',
                                    '0',
                                    str_replace(
                                        'L',
                                        'A',
                                        $country->zip_code_format
                                    )
                                )
                            );
                        }

                        if ($country->need_zip_code && !empty($country->zip_code_format)) {
                            if (empty($address->postcode) || $address->postcode == $default_value || !$country->checkZipCode($address->postcode)) {
                                $this->errors = $this->l('You need to place a post code to show shipping options.');
                            }
                        }
                    }
                }

                // Assign wrapping and TOS
                $this->context->controller->getController('OrderOpcController')->opcAssignWrappingAndTOS();
                
                $id_carrier_selected = $this->context->cart->id_carrier;
                $module_carrier_selected = new Carrier($id_carrier_selected);

                $this->context->smarty->assign(array(
                    'address_collection'    => $this->context->cart->getAddressCollection(),
                    'delivery_option_list'  => $delivery_option_list,
                    'carriers'              => $carriers,
                    'id_carrier_selected'   => $this->context->cart->id_carrier,
                    'module_carrier_selected'   => $module_carrier_selected->external_module_name,
                    'delivery_option'       => $delivery_option,
                    'is_necessary_postcode' => $is_necessary_postcode,
                    'is_necessary_city'     => $is_necessary_city,
                ));

                if (empty($this->errors)) {
                    $vars = array(
                        'HOOK_BEFORECARRIER' => Hook::exec(
                            'displayBeforeCarrier',
                            array(
                                'carriers'             => $carriers,
                                'delivery_option_list' => $delivery_option_list,
                                'delivery_option'      => $delivery_option
                            )
                        )
                    );

                    Cart::addExtraCarriers($vars);

                    $this->context->smarty->assign($vars);
                }
            }

            $this->context->smarty->assign(array(
                'ONEPAGECHECKOUTPS_IMG' => $this->onepagecheckoutps_dir.'views/img/',
                'CONFIGS'               => $this->config_vars
            ));
        }
        
        if (!empty($this->errors)) {
            $this->context->cart->id_carrier = 0;
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->update();
            $this->context->cart->setNoMultishipping();
        }

        if (!$this->config_vars['OPC_SHIPPING_COMPATIBILITY']) {
            //compatibilidad con soliverte v4.3.03 - Common-Services
            if ($this->core->isModuleActive('soliberte')) {
                $base_url = $this->core->getUrlStore().$this->context->shop->getBaseURI().'modules/soliberte/views/';
                array_push($js_files, $base_url.'js/jquery.qtip.min.js');
                array_push($js_files, $base_url.'js/front_liberte.js');
                $css_files[$base_url.'css/jquery.qtip.min.css'] = 'all';
            }

            //support module alex_deliverydate
            if ($this->core->isModuleActive('alex_deliverydate')) {
                array_push($js_files, __PS_BASE_URI__.'modules/alex_deliverydate/views/js/deliverydate.js');
            }
        }

        $this->context->smarty->assign('js_files', $js_files);
        $this->context->smarty->assign('css_files', $css_files);

        //soluciona el problema de mostrar el texto CON IVA cuando colocan el vat_numer sin estar logeado.
        //tiene que ir este codigo antes de que se quite el customer del OPC.
        $this->context->smarty->assign(array(
            'priceDisplay' => Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer),
        ));

        if ($set_id_customer_opc) {
            $this->context->customer         = new Customer();
            $this->context->customer->logged = 0;
            unset($this->context->cookie->id_customer);

            $this->context->cart->id_customer = null;
            $this->context->cart->update();
            $this->context->cart->setNoMultishipping();
        }

        $this->context->smarty->assign(array(
            'IS_VIRTUAL_CART' => $this->context->cart->isVirtualCart(),
            'hasError'        => !empty($this->errors),
            'errors'          => $this->errors
        ));

        if (file_exists(_PS_THEME_DIR_.'modules/onepagecheckoutps/views/templates/front/carrier.tpl')) {
            $html = $this->context->smarty->fetch(_PS_THEME_DIR_.'/modules/onepagecheckoutps/views/templates/front/carrier.tpl');
        } else {
            $html = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/carrier.tpl');
        }

        return $html;
    }

    /**
     * Load payment methods.
     *
     * @return html
     */
    public function loadPayment()
    {
        $id_carrier = (int) $this->context->cart->id_carrier;
        $output = array();
        $output_eu = array();

        $payment_need_register = false;

        if ($this->config_vars['OPC_ENABLE_INVOICE_ADDRESS'] && (int)$this->context->cart->id_address_invoice > 0) {
            $address = new Address($this->context->cart->id_address_invoice);
            $id_country = (int)$address->id_country;
        } elseif ((int)$this->context->cart->id_address_delivery > 0) {
            $address = new Address($this->context->cart->id_address_delivery);
            $id_country = (int)$address->id_country;
        }
        
        if ($this->context->customer->isLogged()) {
            $address_customer = $this->context->customer->getAddresses($this->context->cart->id_lang);
            if (empty($address_customer)) {
                if (Configuration::get('PS_GEOLOCATION_ENABLED') && Context::getContext()->country->active) {
                    $id_country = Context::getContext()->country->id;
                } else {
                    $id_country = Configuration::get('PS_COUNTRY_DEFAULT');
                }
            }
        }

        if (empty($id_country)) {
            return '<p class="alert alert-warning">'.$this->l('Select a country to show the different payment options.').'</p>';
        }

        //support module: packetery - Packetery, Ltd. - v1.17
        if ($this->core->isModuleActive('packetery')) {
            if (!$this->context->cart->isVirtualCart() && empty($id_carrier)) {
                return '<p class="alert alert-warning">'.$this->l('You need to select a store to be able to display the payment options.').'</p>';
            }
        }

        if ($this->context->cart->getOrderTotal() > 0) {
            $set_id_customer_opc = false;
            if (!$this->context->cookie->id_customer) {
                $this->context->cookie->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];

                if (!$this->context->customer->id) {
                    if ($this->core->isModuleActive('taxcloud') || $this->core->isModuleActive('mercadopago')) {
                        $this->context->customer         = new Customer($this->config_vars['OPC_ID_CUSTOMER']);
                        $this->context->customer->logged = 1;
                    } else {
                        $this->context->customer->id = $this->config_vars['OPC_ID_CUSTOMER'];
                    }
                }

                if (!$this->context->cart->id_customer) {
                    $this->context->cart->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];
                }

                $set_id_customer_opc = true;
            }
        }

        //HABILITAMOS ESTA OPCION PARA PS 1.6 DE LO CONTRARIO TRAE PROBLEMAS AL TENER ACTIVO EL GUEST CHECKOUT
        Configuration::updateValue('PS_GROUP_FEATURE_ACTIVE', '1');

        $groups = array();
        if (isset($this->context->customer)) {
            $groups = $this->context->customer->getGroups();
            if (empty($groups)) {
                $groups = array(Configuration::get('PS_UNIDENTIFIED_GROUP'));
            }
        }

        /* If some products have disappear */
        if (method_exists($this->context->cart, 'checkQuantities')) {
            if (is_array($product = $this->context->cart->checkQuantities(true))) {
                return '<p class="alert alert-warning">'.sprintf($this->l('An item (%s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.'), $product['name']).'</p>';
            }
        }

        if (method_exists($this->context->cart, 'checkProductsAccess')) {
            if ((int)$id_product = $this->context->cart->checkProductsAccess()) {
                return '<p class="alert alert-warning">'.sprintf($this->l('An item in your cart is no longer available (%s). You cannot proceed with your order.'), Product::getProductName((int)$id_product)).'</p>';
            }
        }

        $total_order = $this->context->cart->getOrderTotal();
        $this->context->smarty->assign(array(
            'static_token' => Tools::getToken(false), //Algunos modulos necesitan esta variable de token.
            'total_order'  => $total_order,
            'total_price'  => $total_order,
        ));

        if ($total_order > 0) {
            $hook_payment = 'Payment';
            $query        = 'SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\'';
            if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query)) {
                $hook_payment = 'displayPayment';
            }

            //SE TRAEN LOS METODOS DE PAGO HABILITADOS PARA EL TRANSPORTISTA SELECCIONADO.
            //EN LA CONSULTA DE LOS METODOS DE PAGO, SI NO EXISTE ASOCIACION CON EL TRANSPORTE,
            //SE MUESTRAN TODOS LOS METODOS DE PAGO.
            $ids_payment_module = array();
            if (!$this->context->cart->isVirtualCart()) {
                if (version_compare(_PS_VERSION_, '1.6.1.20', '>=')) {
                    $delivery_option = Tools::jsonDecode($this->context->cart->delivery_option);
                    if ($delivery_option) {
                        if (isset($delivery_option->{$this->context->cart->id_address_delivery})) {
                            $id_carrier = str_replace(',', '', $delivery_option->{$this->context->cart->id_address_delivery});
                        }
                    }
                } else {
                    $delivery_option = unserialize($this->context->cart->delivery_option);
                    if (count($delivery_option) > 0) {
                        if (isset($delivery_option[$this->context->cart->id_address_delivery])) {
                            $id_carrier = str_replace(',', '', $delivery_option[$this->context->cart->id_address_delivery]);
                        }
                    }
                }
                
                if (empty($id_carrier)) {
                    $id_carrier = (int) $this->context->cart->id_carrier;
                }

                if (!empty($id_carrier)) {
                    $sql_payment_module = 'SELECT * FROM '._DB_PREFIX_.'opc_ship_to_pay WHERE id_carrier = '.(int)$id_carrier;
                    $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_payment_module);

                    foreach ($rows as $row) {
                        array_push($ids_payment_module, $row['id_payment_module']);
                    }
                }
            }

            //support module: paymentrestrict - presta_world - v7.0.0
            //-------------------------------------------------------
            if ($this->core->isModuleActive('paymentrestrict')) {
                $products = $this->context->cart->getProducts();

                foreach ($products as $prod) {
                    $isExist = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_ . 'presta_product_restriction where id_product ='.(int)$prod['id_product']);

                    if ($isExist) {
                        if ($isExist['allowed_payments']) {
                            $allowed_payments = unserialize($isExist['allowed_payments']);

                            foreach ($allowed_payments as $id_payment) {
                                array_push($ids_payment_module, $id_payment);
                            }
                        }
                    }
                }
            }
            //-------------------------------------------------------

            $idshop = (int) $this->context->shop->id;

            $query = new DbQuery();
            $query->select('DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`');
            $query->from('module', 'm');

            $query->leftJoin('module_country', 'mc', '(m.`id_module` = mc.`id_module` AND mc.`id_shop` = '.(int)$idshop.')');
            $query->where('mc.`id_country` = '.(int)$id_country);
            
            $query->leftJoin('module_currency', 'mcc', '(m.`id_module` = mcc.`id_module` AND mcc.`id_shop` = '.$idshop.')');
            $query->innerJoin('module_group', 'mg', '(m.`id_module` = mg.`id_module` AND mg.`id_shop` = '.(int)$idshop.')');
            $query->innerJoin('module_shop', 'ms', '(m.`id_module` = ms.`id_module` AND ms.`id_shop` = '.(int)$idshop.')');
            $query->innerJoin(
                'customer_group',
                'cg',
                '(cg.`id_group` = mg.`id_group` AND cg.`id_customer` = '.(int) $this->context->customer->id.')'
            );
            $query->leftJoin('hook_module', 'hm', '(hm.`id_module` = m.`id_module` AND hm.`id_shop` = '.(int)$idshop.')');
            $query->leftJoin('hook', 'h', '(h.`id_hook` = hm.`id_hook`)');
            $query->where('h.`name` = "'.pSQL($hook_payment).'"');
            $query->where('mcc.`id_currency` IN (-1, -2, '.(int)$this->context->cart->id_currency.')');
            if (count($ids_payment_module)) {
                $query->where('m.`id_module` IN ('.implode(', ', $ids_payment_module).')');
            }
            $query->groupBy('hm.`id_hook`, hm.`id_module`');
            $query->orderBy('hm.`position`, m.`name` DESC');

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

            $hook_args = array('cookie' => $this->context->cookie, 'cart' => $this->context->cart);

            if ($result) {
                $modules_need_eu = array();
                $modules_external_image = array('mollie', 'paynl_paymentmethods', 'ThePayBinder', 'custompaymentmethod');

                Hook::exec('displayPaymentTop');

                //support module: pmpcrestrictions - v1.3.6 - PrestaSite
                if ($pmpcrestrictions = $this->core->isModuleActive('pmpcrestrictions', 'filterPaymentModules')) {
                    $result = $pmpcrestrictions->filterPaymentModules($result);
                }

                foreach ($result as $i => $module) {
                    $payment = '';
                    $module_instance = $this->core->isModuleActive($module['name'], 'hookDisplayPayment');
                    if (!$module_instance) {
                        $module_instance = $this->core->isModuleActive($module['name'], 'hookpayment');

                        if (!$module_instance) {
                            continue;
                        }
                    }

                    $module_instance_eu = $this->core->isModuleActive($module['name'], 'hookDisplayPaymentEU');
                    if ($module_instance_eu || $module_instance) {
                        $id_payment = PaymentClass::getIdPaymentBy('id_module', (int) $module['id_module']);
                        if (!empty($id_payment)) {
                            $payment = new PaymentClass($id_payment, $this->context->language->id);
                        }
                    }

                    if (Validate::isLoadedObject($payment)) {
                        $display_module = true;

                        if ($payment->test_mode) {
                            $display_module = false;
                            $my_ip = Tools::getRemoteAddr();
                            $array_ips_debug = explode(',', $payment->test_ip);

                            if (in_array($my_ip, $array_ips_debug)) {
                                $display_module = true;
                            }
                        }

                        if (!$display_module) {
                            continue;
                        }
                    }

                    if ($module_instance_eu
                        && !$this->config_vars['OPC_PAYMENTS_WITHOUT_RADIO']
                        && Validate::isLoadedObject($payment)
                        && in_array($module_instance_eu->name, $modules_need_eu)
                    ) {
                        $params = array('cart' => $this->context->cart);
                        $payment_options = $module_instance_eu->hookDisplayPaymentEU($params);

                        if (is_array($payment_options)) {
                            $title = !empty($payment->title) ? $payment->title : $payment_options['cta_text'];
                            $description = !empty($payment->description) ? $payment->description : $payment_options['cta_text'];
                            $logo = array_key_exists('logo', $payment_options) ? $payment_options['logo'] : '';
                            $action = array_key_exists('action', $payment_options) ? $payment_options['action'] : '';
                            $form = array_key_exists('form', $payment_options) ? $payment_options['form'] : '';

                            $find_image = false;
                            if (!empty($payment->name_image)) {
                                $logo = _PS_MODULE_DIR_.$this->name.'/views/img/payments/'.$payment->name_image;

                                if (file_exists($logo)) {
                                    $logo = $this->onepagecheckoutps_dir.'views/img/payments/'.$payment->name_image;

                                    $find_image = true;
                                }
                            }

                            if (!$find_image) {
                                $path_image = _PS_MODULE_DIR_.$this->name.'/views/img/payments/'.$module['name'];

                                if (file_exists($path_image.'.png')) {
                                    $logo = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'.png';
                                } elseif (file_exists($path_image.'.gif')) {
                                    $logo = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'.gif';
                                } elseif (file_exists($path_image.'.jpeg')) {
                                    $logo = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'.jpeg';
                                }
                            }

                            $output_eu[$i] = array(
                                'id'          => $module_instance_eu->id,
                                'title'       => $title,
                                'description' => $description,
                                'name'        => $module_instance_eu->name,
                                'author'      => $module_instance_eu->author,
                                'version'     => $module_instance_eu->version,
                                'form'        => $form,
                                'logo'        => $logo,
                                'action'      => $action,
                                'additional'  => array()
                            );
                        }
                    } else if ($module_instance) {
                        //compatibilidad con nuestro modulo Min Of Payment OPC.
                        if ($this->core->isModuleActive('minofpaymentopc', 'isPaymentEnabled')) {
                            $minofpaymentopc = Module::getInstanceByName('minofpaymentopc');
                            if (Validate::isLoadedObject($minofpaymentopc)) {
                                $dif = $minofpaymentopc->isPaymentEnabled($module['id_module']);
                                if ($dif !== true) {
                                    $dif = Tools::displayPrice(
                                        $dif,
                                        new Currency((int) $this->context->cart->id_currency)
                                    );
                                    $name_module = $module['name'];

                                    if (Validate::isLoadedObject($payment)) {
                                        $name_module = $payment->title;
                                    }

                                    $msg = sprintf($this->l('%s amount remaining to enable: %s'), $dif, $name_module);

                                    echo '<div class="row alert alert-info minofpaymentopc">'.$msg.'</div>';

                                    continue;
                                }
                            }
                        }

                        if (Module::isInstalled('universalpay')) {
                            $universalpay = Module::getInstanceByName('universalpay');
                            if ($universalpay->active) {
                                Configuration::updateValue('universalpay_onepage', 0);
                            }
                        }

                        if (!$module_instance->currencies
                            || ($module_instance->currencies
                                && count(Currency::checkPaymentCurrencies($module_instance->id)))
                        ) {
                            $path_image = _PS_MODULE_DIR_.$this->name.'/views/img/payments/'.$module['name'];
                            $exist_img_multishop = false;

                            if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(true, null, true)) != 1) {
                                if (file_exists($path_image.'_'.$this->context->shop->id.'.png')) {
                                    $url_image = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'_'.$this->context->shop->id.'.png';
                                    $exist_img_multishop = true;
                                } elseif (file_exists($path_image.'_'.$this->context->shop->id.'.gif')) {
                                    $url_image = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'_'.$this->context->shop->id.'.gif';
                                    $exist_img_multishop = true;
                                } elseif (file_exists($path_image.'_'.$this->context->shop->id.'.jpeg')) {
                                    $url_image = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'_'.$this->context->shop->id.'.jpeg';
                                    $exist_img_multishop = true;
                                }
                            }

                            if (!$exist_img_multishop) {
                                $find_image = false;
                                if (Validate::isLoadedObject($payment) && !empty($payment->name_image)) {
                                    $url_image = _PS_MODULE_DIR_.$this->name.'/views/img/payments/'.$payment->name_image;

                                    if (file_exists($url_image)) {
                                        $url_image = $this->onepagecheckoutps_dir.'views/img/payments/'.$payment->name_image;
                                        
                                        $find_image = true;
                                    }
                                }

                                if (!$find_image) {
                                    if (file_exists($path_image.'.png')) {
                                        $url_image = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'.png';
                                    } elseif (file_exists($path_image.'.gif')) {
                                        $url_image = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'.gif';
                                    } elseif (file_exists($path_image.'.jpeg')) {
                                        $url_image = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'.jpeg';
                                    } else {
                                        $url_image = $this->onepagecheckoutps_dir.'views/img/payments/default.png';
                                    }
                                }
                            }

                            $output[$i] = array(
                                'id'          => (int) $module_instance->id,
                                'title'       => '',
                                'description' => '',
                                'url_image'   => $url_image,
                                'name'        => $module['name'],
                                'author'      => $module_instance->author,
                                'version'     => $module_instance->version,
                                'modules_external_image' => $modules_external_image,
                                'url_payment' => '',
                                'force_display' => false,
                                'additional'  => array()
                            );

                            //get title and description of payment
                            if (Validate::isLoadedObject($payment)) {
                                $output[$i]['title_opc']       = $payment->title;
                                $output[$i]['description_opc'] = $payment->description;
                            }

                            $modules_force_display = array('optimalpayments',
                                'mercadopagobr',
                                'pronesis_bancasella',
                                'amanpay',
                                'stripe_official',
                                'psbraintreepayment',
                                'postfinancecw_postfinancecard',
                                'postfinancecw_postfinanceefinance',
                                'a4pwebpaymentspro'
                            );
                            
                            if (in_array($module['name'], $modules_force_display) && !$this->config_vars['OPC_PAYMENTS_WITHOUT_RADIO']) {
                                if (empty($output[$i]['title_opc'])) {
                                    $output[$i]['title_opc'] = $module['name'];
                                }
                                $output[$i]['force_display'] = true;
                                $output[$i]['url_payment'] = $this->context->link->getModuleLink(
                                    $this->name,
                                    'payment',
                                    array('pm' => $module['name'])
                                );
                            } else {
                                if (Validate::isLoadedObject($payment) && $payment->force_display) {
                                    $output[$i]['force_display'] = $payment->force_display;
                                    $output[$i]['url_payment']   = $this->context->link->getModuleLink(
                                        $this->name,
                                        'payment',
                                        array('pm' => $module['name'])
                                    );
                                } else {
                                    $html = '';

                                    if ($module['name'] == 'twenga') {
                                        if (is_callable(array($module_instance, 'hookPayment'))) {
                                            $html .= call_user_func(array($module_instance, 'hookPayment'), $hook_args);
                                        }
                                        if (empty($html) && is_callable(array($module_instance, 'hookDisplayPayment'))) {
                                            $html = call_user_func(array($module_instance, 'hookDisplayPayment'), $hook_args);
                                        }
                                    } else {
                                        if (is_callable(array($module_instance, 'hookPayment'))) {
                                            $html = call_user_func(array($module_instance, 'hookPayment'), $hook_args);
                                        }
                                        if (empty($html) && is_callable(array($module_instance, 'hookDisplayPayment'))) {
                                            $html = call_user_func(array($module_instance, 'hookDisplayPayment'), $hook_args);
                                        }

                                        if ($module_instance->name == 'redsys' && $module_instance->author == 'idnovate') {
                                            if (empty($html)) {
                                                continue;
                                            }
                                        }
                                    }

                                    # fix Moneybookers relative path to images
                                    $html = preg_replace('/src="modules\//', 'src="'.__PS_BASE_URI__.'modules/', $html);

                                    # OPCKT fix Paypal relative path to redirect script
                                    $html = preg_replace('/href="modules\//', 'href="'.__PS_BASE_URI__.'modules/', $html);

                                    $payment_methods = array();

                                    preg_match_all(
                                        '/<a.*?>[^>]*?<img[^>]*?src="(.*?)".*?\/?>(.*?)<\/a>/ms',
                                        $html,
                                        $matches_1,
                                        PREG_SET_ORDER
                                    );
                                    preg_match_all(
                                        '/<input [^>]*?type="image".*?src="(.*?)".*?>.*?<span.*?>(.*?)<\/span>/ms',
                                        $html,
                                        $matches_2,
                                        PREG_SET_ORDER
                                    );
                                    preg_match_all(
                                        '/<a[^>]*?class="(.*?)".*?>(.*?)<\/a>/ms',
                                        $html,
                                        $matches_3,
                                        PREG_SET_ORDER
                                    );
                                    preg_match_all(
                                        '~\bbackground(-image)?\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i',
                                        $html,
                                        $matches_4
                                    );
                                    
                                    for ($x=0; $x < count($matches_3); $x++) {
                                        $matches_3[$x][3] =  $matches_3[$x][1];
                                        $matches_3[$x][1] =  preg_replace(
                                            '/.*?themes\//',
                                            'themes/',
                                            _THEME_IMG_DIR_
                                        );
                                        $matches_3[$x][1] .= $matches_3[$x][1].".png";

                                        if (count($matches_4['image'])) {
                                            $matches_3[$x][1] = $matches_4['image'][$x];
                                        }
                                    }

                                    $matches = array_merge($matches_1, $matches_2, $matches_3);

                                    $payment_number = 0;
                                    foreach ($matches as $match) {
                                        $index = $module_instance->id.'_'.$payment_number;

                                        $url_image_theme = preg_replace('/(\r)?\n/m', ' ', trim($match[1]));
                                        if (!file_exists($url_image_theme) &&
                                            !in_array($module_instance->name, $modules_external_image)) {
                                            $url_image_theme = $url_image;
                                        }

                                        if (file_exists($path_image.'_'.$payment_number.'.png')) {
                                            $url_image_theme = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'_'.$payment_number.'.png';
                                        } elseif (file_exists($path_image.'_'.$payment_number.'.gif')) {
                                            $url_image_theme = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'_'.$payment_number.'.gif';
                                        } elseif (file_exists($path_image.'_'.$payment_number.'.jpeg')) {
                                            $url_image_theme = $this->onepagecheckoutps_dir.'views/img/payments/'.$module['name'].'_'.$payment_number.'.jpeg';
                                        }

                                        $payment_methods[$index]['img'] = $url_image_theme;
                                        $payment_methods[$index]['description'] = preg_replace(
                                            '/\s/m',
                                            ' ',
                                            trim($match[2])
                                        );
                                        $payment_methods[$index]['class'] = '';

                                        if (isset($match[3])) {
                                            $class = trim($match[3]);
                                            if ($class != 'hidden') {
                                                $payment_methods[$index]['class'] = trim($class);
                                            }
                                        }

                                        $payment_number++;
                                    }

                                    //evita problema de cargar contenido en chrome.
                                    if (Configuration::get('PS_SSL_ENABLED')) {
                                        $html = str_replace('href="http://'.$this->context->shop->domain, 'href="https://'.$this->context->shop->domain_ssl, $html);
                                    }

                                    $output[$i]['html'] = htmlentities($html, ENT_COMPAT, 'UTF-8');
                                    $output[$i]['additional'] = $payment_methods;
                                }
                            }

                            $array_payment_need_register = explode(',', $this->config_vars['OPC_PAYMENT_NEED_REGISTER']);
                            if (count($array_payment_need_register) > 0) {
                                if (in_array($module['name'], $array_payment_need_register) && !$output[$i]['force_display']) {
                                    $payment_need_register = true;
                                }
                            }

                            //support module: paypal - v5.3.2 - thirty bees
                            if ($module_instance->name == 'paypal' && $module_instance->author == 'thirty bees') {
                                if (Configuration::get('PAYPAL_WPP_ENABLED')) {
                                    $payment_need_register = true;
                                }
                                //paypal/views/templates/hook/paypal_plus_payment.tpl
                                //if (typeof OnePageCheckoutPS !== typeof undefined) {
                                    //$('#ppplus').remove();
                                    //$('#onepagecheckoutps_step_three #payment_method_container').append('<div id="ppplus"></div>');
                                //}
                            }
                        }
                    }
                }
            }

            if ($set_id_customer_opc) {
                if ($this->core->isModuleActive('taxcloud')) {
                    $this->context->customer         = new Customer();
                    $this->context->customer->logged = 0;
                } else {
                    $this->context->customer->id = null;
                }
                unset($this->context->cookie->id_customer);

                $this->context->cart->id_customer = null;
                $this->context->cart->update();
                $this->context->cart->setNoMultishipping();
            }
        }

        $this->context->smarty->assign(array(
            'payment_modules' => Tools::jsonEncode($output),
            'payment_modules_eu' => Tools::jsonEncode($output_eu),
            'CONFIGS' => $this->config_vars,
            'payment_need_register' => $payment_need_register,
            'is_logged' => $this->context->customer->isLogged(),
            'is_guest' => $this->context->customer->isGuest(),
            'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop')
        ));

        if (file_exists(_PS_THEME_DIR_.'modules/onepagecheckoutps/views/templates/front/payment.tpl')) {
            $html = $this->context->smarty->fetch(_PS_THEME_DIR_.'/modules/onepagecheckoutps/views/templates/front/payment.tpl');
        } else {
            $html = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/payment.tpl');
        }

        //Support module: stripe_official - v2.0.4 - 202 ecommerce
        $stripe_official = $this->core->isModuleActive('stripe_official');
        if ($stripe_official) {
            $stripe_official->hookHeader();
            $this->context->smarty->assign('js_def', Media::getJsDef());
            $javascript = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_.'javascript.tpl');
            $html .= $javascript;
        }

        return $html;
    }

    /**
     * Remove address.
     *
     * @return array
     */
    public function removeAddress()
    {
        if ($this->context->customer->isLogged()) {
            $id_address = (int)Tools::getValue('id_address');

            $address = new Address($id_address);

            if (Validate::isLoadedObject($address)) {
                if ($this->context->customer->id == $address->id_customer) {
                    //Validate if address is used
                    $sql = new DbQuery();
                    $sql->select('id_address_delivery, id_address_invoice');
                    $sql->from('cart');
                    $sql->where('id_address_delivery = '.$id_address.' OR id_address_invoice = '.$id_address);

                    $address_in_use = Db::getInstance()->executeS($sql);

                    if ($address_in_use) {
                        $query = 'UPDATE '._DB_PREFIX_.'address SET deleted = 1 WHERE id_address = '.(int)$id_address;
                        Db::getInstance()->execute($query);
                    } else {
                        $query = 'DELETE FROM '._DB_PREFIX_.'address WHERE id_address = '.(int)$id_address;
                        Db::getInstance()->execute($query);
                    }

                    return array(
                        'message_code' => $this->core->CODE_SUCCESS
                    );
                }
            }
        }

        return array(
            'message_code' => $this->core->CODE_ERROR
        );
    }

    /**
     * Update address.
     *
     * @return array
     */
    public function updateAddress()
    {
        $id_customer = (int)Tools::getValue('id_customer');
        $id_address = (int)Tools::getValue('id_address');
        $object = Tools::getValue('object');
        $update_cart = Tools::getValue('update_cart');
        $is_set_invoice = (bool)Tools::getValue('is_set_invoice');
        $fields = Tools::jsonDecode(Tools::getValue('fields'));
        $id_address_old = $id_address;
        $update_address_order = false;

        if (!Validate::isLoadedObject($this->context->cart) && !Tools::getIsset('rc_page')) {
            return array(
                'message_code' => $this->core->CODE_ERROR
            );
        }

        if (empty($id_address)) {
            if ($this->context->customer->isLogged()) {
                $id_address = $this->getIdAddressAvailable($object);
            } else {
                if ($object == 'delivery') {
                    $id_address = $this->context->cart->id_address_delivery;
                } else if ($object == 'invoice') {
                    $id_address = $this->context->cart->id_address_invoice;

                    if ($update_cart
                        && $this->context->cart->id_address_delivery == $this->context->cart->id_address_invoice
                        && ($this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'] || !$this->context->cart->isVirtualCart())
                    ) {
                        if (!$this->context->customer->isLogged()) {
                            $id_address = $this->getIdAddressAvailable('invoice');
                        }
                    }
                }
            }
        } else {
            //se crea una nueva direccion en el caso de la direccion sea modificada y este usada ya en un pedido
            if ($this->context->customer->isLogged() && !$update_cart) {
                $address = new Address($id_address);

                if ($address->isUsed()) {
                    if ($address->delete()) {
                        $id_address = $this->getIdAddressAvailable($object);
                        $update_cart = true;
                        $update_address_order = true;
                    }
                }
            }
        }

        $address = new Address($id_address);
        $customer = new Customer($id_customer);
        $password = null;
        $custom_fields = array();

        $this->validateFields($fields, $customer, $address, $address, $password, $is_set_invoice, $custom_fields);

//        if (count($this->errors) > 0) {
//            return array(
//                'message_code' => $this->core->CODE_ERROR
//            );
//        }
        
        if (($object == 'delivery' && $this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA'])
            || ($object == 'invoice' && $this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA'])
        ) {
            if ($this->context->customer->isLogged() || $this->context->customer->isGuest()) {
                $address->firstname = $this->context->customer->firstname;
                $address->lastname  = $this->context->customer->lastname;
            } else {
                $address->firstname = '.';
                $address->lastname  = '.';
            }
        }

        if (Validate::isLoadedObject($address)) {
            if (empty($address->id_country)) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(_DB_PREFIX_.'address', array('id_country' => 0), 'UPDATE', 'id_address = '.$address->id);

                return array(
                    'message_code' => $this->core->CODE_ERROR,
                    'message' => $this->l('Select a country to show the different shipping options.')
                );
            }

            $country = new Country($address->id_country);
            if ($country->contains_states && empty($address->id_state)) {
                $address->id_state = null;
            } else {
                if (!$country->contains_states && !empty($address->id_state)) {
                    $address->id_state = null;
                }
            }

            if (count($custom_fields) > 0) {
                foreach ($custom_fields as $custom_field) {
                    $this->saveCustomFields($custom_field, $address->id);
                }
            }

            $address->update();

            if ($object == 'delivery') {
                $this->context->country->id_zone = Address::getZoneById((int) $address->id);

                if ($update_cart) {
                    $this->context->cart->id_address_delivery = $address->id;
                    if (!$is_set_invoice || $update_address_order) {
                        $this->context->cart->id_address_invoice = $address->id;
                    }
                    $this->context->cart->update();
                    $this->context->cart->setNoMultishipping();
                }
            } else if ($object == 'invoice') {
                if ($update_cart) {
                    if ($update_address_order && $this->context->cart->id_address_delivery == $id_address_old) {
                        $this->context->cart->id_address_delivery = $address->id;
                    }
                    if (!$this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'] && $this->context->cart->isVirtualCart()) {
                        $this->context->cart->id_address_delivery = $address->id;
                    }

                    $this->context->cart->id_address_invoice = $address->id;
                    $this->context->cart->update();
                    $this->context->cart->setNoMultishipping();
                }
            }

            return array(
                'message_code' => $this->core->CODE_SUCCESS,
                'id_address_delivery' => (int)$this->context->cart->id_address_delivery,
                'id_address_invoice' => (int)$this->context->cart->id_address_invoice
            );
        }

        return array(
            'message_code' => $this->core->CODE_ERROR
        );
    }

    /**
     * Remove invoice address.
     *
     * @return array
     */
    public function removeAddressInvoice()
    {
        if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
            if ($this->context->cart->id_address_delivery != $this->context->cart->id_address_invoice) {
                $query = 'DELETE FROM '._DB_PREFIX_.'address WHERE id_address = '.(int)$this->context->cart->id_address_invoice;
                Db::getInstance()->execute($query);
                $query = 'DELETE FROM '._DB_PREFIX_.'opc_customer_address WHERE id_address = '.(int)$this->context->cart->id_address_invoice;
                Db::getInstance()->execute($query);
            }
        }
        $this->context->cart->id_address_invoice = $this->context->cart->id_address_delivery;
        $this->context->cart->update();
        $this->context->cart->setNoMultishipping();
    }

    /**
     * Load summary of cart.
     *
     * @return html
     */
    public function loadReview()
    {
        $set_id_customer_opc = false;
        if (!$this->context->cookie->id_customer) {
            $this->context->cookie->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];

            if (empty($this->context->customer->id)) {
                $this->context->customer = new Customer($this->config_vars['OPC_ID_CUSTOMER']);
                $this->context->customer->logged = true;
            }

            if (empty($this->context->cart->id_customer)) {
                $this->context->cart->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];
            }

            $set_id_customer_opc = true;

            $this->context->cart->update();
            $this->context->cart->setNoMultishipping();
        }

        //compatibilidad con modulo quantitydiscountpro - V2.1.28 de idnovate
        if ($this->core->isModuleActive('quantitydiscountpro')) {
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);
        }

        //compatibilidad con modulo oleamultipromos
        if ($this->core->isModuleActive('oleamultipromos')) {
            Module::hookExec('oleaCartRefreshForMultiPromo');
        }

        //compatibilidad con modulo extendedwarranty
        if ($module = $this->core->isModuleActive('extendedwarranty')) {
            $this->context->smarty->assign('extendedwarranty', $module);
        }

        //compatibilidad con modulo attributewizardpro
        if ($module = $this->core->isModuleActive('attributewizardpro')) {
            $this->context->smarty->assign('attributewizardpro', $module);
            $module->hookHeader();
        }

        $this->context->controller->getController('OrderOpcController')->opcAssignSummaryInformations();

        // Assign wrapping and TOS
        $this->context->controller->getController('OrderOpcController')->opcAssignWrappingAndTOS();

        if ($old_message = Message::getMessageByCartId((int) $this->context->cart->id)) {
            $this->context->smarty->assign('oldMessage', $old_message['message']);
        }

        $this->context->smarty->assign(array(
            'customer' => ($this->context->customer->isLogged() ? $this->context->customer : false),
            'onepagecheckoutps' => $this,
            'CONFIGS'               => $this->config_vars,
            'ONEPAGECHECKOUTPS_IMG' => $this->onepagecheckoutps_dir.'views/img/',
            'ONEPAGECHECKOUTPS_TPL' => $this->onepagecheckoutps_tpl,
            'PS_WEIGHT_UNIT'        => Configuration::get('PS_WEIGHT_UNIT'),
            'IS_VIRTUAL_CART'       => $this->context->cart->isVirtualCart()
        ));

        $total_free_ship = 0;
        $free_ship       = Tools::convertPrice(
            (float) Configuration::get('PS_SHIPPING_FREE_PRICE'),
            new Currency((int) $this->context->cart->id_currency)
        );

        //compatibilidad con modulo lgfreeshippingzones
        if (empty($free_ship) && $module = $this->core->isModuleActive('lgfreeshippingzones')) {
            $id_zone_tmp    = $this->context->country->id_zone;
            $id_carrier_tmp = $this->context->cart->id_carrier;

            $query = new DbQuery();
            $query->select('price');
            $query->from('lgfreeshippingzones');
            $query->where('id_zone = '.(int)$id_zone_tmp);
            if (Tools::version_compare($module->version, '1.0', '>')) {
                $query->where('id_carrier = '.(int)$id_carrier_tmp);
            }

            $free_ship = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        } else {
            $carrier = new Carrier($this->context->cart->id_carrier);

            if (Validate::isLoadedObject($carrier)) {
                if ($carrier->shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->is_free == 0) {
                    $total_products = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                    $ranges = RangePrice::getRanges((int)$carrier->id);
                    $id_zone = Address::getZoneById((int)$this->context->cart->id_address_delivery);

                    foreach ($ranges as $range) {
                        $query = new DbQuery();
                        $query->select('price');
                        $query->from('delivery');
                        $query->where('id_range_price = '.(int)$range['id_range_price']);
                        $query->where('id_zone = '.(int)$id_zone);
                        $query->where('id_carrier = '.(int)$carrier->id);
                        $cost_shipping = Db::getInstance()->getValue($query);

                        $delimiter1 = Tools::convertPrice($range['delimiter1'], $this->context->currency);
                        $cost_shipping = Tools::convertPrice($cost_shipping, $this->context->currency);

                        if ($cost_shipping == 0 && $total_products < $delimiter1) {
                            $free_ship = $delimiter1;
                            break;
                        }
                    }
                }
            }
        }

        if ($free_ship) {
            $discounts         = $this->context->cart->getCartRules();
            $total_discounts   = $this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
            $total_products_wt = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
            $total_free_ship   = $free_ship - ($total_products_wt - $total_discounts);

            foreach ($discounts as $discount) {
                if ($discount['free_shipping'] == 1) {
                    $total_free_ship = 0;
                    break;
                }
            }
        }
        $this->context->smarty->assign('free_ship', $total_free_ship);
        $this->context->smarty->assign('free_ship_preferences', $free_ship);

        $js_files  = array();
        $css_files = array();

        if ($this->core->isModuleActive('crossselling_mod')
            && file_exists(dirname(__FILE__).'/../crossselling_mod/js/crossselling_mod.js')
        ) {
            array_push($js_files, $this->core->getUrlStore().$this->context->shop->getBaseURI().'modules/crossselling_mod/js/crossselling_mod.js');
        }

        $this->context->smarty->assign('js_files', $js_files);
        $this->context->smarty->assign('css_files', $css_files);

        $this->addCODFee();
        $this->addBankWireDiscount();
        $this->addPaypalFee();
        $this->addSeQuraFee();
        $this->addModulesExtraFee();

        //soluciona el problema de mostrar el texto CON IVA cuando colocan el vat_numer sin estar logeado.
        //tiene que ir este codigo antes de que se quite el customer del OPC.
        $this->context->smarty->assign(array(
            'priceDisplay'    => Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer),
        ));

        if ($set_id_customer_opc) {
            $this->context->customer = new Customer();
            $this->context->customer->logged = false;
            unset($this->context->cookie->id_customer);

            $this->context->cart->id_customer = null;
            $this->context->cart->update();
            $this->context->cart->setNoMultishipping();
        }

        $this->context->smarty->assign(array(
            'is_logged' => $this->context->customer->isLogged(),
            'is_guest' => $this->context->customer->isGuest()
        ));

        $html = '';

        // Check minimal amount
        $minimal_purchase = $this->checkMinimalPurchase();
        if (!empty($minimal_purchase)) {
            $html .= '<div class="alert alert-warning">'.$minimal_purchase.'</div>';
        }

        if (file_exists(_PS_THEME_DIR_.'modules/onepagecheckoutps/views/templates/front/review_header.tpl')) {
            $html .= $this->context->smarty->fetch(_PS_THEME_DIR_.'/modules/onepagecheckoutps/views/templates/front/review_header.tpl');
        } else {
            $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/review_header.tpl');
        }

        if ($this->config_vars['OPC_COMPATIBILITY_REVIEW']) {
            $html .= $this->context->smarty->fetch(_PS_THEME_DIR_.'shopping-cart.tpl');
        } else {
            if (file_exists(_PS_THEME_DIR_.'modules/onepagecheckoutps/views/templates/front/review.tpl')) {
                $html .= $this->context->smarty->fetch(_PS_THEME_DIR_.'/modules/onepagecheckoutps/views/templates/front/review.tpl');
            } else {
                $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/review.tpl');
            }
        }

        if (file_exists(_PS_THEME_DIR_.'modules/onepagecheckoutps/views/templates/front/review_footer.tpl')) {
            $html .= $this->context->smarty->fetch(_PS_THEME_DIR_.'/modules/onepagecheckoutps/views/templates/front/review_footer.tpl');
        } else {
            $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/review_footer.tpl');
        }

        return $html;
    }

    public function addCODFee()
    {
        //comaptibilidad con modulo maofree_cashondeliveryfee
        $module = $this->core->isModuleActive('maofree_cashondeliveryfee', 'getCostCOD');
        if ($module !== false) {
            if (method_exists($module, 'getCostCOD')) {
                $this->context->smarty->assign(array(
                    'cod_id_module_payment' => $module->id,
                    'COD_FEE'               => $module->getCostCOD($this->context->cart, 2)
                ));
            } elseif (method_exists($module, 'getCost')) {
                $this->context->smarty->assign(array(
                    'cod_id_module_payment' => $module->id,
                    'COD_FEE'               => $module->getCost($this->context->cart, 2)
                ));
            } elseif (method_exists($module, 'getCostFee')) {
                $this->context->smarty->assign(array(
                    'cod_id_module_payment' => $module->id,
                    'COD_FEE'               => $module->getCostFee($this->context->cart)
                ));
            }
        }

        //comaptibilidad con modulo cashondeliverywithfee
        $module = $this->core->isModuleActive('cashondeliverywithfee', 'getCostValidated');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'cod_id_module_payment' => $module->id,
                'COD_FEE'               => $module->getCostValidated($this->context->cart)
            ));
        }

        //comaptibilidad con modulo megareembolso
        $module = $this->core->isModuleActive('megareembolso', 'getCost');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'cod_id_module_payment' => $module->id,
                'COD_FEE'               => $module->getCost($this->context->cart)
            ));
        }

        $module = $this->core->isModuleActive('cashondelivery');
        if ($module !== false) {
            if ($module->author != 'PrestaShop') {
                //comaptibilidad con modulo cashondelivery modificado
                if (file_exists(_PS_MODULE_DIR_.'cashondelivery/recargominimo.php')) {
                    $RecargoMinimo = 2.95;

                    include _PS_MODULE_DIR_.'cashondelivery/recargominimo.php';

                    $config      = Configuration::getMultiple(array('COD_CARRIERS', 'COD_FEE', 'COD_FEEFREE'));
                    $fee         = $config['COD_FEE'];
                    $feefree     = $config['COD_FEEFREE'];
                    $amount_paid = 0;

                    foreach ($this->context->cart->getProducts() as $product) {
                        $amount_paid += $product['price'];
                        $pd = ProductDownload::getIdFromIdProduct((int) $product['id_product']);
                        if ($pd && Validate::isUnsignedInt($pd)) {
                            return false;
                        }
                    }

                    if ($feefree > 0 && $amount_paid > $feefree) {
                        $fee = 0;
                    } else {
                        $cart_total_paid = (float) Tools::ps_round((float) $this->context->cart->getOrderTotal(true, Cart::BOTH), 2);

                        if ($fee > 100) {
                            $fee = ($fee / 100);

                            if (isset($RecargoMinimo) && $fee < $RecargoMinimo) {
                                $fee = $RecargoMinimo;
                            }

    //                        $fee = ($cart_total_paid + $fee);
                        } else {
                            $fee = $cart_total_paid / 100 * $fee;

                            if ($fee > $cart_total_paid) {
                                $fee = $fee / 100;
                            }

                            if (isset($RecargoMinimo) && $fee < $RecargoMinimo) {
                                $fee = $RecargoMinimo;
                            }
                        }

                        $fee = Tools::convertPrice($fee, $this->context->cart->id_currency);
                    }

                    $this->context->smarty->assign(array('cod_id_module_payment' => $module->id, 'COD_FEE' => $fee));
                }
            } else {
                // compatibilidad con modulo cashondelivery - V1.5.2.1 de PrestaShop.
                $cod_fee        = (int)Configuration::get('COD_FEE');
                $cod_feefree    = (int)Configuration::get('COD_FEEFREE');

                if ($cod_feefree > 0
                    && $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING) > $cod_feefree
                ) {
                    $cod_fee = 0;
                }

                if ($cod_fee > 0) {
                    $this->context->smarty->assign(array('cod_id_module_payment' => $module->id, 'COD_FEE' => $cod_fee));
                }
            }
        }

        //comaptibilidad con modulo cashondeliverywithfeeaural
        $module = $this->core->isModuleActive('cashondeliverywithfeeaural', 'getCost');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'cod_id_module_payment' => $module->id,
                'COD_FEE' => number_format($module->getCost(array('cart' => $this->context->cart)), 2, '.', ''),
            ));
        }

        //comaptibilidad con modulo pi_cashondelivery
        $module = $this->core->isModuleActive('pi_cashondelivery', 'getFee');
        if ($module !== false) {
            $total_shippable = 0;
            $this->context->smarty->assign(array(
                'cod_id_module_payment' => $module->id,
                'COD_FEE' => $module->getFee($this->context->cart, $total_shippable, true),
            ));
        }

        //comaptibilidad con modulo deluxecodfees
        $module = $this->core->isModuleActive('deluxecodfees', 'getFeeCost');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'cod_id_module_payment' => $module->id,
                'COD_FEE' => (float) Tools::ps_round((float) $module->getFeeCost($this->context->cart), 2),
            ));
        }

        $module = $this->core->isModuleActive('cashondeliverypro', 'getFeeCost');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'cod_id_module_payment' => $module->id,
                'COD_FEE' => number_format($module->getFeeCost($this->context->cart), 2, '.', ''),
            ));
        }

        $module = $this->core->isModuleActive('lc_paywithfee', 'lc_calculateFee');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'cod_id_module_payment' => $module->id,
                'COD_FEE' => number_format($module->lc_calculateFee(), 2, '.', ''),
            ));
        }
    }

    public function addBankWireDiscount()
    {
        //comaptibilidad con modulo bankwire_plus
        $module = $this->core->isModuleActive('bankwire_plus');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'bnkplus_id_module_payment' => $module->id,
                'BNKPLUS_DISCOUNT' =>
                number_format($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) * ((float) Configuration::get('ORDER_DISCOUNT') / 100), 2),
            ));
        }

        $module = $this->core->isModuleActive('transferenciabancaria', 'getDescuento');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'bnkplus_id_module_payment' => $module->id,
                'BNKPLUS_DISCOUNT' => $module->getDescuento($this->context->cart),
            ));
        }

        $module = $this->core->isModuleActive('discountedbw', 'getDiscount');
        if ($module !== false) {
            $discount = $module->getDiscount();
            $this->context->smarty->assign(array(
                'bnkplus_id_module_payment' => $module->id,
                'BNKPLUS_DISCOUNT' => $discount[1]
            ));
        }

        $module = $this->core->isModuleActive('bankwirediscount', 'getBankwireDiscount');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'bnkplus_id_module_payment' => $module->id,
                'BNKPLUS_DISCOUNT' => $module->getBankwireDiscount($this->context->cart->getOrderTotal(true, 3)),
            ));
        }

        $module = $this->core->isModuleActive('bankwire_discount');
        if ($module !== false) {
            $total_tmp         = $this->context->cart->getOrderTotal(true, Cart::BOTH);
            $this->context->cart->addCartRule(Configuration::get('BANK_WIRE_DISCOUNT_RULEID'));
            $total             = $this->context->cart->getOrderTotal(true, Cart::BOTH);
            $this->context->cart->removeCartRule(Configuration::get('BANK_WIRE_DISCOUNT_RULEID'));
            $discount_quantity = $total_tmp - $total;

            $this->context->smarty->assign(array(
                'bnkplus_id_module_payment' => $module->id,
                'BNKPLUS_DISCOUNT'          => $discount_quantity
            ));
        }
    }

    public function addPaypalFee()
    {
        //comaptibilidad con modulo paypalcargo
        $module = $this->core->isModuleActive('paypalcargo');
        if ($module !== false) {
            //se realiza el calculo aqui ya que el metodo CargoTotalPaypal del modulo esta malo.
            $percent  = (float) Configuration::get('PAYPAL_FEESPERCENT');
            $fixed    = (float) Configuration::get('PAYPAL_FEESFIXED');
            $percentd = (float) $percent / 100;

            if ($percent > 0) {
                $totalfees = (float) $this->context->cart->getOrderTotal() * $percentd;
            }
            if ($fixed > 0) {
                $totalfees += $fixed;
            }

            $this->context->smarty->assign(array(
                'paypal_id_module_payment' => $module->id,
                'PAYPAL_FEE'               => $totalfees
            ));
        }

        $module = $this->core->isModuleActive('paypal');
        if ($module !== false) {
            //comaptibilidad con modulo paypal con recargo de alabaz web
            if (method_exists($module, 'getCost')) {
                $this->context->smarty->assign(array(
                    'paypal_id_module_payment' => $module->id,
                    'PAYPAL_FEE'               => $module->getCost($this->context->cart),
                ));
            } elseif ($module->author != 'PrestaShop') {
                if (Configuration::get('PAYPAL_FEESPERCENT') !== false
                    || Configuration::get('PAYPAL_FEESFIXED') !== false
                ) {
                    //comaptibilidad con modulo paypal de Webhome.es
                    $percent  = (float) !Configuration::get('PAYPAL_FEESPERCENT') ? 0 : Configuration::get('PAYPAL_FEESPERCENT');
                    $fixed    = (float) Configuration::get('PAYPAL_FEESFIXED');
                    $percentd = (float) $percent / 100;

                    if ($percent > 0) {
                        $totalfees = (float) $this->context->cart->getOrderTotal(true, 3) * $percentd;
                        if ($fixed > 0) {
                            $totalfees += $fixed;
                        }
                    } else {
                        if ($fixed > 0) {
                            $totalfees = (float) $fixed;
                        }
                    }

                    $this->context->smarty->assign(array(
                        'paypal_id_module_payment' => $module->id,
                        'PAYPAL_FEE'               => $totalfees
                    ));
                }
            }
        }

        //comaptibilidad con modulo pd_paypal
        $module = $this->core->isModuleActive('pd_paypal');
        if ($module !== false) {
            $shipping_cost_wt = $this->context->cart->getTotalShippingCost();
            $total            = $this->context->cart->getOrderTotal();

            $value_fees_percent  = Configuration::get('PD_PP_FEESPERCENT');
            $percentd            = $value_fees_percent / 100;
            $fees_value_fee      = Configuration::get('PD_PP_FEESFEE');
            $total_cart_products = $total;

            $total_cart_shipping = $shipping_cost_wt;
            $ammount_extra_tpl   = (($total_cart_products + $total_cart_shipping) * $percentd) + $fees_value_fee;

            $this->context->smarty->assign(array(
                'paypal_id_module_payment' => $module->id,
                'PAYPAL_FEE'               => $ammount_extra_tpl
            ));
        }

        $module = $this->core->isModuleActive('paypalwithfee', 'getFee');
        if ($module !== false) {
            $this->context->smarty->assign(array(
                'paypal_id_module_payment' => $module->id,
                'PAYPAL_FEE' => $module->getFee($this->context->cart),
            ));
        }
    }

    public function addSeQuraFee()
    {
        $module = $this->core->isModuleActive('sequrapayment');
        if ($module !== false) {
            $fee = $module->fee()->withTax();
            if ($fee > 0 && SequraPreQualifier::canDisplayInfo('SEQURA_ACTIVE', $this->context->cart->getOrderTotal(true))) {
                $this->context->smarty->assign(
                    array(
                        'sequra_id_module_payment' => $module->id,
                        'SEQURA_FEE'               => Tools::ps_round($fee/100, 2)
                    )
                );
            }
        }
    }

    public function addModulesExtraFee()
    {
        $payment_modules_fee = array();
        $total = $this->context->cart->getOrderTotal();
        $label_fee = $this->l('Additional fees for payment').':';
        $label_fee_tax = $this->l('Fee tax').':';
        $label_total = $this->l('Total + Fee');

        $module = $this->core->isModuleActive('bvkpaymentfees');
        if ($module !== false) {
            if (method_exists('Cart', 'getFee')) {
                $payment_modules = PaymentModule::getInstalledPaymentModules();

                foreach ($payment_modules as $payment) {
                    $this->context->cart->getFee($payment['name']);

                    if (!empty($this->context->cart->feeamount)) {
                        if ($this->context->cart->feeamount < 0) {
                            $label_fee = $this->l('Discount for payment');
                            $label_total = $this->l('Total - Discount');
                        }

                        $payment_modules_fee[$payment['name']] = array(
                            'id' => $payment['id_module'],
                            'label_fee' => $label_fee,
                            'label_total' => $label_total,
                            'fee' => Tools::displayPrice($this->context->cart->feeamount),
                            'total_fee' => Tools::displayPrice($total + $this->context->cart->feeamount)
                        );
                    }
                }
            }
        }

        $module = $this->core->isModuleActive('bestkit_paymentfee');
        if ($module !== false) {
            $payment_modules = PaymentModule::getInstalledPaymentModules();

            foreach ($payment_modules as $payment) {
                $bestkit_fee = BestkitPaymentfee::getRowByModuleName($payment['name']);

                if ($bestkit_fee) {
                    $fee = 0;
                    $total_payment = 0;

                    if (!empty($bestkit_fee['value_percent'])) {
                        $total_tmp = $this->context->cart->getOrderTotal($bestkit_fee['tax'], $bestkit_fee['type']);

                        if ($bestkit_fee['value_percent'] > 1) {
                            $percent = $bestkit_fee['value_percent'] * 100 - 100;
                            $fee = ($percent * $total_tmp) / 100;

                            $total_payment = $total + $fee + $bestkit_fee['value_amount'];
                        } else if ($bestkit_fee['value_percent'] < 1 && $bestkit_fee['value_percent'] > 0) {
                            $percent = (1 - $bestkit_fee['value_percent']) * 100;
                            $fee = ($percent * $total_tmp) / 100;

                            $total_payment = $total - $fee + $bestkit_fee['value_amount'];

                            $label_fee = $this->l('Discount for payment');
                            $label_total = $this->l('Total - Discount');
                        }
                    } else if ($bestkit_fee['value_amount']) {
                        $fee = $bestkit_fee['value_amount'];
                        $total_payment = $total + $fee;
                    }

                    if (!empty($fee)) {
                        $payment_modules_fee[$payment['name']] = array(
                            'id' => $payment['id_module'],
                            'label_fee' => $label_fee,
                            'label_total' => $label_total,
                            'fee' => Tools::displayPrice($fee),
                            'total_fee' => Tools::displayPrice($total_payment)
                        );
                    }
                }
            }
        }

        //compatibilidad con modulo: paymentfee v1.0 de IdeaCommerce
        $module = $this->core->isModuleActive('paymentfee');
        if ($module !== false) {
            $fees = $module->getFees(true);

            if (count($fees)) {
                $paymentmodules = PaymentModule::getInstalledPaymentModules();

                foreach ($paymentmodules as $payment) {
                    $fee = $module->getFeesByModule($payment['name']);

                    if (((float)$fee['percent'] > 0 || (float)$fee['fixed'] > 0)) {
                        $surcharge = Tools::ps_round($module->getTotalFees($this->context->cart->getOrderTotal(), (float)$fee['percent'], (float)$fee['fixed']), 2);

                        if ($surcharge > 0) {
                            $payment_modules_fee[$payment['name']] = array(
                                'id' => $payment['id_module'],
                                'label_fee' => $label_fee,
                                'label_total' => $label_total,
                                'fee' => Tools::displayPrice($surcharge),
                                'total_fee' => Tools::displayPrice($total + $surcharge)
                            );
                        }
                    }
                }
            }
        }
        
        //compatibilidad con modulo: custompaymentmethod v1.3.4 de SeoSa
        $module = $this->core->isModuleActive('custompaymentmethod');
        if ($module !== false && version_compare($module->version, '1.3.4', '>=')) {
            $payments = CustomPayment::getCustomPaymentMethodsCollection($this->context->language->id, true);

            foreach ($payments as $payment) {
                $fee = CustomPaymentMethod::getCommission($this->context->cart, $payment, $this->context);
                $discount = CustomPaymentMethod::getDiscount($this->context->cart, $payment, $this->context);

                if (!empty($fee)) {
                    $payment_modules_fee['custompaymentmethod_'.$payment->id] = array(
                        'id' => $payment->id,
                        'label_fee' => $label_fee,
                        'label_total' => $label_total,
                        'fee' => Tools::displayPrice($fee),
                        'total_fee' => Tools::displayPrice($total + $fee)
                    );
                } elseif (!empty($discount)) {
                    $label_fee = $this->l('Discount for payment');
                    $label_total = $this->l('Total - Discount');

                    $payment_modules_fee['custompaymentmethod_'.$payment->id] = array(
                        'id' => $payment->id,
                        'label_fee' => $label_fee,
                        'label_total' => $label_total,
                        'fee' => Tools::displayPrice($discount * -1),
                        'total_fee' => Tools::displayPrice($total + $discount)
                    );
                }
            }
        }

        //compatibilidad con modulo: paymentdiscountssurcharges v1.0.1 de Silbersaiten
        $module = $this->core->isModuleActive('paymentdiscountssurcharges');
        if ($module !== false) {
            if (method_exists($module, 'getDiscountsSurchargesForFront')) {
                $payment_modules = $module->getDiscountsSurchargesForFront();

                foreach ($payment_modules as $payment) {
                    $module_payment = Module::getInstanceByName($payment['folder']);

                    if (!empty($payment['pds_value'])) {
                        $payment_modules_fee[$module_payment->name] = array(
                            'id' => $module_payment->id,
                            'label_fee' => $payment['pds_value'] < 0 ? $this->l('Discount for payment') : $label_fee,
                            'label_total' => $payment['pds_value'] < 0 ? $this->l('Total - Discount') : $label_total,
                            'fee' => Tools::displayPrice($payment['pds_value']),
                            'total_fee' => Tools::displayPrice($total + $payment['pds_value'])
                        );
                    }
                }
            }
        }

        //compatibilidad con modulo: paymentcommission v1.3.4 de SeoSa
        $module = $this->core->isModuleActive('paymentcommission');
        if ($module !== false) {
            $paymentmodules = PaymentModule::getInstalledPaymentModules();

            foreach ($paymentmodules as $payment) {
                $fee = CommissionPayment::calculateCommission(
                    $this->context->cart,
                    $payment['name'],
                    null,
                    'commission'
                );

                if ($fee != 0) {
                    $payment_modules_fee[$payment['name']] = array(
                        'id' => $payment['id_module'],
                        'label_fee' => $label_fee,
                        'label_total' => $label_total,
                        'fee' => Tools::displayPrice($fee),
                        'total_fee' => Tools::displayPrice($total + $fee)
                    );
                }
            }
        }

        //comaptibilidad con modulo cashondeliveryplus
        $module = $this->core->isModuleActive('cashondeliveryplus');
        if ($module !== false) {
            if (method_exists($module, 'getChargeValue') && ($module->author == 'caleydon.com')) {
                if (method_exists($module, 'getChargeValue')) {
                    $fee = $module->getChargeValue();
                } else {
                    $fee = 0;
                }
                $payment_modules_fee['cashondeliveryplus'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_fee_tax' => $label_fee_tax,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            } else {
                $fee_tax = 0;

                if (method_exists($module, 'getFeeForCart')) {//with tax
                    $fee = $module->getFeeForCart($this->context->cart->id_carrier, 0, $this->context->cart, true);
                    $fee_without = $module->getFeeForCart($this->context->cart->id_carrier, 0, $this->context->cart, false);
                    $fee_tax = $fee - $fee_without;
                    $fee = $fee_without;
                } else {
                    $fee     = Configuration::get('COD_FEE');
                    $feefree = Configuration::get('COD_FEEFREE');

                    if ($feefree > 0 && $this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) > $feefree) {
                        $fee = 0;
                    }
                }

                $payment_modules_fee['cashondeliveryplus'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_fee_tax' => $label_fee_tax,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'fee_tax' => Tools::displayPrice($fee_tax),
                    'total_fee' => Tools::displayPrice($total + $fee + $fee_tax)
                );
            }
        }

        $module = $this->core->isModuleActive('codfee');
        if ($module !== false) {
            $fee = 0;
            if (method_exists($module, 'getFee') && ($module->author == 'presta-apps' || $module->author == 'Presta-Apps Solutions')) {
                $fee = $module->getFee($this->context->cart);
            } elseif (method_exists($module, 'getFeeCost') && $module->author == 'idnovate') {
                include_once _PS_MODULE_DIR_.'codfee/classes/CodfeeConfiguration.php';

                $id_lang = $this->context->cart->id_lang;
                $id_shop = $this->context->cart->id_shop;
                $customer = new Customer($this->context->cart->id_customer);
                $customer_groups = $customer->getGroupsStatic($customer->id);
                $carrier = new Carrier($this->context->cart->id_carrier);
                $carrier = $carrier->id_reference;
                $address = new Address($this->context->cart->id_address_delivery);
                $country = new Country($address->id_country);
                if ($address->id_state > 0) {
                    $zone = State::getIdZone($address->id_state);
                } else {
                    if (!Validate::isLoadedObject($country)) {
                        $id_country = FieldClass::getDefaultValue('delivery', 'id_country');
                    } else {
                        $id_country = $country->id;
                    }
                    $zone = $country->getIdZone($id_country);
                }
                $manufacturers = '';
                $suppliers = '';
                $products = $this->context->cart->getProducts();
                foreach ($products as $product) {
                    $manufacturers .= $product['id_manufacturer'].';';
                    $suppliers .= $product['id_supplier'].';';
                }
                $manufacturers = explode(';', trim($manufacturers, ';'));
                $manufacturers = array_unique($manufacturers, SORT_REGULAR);
                $suppliers = explode(';', trim($suppliers, ';'));
                $suppliers = array_unique($suppliers, SORT_REGULAR);
                $order_total = $this->context->cart->getOrderTotal(true, 3);
                $codfeeconf = new CodfeeConfiguration();
                $codfeeconf = $codfeeconf->getFeeConfiguration($id_shop, $id_lang, $customer_groups, $carrier, $country, $zone, $products, $manufacturers, $suppliers, $order_total);
                $fee = (float)Tools::ps_round((float)$module->getFeeCost($this->context->cart, $codfeeconf), 2);
            } elseif (method_exists($module, 'getCostValidated')) {
                $fee = $module->getCostValidated($this->context->cart);
            } elseif (method_exists($module, 'getCost')) {
                $fee = $module->getCost(array('cart' => $this->context->cart));
            } elseif (method_exists($module, 'getFeeCost')) {
                $fee = $module->getFeeCost($this->context->cart);
            }

            $payment_modules_fee['codfee'] = array(
                'id' => $module->id,
                'label_fee' => $label_fee,
                'label_total' => $label_total,
                'fee' => Tools::displayPrice($fee),
                'total_fee' => Tools::displayPrice($total + $fee)
            );
        }

        //comaptibilidad con modulo seurcashondelivery
        $module = $this->core->isModuleActive('seurcashondelivery', 'calculateCartAmount');
        if ($module !== false) {
            $fee = $module->calculateCartAmount($this->context->cart);

            $payment_modules_fee['seurcashondelivery'] = array(
                'id' => $module->id,
                'label_fee' => $label_fee,
                'label_total' => $label_total,
                'fee' => Tools::displayPrice($fee),
                'total_fee' => Tools::displayPrice($total + $fee)
            );
        }

        //comaptibilidad con modulo redsys con recargo
        $module = $this->core->isModuleActive('redsys', 'getCuota');
        if ($module !== false) {
            if ($module->author == 'www.alabazweb.com') {
                $tpvs = RedsysTpv::getTpv($this->context->shop->id, $this->context->currency->id);
                if ($tpvs) {
                    $tpv = new RedsysTpv($tpvs[0]['id_tpv']);
                    $module->_tpv = $tpv;
                    $fee = $module->getCuota($this->context->cart);

                    if ($fee > 0 || $fee < 0) {
                        $payment_modules_fee['redsys'] = array(
                            'id' => $module->id,
                            'label_fee' => $label_fee,
                            'label_total' => $label_total,
                            'fee' => Tools::displayPrice($fee),
                            'total_fee' => Tools::displayPrice($total + $fee)
                        );
                    }
                }
            }
        }

        //comaptibilidad con modulo redsys con recargo v3.4.0 - idnovate
        $module = $this->core->isModuleActive('redsys', 'getFeeDiscount');
        if ($module !== false) {
            if ($module->author == 'idnovate') {
                $redsystpv = new redsystpv();

                $carrier       = new Carrier($this->context->cart->id_carrier);
                $carrier       = $carrier->id_reference;
                $manufacturers = '';
                $suppliers     = '';
                $products      = $this->context->cart->getProducts();
                foreach ($products as $product) {
                    $manufacturers .= $product['id_manufacturer'].';';
                    $suppliers     .= $product['id_supplier'].';';
                }
                $manufacturers = explode(';', trim($manufacturers, ';'));
                $manufacturers = array_unique($manufacturers, SORT_REGULAR);
                $suppliers     = explode(';', trim($suppliers, ';'));
                $suppliers     = array_unique($suppliers, SORT_REGULAR);

                $zone    = 0;
                $address = new Address($this->context->cart->id_address_delivery);
                if ($address->id_country > 0) {
                    $country = new Country($address->id_country);
                } else {
                    $country = new Country();
                }

                if ($address->id_state > 0) {
                    $zone = State::getIdZone($address->id_state);
                } else if ($country->id > 0) {
                    $zone = $country->getIdZone($country->id);
                }

                $tpvs = $redsystpv->getTpvs($this->context->cart->id_shop, $this->context->cart->id_lang, $carrier, $country, $zone, $suppliers, $manufacturers, $products);
                if ($tpvs) {
                    $tpv = new redsystpv($tpvs[0]['id_redsys_tpv']);
                    $fee = $module->getFeeDiscount($tpv, $this->context->cart);

                    if ($fee > 0 || $fee < 0) {
                        $payment_modules_fee['redsys'] = array(
                            'id'          => $module->id,
                            'label_fee'   => $label_fee,
                            'label_total' => $label_total,
                            'fee'         => Tools::displayPrice($fee),
                            'total_fee'   => Tools::displayPrice($total + $fee)
                        );
                    }
                }
            }
        }

        //compatibilidad con modulo reembolsocargo v3.2.2 - 4webs
        $module = $this->core->isModuleActive('reembolsocargo');
        if ($module !== false) {
            $fee = $module->getFee($this->context->cart);

            if ($fee > 0) {
                $payment_modules_fee['reembolsocargo'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            }
        }

        //compatibilidad con modulo cashondeliveryfeeplus - Prestalia v4.5.7
        $module = $this->core->isModuleActive('cashondeliveryfeeplus');
        if ($module !== false) {
            $fee = Codfp::getTotalFee(
                $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
                $this->context->shop->id
            );

            if ($fee > 0) {
                $payment_modules_fee['cashondeliveryfeeplus'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            }
        }

        //compatibilidad con modulo transbancaria - 4webs v2.2.2
        $module = $this->core->isModuleActive('transbancaria', 'getDiscount');
        if ($module !== false) {
            $discount = number_format($module->getDiscount($this->context->cart), 2, '.', '');

            $payment_modules_fee['transbancaria'] = array(
                'id' => $module->id,
                'label_fee' => $this->l('Discount for payment'),
                'label_total' => $this->l('Total - Discount'),
                'fee' => Tools::displayPrice($discount * -1),
                'total_fee' => Tools::displayPrice($total - $discount)
            );
        }

        //compatibilidad con modulo bankwirediscount - IdeaCommerce v0.9
        $module = $this->core->isModuleActive('bankwirediscount', 'calculateDiscount');
        if ($module !== false) {
            $discount = $module->calculateDiscount();

            $payment_modules_fee['bankwirediscount'] = array(
                'id' => $module->id,
                'label_fee' => $this->l('Discount for payment'),
                'label_total' => $this->l('Total - Discount'),
                'fee' => Tools::displayPrice($discount * -1),
                'total_fee' => Tools::displayPrice($total - $discount)
            );
        }

        //compatibilidad con modulo dm_cashondelivery - David Mrózek v1.6.2.0
        $module = $this->core->isModuleActive('dm_cashondelivery', 'getFeePrice');
        if ($module !== false) {
            $tax_koeficient = (float)((Tax::getCarrierTaxRate($this->context->cart->id_carrier) + 100) / 100);
            $fee_tax_excl = $module->getFeePrice((int)$this->context->cart->id_carrier, (int)$this->context->cart->id_currency, (float)$this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS));
            $fee_tax_incl = (float)$tax_koeficient * (float)$fee_tax_excl;

            if (!Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer)) {
                $fee = $fee_tax_incl;
            } else {
                $fee = $fee_tax_excl;
            }

            if ($fee > 0) {
                $payment_modules_fee['dm_cashondelivery'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            }
        }

        //compatibilidad con modulo bacodwithfees - buy-addons v1.0.17
        $module = $this->core->isModuleActive('bacodwithfees', 'feeOfOrder');
        if ($module !== false) {
            $fee = $module->feeOfOrder($total);

            if ($fee > 0) {
                $payment_modules_fee['bacodwithfees'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            }
        }

        //compatibilidad con modulo esp_contrareembolso - EsPrestaShop v2.0.1
        $module = $this->core->isModuleActive('esp_contrareembolso', 'getCargo');
        if ($module !== false) {
            $fee = $module->getCargo($this->context->cart);

            if ($fee > 0) {
                $payment_modules_fee['esp_contrareembolso'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            }
        }
        
        //compatibilidad con modulo prestalia_cashondelivery
        $module = $this->core->isModuleActive('prestalia_cashondelivery', 'getCost');
        if ($module !== false) {
            $fee = $module->getCost($this->context->cart, 2);

            if ($fee > 0) {
                $payment_modules_fee['prestalia_cashondelivery'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            }
        }

        //compatibilidad con modulo codpro v1.7.6 - MyPresta.eu
        $module = $this->core->isModuleActive('codpro', 'getChargeValue');
        if ($module !== false) {
            $fee = 0;
            if (Configuration::get('CODPRO_CHARGEP') == 1) {
                $p_fee = Configuration::get('CODPRO_CHARGEP_V');
                $fee = ($total / 100 * $p_fee);
            } else if (Configuration::get('CODPRO_CHARGEA') == 1) {
                $fee = Configuration::get('CODPRO_CHARGEA_V');
            }

            if ($fee > 0) {
                $payment_modules_fee['codpro'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            }
        }

        //compatibilidad con modulo vfcobranca v1.5.2 - PrestaShop
        $module = $this->core->isModuleActive('vfcobranca');
        if ($module !== false) {
            $p_fee = Configuration::get('VF_FEE');
            $fee_tax = Configuration::get('VF_FEETAX');
            if ($p_fee > 0) {
                if ($fee_tax == 1) {
                    $wtax = (float)Tax::getCarrierTaxRate(
                        $this->context->cart->id_carrier,
                        (int)$this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}
                    );
                    $fee = $p_fee * (1 + ($wtax / 100));
                } else {
                    $fee = $p_fee;
                }
            } else {
                $fee = 0;
            }

            if ($fee > 0) {
                $payment_modules_fee['vfcobranca'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            }
        }

        //compatibilidad con modulo przelewy24 v3.4.6 - Przelewy24
        $module = $this->core->isModuleActive('przelewy24', 'getExtrachargeAmount');
        if ($module !== false) {
            $cart = $this->context->cart;
            $currency = $module->getCurrencyCode($cart->id_currency);
            $c_sufix = ($currency == 'PLN' || empty($currency)) ? '' : '_' . $currency;
            $fee = 0;
            $fee = $module->getExtrachargeAmount((int)$cart->id);

            if ((float)Configuration::get('P24_EXTRACHARGE_PERCENT' . $c_sufix) > 0) {
                $fee /= 100;
            }

            if ($fee > 0) {
                $payment_modules_fee['przelewy24'] = array(
                    'id' => $module->id,
                    'label_fee' => $label_fee,
                    'label_total' => $label_total,
                    'fee' => Tools::displayPrice($fee),
                    'total_fee' => Tools::displayPrice($total + $fee)
                );
            }
        }

        $this->context->smarty->assign('payment_modules_fee', Tools::jsonEncode($payment_modules_fee));
    }

    public function checkMinimalPurchase()
    {
        $msg = '';
        $currency = Currency::getCurrency((int) $this->context->cart->id_currency);
        $minimal_purchase = Tools::convertPrice((float) Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
        $total_products = $this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        if ($this->core->isModuleActive('syminimalpurchase')) {
            $customer = new Customer((int)($this->context->customer->id));
            $id_group = $customer->id_default_group;
            $minimal_purchase_groups = Tools::jsonDecode(Configuration::get('syminimalpurchase'));

            if ($minimal_purchase_groups && isset($minimal_purchase_groups->{$id_group})) {
                $minimal_purchase = $minimal_purchase_groups->{$id_group};
            }
        } else if ($minimumpurchasebycg = $this->core->isModuleActive('minimumpurchasebycg')) {
            if (!$minimumpurchasebycg->hasAllowedMinimumPurchase()) {
                $minimal_purchase = $minimumpurchasebycg->minimumpurchaseallowed;
            }
        //support module mobs - MyPresta.eu - v1.2.1
        } else if ($this->core->isModuleActive('mobs')) {
            $suppliers = array();
            $currency_from = new Currency($this->context->currency->id);
            $currency_to = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
            $products = $this->context->cart->getProducts();

            if ($products) {
                foreach ($products as $product => $value) {
                    $product = $product;
                    $price = Tools::convertPriceFull($value['total_wt'], $currency_from, $currency_to);

                    if (isset($suppliers[$value['id_supplier']])) {
                        $suppliers[$value['id_supplier']] = $suppliers[$value['id_supplier']] + $price;
                    } else {
                        $suppliers[$value['id_supplier']] = $price;
                    }

                    unset($price);
                }
                
                if ($suppliers) {
                    foreach ($suppliers as $id_supplier => $price) {
                        $restriction = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT value FROM `' . _DB_PREFIX_ . 'mobs` WHERE id_shop=' . (int)$this->context->shop->id . ' AND id_supplier=' . $id_supplier);

                        if (count($restriction) > 0) {
                            if ($restriction > $price) {
                                $supplier = new Supplier($id_supplier, $this->context->language->id);

                                $text = sprintf($this->l('A minimum purchase total of %1s from supplier %2s is required to validate your order, current purchase total from this category is %3s (tax excl.).'), Tools::displayPrice(Tools::convertPriceFull($restriction, $currency_to, $currency_from), $currency_from), $supplier->name, Tools::displayPrice(Tools::convertPriceFull($price, $currency_to, $currency_from), $currency_from));

                                return '<p class="warning">' . $text . "</p>";
                            }
                        }
                    }
                }
            }
        }

        if ($total_products < $minimal_purchase) {
            $msg = sprintf(
                $this->l('A minimum purchase total of %1s (tax excl.) is required to validate your order, current purchase total is %2s (tax excl.).'),
                Tools::displayPrice($minimal_purchase, $currency),
                Tools::displayPrice($total_products, $currency)
            );
        }

        return $msg;
    }

    public function getContentPayment()
    {
        $name_payment = Tools::getValue('name_payment');

        $module = Module::getInstanceByName($name_payment);
        if (Validate::isLoadedObject($module)) {
            $result = '';

            if (method_exists($module, 'hookPayment')) {
                $result = $module->hookPayment(array('cart' => $this->context->cart));
            } elseif (method_exists($module, 'hookDisplayPayment')) {
                $result = $module->hookDisplayPayment(array('cart' => $this->context->cart));
            }

            return $result;
        }
    }

    public function placeOrder()
    {
        $password       = '';
        $is_set_invoice = false;

        //check fields are sent
        if (Tools::getIsset('fields_opc')) {
            $fields                          = Tools::jsonDecode(Tools::getValue('fields_opc'));
            $id_customer                     = Tools::getValue('id_customer', null);
            $id_address_delivery             = Tools::getValue('id_address_delivery');
            $id_address_invoice              = Tools::getValue('id_address_invoice');

            //Soporte a modulo: Carrier Pickup Store - PresTeamShop
            if ($carrierpickupstore = $this->core->isModuleActive('carrierpickupstore', 'getAddressCPS')) {
                $id_carrier_cps = $carrierpickupstore->getCarrier();

                if ((int)$this->context->cart->id_carrier === (int)$id_carrier_cps) {
                    $carrier = new Carrier($id_carrier_cps);
                    $id_address_delivery = $carrierpickupstore->getAddressCPS($carrier->name, $this->context->cart->id_customer);

                    $this->context->cart->id_address_delivery = $id_address_delivery;
                    $this->context->cart->update();
                    $this->context->cart->setNoMultishipping();
                }
            }

            if (empty($id_address_delivery)) {
                $id_address_delivery = $this->context->cart->id_address_delivery;
            }
            if (empty($id_address_invoice)) {
                $id_address_invoice = $this->context->cart->id_address_invoice;
            }

            $customer         = new Customer((int) $id_customer);
            $address_delivery = new Address((int) $id_address_delivery);
            $address_invoice  = new Address((int) $id_address_invoice);
            $custom_fields = array();
            
            $newsletter_customer_old = $customer->newsletter;
            $old_password   = $customer->passwd;

            $this->validateFields($fields, $customer, $address_delivery, $address_invoice, $password, $is_set_invoice, $custom_fields);
            if (property_exists($customer, 'current_passwd')) {
                if ($customer->current_passwd != $old_password) {
                    $this->errors[] = $this->l('The password entered is incorrect');

                    return array(
                        'hasError'  => !empty($this->errors),
                        'errors'    => $this->errors
                    );
                }
            }

            // Check minimal amount
            $minimal_purchase = $this->checkMinimalPurchase();
            if (!empty($minimal_purchase)) {
                $this->errors[] = $minimal_purchase;
            }

            $this->supportModuleDeliveryDays();

            // If some products have disappear
            if ($this->context->cart->nbProducts() > 0) {
                foreach ($this->context->cart->getProducts() as $product) {
                    $show_message_stock = true;
                    if ($this->core->isModuleActive('belvg_preorderproducts')) {
                        require_once _PS_MODULE_DIR_.'belvg_preorderproducts/classes/ProductPreorder.php';

                        if (ProductPreorder::checkActiveStatic($product['id_product'], $product['id_product_attribute'])) {
                            $show_message_stock = false;
                        }
                    }

                    if ($show_message_stock
                        && (!$product['active']
                            || !$product['available_for_order']
                            || (!$product['allow_oosp'] && $product['stock_quantity'] < $product['cart_quantity']))
                    ) {
                        $this->errors[] = sprintf(
                            $this->l('The product "%s" is not available or does not have stock.'),
                            $product['name']
                        );
                    }
                }
            }

            if (is_array($this->errors) && !count($this->errors)) {
                if (!$this->context->cart->isVirtualCart()) {
                    Hook::exec('actionCarrierProcess', array('cart' => $this->context->cart));
                }

                $lanavettepickup = $this->core->isModuleActive('lanavettepickup', 'hookDisplayPaymentTop');
                if ($lanavettepickup) {
                    $lanavettepickup->hookDisplayPaymentTop(array('cart' => $this->context->cart));
                }

                if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
                    $this->createCustomer($customer, $address_delivery, $address_invoice, $password, $is_set_invoice);

                    if (is_array($this->errors) && !count($this->errors)) {
                        //support module Abandoned Cart OPC.
                        Hook::exec('actionACOPCSaveInformation', array(
                            'id_cart' => $this->context->cart->id,
                            'id_customer' => $customer->id
                        ));

                        //if the customer it is same to opc customer, then show it error message
                        if ($customer->id == $this->config_vars['OPC_ID_CUSTOMER']) {
                            $this->errors[] = $this->l('Problem occurred when processing your order, please contact us.');
                        }

                        // Login information have changed, so we check if the cart rules still apply
                        CartRule::autoRemoveFromCart($this->context);
                        CartRule::autoAddToCart($this->context);

                        if (Tools::getIsset('message')) {
                            $this->context->controller->getController('OrderOpcController')->opcUpdateMessage(Tools::getValue('message'));
                        }

                        if (count($custom_fields) > 0) {
                            foreach ($custom_fields as $custom_field) {
                                $id_address = null;

                                if ($custom_field->object === 'delivery') {
                                    $id_address = $this->context->cart->id_address_delivery;
                                } elseif ($custom_field->object === 'invoice') {
                                    $id_address = $this->context->cart->id_address_invoice;
                                }

                                $this->saveCustomFields($custom_field, $id_address);
                            }
                        }
                        
                        return array(
                            'hasError'            => !empty($this->errors),
                            'errors'              => $this->errors,
                            'isSaved'             => true,
                            'isGuest'             => $customer->is_guest,
                            'id_customer'         => (int) $customer->id,
                            'id_address_delivery' => $this->context->cart->id_address_delivery,
                            'id_address_invoice'  => $this->context->cart->id_address_invoice,
                            'secure_key'          => $this->context->cart->secure_key,
                            'static_token'        => Tools::getToken(false),
                            'token'               => Tools::getToken(),
                        );
                    }
                } else {
                    //llamamos al este hook para poder que guarde la informacion extra de los campos cuando se esta logeado.
                    $customfields = $this->core->isModuleActive('customfields');
                    if ($customfields) {
                        if (class_exists('CustomFieldsHolder') && class_exists('CustomField')) {
                            $fields_custom = CustomFieldsHolder::getAllCustomFields(
                                $this->context->language,
                                $this->context->customer
                            );

                            CustomField::setCustomFields($fields_custom, CustomField::CUSTOMER_EDIT, $this->errors);

                            if (is_array($this->errors) && !count($this->errors)) {
                                CustomField::storeCustomFields($fields_custom, CustomField::CUSTOMER_EDIT);
                            }
                        }
                    }

                    $module_newsletter = $this->core->isModuleActive('blocknewsletter');
                    if ($module_newsletter) {
                        if (($customer->newsletter == 1 && $newsletter_customer_old != 1)) {
                            $customer->newsletter = true;
                            $customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
                            $customer->newsletter_date_add        = pSQL(date('Y-m-d H:i:s'));

                            $module_newsletter->confirmSubscription($customer->email);
                        }
                    }

                    if ($this->config_vars['OPC_CHOICE_GROUP_CUSTOMER']
                        && Tools::getIsset('group_customer')
                        && $group_customer = Tools::getValue('group_customer', '')
                    ) {
                        $new_groups_customer = array((int)$group_customer);
                        $groups_availables = explode(
                            ',',
                            $this->config_vars['OPC_CHOICE_GROUP_CUSTOMER_ALLOW']
                        );

                        $groups_customer = $customer->getGroups();
                        foreach ($groups_customer as $id_group_customer) {
                            if (!in_array($id_group_customer, $groups_availables)) {
                                $new_groups_customer[] = $id_group_customer;
                            }
                        }

                        $customer->id_default_group = (int)$group_customer;
                        $customer->cleanGroups();
                        $customer->addGroups($new_groups_customer);
                    }

                    if ($customer->update()) {
                        //compatibilidad con modulo: recargoequivalencia
                        $recargoequivalencia = $this->core->isModuleActive('recargoequivalencia');
                        if ($recargoequivalencia && Tools::isSubmit('chkRecargoEquivalencia')) {
                            $chkRecargoEquivalencia = Tools::getValue('chkRecargoEquivalencia');
                            $customer_with_recargoequivalencia = Configuration::get('NOHRE_clientesConRecargo');
                            $customer_with_recargoequivalencia = explode('|', $customer_with_recargoequivalencia);

                            $exist_customer_re = false;
                            foreach ($customer_with_recargoequivalencia as $key => $id_customer_re) {
                                if ($customer->id == $id_customer_re) {
                                    $exist_customer_re = true;
                                    if (empty($chkRecargoEquivalencia)) {
                                        unset($customer_with_recargoequivalencia[$key]);
                                    }
                                }
                            }

                            if (!empty($chkRecargoEquivalencia) && !$exist_customer_re) {
                                $customer_with_recargoequivalencia[] = $customer->id;
                            }

                            Configuration::updateValue('NOHRE_clientesConRecargo', implode('|', $customer_with_recargoequivalencia));
                        }

                        // Add customer to the context
                        $this->context->customer = $customer;
                        $this->context->cookie->customer_lastname  = $customer->lastname;
                        $this->context->cookie->customer_firstname = $customer->firstname;

                        //actualizamos las opciones newsletter y optin directamente
                        //en la base de datos, ya que prestashop no lo hace por algun bug.
                        if ((int) $customer->newsletter == 1) {
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(
                                _DB_PREFIX_.'customer',
                                array('newsletter' => 1),
                                'UPDATE',
                                'id_customer = '.(int)$customer->id
                            );
                        }

                        if ((int) $customer->optin == 1) {
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->autoExecute(
                                _DB_PREFIX_.'customer',
                                array('optin' => 1),
                                'UPDATE',
                                'id_customer = '.(int)$customer->id
                            );
                        }
                    } else {
                        $this->errors[] = $this->l('An error occurred while creating your account.');
                    }

                    if (is_array($this->errors) && !count($this->errors)) {
                        if (Tools::getIsset('message')) {
                            $this->context->controller->getController('OrderOpcController')->opcUpdateMessage(Tools::getValue('message'));
                        }
                    }

                    $rc_page = Tools::getValue('rc_page', null);
                    if ($rc_page !== 'customer') {
                        if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA']) {
                            $address_delivery->firstname = $customer->firstname;
                            $address_delivery->lastname  = $customer->lastname;
                        }
                        $address_delivery->update();

                        if ($address_delivery->id !== $address_invoice->id) {
                            if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA']) {
                                $address_invoice->firstname = $customer->firstname;
                                $address_invoice->lastname  = $customer->lastname;
                            }
                            $address_invoice->update();
                        }
                    }
                    
                    if (count($custom_fields) > 0) {
                        foreach ($custom_fields as $custom_field) {
                            $id_address = null;

                            if ($custom_field->object === 'delivery') {
                                $id_address = $this->context->cart->id_address_delivery;
                            } elseif ($custom_field->object === 'invoice') {
                                $id_address = $this->context->cart->id_address_invoice;
                            }

                            $this->saveCustomFields($custom_field, $id_address);
                        }
                    }
                }
            }

            return array(
                'hasError'            => !empty($this->errors),
                'hasWarning'          => !empty($this->warnings),
                'errors'              => $this->errors,
                'warnings'            => $this->warnings,
                'secure_key'          => $this->context->cart->secure_key,
                'id_customer'         => $customer->id,
                'id_address_delivery' => $address_delivery->id,
                'id_address_invoice'  => $address_invoice->id
            );
        }
    }

    public function deleteEmptyAddressesOPC()
    {
        $query = new DbQuery();
        $query->select('id_customer');
        $query->from('customer');
        $query->where('email = "'.pSQL('noreply@'.$this->context->shop->domain).'"');
        $opc_result = Db::getInstance()->executeS($query);

        if (count($opc_result) > 0) {
            $opc_customers = array();
            
            foreach ($opc_result as $id_customer_opc) {
                $opc_customers[] = $id_customer_opc['id_customer'];
            }
            $query = 'DELETE FROM '._DB_PREFIX_.'address WHERE id_customer IN('.implode(',', $opc_customers).')';
        } else {
            $query = 'DELETE FROM '._DB_PREFIX_.'address WHERE id_customer = '.(int)$this->config_vars['OPC_ID_CUSTOMER'];
        }

        Db::getInstance()->execute($query);

        $query = 'DELETE FROM `'._DB_PREFIX_.'address` WHERE id_customer NOT IN (SELECT id_customer FROM `'._DB_PREFIX_.'customer`) AND id_address NOT IN (SELECT id_address_delivery AS id_address FROM `'._DB_PREFIX_.'orders` UNION SELECT id_address_invoice AS id_address FROM `'._DB_PREFIX_.'orders` )';
        Db::getInstance()->execute($query);

        $query = new DbQuery();
        $query->select('*');
        $query->from('cart');
        $query->where('id_cart NOT IN (SELECT id_cart FROM '._DB_PREFIX_.'orders)');

        $carts = Db::getInstance()->executeS($query);
        if (count($carts) > 0) {
            foreach ($carts as $cart) {
                $result = true;
                $result1 = true;
                if (!empty($cart['id_address_delivery'])) {
                    $query = 'SELECT * FROM '._DB_PREFIX_.'address WHERE id_address = '.(int)$cart['id_address_delivery'];
                    $result = Db::getInstance()->executeS($query);
                }
                if (!empty($cart['id_address_invoice'])) {
                    $query1 = 'SELECT * FROM '._DB_PREFIX_.'address WHERE id_address = '.(int)$cart['id_address_invoice'];
                    $result1 = Db::getInstance()->executeS($query1);
                }
                $customer = new Customer((int)$cart['id_customer']);
                if (Validate::isLoadedObject($customer) && $customer->email == 'noreply@'.$this->context->shop->domain) {
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart WHERE id_cart = '.(int) $cart['id_cart']);
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart_product WHERE id_cart = '.(int) $cart['id_cart']);
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart = '.(int) $cart['id_cart']);
                } else if (!$result || !$result1) {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'cart SET id_address_delivery = 0, id_address_invoice = 0, delivery_option = "" WHERE id_cart = '.(int) $cart['id_cart']);
                }
            }
        }

        $query = new DbQuery();
        $query->select('*');
        $query->from('opc_customer_address');

        $customer_address = Db::getInstance()->executeS($query);
        if (count($customer_address) > 0) {
            foreach ($customer_address as $item) {
                $query = 'SELECT * FROM '._DB_PREFIX_.'address WHERE id_address = '.(int)$item['id_address'];
                $result = Db::getInstance()->executeS($query);
                if (!$result) {
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'opc_customer_address WHERE id_address = '.(int)$item['id_address']);
                }

                $query = 'SELECT * FROM '._DB_PREFIX_.'customer WHERE id_customer = '.(int)$item['id_customer'];
                $result = Db::getInstance()->executeS($query);
                if (!$result) {
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'opc_customer_address WHERE id_customer = '.(int)$item['id_customer']);
                }
            }
        }

        return array(
            'message_code' => $this->core->CODE_SUCCESS,
            'message' => $this->l('Created temporary addresses were deleted successfully.')
        );
    }

    public function getInstanceOauthClient($social_network)
    {
        if (!class_exists('http_class')) {
            include _PS_MODULE_DIR_.'onepagecheckoutps/lib/social_network/http.php';
        }
        if (!class_exists('oauth_client_class_pts')) {
            include _PS_MODULE_DIR_.'onepagecheckoutps/lib/social_network/oauth_client.php';
        }

        $client = new oauth_client_class_pts;
        $opc_social_networks = Tools::jsonDecode($this->config_vars['OPC_SOCIAL_NETWORKS']);

        if (!empty($social_network) && !empty($opc_social_networks)) {
            $client->redirect_uri = $this->context->link->getModuleLink(
                'onepagecheckoutps',
                'login',
                array('sv' => $social_network)
            );

            $social_network = Tools::strtolower($social_network);

            $client->server             = $opc_social_networks->{$social_network}->network;
            $client->client_id          = $opc_social_networks->{$social_network}->client_id;
            $client->client_secret      = $opc_social_networks->{$social_network}->client_secret;
            $client->scope              = $opc_social_networks->{$social_network}->scope;
            $client->configuration_file = dirname(__FILE__).'/lib/social_network/oauth_configuration.json';

            switch ($client->server) {
                case 'Facebook':
                    $client->reauthenticate = false;
                    break;
                case 'Google':
                    $client->offline = true;
                    break;
                case 'Biocryptology':
                    $client->url_encode = false;
                    break;
            }
        }

        return $client;
    }

    public function callGeonamesJSON()
    {
        $method = Tools::getValue('method');
        $params = http_build_query(Tools::getValue('params'));

        $ch = curl_init('http://api.geonames.org/'.$method.'?'.$params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    /**
     * Support module FreeShipping
     */
    public function getTreeCategories($id_category)
    {
        $sql = 'select c.`id_parent` from `'._DB_PREFIX_.'category` c
                where c.`id_category` = '.(int)$id_category;

        $result = Db::getInstance()->executeS($sql);

        $ret = $id_category;

        foreach ($result as $row) {
            if ($row['id_parent'] != '0') {
                $ret .= ','.$this->getTreeCategories($row['id_parent']);
            }
        }

        return $ret;
    }

    /**
     * Support module FreeShipping
     */
    public function isProductFreeShipping($id_product, $show_html = true, $cart = null)
    {
        if (is_null($cart)) {
            $cart = $this->context->cart;
        }

        $freeshipping = Module::getInstanceByName('freeshipping');
        if (Validate::isLoadedObject($freeshipping) && $freeshipping->active) {
            $controller_name = Tools::getValue('controller');
            $product = new Product($id_product);
            $products_cart = $cart->getProducts(true);

            $sql = 'select * from `'._DB_PREFIX_.'fs_rule`
                where `active` = 1 and `id_shop` = '.(int)$this->context->shop->id.'
                and `from` <= curdate() and ifnull(`to`, curdate()) >= curdate()';
            $rules = Db::getInstance()->executeS($sql);

            foreach ($rules as $rule) {
                //products
                $sql = 'select * from `'._DB_PREFIX_.'fs_rule_product` where id_rule = '.(int)$rule['id_rule'];
                $rules_product = Db::getInstance()->executeS($sql);

                foreach ($rules_product as $rule_product) {
                    if ($rule_product['id_product'] == $id_product) {
                        if ($rule_product['free'] == '1') {
                            return true;
                        } else {
                            $total_amount = 0;

                            if (count($products_cart) > 0 && !$show_html) {
                                foreach ($products_cart as $product_cart) {
                                    if ($rule_product['id_product'] == $product_cart['id_product']) {
                                        $total_amount += (float)$product_cart['price_wt'] * (int)$product_cart['cart_quantity'];
                                    }
                                }
                            } else {
                                $total_amount = $product->getPrice();
                            }

                            if ((float)$total_amount >= (float)$rule_product['amount']) {
                                return true;
                            }

                            //funciona para cuando no se tiene carrito y no se esta en carrito
                            if (!in_array($controller_name, array('orderopc', 'order')) && $show_html) {
                                if (!empty($rule_product['amount'])) {
                                    return ' > '.Tools::displayPrice($rule_product['amount']);
                                }
                            }
                        }
                    }
                }

                //categories
                $sql = 'select * from `'._DB_PREFIX_.'fs_rule_category` where id_rule = '.(int)$rule['id_rule'];
                $rules_category = Db::getInstance()->executeS($sql);

                foreach ($rules_category as $rule_category) {
                    //$product_categories = $product->getCategories();

                    //foreach ($product_categories as $id_category) {
                    $tree_categories = $this->getTreeCategories($product->id_category_default);
                    $tree_categories_array = explode(',', $tree_categories);

                    if (in_array($rule_category['id_category'], $tree_categories_array)) {
                        if ($rule_category['free'] == '1') {
                            return true;
                        } else {
                            $total_amount = 0;

                            if (count($products_cart) > 0 && !$show_html) {
                                foreach ($products_cart as $product_cart) {
                                    $tree_categories_cart = $this->getTreeCategories($product_cart['id_category_default']);
                                    $tree_categories_array_cart = explode(',', $tree_categories_cart);

                                    if (in_array($rule_category['id_category'], $tree_categories_array_cart)) {
                                        $total_amount += (float)$product_cart['price_wt'] * (int)$product_cart['cart_quantity'];
                                    }
                                }
                            } else {
                                $total_amount = $product->getPrice();
                            }

                            if ((float)$total_amount >= (float)$rule_category['amount']) {
                                return true;
                            }

                            //funciona para cuando no se tiene carrito y no se esta en carrito
                            if (!in_array($controller_name, array('orderopc', 'order')) && $show_html) {
                                if (!empty($rule_category['amount'])) {
                                    return ' > '.Tools::displayPrice($rule_category['amount']);
                                }
                            }
                        }
                    }
                    //}
                }

                //manufacturers
                $sql = 'select * from `'._DB_PREFIX_.'fs_rule_manufacturer` where id_rule = '.(int)$rule['id_rule'];
                $rules_manufacturer = Db::getInstance()->executeS($sql);

                foreach ($rules_manufacturer as $rule_manufacturer) {
                    if ($rule_manufacturer['id_manufacturer'] == $product->id_manufacturer) {
                        if ($rule_manufacturer['free'] == '1') {
                            return true;
                        } else {
                            $total_amount = 0;
                            if (count($products_cart) > 0 && !$show_html) {
                                foreach ($products_cart as $product_cart) {
                                    if ($rule_manufacturer['id_manufacturer'] == $product_cart['id_manufacturer']) {
                                        $total_amount += (float)$product_cart['price_wt'] * (int)$product_cart['cart_quantity'];
                                    }
                                }
                            } else {
                                $total_amount = $product->getPrice();
                            }

                            if ((float)$total_amount >= (float)$rule_manufacturer['amount']) {
                                return true;
                            }

                            //funciona para cuando no se tiene carrito y no se esta en carrito
                            if (!in_array($controller_name, array('orderopc', 'order')) && $show_html) {
                                if (!empty($rule_manufacturer['amount'])) {
                                    return ' > '.Tools::displayPrice($rule_manufacturer['amount']);
                                }
                            }
                        }
                    }
                }

                //suppliers
                $sql = 'select * from `'._DB_PREFIX_.'fs_rule_supplier` where id_rule = '.(int)$rule['id_rule'];
                $rules_supplier = Db::getInstance()->executeS($sql);

                foreach ($rules_supplier as $rule_supplier) {
                    if ($rule_supplier['id_supplier'] == $product->id_supplier) {
                        if ($rule_supplier['free'] == '1') {
                            return true;
                        } else {
                            $total_amount = 0;

                            if (count($products_cart) > 0 && !$show_html) {
                                foreach ($products_cart as $product_cart) {
                                    if ($rule_supplier['id_supplier'] == $product_cart['id_supplier']) {
                                        $total_amount += (float)$product_cart['price_wt'] * (int)$product_cart['cart_quantity'];
                                    }
                                }
                            } else {
                                $total_amount = $product->getPrice();
                            }

                            if ((float)$total_amount >= (float)$rule_supplier['amount']) {
                                return true;
                            }

                            //funciona para cuando no se tiene carrito y no se esta en carrito
                            if (!in_array($controller_name, array('orderopc', 'order')) && $show_html) {
                                if (!empty($rule_supplier['amount'])) {
                                    return ' > '.Tools::displayPrice($rule_supplier['amount']);
                                }
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /*
     * Matches each symbol of PHP date format standard
     * with jQuery equivalent codeword
     * @author Tristan Jahier
     */
    public function dateFormartPHPtoJqueryUI($php_format)
    {
        $symbols_matching = array(
            // Day
            'd' => 'dd',
            'D' => 'D',
            'j' => 'd',
            'l' => 'DD',
            'N' => '',
            'S' => '',
            'w' => '',
            'z' => 'o',
            // Week
            'W' => '',
            // Month
            'F' => 'MM',
            'm' => 'mm',
            'M' => 'M',
            'n' => 'm',
            't' => '',
            // Year
            'L' => '',
            'o' => '',
            'Y' => 'yy',
            'y' => 'y',
            // Time
            'a' => '',
            'A' => '',
            'B' => '',
            'g' => '',
            'G' => '',
            'h' => '',
            'H' => '',
            'i' => '',
            's' => '',
            'u' => '',
        );
        $jqueryui_format  = '';
        $escaping         = false;
        $size_format      = Tools::strlen($php_format);
        for ($i = 0; $i < $size_format; $i++) {
            $char = $php_format[$i];
            if ($char === '\\') { // PHP date format escaping character
                $i++;
                if ($escaping) {
                    $jqueryui_format .= $php_format[$i];
                } else {
                    $jqueryui_format .= '\''.$php_format[$i];
                }
                $escaping = true;
            } else {
                if ($escaping) {
                    $jqueryui_format .= "'";
                    $escaping = false;
                }
                if (isset($symbols_matching[$char])) {
                    $jqueryui_format .= $symbols_matching[$char];
                } else {
                    $jqueryui_format .= $char;
                }
            }
        }

        return $jqueryui_format;
    }

    /* sorts an array of named arrays by the supplied fields
      code by dholmes at jccc d0t net
      taken from http://au.php.net/function.uasort
      modified by cablehead, messju and pscs at http://www.phpinsider.com/smarty-forum */
    public function smartyModifierSortby($data, $sortby)
    {
        static $sort_funcs = array();

        if (empty($sort_funcs[$sortby])) {
            $code = "\$c=0;";
            foreach (explode(',', $sortby) as $key) {
                $d = '1';
                if (Tools::substr($key, 0, 1) == '-') {
                    $d   = '-1';
                    $key = Tools::substr($key, 1);
                }
                if (Tools::substr($key, 0, 1) == '#') {
                    $key = Tools::substr($key, 1);
                    $code .= "if ( ( \$c = (\$a['$key'] - \$b['$key'])) != 0 ) return $d * \$c;\n";
                } else {
                    $code .= "if ( (\$c = strcasecmp(\$a['$key'],\$b['$key'])) != 0 ) return $d * \$c;\n";
                }
            }
            $code .= 'return $c;';
            $sort_func           = $sort_funcs[$sortby] = create_function('$a, $b', $code);
        } else {
            $sort_func = $sort_funcs[$sortby];
        }

        uasort($data, $sort_func);

        return $data;
    }

    public function toolsLinkRewrite()
    {
        $value = Tools::getValue('value');

        return Tools::link_rewrite($value);
    }
}
