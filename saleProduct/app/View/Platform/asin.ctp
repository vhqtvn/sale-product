<?php 
	$asin = $params['arg1'] ;

	$System  = ClassRegistry::init("System") ;
	$config = $System->getPlatformConfigByAsin($asin) ;
	
	$url = $config['AMAZON_OFFER_LISTING_URL'] ;
	$url = str_replace("{asin}", $asin, $url) ;
?>
<script type="text/javascript">
window.location.href = "<?php echo $url;?>" ;
</script>