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
					 //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
					{align:"center",key:"AUDIT_STATUS",label:"状态",sort:true, width:"8%",
						format:{type:"json",content:{0:"未审核",5:"合格订单",2:"风险订单"
						,3:"待退单",4:"外购订单",6:"加急单",7:"特殊单"
					}}},
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
					{label:'未审核订单',content:"tab-content"},
					{label:'合格订单',content:"tab-content"},
					{label:'风险客户',content:"tab-content"},
					{label:'待退单',content:"tab-content"},
					{label:'外购订单',content:"tab-content"},
					{label:'加急单',content:"tab-content"},
					{label:'特殊单',content:"tab-content"}
				] ,
				//height:'500px',
				select:function(event,ui){
					var index = ui.index ;
					renderAction(index);
					
				}
			} ) ;
		}) ;
   	 
function renderAction(index){
	$(".save-btn").show() ;
	if(index == 0){//未审核订单
		currentQueryKey = "sql_order_list_nostatus" ;
		$(".grid-content").llygrid("reload",{pickStatus:'',status:5,sqlId:"sql_order_list_nostatus"},true) ;
	}else if(index == 1){//合格订单
		$(".grid-content").llygrid("reload",{pickStatus:'',status:5,sqlId:"sql_order_list"},true) ;
		currentQueryKey = "sql_order_list" ;
	}else if(index == 2){//风险客户
		$(".grid-content").llygrid("reload",{pickStatus:'',status:2,sqlId:"sql_order_list"},true) ;
		currentQueryKey = "sql_order_list" ;
	}else if(index == 3){//待退单
		$(".grid-content").llygrid("reload",{pickStatus:'',status:3,sqlId:"sql_order_list"},true) ;
		currentQueryKey = "sql_order_list" ;
	}else if(index == 4){//外购订单
		$(".grid-content").llygrid("reload",{pickStatus:'',status:4,sqlId:"sql_order_list"},true) ;
		currentQueryKey = "sql_order_list" ;
	}else if(index == 5){//加急单
		$(".grid-content").llygrid("reload",{pickStatus:'',status:6,sqlId:"sql_order_list"},true) ;
		currentQueryKey = "sql_order_list" ;
	}else if(index == 6){//特殊但
		$(".grid-content").llygrid("reload",{pickStatus:'',status:7,sqlId:"sql_order_list"},true) ;
		currentQueryKey = "sql_order_list" ;
	}
}