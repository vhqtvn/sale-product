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
		echo $this->Html->script('modules/keyword/nicheList');
	?>
	
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body style="magin:0px;padding:0px;">
		
		<div class="toolbar task-t toolbar-auto">
				<table>
					<tr>
						<th>关键字名称：
						</th>
						<td>
							<input type="text" name="name" class="input-medium"/>
						</td>	
						<th>状态：</th>
						<td>
							<select id="status">
								<option value="">全部</option>
								<option value="1" selected="selected">待审批</option>
								<option value="2">审批通过</option>
								<option value="3">废弃</option>
							</select>
						</td>					
						<td class="toolbar-btns" rowspan="3">
							<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.niche-grid',qc:'.task-t'}">查询</button>
						</td>
					</tr>						
				</table>
		</div>
		<div class="niche-grid" ></div>
</body>
</html>
