$(function(){

	$(".save").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			$.dataservice("model:SupplyChain.Inbound.saveTracking",json,function(result){
				window.location.reload() ;
			});
		}
	}) ;
	
	$(".to-amazon").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var json = $("#personForm").toJson() ;
			$.dataservice("model:SupplyChain.Inbound.saveTrackingToAmazon",json,function(result){
				window.location.reload() ;
			});
		}
	}) ;
	
});

