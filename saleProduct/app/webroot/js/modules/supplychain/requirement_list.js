$(function(){

	$(".grid-content").llygrid({
		columns:[
		    {align:"center",key:"ID",label:"操作", width:"10%",format:function(val,record){
		    	var html = [] ;
		    	html.push("<a href='#' class='action edit_requirement_plan' val='"+val+"'>处理</a>&nbsp;") ;
		    	return html.join("") ;
		    }},
			{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left"},
			{align:"center",key:"STATUS_C",label:"未设置成本",group:"分类",width:"7%",forzen:false,align:"left",format:function(val,record){
				if(!val) return "-" ;
				return "<a href='#'  class='status-c'>"+val+"</a>" ;
			}},
			{align:"center",key:"STATUS_L",label:"利润不达标",group:"分类",width:"7%",forzen:false,align:"left",format:function(val,record){
				if(!val) return "-" ;
				return "<a href='#'  class='status-l'>"+val+"</a>" ;
			}},
			{align:"center",key:"STATUS0",label:"未审批",group:"状态",width:"6%",forzen:false,align:"left"},
			{align:"center",key:"STATUS1",label:"审批通过",group:"状态",width:"6%",forzen:false,align:"left"},
			{align:"center",key:"STATUS2",label:"审批不通过",group:"状态",width:"6%",forzen:false,align:"left"},
			{align:"center",key:"STATUS3",label:"采购中",group:"状态",width:"6%",forzen:false,align:"left"},
			{align:"center",key:"STATUS4",label:"采购完成",group:"状态",width:"6%",forzen:false,align:"left"},
			{align:"center",key:"STATUS5",label:"入库中",group:"状态",width:"6%",forzen:false,align:"left"},
			{align:"center",key:"STATUS6",label:"需求完成",group:"状态",width:"6%",forzen:false,align:"left"},
           	{align:"center",key:"CREATE_DATE",label:"创建时间",width:"10%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height()-140 ;
		 },
		 title:"需求计划列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_requirement_list",countSqlId:"sql_supplychain_requirement_list_count"},
		 loadMsg:"数据加载中，请稍候......",
		 rowDblClick:function(row,record){
			 //$(".grid-content-detials").llygrid("reload",{accountId:record.ACCOUNT_ID,shippmentId:record.SHIPMENT_ID},true) ;
		 },loadAfter:function(){
			 
			 $(".grid-content").find(".status-c").click(function(){
				 var record = $(this).closest("tr").data("record") ; 
				 openCenterWindow(contextPath+"/page/forward/SupplyChain.requirement_gen_log/"+record.ID+"/C",1000,600,function(result,result1){
					 	if(result1)$(".grid-content").llygrid("reload",{},true) ;
				 }) ;
			 }) ;

			 $(".grid-content").find(".edit_requirement_plan").click(function(){
				 var record = $(this).closest("tr").data("record") ; 
				 openCenterWindow(contextPath+"/page/forward/SupplyChain.requirement_plan_edit/"+record.ID,1000,600,function(result,result1){
					 	if(result1)$(".grid-content").llygrid("reload",{},true) ;
				 }) ;
			 }) ;
			 
			 $(".grid-content").find(".gen_log").click(function(){
				 var record = $(this).closest("tr").data("record") ; 
				 openCenterWindow(contextPath+"/page/forward/SupplyChain.requirement_gen_log/"+record.ID,1000,600,function(result){
					 	var val = $.dialogReturnValue() ;
					 	alert(val);
						if(result)$(".grid-content").llygrid("reload",{},true) ;
				 }) ;
			 }) ;
			 
		 }
	}) ;
	
/*
	$(".grid-content-detials").llygrid({
		columns:[
			{align:"center",key:"SHIPMENT_ID",label:"ShipmentId",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"SELLER_SKU",label:"Seller Sku",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"FULFILLMENT_NETWORK_SKU",label:"Fulfillment Network Sku",width:"20%"},
           	{align:"center",key:"QUANTITY_SHIPPED",label:"Quantity Shipped",width:"10%"},
           	{align:"center",key:"QUANTITY_RECEIVED",label:"Quantity Received",width:"20%"},
           	{align:"center",key:"QUANTITY_IN_CASE",label:"Quantity In Case",width:"20%"},
           	{align:"center",key:"QUANTITY",label:"Quantity",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 380 ;
		 },
		 title:"需求计划明细列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_inbound_plan_details_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
*/	
});

