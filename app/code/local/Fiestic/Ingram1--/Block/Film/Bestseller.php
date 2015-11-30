<?php

class Fiestic_Ingram_Block_Film_Bestseller extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryDataBestSeller("Film");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'BESTSELLERS';
    }
}
