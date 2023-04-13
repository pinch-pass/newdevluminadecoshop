<?php
require_once _PS_MODULE_DIR_ . 'pfspreorder/classes/Preorder.php';
class AdminPreorderController extends ModuleAdminController {

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
            'status' => ['title' => 'Status'],
            'phone' => ['title' => 'Phone'],
            'user_name' => ['title' => 'USER name'],
            'fname' => ['title' => 'Name'],
            'lname' => ['title' => 'Last Name'],
            'messenger' => ['title' => 'Messenger'],
            'other' => ['title' => 'Other'],
            'created' => ['title' => 'Created', 'type' => 'datetime'],
        ];

        $this->addRowAction('edit');
        $this->addRowAction('details');

}

public function renderForm()
     {

        $options = array(
            array(
              'id_option' => 'НОВОЕ', 
              'name' => 'НОВОЕ' 
            ),
            array(
              'id_option' => 'Заявка обработана',
              'name' => 'Заявка обработана'
            ),
            array(
                'id_option' => 'Заявка отправлен',
                'name' => 'Заявка отправлен'
              ),
            array(
                'id_option' => 'Недозвон - 1',
                'name' => 'Недозвон - 1'
              ),
            array(
                'id_option' => 'Недозвон - 2',
                'name' => 'Недозвон - 2'
              ),
            array(
                'id_option' => 'Отмена',
                'name' => 'Отмена'
              ),
        ); 

        $this->fields_form = array(
          'legend' => array(
          'title' => $this->l('Parceiros'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'post_type',
                    'default_value' => 0,
                ),
                array(
                    'type' => 'select',
                    'lang' => true,
                    'label' => $this->l('Статус'),
                    'name' => 'status',
                    'options' => array(
                      'query' => $options,
                      'id' => 'id_option', 
                      'name' => 'name'
                    )
                    
                  ),
                
                array(
                    'type' => 'text',
                    'label' => $this->l('Phone'),
                    'name' => 'phone',
                    'size' => 60,
                 
                    
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('User name'),
                    'name' => 'user_name',
                    'size' => 60,
                 
                    
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'fname',
                    'size' => 60,
                    'required' => true,
                    
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Last Name'),
                    'name' => 'lname',
                    'size' => 60,
                  
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Messenger'),
                    'name' => 'messenger',
                    'size' => 60,
                    'required' => true,
                    
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Other'),
                    'name' => 'other',
                    'size' => 60,
                 
                    'lang' => false,
                )      
                
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );


      
      
        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('deleteparceiros') && Tools::getValue('id_pfspreorder') != '')
        {
            $id_pfspreorder = Tools::getValue('id_pfspreorder');
           // $deletar = 'DELETE * FROM `' . _DB_PREFIX_ . 'parceiros` WHERE id_pfspreorder = '.$id_pfspreorder;
            $deletar = Db::getInstance()->delete($this->table, "id_pfspreorder = {$id_pfspreorder}");
            
            if (!$deletar){
                $this->errors[] = Tools::displayError('An error occurred while deleting the object.')
                        . ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
            }else{
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminPreorder'));
            }
        }elseif (Tools::isSubmit('submitAddpfspreorder')){

            //parent::validateRules();
            if (count($this->errors))
                return false;

            if (!$id_pfspreorder = Tools::getValue('id_pfspreorder')) { // PARA CRIAR


                $dados = array(
                    'status' => Tools::getValue('status'),
                    'phone'  => Tools::getValue('phone'),
                    'user_name'  => Tools::getValue('user_name'),
                    'fname'  => Tools::getValue('fname'),
                    'lname'  => Tools::getValue('lname'),
                    'messenger'  => Tools::getValue('messenger'),
                    'other'  => Tools::getValue('other'),
                    );

                
                $inserir = Db::getInstance()->insert($this->table, $dados);

                if (!$inserir){
                    $this->errors[] = Tools::displayError('An error occurred while deleting the object.')
                            . ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
                }else{
                    
                   Tools::redirectAdmin($this->context->link->getAdminLink('AdminPreorder'));
                }

            }elseif($id_pfspreorder = Tools::getValue('id_pfspreorder')) {
                $id_pfspreorder = Tools::getValue('id_pfspreorder');
                $dados = array(
                    'status' => Tools::getValue('status'),
                    'phone'  => Tools::getValue('phone'),
                    'user_name'  => Tools::getValue('user_name'),
                    'fname'  => Tools::getValue('fname'),
                    'lname'  => Tools::getValue('lname'),
                    'messenger'  => Tools::getValue('messenger'),
                    'other'  => Tools::getValue('other'),
                    );

                
                $inserir = Db::getInstance()->update($this->table, $dados, "id_pfspreorder = {$id_pfspreorder}");

                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPreorder'));
                
                        
            }
            

                         
        }
    }
}