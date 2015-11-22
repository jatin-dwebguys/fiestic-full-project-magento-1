<?php

class Fiestic_Ingram_Block_Music_Award extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryAwards("Music");
    	return $data->Music;
    }
    public function getTitle(){
    	return 'AWARD WINNING';
    }
}
