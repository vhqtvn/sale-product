<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>订单列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

	<script type="text/javascript">
     var accountId = "<?php echo $accountId;?>" ;
     var status = "<?php echo $status;?>"
    </script>
   <?php
   include_once ('config/config.php');
   
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
		echo $this->Html->script('modules/norder/doing_lists');
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
	var img = '<?php echo $this->Html->image('example.gif',array("title"=>"编辑订单")) ?>' ;
</script>
<body>
	<div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="拣货单订单列表" style="padding:2px;">
			<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>订单号：</th>
						<td>
							<input type="text" name="orderId" class="span2" />
						</td>
						<th>内部订单号：</th>
						<td>
							<input type="text" name="orderNumber" class="span2" />
						</td>
						<th>账号：</th>
						<td>
							<select name="accountId" class="span2" >
				     		<option value="">--选择--</option>
					     	<?php
					     		 $amazonAccount  = ClassRegistry::init("Amazonaccount") ;
				   				 $accounts = $amazonAccount->getAllAccounts(); 
					     		foreach($accounts as $account ){
					     			$account = $account['sc_amazon_account'] ;
					     			echo "<option value='".$account['ID']."'>".$account['NAME']."</option>" ;
					     		} ;
					     	?>
							</select>
						</td>
					</tr>
					<tr>
						<th>日期：</th>
						<td>
							<input type="text" name="dateTime" class="span2" data-widget="calendar"/>
						</td>
						<th>SKU：</th>
						<td>
							<input type="text" name="sku" class="span2"/>
						</td>
						<th></th>
						<td>
							<button class="btn btn-primary query query-btn" >查询</button>
						</td>
					</tr>
					<tr class="action-row">
						<th></th>
						<td colspan='2'>
							<button class="btn btn-primary action-btn action-can-disabled" action='4'>打印拣货单</button>
							<button class="btn btn-primary action-btn action-can-disabled" action='10'>导出拣货单</button>
							<button class="btn btn-primary action-btn " action='11'>下载Endicia处理订单</button>
							<!--
							
							-->
						</td>
						<td colspan="3" style="text-align:right;">
							<button class="btn btn-primary action-btn print-btn pick-btn" action='5'>二次分拣</button>
							<button class="btn btn-success btn-outwarehouse" action='6'>订单出仓</button>
							<!--
							<button class="btn btn-danger action-btn print-btn pick-btn" action='6'>同步TN到AMAZON</button>
							-->
						</td>
					</tr>							
				</table>	
			</div>	
			
			<div id="details_tab">
			</div>
			<div class="grid-content" id="tab-content"></div>
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="拣货单列表" style="width:200px;">
			<button class="action add btn btn-primary">创建拣货单</button>
			<div id="picked-grid-content" class="tree" style="padding: 5px; "></div>
		</div>
</body>
</html>
