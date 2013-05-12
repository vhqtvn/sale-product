var currentId="" ;	
$(function(){
			$(".grid-content").llygrid({
				columns:[
				    {align:"center",key:"ID",label:"操作",width:"3%",format:function(val,record){
				    	return   getImage('edit.png','编辑','edit-plan ');
				    },permission:function(){
				    	return $PDEV_EDIT ;
				    }},
		           	{align:"center",key:"NAME",label:"计划名称",width:"15%",forzen:false,align:"left"},
		        	{align:"center",key:"CODE",label:"计划编码",width:"12%",forzen:false,align:"left"},
		        	{align:"center",key:"START_TIME",label:"开始时间",width:"8%",format:{type:'calendar'}},
		        	{align:"center",key:"END_TIME",label:"结束时间",width:"8%",format:{type:'calendar'}},
		           	{align:"center",key:"FOLLOW_TOTAL_NUM",label:"总数量",group:"跟卖产品",width:"5%"},
		           	{align:"center",key:"FOLLOW_PER_NUM",label:"人均数量",group:"跟卖产品",width:"5%"},
		           	{align:"center",key:"FOLLOW_SALE_NUM",label:"销量",group:"跟卖产品",width:"5%"},
		        	{align:"center",key:"SELF_TOTAL_NUM",label:"总数量",group:"自有产品",width:"5%"},
		           	{align:"center",key:"SELF_PER_NUM",label:"人均数量",group:"自有产品",width:"5%"},
		           	{align:"center",key:"SELF_SALE_NUM",label:"销量",group:"自有产品",width:"5%"},
		           	{align:"center",key:"TOTAL",label:"总产品",width:"5%"},
		           	{align:"center",key:"STATUS1",label:"自有",group:'开发状态',width:"4%",format:function(val,record){
						return "<a href='#' class='fs-action' devstatus='1'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS2",label:"跟卖",group:'开发状态',width:"4%",format:function(val,record){
						return "<a href='#' class='fs-action' devstatus='2'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS15",label:"废弃",group:'开发状态',width:"4%",format:function(val,record){
						return "<a href='#' class='fs-action' status='15'>"+(val||'0')+"</a>"
			        }},
		           	{align:"center",key:"STATUS10",label:"产品分析",group:'流程状态',width:"6%",format:function(val,record){
						return "<a href='#' class='fs-action' status='10'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS20",label:"询价",group:'询价|流程状态',width:"6%",format:function(val,record){
						return  "<a href='#' class='fs-action' status='20'>"+(val||'0')+"</a>" ;
			        }},
			        {align:"center",key:"STATUS20_UNASIGN",label:"未分配",group:'询价|流程状态',width:"6%",format:function(val,record){
						return  "<a href='#' class='fs-action' unasignstatus='20'>"+(val||'0')+"</a>" ;
			        }},
			        {align:"center",key:"STATUS20_MY",label:"我的询价",group:'询价|流程状态',width:"6%",format:function(val,record){
						return  "<a href='#' class='fs-action' mystatus='20'>"+(val||'0')+"</a>" ;
			        }},
			        {align:"center",key:"STATUS25",label:"成本利润",group:'流程状态',width:"6%",format:function(val,record){
						return "<a href='#' class='fs-action' status='25'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS30",label:"产品经理审批",group:'流程状态',width:"6%",format:function(val,record){
						return "<a href='#' class='fs-action' status='30'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS40",label:"总监审批",group:'流程状态',width:"6%",format:function(val,record){
						return "<a href='#' class='fs-action' status='40'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS50",label:"货品录入",group:'流程状态',width:"6%",format:function(val,record){
						return "<a href='#' class='fs-action' status='50'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS60",label:"制作Listing",group:'流程状态',width:"6%",format:function(val,record){
						return "<a href='#' class='fs-action' status='60'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS70",label:"Listing审批",group:'流程状态',width:"6%",format:function(val,record){
						return "<a href='#' class='fs-action' status='70'>"+(val||'0')+"</a>"
			        }},
			        {align:"center",key:"STATUS80",label:"结束",group:'流程状态',width:"6%",format:function(val,record){
						return "<a href='#' class='fs-action' status='80'>"+(val||'0')+"</a>"
			        }},
		           	{align:"center",key:"CREATE_TIME",label:"创建时间",width:"8%"},
		           	{align:"center",key:"USERNAME",label:"创建人",width:"8%"},
		           	{align:"center",key:"MEMO",label:"备注",width:"15%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},//"/salegrid/filterTask/"+type},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:130,
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:'sql_pdev_plan_list'},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	var id = rowData.ID  ;
				 	currentId = id ;
				 	$(".grid-content-details").llygrid("reload",{planId:currentId}) ;
				 },loadAfter : function(){
					 $(".edit-plan").bind("click",function(event){
						 	event.stopPropagation() ;
						 	var row =  $(this).parents("tr:first").data("record") ;
							openCenterWindow(contextPath+"/page/forward/Product.developer.createPlan/"+row.ID,800,550,function(){
								$(".grid-content").llygrid("reload",{},true);
							}) ;
							return false ;
						}) ;
					 
					 $(".fs-action").bind("click",function(event){
						 	var row =  $(this).parents("tr:first").data("record") ;
						    event.preventDefault() ;
						    event.stopPropagation()  ;
							var val = $(this).attr("status") ;
							var devStatus = $(this).attr("devstatus") ;
							var mystatus = $(this).attr("mystatus") ;
							var unasignstatus = $(this).attr("unasignstatus") ;
							
							if( unasignstatus ) {
								$(".grid-content-details").llygrid("reload",{devStatus:'',status:'',devStatus1:'',mystatus:'',unasignstatus:unasignstatus,planId:row.ID},true) ;
								return false ;
							}
							
							if( mystatus ) {
								$(".grid-content-details").llygrid("reload",{devStatus:'',status:'',devStatus1:'',mystatus:mystatus,unasignstatus:'',planId:row.ID},true) ;
								return false ;
							}
							
							if(devStatus){
								$(".grid-content-details").llygrid("reload",{devStatus:devStatus,devStatus1:'',status:'',mystatus:'',unasignstatus:'',planId:row.ID},true) ;
							}else{
								$(".grid-content-details").llygrid("reload",{devStatus:'',status:val,devStatus1:'',mystatus:'',unasignstatus:'',planId:row.ID},true) ;
							}
							return false ;
					}) ;
				}
			}) ;
			
			$(".fs-action").live("click",function(){
				var val = $(this).attr("status") ;
				var devStatus = $(this).attr("devstatus") ;
				if(devStatus){
					$(".grid-content-details").llygrid("reload",{devStatus:devStatus,status:''},true) ;
				}else{
					$(".grid-content-details").llygrid("reload",{devStatus:'',status:val},true) ;
				}
			}) ;
			
			$(".grid-content-details").llygrid({
				columns:[
							{align:"center",key:"TASK_ID",label:"操作",width:"5%",format:function(val,record){
						var status = record.STATUS ;
						return "<a href='#' class='process-action' status='"+status+"' val='"+val+"' asin='"+record.ASIN+"'>处理</a>&nbsp;" ;
					}},
					{align:"center",key:"FLOW_STATUS",label:"流程状态",width:"7%",sort:true,
						format:{type:'json',content:{10:'产品分析',15:'废弃',20:'询价',25:'成本利润分析',30:'产品经理审批',40:'总监审批',50:'录入货品',60:'制作Listing',70:'Listing审批',80:'处理完成'}}},
				{align:"center",key:"DEV_STATUS",label:"开发状态",sort:true,width:"5%",format:function(val){
					val = val||"" ;
					var map = {1:'自有',2:'跟卖',3:'废弃'} ;
					return map[val] ;
				}},
				{align:"center",key:"COST_GROUP",label:"利润",width:"8%",sort:true,format:function(val,record){
					var INQUIRY_COUNT = record.INQUIRY_COUNT ;
					var COST_COUNT = record.COST_COUNT ;
					if( INQUIRY_COUNT<=0 ) return "待询价" ;
					if( COST_COUNT<=0 ) return "待成本核算" ;
					if( !val ) return "未算利润" ;
					
					var s = val.split(",") ;
					var maxProfit = 0 ;
					var maxType ;
					$(s).each(function(){
						var ss = this.split("|") ;
						var type = ss[0] ;
						var profit = parseFloat(ss[1]||0)/100 ;
						maxProfit = Math.max(maxProfit,profit) ;
					}) ;
					if(maxProfit <=0 ) return "亏本" ;
					if(maxProfit <0.15 ) return "低利润" ;
					return "利润达标" ;
					
				}},
		           	{align:"center",key:"ASIN",label:"ASIN", width:"8%",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"LOCAL_URL",label:"",width:"3%",forzen:false,align:"left",format:{type:'img'}},
		        	{align:"center",key:"TITLE",label:"开发标题",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"P_TITLE",label:"产品标题",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
		           	},render:function(record){
		           		if(record.ASIN_COUNT >1 ){
		           			$(this).find("td").css("background","pink") ;
		           		}
		           	}},
		        	{align:"center",key:"FILTER_NAME",label:"任务名称",width:"20%",forzen:false,align:"left"},
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
				 title:"",
				 indexColumn: false,
				 querys:{sqlId:'sql_pdev_filter_details'},//status:$("[name='status']").val(),type:type,
				 loadMsg:"数据加载中，请稍候......"
			}) ;

			$(".process-action").live("click",function(){
				var row =  $(this).parents("tr:first").data("record") ;
				var taskId = $(this).attr("val") ;
				var asin = $(this).attr("asin") ;
		
				openCenterWindow(contextPath+"/sale/details1/"+taskId+"/"+asin,950,650,function(){
					$(".grid-content-details").llygrid("reload",{},true) ;
				}) ;
			}) ;
			
   	 });