<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
$sqlMaps = null ;
 
class AppModel extends Model {
		function _clearDBCache() {
		  	
		}
		
		public function getDefaultCodeOnlyIndex($code){
			$index = $this->getMaxValue($code , null , 1) ;
			if( strlen($index) < 5 ){
				$len = 5-strlen($index) ;
				for($i=0 ;$i < $len ;$i++){
					$index = '0'.$index ;
				}
			}
			$defaultCode = "$code-".$index ;
			return $defaultCode ;
		}
		
		/**
		 * code
		 * @param unknown_type $code
		 */
		public function getDefaultCode($code){
			$index = $this->getMaxValue($code , null , 1) ;
			if( strlen($index) < 5 ){
				$len = 5-strlen($index) ;
				for($i=0 ;$i < $len ;$i++){
					$index = '0'.$index ;
				}
			}
			$defaultCode = "$code-".date("ymd").'-'.$index ;
			return $defaultCode ;
		}
		
		public function getUserDefaultCode($code){
			$index = $this->getMaxValue($code , null , 1) ;
			if( strlen($index) < 5 ){
				$len = 5-strlen($index) ;
				for($i=0 ;$i < $len ;$i++){
					$index = '0'.$index ;
				}
			}
			$user = $this->getUser() ;
			$defaultCode = "$code-".strtoupper($user['LOGIN_ID']) ."-".date("ymd").'-'.$index ;
			return $defaultCode ;
		}
		
		function getValue($params , $key){
			if( isset($params[$key]) )
				return $params[$key] ;
			return null ;
		}
		
		function getMaxValue($type ,$belongTo = null , $defaultValue = null){
			if( $belongTo != null ){
				$sql = "select * from sc_index_gen where type = '$type' and belong_to= '$belongTo'" ;
				$result = $this->query($sql) ;
				if( !empty($result) ){
					return $result[0]['sc_index_gen']['INDEX'] ;
				}
			}
			
			$sql = "SELECT ( MAX(`index`)+1 ) AS C FROM sc_index_gen where type = '$type'" ;
			$result = $this->query($sql) ;
			$value = '' ;
			if( empty($result) ){
				$value = $defaultValue ;
			}else{
				$value= $result[0][0]['C'] ;
				if(empty($value)){
					$value = $defaultValue ;
				}
			}
			$sql = "insert into sc_index_gen(`index`,type,belong_to) values($value , '$type','$belongTo')" ;
			$this->query($sql) ;
			return $value ;
		}
		
		function endsWith($haystack, $needle){
		    $length = strlen($needle);
		    if ($length == 0) {
		        return true;
		    }
		
		    return (substr($haystack, -$length) === $needle);
		}
	
		function getScriptRecords($query=null) {
			$sql = 'SELECT * FROM sc_election_rule' ;
			$array = $this->query($sql);
			return $array ;
		}
		
		function formatSqlParams($param = null){
			if( $param == null || empty($param)  || $param == "") return $param ;
			
			return str_replace("'","‘",$param) ;
			
		}
		
		function getAgent($index){
			$agents = array() ;
			$agents[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11" ;
			$agents[] = "(compatible; MSIE 4.01; MSN 2.5; AOL 4.0; Windows 98)" ;
			return $agents[ $index % 2 ]  ;		
		}
		//递归查询，向下
		function getRecursionWithMe($table,$idColName,$parentIdColName,$id){
			$result = $this->getChildRecursion($table,$idColName,$parentIdColName,$id) ;
			$sql = "select * from $table where $idColName = '$id' " ;
			$temp = $this->query($sql) ;
			if( count($temp) > 0 ){
				foreach($temp as $record){
					$record = $record[$table] ;
					$result[] = $record ;
				}
			}
			return $result ;
		}
		
		function getRecursion($table,$idColName,$parentIdColName,$id){
			$result = $this->getChildRecursion($table,$idColName,$parentIdColName,$id) ;
			return $result ;
		}
		
		function getChildRecursion($table,$idColName,$parentIdColName,$id){
			$array = array() ;
			$sql = "select * from $table where $parentIdColName = '$id' " ;
			$temp = $this->query($sql) ;
			if( count($temp) > 0 ){
				foreach($temp as $record){
					$record = $record[$table] ;
					$array[] = $record ;
					$_id = $record[$idColName] ;
					$temp1 = $this->getChildRecursion($table,$idColName,$parentIdColName,$_id) ;
					foreach($temp1 as $record1){
						$array[] = $record1 ;
					}
				}
			}
			return $array ;
		}
		
		//递归查询，向上
		function getRecursionUp($table,$idColName,$parentIdColName,$pid){
			$result = $this->getParentRecursion($table,$idColName,$parentIdColName,$pid) ;
			return $result ;
		}
		
		function getParentRecursion($table,$idColName,$parentIdColName,$pid){
			$array = array() ;
			$sql = "select * from $table where $idColName = '$pid' " ;
			$temp = $this->query($sql) ;
			if( count($temp) > 0 ){
				foreach($temp as $record){
					$record = $record[$table] ;
					$array[] = $record ;
					$_id = $record[$parentIdColName] ;
					$temp1 = $this->getParentRecursion($table,$idColName,$parentIdColName,$_id) ;
					foreach($temp1 as $record1){
						$array[] = $record1 ;
					}
				}
			}
			return $array ;
		}
		
		public function creatdir($path){
			if(!is_dir($path))
			{
				if($this->creatdir(dirname($path)))
				{
					mkdir($path,0777);
					return true;
				}
			}
			else
			{
				return true;
			}
		}
		
		public function getObject($sql , $query){
			$result = $this->exeSql($sql , $query) ;
			if(empty($result)){
				return null ;
			}
			
			$result = $result[0] ;
			$return = array() ;
			foreach($result as $items){
				foreach($items as $key=>$value){
					$return[$key] = $value ;
				}
			}
			
			return $return ;
		}
		
		public function formatObject($result){
			$return = array() ;
			foreach($result as $items){
				foreach($items as $key=>$value){
					$return[$key] = $value ;
				}
			}
			return $return ;
		}
		
		public function exeSqlWithFormat($sql , $query){
			$sql = $this->getDbSql($sql) ;
			$sql = $this->getSql($sql,$query) ;
			//	echo $sql ;
			$records = $this->query($sql) ;
			$items = array() ;
			if(!empty($records)){
				foreach( $records as  $re){
					$re = $this->formatObject($re) ;
					$items[] = $re ;
				}
			}
			return $items ;
		}
		
		public function exeSql($sql , $query){
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			$db->_queryCache = array() ;
			
			$sql = $this->getDbSql($sql) ;
			
			$sql = $this->getSql($sql,$query) ;
			
		//	echo $sql ;
		//	echo $sql ;
			return $this->query($sql) ;
		}
		
		public function getExeSql($sql , $query){
			$sql = $this->getDbSql($sql) ;
			
			$sql = $this->getSql($sql,$query) ;
			//echo $sql ;
			return $sql ;
		}
		
		public function getUser(){
			App::import('Component','Session');
			 $session = new SessionComponent(new ComponentCollection());
			$user =  $session->read("product.sale.user") ;
			return $user ;
		}
		
		public function getSql($sql , $query){
  			 $user =  $this->getUser() ; 
  			 
  			 $userId = $user['LOGIN_ID'] ;
			
			$domain =  $_SERVER['SERVER_NAME'] ;
			$query['domain'] = $domain ;
			//push evn to query
			$query['Evn.loginId'] = $userId ;
			$query['Evn.domain'] = $domain ;
			
			$index = 0 ;
	    	$parseSql = "" ;
	    	$array = explode("{@",$sql);
	    	
	    	foreach( $array as $child ){
	    		if( $index==0 ){
	    			$parseSql .= $child ;
	    		}else{
	    			$childArray = explode("}",$child ) ;
	    			$i1 = 0 ;
	    			foreach($childArray as $t){
	    				if($i1 == 0){
	    					$cArray = explode("#",$t) ;
	    					$i2 =0 ;
	    					
	    					//解析字句
	    					$clause = "" ;
	    					$isTrue = false ;
	    					foreach($cArray as $c){
	    						if( $i2 > 0 && $i2 % 2 == 1 ){
	    							$key = trim($c) ;
	    							$defaultValue = '' ;
	    							$keyarray = explode(':',$key) ;

	    							$key = $keyarray[0] ;
	    							
	    							if( count($keyarray) >=2 ){
	    								$defaultValue = $keyarray[1] ;
	    							}
	    							
	    							$ka = explode('|',$key) ;
	    							$key = $ka[0] ;
	    							$noescape = false ;
	    							if( count($ka) >=2 ){
	    								$noescape = $ka[1] ;
	    							}
	    							
	    							if( strpos($key, '$') === 0 ){//权限环境变量
	    							//	echo 111111111111;
	    								$evnKey = substr( $key , 1 ) ;
	    							//	echo $evnKey ;
	    								//查询权限变量
	    								$evnObj = $this->getObject("sql_security_find_dataSecurity",array('code'=>$evnKey , 'loginId'=>$userId)) ;
	    								
	    								if( isset($evnObj['URL'])  && !empty($evnObj['URL'])  ){
	    									
	    									$evnValue = $evnObj['URL'] ;
	    									$evnValue = str_replace('#loginId#',"'$userId'",$evnValue);
	    									
	    									$clause .=  $evnValue  ;
	    									$isTrue = true ;
	    								}else{
	    									if( !empty($defaultValue) ){
	    										$clause .=  $defaultValue  ;
	    										$isTrue = true ;
	    									}else{
	    										$isTrue = false ;
	    										break ;
	    									}
		    							}
	    							}else  if( isset($query[$key]) &&( $query[$key]=='0' || !empty($query[$key]) 
	    								|| !empty($defaultValue) || $defaultValue == '0' ) ){
	    								
	    								$kValue = $query[$key] ;
	    								//格式化$kValue,防止sql特殊字符
	    								//$kValue = str_replace("'","\'",$kValue);
	    								//if( $this->is_utf8($kValue) ){
	    									//
	    								//}else{
	    								//	$kValue = utf8_encode($kValue) ;
	    								//}
	    								if(empty($kValue) && $kValue != '0'){
	    									$kValue = $defaultValue ;
	    								}
										if($noescape == 'true'){
											$clause .= $kValue ;
										}else{
											$clause .=mysql_escape_string($kValue) ;
										}
	    								
										
	    								$isTrue = true ;
	    							}else{
	    								$isTrue = false ;
	    								break ;
	    							}
	    						}else{
	    							$clause .= $c ;
	     						}
	     						$i2++ ;
	    					}
	    					if($isTrue)$parseSql .= $clause ;
	    				}else{
	    					$parseSql .= $t ;
	    				}
	    				$i1++ ;
	    			} ;
	    		}
	    		$index++ ;
	    	} 
	    	return $parseSql ;
		}
		
		public function getDbSql($key){
			try{
				$sql = "select * from sc_sql where id = '$key'" ;
				$record = $this->query($sql) ;
				if(empty($record) || count($record)<=0){
					
					return $key ;
				}
				return $record[0]['sc_sql']['TEXT'] ;
			}catch(Exception $e){
				return $key ;
			}
		}
		
		function is_utf8($string=null) {  
			
			if(empty($string)) return true ;
			if(!is_string($string))return true;
		    // From http://w3.org/International/questions/qa-forms-utf-8.html      
		    return preg_match('%^(?:  
		        [\x09\x0A\x0D\x20-\x7E]              # ASCII  
		        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte  
		        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs  
		        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte   
		        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates   
		        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3   
		        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15  
		        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16  
		    )*$%xs', $string);  
		}
		
		function create_guid() {
			$charid = strtoupper(md5(uniqid(mt_rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = substr($charid, 0, 8).$hyphen
			.substr($charid, 8, 4).$hyphen
			.substr($charid,12, 4).$hyphen
			.substr($charid,16, 4).$hyphen
			.substr($charid,20,12);
			return $uuid;
		}
}
