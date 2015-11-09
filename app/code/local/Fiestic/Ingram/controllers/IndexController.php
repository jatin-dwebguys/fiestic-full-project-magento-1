<?php

class Fiestic_Ingram_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {

        $client = new SoapClient("http://idswebtest.ingramcontent.com/cws/companion.asmx?wsdl");

        $userInfo = array('UserName' => 'F1eSt1c_1278', 'Password' => 'p@ssW0rd');

        $headers = new SoapHeader('http://ingrambook.com/CompDataAccess/companion', 'UserInfo', $userInfo);

        $client->__setSoapHeaders($headers);

        $params = array(
            'queryType' => 1,
            'query' => 'BN=038535066X',
            'startRecord' => '1',
            'endRecord' => '25',
            'sortField' => '',
            'liveUpdate' => '',
            'dataRequest' => 'IMG,IM60,IM90'
        );

        $res = $client->SearchRequestEnhanced($params);

        $ingramSearch = new SimpleXMLElement($res->SearchRequestTypes12349EnhancedResult->any);
//        echo '<pre>';
//        print_r($ingramSearch);
//        die;
        $product = Mage::getModel('catalog/product');
        foreach ($ingramSearch->Book as $item) {
            $product->setData('item_id', (string)$item->Basic->EAN);
            $product->setData('product_id', (string)$item->Basic->EAN);
            $product->setData('is_in_stock', 1);
            $product->setData('type_id', 'simple');
            $product->setData('name', $item->Basic->TitleLeadingArticle . ' ' . $item->Basic->Title);

            $product->setData('entity_id', (string)$item->Basic->ISBN);
            $product->setData('display_mode', 'PRODUCTS');
            $product->setData('price', (string)$item->Basic->PubListPrice);
            $product->setData('status', 1);
            $product->setData('visibility', 4);

            $product->setData('store_id', 1);
            $product->setData('entity_type_id', 4);
            $product->setData('attribute_set_id', 4);
            $product->setData('image', (string)$item->Basic->Image->IMG187);
            $product->setData('small_image', (string)$item->Basic->Image->IMG60);
            $product->setData('thumbnail', NULL);
            $product->setData('weight', 0.5000);
        }

        Mage::register('product', $product);



        $this->loadLayout();
        $this->renderLayout();
    }

}
