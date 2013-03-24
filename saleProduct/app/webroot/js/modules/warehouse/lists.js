$(function(){
	$(".grid-content").llygrid({
		columns:[
			{align:"center",key:"ID",label:"操作",width:"15%",format:function(val,record){
				var status = record.STATUS ;
				var html = [] ;
				html.push("<a href='#' class='edit' val='"+val+"'>编辑</a>&nbsp;&nbsp;") ;
				html.push("<a href='#' class='view' val='"+val+"'>视图</a>&nbsp;&nbsp;") ;
				return html.join("") ;
			}},
			{align:"center",key:"CODE",label:"代码",width:"8%",forzen:false,align:"left"},
           	{align:"center",key:"NAME",label:"仓库名称",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"ADDRESS",label:"仓库地址",width:"20%"},
           	{align:"center",key:"ZIPCODE",label:"邮编",width:"10%"},
           	{align:"center",key:"MEMO",label:"备注",width:"20%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:200,
		 title:"商家列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_warehouse_lists"},
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	$(".design").live("click",function(){
		var val = $(this).attr("val") ;
		openCenterWindow(contextPath+"/page/model/Warehouse.In.loadDesign/"+val,1000,700) ;
	}) ;
	
	$(".view").live("click",function(){
		var val = $(this).attr("val") ;
		openCenterWindow(contextPath+"/page/model/Warehouse.In.loadDesignView/"+val,830,700) ;
	}) ;
	
	$(".add").click(function(){
		openCenterWindow(contextPath+"/warehouse/addPage",650,530) ;
	}) ;
	
	$(".edit").live("click",function(){
		var val = $(this).attr("val") ;
		openCenterWindow(contextPath+"/warehouse/editPage/"+val,650,530) ;
		return false;
	}) ;
});

