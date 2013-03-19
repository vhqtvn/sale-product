<?php
	/**
	 * 开始同步产品信息
	 * 
	 * @var unknown_type
	 */
	 $random = date("U") ;
	 file_get_contents("http://ultgene.com/saleProductTask/index.php/taskAsynAmazon/startAsynAmazonProducts/4?".$random);
