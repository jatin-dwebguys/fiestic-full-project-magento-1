<?php

class Fiestic_Ingram_Block_Books_Award extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryAwards("Books");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'PULITZER AWARD WINNING';
    }
}
