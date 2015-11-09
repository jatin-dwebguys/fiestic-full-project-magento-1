<?php
class Excellence_Product_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/product?id=15 
    	 *  or
    	 * http://site.com/product/id/15 	
    	 */
    	/* 
		$product_id = $this->getRequest()->getParam('id');

  		if($product_id != null && $product_id != '')	{
			$product = Mage::getModel('product/product')->load($product_id)->getData();
		} else {
			$product = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($product == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$productTable = $resource->getTableName('product');
			
			$select = $read->select()
			   ->from($productTable,array('product_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$product = $read->fetchRow($select);
		}
		Mage::register('product', $product);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}