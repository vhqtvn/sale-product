<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>更新计划</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

  <?php
 		include_once ('config/config.php');
  
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('modules/supplychain/update_plan_item');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$accountId = $params['arg1'] ;
		$shipmentId = $params['arg2'] ;
		
		$plan = $SqlUtils->getObject("select * from sc_fba_inbound_plan
					where shipment_id= '{@#shipmentId#}' and account_id = '{@#accountId#}' ",array("shipmentId"=>$shipmentId,"accountId"=>$accountId)) ;

		$skuMembers = $SqlUtils->exeSqlWithFormat("SELECT s1.*,
								       s2.ASIN ,
								       srp.REAL_SKU,
								       srp.IMAGE_URL,
								       srp.NAME
								FROM sc_fba_inbound_plan_items s1,
								sc_amazon_account_product s2
								LEFT JOIN sc_real_product_rel s3
								ON s3.ACCOUNT_ID = s2.ACCOUNT_ID
								AND s3.SKU = s2.SKU
								LEFT JOIN sc_real_product srp
								ON srp.REAL_SKU = s3.REAL_SKU
								WHERE s1.SELLER_SKU = s2.SKU
								AND s1.ACCOUNT_ID = s2.ACCOUNT_ID and
				 s1.shipment_id= '{@#shipmentId#}' and s1.account_id = '{@#accountId#}' ",array("shipmentId"=>$shipmentId,"accountId"=>$accountId)) ;
	?>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
				<input type="hidden" id="shipmentId" value="<?php echo $params['arg2'];?>"/>
				<input type="hidden" id="accountId" value="<?php echo  $params['arg1'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table  class="table table-bordered">
							    <caption>更新计划</caption>
								<tr>
									<th style="width:30%;">Shipment Status：</th>
									<td>
										WORKING：<input type="radio"  name="shipmentStatus" value="WORKING"   <?php if($plan['SHIPMENT_STATUS'] == 'WORKING') echo 'checked'; ?>/>
										&nbsp;
										SHIPPED：<input type="radio"  name="shipmentStatus" value="SHIPPED"  <?php if($plan['SHIPMENT_STATUS'] == 'SHIPPED') echo 'checked'; ?>/>
										&nbsp;
										CANCELLED：<input type="radio"  name="shipmentStatus" value="CANCELLED"  <?php if($plan['SHIPMENT_STATUS'] == 'CANCELLED') echo 'checked'; ?>/>
									</td>
							    </tr>
						</table>
						<div>
							<table   class="table table-bordered">
								<tr>
									<th>Seller SKU</th>
									<th>Asin</th>
									<th>货品名称</th>
									<th>数量</th>
								</tr>
							<?php  	foreach ($skuMembers as $item){ ?>
								<tr>
									<td><?php echo $item['SELLER_SKU'] ;?></td>
									<td><?php echo $item['ASIN'] ;?></td>
									<td><?php echo $item['NAME'] ;?></td>
									<td class="sellerSku" >
										<input type="hidden" name="sellerSku" value="<?php echo $item['SELLER_SKU'];?>"/>
										<input type="text" name="quantity" value="<?php echo $item['QUANTITY_SHIPPED'] ;?>"/>
									</td>
								</tr>
							<?php 	} ?>
							</table>
							<?php /*
							<div  class="sellerSku" >
							<input type="hidden" name="sellerSku" value="AAA"/>
										<input type="text" name="quantity" value="10"/>
							</div>	
							<div  class="sellerSku" >	
									<input type="hidden" name="sellerSku" value="BB"/>
										<input type="text" name="quantity" value="20"/>
										</div>	
								*/ ?>	
						</div>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class=" save  btn btn-primary">保存</button>
							<button type="button" class=" to-amazon btn btn-danger">提交到Amazon</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>