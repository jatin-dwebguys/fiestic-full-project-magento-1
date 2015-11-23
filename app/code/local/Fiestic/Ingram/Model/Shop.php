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
        //$query = '('.$query .' and SRC<>"X" and DF<>"Y" and (RF<>"P" or RF<>"E" or RF<>"D" or RF<>"L" or RF<>"V"))';
        $query = '('.$query .' and SRC<>"X")';
        $params = array(
            'queryType' => $query_type,
            'query' => $query,
            'startRecord' => $startRecord,
            'endRecord' => $endRecord,
            'sortField' => $sortField,
            'liveUpdate' => $liveUpdate,
            'dataRequest' => $dataRequest
        );
        $res = $client->SearchRequestEnhanced($params);

        $ingramSearch = new SimpleXMLElement($res->SearchRequestTypes12349EnhancedResult->any);

        // echo '<pre>'; print_r($params);print_r($ingramSearch);die;
        if($ingramSearch->Error){
            throw new Exception($ingramSearch->Error . '<br/>' . $query);
        }
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
            $ingramSearch = $this->getApiData('KW=' . $query.' and (MT="Book" or MT="Ebook") ','1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Author'){
            $ingramSearch = $this->getApiData('CO=' . '*'.$query.'*','1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Publisher'){
            $ingramSearch = $this->getApiData('KWPU=' . $query,'1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Subject'){
            $ingramSearch = $this->getApiData('KWSU=' . $query,'1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Music'){
            $ingramSearch = $this->getApiData('KW=' . $query,'2','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Film'){
            $ingramSearch = $this->getApiData('KW=' . $query.' and (MT="Video Product" or MT="Film")','2','1','25','','Y','IMG,IM60,IM90,LOGI');
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
    public function getCategoryDataDesc($category_name,$parent_category,$page = 1,$sort = 'DE|1') {
        $page--;
        $start = $page * 23 + 1;
        $end = ($page + 1) * 23 + 1;

        if($parent_category == 'Music' || $category_name == 'Music'){
            $ingramSearch = $this->getApiData('KW='.$category_name,2,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else if($parent_category == 'Films' || $parent_category == 'Film' || $category_name == 'Films'){
            $ingramSearch = $this->getApiData('BSC='.$category_name.' and (MT="Video Product" or MT="Film") ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else{
            if(sizeof(explode(' ', $category_name)) > 1){
                $exp = explode(' ', $category_name);
                $q = array();
                for($i=0;$i<sizeof($exp);$i++){
                    $q[] = 'BSC='.$exp[$i];
                }
                $query = implode(' or ', $q);
                $ingramSearch = $this->getApiData($query.' and (MT="Book" or MT="Ebook") ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
            }else{
                $ingramSearch = $this->getApiData('BSC='.$category_name.' and (MT="Book" or MT="Ebook") ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
            }
        }
        return $ingramSearch;

        // Mage::register('ingram_category', $ingramSearch);
        //echo "<pre>"; print_r($ingramSearch); die;

    }

     public function getCategoryData($category_name,$parent_category,$page = 1,$sort = 'DE|1',$extended = false) {
        $page--;
        $start = $page * 23 + 1;
        $end = ($page + 1) * 23 + 1;
        $date = date('Ymd');
        $category_name = str_replace(" ", "", $category_name);
        $category_name = preg_replace('/[^a-zA-Z0-9\']/', '|', $category_name);

        if(sizeof(explode('|', $category_name)) > 1){
            $exp = explode('|', $category_name);
            $q = array();
            for($i=0;$i<sizeof($exp);$i++){
                if($extended){
                    $q[] = 'BSD3='.$exp[$i];
                }else{
                    $q[] = 'BSU='.$exp[$i];    
                }
                
            }
            $query = implode(' or ', $q);
        }else{
            if($extended){
                $q[] = 'BSD3='.$category_name;    
            }else{
                $query = 'BSU='.$category_name;
            }
        }

        if($parent_category == 'Music' || $category_name == 'Music'){
            $ingramSearch = $this->getApiData('KW='.$category_name.' and MUMT<>"Book"',2,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else if($parent_category == 'Films' || $parent_category == 'Film' || $category_name == 'Films'){
            $ingramSearch = $this->getApiData($query.' and (MT="Video Product" or MT="Film")',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else{
            $ingramSearch = $this->getApiData($query.' and (MT="Book" or MT="Ebook")',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }
        return $ingramSearch;

        // Mage::register('ingram_category', $ingramSearch);
        //echo "<pre>"; print_r($ingramSearch); die;

    }
     public function getCategoryAwards($category_name){
        $next_60_days = date("Ymd", strtotime("+2 month"));
        $date = date("Ymd");
        $sort = "PD|1,DE|1";
        $start = 1;
        $end = 25;
        $query = "";
        for($i=1;$i<=2;$i++){
            $query[] = "AWD=\"$i\"";
        }
        $query = '('.implode(' or ', $query).')';
        $query = "AWD=3";
        // echo $query;die;
        if($category_name == 'Music'){
            $query = "AWD=5";
            $ingramSearch = $this->getApiData($query,2,$start,$end,$sort,'','IMG,IM60,IM90,AWD');
        }else if($category_name == 'Film'){
            $query = "AWD=6";
            $next_60_days = date("Ymd", strtotime("+6 Months"));
            $ingramSearch = $this->getApiData($query .' and and (MT="Video Product" or MT="Film")',1,$start,$end,$sort,'','IMG,IM60,IM90,AWD');
        }else{
            $ingramSearch = $this->getApiData($query .' and (MT="Book" or MT="Ebook")',1,$start,$end,$sort,'','IMG,IM60,IM90,AWD');
        }
        return $ingramSearch;
    }
    public function getCategoryPreOrder($category_name){
        $next_60_days = date("Ymd", strtotime("+2 month"));
        $date = date("Ymd");
        $sort = "PD|0,DE|1";
        $start = 1;
        $end = 25;
        if($category_name == 'Music'){
            $ingramSearch = $this->getApiData('PD > '.$date." and PD < ".$next_60_days,2,$start,$end,$sort,'','IMG,IM60,IM90');
        }else if($category_name == 'Film'){
            $next_60_days = date("Ymd", strtotime("+6 Months"));
            $ingramSearch = $this->getApiData("(MT=\"Video Product\" or MT=\"Film\") and PD>".$date." and PD < ".$next_60_days,1,$start,$end,$sort,'','IMG,IM60,IM90');
        }else{
            $ingramSearch = $this->getApiData("(MT=\"Book\" or MT=\"Ebook\") and PD>".$date." and PD < ".$next_60_days,1,$start,$end,$sort,'','IMG,IM60,IM90');
        }
        return $ingramSearch;
    }
     public function getCategoryNewReleases($category_name){

        $last_30_date = date("Ymd", strtotime("first day of previous month"));
        $date = date("Ymd");
        $sort = "PD|1,DE|1";
        $start = 1;
        $end = 25;
        if($category_name == 'Music'){
            $ingramSearch = $this->getApiData('PD > '.$last_30_date." and PD < ".$date,2,$start,$end,$sort,'','IMG,IM60,IM90');
        }else if($category_name == 'Film'){
            $last_30_date = date("Ymd", strtotime("-6 Months"));
            $ingramSearch = $this->getApiData("(MT=\"Video Product\" or MT=\"Film\") and PD>".$last_30_date." and PD < ".$date,1,$start,$end,$sort,'','IMG,IM60,IM90');
        }else{
            $ingramSearch = $this->getApiData("(MT=\"Book\" or MT=\"Ebook\") and PD>".$last_30_date." and PD < ".$date,1,$start,$end,$sort,'','IMG,IM60,IM90');
        }
        return $ingramSearch;
     }
     public function getCategoryDataBestSeller($category_name) {

        $start = 1;
        $end = 25;

        $sort = "DE|1";

        if($category_name == 'Music'){
            $ingramSearch = $this->getApiData('',2,$start,$end,$sort,'','LOGI,IMG,IM60,IM90');
        }else if($category_name == 'Film'){
            $ingramSearch = $this->getApiData('(MT=Video or MT=Film)',1,$start,$end,$sort,'','LOGI,IMG,IM60,IM90');
        }else{
            $ingramSearch = $this->getApiData('(MT="Book" or MT="Ebook")',1,$start,$end,$sort,'','LOGI,IMG,IM60,IM90');
        }

        return $ingramSearch;
        //echo "<pre>"; print_r($ingramSearch); die;

    }

    public function getProductUrl($_product){
        $uniq = $_product->Basic->ISBN;
        if(!$uniq){
            $uniq = $_product->Basic->EAN;
        }
        return Mage::getUrl('product/index/index') . 'BN/'. $uniq;
    }
    public function getProductName($_product){
        $name = "";
        if($_product->Basic->Title){
            if($_product->Basic->TitleLeadingArticle){
                $name = (string)$_product->Basic->TitleLeadingArticle.' '.$_product->Basic->Title;
            } else{
                $name = (string)$_product->Basic->Title;
            }
        }else{
            $name = (string)$_product->Basic->MusicTitle;
        }
        return $name;
    }
    public function getProductUniq($_product){
        $uniq = $_product->Basic->ISBN;
        if(!$uniq){
            $uniq = $_product->Basic->EAN;
        }
        return $uniq;
    }
    public function getProductImage($_product){
        $image = false;
        $uniq = $this->getProductUniq($_product);
        $date = date('mY');
        $dirpath=Mage::getBaseDir('base')."/media/server/ean/".$date.'/'.$uniq.'/';
        if(file_exists($dirpath)){
            $imgUrl=$dirpath.'cache.png';
             if(file_exists($imgUrl)){
                 $image = Mage::getBaseUrl('media').'server/ean/'.$date.'/'.$uniq.'/cache.png';
             }
        }
        if(!$image)
        {
            if($_product->Basic->Image->IMG187){
                if($_product->Basic->Image->ImageIndicator187 && $_product->Basic->Image->ImageIndicator187 == 'Y')
                    $image = $_product->Basic->Image->IMG187;
            }
            if(!$image && $_product->Basic->Image->IMG94){
                //if($_product->Basic->Image->ImageIndicator94 && $_product->Basic->Image->ImageIndicator94 == 'Y')
                    $image = $_product->Basic->Image->IMG94;
            }
            if(!$image && $_product->Basic->Image->IMG60){
                //if($_product->Basic->Image->ImageIndicator60 && $_product->Basic->Image->ImageIndicator60 == 'Y')
                    $image = $_product->Basic->Image->IMG60;
            }
        }
        if(!$image){
            $image = Mage::getBaseUrl('media') . 'default.png';
        }
        return $image;
    }
    public function getProductAuthor($_product){
        $authors = '';
        foreach ($_product->Basic->Contributor as $val) {
            if ($val->Role == 'Author') {
                if ($authors == '') {
                    $authors .= $val->Name;
                } else {
                    $authors .= ' - ' . $val->Name;
                }
            }
        }
        return $authors;
    }
    public function getProductPrice($_product){
        return round((float)$_product->Ingram->IngramPrice,2);
    }
    public function getProductMRP($_product){
        return round((float)$_product->Basic->PubListPrice,2);
    }
    public function getProductShortDesc($_product){
        return (string)$_product->Ingram->IngramSubject;
    }
    public function getProductDesc($_product){
        return (string)$_product->Basic->Annotation;
    }
    public function getProductPublicationDate($_product){
        $data = false;
        if($_product->Basic->PubDate){
                $date = (string) $_product->Basic->PubDate;
                $date = substr($date, 0,2) . '-' . substr($date, 2);
        }
        return $date;
    }
    public function getProductPublisher($_product){
        $pub = '';
        if($_product->Basic->RecordLabel){
            $pub = (string)$_product->Basic->RecordLabel;
        }else if($_product->Basic->Publisher){
            $pub = (string)$_product->Basic->Publisher;
        }
        return $pub;
    }
    public function getProductAddToCartUrl($_product,$qty=1,$price = false){
         $ean=$_product->Basic->EAN;
         $isbn=$_product->Basic->ISBN;
         $name = $this->getProductName();
         if(!$price)
            $price = $this->getProductPrice();
         $image = $this->getProductImage();
         $params='cart_product_item_id='.$ean.'&proId='.$isbn.'&cart_product_name='.$name.'&cart_product_price='.$price.'&cart_product_image='.$image;
         $url=Mage::getBaseUrl()."product/index/addtocart?".$params; 
         return $url;

    }
    public function isProductInStock($_product){
        $is_in_stock = 0;

        $stock = $_product->Ingram->Stock;
      
        $stock = json_decode(json_encode($stock),true);
      
        foreach($stock as $loc => $data){
           //print_r($data);
            
            if($data['StockStatus'] == 'Y') {
                $is_in_stock = 1;
                break;
            }
        } 

        return $is_in_stock;
    }
    public function getProductFormat($_product){
        if($_product->Basic->Binding){
            if($_product->Basic->ProductFormat)
                return (string)$_product->Basic->Binding.' '.$_product->Basic->ProductFormat;
            else if($_product->Basic->Format){
                return (string)$_product->Basic->Binding.' '.$_product->Basic->Format;
            }
        }else{
            if($_product->Basic->ProductFormat)
                return (string)$_product->Basic->ProductFormat;
            else if($_product->Basic->Format)
                return $_product->Basic->Format;

        }
    }
    public function getProductMedia($_product){
        return (string)$_product->Basic->Media;
    }
    public function hasProductImage($_product){
        if($_product->Ingram->ImageIndicator == 'Y'){
            return true;
        }else{
            return false;
        }
    }

}
