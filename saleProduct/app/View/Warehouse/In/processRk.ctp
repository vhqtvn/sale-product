<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>处理</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/warehouse/in/process');
	?>
  
   <script type="text/javascript">
   	var inId = '<?php echo $params['arg1'] ;?>' ;	
   	var status = '<?php echo $params['arg2'] ;?>' ;	
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
	
	<script type="text/javascript">
	
		
	</script>

</head>
<body>
	<div class="flow-toolbar toolbar">
		<table style="width:380px;margin:0px auto;">
			<tr>
				<td>
					<button class="btn btn-primary sh" disabled style="font-size:20px;padding:5px 10px;">收货确认</button>
				</td>
				<td>
					<div style="font-size:50px;font-weight:bold;margin-top:-10px;">———</div>
				</td>
				<td>
					<button class="btn btn-primary rh" disabled style="font-size:20px;padding:5px 10px;">货品入库</button>
				</td>
			</tr>
		</table>
	</div>
	
	
	<?php
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$boxs = $SqlUtils->exeSql("sql_warehouse_box_lists",array('inId'=>$params['arg1'] )) ;
		
		//计划单状态
		$inStatus = true;
		
		foreach( $boxs as $box ){
	?>
	<div class="box row-fluid">
	<?php	
			$box = $box['sc_warehouse_box'] ;
	?>
	<!--render box-->
		<div class="box-content span3" style="width:20%;">
			<div class="box-content-label">包装箱编号：</div>
			<div class="box-content-value"><?php echo $box['BOX_NUMBER']; ?></div>
			<div class="box-content-label">重量：</div>
			<div class="box-content-value"><?php echo $box['WEIGHT']; ?></div>
			<div class="box-content-label">尺寸：</div>
			<div class="box-content-value"><?php echo $box['LENGTH'].' * '.$box['WIDTH'].' * '.$box['HEIGHT']; ?></div>
			<div class="box-content-label">备注：</div>
			<div class="box-content-value"><?php echo $box['MEMO']; ?></div>
		</div>
		<div class="box-content span8" style="width:70%;">
			<table class="table" style="table-layout:fixed;">
		<?php
			$boxId = $box['ID'] ;
			$boxProducts = $SqlUtils->exeSql("sql_warehouse_box_products",array('boxId'=>$boxId )) ;
			foreach($boxProducts as $product){
				$product = $SqlUtils->formatObject($product) ;
				$imgUrl = '/saleProduct/'.$product['IMAGE_URL'] ;
			?>
				<tr>
					<td style="width:12%;">
						<img style="width:75px;height:75px;" src="<?php echo $imgUrl;?>"/>
					</td>
					<td style="width:30%;">
							<div class="product-title"><?php echo $product['NAME'] ?></div>
							<div class="pd"><div class='pd-label'>SKU:</div><div class='pd-value'><?php echo $product['SKU'] ?></div></div>
							<div class="pd"><div class='pd-label'>跟踪码：</div><div class='pd-value'><?php echo $product['PRODUCT_TRACKCODE'] ?></div></div>
							<div class="pd"><div class='pd-label'>供货时间：</div><div class='pd-value'><?php echo $product['DELIVERY_TIME'] ?></div></div>
					</td>
					<td style="width:25%;"><?php echo $product['P_MEMO'] ?></td>
					<td style="width:20%;">
						<div class="qt">
							<div class='qt-label'>数量：</div>
							<div class='qt-value'><?php echo $product['QUANTITY'] ?>
							<?php
								if(!empty($product['WASTE_QUANTITY'])){
							?>
									<span style="color:red;">(<?php echo $product['WASTE_QUANTITY'] ?>)</span>
							<?php		
								}
							?>
							</div>
						</div>
						<?php
							if( $product['STATUS'] == 1 ){
								echo '<div style="text-align:center;margin:5px;clear:both;"><img src="/saleProduct/app/webroot/img/m/button-check.png"/></div>' ;
								echo '<button class="btn report-exception" style="margin-top:2px;margin-left:10px;">查看异常</button>' ;
							}else{
								$inStatus = false ;
								echo '<button class="btn btn-success btn-validator-product" style="margin-top:2px;margin-left:10px;">确认验货</button>' ;
								echo '<button class="btn btn-danger report-exception" style="margin-top:2px;margin-left:10px;">报告异常</button>' ;
							}
						?>
						
					</td>
				</tr>
				<tr class="exception-error" style="display:none;">
					<input type="hidden" name="id" value="<?php echo $product['ID'];?>" />
					<input type="hidden" name="quantity" value="<?php echo $product['QUANTITY'] ?>" />
					<td colspan=3>
						<input type="text" name="wasteQuantity" value="<?php echo $product['WASTE_QUANTITY'] ?>" placeholder="残品数量" class="alert-danger" 
							style="width:67px;margin-top:2px;margin-left:20px;"/>
						<textarea style="width:300px;" name="exceptionMemo" placeholder="备注信息"><?php echo $product['EXCEPTION_MEMO'] ?></textarea>
					</td>
					<td>
						<?php
							if( $product['STATUS'] == 1 ){
							}else{
								echo '<button class="btn btn-danger save-waste">保存异常信息</button>' ;
							}
						?>
						
					</td>
				</tr>
			<?php			
			} 
			?>
			</table>
		</div>
	</div>
	<?php	
		} ;
	?>
	<script>
	 $inStatus = '<?php echo $inStatus?>' ;
	</script>
</body>
</html>
