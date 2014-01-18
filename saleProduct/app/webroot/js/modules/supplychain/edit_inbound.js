$(function(){
	$(".save-address").click(function(){
		
		if( !$.validation.validate('.address-table').errorInfo ) {
			var json = $(".address-table").toJson() ;
			$.dataservice("model:Meta.saveAddress",json ,function(result){
				//$(".grid-content-detials").llygrid("reload",{},true) ;
				window.location.reload() ;
			});
		}
	}) ;
	
	$(".address-select").change(function(){
		var metaId = $(this).val() ;
		//alert(metaId);
		$.dataservice("model:Meta.getAddressById",{metaId:metaId},function(result){
			renderForm(result , ".address-table") ;
		});
	}) ;
	
	$(".add-address").click(function(){
		$(".address-table").find(":input").val("") ;
	}) ;
	
	//初始化地址选择
	var addressVal = $("#name,[name='name']",".address-table").val() ;
	$(".address-select").find("option").each(function(){
		if($(this).text() == addressVal){
			$(".address-select").val( $(this).val() ) ;
			$("#metaId,[name='metaId']",".address-table").val($(this).val()) ;
		}
	}) ;
	
	function renderForm( result , formSelector ){
		for(var o in result){
			var val = result[o] ;
				o = o.toLowerCase().replace(/\_(\w)/g, function(all, letter){
		          return letter.toUpperCase();
		        });
			result[o] = val ;
		}
		
		for(var o in result){
			$("#"+o+",[name='"+o+"']",formSelector).val(result[o]) ;
		}
	}

	$(".grid-content-detials").llygrid({
		columns:[
					{align:"center",key:"IMAGE_URL",label:"",width:"2%",format:{type:'img'}},
		           	{align:"center",key:"NAME",label:"货品名称",width:"5%"},
	           		{align:"center",key:"SKU",label:"货品SKU",width:"5%",format:function(val,reocrd){
	           			return "<a href='#' product-realsku='"+val+"'>"+val+"</a>" ;
	           		}},
	           		{align:"center",key:"LISTING_SKU",label:"Listing SKU",width:"5%"},
	           		{align:"center",key:"QUANTITY",label:"数量",width:"3%"},
	           		{align:"center",key:"DELIVERY_TIME",label:"供货时间",width:"6%"},
	           		{align:"center",key:"PRODUCT_TRACKCODE",label:"产品跟踪码",width:"6%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					 return $(window).height()-130;
				 },
				 title:"FBA入库Listing列表",
				 autoWidth:true,
				 querys:{sqlId:"sql_warehouse_box_products_byInId",inId:inId},
				 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	$(".delete-items").live('click',function(){
		var record = $(this).closest("tr").data("record") ;
		if(window.confirm("确认删除吗？")){
			$.dataservice("model:SupplyChain.Inbound.deletePlanItem",{itemId: record.ITEM_ID },function(result){
				$(".grid-content-detials").llygrid("reload",{},true) ;
			});
		}
	}) ;
	
	$(".save-plan").click(function(){
		if( !$.validation.validate('#planForm').errorInfo ) {
			var json = $("#planForm").toJson() ;
			$.dataservice("model:SupplyChain.Inbound.savePlan",json,function(result){
				alert(result) ;
				window.location.href = contextPath+"/page/forward/SupplyChain.edit_inbound/"+result ;
			});
		}
	}) ;
	
	$(".save-to-amazon").click(function(){
		var planId= $("#planId").val() ;
		if(!planId){
			alert("请先创建计划！") ;
			return ;
		}
		if( window.confirm("确认到Amazon创建Inbound计划（确认后，改计划将不能更改）？") ){
			$.dataservice("model:SupplyChain.Inbound.saveToAmazon",{planId:planId},function(result){
				//window.location.href = contextPath+"/page/forward/SupplyChain.edit_inbound/"+result ;
				
			});
		}
	}) ;
	
	
	 $(".add-req-sku").click(function(){
			var planId= $("#planId").val() ;
			if(planId){
				openCenterWindow(contextPath+"/page/forward/Warehouse.In.editFBAListing/"+planId,850,550,function(){
					$(".grid-content-detials").llygrid("reload",{},true) ;
				}) ;
			}else{
				alert("请先创建计划！") ;
			}
		}) ;
	
	$(".add-sku").click(function(){
		var planId= $("#planId").val() ;
		if(planId){
			openCenterWindow(contextPath+"/page/forward/SupplyChain.edit_inbound_sku/"+planId,450,350,function(){
				$(".grid-content-detials").llygrid("reload",{},true) ;
			}) ;
		}else{
			alert("请先创建计划！") ;
		}
		
	}) ;
	
});

