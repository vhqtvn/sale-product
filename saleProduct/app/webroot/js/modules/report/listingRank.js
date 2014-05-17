     var type = '4' ;//查询已经审批通过

	 function formatMoney(val){
	 	val = $.trim(val+"") ;
	 	val = val.replace("$","") ;
	 	return $.trim(val) ;
	 }

	$(function(){
			$(".grid-content").llygrid({
				columns:[
				    {align:"center",key:"IMAGE_URL",label:"",width:"3%",forzen:false,align:"center",format:{type:'img'}},
		        	{align:"center",key:"REAL_SKU",label:"货品SKU",width:"8%",forzen:false,align:"left",format:function(val){
		           		return "<a href='#' product-realsku='"+val+"'>"+(val||"")+"</a>" ;
		           	},render:function(record){
		           		if( parseFloat(record.LIMIT_PRICE) < parseFloat(record.LOWEST_PRICE)  ){
		           			$(this).find("td[key='REAL_SKU']").css("background","pink") ;
		           			return ;
		           		}
		           		
		           		if( parseFloat( record.LIMIT_PRICE ) - parseFloat(record.LOWEST_PRICE)  < 0.5  ){
		           			$(this).find("td[key='REAL_SKU']").css("background","blue") ;
		           		}
		           	}},
		            {align:"center",key:"ACCOUNT_NAME",label:"账号",width:"3%",forzen:false,align:"center"},
		           	{align:"center",key:"SELLER_SKU",label:"Listing SKU",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"ASIN",label:"Asin",width:"10%",format:function(val){
		           		return "<a href='#' offer-listing='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"NAME",label:"货品名称",width:"13%",forzen:false,align:"center"},
		           	{align:"center",key:"LIMIT_PRICE",label:"限价",width:"5%"},
		           	{align:"center",key:"LIST_PRICE",label:"List价格",width:"5%"},
		        	{align:"center",key:"LOWEST_PRICE",label:"最低价格",width:"5%"},
		           	{align:"center",key:"TOTAL_SUPPLY_QUANTITY",label:"总库存",width:"5%"},
		           	{align:"center",key:"IN_STOCK_SUPPLY_QUANTITY",label:"在售库存",width:"5%"},
		           	{align:"center",key:"COST_PROFIT",label:"成本/利润/利润率",width:"10%",format:function(val,record){
		           		return "<div id='"+record.ACCOUNT_ID+"_"+record.SELLER_SKU+"_COST'></div>"
		           	}},
		           	{align:"center",key:"SALE_NUM",label:"销量(7/14/30)",width:"10%",format:function(val,record){
		           		return "<div id='"+record.ACCOUNT_ID+"_"+record.SELLER_SKU+"_SALE'></div>"
		           	}},
		        	{align:"center",key:"PURCHASE_ID",label:"进行中采购单",width:"15%",format:function(val,record){
		           		if(!val)return "-" ;
		           		return "<a href='#' purchase-product='"+val+"'>查看采购单<a>";
		           	}}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40,50,70,100],
				 height:function(){
				 	return $(window).height() - 150 ;
				 },
				 title:"排名列表",
				 indexColumn:false,
				 querys:{sqlId:"sql_report_listingRankForFba"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
				 },
				 loadAfter:function(records){
					 var listings = [] ;
					 $(records).each(function(index,item){
						 listings.push({accountId:item.ACCOUNT_ID,listingSku:item.SELLER_SKU}) ;
					 }) ;
					// alert( $.json.encode(listings) ) ;
					 $(".message").html("销售数据计算中......") ;
					 Cost.getListing(listings,function(result){
						 $(".message").html("") ;
						/* [{"accountId":"4",
							 "listingSku":"10000105-F",
							 "costAvalibe":"5.33",
							 "totalCost":"9.50","purchaseCost":"26","totalProfile":"-1.45","profileRate":"-27.21%","logisticsCost":"0","saleString":"0/0/1"},
							 */
						 $(result).each(function(){
							 $("#"+this.accountId+"_"+this.listingSku+"_COST").html( this.costAvalibe+"/"+this.totalProfile+"/"+this.profileRate) ;
							 $("#"+this.accountId+"_"+this.listingSku+"_SALE").html( this.saleString ) ;
						 }) ;
					 }) ;
				 }
			}) ;
   	 });
   	 