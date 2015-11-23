<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_CategoryController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested category object
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCatagory()
    {
	


        Mage::dispatchEvent('catalog_controller_category_init_before', array('controller_action' => $this));

         $categoryId = (int) $this->getRequest()->getParam('id', false);

        if (!$categoryId) {
            return false;
        }

        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);

        $parent_category = $category->getParentCategory()->getName();

        $page = (int)$this->getRequest()->getParam('p', 1);
        $dir = $this->getRequest()->getParam('dir', false);
        $order = $this->getRequest()->getParam('order', false);

        $sort = 'PD|1';
        if($order == 'ranking'){
            if($dir == 'desc'){
                $sort = 'DE|1';
            }else{
                $sort = 'DE|0';
            }
        }else if($order == 'date'){
            if($dir == 'desc'){
                $sort = 'PD|1';
            }else{
                $sort = 'PD|0';
            }
        }else if($order == 'title'){
            if($dir == 'desc'){
                $sort = 'TTL|1';
            }else{
                $sort = 'TTL|0';
            }
        }else if($order == 'price'){
            if($dir == 'desc'){
                $sort = 'PR|1';
            }else{
                $sort = 'PR|0';
            }
        }else if($order == 'author'){
            if($dir == 'desc'){
                $sort = 'CO|1';
            }else{
                $sort = 'CO|0';
            }
        }


        //-------------------- code added for server categories----------------->> 
        $category_name=$category->getName();
        $description = $category->getDescription();
        if(strlen($description) > 0){
            $data = Mage::getModel('ingram/shop')->getCategoryDataDesc($description,$parent_category,$page,$sort);
        }else{
            $data = Mage::getModel('ingram/shop')->getCategoryData($category_name,$parent_category,$page,$sort);    
        }
        

        $books = array();
        $c = 0;
        foreach($ingramSearch->Book as $book){
            if($this->hasProductImage($book)){
                $c++;
            }
        }
        echo $c;
        if($c < 24){
            $this->getCategoryData
        }

        //------------------------------------------------------------------------->>
        //echo '<pre>';print_r($data);die;






        if (!Mage::helper('catalog/category')->canShow($category)) {
            return false;
        }
        Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());
        Mage::register('current_category', $category);
        Mage::register('current_entity_key', $category->getPath());

        try {
            Mage::dispatchEvent(
                'catalog_controller_category_init_after',
                array(
                    'category' => $category,
                    'controller_action' => $this
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $category;
    }

    /**
     * Recursively apply custom design settings to category if it's option
     * custom_use_parent_settings is setted to 1 while parent option is not
     *
     * @deprecated after 1.4.2.0-beta1, functionality moved to Mage_Catalog_Model_Design
     * @param Mage_Catalog_Model_Category $category
     * @param Mage_Core_Model_Layout_Update $update
     *
     * @return Mage_Catalog_CategoryController
     */
    protected function _applyCustomDesignSettings($category, $update)
    {
        if ($category->getCustomUseParentSettings() && $category->getLevel() > 1) {
            $parentCategory = $category->getParentCategory();
            if ($parentCategory && $parentCategory->getId()) {
                return $this->_applyCustomDesignSettings($parentCategory, $update);
            }
        }

        $validityDate = $category->getCustomDesignDate();

        if (array_key_exists('from', $validityDate) &&
            array_key_exists('to', $validityDate) &&
            Mage::app()->getLocale()->isStoreDateInInterval(null, $validityDate['from'], $validityDate['to'])
        ) {
            if ($category->getPageLayout()) {
                $this->getLayout()->helper('page/layout')
                    ->applyHandle($category->getPageLayout());
            }
            $update->addUpdate($category->getCustomLayoutUpdate());
        }

        return $this;
    }

    /**
     * Category view action
     */
    public function viewAction()
    {
        if ($category = $this->_initCatagory()) {
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');

            if (!$category->hasChildren()) {
                $update->addHandle('catalog_category_layered_nochildren');
            }

            $this->addActionLayoutHandles();
            $update->addHandle($category->getLayoutUpdateHandle());
            $update->addHandle('CATEGORY_' . $category->getId());
            $this->loadLayoutUpdates();

            // apply custom layout update once layout is loaded
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
            }

            if ($root = $this->getLayout()->getBlock('root')) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        }
        elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
}
