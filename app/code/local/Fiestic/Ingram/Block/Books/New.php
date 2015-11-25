<?php

class Fiestic_Ingram_Block_Books_New extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryNewReleases("Books");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'NEW RELEASES';
    }
}
