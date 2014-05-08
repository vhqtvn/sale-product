<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>库存列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<script>
		var deleteHtml = "" ;
	</script>
   <?php
		include_once ('config/config.php');
  		include_once ('config/header.php');
  		
		echo $this->Html->script('modules/warehouse/editInventory');
		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		
		$security  = ClassRegistry::init("Security") ;
		
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["LOGIN_ID"] ;
		
		
		$inventorys = $SqlUtils->exeSqlWithFormat("sc_warehouse_in_ListInventory_details",array("realId"=>$params['arg1'] )) ;

	?>
		<script>
			var realId = '<?php echo $params['arg1'];?>'
		</script>
   
   <style>
   	.span1_5{
		width:100px;
   	}
   	
   	.inventory-tbody-hidden{
		display:none;
   	}
   </style>

</head>
<body>

			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<td>
							<button class="btn btn-primary  add-btn no-disabled" >添加库存</button>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<button class="btn btn-danger  save-inventory" >保存库存</button>
						</td>
					</tr>							
				</table>	
				<hr style="margin:2px;"/>	
			</div>
			<br/>
			<table class="form-table bordered-table">
				<tr>
					<th style="width:50px !important;"></th>
					<th>账号</th>
					<th>Listing SKU</th>
					<th>仓库</th>
					<th>数量</th>
					<th>状态</th>
					<th>类型</th>
				</tr>
				<tbody  class="inventory-tbody">
				<?php  foreach( $inventorys as $inventory){ ?>
						<tr class="inventory-exist-row">
							<th style="width:50px !important;">
									<input type="hidden"  name="inventoryId"  value="<?php echo $inventory['INVENTORY_ID'];?>"/>
							</th>
							<th><?php echo $inventory['ACCOUNT_NAME'];?></th>
							<th><?php echo $inventory['LISTING_SKU'];?></th>
							<th><?php echo $inventory['WAREHOUSE_NAME'];?></th>
							<th>
							<input type="text"  value="<?php echo $inventory['QUANTITY'];?>" name="quantity"  style="width:50px;"/>
							</th>
							<th><?php echo $inventory['INVENTORY_STATUS']==1?"在库":"在途";?></th>
							<th><?php if( $inventory['INVENTORY_TYPE'] ==1){
								echo "FBM" ;
							}else  if( $inventory['INVENTORY_TYPE'] ==2){
								echo "FBA" ;
							}else  if( $inventory['INVENTORY_TYPE'] ==3){
								echo "残品" ;
							}else  if( $inventory['INVENTORY_TYPE'] ==4){
								echo "自由库存" ;
							}?></th>
						</tr>
				<?php 	} ?>
				</tbody>
				<tbody  class="inventory-tbody-hidden">
						<tr  class="inventory-row">
							<th style="width:50px !important;">
								<button  class="delete-row">删除</button>
							</th>
							<th>
							<select name="accountId"   style="width:100px;">
				     		<option value="">--选择--</option>
					     	<?php
					     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
				   				 $accounts = $amazonAccount->getAllAccounts(); 
					     		foreach($accounts as $account ){
					     			$account = $account['sc_amazon_account'] ;
					     			echo "<option value='".$account['ID']."'>".$account['NAME']."</option>" ;
					     		} ;
					     	?>
							</select>
							</th>
							<th>
							<select name="listingSku"  style="width:150px;">
				     		<option value="">--选择--</option>
					     	<?php
				   				 $listingSkus = $SqlUtils->exeSqlWithFormat("select srpr.*,saap.FULFILLMENT_CHANNEL from sc_real_product_rel srpr,sc_amazon_account_product saap
																	 where srpr.real_id = '{@#realId#}' and srpr.account_id = saap.account_id and srpr.sku = saap.sku ",array("realId"=>$params['arg1'])); 
					     		foreach($listingSkus as $listingSku ){
									if( empty($listingSku['SKU']) )continue ;
					     			echo "<option value='".$listingSku['SKU']."'  channel='".$listingSku['FULFILLMENT_CHANNEL']."'>".$listingSku['SKU']."</option>" ;
					     		} ;
					     	?>
							</select>
							</th>
							<th>
								<select class="span1_5"  name="warehouseId">
						    	<option value="">--选择--</option>
							   <?php 
							     // sql_warehouse_lists
							     $warehouses = $SqlUtils->exeSql("sql_warehouse_lists",array()) ;
	                             foreach($warehouses as $w){
	                             	  $w = $SqlUtils->formatObject( $w ) ;
	                             	  echo "<option value='".$w['ID']."'>".$w['NAME']."</option>" ;
	                             }
							   ?>
								</select>
							</th>
							<th><input type="text"  value="" name="quantity" style="width:50px;"/></th>
							<th>
								<select style="width:100px;"  name="inventoryStatus">
										<option value="1">在库</option>
										<option value="2">在途</option>
								</select>
							</th>
							<th>
								<select style="width:100px;"  name="inventoryType">
										<option value="">--选择--</option>
										<option value="1">FBM</option>
										<option value="2">FBA</option>
										<option value="3">残品</option>
										<option value="4">自由库存</option>
								</select>
							</th>
						</tr>
				</tbody>
			</table>
			

</body>
</html>
