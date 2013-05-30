(function($){
	var ajaxQueueArray=[];
	$("body").data("ajaxQueueArray",ajaxQueueArray);
	function arrayContains(_array,_ele){
		for(var i=0;i<_array.length;++i){
			if(_ele==_array[i]){
				return true;
			}
		}
		return false;
	};
	$.ajaxRegisterOauth=function(jqCtx,eventType,beforeFn){
	    if(!!jqCtx && !!eventType){
	       var _ele=eventType+jqCtx;
	       if(arrayContains(ajaxQueueArray,_ele)){
	       	return;
	       }else{
	       	ajaxQueueArray.push(_ele);
	       }
	       $(document).queue("ajaxQueue",function(){
	       	if(typeof beforeFn =="function"){
	       		beforeFn();
	       	}
	       	$(jqCtx).trigger(eventType);
	       });	
	    }else{
	    	throw new Error("参数不允许为空！");
	    }
	};
	$.ajaxObjQueueCallback=function(){
		var len=$(document).queue("ajaxQueue").length;
		for(var i=0;i<len;++i){
			$(document).dequeue("ajaxQueue");
		}
		ajaxQueueArray=[];
	};
	$(function(){
		$(document).ajaxComplete(function(event, XMLHttpRequest, ajaxOptions) {
				  var response;
				  response=XMLHttpRequest.responseText;
				  if(!response){
				  	response=XMLHttpRequest.response;
				  }
				  //console.log("in global complete:");
				  //console.log(response);
				  var contentType=XMLHttpRequest.getResponseHeader("Content-Type");
				  if(contentType && contentType.indexOf("application/json")>-1 && XMLHttpRequest.status==401){
					response=eval("("+response+")");
					if(response.redirectUrl){
						var redirectUrl=response.redirectUrl;
	          			//console.log(redirectUrl);
	          			window.open(redirectUrl,"redirectUrl","width=800,height=600,left=300,top=50,resizable=yes,scrollbars=yes,status=yes");
					}  
		          }
			});
	});
	
})(jQuery);