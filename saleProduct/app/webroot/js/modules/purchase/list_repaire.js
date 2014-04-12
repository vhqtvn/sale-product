     var type = '4' ;//查询已经审批通过

	 function formatMoney(val){
	 	val = $.trim(val+"") ;
	 	val = val.replace("$","") ;
	 	return $.trim(val) ;
	 }

	$(function(){
		
		function loadStatics(){
			$.dataservice("model:NewPurchaseService.loadRepaireStatics",{},function(result){
				 //$(".grid-content-details").llygrid("reload",{},true) ;
				$(".flow-node").find(".count").html("") ;
				$(result).each(function(){
					$(".flow-node[status='"+this.STATUS+"']").find(".count").html("("+this.COUNT+")") ;//this.COUNT
				}) ;
				
				setTimeout(function(){
					loadStatics() ;
				},5000) ;
			 },{noblock:true});
			
		}
		
		loadStatics() ;

		$(".create-purchase-product").live("click",function(){
			openCenterWindow(contextPath+"/page/forward/Purchase.create_purchase_product/",980,620,function(win,ret){
				if(ret){
					$(".grid-content-details").llygrid("reload",{},true) ;
				}
			}) ;
		}) ;
		
		$(".flow-node").click(function(){
			var status = $(this).attr("status");
			$(".flow-node").removeClass("active").addClass("disabled");
			$(this).removeClass("disabled").addClass("active");
			var params = {} ;
			params['status1'] = '' ;
			params['status2'] = '' ;
			params[ 'status'+ status ] = status ;
			$(".grid-content-details").llygrid("reload",params,true);
		}) ;
		
			
			$(".grid-content-details").llygrid({
				columns:[
					//{align:"center",key:"ID",label:"编号",width:"4%"},
					{align:"left",key:"ID",label:"操作",forzen:false,width:"4%",format:function(val,record){
						return '<a href="#" title="处理" class="edit-action" val="'+val+'"><img src="/'+fileContextPath+'/app/webroot/img/edit.png"/></a>&nbsp;';
						/*var isSku =( record.SKU||record.REAL_PRODUCT_SKU)?true:false ;
						
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
						return html.join("") ;	*/
					}},
					{align:"left",key:"STATUS",label:"状态",forzen:false,width:"6%",format: function(val ,record){
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
					{align:"center",key:"TRACK_MEMO",label:"",width:"10%",forzen:false,align:"left"},
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
				 querys:{sqlId:"sql_purchase_new_listForRepaire"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
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
				openCenterWindow(contextPath+"/page/forward/Purchase.edit_purchase_product/"+val,980,620,function(win,ret){
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
   	 