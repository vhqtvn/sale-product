<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
   <?php $orderId =$params['arg1'] ; ?>
    <title>订单信息(<?php echo $orderId;?>)</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('jquery.ui');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/norder/details');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$loginId   = $user['LOGIN_ID'] ;
		
		
		
		$order = $SqlUtils->getObject("sql_sc_order_list",array('orderId'=>$orderId)) ;
		
		$items = $SqlUtils->exeSql("sql_sc_order_item_list",array('orderId'=>$orderId)) ;
		//debug($items) ;
		//echo $orderId ;
		//echo $loginId ;
		//debug( $order) ;
	?>
	<style>
		.table th, .table td{
			padding:5px !important;
		}
	</style>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">
			<table class="table table-bordered base-table">
				<caption>
					基本信息
					<button class="btn btn-primary asyn-details" style="float:right;">同步明细信息</button>
				</caption>
				<tr>
					<th>内部订单号：</th>
					<td><?php echo $order['ORDER_NUMBER']?>(<?php  echo $order['FULFILLMENT_CHANNEL']?>)</td>
					<th>账户：</th>
					<td>
					<input type="hidden"  id="accountId"  value="<?php echo $order['ACCOUNT_ID'] ?>"/>
					<?php echo $order['ACCOUNT_NAME']?></td>
				</tr>
				<tr>
					<th>订单编号：</th>
					<td>
					<input type="hidden"  id="orderId"  value="<?php echo $order['ORDER_ID'] ?>"/>
					<?php echo $order['ORDER_ID']?></td>
					<th>订单状态：</th>
					<td><?php echo $order['ORDER_STATUS']?></td>
				</tr>
				<tr>
					<th>总金额：</th>
					<td><?php echo $order['AMOUNT']?> <?php echo $order['CURRENCY_CODE']?></td>
					<th>数量：</th>
					<td>已发货：<?php echo $order['SHIPPED_NUM']?> 未发货：<?php echo $order['UNSHIPPED_NUM']?></td>
				</tr>
				<tr>
					<th>Ship Service Level Category：</th>
					<td><?php echo $order['SHIPMENT_SERVICE_LEVEL_CATEGORY']?> </td>
					<th>Ship Service Level：</th>
					<td> <?php echo $order['SHIP_SERVICE_LEVEL']?> </td>
				</tr>
				<tr>
					<th>BUYER_NAME：</th>
					<td><?php echo $order['BUYER_NAME']?></td>
					<th>BUYER_EMAIL：</th>
					<td><?php echo $order['BUYER_EMAIL']?></td>
				</tr>
				
				
			</table>

			<table class="table table-bordered">
				<caption>收货人信息</caption>
				<tr>
					<th>收货人：</th>
					<td><?php echo $order['SHIPPER_NAME']?></td>
					<th>Address Line1：</th>
					<td><?php echo $order['ADDRESS_LINE1']?></td>
				</tr>
				<tr>
					<th>Address Line2：</th>
					<td><?php echo $order['ADDRESS_LINE2']?></td>
					<th>Address Line3：</th>
					<td><?php echo $order['ADDRESS_LINE3']?></td>
				</tr>
				<tr>
					<th>City：</th>
					<td><?php echo $order['CITY']?></td>
					<th>Country：</th>
					<td><?php echo $order['COUNTRY']?></td>
				</tr>
				<tr>
					<th>District：</th>
					<td><?php echo $order['DISTRICT']?></td>
					<th>State Or Region：</th>
					<td><?php echo $order['STATE_OR_REGION']?></td>
				</tr>
				<tr>
					<th>Postal Code：</th>
					<td><?php echo $order['POSTAL_CODE']?></td>
					<th>Country Code：</th>
					<td><?php echo $order['COUNTRY_CODE']?></td>
				</tr>
				<tr>
					<th>Phone：</th>
					<td colspan="3"><?php echo $order['PHONE']?></td>
				</tr>
			</table>
			
			<table class="table table-bordered">
				<caption>订单货品</caption>
				<?php 
					foreach( $items as $item ){
						$item = $SqlUtils->formatObject($item) ;
				  ?>
				  	<tr>
				  		<td style="width:100%;">
				  			<div class="alert alert-success" style="margin:2px;font-weight:bold;"><span offer-listing="<?php  echo $item['LISTING_ASIN'] ;?>"><?php  echo $item['Title'] ;?></span>
				  					<?php 
				  					
				  						if( !empty($item['NAME']) ){
				  							echo "[ <span product-realsku='".$item['REAL_SKU']."'>".$item['REAL_SKU']."</span> ]（".$item['NAME']."）" ;
				  						}
				  					?>
				  				</div>
				  			<table style="width:100%;" class="table table-bordered">
				  				<tr>
				  					<th>Order Item Id</th>
				  					<td><?php echo $item['Order_Item_Id'] ;?></td>
				  					<th>Seller SKU</th>
				  					<td><?php echo $item['Seller_SKU'] ;?></td>
				  					<th>购买数量</th>
				  					<td><?php echo $item['Quantity_Ordered'] ;?></td>
				  				</tr>
				  				<tr>
				  					<th>Item Price</th>
				  					<td><?php echo $item['Item_Price_Amount'] ;?>&nbsp;&nbsp;<?php echo $item['Item_Price_Currency_Code'] ;?></td>
				  					<th>Item Tax</th>
				  					<td colspan="3"><?php echo $item['Item_Tax_Amount'] ;?>&nbsp;&nbsp;<?php echo $item['Item_Tax_Currency_Code'] ;?></td>
				  				</tr>
				  				<tr>
				  					<th>Sipping Price</th>
				  					<td><?php echo $item['Shipping_Price_Amount'] ;?>&nbsp;&nbsp;<?php echo $item['Shipping_Price_Currency_Code'] ;?></td>
				  					<th>Shipping Tax</th>
				  					<td colspan="3"><?php echo $item['Shipping_Tax_Amount'] ;?>&nbsp;&nbsp;<?php echo $item['Shipping_Tax_Currency_Code'] ;?></td>
				  				</tr>
				  				<tr>
				  					<th>
				  						<?php 
				  							if( !empty( $item['IMAGE_URL'] ) ){
				  								echo "<img src='/".$fileContextPath."".$item['IMAGE_URL']."'>" ;
				  							}
				  						?>
				  					</th>
				  					<th colspan="5">
				  						<?php echo $item['MEMO'] ;?>
				  					</th>
				  				</tr>
				  			</table>
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