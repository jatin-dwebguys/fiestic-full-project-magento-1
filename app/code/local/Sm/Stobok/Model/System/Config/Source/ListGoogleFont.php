<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Stobok_Model_System_Config_Source_ListGoogleFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'', 'label'=>Mage::helper('stobok')->__('No select')),
			array('value'=>'Dosis Regular', 'label'=>Mage::helper('stobok')->__('Dosis Regular')),
			array('value'=>'Anton', 'label'=>Mage::helper('stobok')->__('Anton')),
			array('value'=>'Questrial', 'label'=>Mage::helper('stobok')->__('Questrial')),
			array('value'=>'Kameron', 'label'=>Mage::helper('stobok')->__('Kameron')),
			array('value'=>'Oswald', 'label'=>Mage::helper('stobok')->__('Oswald')),
			array('value'=>'Open Sans', 'label'=>Mage::helper('stobok')->__('Open Sans')),
			array('value'=>'Ubuntu', 'label'=>Mage::helper('stobok')->__('Ubuntu')),
			array('value'=>'BenchNine', 'label'=>Mage::helper('stobok')->__('BenchNine')),
			array('value'=>'Droid Sans', 'label'=>Mage::helper('stobok')->__('Droid Sans')),
			array('value'=>'Droid Serif', 'label'=>Mage::helper('stobok')->__('Droid Serif')),
			array('value'=>'PT Sans', 'label'=>Mage::helper('stobok')->__('PT Sans')),
			array('value'=>'Vollkorn', 'label'=>Mage::helper('stobok')->__('Vollkorn')),
			array('value'=>'Neucha', 'label'=>Mage::helper('stobok')->__('Neucha')),
			array('value'=>'Cuprum', 'label'=>Mage::helper('stobok')->__('Cuprum'))	
		);
	}
}
