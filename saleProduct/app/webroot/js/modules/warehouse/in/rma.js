$(function(){

	$(".addRma").click(function(){
		openCenterWindow(contextPath+"/page/forward/Warehouse.In.addRma",760,530) ;
	}) ;
	
	$(".grid-content").llygrid({
		columns:[
           	{align:"center",key:"QUALITY",label:"货品质量",width:"20%",forzen:false
           		,format:{type:"json",content:{'good':"良品",'bad':"残品"}}},
           	{align:"center",key:"REAL_SKU",label:"SKU",width:"15%"},
           	{align:"center",key:"NAME",label:"货品名称",width:"15%"},
           	{align:"center",key:"QUANTITY",label:"数量",width:"5%" },
           	//{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
           	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:function(val,record){
           		if(val){
           			val = val.replace(/%/g,'%25') ;
           			return "<img src='/"+fileContextPath+"/"+val+"' style='width:30px;height:30px;'>" ;
           		}
           		return "" ;
           	}},
           	{align:"center",key:"MEMO",label:"备注",width:"25%"}
           	
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 220 ;
		 },
		 title:"",
		// autoWidth:true,
		 indexColumn:false,
		  querys:{sqlId:"sql_warehouse_rma_lists"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
 });
