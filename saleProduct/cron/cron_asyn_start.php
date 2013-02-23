<?php
	/**
	 * 开始同步产品信息
	 * 
	 * @var unknown_type
	 */
	 $random = date("U") ;
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/taskAsynAmazon/startAsynAmazonProducts/5?".$random);
