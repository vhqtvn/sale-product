$(function(){
	/*
	 ACCOUNT_ID, 
	ASIN, 
	SKU, 
	UPC, 
	
	QUALITY_SET, 
	DEFECT_GROUP, 
	DEFECT_ATTRIBUTE, 
	
	ITEM_NAME, 
	RECOMMENDATION_ID, 
	RECOMMENDATION_REASON*/
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
           	
           	{align:"center",key:"QUALITY_SET",label:"Quality Set",width:"10%"},
           	{align:"center",key:"DEFECT_GROUP",label:"Defect Group",width:"10%"},
           	{align:"center",key:"DEFECT_ATTRIBUTE",label:"Defect Attribute",width:"12%"},
           	
        	{align:"center",key:"RECOMMENDATION_ID",label:"Recommendation Id",width:"17%"},
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
		 querys:{sqlId:"sql_supplychain_recommendations_listingQuality_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
});

