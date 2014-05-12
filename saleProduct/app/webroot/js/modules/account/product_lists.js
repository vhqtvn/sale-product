
	
	var currentCategoryId = "" ;
	var currentCategoryText = "" ;
	$(function(){
			var index = 0 ;
			//id:'uncategory',text:'未分类产品',memo:'',isExpand:true
			function loadTree11(selector){
				selector.tree({//tree为容器ID
					//source:'array',
					rootId  : 'root',
					rootText : '产品分类',
					expandLevel:2,
					asyn:false,
					CommandName : 'sqlId:sql_saleproduct_account_categorytree',
					recordFormat:true,
					cascadeCheck:false,
					dataFormat:function(data){
						data.push({id:'uncategory',text:'未分类产品',memo:'',isExpand:true});
						return data;
					},
					nodeFormat:function(record){
						if(record.id=='root' ||record.id == 'uncategory') return record ;
						record.text = record.text+"("+record.TOTAL+")"
						return record ;
					},
					params : {
						accountId: accountId
					},
					//data:treeData ,
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
			};
			
			function loadTree(selector){
				selector.tree({//tree为容器ID
					//source:'array',
					//data:treeData ,
					rootId  : 'root',
					rootText : '产品分类',
					expandLevel:2,
					asyn:false,
					CommandName : 'sqlId:sql_saleproduct_account_categorytree',
					recordFormat:true,
					dataFormat:function(data){
						data.push({id:'uncategory',text:'未分类产品',memo:'',isExpand:true});
						return data;
					},
					nodeFormat:function(record){
						if(record.id=='root' ||record.id == 'uncategory') return record ;
						record.text = record.text+"("+record.TOTAL+")"
						return record ;
					},
					params : {
						accountId: accountId
					},
					onNodeClick:function(id,text,record){
						var uncategory = "" ;
						if(id == 'uncategory'){
							id="" ;
							uncategory = 1 ;
						}else{
							uncategory = "" ;
						}
						
						if( id == 'root' ){
							$(".grid-content").llygrid("reload",{categoryId:"",uncategory:uncategory}) ;
						}else{
							$(".grid-content").llygrid("reload",{categoryId:id,uncategory:uncategory}) ;
						}
					}
		       }) ;
			}
		
			loadTree(  $('#default-tree_0') ) ;
			
	       var gridConfig = {
					columns:[
						/*{align:"center",key:"ID",label:"状态",width:"6%",format:function(val,record){
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
						{align:"center",key:"REAL_ID",label:"动态", width:"6%",format:function(val,record){
							if(!val) return "" ;
							return "<span class='pi-status hide popover-pl'  realId='"+(val||"")+"'  title=''>查看</span>" ;
						}},*/
						{align:"center",key:"ID",label:"操作",width:"5%",format:function(val,record){
							var status = record.STATUS ;
							var html = [] ;
							html.push('<a href="#" class="edit-listing" val="'+val+'">'+getImage("edit_2.png","Listing编辑")+'</a>&nbsp;') ;
							//html.push('<a href="#" class="sale-strategy" val="'+val+'">'+getImage("example.gif","价格调整")+'</a>&nbsp;') ;
							//html.push('<a href="#" class="category-set popover-pl top" val="'+val+'">'+getImage("collapse-all.gif","设置分类")+'</a>&nbsp;') ;
							//html.push('<a href="#" class="list-entity-tag popover-pl top" val="'+val+'">'+getImage("tabs.gif","显示标签")+'</a>&nbsp;') ;
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
						/*{align:"center",key:"RISK_LABEL",label:"需求类型", width:"8%",format:function(val,record){
								return record.RISK_LABEL||"" ;
						}},*/
						{align:"center",key:"RISK_TYPE_NAME",label:"风险类型", width:"6%"},
						{align:"left",key:"SKU",label:"Listing SKU",width:"10%"},
						{align:"left",key:"REAL_SKU",label:"货品SKU",width:"10%",format:function(val,record){
								return "<a href='#' product-realsku='"+val+"'>"+(val||"")+"</a>"
						}},
			           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' offer-listing='"+record.ASIN+"'>"+(val||"")+"</a>" ;
			           	}},
			           	{align:"center",key:"P_TITLE",label:"产品标题",width:"10%",forzen:false,align:"left"},
			           /*	{align:"center",key:"DAY_PAGEVIEWS",label:"每日PV",width:"8%",format:function(val){
			           		if(!val) return '-' ;
			           		return Math.round(val) ;
			           	}},*/
			           	{align:"center",key:"FULFILLMENT_CHANNEL",label:"销售渠道",width:"8%"},
			           /*	{align:"center",key:"ITEM_CONDITION",label:"使用程度",width:"8%",format:function(val){
			           		if(val == 1) return "Used" ;
			           		if(val == 11) return 'New' ;
			           		return '' ;
			           	}},*/
			           //	{align:"center",key:"IS_FM",label:"FM产品",width:"8%" },
			           	{align:"center",key:"QUANTITY",label:"库存",width:"6%"},
			        	{align:"center",key:"C",label:"当天订单数",width:"6%"},
			          // 	 {align:"center",key:"PRICE",label:"Price",width:"6%"},
			          /* {align:"center",key:"SHIPPING_PRICE",label:"Ship",group:"价格",width:"6%"},
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
			           	}},*/
			           	{align:"center",key:"LIMIT_PRICE",label:"最低限价",width:"8%"}//,
			        //	{align:"center",key:"SUPPLY_CYCLE",label:"供应周期",width:"8%" },
			       // 	{align:"center",key:"REQ_ADJUST",label:"需求调整系数",width:"8%" }
			         ],
			         //ds:{type:"url",content:contextPath+"/amazongrid/product/"+accountId},
			         ds:{type:"url",content:contextPath+"/grid/query"},
					 limit:20,
					 pageSizes:[15,20,30,40],
					 height:function(){
						 return $(window).height() - 135;
					 },
					 title:"",
					 indexColumn:false,
					 querys:{accountId:accountId,sqlId:"sql_account_product_list"},
					 loadMsg:"数据加载中，请稍候......",
					 loadAfter:function(records){
						 $(".grid-content").uiwidget();
						 
						 $realIds = [] ;
							$(records).each(function(){
								this.REAL_ID && $realIds.push(this.REAL_ID) ;
							}) ;
							
							Business.getProductStatus($realIds.join(",") , function( map ){
								$(".pi-status").each(function(){
									var realId = $(this).attr("realId") ;
									var $title = map[realId] ;
									if( $title ){
										$(this).show().popover({trigger:'click',content: $title,delay:{hide:50},width:500}) ; 
										$(this).mouseenter(function(){
											var me = $(this) ;
											$(".popover-pl").popover("hide") ;
											$(this).popover("show") ;
											$(".popover-inner").mouseleave(function(){
												me.popover("hide") ;
											}) ;
										}) ;
									}else{
										$(this).hide() ;
									}
								});
							}) ;
							
							$(".list-entity-tag").each(function(){
								var record = $(this).parents("tr:first").data("record");
								$(this).popover({trigger:'click',title:"标签："+record.SKU+"/"+record.ASIN,content: "加载中......",delay:{hide:50},width:500}) ; 
							}) ;
							
							
							$(".list-entity-tag").mouseenter( function(){
								var record = $(this).parents("tr:first").data("record");
								var entityType = "listingTag" ;
								var entityId = record.ACCOUNT_ID+"$$"+record.SKU+"$$"+record.ASIN ;
								var subEntityType = record.REAL_ID ;
								$(".popover-pl").popover("hide") ;
								//获取当前的tag
								$(".popover-content").html("加载中......") ;
								$(this).popover("show") ;
								var me = $(this) ;
								setTimeout(function(){
									$.dataservice("model:Tag.listByEntity",{entityType:entityType,entityId:entityId,subEntityType:subEntityType||"null"},function(result){
										var ul = $("<ul><ul>").appendTo( $(".popover-content").empty() ) ;
										var isTag = false ;
										$(result).each(function(){
											if(parseInt(this.COUNT)){
												isTag = true ;
												var tag = $("<li  tagId='"+this.ID+"'  tagEntityId='"+this.TAG_ENTITY_ID+"'><h4>"+this.NAME +"</h4></li>").appendTo(ul) ;
												var memos = ["<div  class='memo-item'>"+this.MEMO+"<span>"+this.CREATOR_NAME+"|"+this.CREATE_DATE+"</span></div>"] ;
												$(this.MEMOS||[]).each(function(){
													memos.push("<div class='memo-item'>"+this.MEMO+"<span>"+this.CREATOR_NAME+"|"+this.CREATE_DATE+"</span></div>") ;
												}) ;
												
												tag.append("<div class='memo-c'>"+memos.join("")+"</div>") ;
												tag.append("<div class='add-container' style='display:none;'><textarea style='width:90%;height:50px;'></textarea><button class='btn save-memo'>保存</button></div>") ;
											}
										}) ;
										if(!isTag){
											ul.append("<li>未添加标签</li>") ;
										}
										$(".popover-inner").mouseleave(function(){
											me.popover("hide") ;
										}) ;
									},{noblock:true});
								},100) ;
								
							}) ;
							
							$(".category-set").each(function(){
								var record = $(this).parents("tr:first").data("record");
								$(this).popover({trigger:'click',title:"分类:"+record.SKU+"/"+record.ASIN,content: "加载中......",delay:{hide:50},width:500,placement :"bottom"}) ; 
							}) ;
							
							$(".risk").live("click",function(){
								var record = $(this).parents("tr:first").data("record");
								var  isRisk = record.IS_RISK||"" ;
								var  riskType  = record.RISK_TYPE||"" ;
								openCenterWindow(contextPath+"/page/forward/Amazonaccount.product_risk/"+record.ID+"/"+isRisk+"/"+riskType , 850,500,function(win,result){
									if(result)$(".grid-content").llygrid("reload",{},true) ;
								},{showType:"dialog"}) ;
							}) ;
							
							
							$(".category-set").mouseenter( function(){
								var record = $(this).parents("tr:first").data("record");
								var entityType = "listingTag" ;
								var entityId = record.ACCOUNT_ID+"$$"+record.SKU+"$$"+record.ASIN ;
								var subEntityType = record.REAL_ID ;
								$(".popover-pl").popover("hide") ;
								//获取当前的tag
								$(".popover-content").html("加载中......") ;
								$(this).popover("show") ;
								var me = $(this) ;
								setTimeout(function(){
									$.dataservice("model:SaleProduct.getListingCategory",{sku:record.SKU,accountId:record.ACCOUNT_ID},function(result){
										var ul = $("<ul><ul>").appendTo( $(".popover-content").empty() ) ;
										var isTag = false ;
										$(result).each(function(){
											isTag = true ;
											$("<li  tagId='"+this.ID+"'  tagEntityId='"+this.TAG_ENTITY_ID+"'><h4>"+this.NAME +"</h4></li>").appendTo(ul) ;
										}) ;
										if(!isTag){
											ul.append("<li>未添加分类</li>") ;
										}
										$(".popover-inner").mouseleave(function(){
											me.popover("hide") ;
										}) ;
									},{noblock:true});
								},100) ;
							}) ;
							
							
							//Business.getEntityTags() ;
							
							//Business.getListingCategory() ;
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
			
			$(".list-entity-tag").live("click",function(){
				var record = $(this).parents("tr:first").data("record");
				var record = $(this).parents("tr:first").data("record");
				var entityType = "listingTag" ;
				var entityId = record.ACCOUNT_ID+"$$"+record.SKU+"$$"+record.ASIN ;
				var subEntityType = record.REAL_ID ;
				DynTag.openTagByEntity(entityType,entityId,subEntityType) ;
			}) ;
			
			
			
			$(".category-set").live("click",function(){
				var record = $(this).parents("tr:first").data("record");
				var categoryTreeSelect = {
						title:'产品分类选择页面',
						//valueField:"#categoryId",
						//labelField:"#categoryName",
						key:{value:'ID',label:'NAME'},//对应value和label的key
						multi:true ,
						tree:{
							title:"产品分类选择页面",
							method : 'post',
							nodeFormat:function(node){
								node.complete = false ;
							},
							asyn : true, //异步
							rootId  : 'root',
							cascadeCheck:false,
							expandLevel:3,
							rootText : '产品分类',
							CommandName : 'sqlId:sql_saleproduct_account_categorytree',
							recordFormat:true,
							params : {
								accountId: accountId,
								sku:record.SKU
							}
						}
				   } ;
				$.listselectdialog( categoryTreeSelect,function(win,ret){
					if( ret && ret.value ){
						var categoryId = ret.value.join(",") ;
						//保存产品分类
						var productId = record.ID ;
						//accountId
						var SKU = record.SKU ;
						json = {
								categoryId:categoryId,
								sku:SKU,
								accountId:accountId
						} ;
						$.dataservice("model:SaleProduct.saveAccountProductCateogory",json,function(result){
							//刷新树
							$('#default-tree_'+(index)).remove() ;
							index++ ;
							$("#tree-wrap").append('<div id="default-tree_'+index+'" class="tree" style="padding: 5px; "></div>') ;
							loadTree( $('#default-tree_'+index) ) ;
							//alert(222) ;
						});
					}
				}) ;
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
				querys.searchKey = $("[name='searchKey']").val()  ;
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