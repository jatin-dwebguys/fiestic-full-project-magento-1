<?php

class Fiestic_Ingram_Block_Game_Bestseller extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryDataBestSeller("Games");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'BESTSELLERS';
    }
}
