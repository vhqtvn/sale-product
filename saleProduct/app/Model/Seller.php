<?php
class Seller extends AppModel {
	var $useTable = "sc_seller" ;
	
	public function saveSeller($data,$user=null){
		$loginId = $data['loginId'] ;
		$sql = "insert into sc_seller(name,url,creator,create_time,platform_id) values('".$data['name']."','".$data['url']."','$loginId',NOW(),'".$data['platformId']."')" ;
		$this->query($sql) ;
	}

}