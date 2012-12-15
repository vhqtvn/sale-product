<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>入库单计划编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
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
		echo $this->Html->script('modules/warehouse/in/edit');
		echo $this->Html->script('calendar/WdatePicker');
	
	?>
	
	<script>
	
   </script>
</head>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>入库单信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator" class="form-horizontal" >
	        <input type="hidden" id="id" value="<?php echo $result['ID'];?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table col4" >
							<tbody>										   
								<tr>
									<th>入库号：</th><td><input type="text" data-validator="required" id="inNumber" value="<?php echo $result['IN_NUMBER'];?>"/></td>
								
									<th>负责人：</th>
									<td>
									<input type="hidden" data-validator="required" id="charger" 
											value="<?php echo $result['CHARGER'];?>"/>
									<input type="text" data-validator="required" id="chargerName" class="span2" readonly
											value="<?php echo $result['CHARGER_NAME'];?>"/>
									<button class="btn btn-charger">选择</button>
									</td>
								</tr>
								<tr>
									<th>目标仓库：</th>
									<td colspan=3>
									<input data-validator="required" type="hidden" id="warehouseId" 
										value="<?php echo $result['WAREHOUSE_ID'];?>"/>
									<input type="text" data-validator="required" id="warehouseName" readonly
										value="<?php echo $result['WAREHOUSE_NAME'];?>"/>
									<button class="btn btn-warehouse">选择</button>
									</td>
								</tr>
								<tr>
									<th>运输公司：</th><td colspan=3><input data-validator="required" type="text" id="shipCompany"
										value="<?php echo $result['SHIP_COMPANY'];?>"/></td>
								</tr>
								<tr>
									<th>运输方式：</th><td colspan=3><input data-validator="required" type="text" id="shipType"
										value="<?php echo $result['SHIP_TYPE'];?>"/></td>
								</tr>
								<tr>
									<th>达到港口：</th><td colspan=3><input type="text" id="arrivalPort" data-validator="required"
										value="<?php echo $result['ARRIVAL_PORT'];?>"/></td>
								</tr>
								<tr>
									<th>发货时间：</th><td><input type="text" id="shipDate" data-widget="calendar" data-validator="required"
										value="<?php echo $result['SHIP_DATE'];?>"/></td>
									<th>预计到达时间：</th><td><input type="text" id="planArrivalDate" data-widget="calendar" data-validator="required"
										value="<?php echo $result['PLAN_ARRIVAL_DATE'];?>"/></td>
								</tr>
								<tr>
									<th>运单号：</th><td><input type="text" id="shipNo"
										value="<?php echo $result['SHIP_NO'];?>"/></td>
			
									<th>物流跟踪号：</th><td><input type="text" id="shipTracknumber"
										value="<?php echo $result['SHIP_TRACKNUMBER'];?>"/></td>
								</tr>
								<tr>
									<th>备注：</th><td  colspan=3>
										<textarea name="memo"  style="width:90%;height:100px;"><?php echo $result['MEMO'];?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions col4">
							<button type="button" class="btn btn-primary btn-save">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>