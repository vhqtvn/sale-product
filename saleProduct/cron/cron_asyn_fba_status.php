<?php
     /**
      * 同步产品状态 asynAmazonFba
      * 
      * @var unknown_type
      */
	 include_once ('corn_config.php');
	 
	 $random = date("U") ;
	 file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/asynAmazonFba/".accountId."?".$random);
	 
