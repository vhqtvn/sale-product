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
		           	{align:"center",key:"REAL_SKU",label:"货品SKU",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"NAME",label:"货品名称",width:"13%",forzen:false,align:"center"},
		           	{align:"center",key:"C",label:"订单数量",width:"15%",forzen:false,align:"left"},
		           	{align:"center",key:"AMOUNT",label:"总金额",width:"15%",forzen:false,align:"left"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 350 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:"sql_report_orderRealProductList",purchaseDate:newDate},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(record){
					 $(".grid-content-details").llygrid("reload",{"realId":record.REAL_ID,"purchaseDate":$("#purchaseDate").val()}) ;
				 },
				 loadAfter:function(){
				 }
			}) ;

			$(".grid-content-details").llygrid({
				columns:[
		           	{align:"center",key:"SELLER_SKU",label:"Listing SKU",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"C",label:"订单数量",width:"15%",forzen:false,align:"left"},
		           	{align:"center",key:"AMOUNT",label:"总金额",width:"15%",forzen:false,align:"left"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return  170 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:"sql_report_orderRealProductList_Items"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){

				 }
			}) ;
   	 });
   	 