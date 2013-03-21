	$(function(){
			var sqlId = "sql_sc_order_list" ;
			
			$(".grid-content").llygrid({
				columns:[
				      	{align:"center",key:"ORDER_ID",label:"Order Id", width:"15%"},
				      	{align:"left",key:"ORDER_NUMBER",label:"内部订单号", width:"8%"},
				      	{align:"left",key:"ORDER_PRODUCTS",label:"订单货品", width:"10%",format:function(val,record){
				      		val = val||"" ;
				      		var html = [] ;
				      		$( val.split(";") ).each(function(index,item){
				      			var array = item.split("|") ;
				      			item&& html.push("<img src='/saleProduct"+array[0]+"' style='width:25px;height:25px;'>") ;
				      		})  ;
				      		return html.join("") ;
				      	}},
				      	{align:"left",key:"ORDER_STATUS",label:"状态", width:"8%"},
				    	{align:"left",key:"ACCOUNT_NAME",label:"账号", width:"8%"},
						{align:"center",key:"SHIPPED_NUM",label:"发货数量", width:"6%"},
						{align:"center",key:"UNSHIPPED_NUM",label:"未发货数量", width:"6%"},
						{align:"center",key:"AMOUNT",label:"金额", width:"4%" ,align:'right'},
						{align:"center",key:"PURCHASE_DATE",label:"Purchase Date",sort:true, width:"15%"},
				    	{align:"center",key:"LAST_UPDATE_DATE",label:"Last Update Date",sort:true, width:"15%"},
						{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
						{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -155 ;
				 },
				 title:"订单信息列表",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:'',status:status},
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
			
			$(".export").click(function(){
				openCenterWindow("/saleProduct/index.php/order/selectExportOrder/",1000,600) ;
			}) ;
			
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				//if(currentQueryKey)json.sqlId = currentQueryKey ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   
$(".lly-grid-cell-input").live("blur",function(){
	var orderId = $(this).attr("ORDER_ID")||$(this).attr("order_id") ;
	var key = $(this).attr("key") ;
	var val = $(this).val() ;
	
	$.dataservice("model:OrderService.updateProcessField",{type:key,orderId:orderId,value:val},function(result){
		//alert(result);
	}) ;

}) ;