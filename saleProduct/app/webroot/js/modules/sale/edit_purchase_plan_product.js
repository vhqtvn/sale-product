$(function(){
	 function formatMoney(val){
		 	val = $.trim(val+"") ;
		 	val = val.replace("$","") ;
		 	return $.trim(val) ;
	 }

	 //设置页面是否刻度

	 $("#personForm   :input").attr("disabled",'disabled') ;
	 $("."+currentStatus+"-input").removeAttr("disabled") ;
	
	var tabs = [{label:'基本信息',content:"base-info"}] ;
	if( $("#ref-asins").length ){
		tabs.push({label:'关联ASIN',content:"ref-asins"}) ;
	}
	tabs.push( {label:'处理轨迹',content:"tracks"} ) ;
	if( currentStatus >=50 )tabs.push( {label:'货品询价',content:"supplier-tab"} ) ;
	//
	//widget init
	var tab = $('#details_tab').tabs( {
		tabs:tabs ,
		height:function(){
			return $(window).height() - 135 ;
		}
	} ) ;
	
	$(".grid-track").llygrid({

		columns:[
		    {align:"left",key:"MESSAGE",label:"内容", width:"31%"},
           	{align:"center",key:"CREATE_TIME",label:"操作时间",width:"24%" },
            {align:"left",key:"CREATOR_NAME",label:"操作人",width:"10%" },
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:30,
		 pageSizes:[10,20,30,40],
		 height:function(){
		 	return $(window).height() - 370 ;
		 },
		 title:"",
		 indexColumn:false,
		 querys:{pdId:id,sqlId:"sql_purchase_plan_product_listTracks"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	$purchase_cost_view = true ;
	
	if( $("#ref-asins").length ){
		$(".grid-content-details").llygrid({
			columns:[
			    {align:"center",key:"SKU",label:"Listing SKU",width:"14%",forzen:false,align:"left"},
	           	{align:"left",key:"ASIN",label:"ASIN", width:"11%",format:{type:'asin'}},
	           	{align:"center",key:"LOCAL_URL",label:"Image",width:"4%",forzen:false,align:"left",format:{type:'img'}},
	            {align:"left",key:"TITLE",label:"TITLE",width:"10%",forzen:false,align:"left",format:{type:'titleListing'}},
	            {align:"center",key:"QUANTITY",label:"账号库存",width:"6%",forzen:false,align:"left"},
	           	{align:"center",key:"XJ",label:"询价",width:"4%",format:function(val,record){
	           		if(val >0 )return "Y" ;
	           		return "N" ;
	           	}} ,
	           {align:"center",key:"FBM_COST",label:"其他成本",group:"FBM",width:"6%",format:function(val,record){
	           		return "<a href='' class='cost' type='FBM' asin='"+record.ASIN+"'>"+(val||"")+"</a>" ;
	           	},permission:function(){ return $purchase_cost_view; } },
	           	{align:"center",key:"FBM_PRICE",label:"最低价",group:"FBM",width:"6%",permission:function(){ return $purchase_cost_view; }},
	           	{align:"center",key:"FBM_PRICE",label:"利润额",group:"FBM",width:"6%",format:function(val,record){
	           		var lye = parseFloat(formatMoney(record.FBM_PRICE)) 
	           					- parseFloat(formatMoney(record.QUOTE_PRICE||0)) -   parseFloat(formatMoney(record.FBM_COST||0)) ;
	           		
	           		if( !record.QUOTE_PRICE  || record.QUOTE_PRICE == '0' ){
	           			return "-" ;
	           		}
	           		
	           		if( !record.FBM_PRICE || record.FBM_PRICE == '0'){
	           			return "-" ;
	           		}
	           		
	           		if( !record.FBM_COST || record.FBM_COST == '0'){
	           			return "-" ;
	           		}
	           		
	           		if( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBM_COST||0)) <= 0 ){
	           			return "-" ;
	           		}
	           		lye = lye.toFixed(2) ;
	           		if( lye < 0 ){
	           			return "<font color='red'>"+lye+"</font>"
	           		}else{
	           			return lye ;
	           		}
	           	},permission:function(){ return $purchase_cost_view; }},
	           	{align:"center",key:"FBM_PRICE",label:"利润率",group:"FBM",width:"6%",format:function(val,record){
	           		var lye = parseFloat(formatMoney(record.FBM_PRICE)) 
	           					- parseFloat(formatMoney(record.QUOTE_PRICE||0)) -   parseFloat(formatMoney(record.FBM_COST||0)) ;
	           		
	           		if( !record.QUOTE_PRICE  || record.QUOTE_PRICE == '0' ){
	           			return "-" ;
	           		}
	           		
	           		if( !record.FBM_PRICE || record.FBM_PRICE == '0'){
	           			return "-" ;
	           		}
	           		
	           		
	           		if( !record.FBM_COST || record.FBM_COST == '0'){
	           			return "-" ;
	           		}
	           		
	           		if( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBM_COST||0)) <= 0 ){
	           			return "-" ;
	           		}
	           		
	           		var lyl = (lye / ( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBM_COST||0)) ))*100 ;
	           		lyl = lyl.toFixed(2) ;
	           		if( lyl < 0 ){
	           			return "<font color='red'>"+lyl+"%</font>"
	           		}else{
	           			return lyl+"%" ;
	           		}
	           	},permission:function(){ return $purchase_cost_view; }},
	           	{align:"center",key:"FBA_COST",label:"其他成本",group:"FBA",width:"6%",format:function(val,record){
	           		return "<a href='' class='cost' type='FBA' asin='"+record.ASIN+"'>"+(val||"")+"</a>" ;
	           	},permission:function(){ return $purchase_cost_view; }},
	           	{align:"center",key:"FBA_PRICE",label:"最低价",group:"FBA",width:"6%",permission:function(){ return $purchase_cost_view; }},
	           	{align:"center",key:"FBA_PRICE",label:"利润额",group:"FBA",width:"6%",format:function(val,record){
	           		var lye = parseFloat(formatMoney(record.FBA_PRICE)) 
	           					- parseFloat(formatMoney(record.QUOTE_PRICE||0)) -   parseFloat(formatMoney(record.FBA_COST||0)) ;
	           		
	           		if( !record.QUOTE_PRICE  || record.QUOTE_PRICE == '0' ){
	           			return "-" ;
	           		}
	           		
	           		if( !record.FBA_PRICE || record.FBA_PRICE == '0'){
	           			return "-" ;
	           		}
	           		
	           		
	           		if( !record.FBA_COST || record.FBA_COST == '0'){
	           			return "-" ;
	           		}
	           		
	           		if( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBA_COST||0)) <= 0 ){
	           			return "-" ;
	           		}
	           		lye = lye.toFixed(2) ;
	           		if( lye < 0 ){
	           			return "<font color='red'>"+lye+"</font>"
	           		}else{
	           			return lye ;
	           		}
	           	},permission:function(){ return $purchase_cost_view; }},
	           	{align:"center",key:"FBA_PRICE",label:"利润率",group:"FBA",width:"6%",format:function(val,record){
	           		var lye = parseFloat(formatMoney(record.FBA_PRICE)) 
	           					- parseFloat(formatMoney(record.QUOTE_PRICE||0)) -   parseFloat(formatMoney(record.FBA_COST||0)) ;
	           		
	           		if( !record.QUOTE_PRICE  || record.QUOTE_PRICE == '0' ){ return "-" ; } 
	           		if( !record.FBA_PRICE || record.FBA_PRICE == '0'){ return "-" ; } 
	           		if( !record.FBA_COST || record.FBA_COST == '0'){ return "-" ; }
	           		
	           		
	           		if( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBA_COST||0)) <= 0 ){
	           			return "-" ;
	           		}
	           		
	           		var lyl = (lye / ( parseFloat(formatMoney(record.QUOTE_PRICE||0)) +  parseFloat(formatMoney(record.FBA_COST||0)) ))*100 ;
	           		lyl = lyl.toFixed(2) ;
	           		if( lyl < 0 ){
	           			return "<font color='red'>"+lyl+"%</font>"
	           		}else{
	           			return lyl+"%" ;
	           		}
	           	},permission:function(){ return $purchase_cost_view; }}
	           	
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:30,
			 pageSizes:[10,20,30,40],
			 height:function(){
			 	return $(window).height() - 370 ;
			 },
			 title:"",
			 indexColumn:false,
			 querys:{id:id,sqlId:"sql_purchase_plan_getAsinFromSku"},//sql_purchase_plan_details_listForSKU sql_purchase_plan_details_list
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
	}
		
	
	//dom bind events
	$(".btn-save").click(function(){
		var json = $("#personForm").toJson() ;
		$.dataservice("model:Sale.savePurchasePlanProduct",json,function(){
			window.close() ;
		}) ;
	});
	
	$(".edit_supplier").click(function(){
		var sku = $(this).attr("sku");
		openCenterWindow(contextPath+"/supplier/listsSelectBySku/"+sku,800,600) ;
		return false;
	}) ;
	
}) ;

$(function(){
	var chargeGridSelect = {
			title:'用户选择页面',
			defaults:[],//默认值
			key:{value:'LOGIN_ID',label:'NAME'},//对应value和label的key
			multi:false,
			grid:{
				title:"用户选择",
				params:{
					sqlId:"sql_user_list_forwarehouse"
				},
				ds:{type:"url",content:contextPath+"/grid/query"},
				pagesize:10,
				columns:[//显示列
					{align:"center",key:"ID",label:"编号",width:"100"},
					{align:"center",key:"LOGIN_ID",label:"登录ID",sort:true,width:"100"},
					{align:"center",key:"NAME",label:"用户姓名",sort:true,width:"100"}
				]
			}
	   } ;
	   
	$(".btn-charger").listselectdialog( chargeGridSelect,function(){
		var args = jQuery.dialogReturnValue() ;
		var value = args.value ;
		var label = args.label ;
		$("#executor").val(value) ;
		$("#executorName").val(label) ;
		return false;
	}) ;
	
	$(".supplier-select").click(function(){
		openCenterWindow(contextPath+"/supplier/listsSelectBySku/"+sku,800,600,function(){
			window.location.reload();
		}) ;
	}) ;

	$(".supplier").click(function(){
		openCenterWindow(contextPath+"/supplier/addBySku/"+sku,800,600,function(){
			window.location.reload();
		}) ;
	}) ;
	
	$(".update-supplier").click(function(){
		var supplierId = $(this).attr("supplierId") ;
		openCenterWindow(contextPath+"/supplier/updateProductSupplierPage/"+supplierId+"/"+planId+"/"+sku,650,600) ;
		return false;
	}) ;
	
	$(".used").click(function(){
		var supplierId=$(this).attr("supplierId");
		if(window.confirm("确认采用？")){
			$.dataservice("model:Sale.setSupplierFlag",{supplierId:supplierId,sku:sku,planId:planId},function(result){
					window.location.href = "#supplier-tab" ;
					window.location.reload();
			});
		}
	});
	
	//初始化流程数据
	var flow = new Flow() ;
	flow.init(".flow-bar center",flowData) ;
	flow.draw(currentStatus) ;
}) ;

function AuditAction(status , statusLabel){
	if(window.confirm("确认【"+statusLabel+"】？")){
		var memo = "("+statusLabel+")" + ($(".memo").val()||"")
		var json1 = {id:id,status:status,memo:memo} ;
		
		//if( status == 10 || status == 20  ){ //确认采购
			if( !$.validation.validate('#personForm').errorInfo ) {
				var json = $("#personForm").toJson() ;
				$.dataservice("model:Sale.savePurchasePlanProduct",json,function(){
					//执行状态更新
					$.dataservice("model:Sale.doStatus",json1,function(result){
						window.location.reload();
					});
				}) ;
			}
	//	}else{
	//		$.dataservice("model:Sale.doStatus",json1,function(result){
	//			window.location.reload();
	//		});
	//	}
	}
}



var Flow = function(){
	var _data = null ;
	var _selector = null ;
	var itemTemplate = '<td><div class="flow-node {statusClass}" status="{status}">{label}</div></td>' ;
	
	this.init = function(selector , d){
		_data = d ;
		_selector = selector ;
		return this ;
	}

	this.draw = function(current){
		//create container
		var html = '<table class="flow-table">\
						<tr>\
						</tr>\
					</table>\
					<div class="flow-action">\
						<div class="btn-container"></div>\
						<a href="#" class="memo-control">附加备注</a>\
					</div>\
					<textarea class="memo" placeHolder="输入附加备注信息"></textarea>' ;
		
		$(_selector).empty().html(html) ;
		
		$(".memo-control").toggle(function(){
			$(".memo").show() ;
		},function(){
			$(".memo").hide() ;
		}) ;
		
		var flowContainer = $(_selector).find(".flow-table tr")
		
		var length = _data.length ;
		var isContinue = true ;
		$(_data).each(function(index,node){
			if( node.format ) node.format(node) ;
			if(!isContinue) return ;
 			var statusClass = node.statusClass||(current == this.status ?"active":(this.status < current?"passed":"disabled")) ;
			var status = this.status ;
			var isMemo = this.memo ;
			var label = this.label ;
			html =  itemTemplate.replace(/{statusClass}/g,statusClass)
								.replace(/{status}/g,status)
								.replace(/{label}/g,label) ;
			$(html).appendTo(flowContainer) ;
			
			if(length != index+1){
				flowContainer.append("<td class='flow-split'>-</td>") ;
			}
			
			
			if( current == this.status ){
				var actions = this.actions ;
				
				if(this.memo && actions && actions.length >=1 ){
					$(".memo-control").show();
				}
				
				$(actions||[]).each(function(){
					var me = this ;
					$("<button class='btn btn-primary' style='margin-right:3px;'>"+this.label+"</button>&nbsp;&nbsp;")
						.appendTo(".btn-container").click(function(){
							me.action() ;
						}) ;  ;
				}) ;
			}
			if(node.isbreak){
				isContinue = false ;
				var tdlast = $(".flow-table td:last") ;
				if(tdlast.hasClass("flow-split")) tdlast.remove() ;
			}  ;
		}) ;
	}
} ;