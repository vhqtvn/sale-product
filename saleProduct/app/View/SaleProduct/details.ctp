<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品信息</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>


   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tab/jquery.ui.tabs');
		
		$security  = ClassRegistry::init("Security") ;
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["LOGIN_ID"] ;
		$hasViewRelListing = $security->hasPermission($loginId , 'view_rp_rel_listing') ;
		if(  !isset($action) ){
			$action = "edit" ;
		}
		
		$product_stock_quanity_assign = $security->hasPermission($loginId , 'product_stock_quanity_assign') ;
	?>
   <script>
   
   var action ='<?php echo $action;?>' ;
  	$(function(){
  		var realId ='<?php echo $id;?>' ;
  		
  		var tab = $('#tabs-default').tabs( {//$this->layout="index";
			tabs:[
				{label:'基本信息',url:contextPath+"/saleProduct/forward/edit_product/"+realId,iframe:true}
				<?php if($hasViewRelListing){ ?>
				,{label:'渠道产品信息',url:contextPath+"/saleProduct/forward/channel/"+realId,iframe:true}
				<?php }?>
				<?php if($item['TYPE'] == 'package'){?>
					,{label:'打包货品信息',url:contextPath+"/saleProduct/forward/composition/"+realId,iframe:true}
				<?php } ?>
				,{label:'供应商信息',url:contextPath+"/page/forward/Supplier.listsBySku/<?php echo $item['REAL_SKU'];?>",iframe:true}
				,{label:'产品成本',url:contextPath+"/page/forward/Cost.listBySku/<?php echo $item['REAL_SKU'];?>",iframe:true}
				<?php  if( $product_stock_quanity_assign ){?>
				,{label:'库存价格调整',url:contextPath+"/page/forward/Warehouse.In.assign/<?php echo $item['ID'];?>",iframe:true}
				,{label:'出入库',url:contextPath+"/page/forward/Warehouse.In.storageDetails/<?php echo $item['ID'];?>",iframe:true}
				<?php }?>
				,{label:'历史询价',url:contextPath+"/page/forward/SaleProduct.supplierInquiryHistory/<?php echo $item['REAL_SKU'];?>",iframe:true}
				,{label:'销量图',url:contextPath+"/page/forward/SaleProduct.saleChart/<?php echo $item['REAL_SKU'];?>",iframe:true}
			] ,
			height:'588x'
		} ) ;
  	})
  </script>

</head>
<body style="overflow-y:auto;padding:2px;">
	<div id="tabs-default" class="view-source">
	</div>
</body>

</html>