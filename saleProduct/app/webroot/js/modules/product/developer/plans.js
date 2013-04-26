var currentId="" ;	
$(function(){
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"编号", width:"4%"},
		           	{align:"center",key:"NAME",label:"计划名称",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"FOLLOW_TOTAL_NUM",label:"总数量",group:"跟买产品",width:"8%"},
		           	{align:"center",key:"FOLLOW_PER_NUM",label:"人均数量",group:"跟买产品",width:"8%"},
		           	{align:"center",key:"FOLLOW_SALE_NUM",label:"销量",group:"跟买产品",width:"8%"},
		        	{align:"center",key:"SELF_TOTAL_NUM",label:"总数量",group:"只有产品",width:"8%"},
		           	{align:"center",key:"SELF_PER_NUM",label:"人均数量",group:"只有产品",width:"8%"},
		           	{align:"center",key:"SELF_SALE_NUM",label:"销量",group:"只有产品",width:"8%"},
		           	{align:"center",key:"MEMO",label:"备注",width:"15%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},//"/salegrid/filterTask/"+type},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:130,
				 title:"开发计划列表",
				 indexColumn:true,
				 querys:{sqlId:'sql_pdev_plan_list'},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	var id = rowData.ID  ;
				 	currentId = id ;
				 	$(".grid-content-details").llygrid("reload",{planId:currentId}) ;
				 },loadAfter : function(){
					 
				}
			}) ;
			
			$(".grid-content-details").llygrid({
				columns:[
					{align:"center",key:"FILTER_ID",label:"操作",width:"5%",format:function(val,record){
						var status = record.STATUS ;
						
						if( $ProductSpecialistProcess ){
							if(status == 1 || !status || status == 2 || status == 3 ){ //未处理 、处理中
								return "<a href='#' class='process-action' status='"+status+"' val='"+val+"' asin='"+record.ASIN+"'>处理</a>&nbsp;" ;
							}
							return "" ;
						}else if( $ProductManagerProcess ){
							if(status == 4 ){ //未处理 、处理中
								return "<a href='#' class='process-action' status='"+status+"' val='"+val+"' asin='"+record.ASIN+"'>处理</a>&nbsp;" ;
							}
							//return "" ;
						}else if( $GeneralManagerProcess ){
							if(status == 6 ){ //未处理 、处理中
								return "<a href='#' class='process-action' status='"+status+"' val='"+val+"' asin='"+record.ASIN+"'>处理</a>&nbsp;" ;
							}
							//return "" ;
						}
					}},
		           	{align:"center",key:"ASIN",label:"ASIN", width:"8%",format:function(val,record){
		           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"LOCAL_URL",label:"",width:"3%",forzen:false,align:"left",format:function(val,record){
		           		
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           		}else{
		           			return "" ;
		           		}
		           		
		           		return "<img src='/"+fileContextPath+"/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
		           	}},
		           	{align:"center",key:"TITLE",label:"TITLE",width:"20%",forzen:false,align:"left",format:function(val,record){
		           		return "<a href='http://www.amazon.com/gp/offer-listing/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"DAY_PAGEVIEWS",label:"每日PV",width:"5%"},
	           		{align:"center",key:"FM_NUM",label:"FM数量",width:"5%"},
	           		{align:"center",key:"NM_NUM",label:"NM数量",width:"6%"},
	           		{align:"center",key:"UM_NUM",label:"UM数量",width:"6%"},
	           		{align:"center",key:"FBA_NUM",label:"FBA数量",width:"6%"},
	           		{align:"center",key:"REVIEWS_NUM",label:"Reviews数量",width:"9%"},
	           		{align:"center",key:"QUALITY_POINTS",label:"质量分",width:"5%"},
	           		{align:"center",key:"TARGET_PRICE",label:"产品总价",width:"7%"},
					{align:"center",key:"STATUS",label:"状态",width:"10%",format:function(val,record){
						var status = $.trim(record.STATUS||'') ;
						var html = [] ;

						if(status == 1 || !status  ){ //未处理 、处理中
							return "未处理" ;
						}else  if( status == 2 ){//已废弃
							return "处理中" ;
						}else  if( status == 3 ){//已废弃
							return "已废弃" ;
						}else  if(status == 4 ){ // 大于或等于4 产品专员已经处理完成
							return "产品经理待审批" ;
						}else  if(status == 5){
							return "产品经理审批完成" ;
						}else  if(status == 6){
							return "总经理待审批" ;
						}else  if(status ==7){
							return "总经理审批完成" ;
						}
						return status ;
					}}
					
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					 return $(window).height() - 350 ;
				 },
				 title:"产品列表",
				 indexColumn: false,
				 querys:{planId:'----',sqlId:'sql_pdev_filter_details'},//status:$("[name='status']").val(),type:type,
				 loadMsg:"数据加载中，请稍候......"
			}) ;

			
   	 });