<?php

require_once _PS_MODULE_DIR_ . 'pfspreorder/classes/Preorder.php';

class AdminPreorderController extends ModuleAdminController
{

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['config'] = array(
            'short' => $this->l('Preorder'),
            'href' => $this->context->link->getAdminLink('AdminPreorder'),
            'icon' => 'process-icon-cart',
            'desc' => $this->l('Preorder'),
        );
    }

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true; // use Bootstrap CSS
        $this->table = 'pfspreorder'; // SQL table name, will be prefixed with _DB_PREFIX_
        $this->identifier = 'id_pfspreorder'; // SQL column to be used as primary key
        $this->className = 'Preorder'; // PHP class name
        $this->allow_export = true; // allow export in CSV, XLS..

        $this->_defaultOrderBy = 'a.created'; // the table alias is always `a`
        $this->_defaultOrderWay = 'DESC';
        $this->fields_list = [
            'id_pfspreorder' => ['title' => 'ID', 'class' => 'fixed-width-xs'],
            'form_name' => ['title' => 'Order'],
            'product' => ['title' => 'Product'],
            'status' => ['title' => 'Status'],
            'phone' => ['title' => 'Phone'],
            'fname' => ['title' => 'Name'],
            'lname' => ['title' => 'Last Name'],
            'other' => ['title' => 'Other'],
            'created' => ['title' => 'Created', 'type' => 'datetime'],
        ];

        $this->addRowAction('edit');
        $this->addRowAction('details');
        $this->fields_form = [
            'legend' => [
                'title' => 'Детали заявки',
                'icon' => 'icon-list-ul'
            ],
            'input' => [
                ['name' => 'form_name', 'type' => 'text', 'label' => 'Order', 'required' => true,],
                ['name' => 'product', 'type' => 'text', 'label' => 'Product', 'required' => true,],
                ['name' => 'status', 'type' => 'text', 'label' => 'Status', 'required' => true],
                ['name' => 'phone', 'type' => 'text', 'label' => 'Phone', 'required' => true,],
                ['name' => 'fname', 'type' => 'text', 'label' => 'Name', 'required' => true],
                ['name' => 'lname', 'type' => 'text', 'label' => 'Last Name', 'required' => true],
                ['name' => 'created', 'type' => 'text', 'label' => 'Created', 'suffix' => 'YYYY-MM-DD HH:mm',],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ]
        ];

    }
}
