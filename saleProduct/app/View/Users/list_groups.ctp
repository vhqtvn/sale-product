<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>llygrid demo</title>
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
		
		echo $this->Html->script('modules/users/list_groups');
	?>
  
   <script type="text/javascript">


	
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
   </style>

</head>
<body>


	<div class="toolbar toolbar-auto">
		<table style="width:100%;" class="query-table">	
			<tr>
				<th>名称：</th>
				<td>
					<input type="text" id="name"/>
				</td>
				<th>代码：</th>
				<td>
					<input type="text" id="code"/>
				</td>
				<th></th>
				<td>
					<button class="btn btn-primary query" >查询</button>
					<button class="btn btn-primary add-btn" >添加用户组</button>
				</td>
			</tr>						
		</table>	
		<hr style="margin:2px;"/>	
	</div>
	

	<div class="grid-content">
	
	</div>
</body>
</html>
