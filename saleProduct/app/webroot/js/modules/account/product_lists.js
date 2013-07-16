
	
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
	$(function(){
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				onNodeClick:function(id,text,record){
					if( id == 'root' ){
						currentCategoryId = "" ;
						currentCategoryText = "" ;
						$(".grid-content").llygrid("reload",getQueryCondition(),
							{ds:{type:"url",content:contextPath+"/grid/query/"+accountId}}) ;	
					}else{
						currentCategoryId = id ;
						currentCategoryText = text ;
						$(".grid-content").llygrid("reload",getQueryCondition(),
							{ds:{type:"url",content:contextPath+"/grid/query/"+accountId}}) ;	
					}
				}
	       }) ;
	       
	       var gridConfig = {
					columns:[
						{align:"center",key:"ID",label:"状态",width:"6%",format:function(val,record){
							var status = record.STATUS ;
							var html = [] ;
							if(record.COUNTRY >= 1){
								html.push("<span title='非美国卖家' class='country-area-flag'></span>") ;	
							}
							
							if(record.WARNING && record.WARNING.indexOf("rights_warning") >= 0 ){
								html.push("<span title='维权预警' class='rights-warning-flag'></span>") ;	
							}
							
							if(record.WARNING && record.WARNING.indexOf("ranking_warning")>=0 ){
								html.push("<span title='排名预警' class='ranking-warning-flag'></span>") ;	
							}
							return html.join("") ;
						}},
						
						{align:"center",key:"ID",label:"操作",width:"6%",format:function(val,record){
							var status = record.STATUS ;
							var html = [] ;
							html.push('<a href="#" class="sale-strategy" val="'+val+'">'+getImage("example.gif","价格调整")+'</a>&nbsp;') ;
							return html.join("") ;
						}},
						{align:"left",key:"SKU",label:"产品SKU",width:"8%"},
						{align:"left",key:"REAL_SKU",label:"货品SKU",width:"8%",format:function(val,record){
								return "<a data-widget='dialog' data-options='{width:1000,height:650}' href='"+contextPath+"/saleProduct/details/"+val+"/sku#ui-tabs-5'>"+(val||"")+"</a>"
						}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||"")+"</a>" ;
			           	}},
			           	{align:"center",key:"P_LOCAL_URL",label:"图片",width:"6%",forzen:false,align:"left",format:{type:'img'}},
			           	{align:"center",key:"P_TITLE",label:"产品标题",width:"10%",forzen:false,align:"left",format:function(val,record){
			           		return "<a href='"+contextPath+"/page/forward/Platform.asin/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
			           	}},
			           	{align:"center",key:"DAY_PAGEVIEWS",label:"每日PV",width:"8%",format:function(val){
			           		if(!val) return '-' ;
			           		return Math.round(val) ;
			           	}},
			           	{align:"center",key:"FULFILLMENT_CHANNEL",label:"销售渠道",width:"8%"},
			           	{align:"center",key:"ITEM_CONDITION",label:"使用程度",width:"8%",format:function(val){
			           		if(val == 1) return "Used" ;
			           		if(val == 11) return 'New' ;
			           		return '' ;
			           	}},
			           	{align:"center",key:"IS_FM",label:"FM产品",width:"8%" },
			           	{align:"center",key:"QUANTITY",label:"库存",width:"6%"},
			           	 {align:"center",key:"PRICE",label:"Price",group:"价格",width:"6%"},
			           {align:"center",key:"SHIPPING_PRICE",label:"Ship",group:"价格",width:"6%"},
			           	{align:"center",key:"FBM_PRICE__",label:"排名",group:"价格",width:"8%",format:function(val,record){
			           		var pm = '' ;
			           		if(record.FULFILLMENT_CHANNEL != 'Merchant') pm = record.FBA_PM  ;
			           		else if( record.ITEM_CONDITION == '1' ) pm =   record.U_PM||'-'  ;
			           		else if( record.IS_FM == 'FM' ) pm =   record.F_PM||'-'  ;
			           		else if( record.IS_FM == 'NEW' ) pm =   record.N_PM||'-'  ;
			           		if(!pm || pm == '0') return '-' ;
			           		return pm ;
			           	}},
			           	{align:"center",key:"FBM_PRICE__",label:"最低价",group:"价格",width:"8%",format:function(val,record){
			           		if(record.FULFILLMENT_CHANNEL != 'Merchant') return record.FBA_PRICE ;
			           		if( record.ITEM_CONDITION == '1' ) return  record.FBM_U_PRICE ;
			           		if( record.IS_FM == 'FM' ) return  record.FBM_F_PRICE ;
			           		if( record.IS_FM == 'NEW' ) return  record.FBM_N_PRICE ;
			           		return "" ;
			           	}},
			           	{align:"center",key:"EXEC_PRICE",label:"最低限价",group:"价格",width:"8%"}
			         ],
			         //ds:{type:"url",content:contextPath+"/amazongrid/product/"+accountId},
			         ds:{type:"url",content:contextPath+"/grid/query"},
					 limit:15,
					 pageSizes:[15,20,30,40],
					 height:420,
					 title:"",
					 indexColumn:false,
					 querys:{accountId:accountId,sqlId:"sql_account_product_list"},
					 loadMsg:"数据加载中，请稍候......",
					 loadAfter:function(){
						 $(".grid-content").uiwidget();
					 }
				} ;
	       
			setTimeout(function(){
				$(".grid-content").llygrid(gridConfig) ;
			},200) ;
			
			$(".edit-account-product").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow(contextPath+"/amazonaccount/editAccountProduct/"+val,600,480) ;
			}) ;
			/*
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				var sku = $(this).attr("sku") ;
				openCenterWindow(contextPath+"/product/details/"+asin+"/"+accountId+"/"+sku,950,650) ;
			}) ;
			*/
			
			$(".sale-strategy").live("click",function(){
				var record = $(this).parents("tr:first").data("record");
				openCenterWindow(contextPath+"/page/forward/Sale.strategy.strategyConfigForListing/"+record.ACCOUNT_ID+"/"+record.SKU+"/"+record.ID ,1100,650) ;
			}) ;
			
			$(".query-btn").click(function(){
				$(".grid-content").llygrid("reload",getQueryCondition(),
					{ds:{type:"url",content:contextPath+"/grid/query/"+accountId}}) ;	
			}) ;
			
			$(".query-reply-btn").click(function(){
				$(".grid-content").llygrid("reload",{accountId:currentAccountId,reply:'1',sqlId:"sql_account_product_double_list"},
					{ds:{type:"url",content:contextPath+"/grid/query/"+accountId}} ) ;	
			}) ;
			
			$(".product-category-btn").click(function(){
				if( currentCategoryId ){
					openCenterWindow(contextPath+"/amazonaccount/assignCategoryProduct/"+accountId+"/"+currentCategoryId,950,650) ;
				}
			}) ;
			
			
			
			function getQueryCondition(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var querys = {} ;
				querys.reply = 0 ;
				querys.accountId = currentAccountId||'-----';
				querys.asin = asin ;
				querys.title = title ;
				querys.quantity1 = $("[name='quantity1']").val() ;
				querys.quantity2 = $("[name='quantity2']").val() ;
				querys.price1 = $("[name='price1']").val() ;
				querys.price2 = $("[name='price2']").val() ;
				//querys.itemCondition = $("[name='itemCondition']").val() ;
				//querys.fulfillmentChannel = $("[name='fulfillmentChannel']").val() ;
				querys.isFM = $("[name='isFM']").val() ;
				var pm = $("[name='pm']").val() ;
				if(pm=='other') pm = 0 ;
				querys.pm = pm ;
				querys.type = '' ;
				querys.test_status = $("[name='test_status']").val()||"" ;
				querys.warning = $("[name='warning']").val()||"" ;
				//querys.limitArea = $("[name='limitArea']").val()||"" ;
				
				var limitArea = $("[name='limitArea']").val()||"" ;
				if(limitArea == 1){
					querys.outAemricanArea = 1 ;
				}else if(limitArea == 2){
					querys.inAemricanArea = '0' ;
				}
				
				var fulfillmentChannel = $("[name='fulfillmentChannel']").val() ;
				if(fulfillmentChannel == '-'){
					querys.fulfillmentChannelNull = 1 ;
				}else if(fulfillmentChannel){
					querys.fulfillmentChannel = fulfillmentChannel ;
				}
				
				var itemCondition = $("[name='itemCondition']").val() ;
				if(itemCondition == '-'){
					querys.itemContidtionNull = 1 ;
				}else if(itemCondition){
					querys.itemCondition = itemCondition ;
				}
				
				//isPriceQuery isQuantityQuery
				if( currentCategoryId=='-'||currentCategoryId=='uncategory'){
					querys.uncategory = 1;
				}else if(currentCategoryId){
					querys.categoryId = currentCategoryId;
				}
				
				querys.sqlId = "sql_account_product_list" ;
				
				return querys ;
			}
			
			$(".lly-grid-cell-input").live("blur",function(){
				var sku = $(this).attr("SKU")||$(this).attr("sku") ;
				var price = "" ;
				var quantity = "" ;
				var key = $(this).attr("key") ;
				var val = $(this).val() ;
					
				$.ajax({
					type:"post",
					url:contextPath+"/amazonaccount/saveAccountProductFeed",
					data:{type:key,sku:sku,value:val,accountId:currentAccountId},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
					}
				}); 
			}) ;
   	 });