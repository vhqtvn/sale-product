<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>产品库存分配</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
  		include_once ('config/header.php');
  		
		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		echo $this->Html->script('modules/warehouse/in/assign');

		
		
		$realProductId = $params['arg1'] ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$product = $SqlUtils->getObject("sql_saleproduct_getByIdForStorage",array('realProductId'=>$realProductId )) ;
		
		//获取成本
		
		$imgUrl = '/'.$fileContextPath.'/'.$product['IMAGE_URL'] ;
		//sql_cost_product_details_list
		//$fbaCost = $SqlUtils->getObject("sql_cost_product_details_list",array('realSku'=>$product['REAL_SKU'],'type'=>'FBA' )) ;
		//$fbmCost = $SqlUtils->getObject("sql_cost_product_details_list",array('realSku'=>$product['REAL_SKU'],'type'=>'FBM' )) ;
		
		//$fbaTotalCost = empty($fbaCost)?"-":$fbaCost['TOTAL_COST'] ;
		//$fbmTotalCost = empty($fbmCost)?"-":$fbmCost['TOTAL_COST'] ;

		//	$ProductDev  = ClassRegistry::init("ProductDev") ;
		//	$dev = $ProductDev->getLowestLimitPrice($realProductId) ;
		/*
		 'SALE_LOWEST_PRICE_FBA' => '123',
		'SALE_LOWEST_PRICE_FBM' => '22',
		'SALE_SUGGEST_PRICE_FBA' => '11',
		'SALE_SUGGEST_PRICE_FBM' => '33'
		*/
		
	?>
  	<script>
  		var realProductId = '<?php echo $realProductId;?>' ;
  		var reslSku = '<?php echo $product['REAL_SKU'] ?>' ;
  	</script>
  	
  	<style type="text/css">
		.box{
			margin-top:10px;
			margin-bottom:10px;
			
		}
		
		.box-content{
			border:1px solid #CCC;
			-moz-border-radius: 11px;
			-khtml-border-radius: 11px;
			-webkit-border-radius: 11px;
			border-radius: 11px;
			padding:10px;
			background:#EEE;
			
		}
		
		.box-content .box-content-label{
			font-weight:bold;
			padding-top:10px;
			font-size:15px;
			padding-bottom:5px;
		}
		
		.box-content .box-content-value{
			font-weight:bold;
			font-size:20px;
			padding-bottom:5px;
			color:blue;
		}
		
		.product-title{
			font-weight:bold;
			font-size:15px;
			color:#000;
			padding-bottom:5px;
		}
		
		.pd>div{
			float:left;
		}
		.qt{float:left;padding:2px 6px;border-right:2px solid #CCC;}
		.pd{padding-top:3px;}
		.pd .pd-label{
			width:40%;
			font-weight:bold;
			font-size:12px;
		}
		
		.pd .pd-value{
			width:60%;
			font-size:12px;
		}
		
		.qt>div{
			float:left;
		}
		
		.qt-value{
			font-size:20px;
			font-weight:bold;
			color:blue;
		}
		
		table .cell-div input{
			border:1px solid #CCC;
			width:50px;
			margin:1px 3px;
			padding:2px 3px;
			/*background:#EFEFEF;*/
		}
		
		.nopass{
			background:red;
		}
		
		.pass{
			background:green;
			color:#FFF!important;
		}
		
	.popover-inner  .popover-title{
			font-size:12px;
   		}
	</style>
</head>
<script>
	$(function(){
		//DynTag.listByEntity("productTag",'<?php echo $realProductId;?>') ;

		DynTag.listByType({entityType:'listingTag',subEntityType:'<?php echo $realProductId;?>'},function(entityType,tagId){
	    	 $(".grid-content").llygrid("reload",{tagId:tagId},true) ;
		}) ;
	}) ;
</script>
<body>
	<div class="box row-fluid">
			<div class="box-content span8" style="width:96%;">
				<table class="table" style="table-layout:fixed;">
					
					<tr>
						<td colspan=4>
							<div class="qt">
								<button class="btn btn-primary assgin-btn btn-danger"  disabled>分配库存</button>
								<button class="btn btn-primary price-btn btn-danger">调整价格</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
	</div>
	
	<div id="details_tab"></div>
	
	<div class="grid-content" style="width:99%;margin-top:5px" id="assign-grid"></div>
	
	<div class="usinggrid-content" style="width:99%;margin-top:5px" id="using-grid"></div>
</body>
</html>
