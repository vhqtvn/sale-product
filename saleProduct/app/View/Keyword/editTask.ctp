<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>关键字计划编辑</title>
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
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('modules/keyword/editKeyword');
		
		$planId = $params['arg1'] ;
		$taskId =  $params['arg2'] ;
		$keyword  = ClassRegistry::init("Keyword") ;
		$plan = $keyword->getObject("d_sc_task_getById",array("id"=>$taskId)) ;
	?>
  
</head>

<script>
	var planId = "<?php echo $planId;?>" ;
</script>

<body class="container-popup">
	<!-- apply 主场景 -->
	<div class="apply-page">
		<!-- 页面标题 -->
		<div class="page-title">
			<h2>任务基本信息</h2>
		</div>
		<div class="container-fluid">

	        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
	        	<input type="hidden" id="id" value="<?php echo $plan['task_id'] ;?>"/>
				<!-- panel 头部内容  此场景下是隐藏的-->
				<div class="panel apply-panel">
					<!-- panel 中间内容-->
					<div class="panel-content">
						<!-- 数据列表样式 -->
						<table class="form-table" >
							<tbody>
								<tr>
									<th>任务名称：</th>
									<td><input type="text" style="width:90%;"  data-validator="required"
										id="name"  name="name" value="<?php echo $plan['name'] ;?>"
										/></td>
								</tr>
								<tr>
									<th>备注：</th>
									<td><textarea id="memo" style="width:90%;height:100px;"><?php echo $plan['memo'] ;?></textarea></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<!-- panel脚部内容-->
                    <div class="panel-foot">
						<div class="form-actions">
							<button type="button" class="btn btn-primary save-task">提&nbsp;交</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>