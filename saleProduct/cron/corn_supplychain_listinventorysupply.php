<?php
	/**
	 * 开始同步产品信息 startAsynAmazonFba
	 * 
	 * @var unknown_type
	 */
include_once ('corn_config.php');
	 

foreach( $accounts as $ac ) {
	$accountId 	= $ac['accountId'] ;
	$domain 		= $ac['domain'] ;
	$context 		= $ac['context'] ;

	$random = date("U") ;
	$url = "http://".$domain."/".$context."/index.php/taskAsynAmazon/listInventorySupply/".$accountId."?".$random ;

	sock_get($url) ;
}