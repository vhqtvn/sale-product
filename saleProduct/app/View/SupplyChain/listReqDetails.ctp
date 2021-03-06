<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>需求明细</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<script>
		var deleteHtml = "" ;
	</script>
   <?php
		include_once ('config/config.php');
  		include_once ('config/header.php');
  		
		echo $this->Html->script('modules/supplychain/listReqDetails');
		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		$planId = $params['arg1'] ;
		$realId = $params['arg1'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		//获取计划
		$sql = "select * from sc_supplychain_requirement_plan where id = '{@#id#}'" ;
		$plan = $SqlUtils->getObject($sql,array('id'=>$planId)) ;
		
		/*if( $plan['STATUS'] == '' || $plan['STATUS'] == 0 ){
			//更新计划到产品
			$Requirement  = ClassRegistry::init("ScRequirement") ;
			//debug($Requirement) ;
			$Requirement->transferPlanItem2Product($planId) ;
		}*/

		$security  = ClassRegistry::init("Security") ;
		
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["LOGIN_ID"] ;
		
		$product_add = $security->hasPermission($loginId , 'product_add') ;
		$product_edit = $security->hasPermission($loginId , 'product_edit') ;
		$product_giveup = $security->hasPermission($loginId , 'product_giveup') ;
		$view_giveup_product = $security->hasPermission($loginId , 'view_giveup_product') ;
		$product_stock_quanity_assign = $security->hasPermission($loginId , 'product_stock_quanity_assign') ;
		//销售状态变更权限
		$product_onsale =  $security->hasPermission($loginId , 'product_onsale') ;

		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["GROUP_CODE"] ;
		if($loginId == 'general_manager'){
		?>
		<script>
			var deleteHtml = "<a href='#' class='action giveup btn'   type=3>删除</a>" ;
		</script>
		<?php
		}
	?>
	
	<script type="text/javascript">
		$product_edit = <?php echo $product_edit?'true':'false';?> ;
		$product_giveup = <?php echo $product_giveup?'true':'false';?> ;
		$view_giveup_product = <?php echo $view_giveup_product?'true':'false';?> ;
		$product_stock_quanity_assign = <?php echo $product_stock_quanity_assign?'true':'false';?> ;
		$product_onsale = <?php echo $product_onsale?'true':'false';?> ;
		var realId = '<?php echo $realId;?>' ;
		var reqProductId = '<?php echo $params['arg2'];?>' ;

   </script>
   
   <style>
   	.span1_5{
		width:100px;
   	}
   	
   	.track-list {
		position:absolute;
   	    right:30px;
   	    top:320px;
   	    max-height:150px;
   	    border:1px solid #CCC;
   	    background: #FFF;
   	}
   </style>

</head>
<body>
  <div  style="width:100%;height:100%;">
		<div region="center" split="true" border="true"  style="padding:2px;">

			<div class="grid-content"></div>
					
			<div class="row-fluid">
						<div class="grid-content-details"  ></div>
					
			</div>
			
		</div>
		<div region="west"  split="true" border="true" title="货品分类" style="width:180px;display:none;">
			<div id="tree-wrap">
				<div id="default-tree" class="tree" style="padding: 5px; "></div>
			</div>
		</div>
		
		<div  class="track-list hide">
		</div>
   </div>	
</body>
</html>
