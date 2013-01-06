<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>出入库明细</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('modules/warehouse/in/storageDetails');
		
		$realProductId = $params['arg1'] ;
	?>
  	<script>
  		var realProductId = '<?php echo $realProductId;?>' ;
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
			background:#EEBBCC;
			
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
	<?php
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$product = $SqlUtils->getObject("sql_saleproduct_getById",array('realProductId'=>$params['arg1'] )) ;
		
		$imgUrl = '/saleProduct/'.$product['IMAGE_URL'] ;
		
		//debug($product) ;
	?>
	<div class="box row-fluid">
			<div class="box-content span8" style="width:96%;">
				<table class="table" style="table-layout:fixed;">
					<tr>
						<td style="width:12%;">
							<img style="width:75px;height:75px;" src="<?php echo $imgUrl;?>"/>
						</td>
						<td style="width:30%;">
								<div class="product-title"><?php echo $product['NAME'] ?></div>
								<div class="pd"><div class='pd-label'>SKU:</div><div class='pd-value'><?php echo $product['REAL_SKU'] ?></div></div>
						</td>
						<td style="width:25%;"><?php echo $product['MEMO'] ?></td>
						<td style="width:20%;">
							<div class="qt">
								<div class='qt-label'>库存数量：</div>
								<div class='qt-value'><?php echo $product['QUANTITY'] ?></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
	</div>			
	<div id="details_tab"></div>
	
	<div class="grid-content" style="width:99%;margin-top:5px" id="assign-grid"></div>
	
	<div class="ordergrid-content" style="width:99%;margin-top:5px" id="order-grid"></div>
</body>
</html>
