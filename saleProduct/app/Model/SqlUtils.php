<?php

class SqlUtils extends AppModel {
	var $useTable = "sc_sql";
	public $count = 0 ;
	
	public function getDbSql($key){
	
		$sql = "select * from sc_sql where id = '$key'" ;
		$record = $this->query($sql) ;
		if(empty($record)){
			return $key ;
		}
		return $record[0]['sc_sql']['TEXT'] ;
	}
	
	public function loadSqls($flag = null){
		$this->count++ ;
		$dirPath = WWW_ROOT . '../sqls/' ;
		$keyMaps = array() ;
		if (is_dir( $dirPath )){ 
			$dir = opendir($dirPath); 
			while ($file = readdir($dir)){ 
				$file = trim($file) ;
				if($this->endsWith($file,".xml")){
					//load xml
					$xml = simplexml_load_file($dirPath . $file);
			    	$entrys = $xml->entry ;
			    	
			    	foreach($entrys as $entry){
			    		$_key = '' ;
			    		foreach($entry->attributes() as $key => $value) {
						    $_key = $value ;
						}
						$_value = (string)$entry ;
						//$_value = str_replace('\\','\\\\',$_value) ;
						$_value = str_replace("'","\'",$_value) ;
						/*try{
							$_value = iconv("gbk//IGNORE", "UTF-8//IGNORE", $_value);
							//$_value = iconv( 'ASCII' ,'utf-8//IGNORE' ,$_value ) ;
						}catch(Exception $e){
							print_r($e);
						}*/
						
						echo '<br>find sql:::'.$_key;
						try{
							$this->query("insert into sc_sql(id,text) values('$_key' ,'$_value' )") ;
			    		}catch(Exception $e){
			    			try{
			    				$this->query("update sc_sql set text='$_value' where id='$_key'") ;
			    			}catch(Exception $e){
			    				
			    			}
			    		}
						//$keyMaps[trim($_key)] = $_value ;
			    	} ;
				}
			} 
			closedir($dir); 
		}
		
		if( empty($sqlMaps) ){
			$sqlMaps = $keyMaps ;
		}
		
		return $keyMaps ;
	}
	
	public function getRecordSql( $query){
		$sql = $query['sqlId'] ;
		$sql = $this->getDbSql($sql) ;
		
		$sql = $this->getSql($sql,$query) ;
		$limit =  $query["limit"] ;
		$start =  $query["start"] ;
		$sql = "SELECT t.* FROM ( $sql ) t  limit ".$start.",".$limit;
		
		return $sql ;
	}
	
	public function getCountSql($query){
		$sql = $query['sqlId'] ;
		if(isset($query['countSqlId'])){
			$sql = $query['countSqlId'] ;
			$sql = $this->getDbSql($sql) ;
			$sql = $this->getSql($sql,$query) ;
			return $sql ;
		}
		
		$sql = $this->getDbSql($sql) ;
		$sql = $this->getSql($sql,$query) ;
		$sql = "SELECT count(*) FROM ( $sql ) t  ";
		return $sql ;
	}
	
	
}