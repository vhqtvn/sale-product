$(function(){
	
	$("[name='purchaseQuantity']").blur(function(){
		calcInventory() ;
	}) ;
	
	function  getInventoryData(){
		var json = $(".flow-toolbar").toJson() ;
		var qualifiedProductsNum = json.qualifiedProductsNum ;
		var array = [] ;
		$(".rk-product-row").each(function(){
			array.push( $(this).toJson() ) ;
		});
		json.inventoryItem = array ;
		return json ;
	}

	var  calcConfirm = false ;
	function calcInventory(){
		var inventory = getInventoryData() ;
		var array = inventory.inventoryItem ;
		
		var qualifiedProductsNum = inventory.qualifiedProductsNum ;
		
		var total = 0 ;
		$(array).each(function(){
			total += parseInt(this.purchaseQuantity||0) ;
		});

		if(total > qualifiedProductsNum ){
			$("[name='freeQuantity']").val(0) ;
			$(".alert-error").show(200).html("入库数量大于总采购良品数量，请重新确认数量！") ;
			calcConfirm = false ;
		}else{
			$("[name='freeQuantity']").val(qualifiedProductsNum -total ) ;
			$(".alert-error").hide(200) ;
			calcConfirm = true ;
		}
		
		$("[name='purchaseQuantity'],[name='freeQuantity']").each(function(){
			if( $(this).val() && $(this).val() != '0'){
				$(this).removeClass("alert-danger").addClass("alert-success") ;
			}else{
				$(this).val(0).addClass("alert-danger").removeClass("alert-success") ;
			}
		}) ;
		
		//判断仓库是否选择
		if( !$("#warehouseId").val() ){
			$(".alert-error").show(200).html("必须选择入库仓库！") ;
			calcConfirm = false ;
		}
	}
	
	calcInventory() ;
	
	//确认入库
	$(".btn-confirm-in").click(function(){
		calcInventory() ;
		
		if(calcConfirm){
			if(window.confirm("确认列表产品入库吗?")){
				var  actionType = 1 ;//入库
				var  action        = 101 ;//采购入库
				var inventoryData = getInventoryData() ;
				inventoryData.actionType = 1 ;
				inventoryData.action = 101 ;
				
				//return ;
				$.dataservice("model:InventoryNew.purchaseIn",inventoryData,function(result){//确认收货
					if(result){
						alert(result) ;
					}
				});
			} ;
		}
		
		
	}) ;
	
	//确认验货
    $(".btn-validator-product").click(function(){
    	if(window.confirm("确认验货吗?")){
    		var json = $(this).parents("tr:first").next().toJson() ;
    		json.wasteQuantity = json.wasteQuantity||0 ;
			json.genQuantity = json.quantity - (json.wasteQuantity||0) ;
			json.status = 1 ;
			
    		$.dataservice("model:Warehouse.In.doBoxProductStatus",json,function(result){//确认收货
				window.location.reload();
			});
    	}
    }) ;

 });