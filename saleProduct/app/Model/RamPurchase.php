<?php
class RamPurchase extends AppModel {
	var $useTable = "sc_warehouse_in" ;
	
	public function confirmBack($params){
		try{
			$rmaId= $params['rmaId'] ;
			$sql = "update sc_ram_event set BACK_DATE=NOW(),status=45,back_Memo='{@#backMemo#}' where id='{@#rmaId#}'" ;
			$this->exeSql($sql, $params) ;
			$params['id'] = $rmaId ;
			$params['trackMemo'] = "确认发货" ;
			$this->doSaveTrack($params) ;
		}catch(Exception $e){
			ob_clean() ;
			return $e->getMessage() ;
		}
		return "" ;
	}
	
	public function customReceiveBack($params){
		try{
			//获取下一步状态
			$rmaId= $params['rmaId'] ;
			//$policyCode = $params['policyCode'] ;
			$status = $this->getNextStatus($rmaId) ;
			$params['status'] = $status ;
 			
			$sql = "update sc_ram_event set BACK_CUSTOM_RECEVICE_DATE=NOW(),status='{@#status#}',back_Memo='{@#backMemo#}' where id='{@#rmaId#}'" ;
			$this->exeSql($sql, $params) ;
			$params['id'] = $rmaId ;
			$params['trackMemo'] = "确认客户收货" ;
			$this->doSaveTrack($params) ;
		}catch(Exception $e){
			ob_clean() ;
			return $e->getMessage() ;
		}
		return "" ;
	}
	
	public function doRefundConfrim( $params ){
		try{
			//获取下一步状态
			$rmaId= $params['rmaId'] ;
			$params['id'] = $rmaId ;
			//$policyCode = $params['policyCode'] ;
			$status = $this->getNextStatus($rmaId) ;
			$params['status'] = $status ;
			
			$sql = "UPDATE sc_ram_event 
			SET
			refund_value = '{@#refundValue#}'
			,refund_memo = '{@#refundMemo#}'
			,refund_date = NOW()
			,status = '{@#status#}'
			WHERE
			ID = '{@#id#}'" ;
			
			$this->exeSql($sql,$params) ;
		
			$params['trackMemo'] = "确认退款完成，退款金额【".$params['refundValue']."】" ;
			$this->doSaveTrack($params) ;
		}catch(Exception $e){
			ob_clean() ;
			return $e->getMessage() ;
		}
		return "" ;
	}
	
	public function getNextStatus($ramId){
		$sql = "select * from sc_ram_event where id='{@#id#}'" ;
		$ramEvent = $this->getObject($sql, array("id"=>$ramId)) ;
		$currentStatus = $ramEvent['STATUS'] ;
		$policyCode = $ramEvent['POLICY_CODE'] ;
		$policy = $this->getObject("sql_ram_options_getByCode",array('code'=>$policyCode) ) ;
		$isBack 		= $policy['IS_BACK'] ;
		$isRefund		= $policy['IS_REFUND'] ;
		$isResend 	= $policy['IS_RESEND'] ;
		
		if( $currentStatus == 45  ){//退货完成
			if( $isRefund  ){
				return 60 ;
			}
			if( $isResend  ){
				return 75 ;
			}
			return 80 ;
		}
		
		if( $currentStatus == 60  ){//退货完成
			if( $isResend  ){
				return 75 ;
			}
			return 80 ;
		}
	}
	
	public function createRam($params){
		$defaultCode = "RMA-P".date("Ymd")."-".date("His")  ;
		$sqlParams = array( "code"=>$defaultCode ,
				"purchaseId"=>$params['purchaseId'] ,
				'rmaType'=>'P',
				'rmaNum'=>$params['rmaNum'],
				"causeCode"=>$params['causeCode'] ,
				"charger"=>$params['rmaCharger'],
				"policyCode"=>'',
				"loginId"=>'',
				"status"=>'', //状态为空
				"memo"=>'' ) ;
		
		$sql = "select * from sc_ram_event where purchase_id = '{@#purchaseId#}' and cause_code='{@#causeCode#}' and rma_type='{@#rmaType#}'" ;
		$ram = $this->getObject($sql,$sqlParams ) ;
		
		if( !empty($ram) ){
			$sqlParams['id'] = $ram['ID'] ;
			$this->exeSql("sql_ram_event_update",$sqlParams) ;
		}else{
			$sqlParams['id'] = $this->create_guid() ;
			$this->exeSql("sql_ram_event_insert",$sqlParams) ;
		}
	}
	
	public function doSaveTrack($params){
		//保存跟踪意见
		$this->exeSql("sql_ram_track_insert",$params) ;
	}

}