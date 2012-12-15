

	var currentId = '' ;
	$(function(){
		$(".message,.loading").hide() ;
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"编号", width:"9%"},
		           	{align:"center",key:"BOX_NUMBER",label:"包装箱编号",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"SHIP_FEE",label:"运输费用",width:"13%"},
		           	{align:"center",key:"WEIGHT",label:"重量",width:"8%"},
		           	{align:"center",key:"TOTAL",label:"尺寸(长X宽X高)",width:"15%",format:function(val,record){
		           		return (record['LENGTH']||'-') +'X'+ (record['WIDGH']||'-') +"X"+(record['HEIGHT']||'-') ;
		           	}},
		           	{align:"center",key:"STATUS12",label:"备注",width:"7%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:120,
				 title:"",
				 autoWidth:true,
				 querys:{sqlId:"sql_warehouse_box_lists",inId:inId},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(rowIndex , rowData){
				 	var id = rowData.ID  ;
				 	currentId = id ;
				 	$(".grid-content-details").llygrid("reload",{boxId:currentId}) ;
				 	$(".add-box-product").removeAttr("disabled");
				 }
				 
			}) ;

			$(".grid-content-details").llygrid({
				columns:[
				    {align:"center",key:"BOX_NUMBER",label:"包装箱",width:"5%"},
		           	{align:"center",key:"NAME",label:"货品名称",width:"5%"},
	           		{align:"center",key:"SKU",label:"SKU",width:"5%"},
	           		{align:"center",key:"QUANTITY",label:"数量",width:"6%"},
	           		{align:"center",key:"DELIVERY_TIME",label:"供货时间",width:"6%"},
	           		{align:"center",key:"PRODUCT_TRACKCODE",label:"产品跟踪码",width:"6%"},
	           		{align:"center",key:"MEMO",label:"备注",width:"6%"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:200,
				 title:"货品列表",
				 autoWidth:true,
				 querys:{sqlId:"sql_warehouse_box_products",boxId:''},
				 loadMsg:"数据加载中，请稍候......"
			}) ;
			
			$(".process-action").live("click",function(){
				var FILTER_ID = $(this).attr("val") ;
				var asin = $(this).attr("asin") ;
				var status = $(this).attr("status") ;
				openCenterWindow("/saleProduct/index.php/sale/details1/"+FILTER_ID+"/"+asin+"/"+type+"/"+status,950,650) ;
			}) ;
			
			$(".add-box").live("click",function(){
				openCenterWindow("/saleProduct/index.php/page/model/Warehouse.In.editBoxPage/"+inId,550,420) ;
			}) ;
			
			$(".add-box-product").live("click",function(){
				openCenterWindow("/saleProduct/index.php/page/model/Warehouse.In.editBoxProductPage/"+currentId,550,440) ;
			})
			
			$(".query-btn").click(function(){
				var asin = $("[name='asin']").val() ;
				var title = $("[name='title']").val() ;
				var status = $("[name='status']").val() ;
				var querys = {} ;
				if(asin){
					querys.asin = asin ;
				}
				if(title){
					querys.title = title ;
				}
				
				if(status){
					querys.status = status ;
				}
				
				$(".grid-content-details").llygrid("reload",querys) ;	
			}) ;
			
   	 });
   	 
   	 
   function showImg(el){
   		var src = el.src ;
   		openCenterWindow(src,500,300) ;
   }