<?php
class User extends AppModel {
	var $useTable = 'sc_user';
	
	function queryUserByUserName($username){
		$sql = "select * from sc_user where login_id = '$username'" ;
		return $this->query($sql) ;
	}
	
	function queryUserGroups(){
		$sql = "select * from sc_security_groups " ;
		return $this->query($sql) ;
	}
	
	function getFunctionById($id){
		$sql = "select * from sc_security_function where id = '$id'" ;
		return $this->query($sql) ;
	}
	
	function saveFunctoin($data){
		if( empty( $data['id'] ) ){
			$sql = "INSERT INTO sc_security_function 
				(
				NAME, 
				PARENT_ID, 
				URL, 
				TYPE,
				CODE
				)
				VALUES
				(
				'".$data['name']."', 
				'".$data['parentId']."', 
				'".$data['url']."', 
				'".$data['type']."',
				'".$data['code']."'
				)
			" ;
			$this->query($sql) ;
		}else{
			$sql = "
				UPDATE sc_security_function 
					SET
					NAME = '".$data['name']."' , 
					PARENT_ID = '".$data['parentId']."' , 
					URL = '".$data['url']."' , 
					TYPE = '".$data['type']."' ,
					CODE = '".$data['code']."'
					
					WHERE
					ID = '".$data['id']."' " ;
					
					$this->query($sql) ;
		}
	}
	
	function saveUser($user){

		$id = $user["id"] ;
		$name = $user["name"] ;
		$loginId = $user["login_id"] ;
		$password = $user["password"] ;
		$group = $user["group"] ;
		if( !empty($password) ){
			$password = md5($password) ;
		}
		
		$user1 = $this->queryUserByUserName($loginId) ;
		if( $user1 != null ){
			//update
			$sql = "update sc_user set name = '$name',login_id='$loginId',group_code='$group'" ;
			if(!empty($password)  ){
				$sql = $sql.",password='$password'" ;
			}
			$sql = $sql." where id='$id'" ;
			$this->query($sql) ;
		}else{
			$sql = "insert sc_user(name,login_id,password,group_code) values('$name','$loginId','$password','$group')" ;
			$this->query($sql) ;
		}
	}
	
	function getFunctionRelGroups($code){
		$sql = " SELECT 
		  sc_security_function.* ,
		 ( SELECT 1 FROM sc_security_group_function WHERE sc_security_group_function.FUNCTION_CODE
		   = sc_security_function.code AND sc_security_group_function.GROUP_CODE = '$code' ) AS selected
		
		 FROM sc_security_function " ;
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
}