<?php

class Fiestic_Ingram_Block_Film_Preorder extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryPreOrder("Film");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'PRE ORDER';
    }
}
