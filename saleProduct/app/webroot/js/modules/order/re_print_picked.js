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
			var sqlId = "sql_order_list_repicked_print"//"sql_order_list_picked" ;
			/*if(type == '1'){
				sqlId = "sql_order_list_one_repicked_print" ;
			}else if(type == '2'){
				sqlId = "sql_order_list_many_repicked_print" ;
			}*/
			$(".btn-search").click(function(){
				var val = $(this).prev().val() ;
				var records = [] ;
				if( detailRecords ){
					$(detailRecords||[]).each(function(index,record){
						if( val == record.ORDER_ID ){
							records.push(record) ;
						}
					}) ;
				}
				
				if(!val){
					records = detailRecords;
				}
				_index = 1 ;
				gridConfig.ds = {type:"data",records:records} ;
				gridConfig.loadMsg = "获取明细中......";
				gridConfig.loadAfter = null ;
				$(".grid-content").empty().llygrid(gridConfig) ;
			}) ;
			
			jQuery(document).bind('keydown', 'return',function (evt){
				$(".btn-search").click() ;
				 return false; 
			});
			
			 jQuery(document).bind('keydown', 'space',function (evt){
			 $("#orderId").focus() ;
			 return false; });
			
			var detailRecords = null ;
			
			var gridConfig = {
				columns:[
					{align:"left",key:"INDEX",label:"序号", width:"30",format:function(val,record){
						return _index++ ;
					}},
					{align:"left",key:"ORDER_ID",label:"订单编号", width:"90"},
					{align:"left",key:"REAL_SKU",label:"产品SKU", width:"60"},
					{align:"left",key:"NAME",label:"名称", width:"90"},
					{align:"center",key:"IMAGE_URL",label:"图片", width:"45",format:function(val,record){
						if(val){
							return "<img src='/saleProduct/"+val+"' style='width:50px;height:50px;'/>" ;
						}
						return "" ;
					}},
					{align:"right",key:"QUANTITY",label:"数量", width:"30"},
					{align:"left",key:"STATUS",label:"完成状态", width:"50"},
					{align:"left",key:"MENU",label:"备注信息", width:"90"},
					{align:"left",key:"PICKER",label:"拣货人", width:"40"}
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
				 	var options = $(".grid-content").data("options") ;
				 	detailRecords = options.records;
				 }
			} ;
			
			$(".grid-content").llygrid(gridConfig) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   	 