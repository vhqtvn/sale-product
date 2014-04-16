     var type = '4' ;//查询已经审批通过

	 function formatMoney(val){
	 	val = $.trim(val+"") ;
	 	val = val.replace("$","") ;
	 	return $.trim(val) ;
	 }

	$(function(){
		
			$(".grid-content-details").llygrid({
				columns:[
					//{align:"center",key:"ID",label:"编号",width:"4%"},
					{align:"center",key:"REQ_PRODUCT_ID",label:"",forzen:false,width:"2%",render:function(record){
						if(record.REQ_PRODUCT_ID)$(this).find("td[key='REQ_PRODUCT_ID']")
							.css("background","red").attr("title","自动采购单") ;
					},format:function(){
						return "" ;
					}},
					{align:"center",key:"ID",label:"操作",forzen:false,width:"8%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						html.push('<a href="#" title="处理" class="edit-action" val="'+val+'"><img src="/'+fileContextPath+'/app/webroot/img/edit.png"/></a>&nbsp;');
						return  html.join("");
					}},
					{align:"left",key:"STATUS",label:"状态",forzen:false,width:"6%",format: function(val ,record){
						
						return "采购审计" ;
					} },
		           	{align:"center",key:"TRACK_MEMO",label:"",width:"10%",forzen:false,align:"left"},
		           	{align:"center",key:"CODE",label:"编号",width:"15%",forzen:false,align:"left"},
		           	{align:"center",key:"IMAGE_URL",label:"",width:"3%",forzen:false,align:"center",format:{type:'img'}},
		           	{align:"center",key:"TITLE",label:"标题",width:"15%",forzen:false,align:"left"},
					{align:"left",key:"REAL_SKU",label:"货品SKU", width:"8%",format:{type:'realSku'}},
					{align:"left",key:"START_TIME",label:"采购时限",width:"14%",format:function(val,record){
		           		var r = record.START_TIME||"" ;
		           		var r1 = record.END_TIME||"" ;
		           		return $.trim(r.replace("00:00:00","")) +(r1?"到":"")+ $.trim(r1.replace("00:00:00","")) ;
		           	}},
		        	{align:"center",key:"EXECUTOR_NAME",label:"执行用户",width:"6%",forzen:false,align:"left"},
		        	{align:"center",key:"CREATOR_NAME",label:"发起人",width:"6%",forzen:false,align:"left"},
		           	{align:"center",key:"PLAN_NUM",label:"计划采购数量",width:"7%"},
		           	{align:"center",key:"REAL_PURCHASE_NUM",label:"实际采购数量",width:"7%"},
		           	{align:"center",key:"LIMIT_PRICE",label:"采购限价",width:"5%"},
		           	{align:"center",key:"CREATED_DATE",label:"创建时间",width:"11%",forzen:false,align:"left"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - 170 ;
				 },
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:"sql_purchase_new_list_audit"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
					
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
				openCenterWindow(contextPath+"/page/forward/Purchase.edit_purchase_product/"+val+"/audit",980,620,function(win,ret){
					if(ret){
						$(".grid-content-details").llygrid("reload",{},true) ;
					}
					
				}) ;
			}) ;

			$(".process-action").live("click",function(){
				var val = $(this).attr("val") ;
				openCenterWindow(contextPath+"/sale/productFilter/"+val+"/"+type,900,600) ;
			}) ;
			
   	 });
   	 