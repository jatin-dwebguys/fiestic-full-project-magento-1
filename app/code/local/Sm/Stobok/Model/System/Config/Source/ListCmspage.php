<?php
/*------------------------------------------------------------------------
 # SM Stobok - Version 1.1
 # Copyright (c) 2013 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Stobok_Model_System_Config_Source_ListCmspage
{
	public function toOptionArray()
	{	
		return array(
		array('value'=>'homepage1', 'label'=>Mage::helper('stobok')->__('Default')),
		array('value'=>'homepage2', 'label'=>Mage::helper('stobok')->__('Homepage style 2')),
		array('value'=>'homepage3', 'label'=>Mage::helper('stobok')->__('Homepage style 3')),
		array('value'=>'homepage4', 'label'=>Mage::helper('stobok')->__('Homepage style 4')),
		);
	}
}
