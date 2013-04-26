<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>产品开发计划</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('validator/jquery.validation');	
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('calendar/WdatePicker');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		
		$loginId   = $user['LOGIN_ID'] ;
		$result = null ;
	?>
	
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        	<input  type="hidden" id="id"  value="<?php echo $result['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<caption>产品开发计划</caption>
							<tbody>	
								<tr>
									<th>计划名称：</th><td colspan=3><input data-validator="required" type="text" id="name"
										value="<?php echo $result['NAME'];?>"/></td>
								</tr>
								<tr>
									<th>跟卖产品总数量：</th><td  colspan=3><input  type="text" id="followTotalNum"
										value="<?php echo $result['FOLLOW_TOTAL_NUM'];?>"/></td>
								</tr>
								<tr>
									<th>跟卖产品人均数量：</th><td  colspan=3><input type="text" id="followPerNum"
										value="<?php echo $result['FOLLOW_PER_NUM'];?>"/></td>
								</tr>
								<tr>
									<th>跟卖产品销量：</th><td  colspan=3><input  type="text" id="followSaleNum"
										value="<?php echo $result['FOLLOW_SALE_NUM'];?>"/></td>
								</tr>
								<tr>
									<th>自有产品总数量：</th><td  colspan=3><input type="text" id="selfTotalNum"
										value="<?php echo $result['SELF_TOTAL_NUM'];?>"/></td>
								</tr>
								<tr>
									<th>自有产品人均数量：</th><td  colspan=3><input type="text" id="selfPerNum"
										value="<?php echo $result['SELF_PER_NUM'];?>"/></td>
								</tr>
								<tr>
									<th>自有产品销量：</th><td  colspan=3><input type="text" id="selfSaleNum"
										value="<?php echo $result['SELF_SALE_NUM'];?>"/></td>
								</tr>
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  style="width:90%;height:80px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
			
				</div>
			</form>
		</div>
			<div class="panel-foot">
					<div class="form-actions" style="padding:5px;">
						<button type="button" class="btn btn-primary btn-save"  data-widget="formSave"  data-options="{form:'#personForm',service:'ProductDev.savePlan'}">保&nbsp;存</button>
					</div>
			</div>
	</div>
</body>
</html>