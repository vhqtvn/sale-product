$(function(){
	/*
ACCOUNT_ID, 
	ASIN, 
	SKU, 
	UPC, 
	LAST_UPDATED, 
	ITEM_NAME, 
	
	BRAND_NAME, 
	
	PRODUCT_CATEGORY, 
	SALES_RANK, 
	BUYBOX_PRICE, 
	NUMBER_OF_OFFERS, 
	AVERAGE_CUSTOMER_REVIEW, 
	NUMBER_OF_CUSTOMER_REVIEWS, 
	
	RECOMMENDATION_ID, 
	RECOMMENDATION_REASON*/
	$(".grid-content").llygrid({
		columns:[
		    {align:"center",key:"ACCOUNT_NAME",label:"账号",width:"8%",forzen:false,align:"left"},
		    {align:"center",key:"ASIN",label:"Asin",width:"8%",forzen:false,align:"left",format:function(val,record){
				return "<a href='#'  offer-listing='"+val+"'>"+(val||"")+"</a>" ;
			}},
           	{align:"center",key:"SKU",label:"Sku",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"UPC",label:"Upc",width:"8%"},
           	{align:"center",key:"ITEM_NAME",label:"Title",width:"10%"},
           	{align:"center",key:"LAST_UPDATED",label:"Last Updated",width:"5%"},
           	{align:"center",key:"BRAND_NAME",label:"Brand",width:"10%"},
           	
           	{align:"center",key:"PRODUCT_CATEGORY",label:"Category",width:"10%"},
           	{align:"center",key:"SALES_RANK",label:"Sales Rank",width:"10%"},
           	{align:"center",key:"BUYBOX_PRICE",label:"Buybox Price",width:"10%"},
           	{align:"center",key:"NUMBER_OF_OFFERS",label:"Number Of Offers",width:"10%"},
           	{align:"center",key:"AVERAGE_CUSTOMER_REVIEW",label:"Average Customer Review",width:"10%"},
           	{align:"center",key:"NUMBER_OF_CUSTOMER_REVIEWS",label:"Number Of Customer Reviews",width:"10%"},
           	
           	{align:"center",key:"RECOMMENDATION_ID",label:"Recommendation Id",width:"20%"},
        	{align:"center",key:"RECOMMENDATION_REASON",label:"Recommendation Reason",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 160 ;
		 },
		 title:"库存报告列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_recommendations_selection_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
});

