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
	   
	var RePicked = {
		clear:function(){
			$("#orderId").val("").css("background","#EEE").css("color","#000") ;
			detailRecords= null ;
			$(".lly-grid-row ").remove();
			$(".message-alert").hide(2000) ;
		},
		passed:function(orderId){
			$.ajax({
				type:"post",
				url:"/saleProduct/index.php/order/repickedException/" ,
				data:{type:'',memo:'',orderId:orderId,status:9},//9拣货 11异常 10完成 12拣货完成
				cache:false,
				dataType:"text",
				success:function(result,status,xhr){
					$("#orderId").css("background","green").css("color","#000") ;
					$(".message-alert").show();
					setTimeout(function(){
						RePicked.clear() ;
					},1000) ;
				}
			});
		},
		error:function(){
			$("#orderId").css("background","red").css("color","#000") ;
			$(".message-alert").hide() ;
		},
		running:function(){
			$("#orderId").css("background","#EEE").css("color","#000") ;
			$(".message-alert").hide() ;
		},
		rowPassed:function(target){
			$(target).parents("tr:first").find("td").css("background","green") ;
		}
	} ;

	$(function(){
			var _index = 1 ;
			var sqlId = "sql_order_list_repicked_print" ;//"sql_order_list_picked" ;
			
			$(".exception-btn").click(function(){
				var type = $("#type").val() ;
				var memo = $("#memo").val() ;
				var orderId = $("#orderId").val() ;
				if( window.confirm("确认订单为异常订单吗？") ){
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/order/repickedException/" ,
						data:{type:type,memo:memo,orderId:orderId,status:11},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.location.reload() ;
						}
					});
				}

			}) ;
			
			function updatePickQuantity(productSKU,type){
				var exists = false ;
				var orderId = '' ;
				$("[key='BARCODE']").each(function(){
					var barcode = $.trim($(this).text()) ;
					if( barcode == productSKU ){
						exists = true ;
						var currentNum = $(this).parents("tr:first").find("[key='QUANTITY']").find("span").text()||'0' ;
						var pickedNum = $.trim($(this).parents("tr:first").find("[key='PICKED_QUANTITY']").find("span").text())||'0' ;
						currentNum = parseInt($.trim(currentNum)) ;
						pickedNum = parseInt($.trim(pickedNum)) ;
						if( currentNum >= 1 ){//正常情况
							$(this).parents("tr:first").find("[key='QUANTITY']").find("span").html(currentNum-1) ;
							$(this).parents("tr:first").find("[key='PICKED_QUANTITY']").find("span").html(pickedNum+1) ;
						}else{//告警
							RePicked.error() ;
							exists = false ;
						}
					}
				}) ;
				
				if( !exists ){
					RePicked.error() ;
				}else{
					orderId = $.trim($("[key='ORDER_ID']").text()) ;
					//判断是否已经完成二次拣货了
					var isComplete = true ;
					$("[key='QUANTITY']").each(function(){
						$q = $.trim($(this).text()) ;
						$q = parseInt($q) ;
						if($q >= 1){
							isComplete = false ;
						}else{
							RePicked.rowPassed(this) ;
						}
					}) ;
					if(isComplete){
						RePicked.passed(orderId) ;
					}else{
						RePicked.running(orderId) ;
					}
				}
			}

			$(".btn-search").unbind("click").bind('click',function(){
				var val = $(this).prev().val() ;
				//判断是否订单号还是产品号码
				if( detailRecords && detailRecords.length > 0 ){//当前为产品号
					updatePickQuantity(val) ;
				}else{//订单号
					$(".grid-content").llygrid("reload",{orderId:val}) ;
				}
			}) ;
			jQuery(document).bind('keydown', 'return',function (evt){
				$(".btn-search").click() ;
				return false; 
			});
			
			 jQuery(document).bind('keydown', 'space',function (evt){
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
					{align:"left",key:"BARCODE",label:"条形码", width:"45"},
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
					{align:"right",key:"QUANTITY",label:"待拣数量", width:"30"},
					{align:"right",key:"PICKED_QUANTITY",label:"已拣数量", width:"30"}
		         ],
		        // 序号、产品SKU、名称、图片，位置、数量，完成状态，备注信息。拣货人

		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId},
				 limit:1000,
				 pageSizes:[1000],
				/* height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -85 ;
				 },*/
				 title:"",
				 autoWidth:true,
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId,status:'',pickStatus:'9',pickId:pickedId},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(){
				 	_index = 1 ;
				 	if(isFirst){
				 		isFirst = false ;
				 		$("#orderId").css("background","#EEE").css("color","#000") ;
				 		return ;
				 	}
				 	var options = $(".grid-content").data("options") ;
				 	detailRecords = options.records;
				 	if( detailRecords && detailRecords.length > 0){
						var orderId = $("#orderId").val() ;
						$("#orderId").css("background","#EEE").css("color","#000") ;
					}else{
						$("#orderId").css("background","red").css("color","#000") ;
					}
				 }
			} ;
			
			$(".grid-content").llygrid(gridConfig) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   	 