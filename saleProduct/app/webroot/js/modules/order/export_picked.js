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
			var sqlId = "sql_order_list_picked_export"//"sql_order_list_picked" ;
		setTimeout(function(){
			/**
			 *  s4.REAL_SKU ,
				s4.NAME,
		        s4.IMAGE_URL ,
		        s4.MEMO,
				s2.SKU ,
		        s2.QUANTITY_TO_SHIP,
		        s2.ORDER_ID,
		        s2.ORDER_ITEM_ID,
		        s2.RECIPIENT_NAME,
		        s2.SHIP_ADDRESS_1,
		        s2.SHIP_ADDRESS_2,
		        s2.SHIP_ADDRESS_3,
		        s2.SHIP_COUNTRY,
		        s2.SHIP_CITY,
		        s2.SHIP_STATE,
		        s2.SHIP_POSTAL_CODE
			 */
			$(".grid-content").llygrid({
				columns:[
					{align:"left",key:"INDEX",label:"序号", width:"5%",format:function(val,record){
						return _index++ ;
					}},
					{align:"left",key:"PICK_ID",label:"拣货号", width:"5%"},
					{align:"left",key:"ORDER_NUMBER",label:"系统货号", width:"8%"},
					{align:"left",key:"REAL_SKU",label:"货品SKU", width:"6%"},
					{align:"left",key:"NAME",label:"货品名称", width:"10%"},
					{align:"center",key:"IMAGE_URL",label:"图片", width:"10%",format:function(val,record){
						if(val){
							return "<img src='/saleProduct/"+val+"' style='width:50px;height:50px;'/>" ;
						}
						return "" ;
					}},
					{align:"right",key:"QUANTITY_TO_SHIP",label:"数量", width:"5%"},
					{align:"left",key:"ORDER_ID",label:"订单编号", width:"15%"},
					{align:"left",key:"ORDER_ITEM_ID",label:"订单项编号", width:"12%"},
					{align:"left",key:"RECIPIENT_NAME",label:"接收人", width:"12%"},
					{align:"left",key:"BUYER_PHONE_NUMBER",label:"电话", width:"12%"},
					{align:"left",key:"BUYER_EMAIL",label:"邮箱", width:"12%"},
					{align:"left",key:"SHIP_COUNTRY",label:"COUNTRY", width:"8%"},
					{align:"left",key:"SHIP_CITY",label:"CITY", width:"10%"},
					{align:"left",key:"SHIP_STATE",label:"STATE", width:"8%"},
					{align:"left",key:"SHIP_ADDRESS_1",label:"SHIP_ADDRESS_1", width:"18%"},
					{align:"left",key:"SHIP_ADDRESS_2",label:"SHIP_ADDRESS_2", width:"15%"},
					{align:"left",key:"SHIP_ADDRESS_3",label:"SHIP_ADDRESS_3", width:"10%"},
					{align:"left",key:"SHIP_POSTAL_CODE",label:"邮编", width:"8%"},
					{align:"left",key:"MEMO",label:"备注信息", width:"13%"}
		         ],
		        // 序号、产品SKU、名称、图片，位置、数量，完成状态，备注信息。拣货人

		         ds:{type:"url",content:"/saleProduct/index.php/grid/query/"+accountId},
				 limit:1000,
				 pageSizes:[1000],
				/* height:function(){
				 	return $(window).height() - $(".toolbar-auto").height() -85 ;
				 },*/
				 title:"",
				 indexColumn:false,
				 querys:{sqlId:sqlId,pickId:pickedId},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		
		},300) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   	 