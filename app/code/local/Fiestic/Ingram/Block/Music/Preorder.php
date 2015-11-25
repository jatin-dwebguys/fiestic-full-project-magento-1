<?php

class Fiestic_Ingram_Block_Music_Preorder extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryPreOrder("Music");
    	return $data->Music;
    }
    public function getTitle(){
    	return 'PRE ORDER';
    }
}
