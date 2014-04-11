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
			{align:"center",key:"ASIN",label:"Asin",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"SKU",label:"Sku",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"UPC",label:"Upc",width:"8%"},
           	{align:"center",key:"ITEM_NAME",label:"Title",width:"10%"},
           	
           	{align:"center",key:"QUALITY_SET",label:"Quality Set",width:"10%"},
           	{align:"center",key:"DEFECT_GROUP",label:"Defect Group",width:"10%"},
           	{align:"center",key:"DEFECT_ATTRIBUTE",label:"Defect Attribute",width:"12%"},
           	
        	{align:"center",key:"RECOMMENDATION_ID",label:"Recommendation Id",width:"17%"},
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
		 querys:{sqlId:"sql_supplychain_recommendations_listingQuality_list"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
});

