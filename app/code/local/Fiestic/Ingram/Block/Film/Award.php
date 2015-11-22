<?php

class Fiestic_Ingram_Block_Film_Award extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryAwards("Film");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'AWARD WINNING';
    }
}
