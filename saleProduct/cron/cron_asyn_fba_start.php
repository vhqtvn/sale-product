<?php
	/**
	 * 开始同步产品信息 startAsynAmazonFba
	 * 
	 * @var unknown_type
	 */
	 include_once ('corn_config.php');
	 
	 $random = date("U") ;
	 file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/startAsynAmazonFba/".accountId."?".$random);
