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
            'liveUpdate' => 'Y',
            'dataRequest' => $dataRequest
        );
        $res = $client->SearchRequestEnhanced($params);

        $ingramSearch = new SimpleXMLElement($res->SearchRequestTypes12349EnhancedResult->any);

         //echo '<pre>'; print_r($params);print_r($ingramSearch);die;
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
        $date = date("Ymd");
        $search = array();
        if($type == 'Book'){
            $ingramSearch = $this->getApiData('KW=' . $query.' and (MT="Book" or MT="Ebook") and (PD < ' . $date . ')','1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Author'){
            $ingramSearch = $this->getApiData('CO=' . '*'.$query.'* and (PD < ' . $date . ')','1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Publisher'){
            $ingramSearch = $this->getApiData('KWPU=' . $query.' and (PD < ' . $date . ')','1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Subject'){
            $ingramSearch = $this->getApiData('KWSU=' . $query.' and (PD < ' . $date . ')','1','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Music'){
            $ingramSearch = $this->getApiData('(MUMT="Audio" or MUMT="Compact Disc" or MUMT="CD-ROM") and KW=' . $query.' and (PD < ' . $date . ')','2','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else if($type == 'Film'){
            $ingramSearch = $this->getApiData('KW=' . $query.' and (PD < ' . $date . ')'.' and (MT="Video Product" or MT="Film")','2','1','25','','Y','IMG,IM60,IM90,LOGI');
        }else{
            $ingramSearch = $this->getApiData('KW=' . $query.' and (PD < ' . $date . ')','1','1','25','','Y','IMG,IM60,IM90,LOGI');
            $ingramSearch2 = $this->getApiData('KW=' . $query.' and (PD < ' . $date . ')','2','1','25','','Y','IMG,IM60,IM90,LOGI');
        }
        foreach($ingramSearch->Book as $item){
            $search[] = $item;
        }
        if($ingramSearch2){
            foreach($ingramSearch2->Music as $item){
                $search[] = $item;
            }   
        }
        //$result = Mage::getModel('ingram/product')->setProductData($ingramSearch);
        return $search;
    }

    public function getProductData($id, $type) {
        $ingramSearch = $this->getApiData($id,1,1,2,'','Y','IMG,IM60,IM90,LOGI,PM,BIB,RQ,JF,JB,AWD');
        if((int)$ingramSearch->SearchMessage->MatchingRecs == 0){
            $ingramSearch = $this->getApiData($id,2,1,2,'','Y','IMG,IM60,IM90,LOGI,PM,BIB,RQ,JF,JB,AWD');
        }
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
        
        $date = date("Ymd");

        if($parent_category == 'Music' || $category_name == 'Music'){
            $ingramSearch = $this->getApiData('(MUMT="Audio" or MUMT="Compact Disc" or MUMT="CD-ROM") and KW='.$category_name . ' and (PD < ' . $date.')',2,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else if($parent_category == 'Films' || $parent_category == 'Film' || $category_name == 'Films'){
            $ingramSearch = $this->getApiData('BSC='.$category_name . ' and (PD < ' . $date.')'.' and (MT="Video Product" or MT="Film") ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else{
            if(sizeof(explode(' ', $category_name)) > 1){
                $exp = explode(' ', $category_name);
                $q = array();
                for($i=0;$i<sizeof($exp);$i++){
                    $q[] = 'BSC='.$exp[$i];
                }
                $query = implode(' or ', $q);
                $ingramSearch = $this->getApiData($query. ' and (PD < ' . $date . ')' .' and (MT="Book" or MT="Ebook") ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
            }else{
                $ingramSearch = $this->getApiData('BSC='.$category_name. ' and (PD < ' . $date.')'.' and (MT="Book" or MT="Ebook") ',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
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
        }//(MT="DVD" or MT="Blu-Ray")

        if($parent_category == 'PSVita' || $category_name == 'PSVita'){
            $ingramSearch = $this->getApiData('(IFMT="PlayStation Vita") and PD < '.$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($parent_category == '3DS' || $category_name == '3DS'){
            $ingramSearch = $this->getApiData('(IFMT="Nintendo 3ds") and PD < '.$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($parent_category == 'WiiU' || $category_name == 'WiiU'){
            $ingramSearch = $this->getApiData('(IFMT="Wii Universe" or IFMT="Nintendo Wii") and PD < '.$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($parent_category == 'XboxOne' || $category_name == 'XboxOne'){
            $ingramSearch = $this->getApiData('(IFMT="Xbox") and PD < '.$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($parent_category == 'PlayStation4' || $category_name == 'PlayStation4'){
            $ingramSearch = $this->getApiData('(IFMT="Playstation*") and PD < '.$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($parent_category == 'Xbox360' || $category_name == 'Xbox360'){
            $ingramSearch = $this->getApiData('(IFMT="Xbox 360") and PD < '.$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($parent_category == 'PlayStation3' || $category_name == 'PlayStation3'){
            $ingramSearch = $this->getApiData('(IFMT="PlayStation 3") and PD < '.$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($parent_category == 'PCGames' || $category_name == 'PCGames'){
            $ingramSearch = $this->getApiData('(IFMT="Game") and PD < '.$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($parent_category == 'Music' || $category_name == 'Music'){
            $ingramSearch = $this->getApiData('(MUMT="Audio" or MUMT="Compact Disc" or MUMT="CD-ROM") and KW='.$category_name. ' and (PD < ' . $date.')'.' and MUMT<>"Book"',2,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else if($parent_category == 'Films' || $parent_category == 'Film' || $category_name == 'Films'){
            $ingramSearch = $this->getApiData($query. ' and (PD < ' . $date.')'.' and (IFMT="DVD" or IFMT="Blu-Ray")',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }else{
            $ingramSearch = $this->getApiData($query. ' and (PD < ' . $date.')'.' and (MT="Book" or MT="Ebook")',1,$start,$end,$sort,'Y','LOGI,IMG,IM60,IM90');
        }
        //echo "<pre>";print_r($ingramSearch);die;
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
            $query = "AWD=130";
            $ingramSearch = $this->getApiData($query. ' and (MUMT="Audio" or MUMT="Compact Disc" or MUMT="CD-ROM")  and (PD < ' . $date.')',2,$start,$end,$sort,'','IMG,IM60,IM90,AWD,LOGI');
        }else if($category_name == 'Film'){
            $query = "AWD=101";
            $next_60_days = date("Ymd", strtotime("+6 Months"));
            $ingramSearch = $this->getApiData($query. ' and (PD < ' . $date.')',1,$start,$end,$sort,'','IMG,IM60,IM90,AWD,LOGI');
        }else if($category_name == 'Games'){
            $query = "AWD=23";
            //$next_60_days = date("Ymd", strtotime("+6 Months"));
            $ingramSearch = $this->getApiData($query,1,$start,$end,$sort,'','IMG,IM60,IM90,AWD,LOGI');
        }else{
            $query = "AWD=23";
            $ingramSearch = $this->getApiData($query. ' and (PD < ' . $date.')'.' and (MT="Book" or MT="Ebook")',1,$start,$end,$sort,'','IMG,IM60,IM90,AWD,LOGI');
            
        }
        return $ingramSearch;
    }
    public function getCategoryPreOrder($category_name){
        $next_60_days = date("Ymd", strtotime("+2 month"));
        $next_30_days = date("Ymd", strtotime("+1 month"));
        $date = date("Ymd",strtotime('+7 days'));
        // echo $date;die;
        $sort = "PD|0,DE|1";
        $start = 1;
        $end = 25;
        if($category_name == 'Music'){
            $ingramSearch = $this->getApiData('(MUMT="Audio" or MUMT="Compact Disc" or MUMT="CD-ROM")  and PD > '.$date." and PD < ".$next_60_days,2,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($category_name == 'Film'){
            $next_60_days = date("Ymd", strtotime("+6 Months"));
            $ingramSearch = $this->getApiData("(MT=\"Video Product\" or MT=\"Film\") and PD>".$date." and PD < ".$next_60_days,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($category_name == 'Games'){
            //$next_60_days = date("Ymd", strtotime("+6 Months"));
            $ingramSearch = $this->getApiData('(IFMT="Xbox*" or IFMT="Playstation*" or IFMT="Nintendo*" or IFMT="Wii*" or IFMT="PS Vita")  and PD > '.$date.' and PD < '.$next_30_days,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else{
            $ingramSearch = $this->getApiData("(MT=\"Book\" or MT=\"Ebook\") and PD>".$date." and PD < ".$next_60_days,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }
        // echo '<pre>';print_r($ingramSearch);die;
        return $ingramSearch;
    }
     public function getCategoryNewReleases($category_name){

        $last_30_date = date("Ymd", strtotime("-2 months"));
        $date = date("Ymd");
        $sort = "DE|1";
        $start = 1;
        $end = 25;
        if($category_name == 'Music'){
            $ingramSearch = $this->getApiData('(IS="Pop*" or IS="Rock*" or IS="R & B*" or IS="R&B*") and (MUMT="Audio" or MUMT="Compact Disc" or MUMT="CD-ROM")  and PD > '.$last_30_date." and PD < ".$date,2,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }else if($category_name == 'Film'){
            $last_30_date = date("Ymd", strtotime("-6 Months"));
            $ingramSearch = $this->getApiData("(BSU=\"Suspense\" or \"Thriller\") and (MT=\"Video Product\" or MT=\"Film\") and PD>".$last_30_date." and PD < ".$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
            //echo '<pre>';print_r($ingramSearch);die;
        }else if($category_name == 'Games'){
            //$last_30_date = date("Ymd", strtotime("-6 Months"));
            $ingramSearch = $this->getApiData('(IFMT="Xbox*" or IFMT="Playstation*")',1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
            
        }else{
            $ingramSearch = $this->getApiData("(MT=\"Book\" or MT=\"Ebook\") and PD>".$last_30_date." and PD < ".$date,1,$start,$end,$sort,'','IMG,IM60,IM90,LOGI');
        }
        return $ingramSearch;
     }
     public function getCategoryDataBestSeller($category_name) {

        $start = 1;
        $end = 25;
        $date = date("Ymd");
        $sort = "DE|1";

        if($category_name == 'Music'){
            $ingramSearch = $this->getApiData('(IS="Pop*" or IS="Rock*" or IS="R & B*" or IS="R&B*") and (MUMT="Audio" or MUMT="Compact Disc" or MUMT="CD-ROM") and (PD < ' . $date.')',2,$start,$end,$sort,'','LOGI,IMG,IM60,IM90');
        }else if($category_name == 'Film'){
            $ingramSearch = $this->getApiData('(BSU="Romance") and (MT=Video or MT=Film)',1,$start,$end,$sort,'','LOGI,IMG,IM60,IM90');
            //echo '<pre>';print_r($ingramSearch);die;
        }else if($category_name == 'Games'){
            $ingramSearch = $this->getApiData('(IFMT="Nintendo*" or IFMT="Wii*")',1,$start,$end,$sort,'','LOGI,IMG,IM60,IM90');
        }else{
            $ingramSearch = $this->getApiData('(BSC="FIC*")',1,$start,$end,$sort,'','LOGI,IMG,IM60,IM90');
            //$ingramSearch = $this->getApiData('(MT="Book" or MT="Ebook")',1,$start,$end,$sort,'','LOGI,IMG,IM60,IM90');
        }

        return $ingramSearch;
        //echo "<pre>"; print_r($ingramSearch); die;

    }

    public function getProductUrl($_product){
        $uniq = $_product->Basic->ISBN;
        if(!$uniq){
            $uniq = $_product->Basic->EAN;
        }
        // if($_product->Basic->MusicTitle){
        //     return Mage::getUrl('product/index/index') . 'BN/'. $uniq.'/type/Music';
        // }else{
            return Mage::getUrl('product/index/index') . 'BN/'. $uniq;
        // }
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
    public function hasCacheImage($_product){
        $image = false;
        $uniq = $this->getProductUniq($_product);
        $date = date('mY');
        $dirpath=Mage::getBaseDir('base')."/media/server/ean/".$uniq.'/';
        if(file_exists($dirpath)){
            $imgUrl=$dirpath.'cache.png';
             if(file_exists($imgUrl)){
                 return true;
             }
        }
        return false;
    }
    public function getProductImage($_product){
        $image = false;
        $uniq = $this->getProductUniq($_product);
        $date = date('mY');
        $dirpath=Mage::getBaseDir('base')."/media/server/ean/".$uniq.'/';
        if(file_exists($dirpath)){
            $imgUrl=$dirpath.'cache.png';
             if(file_exists($imgUrl)){
                 $image = Mage::getBaseUrl('media').'server/ean/'.$uniq.'/cache.png';
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
    public function getProductActor($_product){
        $authors = '';
        foreach ($_product->Basic->Contributor as $val) {
            if ($val->Role == 'Actor') {
                if ($authors == '') {
                    $authors .= $val->Name;
                } else {
                    $authors .= ' - ' . $val->Name;
                }
            }
        }
        return $authors;
    }
    public function getProductDirector($_product){
        $authors = '';
        foreach ($_product->Basic->Contributor as $val) {
            if ($val->Role == 'Director') {
                if ($authors == '') {
                    $authors .= $val->Name;
                } else {
                    $authors .= ' - ' . $val->Name;
                }
            }
        }
        return $authors;
    }
    public function getProductAuthor($_product){
        $authors = '';
        $is_music = false;
        if($_product->Basic->MusicTitle){
            $is_music = true;
        }
        foreach ($_product->Basic->Contributor as $val) {
            if(!$is_music){
                if ($val->Role == 'Author') {
                    if ($authors == '') {
                        $authors .= $val->Name;
                    } else {
                        $authors .= ' - ' . $val->Name;
                    }
                }
            }else{
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
         $name = $this->getProductName($_product);
         if(!$price)
            $price = $this->getProductPrice($_product);
         $image = $this->getProductImage($_product);
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
            if($this->hasCacheImage($_product)){
                return true;
            }
            return false;
        }
    }

}
