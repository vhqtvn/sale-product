<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>采购货品入库</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/inventory/in_purchase');
		echo $this->Html->script('calendar/WdatePicker');
		
		$purchaseProductId = $params['arg1'] ;
		$realId =  $params['arg2'] ;
		$reqProductId = $params['arg3'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		
		//获取需求对应产品
		//debug($reqPlanId) ;
		$result = $SqlUtils->exeSqlWithFormat("sql_supplychain_requirement_plan_product_details_list" ,array("reqProductId"=>$reqProductId,"realId"=>$realId)) ;
		//debug($result) ;
		
		$purchaseProduct =  $SqlUtils->getObject("select * from sc_purchase_product
				 where id= '{@#productId#}'", array( "productId"=>$purchaseProductId)) ;

		$warehouseId = $purchaseProduct['WAREHOUSE_ID'] ;
		$sql = "select * from sc_warehouse where id = '{@#WAREHOUSE_ID#}'" ;
		$warehouse = $SqlUtils->getObject($sql,$purchaseProduct ) ;
		
		$status =$purchaseProduct['STATUS'] ;
		
	?>
  
   <script type="text/javascript">
	   	var purchaseProductId = '<?php echo $params['arg1'] ;?>' ;	
	   	var warehouseId =  '<?php echo $purchaseProduct['WAREHOUSE_ID'] ;?>' ;	
   </script>
   
    <style type="text/css">
		.exception-form{
			background:#ffc0cb ;
			border:1px solid #CCC;
			padding:10px;
			text-align:left;
			display:none;
			position:absolute;
			top:32px;
			right:0px;
			z-index:1;
			text-align:right;
		}
		
		.exception-form div{
			margin:3px;
		}
		
		.box{
			margin-top:10px;
		}
		
		.box-content{
			border:1px solid #CCC;
			-moz-border-radius: 11px;
			-khtml-border-radius: 11px;
			-webkit-border-radius: 11px;
			border-radius: 11px;
			padding:10px;
			
		}
		
		.box-content.span3{
			background:#AABBCC;
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
		.pd,.qt{clear:both;padding-top:3px;}
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
	</style>
</head>
<body>
	

	<div class="flow-toolbar toolbar">
		<input type="hidden" name="realId" value="<?php echo $realId;?>"/>
		<input type="hidden"  name="qualifiedProductsNum"  value="<?php echo $purchaseProduct['QUALIFIED_PRODUCTS_NUM'] ; ?>"/>
		<input type="hidden"  name="badProductsNum"  value="<?php echo $purchaseProduct['BAD_PRODUCTS_NUM'] ; ?>"/>
		<input type="hidden" name="purchaseProductId" value="<?php echo $purchaseProductId;?>"/>
	
		<div class="row-fluid">
			<div class="span10 "  style="font-weight:bold;font-size:15px;">
				   良品产品数量：<span class="alert-success" style="padding:1px 5px;"><?php echo $purchaseProduct['QUALIFIED_PRODUCTS_NUM'] ; ?></span>
				   &nbsp; &nbsp; 
					残品产品数量：<span class="alert-danger" style="padding:1px 5px;"><?php echo $purchaseProduct['BAD_PRODUCTS_NUM'] ; ?></span>
					&nbsp; &nbsp; 
					自由库存：<input type="text"  style="width:50px;"   name="freeQuantity"  value="" <?php echo $status>=70?"disabled":"" ?>/>
					&nbsp; &nbsp; 
					<div style="height:10px;">&nbsp;</div>
					入库仓库：<select   id="warehouseId"  style="width:150px;"  <?php echo $status>=70?"disabled":"" ?>>
										    	<option value="">--选择--</option>
											   <?php 
											     // sql_warehouse_lists
											     $warehouses = $SqlUtils->exeSql("sql_warehouse_lists",array()) ;
					                             foreach($warehouses as $w){
					                             	  $w = $SqlUtils->formatObject( $w ) ;
					                             	  $selected = $purchaseProduct['WAREHOUSE_ID'] == $w['ID'] ?"selected":"" ;
					                             	  echo "<option $selected value='".$w['ID']."'>".$w['NAME']."</option>" ;
					                             }
											   ?>
											</select>
						入库时间： <?php if($status>=70){ 
							echo $purchaseProduct['WAREHOUSE_TIME'];
						}else{ ?>
						<input id="warehouseTime" class="60-input input"  data-options="{'isShowWeek':'true','dateFmt':'yyyy-MM-dd HH:mm:ss'}"
													  type="text"  data-widget="calendar"
															value='<?php 
															if( $purchaseProduct['WAREHOUSE_TIME'] == "0000-00-00 00:00:00"){
																echo "";
															} else{
																echo $purchaseProduct['WAREHOUSE_TIME'];
															};?>' />
					<?php }?>
			</div>
			<div class="span2">
			<?php if($status>=70){?>
				 <div class='alert-success'   style="font-size:20px;padding:6px 0px 6px 15px;margin-top:15px;"> 入库完成</div> 
			<?php }else{ ?>
				<button class="btn btn-danger btn-confirm-in"  style="font-size:20px;padding:6px 2px;margin-top:15px;">确认入库</button>
			<?php }?>
			</div>
		</div>
	</div>
	
	<div  class="alert alert-error  hide"  style="margin-top:5px;"></div>
	
	<div class="box row-fluid">
			<div class="box-content span8" style="width:96%;">
				<table class="table" style="table-layout:fixed;">
			<?php
				foreach($result as $product){
					
					$imgUrl = '/'.$fileContextPath.'/'.$product['IMAGE_URL'] ;
					$quantity = $product['PURCHASE_QUANTITY'];
					if( $status>=70 ){
						$quantity =  $product['REAL_PURCHASE_QUANTITY'];
					}
				?>
					<tr class="rk-product-row">
						<td style="width:12%;">
							
							<input type="hidden" name="accountId" value="<?php echo $product['ACCOUNT_ID'];?>"/>
							<input type="hidden" name="planId" value="<?php echo $product['PLAN_ID'];?>"/>
							<input type="hidden" name="listingSku" value="<?php echo $product['LISTING_SKU'] ?>"/>
							<input type="hidden" name="planPurchaseQuantity" value="<?php echo $product['PURCHASE_QUANTITY'];?>"/>
							<input type="hidden" name="channel" value="<?php echo $product['FULFILLMENT_CHANNEL'] ?>"/>
							<?php if( !empty($product['IMAGE_URL'] ) ){ ?>
							<img style="width:75px;height:75px;" src="<?php echo $imgUrl;?>"/>
							<?php } ?>
						</td>
						<td style="width:30%;">
								<div class="pd">
									<div class='pd-label'>Listing SKU:</div>
									<div class='pd-value'><?php echo $product['LISTING_SKU'] ?></div>
								</div>
								<div class="pd">
									<div class='pd-label'>账号： </div>
									<div class='pd-value'><?php echo $product['ACCOUNT_NAME'] ?></div>
								</div>
								<div class="pd">
									<div class='pd-label'>渠道： </div>
									<div class='pd-value'><?php echo $product['FULFILLMENT_CHANNEL'] ?> </div>
								</div>
								<div class="pd">
									<div class='pd-label'>优先级： </div>
									<div class='pd-value'><?php echo $product['URGENCY'] ?></div>
								</div>
						</td>
						<td style="width:25%;">
							<div class="pd">
									<div class='pd-label'>计划采购数量：</div>
									<div class='pd-value'><?php echo $product['PURCHASE_QUANTITY'];?> </div>
							</div>
							<br/><br/>
							<div class="pd">
									<div class='pd-label'  style="padding-top:5px;">实际入库数量：</div>
									<div class='pd-value'><input type="text" name="purchaseQuantity"  style="width:50px;" value="<?php echo $quantity;?>" <?php echo $status>=70?"disabled":"" ?>/></div>
								</div>
						</td>
						
					</tr>
				<?php			
				} 
				?>
				</table>
			</div>
		</div>
</body>
</html>
