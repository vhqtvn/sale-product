$(function(){
	var currentPlan = null ;
	$(".plan-grid").llygrid({
		columns:[
		     {align:"center",key:"plan_id",label:"", width:"5%",format:function(val,record){
		    	 	if(!addKwPlan) return "";
					var html = [] ;
					var val = record["CODE"] ;
					html.push("<a href='#' class='action plan-update' val='"+val+"'>修改</a>&nbsp;") ;
					return html.join("") ;
			}},
		    {align:"left",key:"name",label:"计划名称", width:"31%"},
		    {align:"left",key:"memo",label:"备注", width:"31%"},
           	{align:"center",key:"create_date",label:"创建时间",width:"24%" }
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return 150 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{_data :"d_list_plan"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......",
		 rowClick:function(row , record){
			 currentPlan = record ;
			 $(".add-task").removeAttr("disabled") ;
			 $(".task-grid").llygrid("reload",{planId:record.plan_id}) ;
		 }
	}) ;
	
	$(".task-grid").llygrid({
		columns:[
		     {align:"center",key:"task_id",label:"", width:"10%",format:function(val,record){
					var html = [] ;
					var val = record["CODE"] ;
					if(addKwTask)html.push("<a href='#' class='action task-update'>修改</a>&nbsp;") ;
					html.push("<a href='#' class='action keyword-dev'>关键字开发</a>&nbsp;") ;
					return html.join("") ;
			}},
		    {align:"left",key:"name",label:"任务名称", width:"31%"},
		    {align:"left",key:"memo",label:"任务备注", width:"31%"},
           	{align:"center",key:"create_date",label:"创建时间",width:"24%" }
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 350 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{_data :"d_list_task",planId:''},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	$(".plan-update").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Keyword.editPlan/"+record.plan_id,600,400,function(win,ret){
			$(".plan-grid").llygrid("reload",{},true) ;
		}) ;
	}) ;
	
	$(".task-update").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Keyword.editTask/"+record.plan_id+"/"+record.task_id,600,400,function(win,ret){
			$(".plan-grid").llygrid("reload",{},true) ;
		}) ;
	}) ;
	
	$(".add-plan").click(function(){
		openCenterWindow(contextPath+"/page/forward/Keyword.editPlan",600,400,function(win,ret){
			$(".plan-grid").llygrid("reload") ;
		}) ;
	}) ;
	
	$(".add-task").click(function(){
		openCenterWindow(contextPath+"/page/forward/Keyword.editTask/"+currentPlan.plan_id,600,400,function(win,ret){
			$(".task-grid").llygrid("reload",{},true) ;
		}) ;
	}) ;
	
	$(".keyword-dev").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Keyword.keywordDev/"+record.task_id,1100,650,function(win,ret){
			$(".task-grid").llygrid("reload",{},true) ;
		}) ;
	}) ;
	
	
}) ;