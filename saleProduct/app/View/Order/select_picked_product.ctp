<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>订单列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

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
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('tab/jquery.ui.tabs');
		
	?>
  
   <script type="text/javascript">
     var accountId = "" ;
     var status = "5"
   //result.records , result.totalRecord
	 function formatGridData(data){
		var records = data.record ;
 		var count   = data.count ;
 		
 		count = count[0][0]["count(*)"] ;
 		
		var array = [] ;
		$(records).each(function(){
			var row = {} ;
			for(var o in this){
				var _ = this[o] ;
				for(var o1 in _){
					row[o1] = _[o1] ;
				}
			}
			array.push(row) ;
		}) ;
	
		var ret = {records: array,totalRecord:count } ;
			
		return ret ;
	   }

	$(function(){
		var pickedId = "<?php echo $pickId;?>"
			var sqlId = "sql_order_list_picked" ;
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ORDER_ID",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
							if(record.checked >=1){
								$(this).attr("checked",true) ;
							}
					}}},
					 //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
					{align:"center",key:"AUDIT_STATUS",label:"状态",sort:true, width:"8%",format:function(val,record){
						var pickStatus = record.PICK_STATUS ;
						if(pickStatus == 9){
							return "拣货中" ;
						}else if(pickStatus == 10){
							return "完成拣货" ;
						}
						
						var map = {0:"未审核",5:"合格订单",2:"风险订单",3:"待退单",4:"外购订单",6:"加急单",7:"特殊单"} ;
						return map[val] ;
					}},
		           	{align:"center",key:"ORDER_ID",label:"ORDER_ID", width:"15%"},
		           	{align:"center",key:"ORDER_ITEM_ID",label:"ORDER_ITEM_ID", width:"12%"},
		           	{align:"center",key:"SKU",label:"SKU",sort:true, width:"10%"},
		           	{align:"center",key:"PRODUCT_NAME",label:"PRODUCT_NAME", width:"20%"},
		           	{align:"center",key:"PURCHASE_DATE",label:"PURCHASE_DATE",sort:true, width:"20%"},
		           	{align:"center",key:"PAYMENTS_DATE",label:"PAYMENTS_DATE",sort:true, width:"20%"},
		           	{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
		           	{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"},
		           	{align:"center",key:"BUYER_PHONE_NUMBER",label:"BUYER_PHONE_NUMBER", width:"10%"},
		           	{align:"center",key:"QUANTITY_PURCHASED",label:"QUANTITY_PURCHASED", width:"10%"},
		           	{align:"center",key:"CURRENCY",label:"CURRENCY", width:"10%"},
		           	{align:"center",key:"ITEM_PRICE",label:"ITEM_PRICE", width:"10%"},
		           	{align:"center",key:"ITEM_TAX",label:"ITEM_TAX", width:"10%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -155 ;
				 },
				 title:"订单信息列表",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId,status:'',pickStatus:'9'},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			
			$(".action-btn").click(function(){
				var action = $(this).attr("action");
				var checkedRecords = $(".grid-content").llygrid("getSelectedRecords",{key:"ORDER_ID",checked:true},true) ;
				var status = $(this).attr("status");
			
				var orders = [] ;
				$(checkedRecords).each(function(index,item){
					orders.push(item.ORDER_ID+"|"+item.ORDER_ITEM_ID) ;
				}) ;
					
				if( orders.length <=0 ){
					alert("未选中任意订单！");
					return ;
				}	
				
				var text = $.trim( $(this).text() ) ;
				
				var msgs = {
					1:"确认将选择订单添加到拣货单中吗？",
					2:"确认将选择订单移除出拣货单吗？",
					3:"确认完成拣货吗？"
				} ;
				
				var msg = msgs[action] ;
				
				if( window.confirm(msg) ){
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/order/savePickedOrder/"+pickedId ,
						data:{status:status,orders:orders.join(","),memo:$("#memo").val(),action:action},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							alert("保存成功!");
							window.location.reload();
						}
					});
				}
				
				
			}) ;
			
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				//if(currentQueryKey)json.sqlId = currentQueryKey ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   	 $(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'待拣货订单',content:"tab-content"},
					{label:'完成拣货单',content:"tab-content"},
					{label:'合格订单',content:"tab-content"},
					{label:'风险客户',content:"tab-content"},
					//{label:'待退单',content:"tab-content"},
					//{label:'外购订单',content:"tab-content"},
					{label:'加急单',content:"tab-content"},
					{label:'特殊单',content:"tab-content"}
				] ,
				//height:'500px',
				select:function(event,ui){
					var index = ui.index ;
					$(".save-btn").show().html("添加到拣货单").removeClass("btn-danger").addClass("btn-success").attr("action","1");
					$(".pick-btn").hide() ;
					if(index == 0){//拣货单订单
						$(".pick-btn").show() ;
						$(".save-btn").html("移除出拣货单").removeClass("btn-success").addClass("btn-danger").attr("action","2");
						$(".grid-content").llygrid("reload",{pickStatus:9,status:'',sqlId:"sql_order_list_picked"},true) ;
						currentQueryKey = "sql_order_list_picked" ;
					}else if(index == 1){//完成拣货单
						$(".save-btn").hide();
						$(".grid-content").llygrid("reload",{pickStatus:10,status:'',sqlId:"sql_order_list_picked"},true) ;
						currentQueryKey = "sql_order_list_picked" ;
					}else if(index == 2){//合格订单
						$(".grid-content").llygrid("reload",{pickStatus:'',status:5,sqlId:"sql_order_list_picked"},true) ;
						currentQueryKey = "sql_order_list_picked" ;
					}else if(index == 3){//风险客户
						$(".grid-content").llygrid("reload",{pickStatus:'',status:2,sqlId:"sql_order_list_picked"},true) ;
						currentQueryKey = "sql_order_list_picked" ;
					}else if(index == 4){//加急单
						$(".grid-content").llygrid("reload",{pickStatus:'',status:6,sqlId:"sql_order_list_picked"},true) ;
						currentQueryKey = "sql_order_list_picked" ;
					}else if(index == 5){//特殊但
						$(".grid-content").llygrid("reload",{pickStatus:'',status:7,sqlId:"sql_order_list_picked"},true) ;
						currentQueryKey = "sql_order_list_picked" ;
					}
				}
			} ) ;
		}) ;
   	 
   </script>
   
</head>
<!--
风险客户<input type="radio" name="status" value="2">
										待退单<input type="radio" name="status" value="3">
										外购订单<input type="radio" name="status" value="4">
										合格订单<input type="radio" name="status" value="5">
										加急单<input type="radio" name="status" value="6">
										特殊单
-->
<body>
	<div class="toolbar toolbar-auto">
		
		<table style="width:100%;" class="query-table">	
			<tr>
				<th>订单号：</th>
				<td>
					<input type="text" name="orderId"/>
				</td>
				<th>人名：</th>
				<td>
					<input type="text" name="userName"/>
				</td>
				<th>邮件：</th>
				<td>
					<input type="text" name="email"/>
				</td>
			</tr>
			<tr>
				<th>日期：</th>
				<td>
					<input type="text" name="dateTime" data-widget="calendar"/>
				</td>
				<th>SKU：</th>
				<td>
					<input type="text" name="sku"/>
				</td>
				<th></th>
				<td>
					<button class="btn btn-primary query" >查询</button>
				</td>
			</tr>	
			<tr>
				<th></th>
				<td colspan="5">
					<button class="btn btn-primary action-btn print-btn pick-btn" action='4'>合并打印拣货单</button>
					<button class="btn btn-primary action-btn confirm-btn pick-btn" action='3'>确认拣货完成</button>
					&nbsp;&nbsp;
					<button class="btn btn-danger action-btn save-btn" action="2" >移除出拣货单</button>
				</td>
			</tr>						
		</table>
	</div>	
	
	<div id="details_tab">
	</div>
	<div class="grid-content" id="tab-content">
	
	</div>
</body>
</html>
