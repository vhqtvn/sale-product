	$(function(){
			var _index = 1 ;
			var sqlId = "sql_sc_order_list_picked_export"//"sql_order_list_picked" ;
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
				 indexColumn:false,
				 querys:{sqlId:sqlId,pickId:pickedId},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
		
		},300) ;
   	 });
   	 
   	 var currentQueryKey = "" ;
     //{未审核订单：,合格订单：5，风险订单：2，待退单：3，外购订单：4，加急单：6，特殊单：7}
   	 