$(function(){
	$(".create-task").click(function(){
		openCenterWindow(contextPath+"/page/forward/Sale.createPurchaseTask/",730,380,function(){
			$(".grid-task").llygrid("reload",{}) ;
		}) ;
	}) ;
	
	$(".grid-task").llygrid({
		columns:[
		    {align:"left",key:"TASK_CODE",label:"编号", width:"15%"},
		    {align:"center",key:"START_TIME",label:"开始时间",width:"10%" },
		    {align:"center",key:"END_TIME",label:"结束时间",width:"10%" },
		    {align:"left",key:"STATUS",label:"状态", width:"5%",format:{type:'json',content:{1:'编辑中',2:'采购中',3:'采购完成'}}},
		    
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
           	
		    {align:"left",key:"MEMO",label:"备注", width:"25%"},
           	{align:"center",key:"LAST_UPDATED_TIME",label:"操作时间",width:"15%" },
            {align:"left",key:"EXECUTOR_NAME",label:"操作用户",width:"5%" },
            {align:"left",key:"ID",label:"操作",width:"10%" ,format:function(val,record){
            	var html = [] ;
            	if( record.STATUS==1 && editPermission) {
            		html.push( getImage("delete.gif","删除任务","btn-delete-plan") ) ;
            		html.push( getImage("pkg.gif","选择货品","btn-select-product") ) ;
            	}
            	
            	if( editPermission  ){
            		html.push( getImage("edit.png","编辑任务","btn-edit-plan")+"&nbsp;" ) ;
            		html.push( getImage("print.gif","打印采购确认单","print-product") +"&nbsp;") ;
            	}
            	
            	html.push( getImage("print.gif","打印入库单","print-inproduct") ) ;
            	//html.push('<a href="#" class="print-product" val="'+val+'">打印</a>') ;
            	return html.join("") ;
            },permission:function(){
            	return true;
            }}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return 150 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{ sqlId:"sql_purchase_task_list" },
		 loadMsg:"数据加载中，请稍候......",
		 loadAfter:function(){
			 $(".status-action").click(function(event){
				 event.stopPropagation() ;
				 var taskId = $(this).attr("planId") ;
					var status = $(this).attr("status") ;
					
					var params = {} ;
					params.taskId = taskId ;
					if(status == 1){
						params.status1 = 1 ;
					}else{
						params.status = status ;
					}
					$(".grid-task-product").llygrid("reload",params) ;
					return false ;
			 }) ;
			 
			 $(".btn-delete-plan").bind("click",function(event){
				 event.stopPropagation() ;
				 var record = $(this).parents("tr:first").data("record");
				 if( (record.STATUS==1 && window.confirm("是否确认删除该任务？") )){
					var val = record.ID ;
					$.dataservice("model:Sale.deletePurchaseTask",{taskId:record.ID},function(){
						$(".grid-task").llygrid("reload",{},true) ;
						$(".grid-task-product").llygrid("reload",{taskId:'--'}) ;
					}) ;
				 }
			}) ;
			 
			 $(".btn-edit-plan").bind("click",function(){
				 event.stopPropagation() ;
				 var record = $(this).parents("tr:first").data("record");
				 openCenterWindow(contextPath+"/page/forward/Sale.createPurchaseTask/"+record.ID,730,380,function(){
						$(".grid-task").llygrid("reload",{},true) ;
					}) ;
				 return false ;
			 }) ;
			 
			 $(".print-product").bind("click",function(event){
				 event.stopPropagation() ;
				 var record = $(this).parents("tr:first").data("record");
				if( (record.STATUS==1 && window.confirm("是否确认打印，如果点击确定，该任务单将不能更改！") ) || record.STATUS >1){
					var val = record.ID ;
					openCenterWindow(contextPath+"/page/forward/Sale.purchaseTaskPrint/"+val,1000,700) ;
				}
			}) ;
			 
			 $(".print-inproduct").bind("click",function(event){
				 event.stopPropagation() ;
				 var record = $(this).parents("tr:first").data("record");
					var val = record.ID ;
					openCenterWindow(contextPath+"/page/forward/Sale.purchaseInPrint/"+val,1000,700) ;
			}) ;
			 
			 $(".btn-select-product").click(function(){
				 var record = $(this).parents("tr:first").data("record");
				 var taskId = record.ID ;
				 
				 var productGridSelect = {
							title:'货品选择页面',
							defaults:[],//默认值
							key:{value:'ID',label:'SKU'},//对应value和label的key
							multi:true,
							indexColumn:false,
							grid:{
								title:"货品选择",
								params:{
									sqlId:"sql_purchase_task_product_selectable",
									taskId:taskId
								},
								ds:{type:"url",content:contextPath+"/grid/query"},
								pagesize:10,
								columns:[//显示列
									{align:"center",key:"ID",label:"编号",width:"100"},
									{align:"left",key:"STATUS",label:"状态",forzen:false,width:"7%",format:{type:'purchaseProductStatus'}},
									{align:"left",key:"PLAN_TIME",label:"采购时限",width:"15%",format:function(val,record){
						           		var r = record.PLAN_START_TIME||"" ;
						           		var r1 = record.PLAN_END_TIME||"" ;
						           		return $.trim(r.replace("00:00:00","")) +(r1?"到":"")+ $.trim(r1.replace("00:00:00","")) ;
						           	}},
									{align:"left",key:"SKU",label:"货品SKU", width:"8%",format:{type:'realSku'}},
						           	{align:"center",key:"IMAGE_URL",label:"Image",width:"4%",forzen:false,align:"left",format:{type:'img'}},
						           	{align:"center",key:"TITLE",label:"标题",width:"10%",forzen:false,align:"left"},
						        	{align:"center",key:"searchKey",label:"关键字",hidden:true,query:true,align:"left"},
						        	{align:"center",key:"EXECUTOR_NAME",label:"执行用户",width:"6%",forzen:false,align:"left"},
						           	{align:"center",key:"SUPPIERABLE_NUM",label:"可采购",width:"5%"},
						        	{align:"center",key:"PLAN_NUM",label:"计划采购",width:"5%"},
						        	{align:"center",key:"QUALIFIED_PRODUCTS_NUM",label:"已采购",width:"5%"},
						           	{align:"center",key:"QUOTE_PRICE",label:"采购限价",width:"5%"},
						           	{align:"center",key:"AREA",label:"采购地区",width:"6%",
						           			format:{type:"json",content:{"china":"大陆","taiwan":"台湾","american":"美国"}}},
						          
						           	{align:"center",key:"PROVIDOR_NAME",label:"供应商信息",width:"12%",format:function(val,record){
						           		if(!val) return "";
						           		return "<a href='#' supplier-id='"+record.PROVIDOR+"'>"+val+"</a>" ;
						           	}} ,
						           	{align:"center",key:"SAMPLE",label:"样品",format:{type:"json",content:{'0':'无','1':'准备中','2':'有'}},width:"6%"},
						            {align:"center",key:"SAMPLE_CODE",label:"样品编码",width:"8%"}
								]
							}
					   } ;
			
				 $.listselectdialog( productGridSelect,function(  ){
						var args = jQuery.dialogReturnValue() ;
						var value = args.value ;
						var label = args.label ;
						//$("#executor").val(value) ;
						
						$.dataservice("model:Sale.savePurchaseTaskProducts",{taskId:record.ID,products:value.join(",")},function(){
							$(".grid-task-product").llygrid("reload",{},true) ;
						}) ;
						
						//$("#executorName").val(label) ;
						return false;
					}) ;
			 }) ;

		 },
		 rowClick:function(rowIndex,rowData){
			 $(".grid-task-product").llygrid("reload",{taskId:rowData.ID}) ;
		 }
	}) ;
	
	window.StatusClick = function (el){
		return false ;
	} ;
	
	$(".grid-task-product").llygrid({
		columns:[
			//{align:"center",key:"ID",label:"编号",width:"4%"},
			{align:"left",key:"ID",label:"操作",forzen:false,width:"3%",format:function(val,record){
				var isSku = record.SKU?true:false ;
				
				var status = record.STATUS ;
				var html = [] ;

			//	if(status == 4 || status == 6){
				isSku && html.push('<a href="#" title="查看" class="edit-action" val="'+val+'"><img src="/'+fileContextPath+'/app/webroot/img/pre_print.gif"/></a>&nbsp;') ;
			
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
        	{align:"center",key:"SUPPIERABLE_NUM",label:"可采购",group:'采购数量',width:"5%"},
        	{align:"center",key:"PLAN_NUM",label:"计划采购",group:'采购数量',width:"5%"},
        	{align:"center",key:"QUALIFIED_PRODUCTS_NUM",label:"已采购",group:'采购数量',width:"5%"},
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
		 	return $(window).height() - 390 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{taskId:'',sqlId:"sql_purchase_task_product_list"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
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
	
	$(".edit-action").live("click",function(){
		var val = $(this).attr("val") ;//采购计划ID
		openCenterWindow(contextPath+"/sale/editPurchasePlanProduct/"+val,910,620,function(){
			$(".grid-content-details").llygrid("reload",{},true) ;
		}) ;
	}) ;
	
}) ;