

var currentId = '' ;
$(function(){
	
		$(".grid-content").llygrid({
			columns:[
	           	{align:"center",key:"CREATE_TIME",label:"操作时间", width:"130"},
	           	{align:"center",key:"TYPE",label:"",width:"40",format:function(val,record){
	           		if( record.DISK_ID ) return "盘点" ;
	           		return "计划" ;
	           	}},
	           	{align:"center",key:"TYPE",label:"出入库",width:"40",format:{type:"json",content:{'in':"入库",'out':'出库'}}},
	        	{align:"center",key:"INVENTORY_TYPE",label:"库存类型",width:"60",format:{type:"json",content:{'1':"普通库存",'2':'FBA库存'}}},
	           	{align:"right",key:"IN_QUANTITY",label:"良品数量",width:"60"},
	        	{align:"right",key:"BAD_IN_QUANTITY",label:"残品数量",width:"60"},
	           	{align:"center",key:"CREATOR_NAME",label:"操作用户", width:"60"},
	           	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"100"},
	           	{align:"center",key:"IN_NUMBER",label:"计划入库单号",width:"100"},
	           	{align:"center",key:"DISK_NO",label:"盘点单号",width:"100"}
	            
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:20,
			 pageSizes:[10,20,30,40],
			 height:function(){
			 	return $(window).height()-280 ;
			 },
			 title:"",
			 //autoWidth:true,
			 querys:{sqlId:"sql_warehouse_storage_detailsByProduct",realProductId:realProductId},
			 loadMsg:"数据加载中，请稍候......"
		}) ;
		
		var orderGridConfig = {
			columns:[
				{align:"center",key:"CHANNEL_NAME",label:"ACCONT",width:"100"},
	           	{align:"center",key:"QUANTITY",label:"出库数量",width:"100"},
	           	{align:"center",key:"ORDER_NUMBER",label:"内部订单号",width:"100"},
	           	{align:"center",key:"SKU",label:"订单产品SKU",width:"150"},
	           	{align:"left",key:"PAYMENTS_DATE",label:"支付日期", width:"150" },
	           	{align:"center",key:"ORDER_ID",label:"ORDER ID",width:"150" }
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:100,
			 pageSizes:[100],
			 height:function(){
			 	return	$(window).height() - 280
			 },
			// autoWidth:true,
			 title:"",
			 indexColumn:false,
			 querys:{id:realProductId,sqlId:"sql_listshippedOrderForStorage"},
			 loadMsg:"数据加载中，请稍候......"
		} ;
		setTimeout(function(){
			$(".ordergrid-content").llygrid(orderGridConfig) ;
		},200) ;
		
		var ramGridConfig = {
			columns:[
				{align:"center",key:"CREATE_TIME",label:"操作时间", width:"130"},
				{align:"center",key:"QUALITY",label:"货品质量",width:"50",forzen:false
           		,format:{type:"json",content:{'good':"良品",'bad':"残品"}}},
	           	{align:"center",key:"QUANTITY",label:"数量",width:"100" },
	           	{align:"center",key:"MEMO",label:"备注",width:"300"}
	         ],
	         ds:{type:"url",content:contextPath+"/grid/query"},
			 limit:100,
			 pageSizes:[100],
			 height:function(){
			 	return	$(window).height() - 280
			 },
			// autoWidth:true,
			 title:"",
			 indexColumn:false,
			 querys:{id:realProductId,sqlId:"sql_listRamForStorage"},
			 loadMsg:"数据加载中，请稍候......"
		} ;
		setTimeout(function(){
			$(".ramgrid-content").llygrid(ramGridConfig) ;
		},200) ;
		
		var tab = $('#details_tab').tabs( {
			tabs:[
				{label:'计划盘点出入库',content:"assign-grid"},
				{label:'RAM入库',content:"ram-grid"},
				{label:'订单出库',content:"order-grid"}
			] ,
			//height:'500px',
			select:function(event,ui){
				var index = ui.index ;
				
				if(index == 1){
					$(".ramgrid-content").llygrid("reload") ;
				}
				
				if(index == 2){
					$(".ordergrid-content").llygrid("reload") ;
				}
			}
		} ) ;

 });