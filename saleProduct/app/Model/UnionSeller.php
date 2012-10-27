<?php
class UnionSeller extends AppModel {
	var $useTable = "sc_union_seller" ;
	
	function getById($id){
		$sql = "select * from sc_union_seller where id = '$id'" ;
		return $this->query($sql) ;
	}
	
	function getByAccountId($id){
		$sql = "select * from sc_union_seller where account_id = '$id'" ;
		return $this->query($sql) ;
	}
	
	function save($data , $user){
		if( empty($data['id']) ){
			$sql = "insert into sc_union_seller(name,url,memo,account_id)
					values('".$data['name']."','".$data['url']."','".$data['memo']."','".$data['accountId']."')" ;
			$this->query($sql) ;
		}else{
			$sql = "update sc_union_seller
						set name = '".$data['name']."',
							url = '".$data['url']."',
							memo = '".$data['memo']."',
							account_id = '".$data['account_id']."'
						where id = '".$data['id']."'" ;
			$this->query($sql) ;
		}
	}
}