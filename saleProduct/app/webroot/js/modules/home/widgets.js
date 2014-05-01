$(function(){
	/*
	Widget.tag() ;
	Widget.product() ;

	Widget.order() ;*/
	
	//采购小部件
	Widget.puchase() ;
	Widget.productDev() ;
	Widget.productInfoComplete() ;
	
	//Widget.inplan() ;
	
	//Widget.goods() ;
	
	$(".purchase-widget .flow-node").click(function(){
		var count = $.trim($(this).find(".count").text()) ;
		if( count =='(0)' ) return ;
		openCenterWindow(contextPath+"/page/forward/Purchase.list",1000,650) ;
	}) ;
	
	$(".productDev-widget .flow-node").click(function(){
		var count = $.trim($(this).find(".count").text()) ;
		if( count =='(0)' ) return ;
		openCenterWindow(contextPath+"/page/forward/Product.developer.dev_list",1000,650) ;
	}) ;
	
	$(".productInfoComplete-widget .flow-node").click(function(){
		var count = $.trim($(this).find(".count").text()) ;
		if( count =='(0)' ) return ;
		openCenterWindow(contextPath+"/page/forward/Amazonaccount.product_list_cost_bad",1000,650) ;
	}) ;
	
}) ;

var Widget = {
		puchase:function(){
			$.dataservice("model:Widget.PurchaseWidget.load",{},function(result){
				$(".purchase-widget .flow-node").find(".count").html("(0)") ;
				var map = {} ;
				$(result).each(function(){
					map[this.STATUS+"_"] = parseInt(this.COUNT) ;
				}) ;
				//alert( $.json.encode(map) ) ;
				$(".purchase-widget .flow-node").each(function(){
					var status = $(this).attr("status") ;
					var ss = (status+"").split(",") ;
					var c = 0 ;
					$(ss).each(function(index,item){
						if(!item)return ;
						c = (map[item+"_"]||0) +c;
					}) ;
					$(".purchase-widget .flow-node[status='"+status+"']").find(".count").html("("+c+")") ;
					if( c >0 ){
						$(".purchase-widget .flow-node[status='"+status+"']").find(".count").addClass("has-count") ;
					}else{
						$(".purchase-widget .flow-node[status='"+status+"']").find(".count").removeClass("has-count") ;
					}
				}) ;
			});
			
			
		},
		productDev:function(){
			$.dataservice("sqlId:sql_productDev_new_loadStativcs",{},function(result){
				$(".productDev-widget .flow-node").find(".count").html("(0)") ;
				var map = {} ;
				$(result).each(function(index,item){
					item = item.t ;
					map[item.STATUS+"_"] = parseInt(item.COUNT) ;
				}) ;
				//alert( $.json.encode(map) ) ;
				$(".productDev-widget .flow-node").each(function(){
					var status = $(this).attr("status") ;
					var ss = (status+"").split(",") ;
					var c = 0 ;
					$(ss).each(function(index,item){
						if(!item)return ;
						c = (map[item+"_"]||0) +c;
					}) ;
					$(".productDev-widget .flow-node[status='"+status+"']").find(".count").html("("+c+")") ;
					if( c >0 ){
						$(".productDev-widget .flow-node[status='"+status+"']").find(".count").addClass("has-count") ;
					}else{
						$(".productDev-widget .flow-node[status='"+status+"']").find(".count").removeClass("has-count") ;
					}
				}) ;
			 },{noblock:true});
		},
		productInfoComplete:function(){
			$.dataservice("sqlId:sql_account_product_cost_bad_statics",{},function(result){
				$(".productInfoComplete-widget .flow-node").find(".count").html("(0)") ;
				$(result).each(function(index,item){
					item = item.t ;
					$(".productInfoComplete-widget .flow-node[status='"+item.STATUS+"']").find(".count").html("("+item.COUNT+")") ;
					if( item.COUNT>0  ){
						$(".productInfoComplete-widget .flow-node[status='"+item.STATUS+"']").find(".count").addClass("has-count") ;
					}else{
						$(".productInfoComplete-widget .flow-node[status='"+item.STATUS+"']").find(".count").removeClass("has-count") ;
					}
				});
			},{noblock:true}) ;
		}
}