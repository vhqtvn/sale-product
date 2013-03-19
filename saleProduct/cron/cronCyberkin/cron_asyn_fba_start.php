<?php
	/**
	 * 开始同步产品信息 startAsynAmazonFba
	 * 
	 * @var unknown_type
	 */
	 $random = date("U") ;
	 file_get_contents("http://ultgene.com/saleProductTask/index.php/taskAsynAmazon/startAsynAmazonFba/4?".$random);
