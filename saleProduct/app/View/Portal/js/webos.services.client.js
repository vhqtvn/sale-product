var $hasDeskManagePerm = true ;//有桌面管理权限

var _webosRoot = "" ;
var webosService = {
	cacheDeskAppWidgets: null ,
	backendManageMenu  : null ,
	currentTheme	   : null ,
	root:_webosRoot,
		url:{
		"setting_index" : 	_webosRoot+"setting/setting_index.html",
		"theme"			:	_webosRoot+"setting/theme.html",
		"console"		:	_webosRoot+"setting/console.html",
		"desk"			:	_webosRoot+"setting/desk.html",
		"desk_perm"		:	_webosRoot+"setting/desk_permission.html",
		
		"widget_index"	:	_webosRoot+"market/widget/widget_index.html",
		
		"app_index"		:	_webosRoot+"market/app/app_index.html",
		"app_list"		:	_webosRoot+"market/app/app_list.html",
		"app_content"	:	_webosRoot+"market/app/app_content.html",
		
		"front_index"	:	_webosRoot+"market/front/front_index.html",
		"front_list"	:	_webosRoot+"market/front/front_list.html",
		"front_content"	:	_webosRoot+"market/front/front_content.html"
	},
	loadDesktops: function(callback){//加载桌面应用
		var deskAppWidgets = _deskAppWidgets ;
		webosService.cacheDeskAppWidgets = deskAppWidgets ;
		callback(webosService.cacheDeskAppWidgets) ;
	},
	loadTheme:function(callback){//加载主题
		var theme=$.cookie("theme") ;
		if(theme == null){ 
			theme = {url:"images/theme/1_b.jpg"}
		}else{
			theme = $.parseJSON(theme) ;
		}
		
		webosService.currentTheme = theme ;
		callback( theme );
	},
	loadSysThemes:function(callback){//加载系统可选主题
		var themes = [] ;
		var themeNum = 8;
		for (var i=0;i<themeNum;i++){
			themes.push({id:"theme_"+i+1,name:"theme_"+i+1,thumbnail:"images/theme/"+(i+1)+"_s.jpg",url:"images/theme/"+(i+1)+"_b.jpg"}) ;
		};
		callback( themes ) ;
	},
	saveTheme:function(theme){
		webosService.currentTheme = theme ;
		$.cookie("theme",JSON.stringify(theme),{expires:1000});	
	},
	loadWidgets:function(callback){
		if( webosService.cacheDeskAppWidgets ){
			callback(webosService.cacheDeskAppWidgets) ;
		}else{
			
		}
	},
	loadStartSubMenu:function(url,appId ,appCode, callback){//加载开始菜单中的子菜单
		//TODO
	},
	loadStartMenus:function(callback){
		var menus = [
			{id:"list_favorite",name:"添加到收藏",icon:"icon10",iconSmall:"icon01"},
			{id:"list_savetodeskptop",name:"设为桌面图标",url:"http://www.baidu.com"},
			{id:"list_download",name:"下载应用",childs:[
				{id:"l1" , name:"XXXX"},
				{id:"l2" , name:"XXXX2"}
			]},
			{id:"list_lock",name:"锁定"},
			{id:"list_help",name:"帮助",childs:[
				{ id:'list_hotpoint',name:'热点',childs:[
					{ id:'list_weibo_sina1',name:'新浪1' },
					{ id:'list_weibo_sohu2',name:'搜狐2' }
				]  },
				{ id:'list_weibo',name:'微博',childs:[
					{ id:'list_weibo_sina',name:'新浪微博XXXXXXX' },
					{ id:'list_weibo_sohu',name:'搜狐微博' ,childs:[
						{ id:'list_weibo_sohu1',name:'11111',url:"http://www.sina.com" },
						{ id:'list_weibo_sohu2',name:'22222',url:"http://www.sina.com"  }
					] }
				] }
			]}
		] ;
		menus.push({id:"list_setting",name:"控制面板",func:function(){
			webosService.window.console() ;
		}}) ;
		menus.push({id:"list_app",name:"应用程序"}) ;
		menus.push({id:"list_logout",name:"注销",func:function(){
			webosService.quit() ;
		}}) ;
		
		callback(menus) ;
	},
	loadAppStoreCategory:function(callback){
		//远程加载应用分类
		var categorys = [
			{name:"游戏",id:"game",code:"game"},
			{name:"工具",id:"tools",code:"tools"}
		] ;
		callback( categorys ) ;
	},
	loadAppStoreApps:function(url,categoryId,categoryCode,callback){
		//远程加载对应分类的应用
		var apps = [
			{id:"baidu",name:"百度",url:"http://www.baidu.com",description:"1111",thumbnail:"",configUrl:""},
			{id:"google",name:"谷歌",url:"http://www.google.com",description:"2222",thumbnail:"",configUrl:""},
			{id:"sina",name:"新浪",url:"http://www.sina.com",description:"3333",thumbnail:"",configUrl:""}
		] ;
		
		webosService.remoteApps = apps ;
		
		callback( apps ) ;
	},
	//加载应用
	loadAppMarkets:function(callback){
		var appMarkets = _marketApps  ;
		callback( appMarkets ) ;
	},
	//加载应用前端（快捷方式 front）
	loadMarketAppFunctions:function(url,appId,appCode,callback){
		$(_marketApps).each(function(index , app){
			if( app.id == appId ){
				callback(app.apps) ;
			}
		}) ;
	},
	//加载应用对应的小挂件
	loadAppWidget:function( url,appId,appCode, callback ){
		$(_marketApps).each(function(index , app){
			if( app.id == appId ){
				callback(app.widgets) ;
			}
		}) ;
	},
	saveConfig:function(config){
		//alert(JSON.stringify(config));
		//return ;
		//Portal.services.saveDesk( JSON.stringify(config)) ;
		
		$.cookie("webosDesk",JSON.stringify(config),{expires:1000});	
	},
	loadSettingMenus:function( callback ){
		var apps = [
			{configUrl:"http://www.baidu.com",name:"广告组件",icon:"images/icons/app/small.png"},
			{configUrl:"http://www.baidu.com",name:"任务管理",icon:"images/icons/app/small.png"},
			{configUrl:"http://www.baidu.com",name:"BBS应用",icon:"images/icons/app/small.png"}
		]
		callback(apps) ;
	},
	loadControllApp:function(callback){//加载控制面板应用
		var menus = [
			{name:"桌面管理",func:function(){
				webosService.window.desk();
			}} ,
			{name:"应用设置",func:function(){
				webosService.window.setting();
			} } ,
			{name:"主题设置",func:function(){
				webosService.window.theme();
			}}
		] ;
		callback(menus) ;
	},
	installApp:function(appId,seeall,callback){
		var isSuccess = false ;
		alert("TODO:安装应用！");
		callback() ;
	},
	uninstallApp:function(appId,callback){
		alert("TODO:卸载应用！");
		callback() ;
		 
	},
	loadDeskPermisson:function(deskId,callback){
		
		var data = [
			{"id":"84FA16A2-E5A9-43A3-B202-C88BFE28BF8F","name":"普通员工","checked":"0"},
			{"id":"AD6754E3-B4D8-412D-9CD9-DC19DC42EBEA","name":"系统管理员","checked":"0"}] ;
		callback(data) ;
	},
	saveDeskPermisson:function(deskId,roleIds,callback){
		alert("TODO:保存权限设置！");
		callback() ;
	},
	quit:function(){ //退出系统
		//window.location.href = Config.contextPath+"/common/login/logout.jsp" ;
		alert("quit");
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


webosService.configs = {
	bodyContextMenus:[ //桌面右键菜单
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
		}/*,{ text: "退出系统",
			func: function(){
				var quitChoose = window.confirm('您确定要退出系统吗？');
				if(quitChoose == true){
					alert('您已经成功退出，谢谢使用');
				}
			}
		}*/]
	],
	taskActions:[ //任务栏图标
	  	{name:"设置页面",code:"setting",func:function(){
	  		webosService.window.setting() ;
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
	iconContextMenus:[ //应用图标右键菜单
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
	taskContextMenus:[ //任务栏右键菜单
		[{
			text: "隐藏菜单"	 , func:function(){
				webos.task.showDesk() ;
			}
		}]
	],
	startContextMenus:[
		[{
			text:"打开应用",func:function(){
				webos.icons.open(this) ;
			}
		}],
		[
		{
			text:"添加到桌面",func:function(){
				webos.utils.addDeskShortcut(this,this) ;
			}
		}]
	],
	headLinks:[
		{text:"应用商店",styleClass:"set_store",func:function(){
			webosService.window.addApp() ;
		}},
		{text:"设为首页",styleClass:"set_homepage",func:function(){
			webos.utils.setHome(this,window.location)
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
			height:340
		  })
	},
	addApp:function(){
		webos.window.open({
			  title:"添加应用",
			  iframe:false,
			  width:700,
			  height:500,
			  x:webos.utils.width()/2-350,
			  y:webos.utils.height()/2-260,
			  url:webosService.url.app_index
		   });
	},
	addShortcut:function(){
		webos.window.open({
			  title:"添加快捷方式",
			  iframe:false,
			  width:700,
			  height:500,
			  x:webos.utils.width()/2-350,
			  y:webos.utils.height()/2-260,
			  url:webosService.url.front_index
		   });
	},addWidget:function(){
		webos.window.open({
		  title:"添加小挂件",
		  iframe:false,
		  width:700,
		  height:500,
		  x:webos.utils.width()/2-350,
		  y:webos.utils.height()/2-260,
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
	}
}

var _deskAppWidgets = {
	"desk_A":{
		"name":"我的办公台",
		"apps":[
			{
				"id": "icon15",
				"name": "品高门户",
				"description": "品高内部门户系统",
				"category": "办公",
				"href": "https://portal.bingosoft.net/",
				"desktop":"1",
				"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
				"icon":"",
				"iconLarge":"icon15",
				"iconMiddle":"icon15",
				"iconSmall":"icon15",				
				"size": "max",
				"width": "",
				"height": "",
				"x" : "",									
				"y" : "",									
				"publisher":"管理员",
				"pubdate":"2011-02-14"
			},{
				"id": "icon11",
				"name": "员工工作区",
				"description": "员工工作区",
				"category": "办公",
				"href": "https://portal.bingosoft.net/sites/hr1/DocLib9/Forms/AllItems.aspx",				
				"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
				"icon":"",
				"iconLarge":"icon11",
				"iconMiddle":"icon11",
				"iconSmall":"icon11",				
				"size": "middle",
				"width": "",
				"height": "",
				"x" : "",									
				"y" : "",									
				"publisher":"管理员",
				"pubdate":"2011-02-14"
			}
		],
		"widgets":[
			{
				"id": "widget01",
				"name": "桌面时钟",
				"href": "widgets/widget_clock.html",				
				"icon":"",
				"iconLarge":"icon2",
				"iconMiddle":"icon2",
				"iconSmall":"icon2",				
				"size": "fix",
				"width": "200",
				"height": "60",
				"x" : "220px",
				"y" : "120px"
			},
			{
				"id": "widget02",
				"name": "搜索",
				"href": "widgets/widget_search.html",
				"icon":"",
				"iconLarge":"icon2",
				"iconMiddle":"icon2",
				"iconSmall":"icon2",				
				"size": "fix",
				"width": "200",
				"height": "60",
				"x" : "220px",
				"y" : "60px"
			}
		]
	},
	"desk_B":{
		"name":"我的阅览室",
		"apps":[
			{
				"id": "icon15",
				"name": "培训与活动",
				"description": "培训与活动",
				"category": "办公",
				"href": "https://bsdn.bingosoft.net/Pages/training.aspx",
				"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
				"icon":"",
				"iconLarge":"icon15",
				"iconMiddle":"icon15",
				"iconSmall":"icon15",				
				"size": "max",
				"width": "",
				"height": "",
				"x" : "",									
				"y" : "",									
				"publisher":"管理员",
				"pubdate":"2011-02-14"
			},
			{
				"id": "icon51",
				"name": "百度",
				"description": "百度",
				"category": "查询",
				"href": "http://www.baidu.com",
				"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
				"icon":"",
				"iconLarge":"images/icons/ie/big.png",
				"iconMiddle":"images/icons/ie/middle.png",
				"iconSmall":"images/icons/ie/small.png",				
				"size": "max",
				"width": "",
				"height": "",
				"x" : "",									
				"y" : "",									
				"publisher":"管理员",
				"pubdate":"2011-02-14"
			}
		],
		"widgets":[]
	},
	"desk_C":{
		"name":"我的休闲室",
		"apps":[
			{
				"id": "icon20",
				"name": "部门笔记本",
				"description": "部门笔记本",
				"category": "办公",
				"href": "https://portal.bingosoft.net/sites/hr1/DocLib9/Forms/AllItems.aspx",
				"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
				"icon":"",
				"iconLarge":"icon20",
				"iconMiddle":"icon20",
				"iconSmall":"icon20",
				"size": "max",
				"width": "",
				"height": "",
				"x" : "",									
				"y" : "",									
				"publisher":"管理员",
				"pubdate":"2011-02-14"
			}
		],
		"widgets":[
			{
				"id": "widget03",
				"name": "新闻公告",
				"href": "widgets/widget_tab.html",
				"icon":"icon22",
				"iconLarge":"images/icons/ie/big.png",
				"iconMiddle":"images/icons/ie/middle.png",
				"iconSmall":"images/icons/ie/small.png",
				"size": "",
				"width": "280",
				"height": "212",
				"x" : "301px",
				"y" : "200px"
			}
		]
	},
	"desk_D":{
		"name":"我的新增",
		"apps":[
			{
				"id": "icon10",
				"name": "工作量填报",
				"description": "工作量填报",
				"category": "办公",
				"href": "https://eim.bingosoft.net",
				"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",				
				"icon":"",
				"iconLarge":"icon10",
				"iconMiddle":"icon10",
				"iconSmall":"icon10",				
				"size": "max",
				"width": "",
				"height": "",
				"x" : "",									
				"y" : "",									
				"publisher":"管理员",
				"pubdate":"2011-02-14"
			}
		],
		"widgets":[]
	},
	"desk_E":{
		"name":"我的新增1",
		"apps":[
			{
				"id": "icon11",
				"name": "工作量填报",
				"description": "工作量填报",
				"category": "办公",
				"href": "https://eim.bingosoft.net",
				"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",				
				"icon":"",
				"iconLarge":"icon10",
				"iconMiddle":"icon10",
				"iconSmall":"icon10",				
				"size": "max",
				"width": "",
				"height": "",
				"x" : "",									
				"y" : "",									
				"publisher":"管理员",
				"pubdate":"2011-02-14"
			}
		],
		"widgets":[]
	}
} ;


var _marketApps =  [
		{
			"id":"bg",
			"name": "办公",
			"icon":"",
			"iconLarge":"images/icons/ie/big.png",
			"iconMiddle":"images/icons/ie/middle.png",
			"iconSmall":"images/icons/ie/small.png",
			"apps": [
				{
					"id": "icon15",
					"name": "品高门户",
					"description": "品高内部门户系统",
					"category": "bg",
					"href": "https://portal.bingosoft.net/",
					"desktop":"1",
					"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",					
					"icon":"",
					"iconLarge":"icon15",
					"iconMiddle":"icon15",
					"iconSmall":"icon15",					
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				},
				{
					"id": "icon11",
					"name": "员工工作区",
					"description": "员工工作区",
					"category": "bg",
					"href": "https://portal.bingosoft.net/sites/hr1/DocLib9/Forms/AllItems.aspx",
					"desktop":"1",
					"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
					"icon":"",
					"iconLarge":"icon11",
					"iconMiddle":"icon11",
					"iconSmall":"icon11",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				},
				{
					"id": "icon15",
					"name": "培训与活动",
					"description": "培训与活动",
					"category": "bg",
					"href": "https://bsdn.bingosoft.net/Pages/training.aspx",
					"desktop":"1",
					"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
					"icon":"",
					"iconLarge":"icon15",
					"iconMiddle":"icon15",
					"iconSmall":"icon15",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				},
				{
					"id": "icon20",
					"name": "部门笔记本",
					"description": "部门笔记本",
					"category": "bg",
					"href": "https://portal.bingosoft.net/sites/hr1/DocLib9/Forms/AllItems.aspx",
					"desktop":"1",
					"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
					"icon":"",
					"iconLarge":"icon20",
					"iconMiddle":"icon20",
					"iconSmall":"icon20",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				},
				{
					"id": "icon10",
					"name": "工作量填报",
					"description": "工作量填报",
					"category": "bg",
					"href": "https://eim.bingosoft.net",
					"desktop":"1",
					"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
					"icon":"",
					"iconLarge":"icon10",
					"iconMiddle":"icon10",
					"iconSmall":"icon10",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				},
				{
					"id": "icon10",
					"name": "自定义",
					"description": "自定义",
					"category": "bg",
					"href": "http://www.baidu.com/",
					"desktop":"0",
					"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
					"icon":"",
					"iconLarge":"images/icons/ie/big.png",
					"iconMiddle":"images/icons/ie/middle.png",
					"iconSmall":"images/icons/ie/small.png",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				}
			],
			widgets:[ 
				{ 
					"id": "widget2", 
					"name": "桌面时钟", 
					"category": "bg",
					"href": "widgets/widget_clock.html", 
					"desktop": "1", 					
					"icon":"",
					"iconLarge":"icon2",
					"iconMiddle":"icon2",
					"iconSmall":"icon2",			
					"size": "", 
					"width": "200", 
					"height": "60", 
					"x" : "220px", 
					"y" : "120px" 
				}, { 
					"id": "widget3", 
					"name": "天气预报", 
					"category": "bg",
					"href": "widgets/widget_weather.html", 
					"desktop": "0", 
					"icon":"",
					"iconLarge":"icon21",
					"iconMiddle":"icon21",
					"iconSmall":"icon21",
					"size": "", 
					"width": "203", 
					"height": "240", 
					"x" : "", 
					"y" : "" 
				}, { 
					"id": "widget4", 
					"name": "新闻公告", 
					"category": "bg",
					"href": "widgets/widget_tab.html", 
					"desktop": "1", 					
					"icon":"",
					"iconLarge":"images/icons/ie/big.png",
					"iconMiddle":"images/icons/ie/middle.png",
					"iconSmall":"images/icons/ie/small.png",
					"size": "", 
					"width": "280", 
					"height": "200", 
					"x" : "300px", 
					"y" : "200px" 
				} 
			]
		},
		{
			"id":"zl",
			"name": "资料",
			"icon":"",
			"iconLarge":"cat_7",
			"iconMiddle":"cat_7",
			"iconSmall":"cat_7",
			"apps": [
				{
					"id": "icon8",
					"name": "品高爱问",
					"description": "品高内部门户系统",
					"category": "zl",
					"href": "https://bsdn.bingosoft.net/expert/Pages/Iwen.aspx",
					"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
					"desktop": "0",
					"icon":"",
					"iconLarge":"icon8",
					"iconMiddle":"icon8",
					"iconSmall":"icon8",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				},
				{
					"id": "icon9",
					"name": "品高知识库",
					"description": "品高知识库",
					"category": "zl",
					"href": "https://bsdn.bingosoft.net/expert/Pages/Iwen.aspx",
					"thumbnail":"",
					"desktop": "0",
					"icon":"",
					"iconLarge":"icon9",
					"iconMiddle":"icon9",
					"iconSmall":"icon9",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				},
				{
					"id": "icon3",
					"name": "常用开发库",
					"description": "常用开发库",
					"category": "zl",
					"href": "https://bsdn.bingosoft.net/expert/Pages/Iwen.aspx",
					"images":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
					"desktop": "0",
					"icon":"",
					"iconLarge":"icon3",
					"iconMiddle":"icon3",
					"iconSmall":"icon3",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				},
				{
					"id": "icon4",
					"name": "多媒体素材库",
					"description": "多媒体素材库",
					"category": "zl",
					"href": "http://developer.bingosoft.net/",
					"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
					"desktop": "0",
					"icon":"",
					"iconLarge":"icon4",
					"iconMiddle":"icon4",
					"iconSmall":"icon4",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				}
			],
			widgets:[ 
				{ 
					"id": "widget6", 
					"name": "桌面时钟", 
					"category": "zl",
					"href": "widgets/widget_clock.html", 
					"desktop": "1", 					
					"icon":"",
					"iconLarge":"icon2",
					"iconMiddle":"icon2",
					"iconSmall":"icon2",			
					"size": "", 
					"width": "200", 
					"height": "60", 
					"x" : "220px", 
					"y" : "120px" 
				}, { 
					"id": "widget7", 
					"name": "天气预报", 
					"category": "zl",
					"href": "widgets/widget_weather.html", 
					"desktop": "0", 
					"icon":"",
					"iconLarge":"icon21",
					"iconMiddle":"icon21",
					"iconSmall":"icon21",	
					"size": "", 
					"width": "203", 
					"height": "240", 
					"x" : "", 
					"y" : "" 
				}
			]
		},
		{
			"id":"cx",
			"name": "查询",
			"icon":"",
			"iconLarge":"cat_5",
			"iconMiddle":"cat_5",
			"iconSmall":"cat_5",
			"apps": [
				{
					"id": "icon51",
					"name": "百度",
					"description": "百度",
					"category": "cx",
					"href": "http://www.baidu.com",
					"thumbnail":"http://9.web.qstatic.com/webqqpic/pubapps/5/5849/images/thumb2.png",
					"desktop": "1",
					"icon":"",
					"iconLarge":"images/icons/ie/big.png",
					"iconMiddle":"images/icons/ie/middle.png",
					"iconSmall":"images/icons/ie/small.png",
					"size": "max",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				},
				{
					"id": "icon9",
					"name": "谷歌",
					"description": "谷歌",
					"category": "cx",
					"href": "http://baidu.com",
					"thumbnail":"",
					"desktop": "0",
					"icon":"",
					"iconLarge":"images/icons/ie/big.png",
					"iconMiddle":"images/icons/ie/middle.png",
					"iconSmall":"images/icons/ie/small.png",
					"size": "middle",
					"width": "",
					"height": "",
					"x" : "",									
					"y" : "",									
					"publisher":"管理员",
					"pubdate":"2011-02-14"
				}
			],
			widgets:[ 
				{ 
					"id": "widget11", 
					"name": "新闻公告", 
					"category": "cx",
					"href": "widgets/widget_tab.html", 
					"desktop": "1", 					
					"icon":"",
					"iconLarge":"images/icons/ie/big.png",
					"iconMiddle":"images/icons/ie/middle.png",
					"iconSmall":"images/icons/ie/small.png",
					"size": "", 
					"width": "280", 
					"height": "200", 
					"x" : "300px", 
					"y" : "200px" 
				} 
			]
		}
	] ;