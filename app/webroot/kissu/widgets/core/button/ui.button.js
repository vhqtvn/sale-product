/*
 * Title:
 * Description:
 * Aucthor: 曾宪斌
 * Email: zengxianbin@gmail.com
 * Create Date:2009-06-18
 * Copyright 2009
 */ 
(function($){
	var clz = {
		hover:'z-btn-over ui-state-hover'
	} ;
	
	var perventInterval = 300 ;//阻止重复提交时间间隔
	
	$.fn.btn = function(){
		
		var btn = this.data("_self");;
		if(btn){
			return btn;
		};
		this.init = function( jqueryObj,json4Options){//_为掺入参数，格式为json对象 {click:function(){}}
	
			var id = jqueryObj.attr('id')||"gen" + Math.random();
			var icon = json4Options.icon||'icon-btncom';
			var clz = jqueryObj.attr('class')||'';
			var type = jqueryObj.attr('type');
			
			var bntStr=[
				'<table id="',id,'" class="z-btn '+clz+'" cellSpacing=0 cellPadding=0 border=0 style="width:auto;"><tbody><tr>',
					'<TD class=z-btn-left><i>&nbsp;</i></TD>',
					'<TD class="z-btn-center ui-helper-reset ui-corner-all ui-widget-content  ui-state-default">',
					'<div><EM unselectable="on">',
							'<input style="border:0px;"  type="'+type+'" class="z-button z-btn-text ui-state-default ',icon,'"  value="',jqueryObj.attr('value'),'"/>',
					'</EM></div>' ,
					'</TD>',
					'<TD class=z-btn-right><i>&nbsp;</i></TD>',
				'</tr></tbody></table>'
			];
			var bnt = $(bntStr.join('')).btn();
			//构造事件
			var _onclick = jqueryObj.attr("onclick")||( json4Options?json4Options.click:'' ) ;
			//var _click =  typeof(_onclick)=='string'?eval(_onclick):_onclick ;
			var temp =  typeof(_onclick)=='string'?eval(_onclick):_onclick ;
			
			var _click = function(){
				if(!temp) return ;
				temp() ;
				try{
				bnt.disable() ;
				setTimeout(function(){bnt.enable() ;},perventInterval) ;
				}catch(e){}
			}
			var self = this ;
			bnt._click = _click ;
			bnt.disable();
			if(jqueryObj.attr("disabled")){
				bnt.disable();
			}else{
			   bnt.enable();
			}
			jqueryObj.replaceWith(bnt);
			bnt.data("_self", bnt);  
			
			bnt.find('div').css({width:(bnt.find('input').width()+20),overflow:'hidden'});
			
			return bnt;
		};
		
		this.show = function(){
			$(this).show();
		};
		
		this.hide = function(){
			$(this).hide();
		};
		
		this.enable = function(){
			if(!this.hasClass('z-btn-dsb')) return ;
			this.removeClass("z-btn-dsb").find('input').removeClass('z-btn-dsb').attr('disabled',false);
			this.click(this._click);
			this.find('.z-btn-center , .z-btn-center .z-button').hover(//
				  function () {
				    $(this).addClass(clz.hover);
				  },
				  function () {
				    $(this).removeClass(clz.hover);
				  }
				) ;
		};
		this.disable = function(){
			 this.addClass("z-btn-dsb").find('input').addClass('z-btn-dsb').attr('disabled',true) ;
			 this.unbind("click");
			 this.find('.z-btn-center , .z-btn-center .z-button').hover(
			 	function(){
			 		$(this).removeClass(clz.hover);
			 	},function(){
			 		$(this).removeClass(clz.hover);
			 	}
			 ) ;
		};  
		return this;
	};
	
	$.buttonInit = function(jqueryObj,json4Options){
		 if(jqueryObj.attr("type") == "button"){
			  jqueryObj.btn().init(jqueryObj,json4Options);
		 }
		 
		 if(jqueryObj.attr("type") == "reset"){
			  jqueryObj.btn().init(jqueryObj,json4Options).click(function(){
				var form = jqueryObj.parents("form")[0];
				if(form)
					form.reset();
			});
		 }
		 
		 if(jqueryObj.attr("type") == "submit"){
			  jqueryObj.btn().init(jqueryObj,json4Options).click(function(){
				var form = jqueryObj.parents("form")[0];
				if(form)
					form.submit();
			});
		 }
	     return jqueryObj.btn();
	}
	
})(jQuery);	