$(function(){

	$(".grid-content").llygrid({
		columns:[
		   {align:"center",key:"ID",label:"操作", width:"5%",format:function(val,record){
					var html = [] ;
					html.push("<a href='#' class='action update' val='"+val+"'>编辑</a>&nbsp;") ;
					return html.join("") ;
			}},
			{align:"center",key:"STATUS",label:"状态",width:"5%",format:function(val,record){
				return val == 1?"已同步":"" ;
			}},
		 	{align:"center",key:"IN_NUMBER",label:"入库计划编号",width:"13%"},
		 	{align:"center",key:"ACCOUNT_NAME",label:"账号",width:"13%"},
           	{align:"center",key:"LABEL_PREP_TYPE",label:"Label类型",width:"10%"},
           	{align:"center",key:"NAME",label:"名称",group:"发货地址",width:"10%"},
           	{align:"center",key:"ADDRESS_LINE1",label:"AddressLine1",group:"发货地址",width:"20%"},
           	{align:"center",key:"ADDRESS_LINE2",label:"AddressLine2",group:"发货地址",width:"10%"},
           	{align:"center",key:"DISTRICT_OR_COUNTY",label:"国家或地区",group:"发货地址",width:"8%"},
           	{align:"center",key:"CITY",label:"城市",group:"发货地址",width:"8%"},
           	{align:"center",key:"STATE_OR_PROVINCE_CODE",label:"州或省代码",group:"发货地址",width:"8%"},
           	{align:"center",key:"COUNTRY_CODE",label:"国家代码",group:"发货地址",width:"8%"},
           	{align:"center",key:"POSTAL_CODE",label:"邮编",group:"发货地址",width:"8%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query"},
		 limit:10,
		 pageSizes:[10,20,30,40],
		 height:function(){
			 return 150 ;
		 },
		 title:"FBA入库计划列表",
		 indexColumn:false,
		 querys:{sqlId:"sql_supplychain_inbound_local_plan_list"},
		 loadMsg:"数据加载中，请稍候......",
		 rowDblClick:function(row,record){
			 $(".grid-content-detials").llygrid("reload",{inId: record.IN_ID},true) ;
		 },loadAfter:function(){
			 $(".grid-content").find(".update").click(function(){
				 var record = $(this).closest("tr").data("record") ;
				 openCenterWindow(contextPath+"/page/forward/SupplyChain.edit_inbound/"+record.PLAN_ID,1100,600,function(){
						$(".grid-content").llygrid("reload",{},true) ;
					}) ;
				 return false ;
			 }) ;
		 }
	}) ;
	

	$(".grid-content-detials").llygrid({
		columns:[
					{align:"center",key:"IMAGE_URL",label:"",width:"2%",format:{type:'img'}},
		           	{align:"center",key:"NAME",label:"货品名称",width:"5%"},
	           		{align:"center",key:"SKU",label:"货品SKU",width:"5%",format:function(val,reocrd){
	           			return "<a href='#' product-realsku='"+val+"'>"+val+"</a>" ;
	           		}},
	           		{align:"center",key:"LISTING_SKU",label:"Listing SKU",width:"5%"},
	           		{align:"center",key:"QUANTITY",label:"数量",width:"3%"},
	           		{align:"center",key:"DELIVERY_TIME",label:"供货时间",width:"6%"},
	           		{align:"center",key:"PRODUCT_TRACKCODE",label:"产品跟踪码",width:"6%"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:30,
				 pageSizes:[10,20,30,40],
				 height:function(){
					 return $(window).height()-130;
				 },
				 title:"FBA入库Listing列表",
				 autoWidth:true,
				 querys:{sqlId:"sql_warehouse_box_products_byInId",inId:'-'},
				 loadMsg:"数据加载中，请稍候......"
	}) ;
	
	$(".create-plan").click(function(){
		openCenterWindow(contextPath+"/page/forward/SupplyChain.edit_inbound",1100,600,function(){
			$(".grid-content").llygrid("reload",{},true) ;
		}) ;
	}) ;
});

