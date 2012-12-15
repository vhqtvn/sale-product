

var currentId = '' ;
$(function(){
	$(".grid-content").llygrid({
		columns:[
           	{align:"center",key:"STATUS",label:"状态",width:"10%",forzen:false,align:"left"},
           	{align:"center",key:"MEMO",label:"备注",width:"20%"},
           	{align:"center",key:"CREATE_TIME",label:"时间",width:"8%"}
         ],
         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 150 ;
		 },
		 title:"跟踪列表",
		 autoWidth:true,
		 querys:{sqlId:"sql_warehouse_in_track_lists",inId:inId},
		 loadMsg:"数据加载中，请稍候......",
		 rowClick:function(rowIndex , rowData){
		 	var id = rowData.ID  ;
		 	currentId = id ;
		 	$(".grid-content-details").llygrid("reload",{id:currentId}) ;
			 }
			 
		}) ;
		
		$(".add-track").live("click",function(){
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.In.addTrack/"+inId,550,420) ;
		}) ;
 });