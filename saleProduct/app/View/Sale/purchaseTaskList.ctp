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
		
		$editPermission 	=  $security->hasPermission($loginId , 'purchaseTask_edit') ;
		
	?>
	
	<script type="text/javascript">
		var editPermission = <?php  echo $editPermission?'true':'false' ;?>

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
					任务编号:
				</th>
				<td>
					<input type="text" id="taskCode"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.grid-task',qc:'.toolbar'}">查询</button>
					<?php if( $editPermission ){ ?>
					<button class="create-task btn">创建采购任务</button>
					<?php } ?>
				</td>
			</tr>						
		</table>					

	</div>	
	
	<div class="grid-task"></div>
	<div style="clear:both;height:1px;" ></div>
<div class="toolbar toolbar-auto toolbar1">
		<table>
			<tr>
				<th>
					关键字:
				</th>
				<td>
					<input type="text" id="searchKey" placeHolder="输入货品SKU、标题" style="width:400px;"/>
				</td>								
				<td class="toolbar-btns">
					<button class="btn btn-primary" data-widget="grid-query"  data-options="{gc:'.grid-task-product',qc:'.toolbar1'}">查询</button>
				</td>
			</tr>						
		</table>
	</div>	
	<div class="grid-task-product" style="margin-top:5px;"></div>
	</div>
	<iframe src="" id="exportIframe" style="display:none"></iframe>
</body>
</html>
