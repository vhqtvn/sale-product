<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>物流单信息</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

	
   <?php
   include_once ('config/config.php');
   
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
		$loginId   = $user['LOGIN_ID'] ;
		$inId = $params['arg1'];
		
		//获取
		$warehoseIn = $SqlUtils->getObject("sql_warehouse_in_getById",array("id"=>$inId)) ;
		
	?>
	
	<script type="text/javascript">
     var inId = "<?php echo $inId;?>" ;
     var currentStatus = "<?php echo $warehoseIn['STATUS'];?>" ;
     
     function AuditAction(status , statusLabel){
		if(window.confirm("确认【"+statusLabel+"】？")){
			var json = {inId:inId,status:status,memo:$(".memo").val()} ;
			$.dataservice("model:Warehouse.In.doStatus",json,function(result){
				window.location.reload();
			});
		}
	}
	
	function productInWarehouse(){
		openCenterWindow(contextPath+"/page/forward/Warehouse.In.process/"+inId+"/"+status,860,630) ;
	}

	function printBox(){
		window.location.href = contextPath+"/excel/box/"+inId ;
	}

	function printInvoice(){
		window.location.href = contextPath+"/excel/read/"+inId ;
		//openCenterWindow(contextPath+"/excel/read/"+inId,860,630) ;
	}
     
     var flowData = [
		{status:0,label:"编辑中",memo:true
			<?php if( $security->hasPermission($loginId , 'IN_STATUS0')) { ?>
			,actions:[{label:"提交审批",action:function(){ AuditAction(10,"提交审批") }}]
			<?php };?>
		},
		{status:10,label:"待审批",memo:true
			<?php if( $security->hasPermission($loginId , 'IN_STATUS10')) { ?>
			,actions:[{label:"审批通过",action:function(){ AuditAction(20,"审批通过") } },
				{label:"审批不通过",action:function(){ AuditAction(0,"审批不通过") } }]
			<?php };?>
		},
		{status:20,label:"待发货",memo:true
			<?php if( $security->hasPermission($loginId , 'IN_STATUS20')) { ?>
			,actions:[
{label:"导出装箱单",action:function(){ printBox();} },
				{label:"导出发票",action:function(){ printInvoice();} },
				{label:"发货完成",action:function(){ AuditAction(30,"发货完成") } }
			]
			<?php };?>
		},
		{status:30,label:"已发货",memo:true
			<?php if( $security->hasPermission($loginId , 'IN_STATUS30')) { ?>
			,actions:[
{label:"导出装箱单",action:function(){ printBox();} },
						{label:"导出发票",action:function(){ printInvoice();} },
						{label:"到达海关",action:function(){ AuditAction(40,"到达海关") } }
			]
			<?php };?>
		},
		{status:40,label:"到达海关",memo:true
			<?php if( $security->hasPermission($loginId , 'IN_STATUS40')) { ?>
			,actions:[{label:"开始验货",action:function(){ AuditAction(50,"开始验货") } }]
			<?php };?>
		},
		{status:50,label:"验货中"
			<?php if( $security->hasPermission($loginId , 'IN_STATUS50')) { ?>
			,actions:[{label:"货品验收",action:function(){ productInWarehouse() ; } } ]
			<?php };?>
		},
		{status:60,label:"入库中"
			<?php if( $security->hasPermission($loginId , 'IN_STATUS60')) { ?>
			,actions:[
				{label:"货品入库",action:function(){ productInWarehouse() }   }
			]
			<?php };?>
		},
		{status:70,label:"入库完成"
			,actions:[
{label:"导出装箱单",action:function(){ printBox();} },
{label:"导出发票",action:function(){ printInvoice();} },
						{label:"查看入库货品",action:function(){ productInWarehouse();} } 
			]
		}
	] ;
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
			top:48px;
		}
		
		.flow-split{
			font-size:30px;
		}
		
		.memo{
			position:absolute;
			top:80px;
			z-index:1;
			right:10px;
			width:300px;
			height:50px;
			background:#ffd700;
			display:none;
		}
		
		.memo-control{
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
		</div>
	</center>
	</div>

	<div id="details_tab">
	</div>
</body>
</html>
