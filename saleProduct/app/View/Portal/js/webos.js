// JavaScript Document
var $IE=$.browser.msie;

//第1次加载的时候执行
$(function(){
	//初始化
	webos.init();
})
//窗口改变的时候执行
$(window).resize(function(){
	webos.desk();
	webos.theme.set();
	webos.events.setheight();
	webos.icons.reset();
});

//webos
var webos = new Object;
	webos = {
		init:function(){				//初始化
				//初始化时间
				webos.utils.getTimeNow();
				
				webosService.loadDesktops( function(){
					webos.container.show() ;
					//装载图标
					webos.icons.show();	
					//加载图标以后激活图标右键菜单
					webos.events.smartmenu();
				} ) ;//加载数据
				
				this.headLinker() ;
				
				this.theme.read();			//加载主题背景
				this.desk();  				//加载桌面拖动点击控制
				this.theme.set();
				this.widgets.show();		//加载widget在桌面
				this.task.startmenu();		//激活开始菜单
				this.task.tool.init();		//激活任务栏的右下角工具
				this.toptool();	 			//顶部的事件
				this.events.f11();			//激活f11键盘事件
				//计算桌面高度
				this.events.setheight();	//设置桌面中间图标的高度
				//双击不要选中文本 修复chrome跟safari浏览器
				$('body').bind('dblclick',function(event){					
					webos.events.noselect();
				}); 
		},
		save:function(entity){
			var result = {} ;
			
			$(".deskpage_list li[deskid]").each(function(){
				var deskid = $(this).attr("deskid");
				result[deskid] = {} ;
			}) ;

			$('li.nav_ico').each(function(){
				var appid = $(this).attr('id')||$(this).attr('appid'),
					value = $(this).parent().attr('id').substring(13),
					top = $(this).css('top'),
					left = $(this).css('left');
				//发送数据到webservice 保存图标的位置坐标，供下次载入使用
				var params = { id: appid, left: left, top: top};	
				result[value] = result[value]||{} ;
				result[value].apps = result[value].apps||[] ;
				result[value].apps.push(params) ;
			});
			$('div.widget_wrap').each(function(){
				var widgetid= $(this).find('.widget_tool').next().attr('widgetId')||$(this).find('.widget_tool').next().attr('id'),
					value 	= $(this).parent().find('>ul').attr("id").substring(13),
					left 	= $(window).width() - parseInt($(this).css('left'))+'px',
					top 	= $(this).css('top'),
					width 	= $(this).width() ,
					height 	= $(this).height() - 21 ;
					
				//发送数据到webservice 保存widget的位置坐标，供下次载入使用
				var params = { id: widgetid , left: left, top: top ,width:width,height:height};
				
				//alert(JSON.stringify(params));
				result[value] = result[value]||{} ;
				result[value].widgets = result[value].widgets||[] ;
				result[value].widgets.push(params) ;
			});
			
			webosService.saveConfig( result , entity) ;
		},
		headLinker:function(){
			var configs = webosService.configs.headLinks ;
			$(configs).each(function(index,headlink){
				var clz = headlink.styleClass||headlink.icon ;
				
				var link = $('<a id="_portal_menu_'+index+'" href="javascript:;"><span class="'+clz+'"></span>'+headlink.text+'</a>').appendTo(".header_link") ;
				
				headlink.func = headlink.func||function(){} ;
				$("#_portal_menu_"+index).click(headlink.func);
			}) ;
		},
		container:{//桌面动态容器
			show:function(){
				var index = 0 ;
				var width = 0 ;
				for( var deskId in webosService.cacheDeskAppWidgets){
					if(deskId == 'deskActions') continue ;//桌面操作
					var desk = webosService.cacheDeskAppWidgets[deskId] ;
					index++ ;
					createDeskBar(index,desk,deskId) ;
				} ;
				
				if(index == 0){
					webosService.cacheDeskAppWidgets = {
						"default_desk_id":{
							name:"默认桌面",
							client:true
						}
					}
					webos.container.show() ;
					return ;
					//createDeskBar(1,{name:"默认桌面",id:"default_desk_id"},"default_desk_id") ;
				}
				
				if(window.createDeskbarAfter)createDeskbarAfter() ;
				
				width += 64 ;
				$("#deskpage .deskpage_list li:first").addClass("Current");
				$("#deskpage").width(width).css({"margin-left":"-"+(width/2)+"px"}) ;
				
				function createDeskBar(index,item,deskId){
					$("#deskpage .deskpage_list").append('<li deskid="'+deskId+'">'+index+' <div class="showTip">'+item.name+'</div> </li>') ;
					var html = [] ;
					html.push('<div class="desktop_item">');
					html.push('		 <ul id="desk_ico_wrap'+deskId+'" class="desk_ico_wrap"></ul>');	
					
					html.push('</div>');
					width += 30 ;
					$(".desktop_main").append(html.join("")) ;
				}
			}
		},
		theme:{							//主题的读取
			read:function(){				//主题读取
				webosService.loadTheme( function(theme){
					$("body").css({"background-image":"url("+theme.url+")"});
					webos.utils.cloud_move();				//背景移动的白云
				}) ;
			},
			set:function(){					//设置多桌面的背景跟桌面的标题
				var winWid = $(window).width(),
					winHei = $(window).height(),
					$nowDesk = $('ul.deskpage_list li.Current'),
					index = $nowDesk.index(),
					tip = $nowDesk.find('div.showTip').text(),
					$headTitle = $('div.header_title'),
					$icon = $('.title_name').find('span');
					
				$headTitle.find('strong').text(tip);
				$icon.removeClass().addClass('title_name_icon deskIco'+index);
				
				$('#desk_bg div').width(winWid).height(winHei).eq(index).fadeIn('800').siblings().hide();
				//$('body').css({'background-image':'url(images/desk'+index+'.jpg)'});
			}
		},
		desk:function(){				//桌面控制
				var winWid = $(window).width(),
					winHei = $(window).height();
					//var flag;
					$desk = $("div.desktop_item"),
					deskLength = $desk.length;
					$deskWrap = $("div.desktop_main");
					$left1 = $("div.img_l");
					$right1 = $("div.img_r");
					 
					var $deskPage = $('ul.deskpage_list li'),
						pageLength = $deskPage.length - 1;
					var clickTrue = 0;		
					var lc1 = 0;
					
					$('div.desktop_content').css({'height':winHei - 40});    // 桌面显示区域的高度,40可以分配给底部菜单
					$deskWrap.css({"width": deskLength * winWid,'height': winHei - 40});
					$desk.css({"width": winWid,'height':winHei - 40});
					$('div,p').disableSelection();
					//桌面点击
					function pageClick(){
						$deskPage.click(function(o){
							//先隐藏掉所有打开的窗口
							var index = $(this).index();				
							var scrollLeft = -(index * winWid);
							$(this).addClass('Current').siblings().removeClass('Current');
							$deskWrap.stop().css({'left':scrollLeft});
							clickTrue = 1;
							lc1 = index;
							webos.theme.set();//设置每个桌面背景
							deskDrag();
						});			
						$left1.click(function() {
							if($deskWrap.is(":animated")){
								return false;
							}else{
								//先隐藏掉所有打开的窗口	
								if (lc1 < 1) {
									$('.deskpage_list li:eq('+(lc1)+')').removeClass("Current");
									$('.deskpage_list li:eq('+(lc1 + pageLength)+')').addClass("Current");
									lc1 = lc1 + pageLength;
									$deskWrap.animate( {
										left : -(deskLength-1) * winWid +'px'
									}, 1000);
								} else {
									$('.deskpage_list li:eq('+(lc1)+')').removeClass("Current");
									$('.deskpage_list li:eq('+(lc1-1)+')').addClass("Current");
									lc1--;
									$deskWrap.animate( {
										left : '+='+ winWid +'px'
									}, 1000);
								};
								clickTrue = 1;
								webos.theme.set();//设置每个桌面背景
								return true;
							}
						});			
						$right1.click(function() {
							if($deskWrap.is(":animated")){
								return false;
							}else{
								//先隐藏掉所有打开的窗口				
								if (lc1 >= pageLength) {
									$('ul.deskpage_list li:eq('+(lc1)+')').removeClass("Current");
									lc1 = 0;
									$('ul.deskpage_list li:eq('+(lc1)+')').addClass("Current");

									$deskWrap.animate( {
										left : '0px'
									}, 1000);
								} else {
									$('ul.deskpage_list li:eq('+lc1+')').removeClass("Current");
									$('ul.deskpage_list li:eq('+(lc1+1)+')').addClass("Current");
									lc1++;
									$deskWrap.animate({
										left : '-='+ winWid +'px'
									}, 1000);					
								};
								clickTrue = 1;
								webos.theme.set();//设置每个桌面背景
								return true;
							}
						});	
						//ie6下的展示效果
						if($.browser.msie){
							if($.browser.version == '6.0'){
								$left1.hover(function(){
									$(this).addClass('img_l_hover');
								},function(){
									$(this).removeClass('img_l_hover');
								});
								$right1.hover(function(){
									$(this).addClass('img_r_hover');
								},function(){
									$(this).removeClass('img_r_hover');
								});
							}
						}
					}
					//桌面拖动UI
					function deskDrag(){
						var beginx=0;//拖动div的起始坐标
						var stopx=0;//停止拖动时的坐标		
						var prebeginx=0;//保存上一次拖动个数与宽度乘积为起始坐标，  		
						var picNum=0;//记录本次拖动数量  		
						var prepicNum=1;//记录上一次拖动的总量  		
						var liNum=0;//记录数字位置
						
						$("div.desktop_main").draggable({
							//delay:500,
							refreshPositions:true,
							axis:'x',
							handle:'ul.desk_ico_wrap',
							cancel:'.nav_ico,div.widget_wrap',
							start:function(event,ui){
								if($deskWrap.is(":animated")){
									return false;
								}
								
								//赋于当前拖动起始坐标,如果有点击过分页，直接获取分页的坐标跟页数
								if(clickTrue == 1){
									beginx=this.offsetLeft;
									prepicNum = $('ul.deskpage_list').find('li.Current').index()+1;
								}else{
									beginx=prebeginx;
								};
								picNum=1;
								stopx=0;	
								
							},
							stop:function(event,ui){				
								if(this.offsetLeft>0){					
									$deskWrap.animate({left : '0px'}, 1000);//判断到第1屏						
								}else{
									var deskWidth = pageLength*winWid;
									if(parseInt(this.offsetLeft)<parseInt(-deskWidth)){					
										$deskWrap.stop().animate({left : -(deskWidth)+'px'}, 1000);//判断到最后1屏
									}else{
										stopx=this.offsetLeft;
										var s=beginx-stopx;	
										picNum=Math.round(s/600);//得到移动几个桌面
										if (picNum > 1)	{
											picNum = picNum-1;
										} else if (picNum < -1)	{
											picNum = picNum+1;
										};
																	
										prepicNum+=picNum;	
										prebeginx = -((prepicNum-1)*winWid);//保存本次拖动的个数位移
										var byS=(prepicNum-1)*winWid+stopx;//得到差距离	
										$deskWrap.stop().animate({left : '-='+byS+'px'}, 1000);//再移动一定距离									
										$('ul.deskpage_list li').removeClass("Current");
										liNum=prepicNum-1;
										$('ul.deskpage_list li:eq('+liNum+')').addClass("Current");
										lc1=liNum;					
									}
								}
								webos.theme.set();//为每个桌面设置背景
							}
						});	
					};
					//鼠标经过显示桌面名称
					$(".deskpage_list li").hover(function(event){
						$(this).addClass('hoverTip');
						//$(this).find("div.showTip").show();
					},function(event){
						$(this).removeClass('hoverTip');
						//$(this).find("div.showTip").hide();
					});
					
					// 执行
					pageClick();//页面点击
					deskDrag();//桌面拖动
					
					//键盘事件 如果键盘点击太快，会造成图标错位
					
					$(window).keydown(function(event){
						if(event.keyCode == 37 ){
							$left1.trigger('click');
						}else if(event.keyCode == 39 ){
							$right1.trigger('click');
						}
					});
		},
		widgets:{						//widgets对象的加载
			show:function(){			//根据widgets json参数加载到桌面					
				//widget遍历读取
				webosService.loadWidgets(function(deskAppWidgets){
					for(var desk in deskAppWidgets){
						var _fixLoadTwice = {};
						$( deskAppWidgets[desk].widgets ).each(function(index , widget){
							if( _fixLoadTwice[widget.id] ) return ;
							widget.desk = desk ;
							webos.window.load_widget(widget);
							_fixLoadTwice[widget.id] = true ;
						}) ;
					}
				})
			}
		},
		task:{							//任务栏对象的各类事件
			startmenu:function(){			//开始菜单
				webosService.loadStartMenus(function(menus){
					renderStartMenus(menus,$(".menu_list"),2,{top:23}) ;
					
					//common action
					var $strarlogo = $("#start_logo"),
						$menulogo = $(".menulogo"),
						$menuContain = $(".start_menu") ;

						$strarlogo.click(function(event){
							$menuContain.show("fast");
							event.stopPropagation();
						});
						$menulogo.click(function(){
							$menuContain.hide(10);
						});
						$("body").click(function(){
							$menuContain.hide("fast",function(){
								hideMenu(2) ;
							});
						});
						
					function hideMenu(level){
						if( !$(".menu_level"+level)[0] )return ;
						$(".menu_level"+level).hide(10 , function(){
							hideMenu(level+1) ;
						}) ;
					}	
				}) ;
		
				
				function createSubMenuCon(subClassId){
					var html = [] ;
					html.push(' <div class="startsonMenuContainer '+subClassId+'">') ;
					html.push(' 	<div class="starsonMenu_top"></div>') ;
					html.push(' 	<div class="starsonMenu_main">') ;
					html.push(' 		<ul class="sonmenu_list">') ;
					html.push(' 		</ul>') ;
					html.push(' 	</div>') ;
					html.push(' 	<div class="starsonMenu_bottom"></div>') ;
					html.push(' </div>') ;
					$(html.join("")).appendTo(document.body).hide() ;
				}
				
				function hasChild(menu){
					return (menu.childs && menu.childs.length>0)||menu.childsUrl;
				}
				
				function renderStartMenu(menu , subconEl,level,fixPos,parentId){
					var c = '' ;
					if( hasChild(menu) ){
						c = '<em class="help_arrow"></em>' ;
					}
					var _icon = webos.utils.parseIcon( menu.iconSmall || menu.icon || "icon_applink") ;
					
					//alert(_icon.value);
					
					var img = "" ;
					if( _icon.isClass ){
						img = "<span class='"+_icon.value+"'></span>" ;
					}else{
						img = "<span style='background:url("+_icon.value+") no-repeat;'></span>" ;
					}
					
					$('<li menuid="'+menu.id+'" code="'+menu.code+'" class="startmenu-item" style="overflow:hidden;height:auto;">'
						+img+' '+c+' <a href="###">'+menu.name+'</a></li>')
						.appendTo(subconEl)
						.smartMenu(
							(menu.url&&menu.url !='null')?webosService.configs.startContextMenus:[],
							{
								name:"startmenu"
							}
						) ;
					
					
					menu.categoryId = parentId||"" ;
					
					var copyMenu = {} ;
					for(var o in menu){
						if( typeof o == 'object' ){
							//
						}else{
							copyMenu[o] = menu[o] ;
						}
					}
					
					$("[menuid='"+menu.id+"']").data("entity",copyMenu ) ;
					
					if( hasChild(menu) ){
						$("[menuid='"+menu.id+"']").hover(function(e){
							var me = this ;

							for(var i = level ;i<10 ;i++){
								$(".menu_level"+i).remove() ;
							}
							
							var subMenuClass = menu.id+"_sub" ;
							
							var subMenu = $("."+subMenuClass) ;
							
							if( !subMenu[0] ){
								createSubMenuCon(subMenuClass+" menu_level"+level) ;
								subMenu = $("."+subMenuClass) ;
							}
							
							$( ".sonmenu_list",subMenu).empty() ;
							
							if( menu.childsUrl ){
								$("[menuid='"+menu.id+"']").find(".help_arrow").addClass("wait_small") ;
								
								webosService.loadStartSubMenu(menu.childsUrl,menu.id, menu.code , function(menus){
									if( !menus || menus.length<=0 ){
										//删除箭头
										$("[menuid='"+menu.id+"']").find(".help_arrow").remove() ;
										return ;
									};
									
									$("[menuid='"+menu.id+"']").find(".wait_small").removeClass("wait_small");
									
									renderStartMenus(menus,$(".sonmenu_list",subMenu),level+1,{left:3,top:15},menu.id) ;
									subMenu.show("fast") ;
									var top  = $(me).offset().top - $( ".sonmenu_list",subMenu).height() ;
									var left = $(me).offset().left + $(me).width()+15 ;
									subMenu.css({top:(top+(fixPos.top||0)) +"px" ,left:(left+(fixPos.left||0)) +"px"}).show() ;
								
								}) ;
							}else{
								renderStartMenus(menu.childs,$(".sonmenu_list",subMenu),level+1,{left:3,top:15},menu.id) ;
								subMenu.show("fast") ;
								var top  = $(me).offset().top - $( ".sonmenu_list",subMenu).height() ;
								var left = $(me).offset().left + $(me).width()+15 ;
								subMenu.css({top:(top+(fixPos.top||0)) +"px" ,left:(left+(fixPos.left||0)) +"px"}).show() ;
							
							}
						},function(e){}) ;
			
					}else{
						$("[menuid='"+menu.id+"']").hover(function(e){
							for(var i = level ;i<10 ;i++){
								$(".menu_level"+i).remove() ;
							}
						}) ;
					}
					
					if(menu.url){
						$("[menuid='"+menu.id+"']").click(function(){
							webos.window.open({
									title:menu.name,
									url:menu.url,
									iframe:menu.iframe/*,
									width:730,
									height:440*/
							})
						})
					}else if(menu.func){
						$("[menuid='"+menu.id+"']").click(function(){
							menu.func(menu) ;
						})
					}
				}
				
				function renderStartMenus(menus, subcon,level,fixPos,parentId){
					$(menus).each(function(index,menu){
						renderStartMenu(menu,subcon,level,fixPos,parentId) ;
					}) ;
				}
			},
			tool:{
				  init:function(){
				  	$(webosService.configs.taskActions).each(function(){
				  		var clz = this.icon||("t_"+this.code) ;
				  		$("#task_tool").append('<span class="'+clz+' t_tool_icon" code="'+this.code+'" title="'+this.name+'"></span>') ;
				  	}) ;
				  	
				  	$("#task_tool .t_tool_icon").click(function(){
				  		var code = $(this).attr("code");
				  		$(webosService.configs.taskActions).each(function(){
				  			( this.code == code ) && this.func && this.func() ;
					  	}) ;
				  	}) ;
				  	
					$("#task_tool .t_desk").bind('mouseover',function(){
						$('#navbox').stop().animate({bottom:-10+'px'},500);
					});
				
					//ie6下的鼠标经过展示效果
					if($.browser.msie && $.browser.version == '6.0'){
						$("#task_tool .t_tool_icon").hover(function(){
							var code = $(this).attr("code");
							$(this).addClass('t_'+code+'_hover');
						},function(){
							var code = $(this).attr("code");
							$(this).removeClass('t_'+code+'_hover');
						}) ;
					}
				  }
				},
			showTaskWin:function(winNum){ //显示任务栏存在的窗口
				var o = $("#app_task"+winNum) ;
				var wd = $("#app_window"+winNum) ;
				var dn = o.attr("desknum") ;
				
				webos.events.todesk( dn );
				//设置桌面的名字
				webos.theme.set();
				//获取当前窗口的大小
				var win_wid = o.attr('width'),
					win_hei = o.attr('height'),
					win_y = o.attr('top'),
					win_x = o.attr('left');
				$(o).addClass("ontag").siblings().removeClass("ontag");
				 //还原窗口位置
				wd.show().css({"top":win_y,"left":win_x,"width":win_wid+'px',"height":win_hei+'px'}); 
				 //修正窗口高度
				webos.window.content_fit_height(wd,1,win_hei);						
				webos.window.to_top(wd);
				o.data('isshow',1);
			},
			hideTaskWin:function( winNum ){ //隱藏任务栏存在的窗口
				var o = $("#app_task"+winNum) ;
				var wd = $("#app_window"+winNum) ;
				var dn = o.attr("desknum") ;
				
				if(o.hasClass("ontag")){
					o.removeClass("ontag");
				}
				//触发窗口缩小方法
				wd.find('.win_min').click();
				o.data('isshow',0);
			},showDesk:function(){
				if($(".window:visible").size()>0){
					$(".window:visible").each(function(){
						var win = this ;
						var winNum = $(win).attr('id').substring(10);
						webos.task.hideTaskWin(winNum) ;
					})
				}
			}
		},
		window:{						//窗口对象的各类事件
				num:0,
				w_size:0,
				zindex:99,
				f11_tip:true,
				open:function(option){//窗口打开事件
					//判断窗口是否已经打开，如果已经打开，则使用已经打开的
					var winNum = webos.window.cache.get(option.url) ;
					
					if( winNum ){
						webos.task.showTaskWin(winNum) ;
						return ;
					}
					
					var desktop_num = parseInt($("#deskpage li.Current").text());
					var dft={
					   title:'',
					   url:'',
					   width:800,
					   height:550,
					   iframe:true,
					   scroll:'auto',		//是否有滚动条 auto || hidden
					   x:webos.utils.width()/2-400,
					   y:webos.utils.height()/2-275,
					   resizable:false,
					   winMin:true,
					   winMax:true,
					   size:'middle',	//2种窗口大小 max || middle 
					   clicked:1,
					   string:''
					};
					
					var p = jQuery.extend(dft, option);
					
					//獲取cookie中的
					var winSize = webosService.winSize(option.url) ;
					if(winSize){
						p.width  = winSize.width ;
						p.height = winSize.height ;
						p.size   = winSize.size ;
					}else{
						if(option.width || option.height ){
							//nothing
						}else{
							p.size = "max" ;
						}
					}

					if( p.size != "max" ){
						p.width = Math.min( webos.utils.width() , p.width ) ;
						p.height = Math.min( webos.utils.height()-45 , p.height ) ;
					}
					
					var win_id="app_window"+(this.num+1),task_id="app_task"+(this.num+1);
					
					webos.window.cache.put(option.url ,this.num+1 ) ;
					
					webosService.rebuildUrl = webosService.rebuildUrl||function(url){return url;} ;
					p.url = webosService.rebuildUrl(p.url) ;
					
					var win_html = "";
						win_html += "<div class='window' id='"+win_id+"' key='"+option.url+"'>";//以url作为每个窗口的唯一标识
						win_html += "<div class='win_head'>";
						win_html += "<div class='w_l_t'></div>";
						win_html += "<div class='w_r_t'></div>";
						win_html += "<div class='w_title'>";
						win_html += "<div class='w_t_slider'></div>";
						win_html += "<div class='w_t_bar'>"+this.check_para(0,p.winMin)+this.check_para(1,p.winMax)+"<span class='win_clo'></span>";
						win_html += "</div>";
						win_html += "<h2>"+p.title+"</h2>";
						win_html += "</div></div>";
						win_html += "<div class='win_center' style='position:relative;'>";
						win_html += "<div class='w_l_c'></div>";
						win_html += "<div class='w_r_c'></div>";
						win_html += "<div class='w_content'><div class='w_content_inner' style='overflow:"+p.scroll+"'></div></div>";
						win_html += "</div>";
						win_html += "<div class='win_bottom'><div class='w_l_b'></div><div class='w_r_b'></div><div class='w_c_b'></div></div>";
						win_html +=	"</div>"
					var	window_html= $(win_html).appendTo(".desktop_item:eq("+(desktop_num-1)+")");		    
					var win_obj=$("#"+win_id);
					var xNum = (webos.utils.width() - p.width)/2;
					var yNum = (webos.utils.height() - p.height)/2;
					win_obj.css({"width":p.width,"height":p.height,"left":xNum,"top":yNum,"z-index":(100+this.zindex)});
					win_obj.data("target_url",option.url) ;
					this.content_fit_height(win_obj);
					this.num+=1;
					this.zindex+=1;
					this.w_size+=1;
					if(p.winMin){
						 var task_html=$("<div class='task ontag' id='"+task_id+"' desknum='"+desktop_num+"'>"+p.title+"</div>")
							 .appendTo("#task_opened");
						 var task_obj=$("#"+task_id);
						 
						 var win_wid = win_obj.width();
						 this.bind_task_bar(task_obj,xNum,yNum,win_wid,p.height,desktop_num);
					};
					var win_con=win_obj.find(".w_content_inner");
					var pIfr = null;
					if(p.string!=""){
						win_con.append("<div class='w_alert'>"+p.string+"</div>");
					}
					else{
						//如果iframe=true 和 模式等于窗口，默认用iframe打开，否则用ajax的方式加载
					  if(p.iframe){
							win_con.append("<div class='wait'></div>");
							win_con.append("<iframe id='app_iframe"+(this.num)+"' onload='webos.window.show_iframe(this)' scrolling='auto' class='dis_no' width='100%' height='100%' frameborder='0'></iframe>");
							var ifm=$("#app_iframe"+(this.num));
							pIfr = ifm ;
							//ifm.attr("src",p.url);
					  }else{
						  win_con.append("<div class='wait'></div>");
						  $.ajax({
						  	url: p.url,
						  	cache: false,
						  	success: function(html){
								win_con.html(html);
							}
						  }); 
					  }
					}
					//添加cancel: "div.w_t_bar"修正在iframe上点击关闭按钮无效的bug
					win_obj.draggable({handle:"div.w_title",cancel: "div.w_t_bar",containment: "div.desktop_content", scroll: false,iframeFix:true});
					win_obj.find("div.w_title").click(function(){webos.window.to_top(win_obj)});

					if(p.resizable){
					 win_obj.resizable({
						 minHeight: 150,
						 minWidth: 300,
				 		 handles: 'n, e, s, w , se',
				 		resize: function(event, ui){webos.window.content_fit_height($(this))}
					 }).find(".ui-resizable-se").removeAttr("style");
					};
					
					this.bind_tool_bar(win_obj,p.winMin,p.winMax,xNum,yNum,p.width,p.height,p.size,p.clicked);//新增最大化判断		
					 if(p.iframe){//修复IE下iframe高度Bug
						pIfr.height( win_con.height() );
						setTimeout(function(){
							pIfr.attr("src",p.url);
						},0);
					}
					
					return win_obj ;
				
				},
				cache:{
					put:function(key,value){
						window['webosWindowCache'] = window['webosWindowCache']||{} ;
						window['webosWindowCache'][key] = value ;
					},
					get:function(key){
						window['webosWindowCache'] = window['webosWindowCache']||{} ;
						return window['webosWindowCache'][key] ;
					},
					del:function(key){
						delete window['webosWindowCache'][key] ;
					},
					delAll:function(){
						window['webosWindowCache'] = {} ;
					}
				},
				show_iframe:function(m){	//移除打开iframe以后的loading
					$(m).css({"display":"block"}).prev().remove()
				},		
				maxauto:function(o,x,y,w,h){//自动最大化
					o.animate({"top":y,"left":x,"width":w,"height":h}).data('isfull',0);
					webos.window.content_fit_height(o,1,h);
				},
				check_para:function(n,p){	//窗口最小化最大化的检测
					var for_re='';
					switch(n){
					  case 0:
					   if(p){for_re="<span class='win_min'></span>"}
					   break;
					  case 1:
					   if(p){for_re="<span class='win_max'></span>"}
					   break;
					  default:
					}
					return for_re;
				},
				bind_tool_bar:function(o,mi,ma,x,y,w,h,si,clicked){//最小化最大化的事件
				   var slider=$("div.w_t_slider",o);
				   var win_tool_bar=o.find("div.w_t_bar").find("span");
				   var win_clo=$("span.win_clo",o);
				   var win_max=$("span.win_max",o);
				   var win_min=$("span.win_min",o);
				   var win_title = o.find('div.w_title');
				   var s=250;
				   win_tool_bar.hover(function(){
					 slider.stop();
					 var a=$(this).attr('class');
					 if(a=='win_min'){
					   var r;
					   if(win_max.size()==0){r=43}
					   else{r=64}
					   slider.animate({'right':r},s)
					 }
					 else if(a=='win_max')
					 {
					   slider.animate({'right':43},s)
					 }
					 else{
					   slider.animate({'right':22},s);
					 }
					 
					},function(){
					   slider.stop();
					   slider.animate({'right':22},250);
				   });
				   win_tool_bar.mousedown(function(){
						slider.addClass("w_t_slider_down")
				   }).mouseup(function(){
						slider.removeClass("w_t_slider_down")
				   });
				   win_clo.click(function(e){webos.window.close_window(o,e,clicked)});//新增
				
				   if(ma){
					  win_max.click(function(){webos.window.resize_window(o,x,y,w,h)});
					  // 双击最大或者还原窗口
					  win_title.dblclick(function(){
							win_max.trigger('click');
					   }).disableSelection();
				   };
				   if(mi){
					  win_min.click(function(e){ 
							//记录当前窗口的大小
							var win_wid = $(this).parents('.window').width(),
								win_hei = $(this).parents('.window').height(),
								win_y = $(this).parents('.window').css('top'),
								win_x = $(this).parents('.window').css('left'),
								num = $(this).parents('.window').attr('id').substring(10);
								
								$('#app_task'+num).attr({'width':win_wid,'height':win_hei,'left':win_x,'top':win_y});
								
								webos.window.min_window(o,e);
						})
				   };
				   
				   //如果默认最大化属性为true，打开自动最大化	
					function checkSize(si){
						if(si == 'max'){
							win_max.trigger('click');
						}
					}
					checkSize(si);
				},		
				bind_task_bar:function(o,x,y,w,h,s){	//窗口在任务栏上面的最小化跟还原窗口事件
				   o.siblings().removeClass("ontag");
				   var winNum = o.attr('id').substring(8) ;
				   var wd=$("#app_window"+winNum);
				   
				   o.mousedown(function(){$(this).addClass("ontag")})
					.mouseup(function(){$(this).removeClass("ontag")})
					.click(function(){	
						   //获取当前是第几屏幕
						   var desktop_num = parseInt($("#deskpage li.Current").text());  
							 //如果对象o的data数据isshow为1或者没有isshow的话
						   if(o.data('isshow')==1 || o.data('isshow')== undefined){   
								$(this).removeClass("ontag");
								
								//如果记录下来的桌面不等于当前桌面
								if (s != desktop_num){
									//切换回去窗口的桌面
									webos.events.todesk(s);  
									//设置桌面的名字
									webos.theme.set();
								} else {
									//触发窗口缩小方法
									wd.find('.win_min').click();
									o.data('isshow',0); 
								}
						   }else{
						   		webos.task.showTaskWin(winNum) ;
						   }
				   });
				},
				resize_window: function (o, x, y, w, h) {	//窗口的最大化事件
					var target_url = o.data("target_url") ;
					
					if (o.data('isfull') == 1) {
						webosService.winSize(target_url,{width:w,height:h,size:""}) ;
						o.css({ "top": y, "left": x, "width": w, "height": h }).data('isfull', 0).draggable({ disabled: false, handle: "div.w_title", containment: "div.desktop_content", scroll: false });
						this.content_fit_height(o, 1, h);
					}else {
						webosService.winSize(target_url,{width:w,height:h,size:"max"}) ;
						var win_hei = webos.utils.height() - 30 ;
						o.css({ "top": 0, "left": 0, "width": webos.utils.width(), "height": win_hei }).data("isfull", 1).draggable({ disabled: true });
						
						this.content_fit_height(o, 1, win_hei);
						if (this.f11_tip) {
							$("body").append("<div id='tip_f11'></div>");
							this.f11_tip = false;
							setTimeout(function () { $('#tip_f11').remove() }, 3000);
						}
					}
				},
				min_window:function(o,e){					//窗口的最小化事件
					var t=$("#app_task"+o.attr('id').substring(10));
					if(t.size()!=0){
						t.removeClass("ontag").data('isshow',0);		
						o.data('isfull',0);
						o.css({"top":t.offset().top,"left":t.offset().left,"width":200,"height":100},function(){$(this).hide();webos.window.check_maxz_win()});
						this.content_fit_height(o,1,100);
					};
					if(e){
						e.stopPropagation()
					}
				},
				check_maxz_win:function(){					//
					if($(".window:visible").size()!=0){
						var maxz_obj=$(".window:visible").eq(0);
						var m_index = parseInt(maxz_obj.css("z-index"));
						for(var i=1; i<$(".window:visible").size(); i++){
						  var c_index=parseInt($(".window:visible").eq(i).css("z-index"));
						  if(c_index>m_index){
						   m_index=c_index;
						   maxz_obj=$(".window:visible").eq(i);
						  }
						}
						var ct=$("#app_task"+maxz_obj.attr('id').substring(10));
						if(ct.data('isshow')!=0){
						  ct.addClass("ontag").siblings().removeClass("ontag")
						}
					  }
				},
				close_window:function(o,e,clicked){ 
					//webos.window.cache.del() ;
					
					var key = o.attr("key")  ;
					webos.window.cache.del(key) ;
					
					//如果关闭了窗口，把桌面对应的图标的open属性改为0
					var title = $('div.w_title',o).find('h2').text();
					$(document).find('.nav_ico').each(function(){				
						if($(this).attr('title') == title){
							$(this).attr('open',0);
						}
					});
					$('li.iconApp').attr('open',0);
					
					webos.utils.remove(o) ;
					var t=$("#app_task"+o.attr('id').substring(10));
					t.remove();
					webos.window.check_maxz_win();
					this.w_size-=1;
					if(e){
					 e.stopPropagation()
					}
				},
				close_all:function(){
					if(webos.window.w_size>0){
			  		   //clear cache
			  		   webos.window.cache.delAll() ;
			  			
					   $(".window,.task").remove();
					   webos.window.w_size=0;
					   //重置图标的打开状态
					   $('.deskWraper').find('.nav_ico').attr('open','0');
					}
				},
				content_fit_height:function(o,fun,hei){
					if(fun){
					   o.find("div.win_center").css({"height":(hei-72)});
					}else{
					   o.find("div.win_center").height(o.height()-72);
					}
				},
				to_top:function(o){
					var zindex_original = parseInt(o.css("z-index"));
					var zindex = zindex_original;
					for(var i=0; i<$(".window").size(); i++){
						var cindex=parseInt($(".window").eq(i).css("z-index"));
						if(cindex>zindex){
						   zindex=cindex;
						}
					}
					if(zindex!=zindex_original){
					   zindex+=1;
					   o.css("z-index",zindex);
					   this.zindex=zindex;
					   webos.window.check_maxz_win()
					}	
				},
				check_win_num:function(){					//最多允许打开多少个窗口
				  if(this.w_size<=5){
					 return true
				  }
				  else{
					this.show_alert('对不起，打开太多窗口可能会影响性能！');
					return false;
				  }
				},
				//添加widget 并加载到当前桌面
				show_widget:function(widget){
					widget.desk = $('ul.deskpage_list').find('li.Current').attr("deskid") ;
					webos.window.load_widget(widget);	
				},
				//load_widget:function(id,title,url,w,h,clicked,value,left,top){
				load_widget:function(widget){
					//显示widget 直接加载到当前桌面
					var	desk = widget.desk ? widget.desk : 'desk0';	//widget 加载到哪个桌面
					
					// 发送到json保存的是右边块的宽度，就是 屏幕宽-左边位移，所以加载的时候就是屏幕的宽 - 右边宽度 = 现在的newleft
					var w = widget.width||300 ;
					var h = widget.height||200 ;
					
					var newleft = $(window).width() -parseInt(widget.x||widget.left) ;
					var newheight = parseInt(h) + 21 ;
					var	x = (widget.x||widget.left) ? newleft : '35%';		//widget 的left值
					var	y = (widget.y||widget.top) || '30%';			//widget 的top值
					var url = $.utils.parseUrl(widget.href) ;
					
					var widgetId = widget.id  ,
						widTitle = widget.name;
						
					var deskCurrent =  $("#desk_ico_wrap"+desk).parent() ;
					var _id = widgetId+""+this.zindex ;
						
					var	window_html= $("<div class='widget_wrap' title='"+widTitle+"'>"+
											"<div class='widget_tool'>"+
												"<a href='javascript:' class='widget_close' title='关闭'></a>"+
											"</div>"+
											"<div widgetId='"+widgetId+"' id='"+_id+"'></div>"+									
										"</div>")
							 .appendTo(deskCurrent);
			
				   if( widget.widgetType == 'gadget'|| widget.sourceType=="gadget" ){
				   		if( AppLContainer.isReady ){
				   			LContainer.Rest.getWidget(widget.id , function(widget){
								Widget.render(widget, _id) ;
							}) ;
				   		}else{
				   			AppLContainer.ready(function(){
								LContainer.Rest.getWidget(widget.id , function(widget){
									Widget.render(widget, _id) ;
								}) ;
						    }) ;
				   		}
				   }
	
					
				  $("[widgetId='"+widgetId+"']").css({'width':w+'px','height':h+'px'})
						.parent()
						.css({'width':w+'px','height':newheight+'px','top':y,'left':x,'z-index':this.zindex+1,'position':'absolute'})											 
						.draggable({
							stop:function(event,ui){
								webos.save() ;
							}
						}) ;
					if(widget.widgetType != 'gadget'){
						if( url.indexOf("http://") === 0 || url.indexOf("https://")=== 0){
							//iframe 加载
							$("[widgetId='"+widgetId+"']").css({'width':w+'px','height':h+'px'}).html("<iframe class='widget_iframe' frameborder=0 src='"+url+"' style='width:"+w+"px;height:"+h+"px'></iframe>") ;
						}else {
							$("[widgetId='"+widgetId+"']").css({'width':w+'px','height':h+'px'})
											.load(url) ;
						}	
					}
					 
					
					$("[widgetId='"+widgetId+"']").parent()
						.resizable( {
							handles:'e,s,se',
							stop: function(event, ui) {
								var width  = $("[widgetId='"+widgetId+"']").parent().width() ;
								var height = $("[widgetId='"+widgetId+"']").parent().height() ;
								$("[widgetId='"+widgetId+"'],[widgetId='"+widgetId+"'] .widget_iframe").css({width:width+"px",height:(height-21)+"px"}) ;
								webos.save() ;
							}
						});	//{ handles: 'n, e, s, w' }
						 
						// tool control
						$("[widgetId='"+widgetId+"']").hover(function(){
							//widget工具栏的显示隐藏
							$(this).prev()
									.css({'visibility':'visible'})
										.hover(function(){
											$(this).css({'visibility':'visible'});									
										},function(){
											$(this).css({'visibility':'hidden'});
										});					
						},function(){
							$('div.widget_tool').css({'visibility':'hidden'})
						});
						//关闭当前widget
						$('a.widget_close').unbind("click");
						$('a.widget_close').click(function(){
							$(document).find('li.nav_ico').each(function(){
								if($(this).attr('title')==widTitle){
									$(this).attr('open',0);
								}
							})
							$(this).parent().parent().remove();
							
							webos.save() ;
						});
				},
				show_alert:function(str){
				   this.open({
					title:'系统提示',
					string:str,
					winMax:false,
					winMin:false,
					resizable:false,
					iframe:false,
					width:350,
					height:160,
					x:webos.utils.width()/2-175,
					y:webos.utils.height()/2-80
				  })
				}
				
		},
		icons:{							//图标对象的各类事件
			show:function(){				//图标展示事件
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
						
					var d_o0="",d_o1="",d_o2="",d_o3="",p_o="",c_o="";
					
					for( var desk in webosService.cacheDeskAppWidgets){
						var sHtml="";
						var apps = webosService.cacheDeskAppWidgets[desk].apps ;
						$(apps).each(function(index,app){
								appID = app.id, // 应用ID
								appName = app.name, //应用名字
								//appCategory = app.category, //应用分类
								//appDesktop = app.desktop, //应用分类
								appHref = app.href||"", // 应用目标地址
								//appValue = desk ; // 应用在哪个桌面，如果为空则在第1个桌面
								appSize = app.size||"", // 应用的大小模式
								appWidth = app.width||"", // 应用的宽度
								appHeight = app.height||"", // 应用的高度
								appDesc = app.description||""//, // 应用的描述
								//appImgs = app.thumbnail, // 应用的截图
								//appPublisher = app.publisher, //应用的发布者
								//appPubdate = app.pubdate; //应用的发布时间
								//alert(appName+"  "+appIcon);
								var position = _getDeskAppPosition(index);
								
								appIcon = webos.utils.parseIcon( app.iconLarge||app.icon) ;
								
								 sHtml += "<li class='nav_ico ico_absolute' title='"+appName+"' style='left:" + position.left + "px;top:" + position.top + "px;"+"' appid='"+appID+"' title='"+appDesc+"' href='"+appHref+"' size='"+appSize+"' fixwidth='"+appWidth+"' fixheight='"+appHeight+"'>";
								 if(appIcon.isClass ){
								 	sHtml += "<div class='appicon "+appIcon.value+"'></div>";			
								 }else{
									sHtml += "<div class='appicon' style='background:url("+appIcon.value+") no-repeat;'></div>";		
								 };
								 sHtml += "<h2>"+appName+"</h2>";
								 sHtml += "<div class='app_remove' title='卸载应用'></div>";
								 sHtml +="</li>";
								
						}) ;
						
						$("#desk_ico_wrap"+desk).html(sHtml);
						
						webos.icons.add("#desk_ico_wrap"+desk);
					}

					//排列图标
					webos.icons.reset();
	
					//初始化导航滑动
					//webos.panel.scroll.init();
					//绑定长按出现删除
					webos.icons.remove();
					//绑定图标双击事件
					webos.icons.click();
					
					$(".app_remove").live("click",function(){
						var tgt = $(this).parent() ;
						webos.icons.uninstall(tgt) ;
					}) ;
					
					function _getDeskAppPosition(index){
						var desktop_height = webos.utils.height()-140;  
						if (parseInt((index+1)*81) < desktop_height){
							return {left:0,top: index*81} ;
						} else if (parseInt((index+1)*81) >= desktop_height)	{
							var item_count = parseInt(((index+1)*81) / parseInt(desktop_height-parseInt(desktop_height%83)));  
							return {left:item_count*81,top: parseInt(index%(parseInt(desktop_height / 81)))*81} ;
						}
					}
			},
			sort:function(){				//图标排列事件
				$(".desk_ico_wrap").sortable({
					 delay: 1, 
					 helper: 'clone',
					 cancel: '.iconApp',
					 items: 'li.nav_ico',    
					 start:function(){						
						//clearTimeout(t);
						$(window["_ts"]||[]).each(function(){
							this && clearTimeout(this);
						}) ;
					 },
					 deactivate: function(event, ui) {
						webos.icons.reset();
						webos.save();
					 }
				});
			},
			remove:function(o){				//图标移除事件
					var obj=o?o:$("div.desktop_main .nav_ico");
				   //为ie6下添加ie hover事件
					if($.browser.msie ){
						if($.browser.version == '6.0'){
							$('.iconApp').hover(function(){
								$(this).addClass('nav_ico_hover');
						   },function(){
								$(this).removeClass('nav_ico_hover')
						   })
						}
					}
						
				   webos.icons.sort();
					
				   var t ;
				   
				   $(obj).bind("mousedown",function(event){
				   		//alert(12345678);
						t = setTimeout(function(){
							//alert($(this));
							$(".nav_ico").unbind("click");
							$(obj).addClass("iconhover");
							
							$(".iconApp").unbind();
							$(".iconApp").find("h2").text("退出编辑");
							$(".iconadd").css("backgroundPosition","-305px 0px").parent().addClass("iconhover iconback").bind("click",function(){
								$(obj).removeClass("iconhover");
								$(obj).find(".app_remove").css("display","none");
								$('.iconApp').find(".iconadd").css("backgroundPosition","-260px 0px");
								webos.icons.click();
								webos.icons.exitedit();
								$('.iconApp').removeClass("iconhover iconback");
								$(".iconApp").find("h2").text("添加快捷方式");
							});

							$(obj).find(".app_remove").css("display","block").css("z-index","1") ;
							
							
						},1500);
						window["_ts"] = window["_ts"]||[] ;
						window["_ts"].push(t);
				   });
				  // $(obj).unbind("mousemove");
				   $(obj).bind("mouseup",function(event){
						clearTimeout(t);
						//webos.icons.sort();
				   });
			},
			reset:function(o,num){			//重置图标的位置事件
					var desktop_height = webos.utils.height()-140;                      //获取桌面高度(去掉底部菜单和margin)
					var icon_height = 82;
					var col_num = Math.floor(desktop_height/icon_height);
					$('.desk_ico_wrap').each(function(){
						var desk_one_icon_li = $(this).find('li');						
						for (var i=0;i< desk_one_icon_li.size();i++ ){
							if (parseInt((i+1)*icon_height) <= desktop_height){
								desk_one_icon_li.get(i).style.top = i*icon_height+"px";
								desk_one_icon_li.get(i).style.left ="0px";
							} else if (parseInt((i+1)*icon_height) > desktop_height){
								if((i+1)%col_num==0){
									var item_count= Math.floor((i+1)/col_num)-1;
								}else{
									var item_count= Math.floor((i+1)/col_num);
								}
								desk_one_icon_li.get(i).style.top = parseInt((i%col_num)*icon_height)+"px";
								desk_one_icon_li.get(i).style.left = (item_count)*91+"px";
							
							}
					   }
					})
				   
			},
			click:function(o){				//图标的点击事件
					var obj=o?o:$(".nav_ico");
					   if($.browser.msie ){
							if($.browser.version == '6.0'){
								$(obj).hover(function(){
									$(this).addClass('nav_ico_hover');
							   },function(){
									$(this).removeClass('nav_ico_hover')
							   })
							}
						}
					   $(obj).click(function(){	
							webos.icons.open(this) ;	
					   })
			},
			open: function(iconTgt){
				var entity = $(iconTgt).data("entity")  ;
				if(entity){
					var menu = entity ;
					if(menu.url){
						webos.window.open({
								title:menu.name,
								url:menu.url,
								iframe:menu.iframe,
								width:730,
								height:440
						})
					}else if(menu.func){
						menu.func(menu) ;
					}
				}else{
					if(webos.window.check_win_num()){
						//如果是window 或者 ajax 然后 是固定宽高的时候，获取传进来的宽高度否则采用默认大小
						if($(iconTgt).attr('fixwidth') != '' && $(iconTgt).attr('fixheight') != ''){
						  webos.window.open({
							title:$(iconTgt).find("h2").text(),
							url:$(iconTgt).attr("href"),
							size:$(iconTgt).attr("size"),
							x:webos.utils.width()/2-$(iconTgt).attr("fixwidth")/2,// 用来检测最大化，在函数内部处理
							y:webos.utils.height()/2-$(iconTgt).attr("fixheight")/2,// 用来检测最大化，在函数内部处理
							width:parseInt($(iconTgt).attr("fixwidth")),
							height:parseInt($(iconTgt).attr("fixheight")),
							clicked:$(iconTgt).attr('open')
						  });
						}else{
							webos.window.open({
								title:$(iconTgt).find("h2").text(),
								url:$(iconTgt).attr("href"),
								size:$(iconTgt).attr("size"),
								clicked:$(iconTgt).attr('open')
							  });
						}
					}
					
				}	
			},
			uninstall:function(tgt){
				var isApp = $(tgt).attr('href');
				//通过app应用市场的地址来判断，可能会更改
				if(isApp != 'app_map.html'){
					$(tgt).remove();
					webos.save() ;
					webos.icons.reset();	
				}
			},
			add:function(o){			//添加图标的按钮事件
				var li_last_e = $("<li></li>");
				li_last_e.addClass("ico_absolute iconApp").attr({'which':20,'open':0});
				li_last_e.html("<div class='iconadd'></div><h2>添加快捷方式</h2>")
				li_last_e.appendTo($( o ));
				$( o ).find('.iconApp').click(function(){
					var whichOpen;
					//判断当前是否已经开启，如果是的话就跳转到开启的那个桌面，1次只能开启一个
					if( $(this).attr('open') == '1'){
						var icoH2 = $(this).find('h2').text();
						$('.desktop_item').find('.w_title h2').each(function(){
							var winH2 = $(this).text();
							if(winH2 == icoH2){
								whichOpen = $(this).parents('.desktop_item').index();
							}
						})
						$('.deskpage_list').find('li').eq(whichOpen).trigger('click');
					}else{
						$('.iconApp').attr({'open':1});
						webosService.window.addShortcut() ;
					}
				}) ;
				return ;
			},
			exitedit:function(){			//退出编辑图标的事件
				var add_icon = $(".iconback");
					add_icon.click(function(){
						var whichOpen;
						//判断当前是否已经开启，如果是的话就跳转到开启的那个桌面，1次只能开启一个
						if( $(this).attr('open') == '1'){
							var icoH2 = $(this).find('h2').text();
							$('div.desktop_item').find('div.w_title h2').each(function(){
								var winH2 = $(this).text();
								if(winH2 == icoH2){
									whichOpen = $(this).parents('div.desktop_item').index();
								}
							});
							$('ul.deskpage_list').find('li').eq(whichOpen).trigger('click');
						}else{
							$('li.iconApp').attr({'open':1});
							webosService.window.addApp() ;
						};
					});	
			}
		},
		events:{						//webos对象的自定义方法
			smartmenu:function(){			//绑定桌面跟图标的右键菜单
				var icoMenuData 	= webosService.configs.iconContextMenus ;
				var taskMenuData 	= webosService.configs.taskContextMenus;
				var bodyMenuData 	= webosService.configs.bodyContextMenus ;
				$("body").smartMenu(bodyMenuData, {
					name: "body" 
				});
				$("#taskbar").smartMenu(taskMenuData, {
					name: "task" ,
					afterShow: function(){
						$.smartMenu.hide();
					}
				});
				$(document).find("li.nav_ico").smartMenu(icoMenuData, {
					name: "icon"
				});
			},
			noselect:function(){			//双击不要选中文本 修复chrome跟safari浏览器
				if(document.selection && document.selection.empty) {
					document.selection.empty();
				}else if(window.getSelection) {
					var sel = window.getSelection();
					sel.removeAllRanges();
				}
			},
			setheight:function(){			//设置桌面中间图标的高度
				var winHei = webos.utils.height()-110;
				$(".desk_ico_wrap").height(winHei) ;
			},			
			f11:function(){					//键盘f11方法
				$(window).keydown(function(event){		
					if(event.keyCode=="122" && $(".window:visible").size()!=0){ 	
							var maxz_obj=$(".window:visible").eq(0);
							var m_index = parseInt(maxz_obj.css("z-index"));
							for(var i=1; i<$(".window:visible").size(); i++){
							  var c_index=parseInt($(".window:visible").eq(i).css("z-index"));
							  if(c_index>m_index){
							   m_index=c_index;
							   maxz_obj=$(".window:visible").eq(i);
							  }
							}
							if(maxz_obj.data("isfull")==1){
								var go_f11=function(){
									maxz_obj.css({"top":"0","left":"0","width":webos.utils.width(),"height":webos.utils.height()});
									maxz_obj.find(".win_center").height(webos.utils.height()-72);
								};
								go_f11();
								$(window).unbind("resize",go_f11);
								$(window).bind("resize",go_f11);
							}
							else{
								$(window).unbind("resize",go_f11);
							}
					}
				})
			},
			todesk:function(index){			//跳到窗口的桌面，这个在点击任务栏上面的窗口用到	
				var $deskWrap = $("div.desktop_main");
				var winWid = $(window).width();
				var scrollLeft = -parseInt(index-1) * winWid;
					$('ul.deskpage_list li:eq('+ (index-1) +')').addClass('Current').siblings().removeClass('Current');
					$deskWrap.css({'left':scrollLeft});
			}
		},
		toptool:function(){				//顶部下拉工具栏
			var handle=$("#header_user_info"),box=$("#header_user_main"),s=500;
				//$('#header_user_main').fadeTo('fast',0.4);
				handle.click(function(){
				   if($(this).attr('open')=="1"){      //自定义属性open，当为1的时候，$("#navbox")的left值为0，菜单展开。
					box.stop().animate({top:0},s);
					$(this).animate({top:66+'px'},s).attr('open','0');          //展开后将open设为0
					box.next().find(".arrow_down_icon").css({"background-position":"-228px -35px"});
				   }
				   else{
					box.stop().animate({top:-66},s); //自定义属性open，当为0的时候，$("#navbox")的left值为-274，菜单收缩。
					$(this).animate({top:0},s).attr('open','1');
					box.next().find(".arrow_down_icon").css({"background-position":"-228px -17px"});
				   }
				});	
		}
};


webos.utils = {
	/**   
	 *    
	 * @param {} sURL 收藏链接地址   
	 * @param {} sTitle 收藏标题   
	<a href="javascript:;" onclick="addFavorite(window.location,document.title)">加入收藏</a>
	 */ 
	addFavorite:function(sURL, sTitle) {   
	    try {   
	        window.external.addFavorite(sURL, sTitle);   
	    } catch (e) {   
	        try {   
	            window.sidebar.addPanel(sTitle, sURL, "");   
	        } catch (e) {   
	            alert("加入收藏失败，请使用Ctrl+D进行添加");   
	        }   
	    }   
	} ,
	/**   
	 *    
	 * @param {} obj 当前对象，一般是使用this引用。   
	 * @param {} vrl 主页URL   
	<a href="javascript:;" onclick="setHome(this,window.location)">设为首页</a>
	 */ 
	setHome : function(obj, vrl){
		try {
	        obj.style.behavior = 'url(#default#homepage)';   
	        obj.setHomePage(vrl);   
	    } catch (e) {   
	        if (window.netscape) {   
	            try {   
	                netscape.security.PrivilegeManager   
	                        .enablePrivilege("UniversalXPConnect");   
	            } catch (e) {   
	                alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。");   
	            }   
	            var prefs = Components.classes['@mozilla.org/preferences-service;1']   
	                    .getService(Components.interfaces.nsIPrefBranch);   
	            prefs.setCharPref('browser.startup.homepage', vrl);   
	        }   
	    }   
	},
	//漂浮白云特效
	cloud_move: function(){
		if($.browser.msie ){
			if($.browser.version != '6.0'){
				var start_x = -600;
				var end_x = parseInt(webos.utils.width() + 600);
				var temp_x = 0;
					setInterval(function(){
						$("div.scene_cloud_container > div.scene_cloud").css({left:start_x+temp_x+"px"}).show();
						$("div.scene_cloud_container").show();
						temp_x++;
						if (temp_x > (end_x)){
							temp_x = 0;
							$("div.scene_cloud_container > div.scene_cloud").css("left","0px").hide();
						}
					},50);
			}
		}else{
			var start_x = -600;
			var end_x = parseInt(webos.utils.width() + 600);
			var temp_x = 0;
				setInterval(function(){
					$("div.scene_cloud_container > div.scene_cloud").css({left:start_x+temp_x+"px"}).show();
					$("div.scene_cloud_container").show();
					temp_x++;
					if (temp_x > (end_x)){
						temp_x = 0;
						$("div.scene_cloud_container > div.scene_cloud").css("left","0px").hide();
					}
				},50);
		};
	},getTimeNow:function(){
		//取当前日期、时、分、秒
		today=new Date();
		var hours=today.getHours();
		var minutes=today.getMinutes();
		var seconds=today.getSeconds();
		//将小时按12小时存储
		var timeValue=(hours>=12)?"下午":"上午";
		timeValue+=((hours>12)?hours-12:hours)+"时";
		timeValue+=minutes+"分";
		timeValue+=seconds+"秒";
		
		function initArray() {
			this.length=initArray.arguments.length;
			for(var i=0;i<this.length;i++)
				//依次读取星期日至星期六7个中文名称
				this[i+1]=initArray.arguments[i];
		}
		
		//建立一个数组，存储星期日到星期六7个中文名称
		var d=new initArray (
			"星期日",
			"星期一",
			"星期二",
			"星期三",
			"星期四",
			"星期五",
			"星期六"
		);
		
		$(".title_time:first").append("<p>",today.getFullYear(),"年",today.getMonth()+1,"月",today.getDate(),"日"," ",d[today.getDay()+1],"</p>");
	},parseIcon:function(icon , defaultIcon){
		var _icon = jQuery.utils.parseUrl( $.trim( icon||defaultIcon ) );
		
		if(!_icon)return {isClass:true,value:"icon9"} ;
		
		if( _icon.indexOf(".")!=-1 || _icon.indexOf("/")!=-1 ){//图片地址
			if( _icon.indexOf("http://") === 0 ){
				return {isClass:false,value:_icon} ;
			}else{
				return {isClass:false,value:_icon} ;
			}
		}
		
		return {isClass:true,value:_icon} ;
	},include: function(includePath,file)
    {
        var files = typeof file == "string" ? [file] : file;
        for (var i = 0; i < files.length; i++)
        {
            var name = files[i].replace(/^\s|\s$/g, "");
            var att = name.split('.');			
            var ext = att[att.length - 1].toLowerCase();			
            var isCSS = ext == "css";

            if(isCSS && $("link[href$='"+includePath + name+"']").length == 0 ){
			    var styleTag = document.createElement("link");
			    styleTag.setAttribute('type', 'text/css');
			    styleTag.setAttribute('rel', 'stylesheet');
			    styleTag.setAttribute('href', includePath + name);
			    $("head")[0].appendChild(styleTag);
            }else  if(!isCSS ){
            	$("script[src$='"+includePath + name+"']").remove() ;
			    var scriptTag = document.createElement("script");
			    scriptTag.setAttribute('type', 'text/javascript');
			    scriptTag.setAttribute('language', 'javascript');
			    scriptTag.setAttribute('src', includePath + name);
			    $("head")[0].appendChild(scriptTag);
            }
        }
    },
    height:function(){
    	return $(window).height() ;//document.documentElement.clientHeight
    },
    width:function(){
    	return $(window).width() ;//document.documentElement.clientWidth
    },    
    remove:function(el){
    	if( $(el)[0] && $IE ){
    		var iframes = $(el)[0].getElementsByTagName("iframe");
    		for(var i=0 ;i<iframes.length ;i++){
    			try{
		   	   	  clearIframe(iframes[i],true) ;
    			}catch(e){
    				//alert(">>"+e.message);
    			}
		   	}
    	}
    	$(el).remove() ;
    	
    	function clearIframe(ifr,bool,b2){//清除iframe瀏覽器緩存
		   ifr.src = "about:blank"; 
	   	   
	   	   var frames = ifr.contentWindow.document.getElementsByTagName("iframe");
	   	   for(var i=0 ;i<frames.length ;i++){
	   	   	  clearIframe(frames[i],true) ;
	   	   }
	   	   
		   ifr.contentWindow.document.write(""); 

		   if(!b2){
		   		ifr.removeNode(true);  
		   		ifr = null ;
		   }
		   if(!bool)CollectGarbage();
		}
    },isNull:function(obj){
    	return obj == null || obj == 'undefined' || obj == '' ;
    },
    addDeskShortcut:function(tgt,entityTgt){
    	var app = $(entityTgt).data("entity") ;
    	
    	var	appDID = app.id||"", // 应用ID
			appDName = app.name , //应用名字
			appDHref = app.href||app.url||"", // 应用目标地址
			appDSize = app.size||"", // 应用的大小模式
			appDWidth = app.width||"", // 应用的宽度
			appDHeight = app.height||"", // 应用的高度
			appDRecommend = app.recommend||"", //应用是否推荐
			appDDesc = app.description||"" , // 应用的描述
			appDImgs = app.thumbnail||"" , // 应用的截图
			appDStars = app.stars||"" , // 应用的得分
			appDCount = app.count||"" , // 应用的人气指数
			appDPublisher = app.publisher||"" , //应用的发布者
			appDPubdate = app.pubdate||"" ; //应用的发布时间
			
		var appIcon = webos.utils.parseIcon( app.iconLarge||app.icon||"" ) ;
    	
    	var deskIndex = $('ul.deskpage_list').find('li.Current').index();
		var $currentDesk = $('.desktop_item').eq(deskIndex);
    	
    	//保存到数据库
    	var deskIcon = "";
			deskIcon += "<li fixheight='"+appDHeight+"' fixwidth='"+appDWidth+"' size='"+appDSize+"' href='"+appDHref+"' appid='"+appDID+"' title='"+appDName+"' class='nav_ico ico_absolute'>";
		
		if(appIcon.isClass ){
			deskIcon += "<div class='appicon "+appIcon.value+"'></div><h2>"+appDName+"</h2><div title='卸载应用' class='app_remove'></div>"
			deskIcon += "</li>"
		}else{
			deskIcon += "<div class='appicon' style='background-image: url("+appIcon.value+");'></div><h2>"+appDName+"</h2><div title='卸载应用' class='app_remove'></div>"
			deskIcon += "</li>"
		};
		
		//获取当前列表的添加事件，如果是0，就往桌面添加图标
		var addedNum = $(this).attr('added')||0;
		
		if( addedNum < 1 ){				
			$currentDesk.find('.iconApp').before(deskIcon);
			$(tgt).attr('added','1').addClass('appAdded');
		}

		webos.icons.reset();
		webos.events.smartmenu();//加载右键功能
		webos.icons.click();
		webos.icons.remove();
		webos.save( {type:"front",content:{
			appCode:app.categoryId ,
			code:appDID,
			name:appDName,
			content:appDHref,
			width:appDWidth,
			height:appDHeight,
			icon:appIcon.value,
			description:appDDesc,
			thumbnail:appDImgs
		}} );
    }
} ;
