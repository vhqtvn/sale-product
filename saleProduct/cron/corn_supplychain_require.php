<?php
ignore_user_abort(1);
set_time_limit(0);

include_once ('corn_config.php');

$random = date("U") ;
$url = "http://www.smarteseller.com/saleProduct/index.php/cronTask/createAmazonRequirement?".$random ;

sock_get($url) ;
	