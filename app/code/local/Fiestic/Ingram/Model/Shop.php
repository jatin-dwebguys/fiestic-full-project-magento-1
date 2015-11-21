<?php

class Fiestic_Ingram_Model_Shop extends Mage_Core_Model_Abstract {
    private function getSoap(){
        $client = new SoapClient("https://cws.ingrambook.com/CWS/companion.asmx?wsdl");

        $userInfo = array('UserName' => 'F1eSt1c_1278', 'Password' => 'phaFEn2v');

        $headers = new SoapHeader('http://ingrambook.com/CompDataAccess/companion', 'UserInfo', $userInfo);

        $client->__setSoapHeaders($headers);
        return $client;
    }
    public function getApiData($query,$query_type = 1,$startRecord = 1, $endRecord = 25,$sortField='',$liveUpdate ='',$dataRequest='IMG,IM60,IM90'){
        $client = $this->getSoap();
        $params = array(
            'queryType' => $query_type,
            'query' => '('.$query .' and SRC<>"X" and DF<>"Y" and (RF<>"P" or RF<>"E" or RF<>"D" or RF<>"L" or RF<>"V"))',
            'startRecord' => $startRecord,
            'endRecord' => $endRecord,
            'sortField' => $sortField,
            'liveUpdate' => $liveUpdate,
            'dataRequest' => $dataRequest
        );
        $res = $client->SearchRequestEnhanced($params);

        $ingramSearch = new SimpleXMLElement($res->SearchRequestTypes12349EnhancedResult->any);

        //echo '<pre>'; print_r($params);print_r($ingramSearch);die;

        $now=Mage::getModel('core/date')->date('Y-m-d H:i:s');

        $log=array();
        $log[]="Params";
        $log[]=$params;
        $log[]="Response";
        $log[]=$ingramSearch;
        //Mage::log($log, null, '/Ingram/'.$now.'.log');
        return $ingramSearch;

    }

    public function getProductReview($id){
        $msg = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Header>
            <UserInfo xmlns="http://ingrambook.com/CompDataAccess/companion">
              <UserName>F1eSt1c_1278</UserName>
              <Password>phaFEn2v</Password>
            </UserInfo>
          </soap:Header>
          <soap:Body>
            <SearchRequestType5 xmlns="http://ingrambook.com/CompDataAccess/companion">
              <queryType>5</queryType>
              <isbn>'.$id.'</isbn>
              <dataRequest>PM</dataRequest>
            </SearchRequestType5>
          </soap:Body>
        </soap:Envelope>';

        $client = new soapClient("https://cws.ingrambook.com/CWS/companion.asmx?wsdl");

        $res = $client->__doRequest($msg,"https://cws.ingrambook.com/CWS/companion.asmx",
                "http://ingrambook.com/CompDataAccess/companion/SearchRequestType5",0);

        echo '<pre>';
        $res = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $res);
        $xml = new SimpleXMLElement($res);
        $array = json_decode(json_encode($xml->soapBody->SearchRequestType5Response->SearchRequestType5Result), TRUE);
        print_r($array);
        die;
    }

    public function getSearchData($query,$type){
        if($type == 'Book'){
            $ingramSearch = $this->getApiData('KW=' . $query,'1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Author'){
            $ingramSearch = $this->getApiData('CO=' . '*'.$query.'*','1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Publisher'){
            $ingramSearch = $this->getApiData('KWPU=' . $query,'1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Subject'){
            $ingramSearch = $this->getApiData('KWSU=' . $query,'1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }
        //$result = Mage::getModel('ingram/product')->setProductData($ingramSearch);
        return $ingramSearch;
    }

    public function getProductData($id, $type) {
        $ingramSearch = $this->getApiData($id,1,1,2,'','Y','IMG,IM60,IM90,LOGI,PM,BIB,RQ,JF,JB,AWD');
        $result = Mage::getModel('ingram/product')->setProductData($ingramSearch);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }


     public function getCategoryData($category_name,$parent_category,$page = 1,$sort = 'DE|1') {
        $page--;
        $start = $page * 23 + 1;
        $end = ($page + 1) * 23 + 1;

        if($parent_category == 'Music' || $category_name == 'Music'){
            $ingramSearch = $this->getApiData('KW='.$category_name,2,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else if($type == 'Film' || $category_name == 'Film'){
            $category_name = str_replace(" ", "", $category_name);
            $category_name = preg_replace('/[^a-zA-Z0-9\']/', '|', $category_name);
            $ingramSearch = $this->getApiData('BSU='.$category_name.' and BND=DVD ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else{
            $category_name = str_replace(" ", "", $category_name);
            $category_name = preg_replace('/[^a-zA-Z0-9\']/', '|', $category_name);
            $ingramSearch = $this->getApiData('BSU='.$category_name.' and BND<>DVD ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }


        Mage::register('ingram_category', $ingramSearch);
        //echo "<pre>"; print_r($ingramSearch); die;

    }
    
     public function getCategoryDataBestSeller($category_name,$parent_category,$page = 1,$sort = 'DE|1') {
        $page--;
        $start = $page * 23 + 1;
        $end = ($page + 1) * 23 + 1;

        if($parent_category == 'Music' || $category_name == 'Music'){
            $ingramSearch = $this->getApiData('KW='.$category_name,2,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else if($type == 'Film' || $category_name == 'Film'){
            $category_name = str_replace(" ", "", $category_name);
            $category_name = preg_replace('/[^a-zA-Z0-9\']/', '|', $category_name);
            $ingramSearch = $this->getApiData('BSU='.$category_name.' and BND=DVD ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else{
            $category_name = str_replace(" ", "", $category_name);
            $category_name = preg_replace('/[^a-zA-Z0-9\']/', '|', $category_name);
            $ingramSearch = $this->getApiData('BSU='.$category_name.' and BND<>DVD ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }


        Mage::register('ingram_category_best_seller', $ingramSearch);
        //echo "<pre>"; print_r($ingramSearch); die;

    }


}
