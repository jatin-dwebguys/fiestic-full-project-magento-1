<?php
/*------------------------------------------------------------------------
 # SM Image Slider - Version 1.1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Imageslider_Model_System_Config_Source_WideBoxed
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'wide',     'label' => Mage::helper('imageslider')->__('Wide')),
			array('value' => 'boxed',    'label' => Mage::helper('imageslider')->__('Boxed')),
		);
	}
}
