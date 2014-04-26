<?php
class ScCache extends AppModel {
	var $useTable = "sc_product_flow" ;
	
	public function exist($key){
		$sql= "select * from sc_cache where cache_key= '{@#key#}'" ;
		$cache = $this->getObject($sql, array("key"=>$key)) ;
		return empty( $cache )?false:true ;
	}
	
	public function refreshCache($key ){
		ini_set('date.timezone','Asia/Shanghai');
		$printTime = date('Y-m-d H:i:s');
		$sql = "update sc_cache set LAST_UPDATED_TIME='{@#time#}' where  CACHE_KEY = '{@#key#}'" ;
		$this->exeSql($sql, array("key"=>$key,"time"=>$printTime)) ;
	}
	
	public function createCache($key , $value){
		ini_set('date.timezone','Asia/Shanghai');
	    $printTime = date('Y-m-d H:i:s');
		
		$sql = "INSERT INTO  sc_cache 
						(CACHE_KEY, 
						CACHE_VALUE, 
						CREATED_TIME, 
						LAST_UPDATED_TIME
						)
						VALUES
						('{@#key#}', 
						'{@#value#}', 
						'{@#time#}', 
						'{@#time#}'
						)" ;
		$this->exeSql($sql, array("key"=>$key,"value"=>$value,'time'=>$printTime)) ;
	}
	
	public function getCache($key){
		$sql= "select * from sc_cache where cache_key= '{@#key#}'" ;
		$cache = $this->getObject($sql, array("key"=>$key)) ;
		return $cache ;
	}
	
	public function removeCache($key){
		$sql = "delete from sc_cache where cache_key= '{@#key#}'" ;
		$this->exeSql($sql, array("key"=>$key)) ;
	}
}