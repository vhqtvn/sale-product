<?php
class PublishEbay extends AppModel {
	var $useTable = "sc_product_cost" ;
	
	public function loadProfile($params){
		$type = $params['type'] ;
		$data = null ;
		if( $type == "detail_wuliu" ){//物流
			$data = $this->exeSqlWithFormat("sql_ebay_profile_logistics_list", array()) ;
		}else if( $type == "detail_location" ){
			$data = $this->exeSqlWithFormat("sql_ebay_profile_location_list", array()) ;
		}else if( $type == "detail_return" ){
			$data = $this->exeSqlWithFormat("sql_ebay_profile_return_list", array()) ;
		}
		
		return $data ;
	}
	
	public function getProfile($params){
		$type = $params['type'] ;
		$data = null ;
		if( $type == "detail_wuliu" ){//物流
			$data = $this->getObject("sql_ebay_profile_logistics_get",$params) ;
		}else if( $type == "detail_location" ){
			$data = $this->getObject("sql_ebay_profile_location_get", $params) ;
		}else if( $type == "detail_return" ){
			$data = $this->getObject("sql_ebay_profile_return_get",$params) ;
		}
	
		return $data ;
	}
	
	public function deleteProfile($params){
		$type = $params['type'] ;
		$data = null ;
		if( $type == "detail_wuliu" ){//物流
			$data = $this->exeSqlWithFormat("sql_ebay_profile_logistics_delete",$params) ;
		}else if( $type == "detail_location" ){
			$data = $this->exeSqlWithFormat("sql_ebay_profile_location_delete", $params) ;
		}else if( $type == "detail_return" ){
			$data = $this->exeSqlWithFormat("sql_ebay_profile_return_delete",$params) ;
		}
	
		return $data ;
	}
	
	public function saveProfile( $params ){
		$type = $params['type'] ;
		
		if( $type == "detail_wuliu" ){//物流
			$data = $params ;
			$data['SD_SSO1_SHIPPINGSERVICE'] 								= $data['shippingdetails']['ShippingServiceOptions'][0]['ShippingService'] ;
			$data['SD_SSO1_SHIPPINGSERVICECOST'] 						=$data['shippingdetails']['ShippingServiceOptions'][0]['ShippingServiceCost'] ;
			$data['SD_SSO1_SHIPPINGSERVICEADDITIONALCOST'] = $data['shippingdetails']['ShippingServiceOptions'][0]['ShippingServiceAdditionalCost'] ;
			$data['SD_SSO2_SHIPPINGSERVICE'] 								= $data['shippingdetails']['ShippingServiceOptions'][1]['ShippingService'] ;
			$data['SD_SSO2_SHIPPINGSERVICECOST'] 						=$data['shippingdetails']['ShippingServiceOptions'][1]['ShippingServiceCost'] ;
			$data['SD_SSO2_SHIPPINGSERVICEADDITIONALCOST'] = $data['shippingdetails']['ShippingServiceOptions'][1]['ShippingServiceAdditionalCost'] ;
			$data['SD_SSO3_SHIPPINGSERVICE'] 								= $data['shippingdetails']['ShippingServiceOptions'][2]['ShippingService'] ;
			$data['SD_SSO3_SHIPPINGSERVICECOST'] 						=$data['shippingdetails']['ShippingServiceOptions'][2]['ShippingServiceCost'] ;
			$data['SD_SSO3_SHIPPINGSERVICEADDITIONALCOST'] = $data['shippingdetails']['ShippingServiceOptions'][2]['ShippingServiceAdditionalCost'] ;
			$data['SD_ISSO1_SHIPPINGSERVICE'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingService'] ;
			$data['SD_ISSO1_SHIPPINGSERVICECOST'] 						= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingService'] ;
			$data['SD_ISSO1_SHIPPINGSERVICEADDITIONALCOST'] 	= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShippingService'] ;
			$data['SD_ISSO1_SHIPTOLOCASTION'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][3]['ShipToLocation'] ;
			$data['SD_ISSO2_SHIPPINGSERVICE'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingService'] ;
			$data['SD_ISSO2_SHIPPINGSERVICECOST'] 						= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingService'] ;
			$data['SD_ISSO2_SHIPPINGSERVICEADDITIONALCOST'] 	= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShippingService'] ;
			$data['SD_ISSO2_SHIPTOLOCASTION'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][4]['ShipToLocation'] ;
			$data['SD_ISSO3_SHIPPINGSERVICE'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingService'] ;
			$data['SD_ISSO3_SHIPPINGSERVICECOST'] 						= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingService'] ;
			$data['SD_ISSO3_SHIPPINGSERVICEADDITIONALCOST'] 	= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShippingService'] ;
			$data['SD_ISSO3_SHIPTOLOCASTION'] 								= $data['shippingdetails']['InternationalShippingServiceOption'][5]['ShipToLocation'] ;
			$data['SD_SALESTAXSTATE'] = $data['shippingdetails']['SalesTax']['SalesTaxState'] ;
			$data['SD_SALESTAXPERCENT'] = $data['shippingdetails']['SalesTax']['SalesTaxPercent'] ;
			//$data['SD_SHIPPINGTYPE'] = $data['shippingdetails']['ShippingType'] ;
			$data['guid'] = $this->create_guid() ;
			$this->exeSql("sql_ebay_profile_logistics_insert", $data) ;
		}else if( $type == "detail_location" ){
			$data = $params ;
			$data['RP_RETURNSACCEPTEDOPTION'] = $data['return_policy']['ReturnsAcceptedOption'] ;
			$data['RP_REUNDOPTION'] = $data['return_policy']['RefundOption'] ;
			$data['RP_RETURNSWITHINOPTION'] = $data['return_policy']['ReturnsWithinOption'] ;
			$data['RP_SHIPPINGCOSTPAIDBYOPTION'] = $data['return_policy']['ShippingCostPaidByOption'] ;
			$data['RP_DESCRIPTION'] = $data['return_policy']['Description'] ;
			$data['guid'] = $this->create_guid() ;
			$this->exeSql("sql_ebay_profile_location_insert", $data) ;
		}else if( $type == "detail_return" ){
			$data = $params ;
			$pms = "" ;
			foreach( $data['paymentmethods'] as $pm){
				if( $pms == "" ){
					$pms = $pm ;
				}else{
					$pms .= ",".$pm ;
				}
			}
			//format parymethod
			$data['PAYMENTMETHODS1'] = $pms ;
			$data['guid'] = $this->create_guid() ;
			$this->exeSql("sql_ebay_profile_return_insert", $data) ;
		}
		
		return $data ;
	}
	
	public function loadMessageCounts(){
		return $this->exeSqlWithFormat("sql_ebay_message_getFalseCount", array()) ;
	}
	
	public function saveLocalResponse( $params ){
		$messageIds = $params['messageIds'] ;
		$messageIds = explode(",", $messageIds) ;
		
		$subject = $params['subject'] ;
		$body = $params['body'] ;
		
		foreach (  $messageIds as $messageId){
			$this->exeSql("sql_ebay_message_update_localResponse", array('MessageID'=>$messageId,'body'=>$body,'subject'=>$subject) ) ;
		}
	}
	
	/**
	 * 修改消息本地zhuan
	 * @param unknown $params
	 */
	public function tagMessageLocalStatus($params){
		$messageIds = $params['messageIds'] ;
		$messageIds = explode(",", $messageIds) ;
		
		$read = "" ;
		if(isset( $params['read'])){
			$read = $params['read'] ;
		}
		
		$flagged = "" ;
		if(isset( $params['flagged'])){
			$flagged = $params['flagged'] ;
		}
		
		foreach (  $messageIds as $messageId){
			$this->exeSql("sql_ebay_message_update_localstatus", array('MessageID'=>$messageId,'read'=>$read,'flagged'=>$flagged) ) ;
		}
	}
}