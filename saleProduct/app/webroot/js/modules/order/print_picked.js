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
			var sqlId = "sql_order_list_picked_print"//"sql_order_list_picked" ;
		setTimeout(function(){
			
			$(".grid-content").llygrid({
				columns:[
					{align:"left",key:"INDEX",label:"序号", width:"5%",format:function(val,record){
						return _index++ ;
					}},
					{align:"left",key:"REAL_SKU",label:"货品SKU", width:"13%",format:function(val,record){
						if(record.P_TYPE == 1){
							return "<font color=red>"+val+"</font>" ;
						}else
							return val ;
					}},
					{align:"left",key:"NAME",label:"货品名称", width:"20%"},
					{align:"center",key:"IMAGE_URL",label:"图片", width:"10%",format:function(val,record){
						if(val){
							return "<img src='/"+fileContextPath+"/"+val+"' style='width:50px;height:50px;'/>" ;
						}
						return "" ;
					}},
					{align:"left",key:"POSITION",label:"位置", width:"10%"},
					{align:"right",key:"QUANTITY",label:"数量", width:"5%",format:function(val,record){
						if(record.RMA_STATUS==1 || record.RMA_VALUE==10){
			        		return record.RMA_QUANTITY ;
			        	}
			        	return val ;
					}},
					{align:"left",key:"MEMO",label:"备注信息", width:"13%"},
					{align:"left",key:"STATUS",label:"完成状态", width:"8%"},
					{align:"left",key:"PICKER",label:"拣货人", width:"5%"}
		         ],
		        // 序号、产品SKU、名称、图片，位置、数量，完成状态，备注信息。拣货人

		         ds:{type:"url",content:contextPath+"/grid/query/"+accountId},
				 limit:1000,
				 pageSizes:[1000],
				/* height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -85 ;
				 },*/
				 title:"",
				 autoWidth:true,
				 indexColumn:false,
				 querys:{sqlId:sqlId,accountId:accountId,status:'',pickStatus:'9',pickId:pickedId},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		
		},300) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   	 