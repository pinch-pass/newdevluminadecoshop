<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Jsox_manyworks extends Module
{
    protected $config_form = false;
    public $jsox_contact_block_secret = 'rdvlkErtr34hHWhmh';

    public function __construct()
    {
        $this->name = 'jsox_manyworks';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'prestaservice';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Работы по фронт-енду');
        $this->description = $this->l('Работы по фронт-енду и т.д.');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        if (Tools::getValue('jsox_contact_block') && Tools::getValue('secret') === $this->jsox_contact_block_secret) {
            $this->sendEmail();
        }
    }

    public function sendEmail()
    {
        $emailTo = 'info@luminadecoshop.ru';
        // $emailTo = 'admin@jsox.ru';
        $name = Tools::getValue('name');
        $phone = Tools::getValue('phone');
        $email = Tools::getValue('email');
        $comment = Tools::getValue('comment');

        $sent = Mail::Send(
            Configuration::get('PS_LANG_DEFAULT'),
            'form',
            'НАЧАТЬ СОТРУДНИЧЕСТВО - сообщение из формы',
            array('{shop_name}' => Configuration::get('PS_SHOP_NAME'), '{name}' => $name, '{email}' => $email, '{phone}' => $phone, '{comment}' => $comment),
            $emailTo,
            null,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            null,
            null,
            dirname(__FILE__) . '/mails/'
        );
        if ($sent)
            echo json_encode(['answer' => 'Успешно отправлено!']);
        else {
            echo json_encode(['answer' => 'Ошибка отправки! Повторите чуть позже.']);
        }
        die();
    }

    public function install()
    {
        Configuration::updateValue('JSOX_MANYWORKS_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('JSOX_MANYWORKS_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        // if (Tools::getValue('module_name') == $this->name) {
        //     $this->context->controller->addJS($this->_path.'views/js/back.js');
        //     $this->context->controller->addCSS($this->_path.'views/css/back.css');
        // }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        /** */
    }

    public function hookDisplayBackOfficeHeader()
    {
        /** */
    }

    public function hookDisplayHeader()
    {
        // $html = $this->context->smarty->fetch(__DIR__ . '/views/templates/contact_form.tpl');
        $html = file_get_contents(__DIR__ . '/views/templates/contact_form.tpl');
        // $this->dump($html);
        Media::addJsDef(array(
            'jsox_contact_block_secret' => $this->jsox_contact_block_secret,
            'jsox_contact_block_html' => json_encode($html),
        ));
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
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
}
