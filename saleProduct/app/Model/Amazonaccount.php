<?php
class Amazonaccount extends AppModel {
	var $useTable = "sc_election_rule" ;
	
	function getAmazonProductCategoryBySKU($accountId,$sku ){
			$sql = "select sc_amazon_product_category.* 
			 from sc_amazon_product_category ,sc_amazon_product_category_rel
				where sc_amazon_product_category_rel.account_id = '$accountId'
					and sc_amazon_product_category_rel.category_id = sc_amazon_product_category.id
					and sc_amazon_product_category_rel.sku = '$sku' limit 0,1" ;
			
			return $this->query($sql) ;
	}
	
	function getAmazonProductCategory($accountId,$asin = null,$type = null,$sku = null ){
		if( !empty($sku) ){
			$sql = "select sc_amazon_product_category.* ,
			(select count(*) from sc_amazon_product_category_rel
					where sc_amazon_product_category_rel.category_id = sc_amazon_product_category.id
					 and sc_amazon_product_category_rel.sku in (
						select sc_amazon_account_product.sku from
								sc_amazon_account_product where account_id = '$accountId' and status = 'Y'
					) and
			sc_amazon_product_category_rel.sku = '$sku' ) as selected
			 from sc_amazon_product_category where account_id = '$accountId' and account_id is not null " ;
			
			return $this->query($sql) ;
		}else if( !empty($asin) ){
			$sql = "select sc_amazon_product_category.* ,
			(select count(*) from sc_amazon_product_category_rel
					where sc_amazon_product_category_rel.category_id = sc_amazon_product_category.id
					 and sc_amazon_product_category_rel.sku in (
						select sc_amazon_account_product.sku from
								sc_amazon_account_product where account_id = '$accountId' and status = 'Y'
					) and
			sc_amazon_product_category_rel.asin = '$asin' ) as selected
			 from sc_amazon_product_category where account_id = '$accountId' and account_id is not null " ;
			
			return $this->query($sql) ;
		}else{
			$sqlcause = "" ;
			if( !empty($type) ){
				$sqlcause = " and ( sc_amazon_account_product.feed_$type is not null and sc_amazon_account_product.feed_$type <> '') " ;
			}
			
			$sql = "select sc_amazon_product_category.*,
              (select count(*) from sc_amazon_product_category_rel
					where sc_amazon_product_category_rel.category_id = sc_amazon_product_category.id
					and sc_amazon_product_category_rel.sku in (
						select sc_amazon_account_product.sku from sc_amazon_account_product
							where sc_amazon_account_product.account_id = '$accountId' and status='Y' $sqlcause
					)) as TOTAL
              from sc_amazon_product_category where account_id = '$accountId' and account_id is not null " ;
			
			return $this->query($sql) ;
		}
	}
	
	function saveAmazonProductCategory($ids , $sku ,$accountId ){
		//删除所有
		$sql = "delete from sc_amazon_product_category_rel where sku = '$sku' and category_id  in (
			select id from sc_amazon_product_category where account_id = '$accountId'
		)" ;
		$this->query($sql) ; 
		
		foreach( explode(",",$ids) as $id ){
			$sql = "insert into sc_amazon_product_category_rel(sku,category_id,account_id) values('$sku','$id','$accountId')" ;
			$this->query($sql) ; 
		}
	}
	
	/**
	 * 给分类添加产品，多个差评
	 */
	function saveAmazonProductsCategory($params ){
		
		$skus = $params['checked_skus'] ;
		$unskus = $params['unchecked_skus'] ;
		$categoryId = $params['categoryId'] ;
		$accountId = $params['accountId'] ;
		
		foreach( explode(",",$skus) as $sku ){
			try{
				$sql = "insert into sc_amazon_product_category_rel(sku,category_id,account_id) values('$sku','$categoryId','$accountId')" ;
				$this->query($sql) ; 
			}catch(Exception $e){}
		}
		
		foreach( explode(",",$unskus) as $sku ){
			$sql = "delete from sc_amazon_product_category_rel where sku='$sku' and account_id = '$accountId' and category_id = '$categoryId'" ;
			$this->query($sql) ;
		}
	}
	
	/**
	 * 获取分类策略  PRICE_STRATERY
	 */
	function getAmazonProductCategoryStratery($productCategory){
		if(empty($productCategory) ){//没有设置分类
			//获取上级策略
			return null ;
		}else{
			$str = $productCategory['PRICE_STRATERY'] ;
			if(empty($str)){
				$parentCategory = $this->getAmazonProductParentCategory($productCategory) ;
				if($productCategory == null){
					return $str ;
				}
				return $this->getAmazonProductCategoryStratery($parentCategory) ;	
			}
			return $str ;
		}
	}
	
	function getAmazonProductParentCategory($productCategory){
		$parentId = $productCategory['PARENT_ID'] ;
		if( empty($parentId) ){
			return null ;
		}else{
			$sql = "select * from sc_amazon_product_category where id = '$parentId' " ;
			$result = $this->query($sql) ; 
			$result = $result[0]['sc_amazon_product_category'] ;
			return $result ;
		}
	}
	
	function saveCategory($category,$user,$accountId){
		$memo = $this->getValue( $category, 'memo') ;
		$name = $this->getValue( $category,'name') ;
		$gatherLevel = $this->getValue( $category,'gatherLevel');
		$priceStratery = $this->getValue( $category,'priceStratery') ;
		$id = '' ;
		if( isset( $category['id'] )  ){//update
			$id = $category['id'] ;
			$sql = "
				UPDATE  sc_amazon_product_category 
					SET
					NAME = '$name' , 
					MEMO = '$memo' ,
					PRICE_STRATERY = '$priceStratery',
					GATHER_LEVEL = '$gatherLevel',
					PARENT_ID = '".$category['parentId']."'
					WHERE
					ID = '".$category['id']."'" ;
					$this->query($sql) ;
		}else{//insert
			$sql = "select max(id) as m from sc_amazon_product_category " ;
			$result = $this->query($sql) ;
			$id = 1 ;
			if(isset($result) &&  !empty($result)){
				$id = $result[0][0]['m'] +1 ;
			}
		
			$sql = "
				INSERT INTO sc_amazon_product_category 
					(ID, NAME, 
					PARENT_ID, 
					MEMO,creator,create_time,account_id,gather_level,PRICE_STRATERY
					)
					VALUES
					('$id',  '$name', 
					'".$category['parentId']."', 
					'$memo','".$user['LOGIN_ID']."',NOW(),'$accountId','$gatherLevel','$priceStratery'
					)" ;

				$this->query($sql) ;
		}
		
		//save relation
		$warning = $this->getValue($category , 'warning') ;
		$sql = "delete from sc_warning_category where category_id = '$id'" ;
		$this->query($sql) ;
		
		if( !empty($warning) ){
			$warnings = explode(",",$warning) ;
			foreach($warnings as $item){
				$sql = "insert into sc_warning_category(warning_id , category_id) values( '$item','$id' )" ;
				$this->query($sql) ;
			}
		}
	}

	public function saveAccount($data,$user){
		
		$loginId = $user["LOGIN_ID"] ;
		if( empty($data["ID"]) ){
			$sql = "
					INSERT INTO sc_amazon_account 
						(
						NAME, 
						URL, 
						CODE,
						DOMAIN,  
						CREATOR, 
						CREATE_TIME, 
						AWS_ACCESS_KEY_ID, 
						AWS_SECRET_ACCESS_KEY, 
						APPLICATION_NAME, 
						APPLICATION_VERSION, 
						MERCHANT_ID, 
						MARKETPLACE_ID, 
						MERCHANT_IDENTIFIER
						)
						VALUES
						(
						'".$data['NAME']."', 
						'".$data['URL']."', 
						'".$data['CODE']."', 
						'".$data['DOMAIN']."', 
						'".$loginId."', 
						NOW(), 
						'".$data['AWS_ACCESS_KEY_ID']."', 
						'".$data['AWS_SECRET_ACCESS_KEY']."', 
						'".$data['APPLICATION_NAME']."', 
						'".$data['APPLICATION_VERSION']."', 
						'".$data['MERCHANT_ID']."', 
						'".$data['MARKETPLACE_ID']."', 
						'".$data['MERCHANT_IDENTIFIER']."'
						);
					" ;
			$this->query($sql) ;
		}else{
			$sql = "UPDATE  sc_amazon_account 
				SET
				NAME = '".$data['NAME']."' , 
				URL = '".$data['URL']."' , 
				CODE = '".$data['CODE']."' ,
				DOMAIN = '".$data['DOMAIN']."' ,  
				AWS_ACCESS_KEY_ID = '".$data['AWS_ACCESS_KEY_ID']."' , 
				AWS_SECRET_ACCESS_KEY = '".$data['AWS_SECRET_ACCESS_KEY']."' , 
				APPLICATION_NAME = '".$data['APPLICATION_NAME']."' , 
				APPLICATION_VERSION = '".$data['APPLICATION_VERSION']."' , 
				MERCHANT_ID = '".$data['MERCHANT_ID']."' , 
				MERCHANT_IDENTIFIER = '".$data['MERCHANT_IDENTIFIER']."' , 
				MARKETPLACE_ID = '".$data['MARKETPLACE_ID']."'
				
				WHERE
				ID = '".$data['ID']."' " ;
				$this->query($sql) ;
		}
	}
	
	public function saveAccountProduct($data,$user){
		$loginId = $user["LOGIN_ID"] ;
		$sql = "UPDATE  sc_amazon_account_product 
			SET
			MEMO = '".$data['MEMO']."' , 
			STRATEGY = '".$data['STRATEGY']."' , 
			EXEC_PRICE = '".$data['EXEC_PRICE']."' 
			WHERE
			ID = '".$data['ID']."' " ;
			$this->query($sql) ;
	}
	
	public function saveAccountProductFeed($data , $user){
		$type = $data['type'] ;
		if($type == 'FEED_QUANTITY'){
			$this->saveAccountProductFeedQuantity($data) ;
		}else if($type == 'FEED_PRICE'){
			$this->saveAccountProductFeedPrice($data) ;
		}
	}
	
	public function saveAccountProductFeedPrice($data){
		
		$sql = " UPDATE sc_amazon_account_product 
					SET 
					FEED_PRICE = '".$data['value']."'
					WHERE
					account_id = '".$data['accountId']."' and SKU = '".$data['sku']."' " ;
		$this->query($sql) ;
	}
	
	public function saveAccountProductFeedQuantity($data){
		
		$sql = " UPDATE sc_amazon_account_product 
					SET 
					FEED_QUANTITY = '".$data['value']."'
					WHERE
					account_id = '".$data['accountId']."' and SKU = '".$data['sku']."' " ;
		$this->query($sql) ;
	}
	
	public function saveAccountProductByAsyn($data,$type){
		
		$sql = "select * from sc_amazon_account_product where sku = '".$data['SKU']."' and account_id = '".$data['accountId']."'" ;
		$tt = $this->query($sql) ;
		
		$listingId = "" ;
		$fulfillment = "" ;
		$pendingQuantity = "" ;
		
		if(isset($data['listingId']))
			$listingId = $data['listingId'] ;
		if(isset($data['fulfillment']))
			$fulfillment = $data['fulfillment'] ;
		if(isset($data['pendingQuantity']))
			$pendingQuantity = $data['pendingQuantity'] ;
			
		if(empty($data['ASIN']))
			return ;
		
		if( !empty($tt) && count($tt) >= 1){
			if($type == 2){
				$sql = " UPDATE sc_amazon_account_product 
					SET 
					ASIN = '".$data['ASIN']."' ,
					PRICE = '".$data['price']."' ,
					LIST_ID = '$listingId' , 
					FULFILLMENT_CHANNEL = '$fulfillment' , 
					PADDENT_QUANTITY = '$pendingQuantity' , 
					QUANTITY = '".$data['quantity']."',
					ITEM_CONDITION = '".$data['itemCondition']."',
					STATUS = 'Y',
					ASYN_STATUS = 'Y'
					WHERE
					account_id = '".$data['accountId']."' and SKU = '".$data['SKU']."' " ;
			}else{
				$sql = " UPDATE sc_amazon_account_product 
					SET 
					ASIN = '".$data['ASIN']."' ,
					PRICE = '".$data['price']."' ,
					QUANTITY = '".$data['quantity']."',
					STATUS = 'Y',
					ASYN_STATUS = 'Y'
					WHERE
					account_id = '".$data['accountId']."' and SKU = '".$data['SKU']."' " ;
			}
			
			$this->query($sql) ;
		}else{
			$sql = "INSERT INTO sc_amazon_account_product 
						(
						ACCOUNT_ID, 
						ASIN, 
						PRICE, 
						CREATE_TIME, 
						SKU, 
						LIST_ID, 
						FULFILLMENT_CHANNEL, 
						PADDENT_QUANTITY, 
						QUANTITY,
						STATUS,
						ASYN_STATUS
						)
						VALUES
						(
						'".$data['accountId']."', 
						'".$data['ASIN']."', 
						'".$data['price']."', 
						NOW(),  
						'".$data['SKU']."', 
						'$listingId', 
						'$fulfillment', 
						'$pendingQuantity',
						'".$data['quantity']."', 
						'Y','Y'
						)
					 " ;
				$this->query($sql) ;
		}
	}
	
	function asynProductStatusStart($accountId,$reportType){
		//clear status
		$sql = "update sc_amazon_account_product set asyn_status = '' where account_id = '$accountId'" ;
		$this->query($sql) ;
	}
	
	function asynProductStatusEnd($accountId,$reportType){
		$sql = "select count(*) c from sc_amazon_account_product where account_id= '$accountId' and asyn_status = 'Y'" ;
		$result = $this->query($sql) ;
		if( $result[0][0]['c'] > 0 ){
			$sql = "update sc_amazon_account_product set status = 'deleted' where asyn_status != 'Y' or asyn_status is null or asyn_status = ''" ;
			$this->query($sql) ;
			$sql = "update sc_amazon_account_product set status = 'Y' where asyn_status = 'Y'" ;
			$this->query($sql) ;
		}
		//|| status is null || status=''
	}
	
	function saveConfigItem($data,$user){
		
		$loginId = $user["LOGIN_ID"] ;
		
		if( empty($data["ID"]) ){
			$sql = "
				INSERT INTO  sc_amazon_config 
					(
					NAME, 
					LABEL,
					VALUE, 
					TYPE, 
					MEMO
					)
					VALUES
					(
					'".$data['NAME']."', 
					'".$data['LABEL']."',
					'".$data['VALUE']."', 
					'".$data['TYPE']."', 
					'".$data['MEMO']."'
					);
				" ;
			$this->query($sql) ;
		}else{
			$sql = "UPDATE sc_amazon_config 
				SET
				NAME = '".$data['NAME']."' , 
				LABEL = '".$data['LABEL']."' , 
				VALUE = '".$data['VALUE']."' , 
				TYPE = '".$data['TYPE']."' , 
				MEMO = '".$data['MEMO']."'
				
				WHERE
				ID = '".$data['ID']."' " ;
			$this->query($sql) ;
		}
		
	}
	
	function getAccount($id,$categoryId=null){
		if(empty($categoryId)){
			$domain = $_SERVER['SERVER_NAME'] ;
			$sql = "SELECT * FROM sc_amazon_account where id = '$id' and domain = '$domain'";
			$array = $this->query($sql);
			return $array ;
		}else{
			$sql = "SELECT sc_amazon_account.* FROM sc_amazon_product_category as sc_amazon_account
				where sc_amazon_account.id = '$categoryId' and sc_amazon_account.account_id = '$id'";
			$array = $this->query($sql);
			return $array ;
		}
	}
	
	function getAccountAsyn($accountId,$reportType){
		$sql = "SELECT * FROM sc_amazon_account_asyn where account_id = '$accountId'
		and report_type = '$reportType'
		and ( status is null or status = '') ";
		$array = $this->query($sql);
		return $array ;
	}
	
	function saveAccountAsyn($accountId ,$request , $user){
		$sql = "INSERT INTO  sc_amazon_account_asyn 
				( 
				ACCOUNT_ID, 
				REPORT_REQUEST_ID,
				CREATOR, 
				CREATE_TIME, 
				REPORT_TYPE
				)
				VALUES
				( 
				$accountId , 
				'".$request['reportRequestId']."',
				'".$user["LOGIN_ID"]."', 
				NOW(), 
				'".$request['reportType']."'
				)" ;
		 $array = $this->query($sql);
	}
	
	function updateAccountAsyn2($accountId ,$request , $user){
		$sql = "UPDATE sc_amazon_account_asyn 
					SET 
					REPORT_ID = '".$request['reportId']."'
					WHERE
					ACCOUNT_ID = '$accountId' and REPORT_TYPE = '".$request['reportType']."' and  ( status is null or status = '')" ;
		 $array = $this->query($sql);
	}
	
	function updateAccountAsyn3($accountId ,$request , $user=null){
		$sql = "UPDATE sc_amazon_account_asyn 
					SET 
					STATUS = 'complete'
					WHERE
					ACCOUNT_ID = '$accountId' and REPORT_TYPE = '".$request['reportType']."' and  ( status is null or status = '')" ;
		 $array = $this->query($sql);
	}
	
	function getAccountsFront(){
		$domain = $_SERVER['SERVER_NAME'] ;
		$sql = "SELECT ID,NAME FROM sc_amazon_account where domain='$domain'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getAccounts(){
		$domain = $_SERVER['SERVER_NAME'] ;
		$sql = "SELECT * FROM sc_amazon_account where domain='$domain'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getAllAccounts(){
		$sql = "SELECT * FROM sc_amazon_account";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getAccountProduct($id){
		$sql = "SELECT sc_amazon_account_product.* , sc_product.TITLE,sc_product.ASIN
			FROM sc_amazon_account_product , sc_product where sc_amazon_account_product.asin = sc_product.asin
			and sc_amazon_account_product.id = '$id'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function getAccountProductsForLevel($accountId,$level){
		
		$where = " AND sc_amazon_product_category.gather_level='$level' " ;
		if($level == '-'){
			$where = " AND sc_amazon_product_category.gather_level not in ('A','B','C','D') " ;
		}
		$sql = "SELECT DISTINCT sc_amazon_account_product.ASIN,sc_amazon_account_product.ITEM_CONDITION
						FROM sc_amazon_product_category ,
						sc_amazon_product_category_rel ,
						sc_amazon_account_product
						WHERE
				sc_amazon_account_product.sku = sc_amazon_product_category_rel.sku
				and sc_amazon_account_product.account_id = '$accountId'
				and sc_amazon_product_category_rel.category_id = sc_amazon_product_category.id
				and sc_amazon_account_product.status = 'Y'
				AND sc_amazon_product_category.account_id = '$accountId' $where ";
			
		$array = $this->query($sql);
	
		return $array ;
	}
	
	function getAccountProductsForLevelSale($accountId,$level){
		$where = " AND sc_amazon_product_category.gather_level='$level' " ;
		if($level == '-'){
			$where = " AND (
						sc_amazon_product_category.gather_level not in ('A','B','C','D')
						or sc_amazon_product_category.gather_level is null
				)" ;
		}
		
		$sql = "SELECT sc_amazon_account_product.*,sc_amazon_product_category.*
						FROM sc_amazon_product_category ,
						sc_amazon_product_category_rel ,
						sc_amazon_account_product
						WHERE sc_amazon_product_category_rel.category_id = sc_amazon_product_category.id  
							and sc_amazon_account_product.sku = sc_amazon_product_category_rel.sku
                            and sc_amazon_account_product.account_id = '$accountId'
				and sc_amazon_account_product.status = 'Y'
				AND sc_amazon_product_category.account_id = '$accountId' $where ";
		$array = $this->query($sql);
		
		//print_r($array) ; 
		return $array ;
	}
	
	function getAccountProductsForCategorySale($accountId,$categoryId){
		$where = " AND sc_amazon_product_category.id='$categoryId' " ;
		
		$sql = "SELECT sc_amazon_account_product.*,sc_amazon_product_category.*
						FROM sc_amazon_product_category ,
						sc_amazon_product_category_rel ,
						sc_amazon_account_product
						WHERE sc_amazon_product_category_rel.category_id = sc_amazon_product_category.id  
							and sc_amazon_account_product.sku = sc_amazon_product_category_rel.sku
                            and sc_amazon_account_product.account_id = '$accountId'
				and sc_amazon_account_product.status = 'Y'
				AND sc_amazon_product_category.account_id = '$accountId' $where ";
		$array = $this->query($sql);
		
		//print_r($array) ; 
		return $array ;
	}
	
	function getAccountProducts($accountId,$categoryId = null ){
		$array = null ;
		if(empty($categoryId)){ 
			$sql = "SELECT distinct sc_amazon_account_product.ASIN,sc_amazon_account_product.ITEM_CONDITION
				FROM sc_amazon_account_product 
			where account_id = '$accountId' and status = 'Y' 
						 and ( cast(quantity as signed) > 0 or fulfillment_channel like 'AMAZON%' )";
			$array = $this->query($sql);
		}else{
			$categorys = $this->getRecursionWithMe('sc_amazon_product_category','ID','PARENT_ID',$categoryId) ;
			$catIds = array() ;
			foreach($categorys as $category){
				$id = $category['ID'] ;
				$catIds[] = $id ;
			}
			
			$ins = join("','",$catIds) ;
			$sql = "SELECT DISTINCT sc_amazon_account_product.ASIN,sc_amazon_account_product.ITEM_CONDITION
						FROM sc_amazon_product_category ,
						sc_amazon_product_category_rel ,
						sc_amazon_account_product
						WHERE sc_amazon_account_product.sku = sc_amazon_product_category_rel.sku
						 and sc_amazon_product_category_rel.category_id = sc_amazon_product_category.id  
						 and sc_amazon_account_product.status = 'Y'
						 and ( cast(quantity as signed) > 0 or fulfillment_channel like 'AMAZON%' )
				AND sc_amazon_product_category.account_id = '$accountId' AND sc_amazon_product_category.id in ('$ins')";
			$array = $this->query($sql);
		}
		
		
		return $array ;
	}
	
	function getAccountProductsForGather($accountId){
		$sql = "SELECT distinct sc_amazon_account_product.ASIN
			FROM sc_amazon_account_product 
			where account_id = '$accountId'";
		$array = $this->query($sql);
		return $array ;
	}
	
	function updateAccountGatherStatus($accountId,$key , $val,$categoryId=null){
		$tableName = "" ;
		$id = "" ;
		if(empty($categoryId)){
			$id = $accountId ;
			$tableName = "sc_amazon_account" ;
		}else{
			$id = $categoryId ;
			$tableName = "sc_amazon_product_category" ;
		}
		
		if( empty($val) ){
			$gtimeKey = $key.'_time' ;
			$sql = "update $tableName set $key = '' ,$gtimeKey = NOW()  where id = '$id'";
			$this->query($sql);
		}else{
			$sql = "update $tableName set $key = '$val' where id = '$id'";
			$this->query($sql);
		}
	}
	
	//feed
	
	function saveAccountFeed($data){
		$sql = "INSERT INTO sc_amazon_account_feed 
			(
			FEEDSUBMISSION_ID, 
			CREATE_TIME, 
			CREATOR, 
			TYPE, 
			ACCOUNT_ID, 
			STATUS, 
			MESSAGE, 
			FEED
			)
			VALUES
			(
			'".$data['feedsubmissionId']."', 
			now(), 
			'".$data['loginId']."', 
			'".$data['type']."', 
			'".$data['accountId']."', 
			'".$data['status']."', 
			'".$data['message']."', 
			'".$data['feed']."'
			) " ;
		$this->query($sql);
	}
	
	/**
	 * 	"feedId"=>$feedId,
	  		   	"status"=>$Status,
	  		   	"sucess"=>$sucess,
	  		   	"error"=>$error,
	  		   	"message"=>$string
	 */
	function updateAccountProductFeed($array){
		$sql = "UPDATE  sc_amazon_account_feed 
			SET
			STATUS = '".$array['status']."' , 
			MESSAGE = '".$array['message']."' , 
			SUCCESS_NUM = '".$array['sucess']."' , 
			FAIL_NUM = '".$array['error']."' 
			
			WHERE
			FEEDSUBMISSION_ID = '".$array['feedId']."' " ;
		$this->query($sql);	
	}
	
	function listAccountUpdatableProductForPrice($accountId){
		$sql = "select * from sc_amazon_account_product where account_id = '$accountId' and feed_price is not null and feed_price <> '' " ;	
		return $this->query($sql);	 
	}
	
	function listAccountUpdatableProductForQuantity($accountId){
		$sql = "select * from sc_amazon_account_product where account_id = '$accountId' and feed_quantity is not null and feed_quantity <> '' " ;	
		return $this->query($sql);	
	}
	
	////////////////////////////////////////////////
		
	function getQuantityFeed($MerchantIdentifier , $products){
		////////////////////////////////////////////////////////////////////////////		
$Feed = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
	<Header>
		<DocumentVersion>1.01</DocumentVersion>
		<MerchantIdentifier>$MerchantIdentifier</MerchantIdentifier>
	</Header>
	<MessageType>Inventory</MessageType>
EOD;
////////////////////////////////////////////////////////////////////////////
		
		$index = 0 ;
		
		for( $i = 0 ;$i < count($products) ;$i++  ){
			$index++ ;
			$product = $products[$i] ;

			$sku = $product["SKU"] ;
			$quantity = $product["FEED_QUANTITY"] ;
	   		
////////////////////////////////////////////////////////////////////////////
$Feed .= <<<EOD
	<Message>
		<MessageID>$index</MessageID>
		<Inventory>
			<SKU>$sku</SKU>
			<Quantity>$quantity</Quantity>
		</Inventory>
	</Message>
EOD;
	
		}
$Feed .= <<<EOD
</AmazonEnvelope>
EOD;
		return $Feed ;
	}	
		
	/**
	 $feed = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
	<Header>
		<DocumentVersion>1.01</DocumentVersion>
		<MerchantIdentifier>M_CYBERKIN_107805233</MerchantIdentifier>
	</Header>
	<MessageType>Price</MessageType>
	<Message>
		<MessageID>1</MessageID>
		<Price>
			<SKU>B003D8GAA0</SKU>
			<StandardPrice currency="USD">16.01</StandardPrice>
		</Price>
	</Message>
</AmazonEnvelope>
EOD;
	 */
	function getPriceFeed($MerchantIdentifier , $products){
////////////////////////////////////////////////////////////////////////////		
$Feed = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
	<Header>
		<DocumentVersion>1.01</DocumentVersion>
		<MerchantIdentifier>$MerchantIdentifier</MerchantIdentifier>
	</Header>
	<MessageType>Price</MessageType>
EOD;
////////////////////////////////////////////////////////////////////////////
		
		$index = 0 ;
		
		for( $i = 0 ;$i < count($products) ;$i++  ){
			$index++ ;
			$product = $products[$i] ;

			$sku = $product["SKU"] ;
			$price = $product["FEED_PRICE"] ;
		   		
/*
<StandardPrice currency="USD">80.00</StandardPrice>
<Sale>
<StartDate>2009-05-15T00:00:01-08:00</StartDate>
<EndDate>2009-05-17T00:00:01-08:00</EndDate>
<SalePrice currency="USD">77.00</SalePrice>
</Sale>*/		   		
////////////////////////////////////////////////////////////////////////////
$Feed .= <<<EOD
	<Message>
		<MessageID>$index</MessageID>
		<Price>
			<SKU>$sku</SKU>
			<StandardPrice currency="USD">$price</StandardPrice>
		</Price>
	</Message>
EOD;
////////////////////////////////////////////////////////////////////////////

		}
////////////////////////////////////////////////////////////////////////////
$Feed .= <<<EOD
</AmazonEnvelope>
EOD;
		return $Feed ;
	}	
	
}