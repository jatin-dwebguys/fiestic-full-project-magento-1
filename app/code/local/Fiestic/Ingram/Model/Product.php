<?php

class Fiestic_Ingram_Model_Product extends Mage_Core_Model_Abstract {

    public function setProductData($data) {
        $product = Mage::getModel('catalog/product');
        foreach ($data->Book as $item) {
            $product->setData('item_id', (string) $item->Basic->EAN);
            $product->setData('product_id', (string) $item->Basic->EAN);
            $product->setData('is_in_stock', 1);
            $product->setData('type_id', 'simple');
            $product->setData('name', $item->Basic->TitleLeadingArticle . ' ' . $item->Basic->Title);

            $product->setData('entity_id', (string) $item->Basic->ISBN);
            $product->setData('display_mode', 'PRODUCTS');
            $product->setData('price', (string) $item->Basic->PubListPrice);
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
            $product->setData('short_description', 'The...........');
            $authors = '';
            foreach ($item->Basic->Contributor as $val) {
                if ($val->Role == 'Author') {
                    if ($authors == '') {
                        $authors .= $val->Name;
                    } else {
                        $authors .= '<br>' . $val->Name;
                    }
                }
            }
            $additional = array();

            $additional['Author'] = $authors;
            $additional['PubDate'] = (string) $item->Basic->PubDate;
            $additional['Publisher'] = (string) $item->Basic->Publisher;
            $additional['Binding'] = (string) $item->Basic->Binding;
            $additional['ProductFormat'] = (string) $item->Basic->ProductFormat;
            $additional['Media'] = (string) $item->Basic->Media;
            $additional['Language'] = (string) $item->Basic->Language;
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

