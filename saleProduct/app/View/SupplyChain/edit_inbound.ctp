<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>功能编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

  <?php
 		 include_once ('config/config.php');
  
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/supplychain/edit_inbound');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$planId = $params['arg1'] ;
		
		$plan = $SqlUtils->getObject("sql_supplychain_inbound_local_plan_getByPlanId",array('planId'=>$planId)) ;
		$inId =$plan['IN_ID'] ; 
		
	?>
	<style type="text/css">
	.span5 table td,.span5 table th{
		padding:3px 5px!important;
	}
	</style>
</head>
<script>
	var status = '<?php echo $plan['STATUS'];?>'
	var planId = '<?php echo $planId;?>' ;
	var inId =  '<?php echo $inId;?>' ;
</script>
<body class="container-popup">
	<div class="row-fluid">
			<div class="span5">
				<div class="row-fluid" style="margin-bottom:5px;margin-top:5px;">
					<div class="span4"><h4>Inbound计划</h4></div>
					<div class="span8">
						<?php  if( $plan['STATUS'] !=1 ){ ?>
					<button class="btn btn-primary  save-plan"  >保存计划</button>
					<?php } ?>
					</div>
				</div>
				
				<form  id="planForm" action="">
				<input type="hidden" name="planId" id="planId"  value="<?php echo $plan['PLAN_ID'];?>"/>
				<table  class="table table-bordered">
				    <caption>基本信息</caption>
					<tr>
						<th>账号：</th>
						<td>
							<select name="accountId" class="span2"  disabled>
				     		<option value="">--选择--</option>
					     	<?php
					     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
				   				 $accounts = $amazonAccount->getAllAccounts(); 
					     		foreach($accounts as $account ){
					     			$account = $account['sc_amazon_account'] ;
					     			$checked = $account['ID'] == $plan['ACCOUNT_ID']?"selected":"" ;
					     			echo "<option value='".$account['ID']."'  $checked>".$account['NAME']."</option>" ;
					     		} ;
					     	?>
							</select>
						</td>
				    </tr>
				    <tr>
						<th>Label Type：</th>
						<td>
							<select name="labelPrepType" class="span2">
								<option value="">选择</option>
								<option value="SELLER_LABEL"  <?php if($plan['LABEL_PREP_TYPE'] == 'SELLER_LABEL') echo 'selected'; ?>>SELLER_LABEL</option>
								<option value="AMAZON_LABEL_ONLY" <?php if($plan['LABEL_PREP_TYPE'] == 'AMAZON_LABEL_ONLY') echo 'selected'; ?>>AMAZON_LABEL_ONLY</option>
								<option value="AMAZON_LABEL_PREFERRED" <?php if($plan['LABEL_PREP_TYPE'] == 'AMAZON_LABEL_PREFERRED') echo 'selected'; ?>>AMAZON_LABEL_PREFERRED</option>
							</select>
						</td>
				    </tr>					
			</table>
			<table  class="table table-bordered  address-table" data-widget="validator,ajaxform">
				    <caption>
				    发货地址信息
				    <select style="width:150px;padding:2px;"  class="address-select">
				    	<option value="">选择发货地址</option>
				    <?php 
				    $Meta = ClassRegistry::init("Meta") ;
				    $addresss = $Meta->listAddress() ;
				    foreach( $addresss as $addr ){
				    	echo '<option value="'.$addr['META_ID'].'">'.$addr['NAME'].'</option>' ;
				    }
				    ?>
				    </select>
				    <button class="btn btn-primary save-address">保存地址</button>
				    <button class="btn btn-primary add-address" title="添加新地址">+</button>
				    <input type="hidden" name="metaId"/>
				    </caption>
					<tr>
						<th>地址名称：</th>
						<td>
							<input type="text" name="name" data-validator="required"  value="<?php echo $plan['NAME'];?>"/>
						</td>
				    </tr>
				    <tr>
						<th>Address Line1：</th>
						<td>
							<input type="text" name="addressLine1" data-validator="required"  value="<?php echo $plan['ADDRESS_LINE1'];?>"/>
						</td>
				    </tr>
				    <tr>
						<th>Address Line2：</th>
						<td>
							<input type="text" name="addressLine2"  value="<?php echo $plan['ADDRESS_LINE2'];?>"/>
						</td>
				    </tr>
				    <tr>
						<th>District/Country：</th>
						<td>
							<input type="text" name="districtOrCounty" data-validator="required"  value="<?php echo $plan['DISTRICT_OR_COUNTY'];?>"/>
						</td>
				    </tr>
				    <tr>
						<th>City：</th>
						<td>
							<input type="text" name="city" data-validator="required"  value="<?php echo $plan['CITY'];?>"/>
						</td>
				    </tr>
				    <tr>
						<th>State/Province：</th>
						<td>
							<input type="text" name="stateOrProvinceCode" data-validator="required"  value="<?php echo $plan['STATE_OR_PROVINCE_CODE'];?>"/>
						</td>
				    </tr>
				    <tr>
						<th>Country Code：</th>
						<td>
							<input type="text" name="countryCode" data-validator="required"  value="<?php echo $plan['COUNTRY_CODE'];?>"/>
						</td>
				    </tr>
				    <tr>
						<th>Postal Code：</th>
						<td>
							<input type="text" name="postalCode" data-validator="required"  value="<?php echo $plan['POSTAL_CODE'];?>"/>
						</td>
				    </tr>						
			</table>
			<table  class="table table-bordered">
				    <caption>运输信息</caption>
					<tr>
						<th>IsPartnered：</th>
						<td>
							是：<input type="radio"  name="isPartnered" value="true"   <?php if($plan['IS_PARTNERED'] == 'true') echo 'checked'; ?>/>
							否：<input type="radio"  name="isPartnered" value="false"  <?php if($plan['IS_PARTNERED'] != 'true') echo 'checked'; ?>/>
						</td>
				    </tr>
				    <tr>
						<th>Shipment Type：</th>
						<td>
							<select name="shipmentType" class="span2">
								<option value="">选择</option>
								<option value="SP" <?php if($plan['SHIPMENT_TYPE'] != 'LTL') echo 'selected'; ?>>SP</option>
								<option value="LTL" <?php if($plan['SHIPMENT_TYPE'] == 'LTL') echo 'selected'; ?>>LTL</option>
							</select>
						</td>
				    </tr>	
				    <tr>
						<th>Carrier Name ：</th>
						<td>
							<select name="carrierName" class="span2">
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
			</table>
			</form>
			</div>
			<div class="span7">
				
				<div class="row-fluid" style="margin-bottom:5px;margin-top:5px;">
					<div class="span4"><h4>Inbound计划Listing</h4></div>
					<div class="span8">
						<?php  if( $plan['STATUS'] !=1 ){ ?>
					<button class="btn btn-danger save-to-amazon"  >创建到Amazon</button>
					<?php } ?>
					</div>
				</div>
				<div class="grid-content-detials" style="width:98%;"></div>
			</div>
	</div>
</body>
</html>