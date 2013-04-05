<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title></title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('modules/sale/edit_purchase_plan_product');
		
		$id = $product["ID"] ;
		$asin = $product["ASIN"] ;
		$sku = $product["SKU"] ;
		$title = $product["TITLE"] ;
		$planId = $product["PLAN_ID"] ;
		
		$cost = $product["COST"] ;
		$plan_num = $product["PLAN_NUM"] ;
		$quote_price = $product["QUOTE_PRICE"] ;
		$providor = $product["PROVIDOR"] ;
		$sample_code = $product["SAMPLE_CODE"] ;
		$sample  = $product["SAMPLE"] ;
		$area = $product["AREA"] ;
		
		$security  = ClassRegistry::init("Security") ;
		
		$loginId = $user['LOGIN_ID'] ;
		$create_pp 				= $security->hasPermission($loginId , 'create_pp') ;
		$delete_pp 				= $security->hasPermission($loginId , 'delete_pp') ;
		$add_pp_product 		= $security->hasPermission($loginId , 'add_pp_product') ;
		$add_pp_audit_product	= $security->hasPermission($loginId , 'add_pp_audit_product') ;
		$export_pp 				= $security->hasPermission($loginId , 'export_pp') ;
		$print_pp 				= $security->hasPermission($loginId , 'print_pp') ;
		$edit_pp_product 		= $security->hasPermission($loginId , 'edit_pp_product') ;
		$reedit_pp_product 		= $security->hasPermission($loginId , 'reedit_pp_product') ;
		$delete_pp_product 		= $security->hasPermission($loginId , 'delete_pp_product') ;
		$apply_purchase 		= $security->hasPermission($loginId , 'apply_purchase') ;
		$audit_purchase 	= $security->hasPermission($loginId , 'audit_purchase') ;
		$purchase_cost_view 	= $security->hasPermission($loginId , 'purchase_cost_view') ;
		$confirm_purchase 	= $security->hasPermission($loginId , 'confirm_purchase') ;
		
	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

   <script type="text/javascript">
   var $create_pp = <?php echo $create_pp?"true":"false" ;?> ;
	var $delete_pp = <?php echo $delete_pp?"true":"false" ;?> ;
	var $add_pp_product = <?php echo $add_pp_product?"true":"false" ;?> ;
	var $add_pp_audit_product = <?php echo $add_pp_audit_product?"true":"false" ;?> ;
	var $export_pp = <?php echo $export_pp?"true":"false" ;?> ;
	var $print_pp = <?php echo $print_pp?"true":"false" ;?> ;
	var $edit_pp_product = <?php echo $edit_pp_product?"true":"false" ;?> ;
	var $reedit_pp_product = <?php echo $reedit_pp_product?"true":"false" ;?> ;
	var $delete_pp_product = <?php echo $delete_pp_product?"true":"false" ;?> ;
	var $apply_purchase = <?php echo $apply_purchase?"true":"false" ;?> ;
	var $audit_purchase = <?php echo $audit_purchase?"true":"false" ;?> ;
	var $purchase_cost_view = <?php echo $purchase_cost_view?"true":"false" ;?> ;
	var $confirm_purchase = <?php echo $confirm_purchase?"true":"false" ;?> ;

	var id = '<?php echo $id ;?>' ;
   </script>
</head>


<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">
		
			<div id="details_tab"></div>
			<div id="base-info">
		        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
		        	<input id="id" type="hidden" value='<?php echo $id ;?>' />
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table" >
								<caption>采购产品信息</caption>
								<tbody>										   
									<tr>
										<th>编号：</th><td><?php echo $id ;?></td>
									</tr>
									<tr>
										<th>SKU：</th><td><?php echo $sku ;?></td>
									</tr>
									<tr>
										<th>标题：</th><td><?php echo $title ;?></td>
									</tr>
									<tr>
										<th>采购数量：</th>
										<td><input id="plan_num" type="text" value='<?php echo $plan_num ;?>' /></td>
									</tr>
									<tr>
										<th>采购价：</th>
										<td><input id="quote_price" type="text" value='<?php echo $quote_price ;?>' /></td>
									</tr>
									<tr>
										<th>供应商：</th><td>
										<select id="providor">
											<option value="">--</option>
										<?php
											$SqlUtils  = ClassRegistry::init("SqlUtils") ;
											
											foreach($supplier as $suppli){
												$suppli = $SqlUtils->formatObject($suppli) ;
												$temp = "" ;
												if( $suppli['ID'] == $providor ){
													$temp = "selected" ;
												}
												echo "<option $temp value='".$suppli['ID']."'>".$suppli['NAME']."</option>" ;
											}
										?>
										</select>  
										
										<a href="javascript://" class="edit_supplier">编辑产品供应商</a>
										</td>
									</tr>
									<tr>
										<th>样品：</th><td>
										<select id="sample">
											<option value="0" <?php if($sample == 0 ) echo 'selected' ;?>>无</option>
											<option value="1" <?php if($sample == 1 ) echo 'selected' ;?> >准备中</option>
											<option value="2" <?php if($sample == 2 ) echo 'selected' ;?>>有</option>
										</select>
										</td>
									</tr>
									<tr>
										<th>样品编码：</th><td>
										<input type="text" id="sample_code" value='<?php echo $sample_code ;?>' />(位置码+产品码组成，中间以下划线连接)
										</td>
									</tr>
									<tr>
										<th>采购地区：</th><td>
										<select id="area">
											<option value="china" <?php if($area == 'china' ) echo 'selected' ;?>>大陆</option>
											<option value="taiwan" <?php if($area == 'taiwan' ) echo 'selected' ;?> >台湾</option>
											<option value="american" <?php if($area == 'american' ) echo 'selected' ;?>>美国</option>
										</select>
										</td>
									</tr>
									<tr>
										<th>采购原因：</th><td>
										<textarea style="width:500px;height:80px;" id="memo"><?php echo $product['MEMO'] ;?></textarea>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<!-- panel脚部内容-->
	                    <div class="panel-foot">
							<div class="form-actions">
								<button type="button" class="btn btn-primary btn-save">提&nbsp;交</button>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div id="ref-asins" style="width:880px;padding:10px;">
				<div class="grid-content-details" style="width:850px;"></div>
			</div>
		</div>
	</div>
</body>

</html>