<?php
class Amazonaccount extends AppModel {
	var $useTable = "sc_election_rule" ;
	
	function getAmazonProductCategory($accountId,$asin = null,$type = null ){
		if( !empty($asin) ){
			$sql = "select sc_amazon_product_category.* ,
			(select count(*) from sc_amazon_product_category_rel
					where sc_amazon_product_category_rel.category_id = sc_amazon_product_category.id
					 and sc_amazon_product_category_rel.asin in (
						select sc_amazon_account_product.asin from sc_amazon_account_product 
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
					and sc_amazon_product_category_rel.asin in (
						select sc_amazon_account_product.asin from sc_amazon_account_product
							where sc_amazon_account_product.account_id = '$accountId' $sqlcause
					)) as TOTAL
              from sc_amazon_product_category where account_id = '$accountId' and account_id is not null " ;
			
			return $this->query($sql) ;
		}
	}
	
	function saveAmazonProductCategory($ids , $asin ){
		//删除所有
		$sql = "delete from sc_amazon_product_category_rel where asin = '$asin'" ;
		$this->query($sql) ; 
		
		foreach( explode(",",$ids) as $id ){
			$sql = "insert into sc_amazon_product_category_rel(asin,category_id) values('$asin','$id')" ;
			$this->query($sql) ; 
		}
	}
	
	function saveCategory($category,$user,$accountId){
		$memo = $category['memo'] ;
		$name = $category['name'] ;
		
		if( isset( $category['id'] )  ){//update
			$sql = "
				UPDATE  sc_amazon_product_category 
					SET
					NAME = '$name' , 
					MEMO = '$memo'
					
					WHERE
					ID = '".$category['id']."'" ;
					
					$this->query($sql) ;
		}else{//insert
			$sql = "
				INSERT INTO sc_amazon_product_category 
					( NAME, 
					PARENT_ID, 
					MEMO,creator,create_time,account_id
					)
					VALUES
					( '$name', 
					'".$category['parentId']."', 
					'$memo','".$user['LOGIN_ID']."',NOW(),'$accountId'
					)" ;
					
					$this->query($sql) ;
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
		
		if( !empty($tt) && count($tt) >= 1){
			if($type == 2){
				$sql = " UPDATE sc_amazon_account_product 
					SET 
					ASIN = '".$data['ASIN']."' ,
					PRICE = '".$data['price']."' ,
					LIST_ID = '".$data['listingId']."' , 
					FULFILLMENT_CHANNEL = '".$data['fulfillment']."' , 
					PADDENT_QUANTITY = '".$data['pendingQuantity']."' , 
					QUANTITY = '".$data['quantity']."',
					ITEM_CONDITION = '".$data['itemCondition']."'
					WHERE
					account_id = '".$data['accountId']."' and SKU = '".$data['SKU']."' " ;
			}else{
				$sql = " UPDATE sc_amazon_account_product 
					SET 
					ASIN = '".$data['ASIN']."' ,
					PRICE = '".$data['price']."' ,
					QUANTITY = '".$data['quantity']."'
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
						QUANTITY
						)
						VALUES
						(
						'".$data['accountId']."', 
						'".$data['ASIN']."', 
						'".$data['price']."', 
						NOW(),  
						'".$data['SKU']."', 
						'".$data['listingId']."', 
						'".$data['fulfillment']."', 
						'".$data['pendingQuantity']."', 
						'".$data['quantity']."'
						)
					 " ;
				$this->query($sql) ;
		}
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
	
	function getAccountProducts($accountId,$categoryId = null ){
		$array = null ;
		if(empty($categoryId)){
			$sql = "SELECT distinct sc_amazon_account_product.ASIN FROM sc_amazon_account_product 
			where account_id = '$accountId'";
			$array = $this->query($sql);
		}else{
			$sql = "SELECT DISTINCT sc_amazon_account_product.ASIN FROM sc_amazon_product_category ,
						sc_amazon_product_category_rel AS sc_amazon_account_product
						WHERE sc_amazon_account_product.category_id = sc_amazon_product_category.id 
				AND sc_amazon_product_category.account_id = '$accountId' AND sc_amazon_product_category.id='$categoryId'";
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
	
}