<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Stobok_Model_System_Config_Source_ListBgRepeat
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'repeat', 'label'=>Mage::helper('stobok')->__('repeat')),
			array('value'=>'repeat-x', 'label'=>Mage::helper('stobok')->__('repeat-x')),
			array('value'=>'repeat-y', 'label'=>Mage::helper('stobok')->__('repeat-y')),
			array('value'=>'no-repeat', 'label'=>Mage::helper('stobok')->__('no-repeat'))
		);
	}
}
