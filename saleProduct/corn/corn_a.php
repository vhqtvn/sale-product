<?php
	 $random = date("U") ;
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/cron/amazonAsin/5/A?".$random);
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/cron/gatherAmazonCompetitions/5/A?".$random);
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/cron/gatherAmazonFba/5/A?".$random);
	 file_get_contents("http://www.smarteseller.com/saleProduct/index.php/cron/amazonShippingAsin/5/A?".$random);