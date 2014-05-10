     var type = '4' ;//查询已经审批通过

	 function formatMoney(val){
	 	val = $.trim(val+"") ;
	 	val = val.replace("$","") ;
	 	return $.trim(val) ;
	 }

	$(function(){
		
		function loadStatics(){
			$.dataservice("model:NewPurchaseService.loadStatics",{},function(result){
				 //$(".grid-content-details").llygrid("reload",{},true) ;
				$(".flow-node").find(".count").html("(0)") ;
				var count = 0 ;
				var map = {} ;
				$(result).each(function(){
					map[this.STATUS+"_"] = parseInt(this.COUNT) ;
					//$(".flow-node[status='"+this.STATUS+"']").find(".count").html("("+this.COUNT+")") ;//this.COUNT
					count +=parseInt(this.COUNT) ;
				}) ;
				//alert( $.json.encode(map) ) ;
				$(".flow-node").each(function(){
					var status = $(this).attr("status") ;
					var ss = (status+"").split(",") ;
					var c = 0 ;
					$(ss).each(function(index,item){
						if(!item)return ;
						c = (map[item+"_"]||0) +c;
					}) ;
					$(".flow-node[status='"+status+"']").find(".count").html("("+c+")") ;
				}) ;
				
				$(".total").find(".count").html("("+count+")") ;

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
			$(".grid-content-details").llygrid("reload",{status:status},true);
		}) ;
		
			
			$(".grid-content-details").llygrid({
				columns:[
					//{align:"center",key:"ID",label:"编号",width:"4%"},
					/*{align:"center",key:"REQ_PRODUCT_ID",label:"",forzen:false,width:"2%",render:function(record){
						if(record.REQ_PRODUCT_ID)$(this).find("td[key='REQ_PRODUCT_ID']")
							.css("background","red").attr("title","自动采购单") ;
					},format:function(){
						return "" ;
					}},*/
					{align:"center",key:"ID",label:"操作",forzen:false,width:"8%",format:function(val,record){
						var status = record.STATUS ;
						var html = [] ;
						html.push('<a href="#" title="处理" class="edit-action" val="'+val+'"><img src="/'+fileContextPath+'/app/webroot/img/edit.png"/></a>&nbsp;');
						if(status == 80 || status==25 ){
							//
						}else{
							html.push( getImage("print.gif","打印采购确认单","print-product") +"&nbsp;") ;
			            	html.push( getImage("print.gif","打印入库单","print-inproduct") ) ;
						}
						return  html.join("");
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
					},render:function(record){
						var tsq = record.TOTAL_SUPPLY_QUANTITY ;
						if( tsq === 0 ||  (tsq>0 && tsq<10) ){
							$(this).find("[key='STATUS']").css("background","red").attr("title","紧急采购") ;
						}
					} },
		           	{align:"center",key:"EXECUTOR_NAME",label:"执行用户",width:"6%",forzen:false,align:"left"},
		           	{align:"center",key:"LIMIT_PRICE",label:"采购限价",width:"5%"},
		           	{align:"center",key:"IMAGE_URL",label:"",width:"3%",forzen:false,align:"center",format:{type:'img'}},
		           	{align:"center",key:"CODE",label:"编号",width:"15%",forzen:false,align:"left"},
		           	{align:"center",key:"TITLE",label:"标题",width:"15%",forzen:false,align:"left",format:function(val,record){
		           		var style =  record.REAL_PROVIDOR_NAME_?"":"style='color:green;'"
		           		return "<div "+style+">"+val+"</div>" ;
		           	}},
					{align:"left",key:"REAL_SKU",label:"货品SKU", width:"8%",format:{type:'realSku'}},
					{align:"left",key:"PROVIDOR_NAME",label:"供应商", width:"10%",group:"计划采购",format:function(val,record){
						return "<a href='#'  supplier-id='"+record.PROVIDOR+"'>"+(val||"")+"</a>" ;
					}},
		           	{align:"center",key:"PLAN_NUM",label:"采购数量",width:"5%",group:"计划采购"},
		           	{align:"center",key:"QUOTE_PRICE",label:"报价",width:"5%",group:"计划采购"},
		           	{align:"center",key:"QUOTE_PRICE",label:"运费",width:"5%",group:"计划采购",format:function(val,record){
		           		var shipFeeType = record.SHIP_FEE_TYPE ;
		           		if(shipFeeType == 'by') return "卖家承担" ;
		           		return record.SHIP_FEE||"" ;
		           	}},
		           	{align:"left",key:"REAL_PROVIDOR_NAME_",label:"供应商", width:"10%",group:"上次采购",format:function(val,record){
						return "<a href='#'  supplier-id='"+record.REAL_PROVIDOR_+"'>"+(val||"")+"</a>" ;
					}},
		           	{align:"center",key:"QUALIFIED_PRODUCTS_NUM_",label:"采购数量",width:"5%",group:"上次采购"},
		           	{align:"center",key:"REAL_QUOTE_PRICE_",label:"采购价",width:"5%",group:"上次采购"},
		           	{align:"center",key:"REAL_SHIP_FEE_TYPE_",label:"运费",width:"5%",group:"上次采购",format:function(val,record){
		           		var shipFeeType = record.REAL_SHIP_FEE_TYPE_ ;
		           		if(shipFeeType == 'by') return "卖家承担" ;
		           		return record.REAL_SHIP_FEE_||"" ;
		           	}},
		           	{align:"center",key:"REAL_PURCHASE_DATE_",label:"采购时间",width:"10%",group:"上次采购"},
		           	{align:"center",key:"REAL_PURCHASE_NUM",label:"实际采购数量",width:"7%"},
		           	{align:"center",key:"TRACK_MEMO",label:"",width:"10%",forzen:false,align:"left"},
		        	{align:"center",key:"CREATOR_NAME",label:"发起人",width:"6%",forzen:false,align:"left"},
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
				 querys:{sqlId:"sql_purchase_new_list"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
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
				 	
				 	 $(".print-product").bind("click",function(event){
						 event.stopPropagation() ;
						 var record = $(this).parents("tr:first").data("record");
				
						if( ((record.STATUS==1||record.STATUS=='null'||!record.STATUS) && window.confirm("是否确认打印，如果点击确定，该任务单将不能更改！") ) || record.STATUS >1){
							var val = record.ID ;
							openCenterWindow(contextPath+"/page/forward/Purchase.purchaseTaskPrint/"+val,1000,700) ;
						}
					}) ;
					 
					 $(".print-inproduct").bind("click",function(event){
						 event.stopPropagation() ;
						 var record = $(this).parents("tr:first").data("record");
							var val = record.ID ;
							openCenterWindow(contextPath+"/page/forward/Purchase.purchaseInPrint/"+val,1000,700) ;
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
   	 