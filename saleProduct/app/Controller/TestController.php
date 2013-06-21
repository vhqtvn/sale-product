<?php

/**
 * 执行任务列表
 */
class TestController extends AppController {
	
	public $helpers = array (
		'Html',
		'Form'
	); //,'Ajax','Javascript
	
	var $uses = array("GatherData");
	
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