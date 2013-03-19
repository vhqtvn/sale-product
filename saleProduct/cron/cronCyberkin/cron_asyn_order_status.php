<?php
     /**
      * 同步产品状态
      * 
      * @var unknown_type
      */
	 $random = date("U") ;
	 file_get_contents("http://ultgene.com/saleProductTask/index.php/taskAsynAmazon/asynOrder/4?".$random);
