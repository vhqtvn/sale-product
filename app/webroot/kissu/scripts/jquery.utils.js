function getContextPath() {
    var pathName = document.location.pathname;
    var index = pathName.substr(1).indexOf("/");
    var result = pathName.substr(0,index+1);
    return result;
}

if( typeof Config == 'undefined' ){
	Config = {contextPath:getContextPath()} ;
}

if( typeof Global == 'undefined' ){
	Global = Config ;
}


/*******************************************
 @description 打开窗口
 @example
    打开窗口            ： jQuery.open( url , width , height , params ,callback ) ;
    在打开窗口中获取参数 ： var args        = jQuery.dialogAraguments() ;
    获取打开窗口中返回值 ： var returnValue = jQuery.dialogReturnValue() ;
 ******************************************/
if( !jQuery.fn.dialogClose ){
	jQuery.fn.dialogClose = function(){
		window.close() ;
	}
}

jQuery.open = function(url,width,height,params,callback,fixParams){
	jQuery.dialogReturnValue("__init__");
	params = params||{} ;
	fixParams = fixParams||{} ;
	if( $.dialog  && (params.showType == 'dialog' || !params.showType ) ){
		var opts = {
			width:width,
			height:height,
			title:params.title||params.Title||fixParams.title||'',
			url:url,
			data:params,
			onload:function(){
				var me = this ;
				if(params.iframe===false || params.iframe === "false"){
					setTimeout(function(){
						//控件初始化
						me.frwDom.uiwidget() ;
						//浏览器兼容
						me.frwDom.browserFix() ;
					},5 ) ;
				}
			},close:function(){
				callback && callback.call(this) ;
			}
		}
		
		opts = $.extend({},opts,params,fixParams) ;
		var _dialog = jQuery.dialog(opts) ;
		
	}else if(!$.browser.msie || params.showType == 'open'){
		
		var win = openCenterWindow(url, width, height);
		
		var _callbak = function(){
			if( $.unblock ){$.unblock() ; }
			callback(window);
		}
		try{
			if( jQuery.browser.msie ){
				win.attachEvent("onunload", _callbak );
			}else{
				win.onbeforeunload = _callbak ;
			}
		}catch(e){
			
		}
		return win ;
	} else if( $.browser.msie ){
		_returnValue = showCenterModalDialog(url , width ,height ,params) ;
		jQuery.dialogReturnValue(_returnValue||"") ;
		callback() ;
	}
	
	function showCenterModalDialog(URL,dlgWidth,dlgHeight,arg){
	    var dlgLeft = (window.screen.width-dlgWidth)/2;
	    var dlgTop  = (window.screen.height-dlgHeight)/2;
	    var widthTmp = dlgWidth ;
	    var form    = "scroll:no;status:no;dialogHeight:" + dlgHeight + "px;dialogWidth:" + widthTmp + "px;dialogLeft:" + dlgLeft + ";dialogTop:" + dlgTop;
	    return window.showModalDialog(URL,arg,form);
	}

	function openCenterWindow(URL,wndWidth,wndHeight){
		var wndLeft = (window.screen.width-wndWidth)/2;
		var wndTop  = (window.screen.height-wndHeight)/2;
		var form    = "width=" + wndWidth + ",height=" + wndHeight + ",left=" + wndLeft + ",top=" + wndTop + ",resizable=yes";
		 return window.open(URL,'',form);        
	}
}

jQuery.dialogAraguments = function(){
	//showmodeldialog
	var args = window.dialogArguments||window.$_dialogArguments ;
	if( args ) return args ;
	var target =  window.opener || window.parent ;
	return target._dialogArguments||target.$_dialogArguments ;
}

jQuery.dialogReturnValue = function(returnValue){
	if(typeof returnValue != 'undefined'){
		if( returnValue == "__init__" ){
			window.returnValue = null ;
			return ;
		}
		//window.winReturnValue = returnValue ;
		window.returnValue = returnValue ;//showModelDialog
		if(window.opener){ //open
			window.opener.returnValue = returnValue ;
		}
		//dialog iframe
		$(document.body).dialogReturnValue(returnValue) ;
		//dialog iframe
		if( $(".ui-dialog:last")[0]){
			$(".ui-dialog:last").find("div:first").dialogReturnValue(returnValue) ;
		}
	}else{
		return window.returnValue ;
	}
}

/*******************************************
 @description 转化form表单元素为JSON对象（也可以为div）
 @author  lixh@bingosoft.net
 @example
    var json = $(formSelector).toJson() ;
 ******************************************/
jQuery.fn.toJson = function(beforeExtend,afterExtend,params) {
	var me = jQuery(this) ;
	beforeExtend = beforeExtend||{} ;
	afterExtend = afterExtend||{} ;
	params = params||{} ;
	var a = {};
	
	//text hidden password
	me.find("input[type=text],input[type=hidden],input[type=password]").each( function(){
		_add(this.name||this.id,this.value) ;
	} ) ;
	me.find("textarea").each( function(){
		_add(this.name||this.id,this.value) ;
	} ) ;
	
	//radio
	me.find("input[type=radio]").filter(":checked").each( function(){
		_add(this.name||this.id,this.value) ;
	} ) ;
	
	//checkbox
	var temp_cb = "" ;
	me.find("input[type=checkbox]").filter(":checked").each( function(){
		if (temp_cb.indexOf(this.name ) == -1) {
			temp_cb += (this.name) + ",";
		}
	} ) ;
	jQuery( temp_cb.split(",") ).each( function(){
		var tempValue = [] ;
		jQuery("input[name='" + this + "']:checked").each(function(i) {
			tempValue.push( this.value ) ;
		});
		_add(this ,tempValue.join(",")) ;
	} ) ;
	
	//select
	me.find('select').each( function(){
		var multi = $(this).attr('multiple')  ;
		
		var val = [] ;
		jQuery(this).find('option[selected]').each(function(){
			if(this.value)val.push( this.value ) ;
		});
		
		if(multi && params.mulSelectSplit ){
			_add(this.name||this.id,"'"+val.join("','")+"'") ;
		}else{
			_add(this.name||this.id,val.join(',')) ;
		}
	} ) ;
	
	return $.extend(beforeExtend , a , afterExtend) ;
	
	function _add(key,value){
		if(!key || !jQuery.trim(key)) return ;
		
		value = value||'' ;
		a[key] = value ;
	}
}

/**
 * 格式化查询form
 * 
 * params
 */
jQuery.fn.formatQueryForm = function(params){
	params = params||{} ;
	var exclude    = params.exclude||'%&<>/' ;
	var gridId = params.gridId||null ;
	var self = this ;
	$(this).find('input[type=text],textarea').each(function(){
		if($(this).excludeChar)
			$(this).excludeChar({exclude:exclude}) ;
		if(gridId)$(this).bind('keyup',function(event) {
			if(event.keyCode==13){
				 gridId['queryForm'](self) ;
			}     
		});
	}) ;
}

/********************************************
 **********添加文件指定窗口（JS、CSS）*********
 ********************************************/
jQuery.getTopWin = function() {
	var parentWin = window;
	while (parentWin != parentWin.parent) {
		try{
			if (parentWin.parent.document.getElementsByTagName("FRAMESET").length > 0)break;
			parentWin = parentWin.parent;
		}catch(e){
			return parentWin ;
		}
		
	}
	return parentWin;
}


jQuery.attachFile = function(win, filename, filetype) {
	if (win == window)
		return;
	if (!filename)
		return;
	var head = win.document.getElementsByTagName('head').item(0);
	var fileref = null;
	var fpath = null;
	if (filetype == "css") {
		if (_(win, 'link', filename))
			return;// 判断当前页面是否存在
		fpath = _(window, 'link', filename);// 获取路径
		if (!fpath)
			return;

		fileref = win.document.createElement("link");
		fileref.setAttribute("rel", "stylesheet");
		fileref.setAttribute("type", "text/css");
		fileref.setAttribute("href", fpath);
	} else if (filetype == "js") {
	
		if (_(win, 'script', filename))
			return;
		fpath = _(window, 'script', filename);// 获取路径
		if (!fpath)
			return;

		fileref = win.document.createElement('script');
		fileref.setAttribute("type", "text/javascript");
		fileref.setAttribute("language", "JavaScript");
		fileref.setAttribute("src", fpath);
	}
	
    head.appendChild(fileref);

	function _(win, tag, filename) {
		var scripts = win.document.getElementsByTagName(tag);
		for (var i = 0; i < scripts.length; i++) {
			var script = scripts[i];
			var _ = script.src || script.href;
			if (_ && _.indexOf("/" + filename) != -1) {
				return _ || '';
			}
		}
		return "";
	}
}

/********************************
 *************jQuery.utils*******
 *********************************/
jQuery.utils = {
	//解析URL
	parseUrl : function(url){
		url = jQuery.trim(url) ;
		if( url.startWith("~") ){
			url = url.substring(1) ;
			url = Config.contextPath+url ;
		}
		//url = url.replace("~",Config.contextPath) ;
		url = url.replace("{host}",getHost()) ;
		url = url.replace("{port}",getPort()) ;
		
		return url ;
		
		function getHost(){
			var host = window.location.host ;
			return host.split(":")[0] ;
		}
		
		function getPort(){
			return window.location.port ;
		}
	},
	scrollContent:function(header,content,footer){
		var header 	= content||".header" ;
		var footbtn = footbtn||".footbtn" ;
		var content = content||".content" ;
		var contentHeight =  $(document.body).height() -  $(header).outerHeight() -$(footbtn).outerHeight() - 5;
		$(content).height(contentHeight).css({'overflow-x':'hidden','overflow-y':'auto'}) ;
	}
};

jQuery.memory = {
	iframe:function(ifr,bool,b2){
	   ifr.src = "about:blank"; 
   	   
   	   var frames = ifr.contentWindow.document.getElementsByTagName("iframe");
   	   for(var i=0 ;i<frames.length ;i++){
   	   	  jQuery.memory.iframe(frames[i],true) ;
   	   }
   	   
	   ifr.contentWindow.document.write(""); 
	   ifr.contentWindow.document.clear(); 
	   
	   if(!b2){
	   		ifr.removeNode(true);  
	   		ifr = null ;
	   }
	   //if(!bool)CollectGarbage();
	}
}

jQuery.file = {
	/**
	 * 文件下载
	 *  eg: $.file.download("E:/bingo-celipse-jee-3.5.2_20100819.rar",realName) ;
	 * @param {} filepath
	 * @param {} filename
	 */
	download:function(filepath,filename){
		if(!filepath){
			alert("下载文件不能为空！");
			return ;
		}
		
		var url = jQuery.utils.parseUrl((window.serviceContext||"~")+'/component/upload.do?action=download') ;
		
		if( !jQuery('#download_ifr')[0] ){
			jQuery(document.body).append("<iframe id=download_ifr name=download_ifr style='display:none;margin:0px;padding:0px;' src=''></iframe>") ;
		}
		
		if( !jQuery('#download_form')[0] ){
			jQuery(document.body).append("<form method=post id=download_form action='"+url+"' style='display:hide;margin:0px;padding:0px;' target=download_ifr>" +
					"<input type=hidden name=filepath id=__filepath value='"+(filepath||'')+"'>" +
					"<input type=hidden name=filename id=__filename value='"+(filename||'')+"'>" +
					"</form>") ;
		}else{
			jQuery('#__filepath').val(filepath||'') ;
			jQuery('#__filename').val(filename||'') ;
		}
		
		jQuery('#download_form').submit() ;
	},
	downloadCallback:function(flag,fileName){
		if(flag == 1){
			var msg = "文件["+fileName+"]不存在或文件路径有误！" ;
			if( $.messageBox ){
		 	 	$.messageBox.error({message:msg}); 
		 	 }else{
		 	 	alert(msg );
		 	 }
		}
	}
}

/**
 * 统一获取数据入口
 * 参数格式：
 * 	1、 params
 *       type: 'post',
         url: 'demo-data.html' ,
         data: req.term ,
         async: true ,
         dataType:'json'
         
         返回数据格式
         returnCode:       --  int
         returnDesc:        -- string
         error:                  --  string
         returnValue:      --  json object
 * 
 */

jQuery.request = function(params){
	var _url     = null ;
	var _data    = null ;
	var _success = null ;
	var _error   = null ;
	 if(typeof params == 'string'){
	 	_url 	= params[0] ;
	 	_data 	= params[1] ;
	 	_success = params[2] ;
	 	_error   = params[3] ;
	 	params = {} ;
	 }
	 
	 if( jQuery.block && !params.noblock ) jQuery.block() ;
	 
 	 var dataType 	= params.dataType||'text' ;
 	 var async 		= typeof params.async == 'undefined' ? true : params.async ;
 	 var type 		= params.type||'post' ;
 	 var error 		= params.error||_error|| jQuery.request.defaultErrorHandler;
 	 var success 	= params.success||_success ;
 	 var url 		= params.url ||_url ;
 	 var data 		= params.data || _data ;
 	 
	 if(jQuery.utils) url = jQuery.utils.parseUrl(url) ;
 	 $.ajax({
        type: type,
        url: url ,
        data: data ,
        async: async ,
        dataType:dataType ,
        success: function(response){
        	if( jQuery.unblock && !params.noblock ) jQuery.unblock() ;
        	if(typeof(response) == 'string'){
        		try{
        			eval("response = "+ response ) ;
        		}catch(e){
        			success(response,params.custom||{}) ;
        			return ;
        		}
        	}

        	if( typeof response.returnCode != 'undefined' && response.returnCode != 200 ){
        		error(null , response.returnCode , response.error,url) ;
        	}else{
        		if( !response.returnValue ||  typeof  response.returnValue == 'string')
        			success(response.returnValue||response) ;
        		else
        			success(response.returnValue.Rows || response.returnValue || response) ;
        	}
        } ,
        error: function(xhr, textStatus, errorThrown){
        	if( jQuery.unblock && !params.noblock ) jQuery.unblock() ;
        	error(xhr, textStatus, errorThrown,url) ;
        }
     });
 }
 
jQuery.request.defaultErrorHandler = function(xhr, textStatus, errorThrown,url){
	 $.open(Global.contextPath+"/common/error/report500.jsp",570,410,errorThrown ,null , {title:"提示信息"} ) ;
	 return ;
 	 /*if( $.messageBox ){
 	 	$.messageBox.error({message:"ERROR:"+errorThrown}); 
 	 }else{
 	 	alert('请求出现异常: ' + errorThrown+"\n\r["+url+"]" );
 	 }*/
}


/**
 * 数据服务统一调用接口
 * @param {} commandName
 * @param {} params
 * @param {} callback   {success:function(){},error:function(){}} or function(){}//success
 */
jQuery.dataservice = function(commandName , params , callback , reqParams ){
	callback 			= callback||{} ;
	params  			= params||{} ;
	params.CommandName 	= commandName ;

	reqParams 			= reqParams||{} ;
	reqParams.data 		= params ;
	reqParams.type		= 'post' ;
	reqParams.noblock 	= reqParams.noblock === false?false:true ;
	reqParams.url 		= commandName?jQuery.utils.parseUrl(window.dataServiceUrl||"~/dataservice"):reqParams.url ;
	reqParams.dataType 	= commandName?'json':"text" ;
	
	//process callback
	if( callback.success ){
		reqParams.success = callback.success ;
	}
	
	if( callback.error ){
		reqParams.error = callback.error ;
	}
	
	if( jQuery.isFunction(callback) ){
		reqParams.success = callback ;
	}
	
	jQuery.request(reqParams) ;
}

/*
 * Title:  控件自动渲染
 * Description:
 * Aucthor: 陈锦城
 * Email: chenjc@bingosoft.net
 * Create Date:2010-07-19
 * Copyright 2010
 */ 
$(function(){
	//获取UI控件的标识
	var ui_widget_class = ".widget-class";
	$(ui_widget_class).each(
	   function(){
		  var _options = $(this).attr('opts')||$(this).attr("options") ;
		  
		  if(_options){
			 _options = eval("(" + _options + ")");
		  }else{
			  _options = {} ;
		  }
		  
		  if( typeof(widgetInitBefore)!='undefined' ){
		  	widgetInitBefore($(this).attr("widget")) ;
		  }
	   	  
	   	  var widgetInit = $(this).attr("widget") + "Init" ;
	   	  if( $[widgetInit] )$[widgetInit]($(this),_options);
	   	  
	   }
	);
	
	
});

/*********************
 * common 
 * */
String.prototype.startWith=function(str){     
      var reg=new RegExp("^"+str);     
      return reg.test(this);        
}  

String.prototype.endWith=function(str){     
      var reg=new RegExp(str+"$");     
      return reg.test(this);        
} 


/*fix*/
$.uiwidget = {
	mark:"data-widget",
	options:"data-options",
	validator:"validator",
	defaultValue:"defaultValue",
	map:{},
	dependMap:{},
	/**
	 * eg: $.widget.register("combotree",function(){})
	 */
	register:function(){//type ,depend , func
		var type = arguments[0] ;
		var func = null ;
		var depend = null ;
		if( arguments.length == 2 ){
			func = arguments[1] ;
		}else if( arguments.length == 3 ){
			func = arguments[2] ;
			depend = arguments[1] ;
		}
		
		$.uiwidget.map[type] = func ;
		$.uiwidget.dependMap[type] = depend ;
	},
	init:function(options,target){
		var widgetTrack = [] ;
		var pushed = {};
		//format dependMap
		for(var o in $.uiwidget.map){
			_addTypeTrack(o) ;
		}
		
		options = options||{} ;
		options.before && options.before(target) ;
		var cacheType = {} ;
		
		$(widgetTrack).each(function(index,type){
			if( $.uiwidget.map[type] ){
				var selector = $("["+$.uiwidget.mark+"^='"+type+"'],["+$.uiwidget.mark+"*=',"+type+"']",target)
				$.uiwidget.map[type]( selector,target)  ;
			}
		})

		options.after && options.after(target) ;
		
		function _addTypeTrack(o){
			var depend = $.uiwidget.dependMap[o] ;
			if( depend ){//存在依赖
				$(depend).each(function(index,type){
					_addTypeTrack(type) ;
				}) ;
			}
			(!pushed[o]) && widgetTrack.push(o) ;
			pushed[o] = true ;
		}
		
		pushed = null ;
		widgetTrack = null ;
		
	}
}

$.uiwidget.register("dialog",function(selector){
	selector.live("click",function(){
		var options = $(this).attr( $.uiwidget.options )||"{}";
		eval(" var jsonOptions = "+options) ;
		var url 	= jsonOptions.url||$(this).attr("href") ;
		var width 	= jsonOptions.width ;
		var height 	= jsonOptions.height ;
		
		var fixOPtions = {} ;
		if($(this)[0].tagName == "A"){
			fixOPtions.requestType = "GET" ;
		}
		fixOPtions.target = this ;

		var id     = $(this).attr("id")||$(this).attr("name");
		var callback = jsonOptions.callback||(window[id+"Callback"]||function(){}) ;
		
		$.open(url , width , height ,jsonOptions,callback,fixOPtions ) ;
		return false ;
	}) ;
}) ;

$.fn.uiwidget = function(){
	$.uiwidget.init({},this) ;
}

$(function(){
	//控件初始化
	$(document.body).uiwidget() ;
}) ;

/*fix*/
$.utils.scriptPath = function(scriptName){
	if( scriptName == "plugin"||scriptName == "plugins") return jQuery.utils.parseUrl("~/widgets/core/") ;
	if( scriptName == "upload") return jQuery.utils.parseUrl("~/widgets/") ;
	if( scriptName == 'jqueryui.css' ) return  jQuery.utils.parseUrl("~/themes/default/ui.css") ;
	var path = "" ;
	$("script,link").each(function(){
		if(path) return ;
		var src = this.src||this.href ;
		if(src &&  src.toLowerCase().indexOf(scriptName.toLowerCase())!=-1 ){
			path = src.substring(0, src.toLowerCase().indexOf(scriptName.toLowerCase()));
			var A = path.lastIndexOf("/");
			if (A > 0)
				path = path.substring(0, A + 1);
			return ;
		}
	}) ;
	return path ;
}