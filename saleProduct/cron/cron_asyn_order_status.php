<?php
     /**
      * 同步产品状态
      * 
      * @var unknown_type
      */
	 $random = date("U") ;
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/taskAsynAmazon/asynOrder/5?".$random);
