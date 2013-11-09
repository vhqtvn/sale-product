$(function(){
	Widget.tag() ;
	Widget.product() ;

	Widget.order() ;
	
	//采购小部件
	Widget.puchase() ;
	
	Widget.inplan() ;
	
	//Widget.goods() ;
	
	$(".widget-action").live("click",function(){
		var url = $(this).attr("href") ;
		openCenterWindow(url,1000,650) ;
		return false ;
	})

}) ;

var Widget = {
		tag: function(){
			$.dataservice("model:Widget.TagWidget.load",{},function(result){
				
				for(var o in result){
					var items = result[o] ;
					var n = items.items[0].TYPE_NAME ;
					 $(".tag-dyn").append("<h4>"+n+"</h4>") ;
					
					var ul = $("<ul></ul>").appendTo(".tag-dyn") ;
					$(items.items||[]).each(function(){
						var c = this.C?"("+this.C+")":"(0)" ;
						ul.append("<li><a href='"+contextPath+items.url+"'  target='_blank'>"+this.NAME+c +"</a></li>") ;
					}) ;
				}
			})
		},
		
		puchase:function(){
			$.dataservice("model:Widget.PurchaseWidget.load",{},function(result){
				for(var o in result){
					$("."+o+"-purchase").html( "<a class='widget-action' href='"+contextPath+result[o].url+"' target='_blank'>" +result[o].value+"</a>") ;
				}
			})
		},
		
		order:function(){
			$.dataservice("model:Widget.OrderWidget.load",{},function(result){
				for(var o in result){
					$("."+o+"-order").html( "<a class='widget-action'  href='"+contextPath+result[o].url+"' target='_blank'>" +result[o].value+"</a>") ;
				}
			})
		},
		
		product:function(){
			$.dataservice("model:Widget.ProductDevWidget.load",{},function(result){
				for(var o in result){
					$("."+o+"-product").html( "<a class='widget-action'  href='"+contextPath+result[o].url+"' target='_blank'>" +result[o].value+"</a>") ;
				}
			}) ;
		},
		
		inplan:function(){
			$.dataservice("model:Warehouse.In.loadStatusCount",{},function(result){
				var items = [] ;
				$(result).each(function(){
					var item = {} ;
					for(var o in this){
						var _ = this[o] ;
						for(var o in _){
							item[o] = _[o] ;
						}
					}
					items.push(item) ;
				}) ;
				
				$(".inplan").html("-") ;
				
				$(items).each(function(){
					$("."+this.STATUS+"-inplan").html( "<a class='widget-action'  href='"+contextPath+"/page/forward/Warehouse.In.lists' target='_blank'>" +this.C+"</a>") ;
				}) ;
				
			});
		},
		
		goods:function(){
			$.dataservice("model:Widget.GoodsWidget.load",{},function(result){
				$(result).each(function(){
					var type = this.TYPE ;
					var val = this.c ;
					$("."+type+"-goods").html( "<a class='widget-action'  href='"+contextPath+"/saleProduct/lists' target='_blank'>" +val+"</a>") ;
				}) ;
			}) ;
		}
}