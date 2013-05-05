<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>打印</title>
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
		echo $this->Html->script('modules/warehouse/in/process');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$in = $SqlUtils->getObject("sql_warehouse_in_getById",array('id'=>$params['arg1'] )) ;
		
		$status = $in['STATUS'] ;//50 验货 60 收货
		
		$type = $status=='50'?'sh':'rk' ;
	?>
  
   <script type="text/javascript">
	   	var inId = '<?php echo $params['arg1'] ;?>' ;	
	   	var type = '<?php echo $type ;?>' ;	
	   	var warehouseId =  '<?php echo $in['WAREHOUSE_ID'] ;?>' ;	
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
			width:30%;
			font-size:12px;
		}
		
		.pd .pd-value{
			width:70%;
			font-size:12px;
		}
		
		.qt>div{
			float:left;
		}
		
		.qt-value{
			font-weight:bold;
			color:blue;
		}
	</style>
</head>
<body>
		
		<div class="box row-fluid">
				<?php 
				$warehoseIn = $SqlUtils->getObject("sql_warehouse_in_getById",array("id"=>$params['arg1']  )) ;//获取
				?>
			<div class="base-info">
				
			</div>
			<div class="box-content span8" style="width:96%;">
				<table class="table" style="table-layout:fixed;">
			<?php
				$inProducts = $SqlUtils->exeSql("sql_warehouse_in_products",array('inId'=>$params['arg1'] )) ;
				$inStatus = true ;
			
				foreach($inProducts as $product){
					$product = $SqlUtils->formatObject($product) ;
				
					$imgUrl = '/'.$fileContextPath.'/'.$product['IMAGE_URL'] ;
					
					/*'IN_ID', 
					'WAREHOUSE_ID', 
					'REAL_PRODUCT_ID', 
					'QUANTITY', 
					'CREATE_TIME', 
					'CREATOR', 
					'DELIVERY_TIME'*/
				?>
					<tr class="rk-product-row">
						<td style="width:12%;">
							<?php if( !empty($product['IMAGE_URL'] ) ){ ?>
							<img style="width:75px;height:75px;" src="<?php echo $imgUrl;?>"/>
							<?php } ?>
						</td>
						<td style="width:30%;">
								<div class="product-title"><?php echo $product['NAME'] ?></div>
								<div class="pd"><div class='pd-label'>SKU:</div><div class='pd-value'><?php echo $product['SKU'] ?></div></div>
						</td>
						<td style="width:25%;">
						<?php echo $product['MEMO'] ?>
						</td>
						<td style="width:20%;">
							<div class="qt">
								<div class='qt-label'>合格数量：</div>
								<div class='qt-value'><?php echo $product['GEN_QUANTITY'] ?></div>
									<?php 
											if( $product['INVENTORY_TYPE'] == 1 ){
												echo "<div style='font-weight:bold;margin-left:10px;;'>（普通库存）</div>" ;
											}else if( $product['INVENTORY_TYPE'] == 2 ){
												echo "<div  style='font-weight:bold;'>（FBA库存）</div>" ;
											}
								  ?>
							</div>
							<?php if(!empty($product['WASTE_QUANTITY'])){ ?>
							<div class="qt" style="clear:left;margin-top:30px;">
								<div class='qt-label'>残品数量：</div>
								<div class='qt-value'>
										<span style="color:red;"><?php echo $product['WASTE_QUANTITY'] ?></span>
								</div>
							</div>
							<?php	 } ?>
								
						</td>
					</tr>
				<?php			
				} 
				?>
				</table>
			</div>
		</div>
		<script>
		 $inStatus = '<?php echo $inStatus?>' ;
		</script>
</body>
</html>
