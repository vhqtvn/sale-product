<?php
	/**
	 * 开始同步有效产品信息
	 * 
	 * @var unknown_type
	 */
	 $random = date("U") ;
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/taskAsynAmazon/startAsynAmazonActiveProducts/5?".$random);
