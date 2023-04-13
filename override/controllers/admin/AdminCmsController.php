<?php

class AdminCmsController extends AdminCmsControllerCore
{
	public function renderForm()
	{
		if (!$obj = $this->loadObject(true))
			return;

		if (Validate::isLoadedObject($this->object))
			$this->display = 'edit';
		else
			$this->display = 'add';

		$this->initToolbar();
		$this->initPageHeaderToolbar();

		$categories = CMSCategory::getCategories($this->context->language->id, false);
		$html_categories = CMSCategory::recurseCMSCategory($categories, $categories[0][1], 1, $this->getFieldValue($this->object, 'id_cms_category'), 1);

		//add


		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('CMS Page'),
				'icon' => 'icon-folder-close'
			),
			'input' => array(
				// custom template
				array(
					'type' => 'select_category',
					'label' => $this->l('CMS Category'),
					'name' => 'id_cms_category',
					'options' => array(
						'html' => $html_categories,
					),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta title'),
					'name' => 'meta_title',
					'id' => 'name', // for copyMeta2friendlyURL compatibility
					'lang' => true,
					'required' => true,
					'class' => 'copyMeta2friendlyURL',
					'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description'),
					'name' => 'meta_description',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
				),
				array(
					'type' => 'tags',
					'label' => $this->l('Meta keywords'),
					'name' => 'meta_keywords',
					'lang' => true,
					'hint' => array(
						$this->l('To add "tags" click in the field, write something, and then press "Enter."'),
						$this->l('Invalid characters:').' &lt;&gt;;=#{}'
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Friendly URL'),
					'name' => 'link_rewrite',
					'required' => true,
					'lang' => true,
					'hint' => $this->l('Only letters and the hyphen (-) character are allowed.')
				),
				//add
				
				array(
					'type' => 'textarea',
					'label' => $this->l('Page content'),
					'name' => 'content',
					'autoload_rte' => true,
					'lang' => true,
					'rows' => 5,
					'cols' => 40,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'text',
					'label' => $this->l('Станция метро'),
					'name' => 'name',
					'lang' => true,
					'hint' => $this->l('Станция метро')
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'text',
					'label' => $this->l('Адрес магазина'),
					'name' => 'adress',
					'lang' => true,
					'hint' => $this->l('Адрес магазина')
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'text',
					'label' => $this->l('Номер телефона'),
					'name' => 'phone',
					'lang' => true,
					'hint' => $this->l('Номер телефона')
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'text',
					'label' => $this->l('Время работы'),
					'name' => 'work_time',
					'lang' => true,
					'hint' => $this->l('Время работы')
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'text',
					'label' => $this->l('Ссылка на карту'),
					'name' => 'map_link',
					'lang' => true,
					'hint' => $this->l('Необходимо вставить только url без iframe и других значений')
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'text',
					'label' => $this->l('Ссылка на простроить маршрут'),
					'name' => 'route_link',
					'lang' => true,
					'hint' => $this->l('Ссылка на простроить маршрут')
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'text',
					'label' => $this->l('Ссылка на 3д тур'),
					'name' => 'tour_link',
					'lang' => true,
					'hint' => $this->l('Ссылка на 3д тур')
				):false),


				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'switch',
					'label' => $this->l(CMS::$options[0]),
					'name' => 'option_1',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'option_1_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'option_1_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'switch',
					'label' => $this->l(CMS::$options[1]),
					'name' => 'option_2',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'option_2_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'option_2_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'switch',
					'label' => $this->l(CMS::$options[2]),
					'name' => 'option_3',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'option_3_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'option_3_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'switch',
					'label' => $this->l(CMS::$options[3]),
					'name' => 'option_4',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'option_4_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'option_4_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'switch',
					'label' => $this->l(CMS::$options[4]),
					'name' => 'option_5',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'option_5_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'option_5_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'switch',
					'label' => $this->l(CMS::$options[5]),
					'name' => 'option_6',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'option_6_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'option_6_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				):false),
				((CMS::getCMSCategory((int)$obj->id) == 2)?
				array(
					'type' => 'switch',
					'label' => $this->l(CMS::$options[6]),
					'name' => 'option_7',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'option_7_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'option_7_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				):false),
				
				
				//end add
				array(
					'type' => 'switch',
					'label' => $this->l('Indexation by search engines'),
					'name' => 'indexation',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'indexation_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'indexation_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Displayed'),
					'name' => 'active',
					'required' => false,
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
			),
			'buttons' => array(
				'save_and_preview' => array(
					'name' => 'viewcms',
					'type' => 'submit',
					'title' => $this->l('Save and preview'),
					'class' => 'btn btn-default pull-right',
					'icon' => 'process-icon-preview'
				)
			)
		);

		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association'),
				'name' => 'checkBoxShopAsso',
			);
		}

		if (Validate::isLoadedObject($this->object))
			$this->context->smarty->assign('url_prev', $this->getPreviewUrl($this->object));

		$this->tpl_form_vars = array(
			'active' => $this->object->active,
			'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
		);
		
		return AdminController::renderForm();
	}

    public function postProcess()
    {
		

        parent::postProcess();
    }
}