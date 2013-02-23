<?php
     /**
      * 同步产品状态 asynAmazonFba
      * 
      * @var unknown_type
      */
	 $random = date("U") ;
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/taskAsynAmazon/asynAmazonFba/5?".$random);
