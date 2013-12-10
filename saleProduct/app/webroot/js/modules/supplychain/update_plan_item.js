$(function(){

	$(".save").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			var items = [] ;
			$(".sellerSku").each(function(){
				var sellerSku = $(this).find("[name='sellerSku']").val() ;
				var quantity = $(this).find("[name='quantity']").val() ;
				items.push({sku:sellerSku,quantity:quantity}) ;
			}) ;
			json.items = items ;
			
			$.dataservice("model:SupplyChain.Inbound.updatePlanItem",json,function(result){
				//window.location.reload() ;
			});
		}
	}) ;
	
	$(".to-amazon").click(function(){
		
		if( !$.validation.validate('#personForm').errorInfo ) {
			if(window.confirm("确认提交到Amazon吗？")){
				var json = $("#personForm").toJson() ;
				$.dataservice("model:SupplyChain.Inbound.updatePlanItemToAmazon",json,function(result){
					window.location.reload() ;
				});
			}
		}
	}) ;
	
});

$.fn.serializeJSON = function() {
	var json = {};
	jQuery.map($(this).serializeArray(), function(n, i) {
		json[n['name']] = n['value'];
	});
	return json;
};

