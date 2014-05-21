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
  		
		echo $this->Html->script('modules/warehouse/inventoryList');
		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$categorys = $SqlUtils->exeSql("sql_saleproduct_categorytree",array() ) ;
		
		$security  = ClassRegistry::init("Security") ;
		
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["LOGIN_ID"] ;
		
		$product_add = $security->hasPermission($loginId , 'product_add') ;
		$product_edit = $security->hasPermission($loginId , 'product_edit') ;
		$product_giveup = $security->hasPermission($loginId , 'product_giveup') ;
		$view_giveup_product = $security->hasPermission($loginId , 'view_giveup_product') ;
		$product_stock_quanity_assign = $security->hasPermission($loginId , 'product_stock_quanity_assign') ;
		//销售状态变更权限
		$product_onsale =  $security->hasPermission($loginId , 'product_onsale') ;

		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["GROUP_CODE"] ;
		if($loginId == 'general_manager'){
		?>
		<script>
			var deleteHtml = "<a href='#' class='action giveup btn'   type=3>删除</a>" ;
		</script>
		<?php
		}
	?>

   
   <style>
   	.span1_5{
		width:100px;
   	}
   </style>

</head>
<body>

			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>关键字：</th>
						<td>
							<input type="text" id="searchKey" class="span5" placeHolder="输入名称、SKU、ASIN、备注进行查询"/>
						</td>
						<th>仓库：</th>
						<td>
						    <select class="span1_5"  id="warehouseId">
						    	<option value="">全部</option>
						   <?php 
						     // sql_warehouse_lists
						     $warehouses = $SqlUtils->exeSql("sql_warehouse_lists",array()) ;
                             foreach($warehouses as $w){
                             	  $w = $SqlUtils->formatObject( $w ) ;
                             	  echo "<option value='".$w['ID']."'>".$w['NAME']."</option>" ;
                             }
						   ?>
							</select>
						</td>
						<th>账号：</th>
						<td>
						<select name="accountId" data-validator="required"  style="width:100px;">
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
					</td>			
						<td>
							<button class="btn btn-primary query query-btn" >查询</button>
						</td>
					</tr>							
				</table>	
				<hr style="margin:2px;"/>	
			</div>
			
			<div class="grid-content"  ></div>
			<div class="row-fluid">
					<div class="span6">
						<div class="grid-content-details"  style="margin-top:3px;"></div>
					</div>
					<div class="span6">
						<div class="grid-content-tracks"  style="margin-top:3px;"></div>
					</div>
			</div>
			

</body>
</html>
