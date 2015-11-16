<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Stobok_Model_System_Config_Source_ListColor
{
	public function toOptionArray()
	{	
		return array(
		array('value'=>'blue', 'label'=>Mage::helper('stobok')->__('Blue')),
		array('value'=>'brown', 'label'=>Mage::helper('stobok')->__('Brown')),
		array('value'=>'green', 'label'=>Mage::helper('stobok')->__('Green')),
		array('value'=>'orange', 'label'=>Mage::helper('stobok')->__('Orange')),
		array('value'=>'purple', 'label'=>Mage::helper('stobok')->__('Purple')),
		array('value'=>'red', 'label'=>Mage::helper('stobok')->__('Red'))
		);
	}
}
