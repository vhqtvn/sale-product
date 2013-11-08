<?php 
	/**
	 * 清除限价数据
	 */

$random = date("U") ;
$url = "http://www.smarteseller.com/saleProduct/index.php/taskAsynAmazon/startAsynAmazonFba/".$accountId."?".$random ;

sock_get($url) ;

?>