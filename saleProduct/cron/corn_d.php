<?php
include_once ('corn_config.php');

foreach( $accounts as $ac ) {
	$accountId 	= $ac['accountId'] ;
	$domain 		= $ac['domain'] ;
	$context 		= $ac['context'] ;

	$random = date("U") ;
	$url = "http://".$domain."/".$context."/index.php/gatherLevel/execute/".$accountId."/D?".$random ;

	triggerRequest($url) ;
}

//file_get_contents("http://".domain."/".context."/index.php/gatherLevel/execute/".accountId."/D?".$random);