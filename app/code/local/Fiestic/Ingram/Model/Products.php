<?php 

class Fiestic_Ingram_Model_Products extends Mage_Catalog_Model_Product

{

    public function getFinalPrice($qty = null) {
       
        $product=$this;
        
         if ($optionIds = $product->getCustomOption('option_ids')) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_'.$option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);
                    $cprice = unserialize(Mage::getModel('core/cookie')->get($confItemOption->getValue()));
                   // echo $confItemOption->getValue().'xx';
                    //$finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                   
                    if($cprice){
                        $finalPrice = $cprice['price'];
                    }
                    else return;
                        
                    }
            }
        }

        return $finalPrice;
   
       
        
                }

}

?>
