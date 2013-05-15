<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>编辑采购任务</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('calendar/WdatePicker');
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		
		
		$taskId = $params['arg1'] ;
		$defaultCode = null ;
		$task = null ;
		if(!empty($taskId)){
			$task = $SqlUtils->getObject("sql_purchase_task_getById",array('taskId'=>$taskId)) ;
			$defaultCode = $task['TASK_CODE'] ;
		}else{
			$defaultCode = $SqlUtils->getUserDefaultCode("PT")  ;
		}
		
		//sql_purchase_task_getById
		
		//if( empty($result['IN_NUMBER']) ){
		/*	$index = $SqlUtils->getMaxValue("PT" , null , 1) ;
			if( strlen($index) < 5 ){
				$len = 5-strlen($index) ;
				for($i=0 ;$i < $len ;$i++){
					$index = '0'.$index ;
				}
			}
			$defaultCode = "PT-".strtoupper($user['LOGIN_ID']) ."-".date("ymd").'-'.$index ;*/
		//}
	?>
  
   <style>
   		*{
   			font:12px "微软雅黑";
   		}

		.rule-content-item{
			clear:both;
		}

		.item-label,.item-relation,.item-value,.item-value{
			float:left;
		}
   </style>

   <script>
   		var planId = '' ;
   
		$(function(){
			
			$(".btn-primary").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;

					$.dataservice("model:Sale.savePurchaseTask",json,function(){
						window.close();
					}) ;
				};
				return false ;
			}) ;
			
		}) ;
		
   </script>

</head>
<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>创建采购任务</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
			<input id="id" type="hidden" value="<?php echo $task['ID']?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<caption>基本信息</caption>
								<tr>
									<th>任务编码：</th>
									<td colspan="3"><input type="text"  disabled id="taskCode"  
										 style="width:300px;" value="<?php echo $defaultCode;?>"/></td>
								</tr>
								<tr>
									<th>开始时间：</th>
									<td><input data-validator="required" type="text" id="startTime" data-widget="calendar"  style="width:150px!important;"
										value="<?php echo $task['START_TIME'];?>"/></td>
									<th>结束时间：</th>
									<td><input data-validator="required" type="text" id="endTime" data-widget="calendar"  style="width:150px!important;"
										value="<?php echo $task['END_TIME'];?>"/></td>
								</tr>
								<tr>
									<th>备注：</th><td colspan="3"><textarea id="memo" style="width:300px;height:100px;" data-validator="required"
										><?php echo $task['MEMO']?></textarea></td>
								</tr>
							</table>
						</div>
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</html>