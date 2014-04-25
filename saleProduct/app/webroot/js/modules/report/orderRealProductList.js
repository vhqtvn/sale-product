     var type = '4' ;//查询已经审批通过

	 function formatMoney(val){
	 	val = $.trim(val+"") ;
	 	val = val.replace("$","") ;
	 	return $.trim(val) ;
	 }

	$(function(){
			$(".grid-content").llygrid({
				columns:[
				    {align:"center",key:"IMAGE_URL",label:"",width:"3%",forzen:false,align:"center",format:{type:'img'}},
		        	{align:"center",key:"REAL_SKU",label:"货品SKU",width:"8%",forzen:false,align:"left",format:function(val){
		           		return "<a href='#' product-realsku='"+val+"'>"+(val||"")+"</a>" ;
		           	}},
		           	{align:"center",key:"SELLER_SKU",label:"Listing SKU",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"ASIN",label:"Asin",width:"10%",format:function(val){
		           		return "<a href='#' offer-listing='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"NAME",label:"货品名称",width:"13%",forzen:false,align:"center"},
		           	{align:"center",key:"C",label:"订单数量",width:"15%",forzen:false,align:"left"},
		           	{align:"center",key:"TOTAL_SUPPLY_QUANTITY",label:"Total Supply Quantity",width:"10%"},
		           	{align:"center",key:"IN_STOCK_SUPPLY_QUANTITY",label:"InStock Supply Quantity",width:"10%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 150 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:"sql_report_orderRealProductList_Items",purchaseDate:newDate},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
				 },
				 loadAfter:function(){
				 }
			}) ;
   	 });
   	 