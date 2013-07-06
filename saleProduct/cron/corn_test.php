<?php
ignore_user_abort(1);
set_time_limit(0);

include_once ('corn_config.php');

$random = date("U") ;
	$url = "http://127.0.0.1/saleProduct/index.php/accountStrategy/adjustPrice?".$random ;
	//	file_get_contents( $url ) ;
	sock_get($url) ;
	
