<?php
class User extends AppModel {
	var $useTable = 'sc_user';
	
	function queryUserByUserName($username){
		return $this->exeSql("sql_security_user_getByUserName",array('username'=>$username)) ;
	}
	
	function queryUserGroups(){
		$sql = "select * from sc_security_groups " ;
		return $this->query($sql) ;
	}
	
	function getFunctionById($id){
		$sql = "select * from sc_security_function where id = '$id'" ;
		return $this->query($sql) ;
	}
	
	
	function editGroup($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_security_group_insert",$params) ;
		}else{
			$this->exeSql("sql_security_group_update",$params) ;
		}
	}
	
	function saveFunctoin($params){
		if( empty( $params['id'] ) ){
			$this->exeSql("sql_security_function_insert",$params) ;
		}else{
			$this->exeSql("sql_security_function_update",$params) ;
		}
	}
	
	function saveUser($user){
		if(!empty($user["password"])){
			$user["password"] = md5( $user["password"] ) ;
		}
		
		if(empty($user['id'])){
			$this->exeSql("sql_security_user_insert",$user) ;
		}else{
			$this->exeSql("sql_security_user_update",$user) ;
		}
	}
	
	function disableUser($params){
		$this->exeSql("sql_security_user_disabled",$params) ;
	}
	
	function getFunctionRelGroups($code){
		$sql = " SELECT 
		  sc_security_function.* ,
		 ( SELECT 1 FROM sc_security_group_function WHERE sc_security_group_function.FUNCTION_CODE
		   = sc_security_function.code AND sc_security_group_function.GROUP_CODE = '$code' ) AS selected
		 FROM sc_security_function where ( parent_id <> 'account' || parent_id IS NULL) order by  parent_id" ;
		 
		 return $this->query($sql) ; 
	}
	
	function getFunctionForAccount($code,$accountId){
		$sql = " SELECT 
		  sc_security_function.* ,'$accountId' as ACCOUNT_ID,
		 ( SELECT 1 FROM sc_security_group_function WHERE sc_security_group_function.FUNCTION_CODE
		   = CONCAT('a___',$accountId,'_',sc_security_function.code) AND sc_security_group_function.GROUP_CODE = '$code' ) AS selected
		 FROM sc_security_function 
		 WHERE ( parent_id = 'account') ORDER BY  parent_id" ;
		 
		 return $this->query($sql) ; 
	}
	
	function getAccountSecurity($code,$accountId){
		$sql = " SELECT 1 as selected FROM sc_security_group_function WHERE group_code = '$code' and function_code = CONCAT('a___',$accountId) " ;
		
		return $this->query($sql) ; 
	}
	
	function getFilterRules($code){
		$sql = "select sc_security_function.* ,
		 ( SELECT 1 FROM sc_security_group_function WHERE sc_security_group_function.FUNCTION_CODE
		   = CONCAT('r___',sc_security_function.CODE) AND sc_security_group_function.GROUP_CODE = '$code' ) AS selected
		  from  (
			SELECT ID,NAME,ID AS CODE , (SELECT ID FROM sc_security_function WHERE CODE = 'filter_rule') AS PARENT_ID FROM sc_election_rule
			) sc_security_function" ;
		  
		  return $this->query($sql) ;
	}

	
	function getAmazonaccountSecurity($accountId , $groupCode){
		$sql = "SELECT * FROM sc_security_group_function WHERE group_code = '$groupCode' AND function_code LIKE 'a___$accountId%'" ;
		 
		 
		 return $this->query($sql) ; 
	}

	
	function saveAssignFunctions($ids , $code ){
		//删除所有
		$sql = "delete from sc_security_group_function where group_code = '$code'" ;
		$this->query($sql) ; 
		
		foreach( explode(",",$ids) as $id ){
			$sql = "insert into sc_security_group_function(group_code,function_code) values('$code','$id')" ;
			$this->query($sql) ; 
		}
	}
	
		
	/**
	 * 根据角色获取功能
	 */
	function getSecurityFunctions( $code ){
		$functions = $this->getFunctionRelGroupsFront($code);  
		
		$filterRules = $this->getFilterRulesFront($code) ;

    	//getAccount Info
		$amazonAccount  = ClassRegistry::init("Amazonaccount") ;
		$accounts = $amazonAccount->getAllAccounts(); 
		
		$accountSecuritys = array() ;
		$accountArray = array() ;
		foreach( $accounts as $Record ){
			
			$sfs = $Record['sc_amazon_account']  ;
			$id   = $sfs['ID'] ;
			$name = $sfs['NAME']  ;
			$securitys1 = $this->getAccountSecurityFront( $code , $id ) ;

			if( empty( $securitys1 ) || empty( $securitys1[0] ) ){
				continue ;
			}
			
			$securitys = $this->getFunctionForAccountFront( $code , $id ) ;
			
			$accountArray[] = $Record ;
			$accountSecuritys[$id] = $securitys ;
		} ;
		
		return array("functions"=>$functions,"accounts"=>$accountArray,"accountSecuritys"=>$accountSecuritys,"filterRules"=>$filterRules) ;
	}
	
	function getFunctionRelGroupsFront($code){
		return $this->exeSql("sql_security_getFunctionRelGroupsFrontByGroupCode",array('code'=>$code)) ;
	}
	
		
	function getFilterRulesFront($code){
		$sql = "select sc_security_function.* ,
		 ( SELECT 1 FROM sc_security_group_function WHERE sc_security_group_function.FUNCTION_CODE
		   = CONCAT('r___',sc_security_function.CODE) AND sc_security_group_function.GROUP_CODE = '$code' ) AS selected
		  from  (
			SELECT ID,NAME,ID AS CODE , (SELECT ID FROM sc_security_function WHERE CODE = 'filter_rule') AS PARENT_ID FROM sc_election_rule
			) sc_security_function where CONCAT('r___',code) in ( 
       		SELECT sc_security_group_function.function_code FROM sc_security_group_function
			WHERE sc_security_group_function.FUNCTION_CODE
		   = CONCAT('r___',sc_security_function.code) AND sc_security_group_function.GROUP_CODE = '$code' )" ;
		  
		  return $this->query($sql) ; 
		
	}
	
	function getFunctionForAccountFront($code,$accountId){
		$sql = " SELECT 
		  sc_security_function.*
		 FROM sc_security_function 
		 WHERE ( parent_id = 'account')
			and CONCAT('a___',$accountId,'_',sc_security_function.code) in (
       		SELECT sc_security_group_function.function_code FROM sc_security_group_function
			WHERE sc_security_group_function.FUNCTION_CODE
		   = CONCAT('a___',$accountId,'_',sc_security_function.code) AND sc_security_group_function.GROUP_CODE = '$code'
		) " ;
		 return $this->query($sql) ; 
	}
	
	function getAccountSecurityFront($code,$accountId){
		$sql = " SELECT 1 as selected FROM sc_security_group_function WHERE group_code = '$code' and function_code = CONCAT('a___',$accountId) " ;
		return $this->query($sql) ; 
	}
	
}