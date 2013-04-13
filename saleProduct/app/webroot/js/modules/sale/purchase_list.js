     var type = '4' ;//查询已经审批通过

	 function formatMoney(val){
	 	val = $.trim(val+"") ;
	 	val = val.replace("$","") ;
	 	return $.trim(val) ;
	 }

	$(function(){

			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"",width:"8%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						if( status == 1){
							if( $pp_edit  ){
								if( record.STATUS0 <= 0 ) html.push('<img title="删除" class="delete_purchase_plan" val="'+val+'" src="/'+fileContextPath+'/app/webroot/img/delete.gif"/>&nbsp;');
								html.push('<img title="修改" class="edit_purchase_plan" val="'+val+'" src="/'+fileContextPath+'/app/webroot/img/edit.png"/>&nbsp;');
							}
							$ppp_add_product && html.push('<img title="添加货品" class="add-outer-product" val="'+val+'" src="/'+fileContextPath+'/app/webroot/img/add.png"/>&nbsp;');
							$ppp_export && html.push('<img title="导出" class="export-product" val="'+val+'" src="/'+fileContextPath+'/app/webroot/img/excel.gif"/>');
							return html.join("") ;
						}
						return "" ;
					},permission:function(){
						return $pp_edit || $ppp_add_product ||  $ppp_export;
					}},
					{align:"center",key:"CODE",label:"采购计划编号",width:"11%",forzen:false,align:"left"},
		           	{align:"center",key:"NAME",label:"采购计划名称",width:"14%",forzen:false,align:"left"},
		           	{align:"left",key:"PLAN_TIME",label:"计划采购时间",width:"15%",format:function(val,record){
		           		var r = record.PLAN_TIME||"" ;
		           		var r1 = record.PLAN_END_TIME||"" ;
		           		return $.trim(r.replace("00:00:00","")) +(r1?"到":"")+ $.trim(r1.replace("00:00:00","")) ;
		           	}},
		           	{align:"center",key:"TYPE",label:"采购用途",width:"6%",format:function(val,record){
		           		if(val == 1){
		           			return "产品试销" ;
		           		}else{
		           			return "正式采购" ;
		           		}
		           	}},	
		           	{align:"center",key:"STATUS0",label:'全部',group:"状态",width:"4%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action' planId='"+record.ID+"' status=''>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS10",label:img1,group:"状态",width:"4%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action' planId='"+record.ID+"' status=10>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS20",label:img2,group:"状态",width:"4%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=20>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS25",label:img25,group:"状态",width:"4%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=25>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS30",label:img3,group:"状态",width:"4%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=30>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS40",label:img4,group:"状态",width:"4%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=40>"+val+"</a>" ;
		           	}},
		        	{align:"center",key:"STATUS45",label:img45,group:"状态",width:"4%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=45>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS50",label:img5,group:"状态",width:"3%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=50>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS60",label:img6,group:"状态",width:"3%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=60>"+val+"</a>" ;
		           	}},
		           	{align:"center",key:"STATUS70",label:img7,group:"状态",width:"3%",format:function(val,record){
		           		return "<a href='javascript:void(0)' onClick='StatusClick(this)' class='status-action'  planId='"+record.ID+"' status=70>"+val+"</a>" ;
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
				 querys:{executor:loginId,sqlId:'sql_purchase_plan_list'},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	if(isLinkClick){
				 		isLinkClick = false ;
				 		 return ;
				 	}
				 	
				 	var planId = rowData.ID  ;
				 	$(".grid-content-details").llygrid("reload",{planId:planId,status:""}) ;
				 },loadAfter:function(){
					 $(".edit_purchase_plan").bind("click",function(event){
						 	event.stopPropagation() ;
							var val = $(this).attr("val") ;//采购计划ID
							openCenterWindow(contextPath+"/sale/createPurchasePlan/"+val,600,460,function(){
								$(".grid-content").llygrid("reload",{}) ;
							}) ;
							return false;
						}) ;
						
						$(".delete_purchase_plan").bind("click",function(event){
							event.stopPropagation() ;
							var val = $(this).attr("val") ;//采购计划ID
							if(window.confirm("确认删除该采购计划吗？")){
								$.dataservice("model:Sale.deletePurchasePlan",{planId:val},function(){
									$(".grid-content").llygrid("reload",{},true) ;
									$(".grid-content-details").llygrid("reload",{planId:'---'}) ;
								}) ;
								
							}
						}) ;
						
						$(".add-outer-product").bind("click",function(event){
							event.stopPropagation() ;
							var val = $(this).attr("val") ;
							//openCenterWindow(contextPath+"/sale/addPurchasePlanOuterProduct/"+val,600,400) ;
							openCenterWindow(contextPath+"/page/forward/Sale.selectPurchaseProduct/"+val,900,600,function(){
								$(".grid-content").llygrid("reload",{},true) ;
							}) ;
							return false ;
						});
						
						$(".export-product").bind("click",function(event){
							event.stopPropagation() ;
							var val = $(this).attr("val") ;//采购计划ID
							$("#exportIframe").attr("src",contextPath+"/sale/exportForPurchasePlanDetails/"+val) ;
							return false ;
						}) ;
						
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

			$(".query-btn").click(function(){
				$(".grid-content").llygrid("reload",{name:$("#name").val(),type:$("#type").val()}) ;
			}) ;
			
			$(".create-plan").click(function(){
				openCenterWindow(contextPath+"/sale/createPurchasePlan/",600,460,function(){
					$(".grid-content").llygrid("reload",{}) ;
				}) ;
			}) ;
			
			$(".grid-content-details").llygrid({
				columns:[
					//{align:"center",key:"ID",label:"编号",width:"4%"},
					{align:"left",key:"ID",label:"操作",forzen:false,width:"4%",format:function(val,record){
						var isSku = record.SKU?true:false ;
						
						var status = record.STATUS ;
						var html = [] ;
						
						if( status <=10 ){
							html.push( getImage("delete.gif","删除","delete-action") ) ;
						}

						if(status == 80 || status==25 ){
							isSku && html.push('<a href="#" title="查看" class="edit-action" val="'+val+'"><img src="/'+fileContextPath+'/app/webroot/img/pre_print.gif"/></a>&nbsp;') ;
						}else{
							isSku && html.push('<a href="#" title="处理" class="edit-action" val="'+val+'"><img src="/'+fileContextPath+'/app/webroot/img/edit.png"/></a>&nbsp;') ;
							
						}
						return html.join("") ;	
					}},
					{align:"left",key:"STATUS",label:"状态",forzen:false,width:"7%",format:{type:'purchaseProductStatus'}},
					{align:"left",key:"PLAN_TIME",label:"采购时限",width:"15%",format:function(val,record){
		           		var r = record.PLAN_START_TIME||"" ;
		           		var r1 = record.PLAN_END_TIME||"" ;
		           		return $.trim(r.replace("00:00:00","")) +(r1?"到":"")+ $.trim(r1.replace("00:00:00","")) ;
		           	}},
					{align:"left",key:"SKU",label:"货品SKU", width:"8%",format:{type:'realSku'}},
		           	{align:"center",key:"IMAGE_URL",label:"Image",width:"4%",forzen:false,align:"left",format:{type:'img'}},
		           	{align:"center",key:"TITLE",label:"标题",width:"10%",forzen:false,align:"left"},
		        	{align:"center",key:"EXECUTOR_NAME",label:"执行用户",width:"6%",forzen:false,align:"left"},
		           	{align:"center",key:"PLAN_NUM",label:"采购数量",width:"5%"},
		           	{align:"center",key:"QUOTE_PRICE",label:"采购价",width:"5%"},
		           	{align:"center",key:"AREA",label:"采购地区",width:"6%",
		           			format:{type:"json",content:{"china":"大陆","taiwan":"台湾","american":"美国"}}},
		          
		           	{align:"center",key:"PROVIDOR_NAME",label:"供应商信息",width:"12%",format:function(val,record){
		           		if(!val) return "";
		           		return "<a href='#' supplier-id='"+record.PROVIDOR+"'>"+val+"</a>" ;
		           	}} ,
		           	{align:"center",key:"SAMPLE",label:"样品",format:{type:"json",content:{'0':'无','1':'准备中','2':'有'}},width:"6%"},
		            {align:"center",key:"SAMPLE_CODE",label:"样品编码",width:"8%"}
		           	
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 370 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{planId:'-----',status:"",sqlId:"sql_purchase_plan_details_listForSKU"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
					 $(".delete-action").click(function(){
						 var record = $(this).parents("tr:first").data("record");
						 if(window.confirm("确认删除吗？")){
							 $.dataservice("model:Sale.deletePurchasePlanProduct",{id:record.ID},function(){
								 $(".grid-content-details").llygrid("reload",{},true) ;
							 });
						 }
					 }) ;
					 
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
			
			$(".edit-action").live("click",function(){
				var val = $(this).attr("val") ;//采购计划ID
				openCenterWindow(contextPath+"/sale/editPurchasePlanProduct/"+val,910,620,function(){
					$(".grid-content-details").llygrid("reload",{},true) ;
				}) ;
			}) ;

			$(".process-action").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow(contextPath+"/sale/productFilter/"+val+"/"+type,900,600) ;
			}) ;
			
   	 });
   	 