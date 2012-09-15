(function(){
	var zIndex = 900 ;
	
	$.fn.dialogResize = function(opts){
		var dialog = $(this).parents(".ui-dialog").data("dialog");
		
		if(!dialog){
			if( window.parent.$("iframe[name='"+window.name+"']").parents(".ui-dialog")[0]){
				var pos = {} ;
				try{
					pos =getWinPos(window);
				}catch(e){
					pos = {width:0,height:0} ;
				}
				pos.iframe = true ;
				window.parent.$("iframe[name='"+window.name+"']").dialogResize(pos);
			}
		}else{
			resizeDialog.call(dialog,opts);
		}
	};
	
	$.fn.dialogReturnValue = function(val){
		var dialog = $(this).parents(".ui-dialog").data("dialog");
		if(!dialog){
			if( window.parent.$("iframe[name='"+window.name+"']").parents(".ui-dialog")[0]){
				window.parent.$("iframe[name='"+window.name+"']").dialogReturnValue(val);
			}
		}else{
			_dialogReturnValue.call(dialog,val);
		}
	}
	
	$.fn.dialogClose = function(){
		var dialog = $(this).parents(".ui-dialog").data("dialog");
		if(!dialog){
			if( window.parent.$("iframe[name='"+window.name+"']").parents(".ui-dialog")[0]){
				window.parent.$("iframe[name='"+window.name+"']").dialogClose() ;
			}
		}else{
			close.call(dialog);
		}
	}
	
	
	$.dialog = function(options){
		var dialogWin = $topWindow() ;
		options.opener = window ;
		options.window = dialogWindow ;
		return new dialogWin.$._dialog( options ) ;
	}
	
	$._dialog = function(options){
		this.settings = $.extend({},$._dialog.defaults,options) ;
		this.settings.iframe   = typeof(this.settings.iframe) == 'undefined'?true:this.settings.iframe ;
		var me  = this ;
		
		if(typeof this.settings.model=="undefined" || this.settings.model){
			model.call(this,true);
		}
		
		var jQDomEl = renderFramework.call(this) ;
		
		if(this.settings.buttonBar){
			jQDomEl.find(".ui-dialog-buttonpane").hide() ;
		}

		attachEvent.call(this,jQDomEl) ;
		
		jQDomEl.show().center(false) ;
		this.frwDom = jQDomEl ;
		
		jQDomEl.find(".dialog_close,.btn-cancel").click(function(){
			close.call(me) ;
		}) ;
		renderContent.call(this) ;
		
		return this ;
	}
	
	
	$._dialog.defaults = {
		okButtonText:"确定",
		cancelButtonText:"取消",
		title:""
	};
	
	///////////private/////////////
	
	function _dialogReturnValue(val){
		this.returnValue = val ;
		this.settings.opener.returnValue = val ;
	}
	
	function renderFramework(){
		var html = '\
		    <div class="ui-dialog ui-widget ui-widget-content ui-corner-all">\
				  <div class=" drag_handler_target ui-dialog-titlebar ui-widget-header ui-corner-top ui-helper-clearfix">\
					 <span class="ui-dialog-title">' + this.settings.title + '</span>\
					 <a href="#" class="ui-dialog-titlebar-close ui-corner-all dialog_close">\
						<span class="ui-icon ui-icon-closethick">X</span>\
					 </a>\
				  </div>\
				  <div class="ui-dialog-content ui-widget-content dialog-content" style="height:100px;">\
			      </div>\
				  <div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix" style="display:none;">\
					<div class="ui-dialog-buttonset">\
	 					<button type="button"  class="btn btn-primary btn-ok">\
							'+this.settings.okButtonText+'\
						</button>\
						<button type="button" class="btn btn-cancel">\
							'+this.settings.cancelButtonText+'\
						</button>\
					</div>\
				</div>\
			</div>\
		';
		return $(html).appendTo(document.body).hide().css("z-index",zIndex).data("dialog",this) ;
	} ;
	
	function block(){
		if($.block)this.frwDom.block() ;
	}
	
	function unblock(){
		if($.block)this.frwDom.unblock() ;
	}
	
	function model(state){
		if(state){
			if( isIE6 ){
				this.modelFrm = $('<iframe src="about:blank" style="filter:alpha(opacity=0);" class="ui-dialog-mask" frameborder="0"></iframe>')
					.appendTo(document.body)
					.css({"z-index":zIndex,"height":$(document).height()+"px","position":"absolute","left":"0px","top":"0px","width":"100%"}) ;
				zIndex++ ;
			}
			this.modelEl = $('<div class="ui-dialog-mask ui-widget-overlay"></div>')
				.appendTo(document.body)
				.css({"z-index":zIndex,"height":$(document).height()+"px","position":"absolute","left":"0px","top":"0px","width":"100%"}) ;
			zIndex++ ;
		}else{
			if(this.modelFrm)this.modelFrm.hide(200).remove();
			if(this.modelEl)this.modelEl.hide(200).remove();
		}
	}
	
	function renderContent(){
		zIndex++ ;
		var me = this ;
		var url = this.settings.url;
		
		block.call(this) ;
		var contentDom = this.frwDom.find(".dialog-content").empty() ;
		var iframe = this.settings.iframe ;
		
		//set params
		this.settings.window.$dialogArguments = this.settings.data ;
		
		if( this.settings.content ){//文本信息
			unblock.call(this) ;
			contentDom.html(this.settings.content) ;
			resizeDialog.call(me) ;
			if( me.settings.onload ) me.settings.onload.call(me) ;
		}else if( this.settings.contentSelector ){//文本内容
			var targetDom = this.settings.opener.$(this.settings.contentSelector)[0].outerHTML ;
			unblock.call(this) ;
			contentDom.html(targetDom) ;
			resizeDialog.call(me) ;
			if( me.settings.onload ) me.settings.onload.call(me) ;
		}else if( iframe ){//iframe
			var ifr = $('<iframe width="100%" name="'+new Date().getTime()+'" scrolling="auto" frameborder="0" style="border:none;display:none;" src="' + url + '"></iframe>').appendTo(contentDom) ;
			$(ifr).bind("load",function(){
				$(ifr).show() ;
				unblock.call(me) ;
				var contentWindow = contentDom.find("iframe")[0].contentWindow ;
				
				me.ifrWindow = contentWindow ;
				
				//get Width
				if( me.settings.width ){
					resizeDialog.call(me,{width:me.settings.width,iframe:true}) ;
				}else{
					try{
						var width = getMaxWidth.call(me, contentWindow.$(contentWindow.document.body) ) ;
						resizeDialog.call(me,{width:width,iframe:true}) ;
					}catch(e){}
				}

				var pos = {} ;
				try{
					pos =getWinPos(contentWindow);//me.ifrWindow
				}catch(e){
					pos = {width:0,height:0} ;
				}
				pos.iframe = true ;
				resizeDialog.call(me,pos) ;
				if( me.settings.onload ) me.settings.onload.call(me) ;
			})
		}else if( !iframe  ){//url no iframe
			var split = url.indexOf("?")!=-1?"&":"?" ;
		 	var options = {
			  	url:url+split+new Date().getTime(),
			  	cache: false,
			  	success: function(html){
			  		setTimeout(function(){
							unblock.call(me) ;
			  				contentDom.html(html) ;
			  				resizeDialog.call(me) ;
			  				if( me.settings.onload ) me.settings.onload.call(me) ;
			  		},10) ;
				},
				_error:function(xhr, textStatus, errorThrown,url){
					me.close(false);
				}
			 } ;
			 
		 	 if(this.settings.requestType){
		 	 	options.type = this.settings.requestType ;
		 	 }
		 	 options.noblock = true ;
		 	 $.request(options);
		}
	}
	
	function getMaxWidth(container){
		var me = this ;
		var width = 0;
		container.find("table").each(function(){
			var _width = $(this).outerWidth() ;
			
			var parent = $(this).parent() ;

			while( parent[0] != container[0] ){
				_width = _width +( parent.outerWidth() - parent.width() ) ;
				parent = parent.parent() ;
			}
			width = Math.max(width,_width) ;
		});
		return width ;
	}
	
	function getWidth(container,opts){
		var _dom = container.find(".dialog-content")[0] ;
		var contentTableWidth = getMaxWidth.call(this, container.find(".dialog-content") ) ;
		var _width  =  Math.max(this.settings.width||0 , _dom.scrollWidth , _dom.offsetWidth , opts.width||0,contentTableWidth||0 );
		return Math.min(_width,$(window).width()-30) ;
	}
	
	function getHeight(container,opts){
		var _dom = container.find(".dialog-content")[0] ;
		return Math.max(this.settings.height||0 , _dom.scrollHeight , _dom.offsetHeight , opts.height||0) ;
	}
	

	function resizeDialog(opts){
		opts = opts||{} ;
		var container = this.frwDom ;
		
		var _height =  getHeight.call(this,container,opts) ;
		var _width  =  getWidth.call(this,container,opts) ;
		
		/*if(opts.iframe && $.browser.msie && !container.data("fixIEScrolling")){
			container.data("fixIEScrolling",true);
			_width = _width +8 ;
		}*/
		
		var titleBar = container.find(".ui-dialog-titlebar");
		var btnBar = container.find(".ui-dialog-buttonpane");
		
		var titleBarHeight = titleBar.is(":visible") ?titleBar.height():0 ;
		var btnPanelHeight = btnBar.is(":visible") ?btnBar.height():0 ;
		
		var width   = Math.min(_width,$(window).width()-30) ;
		var height  = Math.min(_height,$(window).height() - titleBarHeight - btnPanelHeight -50 );
		
		if(height<_height && !container.data("renderHeight")){
			container.data("renderHeight",true);
			var fixWidth = $.browser.msie?25:20 ;
			width = width +fixWidth ;
		}
		
		container.find(".dialog-content").css("overflow-y",height >= _height?"hidden":"auto") ;
		container.find(".dialog-content").css("overflow-x",width >= _width?"hidden":"auto") ;
		
		container.width( width ) ;
		container.find(".dialog-content").height( height ) ;
		
		if( opts.iframe ){
			container.find(".dialog-content>iframe").css("overflow-y",height >= _height?"hidden":"auto") ;
			container.find(".dialog-content>iframe").css("overflow-x",width >= _width?"hidden":"auto") ;
			container.find(".dialog-content>iframe").height( height ) ;
		}
		
		this.frwDom.center(false) ;
	}
	
	function attachEvent(jQDomEl){
		var draggable = typeof this.settings.draggable == "undefined" || this.settings.draggable ;
		var resizable = this.settings.resizable ;
		var me 		  = this ;
		
		if(draggable && jQDomEl.draggable ){
			jQDomEl.draggable({
				handle: jQDomEl.find(".drag_handler_target").css("cursor","move"),
				containment:"window",
				iframeFix: true
			}) ;
		}
		
		if(resizable && jQDomEl.resizable ){
			jQDomEl.resizable({
			  start:function(){
			  	jQDomEl[0].ondragstart = "return false;"  
	        	jQDomEl[0].onselectstart = "return false;"  
	        	jQDomEl[0].onselect = "document.selection.empty();"  
			  },stop:function(){
			  	jQDomEl[0].ondragstart = null ;  
	        	jQDomEl[0].onselectstart = null ;  
	        	jQDomEl[0].onselect = null ;  
			  }
			}) ;
		}
		
		this.settings.window.$(this.settings.window).bind("resize",function(){
			if( me.settings.iframe ){
				var pos = {} ;
				try{
					pos =getWinPos(me.ifrWindow);//me.ifrWindow
				}catch(e){
					pos = {width:0,height:0} ;
				}
				pos.iframe = true ;
				resizeDialog.call(me,pos) ;
			}else{
				resizeDialog.call(me ) ;
			}
			
			if(me.modelFrm)me.modelFrm.css({height:$(document).height()+"px"});
			if(me.modelEl)me.modelEl.css({height:$(document).height()+"px"});
		})
	}
	
	function close(){
		this.frwDom.hide() ;
		
		if(typeof this.settings.model=="undefined" || this.settings.model)
			model.call(this,false) ;
			
		if( this.settings.close ){
			this.settings.close.call(this,this.settings.opener) ;
		}
		this.frwDom.remove() ;
	}
	
	//////////utils///////////////
	jQuery.fn.center = function(f) {  
	    return this.each(function(){  
	        var p = f===false?document.body:this.parentNode;  
	        if ( p.nodeName.toLowerCase()!= "body" && jQuery.css(p,"position") == 'static' )  
	            p.style.position = 'relative';  
	        var s = this.style;  
	        s.position = 'absolute';  
	        if(p.nodeName.toLowerCase() == "body")  
	            var w=$(window);  
	        if(!f || f == "horizontal") {
	            //s.left = "0px";  
	           
	            if(p.nodeName.toLowerCase() == "body") {  
	                var clientLeft = w.scrollLeft() - 3 + (w.width() - parseInt(jQuery.css(this,"width")))/2;  
	                s.left = Math.max(clientLeft,0) + "px";  
	            }else if(((parseInt(jQuery.css(p,"width")) - parseInt(jQuery.css(this,"width")))/2) > 0)  
	                s.left = ((parseInt(jQuery.css(p,"width")) - parseInt(jQuery.css(this,"width")))/2) + "px";  
	        }  
	        
	        if(!f || f == "vertical") {  
	            //s.top = "0px";  
	            if(p.nodeName.toLowerCase() == "body") {  
	                var clientHeight = w.scrollTop() - 4 + (w.height() - parseInt(jQuery.css(this,"height")))/2;  
	                s.top = Math.max(clientHeight,0) + "px";  
	            }else if(((parseInt(jQuery.css(p,"height")) - parseInt(jQuery.css(this,"height")))/2) > 0)  
	                s.top = ((parseInt(jQuery.css(p,"height")) - parseInt(jQuery.css(this,"height")))/2) + "px";  
	        }  
	        
	    });  
	}; 
	
	function isIE6(){
		if (jQuery.browser.msie) {
		   return parseInt(jQuery.browser.version)<=6 ;
		}
		return false;
	}
	
	var dialogWindow = null ;
	window.$topWindow = function () {
	    var parentWin = window;
	    if( parentWin.$._dialog ) dialogWindow = parentWin ;
	    while (parentWin != parentWin.parent) {
	    	try{
	        	if (parentWin.parent.document.getElementsByTagName("FRAMESET").length > 0) break;
	        	parentWin = parentWin.parent;
	        	if( parentWin.$._dialog ) dialogWindow = parentWin ;
	    	}catch(e){
	    		return dialogWindow ;
	    	}
	    }

	    return dialogWindow;
	};
	
	function getWinPos(win){
		return {
			width:Math.max(
				win.document.body['scrollWidth'],
				win.document.documentElement['scrollWidth'],
				win.document.body['offsetWidth'],
				win.$(win.document.body).find("table").width()
			),
			height:Math.max(
				win.document.body['scrollHeight'],
				win.document.documentElement['scrollHeight'],
				win.document.body['offsetHeight']
			)
		};
	}
	
})() ;