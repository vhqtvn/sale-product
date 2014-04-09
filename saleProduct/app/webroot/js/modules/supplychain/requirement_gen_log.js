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
		    {align:"center",key:"LOWEST_PRICE",label:"状态",width:"10%",format:function(val,record){
		    	var channel 		= record.FULFILLMENT_CHANNEL ;
		    	var totalPrice   = record.TOTAL_COST ;
		    	var lowestPrice= record.LOWEST_PRICE ;
		    
		    	if( totalPrice >0 && lowestPrice>0 ){
		    		return "<div class='alert-success'>已处理</div>" ;
		    	}else{
		    		return "<div class='alert-danger'>未处理</div>" ;
		    	}
		    }},
           	{align:"center",key:"ACCOUNT_NAME",label:"账号",width:"10%"},
           	{align:"center",key:"LOCAL_URL",label:"图片",width:"3%",format:{type:'img'}},
           	{align:"center",key:"SKU",label:"SKU",width:"10%"},
        	{align:"center",key:"ASIN",label:"ASIN",width:"10%"},
        	{align:"center",key:"TITLE",label:"商品标题",width:"18%",format:function(val,record){
        		return "<a href='#'  offer-listing='"+record.ASIN+"'>"+val+"</a>" ;
        	}},
        	{align:"center",key:"P_NAME",label:"货品名称",width:"18%",format:function(val,record){
        		return "<a href='#'  product-realsku='"+record.REAL_SKU+"'>"+val+"</a>" ;
        	}},
           	{align:"center",key:"MEMO",label:"备注",width:"15%"},
           	{align:"center",key:"CREATE_DATE",label:"创建日期",width:"10%"},
           	{align:"center",key:"TYPE",label:"类型",width:"5%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query" },
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height()-140 ;
		 },
		 title:"需求计划日志列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_req_log_list",reqPlanId:reqPlanId, type : status},
		 loadMsg:"数据加载中，请稍候......",
		 rowDblClick:function(row,record){
			// $(".grid-content-detials").llygrid("reload",{accountId:record.ACCOUNT_ID,shippmentId:record.SHIPMENT_ID},true) ;
		 },loadAfter:function(){
			
		 }
	}) ;
	
});

