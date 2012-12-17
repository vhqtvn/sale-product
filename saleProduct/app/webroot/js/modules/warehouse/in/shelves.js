var currentId = '' ;
$(function(){
		$(".btn-danger").toggle(function(){
			$(".exception-form").show() ;
		},function(){
			$(".exception-form").hide() ;
		}) ;
	
		$(".btn-confirm").click(function(){
			$.dataservice("model:Warehouse.In.doStatus",{inId:inId,status:3},function(result){//确认收货
				window.opener.openCallback('edit') ;
				window.close();
			});
			return false ;
		}) ;

		$(".grid-content-details").llygrid({
			columns:[
	           	{align:"center",key:"NAME",label:"货品名称",width:"5%"},
           		{align:"center",key:"SKU",label:"SKU",width:"5%"},
           		{align:"center",key:"QUANTITY",label:"数量",width:"6%"},
           		{align:"center",key:"GEN_QUANTITY",label:"合格数量",width:"6%"},
           		{align:"center",key:"WASTE_QUANTITY",label:"残废品数量",width:"6%"},
           		{align:"center",key:"DELIVERY_TIME",label:"供货时间",width:"6%"},
           		{align:"center",key:"PRODUCT_TRACKCODE",label:"产品跟踪码",width:"6%"},
           		{align:"center",key:"MEMO",label:"备注",width:"6%"}
	         ],
	         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
			 limit:30,
			 pageSizes:[10,20,30,40],
			 height:function(){
			 	return $(window).height() - 180 ;
			 },
			 title:"待上架货品列表",
			 autoWidth:true,
			 querys:{sqlId:"sql_warehouse_in_products",inId:inId},
			 loadMsg:"数据加载中，请稍候......"
		}) ;
 });