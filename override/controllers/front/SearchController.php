<?php
class SearchController extends SearchControllerCore

	{
	public function initContent()
		{
			include(_PS_MODULE_DIR_.'blocksearch_mod'.DIRECTORY_SEPARATOR.'IqitSearch.php');
		$query = Tools::replaceAccentedChars(urldecode(Tools::getValue('q')));
		$original_query = Tools::getValue('q');
		$search_query_cat = (int)Tools::getValue('search_query_cat');
		$product_per_page = isset($this->context->cookie->nb_item_per_page) ? (int)$this->context->cookie->nb_item_per_page : Configuration::get('PS_PRODUCTS_PER_PAGE');
		if ($this->ajax_search)
			{
			self::$link = new Link();
			$image = new Image();
			$searchResults = IqitSearch::find((int)(Tools::getValue('id_lang')) , $query, $search_query_cat, 1, 10, 'position', 'desc', true);
			$taxes = Product::getTaxCalculationMethod();
			$currency = (int)Context::getContext()->currency->id;
			$iso_code = $this->context->language->iso_code;
			if (is_array($searchResults))
			foreach($searchResults as & $product)
				{
				$imageID = $image->getCover($product['id_product']);
				if (isset($imageID['id_image'])) $imgLink = $this->context->link->getImageLink($product['prewrite'], (int)$product['id_product'] . '-' . $imageID['id_image'], 'small_default');
				  else $imgLink = _THEME_PROD_DIR_ . $iso_code . "-default-small_default.jpg";
				$product['product_link'] = $this->context->link->getProductLink($product['id_product'], $product['prewrite'], $product['crewrite']);
				$product['obr_thumb'] = $imgLink;
				$product['product_price'] = Product::getPriceStatic((int)$product['id_product'], false, NULL, 2);
				if ($taxes == 0 OR $taxes == 2) $product['product_price'] = Tools::displayPrice(Product::getPriceStatic((int)$product['id_product'], true) , $currency);
				elseif ($taxes == 1) $product['product_price'] = Tools::displayPrice(Product::getPriceStatic((int)$product['id_product'], false) , $currency);
				}

			$this->ajaxDie(Tools::jsonEncode($searchResults));
			}

		// Only controller content initialization when the user use the normal search

		parent::initContent();
		if ($this->instant_search && !is_array($query))
			{
			$this->productSort();
            $this->n = abs((int)(Tools::getValue('n', $product_per_page)));
            $this->p = abs((int)(Tools::getValue('p', 1)));
            $search = IqitSearch::find($this->context->language->id, $query, $search_query_cat, 1, 10, 'position', 'desc');
            Hook::exec('actionSearch', array('expr' => $query, 'total' => $search['total']));
            $nbProducts = $search['total'];
            $this->pagination($nbProducts);
            $this->addColorsToProductList($search['result']);

            $this->context->smarty->assign(array(
                'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
                'search_products' => $search['result'],
                'nbProducts' => $search['total'],
                'search_query' => $original_query,
                'instant_search' => $this->instant_search,
                'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));

			}
		elseif (($query = Tools::getValue('search_query', Tools::getValue('ref'))) && !is_array($query))
			{
			$this->productSort();
			$this->n = abs((int)(Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'))));
			$this->p = abs((int)(Tools::getValue('p', 1)));
			$original_query = $query;
			$query = Tools::replaceAccentedChars(urldecode($query));
			$search = IqitSearch::find($this->context->language->id, $query, $search_query_cat, $this->p, $this->n, $this->orderBy, $this->orderWay);
			if (is_array($search['result']))
			foreach($search['result'] as & $product) $product['link'].= (strpos($product['link'], '?') === false ? '?' : '&') . 'search_query=' . urlencode($query) . '&results=' . (int)$search['total'];
			Hook::exec('actionSearch', array(
				'expr' => $query,
				'total' => $search['total']
			));
			$nbProducts = $search['total'];
			$this->pagination($nbProducts);
			$this->addColorsToProductList($search['result']);
			$this->context->smarty->assign(array(
				'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
				'search_products' => $search['result'],
				'nbProducts' => $search['total'],
				'search_query' => $original_query,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
			));
			}
		elseif (($tag = urldecode(Tools::getValue('tag'))) && !is_array($tag))
			{
			$nbProducts = (int)(Search::searchTag($this->context->language->id, $tag, true));
			$this->pagination($nbProducts);
			$result = Search::searchTag($this->context->language->id, $tag, false, $this->p, $this->n, $this->orderBy, $this->orderWay);
			Hook::exec('actionSearch', array(
				'expr' => $tag,
				'total' => count($result)
			));
			$this->addColorsToProductList($result);

			$this->context->smarty->assign(array(
				'search_tag' => $tag,
				'products' => $result, // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
				'search_products' => $result,
				'nbProducts' => $nbProducts,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
			));
			}
		  else
			{
			$this->context->smarty->assign(array(
				'products' => array() ,
				'search_products' => array() ,
				'pages_nb' => 1,
				'nbProducts' => 0
			));
			}

		$this->context->smarty->assign(array(
			'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY') ,
			'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')
		));
		$this->setTemplate(_PS_THEME_DIR_ . 'search.tpl');
		}
	}

