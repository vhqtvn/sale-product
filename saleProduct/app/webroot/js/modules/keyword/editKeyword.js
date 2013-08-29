$(function(){
	if( $(".niche-grid").length )$(".niche-grid").llygrid({
		columns:[
           	
			{align:"center",key:"keyword_id",label:"操作", width:"8%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='action niche-update' val='"+val+"'>设置</a>&nbsp;") ;

					return html.join("") ;
			}},
			{align:"left",key:"keyword",label:"关键字名称", width:"20%"},
			{align:"left",key:"status",label:"状态", width:"8%",format:function(val , record){
				if( !val ) return "开发中" ;
				if( val==10 ) return "开发中" ;
				if( val==20 ) return "" ;
				if( val==30 ) return "待分配责任人" ;
				if( val==40 ) return "关联开发产品" ;
				if( val==50 ) return "结束" ;
				if( val==15 ) return "废弃" ;
			}},
           	{align:"left",key:"keyword_type",label:"关键字类型", width:"10%"},
           	{align:"left",key:"cpc",label:"CPC",width:"10%",forzen:false,align:"left"},
           	{align:"left",key:"competition",label:"竞争",width:"10%"},
           	{align:"center",key:"site",label:"国家", width:"10%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 140 ;
		 },
		 title:"Niche关键字列表",
		 indexColumn:false,
		 querys:{_data:"d_niche_list",taskId:taskId},
		 loadMsg:"Niche关键字加载中，请稍候......"
	}) ;
	
	$(".niche-update").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Keyword.nicheDev/"+record.keyword_id,900,680,function(win,ret){
			$(".niche-grid").llygrid("reload",{},true) ;
		}) ;
	}) ;
	
	$(".save-plan").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			$.dataservice("model:Keyword.savePlan",json,function(result){
					$(document.body).dialogReturnValue(true) ;
					window.close();
			});
		}
	}) ;
	
	$(".save-task").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			json.planId = planId;
			$.dataservice("model:Keyword.saveTask",json,function(result){
					$(document.body).dialogReturnValue(true) ;
					window.close();
			});
		}
	}) ;
	
	var currentSite = null ;
	
	$(".asyn-keyword").click(function(){
		var mainKeyword = $("#mainKeyword").val() ;
		
		if(window.confirm("确认获取扩展关键字？")){
			var site  = $("#site").val() ;
			currentSite = site ;
			
			var params = {} ;
			params.mainKeyword = mainKeyword ;
			params.site = site ;
			params.taskId = taskId ;
			params.total  = $("#total").val() ;
			
			$.dataservice("model:Keyword.fetchChildKeyWords",params ,function(result){
				$(".main-keyword").llygrid("reload",{},true) ;
			});
		}
	}) ;
	
	$(".btn-query").click(function(){
			if( !currentMainKeyword )alert("先选择主关键字") ;
			var json = $(".toolbar-filter").toJson() ;
			json.taskId = taskId ;
			json.parentId = currentMainKeyword.keyword_id ;
			
			var content = json.search_content||"" ;
			var array = content.split("|") ;
			var pc = [] ;
			if( array.length <=1){
				array = content.split(",") ;
				$(array).each(function(index,item){
					if(!item) return ;
					pc.push("keyword like '%"+item+"%'") ;
				}) ;
				pc = pc.join(" and ") ;
			}else{//or
				$(array).each(function(index,item){
					if(!item) return ;
					pc.push("keyword like '%"+item+"%'") ;
				}) ;
				pc = pc.join(" or ") ;
			}
			
			json.pc = pc ;
			
			$(".child-keyword").llygrid("reload",json) ;
	}) ;
	
	$(".btn-filter").click(function(){
		if( !currentMainKeyword )alert("先选择主关键字") ;
		if( window.confirm("确认筛选吗，不符合条件的关键字将直接废弃？") ){
			var json = $(".toolbar-filter").toJson() ;
			
			var isNull = true ;
			for(var o in json){
				var val = json[o]||"" ;
				if( $.trim(val) ) isNull = false ;
			}
			
			if(isNull){
				alert("筛选条件不能全为空！") ;
				return ;
			}
			
			json.taskId = taskId ;
			json.parentId = currentMainKeyword.keyword_id ;
			$.dataservice("model:Keyword.filterKeyword",json,function(result){
				$(".child-keyword").llygrid("reload",{parentId: currentMainKeyword.keyword_id }) ;
			});
		}
	}) ;
	
	var isInit = false ;
	function createGrid(  keywordId , text ){
		if(isInit){
			$(".child-keyword").llygrid("reload",{parentId:keywordId}) ;
			return ;
		}
		isInit = true ;
		
		$(".child-keyword").llygrid({
			columns:[
				{align:"left",key:"keyword",label:"关键字名称", width:"180px",format:function(val,record){
					if( record.is_niche == 1 ){
						return "<img   src='/"+fileContextPath+"/app/webroot/img/fav.gif'>" +val ;
					}
					return val ;
				}},
				{align:"left",key:"keyword_type",label:"类型", width:"10%"},
				{align:"left",key:"search_volume",label:"搜索量", width:"10%"},
				{align:"left",key:"cpc",label:"CPC", width:"10%"},
				{align:"left",key:"competition",label:"竞争", width:"10%"},
				{align:"center",key:"site",label:"国家", width:"10%"},
	            {align:"center",key:"keyword_id",label:"操作",width:"13%",format:function(val,record){
	            	var img = "" ;
					
					if(record.is_niche != 1){
						if(isDev)img = "<img class='setToNiche' title='设为Niche关键字' src='/"+fileContextPath+"/app/webroot/img/fav.gif'>"  ;
					}
					
					if( isDev ){
						img = img +
						"<img class='getSemrushKeyword' title='获取扩展关键字' src='/"+fileContextPath+"/app/webroot/img/expand-all.gif'>" ;
					}
					
					//网址
					img = img +
					"<img class='getWebsite' title='获取搜索网址' src='/"+fileContextPath+"/app/webroot/img/search.png'>" ;
					
					return img ;
	            }} 
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:20,
			 pageSizes:[10,20,30,40],
			 height:function(){
				 return 245 ;
			 },
			 title:text,
			 indexColumn:false,
			 querys:{_data:"d_list_keywordByMain",parentId:keywordId},
			 loadMsg:"扩展关键字加载中，请稍候......"
		}) ;
	}
	
	function  labelClick(){
		var keywordId = $(this).parents("tr:first").attr("keyword-id") ;
		var keywordText =  $(this).parents("tr:first").attr("keyword")  ;
		var num = $(this).parents("tr:first").find(".num-td").text() ;
		
		createGrid(keywordId,keywordText) ;
	}
	
	$(".label-td").live("dblclick",function(){
		labelClick.call(this) ;
	}) ;
	
	$(".getSemrushKeyword").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		var me = $(this) ;
		if(window.confirm("确认获取扩展关键字？")){
			var keywordId 		= record.keyword_id ;
			var keywordText 	= record.keyword ;
			
			var devItem = $(this).parents(".dev-item:first") ;
			
			var params = {} ;
			params.total  = $("#total").val() ;
			params.mainKeyword = keywordText ;
			params.site = currentSite ;
			params.keywordId = keywordId ;
			params.taskId = taskId ;
		
			$.dataservice("model:Keyword.fetchChildKeyWords", params ,function(result){
				me.parents("tr:first").find("td[key='c']").find("span").text(result).attr("title",result) ;
				me.parents("tr:first").find(".getSemrushKeyword").remove() ;

				$(".main-keyword").llygrid("reload",{},true) ;
			});
		}
		
	}) ;
	
	$(".setToNiche").live("click",function(){
		if(window.confirm("确认设置为Niche关键字？")){
			var record = $.llygrid.getRecord(this) ;
			var keywordId 		= record.keyword_id ;
			var keywordText 	= record.keyword ;
			var me = this ;
			var labelTd = $(me).parents("tr:first").find("td[key='keyword']") ;
			var _kt = $(this).parents("tr:first").find("td:first").text() ;
			$.dataservice("model:Keyword.setToNiche",{mainKeyword:keywordText,'keywordId':keywordId,taskId:taskId},function(result){
				$(me).parents("tr:first").find(".setToNiche").remove() ;
				labelTd.html("<img   src='/"+fileContextPath+"/app/webroot/img/fav.gif'>"+_kt ) ;
				$(".niche-grid").llygrid("reload",{},true) ;
			});
		}
	}) ;
	
	$(".getWebsite").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		var keywordId 		= record.keyword_id ;
		var keywordText 	= record.keyword ;
		openCenterWindow(contextPath+"/page/forward/Keyword.showWebsite/"+keywordId,660,450,function(win,ret){
		},{keyword:keywordText}) ;
	}) ;
	
	var currentMainKeyword = null ;
	function loadMainKeywords(){
		
		$(".main-keyword").llygrid({
			columns:[
				{align:"left",key:"keyword",label:"关键字名称", width:"180px",format:function(val,record){
					if( record.is_niche == 1 ){
						return "<img   src='/"+fileContextPath+"/app/webroot/img/fav.gif'>" +val ;
					}
					return val ;
				}},
				{align:"left",key:"search_volume",label:"搜索量", width:"10%"},
				{align:"left",key:"cpc",label:"CPC", width:"10%"},
				{align:"left",key:"competition",label:"竞争", width:"10%"},
	            {align:"left",key:"c",label:"扩展数",width:"10%"} ,
	            {align:"center",key:"site",label:"国家", width:"10%"},
	            {align:"center",key:"keyword_id",label:"操作",width:"13%",format:function(val,record){
	            	var img = "" ;
					
					if(record.is_niche != 1){
						if(isDev)img = "<img class='setToNiche' title='设为Niche关键字' src='/"+fileContextPath+"/app/webroot/img/fav.gif'>"  ;
					}
					
					if(record.c <=0 && isDev ){
						img = img +
						"<img class='getSemrushKeyword' title='获取扩展关键字' src='/"+fileContextPath+"/app/webroot/img/expand-all.gif'>" ;
					}
					
					//网址
					img = img +
					"<img class='getWebsite' title='获取搜索网址' src='/"+fileContextPath+"/app/webroot/img/search.png'>" ;
					
					return img ;
	            }} 
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:20,
			 pageSizes:[10,20,30,40],
			 height:function(){
				 return 175 ;
			 },
			 title:"主关键字",
			 indexColumn:false,
			 querys:{_data:"d_list_MainKeyword",taskId:taskId},
			 loadMsg:"主关键字加载中，请稍候......",
			 rowDblClick:function(row,record){
				 	currentMainKeyword = record ;
					createGrid(record.keyword_id ,record.keyword ) ;
			 }
		}) ;
	}
	
	
	loadMainKeywords() ;
	
}) ;