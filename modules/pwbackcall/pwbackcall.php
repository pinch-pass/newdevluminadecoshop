<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PwBackCall extends Module
{
    public $html;

    public $isShowButton = false;

    public function __construct()
    {
        $this->tab = 'other';
        $this->name = 'pwbackcall';
        $this->author = 'PrestaWeb.ru';
        $this->version = '2.1.0';
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->only_pwbackcall = true;

        parent::__construct();

        $this->displayName = $this->l('Feedback');
        $this->description = $this->l('Callback and questions on email');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->isShowButton = (Configuration::get('PW_BACK_ENABLE') == '1') ? true : false;
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('header')
            || !$this->registerHook('pwcallback')
            || !Configuration::updateValue('PW_BACK_JS', '')
            || !Configuration::updateValue('PW_BACK_ENABLE', 0)
            || !Configuration::updateValue('PW_BACK_FIELD_NAME', 0)
            || !Configuration::updateValue('PW_BACK_FIELD_EMAIL', 0)
            || !Configuration::updateValue('PW_BACK_FIELD_COMMENT', 0)
            || !Configuration::updateValue('PW_BACK_EMAILS', Configuration::get('PS_SHOP_EMAIL'))
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->unregisterHook('header')
            || !$this->unregisterHook('pwcallback')
            || !Configuration::deleteByName('PW_BACK_JS')
            || !Configuration::deleteByName('PW_BACK_ENABLE')
            || !Configuration::deleteByName('PW_BACK_FIELD_NAME')
            || !Configuration::deleteByName('PW_BACK_FIELD_EMAIL')
            || !Configuration::deleteByName('PW_BACK_FIELD_COMMENT')
            || !Configuration::deleteByName('PW_BACK_EMAILS')
        ) {
            return false;
        }
        return true;
    }

    public function hookHeader($params)
    {
        if (Configuration::get('PW_BACK_ENABLE') == '1') {
            $this->context->controller->addCSS(($this->_path) . 'views/css/pwbackcall.css', 'all');
            $this->context->controller->addJS(($this->_path) . 'views/js/pwbackcall.js');
        }
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitpwbackcall')) {
            $errors = array();
            $emails = $this->prepareEmails(Tools::getValue('PW_BACK_EMAILS'));

            if (empty($emails)) {
                $errors[] = $this->l('Wrong email');
            } else {
                if (!Configuration::updateValue('PW_BACK_JS', Tools::getValue('PW_BACK_JS'))
                    || !Configuration::updateValue('PW_BACK_EMAILS', implode(',', $emails))
                    || !Configuration::updateValue('PW_BACK_ENABLE', Tools::getValue('PW_BACK_ENABLE'))
                    || !Configuration::updateValue('PW_BACK_FIELD_NAME', Tools::getValue('PW_BACK_FIELD_NAME'))
                    || !Configuration::updateValue('PW_BACK_FIELD_PHONE', Tools::getValue('PW_BACK_FIELD_PHONE'))
                    || !Configuration::updateValue('PW_BACK_FIELD_EMAIL', Tools::getValue('PW_BACK_FIELD_EMAIL'))
                    || !Configuration::updateValue('PW_BACK_FIELD_COMMENT', Tools::getValue('PW_BACK_FIELD_COMMENT'))
                ) {
                    $errors[] = $this->l('Error');
                }
            }

            if (!empty($errors)) {
                $this->html .= $this->displayError(implode('<br />', $errors));
            } else {
                $this->html .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $this->html . $this->displayForm();
    }

    public function displayForm()
    {
        $helper = new HelperOptions();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $fields_options = array(
            'general' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'PW_BACK_ENABLE' => array(
                        'title' => $this->l('Show button'),
                        'cast' => 'boolval',
                        'type' => 'bool'
                    ),
                    'PW_BACK_FIELD_NAME' => array(
                        'title' => $this->l('Show field Name'),
                        'cast' => 'boolval',
                        'type' => 'bool'
                    ),
                    'PW_BACK_FIELD_PHONE' => array(
                        'title' => $this->l('Show field Phone'),
                        'cast' => 'boolval',
                        'type' => 'bool'
                    ),
                    'PW_BACK_FIELD_EMAIL' => array(
                        'title' => $this->l('Show field E-mail'),
                        'cast' => 'boolval',
                        'type' => 'bool'
                    ),
                    'PW_BACK_FIELD_COMMENT' => array(
                        'title' => $this->l('Show field Comment'),
                        'cast' => 'boolval',
                        'type' => 'bool'
                    ),
                    'PW_BACK_EMAILS' => array(
                        'title' => $this->l('Recipients'),
                        'type' => 'text',
                        'cast' => 'strval',
                        'hint' => $this->l('Enter e-mails, separated by commas')
                    ),
                    'PW_BACK_JS' => array(
                        'title' => $this->l('JavaScript code'),
                        'type' => 'textarea',
                        'rows' => 5,
                        'cols' => 40,
                        'cast' => 'strval',
                        'hint' => $this->l('This code will be executed after a successful sending data to the server')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitpwbackcall'
                )
            )
        );

        return $helper->generateOptions($fields_options);
    }

    public function hookpwcallback($params)
    {
        if (Configuration::get('PW_BACK_ENABLE') != '1') {
            return;
        }

        $pwbackcall = array(
            'link' => $this->context->link->getModuleLink($this->name, 'ajax'),
        );

        $this->context->smarty->assign(array(
            'showbutton' => (Configuration::get('PW_BACK_ENABLE') == '1') ? true : false,
            'showfieldname' => (Configuration::get('PW_BACK_FIELD_NAME') == '1') ? true : false,
            'showfieldemail' => (Configuration::get('PW_BACK_FIELD_EMAIL') == '1') ? true : false,
            'showfieldphone' => (Configuration::get('PW_BACK_FIELD_PHONE') == '1') ? true : false,
            'showfieldcomment' => (Configuration::get('PW_BACK_FIELD_COMMENT') == '1') ? true : false,
            'pwbackcall' => $pwbackcall,
            'backcalljs' => Configuration::get('PW_BACK_JS'),
        ));

        return $this->display(__FILE__, 'pwbackcall.tpl');
    }

    public function prepareEmails($emails)
    {
        $result = array();
        $emails = preg_split('/,/', $emails);

        foreach ($emails as $email) {
            if ($email !== '' && Validate::isEmail(trim($email))) {
                $result[] = trim($email);
            }
        }
        return $result;
    }
}