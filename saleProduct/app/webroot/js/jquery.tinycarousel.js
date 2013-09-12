(function(a){a.tiny=a.tiny||{};a.tiny.carousel={options:{start:1,display:1,axis:"x",controls:true,pager:false,interval:false,intervaltime:3000,rewind:false,animation:true,duration:1000,callback:null}};a.fn.tinycarousel_start=function(){a(this).data("tcl").start()};a.fn.tinycarousel_stop=function(){a(this).data("tcl").stop()};a.fn.tinycarousel_move=function(c){a(this).data("tcl").move(c-1,true)};function b(q,e){var i=this,h=a(".viewport:first",q),g=a(".overview:first",q),k=g.children(),f=a(".next:first",q),d=a(".prev:first",q),l=a(".pager:first",q),w=0,u=0,p=0,j=undefined,o=false,n=true,s=e.axis==="x";function m(){if(e.controls){d.toggleClass("disable",p<=0);f.toggleClass("disable",!(p+1<u))}if(e.pager){var x=a(".pagenum",l);x.removeClass("active");a(x[p]).addClass("active")}}function v(x){if(a(this).hasClass("pagenum")){i.move(parseInt(this.rel,10),true)}return false}function t(){if(e.interval&&!o){clearTimeout(j);j=setTimeout(function(){p=p+1===u?-1:p;n=p+1===u?false:p===0?true:n;i.move(n?1:-1)},e.intervaltime)}}function r(){if(e.controls&&d.length>0&&f.length>0){d.click(function(){i.move(-1);return false});f.click(function(){i.move(1);return false})}if(e.interval){q.hover(i.stop,i.start)}if(e.pager&&l.length>0){a("a",l).click(v)}}this.stop=function(){clearTimeout(j);o=true};this.start=function(){o=false;t()};this.move=function(y,z){p=z?y:p+=y;if(p>-1&&p<u){var x={};x[s?"left":"top"]=-(p*(w*e.display));g.animate(x,{queue:false,duration:e.animation?e.duration:0,complete:function(){if(typeof e.callback==="function"){e.callback.call(this,k[p],p)}}});m();t()}};function c(){w=s?a(k[0]).outerWidth(true):a(k[0]).outerHeight(true);var x=Math.ceil(((s?h.outerWidth():h.outerHeight())/(w*e.display))-1);u=Math.max(1,Math.ceil(k.length/e.display)-x);p=Math.min(u,Math.max(1,e.start))-2;g.css(s?"width":"height",(w*k.length));i.move(1);r();return i}return c()}a.fn.tinycarousel=function(d){var c=a.extend({},a.tiny.carousel.options,d);this.each(function(){a(this).data("tcl",new b(a(this),c))});return this}}(jQuery));


var cssContent = '\
<style type="text/css">\
.image-container {  overflow:hidden; padding: 0 0 10px 0;position:relative; }\
.image-container img{border:none;}\
.image-container .viewport { width: 200px; height: 130px; float: left; overflow: hidden; position: relative; }\
.image-container .pager { overflow:hidden; list-style: none; clear: both; margin: 0 0 0 45px; }\
.image-container .pager li { float: left; }\
.image-container .pager a { background-color: #fff; text-decoration: none; text-align: center; padding: 5px; color: #555555; font-size: 14px; font-weight: bold; display: block; }\
.image-container .pager .active { color: #fff; background-color:  #555555; }\
.image-container .buttons { display: block; margin: 30px 10px 0 0; text-indent: -999em; float: left; width: 32px; height: 32px; overflow: hidden; position: relative;}\
.image-container .next {   }\
.image-container .disable { visibility: hidden; }\
.image-container .overview { list-style: none; position: absolute; width: 240px; left: 0 top: 0; }\
.image-container .overview li{ float: left; margin: 0 20px 0 0; padding: 1px; height: 121px; width: 236px;}\
.image-container .prev{ background-image:url("/'+fileContextPath+'/app/webroot/img/m/arrow-left.png") }	\
.image-container .next{ background-image:url("/'+fileContextPath+'/app/webroot/img/m/arrow-right.png") }	\
.image-container .edit-img{ cursor:pointer;width:20px;height:20px;position:absolute;right:5px;top:5px;display:none; }	\
</style>';
document.write(cssContent) ;


//$('#slider3').tinycarousel({ pager: true, interval: true  });
$(function(){
	function loadGalleryImage( container ){

		var me = $(container) ;
		var html = '\
		<a class="buttons prev" href="#"><<</a>\
        <div class="viewport">\
            <ul class="overview" style="margin:0px;">\
            </ul>\
        </div>\
        <a class="buttons next" href="#">>></a>\
        <ul class="pager">\
        </ul>' ;
		me.html(html) ;
		var entityId = $(container).attr("entityId") ;
		var entityType = $(container).attr("entityType") ;
		var localUrl = $(container).attr("localUrl") ;
		var json = {} ;
		json.entityId = entityId ;
		json.entityType = entityType ;
		
		var ul =  me.find(".overview") ;
		var pageUrl = me.find(".pager") ;
		$.dataservice("model:File.loadImage",json ,function(result){
			result = result||[] ;
			if(localUrl)result.push({IMAGE_URL:localUrl}) ;
			$(result).each(function(index,item){
				$("<li><img style='width:200px;height:120px;' src='/"+fileContextPath+this.IMAGE_URL+"'></li>").appendTo(ul) ;
				$(" <li><a rel='"+index+"' href='#' class='pagenum'>"+(index+1)+"</a></li>").appendTo(pageUrl) ;
			}) ;
			pageUrl.find("li:first").find("a").addClass("active") ;
			me.tinycarousel({ pager: true, interval: true  });//
		});
		
		var editImg = $("<img class='edit-img' src='/"+fileContextPath+"/app/webroot/img/m/cog.png'>").appendTo(me) ;
		
		editImg.click(function(){
			openCenterWindow(contextPath+"/page/forward/File.image/"+entityType+"/"+entityId,800,600,function(){
				loadGalleryImage(me) ;
			}) ;
			return false ;
		}) ;
		
		me.mouseenter(function(){
			editImg.show() ;
		}) ;
		
		me.mouseleave(function(){
			editImg.hide() ;
		}) ;
	}
	
	$(".image-container").each(function(){
		loadGalleryImage(this) ;
	}) ;
}) ;