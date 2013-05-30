(function(){
	var webos_console = function(){
		
			webosService.loadControllApp(function(categorys){
				$("#app_console_container").empty();
				$(categorys).each(function(){
					var apps = this.apps ;
					var fieldset = $('<fieldset class="control_class"><legend>'+this.name+'</legend></fieldset>')
									.appendTo($("#app_console_container")) ;
					var count = 0 ;
					$(apps).each(function(index , app){
						if( !(app.url||app.func)  ) return ;
						var name = app.title||app.name ;
					
						appIcon = webos.utils.parseIcon( app.styleClass || app.iconLarge||app.icon ) ;
										
						var iconHtml = '' ;
						if(appIcon.isClass ){
							iconHtml = "<div class='appicon "+appIcon.value+"'></div>";			
						}else{
							iconHtml = "<div class='appicon' style='background:url("+appIcon.value+") no-repeat;'></div>";		
						};
					
						var html = [] ;
						html.push('<li class="nav_ico" title="'+name+'" iframe="'+app.iframe+'"');
						html.push('	icon="appicon"');
						html.push('	href="'+app.url+'"');
						html.push('	size="max" fixwidth="null" fixheight="null">');
						html.push(iconHtml);
						html.push('<h2>'+name+'</h2>');
						html.push('</li>');
						
						$( html.join("") ).appendTo(fieldset).click(function(){
							if(app.func){
								app.func() ;
							}else if(app.url){
								webos.window.open({
									title:$(this).attr("title"),
									url:jQuery.utils.parseUrl(app.url),
									iframe:app.iframe === false?false:true,
									width:800,
									height:600
								})
							}
						}) ;
						
						count = index ;
					}) ;
					
				}) ;
			});

		 /*webosService.loadControllApp(function(apps){
			$("#app_console_container ul").empty();
			var count = 0 ;
			$(apps).each(function(index , app){
				if( !(app.url||app.func)  ) return ;
				var name = app.title||app.name ;
				appIcon = webos.utils.parseIcon( app.iconLarge||app.icon ) ;
								
				var iconHtml = '' ;
				if(appIcon.isClass ){
					iconHtml = "<div class='appicon "+appIcon.value+"'></div>";			
				}else{
					iconHtml = "<div class='appicon' style='background:url("+appIcon.value+") no-repeat;'></div>";		
				};
			
				var html = [] ;
				html.push('<li class="nav_ico" title="'+name+'" iframe="'+app.iframe+'"');
				html.push('	icon="appicon"');
				html.push('	href="'+app.url+'"');
				html.push('	size="max" fixwidth="null" fixheight="null">');
				html.push(iconHtml);
				html.push('<h2>'+name+'</h2>');
				html.push('</li>');
				
				$( html.join("") ).appendTo("#app_console_container ul").click(function(){
					if(app.func){
						app.func() ;
					}else if(app.url){
						webos.window.open({
							title:$(this).attr("title"),
							url:jQuery.utils.parseUrl(app.url),
							iframe:app.iframe === false?false:true,
							width:800,
							height:600
						})
					}
				}) ;
				
				count = index ;
			}) ;
		
		 	}) ;*/
	 }
	 
	 webos_console() ;
})() ;

