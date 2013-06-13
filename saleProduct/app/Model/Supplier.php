<?php
class Supplier extends AppModel {
	var $useTable = "sc_supplier" ;
	

	
	public function getProductSuppliers($asin){
		$sql = "select sc_supplier.* from sc_supplier , sc_product_supplier
					where sc_supplier.id = sc_product_supplier.supplier_id and sc_product_supplier.status <> 'invalid'
					and sc_product_supplier.asin = '$asin'" ;
		return $this->query($sql) ;
	}
	
	/**
	 *   根据货品SKU获取对应的信息
	 *   @param unknown_type $sku
	 */
	public function getProductSuppliersBySku( $sku ){
		return $this->exeSql("sql_getProductSuppliersBySku", array('realSku'=>$sku)) ;
	}
	
	/**
	 * 保存
	 * @param unknown_type $data
	 */
	public function saveSupplierBySku($data){
		$time = explode ( " ", microtime () );
		$time = $time [1] . ($time [0] * 1000);
		$time2 = explode ( ".", $time );
		$time = $time2 [0];
		$id  = $time ;
		
		if( isset($data['id']) && !empty($data["id"]) ){
			$id = $data['id'] ;
			$this->exeSql("sql_supplier_update", $data) ;
		}else{
			$data['id'] = $id ;
			$this->exeSql("sql_supplier_insert", $data) ;
		}
		
		if(!empty($data['sku'])){//保存到产品
			try{
				$this->exeSql("sql_realProduct_supplierInsert", array('sku'=>$data['sku'],'supplierId'=>$id,'loginId'=>$data['loginId'])) ;
			}catch(Exception $e){}
			
		}
	}
	
	public function saveSupplier($data){
		$loginId = $data['loginId'] ;
		
		
		$id = '' ;
		
		if( isset($data['id']) && !empty($data["id"]) ){
			$id = $data['id'] ;
			$this->exeSql("sql_supplier_update", $data) ;
		}else{
			$time = explode ( " ", microtime () );
			$time = $time [1] . ($time [0] * 1000);
			$time2 = explode ( ".", $time );
			$time = $time2 [0];
			$id  = $time ;
			$data['id'] = $id ;
			
			$this->exeSql("sql_supplier_insert", $data) ;
		}
		
		$metas = $this->exeSqlWithFormat("select * from sc_supplier_evaluate_meta",array()) ;
		foreach($metas as $meta){
			$sql = "sql_supplier_eva_findBySupplieAndMeta" ;
			$eva = $this->getObject($sql, array("supplierId"=>$data['id'],"metaCode"=>$meta['CODE']) ) ;
			if(empty($eva)){//inset
				$sql="sql_supplier_eva_save" ;
				$this->exeSql($sql, array("supplierId"=>$data['id'],"metaCode"=>$meta['CODE'] ,
						 'score'=>$data[$meta['CODE'].'_select'],'memo'=>$data[$meta['CODE'].'_memo'],'loginId'=>$loginId)) ;
			}else{
				$sql="sql_supplier_eva_update" ;
				$this->exeSql($sql, array("supplierId"=>$data['id'],
						"metaCode"=>$meta['CODE'] ,
						'score'=>$data[$meta['CODE'].'_select'],'memo'=>$data[$meta['CODE'].'_memo'],'loginId'=>$loginId,'id'=>$eva['ID'])) ;
			}
		}
		
		/*
		if(!empty($asin)){//保存到产品
		    $sql="insert into sc_product_supplier(supplier_id,asin,status) values('$id','$asin','valid')" ;
		    $this->query($sql) ;
		}*/
		
		return $data ;
	}
	
	public function delSupplier($id){
		$sql = "delete from sc_supplier where id = '$id'" ;
		$this->query($sql) ;
		
		$sql = "delete from sc_product_supplier where supplier_id = '$id'" ;
		$this->query($sql) ;
	}
	
	public function saveProductSupplierBySku($data){
		$sku = $data['sku'] ;
		$suppliers = $data['suppliers'] ;
		
		/**
		 * 删除安排对应的供应商
		 */
		$this->exeSql("delete from sc_real_product_supplier where real_sku = '{@#sku#}' ", $data) ;
	
		foreach( explode(',',$suppliers) as $supplier ){
			try{
				$this->exeSql("sql_realProduct_supplierInsert", array('sku'=>$sku,'supplierId'=>$supplier,'loginId'=>$data['loginId'] )) ;
			}catch(Exception $e){}
		}
		
		 /*
		$sql = "update sc_product_supplier set status = 'invalid' where sku = '$sku'" ;//update to invalid
	
		foreach( explode(',',$suppliers) as $supplier ){
				$sql = "select * from sc_product_supplier where supplier_id = '$supplier' and sku = '$sku'" ;
				$ps = $this->query($sql) ;
				if( empty($ps[0]) ){
					try{
							$sql = "INSERT INTO  sc_product_supplier
							(
							SUPPLIER_ID,
							SKU,STATUS
							)
							VALUES
							(
							'$supplier',
							'$sku','valid'
							)" ;
							$this->query($sql) ;
					}catch(Exception $e){
								//	print_r( $e ) ;
					}
				}else{
					try{
						$sql = "update  sc_product_supplier
							set status = 'valid' where  supplier_id = '$supplier' and sku = '$sku'" ;
						$this->query($sql) ;
					}catch(Exception $e){
					}
				}
			}*/
	}
	
	public function saveProductSupplier($data,$user){
		$asin = $data['asin'] ;
		$suppliers = $data['suppliers'] ;
		
		$sql = "update sc_product_supplier set status = 'invalid' where asin = '$asin'" ;//update to invalid
		
		foreach( explode(',',$suppliers) as $supplier ){
			$sql = "select * from sc_product_supplier where supplier_id = '$supplier' and asin = '$asin'" ;
			$ps = $this->query($sql) ;
			if( empty($ps[0]) ){
				try{
					$sql = "INSERT INTO  sc_product_supplier 
						(
						SUPPLIER_ID, 
						ASIN,STATUS
						)
						VALUES
						( 
						'$supplier', 
						'$asin','valid'
						)" ;
					$this->query($sql) ;
				}catch(Exception $e){
				//	print_r( $e ) ;
				}
			}else{
				try{
					$sql = "update  sc_product_supplier 
						set status = 'valid' where  supplier_id = '$supplier' and asin = '$asin'" ;
					$this->query($sql) ;
				}catch(Exception $e){}
			}
		}
	}
	
	public function saveProductSupplierXJ($data,$user,$localUrl){
		$image = "" ;
		if(!empty($localUrl)){
			$image =  $localUrl ;
		}
		$data['image'] = $image ;
		$data['loginId'] = $user['LOGIN_ID'] ;

		if( empty( $data['id'] ) ){
			$this->exeSql("sql_purchase_plan_product_inquiry_insert", $data) ;
		}else{
			$this->exeSql("sql_purchase_plan_product_inquiry_update", $data) ;
		}
	}
	
	
	public function getSupplier($id){
		$sql = "SELECT sc_supplier.* FROM sc_supplier where id = '$id'";
		$array = $this->query($sql);
		return $array ;
	}
	
	//seller
	function getGridRecords($query=null){
		$limit =  $query["limit"] ;
		$curPage =  $query["curPage"] ;
		$start =  $query["start"] ;
		$end =  $query["end"] ;
		$where = " where 1 = 1 " ;
		if( isset( $query["name"] ) ){
			$name = $query["name"] ;
			$where .= " and name like '%$name%'" ;
		}
		
		$sql = "SELECT sc_supplier.* ,
			( select sc_user.name from sc_user where sc_user.login_id = sc_supplier.creator ) as USERNAME
			FROM sc_supplier $where  limit ".$start.",".$limit;
		$array = $this->query($sql);
		return $array ;
	}

	function getGridCount($query=null){
		$where = " where 1 = 1 " ;
		if( isset( $query["name"] ) ){
			$name = $query["name"] ;
			$where .= " and name like '%$name%'" ;
		}
		
		$sql = "SELECT count(*) FROM sc_supplier $where ";
		$array = $this->query($sql);
		return $array ;
	}

}