<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>货品管理</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
	<script>
		var deleteHtml = "" ;
	</script>
   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/warehouse/design/loadUnitProduct');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('tab/jquery.ui.tabs');
		
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["GROUP_CODE"] ;
		if($loginId == 'general_manager'){
	?>
	<script>
		var deleteHtml = "<a href='#' class='action giveup btn'   type=3>删除</a>" ;
	</script>
	<?php
		}
	?>

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
				<th>SKU：</th>
				<td>
					<input type="text" id="sku"/>
				</td>
				<th></th>
				<td>
					<button class="btn btn-primary query" >查询</button>
				</td>
			</tr>						
		</table>	
		<hr style="margin:2px;"/>	
	</div>
	
	<div class="row-fluid" style="margin-top:10px;">
		<div class="grid-content span4"></div>
		<div class="grid-details-content span8">4444444444</div>
	</div>
	
</body>
</html>
