var currentId = '' ;

$(function(){
	var gridConfig = {
			columns:[
				{align:"center",key:"CHANNEL_NAME",label:"ACCONT",width:"10%"},
				{align:"left",key:"SKU",label:"SKU",width:"15%",format:function(val,record){
					return val||record.REL_SKU ;
				}},
	           	{align:"center",key:"ASSIGN_QUANTITY",label:"分配库存",width:"8%",format:{type:"editor",fields:['ACCOUNT_ID','SKU'],valFormat:function(val,record){
	           		return record.QUANTITY ;
	           	}}},
	           	{align:"center",key:"QUANTITY",label:"账户库存",width:"8%"},
	           	{align:"center",key:"UNSHIPPED_NUM",label:"待发货数量",width:"8%"},
	        	{align:"center",key:"ORDER_NUM",label:"订单数量",width:"8%"},
	           	{align:"left",key:"ASIN",label:"ASIN", width:"12%",format:function(val,record){
	           		var memo = record.MEMO||"" ;
	           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
	           	}},
	           	{align:"center",key:"TITLE",label:"TITLE",width:"21%",forzen:false,align:"left",format:function(val,record){
	           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+(val||'')+"</a>" ;
	           	}}
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:100,
			 pageSizes:[100],
			 height:function(){
			 	return	$(window).height() - 260
			 },
			 autoWidth:false,
			 title:"",
			 indexColumn:false,
			 querys:{id:realProductId,sqlId:"sql_saleproduct_channel_list"},
			 loadMsg:"数据加载中，请稍候......",
			 loadAfter:function(){
				 var quantity = 0 ;
				 $("td[key='QUANTITY']").each(function(){
					 quantity += parseInt($.trim( $(this).text() )||"0") ;
				 }) ;
				 $(".account-quantity").html(quantity) ;
				 
				 quantity = 0 ;
				 $("td[key='UNSHIPPED_NUM']").each(function(){
					 quantity += parseInt($.trim( $(this).text() )||"0") ;
				 }) ;
				 $(".account-will-shipped-quantity").html(quantity) ;
				 
				 var assignableQuantity = parseInt( $.trim($(".total-quantity").text())||0) 
				 		- parseInt( $.trim($(".security-quantity").text())||0)  
				 		-parseInt( $.trim($(".account-will-shipped-quantity").text())||0)  ;
				 $(".assignable-quantity").html(assignableQuantity) ;
				 
				 calcAssignedQuantity() ;
				 
				 $(":input[key='ASSIGN_QUANTITY']").keyup(function(){
					 calcAssignedQuantity();
				 }) ;
			 }
		} ;
	$(".grid-content").llygrid(gridConfig) ;
	
	function calcAssignedQuantity(){
		var quantity = 0 ;
		 $(":input[key='ASSIGN_QUANTITY']").each(function(){
			 quantity += parseInt($.trim( $(this).val())||'0') ;
		 }) ;
		$(".assigned-quantity").html(quantity) ;
		
		var assignableQuantity = parseInt($.trim($(".assignable-quantity").text())||'0') ;
		if( assignableQuantity < quantity  ){
			$(".assigned-quantity,.assignable-quantity").parents(".qt").removeClass("alert alert-success").addClass("alert alert-error") ;
			$(".assgin-btn").attr("disabled",true) ;
		}else{
			$(".assigned-quantity,.assignable-quantity").parents(".qt").removeClass("alert alert-error").addClass("alert alert-success") ;
			$(".assgin-btn").removeAttr("disabled") ;
		}
	}
	
	/*setTimeout(function(){
		$(".grid-content").llygrid(gridConfig) ;
	},200) ;*/
	
	/*var usingGridConfig = {
			columns:[
				{align:"center",key:"CHANNEL_NAME",label:"ACCONT",width:"100"},
	           	{align:"center",key:"QUANTITY",label:"库存数量",width:"100"},
	           	{align:"center",key:"SKU",label:"订单产品SKU",width:"200"},
	           	{align:"left",key:"PAYMENTS_DATE",label:"支付日期", width:"200" },
	           	{align:"center",key:"ORDER_ID",label:"ORDER ID",width:"150" }
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:100,
			 pageSizes:[100],
			 height:function(){
			 	return	$(window).height() - 320
			 },
			// autoWidth:true,
			 title:"",
			 indexColumn:false,
			 querys:{id:realProductId,sqlId:"sql_listLockedOrderForStorage"},
			 loadMsg:"数据加载中，请稍候......"
		} ;
	
	   setTimeout(function(){
		   $(".usinggrid-content").llygrid(usingGridConfig) ;
	   },200) ;*/
			
		/*var tab = $('#details_tab').tabs( {
			tabs:[
				{label:'库存分配',content:"assign-grid"},
				{label:'待处理订单',content:"using-grid"}
			] ,
			//height:'500px',
			select:function(event,ui){
				var index = ui.index ;
				if(index == 1){
					$(".usinggrid-content").llygrid("reload") ;
				}
			}
		} ) ;*/
		
		$(".assgin-btn").click(function(){
			if( window.confirm("确认同步吗？")){
				var accountMap = {} ;
				 $(":input[key='ASSIGN_QUANTITY']").each(function(){
					 
					  var assignQuantity = $(this).val() ;
					  var accountId = $(this).attr("ACCOUNT_ID") ;
					  var sku = $(this).attr("SKU") ;
					  
					  var _amap = accountMap[accountId]||[] ;
					  _amap.push( {sku:sku,quantity:assignQuantity||0} ) ;
					  accountMap[accountId] = _amap ;
				 }) ;
				 
				 $.ajax({
						type:"post",
						url:  contextPath+"/amazon/quantity" ,
						data: accountMap ,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							alert("执行结束") ;
						}
					});
			}
			
		}) ;
 });