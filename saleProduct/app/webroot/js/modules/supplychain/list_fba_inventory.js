$(function(){
/*
 * 	ACCOUNT_ID, 
	SELLER_SKU, 
	FNSKU, 
	ASIN, 
	CONDITION, 
	TOTAL_SUPPLY_QUANTITY, 
	IN_STOCK_SUPPLY_QUANTITY, 
	EARLIEST_TIMEPOINT_TYPE, 
	EARLIEST_DATETIME, 
	QUANTITY_IN_STOCK, 
	QUANTITY_INBOUND, 
	QUANTITY_TRANSFER
 * */
	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"SELLER_SKU",label:"Seller Sku",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"FNSKU",label:"FNSKU",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"ASIN",label:"Asin",width:"20%"},
           	{align:"center",key:"CONDITION",label:"Condition",width:"10%"},
           	{align:"center",key:"TOTAL_SUPPLY_QUANTITY",label:"Total Supply Quantity",width:"20%"},
           	{align:"center",key:"IN_STOCK_SUPPLY_QUANTITY",label:"InStock Supply Quantity",width:"20%"},
           	{align:"center",key:"QUANTITY_IN_STOCK",label:"Quantitiy InStock",width:"20%"},
           	{align:"center",key:"QUANTITY_INBOUND",label:"Quantitiy Inbound",width:"20%"},
           	{align:"center",key:"QUANTITY_TRANSFER",label:"Quantitiy Transfer",width:"20%"},
           	{align:"center",key:"EARLIEST_TIMEPOINT_TYPE",label:"Earliest Timepoint TYpe",width:"20%"},
        	{align:"center",key:"EARLIEST_DATETIME",label:"Earliest Datetime",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return 150 ;
		 },
		 title:"FBA库存列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_fbainventory_list"},
		 loadMsg:"数据加载中，请稍候......",
		 rowDblClick:function(row,record){
			 $(".grid-content-detials").llygrid("reload",{accountId:record.ACCOUNT_ID,sellerSku:record.SELLER_SKU},true) ;
		 }
	}) ;
	/*
	 * ACCOUNT_ID, 
	SELLER_SKU, 
	QUANTITY, 
	SUPPLY_TYPE, 
	EARLIEST_TIMEPOINT_TYPE, 
	EARLIEST_DATETIME, 
	LATEST_DATETIME, 
	LATEST_TIMEPOINT_TYPE*/

	$(".grid-content-detials").llygrid({
		columns:[
           	{align:"center",key:"SELLER_SKU",label:"Seller Sku",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"QUANTITY",label:"Quantity",width:"20%"},
           	{align:"center",key:"SUPPLY_TYPE",label:"Supply Type",width:"10%"},
           	{align:"center",key:"EARLIEST_TIMEPOINT_TYPE",label:"Earliest Timepoint Type",width:"20%"},
           	{align:"center",key:"EARLIEST_DATETIME",label:"Earliest Datetime",width:"20%"},
        	{align:"center",key:"LATEST_TIMEPOINT_TYPE",label:"Latest Timepoint Type",width:"20%"},
           	{align:"center",key:"LATEST_DATETIME",label:"Latest Datetime",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 380 ;
		 },
		 title:"FBA入库明细列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_fbainventory_details_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	
});

