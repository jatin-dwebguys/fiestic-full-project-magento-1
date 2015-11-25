<?php

class Fiestic_Ingram_Block_Music_New extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryNewReleases("Music");
    	return $data->Music;
    }
    public function getTitle(){
    	return 'NEW RELEASES';
    }
}
