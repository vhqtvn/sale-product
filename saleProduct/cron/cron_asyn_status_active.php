<?php
     /**
      * 同步活动产品明细
      * @var unknown_type
      */
	 include_once ('corn_config.php');
	 
	 $random = date("U") ;
	 file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/asynAmazonActiveProducts/".accountId."?".$random);
	 