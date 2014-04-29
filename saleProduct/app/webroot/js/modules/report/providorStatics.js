     var type = '4' ;//查询已经审批通过

	$(function(){
		
			$(".grid-content").llygrid({
				columns:[
				    {align:"center",key:"PURCHASE_DATE",label:"采购月份",width:"15%",forzen:false,align:"center"},
		        	{align:"center",key:"SUPPLIER_NAME",label:"供应商",width:"8%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='#' supplier-id='"+record.PROVIDOR_ID+"'>"+(val||"")+"</a>" ;
		           	}},
		           	{align:"center",key:"REAL_SKU",label:"货品SKU",width:"10%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='#' product-realsku='"+record.REAL_SKU+"'>"+(val||"")+"</a>" ;
		           	}},
		           	{align:"center",key:"REAL_PRODUCT_NAME",label:"货品名称",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"IMAGE_URL",label:"货品图片",width:"10%",format:{type:"img"}},
		           	{align:"center",key:"QUALIFIED_PRODUCTS_NUM",label:"良品数量",width:"13%",forzen:false,align:"center"},
		           	{align:"center",key:"BAD_PRODUCTS_NUM",label:"残品数量",width:"15%",forzen:false,align:"left"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 150 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:"sql_report_providor_statics",purchaseDate:newDate},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
				 },
				 loadAfter:function(){
				 }
			}) ;
			
   	 });
   	 