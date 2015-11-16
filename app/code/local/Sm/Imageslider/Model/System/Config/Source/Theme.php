<?php

/*------------------------------------------------------------------------
 # SM Image Slider - Version 1.1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Imageslider_Model_System_Config_Source_Theme
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'theme1', 'label' => Mage::helper('imageslider')->__('Theme 1')),
			array('value' => 'theme2', 'label' => Mage::helper('imageslider')->__('Theme 2'))
		);
	}
}
