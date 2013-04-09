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
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('modules/sale/purchaseTaskList');
		
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
		
		$editPermission 				= $security->hasPermission($loginId , 'purchaseTask@edit') ;
		
	?>
	
	<script type="text/javascript">
		var editPermission = <?php  echo $editPermission?'true':'false' ;?>
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
					任务名称:
				</th>
				<td>
					<input type="text" id="name"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary">查询</button>
					<?php if( $editPermission ){ ?>
					<button class="create-task btn">创建采购任务</button>
					<?php } ?>
				</td>
			</tr>						
		</table>					

	</div>	
	
	<div class="grid-task"></div>
	<div style="clear:both;height:1px;" ></div>

	<div class="grid-task-product" style="margin-top:5px;"></div>
	</div>
	<iframe src="" id="exportIframe" style="display:none"></iframe>
</body>
</html>
