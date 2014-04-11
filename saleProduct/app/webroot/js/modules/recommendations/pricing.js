$(function(){
	/*
	 ACCOUNT_ID, 
	ASIN, 
	SKU, 
	UPC, 
	LAST_UPDATED, 
	ITEM_NAME, 
	FULFILLMENT_CHANNEL, 
	YOUR_PRICE_PLUS_SHIPPING, 
	LOWEST_PRICE_PLUS_SHIPPING, 
	PRICE_DIFFERENCE_TO_LOW_PRICE, 
	MEDIAN_PRICE_PLUS_SHIPPING, 
	LOWEST_MERCHANT_FULFILLED_OFFER_PRICE, 
	LOWEST_AMAZON_FULFILLED_OFFER_PRICE, 
	NUMBER_OF_OFFERS, 
	NUMBER_OF_MERCHANT_FULFILLED_OFFERS, 
	NUMBER_OF_AMAZON_FULFILLED_OFFERS, 
	RECOMMENDATION_ID, 
	RECOMMENDATION_REASON*/
	$(".grid-content").llygrid({
		columns:[
		    {align:"center",key:"ACCOUNT_NAME",label:"账号",width:"8%",forzen:false,align:"left"},
			{align:"center",key:"ASIN",label:"Asin",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"SKU",label:"Sku",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"UPC",label:"Upc",width:"8%"},
           	{align:"center",key:"ITEM_NAME",label:"Title",width:"10%"},
           	{align:"center",key:"FULFILLMENT_CHANNEL",label:"Fulfillment Type",width:"5%"},
           	{align:"center",key:"YOUR_PRICE_PLUS_SHIPPING",label:"Your Price+Shipping",width:"10%"},
           	
           	{align:"center",key:"LOWEST_PRICE_PLUS_SHIPPING",label:"Lowest Price+Shipping",width:"10%"},
           	{align:"center",key:"PRICE_DIFFERENCE_TO_LOW_PRICE",label:"Price Defference",width:"10%"},
           	//{align:"center",key:"MEDIAN_PRICE_PLUS_SHIPPING",label:"Days Stock Runsout",width:"10%"},
           	{align:"center",key:"LOWEST_MERCHANT_FULFILLED_OFFER_PRICE",label:"Lowest Merchant Fulfilled Offer Price",width:"10%"},
           	{align:"center",key:"LOWEST_AMAZON_FULFILLED_OFFER_PRICE",label:"Lowest Amazon Fulfilled Offer Price",width:"10%"},
           	{align:"center",key:"NUMBER_OF_OFFERS",label:"Number Of Offers",width:"10%"},
           	{align:"center",key:"NUMBER_OF_MERCHANT_FULFILLED_OFFERS",label:"Number Of Merchant Fulfilled Offers",width:"10%"},
           	{align:"center",key:"NUMBER_OF_AMAZON_FULFILLED_OFFERS",label:"Number Of amazon Fulfilled Offers",width:"20%"},
        	{align:"center",key:"RECOMMENDATION_ID",label:"Recommendation Id",width:"20%"},
        	{align:"center",key:"RECOMMENDATION_REASON",label:"Recommendation Reason",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 160 ;
		 },
		 title:"库存报告列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_recommendations_pricing_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
});

