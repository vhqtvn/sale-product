var _webosRoot = Config.contextPath+"/portal/layouts/webos/" ;
var _customerId = "_customer" ;
//数据服务
var webosService = {
	cacheDeskAppWidgets: null ,
	backendManageMenu  : null ,
	currentTheme	   : null ,
	remoteApps		   : null ,
	root:_webosRoot,
	url:{
		"setting_index" : 	_webosRoot+"setting/setting_index.html",
		"theme"			:	_webosRoot+"setting/theme.html",
		"console"		:	_webosRoot+"setting/console.html",
		"desk"			:	_webosRoot+"setting/desk.html",
		"desk_perm"		:	_webosRoot+"setting/desk_permission.html",
		"app_install"	:	_webosRoot+"setting/app_uninstall.html",
		"account_mapping":	_webosRoot+"setting/account_mapping.jsp",
		
		"widget_index"	:	_webosRoot+"market/widget/widget_index.html",
		"widget_manage" :   "~/portal/plugins/widget/widget/list_widget.jsp",
		
		"app_index"		:	_webosRoot+"market/app/app_index.html",
		"app_list"		:	_webosRoot+"market/app/app_list.html",
		"app_content"	:	_webosRoot+"market/app/app_content.html",
		
		"front_index"	:	_webosRoot+"market/front/front_index.html",
		"front_list"	:	_webosRoot+"market/front/front_list.html",
		"front_content"	:	_webosRoot+"market/front/front_content.html"
	},
	loadDesktops: function(callback){//加载桌面应用
		var deskAppWidgets = Portal.services.loadDeskItems() ;
		webosService.cacheDeskAppWidgets = deskAppWidgets ;
		
		//初始化desk Action
		var deskActions = deskAppWidgets.deskActions ;
		
		//get Root
		var roots = {} ;
		var actionMaps = {} ;
		$(deskActions).each(function(index , action){
			if(!action.parentId){
				roots[ action.type ] = action.seqId ;
			}else{
				actionMaps[action.parentId] = actionMaps[action.parentId]||[] ;
				actionMaps[action.parentId].push(action) ;
			}
			
			if(action.func){
				eval("action.func = function(){"+action.func+"}") ;
			}
			
			action.text = action.text||action.name ;
			
		}) ;
		
		for(var o in roots){
			var root = roots[o] ;
			webosService.configs[o] = actionMaps[ root ] ;
			$(actionMaps[root]).each(function(){
				if( actionMaps[this.seqId] ){
					this.childs = actionMaps[this.seqId] ;
				}
			}) ;
		}
		//分组 group
		for( var o in webosService.configs ){
			var groups = {} ;
			var groupArray = [] ;
			var hasGroup = false ;
			$(webosService.configs[o]).each(function(){
				if( this.displayGroup ){
					hasGroup = true ;
					groups[this.displayGroup] = groups[this.displayGroup]||[] ;
					groups[this.displayGroup].push(this) ;
				}
			}) ;
			for(var o1 in groups){
				groupArray.push(groups[o1]) ;
			}
			
			if( hasGroup ){
				webosService.configs[o] = groupArray ;
			}
		}
		
		callback(webosService.cacheDeskAppWidgets) ;
	},
	loadTheme:function(callback){//加载主题
		var theme = Portal.services.loadDeskTheme() ;
		if(theme == null ){
			theme = {url:"images/theme/1_b.jpg" }
		}else{
			theme = {
				url:theme.themeUrl 
			} ;
		}
		webosService.currentTheme = theme ;
		callback( { url: webosService.root+theme.url } );
	},
	loadSysThemes:function(callback){//加载系统可选主题
		var themes = Portal.services.loadDeskSysThemes() ;
		
		callback( themes ) ;
	},
	saveTheme:function(theme){
		webosService.currentTheme = theme ;
		
		Portal.services.saveDeskTheme({
			themeId: theme.id||"",
			themeName:theme.name||"",
			themeUrl:theme.url
		});
	},
	loadWidgets:function(callback){
		if( webosService.cacheDeskAppWidgets ){
			callback(webosService.cacheDeskAppWidgets) ;
		}else{
			
		}
	},
	loadStartSubMenu:function(url,appId ,appCode, callback){//加载开始菜单中的子菜单
		webosService.StartSubMenuCache = webosService.StartSubMenuCache||{} ;
		if(webosService.StartSubMenuCache[appId]){
			callback( webosService.StartSubMenuCache[appId] ) ;
		}else{
			var menus = Portal.services.loadAppFunctions(appId,appCode)  ;
			
			if(typeof menus == 'string'){
				eval("menus = "+menus) ;
			}
			
			if(menus.returnValue){
				//数据格式转换
				menus = menus.returnValue ;
				
				if(typeof menus == 'string'){
					eval("menus = "+menus) ;
				}
			}
			
			webosService.StartSubMenuCache[appId] = menus ;
			callback( menus ) ;
		}
	},
	loadStartMenus:function(callback){

		var starts = Portal.services.loadDeskStart() ;
		var _menus = starts[0] ;
		//格式化菜单
		//_menus = [] ;//formatMenu( _menus ) ;

		var menus = [] ;
		$(_menus).each(function(index,menu){
			if( menu.code != 'backend_manage' ){
				menus.push(menu) ;
			}else{
				webosService.backendManageMenu = menu ;
			}
		}) ;
		
		$(webosService.configs.startMenus||[]).each(function(){
			this.id = this.code ;

			if(this.id == 'list_app'){//应用程序
				var apps = formatMenu( starts[1] );
				$(apps).each(function(){
					if( !this.childs || this.childs.length <=0 ){
						this.childsUrl = "-" ; //-表示读取系统数据
					}
				}) ;
				this.childs = apps;
			}
			menus.push( this ) ;
		}) ;

		/*
		menus.push({id:"list_setting",name:"控制面板",func:function(){
			webosService.window.console() ;
		}}) ;
		
		menus.push({id:"list_app",name:"应用程序",childs: starts[1]}) ;
		menus.push({id:"list_logout",name:"注销",func:function(){
			webosService.quit() ;
		}}) ;*/
		
		callback(menus) ;
		
		function formatMenu(menus  ){
   			var _roots = [] ;
   			var map = {} ;
   			$( menus ).each(function(){
   				
   				if( !this.pid ){
   					_roots.push(this) ;//root
   				}else{
   					map[this.pid] = map[ this.pid ]||[] ;
   					map[this.pid].push( this ) ;
   				}
   				this.name = this.name||this.title ;
   			}) ;
   			
   			formatChild(_roots,map) ;
   			return _roots ;
   		}
   		
   		function formatChild(menus,map){
   			$(menus).each(function(){
   				if(map[this.id]){
   					this.childs = map[this.id] ;
   					formatChild( this.childs ,map) ;
   				}
   			}) ;
   		}
	},
	loadAppStoreCategory:function(callback){
		//远程加载应用分类
		/*var categorys = [
			{name:"游戏",id:"game",code:"game"},
			{name:"工具",id:"tools",code:"tools"}
		] ;*/
		//alert(1);
		var categorys = Portal.services.httpproxy("list-app-category") ;
		if(typeof categorys == 'string'){
			eval("categorys = "+categorys) ;
		}
		
		//数据格式转换
		categorys = categorys.returnValue ;
		
		if(typeof categorys == 'string'){
			eval("categorys = "+categorys) ;
		}
		
		$(categorys).each(function(){
			this.id = this.id||this.ID ;
			this.name = this.name||this.Name ;
		}) ;
		
		callback( categorys ) ;
	},
	loadAppDetail:function(appId,callback){
		//远程加载应用分类
		/*var categorys = [
			{name:"游戏",id:"game",code:"game"},
			{name:"工具",id:"tools",code:"tools"}
		] ;*/
		/*{"returnCode":200,"returnStatus":null,
		 * "returnValue":{"exProductCharges":[{"goodsSpecs":null,"chargeId":"8ed8ea81-f3bb-4e79-9875-4cf46be6e0b5","productId":"11111111111111","goodsId":null,"schemaId":"cycle-year","chargeCode":"AlarmMonitorUsage","chargeType":"cycle","chargeCycle":"year","priceDesc":null,"marketPrice":10,"price":20,"memberPrice":309,"priceExpr":null,"dispOrder":0},{"goodsSpecs":null,"chargeId":"d7a994cb-1b78-4e7d-aedf-8cf40262edb4","productId":"11111111111111","goodsId":null,"schemaId":"usage-none","chargeCode":"AlarmMonitorUsage","chargeType":"usage","chargeCycle":"none","priceDesc":null,"marketPrice":10.3,"price":30.1,"memberPrice":30.1,"priceExpr":null,"dispOrder":0},{"goodsSpecs":[{"goodsId":"58dc2739-f9d5-4a81-a86e-6a243e5dcf6a","specValueId":"8b0aea59-8de4-4985-a20e-d9529587ef3d","specId":"6c9ccb39-e07f-4e9c-8749-3f72db8031e3","productId":"11111111111111","specValue":"çº¢è²","specDisplay":"çº¢è²","specPicUrl":"","specName":"é¢è²"},{"goodsId":"58dc2739-f9d5-4a81-a86e-6a243e5dcf6a","specValueId":"26c28744-4608-4812-bf44-a0b5ea18b69c","specId":"db4e06ab-0863-48b3-940f-0ae25d29c8cb","productId":"11111111111111","specValue":"å¤§","specDisplay":"å¤§","specPicUrl":"","specName":"è§æ¨¡"}],"chargeId":"087120c1-779b-45e9-a0c4-7de360d5f040","productId":"11111111111111","goodsId":"58dc2739-f9d5-4a81-a86e-6a243e5dcf6a","schemaId":"cycle-year","chargeCode":"AlarmMonitorUsage","chargeType":"cycle","chargeCycle":"year","priceDesc":null,"marketPrice":10,"price":30,"me...cle-year","productId":"11111111111111","modeId":null,"schemaName":"æå¹´","description":null,"priceExpr":null,"chargeMode":"3","productCharge":[]},{"schemaId":"usage-none","productId":"11111111111111","modeId":null,"schemaName":"ä½¿ç¨æ§","description":null,"priceExpr":null,"chargeMode":"2","productCharge":[]}],"productSpec":[{"specId":"6c9ccb39-e07f-4e9c-8749-3f72db8031e3","productId":"11111111111111","name":"é¢è²","dispOrder":"0","specValues":[{"specId":"6c9ccb39-e07f-4e9c-8749-3f72db8031e3","productId":"11111111111111","specValueId":"8b0aea59-8de4-4985-a20e-d9529587ef3d","specValue":"çº¢è²","specDisplay":"çº¢è²","isDefault":false,"dispOrder":1,"description":null},{"specId":"6c9ccb39-e07f-4e9c-8749-3f72db8031e3","productId":"11111111111111","specValueId":"ef0f552a-718e-4410-a155-f008521e491e","specValue":"ç»¿è²","specDisplay":"ç»¿è²","isDefault":false,"dispOrder":2,"description":null}]},{"specId":"db4e06ab-0863-48b3-940f-0ae25d29c8cb","productId":"11111111111111","name":"è§æ¨¡","dispOrder":"1","specValues":[{"specId":"db4e06ab-0863-48b3-940f-0ae25d29c8cb","productId":"11111111111111","specValueId":"02a795e2-4cc2-4ab1-9e4a-67628f4b50f7","specValue":"å°","specDisplay":"å°","isDefault":false,"dispOrder":2,"description":null},{"specId":"db4e06ab-0863-48b3-940f-0ae25d29c8cb","productId":"11111111111111","specValueId":"26c28744-4608-4812-bf44-a0b5ea18b69c","specValue":"å¤§","specDisplay":"å¤§","isDefault":false,"dispOrder":1,"description":null}]}]},"returnDesc":null}*/
		var appDetail = Portal.services.httpproxy("load-app?appId="+appId) ;
		callback( appDetail ) ;
	},
	loadAppStoreApps:function(url,categoryId,categoryCode,callback){
		alert("loadAppStoreApps");
		//远程加载对应分类的应用
		/*var apps = [
			{id:"baidu",name:"百度",url:"http://www.baidu.com",description:"1111",thumbnail:"",configUrl:""},
			{id:"google",name:"谷歌",url:"http://www.google.com",description:"2222",thumbnail:"",configUrl:""},
			{id:"sina",name:"新浪",url:"http://www.sina.com",description:"3333",thumbnail:"",configUrl:""}
		] ;*/
		
		var apps = Portal.services.httpproxy("list-app?categoryId="+categoryId+"&categoryCode="+categoryCode) ;
		
		if(typeof apps == 'string'){
			eval("apps = "+apps) ;
		}
		
		apps = apps.returnValue ;
		
		if(typeof apps == 'string'){
			eval("apps = "+apps) ;
		}
		
		//alert(JSON.stringify(apps));
		//数据格式转换
		//apps=apps.returnValue;
		$(apps).each(function(){
			//this.id=this.ID ;
			//this.name=this.Name ;
		});
		webosService.remoteApps = apps ;
		
		//获取数据库应用，判断是否已经加载
		var appMarkets = Portal.services.loadMarketApps() ;
		
		$(apps).each(function(index , app){
			$(appMarkets).each(function(index , localApp){
				if( app.id == localApp.code  ){
					app.desktop = "1" ;
					app.visibleAll = localApp.visibleAll ;
				}
			}) ;
		}) ;
		
		callback( apps ) ;
	},
	//加载应用
	loadAppMarkets:function(callback){
		//加载应用
		var appMarkets = Portal.services.loadMarketApps() ;
		callback( appMarkets ) ;
	},
	//加载应用前端（快捷方式 front）
	loadMarketAppFunctions:function(url,appId,appCode,callback){
		var appMarkets = null ;
		//if( appId == "custom_widget" ){
		//	appMarkets = Portal.services.loadMarketWidgets(url,appId,appCode);
		//}else{
			//var localAppFunctions = Portal.services.loadMarketAppFunctions(url,appId,appCode) ;
			appMarkets = Portal.services.loadAppFunctions(appId,appCode)  ;
			//appMarkets = Portal.services.httpproxy("list-app-function?appId="+appCode+"&type=front") ;
			//webosService.cacheDeskAppWidgets
			
			if(typeof appMarkets == 'string'){
				eval("appMarkets = "+appMarkets) ;
			}
			
			if(appMarkets.returnValue){
				appMarkets = appMarkets.returnValue ;
				
				if(typeof appMarkets == 'string'){
					eval("appMarkets = "+appMarkets) ;
				}
			}
				
		//}
		
		callback( appMarkets ) ;
	},
	//加载应用对应的小挂件
	loadAppWidget:function( url,appId,appCode, callback ){
		var widgets = null ;
		//if(appId == "custom_widget"){
		//	widgets = Portal.services.loadMarketWidgets(url,appId,appCode);
		//}else{
			//widgets = Portal.services.httpproxy("list-app-function",{appId:appCode,customerId:_customerId,type:"widget"}) ;
			//widgets = Portal.services.httpproxy("list-app-function?appId="+appCode+"&type=widget") ;
			widgets = Portal.services.loadAppWidgets(appId,appCode)  ;
			
			if(typeof widgets == 'string'){
				eval("widgets = "+widgets) ;
			}
			if(widgets.returnValue){
				widgets = widgets.returnValue ;
				
				if(typeof widgets == 'string'){
					eval("widgets = "+widgets) ;
				}
			}
		//}
		callback(widgets) ;
	},
	saveConfig:function(config,entity){
		//alert(JSON.stringify(config));
		var appFunction = "";
		if( entity ){
			appFunction = entity.content ;
		}
		
		Portal.services.saveDesk( JSON.stringify(config) , appFunction) ;
	},
	loadSettingMenus:function( callback ){
		var appMarkets = Portal.services.loadMarketApps(null,true) ;
		//alert(JSON.stringify(apps.childs)) ;
		callback(appMarkets) ;
	},
	loadControllApp:function(callback){//加载控制面板应用
		var categorys = [] ;
		var menus = Portal.services.getConsoles()||[] ;
		
		var _menus = jQuery.parseJSON( JSON.stringify(menus) ) ;
		categorys.push({id:"system",name:"系统管理",apps:_menus}) ;
		var controlPanel = webosService.configs.controlPanel||[] ;
		
		categorys.push({id:"desk",name:"桌面管理",apps:controlPanel}) ;
		
		/*$(webosService.configs.controlPanel||[]).each(function(){
			_menus.push( this ) ;
		}) ;*/
		
		callback(categorys) ;
	},
	installApp:function(appId,seeall,callback){
		var isSuccess = false ;

		var urls = null ;//Portal.services.httpproxy("install-app?appId="+appId) ;
		$(webosService.remoteApps).each(function(index,app){
			if(app.id == appId){
				app.visibleAll = seeall ;
				Portal.services.addApp(app) ;
			}
		}) ;
		
		callback( urls ) ;
	},
	uninstallApp:function(appId,callback){
		var isSuccess = false ;
		var result = null ;//Portal.services.httpproxy("uninstall-app?appId="+appId) ;
		result = result||{returnCode:200} ;
		
		if( typeof result == 'string' || result.returnCode == '200'){
			Portal.services.uninstallApp({appId:appId}) ;
			callback( result ) ; 
		}else{
			try{
				alert(result.returnDesc.error.message);
			}catch(e){
				alert("卸载失败");
			};
		} 
	},
	loadDeskPermisson:function(deskId,callback){
		callback = callback||function(){};
		$.dataservice("spring:portalDeskService.loadDeskPermission",{deskId:deskId},function(data){
			callback(data) ;
		}) ;
	},
	saveDeskPermisson:function(deskId,roleIds,callback){
		$.dataservice("spring:portalDeskService.saveDeskPermission",{deskId:deskId,roleIds:roleIds.join(",")},function(data){
			callback(data) ;
		}) ;
	},
	quit:function(){ //退出系统
		window.location.href = Config.contextPath+"/common/login/logout.jsp" ;
	},rebuildUrl:function(url){
		var split = url.indexOf("?")!=-1?"&":"?" ;
		return jQuery.utils.parseUrl(url+split+"portalCode="+Config.portalCode) ;
	},
	winSize:function(flag,value){
		if( value ){
			$.cookie(flag,JSON.stringify(value),{expires:1000});	
		}else{
			var size = $.cookie(flag)|| null ;
			return size?jQuery.parseJSON( size ):null ;//JSON.stringify(menus)
		}
	}
} ;

webosService.configs = {} ;

/*
webosService.configs = {
	bodyContextMenus:[//桌面右键菜单
		[{ text: "显示桌面",
			func: function(){
				webos.task.showDesk() ;
			}
		},{ text: "锁定屏幕",
			func: function(){
				window.confirm('TODO:您锁定屏幕前，需要先设定解锁密码')
			}
		},{ text: "关闭所有窗口",
			func: function(){
				webos.window.close_all() ;
			}
		}],			
		[{ text: "添加",
			data: [
				[{
					text: "添加应用",
					func: function(){							
						webosService.window.addApp() ;
					}
				},{
					text: "添加快捷方式",
					func: function(){							
						webosService.window.addShortcut() ;
					}
				},{
					text: "添加小挂件",
					func: function(){
						webosService.window.addWidget() ;
					}
				}]
			]
		}],			
		[
		{ text: "切换主题",
			func: function(){
				webosService.window.theme() ;
			}
		},
		{ text: "控制面板",
			func: function(){
				webosService.window.console() ;
			}
		}],
		[{ text: "修改设置",
			func: function(){
				webosService.window.setting() ;
			}
		},{ text: "注销用户",
			func: function(){					
				webosService.quit() ;
			}
		}]
	],
	taskActions:[//任务栏图标
	  	{name:"设置页面",code:"setting",func:function(){
	  		webosService.window.setting() ;
	  		return false ;
	  	}},
	  	{name:"点击显示桌面",code:"desk",func:function(){
	  		webos.task.showDesk() ;
	  	}},
	  	{name:"关闭所有窗口",code:"allclo",func:function(){
	  		webos.window.close_all() ;
	  	}},
	  	{name:"锁屏",code:"lock"},
	  	{name:"切换主题",code:"skin",func:function(){
			webosService.window.theme() ;
	  	}},
	  	{name:"退出系统",code:"quit",func:function(){
	  		webosService.quit() ;
	  	}}
	],
	iconContextMenus:[//应用图标右键菜单
		[{
			text: "打开应用",
			func: function() {
				webos.icons.open(this) ;
			}
		}],
		[{
			text: "卸载应用",
			func: function() {
				webos.icons.uninstall(this) ;
			}
		}]
	],
	taskContextMenus:[//任务栏右键菜单
		[{
			text: "隐藏菜单"				
		}]
	],
	headLinks:[
		{text:"应用商店",styleClass:"set_homepage",func:function(){
			webosService.window.addApp() ;
		}},
		{text:"设为首页",styleClass:"set_homepage",func:function(){
			webos.utils.setHome(this,window.location) ;
		}},
		{text:"添加收藏",styleClass:"set_collection",func:function(){
			webos.utils.addFavorite(window.location,document.title)
		}},
		{text:"反馈意见",styleClass:"set_suggest",func:function(){
			//webos.utils.addFavorite(window.location,document.title)
		}},
		{text:"V1.0",styleClass:"set_version"}
	]
} ;
*/
//用户接口
webosService.window = {
	setting: function(){
		webos.window.open({
		  title:"修改设置",
		  iframe:false,
		  scroll:'hidden',
		  url:webosService.url.setting_index,
		  width:1000,
		  height:500
	   }) ;
	},
	theme:function(){
		webos.window.open({
			title:"切换主题",
			url:webosService.url.theme,
			iframe:false,
			width:730,
			height:340
		  })
	},
	desk:function(){
		webos.window.open({
			title:"桌面管理",
			url:webosService.url.desk,
			iframe:false,
			width:730,
			height:360
		  })
	},
	addApp:function(){
		//获取应用商店地址
		var stores = Portal.services.loadAppStore()||[] ;
		if( stores.length <=0  )
			alert("未注册应用商店！");
		webos.window.open({
			  title:stores[0]['NAME'],
			  iframe:true,
			  width:1000,
			  height:630,			 
			  url:stores[0]['URL']
		   });
	},
	addShortcut:function(){
		webos.window.open({
			  title:"添加快捷方式",
			  iframe:false,
			  width:980,
			  height:630,
			  url:webosService.url.front_index
		   });
	},addWidget:function(){
		webos.window.open({
		  title:"添加小挂件",
		  iframe:false,
		  width:700,
		  height:630,
		  url:webosService.url.widget_index
	   })
	},
	console:function(){
		webos.window.open({
			  title:"控制面板",
			  iframe:false,
			  width:700,
			  height:500,
			  x:webos.utils.width()/2-350,
			  y:webos.utils.height()/2-260,
			  scroll:'hidden',
			  url:webosService.url.console
		   })
	},
	appuninstall:function(){
		webos.window.open({
			  title:"应用卸载",
			  iframe:false,
			  width:600,
			  height:450,
			  x:webos.utils.width()/2-350,
			  y:webos.utils.height()/2-260,
			  scroll:'hidden',
			  url:webosService.url.app_install
		})
	},
	accountmapping:function(){
		webos.window.open({
			  title:"帐号映射",
			  iframe:true,
			  width:400,
			  height:300,
			  scroll:'hidden',
			  url:webosService.url.account_mapping
		})
	},
	widget:function(){//挂件管理
		webos.window.open({
			  title:"挂件管理",
			  iframe:true,
			  width:900,
			  height:650,
			  scroll:'hidden',
			  url: $.utils.parseUrl(webosService.url.widget_manage)
		})
	}
}