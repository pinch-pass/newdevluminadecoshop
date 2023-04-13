<?php
/**
* 2007-2020 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2020 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class AmazzingFilterCronModuleFrontController extends ModuleFrontControllerCore
{
    public function initContent()
    {
        $token = Tools::getValue('token');
        if ($token == $this->module->getCronToken()) {
            $id_shop = (int)Tools::getValue('id_shop');
            $action = pSQL(Tools::getValue('action'));
            $total_indexed = (int)Tools::getValue('total_indexed');
            $time = pSQL(Tools::getValue('time', microtime(true)));
            if (Tools::getValue('complete')) {
                echo 'Total products indexed: '.$total_indexed;
                echo '<br>';
                echo 'Processing time: '.Tools::ps_round((microtime(true) - $time), 2).' seconds';
            } elseif ($action == 'show-summary') {
                $indexation_data = $this->module->indexationInfo('count', $this->module->all_shop_ids);
                echo '<pre>';
                print_r($indexation_data);
                echo '</pre>';
            } elseif ($this->isAvailableAction($action)) {
                $this->indexProducts($action, $id_shop, $total_indexed, $time);
            }
        }
        exit();
    }

    private function isAvailableAction($action)
    {
        $available = array('index-all' => 1, 'index-missing' => 1, 'index-selected' => 1);
        return !empty($available[$action]);
    }

    private function indexProducts($action, $id_shop, $total_indexed, $time)
    {
        $products_per_request = (int)Tools::getValue('products_per_request', 1000);
        $params = array(
            'id_shop' => $id_shop,
            'total_indexed' => (int)$total_indexed,
            'time' => $time,
            'products_per_request' => $products_per_request,
            'action' => $action,
        );
        if ($action == 'index-selected') {
            $ids = $this->module->formatIDs(explode('-', Tools::getValue('ids')));
            $indexed = $this->module->indexProduct($ids, false, array($id_shop));
            $params['total_indexed'] = count($this->module->formatIDs($indexed));
            $params['complete'] = 1;
        } elseif ($action == 'index-all') {
            $params['total_indexed'] += $this->module->reIndexProducts($time, $products_per_request, array($id_shop));
            $indexation_data = $this->module->getIndexationProcessData($time, true);
            if (empty($indexation_data[$id_shop]['missing'])) {
                $params['complete'] = 1;
            }
        } else {
            $params['total_indexed'] += $this->module->indexMissingProducts($products_per_request, array($id_shop));
            $indexation_data = $this->module->indexationInfo('count', array($id_shop));
            if (empty($indexation_data[$id_shop]['missing'])) {
                $params['complete'] = 1;
            }
        }
        $url = $this->module->getCronURL($id_shop, $params);
        Tools::redirect($url);
    }
}
