<?php

class PwBackCallAjaxModuleFrontController extends ModuleFrontController
{
    /**
     * @var array
     */
    public $emails = array();

    /**
     * @var int
     */
    public $formExtended = 0;

    /**
     * @var array
     */
    public $response = array(
        'status' => 1
    );

    /**
     * @throws PrestaShopException
     */
    public function init()
    {
        parent::init();
    }

    /**
     *
     */
    public function initContent()
    {
        parent::initContent();

        $emails = Configuration::get('PW_BACK_EMAILS');
        $this->emails = $this->module->prepareEmails($emails);

        $input = Tools::getAllValues();
        switch ($input['action']) {
            case 'call':
                $this->sendCallNotify($input);
                break;
        }
    }

    /**
     * Отправка формы обратного звонка
     *
     * @param $data
     */
    public function sendCallNotify($data)
    {
        if (Configuration::get('PW_BACK_FIELD_NAME') == '1') {
            if (empty(trim($data['name']))) {
                $this->addError('name', 'Enter your name');
            }
        } else {
            $data['name'] = '';
        }
        if (empty(trim($data['phone'])) || !Validate::isPhoneNumber($data['phone'])) {
            $this->addError('phone', 'Not valid phone number');
        }
        if (Configuration::get('PW_BACK_FIELD_EMAIL') == '1') {
            if (!isset($data['email']) || !Validate::isEmail($data['email'])) {
                $this->addError('email', 'Not valid email address');
            }
        } else {
            $data['email'] = '';
        }
        if (!isset($data['comment'])) {
            $data['comment'] = '';
        }
        if (!isset($data['URL'])) {
            $data['URL'] = '';
        }
        if ($this->isErrors()) {
            $this->ajaxDie(Tools::jsonEncode($this->response));
        }
        $mailParams = $this->prepareMailParams($data);

        $subject = 'Back call';
        foreach ($this->emails as $email) {
            if (!Mail::Send(
                (int)$this->context->cookie->id_lang,
                'pwbackcall',
                $subject,
                $mailParams,
                $email,
                null,
                ($module->context->cookie->email ? $module->context->cookie->email : null),
                ($module->context->cookie->customer_firstname ? $module->context->cookie->customer_firstname . ' ' .
                    $module->context->cookie->customer_lastname : null),
                null,
                null,
                $this->module->getLocalPath() . 'mails/'
            )
            ) {
                $this->addError('form', 'Failed to register a query');
                $this->ajaxDie(Tools::jsonEncode($this->response));
            }
        }

        $this->ajaxDie(Tools::jsonEncode($this->response));
    }

    /**
     * @param $field
     * @param $error
     */
    public function addError($field, $error)
    {
        if (1 == $this->response['status']) {
            $this->response['status'] = 0;
        }
        $this->response['errors'][$field] = $error;
    }

    /**
     * @return bool
     */
    public function isErrors()
    {
        return !empty($this->response['errors']) ? true : false;
    }

    /**
     * @param $data
     * @return array
     */
    public function prepareMailParams($data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $result['{' . $key . '}'] = $value;
        }
        return $result;
    }
}
