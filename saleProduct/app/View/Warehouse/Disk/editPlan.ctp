<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>盘点计划编辑</title>
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
		echo $this->Html->script('modules/warehouse/disk/editPlan');
		echo $this->Html->script('calendar/WdatePicker');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		
		$loginId   = $user['LOGIN_ID'] ;
		
		$result = null ;
		$diskId = $params['arg1'] ;
		if(!empty($diskId)){
			$result = $SqlUtils->getObject("sql_warehouse_disk_plan_lists",array('id'=>$diskId) ) ;
		}
		
		$defaultCode = "DP-".date("Ymd") ;
	?>
	
	<script>
		var diskId = '<?php echo $diskId ; ?>';
   </script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        <input type="hidden" id="id" value="<?php echo $result['ID'];?>"/> 
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table " >
							<caption>盘点计划基本信息</caption>
							<tbody>	
								<tr>
									<th>名称：</th><td colspan=3><input data-validator="required" type="text" id="name"
										value="<?php echo $result['NAME'];?>"/></td>
								</tr>
								<tr>
									<th>代码：</th><td><input data-validator="required" type="text" id="code"
										value="<?php
											if( empty($result['CODE']) ){
												echo $defaultCode ;
											}else
										 		echo $result['CODE'];?>"/></td>
								
									<th>经办人：</th><td><input data-validator="required" type="text" id="charger"
										value="<?php echo $result['CHARGER'];?>"/></td>
								</tr>
																	   
								<tr>
									<th>开始日期：</th>
									<td>
									<?php
										if(!empty($result['START_TIME'])){
											echo $result['START_TIME'] ;
										}else{
										?>
									<input data-validator="required" type="text" id="startTime"
										value="<?php echo $result['START_TIME'];?>" data-widget="calendar"/>
									<?php	
										}
									?>
									</td>
									<th>结束日期：</th>
									<td>
									<?php
										if(!empty($result['END_TIME'])){
											echo $result['END_TIME'] ;
										}else{
										?>
									<input data-validator="required" type="text" id="endTime"
										value="<?php echo $result['END_TIME'];?>" data-widget="calendar"/>
									<?php	
										}
									?>
									</td>
								</tr>
								<tr>
									<th>目标仓库：</th>
									<td colspan="3">
									<?php
										if(!empty($result['WAREHOUSE_NAME'])){
											echo $result['WAREHOUSE_NAME'] ;
										}else{
										?>
									<input data-validator="required" type="hidden" id="warehouseId" 
										value="<?php echo $result['WAREHOUSE_ID'];?>"/>
									<input type="text" data-validator="required" id="warehouseName" readonly
										value="<?php echo $result['WAREHOUSE_NAME'];?>"/>
									<button class="btn btn-warehouse">选择</button>
									<?php	
										}
									?>
									</td>
								</tr>
								
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  style="width:90%;height:40px;"><?php echo $result['MEMO'];?></textarea>
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
							<button type="button" class="btn btn-primary btn-save">保&nbsp;存</button>
						</div>
					</div>
	</div>
</body>
</html>