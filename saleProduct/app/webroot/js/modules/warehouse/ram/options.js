$(function(){
		$(".grid-content").llygrid({
			columns:[
				{key:"CODE",label:"编辑",width:"8%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='edit btn' val='"+val+"'>修改</a>&nbsp;&nbsp;") ;
					return html.join("") ;
				}},
				{key:"CODE",label:"代码",width:"15%",forzen:false,align:"left"},
				{key:"NAME",label:"名称",width:"20%",forzen:false,align:"left"},
	           	{key:"TYPE",label:"类型",width:"8%",format:{type:"json",content:{'cause':"RAM原因",'policy':"PAM决策"}}},
	           	{key:"IS_RESEND",label:"是否重发货",width:"8%",format:function(val,record){
	           		if(record.TYPE == 'cause') return "-" ;
	           		if(val==1) return "是" ;
	           		return "否" ;
	           	}},
	           	{key:"IS_REFUND",label:"是否重退款",width:"8%",format:function(val,record){
	           		if(record.TYPE == 'cause') return "-" ;
	           		if(val==1) return "是" ;
	           		return "否" ;
	           	}},
	           	{key:"IS_BACK",label:"是否需要退货",width:"8%",format:function(val,record){
	           		if(record.TYPE == 'cause') return "-" ;
	           		if(val==1) return "是" ;
	           		return "否" ;
	           	}},
	           	{key:"MEMO",label:"备注",width:"20%"}
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
			 limit:10,
			 pageSizes:[5,10,20,30],
			 height:function(){
			 	return $(window).height()-200 ;
			 },
			 title:"盘仓计划列表",
			 indexColumn:false,
			 querys:{sqlId:"sql_ram_options_list"},
			 loadMsg:"数据加载中，请稍候......",
			 rowClick:function(row,record){
			 	$(".grid-content-active").llygrid("reload",{planId:record.ID});
			 }
		}) ;
	
		//添加选项
		$(".add-btn").click(function(){
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Ram.editOption",780,530) ;
		}) ;
		
		$(".edit").live("click",function(){
			var record = $.llygrid.getRecord(this) ;
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Ram.editOption/"+record.CODE,780,530) ;
			return false;
		}) ;
		
 });
 
 function openCallback(type){
 	$(".grid-content").llygrid("reload") ;
 }

