var currentId = '' ;
$(function(){
	$(".grid-content").llygrid({
		columns:[
           	{align:"left",key:"STATUS",label:"状态",width:"10%",forzen:false,align:"left",format:function(val,record){
           		switch(val){
           			case '0':
           				return "编辑中";
           			case '10':
           				return "编辑完成，提交等待审批" ;
           			case '20':
           				return "审批完成，等待发货" ;
           			case '30':
           				return "完成发货，等待海关接收" ;
           			case '40':
           				return "到达海关" ;
           			case '50':
           				return "等待验货" ;
           			case '60':
           				return "验货完成，等待入库" ;
           			case '70':
           				return "完成入库" ;
           			default:
           				return val ;
           		}
           	}},
           	{align:"left",key:"MEMO",label:"备注",width:"20%"},
           	{align:"center",key:"CREATE_TIME",label:"时间",width:"8%"},
           	{align:"center",key:"CREATOR_NAME",label:"操作人",width:"8%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 150 ;
		 },
		 title:"跟踪列表",
		 autoWidth:true,
		 querys:{sqlId:"sql_warehouse_in_track_lists",inId:inId},
		 loadMsg:"数据加载中，请稍候......"	 
	}) ;
	
	$(".add-track").live("click",function(){
		openCenterWindow(contextPath+"/page/forward/Warehouse.In.addTrack/"+inId,550,420) ;
	}) ;
 });
 
  function openCallback(){
 	$(".grid-content").llygrid("reload",{},true) ;
 }
