<?php
     /**
      * 同步活动产品明细
      * @var unknown_type
      */
	 include_once ('corn_config.php');
	 
foreach( $accounts as $ac ) {
	$accountId 	= $ac['accountId'] ;
	$domain 		= $ac['domain'] ;
	$context 		= $ac['context'] ;

	$random = date("U") ;
	$url = "http://".$domain."/".$context."/index.php/taskAsynAmazon/asynAmazonActiveProducts/".$accountId."?".$random ;

	triggerRequest($url) ;
}
//file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/asynAmazonActiveProducts/".accountId."?".$random);
	 