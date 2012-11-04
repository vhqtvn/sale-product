function formatGridData(data){
		var records = data.record ;
 		var count   = data.count ;
 		
 		count = count[0][0]["count(*)"] ;
 		
		var array = [] ;
		$(records).each(function(){
			var row = {} ;
			for(var o in this){
				var _ = this[o] ;
				for(var o1 in _){
					row[o1] = _[o1] ;
				}
			}
			array.push(row) ;
		}) ;
	
		var ret = {records: array,totalRecord:count } ;
			
		return ret ;
	   }

	$(function(){
		var sqlId = "sql_order_doing_list" ;

			$(".grid-content").llygrid({
				columns:[
					/*{align:"center",key:"ORDER_ID",label:"操作",width:"6%",format:{type:"checkbox",render:function(record){
							if(record.checked >=1){
								$(this).attr("checked",true) ;
							}
					}}},*/
					 //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
					{align:"center",key:"AUDIT_STATUS",label:"状态",sort:true, width:"8%",
						format:{type:"json",content:{0:"未审核",5:"合格订单",2:"风险订单"
						,3:"待退单",4:"外购订单",6:"加急单",7:"特殊单"
					}}},
					{align:"center",key:"CARRIER_CODE",label:"CarrierCode", width:"10%",
						format:{type:"editor",renderType:"select",fields:['ORDER_ID','ORDER_ITEM_ID'],data:[{value:'',text:'-'},{value:'UPS',text:'UPS'}]}},
					{align:"center",key:"TRACK_NUMBER",label:"Tracking Number", width:"20%",format:{type:"editor",fields:['ORDER_ID','ORDER_ITEM_ID']}},
		           	{align:"center",key:"SHIP_SERVICE_LEVEL",label:"SHIP LEVEL", width:"10%"},
		           	{align:"left",key:"ASIN",label:"ASIN", width:"90",format:function(val,record){
			           		var memo = record.MEMO||"" ;
			           		return "<a href='#' class='product-detail' title='"+memo+"' asin='"+val+"' sku='"+record.SKU+"'>"+(val||'')+"</a>" ;
			        }},
		           	{align:"center",key:"LOCAL_URL",label:"Image",width:"6%",forzen:false,align:"left",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           		}else{
		           			return "" ;
		           		}
		           		return "<img src='/saleProduct/"+val+"' onclick='showImg(this)' style='width:25px;height:25px;'>" ;
		           	}},
		           	{align:"center",key:"ORDER_ID",label:"ORDER_ID", width:"15%"},
		           	{align:"center",key:"ORDER_ITEM_ID",label:"ORDER_ITEM_ID", width:"12%"},
		           	{align:"center",key:"SKU",label:"SKU",sort:true, width:"10%"},
		           	{align:"center",key:"PRODUCT_NAME",label:"PRODUCT_NAME", width:"20%"},
		           	{align:"center",key:"PURCHASE_DATE",label:"PURCHASE_DATE",sort:true, width:"20%"},
		           	{align:"center",key:"PAYMENTS_DATE",label:"PAYMENTS_DATE",sort:true, width:"20%"},
		           	{align:"center",key:"BUYER_EMAIL",label:"BUYER_EMAIL", width:"30%"},
		           	{align:"center",key:"BUYER_NAME",label:"BUYER_NAME", width:"10%"},
		           	{align:"center",key:"BUYER_PHONE_NUMBER",label:"BUYER_PHONE_NUMBER", width:"10%"},
		           	{align:"center",key:"QUANTITY_PURCHASED",label:"QUANTITY_PURCHASED", width:"10%"},
		           	{align:"center",key:"CURRENCY",label:"CURRENCY", width:"10%"},
		           	{align:"center",key:"ITEM_PRICE",label:"ITEM_PRICE", width:"10%"},
		           	{align:"center",key:"ITEM_TAX",label:"ITEM_TAX", width:"10%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -155 ;
				 },
				 title:"订单信息列表",
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId,status:'',pickStatus:9},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			//trackNumber编辑框事件
			$(".lly-grid-cell-input").live("blur",function(){
				var orderId = $(this).attr("ORDER_ID") ;
				var orderItemId =  $(this).attr("ORDER_ITEM_ID") ;
				var val = $(this).val() ;
				var key = $(this).attr("key") ;
				
				var params = {orderId:orderId,orderItemId:orderItemId,key:key} ;
				params[key]= val ;
				
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/order/updateTrackNumber",
					data:params,
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
					}
				}); 
			}) ;
			
			$(".save-track").click(function(){
				
				if( window.confirm("确认要更新tracking Number到Amazon？") ){
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/order/saveTrackNumberToAamazon/"+accountId ,
						data:{},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							alert("保存成功!");
							$(".grid-content").llygrid("reload",{},true) ;
						}
					});
				}
				
				
			}) ;
			
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				//if(currentQueryKey)json.sqlId = currentQueryKey ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   	 $(function(){
			var tab = $('#details_tab').tabs( {
				tabs:[
					{label:'拣货中',content:"tab-content"},
					{label:'拣货完成',content:"tab-content"}
				] ,
				//height:'500px',
				select:function(event,ui){
					var index = ui.index ;
					renderAction(index);
					
				}
			} ) ;
		}) ;
   	 
function renderAction(index){
	$(".save-btn").show() ;
	if(index == 0){//拣货中
		$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{pickStatus:9,status:''},true) ;
	}else if(index == 1){//拣货完成
		//$(".save-btn").hide() ;
		$(".grid-content").llygrid("reload",{pickStatus:10,status:''},true) ;
	}
}