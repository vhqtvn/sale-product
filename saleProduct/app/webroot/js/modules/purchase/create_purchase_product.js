$(function(){
	//初始化Tag 标签
	Tags.init( $(".btn-tags") , $(".tag-container") ,$("#tags")) ;
	
	//选择负责人
	var chargeGridSelect = {
			title:'用户选择页面',
			defaults:[],//默认值
			key:{value:'LOGIN_ID',label:'NAME'},//对应value和label的key
			multi:false,
			width:600,
			height:560,
			grid:{
				title:"用户选择",
				params:{
					sqlId:"sql_user_list_forwarehouse"
				},
				ds:{type:"url",content:contextPath+"/grid/query"},
				pagesize:10,
				columns:[//显示列
					{align:"center",key:"ID",label:"编号",width:"20%"},
					{align:"center",key:"LOGIN_ID",label:"登录ID",sort:true,width:"30%"},
					{align:"center",key:"NAME",label:"用户姓名",sort:true,width:"36%"}
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
	
	//选择货品
	var productGridSelect = {
			title:'货品选择页面',
			defaults:[],//默认值
			key:{value:'ID',label:'NAME'},//对应value和label的key
			multi:false,
			width:600,
			height:560,
			grid:{
				title:"货品选择",
				params:{
					sqlId:"sql_purchase_realproduct_select"
				},
				ds:{type:"url",content:contextPath+"/grid/query"},
				pagesize:10,
				columns:[//显示列
					//{align:"center",key:"ID",label:"编号",width:"20%"},
					{align:"center",key:"IMAGE_URL",label:"",sort:true,width:"10%",format:{type:"img"}},
					{align:"center",key:"REAL_SKU",label:"SKU",sort:true,width:"36%",query:true},
					{align:"center",key:"NAME",label:"货品名称",sort:true,width:"36%",query:true}
				]
			}
	   } ;
	
	$(".grid-content-details").llygrid({
		columns:[
		    {align:"center",key:"IN_FLAG",label:"状态",width:"6%",format:function(val,record){
		    	if(val>=1)return "入库中";
		    	return "" ;
		    }},
		    {align:"center",key:"IS_ANALYSIS",label:"供应需求", width:"6%",format:function(val,record){
				var html = [] ;
				if(val == 1){
					html.push('<a href="#" class="analysis" val="'+val+'">'+getImage("success.gif","可计算供应需求")+'</a>&nbsp;') ;
				}else{
					html.push('<a href="#" class="analysis" val="'+val+'">'+getImage("error.gif","不可计算供应需求")+'</a>&nbsp;') ;
				}
				return html.join("") ;
			}},
			{align:"center",key:"IS_RISK",label:"风险", width:"6%",format:function(val,record){
				var html = [] ;
				if(val == 1){
					html.push('<a href="#" class="risk" val="'+val+'">'+getImage("error.gif","存在风险")+'</a>&nbsp;') ;
				}else if(val == 2){
					html.push('<a href="#" class="risk" val="'+val+'">'+getImage("success.gif","不存在风险")+'</a>&nbsp;') ;
				}else{
					html.push('<a href="#" class="risk" title="未设置风险" val="'+val+'">-</a>&nbsp;') ;
				}
				return html.join("") ;
			}},
		 	{align:"center",key:"ACCOUNT_NAME",label:"账号",width:"10%"},
           	{align:"center",key:"LISTING_SKU",label:"Listing SKU",width:"15%",forzen:false,align:"left",format:function(val,record){
        		return "<a href='#'  offer-listing='"+record.ASIN+"'>"+val+"</a>" ;
        	}},
        	{align:"center",key:"FULFILLMENT_CHANNEL",label:"渠道",width:"10%",forzen:false,align:"left"},
        	{align:"center",key:"SALES_FOR_THELAST14DAYS",label:"最近14天销售",width:"8%",sort:true },
        	{align:"center",key:"SALES_FOR_THELAST30DAYS",label:"最近30天销售",width:"8%",sort:true },
        	{align:"center",key:"TOTAL_SUPPLY_QUANTITY",label:"当前账户库存",width:"8%",sort:true },
           	{align:"center",key:"PURCHASE_QUANTITY",label:"待采购数量",width:"8%",format:function(val,record){
           		return "<input type='text' class='edit-purchase-quantity'  value='"+(val||"0")+"' style='width:100%;height:100%;padding:0px;border:none;'/>" ;
           	}}
           	/*,
           	{align:"center",key:"URGENCY",label:"紧急程度",width:"8%",format:function(val,record){
           		if(currentPlanProduct.P_STATUS == 1 || currentPlanProduct.P_STATUS ==0 ){
           			return $.llygrid.format.editor.body(val,record,{align:"center",key:"URGENCY",label:"紧急程度",width:"10%",
		           		format:{type:'editor',renderType:'select',data:[{value:'A',text:'A'},{value:'B',text:'B'}]}})  ;
           		}else{
           			return val ;
           		}
           	}}*/
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:20,
		 pageSizes:[20,10,20,30,40],
		 height:function(){
		 	return 130 ;
		 },
		 title:"货品Listing明细",
		// autoWidth:true,
		 indexColumn:false,
		  querys:{sqlId:"sql_supplychain_requirement_plan_product_details_list_ALL",realId:'-'},
		 loadMsg:"数据加载中，请稍候......",
		 loadAfter:function(records){ 
			 $(".grid-content-details").find('.edit-fix-quantity').blur(function(){
				 var record = $(this).closest("tr").data("record") ;
				 var fixQuatity = $(this).val() ;
				 var id = record.ID ;
			 }) ;
		 }
			
	}) ;
	
	$(".edit-purchase-quantity").live("keyup",function(){
		formatPurchaseData() ;
	}).live("blur",function(){
		formatPurchaseData() ;
	}) ;
	
	function formatPurchaseData(){
		var data = [] ;
		var purchaseQuantity = 0 ;
		$(".grid-content-details").find(".lly-grid-2-body  tr").each(function(){
			var record = $(this).data("record");
			var pq = parseInt($(this).find(".edit-purchase-quantity").val()||0) ;
			 purchaseQuantity  += pq ;
			 data.push({sku:record.LISTING_SKU,accountId:record.ACCOUNT_ID,
				 quantity:pq,
				 fulfillment:record.FULFILLMENT_CHANNEL,
				 supplyQuantity:record.TOTAL_SUPPLY_QUANTITY||'0'}) ;
		 }) ;
		$("#planNum").val(purchaseQuantity||"") ;
		return data ;
	}
	   
	$(".btn-real-product").listselectdialog( productGridSelect,function(){
		var args = jQuery.dialogReturnValue() ;
		var value = args.value ;
		var label = args.label ;
		
		value = value[0] ;
		var selectReocrds = args.selectReocrds[value] ;
		var realSku = selectReocrds['REAL_SKU'] ;
		if( realSku ){
			$("[product-realsku]").attr("product-realsku",realSku).html(realSku) ;
		}
			
		$("#realId").val(value) ;
		$("#realName").val(label) ;
		$.dataservice("model:NewPurchaseService.loadDefault",{"realId":value},function(result){
			$("#executor").val(result.charger.charger) ;
			$("#executorName").val(result.charger.chargerName) ;
			$("#limitPrice").val( result.limitPrice ) ;
		 });
		
		$(".grid-content-details").llygrid("reload",{realId:value}) ;
		return false;
	}) ;
	
	$(".btn-save").click(function(){
		if( !$.validation.validate('#personForm').errorInfo ) {
			var purchaseDetails = formatPurchaseData() ;
			var json = $(".form-table").toJson() ;
			json.purchaseDetails = purchaseDetails ;

			if(window.confirm("确认创建采购单吗？")){
				$.dataservice("model:NewPurchaseService.createNewPurchaseProduct",json,function(result){
					$.dialogReturnValue(true) ;
					window.close() ;
				 });
			}
		}
	}) ;
}) ;