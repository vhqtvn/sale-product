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
		    {align:"center",key:"ID",label:"",width:"10%",forzen:false,align:"left",format:function(val,record){
		    	return "<input type='text'  style='width:35px;height:20px;margin-top:2px;padding:0px;' value='' title='输入打印数量'>&nbsp;<button class='btn print-btn'>打印</button>" ;
		    }},
		    {align:"center",key:"IMAGE_URL",label:"",width:"3%",forzen:false,align:"left",format:{type:"img"}},
		    {align:"center",key:"FC_SKU",label:"FNSKU",width:"10%",forzen:false,align:"left",format:function(val,reocrd){
		    	return reocrd.FNSKU?reocrd.FNSKU:(val||"") ;
		    }},
			{align:"center",key:"SKU",label:"Listing SKU",width:"15%",forzen:false,align:"left"},
           //	{align:"center",key:"FNSKU",label:"FNSKU",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"ASIN",label:"ASIN",width:"10%",format:function(val){
           		return "<a href='#' offer-listing='"+val+"'>"+val+"</a>" ;
           	}},
           	{align:"center",key:"TITLE",label:"Title",width:"30%",format:function(val,record){
           		if(val) return val ;
           		return "<input type='text' placeHolder='输入Title' style='height:18px;margin-top:2px;'/>" ;
           	} },
           	{align:"center",key:"REAL_SKU",label:"货品SKU",width:"8%",forzen:false,align:"left",format:function(val){
           		return "<a href='#' product-realsku='"+val+"'>"+(val||"")+"</a>" ;
           	}},
           	{align:"center",key:"REAL_NAME",label:"货品名称",width:"8%",forzen:false,align:"left"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 130 ;
		 },
		 title:"FBA库存列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_purchase_new_listing_label"},
		 loadMsg:"数据加载中，请稍候......",
		 rowDblClick:function(row,record){
			 $(".grid-content-detials").llygrid("reload",{accountId:record.ACCOUNT_ID,sellerSku:record.SELLER_SKU},true) ;
		 }
	}) ;
	
	$(".print-btn").live("click",function(){
		var record = $(this).closest("tr").data("record") ;
		var printNum = $(this).prev().val() ;
		var accountId = record.ACCOUNT_ID ;
		var listingSku = record.SKU ;
		openCenterWindow(contextPath+"/page/forward/Barcode.barcode/"+(printNum||44)+"/"+listingSku+"/"+accountId ,850,700) ;
	});
	
});

