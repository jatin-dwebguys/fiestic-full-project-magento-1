<?php

class Fiestic_Ingram_Model_Product extends Mage_Core_Model_Abstract {

    public function setProductData($data) {
        $product = Mage::getModel('catalog/product');
        foreach ($data->Book as $item) {
            //echo '<pre>'; print_r($item);
            $product->setData('item_id', (string) $item->Basic->EAN);
            $product->setData('product_id', (string) $item->Basic->EAN);

            $is_in_stock = 0;

            $stock = $item->Ingram->Stock;
            $stock = json_decode(json_encode($stock),true);
            foreach($stock as $loc => $data){
                if($data['StockStatus'] == 'Y') {
                    $is_in_stock = 1;
                    break;
                }
            }
            if($is_in_stock == 0){
                $product->setData('is_salable',false);
            }
            $product->setData('is_in_stock', $is_in_stock);
            $product->setData('type_id', 'simple');

            if($item->Basic->TitleLeadingArticle){
                $product->setData('name', $item->Basic->TitleLeadingArticle . ' ' . $item->Basic->Title);
            }else{
                $product->setData('name', $item->Basic->Title);
            }

            $product->setData('entity_id', (string) $item->Basic->ISBN);
            $product->setData('display_mode', 'PRODUCTS');
            if((int)$item->Ingram->IngramPrice!=(int)$item->Basic->PubListPrice){
                $product->setData('special_price',(string) $item->Ingram->IngramPrice);
                $product->setData('price', (string) $item->Basic->PubListPrice);
            }else{
                $product->setData('price',(string) $item->Ingram->IngramPrice);
                $product->setData('special_price', false);
            }
            $product->setData('status', 1);
            $product->setData('visibility', 4);

            $product->setData('store_id', 1);
            $product->setData('entity_type_id', 4);
            $product->setData('attribute_set_id', 4);
            $product->setData('min_sale_qty', 1.0000);
            $product->setData('max_sale_qty', 0.0000);
            $product->setData('use_config_min_sale_qty', 1);
            $product->setData('use_config_max_sale_qty', 1);
            $product->setData('min_qty', 0.0000);
            $product->setData('image', (string) $item->Basic->Image->IMG187);
            $product->setData('small_image', (string) $item->Basic->Image->IMG60);
            $product->setData('thumbnail', NULL);
            $product->setData('weight', 0.5000);


            $product->setData('description', (string) $item->Basic->Annotation);
            $product->setData('short_description', (string)$item->Ingram->IngramSubject);
            $authors = '';
            foreach ($item->Basic->Contributor as $val) {
                if ($val->Role == 'Author') {
                    if ($authors == '') {
                        $authors .= $val->Name;
                    } else {
                        $authors .= ' - ' . $val->Name;
                    }
                }
            }
            $additional = array();

            $additional['Author'] = $authors;
            if($item->Basic->PubDate){
                $date = (string) $item->Basic->PubDate;
                $date = substr($date, 0,2) . '-' . substr($date, 2);
                $additional['PubDate'] = $date;
            }
            $additional['Publisher'] = (string) $item->Basic->Publisher;
            $additional['Binding'] = (string) $item->Basic->Binding;
            $additional['ProductFormat'] = (string) $item->Basic->ProductFormat;
            $additional['Media'] = (string) $item->Basic->Media;
            $additional['Language'] = (string) $item->Basic->Language;
            $additional['Accessory'] = (string) $item->Basic->Accessory;
            $additional['LargePrint'] = (string) $item->Basic->LargePrint;
            $additional['Illustrated'] = (string) $item->Basic->Illustrated;
            $additional['Pages'] = (string) $item->Basic->Pages;
            $additional['Units'] = (string) $item->Basic->Units;

            
            $additional['CountryOfOrigin'] = (string) $item->Ingram->Logistics->CountryOfOrigin;
            $product->setData('additional', $additional);
        }
        if ($product) {
            Mage::register('product', $product);
            return $item->Basic->TitleLeadingArticle . ' ' . $item->Basic->Title;
        } else {
            return false;
        }
    }

}

