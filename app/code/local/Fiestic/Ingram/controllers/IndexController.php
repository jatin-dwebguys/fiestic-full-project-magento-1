<?php

class Fiestic_Ingram_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
	$isbnNo = 'BN=' . $this->getRequest()->getParam('BN');
	$type = 'BN';
	$name = Mage::getModel('ingram/shop')->getProductData($isbnNo, $type);
	$this->loadLayout();
	$this->getLayout()->getBlock('head')->setTitle($this->__($name));
        $this->renderLayout();
    }

    public function searchAction() {
	$isbnNo = 'KW=' . $this->getRequest()->getParam('q');
	$type = 'KW';//die;

	$data = Mage::getModel('ingram/shop')->getProductData($isbnNo, $type);
	Mage::register('ingram_products', $data);
        $this->loadLayout();
	$this->getLayout()->getBlock('head')->setTitle($this->__('Search Result Of ' . $this->getRequest()->getParam('q')));
	$this->renderLayout();
    }
 
}

