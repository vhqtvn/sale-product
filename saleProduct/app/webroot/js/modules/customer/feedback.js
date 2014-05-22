     var type = '4' ;//查询已经审批通过

	 function formatMoney(val){
	 	val = $.trim(val+"") ;
	 	val = val.replace("$","") ;
	 	return $.trim(val) ;
	 }

	$(function(){
			$(".grid-content").llygrid({
				columns:[
				    {align:"center",key:"DATE",label:"时间",width:"10%"},
				    {align:"center",key:"IMAGE_URL",label:"",width:"3%",forzen:false,align:"center",format:{type:'img'}},
		        	{align:"center",key:"REAL_SKU",label:"货品SKU",width:"8%",forzen:false,align:"left",format:function(val){
		           		return "<a href='#' product-realsku='"+val+"'>"+(val||"")+"</a>" ;
		           	}},
		           	{align:"center",key:"SELLER_SKU",label:"Listing SKU",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"ASIN",label:"Asin",width:"10%",format:function(val){
		           		return "<a href='#' offer-listing='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"REAL_NAME",label:"货品名称",width:"13%",forzen:false,align:"center"},
		           	{align:"center",key:"RATING",label:"Rating",width:"10%"},
		           	{align:"center",key:"COMMENTS",label:"Comments",width:"10%"},
		           	{align:"center",key:"ARRIVED_ON_TIME",label:"Arrived On Time",width:"10%"},
		           	{align:"center",key:"ITEM_AS_DESCRIBED",label:"Item as Described",width:"10%"},
		           	{align:"center",key:"CUSTOMER_SERVICE",label:"Customer Service",width:"10%"},
		           	{align:"center",key:"ORDER_ID",label:"Order Id",width:"10%"},
		           	{align:"center",key:"RATER_EMAIL",label:"Rater Email",width:"10%"},
		           	{align:"center",key:"RATER_ROLE",label:"Rater Role",width:"10%"},
		           	{align:"center",key:"YOUR_RESPONSE",label:"Response",width:"10%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 150 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:"sql_customer_amazon_feedback_list"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
				 },
				 loadAfter:function(){
				 }
			}) ;
   	 });
   	 