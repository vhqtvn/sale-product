	$(function(){
			$(".query").click(function(){
				var json = $(".query-table").toJson() ;
				$(".grid-content").llygrid("reload",json,true) ;
				$(".grid-content-details").llygrid("reload",json,true) ;
				$(".grid-content-tracks").llygrid("reload",json,true) ;
			}) ;
		

			$(".grid-content").llygrid({
				columns:[
				    {align:"center",key:"ID",label:"",width:"5%",format:function(val,record){
				    	return "<a href='#'  class='edit-inventory'>编辑</a>" ;
				    }},
				 	{align:"center",key:"IMAGE_URL",label:"图片",width:"5%",format:{type:'img'}},
		           	{align:"center",key:"NAME",label:"名称",width:"30%",forzen:false,align:"left"},
		           	{align:"center",key:"REAL_SKU",label:"SKU",width:"10%",sort:true},
		           	{align:"center",key:"QUANTITY",label:"库存数量",width:"10%",sort:true},
		           	{align:"center",key:"BAD_QUANTITY",label:"残品数量",width:"10%",sort:true},
		           	{align:"center",key:"LAST_UPDATED_TIME",label:"最近更新时间",width:"15%",sort:true}
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
					 $(".grid-content-tracks").llygrid("reload",{realId:record.REAL_ID}) ;
					 $(".grid-content-details").llygrid("reload",{realId:record.REAL_ID}) ;
				 }
			}) ;
			
			$(".edit-inventory").live('click',function(){
				var record = $(this).closest("tr").data("record");
				openCenterWindow(contextPath+"/page/forward/Warehouse.editInventory/"+record.REAL_ID,980,620,function(win,ret){
					if(ret){
						$(".grid-content-details").llygrid("reload",{},true) ;
					}
				}) ;
				return false ;
			}) ;
			
			$(".grid-content-details").llygrid({
				columns:[
				    {align:"center",key:"ACCOUNT_NAME",label:"账号",width:"10%",sort:true},
		           	{align:"center",key:"LISTING_SKU",label:"SKU",width:"20%",sort:true},
		        	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"20%",sort:true},
		           	{align:"center",key:"QUANTITY",label:"数量",width:"10%",sort:true},
		        	{align:"center",key:"INVENTORY_STATUS",label:"状态",width:"15%",sort:true,format:{type:"json",content:{'1':'在库','2':'在途'}}},
		        	{align:"center",key:"INVENTORY_TYPE",label:"类型",width:"15%",sort:true,format:{type:"json",content:{'1':'FBM','2':'FBA','3':'残品','4':'自由库存'}}}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[20,10,20,30,40],
				 height:function(){
				 	return 200 ;
				 },
				 title:"库存明细",
				 // autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sc_warehouse_in_ListInventory_details",realId:'-'},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(records){
				}
			}) ;
			
			$(".grid-content-tracks").llygrid({
				columns:[
		           	{align:"center",key:"LISTING_SKU",label:"SKU",width:"10%",sort:true},
		        	{align:"center",key:"WAREHOUSE_NAME",label:"仓库",width:"20%",sort:true},
		        	{align:"center",key:"ACTION_TYPE",label:"操作类型",width:"12%",sort:true,format:{type:"json",content:{'1':'入库','2':'出库'}}},
		        	{align:"center",key:"ACTION",label:"操作",width:"12%",sort:true,format:{type:"json",content:{'101':'采购入库','102':'转仓入库','103':'RMA入库'
		        		,'104':'托管入库','105':'库存转换入库','106':'借调入库','107':'其他入库','108':'FBM入库','109':'手工修改库存',
		        		'201':'转仓出库','202':'订单出库','203':'借调归还出库','204':'退货出库','205':'转仓出库'
		        	}}},
		           	{align:"center",key:"QUANTITY",label:"数量",width:"10%",sort:true},
		           	{align:"center",key:"ACTION_TIME",label:"操作时间",width:"20%",sort:true},
		           	{align:"center",key:"ACTIONOR",label:"操作用户",width:"12%",sort:true}
		         ],
		         ds:{type:"url",content:contextPath+"/grid/query"},
				 limit:20,
				 pageSizes:[20,10,20,30,40],
				 height:function(){
				 	return 200 ;
				 },
				 title:"库存轨迹",
				 // autoWidth:true,
				 indexColumn:false,
				  querys:{sqlId:"sc_warehouse_in_ListInventory_track",realId:'-'},
				 loadMsg:"数据加载中，请稍候......",
				 loadAfter:function(records){
				}
			}) ;
   	 });
   	 