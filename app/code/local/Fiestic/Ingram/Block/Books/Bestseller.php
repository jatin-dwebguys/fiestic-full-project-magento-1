<?php

class Fiestic_Ingram_Block_Books_Bestseller extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryDataBestSeller("Books");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'BEST SELLER';
    }
}
