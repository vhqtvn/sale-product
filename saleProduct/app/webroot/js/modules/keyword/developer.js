$(function(){
	var  amazonSiteMap = {
			us:"www.amazon.com",
			uk:"www.amazon.co.uk",
			ca:"www.amazon.ca",
			ru:"www.amazon.ru",
			de:"www.amazon.de",
			fr:"www.amazon.fr",
			es:"www.amazon.es",
			it:"www.amazon.it",
			br:"www.amazon.br",
			au:"www.amazon.com.au",
			"us.bing":"www.amazon.com"
	}
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
					
					if( addKwTask ) html.push("<img class='action task-update' title='修改' src='/"+fileContextPath+"/app/webroot/img/edit.png'>") ;
					html.push("<a href='"+contextPath+"/page/forward/Keyword.keywordDev/"+record.task_id+"' target='_blank'><img class='action' title='关键字开发' src='/"+fileContextPath+"/app/webroot/img/expand-all.gif'></a>") ;
					//if(addKwTask)html.push("<a href='#' class='action task-update'>修改</a>&nbsp;") ;
					//html.push("<a href='#' class='action keyword-dev'>关键字开发</a>&nbsp;") ;
					return html.join("") ;
			}},
		    {align:"left",key:"name",label:"任务名称", width:"31%"},
		    {align:"left",key:"memo",label:"任务备注", width:"31%"},
		    {align:"left",key:"create_name",label:"创建人", width:"10%"},
           	{align:"center",key:"create_date",label:"创建时间",width:"24%" }
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 360 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{_data :"d_list_task",planId:''},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......", 
		 rowDblClick:function(row,record){
			 	currentMainKeyword = record ;
			 	$(".niche-grid").llygrid("reload",{taskId:record.task_id}) ;
		 }
	}) ;
	
	$(".niche-grid").llygrid({
		columns:[
			{align:"center",key:"keyword_id",label:"操作", width:"8%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='action niche-update' val='"+val+"'>设置</a>&nbsp;") ;

					return html.join("") ;
			}},
			{align:"left",key:"keyword",label:"关键字名称", width:"20%",format:function(val,record){
				var site = record.site||"us" ;
				var amazonUrl = amazonSiteMap[site] ;
				return "<a href='http://"+amazonUrl+"/s/ref=nb_sb_noss?field-keywords="+val+"' target='_blank'>"+val+"</a>" ;
			}},
			{align:"left",key:"status",label:"状态", width:"8%",format:function(val , record){
				if( !val ) return "开发中" ;
				if( val==10 ) return "开发中" ;
				if( val==20 ) return "待审批" ;
				if( val==30 ) return "待分配责任人" ;
				if( val==40 ) return "关联开发产品" ;
				if( val==50 ) return "结束" ;
				if( val==15 ) return "废弃" ;
			}},
           	{align:"left",key:"keyword_type",label:"关键字类型", width:"10%"},
           	{align:"left",key:"search_volume",label:"搜索量", width:"10%"},
           	{align:"left",key:"cpc",label:"CPC",width:"10%",forzen:false,align:"left"},
           	{align:"left",key:"competition",label:"竞争",width:"10%"},
           	{align:"center",key:"site",label:"国家", width:"10%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 	return $(window).height() - 340 ;
			 },
		 title:"Niche关键字列表",
		 indexColumn:false,
		 querys:{_data:"d_niche_list",taskId:"-"},
		 loadMsg:"Niche关键字加载中，请稍候......",
		 rowDblClick:function(row,record){
			 //currentNichKeyword = record ;
			 // $(".niche-grid-group").llygrid("reload",{groupId:record.keyword_id}) ;
		 },
		 loadAfter:function(){
			 //currentNichKeyword =null ;
		 }
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