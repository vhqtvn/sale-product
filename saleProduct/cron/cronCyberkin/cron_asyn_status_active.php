<?php
     /**
      * 同步活动产品明细
      * @var unknown_type
      */
	 $random = date("U") ;
	 file_get_contents("http://ultgene.com/saleProductTask/index.php/taskAsynAmazon/asynAmazonActiveProducts/4?".$random);
