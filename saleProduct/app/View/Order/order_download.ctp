<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>订单列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

	<script type="text/javascript">
     var accountId = "<?php echo $accountId;?>" ;
     var status = ""
    </script>
   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		echo $this->Html->css('../js/layout/jquery.layout');
		echo $this->Html->css('../js/tree/jquery.tree');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/order/order_download');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('layout/jquery.layout');
		echo $this->Html->script('tree/jquery.tree');
		
	?>
</head>
<!--
风险客户<input type="radio" name="status" value="2">
										待退单<input type="radio" name="status" value="3">
										外购订单<input type="radio" name="status" value="4">
										合格订单<input type="radio" name="status" value="5">
										加急单<input type="radio" name="status" value="6">
										特殊单
-->
<script>
	var img = '<?php echo $this->Html->image('example.gif',array("title"=>"下载处理完成订单")) ?>' ;
</script>
<body>
	<div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="下载订单列表" style="padding:2px;">
			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>订单号：</th>
						<td>
							<input type="text" name="orderId"/>
						</td>
						<th>内部订单号：</th>
						<td>
							<input type="text" name="orderNumber"/>
						</td>
					</tr>
					<tr>
						<th>SKU：</th>
						<td>
							<input type="text" name="sku" class="span2"/>
						</td>
						<th></th>
						<td>
							<button class="btn btn-primary query" >查询</button>
							<button class="download btn btn-primary">下载订单处理信息</button>
							<!--
							<button class="btn btn-danger toamazon" disabled>同步AMAZON</button>
							-->
						</td>
					</tr>						
				</table>	
			</div>	
			
			<div id="details_tab"></div>
			<div class="grid-content" id="tab-content"></div>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="下载历史列表" style="width:200px;">
			<div id="picked-grid-content" class="tree" style="padding: 5px; "></div>
		</div>
</body>
</html>
