<?php
class Product extends AppModel {
	var $useTable = 'sc_election_rule';
	
	function getProductCategory($asin = null ){
		if( !empty($asin) ){
			return	$this->exeSql("sql_getProductCategoryByAsin", array('asin'=>$asin)) ;
		}else{
			return	$this->exeSql("sql_getProductCategoryByDefault", array()) ;
		}
		
	}
	
	function setSupplierFlag($params){
		$sql="sql_pdev_setSupplierFlag";
		$this->exeSql($sql, $params) ;
	}
	
	function saveProductCategory($ids , $asin ){
		//删除所有
		$sql = "delete from sc_product_category_rel where asin = '$asin'" ;
		$this->query($sql) ; 
		
		foreach( explode(",",$ids) as $id ){
			$sql = "insert into sc_product_category_rel(asin,category_id) values('$asin','$id')" ;
			$this->query($sql) ; 
		}
	}
	
	function saveCategory($category){
		$memo = $category['memo'] ;
		$name = $category['name'] ;
		
		if( isset( $category['id'] )  ){//update
			$this->exeSql("sql_product_category_update", $category) ;
			/*$sql = "
				UPDATE  sc_product_category 
					SET
					NAME = '$name' , 
					MEMO = '$memo',
					PURCHASE_CHARGER='".$category."'
					
					WHERE
					ID = '".$category['id']."'" ;
					
					$this->query($sql) ;*/
		}else{//insert
			$this->exeSql("sql_product_category_insert", $category) ;
			/*$sql = "
				INSERT INTO sc_product_category 
					( NAME, 
					PARENT_ID, 
					MEMO,creator,create_time
					)
					VALUES
					( '$name', 
					'".$category['parentId']."', 
					'$memo','".$category['loginId']."',NOW()
					)" ;
					
					$this->query($sql) ;*/
		}
	}

	function saveConfig($data=null){
		$id = $data["id"] ;
		$name = $data["name"] ;
		$scripts = $data["scripts"] ;
		
		//$params =   array($name,$scripts) ;
		$params[0] = $name ;
		$params[1] = $scripts ;

		if( $id !=null ){
			$this->query("update sc_election_rule set name ='".$name."',scripts='".$scripts."' where id = '".$id."'") ;
		}else{
			$this->query("insert into sc_election_rule(name,scripts) values('".$name."','".$scripts."')") ;
		}
	}

	function deleteConfig($id){
		$this->query("update sc_election_rule set status = 'disabled' where id = '".$id."'") ;
	}
	
	function getProductDetails($asin){
		$sql = "SELECT * FROM sc_product
			LEFT JOIN sc_sale_competition  ON sc_sale_competition.asin = sc_product.asin
			LEFT JOIN sc_sale_potential  ON sc_sale_potential.asin = sc_product.asin
            LEFT JOIN sc_sale_fba  ON sc_sale_fba.asin = sc_product.asin
			WHERE sc_product.asin = '".$asin."'" ;
			
		return $this->query($sql) ;
	}
	
	function getProductImages($asin){
		$sql = "select * from sc_product_imgs
			WHERE asin = '".$asin."'" ;
			
		return $this->query($sql) ;
	}
	
	function getProductCompetitionDetails($asin){
		$sql = "select * from sc_sale_competition_details
			WHERE asin = '".$asin."'" ;
			
		return $this->query($sql) ;
	}
	function getProductFlowDetails($asin){
		$sql = "select * from sc_product_flow_details
			WHERE asin = '".$asin."'" ;
			
		return $this->query($sql) ;
	}
	
	
	function getProductFbaDetails($asin){
		$sql = "select * from sc_sale_fba_details
			WHERE asin = '".$asin."'" ;
			
		return $this->query($sql) ;
	}
	
	function getProductStrategy(){
		$sql = "select * from sc_config where type = 'strategy' ";
			
		return $this->query($sql) ;
	}
	
	function getProductSupplier($asin){
		$sql = "select sc_supplier.*,sc_product_supplier.* from sc_supplier , sc_product_supplier where 
			sc_product_supplier.supplier_id = sc_supplier.id and sc_product_supplier.asin = '$asin' and sc_product_supplier.status <> 'invalid'
		";
			
		return $this->query($sql) ;
	}
	
	function getProductSupplierById($asin,$supplierId){
		$sql = "select sc_supplier.*,sc_product_supplier.* from sc_supplier , sc_product_supplier where 
			sc_product_supplier.supplier_id = sc_supplier.id and sc_product_supplier.asin = '$asin' and sc_supplier.id = '$supplierId'
		";
	
		return $this->query($sql) ;
	}
	
	
	function getProductRankingDetails($asin){
		$sql = "select * from sc_sale_potential_ranking
			WHERE asin = '".$asin."'" ;
			
		return $this->query($sql) ;
	}
	
	function updateProductComment($asin , $comment, $strategy){
		$comment = str_replace("'","‘",$comment) ;
		$sql = "update sc_product set comment = '$comment',strategy='$strategy' where asin = '$asin'" ;
		$this->query($sql) ;
	}
	
	function enableBlackProduct($asin){
		$sql = "delete from sc_product_black where asin = '$asin'" ;
		$this->query($sql) ;
		
		$sql = "update sc_product_filter_details set status = '2' where asin = '$asin'" ;
		$this->query($sql) ;
	}
	
	function getUploadGroup(){
		$sql = "select sc_upload_group.*
			,( select count(*) from sc_upload where sc_upload.group_id = sc_upload_group.id ) as TOTAL
			from sc_upload_group " ;
			
		return $this->query($sql) ;
	}
	
	function saveUploadGroup($group,$user){
		$memo = $group['memo'] ;
		$name = $group['name'] ;
		
		if( isset( $group['id'] )  ){//update
			$sql = "
				UPDATE sc_upload_group 
					SET
					NAME = '$name' , 
					MEMO = '$memo'
					
					WHERE
					ID = '".$group['id']."'" ;
					
					$this->query($sql) ;
		}else{//insert
			$sql = "
				INSERT INTO sc_upload_group 
					( NAME, 
					PARENT_ID, 
					MEMO,creator,create_time
					)
					VALUES
					( '$name', 
					'".$group['parentId']."', 
					'$memo','".$user['LOGIN_ID']."',NOW()
					)" ;
					
					$this->query($sql) ;
		}
	}
}