// JavaScript Document
 $(function(){
    appSettings(); 
 });

  function appSettings(){	
		var $menu = $('#setting_nav'),
			$main = $('#setting_main'),
			$maintitle = $('#setting_title');
			
		var	appID, 		// 应用ID
			settingUrl, //设置的链接地址
			appIcon, 	//设置的链接地址
			appName; 	//应用名字
			
		var menuHtml = '';
		//获取设置的应用json数据
		webosService.loadSettingMenus(function(apps){
			
			$.each(apps,function( i , app){
				settingUrl = app.settingurl||app.configUrl||"";
				appName = app.name;
				//appIcon = app.icon;
				var icon = webos.utils.parseIcon( app.iconSmall||app.icon ) ;
				var appsHtml = "";	
				appsHtml += "<div class='catNavTrigger' url='"+settingUrl+"' title='"+ appName +"'>";
				
				if(icon.isClass ){
					appsHtml += "<span class='cat_icon "+icon.value+"' title='"+icon.value+"'></span>" + appName +"</div>";
				}else{
					appsHtml += "<span class='cat_icon'><img src='"+icon.value+"' alt='"+appName+"'/></span>" + appName +"</div>";
				};	
				
				menuHtml += appsHtml;
			});			
			$menu.empty().html(menuHtml);
			
			//$menu.prepend('<div class="catNavTrigger select"><span class="cat_icon cat_1"></span>推荐应用</div>');
			$menu.find('.catNavTrigger').hover(function() { 
				   $(this).addClass("hover"); 
				}, function() { 
				   $(this).removeClass("hover"); 
				}).click(function(){
					var $this = $(this);
						$this.siblings().removeClass("select");
						$this.addClass("select");
					var appsetting = $this.attr('url'),
						apptitle = $this.text();
					if(!appsetting)	return ;
						//设置的页面地址
						$main.attr('src',appsetting);		
						$maintitle.text(apptitle);		
							
				}).eq(0).trigger('click');; 
			
			return false;
		}) ;
  };
 