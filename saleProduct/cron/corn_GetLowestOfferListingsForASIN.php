<?php
	/**
	 * 获取商品的最低价
	 * 
	 * @DB  sc_amazon_account_product(lowest_price,lowest_fba_price)
	 * 
	 * @var unknown_type
	 */
	 include_once ('corn_config.php');
	 

	foreach( $accounts as $ac ) {
		$accountId 	= $ac['accountId'] ;
		$domain 		= $ac['domain'] ;
		$context 		= $ac['context'] ;
	
		$random = date("U") ;
		$url = "http://".$domain."/".$context."/index.php/taskAsynAmazon/getLowestOfferListingsForASIN/".$accountId."?".$random ;
	
		 sock_get($url) ;
	}
	//	 file_get_contents("http://".domain."/".context."/index.php/taskAsynAmazon/startAsynAmazonFba/".accountId."?".$random);
