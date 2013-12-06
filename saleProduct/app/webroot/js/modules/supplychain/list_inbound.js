$(function(){
/*
 * 	ACCOUNT_ID, 
	SHIPMENT_ID, 
	SHIPMENT_NAME, 
	DESTINATION_FULFILLMENT_CENTER_ID, 
	SHIPMENT_STATUS, 
	LABEL_PREP_TYPE, 
	ARE_CASES_REQUIRED, 
 * */
	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"SHIPMENT_ID",label:"ShipmentId",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"SHIPMENT_NAME",label:"ShipmentName",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"DESTINATION_FULFILLMENT_CENTER_ID",label:"Dest CenterId",width:"20%"},
           	{align:"center",key:"SHIPMENT_STATUS",label:"状态",width:"10%"},
           	{align:"center",key:"LABEL_PREP_TYPE",label:"Label类型",width:"20%"},
           	{align:"center",key:"ARE_CASES_REQUIRED",label:"Cases Required",width:"20%"},
           	
           	{align:"center",key:"NAME",label:"名称",group:"发货地址",width:"20%"},
           	{align:"center",key:"ADDRESS_LINE1",label:"AddressLine1",group:"发货地址",width:"20%"},
           	{align:"center",key:"ADDRESS_LINE2",label:"AddressLine2",group:"发货地址",width:"20%"},
           	{align:"center",key:"DISTRICT_OR_COUNTRY",label:"国家或地区",group:"发货地址",width:"20%"},
           	{align:"center",key:"CITY",label:"城市",group:"发货地址",width:"20%"},
           	{align:"center",key:"STATE_OR_PROVINCE_CODE",label:"州或省代码",group:"发货地址",width:"20%"},
           	{align:"center",key:"COUNTRY_CODE",label:"国家代码",group:"发货地址",width:"20%"},
           	{align:"center",key:"POSTAL_CODE",label:"邮编",group:"发货地址",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return 150 ;
		 },
		 title:"FBA入库计划列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_inbound_plan_list"},
		 loadMsg:"数据加载中，请稍候......",
		 rowDblClick:function(row,record){
			 $(".grid-content-detials").llygrid("reload",{accountId:record.ACCOUNT_ID,shippmentId:record.SHIPMENT_ID},true) ;
		 }
	}) ;
	

	$(".grid-content-detials").llygrid({
		columns:[
			{align:"center",key:"SHIPMENT_ID",label:"ShipmentId",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"SELLER_SKU",label:"Seller Sku",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"FULFILLMENT_NETWORK_SKU",label:"Fulfillment Network Sku",width:"20%"},
           	{align:"center",key:"QUANTITY_SHIPPED",label:"Quantity Shipped",width:"10%"},
           	{align:"center",key:"QUANTITY_RECEIVED",label:"Quantity Received",width:"20%"},
           	{align:"center",key:"QUANTITY_IN_CASE",label:"Quantity In Case",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 380 ;
		 },
		 title:"FBA入库计划明细列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_inbound_plan_details_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
});

