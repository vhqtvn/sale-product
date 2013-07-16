$(function(){
			//创建容器
	StrategyConfig.initContainer() ;
	
	$(".save-stragegy").click(function(){
		var strategy = [] ;
		
		$(".value-cell").each(function(){
			var hour = $(this).attr("hour") ;
			var week = $(this).attr("week") ;
			var price = $.trim($(this).find("span").text()) ;
			if(price){
				strategy.push({week:week,hour:hour,price:price}) ;
			}
		}) ;
		
		var json = {} ;
		json.strategy = strategy ;
		json.sku = sku ;
		json.accountId = accountId ;
		
		if(window.confirm("确认配置完成？")){
			$.dataservice("model:SaleStrategy.saveListingConfig",json,function(result){
					window.location.reload();
			});
		}
	}) ;
	
	$(".save-stragegymemo").click(function(){
		var strategy = [] ;

		var json = {} ;
		json.memo = $(".stragegymemo").val() ;
		json.sku = sku ;
		json.accountId = accountId ;
		
			$.dataservice("model:SaleStrategy.saveStragegyMemo",json,function(result){
					window.location.reload();
			});
	}) ;
	
	$(".memo-item").hover(function(){
		$(this).append("<a class='delete'>删除</a>") ;
	},function(){
		$(this).find(".delete").remove();
	}) ;
	
	$("ul li .delete").live("click",function(){
		var memoId = $(this).parents("li:first").attr("memoId");
		if( window.confirm("确认删除吗？") ){
			$.dataservice("model:SaleStrategy.deleteStragegyMemo",{memoId:memoId},function(result){
					window.location.reload();
			});
		}
	}) ;
			
}) ;

var StrategyConfig = {
		initContainer: function(){
			//create head
			var row = ["<tr>"] ;
			for(var i=0 ;i<24 ;i++){
				row.push("<th>"+i+":00</th>") ;
			}
			$(".strategy-details thead").html( row.join("") ) ;

			//create body
			var row = ["<tr>"] ;
			for(var i=0 ;i<24 ;i++){
				row.push("<td class='value-cell' hour='"+i+"'>"+getImage("edit_2.png","配置","edit_config")+"<span></span></td>") ;
			}

			$(".strategy-details tbody").empty() ;
			for(var i=0 ;i<7 ;i++){
				$(row.join("")).appendTo(".strategy-details tbody").find("td").attr("week",i+1) ;
			}
			
			$(".strategy-details tbody td").hover(function(){
				$(this).find(".edit_config").show();
			},function(){
				$(this).find(".edit_config").hide();
			}) ;
			
			$(configs).each(function(){
				var week = this.WEEK ;
				var hour = this.HOUR ;
				var price = this.PRICE ;
				$(".value-cell[hour='"+hour+"'][week='"+week+"']").find("span").text(price) ;
				$(".value-cell[hour='"+hour+"'][week='"+week+"']").addClass("label-success") ;
			}) ;
			
			$(".value-cell").hover(function(){
					var index = this.cellIndex ;
					$(".strategy-details tbody tr").find(" td:eq("+index+")").addClass("alert") ;
					$(this).parent().addClass("alert") ;
			},function(){
				var index = this.cellIndex ;
				$(".strategy-details tbody tr").find(" td:eq("+index+")").removeClass("alert") ;
				$(this).parent().removeClass("alert") ;
			}) ;
			
			
			$(".edit_config").click(function(){
				var me = $(this) ;
				me.parent("td").addClass("label-info");
				
				openCenterWindow(contextPath+"/page/forward/Sale.strategy.setting",
						400,200,function(){
					var result = $.dialogReturnValue()||{} ;
					me.parent("td").find("span").html(result.price||"") ;
					
					if(result.price){
						me.parent("td").removeClass("label-info").addClass("label-success");
					}else{
						me.parent("td").removeClass("label-info label-success");
					}
					
				}, {content:me.parent("td").find("span").text()}) ;
			}) ;
		}
} ;