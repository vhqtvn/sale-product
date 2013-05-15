var currentId = '' ;
	$(function(){
			
			$(".grid-content-details").llygrid({
				columns:[
					{align:"center",key:"TASK_ID",label:"操作",width:"5%",format:function(val,record){
							var html = [] ;
							if(record.FLOW_STATUS <= 10){
								//if( $loginId == record.CREATOR ) html.push( getImage("delete.gif","删除","delete-tp-action") ) ;
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
					{align:"center",key:"DEV_STATUS",label:"开发状态",sort:true,width:"5%",format:function(val){
						val = val||"" ;
						var map = {1:'自有',2:'跟卖',3:'废弃',4:'自有兼跟卖'} ;
						return map[val] ;
					}},
					{align:"center",key:"COST_GROUP",label:"利润分类",width:"8%",sort:true,format:function(val,record){
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
					   {align:"center",key:"COST_GROUP",label:"利润值",width:"8%",sort:true,format:function(val,record){
							var INQUIRY_COUNT = record.INQUIRY_COUNT ;
							var COST_COUNT = record.COST_COUNT ;
							if( INQUIRY_COUNT<=0 ) return "" ;
							if( COST_COUNT<=0 ) return "" ;
							if( !val ) return "" ;
							
							return val ||"";
						   },permission:function(){
							   return $COST_VIEW_PROFIT ;
						   }},
		           	{align:"center",key:"ASIN",label:"ASIN", width:"8%",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"LOCAL_URL",label:"",width:"3%",forzen:false,align:"left",format:{type:'img'}},
		           	{align:"center",key:"TITLE",label:"开发标题",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"P_TITLE",label:"产品标题",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+(val||"")+"</a>" ;
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
					 return $(window).height() - 120 ;
				 },
				 //title:"产品列表",
				 indexColumn: false,
				 querys:{sqlId:'sql_pdev_discard_list'},//status:$("[name='status']").val(),type:type,
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
					 	$(".process-action").bind("click",function(){
							var row =  $(this).parents("tr:first").data("record") ;
							var taskId = row.TASK_ID ;
							var asin = row.ASIN ;
							openCenterWindow(contextPath+"/sale/details1/"+taskId+"/"+asin,1000,650,function(){
								$(".grid-content-details").llygrid("reload",{},true) ;
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
   	 
   	 
   function showImg(el){
   		var src = el.src ;
   		openCenterWindow(src,500,300) ;
   }
   	 