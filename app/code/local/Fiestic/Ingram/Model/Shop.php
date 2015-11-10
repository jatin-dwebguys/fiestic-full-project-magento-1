<?php

class Fiestic_Ingram_Model_Shop extends Mage_Core_Model_Abstract {

    public function getProductData($id) {

        $client = new SoapClient("http://idswebtest.ingramcontent.com/cws/companion.asmx?wsdl");

        $userInfo = array('UserName' => 'F1eSt1c_1278', 'Password' => 'p@ssW0rd');

        $headers = new SoapHeader('http://ingrambook.com/CompDataAccess/companion', 'UserInfo', $userInfo);

        $client->__setSoapHeaders($headers);
        //$isbncode = 'BN=' . $id;
	//echo $isbncode;die;
        $params = array(
            'queryType' => 1,
            'query' => $id,
            'startRecord' => '1',
            'endRecord' => '25',
            'sortField' => '',
            'liveUpdate' => '',
            'dataRequest' => 'IMG,IM60,IM90'
        );

        $res = $client->SearchRequestEnhanced($params);

        $ingramSearch = new SimpleXMLElement($res->SearchRequestTypes12349EnhancedResult->any);
        $result = Mage::getModel('ingram/product')->setProductData($ingramSearch);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}

