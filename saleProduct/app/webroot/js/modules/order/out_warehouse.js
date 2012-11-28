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
			var _index = 1 ;
			var sqlId = "sql_order_list_repicked_outwarehouse" ;//"sql_order_list_picked" ;
			
			$(".btn-search").click(function(){
				$("#orderId").css("background","#EEE").css("color","#000") ;
				var val = $.trim($(this).prev().val()) ;//orderId
				if(val){
					if(val.indexOf('-') < 0){
						var f1 = val.substring(0,3) ;
						var f2 = val.substring(3,10) ;
						var f3 = val.substring(10) ;
						val = f1+'-'+f2+'-'+f3;
					}
					
					$(".grid-content").llygrid("reload",{orderId:val}) ;
				};
				
			}) ;
			
			jQuery(document).bind('keydown', 'return',function (evt){
				$("#orderId").css("background","#EEE").css("color","#000") ;
				$(".btn-search").click() ;
				 return false; 
			});
			
			 jQuery(document).bind('keydown', 'space',function (evt){
			 	$("#orderId").css("background","#EEE").css("color","#000") ;
			 	$("#orderId").focus() ;
			 	return false; 
			 });
			
			var detailRecords = null ;
			var isFirst = true ;
			
			var gridConfig = {
				columns:[
					{align:"left",key:"INDEX",label:"序号", width:"30",format:function(val,record){
						return _index++ ;
					}},
					{align:"left",key:"ORDER_ID",label:"订单编号", width:"90"},
					{align:"left",key:"REAL_SKU",label:"产品SKU", width:"60",format:function(val,record){
						if(record.P_TYPE == 1){
							return "<font color=red>"+val+"(未关联货品)</font>" ;
						}else
							return val ;
					}},
					{align:"left",key:"NAME",label:"名称", width:"90"},
					{align:"center",key:"IMAGE_URL",label:"图片", width:"45",format:function(val,record){
						if(val){
							return "<img src='/saleProduct/"+val+"' style='width:50px;height:50px;'/>" ;
						}
						return "" ;
					}},
					{align:"right",key:"QUANTITY",label:"数量", width:"30"}
		         ],
		        // 序号、产品SKU、名称、图片，位置、数量，完成状态，备注信息。拣货人

		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"},
				 limit:1000,
				 pageSizes:[1000],
				 title:"",
				 autoWidth:true,
				 indexColumn:false,
				 querys:{sqlId:sqlId,status:'',pickStatus:'9'},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
				 	if(isFirst){
				 		isFirst = false ;
				 		$("#orderId").css("background","#EEE").css("color","#000") ;
				 		return ;
				 	}
				 	var options = $(".grid-content").data("options") ;
				 	detailRecords = options.records;
				 	if( detailRecords && detailRecords.length > 0){
						var orderId = $("#orderId").val() ;
						$.ajax({
							type:"post",
							url:"/saleProduct/index.php/order/repickedException/" ,
							data:{type:'',memo:'',orderId:orderId,status:10},
							cache:false,
							dataType:"text",
							success:function(result,status,xhr){
								$("#orderId").css("background","green").css("color","#000") ;
							}
						});
					}else{
						$("#orderId").css("background","red").css("color","#000") ;
					}
					$("#orderId").val('') ;
				 }
			} ;
			
			$(".grid-content").llygrid(gridConfig) ;
   	 });
   	 
   	 var currentQueryKey = "" ;