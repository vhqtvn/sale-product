<?php
class SaleUser extends AppModel {
	var $useTable = "sc_product_cost" ;
	
		
	/**
	 * 日志操作
	 */
	public function setDanger($params,$user){
		$emails = $params['checked_emails'] ;
		$unemails = $params['unchecked_emails'] ;
		
		foreach( explode(",",$emails) as $email ){
			$status = "danger" ;
			try{
				$sql = " 
					UPDATE sc_amazon_order_user 
						SET 
						STATUS = '$status'
						WHERE
						EMAIL = '$email'  " ;
				$this->query($sql) ; 
			}catch(Exception $e){}
		}
		
		foreach( explode(",",$unemails) as $email ){
			$status = "" ;
			$sql = " 
					UPDATE sc_amazon_order_user 
						SET 
						STATUS = '$status'
						WHERE
						EMAIL = '$email'  " ;
				$this->query($sql) ; 
		}
		
	}
	

}