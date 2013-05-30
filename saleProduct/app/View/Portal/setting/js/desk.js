(function(){
	//初始化桌面
	for( var deskId in webosService.cacheDeskAppWidgets){
		if(deskId == 'deskActions') continue ;//桌面操作
		var desk = webosService.cacheDeskAppWidgets[deskId] ;

		createDeskPanelIcom({deskId:deskId,name:desk.name,client:desk.client}) ;
	} ;
	
	sortDeskPanel() ;
	
	//添加桌面
	$(".app_desk_add").click(function(){
		var deskHtml = [] ;
		deskHtml.push('<li class="nav_ico app_desk_ico" >') ;
		deskHtml.push('	<div class="appicon app_desk_thumbnail"></div>') ;
		deskHtml.push('	<h2 class="app_desk_text"><input type="text" class="app_desk_input new_add"></h2>') ;
		deskHtml.push('<span class="app_desk_del"></span>') ;
		deskHtml.push('</li>') ;
		
		$(".app_desk_add").before(deskHtml.join("")) ;
		$(".app_desk_add").prev().find("input").focus();
		
		sortDeskPanel() ;
	}) ;
	var isEnter = false ;
	//文本框回车事件
	$(".app_desk_text input").live("keydown",function(event){
		if(event.keyCode==13){
			if( $(this).hasClass("new_add") ){
				if( !jQuery.trim($(this).val()) ) return false ;
			}
			
			isEnter = true ;
			event.stopPropagation();
			saveDeskPanel() ;
			return false ;
        }
	}).live("blur",function(){
		if( $(this).hasClass("new_add") ){
				if( !jQuery.trim($(this).val()) ) return false ;
		}
		
		if(isEnter){
			isEnter = false ;
			return ;
		}
		saveDeskPanel() ;
		return false ;
	}) ;
	
	$(".app_desk_del").live("click" , function(){
		var deskId = $(this).parent().find(".app_desk_text").attr("deskid")||"";
		if(!deskId){
			$(this).parent().remove() ;
			return ;
		}else{
			if(confirm("确认删除吗？")){
				//调用后台服务删除桌面
				Portal.services.deleteDeskPanel({deskid:deskId}) ;
				$(this).parent().remove() ;
			}
		}	
	}) ;
	
	$(".app_desk_admin").live("click",function(){
		var deskId = $(this).parent().find(".app_desk_text").attr("deskid")||"";
		window._currentConfigDeskId = deskId ;
		if(!deskId){
			return ;
		}else{
			var buildUrl= webosService.rebuildUrl||function(data){return data ;} ;
			
			window._currentPermWindow = webos.window.open({
				title:"桌面权限分配",
				url:buildUrl(webosService.url.desk_perm),
				iframe:false,
				width:600,
				height:520
			})
		}	
	}) ;
	
	$(".app_desk_text").live("dblclick",function(){
		if($(this).find("input")[0]) return ;
		var val = $(this).text() ;
		$('<input type="text" class="app_desk_input" value="'+val+'">').appendTo($(this).empty()).focus() ;
	}) ;
	
	function saveDeskPanel(){
		var panel = [] ;
		$(".app_desk_text").each(function(index , item){
			var deskId = $(this).attr("deskid")||"";
			var name   = $(this).find("input")[0]?$(this).find("input").val():$(this).text() ;
			var order  = index+1 ;
			if( name ){
				panel.push(deskId+"||"+name+"||"+order) ;
			}else if(deskId){
				panel.push(deskId+"||"+name+"||"+order) ;
			}
			
		}) ;
		
		var result = Portal.services.saveDeskPanel(panel.join(",,")) ;
		$(".app_desk_ico").remove();
		$(result).each(function(){
			createDeskPanelIcom(this) ;
		});
	}
	
	function createDeskPanelIcom(panel){
		var deskHtml = [] ;
		deskHtml.push('<li class="nav_ico app_desk_ico">') ;
		deskHtml.push('	<div class="appicon app_desk_thumbnail"></div>') ;
		deskHtml.push('	<h2 class="app_desk_text" deskid="'+panel.deskId+'">'+panel.name+'</h2>') ;
		deskHtml.push('<span class="app_desk_del"></span>') ;
		if(window.$hasDeskManagePerm && !panel.client){
			deskHtml.push('<span class="app_desk_admin"></span>') ;
		}
		deskHtml.push('</li>') ;
		
		$(".app_desk_add").before(deskHtml.join("")) ;
	}
	
	function sortDeskPanel(){
		$(".app_desk_mg").sortable({
			 delay: 1, 
			 helper: 'clone',
			 cancel: '.app_desk_text',
			 items: 'li.app_desk_ico',    
			 start:function(){						
				
			 },
			 deactivate: function(event, ui) {
				saveDeskPanel() ;
			 }
		});
	}
})();