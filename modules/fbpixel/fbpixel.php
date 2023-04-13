<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2018 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class fbpixel extends Module
{
    public function __construct()
    {
        $this->name = 'fbpixel';
        $this->tab = 'front_office_features';
        $this->author = 'MyPresta.eu';
        $this->version = '1.9.7';
        $this->module_key = 'd16dfcb44d033d05e3bab40156ee80a1';
        $this->mypresta_link = 'https://mypresta.eu/modules/social-networks/fb-conversion-tracking-pixel.html';
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->bootstrap = true;
        $this->displayName = $this->l('Facebook Conversion Pixel');
        $this->description = $this->l('Module adds facebook conversion pixel to order confirmation page.');
        $this->checkforupdates(0, 0);
    }

    public function install()
    {
        if (parent::install() == false or
            $this->registerHook('displayOrderConfirmation') == false or
            $this->registerHook('header') == false or
            !Configuration::updateValue('FBPIXEL_ATC_P', '#add_to_cart') or
            !Configuration::updateValue('FBPIXEL_ATC_L', '.ajax_add_to_cart_button, .cart_quantity_up') or
            !Configuration::updateValue('FBPIXEL_ATC_PC', '.product-container') or
            !Configuration::updateValue('FBPIXEL_ATC_PP', '.product-price') or
            !Configuration::updateValue('FBPIXEL_ATC_PPP', '#our_price_display') or
            !Configuration::updateValue('FBPIXEL_SEPSIGN','-'))
        {
            return false;
        }
        return true;
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 12 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php'))
        {
            @require_once('../modules/' . $this->name . '/key.php');
        }
        else
        {
            if (@file_exists(dirname(__file__) . $this->name . '/key.php'))
            {
                @require_once(dirname(__file__) . $this->name . '/key.php');
            }
            else
            {
                if (@file_exists('modules/' . $this->name . '/key.php'))
                {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1)
        {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_modu\le_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        }
        else
        {
            if (defined('_PS_ADMIN_DIR_'))
            {
                if (Tools::isSubmit('submit_settings_updates'))
                {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') == false)
                {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200))
                    {
                        $actual_version = fbpixelUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (fbpixelUpdate::version($this->version) < fbpixelUpdate::version(Configuration::get('updatev_' . $this->name)))
                    {
                        $this->warning = $this->l('New version available, check http://MyPresta.eu for more informations');
                    }
                }
                if ($display_msg == 1)
                {
                    if (fbpixelUpdate::version($this->version) < fbpixelUpdate::version(fbpixelUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version)))
                    {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    }
                    else
                    {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function inconsistency($ret)
    {
        return true;
    }

    public function getContent()
    {
        return $this->_postProcess() . $this->displayForm() . $this->checkforupdates(0, 1);
    }

    public function psversion()
    {
        $version = _PS_VERSION_;
        $exp = explode(".", $version);
        return $exp[1];
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit'))
        {
            Configuration::updateValue('FBPIXEL_PURCHASE', Tools::getValue('FBPIXEL_PURCHASE'));
            Configuration::updateValue('FBPIXEL_PAGEVIEW', Tools::getValue('FBPIXEL_PAGEVIEW'));
            Configuration::updateValue('FBPIXEL_ID', Tools::getValue('FBPIXEL_ID'));
            Configuration::updateValue('FBPIXEL_LEAD', Tools::getValue('FBPIXEL_LEAD'));
            Configuration::updateValue('FBPIXEL_LEAD_N', Tools::getValue('FBPIXEL_LEAD_N'));
            Configuration::updateValue('FBPIXEL_INITIATE', Tools::getValue('FBPIXEL_INITIATE'));
            Configuration::updateValue('FBPIXEL_SEARCH', Tools::getValue('FBPIXEL_SEARCH'));
            Configuration::updateValue('FBPIXEL_ADDTOCART', Tools::getValue('FBPIXEL_ADDTOCART'));
            Configuration::updateValue('FBPIXEL_ATC_P', Tools::getValue('FBPIXEL_ATC_P', '#add_to_cart'));
            Configuration::updateValue('FBPIXEL_ATC_L', Tools::getValue('FBPIXEL_ATC_L', '.ajax_add_to_cart_button, .cart_quantity_up'));
            Configuration::updateValue('FBPIXEL_ATC_PC', Tools::getValue('FBPIXEL_ATC_PC', '.product-container'));
            Configuration::updateValue('FBPIXEL_ATC_PP', Tools::getValue('FBPIXEL_ATC_PP', '.product-price'));
            Configuration::updateValue('FBPIXEL_ATC_PPP', Tools::getValue('FBPIXEL_ATC_PPP', '#our_price_display'));
            Configuration::updateValue('FBPIXEL_WISHLIST', Tools::getValue('FBPIXEL_WISHLIST'));
            Configuration::updateValue('FBPIXEL_DPA', Tools::getValue('FBPIXEL_DPA'));
            Configuration::updateValue('FBPIXEL_VCONTENT', Tools::getValue('FBPIXEL_VCONTENT'));
            Configuration::updateValue('FBPIXEL_ATTRID', Tools::getValue('FBPIXEL_ATTRID'));
            Configuration::updateValue('FBPIXEL_REG', Tools::getValue('FBPIXEL_REG'));
            Configuration::updateValue('FBPIXEL_SEPSIGN', Tools::getValue('FBPIXEL_SEPSIGN'));
            Configuration::updateValue('FBPIXEL_EXFREE', Tools::getValue('FBPIXEL_EXFREE'));
            $prefix = array();
            $sufix = array();
            Foreach (language::getLanguages(false) AS $lang)
            {
                $prefix[$lang['id_lang']] = Tools::getValue('FBPIXEL_PREFIX_' . $lang['id_lang']);
                $sufix[$lang['id_lang']] = Tools::getValue('FBPIXEL_SUFIX_' . $lang['id_lang']);
            }
            Configuration::updateValue('FBPIXEL_PREFIX', $prefix);
            Configuration::updateValue('FBPIXEL_SUFIX', $sufix);
        }
        return $this->displayConfirmation($this->l('Settings updated'));
    }

    public function displayForm()
    {
        $options = array(
            array(
                'id_option' => 0,
                'name' => 'No'
            ),
            array(
                'id_option' => 1,
                'name' => 'Yes'
            ),
        );

        $options_identification = array(
            array(
                'id_option' => 0,
                'name' => 'id_product'
            ),
            array(
                'id_option' => 1,
                'name' => 'id_attribute'
            ),
            array(
                'id_option' => 2,
                'name' => 'id_product-id_attribute'
            ),
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-wrench'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Your Pixel ID'),
                        'name' => 'FBPIXEL_ID',
                        'desc' => $this->l('Enter here your unique ID of pixel') . ' <a href="https://mypresta.eu/basic-tutorials/new-facebook-pixel-id.html" target="_blank">' . $this->l('Check how to get facebook pixel ID') . '</a>'
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Track PageView'),
                        'name' => 'FBPIXEL_PAGEVIEW',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to track page views') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Track account creation'),
                        'name' => 'FBPIXEL_REG',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to track page usr register') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Track ViewContent (products)'),
                        'name' => 'FBPIXEL_VCONTENT',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to track ViewContent (product pages)') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Track Purchase'),
                        'name' => 'FBPIXEL_PURCHASE',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to track purchases (order confirmation)') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Exclude "free" products from purchase event'),
                        'name' => 'FBPIXEL_EXFREE',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('When you allow to order free products - module will not include them to purchase events') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Track Lead (page view)'),
                        'name' => 'FBPIXEL_LEAD',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to track when a user expresses interest in your offering (product page view)') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Track Lead (newsletter subscription)'),
                        'name' => 'FBPIXEL_LEAD_N',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Applicable for default newsletter subscription feature. Event is tracked when someone subscribe to newsletter.') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Track Initiate Checkout'),
                        'name' => 'FBPIXEL_INITIATE',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to track when people enter the checkout flow (go to cart page)') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Track Search'),
                        'name' => 'FBPIXEL_SEARCH',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to track searches on your website') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Add To Cart'),
                        'name' => 'FBPIXEL_ADDTOCART',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to track when items are added to a shopping cart. Add to cart button must have class="ajax_add_to_cart_button" or id="add_to_cart" (available by default in PrestaShop)') . ''
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('CSS selector of product page "add to cart" button'),
                        'name' => 'FBPIXEL_ATC_P',
                        'desc' => $this->l('Default value of this field is: "#add_to_cart" - and this selector is frequently used by many templates. When someone will press this button module will init "add to cart" event'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('CSS selector of product list "add to cart" button'),
                        'name' => 'FBPIXEL_ATC_L',
                        'desc' => $this->l('Default value of this field is: ".ajax_add_to_cart_button, .cart_quantity_up" - and this selector is frequently used by many templates. When someone will press this button module will init "add to cart" event'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('CSS selector of product list product miniature'),
                        'name' => 'FBPIXEL_ATC_PC',
                        'desc' => $this->l('Default value of this field is: .product-container'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('CSS selector of product list product price'),
                        'name' => 'FBPIXEL_ATC_PP',
                        'desc' => $this->l('Default value of this field is: .product-price'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('CSS selector of product page product price'),
                        'name' => 'FBPIXEL_ATC_PPP',
                        'desc' => $this->l('Default value of this field is: #our_price_display'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Product identification'),
                        'name' => 'FBPIXEL_ATTRID',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options_identification,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select how you want to identify the products - module will send selected identification variable with tracked events') . ''
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Separate sign'),
                        'name' => 'FBPIXEL_SEPSIGN',
                        'lang' => false,
                        'desc' => $this->l('Define the sign (or string) that will separate id_product from id_attribute (by default dash symbol)')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Product identification sufix'),
                        'name' => 'FBPIXEL_SUFIX',
                        'lang' => true,
                        'desc' => $this->l('Define sufix - it will be added to product id in tracked events. Leave field empty if you dont want to use it') . ''
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Product identification prefix'),
                        'name' => 'FBPIXEL_PREFIX',
                        'lang' => true,
                        'desc' => $this->l('Define prefix - it will be added to product id in tracked events. Leave field empty if you dont want to use it') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Add To Wishlist'),
                        'name' => 'FBPIXEL_WISHLIST',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to track when items are added to a wishlist. Add to wishlist button must have id="wishlist_button_nopop" (available by default in PrestaShop)') . ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Dynamic Pixel Events'),
                        'name' => 'FBPIXEL_DPA',
                        'cast' => 'intval',
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'identifier' => 'value',
                        'desc' => $this->l('Select YES if you want to enable Dynamic Product Ads (DPA). DPA allow advertisers to create single-product or carousel ads that are rendered and targeted based on a set of products.') . '<a href="https://developers.facebook.com/docs/ads-for-websites/pixel-troubleshooting#catalog-pair" target="_blank">' . $this->l('For DPA your pixel must be paired with a product catalog') . '</a>'
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));

    }

    public function getConfigFieldsValues()
    {
        $prefix = array();
        $sufix = array();
        Foreach (language::getLanguages(false) AS $lang)
        {
            $prefix[$lang['id_lang']] = Configuration::get('FBPIXEL_PREFIX', $lang['id_lang']);
            $sufix[$lang['id_lang']] = Configuration::get('FBPIXEL_SUFIX', $lang['id_lang']);
        }

        return array(
            'FBPIXEL_ID' => Tools::getValue('FBPIXEL_ID', Configuration::get('FBPIXEL_ID')),
            'FBPIXEL_PAGEVIEW' => Tools::getValue('FBPIXEL_PAGEVIEW', Configuration::get('FBPIXEL_PAGEVIEW')),
            'FBPIXEL_PURCHASE' => Tools::getValue('FBPIXEL_PURCHASE', Configuration::get('FBPIXEL_PURCHASE')),
            'FBPIXEL_LEAD' => Tools::getValue('FBPIXEL_LEAD', Configuration::get('FBPIXEL_LEAD')),
            'FBPIXEL_LEAD_N' => Tools::getValue('FBPIXEL_LEAD_N', Configuration::get('FBPIXEL_LEAD_N')),
            'FBPIXEL_INITIATE' => Tools::getValue('FBPIXEL_INITIATE', Configuration::get('FBPIXEL_INITIATE')),
            'FBPIXEL_SEARCH' => Tools::getValue('FBPIXEL_INITIATE', Configuration::get('FBPIXEL_SEARCH')),
            'FBPIXEL_ADDTOCART' => Tools::getValue('FBPIXEL_ADDTOCART', Configuration::get('FBPIXEL_ADDTOCART')),
            'FBPIXEL_ATC_P' => Tools::getValue('FBPIXEL_ATC_P', Configuration::get('FBPIXEL_ATC_P')),
            'FBPIXEL_ATC_L' => Tools::getValue('FBPIXEL_ATC_L', Configuration::get('FBPIXEL_ATC_L')),
            'FBPIXEL_ATC_PC' => Tools::getValue('FBPIXEL_ATC_PC', Configuration::get('FBPIXEL_ATC_PC')),
            'FBPIXEL_ATC_PP' => Tools::getValue('FBPIXEL_ATC_PP', Configuration::get('FBPIXEL_ATC_PP')),
            'FBPIXEL_ATC_PPP' => Tools::getValue('FBPIXEL_ATC_PPP', Configuration::get('FBPIXEL_ATC_PPP')),
            'FBPIXEL_WISHLIST' => Tools::getValue('FBPIXEL_WISHLIST', Configuration::get('FBPIXEL_WISHLIST')),
            'FBPIXEL_DPA' => Tools::getValue('FBPIXEL_DPA', Configuration::get('FBPIXEL_DPA')),
            'FBPIXEL_VCONTENT' => Tools::getValue('FBPIXEL_VCONTENT', Configuration::get('FBPIXEL_VCONTENT')),
            'FBPIXEL_ATTRID' => Tools::getValue('FBPIXEL_ATTRID', Configuration::get('FBPIXEL_ATTRID')),
            'FBPIXEL_REG' => Tools::getValue('FBPIXEL_REG', Configuration::get('FBPIXEL_REG')),
            'FBPIXEL_SEPSIGN' => Tools::getValue('FBPIXEL_SEPSIGN', Configuration::get('FBPIXEL_SEPSIGN')),
            'FBPIXEL_EXFREE' => Tools::getValue('FBPIXEL_EXFREE', Configuration::get('FBPIXEL_EXFREE')),
            'FBPIXEL_PREFIX' => $prefix,
            'FBPIXEL_SUFIX' => $sufix,
        );
    }

    public function hookHeader($params)
    {
        $this->smarty->assign('fbpixel_currency', $this->context->currency->iso_code);
        if (Tools::getValue('controller') == 'product')
        {
            if (Tools::getValue('id_product', 'false') != 'false')
            {
                $product = new Product(Tools::getValue('id_product'), true, $this->context->language->id);
                $this->smarty->assign('fbpixel_product', $product);
            }
        }
        if (isset($this->context->cookie->account_created))
        {
            $this->context->smarty->assign('account_created', 1);
        }

        if (Tools::isSubmit('submitNewsletter'))
        {
            $track_newsletter = $this->newsletterRegistration();
        }
        else
        {
            $track_newsletter = false;
        }
        $this->context->smarty->assign('track_newsletter', $track_newsletter);
        $this->context->smarty->assign('prefix', Configuration::get('FBPIXEL_PREFIX', $this->context->language->id));
        $this->context->smarty->assign('sufix', Configuration::get('FBPIXEL_SUFIX', $this->context->language->id));

        return $this->display(__FILE__, 'header.tpl');
    }

    public function newsletterRegistration()
    {
        if (Tools::getValue('email', 'false') == 'false' || !Validate::isEmail(Tools::getValue('email')))
        {
            return false;
        }

        if (Tools::getValue('action', 'false') == '0')
        {
            $register_status = $this->isNewsletterRegistered(Tools::getValue('email'));
            if ($register_status == true)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }


    public function isNewsletterRegistered($customer_email)
    {
        $sql = 'SELECT `email`
				FROM ' . _DB_PREFIX_ . 'newsletter
				WHERE `email` = \'' . pSQL($customer_email) . '\'
				AND id_shop = ' . $this->context->shop->id;

        if (Db::getInstance()->getRow($sql))
        {
            return true;
        }

        $sql = 'SELECT `newsletter`
				FROM ' . _DB_PREFIX_ . 'customer
				WHERE `email` = \'' . pSQL($customer_email) . '\'
				AND id_shop = ' . $this->context->shop->id;

        if ($registered = Db::getInstance()->getRow($sql))
        {
            if ($registered['newsletter'] == '1')
            {
                return true;
            }
        }
        return false;
    }

    public function hookdisplayOrderConfirmation($params)
    {
        if (isset($params['objOrder']) || Tools::getValue('id_order', 'false') != 'false' || Tools::getValue('id_cart', 'false') != false)
        {
            if (Tools::getValue('id_order', 'false') != false && Tools::getValue('id_order') != false)
            {
                $id_order = Tools::getValue('id_order');
            }
            elseif (Tools::getValue('id_cart', 'false') != false && Tools::getValue('id_cart') != false)
            {
                $id_order = Order::getOrderByCartId(Tools::getValue('id_cart'));
            }
            elseif (isset($params['objOrder']->id))
            {
                $id_order = $params['objOrder']->id;
            }
            else
            {
                $id_order = false;
            }

            if ($id_order != false)
            {
                $order = new Order($id_order);
                $currency = new Currency($order->id_currency);
                $content_ids = '';
                foreach ($order->getProducts() AS $key => $value)
                {
                    if (Configuration::get('FBPIXEL_EXFREE') == 1)
                    {
                        if ($value['total_price_tax_incl'] <= 0) {
                            continue;
                        }
                    }

                    if (Configuration::get('FBPIXEL_ATTRID') == 1)
                    {
                        for ($x = 0; $x < $value['product_quantity']; $x++)
                        {
                            $content_ids .= "'" . Configuration::get('FBPIXEL_PREFIX', $this->context->language->id) . "{$value['product_attribute_id']}" . Configuration::get('FBPIXEL_SUFIX', $this->context->language->id) . "',";
                        }
                    }
                    else
                    {
                        if (Configuration::get('FBPIXEL_ATTRID') == 2)
                        {
                            for ($x = 0; $x < $value['product_quantity']; $x++)
                            {
                                $content_ids .= "'" . Configuration::get('FBPIXEL_PREFIX', $this->context->language->id) . "{$value['product_id']}".Configuration::get('FBPIXEL_SEPSIGN','')."{$value['product_attribute_id']}" . Configuration::get('FBPIXEL_SUFIX', $this->context->language->id) . "',";
                            }
                        }
                        else
                        {
                            for ($x = 0; $x < $value['product_quantity']; $x++)
                            {
                                $content_ids .= "'" . Configuration::get('FBPIXEL_PREFIX', $this->context->language->id) . "{$value['product_id']}" . Configuration::get('FBPIXEL_SUFIX', $this->context->language->id) . "',";
                            }
                        }
                    }
                }

                $this->context->smarty->assign('content_ids', rtrim($content_ids, ','));
                $this->context->smarty->assign('order_currency_iso_code', $currency->iso_code);
                $this->context->smarty->assign('order_total_paid', number_format($order->total_paid, 2, ".", ""));
                $this->context->smarty->assign('order_total_products_tax_included', number_format($order->total_products_wt, 2, ".", ""));
                $this->context->smarty->assign('order_total_products_tax_excluded', number_format($order->total_products, 2, ".", ""));
            }

            $this->context->smarty->assign('order_id', $id_order);
        }

        return $this->display(__FILE__, 'displayOrderConfirmation.tpl');
    }

}


class fbpixelUpdate extends fbpixel
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3)
        {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2)
        {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1)
        {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0)
        {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen"))
        {
            if (function_exists("file_get_contents"))
            {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}

?>