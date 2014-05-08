$(function(){

	$(".add-btn").click(function(){
		var templateRow = $(".inventory-tbody-hidden").html() ;
		var row = $(templateRow).appendTo(".inventory-tbody") ;
	}) ;
	
	$(".delete-row").live("click",function(){
		$(this).closest("tr").remove() ;
	});
	
	$(".save-inventory").click(function(){
		var existInventorys = [] ;
		$(".inventory-tbody .inventory-exist-row").each(function(){
			var json = $(this).toJson() ;
			existInventorys.push(json) ;
		}) ;
		var array = [];
		$(".inventory-tbody .inventory-row").each(function(){
			var json = $(this).toJson() ;
			if( (!json.quantity)||json.quantity ==0 ) return ;
			if( (!json.warehouseId)  ) return ;
			if( (!json.accountId) && ( json.inventoryType !=3 &&  json.inventoryType!=4) ) return ;
			array.push(json) ;
		}) ;
		var params = {} ;
		params.realId = realId;
		params.inventorys = array ;
		params.existInventorys = existInventorys;
		$.dataservice("model:InventoryNew.saveInventoryFix",params,function(){
			// window.location.reload();
		 });
		
	});
	
	$("[name='listingSku']").live("change",function(){
		var channel = $(this).find("option:selected").attr("channel");
		if( channel== 'AMAZON_NA' ){
			$(this).closest("tr").find("[name='inventoryType']").val(2).attr("disabled","disabled");
		}else if( channel== 'Merchant' ){
			$(this).closest("tr").find("[name='inventoryType']").val(1).attr("disabled","disabled");
		}else{
			$(this).closest("tr").find("[name='inventoryType']").val('').removeAttr("disabled");
		}
	});
});