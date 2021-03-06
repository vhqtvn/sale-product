
	
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
	$(function(){
			var index = 0 ;
			
	       
	       var gridConfig = {
					columns:[
						
						{align:"center",key:"ID",label:"操作",width:"5%",format:function(val,record){
							var status = record.STATUS ;
							var html = [] ;
							html.push('<a href="#" class="edit-listing" val="'+val+'">'+getImage("edit_2.png","Listing编辑")+'</a>&nbsp;') ;
							return html.join("") ;
						}},
			           	{align:"center",key:"P_LOCAL_URL",label:"图片",width:"6%",forzen:false,align:"left",format:{type:'img'}},
						{align:"center",key:"ID",label:"设置",width:"4%",format:function(val,record){
							return '<a href="#" class="setting-ap">'+getImage("edit.png","是否计算需求设置")+'</a>&nbsp;'
						}},
						 {align:"center",key:"IS_ANALYSIS",label:"计算需求", width:"8%",format:function(val,record){
								var html = [] ;
								if(val == 1){
									html.push(  getImage("success.gif","可计算供应需求") ) ;
								}else{
									html.push( getImage("error.gif","不可计算供应需求") ) ;
								}
								return html.join("")  ;
						}},
						{align:"center",key:"RISK_TYPE_NAME",label:"风险类型", width:"6%"},
						{align:"left",key:"SKU",label:"产品SKU",width:"8%"},
						{align:"left",key:"REAL_SKU",label:"货品SKU",width:"10%",format:function(val,record){
							return "<a href='#' product-realsku='"+val+"'>"+(val||"")+"</a>";
						}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' offer-listing='"+record.ASIN+"'>"+(val||"")+"</a>" ;
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
					 querys:{accountId:accountId,sqlId:"sql_account_product_list_noref_product"},
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
			
			$(".setting-ap").live("click",function(){
				var record = $(this).parents("tr:first").data("record");
				var  isRisk = record.IS_RISK||"" ;
				var  riskType  = record.RISK_TYPE||"" ;
				openCenterWindow(contextPath+"/page/forward/Amazonaccount.product_risk/"+record.ID+"/"+isRisk+"/"+riskType , 650,300,function(result,win){
					if(result)$(".grid-content").llygrid("reload",{},true) ;
				},{showType:"dialog"}) ;
			}) ;
			
			
			$(".edit-listing").live("click",function(){
				var record = $(this).closest("tr").data("record") ;
				openCenterWindow(contextPath+"/page/forward/Amazonaccount.edit_listing/"+record.ID,600,480) ;
			}) ;
			
			$(".analysis").live("click",function(){
				
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
				
				querys.sqlId = "sql_account_product_list_noref_product" ;
				
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