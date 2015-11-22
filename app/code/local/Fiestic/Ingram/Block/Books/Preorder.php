<?php

class Fiestic_Ingram_Block_Books_Preorder extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryPreOrder("Books");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'PRE ORDER';
    }
}
