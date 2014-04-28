	$(function(){
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
			}) ;
		

			$(".grid-content").llygrid({
				columns:[
				 	{align:"center",key:"IMG_URL",label:"图片",width:"5%",format:{type:'img'}},
		           	{align:"center",key:"NAME",label:"名称",width:"20%",forzen:false,align:"left"},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%",sort:true},
		           	{align:"center",key:"QUANTITY",label:"库存数量",width:"10%",sort:true}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[20,10,20,30,40],
				 height:function(){
				 	return $(window).height() - 400 ;
				 },
				 title:"",
				 // autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sc_warehouse_in_ListInventory",status:1,categoryId:''},
				 loadMsg:"数据加载中，请稍候......",
				 rowClick:function(row,record){
					 $(".grid-content-details").llygrid("reload",{realId:record.REAL_ID}) ;
				 }
			}) ;
			
			$(".grid-content-details").llygrid({
				columns:[
		           	{align:"center",key:"LISTING_SKU",label:"SKU",width:"10%",sort:true},
		        	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"10%",sort:true},
		        	{align:"center",key:"ACTION",label:"Action",width:"10%",sort:true},
		        	{align:"center",key:"ACTION_TYPE",label:"Action Type",width:"10%",sort:true},
		           	{align:"center",key:"QUANTITY",label:"库存数量",width:"10%",sort:true},
		           	{align:"center",key:"ACTION_TIME",label:"操作时间",width:"20%",sort:true},
		           	{align:"center",key:"ACTIONOR",label:"操作用户",width:"10%",sort:true}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[20,10,20,30,40],
				 height:function(){
				 	return 200 ;
				 },
				 title:"",
				 // autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sc_warehouse_in_ListInventory_track",realId:'-'},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(records){
			
					$realIds = [] ;
					$(records).each(function(){
						$realIds.push(this.ID) ;
					}) ;
	
				}
			}) ;
   	 });
   	 