 function appUninstall(){	

	var	appID, 		// 应用ID
		settingUrl, //设置的链接地址
		appIcon, 	//设置的链接地址
		appName; 	//应用名字
		
	var menuHtml = '';
	//获取设置的应用json数据
	webosService.loadSettingMenus(function(apps){
		var count = 0 ;
		var html = [] ;
		$(apps).each(function(index , app){
			var name = app.title||app.name ;
			appIcon = webos.utils.parseIcon( app.iconLarge||app.icon ) ;

			var iconHtml = '' ;
			if(appIcon.isClass ){
				iconHtml = "<div class='appicon "+appIcon.value+"'></div>";			
			}else{
				iconHtml = "<div class='appicon' style='background:url("+appIcon.value+") no-repeat;'></div>";		
			};
		
			html.push('<li class="nav_ico" title="'+name+'" appid="'+app.code+'"');
			html.push('	icon="appicon"');
			html.push('	href="'+app.url+'"');
			html.push('	size="max" fixwidth="null" fixheight="null">');
			html.push(iconHtml);
			html.push('<h2>'+name+'</h2>');
			html.push('<span class="app_uninstall"></span>') ;
			html.push('</li>');
			
			count = index ;
		}) ;
		
		$("#app_uninstall_container>ul").html(html.join(""))
			.find(".app_uninstall").click(function(e){
				e.stopPropagation();
				var	appDID = $(this).parents('li').attr('appid') ;
				
				if( window.confirm("确认卸载该应用吗？") ){
					webosService.uninstallApp(appDID,function(){
						appUninstall(); 
						//刷新开始菜单
				   		$(".menu_list").empty() ;
				   		webos.task.startmenu() ;
					}) ;
				}
				return false ;
			}) ;
		

		return false;
	}) ;
  };
  

 appUninstall(); 

 