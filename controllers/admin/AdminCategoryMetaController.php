<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminCategoryMetaControllerCore extends AdminController
{

    public function __construct()
    {

        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'category_meta';
        $this->identifier = 'id_category';
        $this->className = 'CategoryMeta';

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('add');

        $this->context = Context::getContext();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_category' => array(
                'title' => $this->l('id'),
                'align' => 'center'
            ),
            'url' => array(
                'title' => $this->l('Адрес категории'),
                'maxlength' => 255
            ),
            'title' => array(
                'title' => $this->l('Заголовок браузера')
            ),
            'h1' => array(
                'title' => $this->l('H1 страницы')
            ),
            'description' => array(
                'title' => $this->l('Описание категории'),
                'callback' => 'getDescriptionClean'
            ),
            'meta_description' => array(
                'title' => $this->l('Мета описание категории')
            ),
            'keywords' => array(
                'title' => $this->l('Ключевые слова')
            )
        );

        parent::__construct();
    }

    public function renderForm()
    {
        /** @var Tag $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Category Meta'),
                'icon' => 'icon-mail'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'lang' => false,
                    'label' => $this->l('Адрес категории'),
                    'name' => 'url',
                    'size' => 250,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Заголовок браузера'),
                    'name' => 'title',
                    'size' => 250,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Мета описание категории'),
                    'name' => 'meta_description',
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Ключевые слова'),
                    'name' => 'keywords',
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('H1 страницы категории'),
                    'name' => 'h1',
                    'required' => false
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Описание категории'),
                    'name' => 'description',
                    'autoload_rte' => true,
                    'lang' => false,
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}'
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        return parent::renderForm();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_category_meta'] = array(
                'href' => self::$currentIndex.'&addcategory_meta&token='.$this->token,
                'desc' => $this->l('Add new category meta'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }



    public function initProcess()
    {
        if (Tools::isSubmit('add')) {
            $this->action = 'add';
            $this->display = 'add';
        }

        if (Tools::isSubmit('view')) {
            $this->action = 'view';
            $this->display = 'view';
        }

        if (Tools::isSubmit('delete')) {
            $this->action = 'delete';
            $this->display = 'delete';
        }

        parent::initProcess();
    }

    public static function getDescriptionClean($description)
    {
        return Tools::getDescriptionClean($description);
    }

}
