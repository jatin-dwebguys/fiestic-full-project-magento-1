<?php

class Fiestic_Ingram_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {

	Mage::getModel('ingram/shop')->getProductData('110188696X');
          /*echo '<pre>';
          print_r($ingramSearch);
          die; //*/
        $this->loadLayout();
        $this->renderLayout();
    }

}

