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
	</style>
</head>
<body>
	<?php
		$realProductId = $params['arg1'] ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$product = $SqlUtils->getObject("sql_saleproduct_getByIdForStorage",array('realProductId'=>$realProductId )) ;
		
		$imgUrl = '/'.$fileContextPath.'/'.$product['IMAGE_URL'] ;
		
		$ProductDev  = ClassRegistry::init("ProductDev") ;
		$dev = $ProductDev->getLowestLimitPrice($realProductId) ;
		/*
		'SALE_LOWEST_PRICE_FBA' => '123',
		'SALE_LOWEST_PRICE_FBM' => '22',
		'SALE_SUGGEST_PRICE_FBA' => '11',
		'SALE_SUGGEST_PRICE_FBM' => '33'
		*/
	?>
	<div class="box row-fluid">
			<div class="box-content span8" style="width:96%;">
				<table class="table" style="table-layout:fixed;">
					<tr>
						<td style="width:12%;">
							<img style="width:75px;height:75px;" src="<?php echo $imgUrl;?>"/>
						</td>
						<td style="width:40%;">
								<div class="product-title"><?php echo $product['NAME'] ?></div>
								<div class="pd"><div class='pd-label'>SKU:</div><div class='pd-value'><?php echo $product['REAL_SKU'] ?></div></div>
						</td>
						<td><?php echo $product['MEMO'] ?></td>
					</tr>
					<tr>
						<td colspan=3>
							<div class="qt">
								<div class='qt-label'>总库存：</div>
								<div class='qt-value total-quantity' style="color:red;font-size:15px;"><?php echo $product['QUANTITY'] ?></div>
							</div>
							<div class="qt">
								<div class='qt-label'>安全库存：</div>
								<div class='qt-value security-quantity' style="color:red;font-size:13px;"><?php echo $product['SECURITY_QUANTITY'] ?></div>
							</div>
							<div class="qt">
								<div class='qt-label'>待发货数量：</div>
								<div class='qt-value account-will-shipped-quantity' style="color:red;font-size:13px;"></div>
							</div>
							<div class="qt">
								<div class='qt-label'>账号库存：</div>
								<div class='qt-value account-quantity' ></div>
							</div>
							<div class="qt" style="margin-bottom:0px;">
								<div class='qt-label'>可分配库存：</div>
								<div class='qt-value assignable-quantity'>--</div>
							</div>
							<div class="qt" style="margin-bottom:0px;">
								<div class='qt-label'>已分配库存：</div>
								<div class='qt-value assigned-quantity'>--</div>
							</div>
							<div class="qt">
								<button class="btn btn-primary assgin-btn btn-danger">分配库存</button>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan=3>
							<div class="qt">
								<div class='qt-label'>FBA最低限价：</div>
								<div class='qt-value SALE_LOWEST_PRICE_FBA' style="color:red;font-size:15px;"><?php echo $dev['SALE_LOWEST_PRICE_FBA'] ?></div>
							</div>
							<div class="qt">
								<div class='qt-label'>FBA销售建议价：</div>
								<div class='qt-value SALE_SUGGEST_PRICE_FBA' ><?php echo $dev['SALE_SUGGEST_PRICE_FBA'] ?></div>
							</div>
							<div class="qt">
								<div class='qt-label'>FBM最低限价：</div>
								<div class='qt-value SALE_LOWEST_PRICE_FBM' style="color:red;font-size:13px;"><?php echo $dev['SALE_LOWEST_PRICE_FBM'] ?></div>
							</div>
							<div class="qt">
								<div class='qt-label'>FBM销售建议价：</div>
								<div class='qt-value SALE_SUGGEST_PRICE_FBM' ><?php echo $dev['SALE_SUGGEST_PRICE_FBM'] ?></div>
							</div>
							
							<div class="qt">
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
