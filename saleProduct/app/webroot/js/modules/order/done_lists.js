	$(function(){
			var sqlId = "nsql_order_doneList" ;//"sql_order_done_list" ;
		
		    $(".btn-startRma").live("click",function(){
		    	var orderId = $(this).attr("orderId") ;
		    	openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Ram.editEvent/"+orderId,830,480) ;
		    }) ;
		 
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ORDER_ID",label:"操作",width:"5%",render:function(record){
							if(record.IS_PACKAGE || record.C > 1){
								$(this).find("td").css("background","#EEBBFF") ;
							}
						}
						,format:function(val,record){
						return "<button class='btn btn-danger btn-startRma' orderId='"+val+"'>RMA</button>" ;
					}},
					 //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
					{align:"center",key:"AUDIT_STATUS",label:"状态",sort:true, width:"8%",
						format:{type:"json",content:{0:"未审核",5:"合格订单",2:"风险订单"
						,3:"待退单",4:"外购订单",6:"加急单",7:"特殊单"
					}}},
					//{align:"center",key:"TRACK_NUMBER",label:"Tracking Number", width:"20%",format:{type:"editor",fields:['ORDER_ID','ORDER_ITEM_ID']}},
		           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
			        }},
			        {align:"left",key:"ORDER_NUMBER",label:"内部订单号", width:"10%"},
		           	{align:"left",key:"REAL_SKU",label:"货品SKU", width:"10%"},
			        {align:"left",key:"NAME",label:"货品名称", width:"10%"},
		           	{align:"center",key:"IMAGE_URL",label:"货品图片",width:"4%",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           		}else{
		           			return "" ;
		           		}
		           		return "<img src='/saleProduct/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
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
				 querys:{sqlId:sqlId,accountId:accountId,status:'',unRedoStatus:1},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			
			
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   	 $(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'完成订单',content:"tab-content"},
					{label:'RMA处理订单',content:"tab-content"}/*,
					{label:'待审批重发货订单',content:"tab-content"},
					{label:'待退货订单',content:"tab-content"},
					{label:'待退款订单',content:"tab-content"},
					{label:'待重发货订单',content:"tab-content"},
					{label:'售后订单',content:"tab-content"}*/
				] ,
				//height:'500px',
				select:function(event,ui){
					var index = ui.index ;
					renderAction(index);
					
				}
			} ) ;
		}) ;
 
var actionType = 0 ;		
function renderAction(index){
	actionType = index ;
	$(".save-btn").show() ;
	if(index == 0){//拣货中
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{rmaStatus:''}) ;
	}else if(index == 1){//RMA订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{rmaStatus:'1'}) ;
	}/*else if(index == 2){//待审批重发货订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:3,status:''}) ;
	}else if(index == 3){//退货订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:1,status:''}) ;
	}else if(index == 4){//退款订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:201,status:''}) ;
	}else if(index == 5){//重发货
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:301,status:''}) ;
	}else if(index == 6){//售后订单
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{unRedoStatus:'',redoStatus:"",status:'',serviceStatus:"doing"}) ;
	}*/
}