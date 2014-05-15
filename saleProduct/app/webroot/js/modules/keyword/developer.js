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
	

	var currentMainKeyword = null ;
	
	$(".main-keyword").llygrid({
		columns:[
			{align:"left",key:"keyword",label:"关键字名称", width:"180px",format:function(val,record){
				var site = record.site||"us" ;
				var amazonUrl = amazonSiteMap[site] ;
				val = "<a href='http://"+amazonUrl+"/s/ref=nb_sb_noss?field-keywords="+val+"' target='_blank'>"+val+"</a>" ;
				
				if( record.is_niche == 1 ){
					return "<img   src='/"+fileContextPath+"/app/webroot/img/fav.gif'>"+val  ;
				}
				return val ;
			}},
			{align:"left",key:"search_volume",label:"搜索量", width:"10%"},
			{align:"left",key:"cpc",label:"CPC", width:"10%"},
			{align:"left",key:"competition",label:"竞争", width:"10%"},
            {align:"left",key:"c",label:"扩展数",width:"10%"} ,
            {align:"center",key:"site",label:"国家", width:"10%"},
            {align:"center",key:"keyword_id",label:"操作",width:"16%",format:function(val,record){
            	var img = "" ;
				
				if(record.is_niche != 1 &&  record.is_pause!=1){
					if(isDev)img = "<img class='img-action setToNiche' title='设为Niche关键字' src='/"+fileContextPath+"/app/webroot/img/fav.gif'>"  ;
				}
				
				if(record.c <=0 && isDev &&  record.is_pause!=1){
					img = img +
					"<img class='img-action getSemrushKeyword' title='获取扩展关键字' src='/"+fileContextPath+"/app/webroot/img/expand-all.gif'>" ;
				}
				
				//网址
				img = img +
				"<img class='img-action getWebsite' title='获取搜索网址' src='/"+fileContextPath+"/app/webroot/img/search.png'>" ;
				if(  record.is_pause!=1  ){
					//网址
					img = img +
					"<img class='img-action uploadKeyword' title='上传关键字列表' src='/"+fileContextPath+"/app/webroot/img/send-now.gif'>" ;
					img = img +
					"<img class='img-action pauseDev' title='结束开发' src='/"+fileContextPath+"/app/webroot/img/delete-row.gif'>" ;
				}
				

				return img ;
            }} 
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 450 ;
		 },
		 title:"主关键字",
		 indexColumn:false,
		 querys:{_data:"d_list_MainKeyword"},
		 loadMsg:"主关键字加载中，请稍候......",
		 rowDblClick:function(row,record){
			 	currentMainKeyword = record ;
			 	$(".child-keyword").llygrid("reload",{parentId:record.keyword_id}) ;
			 	$(".niche-grid").llygrid("reload",{parentId:record.keyword_id}) ;
		 }
	}) ;
	
	$(".child-keyword").llygrid({
		columns:[
			{align:"left",key:"keyword",label:"关键字名称", width:"180px",format:function(val,record){
				
				var site = record.site||"us" ;
				var amazonUrl = amazonSiteMap[site] ;
				val = "<a href='http://"+amazonUrl+"/s/ref=nb_sb_noss?field-keywords="+val+"' target='_blank'>"+val+"</a>" ;
				
				if( record.is_niche == 1 ){
					return "<img   src='/"+fileContextPath+"/app/webroot/img/fav.gif'>"+val  ;
				}
				return val ;
			}},
			{align:"left",key:"keyword_type",label:"类型", width:"10%"},
			{align:"left",key:"search_volume",label:"搜索量", width:"10%"},
			{align:"left",key:"cpc",label:"CPC", width:"10%"},
			{align:"left",key:"competition",label:"竞争", width:"10%"},
			{align:"center",key:"site",label:"国家", width:"10%"},
            {align:"center",key:"keyword_id",label:"操作",width:"16%",format:function(val,record){
            	var img = "" ;
				
				if(record.is_niche != 1 && record.is_pause_p !=1 ){
					if(isDev  ){
						img = "<img class='setToNiche' title='设为Niche关键字' src='/"+fileContextPath+"/app/webroot/img/fav.gif'>"  ;
					}
					
				}
				
				if( isDev  && record.is_pause_p !=1  ){
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
			 return $(window).height() - 450 ;
		 },
		 title:"扩展关键字",
		 indexColumn:false,
		 querys:{_data:"d_list_keywordByMain",parentId:'-'},
		 loadMsg:"扩展关键字加载中，请稍候......"
	}) ;
	
	$(".niche-grid").llygrid({
		columns:[
			{align:"center",key:"keyword_id",label:"操作", width:"8%",format:function(val,record){
					var html = [] ;
					//html.push("<a href='#' class='action niche-update' val='"+val+"'>设置</a>&nbsp;") ;
					html.push("<img class='action niche-update' title='设置' val='"+val+"' src='/"+fileContextPath+"/app/webroot/img/config.gif'>") ;
					html.push("<img class='action niche-delete' title='删除关键字' val='"+val+"' src='/"+fileContextPath+"/app/webroot/img/delete.gif'>") ;
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
			 return 240 ;
		 },
		 title:"Niche关键字列表",
		 indexColumn:false,
		 querys:{_data:"d_niche_list",parentId:'-'},
		 loadMsg:"Niche关键字加载中，请稍候......",
		 rowDblClick:function(row,record){
			 currentNichKeyword = record ;
		 },
		 loadAfter:function(){
			 currentNichKeyword =null ;
		 }
	}) ;
	
	
	
	
	
	$(".pauseDev").live("click",function(){
		if(window.confirm("确认终止该主关键字Niche开发，如确定，将自动清除所有扩展关键字吗？")){
			var record = $.llygrid.getRecord(this) ;
			
			$.dataservice("model:Keyword.pauseMainKeyword",{keywordId:record.keyword_id},function(result){
				$(".main-keyword").llygrid("reload",{},true) ;
			});
		}
		
	}) ;
	
	
	
	
	
	
	
	var currentNichKeyword = null ;
	
	$(".niche-update").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		openCenterWindow(contextPath+"/page/forward/Keyword.nicheDev/"+record.keyword_id,900,680,function(win,ret){
			$(".niche-grid").llygrid("reload",{},true) ;
		}) ;
	}) ;
	
	$(".niche-delete").live("click",function(){
		if(window.confirm("确认删除该niche关键字吗？")){
			var record = $.llygrid.getRecord(this) ;
			
			$.dataservice("model:Keyword.deleteNiche",{nicheId:record.keyword_id},function(result){
				$(".niche-grid").llygrid("reload",{},true) ;
			});
		}
		
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
		if($(".extend-keyword-form").is(":hidden")){
			$(".extend-keyword-form").show() ;
		}else{
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
					$(".extend-keyword-form").hide() ;
				});
			}
		}
	}) ;
	
	$(".btn-query").click(function(){
			//if( !currentMainKeyword )alert("先选择主关键字") ;
			var json = $(".toolbar-filter").toJson() ;
			json.taskId = taskId ;
			json.parentId =currentMainKeyword? currentMainKeyword.keyword_id:"" ;
			
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
		//if( !currentMainKeyword )alert("先选择主关键字") ;
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
			var kId = currentMainKeyword?currentMainKeyword.keyword_id :"";
			json.parentId =kId;
			$.dataservice("model:Keyword.filterKeyword",json,function(result){
				$(".child-keyword").llygrid("reload",{parentId: kId ,taskId:taskId}) ;
			});
		}
	}) ;
	
	var isInit = false ;
	function createGrid(  keywordId , text ){
		if(isInit){
			$(".child-keyword").llygrid("reload",{parentId:keywordId,taskId:taskId}) ;
			return ;
		}
		isInit = true ;
		
		
	}
	
	createGrid("","") ;
	
	function  labelClick(){
		var keywordId = $(this).parents("tr:first").attr("keyword-id") ;
		var keywordText =  $(this).parents("tr:first").attr("keyword")  ;
		var num = $(this).parents("tr:first").find(".num-td").text() ;
		
		createGrid(keywordId,keywordText) ;
	}
	
	$(".label-td").live("dblclick",function(){
		labelClick.call(this) ;
	}) ;
	
	
	
	$(".groupKeyword").live("click",function(){
		if(!currentNichKeyword){
			alert("未选中Niche关键字？") ;
			return ;
		}
		var record = $.llygrid.getRecord(this) ;
		var me = $(this) ;
		if(window.confirm("确认将改关键字添加到Niche关键字【"+currentNichKeyword.keyword+"】分组中？")){
			var keywordId 		= record.keyword_id ;
			var groupId         = currentNichKeyword.keyword_id;
			$.dataservice("model:Keyword.groupKeyword", {keywordId:keywordId,groupId:groupId} ,function(result){
				$(".child-keyword").llygrid("reloadP",{},true) ;
				$(".niche-grid-group").llygrid("reload",{groupId:currentNichKeyword.keyword_id}) ;
			});
		}
		
	}) ;
	
	$(".removeKeyword").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		var me = $(this) ;
		if(window.confirm("确认删除该关键字？")){
			var keywordId 		= record.keyword_id ;
			$.dataservice("model:Keyword.deleteKeyword", {keywordId:keywordId} ,function(result){
				$(".child-keyword").llygrid("reloadP",{},true) ;
			});
		}
		
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
			params.site = currentSite|| $("#site").val()  ;
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
	
	$(".uploadKeyword").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		var keywordId 		= record.keyword_id ;
		var keywordText 	= record.keyword ;
		openCenterWindow(contextPath+"/page/forward/Keyword.upload/"+keywordId+"/"+record.task_id+"/"+record.site,660,450,function(win,ret){
			$(".main-keyword").llygrid("reload",{},true) ;
		},{keyword:keywordText}) ;
	}) ;
	
	$(".transferKeyword").live("click",function(){
		var record = $.llygrid.getRecord(this) ;
		var keywordId 		= record.keyword_id ;
		var keywordText 	= record.keyword ;
	
		var productGridSelect = {
				title:'关键字任务选择',
				defaults:[],//默认值
				key:{value:'task_id',label:'name'},//对应value和label的key
				multi:false ,
				width:700,
				height:600,
				grid:{
					title:"关键字任务选择",
					params:{
						sqlId:"d_list_task"
					},
					ds:{type:"url",content:contextPath+"/grid/query"},
					pagesize:10,
					columns:[//显示列
						{align:"center",key:"name",label:"任务名称",sort:true,width:"30%",query:true},
						{align:"center",key:"create_date",label:"创建时间",sort:true,width:"30%"}
					]
				}
		   } ;
		   
		$.listselectdialog( productGridSelect,function(){
			var args = jQuery.dialogReturnValue() ;
			var value = args.value||[] ;
			var label = args.label ;
			var selectReocrds = args.selectReocrds ;
			
			$.dataservice("model:Keyword.transferKeyword",{'keywordId':keywordId,taskId:value.join(",")},function(result){
				$(".main-keyword").llygrid("reload",{},true) ;
			});
			
			return false;
		}) ;
	}) ;
	
	
}) ;