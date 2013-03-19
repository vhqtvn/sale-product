<?php
	/**
	 * 开始同步产品信息
	 * 
	 * @var unknown_type
	 */
	 $random = date("U") ;
	 file_get_contents("http://ultgene.com/saleProductTask/index.php/taskAsynAmazon/startAsynOrder/4?".$random);
