	$(function(){
			var isAdd = false ;

			//保存
			$(".btn-save").click(function(){
				if(isAdd) return ;
				if( !$.validation.validate('#personForm').errorInfo ) {
					if( window.confirm("确认保存信息准确吗？") ){
						isAdd = true ;
						
						var json = $("#personForm").toJson() ;
						$.dataservice("model:Warehouse.Ram.doSaveRam",json,function(result){
							window.opener.openCallback('edit') ;
							window.close();
						});
					}
					
				};
				return false ;
			}) ;
			
			$(".btn-save-continue").click(function(){
				if(isAdd) return ;
				if( !$.validation.validate('#personForm').errorInfo ) {
					if( window.confirm("确认保存信息准确吗？") ){
						isAdd = true ;
						
						var json = $("#personForm").toJson() ;
						$.dataservice("model:Warehouse.Ram.doSaveRam",json,function(result){
							window.opener.openCallback('edit') ;
							window.location.reload();
						});
					}
					
				};
				return false ;
			}) ;
	
		var warehouseGridSelect = {
				title:'仓库选择',
				defaults:[],//默认值
				key:{value:'ID',label:'NAME'},//对应value和label的key
				valueField:"#warehouseId",
				labelField:"#warehouseName",
				multi:false,
				grid:{
					title:"仓库选择",
					params:{
						sqlId:"sql_warehouse_lists"
					},
					ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					pagesize:10,
					columns:[//显示列
						{align:"center",key:"ID",label:"编号",width:"100"},
						{align:"center",key:"CODE",label:"仓库代码",sort:true,width:"100"},
						{align:"center",key:"NAME",label:"仓库名称",sort:true,width:"100"},
						{align:"center",key:"ADDRESS",label:"地址",sort:true,width:"100"}
					]
				}
		   } ;
		   
		$(".btn-warehouse").listselectdialog( warehouseGridSelect) ;
		
		
	var productGridSelect = {
				title:'货品选择',
				key:{value:'ID',label:'NAME'},//对应value和label的key
				valueField:"#realProductId",
				labelField:"#realProductName",
				multi:false,
				grid:{
					title:"货品选择",
					params:{
						sqlId:"sql_warehouse_disk_products"
					},
					ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
					pagesize:10,
					columns:[//显示列
			           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left" },
			           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%"},
			           	{align:"center",key:"QUANTITY",label:"库存" ,width:"5%" },
			           	
			           	{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",
			           		content:{'base':"基本类型",'package':"打包货品"}}},
			           	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:function(val,record){
			           		
			           		if(val){
			           			val = val.replace(/%/g,'%25') ;
			           			alert(val);
			           			return "<img src='/saleProduct/"+val+"' style='width:30px;height:30px;'>" ;
			           		}
			           		return "" ;
			           	}},
			           	{align:"center",key:"MEMO",label:"备注",width:"20%"}
			           	
					]
				}
		   } ;
		$(".btn-select-product").listselectdialog( productGridSelect ) ;
		
		$("[name='realProductId']").click(function(){
			var sku = $(this).parents("tr:first").find("[key='realSku']").text() ;
			sku = $.trim(sku) ;
			//$(".grid-content-rma").find("")
		}) ;
		
		
		$(".grid-content-rma").llygrid({
				columns:[
					{align:"center",key:"QUALITY",label:"货品质量",width:"60",forzen:false
		           		,format:{type:"json",content:{'good':"良品",'bad':"残品"}}},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"100"},
		           	{align:"center",key:"NAME",label:"货品名称",width:"150"},
		           	{align:"center",key:"QUANTITY",label:"数量",width:"50" },
		           	//{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
		           	{align:"center",key:"IMAGE_URL",label:"图片",width:"50",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/saleProduct/"+val+"' style='width:30px;height:30px;'>" ;
		           		}
		           		return "" ;
		           	}},
		           	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"130" },
		           	{align:"center",key:"MEMO",label:"备注",width:"230"}
		         ],
		         ds:{type:"url",content:"/saleProduct/index.php/grid/query"},
				 limit:20,
				 pageSizes:[20],
				 height:function(){
				 	return 130 ;
				 },
				 title:"入库记录",
				 indexColumn:false,
				 querys:{sqlId:"sql_warehouse_rmaEdit_lists",rmaId:$("#ramId").val()},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
				 	$(".grid-content-active").llygrid("reload",{planId:record.ID});
				 }
			}) ;

   }) ;