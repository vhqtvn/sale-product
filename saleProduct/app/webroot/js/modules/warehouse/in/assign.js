var currentId = '' ;

$(function(){
	var gridConfig = {
			columns:[
				{align:"center",key:"CHANNEL_NAME",label:"ACCONT",width:"10%"},
				{align:"left",key:"SKU",label:"SKU",width:"15%",format:function(val,record){
					
					return val||record.REL_SKU ;
				}},
				{align:"left",key:"ASIN",label:"ASIN", width:"12%",format:function(val,record){
	           		var memo = record.MEMO||"" ;
	           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
	           	}},
	           	{align:"center",key:"TITLE",label:"TITLE",width:"21%",forzen:false,align:"left",format:function(val,record){
	           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+(val||'')+"</a>" ;
	           	}},
	           	{align:"left",key:"ASSIGN_QUANTITY",label:"分配库存",group:"库存",width:"8%",format:{type:"editor",fields:['ACCOUNT_ID','SKU'],valFormat:function(val,record){
	           		return record.QUANTITY||0 ;
	           	}},render:function(record){
	           		if( record.FULFILLMENT_CHANNEL.indexOf("AMAZON") !=-1){
	           			$(this).find("[key='ASSIGN_QUANTITY']").html("&nbsp;&nbsp;"+(record.QUANTITY||0)) ;
	           			$(this).addClass("alert alert-danger");
	           		}else{
	           			
	           		}
	           	}},
	           	{align:"center",key:"QUANTITY",label:"账户库存",group:"库存",width:"7%"},
	           	{align:"center",key:"UNSHIPPED_NUM",label:"待发货数量",group:"库存",width:"8%"},
	        	{align:"center",key:"ORDER_NUM",label:"订单数量",group:"库存",width:"8%"},
	        	{align:"center",key:"FEED_PRICE",label:'调整价格',group:'价格',width:"8%",format:{type:'editor',fields:['ACCOUNT_ID','SKU']},render:function(record){
	        		if( record.FULFILLMENT_CHANNEL.indexOf("AMAZON") !=-1){
	        			var price = $(".SALE_LOWEST_PRICE_FBM").text();
	           			$(this).find("[key='FEED_PRICE']").attr("lowestPrice",price).attr("placeHolder",">="+$(".SALE_LOWEST_PRICE_FBM").text()) ;
	           		}else{
	           			var price = $(".SALE_LOWEST_PRICE_FBA").text();
	           			$(this).find("[key='FEED_PRICE']").attr("lowestPrice",price).attr("placeHolder",">="+$(".SALE_LOWEST_PRICE_FBA").text()) ;
	           		}
	        	}},
	        	{align:"center",key:"PRICE",label:"账户价格",group:"价格",width:"6%"},
	           	{align:"center",key:"SHIPPING_PRICE",label:"运费",group:"价格",width:"4%"},
	           	{align:"center",key:"FBM_PRICE__",label:"排名",group:"价格",width:"4%",format:function(val,record){
	           		var pm = '' ;
	           		if(record.FULFILLMENT_CHANNEL != 'Merchant') pm = record.FBA_PM  ;
	           		else if( record.ITEM_CONDITION == '1' ) pm =   record.U_PM||'-'  ;
	           		else if( record.IS_FM == 'FM' ) pm =   record.F_PM||'-'  ;
	           		else if( record.IS_FM == 'NEW' ) pm =   record.N_PM||'-'  ;
	           		if(!pm || pm == '0') return '-' ;
	           		return pm ;
	           	}},
	           	{align:"center",key:"FBM_PRICE__",label:"最低价",group:"价格",width:"6%",format:function(val,record){
	           		if(record.FULFILLMENT_CHANNEL != 'Merchant') return record.FBA_PRICE ;
	           		if( record.ITEM_CONDITION == '1' ) return  record.FBM_U_PRICE ;
	           		if( record.IS_FM == 'FM' ) return  record.FBM_F_PRICE ;
	           		if( record.IS_FM == 'NEW' ) return  record.FBM_N_PRICE ;
	           		return "" ;
	           	}},
	           	{align:"center",key:"EXEC_PRICE",label:"最低限价",group:"价格",width:"6%"},
	        	
	        	{align:"center",key:"FULFILLMENT_CHANNEL",label:"销售渠道",width:"8%"}
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:100,
			 pageSizes:[100],
			 height:function(){
			 	return	$(window).height() - 320;
			 },
			 autoWidth:false,
			 title:"",
			 indexColumn:false,
			 querys:{id:realProductId,sqlId:"sql_saleproduct_channel_list"},
			 loadMsg:"数据加载中，请稍候......",
			 loadAfter:function(){
				 var quantity = 0 ;
				 $("td[key='QUANTITY']").each(function(){
					 var record = $(this).parents("tr:first").data("record");
					 if( record.FULFILLMENT_CHANNEL == 'Merchant' ){
						 quantity += parseInt($.trim( $(this).text() )||"0") ;
					 }
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
				 calcPrice() ;
				 
				 $(":input[key='ASSIGN_QUANTITY']").keyup(function(){
					 //计算库存
					 calcAssignedQuantity();
				 }) ;
				 
				 $(":input[key='FEED_PRICE']").keyup(function(){
					 //计算价格
					 calcPrice();
				 }) ;
			 }
		} ;
	$(".grid-content").llygrid(gridConfig) ;
	
	function calcPrice(){
		var isPass = true ;
		$(":input[key='FEED_PRICE']").each(function(){
			var lowestPrice = $(this).attr("lowestPrice");
			var val = $(this).val() ;
			if( lowestPrice && val ){
				lowestPrice = parseFloat(lowestPrice) ;
				val = parseFloat(val) ;
				if( val < lowestPrice ){
					$(this).removeClass("pass").addClass("nopass") ;
					isPass = false ;
				}else{
					$(this).removeClass("nopass").addClass("pass");
				}
			}else{
				$(this).removeClass("nopass").removeClass("pass");
			}
		}) ;
		
		if(!isPass){
			$(".price-btn").attr("disabled","disabled") ;
		}else{
			$(".price-btn").removeAttr("disabled") ;
		}
	}
	
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
	
		$(".price-btn").click(function(){
			if( window.confirm("确认同步价格吗？")){
				var accountMap = {} ;
				 $(":input[key='FEED_PRICE']").each(function(){
					  var assignQuantity = $(this).val() ;
					  var accountId = $(this).attr("ACCOUNT_ID") ;
					  var sku = $(this).attr("SKU") ;
					  
					  var _amap = accountMap[accountId]||[] ;
					  _amap.push( {sku:sku,price:assignQuantity||0} ) ;
					  accountMap[accountId] = _amap ;
				 }) ;
				 
				 $.ajax({
						type:"post",
						url:  contextPath+"/amazon/price" ,
						data: accountMap ,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							alert("执行结束") ;
						}
					});
			}
		}) ;
		
		$(".assgin-btn").click(function(){
			if( window.confirm("确认同步库存吗？")){
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