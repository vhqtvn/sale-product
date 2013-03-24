	$(function(){
		
		$(".grid-content-rma").llygrid({
				columns:[
					{align:"center",key:"QUALITY",label:"货品质量",width:"60",forzen:false
		           		,format:{type:"json",content:{'good':"良品",'bad':"残品"}}},
		           	{align:"center",key:"RAM_CODE",label:"RAM编码",width:"100"},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"100"},
		           	{align:"center",key:"NAME",label:"货品名称",width:"150"},
		           	{align:"center",key:"QUANTITY",label:"数量",width:"50" },
		           	//{align:"center",key:"TYPE",label:"货品类型",width:"10%",format:{type:"json",content:{'base':"基本类型",'package':"打包货品"}}},
		           	{align:"center",key:"IMAGE_URL",label:"图片",width:"50",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/"+fileContextPath+"/"+val+"' style='width:30px;height:30px;'>" ;
		           		}
		           		return "" ;
		           	}},
		           	{align:"center",key:"IMAGE",label:"残品图片",width:"50",format:function(val,record){
		           		if(val){
		           			val = val.replace(/%/g,'%25') ;
		           			return "<img src='/"+fileContextPath+"/"+val+"' style='width:30px;height:30px;'>" ;
		           		}
		           		return "" ;
		           	}},
		           	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"130" },
		           	{align:"center",key:"MEMO",label:"备注",width:"100"}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[20],
				 height:function(){
				 	return $(window).height()-100 ;
				 },
				 title:"残品出入库记录",
				 indexColumn:false,
				 querys:{sqlId:"sql_warehouse_rmaEdit_lists",id:realProductId},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
				 }
			}) ;

   }) ;