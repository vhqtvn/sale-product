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
					//$(".grid-content").llygrid("reload",{orderId:val}) ;
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/order/repickedException/" ,
						data:{type:'',memo:'',orderNumber:val,status:10},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							if(result >= 1){
								$("#orderId").css("background","green").css("color","#000") ;
								$("#orderId").val("") ;
							}else{
								$("#orderId").css("background","red").css("color","#000") ;
								$("#orderId").val("") ;
								alert("输入错误") ;
							}
						},
						error:function(){
							$("#orderId").css("background","red").css("color","#000") ;
							$("#orderId").val("") ;
						}
					});
					
					$("#orderId").val("") ;
				};
				
			}) ;
			
			$("#orderId").css("background","#EEE").css("color","#000") ;
			
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
			
   	 });
   	 
   	 var currentQueryKey = "" ;