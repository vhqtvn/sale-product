

	var currentId = '' ;
	$(function(){
		$(".message,.loading").hide() ;
			$(".grid-content").llygrid({
				columns:[
		           	{align:"center",key:"ID",label:"编辑",width:"5%",permission:function(){
		           		return !$isRead ;
		           	},format:function(val,record){
							return "<a href='#' class='edit-box' val='"+val+"'>编辑</a>&nbsp;&nbsp;" ;
					}},
		           	{align:"center",key:"BOX_NUMBER",label:"包装箱编号",width:"15%",forzen:false,align:"left"},
		           	{align:"center",key:"SHIP_FEE",label:"运输费用",width:"13%"},
		           	{align:"center",key:"WEIGHT",label:"重量",width:"8%"},
		           	{align:"center",key:"TOTAL",label:"尺寸(长X宽X高)",width:"15%",format:function(val,record){
		           		return (record['LENGTH']||'-') +'X'+ (record['WIDGH']||'-') +"X"+(record['HEIGHT']||'-') ;
		           	}},
		           	{align:"center",key:"MEMO",label:"备注",width:"33%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[10,20,30,40],
				 height:120,
				 title:"",
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
					{align:"center",key:"ID",label:"编辑",width:"5%",permission:function(){
		           		return !$isRead ;
		           	},format:function(val,record){
							return "<a href='#' class='edit-box-product' val='"+val+"'>编辑</a>&nbsp;&nbsp;" ;
					}},
				    {align:"center",key:"BOX_NUMBER",label:"包装箱",width:"5%"},
		           	{align:"center",key:"NAME",label:"货品名称",width:"5%"},
	           		{align:"center",key:"SKU",label:"SKU",width:"5%"},
	           		{align:"center",key:"INVENTORY_TYPE",label:"库存类型",width:"5%",format:{type:'json',content:{1:'普通库存',2:'FBA库存'}}}, 
	           		{align:"center",key:"QUANTITY",label:"数量",width:"6%"},
	           		{align:"center",key:"DELIVERY_TIME",label:"供货时间",width:"6%"},
	           		{align:"center",key:"PRODUCT_TRACKCODE",label:"产品跟踪码",width:"6%"},
	           		{align:"center",key:"MEMO",label:"备注",width:"6%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
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
				openCenterWindow(contextPath+"/sale/details1/"+FILTER_ID+"/"+asin+"/"+type+"/"+status,950,650) ;
			}) ;
			
			$(".add-box").live("click",function(){
				openCenterWindow(contextPath+"/page/model/Warehouse.In.editBoxPage/"+inId,550,420,function(){
					$(".grid-content").llygrid("reload",{});
					$(".grid-content-details").llygrid("reload",{boxId:''});
				}) ;
			}) ;
			
			$(".edit-box").live("click",function(){
				var boxId = $(this).attr("val") ;
				openCenterWindow(contextPath+"/page/model/Warehouse.In.editBoxPage/"+inId+"/"+boxId,550,420,function(){
					$(".grid-content").llygrid("reload",{},true);
				}) ;
				return false ;
			}) ;
			
			
			
			$(".add-box-product").live("click",function(){
				openCenterWindow(contextPath+"/page/model/Warehouse.In.editBoxProductPage/"+currentId,550,440,function(){
					$(".grid-content-details").llygrid("reload",{},true);
				}) ;
			})
			
			$(".edit-box-product").live("click",function(){
				var boxPId = $(this).attr("val") ;
				openCenterWindow(contextPath+"/page/model/Warehouse.In.editBoxProductPage/"+currentId+"/"+boxPId,550,440,function(){
					$(".grid-content-details").llygrid("reload",{},true);
				}) ;
			})
			
   	 });
   	 
   	 function openCallback(type){
   	 	if(type == 'box'){
   	 		$(".grid-content").llygrid("reload",{},true);
   	 	}else{
   	 		$(".grid-content-details").llygrid("reload",{},true);
   	 	}
   	 }