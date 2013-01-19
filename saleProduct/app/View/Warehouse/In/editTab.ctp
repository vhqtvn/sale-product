<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>物流单信息</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

	
   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('modules/warehouse/in/editTab');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		$inId = $params['arg1'];
		
		//获取
		$warehoseIn = $SqlUtils->getObject("sql_warehouse_in_getById",array("id"=>$inId)) ;
		
	?>
	
	<script type="text/javascript">
     var inId = "<?php echo $inId;?>" ;
     var currentStatus = "<?php echo $warehoseIn['STATUS'];?>" ;
    </script>
	
	<style type="text/css">
		.flow-node{
			width:50px; 
			height:20px; 
			border:5px solid #0FF; 
			border-radius:5px;
			font-weight:bold;
		}
		
		.flow-node.active{
			border-color:#3809F7 ;
			background-color:#3809F7 ;
			color:#EEE;
		}
		
		.flow-node.passed{
			border-color:#92E492 ;
			background-color:#92E492 ;
			
		}
		
		.flow-node.disabled{
			border-color:#CCC ;
			background-color:#CCC ;
			color:#EEE;
		}
		
		.flow-table{
			text-align:center;
		}
		
		.flow-bar{
			width:100%;margin:10px auto;text-align:center;
			position:relative;
		}
		
		.flow-action{
			position:absolute;;
			right:10px;
			top:3px;
		}
		
		.flow-split{
			font-size:30px;
		}
		
		.memo{
			position:absolute;
			top:40px;
			z-index:1;
			right:10px;
			width:300px;
			height:50px;
			background:#ffd700;
			display:none;
		}
	</style>
</head>

<body>
	<div  class="flow-bar">
	<center>
		<table class="flow-table">
			<tr>
				<td>
					<div class="flow-node passed" status='0'>编辑中</div>
				</td>
				<td>--</td>
				<td><div class="flow-node active" status='10'>待审批</div></td>
				<td>--</td>
				<td><div class="flow-node disabled" status='20'>待发货</div></td>
				<td>--</td>
				<td><div class="flow-node disabled" status='30'>已发货</div></td>
				<td>--</td>
				<td><div class="flow-node disabled" status='40'>到达海关</div></td>
				<td>--</td>
				<td><div class="flow-node disabled" status='50'>验货中</div></td>
				<td>--</td>
				<td><div class="flow-node disabled" status='60'>入库完成</div></td>
			</tr>
		</table>
		
		<div class="flow-action">
			<button class="btn btn-primary">执行待审批</button>
		</div>
	</center>
	</div>

	<div id="details_tab">
	</div>
</body>
</html>
