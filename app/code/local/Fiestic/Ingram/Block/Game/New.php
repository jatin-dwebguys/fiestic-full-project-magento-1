<?php

class Fiestic_Ingram_Block_Game_New extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryNewReleases("Games");
    	return $data->Book;
    }
    public function getTitle(){
    	return 'NEW GAMES';
    }
}
