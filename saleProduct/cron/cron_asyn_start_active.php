<?php
	/**
	 * 开始同步有效产品信息
	 * 
	 * @var unknown_type
	 */
	 include_once ('corn_config.php');
	 
	 $random = date("U") ;
	 file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/startAsynAmazonActiveProducts/".accountId."?".$random);