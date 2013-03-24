/* 
 * jquery.windows.js
 */
(function($) {
    $.fn.windows = function(p) {
        var defaults = {
            data : '',
            config : {},
            startupHandler : function() {},
            shutdownHandler : function() {}
        };
        var params = $.extend(defaults, p);
        var backgroundColor = (params.config.backgroundColor==undefined)?'#000':params.config.backgroundColor;
        var backgroundImage = (params.config.backgroundImage==undefined)?'':params.config.backgroundImage;
        var backgroundOption = (params.config.backgroundOption==undefined)?'center':params.config.backgroundOption;
        var startIcon = (params.config.startIcon==undefined)?'start.png':params.config.startIcon;
        var defaultAppIcon = (params.config.defaultAppIcon==undefined)?'default.png':params.config.defaultAppIcon;
        var startupFullscreen = (params.config.startupFullscreen==undefined)?false:params.config.startupFullscreen;
        var shutdownExitFullscreen = (params.config.shutdownExitFullscreen==undefined)?false:params.config.shutdownExitFullscreen;
        var showFullscreenButton = (params.config.showFullscreenButton==undefined)?false:params.config.showFullscreenButton;
        var showShutdownButton= (params.config.showShutdownButton==undefined)?false:params.config.showShutdownButton;
        params.startupHandler();
        $('.gradient').css('filter','none');
        
        backgroundImage = '/'+fileContextPath+'/app/webroot/js/modules/index/wallpaper.jpg' ;
		startIcon = '/'+fileContextPath+'/app/webroot/js/modules/index/start.png' ;
        var windows_desktop = $('<div>').appendTo('body').css({
            'position':'absolute',
            'top':'0px',
            'right':'0px',
            'bottom':'0px',
            'left':'0px',
            'letterSpacing':'1px',
            'wordSpacing':'1px'
        }).hide().fadeIn(400);
        
        switch(backgroundOption){
            case 'fill':
                windows_desktop.css({
                    'background':backgroundColor+' url("' + backgroundImage + '")',
                    'backgroundSize':'cover'
                });
                break;
            case 'fit':
                windows_desktop.css({
                    'background':backgroundColor+' url("' + backgroundImage + '") no-repeat center center',
                    'backgroundSize':'contain'
                });
                break;
            case 'tile':
                windows_desktop.css({
                    'background':backgroundColor+' url("' + backgroundImage + '")'
                });
                break;
            case 'stretch':
                windows_desktop.css({
                    'background':backgroundColor+' url("' + backgroundImage + '")',
                    'backgroundSize':'100% 100%'
                });
                break;
            case 'center':
            default:
                windows_desktop.css({
                    'background':backgroundColor+' url("' + backgroundImage + '") no-repeat center center'
                });
        }
        
        $('<div>').appendTo(windows_desktop).css({
            'position':'absolute',
            'left':'0px',
            'bottom':'0px',
            'width':'100%',
            'height':'36px',
            'borderTop':'#CCC 2px inset',
            'background':'#000 url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2FlYmNiZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjUwJSIgc3RvcC1jb2xvcj0iIzZlNzc3NCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjUxJSIgc3RvcC1jb2xvcj0iIzBhMGUwYSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiMwYTA4MDkiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+)'
        }).fadeTo(200,0.8);
        
        var windows_taskbar = $('<div>').appendTo(windows_desktop).css({
            'position':'absolute',
            'left':'0px',
            'bottom':'0px',
            'width':'100%',
            'height':'40px',
            'overflow':'hidden'
        });
       
        var windows_start_menu_on = false;
        var windows_start_menu = $('<div>').appendTo('body').css({
            'position':'absolute',
            'left':'2px',
            'bottom':'40px',
            'width':'400px',
            'height':'400px',
            'z-index':'9999'
        }).hover(null,windows_off_start_menu).hide();
        function windows_off_start_menu(){
            windows_start_menu_on = false;
            $(this).fadeOut(400);
        }
        
        $('<div>').appendTo(windows_start_menu).css({
            'position':'absolute',
            'left':'0px',
            'top':'0px',
            'width':'100%',
            'height':'100%',
            'border':'#000 1px solid',
            'borderRadius': '8px',
            'background':'#000 url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIxMDAlIiB5Mj0iMTAwJSI+CiAgICA8c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjOTU5NTk1IiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iNDYlIiBzdG9wLWNvbG9yPSIjMGQwZDBkIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iNTAlIiBzdG9wLWNvbG9yPSIjMDEwMTAxIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iNTMlIiBzdG9wLWNvbG9yPSIjMGEwYTBhIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iNzYlIiBzdG9wLWNvbG9yPSIjNGU0ZTRlIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iODclIiBzdG9wLWNvbG9yPSIjMzgzODM4IiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzFiMWIxYiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ1cmwoI2dyYWQtdWNnZy1nZW5lcmF0ZWQpIiAvPgo8L3N2Zz4=)'
        }).fadeTo(200,0.8);
        
        var windows_categories_menu = $('<ul>').appendTo(windows_start_menu).css({
            'position':'absolute',
            'top':'30px',
            'bottom':'30px',
            'left':'0px',
            'width':'160px',
            'margin':'8px',
            'padding':'4px',
            'borderRadius': '4px',
            'color':'#000',
            'backgroundColor':'rgba(255,255,255,0.8)',
            'listStyle':'none',
            'overflowX':'hidden',
            'overflowY':'auto'
        });

        var windows_applications_menu = $('<ul>').appendTo(windows_start_menu).css({
            'position':'absolute',
            'top':'30px',
            'bottom':'30px',
            'left':'175px',
            'width':'205px',
            'margin':'8px',
            'padding':'4px',
            'borderRadius': '4px',
            'color':'#000',
            'backgroundColor':'rgba(255,255,255,0.8)',
            'listStyle':'none',
            'overflowX':'hidden',
            'overflowY':'auto'
        });

        var windows_menu_buttons = $('<div>').appendTo(windows_start_menu).css({
            'position':'absolute',
            'bottom':'10px',
            'right':'10px'
        });
            
        if(showShutdownButton) {
            $('<button>').appendTo(windows_menu_buttons).attr('title','Shutdown').css({
                'float':'right'
            }).html('<span class="ui-icon ui-icon-power"></span>').button().click(windows_confirm_shutdown);
        }
        function windows_confirm_shutdown() {
            if(confirm("Shutdown?"))
                windows_shutdown();
        }
        function windows_shutdown(){
            $.each(windows_dialog_arr, function(key,val){
                val.dialog('destroy');
            });
            windows_desktop.empty().remove();
            windows_start_menu.empty().remove();
            if(shutdownExitFullscreen){
                windows_fullscreen_exit();
            }
            params.shutdownHandler();
        }

        var docElm = document.documentElement;
        var windows_fullscreen_button = $('<button>').attr('title','FullScreen').css({
            'float':'right'
        }).html('<span class="ui-icon ui-icon-arrow-4-diag"></span>').button().bind('click',windows_fullscreen);
        if(showFullscreenButton){
            if(docElm.requestFullscreen||docElm.mozRequestFullScreen||docElm.webkitRequestFullScreen){
                windows_fullscreen_button.appendTo(windows_menu_buttons);
            }
        }
        function windows_fullscreen(){
            if (window.innerWidth == screen.width && window.innerHeight == screen.height) {
                windows_fullscreen_exit();
            } else {
                windows_fullscreen_start();
            }
        }
        function windows_fullscreen_start(){
            if (docElm.requestFullscreen) {
                docElm.requestFullscreen();
            } 
            else if (docElm.mozRequestFullScreen) {
                docElm.mozRequestFullScreen();
            }
            else if (docElm.webkitRequestFullScreen) {
                docElm.webkitRequestFullScreen();
            }
        }
        function windows_fullscreen_exit(){
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
            else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            }
            else if (document.webkitCancelFullScreen) {
                document.webkitCancelFullScreen();
            }
        }
        if(startupFullscreen){
            windows_fullscreen_start();
        }

        var windows_start_button = $('<div>').attr('title','Start').css({
            'position':'absolute',
            'top':'0px',
            'left':'8px',
            'bottom':'0px',
            'margin':'0px',
            'padding':'0px'
        }).fadeTo(100,0.8).hover(function(){
            $(this).fadeTo(100,1.0);
        },function(){
            $(this).fadeTo(100,0.8);
        }).click(windows_start).append('<img src="'+startIcon+'" width="40" height="40" alt="Start" />');
        function windows_start(){
            if(windows_start_menu_on) {
                windows_start_menu_on = false;
                windows_start_menu.hide();
            } else {
                windows_start_menu_on = true;
                windows_start_menu.fadeIn(200);
            }
        }
        $('body').keydown(function(event) {
            if(event.which==17){
                windows_start();
            }
        });
        
        var windows_taskbar_tasks_div = $('<div>').attr('id','taskbar').appendTo(windows_taskbar).css({
            'position':'absolute',
            'top':'5px',
            'right':'120px',
            'bottom':'1px',
            'left':'60px',
            'margin':'0px',
            'padding':'0px',
            'overflow':'hidden',
            'whiteSpace':'nowrap'
        }).mousemove(function(e){
            if($(this).width() < $(this).find('ul').width()){
                if(e.pageX < 120){
                    $(this).scrollLeft($(this).scrollLeft()-10);
                } else if ($(this).width()-e.pageX < 20) {
                    $(this).scrollLeft($(this).scrollLeft()+10);
                } else {
                }
            } else {
                $(this).scrollLeft(0);
            }
        });

        var windows_taskbar_tasks = $('<ul>').appendTo(windows_taskbar_tasks_div).css({
            'position':'absolute',
            'top':'0px',
            'left':'0px',
            'margin':'0px',
            'padding':'0px'
        });
        
        var windows_clock = $('<div>').appendTo(windows_taskbar).css({
            'position':'absolute',
            'top':'5px',
            'bottom':'5px',
            'right':'12px',
            'width':'80px',
            'height':'32px',
            'padding':'0px 8px 0px 8px',
            'background':'rgba(0,0,0,0.6)',
            'color':'#FFF',
            'textAlign':'center',
            'font':'8pt',
            'lineHeight':'150%',
            'cursor':'default'
        });
        function windows_update_clock(){
            var now = new Date();
            var clock = now.toLocaleTimeString() + '<br>' + now.getDate() + "/" + (now.getMonth()+1) + "/" + now.getFullYear();
            windows_clock.html(clock);
        }
        setInterval(windows_update_clock, 1000);
        
        
        
        //$.getJSON(params.data, function(data) {
        setTimeout(function(){
        	 windows_start_button.appendTo(windows_taskbar);
            $.each(params.data, function(key, val) {
            	
                $('<li>').addClass('windows-menu').css({
                    'margin':'4px',
                    'padding':'4px 8px',
                    'cursor':'pointer',
                    'borderRadius': '2px',
                    'whiteSpace':'nowrap',
                    'font':'8pt',
                    'overflow':'hidden'
                }).click(function(){
                    $('.windows-menu').removeClass('windows-menu-active');
                    $(this).addClass('windows-menu-active');
                    windows_open_category(val.children);
                }).html(val.text).appendTo(windows_categories_menu);
                $.each(val.children, function(key,val){
                    if(val.AUTOSTART=='TRUE'){
                        windows_run_application(val);
                    }
                });
            });
        },200) ;
           
        //});
        
        function windows_open_category(items){
            windows_applications_menu.empty();
            $.each(items, function(key,val){
                $('<li>').addClass('windows-menu').css({
                    'margin':'4px',
                    'padding':'4px 8px',
                    'cursor':'pointer',
                    'borderRadius': '2px',
                    'whiteSpace':'nowrap',
                    'font':'8pt arial,sans-serif',
                    'overflow':'hidden'
                }).click(function(){
                	if( val.children && val.children.length > 0){
                		if( $(this).attr("hasRender") ){
                			if( $(this).find("ul").is(":visible") ){
                				 $(this).find("ul").hide() ;
                			}else{
                				//alert(1122);
                				$(".window-submenu-ul").hide(200);//alert(1122);
                				$(this).find("ul").show() ;
                			}
                			return ;
                		}
                		$(".window-submenu-ul").hide(200);
                		var ul = $("<ul class='window-submenu-ul'>").appendTo($(this)) ;
		            	$(val.children).each(function(index,item){
		            		$('<li>').addClass('windows-menu').css({
			                    'margin':'4px',
			                    'padding':'4px 8px',
			                    'cursor':'pointer',
			                    'borderRadius': '2px',
			                    'whiteSpace':'nowrap',
			                    'font':'8pt arial,sans-serif',
			                    'overflow':'hidden'
			                }).appendTo(ul).click(function(){
			                	windows_start_menu_on = false;
                    			windows_start_menu.delay(100).fadeOut(200);
                    			windows_run_application(item);
			                }).html(this.text) ;
		            	}) ;
		            	$(this).attr("hasRender",true) ;
		            }else{
		            	windows_start_menu_on = false;
                    	windows_start_menu.delay(100).fadeOut(200);
                    	windows_run_application(val);
		            }
                }).html(val.text).appendTo(windows_applications_menu);
            });
        }
        
        var windows_dialog_arr = new Array();
        function windows_run_application(window){
            var window_title = window.text||"";
            var window_width = window.WIDTH||window.width||'900';
            var window_height = window.HEIGHT||window.height||'600' ;
            var window_background = window.background||window.BACKGROUND||'#FFF' ;
            var window_resizable = true ;//(window.RESIZABLE=="TRUE");
            var window_scrolling = (window.SCROLLING=="TRUE")?'yes':(window.SCROLLING=="FALSE")?'no':'auto';
            var window_icon = (window.ICON==undefined)?defaultAppIcon:window.ICON;
            var window_path =  window.URL||window.url||'' ;
            var windows_position_x = Math.floor(Math.random()*(screen.availWidth-window_width));
            var windows_position_y = Math.floor(Math.random()*(screen.availHeight-window_height-100));
            var windows_maximized = false;
            
            var windows_dialog_mask = $('<div>').addClass('dialog_mask').css({
                'position':'absolute',
                'top':'2px',
                'right':'2px',
                'bottom':'2px',
                'left':'2px'
            });
            var windows_taskbar_task = $('<li>').addClass('windows-task').css({
                'float':'left',
                'margin':'0px 1px 0px 1px',
                'padding':'2px',
                'width':'54px',
                'height':'100%',
                'listStyle':'none',
                'cursor':'default',
                'textAlign':'center'
            });
            var windows_dialog = $('<div>').css({
                'margin':'0px',
                'padding':'0px',
                'overflow':'hidden'
            }).dialog({
                'title':window_title,
                'width':window_width,
                'height':window_height,
                'resizable':window_resizable,
                'draggable':true,
                'closeOnEscape':false,
                //'position':[windows_position_x,windows_position_y],
                'dragStart': windows_mask_on,
                'dragStop': windows_mask_off,
                'resizeStart': windows_mask_on,
                'resizeStop': windows_mask_off,
                'create': windows_dialog_create,
                'close': windows_dialog_close,
                'focus': windows_dialog_focus
            })
            
            function windows_mask_on(){
                windows_maximized = false;
                $('.dialog_mask').show();
                windows_dialog.parent().css('opacity','0.5');
            }
            function windows_mask_off(){
                windows_dialog_mask.hide();
                windows_dialog.parent().css('opacity','1.0');
            }
            function windows_dialog_focus(event, ui){
                windows_dialog_blur();
                windows_dialog_mask.hide();
                windows_taskbar_task.addClass('windows-task-active');
                $(this).prev().css('color','#FFF');
            }
            function windows_dialog_blur(){
                $('.dialog_mask').show();
                $('.windows-task').removeClass('windows-task-active');
                $('.ui-dialog-titlebar').css('color','#AAA');
            }
            function windows_dialog_create(event, ui){
                $(this).parent().css('position', 'fixed');
                var windows_dialog_extra = $('<div>').css({
                    'position':'absolute',
                    'right':'23px',
                    'top':'50%'
                });
                windows_dialog_extra.append('<a href="#" class="dialog-minimize ui-dialog-titlebar-min ui-corner-all"><span class="ui-icon ui-icon-minusthick"></span></a>');
                windows_dialog_extra.find('.dialog-minimize').hover(function(){
                    $(this).addClass('ui-state-hover');
                },function(){
                    $(this).removeClass('ui-state-hover');
                }).click(function(){
                    windows_dialog.parent().hide();
                    windows_taskbar_task.removeClass('windows-task-active');
                });
                if(window_resizable){
                    windows_dialog_extra.append('<a href="#" class="dialog-restore ui-dialog-titlebar-rest ui-corner-all"><span class="ui-icon ui-icon-newwin"></span></a>');
                    windows_dialog_extra.find('.dialog-restore').hover(function(){
                        $(this).addClass('ui-state-hover');
                    },function(){
                        $(this).removeClass('ui-state-hover');
                    }).click(function(){
                        if(windows_maximized) {
                            windows_dialog.dialog({
                                'width':window_width,
                                'height':window_height
                            });
                            windows_maximized = false;
                        } else {
                            windows_dialog.dialog({
                                'position':[0,0],
                                'width':$('body').width()-4,
                                'height':$('body').height()-44
                            });
                            windows_maximized = true;
                        }
                    });
                }
                $(this).parent().find('.ui-dialog-titlebar').append(windows_dialog_extra);
            }
            function windows_dialog_close(event, ui){
                windows_taskbar_task.remove();
                $(this).dialog("destroy");
            }
            
            $('<div>').appendTo(windows_dialog).css({
                'position':'absolute',
                'top':'2px',
                'right':'2px',
                'bottom':'2px',
                'left':'2px'
            }).html('<iframe src="'+window_path+'" width="100%" height="100%" scrolling="'+window_scrolling+'" frameborder="0" style="background:'+window_background+';"></iframe>');
            windows_dialog_mask.appendTo(windows_dialog);
            
            windows_dialog_arr.push(windows_dialog);
            windows_taskbar_task.attr('title',window_title).click(function(){
                windows_dialog.parent().show();
                windows_dialog.dialog('moveToTop');
            }).html(window_title).appendTo(windows_taskbar_tasks).css('color','#FFF');
            $('.ui-widget').css({
                'fontSize':'8pt'
            });
        }
    };
})(jQuery);

jQuery.fn._center = function(f) {  
	    return this.each(function(){  
	        var p = f===false?document.body:this.parentNode;  
	        if ( p.nodeName.toLowerCase()!= "body" && jQuery.css(p,"position") == 'static' )  
	            p.style.position = 'relative';  
	        var s = this.style;  
	        s.position = 'absolute';  
	        if(p.nodeName.toLowerCase() == "body")  
	            var w=$(window);  
	        if(!f || f == "horizontal") {
	            //s.left = "0px";  
	           
	            if(p.nodeName.toLowerCase() == "body") {  
	                var clientLeft = w.scrollLeft() - 3 + (w.width() - parseInt(jQuery.css(this,"width")))/2;  
	                s.left = Math.max(clientLeft,0) + "px";  
	            }else if(((parseInt(jQuery.css(p,"width")) - parseInt(jQuery.css(this,"width")))/2) > 0)  
	                s.left = ((parseInt(jQuery.css(p,"width")) - parseInt(jQuery.css(this,"width")))/2) + "px";  
	        }  
	        
	        if(!f || f == "vertical") {  
	            //s.top = "0px";  
	            if(p.nodeName.toLowerCase() == "body") {  
	                var clientHeight = w.scrollTop() - 4 + (w.height() - parseInt(jQuery.css(this,"height")))/2;  
	                s.top = Math.max(clientHeight,0) + "px";  
	            }else if(((parseInt(jQuery.css(p,"height")) - parseInt(jQuery.css(this,"height")))/2) > 0)  
	                s.top = ((parseInt(jQuery.css(p,"height")) - parseInt(jQuery.css(this,"height")))/2) + "px";  
	        }  
	        
	    });  
	};
