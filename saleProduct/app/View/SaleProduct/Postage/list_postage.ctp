<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>需求或问题记录</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/saleproduct/postage/list_postage');
	?>
  
</head>
<body>

	
	<div class="toolbar toolbar-auto">
		<table style="width:100%;" class="query-table">	
			<tr>
				<th>名称：</th>
				<td>
					<input type="text" id="name" class="span2"/>
				</td>
				<th>代码：</th>
				<td>
					<input type="text" id="code" class="span2"/>
				</td>
				
				<th></th>
				<td>
					<button class="btn btn-primary query" >查询</button>
					<button class="action add btn btn-primary">添加物流商</button>
				</td>
			</tr>						
		</table>
	</div>
	

	<div class="grid-content"></div>
	<div class="grid-content-services"></div>
</body>
</html>
