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
           	{align:"center",key:"search_volume",label:"搜索量", width:"10%"},
			
           	{align:"left",key:"cpc",label:"CPC",width:"10%",forzen:false,align:"left"},
           	{align:"left",key:"competition",label:"竞争",width:"10%"},
           	{align:"left",key:"result_num",label:"结果数",width:"15%"}//,
            //{align:"left",key:"trends",label:"趋势",width:"25%"} 
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 150 ;
		 },
		 title:"Niche关键字列表",
		 indexColumn:false,
		 querys:{_data:"d_niche_list",taskId:taskId},
		 loadMsg:"Niche关键字加载中，请稍候......"
	}) ;
	
	$(".niche-update").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Keyword.nicheDev/"+record.keyword_id,800,550,function(win,ret){
			if(ret)$(".niche-grid").llygrid("reload",{},true) ;
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
	
	$(".asyn-keyword").click(function(){
		var mainKeyword = $("#mainKeyword").val() ;
		if(window.confirm("确认获取扩展关键字？")){
			$.dataservice("model:Keyword.fetchChildKeyWords",{mainKeyword:mainKeyword,'taskId':taskId},function(result){
					window.location.reload() ;
			});
		}
	}) ;
	
	$(".btn-filter").click(function(){
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
			$.dataservice("model:Keyword.filterKeyword",json,function(result){
				window.location.reload() ;
			});
		}
	}) ;
	
	function createGrid( container , keywordId , text ){
		container.css("width","98%");
		container.llygrid({
			columns:[
				{align:"left",key:"keyword",label:"关键字名称", width:"170px",format:function(val,record){
					if( record.is_niche == 1 ){
						return "<img   src='/"+fileContextPath+"/app/webroot/img/fav.gif'>" +val ;
					}
					return val ;
				}},
				{align:"left",key:"search_volume",label:"搜索量", width:"10%"},
				{align:"left",key:"cpc",label:"CPC", width:"10%"},
				{align:"left",key:"competition",label:"竞争", width:"10%"},
	            {align:"left",key:"result_num",label:"结果数",width:"15%"},
	            {align:"left",key:"c",label:"扩展数",width:"10%"} ,
	            {align:"center",key:"keyword_id",label:"操作",width:"10%",format:function(val,record){
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
				 return 155 ;
			 },
			 title:text,
			 indexColumn:false,
			 querys:{_data:"d_list_keywordByMain",parentId:keywordId},
			 loadMsg:"扩展关键字加载中，请稍候......",
			 rowDblClick:function(row,record){
				    if(record.c == 0) return ;
			
					var parentContainer = row.parents(".dev-item:first") ;
					var container = parentContainer.next(".dev-item") ;
					
					while(parentContainer.next(".dev-item").length){
						parentContainer.next(".dev-item").remove() ;
					}
					container = parentContainer.next(".dev-item") ;
					if( container.length <=0 ){
						container = $("<div class='dev-item'></div>").appendTo(parentContainer.parent()) ;
					}
					
					container.empty() ;
					
					createGrid(container,record.keyword_id ,record.keyword ) ;
			 }
		}) ;
	}
	
	function  labelClick(){
		var keywordId = $(this).parents("tr:first").attr("keyword-id") ;
		var keywordText =  $(this).parents("tr:first").attr("keyword")  ;
		var num = $(this).parents("tr:first").find(".num-td").text() ;
		
		if(num === 0) return ;
		var parentContainer = $(this).parents(".dev-item:first") ;
		var container = parentContainer.next(".dev-item") ;
		
		while(parentContainer.next(".dev-item").length){
			parentContainer.next(".dev-item").remove() ;
		}
		container = parentContainer.next(".dev-item") ;
		if( container.length <=0 ){
			container = $("<div class='dev-item'></div>").appendTo(parentContainer.parent()) ;
		}
		
		container.empty() ;
		
		createGrid(container,keywordId,keywordText) ;
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
		
			$.dataservice("model:Keyword.fetchChildKeyWords",{mainKeyword:keywordText,'keywordId':keywordId,taskId:taskId},function(result){
				me.parents("tr:first").find("td[key='c']").find("span").text(result).attr("title",result) ;
				me.parents("tr:first").find(".getSemrushKeyword").remove() ;
				
				var container = devItem.next(".dev-item") ;
				
				while(devItem.next(".dev-item").length){
					devItem.next(".dev-item").remove() ;
				}
				container = devItem.next(".dev-item") ;
				if( container.length <=0 ){
					container = $("<div class='dev-item'></div>").appendTo(devItem.parent()) ;
				}
				
				container.empty() ;
				
				createGrid(container,keywordId ,keywordText ) ;
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
		/*
		$.dataservice("model:Keyword.getWebSite",{'keywordId':keywordId},function(result){
			$(".website-container").show();
			$(".website-container ul").empty().show() ;
			$(result).each(function(){
				$(".website-container ul").append("<li><a href='"+this.url+"' target='_blank'>"+this.domain+"</a></li>") ;
			}) ;
		});*/
	}) ;
	
	function loadMainKeywords(){
		$.dataservice("model:Keyword.loadMainKeywords",{'taskId':taskId},function(result){
			$(".main-keyword").empty() ;
			var table = $("<table class='table'><caption>主关键字</caption></table>").appendTo(".main-keyword") ;
			
			
			$(result).each(function(){
				var kw = this.keyword ;
				
				var img = "" ;
				
				if(this.is_niche == 1){
					kw = "<img   src='/"+fileContextPath+"/app/webroot/img/fav.gif'>" +kw ;
				}else{
					if(isDev)img = "<img class='setToNiche' title='设为Niche关键字' src='/"+fileContextPath+"/app/webroot/img/fav.gif'>"  ;
				}
				
				if(this.c <=0 && isDev ){
					img = img +
					"<img class='getSemrushKeyword' title='获取扩展关键字' src='/"+fileContextPath+"/app/webroot/img/expand-all.gif'>" ;
				}
				
				//网址
				img = img +
				"<img class='getWebsite' title='获取搜索网址' src='/"+fileContextPath+"/app/webroot/img/search.png'>" ;
				
				$("<tr  keyword-id='"+this.keyword_id+"' keyword='"+this.keyword+"'><td class='label-td'>"+kw+"</td><td class='num-td'>"+this.c+
						"</td><td class='action-td'>"+img+"</td></tr>").appendTo( table ) ;
			}) ;
		});
	}
	
	
	loadMainKeywords() ;
	
}) ;