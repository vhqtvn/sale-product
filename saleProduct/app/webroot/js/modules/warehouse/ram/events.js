$(function(){
	
	var tab = $('#tabs-default').tabs( {//$this->layout="index";
		tabs:[
			{label:'编辑中',content:"tab-container"},
			{label:'待审批',content:"tab-container"},
			{label:'审批完成',content:"tab-container"},
			{label:'处理完成',content:"tab-container"}
		] ,
		select:function(event,ui){
			var index = ui.index ;
			if(index == 0){
				$(".grid-content").llygrid("reload",{status:0}) ;
			}else if(index==1){
				$(".grid-content").llygrid("reload",{status:1}) ;
			}else if(index==2){
				$(".grid-content").llygrid("reload",{status:2}) ;
			}else if(index==3){
				$(".grid-content").llygrid("reload",{status:3}) ;
			}
		}
	} ) ;
	
	$(".grid-content").llygrid({
		columns:[
			{key:"CODE",label:"编辑",width:"5%",format:function(val,record){
				var html = [] ;
				html.push("<a href='#' class='edit btn' val='"+val+"'>修改</a>&nbsp;&nbsp;") ;
				return html.join("") ;
			}},
			{key:"STATUS",label:"状态",width:"5%",forzen:false,align:"center",format:{type:"json",content:{'0':"编辑中",1:"待审批",2:"审批完成",3:"处理完成"}}},
			{key:"CODE",label:"编号",width:"14%",forzen:false,align:"center"},
			{key:"ORDER_ID",label:"订单ID",width:"14%",forzen:false,align:"center"},
			{key:"ORDER_NO",label:"系统货号",width:"8%",forzen:false,align:"center"},
			{key:"REAL_SKU",label:"货品SKU",group:"货品",width:"5%"},
			{key:"IMAGE_URL",label:"图片",group:"货品",width:"3%",format:{type:'func',funcName:"renderGridImg"}},
           	{key:"CAUSE_NAME",label:"原因",width:"13%",align:"left"},
           	{key:"POLICY_NAME",label:"决策",width:"10%",align:"left"},
           	{key:"MEMO",label:"备注",width:"17%",align:"left"}
         ],
         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
		 limit:10,
		 pageSizes:[5,10,20,30],
		 height:function(){
		 	return $(window).height()-230 ;
		 },
		 title:"RAM事件列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_ram_events_list",status:0},
		 loadMsg:"数据加载中，请稍候......",
		 rowClick:function(row,record){
		 	$(".grid-content-active").llygrid("reload",{planId:record.ID});
		 }
	}) ;

	//添加选项
	$(".add-btn").click(function(){
		openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Ram.editEvent",780,530) ;
	}) ;
	
	$(".edit").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Ram.editEvent/"+record.ID,780,530) ;
		return false;
	}) ;
		
 });
 
 function openCallback(type){
 	$(".grid-content").llygrid("reload") ;
 }

