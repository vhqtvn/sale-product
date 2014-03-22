var currentId = '' ;
	$(function(){
			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"CODE",label:"任务编码",width:"15%",forzen:false,align:"left",sort:true},
		           	{align:"center",key:"NAME",label:"任务名称",width:"15%",forzen:false,align:"left",sort:true},
		        	{align:"center",key:"PLATFORM_NAME",label:"平台",width:"10%",forzen:false,align:"left",sort:true},
		           	{align:"center",key:"PLAN_NAME",label:"所属计划",width:"15%",forzen:false,align:"left",sort:true},
		           	{align:"center",key:"START_TIME",label:"开始时间",width:"15%",forzen:false,align:"left",sort:true},
		           	{align:"center",key:"END_TIME",label:"结束时间",width:"15%",forzen:false,align:"left",sort:true},
			        {align:"center",key:"STATUS20_MY",label:"我的询价",width:"6%",sort:true,format:function(val,record){
						return  "<a href='#' class='fs-action' mystatus='20'>"+(val||'0')+"</a>" ;
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
					{align:"center",key:"COST_GROUP",label:"利润分类",width:"8%",sort:true,format:function(val,record){
						var INQUIRY_COUNT = record.INQUIRY_COUNT ;
						var COST_COUNT = record.COST_COUNT ;
						
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
							if( COST_COUNT<=0 ) return "待成本核算" ;
							if( !val ) return "未算利润" ;
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
				 querys:{sqlId:'sql_pdev_filter_details_inquirylist'},//status:$("[name='status']").val(),type:type,
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
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
   	 