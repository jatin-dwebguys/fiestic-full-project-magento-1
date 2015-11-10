<?php

class Fiestic_Ingram_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
	$isbnNo = 'BN=' . $this->getRequest()->getParam('BN');
	Mage::getModel('ingram/shop')->getProductData($isbnNo);
//product = zMage::registry('product');
    	//echo '<pre>';print_r($_product);die; 
          /*echo '<pre>';
          print_r($ingramSearch);
          die; //*/
        $this->loadLayout();
        $this->renderLayout();
    }
 
}

