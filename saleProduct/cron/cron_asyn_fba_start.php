<?php
	/**
	 * 开始同步产品信息 startAsynAmazonFba
	 * 
	 * @var unknown_type
	 */
	 $random = date("U") ;
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/taskAsynAmazon/startAsynAmazonFba/5?".$random);
