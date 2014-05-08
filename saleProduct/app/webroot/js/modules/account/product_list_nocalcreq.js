
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
	$(function(){
			var index = 0 ;
			
	       
	       var gridConfig = {
					columns:[
						{align:"center",key:"ID",label:"设置",width:"4%",format:function(val,record){
							return '<a href="#" class="setting-ap">'+getImage("edit.png","设置")+'</a>&nbsp;'
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
						{align:"center",key:"RISK_LABEL",label:"需求类型", width:"8%",format:function(val,record){
								return record.RISK_LABEL||"" ;
						}},
						{align:"left",key:"ACCOUNT_NAME",label:"账号",width:"8%"},
						{align:"left",key:"SKU",label:"Listing SKU",width:"8%"},
						{align:"left",key:"REAL_SKU",label:"货品SKU",width:"8%",format:function(val,record){
							return "<a href='#' product-realsku='"+val+"'>"+(val||"")+"</a>" ;
						}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		return "<a href='#' offer-listing='"+val+"'>"+val+"</a>" ;
			           	}},
			           	{align:"center",key:"IMAGE_URL",label:"图片",width:"6%",forzen:false,align:"left",format:{type:'img'}},
			           	{align:"center",key:"REAL_NAME",label:"产品标题",width:"10%",forzen:false,align:"left"},
			           	{align:"center",key:"FULFILLMENT_CHANNEL",label:"销售渠道",width:"8%"},
			        	{align:"center",key:"SUPPLY_CYCLE",label:"供应周期",width:"8%" },
			        	{align:"center",key:"REQ_ADJUST",label:"需求调整系数",width:"8%" }
			         ],
			         //ds:{type:"url",content:contextPath+"/amazongrid/product/"+accountId},
			         ds:{type:"url",content:contextPath+"/grid/query"},
					 limit:30,
					 pageSizes:[15,20,30,40],
					 height:function(){
						 return $(window).height()-150;
					 },
					 title:"",
					 indexColumn:false,
					 querys:{sqlId:"sql_account_product_list_nocalcreq"},
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

			
			$(".query-btn").click(function(){
				$(".grid-content").llygrid("reload",getQueryCondition() ) ;	
			}) ;
			
			function getQueryCondition(){
				return $(".query-bar").toJson() ;
			}
			

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

   	 });