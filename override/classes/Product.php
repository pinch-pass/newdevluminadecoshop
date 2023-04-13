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

class Product extends ProductCore
{
	public static function getFrontFeaturesStatic($id_lang, $id_product)
	{
		if (!Feature::isFeatureActive())
			return array();
		if (!array_key_exists($id_product.'-'.$id_lang, self::$_frontFeaturesCache))
		{
      // Display multi-valued features as comma-separated values in product
      // data sheet.
			self::$_frontFeaturesCache[$id_product.'-'.$id_lang] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT name, GROUP_CONCAT(value SEPARATOR \', \') AS value, pf.id_feature
				FROM '._DB_PREFIX_.'feature_product pf
				LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = '.(int)$id_lang.')
				LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = '.(int)$id_lang.')
				LEFT JOIN '._DB_PREFIX_.'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = '.(int)$id_lang.')
				'.Shop::addSqlAssociation('feature', 'f').'
				WHERE pf.id_product = '.(int)$id_product.'
        GROUP BY pf.id_feature
				ORDER BY f.position ASC'
			);
		}
		return self::$_frontFeaturesCache[$id_product.'-'.$id_lang];
	}

	public function addFeaturesToDB($id_feature, $id_value, $cust = 0)
	{
		// Default behavior.
		if ($cust || !is_array($id_value)) {
				return parent::addFeaturesToDB($id_feature, $id_value, $cust);
			}

		// For multi-value features, build array of rows and insert into db.
		$base =  array(
		  'id_feature' => (int)$id_feature,
		  'id_product' => (int)$this->id,
		);
		$rows = array();
		foreach ($id_value as $value) {
		  if(!empty($value)) {
			$rows[] = $base + array('id_feature_value' => $value);
		  }
		}
		if(!empty($rows)) {
		  Db::getInstance()->insert('feature_product', $rows);
		}

		// From parent.
		SpecificPriceRule::applyAllRules(array((int)$this->id));
			if ($id_value) {
				return ($id_value);
		}
	}
    public static function getProductProperties($id_lang, $row, Context $context = null)
    {
		$p = parent::getProductProperties($id_lang, $row, $context);
		$pr = new Product;
		$pr->id = (int)$p['id_product'];
		$images = $pr->getImages(Context::getContext()->cookie->id_image);
		if(isset($images[1]))
			$p['id_image2'] = $p['id_product'].'-'.$images[1]['id_image'];
		return $p;
    }
// public static function getNewProducts($id_lang, $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null, Context $context = null)
//     {
//         $id_category_new = 22;
//         if (!$context) {
//             $context = Context::getContext();
//         }

//         $front = true;
//         if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
//             $front = false;
//         }

//         if ($page_number < 0) {
//             $page_number = 0;
//         }
//         if ($nb_products < 1) {
//             $nb_products = 10;
//         }
//         if (empty($order_by)) {
//             $order_by = 'date_add';
//         }
//         if (empty($order_way)) {
//             $order_way = 'DESC';
//         }
//         if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
//             $order_by_prefix = 'product_shop';
//         } elseif ($order_by == 'name') {
//             $order_by_prefix = 'pl';
//         }
//         if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
//             die(Tools::displayError());
//         }

//         $sql_groups = '';
//         if (Group::isFeatureActive()) {
//             $groups = FrontController::getCurrentCustomerGroups();
//             $sql_groups = ' AND EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
// 				JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = '.$id_category_new.' AND cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')
// 				WHERE cp.`id_product` = p.`id_product`)';
//         }

//         if (strpos($order_by, '.') > 0) {
//             $order_by = explode('.', $order_by);
//             $order_by_prefix = $order_by[0];
//             $order_by = $order_by[1];
//         }

//         if ($count) {
//             $sql = 'SELECT COUNT(p.`id_product`) AS nb
// 					FROM `'._DB_PREFIX_.'product` p
// 					'.Shop::addSqlAssociation('product', 'p').'
// 					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.id_category = '.$id_category_new.' AND cp.id_product = p.id_product)
// 					WHERE product_shop.`active` = 1
// 					AND cp.id_category = '.$id_category_new.'
// 					'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
// 					'.$sql_groups;
//             return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
//         }

//         /* @var $sql DbQueryCore */
//         $sql = new DbQuery();
//         $sql->select(
//             ' cp.position, p.*,product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
// 			pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
// 			product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new'
//         );

//         $sql->from('product', 'p');
//         $sql->join(Shop::addSqlAssociation('product', 'p'));
//         $sql->leftJoin('product_lang', 'pl', '
// 			p.`id_product` = pl.`id_product`
// 			AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl')
//         );
//         $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id);
//         $sql->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang);
//         $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');
//         $sql->leftJoin('category_product', 'cp', 'cp.id_category = '.$id_category_new.' AND cp.id_product = p.id_product');

//         $sql->where('product_shop.`active` = 1');
//         $sql->where('cp.id_category = '.$id_category_new.'');
//         if ($front) {
//             $sql->where('product_shop.`visibility` IN ("both", "catalog")');
//         }
// //        $sql->where('product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'"');
//         if (Group::isFeatureActive()) {
//             $groups = FrontController::getCurrentCustomerGroups();
//             $sql->where('EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
// 				JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = '.$id_category_new.' AND cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')
// 				WHERE cp.`id_product` = p.`id_product`)');
//         }

//         $sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way));
//         $sql->limit($nb_products, $page_number * $nb_products);

//         if (Combination::isFeatureActive()) {
//             $sql->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute');
//             $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id);
//         }
//         $sql->join(Product::sqlStock('p', 0));

//         $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

//         if (!$result) {
//             return false;
//         }

//         if ($order_by == 'price') {
//             Tools::orderbyPrice($result, $order_way);
//         }

//         $products_ids = array();
//         foreach ($result as $row) {
//             $products_ids[] = $row['id_product'];
//         }
//         // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
//         Product::cacheFrontFeatures($products_ids, $id_lang);
//         return Product::getProductsProperties((int)$id_lang, $result);
//     }
}
