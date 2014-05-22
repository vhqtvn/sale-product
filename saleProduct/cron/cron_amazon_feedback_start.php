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
	$url = "http://".$domain."/".$context."/index.php/taskAsynAmazon/getFeedReport1/".$accountId."/_GET_SELLER_FEEDBACK_DATA_?".$random ;

	sock_get($url) ;

	sleep(10) ;
}
//	 file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/startAsynOrder/".accountId."?".$random);