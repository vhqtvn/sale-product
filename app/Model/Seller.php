<?php
class Seller extends AppModel {
	var $useTable = "sc_seller" ;
	
	public function saveSeller($data,$user){
		$loginId = $user['LOGIN_ID'] ;
		$sql = "insert into sc_seller(name,url,creator,create_time) values('".$data['name']."','".$data['url']."','$loginId',NOW())" ;
		$this->query($sql) ;
	}

}