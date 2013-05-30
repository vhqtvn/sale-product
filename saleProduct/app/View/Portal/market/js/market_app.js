(function(){
  var _marketApp = [] ;
  var _appDetail ;
 
 function getMarketApp(appId){
 	var mapp = null ;
 	$(_marketApp||[]).each(function(index,app){
 		if(app.id == appId){
 			mapp = app ;
 		}
 	}) ;
 	return mapp ;
 }
 // 应用市场
 function appMarket(){	
	var $menu = $('#app_nav'),
		$allApps = $('#all_apps');
		
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
		
		webosService.loadAppStoreCategory(function(marketApps){
			
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
				
				webosService.loadAppStoreApps("-" , appId , appCode ,function(functions){
					
					_marketApp = functions ;
					
					//应用列表的Html	
					var	appsListHtml = "";	
					$(functions).each(function(i,app){
							appID = app.id||"", // 应用ID
							appName = app.name||"", //应用名字
							appDesktop = app.desktop||"", //应用分类
							appCategory = $(menuThis).text()||"" , //应用分类
							appHref = app.href||"", // 应用目标地址
							appValue = app.value||"", // 应用在哪个桌面
							appSize = app.size||"", // 应用的大小模式
							appWidth = app.width||"", // 应用的宽度
							appHeight = app.height||"", // 应用的高度
							appDesc = app.briefDesc||"", // 应用的描述
							appImgs = app.thumbnail||"", // 应用的截图
							appPublisher = app.publisher||"", //应用的发布者
							appPubdate = app.pubdate||""; //应用的发布时间	
					
							var	appsList = "";
							
							appIcon = webos.utils.parseIcon( app.iconLarge||app.icon||"appicon_default_large" ) ;
							appsList += "<li appid='"+appID+"' categoryId='"+appId+"' categoryName='"+appCategory+"' >";
							//如果有自定义图标，就采用自定义图标的方式
							
							if(appIcon.isClass ){
							 	appsList += "<div class='column1'><div class='appIcon "+appIcon.value+"'></div></div>";		
							}else{
								appsList += "<div class='column1'><div class='appIcon' style='background:url("+appIcon.value+") no-repeat;'></div></div>";								
							};
							
							appsList += "<div class='column2'>";
							appsList += "<span class='appName'>"+appName+"</span>";
							appsList += "<div class='appBrief'>"+appDesc+"</div>";
							appsList += "</div>";
							appsList += "<div class='column3'>";
							appsList += "<div class='appStars'>";
							appsList += "<div style='width: "+appStars+"' class='appStar'></div>";
							appsList += "</div>";
							appsList += "<div class='appAddedCount'>"+appCount+"</div>";
							appsList += "</div>";
							appsList += "<div class='column4' style=''>";
						
							//如果appDesktop 等于1 ，则桌面已经有这个图标了，不能继续添加
							if(appDesktop == '1'){
								var _v = "" ;
								if( app.visibleAll == 'visible' ){
								//	_v = "所有人可见"
								}
								
								appsList += "<a href='javascript:void(0);' title='卸载' class='appUninstallBtn'>卸载</a>";
								appsList += "<a href='javascript:void(0);' class='appAddBtn' added='1' style='display:none'>已添加</a>";
								appsList += "<span class='app-column4-text'>"+_v+"</span>";
								
							}else{
								appsList += "<a href='javascript:void(0);' title='安装' class='appAddBtn' added='0'>已添加</a>";
								appsList += "<input type='checkbox' value='"+appID+"' checked='true' id='"+appID+"_seeall' style='display:none;'><label style='display:none;' for='"+appID+"_seeall'>所有人可见</label>";
							}
							appsList += "</div>";
							appsList += "</li>";
							
							appsListHtml += appsList;
					});
					
					$allApps.empty().load(webosService.url.app_list,function(){
						$allApps.find('.region_lm_top').text($this.text());
						$allApps.find('.appListUl ul').empty().html(appsListHtml);
						//bind_list_click();//绑定列表的点击事件
						bing_list_add();//绑定列表的添加事件		
						
						//绑定数据
						$(functions).each(function(i,app){
							var tgt = $allApps.find('.appListUl ul').find("[appid='"+app.id+"']") ;
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


	//绑定列表的点击事件
	 function bind_list_click(){
		var appContentHtml = "";	
		
		$('.appListUl li .column1,.appListUl li .column2').click(function(){
			
			var tgt 	= $(this).parent() ;
			var appId 	= tgt.attr('appid') ;
			
			
			var tgt = $allApps.find('.appListUl ul').find("[appid='"+appId+"']") ;
			var app = tgt.data("entity") ;
			
			webosService.loadAppDetail(appId,function(appDetail){
					_appDetail = appDetail ;
					
					var appCID = app.id||"", // 应用ID
						appCName = app.name||"", //应用名字
						appmenu =  app.categoryName||"" , //应用分类
						appCHref = app.href||app.url||"", // 应用目标地址
						appCValue = app.value||"", // 应用在哪个桌面
						appCSize = app.size||"", // 应用的大小模式
						appCWidth = app.width||"", // 应用的宽度
						appCHeight = app.height||"", // 应用的高度
						appCDesc = app.description||"", // 应用的描述
						appCImgs = app.thumbnail||"", // 应用的截图
						appCStars = app.stars||"" , // 应用的得分
						appCCount = app.count||"" , // 应用的人气指数
						appCPublisher = app.publisher||"", //应用的发布者
						appCPubdate = app.pubdate||""; //应用的发布时间	
						
					var addedNum = tgt.find('.appAddBtn').attr('added');	
					
						var appIcon = webos.utils.parseIcon( app.iconLarge||app.icon||"appicon_default_large" ) ;
						//如果有自定义图标，就采用自定义图标的方式
						var appIconHtml = "" ;
						if(appIcon.isClass ){
						 	appIconHtml += "<div class='appBigIconBg'><div class='appBigIcon "+appIcon.value+"'></div></div>";		
						}else{
							appIconHtml += "<div class='appBigIconBg'><div class='appBigIcon' style='background:url("+appIcon.value+") no-repeat;'></div></div>";								
						};	
						
					
					$allApps.empty().load(webosService.url.app_content,function(){
						//$allApps.find('.appPanel').empty().html(appContentHtml);
						//alert( $("[type='text/appTemplate']").text() )
						//var text = $("[type='text/appTemplate']").text() ;
						var text =	$( "#app_render" ).render( appDetail )	
						
						text = text.replace(/\{appName\}/g,appCName) ; 
						text = text.replace(/\{appDesc\}/g,appCDesc) ; 
						text = text.replace(/\{thumbnailUrl\}/g,appCImgs||(_webosRoot+"themes/default/images/AppPhoto/m_1308531072699.jpg")) ; 
						text = text.replace(/\{appCategory\}/g,appmenu) ; 
						text = text.replace(/\{addedNum\}/g,addedNum) ; 
						text = text.replace(/\{appId\}/g,appCID);
						text = text.replace(/\{appIcon\}/g,appIconHtml)
						text = text.replace(/\{publisherDate\}/g,appCPubdate||"2012-04-12")
						text = text.replace(/\{webosImgRoot\}/g,_webosRoot+"themes/default/")
						
						
						$allApps.find('.appPanel').empty().html( text );
						
						if( parseInt(addedNum) >= 1 ){
							$(".appBuyed").show() ;
							$(".appNoBuy").hide() ;
						}else{
							
						}
						
						
						bind_content_add(appDetail , appId);
					});
			}) ;
			
					
							
		});
		if($.browser.msie ){
			if($.browser.version == '6.0'){
				$('.appListUl li').hover(function(){
					$(this).addClass('li_hover');
			   },function(){
					$(this).removeClass('li_hover')
			   })
			}
		}
	 }
 }
	//绑定列表的添加事件
	function bing_list_add(){
		$(".appUninstallBtn").click(function(e){
				e.stopPropagation();
				var	appDID = $(this).parents('li').attr('appid') ;
				if( window.confirm("确认卸载该应用吗？") ){
					webosService.uninstallApp(appDID,function(){
						window.location.reload();
					}) ;
				}
				 
		}) ;
		
		$('.appAddBtn').click(function(e){
				e.stopPropagation();
				var	appDID = $(this).parents('li').attr('appid') ;
				var me  = this ;
				var seeall = $("#"+appDID+"_seeall").attr("checked") ;
				
				webosService.installApp(appDID,seeall,function(){
					 //更新状态
				    $(me).attr('added','1').addClass('appAdded');
				   
				    $("#"+appDID+"_seeall").remove() ;
				    if(!seeall){
				       $("[for='"+appDID+"_seeall']").remove();
				    }
					
				    getMarketApp(appDID).visibleAll = "visible" ;//seeall?"visible":"" ;
				    getMarketApp(appDID).desktop	= 1 ;
				    
				   //刷新开始菜单
				   $(".menu_list").empty() ;
				   webos.task.startmenu() ;
				}) ;
				  
			   return false ;
		})
	};
	
	//绑定内容点击事件
	function bind_content_add(appDetail ,appId){
		
		$('.appNoBuy .ToBuy').click(function(){
			var	appConID = $(this).parents('.AppDetailMaxBox').attr('appid') ;
			var seeall = $("#"+appConID+"_seeall1").attr("checked") ;
			var me  = this ;
			
			webosService.installApp(appConID,seeall,function(){
				
				$(".appBuyed").show() ;
				$(".appNoBuy").hide() ;
			
				 //更新状态
			    $(me).remove() ;
			   
			    $("#"+appConID+"_seeall1").remove() ;
			    if(!seeall){
			       $("[for='"+appConID+"_seeall1']").remove();
			    }
			    
			    getMarketApp(appConID).visibleAll = seeall?"visible":"" ;
				getMarketApp(appConID).desktop	= 1 ;
		
			   //刷新开始菜单
			   $(".menu_list").empty() ;
			   webos.task.startmenu() ;
			}) ;

			return false;
		});
		
		$('.appBuyed .ToBuy').click(function(){
			var	appConID = $(this).parents('.AppDetailMaxBox').attr('appid') ;
			if( window.confirm("确认卸载该应用吗？") ){
				webosService.uninstallApp(appConID,function(){
					window.location.reload();
				}) ;
			}

			return false;
		}) ;
		
		$(".__price").click(function(){
			$(this).parents(".row").find(".__price").removeClass("current") ;
			$(this).addClass("current") ;
			if($(this).hasClass("__schema")){
				var schemaId = $(this).val() ;
				setAppPrdtPrice(appDetail,schemaId);
			}
			calPrice(appDetail ,appId) ;
		}) ;
		
		calPrice(appDetail ,appId) ;
	};
	
	function calPrice(appDetail ,appId){
		var schemaId = "" ;
		var specValueArray = [] ;
		$(".__price").each(function(){
			if( $(this).hasClass("__schema") ){
				schemaId = $(this).val() ;
			}else if($(this).hasClass("current")){
				specValueArray.push( $(this).attr("value")  ) ;
			}
		}) ;
		var goodPrice = getPrice(schemaId ,specValueArray ,appDetail.productCharges )||getSchemaPrice(schemaId ,appDetail.productCharges) ;
		var appePrice = getAppePrice();
		var totalPrice=goodPrice+appePrice;
		
		
		$(".a_price").empty().html(totalPrice||"-") ;
		$(".a_schema").empty().html( $("select.__price option[value='"+schemaId+"']").text()) ;
	}
	
	function setAppPrdtPrice(appDetail,schemaId){
		$.each(appDetail.appeProducts,function(i,appePrdt){
			var appeDom=$("#"+appePrdt.id);
			var appeCharge=null;
			$.each(appePrdt.productCharges,function(j,prdtCharge){
				appeCharge=null;
				if(prdtCharge.schemaId==schemaId){
					appeCharge=prdtCharge;
					return;
				}
			});
			if(appeCharge==null){
				appeDom.val(0);
			}else{
				appeDom.val(appeCharge.price);
			}			
		});
	}
	
	/**
  * lijing添加
  */ 
	 function getAppDetails(response){
	 	var appDetails=response;
	 	var productCharges=appDetails.productCharges;//获取商品及货品费用项扩展信息
	 	var productSchema=appDetails.productSchema;//获取商品所有计费方案
	 	var productSpec=appDetails.productSpec;//获取商品所有规格
	 }
	 function getSchemaPrice(schemaId,productCharges){
		for(var i=0;i<productCharges.length;++i){
			if(schemaId==productCharges[i].schemaId && (!productCharges[i].goodsId)){
				return productCharges[i].price;
			}
		}
	 };
	 function getGoodsPrice(schemaId,specValueArray,productCharges){
	 	for(var i=0;i<productCharges.length;++i){
			if(schemaId==productCharges[i].schemaId && productCharges[i].goodsId && productCharges[i].goodsId!=""){
				var goodsSpecs=productCharges[i].goodsSpecs;
				var num=0;
				for(var k=0;k<specValueArray.length;++k){
					var specValueId=specValueArray[k];
	  				for(var j=0;j<goodsSpecs.length;++j){
	  					if(specValueId==goodsSpecs[j].specValueId){
	  						num++;
	  						break;
	  					}	
	  				}
				}
				if(num==specValueArray.length){
					return productCharges[i].price;
				}
			}
		}
	 };
	 
	 function getAppePrice(){
	 	var appePrice=0;
	 	$(".__appe").each(function(i){
	 		if($(this).attr("checked")){
	 			appePrice+=parseFloat($(this).val());
	 		}
	 	});
	 	return appePrice;
	 }
	 /**
	  * schemaId:计费方案id
	  * specValueArray:所选择的规格值id数组
	  */
	function getPrice(schemaId,specValueArray,productCharges){
	 	if(!specValueArray || specValueArray.length<1){
	 		return getSchemaPrice(schemaId,productCharges);
	 	}
	 	return getGoodsPrice(schemaId,specValueArray,productCharges);
	 };


	appMarket(); 
})() ;
