<?php
include_once ('corn_config.php');

foreach( $ebayAccounts as $ac ) {
	$accountId 	= $ac['accountId'] ;
	$domain 		= $ac['domain'] ;
	$context 		= $ac['context'] ;

	$random = date("U") ;
	$url = "http://".$domain."/".$context."/index.php/eBay/getMyMessagesHeader/".$accountId."/-?".$random ;

	sock_get($url) ;
}
