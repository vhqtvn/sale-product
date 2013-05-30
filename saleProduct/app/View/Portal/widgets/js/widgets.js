// JavaScript Document
 $(function(){
	search();//搜索条
	tab();
 });
 function tab(){
	$('.tab_menu a').click(function(){
		var target = $(this).attr('target');		
		$(this).addClass('current').parent().siblings().find('a').removeClass('current');
		$(target).show().siblings('.tab_content').hide();
		return false;
	})
 }
 function search(){
	var $search = $('#search_value'),
		keyword = $search.attr('value'),
		tips = '搜索网页',
		$keylist = $('div.key_list'),
		$keyresult = $('ul.key_result'),
		keys = ['我要','我想','申请'],
		business = ['请假','报销','会议室','借款'];
		//检查关键字是否等于业务
		function checkKey(k){
				k = k.toLowerCase();//含字母的字符全部转换成小写
			var khtml ="";
				for( i=0, l=keys.length; i<l; i++){
					if( keys[i] == k){					
							khtml +="<li href='"+'widgets/search_resault.html?wd='+keyword+" 请假'><span class='key_event'><a href='javascript:' id='order_online'><img src='images/icon_edit.png' alt='申请'/>申请</a></span><span class='key_value'>"+k+" 请假</span></li>";
							khtml +="<li href='"+'http://www.baidu.com/s?wd='+keyword+" 报销'><span class='key_event'></span><span class='key_value'>"+k+" 报销</span></li>";
							khtml +="<li href='"+'http://www.baidu.com/s?wd='+keyword+" 会议室'><span class='key_event'></span><span class='key_value'>"+k+" 会议室</span></li>";
							khtml +="<li href='"+'http://www.baidu.com/s?wd='+keyword+" 借款'><span class='key_event'></span><span class='key_value'>"+k+" 借款</span></li>";
					}
				};
				$keyresult.empty().append(khtml);
				$keylist.show();
				$keyresult.find('li').click(function(){
					var khref = $(this).attr('href'),
						ktitle = $(this).find('.key_value').text();
					webos.window.open({
						title: ktitle,
						url: khref,
						size:'middle'
					});
				});
		};
		//获取焦点以及失去焦点的值
		$search.focus(function(e){
			var $input = $(e.target);
			if(keyword == tips){
				$input.attr('value','');
			};
		}).blur(function(e){
			var $input = $(e.target);
			keyword = $input.attr('value');			
		}).click(function(e){
			var $input = $(e.target);
			keyword = $input.attr('value');
			checkKey(keyword);//检查关键字是否等于业务
			e.stopPropagation();
		}).bind('keyup',function(e){
			var $input = $(e.target);
			//获取输入的值并检查是否等于业务
			keyword = $input.attr('value');
			checkKey(keyword);//检查关键字是否等于业务
		})
		//隐藏搜索的列表
		$('body').click(function(){
			$keylist.hide();
		});
		//点击以后的动作		
		$('#search_bt').click(function(){
			$keylist.hide();
			$search.trigger('blur');			
			if(keyword == ''){
				return false;
			}else if(keyword == tips){
				$search.focus();
				return false;
			}else{
				webos.window.open({
					title:'搜索结果',
					url:'widgets/search_resault.html',//'http://www.baidu.com/s?wd='+keyword,
					size:'middle'
				})
			};			
			$keylist.hide();
		});
		//按回车键以后的动作
		$search.keydown(function(event){
			if(event.keyCode == 13){				
				$search.trigger('blur');
				webos.window.open({
					title:'搜索结果',
					url:'widgets/search_resault.html',//'http://www.baidu.com/s?wd='+keyword,
					size:'middle'
				})
			};
			$keylist.hide();
		});
		//在线办理
		$('#order_online').click(function(event){
			$search.trigger('blur');
			webos.window.open({
				title:'业务搜索',
				url:'widgets/search_resault.html',//'http://www.baidu.com/s?wd='+keyword,
				size:'middle'
			});
			$keylist.hide();
			event.stopPropagation();
		});	
 }
 