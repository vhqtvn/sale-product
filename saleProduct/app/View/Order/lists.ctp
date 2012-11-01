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

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
	?>
  
   <script type="text/javascript">
     var accountId = "<?php echo $accountId;?>" ;
     var status = "<?php echo $status;?>"
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
		var sqlId = "sql_order_list" ;
		if(!status){
			sqlId = "sql_order_list_nostatus" ;
		}
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ORDER_ID",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
							if(record.checked >=1){
								$(this).attr("checked",true) ;
							}
					}}},
					{align:"center",key:"ORDER_ID",label:"状态",sort:true, width:"8%",format:function(val,record){
						if(!record.AUDIT_STATUS) return "未审核";
						return "" ;
					}},
		           	{align:"center",key:"ORDER_ID",label:"ORDER_ID", width:"15%"},
		           	{align:"center",key:"ORDER_ITEM_ID",label:"ORDER_ITEM_ID", width:"12%"},
		           	{align:"center",key:"SKU",label:"SKU", width:"10%"},
		           	{align:"center",key:"PRODUCT_NAME",label:"PRODUCT_NAME", width:"20%"},
		           	{align:"center",key:"PURCHASE_DATE",label:"PURCHASE_DATE", width:"20%"},
		           	{align:"center",key:"PAYMENTS_DATE",label:"PAYMENTS_DATE", width:"20%"},
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
				 height:400,
				 title:"订单信息列表",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId,status:status},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".save-btn").click(function(){
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
				
				if( window.confirm("确认将选择产品添加到["+text+"]中吗？") ){
					
					
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/order/saveAudit" ,
						data:{status:status,orders:orders.join(","),memo:$("#memo").val()},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							alert("保存成功!");
							window.location.reload();
						}
					});
				}
				
				
			}) ;
   	 });
   	 
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
		<table style="width:100%;">
			<tr><th style="width:100px">审核操作：</th>
				<td>
				<?php
					if( $status != 5){
				?>
					<button class="btn btn-primary save-btn" status="5">合格订单</button>
				<?php		
					}
				?>
				<?php
					if( $status != 4){
				?>	
					<button class="btn btn-primary save-btn" status="4">外购订单</button>
				<?php		
					}
				?>
				<?php
					if( $status != 2){
				?>
					<button class="btn btn-danger save-btn" status="2">风险客户</button>
				<?php		
					}
				?>
				<?php
					if( $status != 3){
				?>
					<button class="btn btn-danger save-btn" status="3">待退单</button>
				<?php		
					}
				?>
				<?php
					if( $status != 6){
				?>
					<button class="btn btn-danger save-btn" status="6">加急单</button>
				<?php		
					}
				?>
				<?php
					if( $status != 7){
				?>
					<button class="btn btn-danger save-btn" status="7">特殊单</button>
				<?php		
					}
				?>
				</td>
			</tr>	
			<tr>
				<th>备注：</th>
				<td>
					<textarea id="memo" style="width:100%;height:50px;"></textarea>
				</td>
			</tr>					
		</table>					

	</div>	
	
	<div class="grid-content">
	
	</div>
</body>
</html>
