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
		
		if(!empty($data['sku'])){//保存到产品
			try{
				$this->exeSql("sql_realProduct_supplierInsert", array('sku'=>$data['sku'],'supplierId'=>$id,'loginId'=>$loginId )) ;
			}catch(Exception $e){
			}
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
			}catch(Exception $e){
			}
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

		$id = $data['id']  ;
		if( empty( $data['id'] ) ){
			$id = $this->create_guid() ;
			$data['id'] = $id ;
			$this->exeSql("sql_purchase_plan_product_inquiry_insert", $data) ;
		}
		$this->exeSql("sql_purchase_plan_product_inquiry_update", $data) ;
		
		//更新成本部分数据
		if( isset( $data['asin'] ) ){//asin  产品开发成本
			$asin = $data['asin']  ;
			if( !empty($asin) ){
				//sql_inquiry_cost_calc
				$inquiryData = $this->exeSqlWithFormat("sql_inquiry_cost_calc", $data) ;
				//计算最小成本
				$minCost = 999999 ;
				$PER_PRICE = 0 ;
				$PER_SHIP_FEE = 0 ;
				$CurrentData = null ;
				foreach($inquiryData as $indata){
					$cost1 = $indata['COST1'] ;
					$cost2 = $indata['COST2'] ;
					$cost3 = $indata['COST3'] ;
					
					if( $cost1 !=0 ){
						$minCost = min($minCost , $cost1 ) ;
						if($minCost == $cost1  ){
							$PER_PRICE = $indata['PER1_PRICE'] ;
							$PER_SHIP_FEE = $indata['PER1_SHIP_FEE'] ;
							$CurrentData = $indata ;
						}
					}
					
					if( $cost2 !=0 ){
						$minCost = min($minCost , $cost2 ) ;
						if($minCost == $cost2  ){
							$PER_PRICE = $indata['PER2_PRICE'] ;
							$PER_SHIP_FEE = $indata['PER2_SHIP_FEE'] ;
							$CurrentData = $indata ;
						}
					}
					
					if( $cost3 !=0 ){
						$minCost = min($minCost , $cost3 ) ;
						if($minCost == $cost3  ){
							$PER_PRICE = $indata['PER3_PRICE'] ;
							$PER_SHIP_FEE = $indata['PER3_SHIP_FEE'] ;
							$CurrentData = $indata ;
						}
					}
				}
				
				//保存询价成本到产品成本
				if( $PER_PRICE >0  ){
					$Cost  = ClassRegistry::init("Cost")  ;
					$Cost->initDevCost($asin,$user['LOGIN_ID'] ) ;
					$params = array() ;	
					$params['listingCosts'] = array() ; 
					$params1['loginId'] = $user['LOGIN_ID'] ;
					$params1['ASIN'] = $asin ;
					$params1['LOGISTICS_COST'] = $PER_SHIP_FEE ;
					$params1['PURCHASE_COST'] = $PER_PRICE ;			
					$params['productCost'] = json_encode($params1) ;			
					$Cost->saveCostAsin($params) ;
					
					//计算转仓物流成本
					$productLength = $CurrentData['PRODUCT_LENGTH'] ;
					$productWidth = $CurrentData['PRODUCT_WIDTH'] ;
					$productHeight = $CurrentData['PRODUCT_HEIGHT'] ;
					$Weight  = $CurrentData['WEIGHT'] ;
					//获取转仓成本单价
					$sql = "SELECT sp.TRANSFER_WH_PRICE FROM sc_platform sp,sc_product spd
											WHERE sp.id = spd.platform_id and asin= '{@#asin#}' " ;
					$temp = $this->getObject($sql, array("asin"=>$asin) ) ;
					if( !empty($temp) ){
						$TRANSFER_WH_PRICE = $temp['TRANSFER_WH_PRICE'] ;
						//转仓物流成本
						$transferCost =round( ($Weight - ( $productLength*$productWidth*$productHeight / 5000 ))*$TRANSFER_WH_PRICE ,2) ;
						
						debug(">>>>>>>>>>>>>".$transferCost) ;
						
						$sql= "update sc_product_cost_details set _TRANSFER_COST = '{@#transferCost#}' where asin = '{@#asin#}' " ;
						if(!empty($transferCost)){
							$this->exeSql($sql, array("transferCost"=>$transferCost,"asin"=>$asin) ) ;
						}
					}
				}
			}
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