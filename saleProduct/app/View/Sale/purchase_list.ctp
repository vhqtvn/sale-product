<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/sale/purchase_list');
		
		$user = $this->Session->read("product.sale.user") ;
		$groupCode = $user["GROUP_CODE"] ;
		$loginId = $user['LOGIN_ID'] ;
		
		/**
		 *  create_pp 添加计划产品操作
			add_pp_product 添加审批产品操作
			add_pp_audit_product 导出操作
			export_pp 打印操作
			print_pp 编辑采购产品操作
			edit_pp_product 删除采购产品操作
			delete_pp_product 申请采购操作
			apply_purchase 审批通过操作
			audit_pass_purchase 审批不通过操作
			audit_nopass_purchase
		*/
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$create_pp 				= $security->hasPermission($loginId , 'create_pp') ;
		$add_pp_product 		= $security->hasPermission($loginId , 'add_pp_product') ;
		$add_pp_audit_product	= $security->hasPermission($loginId , 'add_pp_audit_product') ;
		$export_pp 				= $security->hasPermission($loginId , 'export_pp') ;
		$print_pp 				= $security->hasPermission($loginId , 'print_pp') ;
		$edit_pp_product 		= $security->hasPermission($loginId , 'edit_pp_product') ;
		$delete_pp_product 		= $security->hasPermission($loginId , 'delete_pp_product') ;
		$apply_purchase 		= $security->hasPermission($loginId , 'apply_purchase') ;
		$audit_purchase 	= $security->hasPermission($loginId , 'audit_purchase') ;
		$purchase_cost_view 	= $security->hasPermission($loginId , 'purchase_cost_view') ;
		$confirm_purchase 	= $security->hasPermission($loginId , 'confirm_purchase') ;
		
	?>
	
	<script type="text/javascript">

	    var flag = <?php echo $flag ; ?> ; 
		var loginId = <?php echo $flag == 1?"''":"'lixh'" ?> ;//'lixh' ;
	
		var $create_pp = <?php echo $create_pp?"true":"false" ;?> ;
		var $add_pp_product = <?php echo $add_pp_product?"true":"false" ;?> ;
		var $add_pp_audit_product = <?php echo $add_pp_audit_product?"true":"false" ;?> ;
		var $export_pp = <?php echo $export_pp?"true":"false" ;?> ;
		var $print_pp = <?php echo $print_pp?"true":"false" ;?> ;
		var $edit_pp_product = <?php echo $edit_pp_product?"true":"false" ;?> ;
		var $delete_pp_product = <?php echo $delete_pp_product?"true":"false" ;?> ;
		var $apply_purchase = <?php echo $apply_purchase?"true":"false" ;?> ;
		var $audit_purchase = <?php echo $audit_purchase?"true":"false" ;?> ;
		var $purchase_cost_view = <?php echo $purchase_cost_view?"true":"false" ;?> ;
		var $confirm_purchase = <?php echo $confirm_purchase?"true":"false" ;?> ;
		
		var img1 = '<?php echo $this->Html->image('example.gif',array("title"=>"未处理")) ?>' ;
		var img2 = '<?php echo $this->Html->image('apply.png',array("title"=>"申请采购")) ?>' ;
		var img3 = '<?php echo $this->Html->image('success.gif',array("title"=>"审批通过")) ?>' ;
		var img4 = '<?php echo $this->Html->image('error.gif',array("title"=>"审批未通过")) ?>' ;
		var img5 = '<?php echo $this->Html->image('pkg.gif',array("title"=>"已采购")) ?>' ;
	</script>


</head>
<body>
	
	<div class="toolbar toolbar-auto">
		<table>
			<tr>
				<th>
					计划名称:
				</th>
				<td>
					<input type="text" id="name"/>
				</td>
				<th>
					类型:
				</th>
				<td>
					<select id="type">
						<option value="">--</option>
						<option value='1'>产品试销</option>
						<option value='2'>产品采购</option>
					</select>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary">查询</button>
					<?php if( $create_pp  ){ ?>
					<button class="create-plan btn">创建采购/试销计划</button>
					<?php } ?>
				</td>
			</tr>						
		</table>					

	</div>	
	
	<div class="grid-content">
	</div>
	<div style="clear:both;height:1px;" ></div>
	<div class="grid-content-details" style="margin-top:5px;">
	</div>
	<iframe src="" id="exportIframe" style="display:none"></iframe>
</body>
</html>
