<?php
/*------------------------------------------------------------------------
 # SM Featured Category  - Version 1.0.0
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
class Sm_FeaturedCategory_Model_System_Config_Source_CatOrder
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'name', 'label'        => Mage::helper('featuredcategory')->__('Name')),
            array('value' => 'position', 'label'    => Mage::helper('featuredcategory')->__('Position')),
            array('value' => 'random', 'label'      => Mage::helper('featuredcategory')->__('Random')),
        );
    }
}
