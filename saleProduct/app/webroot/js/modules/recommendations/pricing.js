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
        	{align:"center",key:"FULFILLMENT_CHANNEL",label:"Channel",width:"5%"},
        	{align:"center",key:"IMAGE_URL",label:"",width:"3%",forzen:false,align:"left",format:{type:"img"}},
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
           	{align:"center",key:"YOUR_PRICE_PLUS_SHIPPING",label:"Your Price+Shipping",width:"10%"},
           	
           	{align:"center",key:"LOWEST_PRICE_PLUS_SHIPPING",label:"Lowest Price+Shipping",width:"10%"},
           	{align:"center",key:"PRICE_DIFFERENCE_TO_LOW_PRICE",label:"Price Defference",width:"10%"},
           	//{align:"center",key:"MEDIAN_PRICE_PLUS_SHIPPING",label:"Days Stock Runsout",width:"10%"},
           	{align:"center",key:"LOWEST_MERCHANT_FULFILLED_OFFER_PRICE",label:"Lowest Merchant Fulfilled Offer Price",width:"10%"},
           	{align:"center",key:"LOWEST_AMAZON_FULFILLED_OFFER_PRICE",label:"Lowest Amazon Fulfilled Offer Price",width:"10%"},
           	{align:"center",key:"NUMBER_OF_OFFERS",label:"Number Of Offers",width:"10%"},
           	{align:"center",key:"NUMBER_OF_MERCHANT_FULFILLED_OFFERS",label:"Number Of Merchant Fulfilled Offers",width:"10%"},
           	{align:"center",key:"NUMBER_OF_AMAZON_FULFILLED_OFFERS",label:"Number Of amazon Fulfilled Offers",width:"10%"},
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
		 querys:{sqlId:"sql_supplychain_recommendations_pricing_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
});

