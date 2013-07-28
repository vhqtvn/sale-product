/**
 * 列表选择控件
 * 作者: lixh@bingosoft.net
 * 功能：
 * 	TODO
 * 使用示例：
 *  TODO
 */
;(function($){
	$.listselectdialog = function(params , callback){
		
		var _p = $.extend({},params) ;
		
		callback = callback||function(){
			var args = jQuery.dialogReturnValue() ;
			//alert( $.json.encode(args) );
			if(args){
				var value = args.value ;
				var label = args.label ;
				
				if( _p.valueField && _p.labelField ){
					$(_p.valueField).val(value) ;
					$(_p.labelField).val(label) ;
				}
			}
			if(params.after){
				params.after(args) ;
			}
		} ;
		
		//build default values defaults:[{value:'1',label:'广东省'},{value:'11',label:'广州市'}],
		if( _p.valueField && _p.labelField ){
			var val 	= $(_p.valueField).val() ;
			var label 	= $(_p.labelField).val() ;
			var vals = (val+"").split(",");
			var labels = (label+"").split(",");
			var defaults = [] ;
			$(vals).each(function(index,v){
				defaults.push({value:v,label:labels[index]}) ;
			}) ;
			_p.defaults = defaults ;
		}else if($.isFunction(_p.defaults)){
			_p.defaults = _p.defaults.call(this) ;
		}
		
		var width  = params.width ;
		var height = params.height  ;
		
		var random = new Date().getTime() ; 
		
		var templateName = "" ;
		if(_p.grid || _p.tree ){
			if( params.tree && params.grid ){
				width = width||800 ;
				height = height||650 ;
			}else if(params.tree){
				width = width||500 ;
				height = height||650 ;
			}else{
				width = width||800 ;
				height = height||650 ;
			}
			templateName = "treegrid_template.php" ;
		}else{
			width = width||600 ;
			if( _p.queryFields ){
				height = height||450 ;
			}else{
				height = height||410 ;
			}
			
			templateName = "select_template.php" ;
		}
		
		$.open(
			(Config.serverPath||Config.contextPath)+"/app/webroot/js/listselectdialog/"+templateName+"?random="+random , 
			width , 
			height , 
			{
			 title:_p.title , 
			 params: _p,
			// isScroll:false,
			 titleStyle:_p.titleStyle||""} 
			, callback ) ;
			
	}
	$.fn.listselectdialog = function( params , callback ){
		$( this ).each(function(){
			$(this).unbind("click.listselect").bind("click.listselect",function(){
				var me = $(this) ;
				var _callback = callback? function(){
					callback(me) ;
				} :window.undefined;
				$.listselectdialog( params , _callback ) ;
				return false;
			})
		});
	}
	
})(jQuery)