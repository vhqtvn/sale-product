$(function(){

	$(".grid-content-detials").llygrid({
		columns:[
			{align:"center",key:"ID",label:"操作", width:"10%",format:function(val,record){
				var html = [] ;
				html.push("<a href='#' class='action delete-items' val='"+val+"'>删除</a>&nbsp;") ;
				return html.join("") ;
			}},
			{align:"center",key:"SKU",label:"Sku",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"QUANTITY",label:"Quantity",width:"20%",forzen:false,align:"left"},
           	{align:"center",key:"MEMO",label:"Memo",width:"40%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return $(window).height() - 170 ;
		 },
		 title:"FBA入库计划明细列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_inbound_plan_local_details_list",planId:planId},
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

