<?php
	/**
	 * 开始同步产品信息
	 * 
	 * @var unknown_type
	 */
	 include_once ('corn_config.php');
	 
foreach( $accounts as $ac ) {
	$accountId 	= $ac['accountId'] ;
	$domain 		= $ac['domain'] ;
	$context 		= $ac['context'] ;

	$random = date("U") ;
	$url = "http://".$domain."/".$context."/index.php/taskAsynAmazon/startAsynAmazonProducts/".$accountId."?".$random ;

	triggerRequest($url) ;
}
//file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/startAsynAmazonProducts/".accountId."?".$random);