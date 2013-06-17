$(function(){
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				onNodeClick:function(id,text,record){
					if( id == 'root' ){
						currentCategoryId = "" ;
						$(".grid-content").llygrid("reload",getQueryCondition()) ;
					}else{
						currentCategoryId = id ;
						$(".grid-content").llygrid("reload",getQueryCondition()) ;
					}
				}
	       }) ;
			setTimeout(function(){
				
				var querys = getQueryCondition() ;
				querys.accountId = accountId ;
				
				$(".grid-content").llygrid({
					columns:[
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
			           	}},
			           	{align:"center",key:"TITLE",label:"TITLE",width:"10%",forzen:false,align:"left",format:function(val,record){
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
			           	{align:"center",key:"SKU",label:"SKU",width:"8%"},
			           	{align:"center",key:"QUANTITY",label:"库存",width:"6%"},
			           	{align:"center",key:"PRICE",label:"Price",group:"价格",width:"6%"},
			            {align:"center",key:"FEED_PRICE",label:'Price'+editImg+'',group:'价格',width:"6%",format:{type:'editor',fields:['SKU']}},
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
			         ds:{type:"url",content:contextPath+"/grid/query/"+accountId},
					 limit:15,
					 pageSizes:[15,20,30,40],
					 height:350,
					 title:"",
					 indexColumn:false,
					 querys:querys,
					 loadMsg:"数据加载中，请稍候......"
				}) ;
			},200) ;
		
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow(contextPath+"/product/details/"+asin+"/"+accountId,950,650) ;
			}) ;
		
			$(".query-btn").click(function(){
				var querys = getQueryCondition() ;
				querys.type = "price" ;
				querys.accountId = accountId ;
				$(".grid-content").llygrid("reload",querys ) ;	
			}) ;
			
			function getQueryCondition(){
				var querys = $(".query-table").toJson() ;
				querys.reply = 0 ;
				querys.accountId = currentAccountId||'-----';
				querys.type = '' ;

				if(querys.limitArea == 1){
					querys.outAemricanArea = 1 ;
				}else if(querys.limitArea == 2){
					querys.inAemricanArea = '0' ;
				}

				if(querys.fulfillmentChannel == '-'){
					querys.fulfillmentChannelNull = 1 ;
				}else if(querys.fulfillmentChannel){
					querys.fulfillmentChannel = fulfillmentChannel ;
				}

				if(querys.itemCondition == '-'){
					querys.itemContidtionNull = 1 ;
				}else if(querys.itemCondition){
					querys.itemCondition = itemCondition ;
				}
				
				//isPriceQuery isQuantityQuery
				querys.isPriceQuery = 1 ;
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
			
			$(".price-update").click(function(){
				if( window.confirm("是否确认提交价格更新?") ){
					$.ajax({
						type:"post",
						url: contextPath+"/amazonaccount/doAmazonPrice",
						data:{ accountId : currentAccountId },
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							alert("更新请求提交完成！") ;
							$(".grid-content").llygrid("reload") ;
						}
					}); 
				}
				
			}) ;
   	 });