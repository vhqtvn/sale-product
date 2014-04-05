     var type = '4' ;//查询已经审批通过

	 function formatMoney(val){
	 	val = $.trim(val+"") ;
	 	val = val.replace("$","") ;
	 	return $.trim(val) ;
	 }

	$(function(){

			$(".grid-content").llygrid({
				columns:[
					{align:"center",key:"CODE",label:"采购计划编号",width:"12%",forzen:false,align:"left"},
		           	{align:"center",key:"NAME",label:"采购计划名称",width:"14%",forzen:false,align:"left"},
		           	{align:"left",key:"PLAN_TIME",label:"计划采购时间",width:"15%",format:function(val,record){
		           		var r = record.PLAN_TIME||"" ;
		           		var r1 = record.PLAN_END_TIME||"" ;
		           		return $.trim(r.replace("00:00:00","")) +(r1?"到":"")+ $.trim(r1.replace("00:00:00","")) ;
		           	}},
		           	{align:"center",key:"TYPE",label:"采购用途",width:"6%",format:function(val,record){
		           		if(val == 1){
		           			return "产品试销" ;
		           		}else if(val == 2){
		           			return "正式采购" ;
		           		}else if(val == 3){
		           			return "开发采购" ;
		           		}
		           	}},	
		           	{align:"center",key:"STATUS0",label:'全部',group:"状态",width:"4%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action' planId='"+record.ID+"' status=''>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS25",label:img25,group:"状态",width:"4%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=25>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS80",label:img8,group:"状态",width:"3%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=80>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"EXECUTOR_NAME",label:"负责人",width:"4%"},
		           	{align:"center",key:"USERNAME",label:"创建人",width:"4%"},
		           	{align:"center",key:"CREATE_TIME",label:"创建时间",width:"10%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:5,
				 pageSizes:[5,10,20,30,40],
				 height:130,
				 title:"筛选列表",
				 indexColumn:false,
				 querys:{executor:loginId,sqlId:'sql_purchase_plan_list_done'},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	if(isLinkClick){
				 		isLinkClick = false ;
				 		 return ;
				 	}
				 	
				 	var planId = rowData.ID  ;
				 	$(".grid-content-details").llygrid("reload",{planId:planId,status:""}) ;
				 },loadAfter:function(){
				
				 }
			}) ;
			
			var isLinkClick = false ;
			window.StatusClick = function (el){
			 	isLinkClick = true ;
				var planId = $(el).attr("planId") ;
				var status = $(el).attr("status") ;
				
				var params = {} ;
				params.planId = planId ;
				if(status == 1){
					params.status1 = 1 ;
				}else{
					params.status = status ;
				}
				$(".grid-content-details").llygrid("reload",params) ;
				return false ;
			} ;

			$(".plan-query").click(function(){
				$(".grid-content").llygrid("reload",{name:$("#name").val(),code:$("#code").val()}) ;
			}) ;
			
			$(".create-plan").click(function(){
				openCenterWindow(contextPath+"/sale/createPurchasePlan/",600,460,function(win,ret){
					if(ret)$(".grid-content").llygrid("reload",{}) ;
				}) ;
			}) ;
			
			$(".grid-content-details").llygrid({
				columns:[
					{align:"left",key:"TASK_STATUS",label:"状态",forzen:false,width:"8%",format: function(val ,record){
						if(val){
							return $.llygrid.format.purchaseProductStatus.body(val)  ;
						}
						
						val = record.STATUS || 10 ;
						var message = "" ;
						switch(val){
							case '10':  message = "编辑中";break;
							case '20':  message = "等待审批";break;
							case '25':  message = "审批不通过";break;
							case '30':  message = "限价确认";break;
							case '40':  message = "分配执行人";break;
							case '41':  message = "--";break;
						}
						if(val>25)message = "待采购" ;
						
						return message ;
					} },
					{align:"left",key:"PLAN_TIME",label:"采购时限",width:"14%",format:function(val,record){
		           		var r = record.PLAN_START_TIME||"" ;
		           		var r1 = record.PLAN_END_TIME||"" ;
		           		return $.trim(r.replace("00:00:00","")) +(r1?"到":"")+ $.trim(r1.replace("00:00:00","")) ;
		           	}},
					{align:"left",key:"REAL_PRODUCT_SKU",label:"货品SKU", width:"8%",format:{type:'realSku'}},
		           	{align:"center",key:"IMAGE_URL",label:"Image",width:"4%",forzen:false,align:"left",format:{type:'img'}},
		           	{align:"center",key:"TITLE",label:"标题",width:"15%",forzen:false,align:"left"},
		        	{align:"center",key:"EXECUTOR_NAME",label:"执行用户",width:"6%",forzen:false,align:"left"},
		        	{align:"center",key:"CREATOR_NAME",label:"发起人",width:"6%",forzen:false,align:"left"},
		           	{align:"center",key:"PLAN_NUM",label:"计划采购数量",width:"7%"},
		           	{align:"center",key:"REAL_PURCHASE_NUM",label:"实际采购数量",width:"7%"},
		           	{align:"center",key:"LIMIT_PRICE",label:"采购限价",width:"5%"},
		           	{align:"center",key:"CREATE_TIME",label:"创建时间",width:"11%",forzen:false,align:"left"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 370 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{planId:'',status:"",sqlId:"sql_purchase_plan_details_listForSKU_done"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
					
				 	$(".grid-checkbox").each(function(){
						var val = $(this).attr("value") ;
						if( $(".product-list ul li[asin='"+val+"']").length ){
							$(this).attr("checked",true) ;
						}
					}) ;
				 }
			}) ;
			
			$(".cost").live("click",function(){
				var asin = $(this).attr("asin") ;
				var type = $(this).attr("type") ;
				openCenterWindow(contextPath+"/cost/view/"+asin+"/"+type,600,600) ;
				return false ;
			}) ;
		
			$(".process-action").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow(contextPath+"/sale/productFilter/"+val+"/"+type,900,600) ;
			}) ;
			
   	 });
   	 