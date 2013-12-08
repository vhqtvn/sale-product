<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>跟踪信息</title>
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
		echo $this->Html->script('modules/supplychain/edit_tracking');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$accountId = $params['arg1'] ;
		$shipmentId = $params['arg2'] ;
		
		$plan = $SqlUtils->getObject("select * from sc_fba_inbound_plan
					where shipment_id= '{@#shipmentId#}' and account_id = '{@#accountId#}' ",array("shipmentId"=>$shipmentId,"accountId"=>$accountId)) ;
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
							    <caption>运输信息</caption>
								<tr>
									<th style="width:30%;">IsPartnered：</th>
									<td>
										是：<input type="radio"  name="isPartnered" value="true"   <?php if($plan['IS_PARTNERED'] == 'true') echo 'checked'; ?>/>
										否：<input type="radio"  name="isPartnered" value="false"  <?php if($plan['IS_PARTNERED'] != 'true') echo 'checked'; ?>/>
									</td>
							    </tr>
							    <tr>
									<th>Shipment Type：</th>
									<td>
										<select name="shipmentType">
											<option value="">选择</option>
											<option value="SP" <?php if($plan['SHIPMENT_TYPE'] != 'LTL') echo 'selected'; ?>>SP</option>
											<option value="LTL" <?php if($plan['SHIPMENT_TYPE'] == 'LTL') echo 'selected'; ?>>LTL</option>
										</select>
									</td>
							    </tr>	
							    <tr>
									<th>Carrier Name ：</th>
									<td>
										<select name="carrierName" >
											<option value="OTHER"  <?php if($plan['CARRIER_NAME'] == 'OTHER') echo 'selected'; ?>>OTHER</option>
											<option value="BUSINESS_POST" <?php if($plan['CARRIER_NAME'] == 'BUSINESS_POST') echo 'selected'; ?>>BUSINESS_POST</option>
											<option value="DHL_AIRWAYS_INC" <?php if($plan['CARRIER_NAME'] == 'DHL_AIRWAYS_INC') echo 'selected'; ?>>DHL_AIRWAYS_INC</option>
											<option value="DHL_UK"  <?php if($plan['CARRIER_NAME'] == 'DHL_UK') echo 'selected'; ?>>DHL_UK</option>
											<option value="PARCELFORCE"  <?php if($plan['CARRIER_NAME'] == 'PARCELFORCE') echo 'selected'; ?>>PARCELFORCE</option>
											<option value="DPD"  <?php if($plan['CARRIER_NAME'] == 'DPD') echo 'selected'; ?>>DPD</option>
											<option value="TNT_LOGISTICS_CORPORATION"  <?php if($plan['CARRIER_NAME'] == 'TNT_LOGISTICS_CORPORATION') echo 'selected'; ?>>TNT_LOGISTICS_CORPORATION</option>
											<option value="TNT"  <?php if($plan['CARRIER_NAME'] == 'TNT') echo 'selected'; ?>>TNT</option>
											<option value="YODEL"  <?php if($plan['CARRIER_NAME'] == 'YODEL') echo 'selected'; ?>>YODEL</option>
											<option value="UNITED_PARCEL_SERVICE_INC"  <?php if($plan['CARRIER_NAME'] == 'UNITED_PARCEL_SERVICE_INC') echo 'selected'; ?>>UNITED_PARCEL_SERVICE_INC</option>
										</select>
									</td>
							    </tr>
							    <tr>
									<th>Tracking Id：<br/>
										（多个用逗号分隔）
									</th>
									<td>
										<textarea  style="width:80%;height:100px;" placeholder="多个用逗号分隔"></textarea>
									</td>
							    </tr>
						</table>
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