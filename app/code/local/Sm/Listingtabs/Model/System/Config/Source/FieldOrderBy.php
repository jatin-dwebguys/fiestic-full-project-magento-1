<?php
/*------------------------------------------------------------------------
 # SM Listing Tabs - Version 2.0.1
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Listingtabs_Model_System_Config_Source_FieldOrderBy
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'name', 'label' => Mage::helper('listingtabs')->__('Name')),
            array('value' => 'entity_id', 'label' => Mage::helper('listingtabs')->__('Id')),
            array('value' => 'position', 'label' => Mage::helper('listingtabs')->__('Position')),
            array('value' => 'created_at', 'label' => Mage::helper('listingtabs')->__('Date Created')),
            array('value' => 'price', 'label' => Mage::helper('listingtabs')->__('Price')),
            array('value' => 'lastest_product', 'label' => Mage::helper('listingtabs')->__('New Arrivals')),
            array('value' => 'top_rating', 'label' => Mage::helper('listingtabs')->__('Staff Recommend')),
            array('value' => 'most_reviewed', 'label' => Mage::helper('listingtabs')->__('Most Reviews')),
            array('value' => 'most_viewed', 'label' => Mage::helper('listingtabs')->__('Most Viewed')),
            array('value' => 'best_sales', 'label' => Mage::helper('listingtabs')->__('Best Saller')),
        );
    }
}
