$(function(){
		$(".grid-content-plan").llygrid({
			columns:[
				{key:"STATUS",label:"状态",width:"6%",format:function(val,record){
					if(!val || val=='0') return "盘点中" ;
					if(val == 1) return "等待审批" ;
					if(!val || val=='2') return "盘点结束" ;
					if(!val || val=='3') return "重新盘点" ;
					return val;
				}},
				{key:"ID",label:"编辑",width:"8%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='add-active btn' val='"+val+"'>添加活动</a>&nbsp;&nbsp;") ;
					return html.join("") ;
				}},
				{key:"NAME",label:"名称",width:"15%",forzen:false,align:"left"},
				{key:"CODE",label:"代码",width:"10%",forzen:false,align:"left"},
	           	{key:"START_TIME",label:"开始时间",width:"10%",forzen:false,align:"left"},
	           	{key:"END_TIME",label:"结束时间",width:"10%",forzen:false,align:"left"},
	           	{key:"WAREHOUSE_NAME",label:"仓库",width:"13%"},
	           	{key:"CHARGER",label:"经办人",width:"8%"},
	           	{key:"MEMO",label:"备注",width:"15%"}
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
			 limit:5,
			 pageSizes:[5,10,20,30],
			 height:function(){
			 	return 120 ;
			 },
			 title:"盘仓计划列表",
			 indexColumn:false,
			 querys:{sqlId:"sql_warehouse_disk_plan_lists"},
			 loadMsg:"数据加载中，请稍候......",
			 rowClick:function(row,record){
			 	$(".grid-content-active").llygrid("reload",{planId:record.ID});
			 }
		}) ;
		
		$(".grid-content-active").llygrid({
			columns:[
				{key:"STATUS",label:"状态",width:"6%",format:function(val,record){
					if(!val || val=='0') return "盘点中" ;
					if(val == 1){
						return "等待审批" ;
					}
					if(!val || val=='3'){
						if((record.COUNT  - (record.PASS_COUNT||0)) ==0 && record.COUNT >0 ){
							return "盘点完成" ;
						}else{
							return "重新盘点" ;
						}
						
					}
					return val;
				}},
				{key:"ID",label:"编辑",width:"8%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='edit btn' val='"+val+"'>盘点明细</a>&nbsp;&nbsp;") ;
					return html.join("") ;
				}},
				{key:"DISK_NO",label:"活动代码",width:"15%",forzen:false,align:"left"},
				{key:"COUNT",label:"总数",group:"货品数量",width:"5%",forzen:false,align:"left"},
				{key:"PASS_COUNT",label:"审批通过",group:"货品数量",width:"5%",forzen:false,align:"left"},
				{key:"NOPASS_COUNT",label:"审批未通过",group:"货品数量",width:"7%",forzen:false,align:"left",format:function(val,record){
					return record.COUNT  - (record.PASS_COUNT||0) ;
				},render:function(record){
					if( (record.COUNT  - (record.PASS_COUNT||0))>0 )
						$(this).find("[key='NOPASS_COUNT']").css("background","red").css("color","#FFF");
					
					if( (record.COUNT  - (record.PASS_COUNT||0)) ==0 && record.COUNT >0 ){
						$(this).css("background","#98EF98") ;
					}
				}},
	           	{key:"DISK_TIME",label:"时间",width:"15%",forzen:false,align:"left"},
	           	{key:"PROCESSOR",label:"经办人",width:"8%"},
	           	{key:"MEMO",label:"备注",width:"25%"}
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
			 limit:10,
			 pageSizes:[5,10,20,30],
			 height:function(){
			 	return $(window).height() - 350 ;
			 },
			 title:"盘仓活动列表",
			 indexColumn:false,
			 querys:{sqlId:"sql_warehouse_disk_lists",planId:'-'},
			 loadMsg:"数据加载中，请稍候......"
		}) ;
		
		/**
		 * 添加计划
		 */
		$(".add-btn").click(function(){
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Disk.editPlan",780,530) ;
		}) ;
		
		/**
		 * 添加活动
		 */
		$(".add-active").live("click",function(){
			var record = $.llygrid.getRecord(this) ;
			window.currentPlan=record ;
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Disk.edit",780,530) ;
			return false;
		}) ;
		
		
		$(".edit").live("click",function(){
			var val = $(this).attr("val") ;
			openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.Disk.edit/"+val,860,650) ;
			return false;
		}) ;
	
 });
 
 function openCallback(type){
 	if(type=='editPlan' ){
 		$(".grid-content-plan").llygrid("reload") ;
 	}else{
 		$(".grid-content-active").llygrid("reload");
 	}
 	
 }

