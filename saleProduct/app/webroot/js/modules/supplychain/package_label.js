
$(function(){

	$(".to-amazon").click(function(){
		
		if( !$.validation.validate('#personForm').errorInfo ) {
			if(window.confirm("确认下载Package Labels吗？")){
				var json = $("#personForm").toJson() ;
				$.dataservice("model:SupplyChain.Inbound.getPackageLabels",json,function(result){
					window.location.href = contextPath+"/page/forward/SupplyChain.downloadPackageLabel/"+json.accountId+"/"+json.shipmentId ;
				});
			}
		}
	}) ;
	
});

