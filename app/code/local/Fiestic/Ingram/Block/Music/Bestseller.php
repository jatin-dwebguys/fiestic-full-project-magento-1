<?php

class Fiestic_Ingram_Block_Music_Bestseller extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryDataBestSeller("Music");
    	return $data->Music;
    }
    public function getTitle(){
    	return 'BESTSELLER';
    }
}
