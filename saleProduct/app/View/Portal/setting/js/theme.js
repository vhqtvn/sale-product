(function(){
 	function get_theme_thumb(){
	 	webosService.loadSysThemes(function(themes){
	 		var sHtml="";
	 		var currentTheme = webosService.currentTheme ;
	 		$(themes).each(function(index,theme){
	 			var clz = '' ;
	 			var cho = '' ;
	 			if(currentTheme.url == theme.url){
	 				clz = " class='current' " ;
	 				cho = "<div id='has_choosed'></div>" ;
	 			}
	 			
	 			sHtml+="<li themeid='"+(theme.id||theme.seqId)+"' "+clz+" name='"+theme.name+"' url='"+theme.url+"'><img src='"+webosService.root+theme.thumbnail+"' /> "+cho+" </li>" ;
	 			
	 		}) ;
	 		$("#theme").html(sHtml);
			set_theme() ;
	 	}) ;
	 }
	 function set_theme(){
		 var themes_obj=$("#theme li");
		 themes_obj.click(function(){
		 	 var url = $(this).attr('url') ;
		 	 var id = $(this).attr('themeid') ;
			 $("body").css({"background-image":"url("+webosService.root+url+")"});
			 $("#theme li.current").removeClass("current");
			 $("#has_choosed").remove();
			 $(this).addClass("current").append("<div id='has_choosed'></div>"); 
			 webosService.saveTheme({
			 	id:id,
			 	url:url,
			 	name:$(this).attr('name') 
			 }) ;
		 }) ;
	 }
	 
	 get_theme_thumb();
 })();