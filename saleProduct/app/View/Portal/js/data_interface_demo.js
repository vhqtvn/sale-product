///////////////////////
Config =window.Config||{} ;
Config.contextPath = "/"+fileContextPath ;

String.prototype.startWith=function(str){     
      var reg=new RegExp("^"+str);     
      return reg.test(this);        
}  

String.prototype.endWith=function(str){     
      var reg=new RegExp(str+"$");     
      return reg.test(this);        
} 

String.prototype.getQueryString = function(name){ //name 是URL的参数名字 
	var reg = new RegExp("(^|&|\\?)"+ name +"=([^&]*)(&|$)"), r; 
	if (r=this.match(reg)) return (unescape(r[2])||"").split("#")[0]; return null; 
}; 

webosService.quit = function(){
	window.location.href = contextPath+"/users/logout" ;
}


jQuery.utils = {
	//解析URL
	parseUrl : function(url,params){
		url = jQuery.trim(url) ;
		if( url.startWith("~") ){
			url = url.substring(1) ;
			url = Config.contextPath+url ;
		}
		
		params = $.extend({},params,{host:getHost(),port:getPort()}) ;
		for(var o in params){
			url = url.replace("{"+o+"}",params[o]) ;
		}
		
		
		return url ;
		
		function getHost(){
			var host = window.location.host ;
			return host.split(":")[0] ;
		}
		
		function getPort(){
			return window.location.port||"80" ;
		}
	}
}
/////////////////////////////////////////////
Portal = window.Portal||{} ;
Portal.services = Portal.services||{} ;

Portal.services = {
	saveDesk:function(deskConfig,appFunction,portalCode){
		deskConfig = $.parseJSON(deskConfig) ;
		for( var deskId in deskConfig ){
			//alert(deskId) ;
			var deskName = $("[deskId='"+deskId+"']").find(".showTip").text() ;
			//alert(deskName);
			deskConfig[deskId].name = deskName ;
		}
		
		$.dataservice("model:Portal.saveDeskConfig",{deskConfig: JSON.stringify(deskConfig) },function(){
			//保存成功
		}) ;
		//return result ;
	},loadDeskItems:function(portalCode){
		var desks = null ;
		$.dataservice("model:Portal.loadDeskConfig",{},function(result){
			if(result)eval("desks = " + result ) ;
		},{async:false}) ;
		
		desks = desks||_defaultDeskConfig ;
		
		for(var deskId in desks ){
			var desk = desks[deskId] ;
			var apps = desk.apps||[] ;
			var widgets = desk.widgets||[] ;
			$(apps).each(function(index,app){
				var id = app.id ;
				//alert(id);
				var appDetail = treeMap['id_'+id] ;
				for(var o in appDetail){
					app[o] = appDetail[o] ;
				}
				app.href = app.href || app.url ;
			}) ;
		}
		
		desks.deskActions = _deskActions ;
		
		return desks ;
	},getConsoles:function(portalCode){
		return [];
	},loadMarketApps:function(portalCode){
		return [
		] ;
	},loadMarketWidgets:function(url , appId , appCode ,portalCode){
		var widgets = null ;
		$(_marketApps).each(function(index , app){
			if( app.id == appId ){
				widgets = app.widgets ;
			}
		}) ;
		return widgets ;
	},loadDeskStart:function(portalCode){
		console.log(indexdata) ;
		//return [ indexdata ];
		
		return [[
		],indexdata] ;//前为菜单信息，后卫应用功能信息
	},loadDeskSysThemes:function(portalCode){
		return _deskThemes;
	},loadDeskTheme:function(portalCode){
		return {
				"themeId" : "t7",
				"themeName" : "null",
				"themeUrl" :"/"+fileContextPath+ "/app/View/Portal/images/theme/7_b.jpg"
			};
	},saveDeskTheme:function(theme){
		alert("TODO: saveDeskTheme") ;
		return result ;
	},saveDeskPanel:function(panels){
		var params = {} ;
		params.panels = panels  ;
		params.portalCode = Config.portalCode ;
		
		alert("TODO: saveDeskPanel") ;
		
		//var result = Portal.rest.json(Config.contextPath+Portal.restConfig.saveDeskPanel, params ) ;
		//return result ;
	},deleteDeskPanel:function(panel){
		alert("TODO: deleteDeskPanel") ;
		//panel.portalCode = Config.portalCode ;
		//var result = Portal.rest.json(Config.contextPath+Portal.restConfig.deleteDeskPanel, panel ) ;
		//return result ;
	},addApp:function(app){
		alert("TODO: addApp") ;
		//app.portalCode = app.portalId = Config.portalCode ;
		//var result = Portal.rest.json(Config.contextPath+Portal.restConfig.addApp, app ) ;
		//return result ;
	},uninstallApp:function(app){
		alert("TODO: uninstallApp") ;
		//app.portalCode = app.portalId = Config.portalCode ;
		//var result = Portal.rest.json(Config.contextPath+Portal.restConfig.uninstallApp, app ) ;
		//return result ;
	},httpproxy:function( url , params ){
		if(url.startWith("list-app-category")){//获取应用分类
			return _marketApps ;
		}
		
		if( url.startWith("list-app?") ){//远程加载对应分类的应用
			return   [
					{id:"baidu",name:"百度",url:"http://www.baidu.com",description:"1111",thumbnail:"",configUrl:""},
					{id:"google",name:"谷歌",url:"http://www.google.com",description:"2222",thumbnail:"",configUrl:""},
					{id:"sina",name:"新浪",url:"http://www.sina.com",description:"3333",thumbnail:"",configUrl:""}
				] ;
		}
	}
}



///////////////////////////////data/////////////////////////////////////////////////////

var _defaultDeskConfig = {
	"desk_A":{
		"name":"我的工作台",
		"apps":[
		],
		"widgets":[
		]
	},
	"desk_B":{
		"name":"桌面二",
		"apps":[
		],
		"widgets":[]
	},
	"desk_C":{
		"name":"桌面3",
		"apps":[
		],
		"widgets":[
		]
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
	
var _deskActions = [{
				"seqId" : "bcm",
				"name" : "桌面右键菜单",
				"type" : "bodyContextMenus",
				"parentId" : null,
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "bcm_1",
				"name" : "显示桌面",
				"type" : "bodyContextMenus",
				"parentId" : "bcm",
				"portalId" : "default",
				"func" : "webos.task.showDesk()",
				"url" : null,
				"displayGroup" : "g1"
			}, {
				"seqId" : "bcm_3",
				"name" : "关闭所有窗口",
				"type" : "bodyContextMenus",
				"parentId" : "bcm",
				"portalId" : "default",
				"func" : "webos.window.close_all() ;",
				"url" : null,
				"displayGroup" : "g1"
			}, /*{
				"seqId" : "bcm_4",
				"name" : "添加",
				"type" : "bodyContextMenus",
				"parentId" : "bcm",
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : "g2"
			}, {
				"seqId" : "bcm_8",
				"name" : "切换主题",
				"type" : "bodyContextMenus",
				"parentId" : "bcm",
				"portalId" : "default",
				"func" : "webosService.window.theme() ;",
				"url" : null,
				"displayGroup" : "g2"
			}, {
				"seqId" : "bcm_9",
				"name" : "控制面板",
				"type" : "bodyContextMenus",
				"parentId" : "bcm",
				"portalId" : "default",
				"func" : "webosService.window.console() ;",
				"url" : null,
				"displayGroup" : "g2"
			},*/{
				"seqId" : "bcm_11",
				"name" : "注销用户",
				"type" : "bodyContextMenus",
				"parentId" : "bcm",
				"portalId" : "default",
				"func" : "webosService.quit() ;",
				"url" : null,
				"displayGroup" : "g3"
			},/* {
				"seqId" : "bcm_6",
				"name" : "添加快捷方式",
				"type" : "bodyContextMenus",
				"parentId" : "bcm_4",
				"portalId" : "default",
				"func" : "webosService.window.addShortcut() ;",
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "bcm_7",
				"name" : "添加小挂件",
				"type" : "bodyContextMenus",
				"parentId" : "bcm_4",
				"portalId" : "default",
				"func" : "webosService.window.addWidget() ;",
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "control_2",
				"name" : "切换主题",
				"type" : "controlPanel",
				"parentId" : "control",
				"portalId" : "default",
				"func" : "webosService.window.theme() ;",
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "control",
				"name" : "控制面板",
				"type" : "controlPanel",
				"parentId" : null,
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "control_3",
				"name" : "桌面管理",
				"type" : "controlPanel",
				"parentId" : "control",
				"portalId" : "default",
				"func" : "webosService.window.desk();",
				"url" : null,
				"displayGroup" : null
			},*/ {
				"seqId" : "hl_1",
				"name" : "传统首页",
				"type" : "headLinks",
				"parentId" : "hl",
				"portalId" : "default",
				"code" : null,
				"styleClass" : "set_collection",
				"func" : "webos.utils.addFavorite(window.location,document.title)",
				"url" : null,
				"displayGroup" : null
			},{
				"seqId" : "hl_3",
				"name" : "添加收藏",
				"type" : "headLinks",
				"parentId" : "hl",
				"portalId" : "default",
				"code" : null,
				"styleClass" : "set_collection",
				"func" : "webos.utils.addFavorite(window.location,document.title)",
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "hl_5",
				"name" : "V1.0",
				"type" : "headLinks",
				"parentId" : "hl",
				"portalId" : "default",
				"code" : null,
				"styleClass" : "set_version",
				"func" : null,
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "hl",
				"name" : "TOP快捷方式",
				"type" : "headLinks",
				"parentId" : null,
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "icm_1",
				"name" : "打开应用",
				"type" : "iconContextMenus",
				"parentId" : "icm",
				"portalId" : "default",
				"func" : "webos.icons.open(this) ;",
				"url" : null,
				"displayGroup" : "g1"
			}, {
				"seqId" : "icm_2",
				"name" : "卸载应用",
				"type" : "iconContextMenus",
				"parentId" : "icm",
				"portalId" : "default",
				"func" : "webos.icons.uninstall(this) ;",
				"url" : null,
				"displayGroup" : "g2"
			}, {
				"seqId" : "icm",
				"name" : "快捷方式右键菜单",
				"type" : "iconContextMenus",
				"parentId" : null,
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "scm_1",
				"name" : "打开应用",
				"type" : "startContextMenus",
				"parentId" : "scm",
				"portalId" : "default",
				"func" : "webos.icons.open(this) ;",
				"url" : null,
				"displayGroup" : "g1"
			}, {
				"seqId" : "scm_2",
				"name" : "添加到桌面",
				"type" : "startContextMenus",
				"parentId" : "scm",
				"portalId" : "default",
				"func" : "webos.utils.addDeskShortcut(this,this) ;",
				"url" : null,
				"displayGroup" : "g2"
			}, {
				"seqId" : "scm",
				"name" : "开始菜单右键菜单",
				"type" : "startContextMenus",
				"parentId" : null,
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : null
			},/* {
				"seqId" : "sm_1",
				"name" : "控制面板",
				"type" : "startMenus",
				"parentId" : "sm",
				"portalId" : "default",
				"code" : "list_setting",
				"styleClass" : null,
				"func" : "webosService.window.console() ;",
				"url" : null,
				"displayGroup" : null
			},*/ {
				"seqId" : "sm_2",
				"name" : "应用程序",
				"type" : "startMenus",
				"parentId" : "sm",
				"portalId" : "default",
				"code" : "list_app",
				"styleClass" : null,
				"func" : null,
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "sm_3",
				"name" : "注销",
				"type" : "startMenus",
				"parentId" : "sm",
				"portalId" : "default",
				"code" : "list_logout",
				"styleClass" : null,
				"func" : "webosService.quit() ;",
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "sm",
				"name" : "开始菜单",
				"type" : "startMenus",
				"parentId" : null,
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "ta_2",
				"name" : "点击显示桌面",
				"type" : "taskActions",
				"parentId" : "ta",
				"portalId" : "default",
				"code" : "desk",
				"styleClass" : null,
				"func" : "webos.task.showDesk() ;",
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "ta_3",
				"name" : "关闭所有窗口",
				"type" : "taskActions",
				"parentId" : "ta",
				"portalId" : "default",
				"code" : "allclo",
				"styleClass" : null,
				"func" : "webos.window.close_all() ;",
				"url" : null,
				"displayGroup" : null
			}/*, {
				"seqId" : "ta_5",
				"name" : "切换主题",
				"type" : "taskActions",
				"parentId" : "ta",
				"portalId" : "default",
				"code" : "skin",
				"styleClass" : null,
				"func" : "webosService.window.theme() ;",
				"url" : null,
				"displayGroup" : null
			}*/, {
				"seqId" : "ta_6",
				"name" : "退出系统",
				"type" : "taskActions",
				"parentId" : "ta",
				"portalId" : "default",
				"code" : "quit",
				"styleClass" : null,
				"func" : "webosService.quit() ;",
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "ta",
				"name" : "任务栏",
				"type" : "taskActions",
				"parentId" : null,
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : null
			}, {
				"seqId" : "tcm_1",
				"name" : "隐藏菜单",
				"type" : "taskContextMenus",
				"parentId" : "tcm",
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : "g1"
			}, {
				"seqId" : "tcm",
				"name" : "任务栏右键菜单",
				"type" : "taskContextMenus",
				"parentId" : null,
				"portalId" : "default",
				"func" : null,
				"url" : null,
				"displayGroup" : null
			}]  ;
			
var _deskThemes = [{
			"seqId" : "t1",
			"name" : null,
			"url" : "/"+fileContextPath+"/app/View/Portal/images/theme/1_b.jpg",
			"thumbnail" : "/"+fileContextPath+"/app/View/Portal/images/theme/1_s.jpg"
		}, {
			"seqId" : "t2",
			"name" : null,
			"url" : "/"+fileContextPath+"/app/View/Portal/images/theme/2_b.jpg",
			"thumbnail" : "/"+fileContextPath+"/app/View/Portal/images/theme/2_s.jpg"
		}, {
			"seqId" : "t3",
			"name" : null,
			"url" : "/"+fileContextPath+"/app/View/Portal/images/theme/3_b.jpg",
			"thumbnail" : "/"+fileContextPath+"/app/View/Portal/images/theme/3_s.jpg"
		}, {
			"seqId" : "t4",
			"name" : null,
			"url" : "/"+fileContextPath+"/app/View/Portal/images/theme/4_b.jpg",
			"thumbnail" : "/"+fileContextPath+"/app/View/Portal/images/theme/4_s.jpg"
		}, {
			"seqId" : "t5",
			"name" : null,
			"url" : "/"+fileContextPath+"/app/View/Portal/images/theme/5_b.jpg",
			"thumbnail" : "/"+fileContextPath+"/app/View/Portal/images/theme/5_s.jpg"
		}, {
			"seqId" : "t6",
			"name" : null,
			"url" : "/"+fileContextPath+"/app/View/Portal/images/theme/6_b.jpg",
			"thumbnail" : "/"+fileContextPath+"/app/View/Portal/images/theme/6_s.jpg"
		}, {
			"seqId" : "t7",
			"name" : null,
			"url" : "/"+fileContextPath+"/app/View/Portal/images/theme/7_b.jpg",
			"thumbnail" : "/"+fileContextPath+"/app/View/Portal/images/theme/7_s.jpg"
		}, {
			"seqId" : "t8",
			"name" : null,
			"url" : "/"+fileContextPath+"/app/View/Portal/images/theme/8_b.jpg",
			"thumbnail" : "/"+fileContextPath+"/app/View/Portal/images/theme/8_s.jpg"
		}] ;