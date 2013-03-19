<?php
     /**
      * 同步产品状态 asynAmazonFba
      * 
      * @var unknown_type
      */
	 $random = date("U") ;
	 file_get_contents("http://ultgene.com/saleProductTask/index.php/taskAsynAmazon/asynAmazonFba/4?".$random);
