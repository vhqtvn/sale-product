<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>产品库存分配</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
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
		echo $this->Html->script('modules/warehouse/in/assign');
		
		
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
		
		table .cell-div input{
			border:1px solid #CCC;
			width:100px;
			margin:1px 3px;
			padding:2px 3px;
			background:#EFEFEF;
		}
	</style>
</head>
<body>
	<?php
		$realProductId = $params['arg1'] ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$product = $SqlUtils->getObject("sql_saleproduct_getByIdForStorage",array('realProductId'=>$realProductId )) ;
		
		$imgUrl = '/'.$fileContextPath.'/'.$product['IMAGE_URL'] ;
		
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
								<div class='qt-label'>总库存：</div>
								<div class='qt-value' style="color:red;font-size:15px;"><?php echo $product['QUANTITY'] ?></div>
							</div>
							<div class="qt">
								<div class='qt-label'>安全库存：</div>
								<div class='qt-value' style="color:red;font-size:13px;"><?php echo $product['SECURITY_QUANTITY'] ?></div>
							</div>
							<div class="qt">
								<div class='qt-label'>锁定库存：</div>
								<div class='qt-value' style="color:red;font-size:13px;"><?php echo $product['LOCK_QUANTITY'];?></div>
							</div>
							<div class="qt">
								<div class='qt-label'>可分配库存：</div>
								<div class='qt-value'><?php echo $product['QUANTITY'] - $product['SECURITY_QUANTITY'] - $product['LOCK_QUANTITY'] ?></div>
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
