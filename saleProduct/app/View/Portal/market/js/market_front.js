(function(){
	$(function(){
	    frontMarket(); 
	 });
	
	 
	 // 应用市场
	 function frontMarket(){	
		var $menu = $('#front_nav'),
			$allApps = $('#all_fronts');
		// 分类属性
		var appsName, // 分类名字
			appsClass, //分类样式
			appsIco; //分类图标
		// 应用属性
		var	appID, // 应用ID
			appName, //应用名字
			appHref, // 应用目标地址
			appCategory, //应用分类
			appDesktop, // 应用在哪个桌面
			appValue, // 应用在哪个桌面
			appSize, // 应用的大小模式
			appWidth, // 应用的宽度
			appHeight, // 应用的高度
			appClass, // 应用的样式
			appIcon, // 应用的自定义图标地址
			appRecommend, //应用是否推荐
			appNew, // 应用是否新添加的
			appDesc, // 应用的描述
			appImgs, // 应用的截图
			appStars, // 应用的得分
			appCount, // 应用的人气指数
			appPublisher, //应用的发布者
			appPubdate; //应用的发布时间
			
			webosService.loadAppMarkets(function(marketApps){
				
				marketApps = marketApps||[] ;
				/*marketApps.push({
					name:"默认",
					id:"custom_widget",
					code:"custom_widget"
				}) ;*/
				
				//加载分类（应用）
				var menuHtml = "", appContent = "";
				$.each(marketApps,function(i,category){			
					appsName = category.name ;
					appsIco = webos.utils.parseIcon( category.iconSmall||category.icon ) ;
					//分类的html	
					var appsHtml = "";	
					appsHtml += "<div class='catNavTrigger' appid='"+category.id+"' appcode='"+category.code+"'>";
					
					 if(appsIco.isClass ){
					 	appsHtml += "<div class='cat_icon "+appsIco.value+"'></div>";			
					 }else{
						appsHtml += "<div class='cat_icon' style='background:url("+appsIco.value+") no-repeat;'></div>";		
					 };
					
					appsHtml +=  appsName +"</div>";
					menuHtml += appsHtml;						
				});
				$menu.empty().html(menuHtml);
				
				if(!menuHtml){
					$(".app_front_content").html("没有快捷方式可添加！") ;
					return ;
				}
				
				$menu.find('.catNavTrigger').hover(function() { 
				   $(this).addClass("hover"); 
				}, function() { 
				   $(this).removeClass("hover"); 
				}).click(function(){
					var menuThis = this ;
					var appId 	= $(this).attr("appid") ;
					var appCode = $(this).attr("appcode") ;
					var $this = $(this);
					$this.siblings().removeClass("select");
					$this.addClass("select"); 
						
					webosService.loadMarketAppFunctions("-" , appId , appCode ,function(functions){
						
						$(".desk_ico_wrap li").each(function(){
							var id = $(this).attr("id");
							if(!id)return ;
							$(functions).each(function(){
								if(this.id == id){
									this.desktop = 1 ;
								}
							}) ;
						}) ;
						
						//应用列表的Html	
						var	appsListHtml = "";	
						$(functions).each(function(i,app){
								appID = app.id||"", // 应用ID
								appName = app.name||"", //应用名字
								appDesktop = app.desktop||"", //应用分类
								appCategory = $(menuThis).text()||"" , //应用分类
								appHref = app.href||app.url||"", // 应用目标地址
								appValue = app.value||"", // 应用在哪个桌面
								appSize = app.size||"", // 应用的大小模式
								appWidth = app.width||"", // 应用的宽度
								appHeight = app.height||"", // 应用的高度
								appDesc = app.description||"", // 应用的描述
								appImgs = app.thumbnail||"", // 应用的截图
								appPublisher = app.publisher||"", //应用的发布者
								appPubdate = app.pubdate||""; //应用的发布时间	
	
								var	appsList = "";
								
								var appIcon = webos.utils.parseIcon( app.iconLarge||app.icon||"" ) ;
								appsList += "<li categoryId='"+appId+"' categoryName='"+appCategory+"' appid='"+appID+"'>";
								//如果有自定义图标，就采用自定义图标的方式
								
								if(appIcon.isClass ){
								 	appsList += "<div class='appIcon "+appIcon.value+"'></div>";		
								}else{
									appsList += "<div class='appIcon' style='background:url("+appIcon.value+") no-repeat;'></div>";								
								};
								
								appsList += "<div class='appName'>"+appName+"</div>";
								//appsList += "<div class='appBrief'>"+appDesc+"</div>";

								//如果appDesktop 等于1 ，则桌面已经有这个图标了，不能继续添加

								if($(".nav_ico[id='"+appID+"']")[0]||$(".nav_ico[appid='"+appID+"']")[0]){
									appsList += "<div title='从桌面移除' appid='"+appID+"' class='appAddedBtn'></div>";
								}else{
									appsList += "<div title='添加到桌面' appid='"+appID+"' class='appAddBtn'></div>";
								}
								
								appsList += "</li>";
								
								appsListHtml += appsList;	
						});
						
						$allApps.empty().load(webosService.url.front_list,function(){
							$allApps.find('.region_lm_top_front').text($this.text());
							$allApps.find('.frontListUl ul').empty().html(appsListHtml);
							//bind_list_click();//绑定列表的点击事件
							bing_list_add();//绑定列表的添加事件		
							
							//绑定数据
							$(functions).each(function(i,app){
								var tgt = $allApps.find('.frontListUl ul').find("[appid='"+app.id+"']") ;
								var categoryId = tgt.attr("categoryId");
								var categoryName = tgt.attr("categoryName");
								app.categoryId = categoryId ;
								app.categoryName = categoryName ;
								tgt.data("entity",app) ;
							}) ;
						});	
					});
				}).eq(0).trigger('click'); ;
				
				return false;
			}) ;
			return false;
	
	 }
	//绑定列表的添加事件
	function bing_list_add(){
		if(window.bing_list_addIsInit){
			return ;
		}
		
		window.bing_list_addIsInit = true ;
		
		$('.appAddBtn').live("click",function(e){
			e.stopPropagation();
			webos.utils.addDeskShortcut(this,$(this).parents('li')) ;//tgt , entityTgt
			$(this).removeClass().addClass("appAddedBtn").attr("title","从桌面移除");
			return false ;
		})
		
		$('.appAddedBtn').live("click",function(e){
			var appid = $(this).attr("appid");
			e.stopPropagation();
			
			var tgt = $(".nav_ico[id='"+appid+"']")[0]||$(".nav_ico[appid='"+appid+"']")[0] ;
			
			webos.icons.uninstall( $(tgt) ) ;
			//webos.utils.addDeskShortcut(this,$(this).parents('li')) ;//tgt , entityTgt
			$(this).removeClass().addClass("appAddBtn").attr("title","添加到桌面");
			return false ;
		})
	};
})() ;
	 