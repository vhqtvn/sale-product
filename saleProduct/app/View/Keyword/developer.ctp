<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/layout/jquery.layout');
		echo $this->Html->css('../js/tree/jquery.tree');
		
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('layout/jquery.layout');
		echo $this->Html->script('tree/jquery.tree');
		echo $this->Html->script('modules/keyword/developer');
		

		$security  = ClassRegistry::init("Security") ;
		$loginId = $user['LOGIN_ID'] ;
		
		$add_kw_plan						= $security->hasPermission($loginId , 'add_kw_plan') ;
		$add_kw_task							= $security->hasPermission($loginId , 'add_kw_task') ;
	?>
	
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>
   
   <script type="text/javascript">
		var addKwPlan = <?php echo $add_kw_plan?"true":"false" ;?>;
		var addKwTask = <?php echo $add_kw_task?"true":"false" ;?>
   </script>

</head>
<body style="magin:0px;padding:0px;">
		<div class="toolbar toolbar-auto plan-t">
				<table>
					<tr>
						<th>计划名称：
						</th>
						<td>
							<input type="text" name="name" class="input-medium"/>
						</td>					
						<td class="toolbar-btns" rowspan="3">
							<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.plan-grid',qc:'.plan-t'}">查询</button>
							&nbsp;&nbsp;&nbsp;
							<?php if($add_kw_plan){?>
							<button class="add-plan btn btn-primary">添加计划</button>
							<?php }?>
						</td>
					</tr>						
				</table>
		</div>
		<div class="plan-grid" ></div>
		<div class="toolbar task-t toolbar-auto">
				<table>
					<tr>
						<th>任务名称：
						</th>
						<td>
							<input type="text" name="name" class="input-medium"/>
						</td>					
						<td class="toolbar-btns" rowspan="3">
							<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.task-grid',qc:'.task-t'}">查询</button>
							&nbsp;&nbsp;&nbsp;
							<?php if($add_kw_task){?>
							<button class="add-task btn btn-primary "  disabled="disabled">添加任务</button>
							<?php }?>
						</td>
					</tr>						
				</table>
		</div>
		<div class="task-grid" ></div>
</body>
</html>
