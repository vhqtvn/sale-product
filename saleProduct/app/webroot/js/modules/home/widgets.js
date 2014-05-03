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
	
	$(".purchase-widget .normal").click(function(){
		var count = $.trim($(this).find(".count").text()) ;
		if( count =='(0)' ) return ;
		openCenterWindow(contextPath+"/page/forward/Purchase.list",1000,650) ;
	}) ;
	
	$(".purchase-widget .audit").click(function(){
		var count = $.trim($(this).find(".audit-count").text()) ;
		if( count =='(0)' ) return ;
		openCenterWindow(contextPath+"/page/forward/Purchase.list_audit",1000,650) ;
	}) ;
	
	$(".purchase-widget .repair-node").click(function(){
		var count = $.trim($(this).find(".audit-count").text()) ;
		if( count =='(0)' ) return ;
		openCenterWindow(contextPath+"/page/forward/Purchase.list_repaire",1000,650) ;
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
				$(".purchase-widget .normal").find(".count").html("(0)") ;
				var map = {} ;
				$(result).each(function(){
					map[this.STATUS+"_"] = parseInt(this.COUNT) ;
				}) ;
				//alert( $.json.encode(map) ) ;
				$(".purchase-widget .normal").each(function(){
					var status = $(this).attr("status") ;
					var ss = (status+"").split(",") ;
					var c = 0 ;
					$(ss).each(function(index,item){
						if(!item)return ;
						c = (map[item+"_"]||0) +c;
					}) ;
					$(".purchase-widget .normal[status='"+status+"']").find(".count").html("("+c+")") ;
					if( c >0 ){
						$(".purchase-widget .normal[status='"+status+"']").find(".count").addClass("has-count") ;
					}else{
						$(".purchase-widget .normal[status='"+status+"']").find(".count").removeClass("has-count") ;
					}
				}) ;
			});
			//sql_purchase_new_list_audit_count
			$(".audit-count").html("(0)") ;
			$.dataservice("sqlId:sql_purchase_new_list_audit_count",{},function(result){
				var c = result[0] ;
				c = c.t ;
				c = c.C ;
				$(".audit-count").html("("+c+")") ;
				if( c >0 ){
					$(".audit-count").addClass("has-count") ;
				}else{
					$(".audit-count").removeClass("has-count") ;
				}
			});
			
			$.dataservice("model:NewPurchaseService.loadRepaireStatics",{},function(result){
				 //$(".grid-content-details").llygrid("reload",{},true) ;
				$(".repair-node").find(".count").html("") ;
				$(result).each(function(){
					$(".repair-node[status='"+this.STATUS+"']").find(".count").html("("+this.COUNT+")") ;//this.COUNT
					if( this.COUNT >0 ){
						$(".repair-node[status='"+this.STATUS+"']").find(".count").addClass("has-count") ;
					}else{
						$(".repair-node[status='"+this.STATUS+"']").find(".count").removeClass("has-count") ;
					}
				}) ;
			 },{noblock:true});
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