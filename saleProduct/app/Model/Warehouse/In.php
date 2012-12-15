<?php
class In extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function edit($params,$user){
		$inId = $params['arg1'] ;
		$result = $this->getObject("sql_warehouse_in_getById",array('id'=>$inId)) ;
		return $result ;
	}
	
	public function editTab(){
		return null ;
	}
	
	public function editBox(){
	}
	
	public function editBoxPage($params,$user){
		$inId = $params['arg1'] ;
		$boxId = $params['arg2'] ;
		if(empty($boxId)){
			return null ;
		}else{
			$result = $this->getObject("sql_warehouse_box_getById",array('boxId'=>$boxId)) ;
			return $result ;
		}
	}

 
	public function editBoxProductPage($params,$user){
		$boxId = $params['arg1'] ;
		$boxProductId = $params['arg2'] ;
		if(empty($boxProductId)){
			return null ;
		}else{
			$result = $this->getObject("sql_warehouse_box_getById",array('boxProductId'=>$boxProductId)) ;
			return $result ;
		}
	}
	
	public function editTrack(){}
	
	public function doSave($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_warehouse_in_insert",$params) ;
		}else{
			$this->exeSql("sql_warehouse_in_update",$params) ;
		}
	}
	
	public function doSaveBox($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_warehouse_box_insert",$params) ;
		}else{
			$this->exeSql("sql_warehouse_box_update",$params) ;
		}
	}
	
	public function doSaveBoxProduct($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_warehouse_box_product_insert",$params) ;
		}else{
			$this->exeSql("sql_warehouse_box_product_update",$params) ;
		}
	}
	
	public function doSaveTrack($params){
		$this->exeSql("sql_warehouse_track_insert",$params) ;
	}
}