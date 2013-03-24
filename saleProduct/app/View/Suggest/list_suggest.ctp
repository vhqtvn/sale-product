<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>需求或问题记录</title>
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
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/suggest/list_suggest');
	?>
  
</head>
<body>

	
	<div class="toolbar toolbar-auto">
		<table style="width:100%;" class="query-table">	
			<tr>
				<th>标题：</th>
				<td>
					<input type="text" id="title" class="span2"/>
				</td>
				<th>类型：</th>
				<td>
					<select name="type"  class="span2">
						<option value="">-选择-</option>
						<option value="1">需求</option>
						<option value="2">问题</option>
					</select>
				</td>
				<th>状态：</th>
				<td>
					<select name="status"  class="span2">
						<option value="">全部</option>
						<option value="0">未处理</option>
						<option value="1">已处理</option>
						<option value="2">暂不处理</option>
						<option value="3">处理中</option>
						
					</select>
				</td>
			</tr>
			<tr>
				<th>重要程度：</th>
				<td>
					<select name="importantLevel"  class="span2">
						<option value="">-选择-</option>
						<option value="1">非常重要</option>
						<option value="2">重要</option>
						<option value="3">不重要</option>
					</select>
				</td>
				<th>紧急程度：</th>
				<td>
					<select name="eneryLevel"  class="span2">
						<option value="">--</option>
						<option value="1">非常紧急</option>
						<option value="2">紧急</option>
						<option value="3">不紧急</option>
					</select>
				</td>
				<th></th>
				<td>
					<button class="btn btn-primary query" >查询</button>
					<button class="action add btn btn-primary">添加问题或需求</button>
				</td>
			</tr>						
		</table>
	</div>
	

	<div class="grid-content">
	</div>
</body>
</html>
