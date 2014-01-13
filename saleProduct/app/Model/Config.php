<?php
class Config extends AppModel {
	var $useTable = "sc_config" ;
	
	public function saveConfigItem($data,$user){
		$loginId = $user['LOGIN_ID'] ;
		
		if( empty($data['id']) ){
			$sql = "insert into sc_config(LABEL, 
			`KEY`, 
			`TYPE`,MEMO) values('".$data['label']."','".$data['key']."','".$data['type']."','".$data['memo']."')" ;
		}else{
			$sql = "UPDATE  sc_config 
				SET
				LABEL = '".$data['label']."' , 
				`KEY` = '".$data['key']."' , 
				`TYPE` = '".$data['type']."',
				`MEMO` = '".$data['memo']."'
				WHERE
				ID = '".$data['id']."' " ;
		}
		
		
		$this->query($sql) ;
	}
	
	public function getConfigItem($id){
		$sql = " select * from sc_config where id = '$id'" ;
		return $this->query($sql) ;
	}
	
	public function deleteConfigItem($id){
		$sql = "delete from sc_config where id = '$id'" ;
		return $this->query($sql) ;
	}
	
	public function getAmazonConfig($key,$default = null ){
		$sql = " select * from sc_amazon_config where name = '$key'" ;
		$rows =  $this->query($sql) ;
		if(isset($rows[0]['sc_amazon_config']['VALUE'])){
			return $rows[0]['sc_amazon_config']['VALUE'];
		}
		return $default ;
	}

	public function getAmazonConfigById($id){
		$sql = " select * from sc_amazon_config where id = '$id'" ;
		$rows =  $this->query($sql) ;
		return $rows  ;
	}
	
	public function getAmazonConfigByType($type){
		$sql = " select * from sc_amazon_config where type = '$type'" ;
		$rows =  $this->query($sql) ;
		return $rows  ;
	}
}