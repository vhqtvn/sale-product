	$(function(){
		var sqlId = "sql_sc_order_list_unshipped" ;
		if(!status){
			sqlId = "sql_sc_order_list_unshipped_one" ;
		}
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ORDER_ID",label:"操作",width:"4%",
						render:function(record){
							if(record.IS_PACKAGE || record.C > 1){
								$(this).find("td").css("background","#EEBBFF") ;
							}
						}
						,format:{type:"checkbox",render:function(record){
							if(record.checked >=1){
								$(this).attr("checked",true) ;
							}
					}}},
					 //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
					/*{align:"center",key:"AUDIT_STATUS",label:"状态",sort:true, width:"8%",
						format:{type:"json",content:{0:"未审核",5:"合格订单",2:"风险订单"
						,3:"待退单",4:"外购订单",6:"加急单",7:"特殊单"
					}}},*/
					
					{align:"left",key:"PACKAGE_VALUE",label:"价值", width:"4%",format:{type:"editor",fields:['ORDER_ID']}},
					{align:"left",key:"MAIL_CLASS",label:"MAIL CLASS", width:"200",
						format:{type:"editor",renderType:"select",fields:['ORDER_ID'],valFormat:function(val,record){
							return record.MAIL_CLASS || record.POSTAGE_SERVICE_ID||"" ;
						},data:serviceJson}},
					{align:"left",key:"TRACKING_TYPE",label:"TRACKING", width:"130",
						format:{type:"editor",renderType:"select",fields:['ORDER_ID'],valFormat:function(val,record){
							return val||'2' ;
						},
						data:[{value:'1',text:'1:none'},{value:'2',text:'2:Delivery Confirmation'},{value:'3',text:'3:Signature Confirmation'}]}},
					{align:"left",key:"LENGTH",label:"长", width:"4%",format:{type:"editor",fields:['ORDER_ID'],valFormat:function(val,record){
						return record.LENGTH||record.REAL_LENGTH||'' ;
					}}},
					{align:"left",key:"WIDTH",label:"宽", width:"4%",format:{type:"editor",fields:['ORDER_ID'],valFormat:function(val,record){
						return record.WIDTH||record.REAL_WIDTH||'' ;
					}}},
					{align:"left",key:"HEIGHT",label:"高", width:"4%",format:{type:"editor",fields:['ORDER_ID'],valFormat:function(val,record){
						return record.HEIGHT||record.REAL_HEIGHT||'' ;
					}}},
					{align:"left",key:"WEIGHT",label:"重", width:"4%",format:{type:"editor",fields:['ORDER_ID'],valFormat:function(val,record){
						var realWeight = record['REAL_WEIGHT'] ;
						var weight = record['WEIGHT'] ;
						var w =  weight||realWeight ;
						return (w == 'null' || w== null) ?'':w ;
					}}},
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
					{align:"center",key:"SHIPPED_NUM",label:"发货数量", width:"6%"},
					{align:"center",key:"UNSHIPPED_NUM",label:"未发货数量", width:"6%"},
					{align:"center",key:"AMOUNT",label:"金额", width:"4%" ,align:'right'},
					{align:"center",key:"PURCHASE_DATE",label:"Purchase Date",sort:true, width:"15%"},
			    	{align:"center",key:"LAST_UPDATE_DATE",label:"Last Update Date",sort:true, width:"15%"},
					{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
					{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId},
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
   	 $(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'未审核订单(单品)',content:"tab-content"},
					{label:'未审核订单(多品)',content:"tab-content"},
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
		currentQueryKey = "sql_sc_order_list_unshipped_one" ;
		$(".grid-content").llygrid("reload",{pickStatus:'',status:5,sqlId:"sql_sc_order_list_unshipped_one"},true) ;
	}else if(index == 1){//合格订单
		currentQueryKey = "sql_sc_order_list_unshipped_many" ;
		$(".grid-content").llygrid("reload",{pickStatus:'',status:5,sqlId:"sql_sc_order_list_unshipped_many"},true) ;
	}else if(index == 2){//合格订单
		$(".grid-content").llygrid("reload",{pickStatus:'',status:5,sqlId:"sql_sc_order_list_unshipped"},true) ;
		currentQueryKey = "sql_sc_order_list_unshipped" ;
	}else if(index == 3){//风险客户
		$(".grid-content").llygrid("reload",{pickStatus:'',status:2,sqlId:"sql_sc_order_list_unshipped"},true) ;
		currentQueryKey = "sql_sc_order_list_unshipped" ;
	}else if(index == 4){//待退单
		$(".grid-content").llygrid("reload",{pickStatus:'',status:3,sqlId:"sql_sc_order_list_unshipped"},true) ;
		currentQueryKey = "sql_sc_order_list_unshipped" ;
	}else if(index == 5){//外购订单
		$(".grid-content").llygrid("reload",{pickStatus:'',status:4,sqlId:"sql_sc_order_list_unshipped"},true) ;
		currentQueryKey = "sql_sc_order_list_unshipped" ;
	}else if(index == 6){//加急单
		$(".grid-content").llygrid("relsql_sc_order_list_unshippedoad",{pickStatus:'',status:6,sqlId:"sql_sc_order_list_unshipped"},true) ;
		currentQueryKey = "sql_sc_order_list_unshipped" ;
	}else if(index == 7){//特殊但
		$(".grid-content").llygrid("reload",{pickStatus:'',status:7,sqlId:"sql_sc_order_list_unshipped"},true) ;
		currentQueryKey = "sql_sc_order_list_unshipped" ;
	}
}

$(".lly-grid-cell-input").live("blur",function(){
	var orderId = $(this).attr("ORDER_ID")||$(this).attr("order_id") ;
	var key = $(this).attr("key") ;
	var val = $(this).val() ;
	
	$.dataservice("model:OrderService.updateProcessField",{type:key,orderId:orderId,value:val},function(result){
		//alert(result);
	}) ;

}) ;