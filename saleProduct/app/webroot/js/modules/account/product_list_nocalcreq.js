
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
	$(function(){
			var index = 0 ;
			
	       
	       var gridConfig = {
					columns:[
						{align:"center",key:"ID",label:"标签操作",width:"4%",format:function(val,record){
							var status = record.STATUS ;
							var html = [] ;
							html.push('<a href="#" class="tag-listing" val="'+val+'">'+getImage("edit.png","编辑")+'</a>&nbsp;') ;
							return html.join("") ;
						}},
						{align:"center",key:"IS_ANALYSIS",label:"供应需求", width:"6%",format:function(val,record){
							var html = [] ;
							if(val == 1){
								html.push('<a href="#" class="analysis" val="'+val+'">'+getImage("success.gif","可计算供应需求")+'</a>&nbsp;') ;
							}else{
								html.push('<a href="#" class="analysis" val="'+val+'">'+getImage("error.gif","不可计算供应需求")+'</a>&nbsp;') ;
							}
							return html.join("") ;
						}},
						{align:"left",key:"SKU",label:"产品SKU",width:"8%"},
						{align:"left",key:"REAL_SKU",label:"货品SKU",width:"8%",format:function(val,record){
								return "<a data-widget='dialog' data-options='{width:1000,height:650}' href='"+contextPath+"/saleProduct/details/"+val+"/sku#ui-tabs-4'>"+(val||"")+"</a>"
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
			        	{align:"center",key:"SUPPLY_CYCLE",label:"供应周期",width:"8%" },
			        	{align:"center",key:"REQ_ADJUST",label:"需求调整系数",width:"8%" }
			         ],
			         //ds:{type:"url",content:contextPath+"/amazongrid/product/"+accountId},
			         ds:{type:"url",content:contextPath+"/grid/query"},
					 limit:15,
					 pageSizes:[15,20,30,40],
					 height:function(){
						 return $(window).height()-180;
					 },
					 title:"",
					 indexColumn:false,
					 querys:{accountId:accountId,sqlId:"sql_account_product_list_nocalcreq"},
					 loadMsg:"数据加载中，请稍候......",
					 loadAfter:function(records){
						 $(".grid-content").uiwidget();
						 
						 $realIds = [] ;
							$(records).each(function(){
								this.REAL_ID && $realIds.push(this.REAL_ID) ;
							}) ;
							
					 }
				} ;
	       
			setTimeout(function(){
				$(".grid-content").llygrid(gridConfig) ;
			},200) ;
			
			$(".tag-listing").live("click",function(){
				var record = $(this).parents("tr:first").data("record");
				var record = $(this).parents("tr:first").data("record");
				var entityType = "productRequirementTypeTag" ;
				var entityId = record.ACCOUNT_ID+"$$"+record.SKU+"$$"+record.ASIN ;
				var subEntityType = record.REAL_ID ;
				DynTag.openTagByEntity(entityType,entityId,subEntityType) ;
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
			
			
			$(".analysis").live("click",function(){

				var record = $(this).parents("tr:first").data("record");
				var  isAnalysis = record.IS_ANALYSIS ;
				var json = {} ;
				json.id = record.ID ;
				
				if(isAnalysis == 1  ){
					if(window.confirm("确认取消自动计算供应需求？")){
						json.isAnalysis = 0 ;
						$.dataservice("model:SaleProduct.isAnalysis",json,function(result){
							$(".grid-content").llygrid("reload",{},true) ;
						});
					}
				}else{
					
					if(window.confirm("确认自动计算供应需求？")){
						json.isAnalysis = 1 ;
						$.dataservice("model:SaleProduct.isAnalysis",json,function(result){
							$(".grid-content").llygrid("reload",{},true) ;
						});
					}
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
				
				querys.sqlId = "sql_account_product_list_cost_bad" ;
				
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