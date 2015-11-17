<?php

class Fiestic_Ingram_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $isbnNo = 'BN=' . $this->getRequest()->getParam('BN', '');
        $type = 'BN';
        //$name = Mage::getModel('ingram/shop')->getProductReview($this->getRequest()->getParam('BN'), $type);
        $name = Mage::getModel('ingram/shop')->getProductData($isbnNo, $type);
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__($name));
        $this->renderLayout();
    }

    public function searchAction() {
        $query = $this->getRequest()->getParam('q', '');
        $search_type = $this->getRequest()->getParam('type', 'Book');

        if (sizeof(explode(' ', $query)) == 1) {
            //single word
            if (strlen($query) == 13 || strlen($query) == 10) {
                $this->_redirectUrl('ingram/index/search', array('BN' => $query));
                return;
            }
        }

        $data = Mage::getModel('ingram/shop')->getSearchData($query, $search_type);
        Mage::register('ingram_products', $data);
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Search Result Of ' . $this->getRequest()->getParam('q')));
        $this->renderLayout();
    }

    public function addtocartAction() {

        $isbn = $this->getRequest()->getParam('proId');
        if ($isbn) {

            $qty = $this->getRequest()->getParam('qty');
            $cartproductdetails = array();
            $cartproductdetails['name'] = $this->getRequest()->getParam('cart_product_name');
            $cartproductdetails['price'] = $this->getRequest()->getParam('cart_product_price');
            $cartproductdetails['image'] = $this->getRequest()->getParam('cart_product_image');
            $name = $isbn;
            Mage::getModel('core/cookie')->set($name, serialize($cartproductdetails), "36000");
            $sku = "custom_product";
            $product = Mage::getModel('catalog/product');
            $product->load($product->getIdBySku('custom_product'));
            $product_id = $product->getId();
            $cart = Mage::getSingleton('checkout/cart');
            $cart->init();
            $cart->addProduct($product, array('product_id' => $product_id,
                'qty' => $qty,
                'options' => array(16 => $isbn)
            ));
            $cart->save();
            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

            Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl() . 'checkout/cart');
        } else {
            Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
        }
    }

}
