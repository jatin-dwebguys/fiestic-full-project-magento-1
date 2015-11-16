<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Stobok_Model_System_Config_Source_ListBodyFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'Arial', 'label'=>Mage::helper('stobok')->__('Arial')),
			array('value'=>'Open Sans', 'label'=>Mage::helper('stobok')->__('Open Sans')),
			array('value'=>'Ubuntu', 'label'=>Mage::helper('stobok')->__('Ubuntu')),
			array('value'=>'Arial Black', 'label'=>Mage::helper('stobok')->__('Arial-black')),
			array('value'=>'Courier New', 'label'=>Mage::helper('stobok')->__('Courier New')),
			array('value'=>'Georgia', 'label'=>Mage::helper('stobok')->__('Georgia')),
			array('value'=>'Impact', 'label'=>Mage::helper('stobok')->__('Impact')),
			array('value'=>'Lucida Console', 'label'=>Mage::helper('stobok')->__('Lucida-console')),
			array('value'=>'Lucida Grande', 'label'=>Mage::helper('stobok')->__('Lucida-grande')),
			array('value'=>'Palatino', 'label'=>Mage::helper('stobok')->__('Palatino')),
			array('value'=>'Tahoma', 'label'=>Mage::helper('stobok')->__('Tahoma')),
			array('value'=>'Times New Roman', 'label'=>Mage::helper('stobok')->__('Times New Roman')),	
			array('value'=>'Trebuchet', 'label'=>Mage::helper('stobok')->__('Trebuchet')),	
			array('value'=>'Verdana', 'label'=>Mage::helper('stobok')->__('Verdana'))		
		);
	}
}
