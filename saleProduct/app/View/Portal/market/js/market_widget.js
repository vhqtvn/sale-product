(function(){
	$(function(){
	    widgetMarket(); 
	 });
	
	 // 应用市场
	 function widgetMarket(){	
		var $menu = $('#widget_nav');
		
		var googleGadgetsCategory = {
			/*name:"默认",
			id:"custom_widget",
			code:"custom_widget"*/
		} ;
			
		// 分类属性
		var appsName, // 分类名字
			appsClass, //分类样式
			appsIco; //分类图标
			webosService.loadAppMarkets(function(marketApps){
				marketApps = marketApps||[] ;
				//marketApps.push(googleGadgetsCategory) ;
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
					$(".app_widget_content").html("没有小挂件可添加！") ;
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
					
					webosService.loadAppWidget("-" , appId , appCode ,function(widgets){
						$(".widget_wrap [widgetid]").each(function(){
							var id = $(this).attr("widgetid");
							if(!id)return ;
							$(widgets).each(function(){
								if(this.id == id){
									this.desktop = 1 ;
								}
							}) ;
						}) ;
		
						widgets_index("-" , appId , appCode , widgets) ;
					})
				}).eq(0).trigger('click'); ;
				
				return false;
			}) ;
			return false;
	
	 }
	 
	 function widgets_index(url , appId , appCode , widgets){	
		//widget显示的代码
		var enabled_ico="";
		var unenabled_ico="";	
		
		var enabled_ico2="";
		var unenabled_ico2="";
		
		var widgetID,		
			widgetName,		
			widgetHref,
			widgetDesktop,	//是否在桌面
			widgetValue,	//在哪个桌面
			widgetClass,
			widgetIcon,
			widgetSize,
			widgetWidth,
			widgetHeight,
			widgetLeft,		//距离左边的位移
			widgetTop;		//距离顶部的位移
			
		//widget 开关
		var widgetcheck=new Object();
	    widgetcheck={
		  init:function(obj,fun){
			  var o=$(obj).find(".fun_check");
			  var o2=$(obj).find(".fun_check_open");
	
			  o.data('value',0);
			  o2.data('value',1);
			  o.click(function(){
			      if($(this).data('value')==0){
			      		$(this).css({"background-position":"0px 0px"} );
				  	   try{
				  	   	if(fun){fun(this)} ;
				  	   }catch(e){alert(e.message)}
					  $(this).data('value',1) ;
				  }else{
				  	   $(this).css({"background-position":"-55px 0px"} );
				  	   try{
				  	   	if(fun){fun(this)} ;
				  	   }catch(e){alert(e.message)}
				      //$(this).animate({"background-position":"-55px 0px"},function(){if(fun){fun(this)}});
					  $(this).data('value',0)
				  }
			  });
		  }
		};			
		
		$.each(widgets,function( i , widget){
				widgetID = widget.id||"";
				widgetName = widget.name||"";	
				widgetDesktop = widget.desktop||"";
				widgetHref = widget.href||widget.url||"";
				widgetValue = widget.value||"";
				widgetSize = widget.size||"";
				widgetWidth = widget.width||"";
				widgetHeight = widget.height||"";
				widgetLeft = widget.x||"";
				widgetTop = widget.y||"";
	
				widgeIcon = webos.utils.parseIcon( widget.iconLarge||widget.icon||widget.thumbnail ) ;
				
			var sHtml="";
				sHtml += "<li class='app_nav app_widget_block' sourceType='"+widget.sourceType+"' categoryId='"+appId+"'  id='"+widgetID+"' href='"+widgetHref+"' size='"+widgetSize+"' fixwidth='"+widgetWidth+"' fixheight='"+widgetHeight+"' desktop='"+widgetDesktop+"' left='"+widgetLeft+"' top='"+widgetTop+"'>";//新增窗口属性
				
				 if(widgeIcon.isClass ){
					sHtml += "<div class='app_nav_ico "+widgeIcon.value+"'></div>";			
				 }else{
					sHtml += "<div class='app_nav_ico'><img style='width:45px;height:45px;' src='"+widgeIcon.value+"'></div>";		
				 };
				
				sHtml += "<h2>"+widgetName+"</h2>";
				sHtml += "<div class='fun_check "+returnCheck(widgetDesktop)+"'></div>";
				sHtml +="</li>";
				
				if(widgetDesktop == "1" ){
					enabled_ico += sHtml; 
				}else {
					unenabled_ico += sHtml;
				}				
		});			
		
		$("#enabled_ico_box").html(enabled_ico);
		$("#unenabled_ico_box").html(unenabled_ico);
		widgetcheck.init(".widget_map",do_desk_open);
		app_nav_hover();
		
		
	}//这个是新增
	
	function app_nav_hover(){
		$(".app_nav").hover(function(){
		   $(this).addClass("app_nav_hover")
		},function(){
		   $(this).removeClass("app_nav_hover")
		})
	}
	function returnCheck(val){
		var check_class='';
		if(val=="1"){
			check_class = "fun_check_open"; 
		}
		return check_class;
	}
	
	function do_desk_open(mythis){
		
		// mythis = .fun_check	
		var tli=$(mythis).parent();	
		var tbox=tli.parent().parent();	
		var tgt=tbox.siblings(".widget_map").find('ul');
		//控制开启不开启后看到的效果
			tli.appendTo(tgt).removeClass("app_nav_hover");
		
		var widgetid = tli.attr('id'),
			name = $(mythis).prev().text(),
			widgetHref = tli.attr('href'),
			widgetWidth = $(mythis).parent().attr('fixwidth'),
			widgetHeight = $(mythis).parent().attr('fixheight'),
			widgetDesktop = $(mythis).parent().attr('desktop'),
			widgetLeft = $(mythis).parent().attr('left'),
			widgetTop = $(mythis).parent().attr('top');	
			sourceType = $(mythis).parent().attr("sourceType") ;
		//
		 if(tbox.attr("name")=="enabled_wrap"){
		 //关闭桌面上的widget
			$('.desktop_item').find('.widget_wrap').each(function(){
				var wtitle = $(this).attr('title');
					if( name == wtitle ){						
						$(this).find('.widget_close').trigger('click');
					}
			})
		 } else{
		 	//打开桌面上的widget
		 	var widget = {
		 		id:widgetid,
		 		name:name,
		 		href:widgetHref,
		 		width:widgetWidth,
		 		height:widgetHeight,
		 		desk:widgetDesktop,
		 		x:widgetLeft,
		 		y:widgetTop,
		 		widgetType:sourceType
		 	} ;
		 	
			webos.window.show_widget(widget);
			//打开后把数据发送到webservice
		 }	
		 
		 webos.save({type:"widget",content:{
		 			appCode:tli.attr('categoryId'),
					code:widgetid,
					name:name,
					content:widgetHref,
					width:widgetWidth,
					height:widgetHeight
				}}) ;
	}
	
})()
	 