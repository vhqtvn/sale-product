<?php
     /**
      * 同步活动产品明细
      * @var unknown_type
      */
	 $random = date("U") ;
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/taskAsynAmazon/asynAmazonActiveProducts/5?".$random);
