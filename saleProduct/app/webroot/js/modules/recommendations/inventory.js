$(function(){

	$(".grid-content").llygrid({
		columns:[
		    {align:"center",key:"ACCOUNT_NAME",label:"账号",width:"8%",forzen:false,align:"left"},
        	{align:"center",key:"FULFILLMENT_CHANNEL",label:"Channel",width:"5%"},
			{align:"center",key:"ASIN",label:"Asin",group:"Lsiting",width:"8%",forzen:false,align:"left",format:function(val,record){
				return "<a href='#'  offer-listing='"+val+"'>"+(val||"")+"</a>" ;
			}},
           	{align:"center",key:"SKU",label:"Listing Sku",group:"Lsiting",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"UPC",label:"Upc",group:"Lsiting",width:"8%"},
           	{align:"center",key:"ITEM_NAME",group:"Lsiting",label:"Title",width:"10%"},
           	{align:"center",key:"REAL_SKU",label:"货品Sku",group:"货品",width:"8%",forzen:false,align:"left",format:function(val,reocrd){
           		return "<a href='#'  product-realsku='"+val+"'>"+(val||"")+"</a>" ;
           	}},
        	{align:"center",key:"REAL_NAME",label:"货品名称",group:"货品",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"SALES_FOR_THELAST14DAYS",label:"Sales Last 14 Days",width:"10%"},
           	{align:"center",key:"SALES_FOR_THELAST30DAYS",label:"Sales Last 30 Days",width:"10%"},
           	{align:"center",key:"AVAILABLE_QUANTITY",label:"Avail Quantity",width:"10%"},
           	{align:"center",key:"DAYS_UNTIL_STOCK_RUNSOUT",label:"Days Stock Runsout",width:"10%"},
           	{align:"center",key:"INBOUND_QUANTITY",label:"Inbound Quantity",width:"10%"},
           	{align:"center",key:"RECOMMENDED_INBOUND_QUANTITY",label:"Recommended Inbound Quantity",width:"12%"},
           	{align:"center",key:"DAYS_OUTOFSTOCK_LAST30DAYS",label:"Days OutOfStock Last30Days",width:"10%"},
           	{align:"center",key:"LOST_SALES_IN_LAST30DAYS",label:"Lost Sales In Last30Days",width:"10%"},
           	{align:"center",key:"RECOMMENDATION_ID",label:"RecommendationId",width:"20%"},
        	{align:"center",key:"RECOMMENDATION_REASON",label:"Recommendation Reason",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 150 ;
		 },
		 title:"库存报告列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_recommendations_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
});

