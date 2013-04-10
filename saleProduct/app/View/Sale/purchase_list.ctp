<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
   
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

		$pp_edit 				= $security->hasPermission($loginId , 'pp_edit') ;
		$ppp_add_product				= $security->hasPermission($loginId , 'ppp_add_product') ;
		$ppp_export		= $security->hasPermission($loginId , 'ppp_export') ;
		$ppp_audit	= $security->hasPermission($loginId , 'ppp_audit') ;
		$ppp_setlimitprice				= $security->hasPermission($loginId , 'ppp_setlimitprice') ;
		$ppp_assign_executor				= $security->hasPermission($loginId , 'ppp_assign_executor') ;
		$ppp_qc		= $security->hasPermission($loginId , 'ppp_qc') ;
		$ppp_inwarehouse		= $security->hasPermission($loginId , 'ppp_inwarehouse') ;
		$ppp_confirm 		= $security->hasPermission($loginId , 'ppp_confirm') ;
		/*$apply_purchase 		= $security->hasPermission($loginId , 'apply_purchase') ;
		$audit_purchase 	= $security->hasPermission($loginId , 'audit_purchase') ;
		$purchase_cost_view 	= $security->hasPermission($loginId , 'purchase_cost_view') ;
		$confirm_purchase 	= $security->hasPermission($loginId , 'confirm_purchase') ;*/
		
	?>
	
	<script type="text/javascript">

	    var flag = <?php echo $flag ; ?> ; 
		var loginId = <?php echo $flag == 1?"''":"'$loginId'" ?> ;
	
		var $pp_edit = <?php echo $pp_edit?"true":"false" ;?> ;
		var $ppp_add_product = <?php echo $ppp_add_product?"true":"false" ;?> ;
		var $ppp_export= <?php echo $ppp_export?"true":"false" ;?> ;
		var $ppp_audit = <?php echo $ppp_audit?"true":"false" ;?> ;
		var $ppp_setlimitprice = <?php echo $ppp_setlimitprice?"true":"false" ;?> ;
		var $ppp_assign_executor = <?php echo $ppp_assign_executor?"true":"false" ;?> ;
		var $ppp_qc = <?php echo $ppp_qc?"true":"false" ;?> ;
		var $ppp_inwarehouse = <?php echo $ppp_inwarehouse?"true":"false" ;?> ;
		var $ppp_confirm = <?php echo $ppp_confirm?"true":"false" ;?> ;

		var img0 = '<?php echo $this->Html->image('example.gif',array("title"=>"所有")) ?>' ;
		var img1 = '<?php echo $this->Html->image('example.gif',array("title"=>"未处理")) ?>' ;
		var img2 = '<?php echo $this->Html->image('apply.png',array("title"=>"申请采购")) ?>' ;
		var img25 = '<?php echo $this->Html->image('error.gif',array("title"=>"审批不通过，终止采购")) ?>' ;
		var img3 = '<?php echo $this->Html->image('success.gif',array("title"=>"审批通过，限价待确认")) ?>' ;
		var img4 = '<?php echo $this->Html->image('forum.gif',array("title"=>"限价确认完成，待分配责任人")) ?>' ;
		var img45 = '<?php echo $this->Html->image('cmp.gif',array("title"=>"采购执行")) ?>' ;
		var img5 = '<?php echo $this->Html->image('pkg.gif',array("title"=>"待QC验货产品")) ?>' ;
		var img6 = '<?php echo $this->Html->image('cake.icon.png',array("title"=>"货品待入库")) ?>' ;
		var img7 = '<?php echo $this->Html->image('icon-grid.gif',array("title"=>"采购确认")) ?>' ;
		var img8 = '<?php echo $this->Html->image('test-pass-icon.png',array("title"=>"结束采购")) ?>' ;
	</script>

	 <style type="text/css">
		img{
			cursor:pointer;
		}
	</style>

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
					<?php if( $pp_edit  ){ ?>
					<button class="create-plan btn">创建采购/试销计划</button>
					<?php } ?>
				</td>
			</tr>						
		</table>					

	</div>	
	
	<div class="grid-content">
	</div>
	<div style="clear:both;height:1px;" ></div>
	<div class="row-fluid">
	
	</div>
	<div class="grid-content-details" style="margin-top:5px;">
	</div>
	<iframe src="" id="exportIframe" style="display:none"></iframe>
</body>
</html>
