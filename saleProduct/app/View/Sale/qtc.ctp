<?php 
	include_once ('config/config.php');

	$qtc = $params['arg1'] ;
	
	$SqlUtils  = ClassRegistry::init("SqlUtils") ;
	$qtc = $SqlUtils->getObject("select * from sc_purchase_task_products where qtc='{@#qtc#}'",array("qtc"=>$qtc)) ;
	
?>

<script>
	window.location.href = contextPath+"/page/forward/Sale.edit_purchase_task_product/<?php echo $qtc['PRODUCT_ID'];?>/<?php echo $qtc['TASK_ID'];?>" ;
</script>