$(function(){
	$(".add-sku").click(function(){
		if(shipmentId){
			openCenterWindow(contextPath+"/page/forward/SupplyChain.add_shipment_sku/"+accountId+"/"+shipmentId,450,350,function(){
				window.loaction.reload();
			}) ;
		}
	}) ;
	
	$(".del").click(function(){
		var sellerSku = $(this).closest("tr").find("input[name='sellerSku']").val() ;
		if(window.confirm("确认删除?")){
			var json = $("#personForm").toJson() ;
			json.sku = sellerSku ;
			$.dataservice("model:SupplyChain.Inbound.deletePlanShipmentItem",json,function(result){
				window.location.reload() ;
			});
		}
		return false;
	}) ;

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
			
			$.dataservice("model:SupplyChain.Inbound.updatePlanShipmentItem",json,function(result){
				window.location.reload() ;
			});
		}
	}) ;
});

