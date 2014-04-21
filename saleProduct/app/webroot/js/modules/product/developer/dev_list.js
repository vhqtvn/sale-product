var currentId = '' ;
	$(function(){
			function loadStatics(){
				$.dataservice("sqlId:sql_productDev_new_loadStativcs",{},function(result){
					 //$(".grid-content-details").llygrid("reload",{},true) ;
					$(".flow-node").find(".count").html("(0)") ;
					var count = 0 ;
					var map = {} ;
					$(result).each(function(index,item){
						item = item.t ;
						map[item.STATUS+"_"] = parseInt(item.COUNT) ;
						count +=parseInt(item.COUNT) ;
					}) ;
					//alert( $.json.encode(map) ) ;
					$(".flow-node").each(function(){
						var status = $(this).attr("status") ;
						var ss = (status+"").split(",") ;
						var c = 0 ;
						$(ss).each(function(index,item){
							if(!item)return ;
							c = (map[item+"_"]||0) +c;
						}) ;
						$(".flow-node[status='"+status+"']").find(".count").html("("+c+")") ;
					}) ;
					
					$(".total").find(".count").html("("+count+")") ;

					setTimeout(function(){
						loadStatics() ;
					},5000) ;
					
				 },{noblock:true});
				
			}
			
			loadStatics() ;
			
			$(".flow-node").click(function(){
				var status = $(this).attr("status");
				$(".flow-node").removeClass("active").addClass("disabled");
				$(this).removeClass("disabled").addClass("active");
				$(".grid-content-details").llygrid("reload",{status:status});
			}) ;
			
			//开发新产品
		   $(".create-product-dev").click(function(){
			   openCenterWindow(contextPath+"/page/forward/Product.developer.devFromNewAsin/",600,400,function(win , rt){
					
				}) ;
		   }) ;
		
			$(".grid-content-details").llygrid({
				columns:[
					{align:"center",key:"TASK_ID",label:"操作",width:"4%",format:function(val,record){
							var html = [] ;
							if(record.FLOW_STATUS <= 10){
								if( $loginId == record.CREATOR ) html.push( getImage("delete.gif","删除","delete-tp-action") ) ;
							}
							
							html.push( getImage("edit.png","处理","process-action") ) ;
							return html.join("") ;
					}},
					{align:"center",key:"FLOW_STATUS",label:"流程状态",width:"6%",sort:true,
							format:{type:'json',content:{10:'产品分析',15:'废弃',20:'询价',25:'成本利润分析',30:'产品经理审批',40:'总监审批',42:"样品检测",44:"检测审批",50:'录入货品',
								60:'制作Listing',70:'Listing审批',72:'采购试销',74:'库存到达',76:'营销展开',78:'开发总结',80:'处理完成'}}},
					{align:"left",key:"MEMO",label:"开发备注",sort:true,width:"10%",format:function(val){
						//http://localhost/saleProduct/index.php/sale/details1/F_1366985996/B003J39IKI#track-tab
						return "<a href='#' class='memo-action'>"+(val||"")+"</a>";
					}},
		           	{align:"center",key:"TITLE",label:"开发标题",width:"15%",forzen:false,align:"left"},
					{align:"center",key:"DEV_STATUS",label:"开发状态",sort:true,width:"5%",format:function(val){
						val = val||"" ;
						var map = {1:'自有',2:'跟卖',3:'废弃',4:'自有兼跟卖'} ;
						return map[val] ;
					}},
	           		{align:"center",key:"DEV_CHARGER_NAME",label:"开发负责人",width:"6%"},
	           		{align:"center",key:"INQUIRY_CHARGER_NAME",label:"询价负责人",width:"6%"},
					{align:"center",key:"COST_COUNT",label:"利润分类",width:"8%",sort:true,format:function(val,record){
						var INQUIRY_COUNT = record.INQUIRY_COUNT ;
						var COST_COUNT = record.COST_COUNT ;
						val = record.COST_GROUP;
						if( val ){
							var s = val.split(",") ;
							var maxProfit = 0 ;
							var maxType ;
							$(s).each(function(){
								var ss = this.split("|") ;
								var type = ss[0] ;
								var profit = parseFloat(ss[1]||0) ;
								maxProfit = Math.max(maxProfit,profit) ;
							}) ;
							if(maxProfit <=0 ) return "亏本" ;
							if(maxProfit <0.15 ) return "低利润" ;
							return "利润达标" ;
						}else{
							if( INQUIRY_COUNT<=0 ) return "待询价" ;
							return "-" ;
						}
					   }},
					   {align:"center",key:"COST_GROUP",label:"利润值",width:"20%",sort:true,format:function(val,record){
						   return "-" ;
						   },permission:function(){
							   return $COST_VIEW_PROFIT ;
						   }},
		           	{align:"center",key:"ASIN",label:"ASIN", width:"8%",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"LOCAL_URL",label:"",width:"3%",forzen:false,align:"left",format:{type:'img'}},
		           	{align:"center",key:"P_TITLE",label:"产品标题",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='"+contextPath+"/page/forward/Platform.asin/"+record.ASIN+"' target='_blank'>"+(val||"产品信息页")+"</a>" ;
		           	}},
		           	{align:"center",key:"PPC_STRATEGY_NAME",label:"竞价排名策略",width:"15%"},
	           		{align:"center",key:"LOGI_STRATEGY",label:"物流策略",width:"10%"},
	           		{align:"center",key:"SPREAD_STRATEGY",label:"推广策略",width:"15%"},
	           		{align:"center",key:"CREATE_TIME",label:"创建时间",width:"10%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					 return $(window).height() - 160 ;
				 },
				 //title:"产品列表",
				 indexColumn: false,
				 querys:{taskView:'1',sqlId:'sql_pdev_new_list'},//status:$("[name='status']").val(),type:type,
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(records){
					    var asins = [] ;
					 	$(records).each(function(){
					 		if(this.ASIN)asins.push(this.ASIN);
					 	}) ;
					 	
					 	asins = "'"+asins.join("','")+"'" ;
					 	//setTimeout(function(){
					 		$.dataservice("sqlId:sql_pdev_filter_details_getCostGroup",{asins:asins},function(result){
						 		$(result).each(function(index,item){
						 			var t = item.t ;
						 			var tr = $("[asin='"+t.ASIN+"']").closest("tr") ;
						 			var costGroup = t.COST_GROUP||"" ;
						 			var val = costGroup ;
						 			if(val){
						 				var s = val.split(",") ;
										var maxProfit = 0 ;
										var maxType ;
										var profitRatio = {} ;
										$(s).each(function(){
											var ss = this.split("|") ;
											var type = ss[0] ;
											var profit = parseFloat(ss[1]||0) ;
											var cost = ss[2] ;
											//alert(profit+"   "+cost);
											profitRatio[type] = type+"("+cost+","+( (profit)*100).toFixed(2)+"%"+")" ;
											//profitRatio.push( type+"("+cost+","+( (profit)*100).toFixed(2)+"%"+")") ;
										}) ;
										costGroup = profitRatio["FBA"]+"||"+profitRatio["FBM"] ;
						 			}
						 			
						 			$("<span>"+costGroup+"</span>").appendTo(tr.find("td[key='COST_GROUP']").find(".cell-div").empty() ).attr("title",costGroup) ;
						 			if( t.COST_COUNT<=0 ) {
						 				tr.find("td[key='COST_COUNT']").find(".cell-div").html("<span>待成本核算</span>") ;
						 			}
						 		}) ;
					 			//$(".grid-content-details").llygrid("reload",{},true) ;
							},{noblock:true}) ;
					 	//},500) ;
						 	
					 	$(".process-action").bind("click",function(){
							var row =  $(this).parents("tr:first").data("record") ;
							var devId = row.DEV_ID ;
							openCenterWindow(contextPath+"/page/forward/Product.developer.edit_product_dev/"+devId,1000,670,function(win , rt){
								if(rt){
									$(".grid-content-details").llygrid("reload",{},true) ;
								}
							}) ;
						}) ;
					 	
					 	$(".memo-action").bind("click",function(){
							var row =  $(this).parents("tr:first").data("record") ;
							var taskId = row.TASK_ID ;
							var asin = row.ASIN ;
							var devId = row.ID ;
							openCenterWindow(contextPath+"/sale/details1/"+taskId+"/"+asin+"#track-tab",1000,650,function(win , rt){
								if(rt){
									$(".grid-content-details").llygrid("reload",{},true) ;
								}
							}) ;
						}) ;
					 	
					 	$(".delete-tp-action").click(function(){
					 		var row =  $(this).parents("tr:first").data("record") ;
					 		if(window.confirm("确认删除吗？")){
					 			var taskId = row.TASK_ID ;
								var asin = row.ASIN ;
					 			$.dataservice("model:ProductDev.deleteTaskProduct",{taskId:taskId,asin:asin},function(){
						 			$(".grid-content-details").llygrid("reload",{},true) ;
								}) ;
					 		}
					 	}) ;
				 }
			}) ;
			
			$(".product-detail").live("click",function(){
				var asin = $(this).attr("asin") ;
				openCenterWindow(contextPath+"/product/details/"+asin,950,650) ;
			});
			
			
			$(".create-task").click(function(){
				openCenterWindow(contextPath+"/page/forward/Product.developer.createTask/",700,400,function(){
					var params = $.dialogReturnValue()  ;
					if(!params ) return ;
					if(!params.code) return ;

					$.dataservice("model:ProductDev.saveTask",params,function(){
						$(".grid-content").llygrid("reload",{}) ;
					}) ;
				}) ;
			}) ;
   	 });
	
	function formatGridQuery( json, jsonOptions ){
		if( jsonOptions.qc == '.toolbar1' ){
			for( var o in json){
				json[o] =( json[o]+"").replace("/","") ;
			}
		}
		return json ;
	}
   	 
   	 
   function showImg(el){
   		var src = el.src ;
   		openCenterWindow(src,500,300) ;
   }
   	 