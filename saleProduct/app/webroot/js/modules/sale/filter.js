var currentId = '' ;
	$(function(){
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"ID",label:"操作",width:"5%",format:function(val,record){
						var html = [] ;
						if( record.TOTAL <=0 ){
							if( $loginId == record.CREATOR || $PDT_UPDATE ) html.push( getImage("delete.gif","删除","delete-action") ) ;
						}
						if( $loginId == record.CREATOR || $PDT_UPDATE ){
							html.push( getImage("edit.png","修改","edit-action") ) ;
						}
						
						if( $loginId == record.CREATOR || $PDT_UPDATE )
							html.push( getImage("pkg.gif","添加ASIN","add-asin") ) ;
						
						return html.join("") ;
					}},
					{align:"center",key:"CODE",label:"任务编码",width:"15%",forzen:false,align:"left",sort:true},
		           	{align:"center",key:"NAME",label:"任务名称",width:"15%",forzen:false,align:"left",sort:true},
		        	{align:"center",key:"PLATFORM_NAME",label:"平台",width:"10%",forzen:false,align:"left",sort:true},
		           	{align:"center",key:"PLAN_NAME",label:"所属计划",width:"15%",forzen:false,align:"left",sort:true},
		           	{align:"center",key:"START_TIME",label:"开始时间",width:"15%",forzen:false,align:"left",sort:true},
		           	{align:"center",key:"END_TIME",label:"结束时间",width:"15%",forzen:false,align:"left",sort:true},
		           	{align:"center",key:"TOTAL",label:"总产品",width:"5%",sort:true},
		        	{align:"center",key:"STATUS1",label:"自有",group:'开发状态',width:"4%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' devstatus='1'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS2",label:"跟卖",group:'开发状态',width:"4%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' devstatus='2'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS4",label:"自有&跟卖",group:'开发状态',width:"4%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' devstatus='4'>"+(val||'0')+"</a>"
			        }},
			        /*
			        {align:"center",key:"STATUS15",label:"废弃",group:'开发状态',width:"4%",format:function(val,record){
						return "<a href='#' class='fs-action' status='15'>"+(val||'0')+"</a>"
			        }},*/
		           	{align:"center",key:"STATUS10",label:"产品分析",group:'流程状态',width:"6%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' status='10'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS20",label:"询价",group:'询价|流程状态',width:"6%",sort:true,format:function(val,record){
						return  "<a href='#' class='fs-action' status='20'>"+(val||'0')+"</a>" ;
			        }},
			        {align:"center",key:"STATUS20_UNASIGN",label:"未分配",group:'询价|流程状态',width:"6%",sort:true,format:function(val,record){
						return  "<a href='#' class='fs-action' unasignstatus='20'>"+(val||'0')+"</a>" ;
			        }},
			        {align:"center",key:"STATUS20_MY",label:"我的询价",group:'询价|流程状态',width:"6%",sort:true,format:function(val,record){
						return  "<a href='#' class='fs-action' mystatus='20'>"+(val||'0')+"</a>" ;
			        }},
			        {align:"center",key:"STATUS25",label:"成本利润",group:'流程状态',width:"6%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' status='25'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS30",label:"产品经理审批",group:'流程状态',width:"6%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' status='30'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS40",label:"总监审批",group:'流程状态',width:"6%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' status='40'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS50",label:"货品录入",group:'流程状态',width:"6%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' status='50'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS60",label:"制作Listing",group:'流程状态',width:"6%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' status='60'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS70",label:"Listing审批",group:'流程状态',width:"6%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' status='70'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS72",label:"试销采购",group:'流程状态',width:"6%",sort:true,format:function(val,record){
						return "<a href='#' class='fs-action' status='72'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS80",label:"结束",group:'流程状态',width:"6%",format:function(val,record){
						return "<a href='#' class='fs-action' status='80'>"+(val||'0')+"</a>"
			        }},
			    	{align:"center",key:"CREATE_TIME",label:"创建时间",width:"13%"},
		           	{align:"center",key:"USERNAME",label:"创建人",width:"8%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},//"/salegrid/filterTask/"+type},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:130,
				 //title:"产品开发任务列表",
				 indexColumn:true,
				 querys:{sqlId:'sql_pdev_filter'},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	var id = rowData.ID  ;
				 	currentId = id ;
				 	$(".grid-content-details").llygrid("reload",{taskId:currentId,devStatus1:"1"}) ;
				 },loadAfter : function(){
					 $(".fs-action").bind("click",function(event){
						 	var row =  $(this).parents("tr:first").data("record") ;
						    event.preventDefault() ;
						    event.stopPropagation()  ;
						    
						    var val = $(this).attr("status") ;
							var devStatus = $(this).attr("devstatus") ;
							var mystatus = $(this).attr("mystatus") ;
							var unasignstatus = $(this).attr("unasignstatus") ;
							
							if( unasignstatus ) {
								$(".grid-content-details").llygrid("reload",{devStatus:'',status:'',devStatus1:'',mystatus:'',unasignstatus:unasignstatus,taskId:row.ID},true) ;
								return false ;
							}
							
							if( mystatus ) {
								$(".grid-content-details").llygrid("reload",{devStatus:'',status:'',devStatus1:'',mystatus:mystatus,unasignstatus:'',taskId:row.ID},true) ;
								return false ;
							}
							
							if(devStatus){
								$(".grid-content-details").llygrid("reload",{devStatus:devStatus,devStatus1:'',status:'',mystatus:'',unasignstatus:'',taskId:row.ID},true) ;
							}else{
								$(".grid-content-details").llygrid("reload",{devStatus:'',status:val,devStatus1:'',mystatus:'',unasignstatus:'',taskId:row.ID},true) ;
							}
							return false ;
						    
					}) ;
					 $(".delete-action").bind("click",function(event){
						 	var row =  $(this).parents("tr:first").data("record") ;
						    event.preventDefault() ;
						    event.stopPropagation()  ;
						    if(window.confirm("确认删除该任务？")){
								 var taskId = row.ID;
									$.dataservice("model:ProductDev.deleteTask",{taskId:taskId},function(result){
										$(".grid-content").llygrid("reload",{},true) ;
									});
							 }
							return false ;
					}) ;
					 
					 $(".edit-action").bind("click",function(event){
						 	var row =  $(this).parents("tr:first").data("record") ;
						    event.preventDefault() ;
						    event.stopPropagation()  ;
							var taskId = row.ID;
							openCenterWindow(contextPath+"/page/forward/Product.developer.createTask/"+taskId,700,400,function(){
								var params = $.dialogReturnValue()  ;
								if(!params ) return ;
								if(!params.code) return ;

								$.dataservice("model:ProductDev.saveTask",params,function(){
									$(".grid-content").llygrid("reload",{}) ;
								}) ;
							}) ;
							return false ;
					}) ;
					 
					 $(".add-asin").bind("click",function(event){
						 	var row =  $(this).parents("tr:first").data("record") ;
						    event.preventDefault() ;
						    event.stopPropagation()  ;
							var taskId = row.ID;
							openCenterWindow(contextPath+"/page/forward/ProductDev.addAsinToTask/"+taskId,550,350,function(){
								var rv = jQuery.dialogReturnValue() ;
								if(rv){
									$(".grid-content-details").llygrid("reload",{},true) ;
								}
							}) ;
							return false ;
					}) ;
				}
			}) ;

			$(".grid-content-details").llygrid({
				columns:[
					{align:"center",key:"TASK_ID",label:"操作",width:"5%",format:function(val,record){
							var html = [] ;
							if(record.FLOW_STATUS <= 10){
								if( $loginId == record.CREATOR ) html.push( getImage("delete.gif","删除","delete-tp-action") ) ;
							}
							
							html.push( getImage("edit.png","处理","process-action") ) ;
							//html.push( getImage("forum.gif","转交","transfer-action") ) ;
							
							return html.join("") ;
						//var status = record.STATUS ;
						//return "<a href='#' class='process-action' status='"+status+"' val='"+val+"' asin='"+record.ASIN+"'>处理</a>&nbsp;" ;
					}},
					{align:"center",key:"FLOW_STATUS",label:"流程状态",width:"7%",sort:true,
							format:{type:'json',content:{10:'产品分析',15:'废弃',20:'询价',25:'成本利润分析',30:'产品经理审批',40:'总监审批',50:'录入货品',
								60:'制作Listing',70:'Listing审批',72:'采购试销',74:'库存到达',76:'营销展开',78:'开发总结',80:'处理完成'}}},
					{align:"left",key:"MEMO",label:"开发备注",sort:true,width:"15%",format:function(val){
						//http://localhost/saleProduct/index.php/sale/details1/F_1366985996/B003J39IKI#track-tab
						return "<a href='#' class='memo-action'>"+(val||"")+"</a>";
					}},
					{align:"center",key:"DEV_STATUS",label:"开发状态",sort:true,width:"5%",format:function(val){
						val = val||"" ;
						var map = {1:'自有',2:'跟卖',3:'废弃',4:'自有兼跟卖'} ;
						return map[val] ;
					}},
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
						   var INQUIRY_COUNT = record.INQUIRY_COUNT ;
							var COST_COUNT = record.COST_COUNT ;
							
							if( val ){
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
								
								return profitRatio["FBA"]+"||"+profitRatio["FBM"] ;
							}else{
								return "-" ;
							}
						   },permission:function(){
							   return $COST_VIEW_PROFIT ;
						   }},
		           	{align:"center",key:"ASIN",label:"ASIN", width:"8%",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"LOCAL_URL",label:"",width:"3%",forzen:false,align:"left",format:{type:'img'}},
		           	{align:"center",key:"TITLE",label:"开发标题",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"P_TITLE",label:"产品标题",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='"+contextPath+"/page/forward/Platform.asin/"+record.ASIN+"' target='_blank'>"+(val||"--Listing--")+"</a>" ;
		           	}},
		           	{align:"center",key:"PPC_STRATEGY_NAME",label:"竞价排名策略",width:"15%"},
	           		{align:"center",key:"LOGI_STRATEGY",label:"物流策略",width:"10%"},
	           		{align:"center",key:"SPREAD_STRATEGY",label:"推广策略",width:"15%"},
	           		{align:"center",key:"CREATE_TIME",label:"创建时间",width:"10%"},
	           		{align:"center",key:"USERNAME",label:"创建人",width:"6%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					 return $(window).height() - 390 ;
				 },
				 //title:"产品列表",
				 indexColumn: false,
				 querys:{taskView:'1',sqlId:'sql_pdev_filter_details'},//status:$("[name='status']").val(),type:type,
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
							var taskId = row.TASK_ID ;
							var asin = row.ASIN ;
							var devId = row.ID ;
							openCenterWindow(contextPath+"/sale/details1/"+taskId+"/"+asin,1000,650,function(win , rt){
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
			})
			
			
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
   	 