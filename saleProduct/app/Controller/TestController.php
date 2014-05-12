<?php

/**
 * 执行任务列表
 */
class TestController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array("GatherData","InventoryNew");
	
	public  function purchaseInFix(){
		return ;
		debug(111111);
		$this->InventoryNew->purchaseInFix(  ) ;
	}

	/**
	 * tab也
	 */
	public function test($asin,$platformId){
		
		$gatherParams = array(
						"asin"=>$asin,
						"platformId"=>$platformId,
						"id"=>"",
						"index"=>"",
						"taskId"=>""
				) ;
		$this->GatherData->asinInfoPlatform( $gatherParams ) ;
	}
	
	
}