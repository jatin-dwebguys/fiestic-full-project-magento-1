<?php

class Fiestic_Ingram_Block_Game_Award extends Mage_Core_Block_Template {
    public function getList(){
    	$data = Mage::getModel('ingram/shop')->getCategoryAwards("Games");
    	return $data->Gift;
    }
    public function getTitle(){
    	return 'PULITZER AWARD WINNING';
    }
}
