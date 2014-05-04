<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>RAM事件</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('jquery.ui');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('modules/warehouse/ram/events_purchase');
		
		/*RMA_CUSTOMER_SERVCE
		RMA_AUDIT
		RMA_Logistics
		RMA_Financial*/
		$security  = ClassRegistry::init("Security") ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		
		$loginId = $user['LOGIN_ID'] ;
		
		$rmaEdit 	= $security->hasPermission($loginId , 'RMA_EDIT') ;
	?>
	
	<style>
		.count{
			color:red;
		}
		
		.flow-node{
			cursor:pointer;
		}
		
		.flow-node.disabled{
			background:#AAEEBB ;
			border-color:#AAEEBB ;
			color:#000 ;
		}
		
		.flow-node.actived {
			border-color: #3809F7;
			background-color: #3809F7;
			color: #EEE;
		}
	</style>
</head>
<body>
<div class="toolbar toolbar-auto">
		<table>
			<tr>
				<th>
					RMA编号:
				</th>
				<td>
					<input type="text" name="rmaId" class="span2"/>
				</td>
				<th>
					订单编号:
				</th>
				<td>
					<input type="text" name="orderId" class="span2"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn ">查询</button>
					<?php  if( $rmaEdit ) { ?>
					<button class="add-btn btn btn-primary">添加RAM事件</button>
					<?php }?>
				</td>
			</tr>						
		</table>
	</div>
	
	<div id="tabs-default" class="view-source">
	</div>
	
	<div class="flow-bar">
		<center>
		<table class="flow-table">						
			<tbody>
				<tr>						
					<td><div class="flow-node disabled" status="10">编辑中</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="20">待审批</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="30">退货标签确认</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="40">退货确认</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="50">退货入库</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="60">退款</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="70">重发配置</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="75">确认重发</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="78">重发收货</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="79">Feedback</div></td>
					<td class="flow-split">-</td>
					<td><div class="flow-node disabled" status="80">结束</div></td>
				</tr>					
			</tbody>
		</table>
		</center>
	</div>
	<div class="grid-content" id="tab-container" style="margin-top:5px;"></div>
</body>
</html>
