<?php

class Fiestic_Ingram_Block_Film_New extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryNewReleases("Film");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'NEW RELEASES';
    }
}
