

var currentId = '' ;
$(function(){
	
		$(".grid-content").llygrid({
			columns:[
	           	{align:"center",key:"CREATE_TIME",label:"操作时间", width:"18%"},
	           	{align:"center",key:"TYPE",label:"类型",width:"5%",format:{type:"json",content:{'in':"入库",'out':'出库'}}},
	           	{align:"center",key:"IN_QUANTITY",label:"入库数量",width:"8%"},
	           	{align:"center",key:"OUT_QUANTITY",label:"出库数量",width:"8%"},
	           	{align:"center",key:"CREATOR_NAME",label:"操作用户", width:"10%"},
	           	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"20%"},
	           	{align:"center",key:"IN_NUMBER",label:"入库计划",width:"20%"}
	            
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
			 limit:20,
			 pageSizes:[10,20,30,40],
			 height:function(){
			 	return $(window).height()-220 ;
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
	           	{align:"center",key:"ORDER_NUMBER",label:"系统货号",width:"100"},
	           	{align:"center",key:"SKU",label:"订单产品SKU",width:"150"},
	           	{align:"left",key:"PAYMENTS_DATE",label:"支付日期", width:"150" },
	           	{align:"center",key:"ORDER_ID",label:"ORDER ID",width:"150" }
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
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
		
		var tab = $('#details_tab').tabs( {
			tabs:[
				{label:'计划出入库',content:"assign-grid"},
				{label:'订单出库',content:"order-grid"}
			] ,
			//height:'500px',
			select:function(event,ui){
				var index = ui.index ;
				if(index == 1){
					$(".ordergrid-content").llygrid("reload") ;
				}
			}
		} ) ;

 });