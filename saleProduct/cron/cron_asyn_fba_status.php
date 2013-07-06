<?php
     /**
      * 同步产品状态 asynAmazonFba
      * 
      * @var unknown_type
      */
	 include_once ('corn_config.php');
	 
	foreach( $accounts as $ac ) {
	$accountId 	= $ac['accountId'] ;
	$domain 		= $ac['domain'] ;
	$context 		= $ac['context'] ;

	$random = date("U") ;
	$url = "http://".$domain."/".$context."/index.php/taskAsynAmazon/asynAmazonFba/".$accountId."?".$random ;

	sock_get($url) ;
}
//	 file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/asynAmazonFba/".accountId."?".$random);
	 
